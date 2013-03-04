<?php
	/**
	 * SThis class represents a form entity
	 *
	 * @package		{__APPLICATION_ROOT__}
	 * @subpackage	NONE
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	class FORMHANDLER {
		private static $formFields = array();
		
		/**
		 * Class constructor
		 * The constructor will set the internal fields array with the GET and POST available data capture.
		 * 
		 * @access public
		 * @return FORMHANDLER	
		 */	
		public function __construct() 
		{ 
			self::$formFields = (array) self::getAllFields(); 
		}
		
		/**
		 * Class static initiator
		 * 
		 * @access public static final
		 * @return FORMHANDLER
		 */
		public static final function getInstance()
		{
			return new self();
		}
		
		/**
		 * @deprecated - This method is depreciated because all the methods are static in nature so we cant
		 * 				 assume that the contructor was called.
		 * 
		 * This method sets a field in the internal array of collected GET and POST fields.
		 * This method also assumes that the constructor was called and the getAllFields() sets
		 * 
		 * @access public static final 
		 * @param  string $strFieldName - the field name
		 * @param  mixed  $strFieldValue - the field value
		 * @return void
		 */
		public static final function setField($strFieldName, $strFieldValue) 
		{
			self::$formFields[$strFieldName] = $strFieldValue;
		} 
		
		/**
		 * This method searches in all the fields (GET and POST) for the requested value and returns it
		 * Order or presidence is POST first, GET second.
		 * 
		 * @access public static final
		 * @param  string $strFieldName - the field name
		 * @return mixed - The field value
		 */
		public static final function getField($strFieldName = FALSE) 
		{
			$trFieldValue = NULL;
			$arrAllFields = self::getAllFields();
			if ((bool) $strFieldName)
			{
				if (array_key_exists($strFieldName, $arrAllFields)) 
					$trFieldValue = $arrAllFields[$strFieldName];				
			}
			return($trFieldValue);	
		}
		
		/**
		 * This methog will initiate a form value with NULL if it is not already defined.
		 * 
		 * @access public static final 
		 * @param  string $strFieldName required - the form field name
		 * @return void
		 */
		public static final function param($strFieldName) 
		{
			if (! (isset($_POST[$strFieldName]))) $_POST[$strFieldName] = null;
		}
		
		/**
		 * This method 'parameterises' a form value. if the value exists in the form it is returned,
		 * otherwise, the default value passed as the secong argument is returned instead.
		 * 
		 * @access public static final 
		 * @param  string $strFieldName - The parameter name
		 * @param  mixed  $strDefault - The default parameter value to return otherwise
		 * @return mixed - The parameter value if exists, otherwise the default. 
		 */
		public static final function paramValue($strFieldName, $strDefault=false) 
		{
			return ((isset($_POST[$strFieldName]) ? stripslashes($_POST[$strFieldName]) : ($strDefault ? $strDefault : '')));
		}
		
		/**
		 * This method checks if the passed parameter exists in either the form or url parameters.
		 * If the parameter exists, it returns the value, otherwise, it returns an empty string. 
		 * It returns an empty script because URL parameters are able to pass 'false' as value.
		 * This method is NOT synonimous to isset() becuase it doesnt check if the value exists, 
		 * only if the that parameter exists WITH a value and returns the value.
		 * 
		 * Order or presidence is POST first, GET second.
		 * 
		 * @access public static final 
		 * @param  string $strValue - The parameter name
		 * @return string - The parameter value if exists, otherwise an empty string.
		 */
		public static final function getOrPost($strValue = NULL) 
		{
			$strReturnValue = "";
			if (isset($_POST[$strValue]))
				$strReturnValue = (strlen(trim($_POST[$strValue])) ? $_POST[$strValue] : false);
			else if (isset($_GET[$strValue]))
				$strReturnValue = (strlen(trim($_GET[$strValue])) ? $_GET[$strValue] : false);	
			return ($strReturnValue);	
		}
		
		/**
		 * This method returns true if the current request is a POST request
		 * 
		 * @access public static final 
		 * @return boolean - returns true if the current request is a POST request
		 */
		public static function isPost() 
		{
			return ((strcmp(strtoupper($_SERVER['REQUEST_METHOD']), 'POST') == 0 ? true : false));
		}
		
		/**
		 * This method returns true if the current request is a XHTTP (Ajax) request
		 * 
		 * @access public static final 
		 * @return boolean - returns true if the current request is a XHTTP (Ajax) request
		 */
		public static function isXHTTPRequest()
		{
			return (
				(bool)	
				((isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && 
				(strcmp(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') == 0))		
			);	
		}
		
		/**
		 * Checks if the first value is null, or doesnt have a length, or is equal to false,
		 * if its a null value, it returns the default or the first value
		 * 
		 * @access public static final 
		 * @param  mixed $strValue - The value to compare
		 * @param  mixed $objDefault - The default value
		 * @return mixed The first value or the default.
		 */
		public static final function nullValue($strValue = NULL, $objDefault = NULL) 
		{
			return (((is_null($strValue)) || (! strlen($strValue)) || (FALSE === $strValue)) ? $objDefault : $strValue);	
		}
		
		/**
		 * This method is used in conjuction with a SELECT box, it checks if the current value exists
		 * in the form and if it does, it outputs 'selected' in the select option.
		 * 
		 * @param  string $strFieldName - The current field name (form field name)
		 * @param  string $strValue 	- The form field current value
		 * @return void
		 */
		public static function isSelected($strFieldName=NULL, $strValue=NULL) 
		{
			echo(
				(self::isFormPostValue($strFieldName, $strValue) ? 'selected' : '')
			);
		}
		
		/**
		 * This method is used in conjuction with a CHECK box, it checks if the current value exists
		 * in the form and if it does, it outputs 'checked' in the checkbox option.
		 * 
		 * @param  string $strFieldName - The current field name (form field name)
		 * @param  string $strValue 	- The form field current value
		 * @return void
		 */
		public static final function isChecked($strFieldName=NULL, $strValue=NULL) 
		{
			echo(
				(self::isFormPostValue($strFieldName, $strValue) ? 'checked' : '')
			);
		}
		
		/**
		 * This method checks if the values passed match eachother. This checks if the 
		 * passed value (in a form post) exists and if they match.
		 * 
		 * @access: public, static, final
		 * @param: 	$strFieldName String - The field name posted to check against
		 * @param:	$strValue String - The post field value to check
		 * @return:	Boolean - If the values match and exists in the form post
		 */
		public static final function isFormPostValue($strFieldName=NULL, $strValue=NULL) 
		{
			return(
				((! is_null($strFieldName)) 	&& 
				(! is_null($strValue)) 			&&
				(self::paramValue($strFieldName, false)) && 
				(self::paramValue($strFieldName) == $strValue)) ? true : false
			);
		}
		
		/**
		 * This method clears the post data
		 * 
		 * @access public, static, final
		 * @return void
		 */
		public static final function clearForm() 
		{
			unset($_POST);
		}		
		
		/**
		 * This method will return the post and get merged array
		 * 
		 * @access public, static, final
		 * @return  array - the merged url and post array 
		 */
		public static final function getAllFields() 
		{ 
			return(array_merge($_GET, $_POST)); 
		}
		
		/**
		 * This method will set a URL parameter.
		 * 
		 * @access public, static, final
		 * @param  string $strParamName	- The URL parameter name
		 * @param  mixed  $mxParamValue	- The url parameter value
		 * @return void
		 */
		public static final function setUrlParam($strParamName, $mxParamValue = NULL)
		{
			$_GET[$strParamName] = $mxParamValue;
		}
		
		/**
		 * This method will set a POST parameter.
		 * 
		 * @access public, static, final
		 * @param  string $strParamName	- The URL parameter name
		 * @param  mixed  $mxParamValue	- The url parameter value
		 * @return void
		 */
		public static final function setPostParam($strParamName, $mxParamValue = NULL)
		{
			$_POST[$strParamName] = $mxParamValue;
		}
		
		/**
		 * This method will det a URL parameter.
		 * 
		 * @access public, static, final
		 * @param  string $strParamName	- The URL parameter name
		 * @return mixed  The url parameter if exists
		 */
		public static final function getUrlParam($strParamName)
		{
			return ((true === isset($_GET[$strParamName]) ? $_GET[$strParamName]  :false));
		}
		
		/**
		 * This method will get a POST parameter.
		 * 
		 * @access public, static, final
		 * @param  string $strParamName	- The URL parameter name
		 * @return mixed  The post parameter if exists
		 */
		public static final function getPostParam($strParamName)
		{
			return ((true === isset($_POST[$strParamName]) ? $_POST[$strParamName] : false));
		}
		
		/**
		 * This method will return all POST parameter.
		 * 
		 * @access public, static, final
		 * @return Array  $_POST
		 */
		public static final function getPostData()
		{
			return ((array) $_POST);
		}
		
		/**
		 * This method will return all GET parameter.
		 * 
		 * @access public, static, final 
		 * @param  array - the url array data
		 */
		public static final function getUrlData()
		{
			return ((array) $_GET);
		}

		/**
		 * This method will set a default POST param if its not defined
		 * 
		 * @access 	public, static, final
		 * @param	string $strParamName - the parameter name
		 * @param	mixed  $mxParamValue - the parameter value
		 * @return 	void
		 */
		public static final function setDefaultPostParam($strParamName, $mxParamValue = NULL)
		{
			if (FALSE === isset($_POST[$strParamName]))
			{
				$_POST[$strParamName] = $mxParamValue;	
			}
		}
		
		/**
		 * This method will set a default URL param if its not defined
		 * 
		 * @access 	public, static, final
		 * @param	string $strParamName - the parameter name
		 * @param	mixed  $mxParamValue - the parameter value
		 * @return 	void
		 */
		public static final function setDefaultUrlParam($strParamName, $mxParamValue = NULL)
		{
			if (FALSE === isset($_GET[$strParamName]))
			{
				$_GET[$strParamName] = $mxParamValue;	
			}
		}
	}
?>
