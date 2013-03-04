<?php
	/**
	 * MAIL Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::MAIL::MAIL
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	/**
	 * EXAMPLE:
	 
		require_once('mail/mail.php');
		$objMailer = new MAIL();
		$objMailer->setData(array(
			'E' 	=> 'aviaialon@gmail.com',
			'ROOT'	=> __ROOT_URL__	,
			'DATE'	=> 	date("F j, Y")
		));
		$objMailer->setTo('%%E%%');
		$objMailer->setSubject('Hello %%E%% This is a test!');
		$objMailer->setMessage('Hello %%E%% This is a test!');
		$objMailer->setTemplate('templates/email/template.tmpl');
		if ($objMailer->send()) {
			print "Mail sent .. yay!";
		} else {
			new dump($objMailer->getError());
		}
		print "ok";
	 
	 */
	require_once (__APPLICATION_ROOT__ . '/mail/smtp_mailer.php');
	require_once (__APPLICATION_ROOT__ . '/parser/parser.php');
	require_once (__APPLICATION_ROOT__ . '/io/template.php');
	
	class MAIL extends SITE_EXCEPTION {
		protected $strTo;						# Receiver email
		protected $strFrom;						# From email 
		protected $strSubject;					# Subject 
		protected $strHTMLMessage;				# HTML message
		protected $strTextMessage;				# Text message part
		protected $strTemplate;					# HTML Email template
		protected $arrParseData = array();		# Data to parse
		protected $arrErrors 	= array();		# Processing errors
		private $objParser		= NULL;			# Parseing engine
		
		
		public function __construct() {
			$this->objParser = new PARSER();
		}
		
		public static function getInstance()
		{
			return (new MAIL());	
		}
		
		/**
		 * Setters
		 */
		public function setTo		($strArgTo=NULL)		{ $this->strTo 		 = $strArgTo; }
		public function setFrom		($strArgFrom=NULL)		{ $this->strFrom 	 = $strArgFrom; }
		public function setSubject	($strArgSubject=NULL)	{ $this->strSubject  = $strArgSubject; }
		public function setTemplate	($strArgTemplate=NULL)	{ $this->strTemplate = $strArgTemplate; }
		public function setMessage	($strArgMessage=NULL)	{ 
			$this->strHTMLMessage = $strArgMessage;
			$this->strTextMessage = strip_tags($strArgMessage);
		}
		public function setData		(Array $arrArgData=array()) {
			$this->arrParseData = $arrArgData;
		}
		protected function setError($strError = NULL) {
			$this->arrErrors[] = $strError;
		}
		
		/**
		 * Getters
		 */
		public 	 	function getTo()			{ return($this->strTo); } 
		public  	function getFrom()			{ return($this->strFrom); } 
		public  	function getSubject()		{ return($this->strSubject); } 
		public  	function getHtmlMessage()	{ return($this->strHTMLMessage); } 
		public  	function getTextMessage()	{ return($this->strTextMessage); } 
		public  	function getData()			{ return($this->arrParseData); } 
		public  	function getError()			{ return($this->arrErrors); } 
		protected 	function getParser()		{ return($this->objParser); } 
		protected 	function getTemplate()		{ return($this->strTemplate); } 
		
		/**
		 * Public methods
		 */
		public function parseData() {
			$objParser 	= $this->getParser();
			$blnReturn 	= false;
				
			if (sizeof($this->getData())) {
				$objParser->setData($this->getData());
				// Parse the values
				
				// To (reveiver)
				$this->setTo(
					$objParser->parse($this->getTo())				  
				);
				
				// From (sender)
				$this->setFrom(
					$objParser->parse($this->getFrom())				  
				);
				
				// Subject
				$this->setSubject(
					$objParser->parse($this->getSubject())				  
				);
				
				// Message
				$this->setMessage(
					$objParser->parse($this->getHtmlMessage())				  
				);
				
				$blnReturn 	= true;
			}
			if (strlen($this->getTemplate())) {
				$objTemplate = new TEMPLATE();
				$objTemplate->setTemplateUrl($this->getTemplate());
				$objTemplate->setData(
					array_merge(array('MESSAGE' => $objParser->parse($this->getHtmlMessage())), $this->getData())
				);
				$objTemplate->execute();
				$this->setMessage($objTemplate->getTemplate());
			}
			
			return ($blnReturn);
		}	
		
		public function send() {
			$blnReturn = false;
			$objMailer = SMTP_MAILER::getInstance();
			
			if (TRUE === constant('__USE_SMTP_SETS__')) 
			{
				$objMailer->IsSMTP();
				$objMailer->SMTPAuth   	= true; 
				$objMailer->Port       	= (int) constant('__SMTP_PORT__');
				$objMailer->SMTPSecure 	= constant('__SMTP_SSL_TYPE__'); 
				$objMailer->Host       	= constant('__SMTP_HOST__');
				$objMailer->Username    = constant('__SMTP_USER__');
				$objMailer->Password    = constant('__SMTP_PASS__');
			}
			
			// Set the mail data
			$this->parseData();
			$objMailer->WordWrap    = 50;
			$objMailer->From 		= (strlen($this->getFrom()) ? $this->getFrom() : __INFO_EMAIL__);
			$objMailer->FromName	= ucwords(constant('__SITE_NAME__'));
			$objMailer->Subject    	= $this->getSubject();
			$objMailer->AltBody 	= strip_tags($this->getHtmlMessage());
			$objMailer->MsgHTML($this->getHtmlMessage());
			$objMailer->AddReplyTo($objMailer->From, $objMailer->FromName);
			$objMailer->AddAddress($this->getTo());
			$objMailer->IsHTML(true);
			$blnReturn = $objMailer->Send();
				
			if (FALSE === $blnReturn) 
			{
				$this->setError($objMailer->ErrorInfo);
			}
			
			return ($blnReturn);
		}
	}
?>