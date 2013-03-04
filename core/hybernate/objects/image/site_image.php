<?php
	/**
	 * IMAGE Administration Class
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
	  
	  	CREATE TABLE `site_image` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `creationTimeDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `ownerClassId` int(11) NOT NULL DEFAULT '0',
		  `ownerObjectId` int(11) NOT NULL DEFAULT '0',
		  `imageExtension` varchar(50) NOT NULL,
		  `originalFileName` varchar(500) DEFAULT NULL,
		  `index` int(10) NOT NULL DEFAULT '0',
		  `active` int(4) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`),
		  KEY `itex_classId_ObjectId` (`ownerClassId`,`ownerObjectId`) USING BTREE
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 
	  */
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE_POSITION_AVAILABILITY");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE_POSITION");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::SITE_USERS");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::IO::IMAGE::IMAGE");
	 
	 class SITE_IMAGE extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		/**
		 * This method creates the item images from either a specified image position or all
		 * @param: $objItem - CLASSES::HYBERNATE::OBJECTS::ITEMS::ITEMS - The item
		 * @param: $intOriginalImageId - The original image id (position 0)
		 * @param: $intImagePosition - The image position ID (not required);
		 * @return - $blnReturn - Boolean
		 */
		public static function createItemImagePositions (
			$intOriginalImageId = NULL,
			$intImagePosition	= NULL
		) {
			$arrImagePositions = array();
			$blnReturn = true;
			$arrFilter = array('active' => 1);
			$strOriginalImage = "";
			
			$blnReturn = true; 
			
			if ($blnReturn) {
				// 1. Get the image position array
				if (
					(isset($intImagePosition)) &&
					(! is_null($intImagePosition)) &&
					((int) $intImagePosition)
				) {
					$arrFilter['id'] = (int) $intImagePosition;
				}
				$arrImagePositions = SITE_IMAGE_POSITION::getObjectClassView(array(
					'columns' 	=> 'id,width,height',
					'filter'	=> $arrFilter
				));
				$blnReturn = (
					((bool) sizeof($arrImagePositions)) &&
					((int) $intOriginalImageId)
				);
			}
			
			// 2. Get the original image object
			if ($blnReturn) {
				$objOriginalImage = SITE_IMAGE::getInstance(
					$intOriginalImageId,
					SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
				);
				$strOriginalImage = $objOriginalImage->getImagePath(); // No image position defined to return the default.
				$blnReturn = (
					((bool) $objOriginalImage->getId()) &&
					(strlen($strOriginalImage)) &&
					(file_exists(__SITE_ROOT__ . $strOriginalImage))
				);
			}
			
			// 3. Process that image according to position array
			if ($blnReturn) {
				foreach($arrImagePositions as $intIndex => $arrRow) {
					$blnReturn = (
						((int) $arrRow['id']) &&
						((int) $arrRow['width']) &&
						((int) $arrRow['height']) &&
						(strlen($objOriginalImage->getVariable('imageExtension'))) &&
						(SITE_IMAGE_POSITION::validateImagePostionDirectory($arrRow['id']))
					);
					if ($blnReturn) {
						// Next iterate through the image position array and create the image instances
						$objNewItemImagePosition = new IMAGE(
							$strOriginalImage,
							SITE_IMAGE_POSITION::getImagePositionDirecotryPath($arrRow['id']) . DIRECTORY_SEPARATOR .  
							$objOriginalImage->getId() . "." . $objOriginalImage->getVariable('imageExtension')
						);
						$objNewItemImagePosition->setQuality(100);
						$objNewItemImagePosition->setPreserveAspectRatio(false);
						$blnReturn = $objNewItemImagePosition->resize(
							(int) $arrRow['width'], 
							(int) $arrRow['height'],
							IMAGE::RESIZE_TYPE_NOT_BOX
						);

						// Next insert into the database the item_image_position
						$objItemImagePosition = SITE_IMAGE_POSITION_AVAILABILITY::getInstanceFromKey(array(
							'imageId' 			=> (int) $objOriginalImage->getId(),
							'imagePositionId' 	=> (int) $arrRow['id']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						
						$objItemImagePosition->setVariable('imageId', (int) $objOriginalImage->getId());
						$objItemImagePosition->setVariable('imagePositionId', (int) $arrRow['id']);
						$objItemImagePosition->save();
					}
				}
			}
			
			return ($blnReturn);
		}
		
		/**
		 * This method returns the image path according to a position
		 * @param: $intImagePosition - The image position (default is 0 - original image)
		 * @return - $strImagePath - The image path
		 */
		public function getImagePath($intImagePosition = NULL) {
			$blnContinue = ((bool) $this->getId() > 0);
			$strImagePath = ""; // TODO: Set the default image path here.
			if (
				($blnContinue)	&&
				(strlen($this->getVariable('imageExtension')))
			) {
				$strImagePath = str_replace(__SITE_ROOT__, '', SITE_IMAGE_POSITION::getImagePositionDirecotryPath($intImagePosition)) . 
								DIRECTORY_SEPARATOR . $this->getId() . "." . $this->getVariable('imageExtension');	
			}
			return ($strImagePath);
		}
		
		/**
		 * This method returns the REAL image path according to a position
		 * @param: $intImagePosition - The image position (default is 0 - original image)
		 * @return - $strImagePath - The image path
		 */
		public function getImageServerPath($intImagePosition = NULL) {
			$blnContinue = ((bool) $this->getId() > 0);
			$strImagePath = ""; // TODO: Set the default image path here.
			if (
				($blnContinue)	&&
				(strlen($this->getVariable('imageExtension')))
			) {
				$strImagePath = SITE_IMAGE_POSITION::getImagePositionDirecotryPath($intImagePosition) .
								DIRECTORY_SEPARATOR . $this->getId() . "." . $this->getVariable('imageExtension');	
			}
			return ($strImagePath);
		}
		
		/**
		 * This method returns the image path according to a position - STAIC ACCESS
		 * @param: $intImagePosition - int - The image position (default is 0 - original image)
		 * @param: $intImageId - int - The image ID
		 * @param: $strImageExtension - string - The image extension
		 * @return - $strImagePath - The image path
		 */
		public static function getImageDisplayPath($intImagePosition = NULL, $intImageId=NULL, $strImageExtension=NULL) {
			$blnContinue = ((bool) $intImageId > 0);
			$strImagePath = ""; // TODO: Set the default image path here.
			if (
				($blnContinue)	&&
				(strlen($strImageExtension))
			) {
				$strImagePath = SITE_IMAGE_POSITION::getDisplayImagePositionDirecotryPath($intImagePosition) . 
								DIRECTORY_SEPARATOR . $intImageId . "." . $strImageExtension;	
			}
			return ($strImagePath);
		}
		
		/**
		 * This method returns the temporary image DISPLAY path.
		 * @param: $intImagePosition - int - The image position (default is 0 - original image)
		 * @param: $intImageId - int - The image ID
		 * @param: $strImageExtension - string - The image extension
		 * @return - $strImagePath - The image path
		 */
		public static function getTempDirImageDisplayPath() {
			return (str_replace(constant('__SITE_ROOT__'), "", constant('__TMP_ITEM_IMAGES_DIRECTORY__')) . DIRECTORY_SEPARATOR);
		}
		
		
		/**
		 * This method copies a temp image to position 0
		 * @param: $strImageSourcePath - The image position (default is 0 - original image)
		 * @return - $strImagePath - The image path
		 */
		public static function createPosition0Image($strImagePath = NULL, $intOwnerClassId=NULL, $intOwnerObjectId=NULL) {
			$objImage = new SITE_IMAGE();
			$blnContinue = (
				((bool) (false === is_null($strImagePath))) &&
				((bool) ((int) $intOwnerClassId)) &&
				((bool) ((int) $intOwnerObjectId)) &&
				(true === file_exists($strImagePath))
			); 
			
			if($blnContinue) {
				$objImage->setVariable('ownerClassId', (int) $intOwnerClassId);
				$objImage->setVariable('ownerObjectId', (int) $intOwnerObjectId);
				$objImage->setVariable('originalFileName', $strImagePath);
				$objImage->setVariable('active', ACTIVE_STATUS_ENABLED);	
				$objImage->setVariable('imageExtension', pathinfo($strImagePath, PATHINFO_EXTENSION));
				$objImage->save();
				
				SITE_IMAGE_POSITION::validateImagePostionDirectory(0);
				//$strImagePath = constant('__TMP_ITEM_IMAGES_DIRECTORY__') . DIRECTORY_SEPARATOR . $strImagePath; // TODO: Set the default image path here.
				
				if ($objImage->getId()) {
					copy(
						$strImagePath,
						SITE_IMAGE_POSITION::getImagePositionDirecotryPath(0) . DIRECTORY_SEPARATOR .  
						$objImage->getId() . "." . pathinfo($strImagePath, PATHINFO_EXTENSION)
					);
					@chmod(
						SITE_IMAGE_POSITION::getImagePositionDirecotryPath(0) . DIRECTORY_SEPARATOR .  
						$objImage->getId() . "." . pathinfo($strImagePath, PATHINFO_EXTENSION),
						0777
					);
				}
			}
			return ($objImage);
		}
		
		
		public function onBeforeDelete() {
			if ($this->getVariable('id')) {
				// 1. Delete image positions
				$arrImagePositions = SITE_IMAGE_POSITION_AVAILABILITY::getObjectClassView(array(																				   
					'filter'  => array(
						'imageId' => (int) $this->getVariable('id')		 
					)
				));
				
				foreach($arrImagePositions as $intIndex => $arrImagePositionRow) {
					$strImagePath = $this->getImageServerPath($arrImagePositionRow['imagePositionId']);
					if (file_exists($strImagePath)) {
						unlink($strImagePath);
					}
					$objItemImagePosition = SITE_IMAGE_POSITION_AVAILABILITY::getInstance($arrImagePositionRow['id']);	
					$objItemImagePosition->delete();
				}
				
				// Delete position 0
				$strImagePathPos0 = $this->getImageServerPath(0);
				if (file_exists($strImagePathPos0)) {
					unlink($strImagePathPos0);	
				}
				
				// Delete file in tmp
				$strTmpImagePath = constant('__TMP_ITEM_IMAGES_DIRECTORY__') . DIRECTORY_SEPARATOR . $this->getVariable('originalFileName');
				if (file_exists($strImagePathPos0)) {
					unlink($strTmpImagePath);	
				}
			}
		}
		
		/**
		 * This method deletes images from an owner
		 * @param: $intImagePositionId - int - The image position (if none is defined, all positions are recompiled)
		 * @param: $intItemId - int - The item id to recompile (if none is defined, all items are recompiled)
		 * @return - $blnReturn - Boolean - The image path
		 */
		 public static function clearImagesFromOwner($intOwnerClassId = NULL, $intOwnerObjectId = NULL) 
		 {
			$blnContinue 		= (
				(((int) $intOwnerClassId) > 0) &&
				(((int) $intOwnerObjectId) > 0) /*&&
				(false === empty($arrImagePositionId))*/
			); 
			$arrItemImageView 	= array();
			$arrParams			= array();
			
			if ((bool) $blnContinue) {
				$arrOwnerImages = SITE_IMAGE::getMultiInstance(array(
					'columns' 	=> 'id',
					'filter'	=> array(
						'ownerClassId' 	=> (int) $intOwnerClassId,
						'ownerObjectId' => (int) $intOwnerObjectId
					)
				));	
				
				while (list($intIndex, $objSiteImage) = each($arrOwnerImages)) {
					$objSiteImage->delete();
				}
			}
		 }
		
		/**
		 * This method recompiles item image positions
		 * @param: $intImagePositionId - int - The image position (if none is defined, all positions are recompiled)
		 * @param: $intItemId - int - The item id to recompile (if none is defined, all items are recompiled)
		 * @return - $blnReturn - Boolean - The image path
		 */
		public static function recompileItemImagePositions($arrParam = array(
			'imagePositionId' => NULL,
			'ownerClassId' => NULL,
			'ownerObjectId' => NULL
		)) {
			$blnReturn = true;
			// 1. Get the images
			$arrFilter = array('active' => 1);
			if (
				(true === isset($arrParam['ownerClassId'])) &&
				((int) $arrParam['ownerClassId'])	
			) {
				$arrFilter['ownerClassId'] = (int) $arrParam['ownerClassId'];
			}
			
			if (
				(true === isset($arrParam['ownerObjectId'])) &&
				((int) $arrParam['ownerObjectId'])	
			) {
				$arrFilter['ownerObjectId'] = (int) $arrParam['ownerObjectId'];
			}
			
			$arrIImages = SITE_IMAGE::getObjectClassView(array(
				'columns' 	=> 'id',
				'filter'	=> $arrFilter
			));
			
			foreach($arrIImages as $intIndex => $arrRow) {
				SITE_IMAGE::createItemImagePositions (
					$arrRow['id'],
					(isset($arrParam['imagePositionId']) ? $arrParam['imagePositionId'] : NULL)
				);
			}
		}
		
		
		/**
		 * This method returns the item image view for an item
		 * @param: $arrImagePositionId - array - The image position array (if none is defined, the default 0 is used)
		 * @param: $intOwnerClassId - int - The owner class ID
		 * @param: $intOwnerObjectId - int - The owner object ID
		 * @return - $arrItemImageView - Array - The item image view
		 */
		 public static function getItemImageClassView($intOwnerClassId = NULL, $intOwnerObjectId = NULL, $arrImagePositionId = array()) 
		 {
			$blnContinue 		= (
				(((int) $intOwnerClassId) > 0) &&
				(((int) $intOwnerObjectId) > 0) /*&&
				(false === empty($arrImagePositionId))*/
			); 
			$arrItemImageView 	= array();
			$arrParams			= array();
			
			if ((bool) $blnContinue) {
				
				if (true === empty($arrImagePositionId))
				{
					$arrImagePositions = SITE_IMAGE_POSITION::getObjectClassView(array(
						'columns' 	=> 'id,width,height',
						'filter'	=> $arrFilter
					));	
					while (list($intIndex, $arrImgPositionData) = each($arrImagePositions))
					{
						$arrImagePositionId[] = $arrImgPositionData['id'];
					}
				}
				// Set class view columns
				$arrParams['columns'] =	'a.*, REPLACE(REPLACE(a.originalFileName, "' . constant('__SITE_ROOT__') . '", "/"), "//", "/") originalFileWebPath';
				
				// Set required filters
				$arrParams['filter']['a.active'] = ACTIVE_STATUS_ENABLED;
				$arrParams['filter']['a.ownerClassId'] = (int) $intOwnerClassId;
				$arrParams['filter']['a.ownerObjectId'] = (int) $intOwnerObjectId;
				
				// Create the columns
				foreach($arrImagePositionId as $intIndex => $intImagePositionId) {
					// Set Column Selection
					$intImagePositionId 	= (int) $intImagePositionId;
					$strItemImageDirectory 	= SITE_IMAGE_POSITION::getDisplayImagePositionDirecotryPath($intImagePositionId);
					$strOriginalColumns 	= $arrParams['columns'];
					
					$arrParams['columns'] 	=	' CONCAT("' . $strItemImageDirectory . '/", a.id ,".", a' . 
														'.imageExtension) as imagePosition' . $intImagePositionId . "," . $strOriginalColumns;
														
					// Set Inner Join Selection	
					$arrParams['left_join'][] = 'site_image_position_availability imp' . $intImagePositionId . ' ON imp' . $intImagePositionId . '.imageId = a' . 
												'.id AND imp' . $intImagePositionId . '.imagePositionId = ' . $intImagePositionId . ' AND imp' . $intImagePositionId . 
												'.id IS NOT NULL';	
				}
			}
			// Return the class view
			if (sizeof($arrParams)) {
				$arrItemImageView = SITE_IMAGE::getObjectClassView($arrParams);	
			}
			// Return the recordset
			return($arrItemImageView);
		 }
		 
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE); // No Cache!
		}
	}
?>