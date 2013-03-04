<?php
	/**
	 * ACTIVE_STATUS Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 define('ACTIVE_STATUS_ENABLED', 	'1');
	 define('ACTIVE_STATUS_DISABLED', 	'2');
	 define('ACTIVE_STATUS_PENDING', 	'3');
	 define('ACTIVE_STATUS_REJECTED', 	'4');
	 define('ACTIVE_STATUS_DEACTIVATED','5');
	 
	 class ACTIVE_STATUS extends SHARED_OBJECT {
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