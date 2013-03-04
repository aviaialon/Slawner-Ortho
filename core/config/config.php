<?php
	/**
	 * Core Generic configuration file. This file is automaticaly prepended to every php file in the site.

	 * @package		NONE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 *
	 * Required Read / Write / Delete Access
	  		- {__APPLICATION_ROOT__}/database/cached_queries/
			- {__APPLICATION_ROOT__}/hybernate/object_cache/
			- /logs
			
	  * Rwquired Packages
	  		- Zend Framework [sudo apt-get install zend-framework] and change include path
			- TODO: Include Zend Framework with distribution
			
			http://wave.webaim.org
			http://www.hongkiat.com/blog/20-facebook-tipstricks-you-might-not-know/
	  */
	 
	 /**
	  * REQUIRED CRON JOBS:
	  
	  	MAILTO="aviaialon@gmail.com"

		#---------------------------------
		# ADZARO SESSION MANAGEMENT CLEANUP CRON
		#---------------------------------
		*\/3 * * * *  /usr/bin/php {__APPLICATION_ROOT__}/cron/static_call.php classes::session::session::collectGarbage;
		
		#---------------------------------
		# ADZARO AUCTION LISTINGS CLOSER
		#---------------------------------
		*\/3 * * * * /usr/bin/php {__APPLICATION_ROOT__}/cron/static_call.php classes::hybernate::objects::auction::auction::closeAuctions;
		
		#---------------------------------
		# ADZARO FACEBOOK TOP 10 LISTINGS
		#       Running @ 11:30PM daily
		#---------------------------------
		30 23 * * * /usr/bin/php {__APPLICATION_ROOT__}/cron/static_call.php classes::facebook::adzaro_feeds::__compileAdzaroFeeds;
		
		#---------------------------------
		# MYSQL DATABASE BACKUP
		#---------------------------------
		22,58 * * * * /usr/bin/mysqldump -u root -p'merlin' --all-databases | gzip > /var/backups/mysql/$(date +'%d-%m-%y-%T').gz
	*/
	 
	 /**
	  * Step 1: Make sure the configuration file is loaded
	  */
	  if (FALSE === isset($CONFIG_LOADED))
	  {
		  die('Configuration error. Please contact the site administrator.');
	  }
	  
	 /**
	  * @depreciated
	  * 	- 	This check is depreciated because a file check at every request is very expensive
	  * 		Its the admin's responsability to make sure the .htaccess file is created properly
	  * Step 2: Check for a valid .htaccess
	  */
	 /* 
	 if (FALSE === file_exists($_SERVER['DOCUMENT_ROOT'] . DS . '.htaccess')) 
	 {
		throw new Exception('Please configure your .htaccess file before continuing.');
		die;
	 }
	 */
	   
	 /**
	  * Add default include paths
	  */
	 set_include_path(get_include_path(). PATH_SEPARATOR . __APPLICATION_ROOT__); 
		
	/**
	 * Memory limit
	 */
	 ini_set("memory_limit","192M");
	
	/**
	 * Error Reporting....
	 *		Error handling is disabled when using error in the URL due to internal php error
	 *		handling fuck ups from PHP...
	 */
	 define('__USE_CUSTOM_ERROR_HANDLER__', (FALSE === isset($_GET['error'])));
	 
	 if (TRUE === constant('__USE_CUSTOM_ERROR_HANDLER__'))
	 {
		 if (phpversion() > 5 && defined('E_DEPRECATED')) 
		 { 
			ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
			error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
		 } 
		 else 
		 {
			 ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING);
			 error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		 } 
		 
		 require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php'); 
	 	 set_error_handler('callSiteException');
		 set_exception_handler('callSiteException');
		 register_shutdown_function('applicationFatalError');
		 ini_set('display_errors', __ERROR_DISPLAY__ ? 'On' : 'Off');  
	 }
 	 else
	 {
		 restore_error_handler();
		 restore_exception_handler();
		 ini_set('display_errors', 'On');  
	 	 ini_set('error_reporting', ((version_compare(PHP_VERSION,5,'>=') && version_compare(PHP_VERSION,6,'<')) ? E_ALL^E_STRICT : E_ALL)); 
	 }
	 
	 
	 
	/**
	 * SMTP SETTINGS
	 */
	 if ((bool) __USE_SMTP_SETS__) { 
		ini_set('smtp_server',	 __SMTP_HOST__); 
		ini_set('smtp_port',	 __SMTP_PORT__); 
		ini_set('default_domain',__SITE_DOMAIN__); 
		ini_set('auth_username', __SMTP_USER__); 
		ini_set('auth_password', __SMTP_PASS__); 
		ini_set('force_sender',  __SMTP_SENDER__); 
	 }
	 
	
	/*
	ini_set(
		   'error_log', '/var/logs/adzaro/phpsiteerror-' .
		   __SITE_NAME__ . '_'.
		   str_replace("\n", '', '192-168-2-21') . '_' .
		   $intSiteId . '-' . date('Ymd') . '.log'
   	);
	if(
		(isset($_SERVER['REMOTE_ADDR'])) &&
		($_SERVER['REMOTE_ADDR'] == __DEBUGGING_IP__ ||
		($_SERVER['REMOTE_ADDR'] == '74.57.52.93'))
	) {
		   //error_reporting(E_ALL ^ E_NOTICE);  //Warning Output sur livebucks ???
		   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		   if (phpversion() > 5 && defined('E_DEPRECATED')) {
				   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED );
		   }
		   
		   if ($_SERVER['REMOTE_ADDR'] == __DEBUGGING_IP__ && $_GET['deperror'] == 1 ) {
				   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING );
		   }
		   
		   if (
				($_SERVER['REMOTE_ADDR'] == __DEBUGGING_IP__) &&
				($_GET['debugError'] == 1)
			) {
				   error_reporting(30719);
		   }
		   ini_set('display_errors', '1');
		   
	} else {
		   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		   if (phpversion() > 5 && defined('E_DEPRECATED')) {
				   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED );
		   }
	}
	*/

	
	/**
	 * The following will calculate the __APLICATION_CLASS_PATH__ constant 
	 * 		Ex: for a __APPLICATION_ROOT__ value of /application/core the class path becomes APPLICATION::CORE 
	 *		To override this setting, set the __APPLICATION_CLASS_PATH__ constant in config.php
	 */
	 if (! defined("__APPLICATION_CLASS_PATH__")) 
	 {
		$strApplicationClassPath = str_replace(__SITE_ROOT__, DIRECTORY_SEPARATOR, __APPLICATION_ROOT__);
		$strApplicationClassPath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $strApplicationClassPath);
		$strApplicationClassPath = (
		strcmp(substr($strApplicationClassPath, strlen($strApplicationClassPath) - 1), DIRECTORY_SEPARATOR)	== 0 ?
			substr($strApplicationClassPath, 0, strlen($strApplicationClassPath) - 1) : $strApplicationClassPath
		);
		$strApplicationClassPath = strtoupper(str_replace(
			DIRECTORY_SEPARATOR, '::', (strcmp($strApplicationClassPath[0], DIRECTORY_SEPARATOR) == 0 ? substr($strApplicationClassPath, 1) : $strApplicationClassPath)
		)); 
		
		define('__APPLICATION_CLASS_PATH__', $strApplicationClassPath);
	 }
	
	
	/**
	  * __APPLICATION_CLASS_REAL_PATH__ defined the application class path but in a case sensitive manner
	  *	its the difference between APPLICATION::CORE::CLASS_NAME and Application::Core::Class_Name
	  * its useful when using the method SHARED_OBJECT::requireLibrary() instead of SHARED_OBJECT::getObjectFromPackage()
	  */
	 if (! defined("__APPLICATION_CLASS_REAL_PATH__")) 
	 {
		$strApplicationClassPath = str_replace(__SITE_ROOT__, DIRECTORY_SEPARATOR, __APPLICATION_ROOT__);
		$strApplicationClassPath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $strApplicationClassPath);
		$strApplicationClassPath = (
		strcmp(substr($strApplicationClassPath, strlen($strApplicationClassPath) - 1), DIRECTORY_SEPARATOR)	== 0 ?
			substr($strApplicationClassPath, 0, strlen($strApplicationClassPath) - 1) : $strApplicationClassPath
		);
		$strApplicationClassPath = str_replace(
			DIRECTORY_SEPARATOR, '::', (strcmp($strApplicationClassPath[0], DIRECTORY_SEPARATOR) == 0 ? substr($strApplicationClassPath, 1) : $strApplicationClassPath)
		); 
		define('__APPLICATION_CLASS_REAL_PATH__', $strApplicationClassPath);
	 }
	 
	try{	 
		/**
		 * Include Required Classes
		 */
		if (FALSE === isset($_GET['noIni'])) 
		{
			//
			// Web Request Begin
			//
			require_once(__APPLICATION_ROOT__ . '/application.php');
			if (FALSE === (class_exists('ADMIN_APPLICATION')))
			{
				// This is in a '@' error handler because the 'admin' application is not required in most cases. 
				// The base Application class can handle all the overhead except within the admin section
				@include_once(
					(defined('__APPLICATION_ADMIN_PATH__') ? constant('__APPLICATION_ADMIN_PATH__') : constant('__SITE_ROOT__') . '/admin') . '/mvc/application/admin_application.php'
				  ); 	
			}
			/*
			class_exists('ADMIN_APPLICATION')  
				| require_once(
					(defined('__APPLICATION_ADMIN_PATH__') ? constant('__APPLICATION_ADMIN_PATH__') : constant('__SITE_ROOT__') . '/admin') . '/mvc/application/admin_application.php'
				  );
			*/ 
			/**
			 *
			 * Reinstated so that all files have access to the application.
			 */
			
			
			if (FALSE === (dirname($_SERVER["REQUEST_URI"]) === str_replace('//', '/', constant('__ROOT_DIR__') . '/install'))) {
				$Application = APPLICATION::getInstance();
				$Application->bootstrap();
				if (! isset($_SERVER['CRON_ACTIVE'])) 
				{
					$Application->webControllerInitiate();
				}
			}
		} // no ini eof
	} 
	catch (Exception $e) 
	{
		require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php'); 
		SITE_EXCEPTION::throwNewException($e);
	}