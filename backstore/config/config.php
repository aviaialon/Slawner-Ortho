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
 
/**
 * 
 * ************************************************************************
 * ********************* SITE CONFIGURATION EOF ***************************
 * ************************************************************************
 * 
 */
 
 // Config overrides
 define("__ROOT_DIR__"					, ""); 	# This config is used if the site starts from a directory
 define("__SITE_ROOT__"					, (((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0))  ? 
										  constant('__SSL_ROOT__') . constant('DIRECTORY_SEPARATOR')  .  constant('__ROOT_DIR__') :  $_SERVER['DOCUMENT_ROOT'] .  constant('__ROOT_DIR__')));
 define("__BASE_ROOT__"					, (((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0))  ? 
										  constant('__SSL_ROOT__') . constant('DIRECTORY_SEPARATOR')  :  $_SERVER['DOCUMENT_ROOT']));	
 define("__ROOT_ADM_URL__"				, 	"http" . ((isset($_SERVER['HTTPS'])) && (strcmp(strtolower($_SERVER['HTTPS']), "on") == 0)  ? 's' : '') . 
									  		"://" . filter_var((isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "") . '/backstore', FILTER_SANITIZE_URL)); 				  
				  
 define("__APP_CONTROLLER_ROOT_BASE__"	, constant('__SITE_ROOT__') . '/backstore/app'); 	# This is where the controllers and views are stored.	 
 define("__APP_CONTROLLER_DIR__"		, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'controllers'); 	# This is where the controllers are stored.
 define("__APP_VIEW_DIR__"				, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'views'); 		# This is where the views are stored.
 define("__APP_PARTIAL_DIR__"			, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'partials'); 	# This is where the partials are stored.
 define("__APP_MODEL_DIR__"				, __APP_CONTROLLER_ROOT_BASE__ . DIRECTORY_SEPARATOR . 'model'); 		# This is where the partials are stored.
 
 # This is where the application class files are stored.
 define("__APPLICATION_ROOT__"			,  constant('__BASE_ROOT__') . '/core');		
 define("__APPLICATION_CORE__"			, __APPLICATION_ROOT__);		# Duplication
 define("__APPLICATION_VERSION__"		, "6.3.5");	 	

 define("__STATIC_RESOURCES_PATH__"		, constant('__ROOT_ADM_URL__') . "/static/");
 
 /*
  * Include the site config file here, which bootstraps the application
  */
  require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'config/config.php'); 

