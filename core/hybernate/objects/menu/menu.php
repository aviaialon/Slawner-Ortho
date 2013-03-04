<?php
/**
 * MENU Administration Class
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
 
class MENU extends SHARED_OBJECT {
	
	public static $blnIsCached = false;
	
	/**
	 * This method loads the menu  
	 * @param Integer 	$intMenuGroupId			- 	The Menu Group Id 
	 * @param String 	$strAttribute 			- 	An extra attribute to add the main <UL> menu. 
	 * 												Ex: attr = 'id="test"' will generate <ul id="test">...</ul>
	 * @param String	$strCurrentCononicalUrl	-	The current canonical URL (used to find the active menu item)
	 * @return String	$strMenuHtml			- 	The menu HTML
	 */
	public static final function getSiteMenuHtml($intMenuGroupId, $strAttribute = '', $strCanonicalUrl = NULL)
	{
		$objMenuTree	 = MENU_TREE::getInstance();
		$objDb 			 = DATABASE::getInstance();
		$objMemcache	 = MEMCACHE_MANAGER::getInstance();
		$strCurrentCanonicalUrl = (
			(false === is_null($strCanonicalUrl)) ? 
			$strCanonicalUrl : 
			URL::getCanonicalUrl(NULL, false, true, true, array(session_name()), false)
		);
		$strCurrentCanonicalUrl = ((true === empty($strCurrentCanonicalUrl)) ? '/' : $strCurrentCanonicalUrl);
		$strMenuCacheKey = __CLASS__ . '::' . __FUNCTION__ .  '[' . $intMenuGroupId . ',' .  $strCurrentCanonicalUrl  . ']';
		$strMenuHtml	 = NULL;
		$arrPageMenu	 = array();
		
		//$objMemcache->delete($strMenuCacheKey);
		$strMenuHtml = $objMemcache->get($strMenuCacheKey);
		if (FALSE === $strMenuHtml || is_null($strMenuHtml))
		{
			if (
				(false === empty($strCurrentCanonicalUrl)) &&
				(true === (in_array(substr($strCurrentCanonicalUrl, 0, 3), array('/en', '/fr'))))
			) {
				$strCurrentCanonicalUrl = '/' . (substr($strCurrentCanonicalUrl, 3));
				$strCurrentCanonicalUrl = str_replace('//', '/', $strCurrentCanonicalUrl);
			}
			
			$arrPageMenu = MENU::getObjectClassView(array(
				'cacheQuery'=> 	false,	
				'filter' 	=>	array(
					'group_id' 	=> (int) $intMenuGroupId
				),
				'orderBy'	=> 'parent_id ASC, position',
				'direction'	=> 'ASC'
			));
			if (FALSE === empty($arrPageMenu))
			{
				$arrParents = array();
				foreach ($arrPageMenu as $arrRow) 
				{
					if (
						(false === empty($arrRow['parent_id'])) &&
						(((int) $arrRow['parent_id']) > 0)
					) {
						$arrParents[(int) $arrRow['parent_id']] = true;
					}
				}
				
				reset ($arrRow);
				foreach ($arrPageMenu as $arrRow) 
				{
					$blnIsCurrentPage 	= false;
					$blnUsedCurrent 	= false;
					if (false === empty($strCurrentCanonicalUrl))
					{
						$strMenuUrl = $arrRow['url'];
						$strMenuUrl = str_replace(constant('__ROOT_URL__'), '/', $strMenuUrl);
						$strMenuUrl = str_replace('//', '/', $strMenuUrl);
						$blnIsCurrentPage = $blnUsedCurrent = (bool) ($strCurrentCanonicalUrl == ($strMenuUrl));	
					}
					
					$label  = '<a href="'. $arrRow['url'] .'" ' . ($blnIsCurrentPage ? ' class="current active" ' : '') . '>';
					$label .= $arrRow['title'];
					if (
						(true  === array_key_exists((int) $arrRow['id'], $arrParents)) &&
						(false === empty($arrRow['parent_id'])) // Make sure its not the first row!
					) {
						$label .= '<img src="/static/images/right.png" class="menuArrow" />';
					}
					$label .= '</a>';
					$li_attr = ' class="';
					if ($arrRow['class']) 
					{
						$li_attr .= $arrRow['class'] . " ";
					}
					$li_attr .= ($blnIsCurrentPage ? 'current active' : '') . '"';
					$objMenuTree->add_row($arrRow['id'], $arrRow['parent_id'], $li_attr, $label);
				}
				
				$strMenuHtml = $objMenuTree->generate_list($strAttribute);
				$objMemcache->set($strMenuCacheKey, $strMenuHtml, strtotime('+30 minute'));
			}
		}
		else 
		{
			self::$blnIsCached = true;
		}
		
		return ($strMenuHtml);
	}
	
	/**
	 * This method returns if the current menu output is cached
	 * @return boolean self::$blnIsCached
	 */
	public static final function isCached()
	{
		return ((bool) self::$blnIsCached);
	} 
	
	/**
	 * This method updates the position of a certain menu. its a recursive function.
	 * 
	 * @access	public, static
	 * @param 	integer 	$intParentId 		- The menu parent ID
	 * @param 	array 		$arrMenuChildren 	- The menu children
	 * @param 	integer		$intMenuGroupId 	- The menu groupId (used to clear the cache)
	 * @return 	void
	 */
	public static function updateMenuPosition($intParentId = 0, $arrMenuChildren = array(), $intMenuGroupId = false) 
	{
		$i = 1;
		foreach ($arrMenuChildren as $k => $v) 
		{
			/*
			$objMenu = MENU::getInstance((int) $arrMenuChildren[$k]['id']);
			if ($objMenu->getId())
			{
				$objMenu->setParent_Id($intParentId);
				$objMenu->setPosition($i);
				$objMenu->save();
			}
			if (isset($arrMenuChildren[$k]['children'][0])) 
			{
				self::updateMenuPosition($objMenu->getId(), $arrMenuChildren[$k]['children']);
			}
			*/
			
			/**
			 * Using iQuery here greatly improves the performance!
			 */
			$intId = (int) $arrMenuChildren[$k]['id'];
			APPLICATION::getInstance()->getDatabase()->iQuery($strSql = 
				"UPDATE " . strtolower(__CLASS__) . " " .
				"SET	parent_id = " . (int) $intParentId . ", " .
				"		position = " . (int) $i . " " .
				"WHERE	id = " . $intId	
			);
			
			
			if (isset($arrMenuChildren[$k]['children'][0])) 
			{
				self::updateMenuPosition($intId, $arrMenuChildren[$k]['children']);
			}
			$i++;
		}
		
		/**
		 * Clear the menu cache for the group
		 */
		if ((int) $intMenuGroupId)
		{
			self::clearMenuGroupCache((int) $intMenuGroupId);
		}
	}
	
	
	
	/**
	 * This method loads the menu  
	 * @param Integer 	$intMenuGroupId	- 	The Menu Group Id 
	 * @param String 	$strAttribute 	- 	An extra attribute to add the main <UL> menu. 
	 * 										Ex: attr = 'id="test"' will generate <ul id="test">...</ul>
	 * @return Array	$arrPageMenuTree	- 	The menu Tree
	 */
	public static final function getRawMenuData($intMenuGroupId, $strAttribute = '')
	{
		$objMenuTree	 = MENU_TREE::getInstance();
		$objDb 			 = DATABASE::getInstance();
		$objMemcache	 = MEMCACHE_MANAGER::getInstance();
		
		$strMenuCacheKey = __CLASS__ . '::' . __FUNCTION__ .  '[' . $intMenuGroupId . ']';
		$arrPageMenu	 = array();
		$arrPageMenuTree = array();
		$objSortedMenu	 = new stdClass();

		//$objMemcache->delete($strMenuCacheKey);
		$arrPageMenuTree = $objMemcache->get($strMenuCacheKey);
		if (FALSE === $arrPageMenuTree || (true === empty($arrPageMenuTree)))
		{
			$arrPageMenu = MENU::getObjectClassView(array(
				'cacheQuery'=> 	false,	
				'filter' 	=>	array(
					'group_id' 	=> (int) $intMenuGroupId
				),
				'orderBy'	=> 'parent_id ASC, position',
				'direction'	=> 'ASC'
			));
			
			if (FALSE === empty($arrPageMenu))
			{
				foreach ($arrPageMenu as $arrRow) 
				{
					$li_attr = array();
					if (false === empty($arrRow['class'])) {
						$li_attr['class'] = $arrRow['class'];
					}
					
					$objMenuTree->add_row($arrRow['id'], $arrRow['parent_id'], $li_attr, $arrRow['title'], $arrRow['url']);
				}
				
				$arrPageMenuTree = $objMenuTree->generate_raw_list(0, $strAttribute);
				
				$objMemcache->set($strMenuCacheKey, $arrPageMenuTree, strtotime('+30 minute'));
			}
			
			
		}
		else 
		{
			self::$blnIsCached = true;
		}
		
		return ($arrPageMenuTree);
	}
	
	
	/**
	 * This method clears the site menu cache for a menu group.
	 * @access protected, static	
	 * @param  integer $intGroupId 	- The menu group Id
	 * @return void
	 */
	public static function clearMenuGroupCache($intGroupId = false)
	{
		if ((int) $intGroupId)
		{
			$strMenuCacheKey = __CLASS__ . '::getSiteMenuHtml[' . (int) $intGroupId . ']';
			$objMemcache = MEMCACHE_MANAGER::getInstance();
			$objMemcache->delete($strMenuCacheKey);
		}
	}
	
	
	/**
		Abstraction Methods
	 **/
	protected function onBefore_getInstance() 
	{
		$this->setConstructIntegrity ( SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE );
		$this->setObjectCacheType ( SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE );
	}
	
	/**
	 * Here, we inplement the onbefore clear cache because when a menu item is updated, 
	 * we need to clear the cache for the whole group
	 * TODO: need to implement a caching mechanisim in case a certain server does not support memcache
	 * 
	 * @return void
	 */
	protected function onBeforeClearCache()
	{
		if ($this->getId())
		{
			self::clearMenuGroupCache((int) $this->getVariable('group_id'));
		}
	}
	
	
	protected function getClassPath() 
	{
		return (__FILE__);
	}

}

/**
 * Class for generating nested lists
 *
 * example:
 *
 * $tree = new Tree;
 * $tree->add_row(1, 0, '', 'Menu 1');
 * $tree->add_row(2, 0, '', 'Menu 2');
 * $tree->add_row(3, 1, '', 'Menu 1.1');
 * $tree->add_row(4, 1, '', 'Menu 1.2');
 * echo $tree->generate_list();
 *
 * output:4
 * <ul>
 * 	<li>Menu 1
 * 		<ul>
 * 			<li>Menu 1.1</li>
 * 			<li>Menu 1.2</li>
 * 		</ul>
 * 	</li>
 * 	<li>Menu 2</li>
 * </ul>l..[''[0o;.0
 *
 * @author gawibowo
 */
class MENU_TREE {
	
	/**
	 * variable to store temporary data to be processed later
	 *
	 * @var array
	 */
	var $data;
	
	public static final function getInstance()
	{
		return (new self());
	}
	
	/**
	 * Add an item
	 *
	 * @param int $id 			ID of the item
	 * @param int $parent 		parent ID of the item
	 * @param string $li_attr 	attributes for <li>
	 * @param string $label		text inside <li></li>
	 */
	function add_row($id=NULL, $parent=NULL, $li_attr=NULL, $label=NULL, $url=NULL) 
	{
		$this->data [$parent] [] = array ('id' => $id, 'li_attr' => $li_attr, 'label' => $label, 'url' => $url );
	}
	
	/**
	 * Generates nested lists
	 *
	 * @param string $ul_attr
	 * @return string
	 */
	function generate_list($ul_attr = '') 
	{
		return $this->ul ( 0, $ul_attr );
	}

	/**
	 * Generates nested lists and returns the raw array
	 *
	 * @param integer $parentId - For recursive purposes
	 * @return array
	 */
	function generate_raw_list($parent=0) 
	{
		static $i = 1;
		static $level = 0;
		if (isset ( $this->data [$parent] )) {
			if ($strAttribute) {
				$strAttribute = ' ' . $strAttribute;
			}
			$menuArray = array();
			$i ++;
			$level++;
			foreach ( $this->data [$parent] as $row ) {
				$child = $this->generate_raw_list ( $row ['id'] );
				$menuArray[] = array(
					'attributes' => $row ['li_attr'],
					'label'		 => $row ['label'],
					'url'		 => $row ['url'],
					'children'	 => $child,
					'menu_level' => $level	
				);
				
				if ($child) { $i --; }
			}
			$level--;
			
			return $menuArray;
		} 
		else 
		{
			return array();
		}
	}
	
	/**
	 * Recursive method for generating nested lists
	 *
	 * @param int $parent
	 * @param string $strAttribute
	 * @return string
	 */
	function ul($parent = 0, $strAttribute = '') 
	{
		static $i = 1;
		$indent = str_repeat ( "\t\t", $i );
		if (isset ( $this->data [$parent] )) {
			if ($strAttribute) {
				$strAttribute = ' ' . $strAttribute;
			}
			$html = "\n$indent";
			$html .= "<ul$strAttribute>";
			$i ++;
			foreach ( $this->data [$parent] as $row ) {
				$child = $this->ul ( $row ['id'] );
				$html .= "\n\t$indent";
				$html .= '<li' . $row ['li_attr'] . '>';
				$html .= $row ['label'];
				
				if ($child) {
					$i --;
					$html .= $child;
					$html .= "\n\t$indent";
				}
				$html .= '</li>';
			}
			$html .= "\n$indent</ul>";
			return $html;
		} else {
			return false;
		}
	}
	
	/**
	 * Clear the temporary data
	 *
	 */
	function clear() 
	{
		$this->data = array ();
	}
}
