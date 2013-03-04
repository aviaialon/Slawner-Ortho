<?php
	/**
	 * SITE_IMAGE_POSITION_AVAILABILITY Administration Class
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
	 /*
	  * TABLE DEFINITION:
	  	
		CREATE TABLE `site_image_position_availability` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `imageId` int(11) NOT NULL,
		  `imagePositionId` int(11) NOT NULL,
		  PRIMARY KEY (`id`,`imagePositionId`),
		  KEY `idx_imgId_imgPosId` (`imageId`,`imagePositionId`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1
	  */	
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 
	 class SITE_IMAGE_POSITION_AVAILABILITY extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
		
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
		}
	}
?>