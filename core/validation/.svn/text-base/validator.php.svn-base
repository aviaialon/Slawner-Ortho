<?php
	/**
	 * VALIDATOR Administration Class
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
	 
	 class VALIDATOR {
	 	protected static $error = NULL;
	 
		public function __construct() {
			
		}
		
		/**
		 *
		 * @sets the private error message
		 * @access protected, static
		 * @return string - validation error message
		 */
		 protected static function setError($strError=NULL) {
		 	self::$error = $strError;
		 }
		
		/**
		 *
		 * @returns the private error message
		 * @access public, static
		 * @return string - validation error message
		 */
		 public static function getError() {
		 	return (self::$error);
		 }
		
		/**
		 *
		 * @validate an email address
		 * @access public, static
		 * @param string $strEmail - The email address
		 * @return boolean - validation success
		 *
		 */
		public static function email($strEmail = NULL) {
			$blnContinue = ((! is_null($strEmail)) && (strlen($strEmail)));
			if ($blnContinue) {
				$blnContinue = ((bool) preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $strEmail));	
			}
			return ($blnContinue);
		}
		
		
		/**
		 *
		 * @validate an email address Extensivly by also checking the DNS records
		 * @access public, static
		 * @param string $strEmail - The email address
		 * @return boolean - validation success
		 *
		 */
		public static function emailExtended ($strEmail = NULL) {
			$blnIsValidEmail 	= true;
			$strMessage 		= '';
			
			// Find the @ index
			$intAtIndex = strrpos($strEmail, "@");
			$blnIsValidEmail = ((is_bool($intAtIndex) && (! $intAtIndex)) ? false : true);
			
			if ($blnIsValidEmail) {
				$blnIsValidEmail = (bool) self::email($strEmail);
				if (! $blnIsValidEmail) self::setError("Please enter a valid email address.");
			}
			
			if ($blnIsValidEmail) {
				$strDomain 		= substr($strEmail, $intAtIndex + 1);
				$strLocal 		= substr($strEmail, 0, $intAtIndex);
				$intLocalLen 	= strlen($strLocal);
				$intDomainLen 	= strlen($strDomain);
				
				if (($intLocalLen < 1) || ($intLocalLen > 64)){
					$strMessage = 'local part length exceeded';
					$blnIsValidEmail = false;
				}
				else if (($intDomainLen < 1) || ($intDomainLen > 255)){
					$strMessage = ' domain part length exceeded ';
					$blnIsValidEmail = false;
				}
				else if ($strLocal[0] == '.' || $strLocal[$intLocalLen-1] == '.'){
					$strMessage = ' local part starts or ends with .';
					$blnIsValidEmail = false;
				}
				else if (preg_match('/\\.\\./', $strLocal)){
					$strMessage = 'local part has two consecutive dots';
					$blnIsValidEmail = false;
				}
				else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $strLocal)){
					$strMessage = 'character not valid in local part';
					$blnIsValidEmail = false;
				}
				else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $strDomain)){
					$strMessage = 'character not valid in domain part';
					$blnIsValidEmail = false;
				}
				else if (preg_match('/\\.\\./', $strDomain)){
					$strMessage = '  domain part has two consecutive dots';
					$blnIsValidEmail = false;
				}
				else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $strLocal))){
					$strMessage = '  character not valid in local part unless local part is quoted';
					if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","", $strLocal))){
						$blnIsValidEmail = false;
					}
				}
				if ($blnIsValidEmail && !(checkdnsrr($strDomain, "MX") || checkdnsrr($strDomain, "A"))){
					$strMessage = '  domain <b>'.$strDomain.'</b> not found in DNS';
					$blnIsValidEmail = false;
				}
			}
			self::setError($strMessage);
			return ((bool) $blnIsValidEmail);
		}
		/**
		 *
		 * @validate a user name (no special characters only letters, numbers, underscores and dashes)
		 * @access public, static
		 * @param string $strEmail - The email address
		 * @return boolean - validation success
		 *
		 */
		public static function userName($strUserName = NULL) {
			$blnContinue = ((! is_null($strUserName)) && (strlen($strUserName)));
			if ($blnContinue) {
				$blnContinue = ((bool) (preg_match("/[^a-zA-Z0-9_-]/", $strUserName) ? false : true));
			}
			return ($blnContinue);
		}
		
		/**
		 *
		 * @validate an ipv4 IP address
		 * @access public, static
		 * @param string $ipAddr The variable name
		 * @return boolean - validation success
		 *
		 */
		 public static function ipv4($ipAddr=NULL) {
			 $blnContinue = (! is_null($ipAddr));
			 if ($blnContinue) {
				 $blnContinue = (filter_var($ipAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE ? false : true);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate an ipv6 IP address
		 * @access public, static
		 * @param string $ipAddr The variable name
		 * @return boolean - validation success
		 *
		 */
		 public static function ipv6($ipAddr=NULL) {
			 $blnContinue = (! is_null($ipAddr));
			 if ($blnContinue) {
				 $blnContinue = (filter_var($ipAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE ? false : true);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate an simple IP address
		 * @access public, static
		 * @param string $ipAddr The variable name
		 * @return boolean - validation success
		 *
		 */
		 public static function ipAddress($ipAddr=NULL) 
		 {
			 $blnContinue = (! is_null($ipAddr));
			 if ($blnContinue) {
				 $blnContinue = (bool) preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ipAddr);
			 }
			 return ($blnContinue);
		 }
		
		/**
		 *
		 * @validate a floating point number
		 * @access public, static
		 * @param numeric $fltNum - The variable name, float
		 * @return boolean - validation success
		 *
		 */
		 public static function isFloat($fltNum=NULL) {
			 $blnContinue = (! is_null($fltNum));
			 if ($blnContinue) {
				 $blnContinue = (filter_var($ipAddr, FILTER_VALIDATE_FLOAT) === FALSE ? false : true);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate a numeric range (ex: from 1 to 10)
		 * @access public, static
		 * @param numeric $intNume 	- The number being validated
		 * @param array $arrRange 	- numeric range - keys: min (the minimum) max - (the maximum)
		 * @return boolean - validation success
		 *
		 */
		 public static function numberRange($intNume=NULL, $arrRange = array()) {
			 $blnContinue = (
				(! is_null($intNume)) &&
				(isset($arrRange['min'])) &&
				(isset($arrRange['max'])) &&
				(is_numeric($arrRange['min']))&&
				(is_numeric($arrRange['max'])) &&
				((int) $arrRange['max'] > (int) $arrRange['min'])
			 );
			 if ($blnContinue) {
				 $blnContinue = (
					(filter_var($intNume, FILTER_VALIDATE_INT, array("options" => array("min_range" => $arrRange['min'], "max_range"=> $arrRange['max'])))===FALSE) ? false : true
				);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate a URL
		 * @access public, static
		 * @param string $strUrl - The variable name, URL
		 * @return boolean - validation success
		 *
		 */
		 public static function url($strUrl=NULL) {
			 $blnContinue = (! is_null($fltNum));
			 if ($blnContinue) {
				 $blnContinue = (filter_var($strUrl, FILTER_VALIDATE_URL) === FALSE ? false : true);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate a Boolean
		 * @access public, static
		 * @param boolean $blnVar - The boolean value
		 * @return boolean - validation success
		 *
		 */
		 public static function isBoolean($blnVar=NULL) {
			 $blnContinue = (! is_null($blnVar));
			 if ($blnContinue) {
				 $blnContinue = (filter_var($blnVar, FILTER_VALIDATE_BOOLEAN) === FALSE ? false : true);
			 }
			 return ($blnContinue);
		 }
		 
		/**
		 *
		 * @validate a phone number
		 * @access public, static
		 * @param boolean $strTelNumber - The phone number value
		 * @return boolean - validation success
		 *
		 */
		  public static function phone($strTelNumber=NULL) {
			 $blnContinue = (! is_null($strTelNumber));
			 if ($blnContinue) {
				 $blnContinue = ((bool) preg_match('/^\(?[0-9]{3}\)?|[0-9]{3}[-. ]? [0-9]{3}[-. ]?[0-9]{4}$/', $strTelNumber));
			 }
			 return ($blnContinue);
		 }
		 
		 /**
		 *
		 * @validate a string length
		 * @access public, static
		 * @param boolean $strTelNumber - The phone number value
		 * @return boolean - validation success
		 *
		 */
		  public static function stringValidate($strString=NULL) {
			 $blnContinue = ((! is_null($strString)) && (strlen(trim($strString))));
			 return ($blnContinue);
		 }
		 
	 	/**
		 *
		 * @validate a credit card
		 * @access 		public, static
		 * @param 		string $intCreditCardNumber - The Credit Card Number
		 * @return	 	boolean - validation success
		 *
		 */
		public static function creditCard($intCreditCardNumber = NULL) {
			$blnContinue = ((! is_null($intCreditCardNumber)) && (strlen($intCreditCardNumber)));
			if ($blnContinue) {
				$intCreditCardNumber = preg_replace('/[^0-9]+/', '', $intCreditCardNumber);
				$intSum = 0;
				$intStringLength = strlen($intCreditCardNumber);
				if ($intStringLength < 13){ return false; }
				for ($i=0;$i<$intStringLength;$i++)
				{
					$intDigit = substr($intCreditCardNumber, $intStringLength - $i - 1, 1);
					if ($i % 2 == 1)
					{
						$intSubTotal = $intDigit * 2;
						if($intSubTotal > 9)
						{
							$intSubTotal = 1 + ($intSubTotal - 10);
						}
					} 
					else 
					{
						$intSubTotal = $intDigit;
					}
					$intSum += $intSubTotal;
				}
				if ($intSum > 0 && $intSum % 10 == 0)
				{ 
					return (true); 
				}
				return (false);
			}
			
			return (false);
		}
	}
?>