<?php
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MEMCACHE::MEMCACHE_MANAGER");
	 
	/**
	 * PAGES Administration Class
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
	 
	 class PAGES extends SHARED_OBJECT {
	 
	 	protected $arrExecutedComponentData	= array(); // This is where the executed component data is stored for future use
		
		protected final function addComponentData($strComponentName, $mxComponentData) {
			$this->arrExecutedComponentData[$strComponentName] = $mxComponentData;
		}
		
		protected final function getComponentData($strComponentName = NULL) {
			$mxReturnData = (
				is_null($strComponentName) ? $this->arrExecutedComponentData : 
				(isset($this->arrExecutedComponentData[$strComponentName]) ? $this->arrExecutedComponentData[$strComponentName] : false)
			);
			return ($mxReturnData);
		}
		
		
		/**
		 * This method returns the quick view menu data
		 *
		 * @access	public static final
		 * @param	integer	$intSecureLevel	- Display menus that require a certain secure level (defaults to NULL)
		 * @return	array	
		 */
		public static final function getQuickViewMenu($intSecureLevel = NULL)
		{
			$arrparams 	=  array
			(
				'columns' 		=>	array(
					'a.id',
					'a.title',
					'a.date_changed',
					'CONCAT("' . constant('__SITE_URL__') . '", "/pages", a.url) url'
				),
				'filter'		=>	array(
					'active_status'		=>	1,
					'show_quick_links'	=>	1
				),
				'orderBy'		=> 	'a.id',
				'direction'		=>	'ASC'
			);
			
			if (FALSE === is_null($intSecureLevel))
			{
				$arrparams['filter']['secure_level'] = (int) $intSecureLevel;
			}
			
			return (PAGES::getObjectClassView($arrparams));
		}
		
		
		/**
		 * This method returns a page for a given URL path
		 *
		 * @access	public static final
		 * @param	String	$strPath - The URL path for the page
		 * @return	PAGES	
		 */
		public static final function getPageFromPath($strPath = NULL)
		{
			$objDb = DATABASE::getInstance();
			
			return (array_shift(PAGES::getMultiInstance(array(
				'columns' 		=>	'a.*',
				'filter'		=>	array(
					'active_status'	=>	1,
					'url'			=>	"('" . $objDb->escape($strPath) . "', '/" . $objDb->escape($strPath) . "')"
				),
				'operator'		=>	array(
					'=', 'IN'
				),
				'limit'			=>	1,
				'escapeData'	=>	false
			))));
		}
		
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() 
		{
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE);
		}
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>
