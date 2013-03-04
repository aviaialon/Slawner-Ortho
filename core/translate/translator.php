<?php
	/**
	 * TRANSLATOR Administration Class
	 * This class represents the sites translation features
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::TRANSLATE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
	
	class TRANSLATOR {
		// This is the constant where the dictionary files are found and default language
		private 	$TRANSLATOR_DICTONARY_TRANSLATION_FILE_PATH 	= '/application/translate/dictionary';
		private 	$TRANSLATOR_DICTONARY_DEFAULT_LANGUAGE 			= 'en';
		
		private $strLanguage			= 'en';
		private $strLangFileExtension 	= 'lang';
		private $arrLanguage			= array();
		/**
		 * Class constructor
		 * @param: string - $strLang - optional [Default: en] - [The language to load]
		 * @return: TRANSLATOR $this
		 */
		public function __construct($strLang = 'en') {
			$this->strLanguage = strtolower((string) $strLang);
		}
		
		/**
		 * This method returns an instance of the translator
		 * @return: TRANSLATOR [new __CLASS__]
		 */
		final public static function getInstance() {
			$objSession = SESSION::getSession();		
			$objSession->set('lang', (string) (
				(isset($_GET['lang'])) ? $_GET['lang'] : 'en' 								   
			));	
			$objNewTranslator = new TRANSLATOR($objSession->get('lang'));
			return ($objNewTranslator);
		}
		
		/**
		 * This method sets the class language
		 * @param: string - $strLang - optional [Default: en] - [The language to load]
		 * @return: void
		 */
		public function setLanguage($strLang = 'en') {
			$this->strLanguage = strtolower((string) $strLang);
		}
		
		/**
		 * This method gets a translated string
		 * @param: string 	- $strTranslateString 	- optional [Default: NULL] - [The string to translate]
		 * @param: boolean 	- $echoString 			- optional [Default: TRUE] - [If true, the translated string is printed]
		 * @return: void
		 */
		private function findString($strTranslateString = NULL, $echoString = true) {
			$strTranslatedString = $strTranslateString;
			if (
				(isset($this->arrLanguage[$this->strLanguage])) &&
				(array_key_exists(strtolower($strTranslateString), $this->arrLanguage[$this->strLanguage]))	
			) {
				$strTranslatedString = $this->arrLanguage[$this->strLanguage][strtolower($strTranslateString)];
			}
			if ((bool) $echoString) {
				echo($strTranslatedString);	
			}
			return ($strTranslatedString);
		}
		
		/**
		 * This method splits the original string to translated string
		 * @param: string 	- $strTargetString 	- optional [Default: NULL] - [The string key/pair to split]
		 * @return: array 	- The splitted string key / pair
		 */
		private function splitStrings($strTargetString = NULL) {
			return (explode('=', trim($strTargetString)));
		}
		
		/**
		 * This method normalises a string
		 * @param: string 	- $strTargetString 	- optional [Default: NULL] - [The string key/pair to split]
		 * @return: string 	- The normalised string
		 */
		private function normaliseString($strTargetString = NULL) {
			return (trim(preg_replace('[\s{2,}]', ' ', preg_replace('[\r]', '', $strTargetString))));
		}
		
		/**
		 * This method translates the string
		 * @param: string 	- $strString 	- optional [Default: NULL] - [The string to translate]
		 * @return: string	- The translated string.
		 */
		public function translate($strString=NULL) {
			$this->__loadLanguageConfigFile();
			$strTargetString = $this->normaliseString($strString);
			return ($this->findString($strTargetString));
		}
		
		/**
		 * This method loads (initially) the translation dictionary
		 * @param: 	void
		 * @return: void
		 */
		private function __loadLanguageConfigFile() {
			/**
			 * Create the language array on first use
			 */
			if (! (array_key_exists($this->strLanguage, $this->arrLanguage))) {	
				$strTranslationDictionary = __SITE_ROOT__ .  $this->TRANSLATOR_DICTONARY_TRANSLATION_FILE_PATH . DIRECTORY_SEPARATOR . $this->strLanguage.'.' . $this->strLangFileExtension;
				if (file_exists($strTranslationDictionary)) {
					$arrStrings = array_map(array($this,'splitStrings'), file($strTranslationDictionary));
					foreach ($arrStrings as $intIndex => $arrTranslatedKey) {
						$this->arrLanguage[$this->strLanguage][strtolower($this->normaliseString($arrTranslatedKey[0]))] = $this->normaliseString($arrTranslatedKey[1]);
					}
				}
			}	
		}
	}
?>