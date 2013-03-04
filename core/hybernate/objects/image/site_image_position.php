<?php
	/**
	 * SITE_IMAGE_POSITION Administration Class
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
	  	CREATE TABLE `site_image_position` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(250) DEFAULT NULL,
		  `description` varchar(500) DEFAULT NULL,
		  `width` int(11) NOT NULL DEFAULT '0',
		  `height` int(11) NOT NULL DEFAULT '0',
		  `active` int(4) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1
	  */
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 
	 class SITE_IMAGE_POSITION extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
		 * This method makes sure a directory exists for an image position, if it
		 * doesnt exists, it attempts to create it.
		 * @param: $intImagePosition - int - The image position
		 * @return - $blnContinue - Boolean
		 */
		public static function validateImagePostionDirectory($intImagePosition = NULL) {
			$blnContinue = is_dir(__ITEM_IMAGES_DIRECTORY__ . DIRECTORY_SEPARATOR . $intImagePosition);	
			if (! (bool) $blnContinue) {
				$blnContinue = mkdir(__ITEM_IMAGES_DIRECTORY__ . DIRECTORY_SEPARATOR . $intImagePosition, 0777);	
			}
			return ($blnContinue);
		}
		
		/**
		 * This method returns the direcotry path for a position.
		 * if the $intImagePosition param is null, it will return the 
		 * original direcotry image path
		 * @param: $intImagePosition - int - The image position
		 * @return - $strImageDirectoryPath - String - The image position directory path
		 */
		public static function getImagePositionDirecotryPath($intImagePosition = NULL) {
			$strImageDirectoryPath = __ITEM_IMAGES_DIRECTORY__ . DIRECTORY_SEPARATOR . (is_null($intImagePosition) ? 0 : $intImagePosition);	
			return ($strImageDirectoryPath);
		}
		
		/**
		 * This method returns the displayable direcotry path for a position.
		 * if the $intImagePosition param is null, it will return the 
		 * original direcotry image path
		 * @param: $intImagePosition - int - The image position
		 * @return - $strImageDirectoryPath - String - The image position directory path
		 */
		public static function getDisplayImagePositionDirecotryPath($intImagePosition = NULL) {
			$strImageDirectoryPath = str_replace(__SITE_ROOT__, '', __ITEM_IMAGES_DIRECTORY__) . DIRECTORY_SEPARATOR . (is_null($intImagePosition) ? 0 : $intImagePosition);	
			return ($strImageDirectoryPath);
		}
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>