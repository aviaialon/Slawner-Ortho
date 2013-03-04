<?php
	/**
	 * ITEM_VIEWS Administration Class
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
	 
	 class ITEM_VIEWS extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		public static function getItemViews($intItemId = NULL) {
			$arrFilter = array();
			if (! is_null($intItemId)) {
				$arrFilter['itemId'] = (int) $intItemId; 
			}	
			return(ITEM_VIEWS::getObjectClassView(array(
				'columns'	=>	'COUNT(a.id) viewCount, a.itemId',		
				'filter'	=> 	$arrFilter,
				'groupBy'	=>	'a.itemId'		
			)));
		}
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>