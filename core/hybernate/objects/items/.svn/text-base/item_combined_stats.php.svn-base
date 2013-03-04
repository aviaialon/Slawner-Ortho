<?php
	/**
	 * ITEM_COMBINED_STATS Administration Class
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
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 
	 class ITEM_COMBINED_STATS extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		public 	  function increase($strVariable = NULL) {
			$blnContinue = ((! is_null($strVariable)) && ((bool) $this->getVariable('id')));	
			if ($blnContinue) {
				$intCurrentVal = (int) $this->getVariable($strVariable);	
				if (! $intCurrentVal) {
					$intCurrentVal = 1;	
				}
				$this->setVariable($strVariable, $intCurrentVal + 1);
			}
		}
		
		public 	  function decrease($strVariable = NULL) {
			$blnContinue = ((! is_null($strVariable)) && ((bool) $this->getVariable('id')));	
			if ($blnContinue) {
				$intCurrentVal = (int) $this->getVariable($strVariable);	
				if (! $intCurrentVal) {
					$intCurrentVal = 1;	
				}
				$this->setVariable($strVariable, ((int) $intCurrentVal) - 1);
			}
		}
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE); // No Cache
		}
	}
?>