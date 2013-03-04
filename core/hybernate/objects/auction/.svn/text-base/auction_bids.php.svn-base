<?php
	/**
	 * AUCTION_BIDS Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::::AUCTION::AUCTION_BIDS
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
	SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::AUCTION::AUCTION");
	SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::AUCTION::AUCTION_STATUS");
	SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::IMAGE_POSITION");
	
	class AUCTION_BIDS extends SHARED_OBJECT {
		/**
		 * CLASS STATIC CONSTANTS
		 **/
		const AUCTION_BIDS_DEFAULT_BID_INCREMENT = 0.10;
		const AUCTION_BIDS_USER_NOT_LOGGED_IN 	 = "Please login to bid on this gig!";
		const AUCTION_BIDS_NO_VALID_AUCTION 	 = "Sorry, no auction was not found for this item.";
		const AUCTION_BIDS_BID_SUCCESS 	 		 = "Thank you, your bid was accepted.";
		const AUCTION_BIDS_DEFAULT_ERROR 	 	 = "Sorry, an error occured. Please try again.";
		
		public static function bid($intItemId=NULL, $objUser=NULL) {
			$blnContinue = ((int) $intItemId > 0 ? true : false);
			$arrReturn 	 = array(
				'blnResult' 	=> false,
				'strMessage'	=> ''
			);
			// Validate arguments ..
			if (! ($blnContinue)) {
				$arrReturn['strMessage'] = "Please select a valid item.";
				$blnContinue = false;
			}
			
			// Validate the User...
			if (
				($blnContinue) &&
				(
				  (! isset($objUser)) 		||
				  (! is_object($objUser))	||
				  (! method_exists($objUser, 'isLoggedIn'))	||
				  (! $objUser->isLoggedIn())
				)
			) {
				$arrReturn['strMessage'] = AUCTION_BIDS::AUCTION_BIDS_USER_NOT_LOGGED_IN;
				$blnContinue = false;
			}
			
			// get the auction
			if ($blnContinue) {
				$objAuction  = AUCTION::getActiveInstanceFromItemId($intItemId);
				$blnContinue = ((bool) is_object($objAuction));
				if (! $blnContinue) {
					$arrReturn['strMessage'] = AUCTION_BIDS::AUCTION_BIDS_NO_VALID_AUCTION;	
				}
			}
			
			// add the bid....
			if ($blnContinue) {
				$objBid  = new AUCTION_BIDS();				
				$objBid->setVariable('auctionId', $objAuction->getId());
				$objBid->setVariable('bidderUserId', $objUser->getUserId());
				$objBid->setVariable('bidAmount', AUCTION_BIDS::AUCTION_BIDS_DEFAULT_BID_INCREMENT);
				$objBid->setVariable('userIP', $_SERVER['REMOTE_ADDR']);
				
				// Nail bitter
				/*
				$intTimeLeft = strtotime($objAuction->getVariable('closeTimeDate'));
				$strNewTime = false;
				if (time() - $intTimeLeft) <= 15) {
					$strNewTime = date('Y-m-d H:i:s', strtotime($objAuction->getVariable('closeTimeDate')) + 15);
					$objAuction->setVariable('closeTimeDate', $strNewTime);
				}
				*/				
				// Update the auction ...
				$objAuction->setVariable('currentAmount', $objAuction->getVariable('currentAmount') + AUCTION_BIDS::AUCTION_BIDS_DEFAULT_BID_INCREMENT);
				$objAuction->setVariable('lastBidderUserId', (int) $objUser->getVariable('id'));
				
				if (
					$objBid->save() &&
					$objAuction->save()
				) {
					$arrReturn['strMessage'] 	= AUCTION_BIDS::AUCTION_BIDS_BID_SUCCESS;
					$arrReturn['blnResult']  	= true;
					$arrReturn['newPrice']		= $objAuction->getVariable('currentAmount');
					$arrReturn['totalBids']		= $objAuction->getTotalBids();
					$arrReturn['lastBidder']	= $objUser->getInfo('username');
					$arrReturn['itemId']		= $objAuction->getVariable('itemId');
					$arrReturn['auctionId']		= $objAuction->getId();
					//$arrRetuen['nailBitter']	= $strNewTime;
				} else {
					$arrReturn['strMessage'] = AUCTION_BIDS::AUCTION_BIDS_DEFAULT_ERROR;
				}
			}
			return ($arrReturn);
		}
		
		/**
		 * This method returns the last watched bid id for an auction group.
		 * @access: STATIC
		 * @scope: 	PUBLIC 
		 * @param: 	array $arrAuctions 	- An array of auction listings
		 * @return:	int $intReturn 		- The last watched bid id.
		 */
		public static function getLastWatchedBidIdFromAuctionListing($arrItems=array()) {
			$blnContinue 	= ((bool) sizeof($arrItems));
			$arrItemIds		= array();
			$intReturn		= 0;
			if ($blnContinue) {
				foreach($arrItems as $intIndex => $arrRow) {
					if (isset($arrRow['auctionId'])) $arrItemIds[] = $arrRow['auctionId'];
				}
				if (sizeof($arrItemIds)) {
					$objDb 		= DATABASE::getInstance();
					$arrResult  = $objDb->query("
						SELECT 	MAX(ab.id) AS maxBidId
						FROM	auction_bids ab
						WHERE	ab.auctionId IN (" . implode(',', $arrItemIds) . ")	
					");
					$intReturn = (isset($arrResult[0]['maxBidId']) ? $arrResult[0]['maxBidId'] : 0);
				}
			}
			return ($intReturn);
		}
		
		/**
		 * This method returns the last changed watched bid id for an auction group.
		 * @access: STATIC
		 * @scope: 	PUBLIC 
		 * @param: 	array $arrItemIds 	- An array of itemId's to watch
		 * @return:	int $intReturn 		- The last watched bid id.
		 */
		public static function getWatchedItemsChangeList($intLastBidId=NULL, $lstAuctionId=NULL, $intUserId=NULL) {
			$arrReturn = array(
				'newHighBidId' => 0,
				'arrChangedAuctions' => NULL
			);
			$blnContinue  = false;
			$blnContinue |= (! is_numeric($intLastBidId) ? false : true);
			$blnContinue |= (preg_match('/[^0-9,]/', $lstAuctionId) ? false : true);
			if ($blnContinue) {
				$objDb = DATABASE::getInstance();	
				$arrReturn['arrChangedAuctions'] = $objDb->query("
					SELECT	abView.auctionId 			as auctionId,
							abView.currentAmount		as newPrice,		
							abView.itemId				as itemId,
							su.username 				as lastBidder,
							abView.bidCount 			as bidCount,
							abView.hightBidId			as lastBidId,
							abView.itemTitle			as itemTitle,
							IF(
							 	abView.imagePos1Id IS NOT NULL,
								CONCAT(abView.imagePos1BasePath, abView.imagePos1Id, '.', abView.imagePos1Extension),
								'DEFAULT_POS_1'
							)							as itemImagePosition1
							
					FROM	 (
						SELECT 	IFNULL(COUNT(ab.id),0)  as bidCount,
								MAX(ab.id)			  	as hightBidId,
								a.id					as auctionId,
								a.itemId 				as itemId,
								a.currentAmount 		as currentAmount,
								'" . IMAGE_POSITION::getDisplayImagePositionDirecotryPath(1) . "/' as imagePos1BasePath,
								ii.id 					as imagePos1Id,
								ii.imageExtension 		as imagePos1Extension,
								i.title					as itemTitle
						FROM	auction_bids ab
						
						INNER JOIN 	auction a
						ON 			a.id = ab.auctionId
						AND			a.auctionStatusId = " . AUCTION_STATUS_ENABLED . " 
						
						INNER JOIN	items i
						ON			i.id = a.itemId
						AND			i.active_status = " . AUCTION_STATUS_ENABLED . " 
						AND			i.user_enabled = " . AUCTION_STATUS_ENABLED . " 
												
						LEFT JOIN	item_image ii
						ON			ii.itemId = a.itemId
						AND			ii.active = 1
						
						WHERE		a.id IN (" . $lstAuctionId . ") 
						AND			ab.id > " . (int) $intLastBidId . " 
						AND			ab.bidderUserId != " . (int) $intUserId . " 
						GROUP BY 	ab.auctionId
						ORDER BY 	ab.id DESC
					) abView
					
					INNER JOIN	auction_bids ab
					ON			ab.id = abView.hightBidId
					AND			ab.auctionId = abView.auctionId
					
					INNER JOIN 	site_users su
					ON			su.id = ab.bidderUserId
					AND			su.active = " . ACTIVE_STATUS_ENABLED . ";
				");
				
				/*
				$objDb->query("
					SELECT	a.id 				as auctionId,
							a.currentAmount 	as newPrice,
							a.itemId 			as itemId,
							su.username 		as lastBidder,
							tmpView2.bidCount 	as bidCount,
							MAX(tmpView2.id)	as lastBidId
							
					FROM	auction_bids ab
					
					INNER JOIN 	auction a
					ON			a.id = ab.auctionId
					AND			a.auctionStatusId = " . AUCTION_STATUS_ENABLED . " 
					
					INNER JOIN	(
						SELECT 	auction_bids.id id,
								COUNT(auction_bids.id) bidCount
						FROM	auction_bids 
						WHERE	auction_bids.auctionId IN (" . $lstAuctionId . ") 
						GROUP BY auction_bids.auctionId
					) tmpView2 
					
					INNER JOIN	auction_bids ab2
					ON			ab2.id = tmpView2.id
					
					
					INNER JOIN 	site_users su
					ON			su.id = ab.bidderUserId
					AND			su.active = " . ACTIVE_STATUS_ENABLED . " 
					
					
					WHERE		ab.id > " . (int) $intLastBidId . " 
					AND			ab.bidderUserId != " . (int) $intUserId . " 
					AND			a.id IN (" . $lstAuctionId . ") 
					GROUP BY 	ab.auctionId
					ORDER BY 	ab.id DESC
				");
				*/
				if (sizeof($arrReturn['arrChangedAuctions'])) {
					$arrHighBid = $objDb->query("
						SELECT 	MAX(ab.id) highBidId
						FROM	auction_bids ab
						WHERE	ab.auctionId IN (" . $lstAuctionId . ") 
						LIMIT 	1
					");
					$arrReturn['newHighBidId'] = (isset($arrHighBid[0]['highBidId']) ? $arrHighBid[0]['highBidId'] : 0);
				}
				
				$arrReturn['hasChanges'] = (bool) sizeof($arrReturn['arrChangedAuctions']);
			}
			return ($arrReturn);
		}
		/**
			Abstraction Methods
		**/
		/*
		public static function getInstance($intId = 0) {
			$__strObjClassName__ = __CLASS__;
			$objReturn = new $__strObjClassName__((int) $intId);
			return ($objReturn->_getInstance((int)  $intId));
		}
		*/
		protected function getClassPath()  	 { return (__FILE__); }
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE); // No Cache!
		}
	}
?>