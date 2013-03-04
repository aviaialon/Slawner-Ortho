<?php
	/**
	 * PATIENT_PROFILES Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS
	 * @subpackage	PATIENT_PROFILES
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 /*
	 	CREATE TABLE `patient_profiles` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `friendly_url` varchar(800) DEFAULT NULL,
		  `ownerUserId` int(11) NOT NULL DEFAULT '0',
		  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `active` tinyint(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`),
		  KEY `idx_lang` (`id`,`active`),
		  KEY `idx_timestamp` (`post_date`)
		) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

	 */
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORIES");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORY");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CONTENT");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_CLASS_PATH__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_COMMENTS");
	 
	 class PATIENT_PROFILES extends SHARED_OBJECT 
	 {
		/**
		 * This method returns the objects's friendly URL value.
		 */
		public function createFriendlyUrl() {
			$strTitle = $this->getVariable('title');	
			$strTitle = preg_replace('/[\s]{1,}/', ' ', ucwords($strTitle));
			$strTitle = preg_replace('/[\s]/', '-', $strTitle);
			$strTitle = preg_replace('`[^a-z0-9-]`i', '', $strTitle . '-' . $this->getId());
			return ($strTitle);
		}
		
		public static function getFriendlyUrl($intItemId, $strTitle=NULL) {
			$strTitle = preg_replace('/[\s]{1,}/', ' ', ucwords($strTitle));
			$strTitle = preg_replace('/[\s]/', '-', $strTitle);
			$strTitle = preg_replace('`[^a-z0-9-]`i', '', $strTitle);
			return ('/gigs/' . ((int) $intItemId) . '-' . $strTitle);
		}
		
		public function setActive() { 
			$this->setVariable('active', ACTIVE_STATUS_ENABLED);
			$this->save();
		}
		
		public function disable() {
			if ($this->getVariable('id')) {
				$this->setVariable('active', ACTIVE_STATUS_DISABLED);	
				$this->save();
			}
		}
		/**
		 * This method returns the class view for ITEMS with item images
		 *
		 * @access public, static
		 * @params array $arrView - The array used to build the class view <see>SHARED_OBJECT::getObjectClassView()</see>
		 * @return array
		 */
		public static function getItemObjectClassView($arrView = array(), $blnReturnViewArray = false) {
			$objDb = DATABASE::getInstance();
			$Application = APPLICATION::getInstance();
			
			// Define a default set of view arguments
			$arrDefaultView = array(
				'columns'	=>	array(
					'a.id id',
					'ncnt.name name ',
					'ncnt.content content ',
					'DATE_FORMAT(a.post_date, "%b %e, %Y") post_date',
					'u.first_name ownerName',
					'IFNULL(COUNT(DISTINCT nc.id), 0) comment_count',
					'GROUP_CONCAT(DISTINCT CONCAT(ncatrs.name, ":", ncatrs.id)) patient_profiles_categories'
				),			
				'filter'	=>	array(),
				'operator'	=>	array(),		
				'limit'		=>	0,				
				'orderBy'	=> 	'a.id',			
				'direction'	=> 	'DESC',			
				'groupBy'	=>	'a.id',			
				'escapeData'=>	true,			
				'inner_join'=>	array(),
				'imagePositionId' => array(),
				'debug'		=> false,
				'keyword'	=> NULL
			);
			
			// Merge the arguments
			$arrViewParams = array_merge(
				(array) $arrDefaultView,
				(array) $arrView
			);	
			
			// Add the item image table
			if (sizeof($arrViewParams['imagePositionId'])) {
				// Set Inner Join Selection	
				$arrViewParams['left_join'][] 	= 	'site_image im ON im.ownerClassId = ' . self::getClassKeyId() . 
													' AND im.ownerObjectId = a.id AND im.active = 1 AND im.id IS NOT NULL';	
			}
			
			// Set manditory joins
			$arrViewParams['inner_join'][] 	= 'site_users u ON u.id = a.ownerUserId';
			$arrViewParams['inner_join'][] 	= 'patient_profiles_content ncnt ON ncnt.patient_profiles_id = a.id AND ncnt.lang = "' . $Application->translate('en', 'fr') . '"';
			$arrViewParams['left_join'][] 	= 'patient_profiles_comments nc ON nc.patient_profiles_id = a.id AND nc.active = 1 AND nc.patient_profiles_id IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'patient_profiles_category ncat ON ncat.patient_profiles_id = a.id AND ncat.patient_profiles_id IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'patient_profiles_categories ncatrs ON ncatrs.id = ncat.patient_profiles_category_id AND ncatrs.lang = "' . $Application->translate('en', 'fr') . 
											  '" AND ncat.patient_profiles_Id IS NOT NULL';

			// Set manditory filters	
			$arrViewParams['filter_unescaped']['IF(ncatrs.id IS NOT NULL, ncatrs.lang="' . $Application->translate('en', 'fr') . '", 1=1)'] = NULL;
			//$arrViewParams['filter_unescaped']['IF(ntgs.id IS NOT NULL, ntgs.lang="' . $Application->translate('en', 'fr') . '", 1=1)'] 	= NULL;
			$arrViewParams['filter']['ncnt.lang'] 	= $Application->translate('en', 'fr');
			$arrViewParams['filter']['a.active'] 	= 1;
			
			// Set filter operator
			$arrViewParams['operator'][]  = "=";
			$arrViewParams['operator'][]  = "=";
			$arrViewParams['operator'][]  = "";
			$arrViewParams['operator'][]  = "";
			
			// Keyword search
			if (
				(! is_null($arrViewParams['keyword']))	 &&
				(strlen($arrViewParams['keyword']))	
			) {
				//$arrViewParams['escapeData'] 			= false;
				$arrViewParams['operator'][]  			= " IN ";
				$arrViewParams['operator'][]  			= " IN ";
				$arrViewParams['filter_unescaped']['a.id'] 	= " (( 	SELECT 		IFNULL(_nc.patient_profiles_id, 0) id " .
												"				FROM		patient_profiles_content _nc " .
												"				WHERE		(_nc.name REGEXP '" . $objDb->escape(trim($arrViewParams['keyword'])) . "' " .
												"				OR			_nc.content REGEXP '" . $objDb->escape(trim($arrViewParams['keyword'])) . "') " .
												"				AND 		_nc.lang = '" . $Application->translate('en', 'fr') . "' " .
												" ))";										
			}
			
			$strItemImageDirectory = str_replace(__SITE_ROOT__, "", constant('__ITEM_IMAGES_DIRECTORY__'));
			foreach($arrViewParams['imagePositionId'] as $intIndex => $intImagePositionId) {
				// Set Column Selection
				$intImagePositionId = (int) $intImagePositionId;
				$strOriginalColumns = $arrViewParams['columns'];
				$strNewColumns 	 	= 	' GROUP_CONCAT(DISTINCT CONCAT("' . $strItemImageDirectory . '/", imp' . $intImagePositionId . 
										'.imagePositionId, "/", imp' . $intImagePositionId . '.imageId ,".", im' . 
										'.imageExtension)) as imagePosition' . $intImagePositionId;
										
				if (true === is_array($strOriginalColumns)) {
					$arrViewParams['columns'][] = $strNewColumns;
				} else {
					$arrViewParams['columns'] .= ',' . $strNewColumns;
				}
				
													
				// Set Inner Join Selection	
				$arrViewParams['left_join'][] 	= 	'site_image_position_availability imp' . $intImagePositionId . ' ON imp' . $intImagePositionId . '.imageId = im' . 
													'.id AND imp' . $intImagePositionId . '.imagePositionId = ' . $intImagePositionId . 
													' AND imp' . $intImagePositionId . '.id IS NOT NULL';	
			}
			
			// Set the group by
			$arrViewParams['groupBy'] = (strlen($arrViewParams['groupBy']) ? $arrViewParams['groupBy'] : 'a.id');
			// Return the class view
			return((true === $blnReturnViewArray ? $arrViewParams : self::getObjectClassView($arrViewParams)));
		}
		
		/**
			Callback Metods
		**/
		protected function onSave() { 
			$arrArguments = func_get_args();
		}
		
		protected function onAfterSave() {
			$this->setVariable('friendly_url', $this->createFriendlyUrl());
			$objDb = DATABASE::getInstance();
			$objDb->insertUpdateFromArray(strtolower(__CLASS__), array(
				'id' => $this->getId(),
				'friendly_url' => $this->getVariable('friendly_url')
			));
		}
		
		protected function on_getInstance() {
			$arrArguments = func_get_args();
			if ($this->getId() <= 0) { 
				$this->setVariable('active', ACTIVE_STATUS_PENDING);	
			}
			//$this->mapLinkedObject(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS", "active_status");
			// Load other propertied like categories etc....
		}
		
		/**
		 * This method is executed before deleteing a news post
		 * 
		 * @author	Avi Aialon <aviaialon@gmail.com>
		 * @access	public final
		 * @param	none
		 * @return	void
		 */
		public final function onBeforeDelete() 
		{
			if (((int) $this->getVariable('id')) > 0) 
			{
				// 1. Delete item images
				SITE_IMAGE::clearImagesFromOwner(
					(int) $this->getClassNumericId(), 
					(int) $this->getId()
				);
				
				// 2. Clear Categories..
				$arrProfileCategories = PATIENT_PROFILES_CATEGORY::getMultiInstance(array(
					'columns'		=> array('a.*'),
					'filter' 		=> array('patient_profiles_id'	=> (int) $this->getId())
				));
				reset ($arrProfileCategories);
				while (list($intIndex, $objProfileCategory) = each($arrProfileCategories)) {
					$objProfileCategory->delete();
				}
				
				// 3. Clear the content...
				$arrProfileContents = PATIENT_PROFILES_CONTENT::getMultiInstance(array(
					'columns'		=> array('a.*'),
					'filter' 		=> array('patient_profiles_id'	=> (int) $this->getId())
				));
				reset ($arrProfileContents);
				while (list($intIndex, $objProfileContent) = each($arrProfileContents)) {
					$objProfileContent->delete();
				}
				
				// 4. Clear the coments...
				$arrProfileComments = PATIENT_PROFILES_COMMENTS::getMultiInstance(array(
					'columns'		=> array('a.*'),
					'filter' 		=> array('newsId'	=> (int) $this->getId())
				));
				reset ($arrProfileComments);
				while (list($intIndex, $objProfileComment) = each($arrProfileComments)) {
					$objProfileComment->delete();
				}
			}
		}
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 		 		{ return (__FILE__); }
		public final function getClassKeyId()  				{ return (3); }
	}
?>