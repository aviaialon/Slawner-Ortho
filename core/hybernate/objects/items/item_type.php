<?php
	/**
	 * ITEM_TYPE Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 class ITEM_TYPE extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>