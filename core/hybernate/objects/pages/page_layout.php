<?php
	/**
	 * PAGE_LAYOUT Administration Class
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
	 
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PARSER::PARSER");
	 
	 class PAGE_LAYOUT extends SHARED_OBJECT {
		 
		 private $strParsedLayoutData 	= NULL;	// The parsed HTML data
		 private $strLayoutDataRaw 		= NULL; // The Raw layout html data
		 
		 public function getTemplateComponentPositions() {
		 	$objParser = PARSER::getInstance();
			return ($objParser->getAvailableVariable($this->getLayoutContent()));
		 }
		 
		 /**
		  * This method returns the layout HTML data,
		  * If a template path is available it takes presidence
		  * over the template HTML value in the database
		  * @param: Void
		  * @return: string - the template HTML (Unparsed)
		  */
		 public function getLayoutContent() { 
		 	$objParser 			= PARSER::getInstance();
			
			if (! (bool) $this->strLayoutDataRaw) {
				switch (true) {
					/**
					 * Here we have a file template path to use.
					 */
					case ((bool) $this->getVariable('template_path')) : 
					{
						$strFilePath = __SITE_ROOT__ . DIRECTORY_SEPARATOR . $this->getVariable('template_path');
						$strFilePath = str_replace('//', '/', $strFilePath);
						$strFilePath = str_replace('\\\\', '\\', $strFilePath);
						
						if (file_exists($strFilePath)) 
						{
							$this->strLayoutDataRaw = file_get_contents($strFilePath);
						} 
						else 
						{
							throw new Exception(
								'The template: ' . $strFilePath . ' was not found.'
							);	
						}
						break;	
					}
					
					/**
					 * Here we have template HTML to use.
					 */
					case ((bool) $this->getVariable('template_html')) : 
					{
						$this->strLayoutDataRaw = $this->getVariable('template_html');
						break;
					}
				}
			} 
			
			return ($this->strLayoutDataRaw);
		 }
		 
		/**
			Abstraction Methods
		**/
		protected function onBefore_getInstance() {
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE);
		}
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>