<?php
	/**
	 * AUCTION_STATUS Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::AUCTION
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 define('AUCTION_STATUS_ENABLED', 		'1');
	 define('AUCTION_STATUS_CLOSED', 		'2');
	 define('AUCTION_STATUS_PAYED', 		'3');
	 define('AUCTION_STATUS_SUSPENDED', 	'4');
	 define('AUCTION_STATUS_PROCESSING', 	'5');
	 
	 class AUCTION_STATUS extends SHARED_OBJECT {
		/**
			Abstraction Methods
		**/
		/**
			Abstraction Methods
		**/
		/*
		public static function getInstance($intId = 0) {
			$__strObjClassName__ = __CLASS__;
			$objReturn = new $__strObjClassName__((int) $intId);
			return ($objReturn->_getInstance((int)  $intId));
		}
		*/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>