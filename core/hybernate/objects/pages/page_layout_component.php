<?php
	/**
	 * PAGE_LAYOUT_COMPONENT Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::PAGES
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 
	 class PAGE_LAYOUT_COMPONENT extends SHARED_OBJECT {
		
		public static function getArrayInstanceFromPageLayout($intLayoutId = NULL) {
			$intPageLayoutId  = (int) $intLayoutId;
			$arrComponentData = array();
			if ($intPageLayoutId) {
				$arrComponentData = PAGE_LAYOUT_COMPONENT::getObjectClassView(array(
					'columns'	=> 'a.spot_name, a.component_name, a.is_cacheable, a.index',	  
					'filter'	=> array(
						'page_layout_id' => $intPageLayoutId
					),
					'groupBy'	=> 'a.id',
					'orderBy'	=> 'a.index ASC, a.spot_name',
					'direction'	=> 'DESC'
				));	
			}
			return ($arrComponentData);
		}
		
		protected function executeComponent() {
			
		}
		
		public static function loadComponent($strComponentName) {
			SHARED_OBJECT::getObjectFromPackage("COMPONENTS::" . strtoupper($strComponentName) . "::COMPONENT");
			$objComponent = new $strComponentName();
			return ($objComponent);
		}
		
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() {
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
		}
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>