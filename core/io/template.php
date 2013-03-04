<?php
	require_once(__APPLICATION_ROOT__ . '/parser/parser.php'); 
	require_once(__APPLICATION_ROOT__ . '/content/savecontent.php');
	/**
	 * This class represents a templateing engine
	 * @author Avi Aialon
	 * @package CLASSES::IO
	 */
	class TEMPLATE extends SITE_EXCEPTION {
		private   $blnIsValid 		= false;
		protected $objContent 		= null;
		protected $arrParseData		= array();
		protected $strTemplate 		= "";
		protected $strRawTemplate 	= "";
		protected $strTemplatePath 	= "";
		protected $strTemplateExt 	= "tmpl"; // Template file extensions. [.tmpl]
		
		public function __construct($strTemplatePath=NULL) {
			if (! (is_null($strTemplatePath)))
				$this->setTemplatePath($strTemplatePath);
		}
		
		/**
		 * Setters / Getters
		 **/
		// Set the template by path EX: templates::email::template (would be: templates/email/template.tmpl); 
		public function setTemplatePath($strTemplatePath=NULL) {
			$strTemplateFile = __SITE_ROOT__ . DIRECTORY_SEPARATOR . str_replace('::', DIRECTORY_SEPARATOR, $strTemplatePath) . "." . $this->getTemplateExtension();
			if (file_exists($strTemplateFile)) {
				$this->strTemplatePath = $strTemplateFile;	
				$this->blnIsValid = true;
			} else {
				throw new Exception('Template ' . $strTemplateFile . ' does not exists. Please double check the path.');	
			}
		}
		
		// Set the template by URL EX: templates/email/template.tmpl 
		public function setTemplateUrl($strTemplatePath=NULL) {
			$strTemplateFile = __SITE_ROOT__ . DIRECTORY_SEPARATOR . $strTemplatePath;
			if (file_exists($strTemplateFile)) {
				$this->strTemplatePath = $strTemplateFile;	
				$this->blnIsValid = true;
			} else {
				throw new Exception('Template ' . $strTemplateFile . ' does not exists. Please double check the path.');	
			}
		}
		
		public function setTemplateExtension($strTemplateExt=NULL) {
			if (
				(! is_null($strTemplateExt)) &&
				(strlen($strTemplateExt))
			) {
				$this->strTemplateExt = trim($strTemplateExt);
			}
		}
		
		public static function getFileTemplate($strFileTemplate=NULL) {
			$strReturn = "";
			if (
				(! is_null($strFileTemplate)) &&
				(file_exists($strFileTemplate))
			) {
				$objContent = new SAVECONTENT();	
				$objContent->start();
					require_once($strFileTemplate);
				$objContent->stop();	
				$strReturn = $objContent->getContent();
			}
			return ($strReturn);
		}
		
		public static function getFileTemplateINI($strFileTemplate=NULL) 
		{
			$strFilePath = __SITE_ROOT__ . DIRECTORY_SEPARATOR . $strFileTemplate;
			$strFilePath = str_replace('//', '/', $strFilePath);
			$strFilePath = str_replace('\\\\', '\\', $strFilePath);
			
			if (file_exists($strFilePath)) 
			{
				$strReturn = file_get_contents($strFilePath);
			} 
			else 
			{
				throw new Exception(
					'The template: ' . $strFilePath . ' was not found.'
				);	
			}
		
			/*
			$strReturn = "";
			if (
				(! is_null($strFileTemplate))
			) {
				$strReturn = file_get_contents($strFileTemplate);
			}
			*/
			return ($strReturn);
		}
		
		public	  function setData($arrData=array()) { $this->arrParseData = (array) $arrData; } 
		public 	  function render() 				 { echo($this->getTemplate()); }
		public 	  function getTemplate() 			 { return($this->strTemplate); }
		
		protected function setTemplate($strTemplateOpt=NULL) 	{ $this->strTemplate = $strTemplateOpt; }
		protected function getParseData() 			 			{ return($this->arrParseData); } 
		protected function getTemplatePath() 	 				{ return($this->strTemplatePath); }
		protected function getTemplateExtension() 	 			{ return($this->strTemplateExt); }
		protected function getRawTemplate() 		 			{ return($this->strRawTemplate); }
		
		public function execute() {
			if ((bool) $this->blnIsValid) { 
				$objParser				= new PARSER();
				$this->strRawTemplate 	= file_get_contents($this->getTemplatePath());
				$objParser->setData($this->getParseData());
				$this->setTemplate(
					$objParser->parse($this->getRawTemplate())				   
				);
			}
		}
	}
?>