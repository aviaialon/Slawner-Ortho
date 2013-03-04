<?php
class USER_MANAGER_CONTROLLER extends ADMIN_APPLICATION implements IMODULE_BASE
{	
	/**
	 * Module index action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function indexAction(array $arrRequestParams)
	{
		/*
		$arrDefaultView = array(
				'columns'	=>	'*',			# The columns to be selected, can be an array as well
				'filter'	=>	array(),		# Filter to use in the where clause (ex: id=1) 
				'operator'	=>	array(),		# The operator to use in the filtering, ex: array('=', '>') :: First param will be id=1 second id > 1 (mapped with the filter value)
				'limit'		=>	false,			# Max amount of rows
				'orderBy'	=> 	'a.id',			# Order by value
				'direction'	=> 	'DESC',			# Filtering direction ASC/DESC
				'groupBy'	=>	NULL,			# Group by data,
				'escapeData'=>	true,			# Escape filter data.
				'inner_join'=>	array(),		# Inner join query array
				'left_join' =>	array(),		# Left join query array
				'debug'		=>	false,			# DEBUG true/false
				'forceClass'=>	false,			# Force the class | Used in emulation
				'cacheQuery'=>	false,			# Cache the query true/false
				'cacheTime' =>	'+30 minute'	# Query cache length 
			);
		*/	
		//
		// 1. get the pending users
		$this->setViewData('Pending_User_List', SITE_USERS::getObjectClassView(array(
			'column' 	=> 'a.*',
			'operator'	=> array('=', '<='),
			'filter'	=> array(
				'active' => ACTIVE_STATUS_PENDING,
				'access_level' => $this->getUser()->getAccess_Level()
			)
		)));
		
		$this->setViewData('User_List', SITE_USERS::getObjectClassView(array(
			'column' => 'a.*',
			'operator'	=> array('<='),
			'filter'	=> array(
				'access_level' => $this->getUser()->getAccess_Level()
			)
		)));
		
		$this->setViewData('Resource_Path', '/admin/mvc/modules/user_manager');
	}
	
	/**
	 * Add user method
	 * 
	 * @access	public, static
	 * @param 	array $arrConfig
	 * @return 	void
	 */
	public final function add_userAction(array $arrRequestParams)
	{
		$objSaveUserUrl = $this->newActionUrl(array('save-user'));
		$this->setViewData('Save_User_Url', $objSaveUserUrl->build());
		/*
		// Add Menu Action: Make sure the group ID is passed in the URL --> /add-menu/group/1
		if (
			(false === empty($arrRequestParams)) &&
			(count($arrRequestParams) >= 2) &&
			((int) $arrRequestParams[1]) &&
			($objMenu = MENU_GROUP::getInstance((int) $arrRequestParams[1])) &&
			($objMenu->getId())
		) {
			// Since this is called via a ajax request, we'll need to render the view here
			// Because the MVC does not render views on AJAX requests.
			$objEditMenuUrl = $this->newActionUrl(array('save-menu'));
			$this->setViewData('Menu_Group_Id', $objMenu->getId());
			$this->setViewData('Save_Menu_Url', $objEditMenuUrl->build());
			$this->renderOutput(array(
				'Request_Data' 	=> $this->getRequestData(),
				'View_Data'		=> $this->getViewData()
			)); 
		}	
		*/
	}
	
	
	/**
	 * save user method
	 * 
	 * @access	public, static
	 * @param 	array $arrConfig
	 * @return 	void
	 */
	public static function save_userAction(array $arrRequestParams)
	{
		$objEditMenuUrl = $this->newActionUrl(array('save-user'));	
		
		// Add Menu Action: Make sure the group ID is passed in the URL --> /save-user/{:userId}
		if (
			(false === empty($arrRequestParams)) &&
			(count($arrRequestParams) >= 2) &&
			((int) $arrRequestParams[1]) &&
			($objMenu = MENU_GROUP::getInstance((int) $arrRequestParams[1])) &&
			($objMenu->getId())
		) {
			// Since this is called via a ajax request, we'll need to render the view here
			// Because the MVC does not render views on AJAX requests.
			$objEditMenuUrl = $this->newActionUrl(array('save-menu'));
			$this->setViewData('Menu_Group_Id', $objMenu->getId());
			$this->setViewData('Save_Menu_Url', $objEditMenuUrl->build());
			$this->renderOutput(array(
				'Request_Data' 	=> $this->getRequestData(),
				'View_Data'		=> $this->getViewData()
			)); 
		}	
	}
	
	/**
	 * Abstraction method: This method sets the config read from the config.ini file
	 * 
	 * @access	public, static
	 * @param 	array $arrConfig
	 * @return 	void
	 */
	public static function setIniConfig(array $arrConfig)
	{
		
	}
	
	/**
	 * Abstraction method, this method returns the module's display name
	 * 
	 * @access	public, static
	 * @return string
	 */
	public static function getDisplayName()
	{
		return ("User Manager");
	}
	
	/**
	 * Abstraction method, this method returns the module's sub menus
	 * 
	 * (non-PHPdoc)
	 * @see IMODULE_BASE::getSubMenuActions()
	 * @access	public, static
	 * @return 	array
	 */
	public static function getSubMenuActions()
	{
		return array();
	}
	
	/**
	 * Abstraction method, this method returns the module's output
	 * 
	 * (non-PHPdoc)
	 * @see IMODULE_BASE::renderOutput()
	 * @access	public
	 * @return 	void
	 */
		
	public function renderOutput(array $arrRequestParams)
	{
		$strViewFile = 'views/default.php';
		
		switch ($this->getRequest_Action())
		{
			case ADMIN_APPLICATION::ADMIN_APPLICATION_DEFAULT_ACTION : 
			{
				break;
			}
			case 'edit-user' : 
			{
				$strViewFile = 'views/edit-user.php';
				break;
			}
			case 'add-user' : 
			{
				$strViewFile = 'views/add-user.php';
				break;
			}
		}
		
		require_once($strViewFile);
	}
}