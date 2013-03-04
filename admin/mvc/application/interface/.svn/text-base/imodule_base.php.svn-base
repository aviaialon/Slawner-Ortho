<?php
/**
 * IMODULE_BASE Interface Base Class
 * This class represents the interface base class for modules
 *
 * @package		{ADMIN}::APPLICATION
 * @subpackage	INTERFACE
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
interface IMODULE_BASE 
{
	/**
	 * Abstraction methods
	 */
	
	/**
	 * Outputs the modules view data
	 * @param array $arrParameters - GET / POST / REQUEST data
	 */
	public 				function renderOutput		(array $arrParameters);
	
	/**
	 * sets the INI config which can be overriden by config.ini
	 * @param array $arrConfig - the config data
	 */
	public static		function setIniConfig		(array $arrConfig);
	
	/**
	 * gets the module's display name
	 */
	public static 		function getDisplayName		();
	
	/**
	 * Gets the array of sub-menu declared actions., primarily used in the menu
	 * @return array
	 */
	public static 		function getSubMenuActions	();
} 