<?php
	/**
	 * MAIL_QUEUE Administration Class
	 * This class represents a mail queue
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::MAIL::MAIL_QUEUE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 require_once(__APPLICATION_ROOT__  . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR .  'mail.php');
	 require_once(__APPLICATION_ROOT__  . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR .  'database.php');
	 require_once(__APPLICATION_ROOT__  . DIRECTORY_SEPARATOR . 'utility-functions.php');
	 
	 class MAIL_QUEUE extends MAIL {
		
		public function __construct() {
			parent::__construct();	
		}
		
	 	public static function getInstance() {
			return (new self());	
		}
		
		public function setQueueDate($dtDate = NULL) {
			$this->queueDate = (is_null($dtDate) ? now() : $dtDate);	
			return ($this->queueDate);
		}
		
		public function getQueueDate() {
			return (isset($this->queueDate) ? $this->queueDate : $this->setQueueDate());
		}
		
		public function queueMail() {
			$blnReturn = true;
			$objDb = DATABASE::getInstance();
			$this->parseData();
			// Insert mail in the mail_queue
			try {
				DATABASE::insertUpdateFromArray(
					strtolower(__CLASS__),
					array(
						'from'			=> (strlen($this->getFrom()) ? $this->getFrom() : __INFO_EMAIL__), ucwords(__SITE_NAME__),
						'to'			=> $this->getTo(),
						'subject'		=> $this->getSubject(),
						'bodyText'		=> $this->getTextMessage(),
						'bodyTextHtml'	=> $this->getHtmlMessage(),
						'sendOnTimeDate'=> $this->getQueueDate(),
						'isSent'		=> 0,
						'dateSent'	 	=> 'NULL'
					),
					array(
						'creationDate'	 => 'NOW()',
						'updateTimeDate' => 'NOW()'
					)
				);
			} catch (Exception $e) {
				$blnReturn = false;	
			}
			return ($blnReturn);
		}
		
		final public static function sendQueuedMail($blnVerbose = false) {
			$blnSuccess = false;
			$objDb = DATABASE::getInstance();
			$MAIL_QUEUE_CAST = SHARED_OBJECT::cast(new MAIL_QUEUE());	
			$arrMailQueue = SHARED_OBJECT::getObjectClassView(array(
				'forceClass'	=> 	'MAIL_QUEUE',
				'filter' 		=> array(
					/*'sendOnTimeDate' =>	"DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MINUTE), '%Y-%m-%d %H:%i:%s')",*/
					'isSent'		 => 0
				),
				'operator' 		=> array('<='),
				'escapeData' 	=> false
			));
			
			foreach ($arrMailQueue as $intIndex => $arrQueueMail) {
				$objMailer = new parent();
				$objMailer->setTo($arrQueueMail['to']);
				$objMailer->setFrom($arrQueueMail['from']);
				$objMailer->setSubject($arrQueueMail['subject']);
				$objMailer->setMessage($arrQueueMail['bodyTextHtml']);
				$objMailer->send();		
				
				if ((bool) $blnVerbose ) 
				{
					echo ('Sending mail ID: ' . $arrQueueMail['id'] . ' to: ' . $arrQueueMail['to'] . "\n");	
				}
				$blnSuccess = DATABASE::insertUpdateFromArray(
					strtolower(__CLASS__),
					array(
						'id'			=>  $arrQueueMail['id'],
						'isSent'	 	=> 1
					),
					array('dateSent' => 'NOW()')
				);
			}
			
			return ($blnSuccess);
		}
	 }
?> 