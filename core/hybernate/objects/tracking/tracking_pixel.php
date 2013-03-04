<?php
	/**
	 * TRACKING_PIXEL Administration Class
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
	 class TRACKING_PIXEL extends SHARED_OBJECT {
	 
		public final static function track() {
			// Track data in the Database
			if (isset($_GET['trackPixel']))
			{
				$objTrackingPixel = TRACKING_PIXEL::newInstance();
				$objTrackingPixel->setTimeDate(now());
				$objTrackingPixel->setVariable('referrer', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL));
				$objTrackingPixel->setVariable('user_agent', (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL));
				$objTrackingPixel->setVariable(
					'data', 
					'POST => ' . 	 print_r($_POST, true) 	. '<br />' . 
					'GET => ' .  	 print_r($_GET, true) 	. '<br />' .  
					'COOKIE => ' .   print_r($_COOKIE, true) . '<br />' . 
					'ENV => ' .  	 print_r($_ENV, true) 	. '<br />' .  
					'REQUEST => ' .  print_r($_REQUEST, true) . '<br />' 
				);
				$objTrackingPixel->save();
				
				// Create the tracking pixel
				$objTrackingPixelImage = imagecreatetruecolor(1, 1);
				imagealphablending($objTrackingPixelImage, false);
				
				$col = imagecolorallocatealpha($objTrackingPixelImage,255,255,255,127);
				imagefilledrectangle($objTrackingPixelImage, 0, 0, 1, 1, $col);
				imagealphablending($objTrackingPixelImage, true);
				
				header("Content-Type:image/png");
				imagepng($objTrackingPixelImage);
				imagedestroy($objTrackingPixelImage);
				exit;
			}
		}
		
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() 
		{
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
		}
		
		protected function getClassPath()  	 { return (__FILE__); }
		
	}
?>