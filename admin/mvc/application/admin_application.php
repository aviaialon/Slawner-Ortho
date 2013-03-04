<?php
/**
 * ADMIN_APPLICATION Administration Class
 * This class represents the administration class, which extends the APPLICATION class
 *
 * @package		{ADMIN}
 * @subpackage	none
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
  class_exists('APPLICATION') 
		| require_once(__APPLICATION_ROOT__ . '/application.php');  
 
 interface_exists('IMODULE_BASE') 
		| require_once(__SITE_ROOT__ . '/admin/mvc/application/interface/imodule_base.php');  
		
 class ADMIN_APPLICATION extends APPLICATION 
 {
 	/**
 	 * These constants hold the default controller and action
 	 * 
 	 * @access protected
 	 * @var string
 	 */
 	const ADMIN_APPLICATION_DEFAULT_CONTROLLER 	= 'index';
 	const ADMIN_APPLICATION_DEFAULT_ACTION 		= 'index';
	
 	/**
 	 * This holds the available admin modules so that
 	 * we dont need to stat the modules folder everytime
 	 * 
 	 * @access protected
 	 * @var array
 	 */
 	protected static $arrAdminModules = false;
 	
 	/**
 	 * This holds the view data passed to the current view.
 	 * 
 	 * @access protected
 	 * @var array
 	 */
 	protected $arrViewData = array();
	
	/**
 	 * This holds a copy of the application instance
 	 * 
 	 * @access 	protected
 	 * @var 	ADMIN_APPLICATION
 	 */
 	protected static $CLASS_INSTANCE = NULL;
	
 	/**
	 * on After getInstance callback called by the APPLICATION parent
	 *
	 * @return void
	 */
 	protected final function onAfter_getInstance()
	{
		self::$CLASS_INSTANCE = $this;
		$this->setMemcache(new MEMCACHE_MANAGER());
	}
 	
	
	/**
	 * this method parses the MVC path and unsets it
	 * @return void
	 */
	protected final function parseMVCPath()
	{
		/**
		 * Set the defaults
		 */
		$this->setRequest_Controller(self::ADMIN_APPLICATION_DEFAULT_CONTROLLER);
		$this->setRequest_Action(self::ADMIN_APPLICATION_DEFAULT_ACTION);
		$this->setRequest_Params(array());
		
		if (
			(TRUE  === isset($_GET['mvc-path'])) &&
			(FALSE === empty($_GET['mvc-path']))
		) {
			$arrMVCPath = explode('/', $_GET['mvc-path']);
			$arrMVCParameters = array();
			
			while (list($intIndex, $strValue) = each($arrMVCPath)) 
			{ 
				if (strlen($strValue)) 
				{
					switch ((int) $intIndex)
					{
						case 0 : 
						{
							
							$this->setRequest_Controller($strValue);
							break;
						}
						case 1 : 
						{
							$this->setRequest_Action($strValue);
							break;
						}
						default : 
						{
							$arrMVCParameters[] = $strValue;
						}
					}
				}
			}
			
			$this->setIsMVCPathParsed(true);
			$this->setRequest_Params($arrMVCParameters);
		
			unset($_GET['mvc-path']);
		}
	}
	
	/**
	 * This method dispatches the request!
	 * 
	 * @return void
	 */
	 public final function dispatchRequest()
	 {
	 	// Parse the MVC request
		$this->parseMVCPath();
		
	 	$strRequestController 	= $this->getRequest_Controller();
		$strRequestAction		= $this->getRequest_Action();
		$arrRequestParams		= $this->getRequest_Params(); 	
		$strControllerFile 		= __SITE_ROOT__ . '/admin/mvc/modules/' . strtolower(($strRequestController ? $strRequestController : 'index')) . '/controller.php';
		$strIniConfigFile 		= __SITE_ROOT__ . '/admin/mvc/modules/' . strtolower(($strRequestController ? $strRequestController : 'index')) . '/config.ini';
		$strControllerClass		= strtoupper($strRequestController) . '_CONTROLLER';

	 	$strActionMethod	= (
			(FALSE === empty($strRequestAction)) ? 
			str_replace(array(' ', '-'), '_', ucwords($strRequestAction)) . 'Action' : 'indexAction'
		);
		
		if (TRUE === file_exists($strControllerFile))
		{
			require_once($strControllerFile);
			
			if (class_exists($strControllerClass))
			{
				// Create the object
				$objController = new $strControllerClass();
				
				// Check for an INI configuration file.
				$arrIniConfig = array();
				if (
					(TRUE === file_exists($strIniConfigFile)) &&
					(TRUE === is_readable($strIniConfigFile))
				) {
					$arrIniConfig = parse_ini_file($strIniConfigFile);
					$objController->setIniConfig($arrIniConfig);
				}
				
				// Call the action
				if (TRUE === method_exists($objController, $strActionMethod))
				{
					call_user_func(array($objController, $strActionMethod), $arrRequestParams);
					
					// On a successful call and if its a AJAX request, we exit
					if (true === $this->getRequestDispatcher()->isXHTTPRequest())
					{
						exit();
					}
				}
				else
				{
					// 
					// 404 ... Action doesnt exists!
					//
					die('OOppss.... 404!: ' . $strActionMethod);
				}
			} 
			else
			{
				// 
				// 404 ... Controller exists, but the class name isnt spellt properly
				//
				die('OOppss.... 404!: ' . $strControllerClass);
			}
			
		}
		else
		{
			//
			// 404 ... Controller doesnt exists!
			//
			die('OOppss.... 404!: ' . $strControllerFile);
		}
	 }
	 
	 /**
	  * This method returns the applications current request data
	  * 
	  * @return array
	  */
	 public final function getRequestData()
	 {
	 	if (FALSE === $this->getIsMVCPathParsed())
		{
			// Parse the MVC request
			$this->parseMVCPath();
		}
	 	return(array(
			'CONTROLLER' =>	 $this->getRequest_Controller(),
			'ACTION' 	 =>	 $this->getRequest_Action(),
			'PARAMS' 	 =>	 $this->getRequest_Params()
		));
	 }
	 
	 /**
	  * This method returns the current controllers base URL without the parameters (does not add the index Controller and Action)
	  * 
	  * @return URL
	  */
	 
	 public function getCurrentBaseUrl()
	 {
	 	$strBasePath = (
			strcmp($this->getRequest_Controller(), self::ADMIN_APPLICATION_DEFAULT_CONTROLLER) <> 0 ? 
			$this->getRequest_Controller() : ''
		);
		$strBasePath .= (strlen($strBasePath) ? '/' . (
			strcmp($this->getRequest_Action(), self::ADMIN_APPLICATION_DEFAULT_ACTION) <> 0 ? 
			$this->getRequest_Action() : ''
		) : '');
		
	 	$objBaseUrl = new URL();
		$objBaseUrl->setPath($strBasePath);
		$objBaseUrl->clearAttribute();
		$objBaseUrl->setAttribute('authToken', $this->getForm()->getUrlParam('authToken'));
	 	return ($objBaseUrl->build());
	 }
	 
	 /**
	  * This method returns the current controllers base URL without the parameters
	  * 
	  * @return URL
	  */
	 
	 public function getCurrentUrl()
	 {
	 	$objBaseUrl = new URL();
		$objBaseUrl->setPath($this->getRequest_Controller() . '/' . $this->getRequest_Action());
		$objBaseUrl->clearAttribute();
		$objBaseUrl->setAttribute('authToken', $this->getForm()->getUrlParam('authToken'));
		return ($objBaseUrl->build());
	 }
	 
 	/**
	  * This method returns a new action URL. It starts from the current URL (controller)
	  * 
	  * @return URL
	  */
	 
	 public function newActionUrl(array $arrPath = array(), array $arrUrlParams = NULL)
	 {
		$objApplication = self::$CLASS_INSTANCE;
		if (FALSE == is_object($objApplication))
		{
			$objApplication = self::getInstance();
		}
		
	 	$arrUrlPath = implode('/', $arrPath);
	 	$objMenuUrl = new URL();
		$objMenuUrl->setPath($objApplication->getRequest_Controller() . '/' . $arrUrlPath);
		$objMenuUrl->clearAttribute();
		if (FALSE === is_null($arrUrlParams)) {
			$objMenuUrl->setAttributeFromArray($arrUrlParams);	
		}
		$objMenuUrl->setAttribute('authToken', $objApplication->getForm()->getUrlParam('authToken'));
		
		return ($objMenuUrl);
	 }
	 
	 /**
	  * This method returns the current view data
	  * 
	  * @return mixed
	  */
	 
	 public function getViewData($strDataIndex = NULL)
	 {
		$AdminApplicationInstance = self::$CLASS_INSTANCE;
		if (FALSE == is_object($objApplication))
		{
			$AdminApplicationInstance = self::getInstance();
		}
		
	 	$mxReturnData = false;
		
	 	if (is_null($strDataIndex))
		{
			$mxReturnData = $AdminApplicationInstance->arrViewData;
		}
		else if (FALSE === empty($AdminApplicationInstance->arrViewData[$strDataIndex]))
		{
			$mxReturnData = $AdminApplicationInstance->arrViewData[$strDataIndex];
		}
		return ($mxReturnData);
	 }
	 
	 public function setViewData($strViewDataName, $mxViewData = NULL)
	 {
		$AdminApplicationInstance = self::$CLASS_INSTANCE;
		if (FALSE == is_object($objApplication))
		{
			$AdminApplicationInstance = self::getInstance();
		}
		
	 	$AdminApplicationInstance->arrViewData[$strViewDataName] = $mxViewData;
	 }
	 
 	/** 
 	 * This method returns the array of available modules to the admin panel
	 * Starting from the /admin/ root path
	 *
	 * returns an array: Display Name => array(Include Path, Class Name)
 	 * @return array the modules array
 	 */
	public static final function getAdminModules()
	{
		$Application = self::$CLASS_INSTANCE;
		if (FALSE == is_object($objApplication))
		{
			$Application = self::getInstance();
		}
		
		/**
		 * This is an array of paths / widgets to ignore	
		 * @var array
		 */
		if (FALSE === is_array(self::$arrAdminModules))
		{
			$arrIgnorePaths 	= array(
				'.svn'
			);
			$strModuleDirectory = __SITE_ROOT__ . "/admin/mvc/modules/";
			self::$arrAdminModules = array();
			
			// Open a known directory, and proceed to read its contents
			if (is_dir($strModuleDirectory))
			{
				if ($resDirectoryHandle = opendir($strModuleDirectory))
				{
					while (($dirModuleName = readdir($resDirectoryHandle)) !== false)
					{
						if (
							($dirModuleName !== '.') &&
							($dirModuleName !== '..') &&
							(is_dir($strModuleDirectory . $dirModuleName)) &&
							(FALSE === in_array($dirModuleName, $arrIgnorePaths))
						) {
							//$arrPathInfo = pathinfo($strFileName);
							if (FALSE === empty($dirModuleName))
							{
								require_once($strModuleDirectory . $dirModuleName . DIRECTORY_SEPARATOR . 'controller.php');
								
								$strClassPath 	= $strModuleDirectory . $dirModuleName . DIRECTORY_SEPARATOR . 'controller.php';
								$strClassName	= strtoupper($dirModuleName) . '_CONTROLLER';

								$strDisplayName = call_user_func_array(array($strClassName, 'getDisplayName'), array()); 
								
								// Check if were at the current place:
								$strCurrentController 	= strtoupper($Application->getRequest_Controller());
								$strClassCompare	 	= strtoupper(substr($strClassName, 0, strlen($strClassName) - 11));
								$blnIsCurrent			= (bool) (strcmp($strCurrentController, $strClassCompare) === 0);
					
								self::$arrAdminModules[$dirModuleName] = array(
									'SCRIPT_PATH' 	  => $strClassPath,
									'CLASS_NAME'	  => $strClassName,
									'DISPLAY_NAME'	  => $strDisplayName,
									'CONTROLLER_PATH' => strtoupper($dirModuleName),
									'IS_CURRENT'	  => $blnIsCurrent
								);
								
								// set the current active module
								if ($blnIsCurrent)
								{
									$Application->setCurrentActiveModule(new $strClassName());
								}
							}
						}
					}
					closedir($resDirectoryHandle);
				}
			}
		}
		
		
		return (self::$arrAdminModules);
	}
	
	
	/**
	 * Force users to be authenticated on a minimum of admin level clearence (SITE_USERS_ROLE_ADMIN_USER) if not already set
	 */
	public function onWebControllerInitiate() 
	{
		$Application = parent::getInstance();
		$Application->getUser()->requireLogin(SITE_USERS_ROLE_ADMIN_USER);
	}
	
	
	/**
	 * Properly returns an application instance
	 *
	 * @return 	ADMIN_APPLICATION
	 */
	public static final function getApplicationInstance()
	{
		$AdminApplicationInstance = self::$CLASS_INSTANCE;
		if (FALSE == is_object($objApplication))
		{
			$AdminApplicationInstance = self::getInstance();
		}
		
		return ($AdminApplicationInstance);
	}
 }