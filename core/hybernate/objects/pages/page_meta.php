<?php
	/**
	 * PAGE_META Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::PAGES
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PARSER::PARSER");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTILITY-FUNCTIONS");
	 
	 class PAGE_META extends SHARED_OBJECT {
	 	public static $arrParsableTags = array();
		public static $identifiedPageName = false;
		/**
		 * Class constructor
		 */
		public function __construct() { }
		
		/**
		 * This method gets the meta tags for a page - NON STATIC ACCESS
		 * @access: public, static
		 * @param: 	none
		 * @return: $arrMeta - Array - The page meta tags
		 */
		 public static function addParsableTag($strTag=NULL, $strValue=NULL) {
		 	if (
				(! is_null($strTag)) &&
				(! is_null($strValue)) 
			) {
				PAGE_META::$arrParsableTags[$strTag] = $strValue;
			}
		 }
		 
		 /**
		 * This method forces a page to be identified by a certain id (will override all internal
		 * page identification setting) - ex: PAGE_META::identifyPageName('/users/login');																	   
		 * @access: public, static
		 * @param: 	none
		 * @return: $strPageName - String - The page name
		 */
		 public static function identifyPageName($strPageName = false) {
				PAGE_META::$identifiedPageName = $strPageName;
		 }
		 
		/**
		 * This method gets the meta tags for a page
		 * @access: public, static
		 * @param: Boolean 	$blnEchoContent - To echo or return the content
		 * @param: Boolean 	$blnReturnDataArray - To return the data array rather than the meta
		 * @return: $arrMeta - Array - The page meta tags
		 */
		public static function getPageMeta($blnEchoContent = true, $blnReturnDataArray = false) {
			$blnIsCached	 = false;
			$arrMeta	 	 = array();
			$strRetContent 	 = NULL; 
			$objDatabase 	 = DATABASE::getInstance();
			$objMemcache	 = MEMCACHE_MANAGER::getInstance();
			/*
			$objCurrentUrl 	 = new URL(URL::getCurrentUrl());
			$strServerSelf	 = (
				(bool) self::getIdentifyPageName() ? 
				self::getIdentifyPageName() : 
				(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'])
			);
			*/
			$strServerSelf	 = URL::getCanonicalUrl(NULL, false, true, true, array(session_name()), false);
			if (! $strServerSelf) 
			{
				// This will return the correct current path when called by request dispacther..
				$objCurrentUrl 	 = new URL(URL::getCurrentUrl());
				$strServerSelf	 = PAGE_META::getFileRealPath($objCurrentUrl->getPath());
			}
			$strCacheKey	 = __CLASS__ . '::' . __FUNCTION__ . '[' . $strServerSelf . ']';

			/*
			$strDefaultCanonicalUrl = URL::getCanonicalUrl(null, true, false, true);
			// Remove the trailing slash at the end of the canonical URL.
			$strDefaultCanonicalUrl = (
				(substr($strDefaultCanonicalUrl, strlen($strDefaultCanonicalUrl) - 1, strlen($strDefaultCanonicalUrl)) == '/') ? 
				(substr($strDefaultCanonicalUrl, 0, strlen($strDefaultCanonicalUrl) - 1)) : $strDefaultCanonicalUrl
			);
			$strCacheKey	 = __CLASS__ . '::' . __FUNCTION__ . '[' . $strDefaultCanonicalUrl . ']';
			*/
			
			if (is_object($objMemcache))
			{
				$blnIsCached = $arrMeta = $objMemcache->get($strCacheKey);
			}
			
			if (true === empty($arrMeta))
			{
				/**
				 * Begin collecting the page meta
				 */
				$strCurrentPage  = (
					(bool) self::getIdentifyPageName() ? 
					self::getIdentifyPageName() : 
					/*PAGE_META::getFileRealPath($objCurrentUrl->getPath())*/ $strServerSelf
				);
				
				/*
				$strCurrentPage  = (
					(bool) self::getIdentifyPageName() ? 
					self::getIdentifyPageName() : 
					$strDefaultCanonicalUrl
				);
				*/
				// 1. get the current page name - We're looking for index.(*)
				$arrIndexCheck	 = explode(DIRECTORY_SEPARATOR, $strServerSelf);
				$arrIndexCheck	 = explode(".", end($arrIndexCheck));
				
				// 2. Check if there is a current page set - /folder/index.htm or /folder/
				$strDirName		 = ((count($arrIndexCheck) && $arrIndexCheck[0] == 'index') ?
					(substr($strCurrentPage, strlen($strCurrentPage) - strlen($strServerSelf), strlen($strCurrentPage)) == $strServerSelf ?
					dirname($strCurrentPage) : $strCurrentPage) : false
				);
				
				// Remove trailing slash from the $strDirName var
				// if its a folder
				/*
				$strDirName = (
					($strDirName && substr($strDirName, strlen($strDirName) - 1, strlen($strDirName)) == '/') ?
					substr($strDirName, strlen($strDirName) - 1, strlen($strDirName)), $strDirName
				);
				*/
				
				// 3. Filter variables for trailing slashes
				// var_dump(filter_var('example.com', FILTER_VALIDATE_URL));
				$strDirName 	= ((substr($strDirName, -1) == '/') ? substr($strDirName, 0, -1) : $strDirName);
				$strServerSelf 	= ((substr($strServerSelf, -1) == '/') ? substr($strServerSelf, 0, -1) : $strServerSelf);
				
				// Make sure the folder set is not the same as the page set 
				// if you go to ex: /folder/ the $strDirName and $strCurrentPage values
				// will both be /folder
				$strDirName = ($strDirName == $strCurrentPage ? false : $strDirName);
				$strWildCardSearch = ((bool) self::getIdentifyPageName() ? self::getIdentifyPageName() : dirname($strServerSelf)) . DIRECTORY_SEPARATOR . '*';
							
				// 4. Clean the variables for multiple slashes
				$strDirName 		= str_replace("//", "/", $strDirName);
				$strCurrentPage 	= str_replace("//", "/", $strCurrentPage);
				$strWildCardSearch 	= str_replace("//", "/", $strWildCardSearch);
				
				$strDirName 		= $objDatabase->escape($strDirName); 
				$strCurrentPage 	= $objDatabase->escape($strCurrentPage);
				$strWildCardSearch 	= $objDatabase->escape($strWildCardSearch);
				
				// 5. Launch the query
				$arrPageMetaView = PAGE_META::getObjectClassView($arrParams = array(
					'columns'	=> 	'meta_tag,meta_value,meta_prefix',	
					'filter'	=>	array(
						'pageUrl'	=>  ((bool) $strDirName ? 
										"('" . $strCurrentPage . "', '" . $strDirName . "', '" . $strWildCardSearch . "')" : 
										"('" . $strCurrentPage. "', '" . $strWildCardSearch . "')")
					),
					'operator'	=> 	array('IN'),
					'orderBy'	=> 	'order_index',
					'direction'	=> 	'ASC',
					'groupBy'	=>	'meta_tag',
					'escapeData'=>  false
				));
				
				$arrPageMeta = array();
				foreach($arrPageMetaView as $intI => $arrData) {
					$arrPageMeta[$arrData['meta_tag']] = array(
						'prefix' => $arrData['meta_prefix'],
						'data'	 => $arrData['meta_value']
					);
				}
				
				$arrDefaultPageMetaView = PAGE_META::getObjectClassView(array(
					'columns'	=> 	'meta_tag,meta_value,meta_prefix',	
					'filter'	=>	array(
						'is_default' =>  1
					),
					'orderBy'	=> 	'order_index',
					'direction'	=> 	'ASC',
					'groupBy'	=>	'meta_tag'
				));
				$arrDefaultPageMeta = array();
				foreach($arrDefaultPageMetaView as $intI => $arrData) {
					$arrDefaultPageMeta[$arrData['meta_tag']] = array(
						'prefix' => $arrData['meta_prefix'],
						'data'	 => $arrData['meta_value']
					);
				}
				
				
				
				$arrMeta = array_merge($arrDefaultPageMeta, $arrPageMeta);
				
				// Return the data array if required::
				if (TRUE === $blnReturnDataArray)
				{
					return (array(
						'cacheKey'	=>	$strCacheKey,
						'urlPath'	=> 	$strCurrentPage,
						'dataArray'	=>	$arrMeta
					));
				}
				
				/**
				 * Cache the data
				 */
				if (is_object($objMemcache))
				{
					$objMemcache->set(
						$strCacheKey,
						$arrMeta,
						strtotime("+30 minutes")
					);
				}
			}
			
			/**
			 * Begin meta output
			 */
			$strMetaDataHtml = '';
			foreach($arrMeta as $strMetaName => $arrMetaValue) {
				if ($arrMetaValue['prefix'] == 'tag')
					$strMetaDataHtml .= '<' . $strMetaName . '>' . $arrMetaValue['data'] . '</' . $strMetaName . '>' . "\n";
				else
					$strMetaDataHtml .= '<meta ' . $arrMetaValue['prefix'] . '="' . $strMetaName . '" content="' . $arrMetaValue['data'] . '" />' . "\n";
			}
			if (strlen($strMetaDataHtml)) {
				$strMetaDataHtml = "<!-- @page meta - c = " . ($blnIsCached ? 'true' : 'false') . " -->\n\r" . $strMetaDataHtml; 
				$objParser = new PARSER();	
				$objParser->setData(array_merge(array(
					'SITE_NAME'		=> __SITE_NAME__,
					'SITE_URL'		=> __SITE_URL__,
					'SITE_TITLE'	=> __SITE_TITLE__,
					'NOW'			=> now(),
					'DATE'			=> now(),
				), PAGE_META::$arrParsableTags));
				
				// Specific page meta
				$strRetContent = $objParser->parse($strMetaDataHtml);
			}
			
			if ($blnEchoContent)
			{
				echo ($strRetContent);	
			}
			else
			{
				return ($strRetContent);	
			}
		}
		
		public static function getFileRealPath($strFilePath=NULL) {
			$blnContinue = (! is_null($strFilePath) ? true : false);	
			$strRealFilePath = false;
			if ($blnContinue) {
				$strRealFilePath = str_replace(
					DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, 
					DIRECTORY_SEPARATOR, 
					pathinfo($strFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($strFilePath, PATHINFO_BASENAME)
				);	
			}
			return ($strRealFilePath);
		}
		
		/**
		 * This method forces a page to be identified by a certain id (will override all internal
		 * page identification setting																	   
		 * @access: private, static
		 * @param: 	none
		 * @return: PAGE_META::$identifiedPageName - String - The identified page name
		 */
		 private static function getIdentifyPageName() {
				return (PAGE_META::$identifiedPageName);
		 }
		
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() {
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
		}
		
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>