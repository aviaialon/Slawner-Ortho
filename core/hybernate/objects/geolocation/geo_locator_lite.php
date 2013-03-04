<?php
/**
 * GEO_LOCATOR_LITE: Lite Version of the Geo Location Administration Class
 * This class represents the CRUD [Hybernate] behaviors implemented 
 * with the Hybernate framework 
 *
 * @data_provider 	http://freegeoip.net/static/index.html
 * @source			https://github.com/fiorix/freegeoip
 * @request_limits	1000 / per hour
 * 
 * @category   		PHP5
 * @package			{APPLICATION_CORE}::HYBERNATE::OBJECTS
 * @subpackage		GEOLOCATION
 * @author      	Avi Aialon <aviaialon@gmail.com>
 * @copyright		2012 Deviant Logic. All Rights Reserved
 * @license			http://www.deviantlogic.ca/license
 * @version			SVN: $Id$
 * @link			SVN: $HeadURL$
 * @since			12:35:53 PM
 *
 */
 
 
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::COUNTRY::COUNTRY");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::STATE::STATE");

class GEO_LOCATOR_LITE extends SHARED_OBJECT
{	
	/**
	 * These constants represent the format in which the data is returned
	 * 
	 * @access	protected
	 * @var 	string
	 */
	const GEO_LOCATOR_LITE_RETURN_FORMAT_JSON 	= 'json';
	const GEO_LOCATOR_LITE_RETURN_FORMAT_XML 	= 'xml';
	
   /**
	* This is the XML-RPC/REST service domain 
	*
	* @access	protected
	* @var 		string
	*/
	const GEO_LOCATOR_LITE_SERVICE_DOMAIN		= 'freegeoip.net';
	
  /**
	* This is the XML-RPC/REST service URL scheme (http || https)
	*
	* @access	protected
	* @var 		string
	*/
	const GEO_LOCATOR_LITE_SERVICE_URL_SCHEME  = 'http';
	
   /**
	* Processing errors
	*
	* @access	protected
	* @var	 	array
	*/
	protected static $_PROCESSING_ERRORS 		= array();
	
   /**
	* Retains the called IP address.
	*
	* @access	protected
	* @var 		string
	*/
	protected static $_CALLED_IP_ADDRESS 		= '';
	
   /**
	* Overload Method
	* Class constructor
	*
	*  @abstract 	public
	*  @param		string 	$strIpAdress The IP address
	*  @return 		GEO_LOCATOR_LITE
	*/
	public function __construct($strIpAdress = NULL)
	{
		self::$_CALLED_IP_ADDRESS = $strIpAdress;
	}
		
   /**
	* Returns class errors
	*
	*  @abstract 	public
	*  @param		none
	*  @return 		array
	*/
	public function getErrors()
	{
		return (self::$_PROCESSING_ERRORS);
	}		
  
   /**
	* Sets class errors
	*
	*  @abstract 	public
	*  @param		string $strError The error text
	*  @return 		void
	*/
	public function setError($strError = NULL)
	{
		if (FALSE === is_null($strError))
		{
			self::$_PROCESSING_ERRORS[] = $strError;
		}
	}		
	
  /**
	* Gets member data from a passed object
	*
	*  @abstract 	protected
	*  @param		string 	$strDataVarName the data variable name
	*  @param		object 	$objTarget 		the target object
	*  @return 		string
	*/
	protected function getReturnVariable($strDataVarName = NULL, $objTarget = NULL) 
	{
		$strReturn = false;
		if (
			(FALSE === is_null($strDataVarName)) &&
			(TRUE  === is_object($objTarget)) 
		) {
			$strReturn = ((TRUE === isset($objTarget->$strDataVarName)) ? $objTarget->$strDataVarName : false);
		}
		
		return ($strReturn);
	}
	
   /**
	* Overload Method
	* Called before the getInstance method is called
	*
	*  @abstract 	protected
	*  @see			SHARED_OBJECT
	*  @return 		void
	*/
	protected function onBefore_getInstance() 
	{
		// Set the current construct integrity to 'soft'. 
		// (it will not throw an error if no instance is returned from __CLASS__::getInstance();
		$this->setConstructIntegrity(parent::SHARED_OBJECT_SOFT_INSTANCE);
		
		// We map the current class to geo_locator (for database updates because the geo_locator_lite table doesnt exist)
		$this->setDatabaseTargetClass('GEO_LOCATOR');
	}
	
   /**
	* Protected Method
	* Returns the called IP address
	*
	*  @abstract 	protected
	*  @see			SHARED_OBJECT
	*  @return 		void
	*/
   /**
	* Overload Method
	* Called after the getInstance method is called
	*
	*  @abstract 	protected
	*  @see			SHARED_OBJECT
	*  @return 		void
	*/
	protected function on_getInstance() 
	{
		SITE_EXCEPTION::supressException();
		
		$arrInstanceKeys 	= $this->getInstanceKeyArray();
		$arrRequestData		= array();
		$arrErrors			= array();
		
		if (
			(FALSE 	=== empty($arrInstanceKeys)) 		&&
			(TRUE 	=== isset($arrInstanceKeys['id'])) 	&&
			(FALSE 	=== empty($arrInstanceKeys['id'])) 	&&
			(FALSE 	=== ((bool) $this->getId()))
		) {
			$strIpAddress = $arrInstanceKeys['id'];
			
			if (FALSE === VALIDATOR::ipAddress($strIpAddress)) 
			{
				$this->setError('Please provide a valid ip address.');	
			}
			
			$arrErrors = $this->getErrors();
			if (TRUE === empty($arrErrors))
			{
				$strRequestUrl  = GEO_LOCATOR_LITE::GEO_LOCATOR_LITE_SERVICE_URL_SCHEME . '://';
				$strRequestUrl .= GEO_LOCATOR_LITE::GEO_LOCATOR_LITE_SERVICE_DOMAIN . '/';
				$strRequestUrl .= self::GEO_LOCATOR_LITE_RETURN_FORMAT_JSON . '/';
				$strRequestUrl .= trim($strIpAddress);
				$strDataIO_in 	= @file_get_contents($strRequestUrl);	
				$objRequestData = json_decode($strDataIO_in);
	
	
				if (TRUE === is_object($objRequestData))
				{
					$this->setId(trim($strIpAddress));
					$objDb = DATABASE::getInstance();
					$objDb->query(
						"INSERT INTO " . strtolower($this->class_name) . " (id) VALUES ('" . $objDb->escape($this->getId()) . "') " . 
						"ON DUPLICATE KEY UPDATE id = '" . $objDb->escape($this->getId()) . "'"			  
					);
				
					// Gather country and state info if available
					$objCountry = COUNTRY::getInstanceFromKey(array(
						'country_name' => $this->getReturnVariable('country_name', $objRequestData)
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
	
					$objState = STATE::getInstanceFromKey(array(
						'state_name' => $this->getReturnVariable('region_name', $objRequestData)
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// Set variables
					$this->setCountryId($objCountry->getId());
					$this->setStateId($objState->getId());
					
					foreach (array(
  					 /* Returned Fieds 		Database Fields */
						'country_code' 	=> 	'CountryCode',
						'country_name'	=> 	'CountryName',
						'region_code'	=> 	'RegionCode',
						'region_name'	=> 	'RegionName',
						'city'			=> 	'City',
						'zipcode'		=> 	'ZipPostalCode',
						'latitude'		=> 	'Latitude',
						'longitude'		=> 	'Longitude',
						'metrocode'		=> 	'metroCode'
					) as $strReturnedField => $strDatabaseMap) 
					{
						if ($this->getReturnVariable($strReturnedField, $objRequestData))	{
							$this->setVariable($strDatabaseMap, $this->getReturnVariable($strReturnedField, $objRequestData));
						}
					}
					
					$this->save(); 
				}
			}
			
			SITE_EXCEPTION::clearExceptionSupress();
		}
	}
	
	/**
	 * Abstraction method. 
	 * Returns the current class's class path
	 * 
	 * @access 	protected
	 * @see		SHARED_OBJECT
	 * @return	string
	 */
	protected function getClassPath()  	 
	{
		return (__FILE__);
	}
}