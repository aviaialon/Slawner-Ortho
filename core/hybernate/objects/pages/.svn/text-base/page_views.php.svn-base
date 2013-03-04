
<?php
	/**
	 * PAGE_VIEWS Administration Class
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
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTILITY-FUNCTIONS");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::SITE_USERS");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::GEOLOCATION::GEO_LOCATOR");
	 
	 class PAGE_VIEWS extends SHARED_OBJECT {
	 	/**
	 	 * This member variable holds the array of directories to
	 	 * NOT update as a page view (this should include remote call directories,
	 	 * or other directories like css, js directories etc...
	 	 * 
	 	 * @var 	array
	 	 * @access	public
	 	 */
	 	public static $arrRestrictedDirectories = array(1 => 	
			'/remote-call',
			'/classes',
			'/component',
			'/assets',
			'/admin',
			'/static',
			'/favicon.ico'
		);
		/**
		 * Class constructor
		 *
		 * @param	boolean	$blnLoadData - 	If false, the tracking data is not loaded.
		 *									This was added so that the object can be loaded for
		 *									component callback introspection.
		 *
		 *	@return SHARED_OBJECT::PAGE_VIEWS
		 */
		public 	   	  function __construct($blnLoadData = true) 
		{ 
			if (TRUE === $blnLoadData)
			{
				$this->setVariable('ipAddress', 	(isset($_SERVER['REMOTE_ADDR']) 	? $_SERVER['REMOTE_ADDR'] 	: NULL));
				$this->setVariable('userAgent', 	(isset($_SERVER['HTTP_USER_AGENT'])	? $_SERVER['HTTP_USER_AGENT'] : NULL));
				$this->setVariable('landingPage', 	(isset($_SERVER['PHP_SELF']) 		? $_SERVER['PHP_SELF'] 		: NULL));
				$this->setVariable('requestUri', 	(isset($_SERVER['REQUEST_URI']) 	? $_SERVER['REQUEST_URI'] 	: NULL));
				$this->setVariable('referredPage',	(isset($_SERVER['HTTP_REFERER']) 	? $_SERVER['HTTP_REFERER'] 	: NULL));
				$this->setVariable('queryString',	(isset($_SERVER['QUERY_STRING']) 	? $_SERVER['QUERY_STRING'] 	: NULL));
				$this->setVariable('timeDate', 		now());
				$this->setVariable('isCrawler', 	(int) is_bot());	
	
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$objGeoLocator	= GEO_LOCATOR::getInstance($_SERVER['REMOTE_ADDR']); 
					if ((bool) $objGeoLocator->getId()) {
						$this->setVariable('city', 		$objGeoLocator->getVariable('City'));	
						$this->setVariable('stateId', 	$objGeoLocator->getVariable('stateId'));	
						$this->setVariable('countryId',	$objGeoLocator->getVariable('countryId'));
					}
				}	
			}
		}
		
		/**
		 * This method saves the page view to the database.
		 * @access: public, static
		 * @param: 	none
		 * @return: Boolean - If the page view was saved
		 */
		public static function savePageView() {
			$strCurrentPath = dirname($_SERVER['PHP_SELF']); 
			$blnCanSave 	= true;
			if (sizeof(PAGE_VIEWS::$arrRestrictedDirectories)) {
				foreach(PAGE_VIEWS::$arrRestrictedDirectories as $intIndex => $strDirectory) {
					if (SITE_USERS::compareDirectoryPaths($strCurrentPath, $strDirectory)) {
						$blnCanSave = false;
						break;
					}
				}
			}
				
			if ($blnCanSave && ($_SERVER['PHP_SELF'] !== '/error.php')) {
				$objPageTracker = __CLASS__;
				$objPageViews = new $objPageTracker();
				$blnCanSave = (bool) $objPageViews->save();
			}
			
			return ($blnCanSave);
		}
		
		/**
		 * This method saves the page view to the database asyncronously.
		 * @access: public, static
		 * @param: 	none
		 * @return: Boolean - If the page view was saved
		 */
		public static function savePageViewAsync(array $arrRequestParams = NULL) {
			/*
				For some odd reason, setting the user agent in the callback url from the
				request dispatcher, resulted in a very weird broken array. so static data
				such as the user's IP and user agent are not overwritten, only data that's 
				been modified due to the ajax request is (requestURI, Referrer etc...)
			*/
			if (
				(TRUE  === is_array($arrRequestParams)) &&
				(TRUE  === is_array($arrRequestParams['parameters'])) &&
				(FALSE === empty($arrRequestParams['parameters']))
			) {
				$strCurrentPath = realpath($arrRequestParams['requestUri']); 
				$blnCanSave 	= true;
				if (sizeof(PAGE_VIEWS::$arrRestrictedDirectories)) {
					foreach(PAGE_VIEWS::$arrRestrictedDirectories as $intIndex => $strDirectory) {
						if (SITE_USERS::compareDirectoryPaths($strCurrentPath, $strDirectory)) {
							$blnCanSave = false;
							break;
						}
					}
				}
					
				if ($blnCanSave && ($_SERVER['PHP_SELF'] !== '/error.php')) {
					$objPageTracker = __CLASS__;
					$objPageViews = new $objPageTracker();
					// Set the new variables
					reset ($arrRequestParams['parameters']);
					while (list($strParamName, $strParamValue) = each($arrRequestParams['parameters'])) 
					{
						$objPageViews->setVariable($strParamName, $strParamValue);
					}
					$blnCanSave = (bool) $objPageViews->save();
					
					echo $objPageViews->getId();
				}	
			}
		}
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
		}
		
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>