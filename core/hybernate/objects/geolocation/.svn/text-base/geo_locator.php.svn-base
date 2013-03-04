<?php
/**
* IPInfoDB geolocation API class
* http://ipinfodb.com/ip_location_api.php
* Bug report : http://forum.ipinfodb.com/viewforum.php?f=7
* Updated April 17 2010
* @version 1.3
* @author Marc-Andre Caron - IPInfoDB -  http://www.ipinfodb.com
* @license http://www.gnu.org/copyleft/lesser.html LGPL
	
	_______________
	
	EXAMPLE USAGE:
	_______________
	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::GEOLOCATION::GEO_LOCATOR");
	$geolocation = new GEO_LOCATOR(GEO_LOCATOR::GEO_LOCATOR_CITY_PRESISION); 
	$geolocation->setTimeout(0);
	$geolocation->showTimezone(true);
	$geolocation->setIP($_SERVER['REMOTE_ADDR'], true);
	new dump($geolocation->getGeoLocation());
	new dump($geolocation->getErrors());
	
	or as a shared object:
	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::GEOLOCATION::GEO_LOCATOR");
	$geolocation = GEO_LOCATOR::getInstance($_SERVER['REMOTE_ADDR']);

*/

SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");

class GEO_LOCATOR extends SHARED_OBJECT {
	//------------------------------------------------------------
	// PROPERTIES
	//------------------------------------------------------------
	
	//--Protected properties--//
	
	/**
	* The main API domain
	* @var string
	* @access protected
	*/
	//protected $_apiDomain = 'ipinfodb.com';
	protected $_apiDomain = __GEO_LCATION_API_DOMAIN__;
	
	/**
	* The backup API domain
	* @var string
	* @access protected
	*/
	protected $_apiBackupDomain = 'backup.ipinfodb.com';
	
	/**
	* The backup API key
	* @var string
	* @access protected
	*/
	protected $_apiKey= __GEO_LCATION_API_KEY__;
	
	/**
	* The IP array
	* @var array
	* @access protected
	*/
	protected $_ips = array();
	
	/**
	* The errors array
	* @var array
	* @access protected
	*/
	protected $_errors = array();
	
	/**
	* The IP geolocation
	* @var array
	* @access protected
	*/
	protected $_geolocation = array();
	
	/**
	* The IP geolocation
	* @var array
	* @access protected
	*/
	protected $_cityPrecision;
	
	/**
	* If the server is on IPInfoDB whitelist or not
	* @var bool
	* @access protected
	*/
	protected $_whitelist = false;
	
	/**
	* Show timezone data or not
	* @var array
	* @access protected
	*/
	protected $_showTimezone = false;
	
	//--Constants--//
	
	const IP_QUERY = 'ip_query.php';
	const IP_QUERY_COUNTRY = 'ip_query_country.php';
	const IP_QUERY2 = 'ip_query2.php';
	const IP_QUERY2_COUNTRY = 'ip_query2_country.php';
	
	const IP_ERROR = 'is an invalid IP address  (eg : 123.123.123.123)';
	const IP_ARRAY_ERROR = 'IPs must be an array  (eg : array(123.123.123.123, 124.124.124.124)';
	const DOMAIN_ERROR = 'is an invalid domain name (eg : example.com)';
	const NONE_SPECIFIED = 'No IP or domain specified';
	const CONNECT_ERROR = 'Could not connect to API server. Will try backup server';
	const CONNECT_BACKUP_ERROR = 'Could not connect to backup API server.';
	
	const GEO_LOCATOR_CITY_PRESISION = true;
	const GEO_LOCATOR_NO_CITY_PRESISION = true;

	//------------------------------------------------------------
	// METHODS
	//------------------------------------------------------------
	
	//--Public methods--//
	/**
	* Class constructor
	* Set if the query should get city or country precision
	* @param bool $cityPrecision True for city precision, false for country precision
	* @access public
	* @return	void
	*/
	public function __construct() { }

	// --------------------------------------------------------------------

	/**
	* Set IP address
	* @param string $ip The ip address
	* @param bool $test To test if the IP is valid or not
	* @access public
	* @return	void
	*/
	public function setIP($ip, $test = false) {
		//Test IP if required
		if ($test) {
		  if (!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip)) {
			$this->_setError(new Exception($ip . ' ' . self::IP_ERROR));
			return;
		  }
		}
		$this->_ips[] = $ip;
	}

	// --------------------------------------------------------------------

	/**
	* Set multiple IP addresses
	* @param array $ips The ip address
	* @param bool $test To test if the IP is valid or not
	* @access public
	* @return	void
	*/
	public function setIPs($ips, $test = false) {
		//Make sure IP list is an array
		if (!is_array($ips)) {
		  $this->_setError(new Exception(self::IP_ARRAY_ERROR));
		  return;
		}
		
		//Test IP if required
		if ($test) {
		  foreach($ips as $ip) {
			if (!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip)) {
			  $this->_setError(new Exception($ip . ' ' . self::IP_ERROR));
			  return;
			}
		  }
		}
		
		foreach ($ips as $ip) {
		  $this->_ips[] = $ip;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Set domain
	* @param string $domain The domain name
	* @param bool $test To test if the domain is valid or not
	* @access public
	* @return	void
	*/
	public function setDomain($domain, $test = false) {
		//Test domain if required
		if ($test) {
		  if (!preg_match ("/^[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $domain)) {
			$this->_setError(new Exception($domain . ' ' . self::DOMAIN_ERROR));
			return;
		  }
		}
		
		$this->_ips[] = gethostbyname($domain);
	}
	
	// --------------------------------------------------------------------

	/**
	* Set if the query should get city or country precision
	* @param bool $cityPrecision True for city precision, false for country precision
	* @access public
	* @return	void
	*/
	public function useCityPrecision($blnUsePrecision=GEO_LOCATOR::GEO_LOCATOR_CITY_PRESISION) {
		$this->_cityPrecision = (bool) $blnUsePrecision;
	}
    
	// --------------------------------------------------------------------

	/**
	* Set file_get_contents timeout
	* @param int $timeout Timeout in seconds
	* @access public
	* @return	void
	*/
	public function setTimeout($timeout) {
		$timeout = (int)$timeout;
		if ($timeout > 0) {
		  ini_set('default_socket_timeout', $timeout);
		}
	}
    
	// --------------------------------------------------------------------

	/**
	* Set main query server to US server
	* @access public
	* @return	void
	*/
	public function useUSServer() {
	  $this->_apiBackupDomain = $this->_apiDomain;
	  $this->_apiDomain = $this->_apiDomain; //'us.ipinfodb.com';
	}

	// --------------------------------------------------------------------

	/**
	* Set if the server is on the whitelist or not
	* @param bool $enabled If the server is on the whitelist or not
	* @access public
	* @return	void
	*/
	public function setWhitelist($enabled) {
		$this->_whitelist = (bool)$enabled;
	}

	// --------------------------------------------------------------------

	/**
	* Show timezone data
	* @param bool $val Show timezone data or not
	* @access public
	* @return	void
	*/
	public function showTimezone($val) {
		$this->_showTimezone = (bool)$val;
	}

	// --------------------------------------------------------------------

	/**
	* Get geolocation as an array
	* @access public
	* @return	array
	*/
	public function getGeoLocation() {
		//Make sure IPs and/or domains are specified
		if (empty($this->_ips)) {
		  $this->_setError(new Exception(self::NONE_SPECIFIED));
		  return array();
		}
		
		//Check if ip_query.php or ip_query2.php to be used
		$singleLookup = (empty($this->_domains) && (count($this->_ips) == 1)) ? true : false;
		
		switch ($singleLookup) {
			case true:
			  //Use ip_query
			  $this->_query();
			  break;
			  
			case false:
			  //Use ip_query2 for domain or multiple lookups
			  
			  //Split IP array by 25
			  $k = 0;
			  $ipsSplit = array();
			  for ($i=0; $i<count($this->_ips); $i++) {
				if (!(($i+1) % 25)) $k++;
				$ipsSplit[$k][] = $this->_ips[$i];
			  }
			  
			  //Do multiple queries if required
			  for ($i=0;$i<count($ipsSplit);$i++) {
				//Sleep for 0.5s after each request (after 1st one) if not on the whitelist
				if (!$this->_whitelist && $i > 0) {
				  usleep(500000);
				}
				
				//Do the request
				if (count($ipsSplit[$i])) {
				  $this->_query2($ipsSplit[$i]);
				}
			  }
			
			  //Unset $ipsSplit 
			  unset($ipsSplit);
			  
			  break;
		}
		return ($this->_geolocation);
	}

	// --------------------------------------------------------------------

	/**
	* Get the errors
	* @access public
	* @return	void
	*/
	public function getErrors() {
		return $this->_errors;
	}

	// --------------------------------------------------------------------

	//--protected methods--//
	
	/**
	* Single IP query
	* @access protected
	* @return	void
	*/
	protected function _query() {
		//Select the proper API
		SITE_EXCEPTION::supressException();
		$api = $this->_cityPrecision ? self::IP_QUERY : self::IP_QUERY_COUNTRY;
		//Connect to IPInfoDB
		$showTimezone = $this->_showTimezone ? 'true' : 'false';
		if (!($d = @file_get_contents("http://" . $this->_apiDomain . "/$api?ip={$this->_ips[0]}&timezone=$showTimezone&key=" . $this->_apiKey))) {
		  $this->_setError(new Exception(self::CONNECT_ERROR));
		  //Try backup server
		  if (!($d = @file_get_contents("http://" . $this->_apiBackupDomain . "/$api?ip={$this->_ips[0]}&timezone=$showTimezone&key=" . $this->_apiKey))) {
			$this->_setError(new Exception(self::CONNECT_BACKUP_ERROR));
			return;
		  }
		}
		
		try {
		  $answer = @new SimpleXMLElement($d);
		} catch(Exception $e) {
		  $this->_setError($e);
		  return;
		}
		
		foreach($answer as $field => $val) {
		  $this->_geolocation[0][(string)$field] = (string)$val;
		}
		SITE_EXCEPTION::supressException();
	}

	// --------------------------------------------------------------------

	/**
	* Multiple IP query
	* @param array $ipsSplit The ips array (max 25)
	* @access protected
	* @return	void
	*/
	protected function _query2($ipsSplit) {
		SITE_EXCEPTION::supressException();
		//Select the proper API
		$api = $this->_cityPrecision ? self::IP_QUERY2 : self::IP_QUERY2_COUNTRY;
		
		//Separate all IPs with a comma
		$ipsCs = implode(",", $ipsSplit);
		
		//Connect to IPInfoDB
		$showTimezone = $this->_showTimezone ? 'true' : 'false';
		try {
			if (!($d = @file_get_contents("http://" . $this->_apiDomain . "/$api?ip=$ipsCs&timezone=$showTimezone"))) {
			  $this->_setError(new Exception(self::CONNECT_ERROR));
			  //Try backup server
			  if (!($d = @file_get_contents("http://" . $this->_apiBackupDomain . "/$api?ip=$ipsCs&timezone=$showTimezone"))) {
				$this->_setError(new Exception(self::CONNECT_BACKUP_ERROR));
				return;
			  }
			}
		} catch (Exception $e) {
			$this->_setError($e);
		  	return;	
		}
		
		try {
		  $answer = @new SimpleXMLElement($d);
		} catch(Exception $e) {
		  $this->_setError($e);
		  return;
		}
		
		//Add them to _geolocation
		foreach($answer->Location as $key => $ipData) {
		  foreach($ipData as $field => $val) {
			$location[(string)$field] = (string)$val;
		  }
		  $this->_geolocation[] = $location;
		  unset($location);
		}
		SITE_EXCEPTION::supressException();
	}

	// --------------------------------------------------------------------

	/**
	* Set error
	* @param string $msg The error message
	* @access protected
	* @return	void
	*/
	protected function _setError($msg) {
		$exceptionMessage = "{$msg->getMessage()} in {$msg->getFile()}({$msg->getLine()})\n";
		$exceptionTrace = "Trace : {$msg->getTraceAsString()}";
		$this->_errors[] = $exceptionMessage . $exceptionTrace;
	}
	
	// --------------------------------------------------------------------
	// --------------------------------------------------------------------
	// --------------------------------------------------------------------
	//		SHARED OBJECT ABSTRACTION METHODS
	// --------------------------------------------------------------------
	// --------------------------------------------------------------------
	// --------------------------------------------------------------------
	/**
		Abstraction Methods
	**/
	
	/*
	public static function getInstance($strIPAddress = NULL) {
		$__strObjClassName__ = __CLASS__;
		$objReturn = new $__strObjClassName__($strIPAddress);
		return ($objReturn->_getInstance($strIPAddress, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE));
	}
	*/
	protected function getClassPath()  	 { return (__FILE__); }
	
	/**
		Abstraction Callbacks
	**/
	protected function onBefore_getInstance() 
	{
		$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
		$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE);
	}

	protected function on_getInstance() 
	{
		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::COUNTRY::COUNTRY");
		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::STATE::STATE");
		
		// If the object doesnt exists, we need to create one.
		if (
			(! strlen($this->getId())) ||
			($this->getId() == 0) ||
			($this->getId() == '0')
		) {
			SITE_EXCEPTION::supressException();
			
			$this->setTimeout(5);
			$this->showTimezone(true);	
			$this->useCityPrecision(GEO_LOCATOR::GEO_LOCATOR_CITY_PRESISION);
			$this->setIP($_SERVER['REMOTE_ADDR'], true);
			
			$arrGeoLocation = $this->getGeoLocation();
			
			if (count($arrGeoLocation)) {
				$this->setVariable("id", $_SERVER['REMOTE_ADDR']); 
				$objDb = DATABASE::getInstance();
				$objDb->query(
					"INSERT INTO " . strtolower(get_class($this)) . " (id) VALUES ('" . $objDb->escape($this->getId()) . "') " . 
					"ON DUPLICATE KEY UPDATE id = '" . $objDb->escape($this->getId()) . "'"			  
				);
				// Get the country id
				$objCountry = COUNTRY::getInstanceFromKey(array(
					'country_name' => isset($arrGeoLocation[0]['CountryName']) ? $arrGeoLocation[0]['CountryName'] : 0
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);

				$objState = STATE::getInstanceFromKey(array(
					'state_name' => isset($arrGeoLocation[0]['RegionName']) ? $arrGeoLocation[0]['RegionName'] : 0
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				// Set the country and state id
				$this->setVariable('countryId', $objCountry->getId());
				$this->setVariable('stateId', $objState->getId());
				
				// We set this because, in this case the IP is used as the table ID, so it will cause an error otherwise
				// Because when we call the save() methid, it will try to update Ip = ...., we could also just unset arr['ip']
				$arrAllowedValues = array(
					2	=> 	'CountryCode',
					3	=> 	'CountryName',
					4	=> 	'RegionCode',
					5	=> 	'RegionName',
					6	=> 	'City',
					7	=> 	'ZipPostalCode',
					8	=> 	'Latitude',
					9	=> 	'Longitude',
					10	=> 	'TimezoneName',
					11	=> 	'Gmtoffset',
					12	=> 	'Isdst'
				);
				foreach($arrAllowedValues as $intIndex => $strValue) {
					if (
						(isset($arrGeoLocation[0][$strValue])) 	&&
						(strlen($arrGeoLocation[0][$strValue]))	&&
						($arrGeoLocation[0][$strValue] !== 0)	&&
						($arrGeoLocation[0][$strValue] !== '0')
					) {
						$this->setVariable($strValue, $arrGeoLocation[0][$strValue]); 	
					}
				}
				$this->save(); 
			}
			
			SITE_EXCEPTION::clearExceptionSupress();
		}
	}
}
?>