<?php
/**
 * This class represents the partial base implementation.
 * All partials must implement the partial base class
 * 
 * @package		{APPLICATION_CORE}::HTTP
 * @subpackage	none
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */		
abstract class PARTIAL_BASE extends SITE_EXCEPTION
{
	protected $strData = NULL;
	
	protected function getData()
	{
		return ($this->strData);
	}
	
	/**
	 * This method outputs the partial's signature as a comments
	 */
	protected static function getSignature()
	{
		echo ("\n <!-- partial: " . str_replace('PARTIAL_', '', get_called_class()) . " --> \n");
	}
	
	/**
	 * Support method for getSignature
	 */
	public static function signature()
	{
		self::getSignature();
	}
	
	/**
	 * This method is called prior to render.
	 * All queries and processing should be done here
	 * 
	 * @param array $arrparameters
	 * @return void
	 */
	abstract public function execute(array $arrparameters);
	
	/**
	 * This method is called to render the partial's data
	 * 
	 * @return string - The partials execution results
	 */
	abstract public function render();
}