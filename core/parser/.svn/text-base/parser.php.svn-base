<?php

/**
 * This class represents a parser
 * @package PARSER
 */ 
class PARSER {
	/**
	 * This returns a new instance of the parser object
	 */
	public static function getInstance() {
		$objParser = new PARSER();
		$objParser->setData(array());
		return ($objParser);
	}
	
	/**
	 * This method will return all available variable for the string it receive in an array
	 * @param string $strToParse
	 * @return array
	 */
	function getAvailableVariable($strToParse) {
		$arrMatch = array();
		$arrReturn = array();

		preg_match_all("/%%(\w+)%%/i", $strToParse, $arrMatch);
		if(is_array($arrMatch[1])) {
			$arrReturn = $arrMatch[1];
		}
		return $arrReturn;
	}
	
	/**
	 * Parser constructor. Create parser object and initialise object with array('name' => 'Yanik', 'domain' => 'www.privatefeeds.com')
	 * @param optional array attarrData 
	 * @return void
	 */
	function PARSER($attarrData = array()) {
		$this->setData($attarrData);
	}

	/**
	 * Set object data with specified array use array like array('name' => 'Yanik', 'domain' => 'www.privatefeeds.com')
	 * @param optional array attarrData 
	 * @return void
	 */
	function setData($attarrData = array()) 	{
		if ($attarrData) {
			$this->arrData = array_change_key_case($attarrData, CASE_UPPER);
		}
	}

	/**
	 * Parser call back method. For internal use. 
	 * @param array attarrData
	 * @return string
	 */
	function _parseCallBack($attarrData) {
		return (isset($this->arrData[strtoupper($attarrData[1])]) ? $this->arrData[strtoupper($attarrData[1])] : false);
	}

	/**
	 * Parse method. 
	 * @param mixed attMixedTemplate "Hi %%NAME%%, your domain is : %%DOMAIN%%"
	 * @return mixed "Hi Yanik, your domain is : www.privatefeeds.com"
	 */
	function parse($attMixedTemplate) {
		return preg_replace_callback('(%%(\w+)%%)', array($this, '_parseCallBack'), $attMixedTemplate);
	}

	/**
	 * Array form string. 
	 * @param string attstrTemplate "name=%%NAME%%&domain=%%DOMAIN%%"
	 * @return array ('name' => 'Yanik', 'domain' => 'www.privatefeeds.com')
	 */
	function getArrayFromString($attstrTemplate) {
		$attstrTemplate = preg_replace('(%%(\w+)%%)', '##$1##', $attstrTemplate);
		parse_str($attstrTemplate, $arrReturn);
		$arrReturn = preg_replace('(##(\w+)##)', '%%$1%%', $arrReturn);
		return $this->Parse($arrReturn);
	}
	
	
}
?>