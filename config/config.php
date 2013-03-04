<?php
	/**
	 * Core configuration file.

	 * @package		NONE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */
	 $CONFIG_LOADED = true; 
	 
	 // Set the default timezone.
	 if (function_exists('date_default_timezone_set')) 
	 {
	 	date_default_timezone_set('America/Montreal');
	 }
	 
	 define("DS" 					, DIRECTORY_SEPARATOR );
	 if (false === defined('__ROOT_DIR__')) {
	 	define("__ROOT_DIR__"			, ""); 	# This config is used if the site starts from a directory
	 }
	 define("__HAS_CONFIG__"		, true);  
	 define("__SITE_NAME__"			, "Slawner Ortho");
	 define("__SITE_DOMAIN__"		, ""); 
	 define("__MAIL_DOMAIN__"		, "slawner.com"); 
	 define("__SITE_TITLE__"		, "Improving your quailty of life"); 
	 define("__ROOT_URL__"			, "http" . ((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0)  ? 's' : '') . 
									  "://" . filter_var((isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") . __ROOT_DIR__, FILTER_SANITIZE_URL));  
	 define("__ROOT_PATH__"			, "http" . ((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0)  ? 's' : '') . 
									  "://" . filter_var((isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") . __ROOT_DIR__, FILTER_SANITIZE_URL));  
	 define("__SITE_URL__"			, "http" . ((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0)  ? 's' : '') . 
									  "://" . filter_var((isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") . __ROOT_DIR__, FILTER_SANITIZE_URL));  
	 define("__SSL_ROOT__"			, $_SERVER['DOCUMENT_ROOT']);
	 if (false === defined('__SITE_ROOT__')) {
		define("__SITE_ROOT__"			, (((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0))  ? 
											constant('__SSL_ROOT__') . constant('DIRECTORY_SEPARATOR')  .  constant('__ROOT_DIR__') :  $_SERVER['DOCUMENT_ROOT'] .  __ROOT_DIR__));
	 }
	 
	 define("__SSL_URL__"					, "http://slawner.dns04.com/"); 
	
	 if (false === defined('__APP_CONTROLLER_ROOT_BASE__'))	
	 {
		 define("__APP_CONTROLLER_ROOT_BASE__"	, __SITE_ROOT__ . '/app'); 	# This is where the controllers and views are stored.	 
		 define("__APP_CONTROLLER_DIR__"		, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'controllers'); 	# This is where the controllers are stored.
		 define("__APP_VIEW_DIR__"				, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'views'); 		# This is where the views are stored.
		 define("__APP_PARTIAL_DIR__"			, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'partials'); 	# This is where the partials are stored.
		 define("__APP_MODEL_DIR__"				, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'model'); 		# This is where the partials are stored.
		 
		 # This is where the application class files are stored.
		 define("__APPLICATION_ROOT__"			,  constant('__SITE_ROOT__') . '/core');		
		 define("__APPLICATION_CORE__"			, __APPLICATION_ROOT__);		# Duplication
		 define("__APPLICATION_VERSION__"		, "6.3.5");	 
	 }
	 
	 /**
	  * This is the path used for temporary uploads and storage
	  */
	 define("__DEV_NULL_PATH__"				, __SITE_ROOT__ . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "tmp");
	 
	 /**
	  * Item images directory
	  */
	  define("__ITEM_IMAGES_DIRECTORY__" 	 , __SITE_ROOT__ . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "uploads");   	
	  define("__TMP_ITEM_IMAGES_DIRECTORY__" , __ITEM_IMAGES_DIRECTORY__ . DIRECTORY_SEPARATOR . "tmp");  
	 
	 /**
	  * This is the base path used for static resources (images, css, js etc..)
	  */
	 if (false === defined('__STATIC_RESOURCES_PATH__'))
	 	define("__STATIC_RESOURCES_PATH__"		, __ROOT_URL__ . DIRECTORY_SEPARATOR . "static/");
	 
	 /*
	  * Define the application class path here to override the compiled version
	  */
	  // define("__APPLICATION_CLASS_PATH__", '/application');
	 
	 /**
	  * Debugging : Can use multiple IPs seperated by commas
	  */
	  define("__DEBUGGING_IP__"	, "184.144.164.9,76.65.129.110");   										
	  
	 /**
	  * Encryption Key Settings
	  */
	  define("__ENCRYPTION_KEY__"	, "1WANT2BAMI11I0NA1R3");   
	  
	 /**
	  * CDN Settings
	  */
	  define("__CDN_URL__"			, "http://" . filter_var((isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") . __ROOT_DIR__, FILTER_SANITIZE_URL));  
	  											
	 /**
	  * Error handling Settings 
	  */
	 define("__ERROR_PAGERED__"		, __APPLICATION_ROOT__ . "/http-errors/error.php");
	 define("__ERROR_DISPLAY__"		, true); 	//	Display errors on screnn
	 define("__ERROR_MAIL__"		, false);  	//	Send errors by email?
	 define("__QUEUE_ERROR_MAIL__"	, true); 	// 	Send errors in mail queue pool
	 
	/**
	  * Database Settings
	  */
	 define("__DATABASE_HOST__"		, "<DATABASE HOST>"); 
	 define("__DATABASE_PORT__"		, 3306); 
	 define("__DATABASE__"			, "<DATABASE NAME>"); 
	 define("__DATABASE_UNAME__"	, "<DATABASE USERNAME>");
	 define("__DATABASE_PASS__"		, "<DATABASE PASSWORD>"); 
	 define("__DATABASE_DRIVER__"	, "<DATABASE DRIVER>"); // mysql, sybase, sqllite, mssql
	 
	 /**
	  * ROOT ACCESS LEVEL Database Settings
	  */
	 define("__ROOT_DATABASE_UNAME__"	, __DATABASE_UNAME__);
	 define("__ROOT_DATABASE_PASS__"	, __DATABASE_PASS__); 
	 
	 
	 define("__CACHE_DIRECTORY__"		, __SITE_ROOT__ . "cache/");
	 
	 /**
	  * Contact Settings
	  */ 
	 define("__CONTACT_PHONE__"		, /*"514-331-5858"*/ "1 (866) 752.9637"); 
	 define("__ERROR_EMAILS__"		, "aviaialon@gmail.com"); 
	 define("__ADMIN_EMAIL__"		, "aviaialon@gmail.com"); 
	 define("__MODERATOR_EMAIL__"	, "info@slawner.dns04.com"); 
	 define("__INFO_EMAIL__"		, "info@" . __MAIL_DOMAIN__); 
	 define("__SALES_EMAIL__"		, "sales@" . __MAIL_DOMAIN__); 
	 
	 /**
	  * SMTP Settings
	  */
	 define("__USE_SMTP_SETS__"	, false); # !! Weather or not to use these SMTP settings.
	 define("__SMTP_SSL__"		, false);
	 define("__SMTP_SSL_TYPE__"	, "tls"); 
	 define("__SMTP_HOST__"		, ""); 
	 define("__SMTP_PORT__"		, 587); 
	 define("__SMTP_USER__"		, ""); 
	 define("__SMTP_PASS__"		, ""); 
	 define("__SMTP_SENDER__"	, "notice@" . __SITE_DOMAIN__); 
	 
	 /**
	  * Facebook Application Settings 
	  *		- http://www.facebook.com/developers/apps.php
	  *		- http://developers.facebook.com/docs/reference/fbml/comments_(XFBML)
	  */
	 define("__FB_APPLICATION_ID__"	, "<INPUT CONFIG>");
	 define("__FB_API_KEY__"		, "<INPUT CONFIG>"); // <-- wtf?
	 define("__FB_APP_SECRET_ID__"	, "<INPUT CONFIG>");
	 define("__FACEBOOK_PAGE_URL__"	, "https://www.facebook.com/SlawnerOrtho");
	 define("__FB_USE_COOKIES__"	, true);
	 
	  /**
	  * Yahpp Application Settings 
	  *		- https://developer.apps.yahoo.com/wsregapp/
	  *		- https://developer.apps.yahoo.com/dashboard/createKey.html (Create and API key)
	  *		- INFO: http://youhack.me/2011/06/15/integrate-login-system-with-yahoo-connect-using-oauth/
	  */
	 define("__YAHOO_APPLICATION_ID__"	, "<INPUT CONFIG>"); // Used in the suggestion engine (did you mean...)
	 
	 /**
	  * Phono API Key
	  *		- http://phono.com/users/new
	  * 
	  */
	 define("__PHONO_API_KEY__"	, "<INPUT CONFIG>");
	 
	 /**
	  * Recaptcha Public key
	  *		- http://phono.com/users/new
	  */
	 define("__RECAPTCHA_API_PUBLIC_KEY__"	, "<INPUT CONFIG>");
	 define("__RECAPTCHA_API_PRIVATE_KEY__"	, "<INPUT CONFIG>");
	 
	 
	 /**
	  * Twitter Application Settings 
	  *		- http://twitter.com/oauth_clients
	  *		- http://www.jaisenmathai.com/articles/twitter-async-documentation.html#methodnames
	  */
-	 define("__TWITTER_CONSUMER_KEY__"	, "<INPUT CONFIG>");
-	 define("__TWITTER_SECRET_KEY__"	, "<INPUT CONFIG>");
	 define("__TWITTER_PAGE_URL__"		, "http://twitter.com/slawnerortho");
	
	 /**
	  * Paypal API access
	  *		- https://www.paypal.com/ca/cgi-bin/webscr?cmd=_profile-api-signature
	  * The relm URL is set in https://devportal.x.com/ application. and MUST be a top level URL like www.yoursite.com 
	  * Not www.yoursite.com/subfolder
	  */
	 define("__PAYPAL_API_RELM_URL__"	, "http://slawner.com"); 
	 define("__PAYPAL_API_UNAME__"		, "aviaialon@gmail.com");
-	 define("__PAYPAL_API_PASSORD__"	, "<INPUT CONFIG>");
	 define("__PAYPAL_API_SIGNATURE__"	, "<INPUT CONFIG>");
	 
	 
	 /**
	  * Stripe API access
	  *		- https://manage.stripe.com/
	  */
	 define("__STRIPE_API_TEST_SECRET_KEY__"		, "<INPUT CONFIG>"); 
	 define("__STRIPE_API_TEST_PUBLISH_KEY__"		, "<INPUT CONFIG> ");
-	 define("__STRIPE_API_LIVE_SECRET_KEY__"		, "<INPUT CONFIG>");
	 define("__STRIPE_API_LIVE_PUBLISH_KEY__"		, "<INPUT CONFIG>");
	 
	 /**
	  * LinkedIn API access
	  *		- https://developer.linkedin.com/documents/linkedins-oauth-details
	  *		- https://www.linkedin.com/secure/developer	
	  */
	 define("__LINKEDIN_API_KEY__"			, "<INPUT CONFIG>"); 
	 define("__LINKEDIN_APP_SECRET_ID__"	, "<INPUT CONFIG>");
	 define("__LINKEDIN_PROFILE_PAGE_URL__"	, "");
	 
	 /**
	  * FourSquare API access
	  *		- https://foursquare.com/oauth/
	  */
	 define("__FOURSQUARE_API_KEY__"		, "<INPUT CONFIG>"); 
	 define("__FOURSQUARE_APP_SECRET_ID__"	, "<INPUT CONFIG>");
	 
	 
	 /**
	  * GitHub Application Settings
	  *		- https://github.com/settings/applications
	  *		- Docs: http://developer.github.com/v3/oauth
	  *
	  *		Please note that the redirect_uri parameter is optional. 
	  *		If left out, GitHub will redirect users to the callback 
	  *		URL configured in the OAuth Application settings. If provided, 
	  *		the redirect URL must match the callback URL’s host.
	  */
	 define("__GITHUB_CLIENT_ID__"		, "<INPUT CONFIG>"); 
	 define("__GITHUB_APP_SECRET_ID__"	, "<INPUT CONFIG>");
	 
	 /**
	  * GOOGLE ANALYTICS
	  */
	 define("__GA_CODE__"					, "<INPUT CONFIG>");  # Google analytics tracking code
	 
	 /**
	  * -- 	Make sure to add authorised redirect URLs here in the Legacy.Contact.Importer Application.
	  *		Go to https://code.google.com/apis/console, select the "Google Contact Importer" Application
	  *		Then click on "Edit Settings" and add the new domain. this is required for every site
	  */
	 define("__GOOGLE_CLIENT_ID__"			, "<INPUT CONFIG>"); 
	 define("__GOOGLE_CLIENT_SECRET_KEY__"	, "<INPUT CONFIG>");
	 
	 
	 /**
	  * INTERNAL PAGE TRACKNG
	  */
	 define("__INTERNAL_PAGE_TRACKING__", 	 false); # Enable's the internal page tracking system (@page_views) [TRUE/FALSE] 
	 define("__ASYNCRONOUS_PAGE_TRACKING__", true); # Enable's the internal ASYNC page tracking system (@page_views) [TRUE/FALSE] 
	 
	 /**
	  * GEO LCATION API KEY
	  */
	 // http://www.ipinfodb.com
	 // u: aviaialon
	 // p: merlin
	 define("__GEO_LCATION_API_KEY__", "<INPUT CONFIG>");
	 define("__GEO_LCATION_API_DOMAIN__", "api.ipinfodb.com/v2/");
	 
	 /**
	  * CRONTAB SETTINGS
	  */
	 define("__LOG_CRON__"			, true);
	 
	/**
	 * Session 
	 * @info: Curently used in session class [@package: classes::session].
	 */
	 define("__SESSION_EXPIRATION_SECONDS__", 3600); 
	 define("__SESSION_NAME__"				, trim(preg_replace('/[^A-Za-z0-9-_]/','', str_replace(array(" ", "-", "."), "_", strtoupper(__SITE_NAME__)))) . "_SESS"); 
	 
	/**
	 * CAPTCHA PUBLIC KEY
	 * @info: Curently used in session class [@package: classes::session].
	 */
	 define("__CAPTCHA_PUBLIC_KEY__", 	"<INPUT CONFIG>");
	 define("__CAPTCHA_PRIVATE_KEY__", "<INPUT CONFIG>"); 
	 
	 /*
	 define("__SESSION_SAVE_PATH__", __SITE_ROOT__ . "/sessions"); 
	 session_save_path(__SESSION_SAVE_PATH__);
	 session_start();
	 */	
	 
	 /**
	  * Memcache Settings
	  */ 
	  define("__MEMCACHE_ENABLED__", 		false); 
	  define("__MEMCACHE_SERVER_IP__", 		"<INPUT CONFIG>"); 
	  define("__MEMCACHE_SERVER_PORT__", 	11211); 
	  define("__MEMCACHE_UNIQUE_KEYS__", 	true); 	# this value sets whether the 'keys' set in memcache should be unique (appended by the __SITE_NAME__) 
	  
	 /**
	  * User Management Constants 
	  * This section manages the user portion of the site and is crutial for the 
	  * proper execution of the site.
	  */ 
	  define("__USER_LOGIN_URL__", 				__ROOT_URL__ . "/users/login"); 				# REWRITE ENGINE -> /login/index.php?rel=login
	  define("__USER_SIGNUP_URL__", 			__ROOT_URL__ . "/index.php"); 					# REWRITE ENGINE -> /login/index.php?rel=register
	  define("__USER_PASS_REMINDER_URL__", 		__ROOT_URL__ . "/users/reset-password"); 		# REWRITE ENGINE -> /login/index.php?rel=reminder
	  define("__USER_CHANGE_PASS_URL__", 		__ROOT_URL__ . "/index.php"); 					# REWRITE ENGINE -> /login/index.php?rel=change-password
	  define("__USER_CONFIRM_MEMBERSHIP_URL__", __ROOT_URL__ . "/index.php"); 					# REWRITE ENGINE -> /login/index.php?rel=confirm-membership
	  define("__USER_FACEBOOK_LOGIN_URL__", 	__ROOT_URL__ . "/users/auth/oauth:facebook"); 	# REWRITE ENGINE -> /login/index.php?rel=facebook
	  define("__USER_TWITTER_LOGIN_URL__", 		__ROOT_URL__ . "/users/auth/oauth:twitter"); 	# REWRITE ENGINE -> /login/index.php?rel=twitter
	  define("__USER_GOOGLE_LOGIN_URL__", 		__ROOT_URL__ . "/users/auth/oauth:google"); 	# REWRITE ENGINE -> /login/index.php?rel=google
	  define("__USER_LINKEDIN_LOGIN_URL__", 	__ROOT_URL__ . "/users/auth/oauth:linkedin"); 	# REWRITE ENGINE -> /login/index.php?rel=linkedin
	  define("__USER_NOTICE_URL__", 			__ROOT_URL__ . "/users/notice"); 				# REWRITE ENGINE -> /login/index.php?rel=notice
	  define("__USER_PROFILE_URL__", 			__ROOT_URL__ . "/users/profile"); 				# REWRITE ENGINE -> /login/index.php?rel=profile
	  
	  //	  
	  // Secure directories and sub directories...
	  //		This variable is an array of secured
	  //		directories ex: PATH => ACCESS_LEVEL
	  //		 - The PATH variable is the directory path to secure starting from 	__SITE_ROOT__
	  //		 - The ACCESS_LEVEL variable is the access level required to access the directory
	  //		   see SITE_USERS for defined access levels	
	  //	
	  $GLOBALS["__USER_SECURED_DIRECTORIES__"] = array(
	 	"/users/service" 				=> 1,
	 	"/users/profile" 				=> 1,
	 	"/users/dashboard" 				=> 1,
	 	"/admin" 						=> 10,
	 	"/backstore" 					=> 10,
	 	"/news/manage" 					=> 10,
	 	"/news/preview" 				=> 10,
	 	"/patient-profiles/manage" 		=> 10,
	 	"/patient-profiles/preview" 	=> 10
	  );
	  
	  /**
	   * 
	   * ************************************************************************
	   * ********************* SITE CONFIGURATION EOF ***************************
	   * ************************************************************************
	   * 
	   */
	  
	  

	 /*
	  * Include the main config file here, which bootstraps the application
	  */
	  require_once(constant('__APPLICATION_ROOT__') . DIRECTORY_SEPARATOR . 'config/config.php'); 
