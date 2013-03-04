<?php
/**
 * USER_MANAGER Administration Class
 * This class manages users, user types and roles
 *
 * @package		{__APPLICATION_CLASS_PATH__}::OAUTH
 * @subpackage	none
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");
 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::USERS::USERLOGIN_LOG");
	 
 class USER_MANAGER extends SITE_EXCEPTION {
 	/**
	 * Instance Reference
	 * @var USER_MANAGER
	 */
 	private static $USER_MANAGER_INSTANCE 		= NULL;
 	private static $IS_USER_MANAGER_INSTANCE 	= FALSE;
	
	public function __construct() 
	{
		if (TRUE === USER_MANAGER::$IS_USER_MANAGER_INSTANCE)
		{
			return ($this);
		}
		else
		{
			parent::raiseException('Please use the getInstance method to invoke ' . __CLASS__);
		}
	}
	
	public static final function getInstance()
	{
		if(NULL === USER_MANAGER::$USER_MANAGER_INSTANCE)
		{
			USER_MANAGER::$IS_USER_MANAGER_INSTANCE = TRUE;
			USER_MANAGER::$USER_MANAGER_INSTANCE = new USER_MANAGER();
		}
		
		return (USER_MANAGER::$USER_MANAGER_INSTANCE);
	}
 }