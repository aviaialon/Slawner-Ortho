<?php
	/**
	 * AUCTION Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::AUCTION::AUCTION
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
	SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::AUCTION::AUCTION_STATUS");
	
	class AUCTION extends SHARED_OBJECT {
		/**
		 *	@access: PUBLIC	
		 *  This function will return the total bids for a current auction
		 */
		public function getTotalBids() {
			$intTotalBids = 0;
			if ($this->getInstanceType() <> SHARED_OBJECT::SHARED_OBJECT_NEW_INSTANCE) {
				$objDb = DATABASE::getInstance();
				$arrBidCount = $objDb->query("
					SELECT 	COUNT(ab.id) as bidCount
					FROM	auction_bids ab
					WHERE	ab.auctionId = " . $this->getId()
				);
				$intTotalBids = (count($arrBidCount) ? $arrBidCount[0]['bidCount'] : 0);
			}
			return ($intTotalBids);
		}
		
		/**
		 *	@access: STATIC	
		 *  This function will return open auctions
		 */
		 public static function getAuctionListings($arrayOptions = array()) {
		 	$objDb = DATABASE::getInstance();
			$arrConfig = array_merge(array(
				'auctionId'	=> NULL,																		 
				'orderBy' 	=> NULL,
				'limit'		=> NULL,
				'status'	=> AUCTION_STATUS_ENABLED
			 ), $arrayOptions);
		 	$arrAuctions = array();
		 	
			// Close the auctions, just in case.
			// TODO: Remove this call, its not performant to have this call on the front end
		 	AUCTION::closeAuctions();
		 	
		 	// Get the auctions
		 	$arrAuctions = $objDb->query("
		 		SELECT	a.id 					 as auctionId,
		 				a.itemId				 as itemId,
		 				a.sellerUserId			 as sellerUserId,
		 				a.openTimeDate			 as openTimeDate,
		 				a.closeTimeDate			 as closeTimeDate,
		 				a.currentAmount			 as currentAmount,
		 				a.basePrice				 as basePrice,
						a.auctionDurationSeconds as auctionDurationSeconds,
						a.auctionStatusId		 as auctionStatusId,
						i.title					 as title,
						i.description			 as description,
						z.price_point			 as sale_price,
						IFNULL(MAX(ab.id), 0)	 as lastBidId,
						IFNULL(COUNT(ab.id), 0)	 as totalBids,
						su.`username`			 as lastBidderUserName,
						su_seller.`username`	 as sellerUserName,
						UNIX_TIMESTAMP(a.closeTimeDate) as unix_closeTime
						
				FROM	auction a 			
				
				INNER JOIN	items i
				ON			i.id = a.itemId
				AND			i.active_status =" . (int) $arrConfig['status'] . " 
				AND			i.user_enabled = " . (int) $arrConfig['status'] . " 
				
				INNER JOIN	zones z
				ON			z.id = i.zoneId
				AND			z.active_status = " . ACTIVE_STATUS_ENABLED . " 
				
				INNER JOIN	site_users su_seller
				ON			su_seller.id = a.sellerUserId
				AND			su_seller.active = " . ACTIVE_STATUS_ENABLED . " 
				
				LEFT JOIN	auction_bids ab
				ON			ab.auctionId = a.id
				AND			ab.id IS NOT NULL
				
				LEFT JOIN	site_users su
				ON			su.id = ab.bidderUserId
				AND			su.active = " . ACTIVE_STATUS_ENABLED . " 
				
		 		WHERE	1=1 ".
					(! is_null($arrConfig['status']) ? " AND a.auctionStatusId = " . (int) $arrConfig['status'] . " " : "") . 
					(! is_null($arrConfig['auctionId']) ? " AND a.id = " . $arrConfig['auctionId'] . " " : "") . "
				GROUP BY a.id 
		 		ORDER BY " . (! is_null($arrConfig['orderBy']) ? $arrConfig['orderBY'] : "RAND() ") .
		 					 (! is_null($arrConfig['limit']) ? "LIMIT " . $arrConfig['limit'] . " " : "") 
		 	);
		 	
		 	return ($arrAuctions);
		 }
		 
		/**
		 *	@access: CRON - STATIC	
		 *  This function will update the auctions, close them and reset them to a new auction.
		 */
		public static function closeAuctions() {
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::DATABASE::DATABASE");
			//	Cron Static Call
			//	
			//
			$objDb = DATABASE::getInstance();
			//
			// 1 .	CLOSE THE AUCTIONS ...
			//
			$objDb->query(
				" UPDATE 	auction a " .
				" SET		a.auctionStatusId = " . AUCTION_STATUS_PROCESSING .
				" WHERE		a.closeTimeDate <= NOW() "	.
				" AND		a.auctionStatusId = " . AUCTION_STATUS_ENABLED
			);
			//
			// 2. RE-CREATE THE NEW AUCTIONS
			//
			$objDb->query(
				" INSERT INTO 	auction (
					itemId,
					auctionStatusId,
					sellerUserId,
					timeDate,
					openTimeDate,
					closeTimeDate,
					currentAmount,
					basePrice,
					auctionDurationSeconds,
					isRecurring,
					zoneId
				) 
				SELECT		auction.itemId 				as itemId,	
				" . AUCTION_STATUS_ENABLED . "			as auctionStatusId,
							auction.sellerUserId		as sellerUserId,
							NOW()						as timeDate,
							NOW()						as openTimeDate,
							DATE_ADD(NOW(), INTERVAL auction.auctionDurationSeconds SECOND) as closeTimeDate,
							zones.auction_base_price	as currentAmount,
							zones.auction_base_price	as basePrice,
							auction.auctionDurationSeconds as auctionDurationSeconds,
							auction.isRecurring			as isRecurring,
							zones.id					as zoneId
				FROM		auction
				
				INNER JOIN 	items
				ON			items.id = auction.itemId
				AND			items.active_status = " . ACTIVE_STATUS_ENABLED . " 
				AND			items.user_enabled = " . ACTIVE_STATUS_ENABLED . " 
				
				INNER JOIN	zones
				ON			zones.id = items.zoneId
				AND			zones.active_status = " . ACTIVE_STATUS_ENABLED . "
				
				WHERE		auction.auctionStatusId = " . AUCTION_STATUS_PROCESSING . " 
				AND			isRecurring = 1;"
			);
			//
			// 3. PROCESS THE WINNING AUCTIONS ...
			//
			$objDb->query("
				INSERT INTO 	auction_complete (
					buyerUserId,
					auctionId,
					closePrice,
					itemId,
					auctionBidId,
					totalBids,
					closeTimeDate,
					timeDate,
					zoneId
				)
				SELECT 			ab.bidderUserId			as buyerUserId,
								a.id					as auctionId,
								a.currentAmount			as closePrice,
								a.itemId				as itemId,
								tmp.id					as auctionBidId,
								tmp.bidCount			as totalBids,
								a.closeTimeDate			as closeTimeDate,
								NOW()					as timeDate,
								a.zoneId				as zoneId
								
				FROM 			auction a
				
				INNER JOIN 		(
					SELECT 		MAX(auction_bids.id)				as id,
								IFNULL(COUNT(auction_bids.id), 0)	as bidCount,
								auction_bids.auctionId				as auctionId
					FROM		auction_bids
					INNER JOIN	auction
					ON			auction.id = auction_bids.auctionId
					AND			auction.auctionStatusId	= " . AUCTION_STATUS_PROCESSING . " 
					GROUP BY	auction_bids.auctionId
					HAVING		bidCount > 0
				) tmp
				ON				tmp.auctionId = a.id
				AND				a.auctionStatusId	= " . AUCTION_STATUS_PROCESSING . " 
				
				INNER JOIN		auction_bids ab
				ON				ab.auctionId = tmp.auctionId
				AND				ab.id = tmp.id
				
				WHERE			ab.id IS NOT NULL
				AND				a.auctionStatusId	= " . AUCTION_STATUS_PROCESSING . " 
				GROUP BY		a.id;			  
			");
			//
			// 5. ADD ALL THE CLOSED AUCTIONS TO THE auction_closed TABLE ...
			//
			$objDb->query("
				INSERT INTO auction_closed (
					`auction_closed`.`itemId`,
					`auction_closed`.`auctionId`,
					`auction_closed`.`auctionStatusId`,
					`auction_closed`.`sellerUserId`,
					`auction_closed`.`timeDate`,
					`auction_closed`.`openTimeDate`,
					`auction_closed`.`closeTimeDate`,
					`auction_closed`.`currentAmount`,
					`auction_closed`.`basePrice`,
					`auction_closed`.`auctionDurationSeconds`,
					`auction_closed`.`timeIncreaseFromBid`,
					`auction_closed`.`isRecurring`,
					`auction_closed`.`hasBids`,
					`auction_closed`.`zoneId`
				)	
				SELECT 	`a`.`itemId`,
						`a`.`id` as `auctionId`,	
						" . AUCTION_STATUS_CLOSED . " as `auctionStatusId`,
						`a`.`sellerUserId`,
						`a`.`timeDate`,
						`a`.`openTimeDate`,
						`a`.`closeTimeDate`,
						`a`.`currentAmount`,
						`a`.`basePrice`,
						`a`.`auctionDurationSeconds`,
						`a`.`timeIncreaseFromBid`,
						`a`.`isRecurring`,
						IFNULL(`ac`.`totalBids`, 0) `totalBids`,
						`a`.`zoneId` as `zoneId`
				FROM	auction	a
				
				LEFT JOIN	auction_complete ac
				ON			ac.auctionId = a.id
				AND			ac.id IS NOT NULL
				
				WHERE		a.auctionStatusId IN (" . AUCTION_STATUS_PROCESSING . "," . AUCTION_STATUS_CLOSED . ")
			");
			//
			// 6. DELETE CLOSED AUCTIONS ...
			//
			$objDb->query("
				DELETE FROM	auction 
				WHERE	auction.auctionStatusId IN (" . AUCTION_STATUS_PROCESSING . "," . AUCTION_STATUS_CLOSED . ")
			");
			/*
			$objDb->query("
				UPDATE 	auction 
				SET 	auction.auctionStatusId = " . AUCTION_STATUS_CLOSED . "
				WHERE	auction.auctionStatusId = " . AUCTION_STATUS_PROCESSING . "
			");
			*/
			return (true);
		}
		
		public static function getActiveInstanceFromItemId($intItemId=NULL) {
			$blnContinue = (is_null($intItemId) ? false : ((bool) intval($intItemId) ? true : false));
			$objInstance = $blnContinue;
			if ($blnContinue) {
				$objDb = DATABASE::getInstance();
				$arrReturn = $objDb->query("
					SELECT 			a.*
					FROM 			auction a 
					
					INNER JOIN		site_users su_seller
					ON				su_seller.id = a.sellerUserId
					AND				su_seller.active = " . ACTIVE_STATUS_ENABLED . " 		
					
					INNER JOIN		items i
					ON				i.id = a.itemId
					AND				i.active_status = " . ACTIVE_STATUS_ENABLED . " 	
					AND				i.user_enabled =  " . ACTIVE_STATUS_ENABLED . " 	
					
					WHERE			a.auctionStatusId = " . AUCTION_STATUS_ENABLED . " 
					AND				a.itemId = " . (int) $intItemId . " 
					ORDER BY		a.id DESC
					LIMIT			1
				");
				if (count($arrReturn)) {
					$objInstance = new AUCTION();
					$objInstance->loadFromArray($arrReturn[0]);
				}
			}
			return ($objInstance);
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