<?php
	/**
	 * MENU_GROUP Administration Class
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
	 class MENU_GROUP extends SHARED_OBJECT {
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }

	protected function onBefore_getInstance() 
	{
		$this->setConstructIntegrity ( SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE );
		$this->setObjectCacheType ( SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE );
	}
	
	}
?>