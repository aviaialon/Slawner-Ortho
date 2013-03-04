<?php
	/**
	 * ITEMS Administration Class
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
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ITEMS::ITEM_IMAGE");
	 
	 class ITEMS extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
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
			$this->setVariable('active_status', ACTIVE_STATUS_ENABLED);
			$this->save();
		}
		
		public function disable() {
			if ($this->getVariable('id')) {
				$this->setVariable('active_status', ACTIVE_STATUS_DISABLED);	
				$this->setVariable('user_enabled', ACTIVE_STATUS_DISABLED);	
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
		public static function getItemObjectClassView($arrView = array()) {
			$objDb = DATABASE::getInstance();
			// Define a default set of view arguments
			$arrDefaultView = array(
				'columns'	=>	'a.*, u.username, it.name itemType, GROUP_CONCAT(DISTINCT itg.tag) tags,' .
								'act.id auctionId, act.openTimeDate auctionStartDate, act.closeTimeDate auctionCloseDate, act.currentAmount auctionCurrentAmount, ' . 
								'COUNT(DISTINCT ab.id) bidCount, si.username auctionLeader, IF(act.id IS NOT NULL, 1, 0) isAuctionItem',			
				'filter'	=>	array(),
				'operator'	=>	array(),		
				'limit'		=>	0,				
				'orderBy'	=> 	'id',			
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
				$arrViewParams['left_join'][] 	= 	'item_image im ON im.itemId = a.id AND im.active = 1 AND im.id IS NOT NULL';	
				// Set Filter Selection			
				//$arrViewParams['filter']['im.active'] = 1;
			}
			
			// Set manditory joins
			$arrViewParams['inner_join'][] 	= 'site_users u ON u.id = a.userId';
			$arrViewParams['left_join'][] 	= 'item_type it ON it.id = a.itemTypeId AND it.id IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'item_tag itg ON itg.itemId = a.id AND itg.itemId IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'auction act ON act.itemId = a.id AND act.sellerUserId = a.userId ' .
											  'AND act.auctionStatusId = 1 AND act.itemId IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'auction_bids ab ON ab.auctionId = act.id AND ' .
											  'ab.id IS NOT NULL';
			$arrViewParams['left_join'][] 	= 'site_users si ON si.id = act.lastBidderUserId AND si.id IS NOT NULL';

			// Set manditory filters	
			$arrViewParams['filter']['a.active_status'] = 1;
			$arrViewParams['filter']['a.user_enabled'] 	= 1;
			$arrViewParams['filter']['u.active'] 		= 1;
			
			// Set filter operator
			$arrViewParams['operator'][]  = "=";
			$arrViewParams['operator'][]  = "=";
			$arrViewParams['operator'][]  = "=";
			$arrViewParams['operator'][]  = "=";
			
			// Keyword search
			if (
				(! is_null($arrViewParams['keyword']))	 &&
				(strlen($arrViewParams['keyword']))	
			) {
				$arrViewParams['escapeData'] = false;
				$arrViewParams['operator'][]  = "";
				$arrViewParams['operator'][]  = "";
				$arrViewParams['operator'][]  = "IN";
				$arrViewParams['filter']['(a.description_clean'] = "REGEXP '" . $objDb->escape(trim($arrViewParams['keyword'])) . 
																  "' OR a.title REGEXP '" . $objDb->escape(trim($arrViewParams['keyword'])) . "') OR" .
																  " a.id IN ( 	SELECT 		IFNULL(itt.itemId, 0) itemId " .
																  "				FROM		item_tag itt " .
																  "				WHERE		itt.tag REGEXP '" . $objDb->escape(trim($arrViewParams['keyword'])) . "' )";
			}
			
			$strItemImageDirectory = str_replace(__SITE_ROOT__, "", constant('__ITEM_IMAGES_DIRECTORY__'));
			foreach($arrViewParams['imagePositionId'] as $intIndex => $intImagePositionId) {
				// Set Column Selection
				$intImagePositionId = (int) $intImagePositionId;
				$strOriginalColumns = $arrViewParams['columns'];
				$arrViewParams['columns'] 	 	= 	' CONCAT("' . $strItemImageDirectory . '/", imp' . $intImagePositionId . 
													'.imagePositionId, "/", imp' . $intImagePositionId . '.itemImageId ,".", im' . 
													'.imageExtension) as imagePosition' . $intImagePositionId . "," . $strOriginalColumns;
													
				// Set Inner Join Selection	
				$arrViewParams['left_join'][] 	= 	'item_image_position imp' . $intImagePositionId . ' ON imp' . $intImagePositionId . '.itemImageId = im' . 
													'.id AND imp' . $intImagePositionId . '.imagePositionId = ' . $intImagePositionId . 
													' AND imp' . $intImagePositionId . 
													'.id IS NOT NULL';	
			}
			
			// Set the group by
			$arrViewParams['groupBy'] = (strlen($arrViewParams['groupBy']) ? $arrViewParams['groupBy'] : 'a.id');

			// Return the class view
			return(ITEMS::getObjectClassView($arrViewParams));
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
				$this->setVariable('active_status', ACTIVE_STATUS_PENDING);	
			}
			//$this->mapLinkedObject(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS", "active_status");
			// Load other propertied like categories etc....
		}
		
		public function onBeforeDelete() {
			if ($this->getVariable('id')) {
				// 1. Delete item images
				$arrImages = ITEM_IMAGE::getObjectClassView(array(																				   
					'filter'  => array(
						'itemId' => (int) $this->getVariable('id')		 
					)
				));
				
				foreach($arrImages as $intIndex => $arrImageRow) {
					$objItemImage = ITEM_IMAGE::getInstance($arrImageRow['id']);	
					$objItemImage->delete();
				}
			}
		}
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>