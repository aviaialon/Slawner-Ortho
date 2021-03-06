<?php
	/**
	 * REQUEST_DISPATCHER Administration Class
	 * This class handles the requests made to the server and dispatched them accordingly
	 *
	 * @package		{APPLICATION_ROOT}::HTTP::REQUEST_DISPATCHER
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 
	 /**
	  * Dependancies:
	  *		- class SHARED_OBJECTS is loaded from ini.php
	  */
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGES");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_META");
	 
	 // Include base classes
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::INTERFACE::PARTIAL_BASE");
	 
	 class REQUEST_DISPATCHER {
		/**
		 * The REQUEST_DISPATCHER static singleton instance
		 * @type:	REQUEST_DISPATCHER
		 * @access:	protected
		 */
		 protected static $REQUEST_DISPATCHER_INSTANCE = NULL;
		 
		/**
		 * The URL request array
		 * @type:	Array
		 * @access:	protected
		 */
		protected static $ARR_REQUEST_DATA 	= NULL;
		
		/**
		 * The URL request string
		 * @type:	String
		 * @access:	protected
		 */
		protected static $STR_REQUEST_PATH 	= NULL;
		
		/**
		 * The parsed controller data
		 * @type:	String
		 * @access:	protected
		 */
		protected static $ARR_DISPATCH_DATA 	= NULL;
		
		/**
		 * The view variables data
		 * @type:	String
		 * @access:	protected
		 */
		protected static $ARR_VIEW_DATA 	= NULL;
		
		/**
		 * assignes weather or not to cache the 
		 * current view - defaults to false
		 * @type:	Booleam
		 * @access:	protected
		 */
		protected static $VIEW_CACHEABLE 	= false;
		
		/**
		 * Class constructor
		 * @access:	protected
		 * @param:	none
		 * @return: REQUEST_DISPATCHER
		 */
		protected function __construct() { 
			self::getRequestData();
		}
		
		/**
		 * This function returns the Application instance
		 * @access:	protected
		 * @param:	none
		 * @return: APPLICATION
		 */
		protected final function getApplication() { 
			return (APPLICATION::getInstance());
		}
		
		/**
		 * This method neutralises a url request part so it can be used in the process.
		 * Replaces spaces (multiples also) with an underscore 
		 * and replaces dashes (multiples also) with unserscores
		 *
		 * @access:	public, static
		 * @param: 	$strData - The string to newtralise
		 * @return:	$strData - The newtralised data
		 */
		public static function neutralise($strData = NULL) {
			$strData = preg_replace("/\s\s+/", "_", $strData);
			$strData = preg_replace("/-+/", "_", $strData);
			return ($strData);
		}
		
		/**
		 * This method returns the URL request array
		 * @access:	public, static
		 * @param: 	none
		 * @return:	array - The Url request array
		 */
		public static function getRequestData() {
			$arrRequestData = array();
			if (! is_array(self::$ARR_REQUEST_DATA)) {
				
				// $arrDataIn = (isset($_GET['path']) && strlen($_GET['path'])) ? explode('/', $_GET['path']) : array();
				$arrDataIn = (isset($_REQUEST['path']) && strlen($_REQUEST['path'])) ? explode('/', $_REQUEST['path']) : array();
				
				// Clean the incomming array because of the first '/' will create an empty entry in the array
				foreach($arrDataIn as $intIndex => $strData) {
					if (! strlen($strData) || is_null($strData)) {
						unset($arrDataIn[$intIndex]);	
					}
				}
				self::$ARR_REQUEST_DATA = $arrDataIn;
			}
			$arrRequestData = self::$ARR_REQUEST_DATA;
			return ((array) $arrRequestData);
		}
		
		/**
		 * This method returns the URL request as a string
		 * @access:	public, static
		 * @param: 	none
		 * @return:	string - The Url request string
		 */
		public static final function getRequestPath() {
			if (is_null(self::$STR_REQUEST_PATH)) {
				/*
				self::$STR_REQUEST_PATH = (
					(isset($_GET['path']) && strlen($_GET['path'])) ? 
					(
						$_GET['path'][strlen($_GET['path'])-1] == '/' ? 
						substr($_GET['path'], 0, strlen($_GET['path']) - 1) : 
						$_GET['path']
					) : NULL						   
				);
				*/
				self::$STR_REQUEST_PATH = (
					(isset($_REQUEST['path']) && strlen($_REQUEST['path'])) ? 
					(
						$_REQUEST['path'][strlen($_REQUEST['path'])-1] == '/' ? 
						substr($_REQUEST['path'], 0, strlen($_REQUEST['path']) - 1) : 
						$_REQUEST['path']
					) : NULL						   
				);
				
				if (substr(self::$STR_REQUEST_PATH, 0, 1) != '/') {
					self::$STR_REQUEST_PATH = '/' . self::$STR_REQUEST_PATH;	
				}
					
			}
			return ((string) strtolower(self::$STR_REQUEST_PATH));
		}
		
		/**
		 * This method returns the static instance for the request dispatcher
		 * @access:	public static
		 * @param:	none
		 * @return: REQUEST_DISPATCHER $objRequestDistacher - The request dispatcher instance
		 */
		public static final function getInstance() {
			if (
				(! is_object(self::$REQUEST_DISPATCHER_INSTANCE)) ||
				(! is_a(self::$REQUEST_DISPATCHER_INSTANCE, __CLASS__))
			) {
				self::$REQUEST_DISPATCHER_INSTANCE = new REQUEST_DISPATCHER();
			} 
			return (self::$REQUEST_DISPATCHER_INSTANCE);
		}
		/**
		 * This method re-dispatches the route to a new controllers (delegates the route to a new controller)
		 * @access:	protected final
		 * @param:	array - The nre request path to dispatch ex array('users', 'login')
		 * @return: REQUEST_DISPATCHER $objRequestDistacher - The request dispatcher instance
		 */
		protected final function delegateRoute(array $arrRoute = array())
		{
			self::$ARR_REQUEST_DATA = $arrRoute;
			$Application = APPLICATION::getInstance();	
			$Application->getRequestDispatcher()->dispatch();
		}
		
		/**
		 * This method returns if the current request is HTTPX (Ajax)
		 * @access:	public final
		 * @param:	none
		 * @return: Boolean
		 */
		public final function isXHTTPRequest()
		{
			return (
				(bool)	
				((isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && 
				(strcmp(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') == 0))		
			);	
		}
		
		/**
		 * This method executes a partial. 
		 * @param string $strPartialRoute - The partial (static) route starting from 
		 * 									{__APP_PARTIAL_DIR__} see config.php Ex: TEST::MENU
		 * 									will render {__APP_PARTIAL_DIR__}/test/menu.php
		 * @param array  $arrParam		  - Array of parameters to send to the partial
		 */
		public final function renderPartial($strPartialRoute = FALSE, $arrParam = array(), $blnUsePartialDir = TRUE)
		{
			if (FALSE === empty($strPartialRoute))
			{
				$arrPartial 		= explode('::', $strPartialRoute);
				$strClassname		= end($arrPartial);
				$strPartialClass 	= 'PARTIAL_' . strtoupper($strClassname);
				$strExecRoute 		= ($blnUsePartialDir ? __APP_PARTIAL_DIR__ . DIRECTORY_SEPARATOR : '') . str_replace('::', DIRECTORY_SEPARATOR, $strPartialRoute) . '.php';
				$strExecRoute		= strtolower($strExecRoute);
				
				if (FALSE === @include_once($strExecRoute)) 
				{
					SITE_EXCEPTION::raiseException('Partial: <strong>' . $strPartialRoute . '</strong> does not exists. [' . $strExecRoute . ']');
				}
				
				if (FALSE === class_exists($strPartialClass))
				{
					SITE_EXCEPTION::raiseException('Please define class <b>' . $strPartialClass . '</b> in Partial: <b>' . $strPartialRoute . '</b>.');
				}
				
				$objPartial = new $strPartialClass();
				
				if (
					(FALSE === method_exists($objPartial, 'execute')) ||
					(FALSE === method_exists($objPartial, 'render'))
				) {
					SITE_EXCEPTION::raiseException('Please define methods <b>execute() and render()</b> in Partial: <b>' . $strPartialRoute . '</b>.');
				}
				
				$objPartial->execute($arrParam);
				$objPartial->signature();
				$objPartial->render();
			}
		} 
		
		/**
		 * Entry method. - Dispatches the request
		 */
		public final function dispatch() 
		{
			$arrRequestData		= self::getRequestData();
			$blnError 			= false;
			
			/**
			 * Build the dispatch actions array
			 */
			$arrModelControllerActions = array(
				'MODEL'			=> NULL,
				'CONTROLLER'	=> NULL,
				'ACTION'		=> NULL,
				'VIEW'			=> NULL,
			 	'COMPRESSION'	=> true,
				'PARAMS'		=> array()
			);
			
			if (count($arrRequestData)) {
			 	/**
				 * Get the view file:
				 *		- We loop only the 2 first items becase: <-- @depreciated (loop through all of them)
				 *		- The first param is the folder 
				 *		- Second param is the view file (defaults to index.php); 
				 */
				$arrViewFileData = $arrRequestData;
				
				//  Remove possible URL params from the array
				foreach ($arrViewFileData as $intIndex => $strViewVal) {
					if ((bool) strpos($strViewVal, ':')) { 
						$arrData 		= explode(':', $strViewVal);
						$strDataKey 	= array_shift($arrData);
						$strDataValue 	= array_shift($arrData);
						$arrTempRequestData = array_flip($arrRequestData);
						$this->setRequestParam($strDataKey, $strDataValue);
						unset($arrViewFileData[$intIndex]);
						unset($arrTempRequestData[$strDataKey . ':' . $strDataValue]);
						$arrRequestData = array_flip($arrTempRequestData);
					} 
				}
				/*
				array_walk($arrViewFileData, function($strViewVal, $intIndex) use(&$arrViewFileData, &$arrRequestData) { 
					if ((bool) strpos($strViewVal, ':')) { 
						$arrData 		= explode(':', $strViewVal);
						$strDataKey 	= array_shift($arrData);
						$strDataValue 	= array_shift($arrData);
						$arrTempRequestData = array_flip($arrRequestData);
						REQUEST_DISPATCHER::setRequestParam($strDataKey, $strDataValue);
						unset($arrViewFileData[$intIndex]);
						unset($arrTempRequestData[$strDataKey . ':' . $strDataValue]);
						print $strDataKey . ':' . $strDataValue . '<br>';
						$arrRequestData = array_flip($arrTempRequestData);
					} 
				});
				*/
				
				$arrModelControllerActions['VIEW'] .= strtolower(self::neutralise(array_shift($arrViewFileData)) . '/' . (
					(FALSE === empty($arrViewFileData)) ? (end($arrViewFileData)) : 'index'
				));
				/*
				 * Old logic
				 *
				for ($intI = 0; $intI <= 1; $intI++) {
					if (! empty($arrRequestData[$intI])) {
						$arrModelControllerActions['VIEW'] .= self::neutralise($arrRequestData[$intI]) . (
							(! empty($arrRequestData[$intI+1]) && $intI < 1) ? '/' : ''
						);
					}
				}
				*/
				
			 	if (! empty($arrModelControllerActions['VIEW'])) {
					$arrModelControllerActions['VIEW'] .= '.php';
				}
								
				/**
				 * Get the controller
				 */
				$arrModelControllerActions['MODEL'] 	 = strtolower((count($arrRequestData) ? self::neutralise(array_shift($arrRequestData)) : 'index'));
				$arrModelControllerActions['CONTROLLER'] = strtoupper($arrModelControllerActions['MODEL'] . '_CONTROLLER');
				$arrModelControllerActions['CONTROLLER_NAME'] = strtoupper($arrModelControllerActions['MODEL']);

				/**
				 * Get the action and make sure its not a get parameter
				 */
				$arrModelControllerActions['ACTION'] = ((count($arrRequestData) && (FALSE === strpos(current($arrRequestData), ':'))) ? self::neutralise(array_shift($arrRequestData)) : 'index') . 'Action';
				$arrModelControllerActions['ACTION_NAME'] = substr($arrModelControllerActions['ACTION'], 0, (strlen($arrModelControllerActions['ACTION']) - 6));
					
				
				/**
				 * Get the parameters
				 */
				$arrModelControllerActions['PARAMS'] = self::parseRequestParam($arrRequestData); 
				$arrModelControllerActions['PARAMS'] = self::getRequestParams(); 
				
				// Depreciated, moved to self::parseRequestParam();
				// Set the URL variables...
				// This segment will take friendly URLs (varName:Value) into URL variables
				/*
				while (list($intIndex, $strUrlParam) = each($arrRequestData))
				{
					$arrUrlParams = explode(':', $strUrlParam);
					if (FALSE === empty($arrUrlParams))
					{
						$strUrlParamName 	= array_shift($arrUrlParams);
						$strUrlParamValue 	= array_shift($arrUrlParams);
						$_GET[$strUrlParamName] = $strUrlParamValue;
					}
				}
				*/
			} else {
			 	/**
				 * Index Controller, index action, index view
				 */
				 $arrModelControllerActions = array(
					'CONTROLLER'	=> 'INDEX_CONTROLLER',
					'ACTION'		=> 'indexAction',
					'MODEL'			=> 'index',
					'VIEW'			=> 'index',
				 	'COMPRESSION'	=> true,
					'PARAMS'		=> array()
				 );
			}
			
			/**
			 * Internal storage of the request dispatch array
			 */
			self::$ARR_DISPATCH_DATA = $arrModelControllerActions; 
			
			/**
			 * Begin the dispatch process
			 */
			 
			 /**
			  * Here, we check for a core component callback
			  * 
			  * 	Components callbacks URLs are constructed as follows:
			  * 		http://www.domain.com/callback/{path seperated by -}/{method}/{param}/{param}/
			  * 
			  * 		Methods must be protected.
			  * 		Params passed to the method are:
			  * 			1 (array) the parameters set in the request path
			  * 			2 (array) the dispatcher request
			  */
			 if (strtolower($this->getVariable('MODEL', self::$ARR_DISPATCH_DATA)) === 'callback')
			 {
				// Get the decryption tool
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::CRYPT::AES_CRYPTO");
				$objCrypto = AES_CRYPTO::getInstance();
				 
			 	// Disable the default view action
			 	$this->assignNoView();
			 	
				// Re-route the content here from a callback method
				// 1. we use the action to determine what core component were calling
				$arrCallbackRequest = (array) self::getRequestData();
				
				// use array_shift to auto remove the first element because we dont use the 'callback' url element
				array_shift($arrCallbackRequest); 
				
				// Here, we need to decrypt the request since it comes in encrypted
				$arrDecryptedRequestData = explode('/', $objCrypto->decrypt(base64_decode(array_shift($arrCallbackRequest))));
				
				$arrCallbackRequest 	 = $arrDecryptedRequestData;
				
				if (count($arrCallbackRequest) >= 3) 
				{
					 // Gather the controller information
					$arrControllerInfo = explode('-', array_shift($arrCallbackRequest));
					/*
					 * Commented. This part was removed because the component can be rooted anywhere, not only in the {APPLICATION_ROOT}
					 *
					// Now parse the component include path
					$strControllerFilePath = constant('__APPLICATION_ROOT__') . 
										 	 constant('DIRECTORY_SEPARATOR').
										 	 self::neutralise(implode(DIRECTORY_SEPARATOR, $arrControllerInfo)) . '.php';
					*/	
					//
					// We added the trailing slash because the path of the file should start from the root. and because
					// during the processing of the file name, we explode on the '/' character, we loose the first and last
					// slashes. So the path is no longer absolute and become relative.
					$strControllerFilePath = '/' . self::neutralise(implode(DIRECTORY_SEPARATOR, $arrControllerInfo)) . '.php';	

					// Set the model and controller request data
					self::$ARR_DISPATCH_DATA['MODEL'] 		= 
					self::$ARR_DISPATCH_DATA['CONTROLLER'] 	= self::neutralise(strtoupper(array_shift($arrCallbackRequest)));

					// Set the callback action:
					self::$ARR_DISPATCH_DATA['ACTION'] 		= self::neutralise(array_shift($arrCallbackRequest));
					
					// Set the view equal to the component include path
					self::$ARR_DISPATCH_DATA['PARAMS'] 		= self::parseRequestParam($arrCallbackRequest);

					// Set the is callback flag
					self::$ARR_DISPATCH_DATA['IS_CALLBACK']	= true;
				}
			 }
			 else
			 {
			 	// Regular dispatch controller path include
			 	$strControllerFilePath = __APP_CONTROLLER_DIR__ . DIRECTORY_SEPARATOR . $this->getVariable('MODEL', self::$ARR_DISPATCH_DATA) . '.php';
			 }
			 
			/**
			 * 1. Check that the controller file exists
			 */
			if (! file_exists($strControllerFilePath)) 
			{
				//include_once(__APPLICATION_ROOT__ . '/http-errors/404.php');
				$this->pageNotFound();
				$blnError = true;
				/*
				throw new Exception(
					'Controller File: ' . $strControllerFilePath . ' doesnt exist.'				
				); 
				*/
			}
			
			/**
			 * 2. Load the controller
			 */
			if (! $blnError)
			{ 
				require_once($strControllerFilePath);
							
				/**
				 * 3. Make sure that the class exists
				 */
				if (! class_exists($this->getVariable('CONTROLLER', self::$ARR_DISPATCH_DATA))) 
				{
					$this->pageNotFound();
					$blnError = true;
					/*
					throw new Exception(
						'Class: ' . $this->getVariable('CONTROLLER', self::$ARR_DISPATCH_DATA) . 
						' doesnt exisis in model: ' . $strControllerFilePath	
					);
					*/
				}
					
				/**
				 * 4. Load the controller object
				 */
				$strControllerClassName = $this->getVariable('CONTROLLER', self::$ARR_DISPATCH_DATA);
				$objController = new $strControllerClassName();	
				
				/** 
				 * 5. Make sure that the action exists
				 */
				if (! method_exists($objController, $this->getVariable('ACTION', self::$ARR_DISPATCH_DATA))) 
				{
					if (method_exists($objController, 'catchAllAction')) 
					{
						self::$ARR_DISPATCH_DATA['ACTION'] = 'catchAllAction';
					} 
					else 
					{
						$this->pageNotFound();
						$blnError = true;
						/*
						throw new Exception(
							'Action: ' . $strControllerClassName . '::' . $this->getVariable('ACTION', self::$ARR_DISPATCH_DATA) . '()' .
							' is not defined in class: ' . $this->getVariable('CONTROLLER', self::$ARR_DISPATCH_DATA) . 
							' [' . $strControllerFilePath . ']'
						);
						*/	
					}
				}
				
				
				if (! $blnError)
				{
					/**
					 * - this segment was optional but later became invaluable for
					 * 	 the overall application. We set the data as get request params
					 *		PLEASE NOTE: Data passed via $_GEt will take presedence over url friendly daya
					 */
					$_GET = array_merge($this->getVariable('PARAMS', self::$ARR_DISPATCH_DATA), $_GET);
					
					/**
					 * 6. Check for a before callback method
					 */
					if (method_exists($objController, 'onBeforeAction'))
					{
						$objController->onBeforeAction(
							(array) $this->getVariable('PARAMS', self::$ARR_DISPATCH_DATA)
						);
					}
					
					/** 
					 * 7. Call the action
					 */
					call_user_func_array(array(
						$objController, $this->getVariable('ACTION', self::$ARR_DISPATCH_DATA)
					), array(
						array('parameters' => $this->getVariable('PARAMS', self::$ARR_DISPATCH_DATA)), 
						$arrModelControllerActions
					));
					
					
					/**
					 * 8. Check for a after callback method
					 */
					if (method_exists($objController, 'onAfterAction'))
					{
						$objController->onAfterAction(
							(array) $this->getVariable('PARAMS', self::$ARR_DISPATCH_DATA)
						);
					}
					
					/** 
					 * 9. Make sure that the view exists 
					 */
					$strViewFile = NULL;
					if (FALSE === ((bool) $this->getVariable('IS_VIEWLESS', self::$ARR_DISPATCH_DATA)))
					{
						$arrAllowedViewFiles = array(
							constant('__APP_VIEW_DIR__') . DIRECTORY_SEPARATOR . self::neutralise($this->getVariable('VIEW', self::$ARR_DISPATCH_DATA)),
							constant('__APP_VIEW_DIR__') . DIRECTORY_SEPARATOR . 'index.php',
							constant('__APP_VIEW_DIR__') . DIRECTORY_SEPARATOR . 'default.php'
						);
						
						foreach ($arrAllowedViewFiles as $intIndex => $strPossibleViewFile) {
							if (
								(TRUE === file_exists($strPossibleViewFile)) &&
								(FALSE === is_dir($strPossibleViewFile))
							) {
								$strViewFile = $strPossibleViewFile;
								break;
							}
						}
					}
					
					/**
					 * Load the view and we're outta here!
					 */
					$this->renderView($strViewFile);
					
				}
			}
			
			die;
		}
		
		
		/**
		 * This method will render a view file
		 * 
		 * @throws  SITE_EXCEPTION
		 * @example $this->renderView('file path');
		 * @access  public, final
		 * @param   String $strViewFile  - 	The View File to execute
		 * @return  Void 
		 */
		public function renderView($strViewFile = NULL)
		{
			$Application 		= APPLICATION::getInstance();
			$objMemcache		= $Application->getMemcache();
			$strUrlRequestPath	= self::getRequestPath();
			$intStartTime		= getMicrotime();
			$strCacheKey		= __CLASS__ . '::' . __FUNCTION__ . '[' . $strUrlRequestPath . ']';
			$blnCached 	 		= false; 
			$strCachedContent 	= $objMemcache->get($strCacheKey);
			$strViewFile		= self::neutralise($strViewFile);
			
			if (TRUE === file_exists($strViewFile)) 
			{
				/**
				 * Load page blocks
				 */
				$this->getApplication()->setPageBlocks(PAGE_BLOCK::getInstanceFromPage());
					
					
				if (
					(! is_object($objMemcache)) ||	
					(FALSE === $strCachedContent) ||
					(FALSE === self::$VIEW_CACHEABLE)
				) {
					ob_flush();
					ob_start(); 
					echo (eval(" ?>" . file_get_contents($strViewFile) . "<?php "));
					$strContent = ob_get_clean();
					
					if ((bool) self::$VIEW_CACHEABLE)
					{
						$objMemcache->set(
							$strCacheKey,
							$strContent,
							strtotime("+30 minutes")
						);
					} 
					else if ((bool) $strCachedContent)
					{
						// Here, we clear the cache to ensure we have the latest available
						// cache. For example, if the cache is turned on, then turned off,
						// we need to clear the old cache so that it can get rebuild if we
						// decide to turn the cache back on again.
						$objMemcache->delete($strCacheKey);
					}
				} 
				else
				{
					$blnCached = true;
					$strContent = $strCachedContent;
				}
				
				// If we have content, then lets display it!
				if ($strContent)
				{
					// Add the META tags
					$strContent = preg_replace("/<head>/", "<head>" . PAGE_META::getPageMeta(false), $strContent);
					
					// Add Google Analytics
					$strContent = preg_replace("/<\/body>/", 
						'<script type="text/javascript">' .
						'var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");' .
						'document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' ' .
						'type=\'text/javascript\'%3E%3C/script%3E"));</script></body>', $strContent);
					
					$strContent = preg_replace(
						"/<\/body>/", 
						'<script type="text/javascript">' .
						'try { var pageTracker = _gat._getTracker("' . __GA_CODE__ . '"); ' . 
						'pageTracker._trackPageview(); } catch(err) {}</script></body>', 
						$strContent
					);
					
					if (
						(TRUE  === constant('__ASYNCRONOUS_PAGE_TRACKING__')) &&
						(FALSE === ((bool) $this->getPageAsyncTrackingDisabled()))
					)	{	
						$objPageView = new PAGE_VIEWS(false);
						$strCallbackUrl	= $this->createCallbackUrl($objPageView, 'savePageViewAsync', array(
							'landingPage'	=>	(isset($_SERVER['PHP_SELF']) 		? $_SERVER['PHP_SELF'] 		: 	NULL),
							'requestUri'	=>	(isset($_SERVER['REQUEST_URI']) 	? $_SERVER['REQUEST_URI'] 	: 	NULL),
							'referredPage'	=>	(isset($_SERVER['HTTP_REFERER']) 	? $_SERVER['HTTP_REFERER'] 	: 	NULL),
							'queryString'	=>	(isset($_SERVER['QUERY_STRING']) 	? $_SERVER['QUERY_STRING'] 	: 	NULL),
							'isCrawler'		=>	(int) is_bot()	
						));

						// Add the javascript tracker code.
						// its less obstructivce than tracking
						// on before page load.
						$strContent = preg_replace
						(
							"/<\/body>/", 
							'<script type="text/javascript">' .
							'	if (typeof($) == "undefined") {'  . 
							'		document.write(\'<\' + \'script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"><\' + \'/script>\');' .
							'		window.onload = function() { '.
							'			$(document).ready(function(e) {' .
							'				$.ajax({'.
							'					type: 		 "GET",'.
							'					url: 		 "' . $strCallbackUrl . '",'.
							'					dataType: 	 "html",'.
							'					timeout: 	 4000,'.
							'					cache: 		 false,'.
							'					processData: false,'.
							'					xhrFields: 	{ withCredentials: true },'.
							'					success: 	function (data) {},'.
							'					error: 		function (jqXHR, textStatus, errorThrown) {},'.
							'					complete: 	function () {}'.
							'				});'.
							'			});' .
							'		}' .
							'	} else { ' .
							'		$(document).ready(function(e) {' .
							'			$.ajax({'.
							'				type: 		 "GET",'.
							'				url: 		 "' . $strCallbackUrl . '",'.
							'				dataType: 	 "html",'.
							'				timeout: 	 4000,'.
							'				cache: 		 false,'.
							'				processData: false,'.
							'				xhrFields: 	{ withCredentials: true },'.
							'				success: 	function (data) {},'.
							'				error: 		function (jqXHR, textStatus, errorThrown) {},'.
							'				complete: 	function () {}'.
							'			});'.
							'		});' .
							'	} '.
							'</script></body>', 
							$strContent
						);
					}
					
					// Output the results
					
					// Data Compression
					if (TRUE === ((bool) $this->getVariable('COMPRESSION', self::$ARR_DISPATCH_DATA)))
					{
						// 1. replace the inline javascript comments	, 
						// TODO: Fix this RegExp because it can be VERY useful
						//$strContent = preg_replace("/(?<!\:)\/\/(.*)\\n/", "", $strContent);
						
						// 2. Replace new lines and tabs
						$strContent = preg_replace("/[\n\t]/", "", $strContent);
					}
					
					// Output the execution time
					$intEndTime	 = getMicrotime();
					$strExecutionTime  = ("\n <!-- \n");
					$strExecutionTime .= (" T: " . ($intEndTime - $intStartTime) . "\n");
					$strExecutionTime .= (" C: " . ($blnCached ? 'TRUE' : 'FALSE') . "\n -->");
					$strContent =  preg_replace("/<\/body>/", $strExecutionTime . "</body>", $strContent);
					
					// Lets output the results... we're done here!
					echo ($strContent);
				}
				
				/**
				 * Here, we add some debug info if for example theres a catchAll action and 
				 * The view file requested isnt found, this part will notify that the default view (default.php)
				 * was used in this case.
				 */
				if (
					(true === isset($strViewFile)) &&
					(true === isset($strDefaultViewFile)) &&
					(strcmp($strViewFile, $strDefaultViewFile) == 0) &&
					(strcmp($this->getVariable('CONTROLLER', self::$ARR_DISPATCH_DATA), 'INDEX_CONTROLLER') <> 0)
				) {
					echo (" <!-- \n * Default View NF[" . $this->getVariable('VIEW', self::$ARR_DISPATCH_DATA) . "] * --> \n");
				}
			}
		}
		 
		
		/**
		 * This method creates a callback URL used to callback core components objects
		 * 
		 * @throws  SITE_EXCEPTION
		 * @example $this->createCallbackUrl($this, __FUNCTION__, array('param-1', 'param-2'))
		 * @access  protected, static, final
		 * @param   Object $objCallbackTaget	-	The target object / Or object class path (path::to::class::{classname})
		 * @param   String $strMethodCallback 	- 	The callback method
		 * @param   Array  $arrParams		 	- 	The additional parameters [optional]
		 * @return  String 
		 */
		public static final function createCallbackUrl($objCallbackTaget, $strMethodCallback, $arrParams = array())
		{
			$strCallbackUrl	= false;
			$Application 	= APPLICATION::getInstance();
			
			if (is_string($objCallbackTaget)) {
				$arrClassSegments = explode('::', $objCallbackTaget); 
				$strClassName = array_pop($arrClassSegments); // get the class name and remove it from the path info array
				SHARED_OBJECT::getObjectFromPackage(strtoupper(implode('::', $arrClassSegments)));	
				$objCallbackTaget = new $strClassName();	
			}
			
			if (TRUE === is_object($objCallbackTaget))
			{
				// 1. get a reflection object instance
				$objTaget = new ReflectionObject($objCallbackTaget);
				
				// 2. Validate that the object's target method exists
				if (FALSE === $objTaget->hasMethod($strMethodCallback)) 
				{
					SITE_EXCEPTION::raiseException(
						'Callback method [' . $objTaget->getName() . '::' . $strMethodCallback . '()] does not exists. <br />' .
						'File Path: ' . $objTaget->getFileName()
					);
				}
				
				// 3. Replace the root server path from the object file path
				// We use realpath because the file's path can be shared
				// or can be included via symlink
				//$strCallbackUrl = str_replace(realpath(constant('__SITE_ROOT__')), '', $objTaget->getFileName());
				$strCallbackUrl = realpath($objTaget->getFileName());

				// 4.  Remove the file extension
				$strCallbackUrl = substr($strCallbackUrl, 0, (strlen($strCallbackUrl) - strlen('.' . pathinfo($strCallbackUrl, PATHINFO_EXTENSION))));
				
				// 5. Clean ampty values from the path array
				$arrClassPath = explode(DIRECTORY_SEPARATOR, $strCallbackUrl);
				array_walk($arrClassPath, function($strDirectory, $intIndex) use(&$arrClassPath){ 
				    if (! strlen(trim($strDirectory)))
				    {
				    	unset($arrClassPath[$intIndex]);
				    }
				});
				
				// 6. Join the path with the callback directory separator and
				// Build the final callback URL.
				$arrCallbackUrl = array(implode('-', $arrClassPath));
				
				// 7. Add the object class name. this is useful if the class name differse from the class path
				$arrCallbackUrl[] = $objTaget->getName();
				
				// 8. Add the callback method
				$arrCallbackUrl[] = $strMethodCallback;
				
				// 9. Add the parameters
				array_walk($arrParams, function($strParam, $intIndex) use(&$arrParams, &$arrCallbackUrl){ 
				    if (strlen(trim($strParam)))
				    {
				    	// This adds the URL parameters, if the array keys are string based
				    	// The URL parameters are constructed in the following fashion /param:value/param:value/
				    	$arrCallbackUrl[] = (TRUE === is_string($intIndex) ? $intIndex . ':' : '') . urlencode($strParam);
				    }
				});
				
				// 10. Build the final URL
				// We use a URL object to make sure the session attributes are added.
				$strComponentCallbackPath = base64_encode($Application->getCrypto()->encrypt(implode('/', $arrCallbackUrl)));
				$objCallbackUrl = new URL(constant('__SITE_URL__') . '/callback/' . $strComponentCallbackPath);
				$objCallbackUrl->clearAttribute();
				$strCallbackUrl = $objCallbackUrl->build();
			}
			return ($strCallbackUrl);
		}
		
		/**
		 * This method creates a URL
		 * 
		 * @throws  SITE_EXCEPTION
		 * @example $this->createRoute(array(
		 *		'controller' 	=> 'index',
		 8		'action'		=> 'do_test'
		 * ))
		 * @access  protected, static, final
		 * @param   Array  $arrParams -	The Url parameters, Expended (CONTROLLER / ACTION / (...PARAMS))
		 * @param	Boolean $blnUserCanonicalUrlPath - If set to true, the site's URL will be prepended to the URL - otherwise a /path/to/file url will be built
		 * @return  String 
		 */
		public function createRoute($arrParams = array(), $blnUserCanonicalUrlPath = true)
		{
			$arrRouteUrl = array();
			$arrParams	 = array_change_key_case($arrParams, CASE_LOWER);
			
			// Add the Language
			if ($this->getApplication()->getForm()->getOrPost('lang'));
			{
				$arrRouteUrl[] = $this->getApplication()->getForm()->getOrPost('lang');	
			}
			
			if (
				(TRUE === isset($arrParams['controller'])) &&
				(strlen($arrParams['controller']))
			) {
				$arrRouteUrl[] = strtolower(trim(self::neutralise($arrParams['controller'])));
			}
			
			if (
				(TRUE === isset($arrParams['action'])) &&
				(strlen($arrParams['action']))
			) {
				$arrRouteUrl[] = strtolower(trim(self::neutralise($arrParams['action'])));
			}

			if (TRUE === isset($arrParams['params'])) {
				$arrParams = $arrParams['params'];
				array_walk($arrParams, function($strValue, $strKey) use(&$arrRouteUrl) {
					$arrRouteUrl[] = REQUEST_DISPATCHER::neutralise($strKey) . (strlen($strValue) ? (':' . REQUEST_DISPATCHER::neutralise($strValue)) : '');
				});
			}
			
			return (((true === $blnUserCanonicalUrlPath) ? constant('__SITE_URL__') : '') . implode('/', $arrRouteUrl));
		}
		
		/**
		 * This method assigns the view to use
		 * @access:	public
		 * @param:	$strViewPath - The view path relative from the __APP_VIEW_DIR__
		 * @return: void
		 */
		public final function assignView($strViewPath = NULL)
		{
			if (
				(is_null($strViewPath)) ||
				(FALSE == ((bool) $strViewPath))
			) {
				$this->assignNoView();
			}
			else
			{
				self::$ARR_DISPATCH_DATA['VIEW'] = $strViewPath;
			}
		}
		
		/**
		 * This method removes a view output
		 * @access:	public
		 * @return: void
		 */
		public final function assignNoView()
		{
			self::$ARR_DISPATCH_DATA['IS_VIEWLESS'] = true;
		}
		
		/**
		 * This method sets the compression method
		 * @param:	Boolean $blnUseCompression - TRUE/FALSE use the compression
		 * @access:	public
		 * @return: void
		 */
		public final function useCompression($blnUseCompression = false)
		{
			self::$ARR_DISPATCH_DATA['COMPRESSION'] = (bool) $blnUseCompression;
		}
		
	 	/**
	 	 * @deprecated : Should use the useCompression() method	
		 * This method prevents the compression output
		 * @access:	public
		 * @return: void
		 */
		public final function noCompression($blnUseCompression = false)
		{
			$this->useCompression($blnUseCompression);
		}
		
		/**
		 * This method sets if the view can be cached
		 * @access:	public
		 * @param: $blnCache Boolean - Default true
		 * @return: void
		 */
		public final function enableViewCache($blnCache = TRUE)
		{
			self::$VIEW_CACHEABLE = (bool) $blnCache;
		}
		
		/**
		 * Class variable setter
		 * @access package
		 * @param $appVarName    string - The class name thats being loaded - Required
		 * @param $mxAppVarValue Mixed  - The class name thats being loaded - Optional
		 * @return VOID
		 */
		 public function setVariable($appVarName, $mxAppVarValue=NULL) 
		 {
		 	self::$ARR_DISPATCH_DATA[strtolower($appVarName)] = $mxAppVarValue;
		 }
		 
		/**
		 * Class variable getter - If the variable requested is null, all the application variables are returned
		 * @access package
		 * @param $appVarName    string - The class name thats being loaded - Optional
		 * @return Application Variable
		 */
		 public function getVariable($appVarName=NULL, $arrSearchFrom = NULL) 
		 {
			if (! is_array($arrSearchFrom)) {
				$arrSearchFrom = self::$ARR_DISPATCH_DATA;	
			} else {
				$arrSearchFrom = $arrSearchFrom;
			}
			
			$arrSearchFrom = array_change_key_case($arrSearchFrom, CASE_LOWER);
			
		 	$mxAppVarVal = false;
			if (is_null($appVarName)) {
				$mxAppVarVal = $arrSearchFrom;
			} else {
				$mxAppVarVal = (
					isset($arrSearchFrom[strtolower($appVarName)]) ?
					$arrSearchFrom[strtolower($appVarName)] : FALSE
				);
			}
		 	return($mxAppVarVal);
		 }
		 
		/**
		 * This method renders a 404 error
		 *
		 * @access 	package
		 * @param 	none
		 * @return 	void
		 */
		 public final function pageNotFound()
		 {
			 if (FALSE === headers_sent())
			 {
				 header('HTTP/1.0 404 Not Found');
			 }
			 
			 require_once(__APPLICATION_ROOT__ . '/http-errors/404.php');
			 die;
		 }
		 
		/**
		 * This method returns a request parameter from the POST then GET array
		 * 
		 * @access	protected final
		 * @namespace	\http
		 * @param	String $strReqParam - The request parameter
		 * @return	String | false
		 */
		public final function getRequestParam($strReqParam = NULL)
		{
			$mxRequestParamValue = false;
			$strReqParam 		 = trim($strReqParam);
			
			if (false === empty($strReqParam))
			{
				$mxRequestParamValue = (
					((false === empty($_POST)) && (true  === isset($_POST[$strReqParam]))) ? $_POST[$strReqParam] :
					(((false === empty($_GET)) && (true  === isset($_GET[$strReqParam]))) ? $_GET[$strReqParam] : false)
				);
			}
	
			return ($mxRequestParamValue);
		}
		
		/**
		 * This method sets a request parameter from the POST then GET array
		 * 
		 * @access	protected final
		 * @namespace	\http
		 * @param	String $strReqParam - The request parameter
		 * @return	String | false
		 */
		public final function setRequestParam($strParamName, $strParamValue = NULL)
		{
			$_GET[$strParamName] 	= $strParamValue;
			$_POST[$strParamName] 	= $strParamValue;
		}
		 
		 /**
		  * This method add data that will be accisible to the view
		  * @param:	String [Optional] - $strKey - The variable name 
		  * @return:	String / array / false - The member variable		  
		  */
		 public function setViewData($strFnName, $arrArguments) 
		 { 
			self::$ARR_VIEW_DATA[strtolower($strFnName)] = $arrArguments;
		 }
		 
		 /**
		  * This method returns a view data
		  * @param:	String [Optional] - $strKey - The variable name 
		  * @return:	String / array / false - The member variable		  
		  */
		 public function getViewData($appVarName=NULL) 
		 { 
			return ($this->getVariable(strtolower($appVarName), self::$ARR_VIEW_DATA));
		 }
		 
		 /**
		  * This method returns the current request parameters, can only 
		  * be called after the dispatch() method is called. Its a wrapper
		  * for self::getVariable('PARAMS', self::$ARR_DISPATCH_DATA);
		  *
		  * @param:	 none
		  * @return: Array - The request parameter array		  
		  */
		 public function getRequestParams() 
		 { 
			//return (self::$ARR_DISPATCH_DATA['PARAMS']);
			return array_merge($_GET, $_POST);
		 }
		 
		/**
		 * This method processes an input array of get variables (/var:value/var:value/)
		 * and returns an associative array of key value pairs
		 * 
		 * @throws  SITE_EXCEPTION
		 * @example $this->parseRequestParam($arrInput);
		 * @access  protected, static, final
		 * @param   Array  $arrInput			-	The input array
		 * @return  Array
		 */
		 protected static final function parseRequestParam(array $arrRequestParams = NULL)
		 {
			$arrReturn = array();
			if (FALSE === empty($arrRequestParams))	 
			{
				reset ($arrRequestParams);
				while (list($intIndex, $strDataIn) = each($arrRequestParams))	
				{
					$arrParsedData = explode(':', $strDataIn);
					if (FALSE === empty($arrParsedData))	
					{
						$strDataKey 	= array_shift($arrParsedData);
						$mxParsedData 	= urldecode(array_shift($arrParsedData));
						self::setRequestParam($strDataKey, $mxParsedData);
						$arrReturn[trim($strDataKey)] = (FALSE === empty($mxParsedData) ? $mxParsedData : NULL);
					}
				}
			}
			
			return ((array) $arrReturn);
		 }
		 
		/**
		 * This method returns a class variable. if no arguments are passed
		 * This is a magic method implementation and should not be called directly
		 * all of the class variables are returned. if an argument is passes and
		 * the data not found, FALSE is returned.
		 * @param:	String [Optional] - $strKey - The variable name 
		 * @return:	String / array / false - The member variable
		 */
		 public function __call($strFnName, $arrArguments) 
		 {
			if (strcmp(strtolower(substr($strFnName, 0, 3)), 'get') === 0) {
				return ($this->getVariable(strtolower(substr($strFnName, 3))));	
			}
			
			else if (
				(strcmp(strtolower(substr($strFnName, 0, 3)), 'set') === 0) &&
				(isset($arrArguments[0]))
			) {
				return ($this->setVariable(strtolower(substr($strFnName, 3)), $arrArguments[0]));	
			}
			else {
				self::raiseException("Undefined method: " . $strFnName . "() in " . __CLASS__);
			}
		 }
	}
