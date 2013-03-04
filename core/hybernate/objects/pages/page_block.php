<?php
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MEMCACHE::MEMCACHE_MANAGER");
	 
	/**
	 * PAGE_BLOCK Administration Class
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
	 
	 /**
	  * TABLE DEFINITION:
	  	DROP TABLE IF EXISTS `page_block`;
	  	CREATE TABLE `page_block` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `sectionBlockId` int(11) NOT NULL,
			  `lang` varchar(2) NOT NULL,
			  `urlPath` varchar(600) NOT NULL,
			  `blockContent` text,
			  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `active` bit(1) NOT NULL DEFAULT b'1',
			  `description` varchar(500) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_lang_urlPath` (`lang`,`urlPath`)
		) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1
	  */
	  
	 /**
	  * USAGE:
	  *	PAGE_BLOCK::getInstanceFromPage() will return an array (sectionBlockId => page_block object)
	  * From that instance calling getExecutedBlockContent() will return the executed block content
	  * Also from that instance, calling getBlockGroup(int id) will return a block specific instance
	  * Ex:
	  *			$objPageBlockSections = PAGE_BLOCK::getInstanceFromPage(); // Load all the page blocks for this page
	  *			echo($objPageBlockSections->getBlockGroup(1)->getExecutedBlockContent()); // Echo section #1
	  *
	  */
	 class PAGE_BLOCK extends SHARED_OBJECT 
	 {
		/**
		 * This method returns the pages instance
		 *
		 * @access	public static final
		 * @param	String $strPageCanonicalUrl - The page canonical URL. if none is provided, the current canonical URL is used
		 * @return	PAGE_BLOCK
		 */
		public static final function getInstanceFromPage($strPageCanonicalUrl = NULL)
		{
			$Application 			= APPLICATION::getInstance();
			$objPageBlockInstance	= PAGE_BLOCK::getInstance();
			$strDefaultCanonicalUrl = URL::getCanonicalUrl(NULL, true, false, true, array(), false);
			$_objMemcache 			= MEMCACHE_MANAGER::getInstance(); 

			// Remove the trailing slash at the end of the canonical URL.
			$strDefaultCanonicalUrl = (
				((substr($strDefaultCanonicalUrl, strlen($strDefaultCanonicalUrl) - 1, strlen($strDefaultCanonicalUrl)) == '/') && ($strDefaultCanonicalUrl !== '/')) ? 
				(substr($strDefaultCanonicalUrl, 0, strlen($strDefaultCanonicalUrl) - 1)) : $strDefaultCanonicalUrl
			);

			$strQueryUrl = ((true === is_null($strPageCanonicalUrl)) ? $strDefaultCanonicalUrl : $strPageCanonicalUrl);
			
			// Load the cache
			/*
			$_blnMemcacheEnabled = false;
			if (
				(true === is_object($_objMemcache)) &&
				(true === $_objMemcache->isServerOnline())
			) {
				$_blnMemcacheEnabled 	= true;
				$intKeyVersionNumber 	= $_objMemcache->getKeyVersionNumber();
				$strCacheKey 			= __CLASS__ . '::' . __FUNCTION__ . '[' . $intKeyVersionNumber . '][' . $Application->translate('en', 'fr') . '][' . $strQueryUrl . ']';	
				$arrCachedData			= $_objMemcache->get($strCacheKey);
				if (false === empty($arrCachedData)) {
					$objPageBlockInstance->setPageBlockGroups($arrCachedData);
					return ($objPageBlockInstance);	
				}
			}
			*/
			$arrObjPageBlocks 	= (PAGE_BLOCK::getMultiInstance(array(
				'columns' 		=>	array('a.id', 'a.sectionBlockId', 'a.lang', 'a.blockContent', 'a.urlPath'),
				'filter'		=>	array(
					'a.urlPath'	=>	"('" . implode("','", array($strQueryUrl, '*')) . "')",
					'a.active'	=>	1,
					'a.lang'	=> 	"'" . $Application->getDatabase()->escape($Application->translate('en', 'fr')) . "'"
				),
				'operator'		=>	array(
					'IN', '=', '='
				),
				'groupBy'		=> 'a.sectionBlockId, a.lang',
				'orderBy'		=> 'IF(a.urlPath = "*", NULL, a.urlPath), a.sectionBlockId',
				'direction'		=> 'ASC',
				'escapeData'	=> false,
				'debug' 		=> false
			), false));
			
			$arrPageBlockObjects = array();
			while (list($intIndex, $objPageBlock) = each($arrObjPageBlocks))
			{
				/**
				 * Removed because the execution of the block content should be handled by the 
				 * Object instance. so that instance remain compatible with shared hybernate objects.
				 *
				$strExecutedPageBlock 	= $objPageBlock->getBlockContent();
				$strExecutedPageBlock	= str_replace('<code>', '<?php ', $strExecutedPageBlock);
				$strExecutedPageBlock	= str_replace('</code>', ' ?>', $strExecutedPageBlock);
				ob_start(); eval('?>' . $strExecutedPageBlock);
				$strComponentOutput = ob_get_clean();
				$objPageBlock->setExecutedPageBlock($strComponentOutput);	
				*/
				$arrPageBlockObjects[(int) $objPageBlock->getSectionBlockId()] = $objPageBlock;
			}
			
			$objPageBlockInstance->setPageBlockGroups($arrPageBlockObjects);
			/*
			if (true === $_blnMemcacheEnabled) 
			{
				SITE_EXCEPTION::supressException();
				$_objMemcache->set($strCacheKey, $arrPageBlockObjects);
				SITE_EXCEPTION::clearExceptionSupress();
			}
			*/
			return ($objPageBlockInstance);
		}
		
		/**
		 * This method returns a page block group instance
		 *
		 * @access	public final
		 * @param	int $intBlockGroupId - The page block group ID
		 * @return	PAGE_BLOCK
		 */
		 public final function getBlockGroup($intBlockGroupId = NULL)
		 {
			 $objPageBlockReturn = self::getInstance();
			 $arrPageBlockGroups = $this->getPageBlockGroups();
			 if (
			 	(false === is_null($intBlockGroupId)) &&
				((int) $intBlockGroupId > 0) &&
				(false === empty($arrPageBlockGroups)) &&
				(true  === isset($arrPageBlockGroups[(int) $intBlockGroupId]))
			 ) {
				$objPageBlockReturn = $arrPageBlockGroups[(int) $intBlockGroupId];
			 }
			 
			 return ($objPageBlockReturn);
		 }
		 
		/**
		 * This method returns the executed page block content
		 *
		 * @access	public final
		 * @param	none
		 * @return	String
		 */
		public final function getExecutedBlockContent()
		{
			$Application 			= APPLICATION::getInstance();
			$strExecutedPageBlock 	= $this->getBlockContent();
			$strExecutedPageBlock	= str_replace('<php>', '<?php ', $strExecutedPageBlock);
			$strExecutedPageBlock	= str_replace('</php>', ' ?>', $strExecutedPageBlock);
			$strExecutedPageBlock	= str_replace('<javascript>', '<script type="text/javascript">', $strExecutedPageBlock);
			$strExecutedPageBlock	= str_replace('</javascript>', '</script>', $strExecutedPageBlock);
			ob_start(); eval('?>' . utf8_encode($strExecutedPageBlock));
			$strComponentOutput = ob_get_clean();
			return ($strComponentOutput);
		}
		
		/**
		 * This method renders the pageBlockGroups in JavaScript format / Used in the admin section
		 *
		 * @access	public final
		 * @param	boolean $blnReturnArray  - return the data array
		 * @return	void
		 */
		public final function jsonEncodePageBlockGroups($blnReturnArray = false)
		{
			$arrPageBlockGroups = $this->getPageBlockGroups();	
			$arrPageBlockData	= array();	 
			while (list($intPageBlockId, $objPageBlock) = each($arrPageBlockGroups))
			{
				$arrPageBlockData[(int) $intPageBlockId] = $objPageBlock->get();	
				$arrPageBlockData[(int) $intPageBlockId]['blockContent'] = utf8_encode($objPageBlock->getBlockContent());
				$arrPageBlockData[(int) $intPageBlockId]['executedData'] = utf8_encode($objPageBlock->getExecutedBlockContent());
			}
			
			if (false === $blnReturnArray)
			{
				
				echo (json_encode($arrPageBlockData));	
			}
			else
			{
				return ($arrPageBlockData);
			}
		}
		
		/**
		 * This method saves the edited page blocks
		 *
		 * @access	public final
		 * @return	void
		 */
		public final function saveEditedPageBlocks()
		{
			$this->setErrors(array());
			
			$Application 			= APPLICATION::getInstance();
			$objPageBlockInstance 	= PAGE_BLOCK::getInstance();
			$Application->getUser()->requireLogin(SITE_USERS_ROLE_ADMIN_USER);
			$arrEditedContentBlocks = $Application->getRequestDispatcher()->getRequestParam('editedContentBlocks');	
			$strCanonicalUrl 		= $Application->getRequestDispatcher()->getRequestParam('canonicalUrl');	
			$blnContinue 			= ((false === (empty($arrEditedContentBlocks))) && (false === (empty($strCanonicalUrl)))) || ($this->addToErrors('Please validate Data') & false);
			
			if (true === $blnContinue) 
			{
				while (list($intContentBlockId, $strNewPageBlockHtml) = each($arrEditedContentBlocks))
				{
					$objPageBlock = PAGE_BLOCK::getInstanceFromKey(array(
						'sectionBlockId' => (int) $intContentBlockId,
						'urlPath'	 	 => $strCanonicalUrl,
						'lang'			 => $Application->translate('en', 'fr')
					));
					
					if ($objPageBlock->getId() > 0)
					{
						$objPageBlock->setBlockContent(utf8_decode($strNewPageBlockHtml));
						$objPageBlock->save();
					}
				}
				
				$objPageBlockInstance = PAGE_BLOCK::getInstanceFromPage($strCanonicalUrl);
			}

			echo (json_encode(array(
				'SUCCESS' 	=>  $blnContinue,
				'ERRORS' 	=>	$this->getErrors(),
				'DATA'		=>	$objPageBlockInstance->jsonEncodePageBlockGroups(true)
			)));
		}
		 
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() 
		{
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE);
		}
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>
