<?php
class_exists('OBJECT_BASE') 
		| require_once(constant('__APPLICATION_ROOT__') . '/interface/object_base.php');  	
		
class_exists('SHARED_OBJECT')  
		| require_once(constant('__APPLICATION_ROOT__') . '/hybernate/shared_object.' . constant('__APPLICATION_VERSION__') . '.php');
		
class_exists('PDO_DATABASE_SINGLETON')  
		| require_once(constant('__APPLICATION_ROOT__') . '/database/pdo_database_singleton_interface.php'); 	 		
/**
 * PDO_DATABASE Class File
 * This is the main database management class. 
 *
 * @category   PHP5
 * @package    DATABASE
 * @subpackage {APPLICATION_CORE}
 * @author     Avi Aialon <aviaialon@gmail.com>
 * @copyright  2012 DeviantLogic. Inc. All rights reserved
 * @license    http://www.deviantlogic.ca/license
 * @version    SVN: $Id: pdo_database.php 290967 2011-10-19 21:46:23Z crinu $
 * @link       SVN: $HeadURL: svn+ssh://ubuntu.dns05.com/var/www/svn-repositories/platform $
 * @since      2012-04-18
 */

/**
 * PDO_DATABASE Management Class 
 *
 * @category   PHP5
 * @package    DATABASE
 * @subpackage APPLICATION_CORE
 * @author     Avi Aialon <aviaialon@gmail.com>
 */
 interface 			INTERFACE_DATABASE_BASE 					{}
 interface 			INTERFACE_DATABASE_ERROR 					{}
 class 				PDO_DATABASE 
 extends			PDO_DATABASE_SINGLETON_INTERFACE
 implements 		INTERFACE_DATABASE_BASE, INTERFACE_DATABASE_ERROR
 {
	/**
	 * This method is self called after instantiation.
	 * it configues the pdo database object
	 *
	 * @access 	protected, static, final
	 * @param	array $arrRconnectionsConfig - Connection info array
	 * @return 	void
	 */
	 protected final function onGetInstance(array $arrRconnectionsConfig = array())
	 {
		parent::___connect($arrRconnectionsConfig);
	 }
	 
	  
	/**
	 * Prevents the singleton enforcer from cloning
	 * 
	 * @access 	public
	 * @return	void
	 */
	 public final function __clone()
	 {
		throw new Exception("Access to " . __CLASS__ . " can only be instantiated via " . __CLASS__ . "::getInstance()");
	 }
	
   /**
	* Prevents the singleton enforcer from __wakeup (unserializing)
	*
	* @access 	public
	* @return	void
	*/
	 public final function __wakeup()
	 {
		throw new Exception("Access to " . __CLASS__ . " can only be instantiated via " . __CLASS__ . "::getInstance()");	
	 }
 }