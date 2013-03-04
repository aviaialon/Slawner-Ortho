<?php
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::MENU::MENU");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::MENU::MENU_GROUP");

class MENU_MANAGER_CONTROLLER extends ADMIN_APPLICATION implements IMODULE_BASE
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
		$strMenuUlHtml 		= '<ul id="easymm"></ul>';	 
		$intMenuGroupId		= 1;
		
		if (
			(FALSE === empty($arrRequestParams)) &&
			(TRUE  === (count($arrRequestParams) > 1)) &&
			(TRUE  === (strtolower($arrRequestParams[0]) === 'edit-group'))
		) {
			$intMenuGroupId	= (
				(int) $arrRequestParams[1] >= 1 ?
				(int) $arrRequestParams[1] : 1
			);
		}
		
		$objMenuTree	= MENU_TREE::getInstance();
		$objMenuGroup 	= MENU_GROUP::getInstance($intMenuGroupId);
		$arrMenuGroups	= MENU_GROUP::getObjectClassView(array(
			'direction' => 'ASC'
		));
		$arrPageMenu 	= MENU::getObjectClassView(array(
			'cacheQuery'=> 	false,	
			'filter' 	=>	array(
				'group_id' 	=> ($objMenuGroup->getId() ? $objMenuGroup->getId() : 1)
			),
			'orderBy'	=> 'parent_id ASC, position',
			'direction'	=> 'ASC'
		));
		#$intMenuGroupId = ($objMenuGroup->getId() ? $objMenuGroup->getId() : 1);
		
		while (list($intMenuIndex, $arrMenuData) = each($arrPageMenu))
		{
			$objMenuTree->add_row(
				(int) $arrMenuData['id'],
				(int) $arrMenuData['parent_id'],
				' id="menu-'. $arrMenuData['id']. '" class="sortable_menu_list" ',
				'<div class="ns-row ns-row-dark">' .
					'<div class="ns-title">' . $arrMenuData['title'].'</div>' .
					'<div class="ns-url">'.$arrMenuData['url'].'</div>' .
					'<div class="ns-class">'.$arrMenuData['class'].'</div>' .
					'<div class="ns-actions">' .
						
						'<a href="#" title="" menuId="' . (int) $arrMenuData['id'] . '" class="edit-menu btn14 leftDir btnLessPad" ' .
						'	original-title="Edit This Menu"><img src="/admin/static/images/icons/dark/pencil.png" alt=""></a>' .
						
						'<a href="#" title="" menuId="' . (int) $arrMenuData['id'] . '" class="delete-menu btn14 leftDir btnLessPad" ' .
						'	original-title="Delete This Menu"><img src="/admin/static/images/icons/dark/trash.png" alt=""></a>' .

						'<input class="leftDir" type="checkbox" id="del_' . (int) $arrMenuData['id'] . '" name="del' . (int) $arrMenuData['id'] . 
							'" value="' . (int) $arrMenuData['id'] . '" original-title="Add to Delete Queue" />'.
				
						'<input type="hidden" name="menu_id" value="'.$arrMenuData['id'].'">' .
					'</div>' .
				'</div>'
			);
		}
		
		$strMenuUlHtml = $objMenuTree->generate_list('id="easymm"');
		$strMenuGroupTitle = $objMenuGroup->getTitle();
		
		// Create the save menu URL
		$strSaveMenuUrl = $this->newActionUrl(array('save-position'))->build();

		// Assign the view data
		$this->setViewData('Current_Url',  $this->getCurrentUrl());
		$this->setViewData('Resource_Path', '/admin/mvc/modules/menu_manager/');
		$this->setViewData('Array_Menu_Groups', $arrMenuGroups);
		$this->setViewData('Int_Current_Active_Group', $intMenuGroupId);
		$this->setViewData('Str_Current_Active_Group_Title', $strMenuGroupTitle);
		$this->setViewData('Str_Save_Menu_Position_Url', $strSaveMenuUrl);
		$this->setViewData('Str_Menu_Html', $strMenuUlHtml);
	
	}
	
	/**
	 * Module edit menu action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function edit_menuAction(array $arrRequestParams)
	{
		if (
			(false === empty($arrRequestParams)) &&
			((int) $arrRequestParams[0]) &&
			($objMenu = MENU::getInstance((int) $arrRequestParams[0])) &&
			($objMenu->getId())
		) {
			// Since this is called via a ajax request, we'll need to render the view here
			// Because the MVC does not render views on AJAX requests.
			$objEditMenuUrl = $this->newActionUrl(array('save-menu', $objMenu->getId()));
			
			$this->setViewData('Save_Menu_Url', $objEditMenuUrl->build());
			$this->setViewData('Arr_Current_Menu', $objMenu->getVariable());
			$this->renderOutput(array(
				'Request_Data' 	=> $this->getRequestData(),
				'View_Data'		=> $this->getViewData()
			)); 
		}
	}
	
	
	/**
	 * Module Delete menu action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function menu_deleteAction(array $arrRequestParams)
	{
		if (
			(false === empty($arrRequestParams)) &&
			((int) $arrRequestParams[0]) &&
			($objMenu = MENU::getInstance((int) $arrRequestParams[0])) &&
			($objMenu->getId())
		) {
			$intMenuGroup = (int) $objMenu->getGroup_Id();
			$this->getDatabase()->query(
				"DELETE FROM " . strtolower(get_class($objMenu)) . " " . 
				"WHERE (id = " . (int) $objMenu->getId() . " OR " .
				"parent_id = " . (int) $objMenu->getId() . ")"
			);
			MENU::clearMenuGroupCache($intMenuGroup);
			
			header('Content-type: application/json');
			echo json_encode(array(
				'success' => true
			));
		}
	}
	
	/**
	 * Add menu action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function add_menuAction(array $arrRequestParams)
	{
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
	}
	
	/**
	 * Module save menu action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function save_menuAction(array $arrRequestParams)
	{
		// Save Edit Menu Action
		if (
			(false === empty($arrRequestParams)) &&
			((int) $arrRequestParams[0]) &&
			($objMenu = MENU::getInstance((int) $arrRequestParams[0])) &&
			($objMenu->getId())
		) {
			// JSON Response
			$arrJsonResponse = array();
			$arrAllFields 	= $this->getForm()->getAllFields();
			
			if (FALSE === empty($arrAllFields))
			{
				$objMenu->setTitle((string) $this->getForm()->getField('title'));
				$objMenu->setUrl((string) 	$this->getForm()->getField('url'));
				$objMenu->setClass((string) $this->getForm()->getField('class'));
				
				$blnSuccess 				= (bool) $objMenu->save();
				$arrJsonResponse['status'] 	= ($blnSuccess ? 1 : 2);
				if (FALSE === $blnSuccess)
				{
					$arrJsonResponse['msg'] = 'Edit menu error.';
				}
				else
				{
					$arrJsonResponse['menu'] = $objMenu->getVariable();
				}
				
				header('Content-type: application/json');
				echo json_encode($arrJsonResponse);
			}
		}
		
		
		// Add a New Menu Action
		if (
			($this->getForm()->getField('menu_type')) 	&&
			($this->getForm()->getField('title')) 		&&
			((int) $this->getForm()->getField('menu_group_id')) 
		) {
			$objNewMenu = MENU::newInstance();
			$objNewMenu->setTitle($this->getForm()->getField('title'));
			$objNewMenu->setUrl($this->getForm()->getField('url'));
			$objNewMenu->setClass($this->getForm()->getField('class'));
			$objNewMenu->setGroup_Id((int) $this->getForm()->getField('menu_group_id'));
			$objNewMenu->save();
			
			$arrJsonResponse = array();
			
			// Build the return data
			$arrJsonResponse['status'] 	= 1;
			$arrJsonResponse['li_id'] 	= 'menu-'. $objNewMenu->getId();
			$arrJsonResponse['li'] 		= '<li id="menu-'. $objNewMenu->getId() . '" class="sortable_menu_list"> ' .
											'<div class="ns-row ns-row-dark">' .
												'<div class="ns-title">' . $objNewMenu->getTitle() .'</div>' .
												'<div class="ns-url">'. $objNewMenu->getUrl() .'</div>' .
												'<div class="ns-class">'. $objNewMenu->getClass() .'</div>' .
												'<div class="ns-actions">' .
													
													'<a href="#" title="" menuId="' . (int) $objNewMenu->getId() . '" class="edit-menu btn14 leftDir btnLessPad" ' .
													'	original-title="Edit This Menu"><img src="/admin/static/images/icons/dark/pencil.png" alt=""></a>' .
													
													'<a href="#" title="" menuId="' . (int) $objNewMenu->getId() . '" class="delete-menu btn14 leftDir btnLessPad" ' .
													'	original-title="Delete This Menu"><img src="/admin/static/images/icons/dark/trash.png" alt=""></a>' .
													
													'<input class="leftDir" type="checkbox" id="del_' . (int) $arrMenuData['id'] . '" name="del' . (int) $arrMenuData['id'] . 
														'" value="' . (int) $arrMenuData['id'] . '" original-title="Add to Delete Queue" />'.
			
													'<input type="hidden" name="menu_id" value="'. $objNewMenu->getId() .'">' .
												'</div>' .
											'</div>';
			
			header('Content-type: application/json');
			echo json_encode($arrJsonResponse);
		}
	}
	
	
	/**
	 * Module save menu position action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function save_positionAction(array $arrRequestParams)
	{
		$arrFormData 	= (array) $this->getForm()->getField('easymm');
		$intGroupId 	= (int) $this->getForm()->getField('groupId');
		if (false === empty($arrFormData)) 
		{
			$intParent = 0;
			MENU::updateMenuPosition($intParent, $arrFormData);
			MENU::clearMenuGroupCache($intGroupId);
		}
		/*
	function save_position() {
		if (isset($_POST['easymm'])) {
			$easymm = $_POST['easymm'];
			$this->update_position(0, $easymm);
		}
	}

	
	function update_position($parent, $children) {
		$i = 1;
		foreach ($children as $k => $v) {
			$id = (int)$children[$k]['id'];
			$data[MENU_PARENT] = $parent;
			$data[MENU_POSITION] = $i;
			$this->db->update(MENU_TABLE, $data, MENU_ID . ' = ' . $id);
			if (isset($children[$k]['children'][0])) {
				$this->update_position($id, $children[$k]['children']);
			}
			$i++;
		}
	}
		
		if (
			(false === empty($arrRequestParams)) &&
			((int) $arrRequestParams[0]) &&
			($objMenu = MENU::getInstance((int) $arrRequestParams[0])) &&
			($objMenu->getId())
		) {
			// JSON Response
			$arrJsonResponse = array(
				'menu' => $objMenu->getVariable()
			);
			$arrAllFields 	= $this->getForm()->getAllFields();
			
			if (FALSE === empty($arrAllFields))
			{
				$objMenu->setTitle((string) $this->getForm()->getField('title'));
				$objMenu->setUrl((string) 	$this->getForm()->getField('url'));
				$objMenu->setClass((string) $this->getForm()->getField('class'));
				
				$blnSuccess 				= (bool) $objMenu->save();
				$arrJsonResponse['status'] 	= ($blnSuccess ? 1 : 2);
				if (FALSE === $blnSuccess)
				{
					$arrJsonResponse['msg'] = 'Edit menu error.';
				}
				
				header('Content-type: application/json');
				echo json_encode($arrJsonResponse);
			}
		}
		*/
	}
	
	
	private static function update_position($parent, $children) 
	{
		$i = 1;
		foreach ($children as $k => $v) 
		{
			$objMenu = MENU::getInstance((int) $children[$k]['id']);
			if ($objMenu->getId())
			{
				$objMenu->setParent_Id($parent);
				$objMenu->setPosition($i);
				$objMenu->save();
			}
			if (isset($children[$k]['children'][0])) 
			{
				self::update_position($id, $children[$k]['children']);
			}
			$i++;
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
		return ("Menu Manager");
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
		$strViewFile = 'assets/views/menu.php';
		
		switch ($this->getRequest_Action())
		{
			case ADMIN_APPLICATION::ADMIN_APPLICATION_DEFAULT_ACTION : 
			{
					$strViewFile = 'assets/views/menu.php';
				break;
			}
			case 'edit-menu' : 
			{
					$strViewFile = 'assets/views/menu-edit.php';
				break;
			}
			case 'add-menu' : 
			{
					$strViewFile = 'assets/views/add-menu.php';
				break;
			}
		}
		
		require_once($strViewFile);
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
}