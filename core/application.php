<?php
	class_exists('SHARED_OBJECT')  
		| require_once(constant('__APPLICATION_ROOT__') . '/hybernate/shared_object.' . constant('__APPLICATION_VERSION__') . '.php'); 
	
	class_exists('SITE_EXCEPTION') 
		| require_once(constant('__APPLICATION_ROOT__') . '/exception/site_exception.php');  

	class_exists('OBJECT_BASE') 
		| require_once(constant('__APPLICATION_ROOT__') . '/interface/object_base.php');  	
	/*	
	class_exists('ADMIN_APPLICATION')  
		| require_once((defined('__APPLICATION_ADMIN_PATH__') ? constant('__APPLICATION_ADMIN_PATH__') : constant('__SITE_ROOT__') . '/admin') . '/mvc/application/admin_application.php'); 	
	*/
	class APPLICATION extends SITE_EXCEPTION 
	{
		
		private static $_APPLICATION_INSTANCE 				= NULL;
		private static $_APPLICATION_CONTROLLER_INITIATED 	= FALSE;
		private static $_APPLICATION_BOOTSTRAP_INITIATED 	= FALSE;
		private static $_IS_GETINSTANCE 					= FALSE;
		private static $_APPLICATION_VARS					= array();	
		
		/**
		 * Class Constructor
		 * @return APPLICATION
		 */
		public final function __construct() 
		{
			if (! self::$_IS_GETINSTANCE) // Insure singleton behavior.
			{
				throw new Exception(
					'Please use the "getInstance()" static method to retreive the active ' . 
					__CLASS__ . ' instance. [' . __CLASS__. '::getInstance()]'
				);
			}
			else 
			{
				$this->bootstrap();
				$this->setApplicationId(__SITE_NAME__ . '_Application');
			}
		}
		
		/**
		 * Class Static Constructor
		 * @access static
		 * @return APPLICATION
		 */
		final public static function getInstance() 
		{
			/**
			 * Execute the before callback
			 */
			$arrArgument = func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArgument); 
			
			self::$_IS_GETINSTANCE = true;
			// Get the called class
			$__STR_CALLED_CLASS__ = (
				function_exists('get_called_class') ? get_called_class() : get_class(self)
			);
			
			if (
				(FALSE === is_object(self::$_APPLICATION_INSTANCE)) ||
				(
					(TRUE  === is_object(self::$_APPLICATION_INSTANCE)) &&
					(FALSE === is_a(self::$_APPLICATION_INSTANCE, $__STR_CALLED_CLASS__))
				)
			) {
				// Backwards compatibility, we store the last called application object in the session in case 
				// get_called_class() fails (PHP 5.2.17)
				$_SESSION['CORE::APPLICATION::CALLED_CLASS'] = $__STR_CALLED_CLASS__;
				
				$objApplication = new $__STR_CALLED_CLASS__;	
				
				self::$_APPLICATION_INSTANCE = $objApplication;
				
				if (method_exists(self::$_APPLICATION_INSTANCE, 'onAfter_getInstance'))
				{
					self::$_APPLICATION_INSTANCE->onAfter_getInstance();
				}
			}
			
			/**
			 * Execute the post callback
			 */
			self::__callback(__FUNCTION__, $arrArgument);
		
			return (self::$_APPLICATION_INSTANCE);
		}
		
		/**
		 * Class initator
		 * @access package
		 * @return APPLICATION
		 */
		final public function bootstrap() 
		{
			/**
			 * Execute the before callback
			 */
			$arrArgument = func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArgument);
			
			/**
			 * Application is already bootstraped, return static application instance.
			 */
			if (self::$_APPLICATION_BOOTSTRAP_INITIATED) 
			{
				/**
				 * Execute the post callback
				 */
				self::__callback(__FUNCTION__, $arrArgument);
				
				return (self::$_APPLICATION_INSTANCE);		
			}
			
			// Load required files:
			
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::DATABASE::DATABASE");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HTTP::REQUEST_DISPATCHER");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::SITE_USERS");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_VIEWS");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_BLOCK");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MEMCACHE::MEMCACHE_MANAGER");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::URL::URL");	
			
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PARSER::PARSER");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::CONTENT::SAVECONTENT");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::DEBUG::DUMP");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTILITY-FUNCTIONS");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::TRANSLATE::TRANSLATOR");	
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::CRYPT::AES_CRYPTO");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::GEOLOCATION::GEO_LOCATOR");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::TRACKING::TRACKING_PIXEL");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTIL::MINIFICATION::MINIFICATION");

			require_once(__APPLICATION_ROOT__ . '/formHandler.php');
			
			// Initiate the session immediatly
			SESSION::getSession();		
				
			// initiate the autoload
			spl_autoload_register("APPLICATION::autoload");
			
			self::$_APPLICATION_BOOTSTRAP_INITIATED = true;
			
			/**
			 * Execute the post callback
			 */
			self::__callback(__FUNCTION__, $arrArgument);
			
			return (self::getInstance());		
		}
		
		
		final public function webControllerInitiate() 
		{
			/**
			 * Execute the before callback
			 */
			$arrArgument = func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArgument);
			
			/**
			 * Web controller is already instantiated, return static application instance.
			 */
			if (self::$_APPLICATION_CONTROLLER_INITIATED) 
			{
				/**
				 * Execute the post callback
				 */
				self::__callback(__FUNCTION__, $arrArgument);
				return (self::$_APPLICATION_INSTANCE);
			}
			
			/** 
			 * Path handler
			 */	
			if (isset($_GET['path'])) {
				$_REQUEST['path'] = $_GET['path'];
				unset($_GET['path']); 
			}
			
			if (! self::$_APPLICATION_CONTROLLER_INITIATED) 
			{
				$this->setSession(SESSION::getSession());							 # Session Management
				$this->setRequestDispatcher(REQUEST_DISPATCHER::getInstance());		 # The request dispatcher
				$this->setDatabase(DATABASE::getInstance());						 # Database Management.
				$this->setMemcache(MEMCACHE_MANAGER::getInstance());				 # Memcache Management.
				$this->setForm(FORMHANDLER::getInstance());							 # Form Handling		
				$this->setContent(SAVECONTENT::getInstance());						 # Content Save Handling (cfsavecontent) - USED ALOT IN REMOTE-CALLES						
				$this->setParser(PARSER::getInstance());							 # Object Parser
				$this->setUser(SITE_USERS::getCurrentUser());						 # User Management
				$this->setCrypto(AES_CRYPTO::getInstance());						 # Cryptology Management.
				#$this->setTranslator(TRANSLATOR::getInstance());					 # Translator
				$this->setMinification(MINIFICATION::getInstance());				 # Resource Minification Management.
								
				// Helper functions data sets
				$this->setStaticResourcePath(constant('__STATIC_RESOURCES_PATH__'));
				$this->setBaseStaticResourcePath(str_replace(constant('__SITE_URL__'), '', $this->getStaticResourcePath()));
				
				// GEO_LOCATOR_LITE is also available for fallback
				$this->setGeoLocator(GEO_LOCATOR::getInstance(
					isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL	 # GeoLocation Management
				));
				
				/**
				 * User Management Section 
				 * This section manages the user portion of the site and is crutial for the 
				 * proper execution of the site.
				 */ 
				$this->getUser()->setLoginUrl(constant('__USER_LOGIN_URL__'));				
				$this->getUser()->setSignupUrl(constant('__USER_SIGNUP_URL__'));
				$this->getUser()->setPassReminderUrl(constant('__USER_PASS_REMINDER_URL__'));
				$this->getUser()->setChangePassUrl(constant('__USER_CHANGE_PASS_URL__'));
				$this->getUser()->setConfirmMembershipUrl(constant('__USER_CONFIRM_MEMBERSHIP_URL__'));
				$this->getUser()->setLinkedInLoginUrl(constant('__USER_LINKEDIN_LOGIN_URL__'));
				$this->getUser()->setFacebookLoginUrl(constant('__USER_FACEBOOK_LOGIN_URL__'));
				$this->getUser()->setTwitterLoginUrl(constant('__USER_TWITTER_LOGIN_URL__'));
				$this->getUser()->setGoogleLoginUrl(constant('__USER_GOOGLE_LOGIN_URL__'));
				$this->getUser()->setNoticeUrl(constant('__USER_NOTICE_URL__'));
				$this->getUser()->setProfileUrl(constant('__USER_PROFILE_URL__'));
				
				//
				// Secure directories and sub directories...
				//	
				reset($GLOBALS["__USER_SECURED_DIRECTORIES__"]);
				while (list($strDirectoryPath, $intAccessLevelRequired) = each($GLOBALS["__USER_SECURED_DIRECTORIES__"]))
				{
					$this->getUser()->secureDirectory(__SITE_ROOT__ . $strDirectoryPath, (int) $intAccessLevelRequired, false);
				}
			}
			
			$this->getUser()->secure();
			
			/**
			 * Pixel tracking
			 *   - To use it, the request url must be http://www.{host}.com/trc.png
			 */
			TRACKING_PIXEL::track(); 
			
			/** 
			 * Page tracking
			 */	
			if ((bool) __INTERNAL_PAGE_TRACKING__) 
			{
				PAGE_VIEWS::savePageView();
			}

			/**
			 * Set the language
			 */ 
			// $this->getForm()->setUrlParam('lang', $this->getSession()->get('lang'));
			$strLang = ($this->getSession()->get('lang') ? $this->getSession()->get('lang') : (
				((false === empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) && (true === in_array(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)), array('en', 'fr')))) ? 
				strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : 'en'
			));
			
			if (
				(true === isset($_GET['lang'])) &&
				(true === (in_array($_GET['lang'], array('en', 'fr'))))
			) {
				$strLang = $_GET['lang'];
			}
			
			$this->setLanguage($strLang, false);
			$this->setFrenchCanonicalUrl(('/fr' . URL::getCanonicalUrl(null, true, true, true)));
			$this->setEnglishCanonicalUrl(('/en' . URL::getCanonicalUrl(null, true, true, true)));
			
			
			/** 
			 * Session login user
			 */	 
			if (
				 ($this->getForm()->isPost()) && 
				 ($this->getForm()->paramValue('__LOGIN__'))
			) {
				$this->getUser()->login(
					$this->getForm()->paramValue('user_login'), 
					$this->getForm()->paramValue('user_password'),
					$this->getForm()->paramValue('user_redirect', true)
				);
			}
			
		
			/**
			 * Session Logout Handler
			 */ 
			if (isset($_GET['logout'])) {
				$strRedirect = false;
				$this->getUser()->logout(((bool) $strRedirect ? false : true));
			}
			

			/**
			 * Reset headers
			 */
			 if (ob_get_length())
			 {
				@ob_end_clean();     
				if (function_exists('header_remove')) 
				{
					@header_remove();	
				}
			}    
			@ob_start();
			
			self::$_APPLICATION_CONTROLLER_INITIATED = true;
			
			/**
			 * Execute the post callback
			 */
			self::__callback(__FUNCTION__, $arrArgument);
			
			return (self::getInstance());		
		}
		
		/**
		 * Class Autoload initiator
		 * @access static
		 * @param $c string - The class name thats being loaded
		 * @return VOID
		 */
		 public static function autoload($c) 
		 {
			$strFile = strtolower(implode(DIRECTORY_SEPARATOR, explode('_', str_replace("__", "*", $c))));
			$strFile = str_replace('*', '_', $strFile);
			if (file_exists(__APPLICATION_ROOT__ . DIRECTORY_SEPARATOR . $strFile . '.php')) {
				require_once(__APPLICATION_ROOT__ . DIRECTORY_SEPARATOR . $strFile . '.php');	
				$classNameArray = explode(DIRECTORY_SEPARATOR, $strFile);
				$className = end($classNameArray);
				
				if (! class_exists(strtoupper($c))) {
					$strClass = 'class ' . strtoupper($c) . ' extends ' . strtoupper($className) . ' { ' .
									 'public $class_name = ' . strtoupper($className) . '; ' .
									 'public static function getInstance($a=null, $b=null) { ' . 
										'$p = new parent(); ' .
										'return $p::getInstance($a, $b);' .
									 '}' .
									 'public static function getInstanceFromKey($a=null, $b=null) { ' . 
										'$p = new parent(); ' .
										'return $p::getInstanceFromKey($a, $b);' .
									 '}' .
								'}';	 
								
					
					eval($strClass);
				}
			}	 
		 }
		 
		/**
		 * Class variable setter
		 * @access package
		 * @param $appVarName    string - The class name thats being loaded - Required
		 * @param $mxAppVarValue Mixed  - The class name thats being loaded - Optional
		 * @return VOID
		 */
		 public function setVariable($appVarName, &$mxAppVarValue=NULL) 
		 {
		 	/**
			 * Execute the before callback
			 */
			$arrArgument = func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArgument);
			
		 	self::$_APPLICATION_VARS[strtolower($appVarName)] = &$mxAppVarValue;
		 	
		 	/**
			 * Execute the post callback
			 */
			self::__callback(__FUNCTION__, $arrArgument);
		 }
		 
		 /**
		 * Class variable getter - If the variable requested is null, all the application variables are returned
		 * @access package
		 * @param $appVarName    string - The class name thats being loaded - Optional
		 * @return Application Variable
		 */
		 public function getVariable($appVarName=NULL) 
		 {
		 	/**
			 * Execute the before callback
			 */
			$arrArgument = func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArgument);
			
		 	$mxAppVarVal = false;
			if (is_null($appVarName)) {
				$mxAppVarVal = self::$_APPLICATION_VARS;
			} else {
				$mxAppVarVal = (
					isset(self::$_APPLICATION_VARS[strtolower($appVarName)]) ?
					self::$_APPLICATION_VARS[strtolower($appVarName)] : FALSE
				);
			}
			
			/**
			 * Execute the post callback
			 */
			self::__callback(__FUNCTION__, $arrArgument);
			
		 	return($mxAppVarVal);
		 }
		
		/**
		 * This method sets the language in the URL and session. 
		 * It then redirects the user to the current page with the 
		 * proper language prefix
		 *
		 * @throws		SITE_EXCEPTION
		 * @acccess		public final
		 * @param		string 	$strLanguage - The language prefix
		 * @param		boolean $blnRedirect - If the application should redirect the user.
		 * @return 		void
		 */
		 public final function setLanguage($strLanguage = NULL, $blnRedirect = TRUE)
		 {
			// Set the language in the session and URL
			if (true === isset($_URL['lang'])) {
				unset($_URL['lang']);
			}
			$this->getSession()->remove('lang');
			$this->getSession()->set('lang', $strLanguage);
			$this->getForm()->setUrlParam('lang', $strLanguage);
			
			if ($strLanguage == 'fr') {
				setlocale(LC_ALL, 'fr_FR');
			}
			
			if (TRUE === $blnRedirect)
			{
				// Build the redirect URL
				$strRedirectUrl = (
					(TRUE === isset($_SERVER['HTTP_REFERER'])) &&
					(FALSE === empty($_SERVER['HTTP_REFERER'])) ?
					$_SERVER['HTTP_REFERER'] : constant('__SITE_URL__')
				);

				// get the path and remove the leading '/'
				$strRedirectUrl = ltrim(parse_url($strRedirectUrl, PHP_URL_PATH), '/');

				// Remove the language if its already there..
				if (
					(strlen($strRedirectUrl) == 2)  ||
					(substr($strRedirectUrl, 2, 1) == '/')
				) {
					$strRedirectUrl = substr($strRedirectUrl, 2);
				}

				$strRedirectUrl = '/' . $strLanguage . '/' . ltrim(parse_url($strRedirectUrl, PHP_URL_PATH), '/');
				URL::redirect($strRedirectUrl);
				exit();
			}    
		 }	
		
		/**
		 * This translate the texts provided with the session language
		 *
		 * @throws		SITE_EXCEPTION
		 * @acccess		public final
		 * @param		string 	$strEnText - The text in English language
		 * @param		string 	$strFrText - The text in French language
		 * @return 		String
		 */
		 public final function translate($strEnText = NULL, $strFrText)
		 {
			 $strReturn = $strEnText;
			 if ($this->getSession()->get('lang') === 'fr')
			 {
				$strReturn = $strFrText;	 
			 }
			 return ($strReturn);
		 }
		 
		 /**
		 * This method returns a class variable. if no arguments are passed
		 * This is a magic method implementation and should not be called directly
		 * all of the class variables are returned. if an argument is passes and
		 * the data not found, FALSE is returned.
		 * @param:	String [Optional] - $strKey - The variable name 
		 * @return:	String / array / false - The member variable
		 */
		 public function __call($strFnName, $arrArguments) {
		 	/**
			 * Execute the before callback
			 */
			self::__beforeCallback($strFnName, $arrArguments); 
			
			if (strcmp(strtolower(substr($strFnName, 0, 3)), 'get') === 0) {
				return ($this->getVariable(strtolower(substr($strFnName, 3))));	
			}
			
			else if (
				(strcmp(strtolower(substr($strFnName, 0, 3)), 'set') === 0) /*&&
				(isset($arrArguments[0]))*/
			) {
				/*
				 * On a setter method, we return the object so we can chain commands
				 * EX: $object->getUser()->setFirstName('Avi')->save();
				 */
				$this->setVariable(strtolower(substr($strFnName, 3)), $arrArguments[0]);
				return ($this);	
			}
			else {
				self::raiseException("Undefined method: " . $strFnName . "() in " . __CLASS__);
			}
			
			/**
			 * Execute the post callback
			 */
			self::__callback($strFnName, $arrArguments);
		 }
		 
		
		/**
		 * This method is called before a extended action is called
		 * 
		 * @access 	protected,static
		 * @param 	string $strAction - The called action
		 * @param 	array  $arrArgs	- The array of arguments
		 * @return	void
		 */
		protected static function __beforeCallback ($strAction, $arrArgs) 
		{
			if (method_exists(get_called_class(), 'onBefore' . ucwords($strAction))) 
			{
				call_user_func_array(array(get_called_class(), 'onBefore' . ucwords($strAction)), (array) $arrArgs);	
			}
		}
		
		/**
		 * This method is called after a extended action is called
		 * 
		 * @access 	protected, static
		 * @param 	string $strAction - The called action
		 * @param 	array  $arrArgs	- The array of arguments
		 * @return	void
		 */
		protected static function __callback ($strAction, $arrArgs) 
		{
			if (method_exists(get_called_class(), 'on' . ucwords($strAction))) 
			{
				call_user_func_array(array(get_called_class(), 'on' . ucwords($strAction)), (array) $arrArgs);	
			}
		}
	}
