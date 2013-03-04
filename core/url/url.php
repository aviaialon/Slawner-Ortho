<?php
define('SCHEME_SAFEMODE_HTTP', false);
define('SCHEME_HTTP', 'HTTP');
define('SCHEME_HTTPS', 'HTTPS');
define('SCHEME_FTP', 'FTP');
define('SCHEME_MAILTO', 'MAILTO');
define('SCHEME_XML', 'XML');
define('SCHEME_JAVASCRIPT', 'JAVASCRIPT');


// URL Elements {{{

define('URL_SCHEME', 1);
define('URL_HOST', 2);
define('URL_PORT', 4);
define('URL_URI', 8);
define('URL_QUERY', 16);

define('URL_SERVER', 2+1); 				// Server location				Ex: http://www.host.com:80
define('URL_SERVERPATH', 8+2+1);		// Server location + path		Ex: http://www.host.com:80/path/file.php
define('URL_ABSOLUTE', 16+8+4+2+1); 	// Full resource location 		Ex: http://www.host.com:80/path/file.php?id=4&m=2268

define('URL_DEFAULT', 16+8);

// }}}

// URL Options {{{

define('URL_CURRENT_SCHEME', 1);
define('URL_CURRENT_HOST', 2);
define('URL_CURRENT_PATH', 4);
define('URL_CURRENT_PORT', 8);
define('URL_CURRENT_ATTRIBUTE', 16);

define('URL_CURRENT', 16+8+4+2+1);

define('URL_FORCE_PORT', 32);
define('URL_SESSION', 64);

define('URL_DEFAULT_OPTION', URL_CURRENT | URL_SESSION);

// }}}


/**
 * This class represent an internet resource location
 * @package DHL
 */
class URL {

	var $arrAttribute = array(), $strScheme, $strHost, $intPort, $strPath;
	var $strUserName, $strPassword;
	var $arrTempAttribute = array();
	var $strFragment;
	var $blnAttribute;
	var $blnUrlEncodeFragment = true;
	var $blnIsFriendlyUrl = false; // If set to true, URL params are created as http://.../var:value/var:value

	/**
	 * STATIC Get current url.
	 * @param string strUrl if a path or param are specified.
	 * @return object URL
	 */
	public static function getCurrent($strUrl = NULL) {
		$objUrl = new URL($strUrl);
		return $objUrl;
	}

	/**
	 * STATIC Return current absolut url.
	 * @param integer intElement [URL_SCHEME|URL_HOST|..]
	 * @return object URL
	 */
	public static function create(
		$strUrl = '',
		$intElement = URL_DEFAULT,
		$intOption = URL_DEFAULT_OPTION
	) {

		$arrCurrentUrl = array();
		$arrCurrentUrlAttribute = array();
		$arrSpecifiedUrlAttribute = array();
		$arrUrl = array();

		$arrCurrentUrl = array(
			'scheme' => ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http'),
			'query' => $_SERVER['QUERY_STRING'],
			'host' => $_SERVER['HTTP_HOST'],
			'port' => $_SERVER['SERVER_PORT'],
			'path' => $_SERVER['SCRIPT_NAME']
		);

		if ($intOption & URL_CURRENT_HOST) {
			$arrUrl['query'] = $_SERVER['QUERY_STRING'];
		}

		if ($intOption & URL_CURRENT_HOST) {
			$arrUrl['host'] = $_SERVER['HTTP_HOST'];
		};

		if ($intOption & URL_CURRENT_PORT) {
			$arrUrl['port'] = $_SERVER['SERVER_PORT'];
		};

		if ($intOption & URL_CURRENT_SCHEME) {
			$arrUrl['scheme'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
		}

		if ($intOption & URL_CURRENT_HOST) {
			$arrUrl['host'] = $_SERVER['HTTP_HOST'];
		}

		if ($intOption & URL_CURRENT_PATH) {
			$arrUrl['path'] = $_SERVER['SCRIPT_NAME'];
		}

		if ($intOption & URL_CURRENT_ATTRIBUTE) {
			$arrUrlAttribute = array_merge((array)$_GET, (array)$arrSpecifiedUrlAttribute);
		}

		$arrSpecifiedUrl = parse_url($strUrl);
		if ($arrSpecifiedUrl['path'] == '') unset($arrSpecifiedUrl['path']);

		if (
			isset($arrSpecifiedUrl['scheme']) &&
			$arrSpecifiedUrl['scheme'] != 'http' &&
			$arrSpecifiedUrl['scheme'] != 'https' &&
			$arrSpecifiedUrl['scheme'] != ''
		) {
			$arrUrl = array();
		}

		if ($arrSpecifiedUrl) {
			$arrUrl = array_merge((array)$arrUrl, (array)$arrSpecifiedUrl);
			parse_str((isset($arrSpecifiedUrl['query']) ? $arrSpecifiedUrl['query'] : ""), $arrSpecifiedUrlAttribute);
			if ($arrSpecifiedUrlAttribute) {
				$arrUrlAttribute = array_merge((array)$arrUrlAttribute, (array)$arrSpecifiedUrlAttribute);
			}
		}

		if (
			isset($arrSpecifiedUrl['host']) &&
			$arrSpecifiedUrl['host'] &&
			$arrSpecifiedUrl['host'] != $arrCurrentUrl['host']
		) {
				$arrUrlAttribute = $arrSpecifiedUrlAttribute;
		}

		if (isset($arrUrl['scheme']) && ($arrUrl['scheme'] == '' || $arrUrl['scheme'] == 'http' || $arrUrl['scheme'] == 'https')) {
			$arrPath = explode('/', $_SERVER['SCRIPT_NAME']);
			$arrNewPath = explode('/', $arrUrl['path']);

			array_pop($arrPath);
			foreach($arrNewPath as $intIndex => $strEntry) {
				if ($strEntry == '.') {

				} elseif ($strEntry == '' && $intIndex == 0) {
					$arrPath = array('');
				} elseif ($strEntry == '..') {
					if (count($arrPath) > 1) array_pop($arrPath);
				} else {
					$arrPath[] = $strEntry;
				}
			}

			$arrUrl['path'] = implode('/', $arrPath);
		}

		if ((
			array_key_exists('host', $arrSpecifiedUrl) ||
			(
			 	isset($arrCurrentUrl['host']) 	&&
				isset($arrSpecifiedUrl['host'])	&&
			 	$arrSpecifiedUrl['host'] == $arrCurrentUrl['host']
			)
		) && ($intOption & URL_SESSION)) {
			if (session_id() && !isset($_COOKIE[session_name()]) && !is_null(session_id())) {
				$strNewSession = ereg_replace("[^[:alnum:]:]","", session_id());
				$arrUrlAttribute[session_name()] = $strNewSession;
			}
		}

		if ($intOption & URL_SESSION) {
			if (isset($_COOKIE[session_name()])
				&& session_id() == $_COOKIE[session_name()]
			) {
				unset($arrUrlAttribute[session_name()]);
			}

			if (
				isset($arrSpecifiedUrl['host']) &&
				$arrSpecifiedUrl['host'] &&
				$arrSpecifiedUrl['host'] != $arrCurrentUrl['host']
			) {
				unset($arrUrlAttribute[session_name()]);
			}
		}

		if (
			($intOption & URL_FORCE_PORT) == false &&
			(isset($arrUrl['scheme']) && $arrUrl['scheme'] == 'https') &&
			(isset($arrUrl['port']) && $arrUrl['port'] == 443)
		) {
			$arrUrl['port'] = '';
		}

		if (($intOption & URL_FORCE_PORT) == false &&
			(isset($arrUrl['scheme']) && $arrUrl['scheme'] == 'http') &&
			(isset($arrUrl['port']) && $arrUrl['port'] == 80)
		) {
			$arrUrl['port'] = '';
		}

		$objUrl = new URL('', false, false);
		$objUrl->blnAttribute = true;

		if (isset($arrUrl['scheme']) && $arrUrl['scheme'] == 'javascript') {
			$intElement |= URL_SCHEME;
			$intElement |= URL_SCHEME;
			$intElement = $intElement ^ (URL_HOST | URL_PORT | URL_QUERY);
		}

		($intElement & URL_SCHEME) && $objUrl->strScheme = strtoupper($arrUrl['scheme']);
		($intElement & URL_HOST) && $objUrl->strHost = $arrUrl['host'];
		($intElement & URL_PORT) && $objUrl->intPort = $arrUrl['port'];
		($intElement & URL_URI) && $objUrl->strPath = $arrUrl['path'];
		($intElement & URL_QUERY) && $objUrl->arrAttribute = $arrUrlAttribute;

		return $objUrl;
	}

	/**
	 * Constructor
	 * @param string $attstrUrl
	 * @param boolean $attblnCurrent
	 * @param boolean $blnParse
	 */
	function URL($attstrUrl = NULL, $attblnCurrent = true, $blnParse = true) {
		if ($blnParse == false) {
			return ;
		}

		$this->blnAttribute = true;

		$arrUrl = parse_url($attstrUrl); 
		if (isset($arrUrl['query'])) {
			$tmp = parse_str($arrUrl['query'], $this->arrAttribute);
		}

		if(isset($arrUrl['scheme'])) {
			$this->strScheme = strtoupper($arrUrl['scheme']);
			if($this->strScheme == SCHEME_JAVASCRIPT)
				$this->blnAttribute = false;
		}
		if (isset($arrUrl['host'])) {
			$this->strHost = $arrUrl['host'];
		}
		if (isset($arrUrl['port'])) {
			$this->intPort = $arrUrl['port'];
		}
		if (isset($arrUrl['user'])) {
			$this->strUserName = $arrUrl['user'];
		}
		if (isset($arrUrl['pass'])) {
			$this->strPassword = $arrUrl['pass'];
		}
		if (isset($arrUrl['fragment'])) {
			$this->strFragment = $arrUrl['fragment'];
		}

		if (!$this->strHost || $this->strHost == $_SERVER['HTTP_HOST']) {
			if (session_id() && !isset($_COOKIE[session_name()]) && !is_null(session_id())) {
				$strNewSession = preg_replace("/[^[:alnum:]:]/","", session_id());
				if($strNewSession != session_id()) {
					mail(__ADMIN_EMAIL__, 'Session Change', 'Session will be change from : ' . session_id() . ' to : ' . $strNewSession . print_r($_SERVER, true));
				}
				$this->arrAttribute[session_name()] = $strNewSession;
			}
		}

		if ((!isset($arrUrl['path']) || !$arrUrl['path']) && ((isset($_SERVER['HTTP_HOST']) && $this->strHost == $_SERVER['HTTP_HOST']) || !$this->strHost) && $attblnCurrent) {
			if (true === $this->blnIsFriendlyUrl) {
				$this->strPath = $attstrUrl;
			} else {
				$this->strPath = $_SERVER['SCRIPT_NAME'];	
			}
		} elseif ($this->strScheme == SCHEME_HTTP || $this->strScheme == SCHEME_HTTPS || $this->strScheme == '') {

			$arrPath = explode('/', $_SERVER['SCRIPT_NAME']);
			$arrNewPath = array('');
			if (array_key_exists('path', $arrUrl)) {
				$arrNewPath = explode('/', $arrUrl['path']);
			}

			array_pop($arrPath);
			foreach($arrNewPath as $intIndex => $strEntry) {
				if ($strEntry == '.') {

				} elseif ($strEntry == '' && $intIndex == 0) {
					$arrPath = array('');
				} elseif ($strEntry == '..') {
					if (count($arrPath) > 1) array_pop($arrPath);
				} else {
					$arrPath[] = $strEntry;
				}
			}

			$this->strPath = implode('/', $arrPath);
		} else {
			$this->strPath = $arrUrl['path'];
		}

		if (
			$this->strScheme == SCHEME_HTTP ||
			$this->strScheme == SCHEME_HTTPS |
			!isset($arrUrl['scheme'])
		) {
			if (!$this->strHost || $this->strHost == $_SERVER['HTTP_HOST']) {
				if ($attblnCurrent) {
					$this->arrAttribute = array_merge(
						(array) (isset($_GET) ? $_GET : array()), 
						(array) (sizeof($this->arrAttribute) ? $this->arrAttribute : array())
					);
				}
			}
		}

		$this->addSession();
	}

	/**
	 * Add the session to the url if it is not set
	 * @param void
	 * @return void
	 */
	function addSession() {
		if (isset($_COOKIE[session_name()])
			&& session_id() == $_COOKIE[session_name()]
		) {
			unset($this->arrAttribute[session_name()]);
		}
		// Rewrite PHPSESSID value when exists to make sure it is current session id
		if (session_id() && array_key_exists(session_name(), $this->arrAttribute)) {
			$this->arrAttribute[session_name()] = session_id();
		}
	}

	function setScheme($attstrScheme) {
		$this->strScheme = $attstrScheme;
	}

	function getScheme() {
		return $this->strScheme;
	}

	function setUserName($attstrUserName) {
		$this->strUserName = $attstrUserName;
	}

	function setPassword($attstrPassword) {
		$this->strPassword = $attstrPassword;
	}

	function setPort($atttintPort) {
		$this->intPort = $atttintPort;
	}
	
	function setIsFriendlyUrl($blnIsFriendly) {
		$this->blnIsFriendlyUrl = (bool) $blnIsFriendly;
	}
	
	function getPort() {

		if($this->intPort) {
			return $this->intPort;
		}

		switch($this->strScheme) {
			case SCHEME_HTTPS:
				$intPort = 443;
			break;
			case SCHEME_FTP:
				$intPort = 21;
			break;
			default :
				$intPort = 80;
		}

		return $intPort;
	}

	function setAttributeFromArray($arr) {
		$this->arrAttribute = array_merge((array)$this->arrAttribute, (array)$arr);
	}

	function setAttribute($attstrName, $attstrValue = NULL) {
		$this->arrAttribute[$attstrName] = $attstrValue;
	}

	function getAttribute($strName) {
		if (array_key_exists($strName, $this->arrAttribute)) {
			return $this->arrAttribute[$strName];
		}
	}

	function deleteAttribute($strName) {
		if (array_key_exists($strName, $this->arrAttribute)) {
			unset($this->arrAttribute[$strName]);
		}
	}

	function setFragment($strName) {
		$this->strFragment = $strName;
	}

	function getFragment() {
		return $this->strFragment;
	}
	
	function urlEncodeFragment($blnEncode) {
		$this->blnUrlEncodeFragment = $blnEncode;
	}

	function setHost($attstrHost) {
		$this->strHost = $attstrHost;
	}

	function getHost() {
		return $this->strHost;
	}

	function removeHost() {
		$this->strHost = '';
		$this->strScheme = '';
	}
	
	function forcePath($strPath) {
		$this->strPath = $strPath;
	}

	function setPath($strPath) {
		$arrPath = explode('/', $this->strPath);
		$arrNewPath = explode('/', $strPath);
		// $this->blnIsFriendlyUrl
		array_pop($arrPath);
		foreach($arrNewPath as $intIndex => $strEntry) {
			if ($strEntry == '.') {

			} elseif ($strEntry == '' && $intIndex == 0) {
				$arrPath = array('');
			} elseif ($strEntry == '..') {
				if (count($arrPath) > 1) array_pop($arrPath);
			} else {
				$arrPath[] = $strEntry;
			}
		}

		$this->strPath = implode('/', $arrPath);
	}

	function getPath() {
		return $this->strPath;
	}
	
	function getIsFriendlyUrl() {
		return ((bool) $this->blnIsFriendlyUrl);
	}
	
	/**
	 * Adds PHPSESSID to the URL
	 * @return void
	 */
	function addSessionAttributes() {
		$this->setAttribute(session_name(), session_id());
	}

	/**
	 * Return the current attribute of the url
	 * @return array
	 */
	function getArrAttribute() {
		return $this->arrAttribute;
	}

	/**
	 * Set the attribute of the url
	 * @param array
	 */
	function setArrAttribute($arrAttribute) {
		if(is_array($this->arrAttribute)) {
			$this->arrAttribute = array_merge((array)$this->arrAttribute, (array)$arrAttribute);
		}
		else {
			$this->arrAttribute = $arrAttribute;
		}
	}

	/**
	  * Clear all the attribute of the url
	  * @return boolean
	  */
	function clearAttribute() {
		$blnAddSession = false;
		if(array_key_exists(session_name(), $this->arrAttribute) && session_id()) {
			$blnAddSession = true;
		}
		$this->arrAttribute = array();
		if($blnAddSession) {
			$this->arrAttribute[session_name()] = session_id();
		}
		return true;
	}

	function clearAll() {
		$this->arrAttribute = array();
		$this->strScheme = '';
		$this->strHost = '';
		$this->intPort = 0;
		$this->strPath = '';
		$this->strUserName = '';
		$this->strPassword = '';
	}

	function build() {
		$strSheme = '';
		$strPort = '';
		$strAttribute = '';
		$strUserPass = '';
		$strFragment = '';

		if ($this->strUserName)
			$strUserPass = "{$this->strUserName}:{$this->strPassword}@";

		$strUrlEncode = 'urlencode';
		switch ($this->strScheme) {
			case SCHEME_HTTP:
				$strSheme = 'http://';
			break;
			case SCHEME_HTTPS:
				$strSheme = 'https://';
			break;
			case SCHEME_FTP:
				$strSheme = 'ftp://';
			break;
			case SCHEME_MAILTO:
				$strSheme = 'mailto:';
				$strUrlEncode = 'rawurlencode';
			break;
			case SCHEME_JAVASCRIPT:
				$strSheme = 'javascript:';
			break;
			case SCHEME_XML:
				$strSheme = 'xml://';
			break;
		}

		if ($this->intPort) {
			$strPort = ":{$this->intPort}";
		}

		//Check if we have a variable defined in the environment that tell us to remove DirectoryIndex
		if(
			getenv('URL_REMOVEDIRECTORYINDEX') == 1 &&
			in_array(basename($this->strPath), array('index.htm','index.html','index.php'))
		) {
			$this->strPath = substr($this->strPath, 0, strrpos($this->strPath, basename($this->strPath)));
			//Verify if all the url is empty, if so set the path to ./
			if(
				$this->strPath == '' &&
				$strScheme == '' &&
				$this->strHost == ''
			) {
				$this->strPath = './';
			}
		}

		if (is_array($this->arrAttribute) && $this->blnAttribute) {
			foreach ($this->arrAttribute as $strKey => $strValue) {
				if ($this->getIsFriendlyUrl()) 
				{
					$strAttribute .= "/" . $strUrlEncode($strKey) . ":" . $strUrlEncode($strValue);
				}
				else 
				{
					$strAttribute .= ($strAttribute ? "&" : "") . $strUrlEncode($strKey) . "=" . $strUrlEncode($strValue);
				}				
			}
		}

		if ($strAttribute && (FALSE === $this->getIsFriendlyUrl())) {
			$strAttribute = '?' . $strAttribute;
		}

		if ($this->strFragment) {
			$strFragment = "#" . ($this->blnUrlEncodeFragment? $strUrlEncode($this->strFragment): $this->strFragment);
		}

		// Fix of FunWebProducts url. Contain lot of backslashes
		$strAttribute = str_replace("%5C%5C%5C", "", $strAttribute);

		$strUrl = "{$strUserPass}{$strSheme}{$this->strHost}{$strPort}";
		$strMainPath = str_replace("//", "/", "{$this->strPath}{$strAttribute}{$strFragment}");
		$strUrl .= $strMainPath;
		
		static $blnSent = false;

		if (
			$blnSent == false &&
			strlen($strUrl) > 2048 &&
			$this->strScheme != 'JAVASCRIPT'
		) {
			mail(
				__ADMIN_EMAIL__, "URL Big url",
				print_r($this, true) .
				print_r($_SERVER, true)
			);
			$blnSent = true;
		}

		return $strUrl;
	}
	
	
	function cloneObject() {
		$strReturn = serialize($this);
		
		return unserialize($strReturn);
	}
	
	
	/**
	 * This method will return the current URL, removing unwanted URL Query String parameters
	 * @return String - The current URL
	 */ 
	public static function getCurrentUrl($blnAddQueryString=true) {
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$currentUrl = $protocol. (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		$parts = parse_url($currentUrl);
		
		// drop unwanted url params
		$query = '';
		if (!empty($parts['query']) && $blnAddQueryString) {
			$params = array();
			parse_str($parts['query'], $params);
			$DROP_QUERY_PARAMS = array('session', 'logout');
	
			foreach($DROP_QUERY_PARAMS as $key) {
				unset($params[$key]);
			}
			if (!empty($params)) {
				$query = '?'.http_build_query($params);
			}
		}
	
		// use port if non default
		$port =
		isset($parts['port']) && (
			($protocol === 'http://' && 
			 $parts['port'] !== 80) || 
			($protocol === 'https://' && $parts['port'] !== 443)) ? ':'.$parts['port'] : '';
		// rebuild
		return ($protocol.$parts['host'].$port.$parts['path'].$query);
	} 
	
	/*
	public static function getCurrentUrl() {
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
		  ? 'https://'
		  : 'http://';
		$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);
	
		// drop unwanted url params
		$query = '';
		if (!empty($parts['query'])) {
		  $params = array();
		  parse_str($parts['query'], $params);
		$DROP_QUERY_PARAMS = array(
		'session',
		'logout'
		);
		  
		  foreach(self::$DROP_QUERY_PARAMS as $key) {
			unset($params[$key]);
		  }
		  if (!empty($params)) {
			$query = '?' . http_build_query($params);
		  }
		}
	
		// use port if non default
		$port =
		  isset($parts['port']) &&
		  (($protocol === 'http://' && $parts['port'] !== 80) ||
		   ($protocol === 'https://' && $parts['port'] !== 443))
		  ? ':' . $parts['port'] : '';
	
		// rebuild
		return ($protocol . $parts['host'] . $port . $parts['path'] . $query);
  }
  	*/
   public static function redirect($strURL=NULL) {
		$strRedirectURL = ($strURL ? $strURL : __ROOT_URL__);
		if (! headers_sent())
			header('Location: ' . $strRedirectURL);
		else {
			print "<html><head>";
			print "<meta http-equiv='refresh' content='1;URL={$strRedirectURL}'>";
			print "</head>";
			print "<body>";
			print "You will be redirected, please wait. <br />";
			print "Please <a href='{$strRedirectURL}'>click here</a> in you are not redirected in 3 seconds.";
			print "</body></html>";
		}
		die;
	}
	
	/**
	 * This method will redirect a url object
	 */
	public function forward()
	{
		$this->redirect($this->build());		
	}

	/**
	 * This method returns the a canonical URL, if no URL is provided, the current one is used
	 *
	 * @access	public, static
	 * @param	string	$strUrl - A URL to extract the canonical URL
	 * @param	boolean	$blnRemoveLang - Remove the language attribute
	 * @param	boolean	$blnAddQueryString - Keep the query string
	 * @param	boolean	$blnIsFriendlyUrl  - Tells the system if its a friendly URL, if so, it'll parse the get params like /param:value/ in the URL
	 * @return 	string
	 */
	public static function getCanonicalUrl($strUrl = NULL, $blnRemoveLang = false, $blnAddQueryString = true, $blnIsFriendlyUrl = false, $arrRemoveUrlParams = array(), $blnAddHost = false)
	{
		$strTmpUrl = (false === is_null($strUrl) ? $strUrl : $_SERVER['REQUEST_URI']);
		if (true === is_null($strTmpUrl)) 
		{
			// This will return the correct current path when called by request dispacther..
			$objCurrentUrl 	= new URL(URL::getCurrentUrl());
			$strTmpUrl	 	= PAGE_META::getFileRealPath($objCurrentUrl->getPath());
		}
		
		$arrCurrentUrl 	= parse_url($strTmpUrl);
		if (false === $arrCurrentUrl) {
			$arrCurrentUrl = parse_url(constant('__SITE_URL__') . '/' . $strTmpUrl);	
		}

		$strUrl 		= str_replace('//', '/', $arrCurrentUrl['path']);
		$arrRemoveUrlParams = array_flip($arrRemoveUrlParams);
		
		// Here, we remove the language attribute from the URL. both from the query string
		// and from the actual URL path, ex: /en/test/
		if (true === ($blnRemoveLang)) 
		{
			$arrUrlParts = explode('/', $arrCurrentUrl['path']);	
			foreach ($arrUrlParts as $intIndex => $strValue) {
				if ($strValue == '') { unset($arrUrlParts[$intIndex]); }	
			}
			
			$arrFinalUrlParts = array();
			foreach ($arrUrlParts as $intIndex => $strValue) {
				$arrFinalUrlParts[] = $strValue;
			}
			
			if (
				(false === empty($arrFinalUrlParts)) &&
				(true === (strlen($arrFinalUrlParts[0]) == 2)) &&
				(true === in_array($arrFinalUrlParts[0], array('en', 'fr')))
			) {
				unset($arrFinalUrlParts[0]);
				// add an empty value if the array is empty like in the homepage
				// or we wont have a URL anymore.
				if (true === empty($arrFinalUrlParts)) {
					$arrFinalUrlParts[0] = '/';	
				}
				$strUrl = ('/' . implode('/', $arrFinalUrlParts));
				$strUrl = (str_replace('//', '/', $strUrl));
			}
		}
		
		// Here, we test for friendly urls and remove the parts that need to be added to the query string.
		// ex: /path/to/controller/param1:test/param2:another-test/ will become /path/to/controller/?param1=test&param2=another-test
		if (true === ($blnIsFriendlyUrl))
		{
			$arrFriendlyUrl = explode('/', $strUrl);
			/**
			 * Uncomment this part is the friendly URL could contain more than 2 key parts 
			 * before the query string. without it, friendly URLS are assumed to be
			 * /model/controller/param/param/
			 *
			foreach ($arrFriendlyUrl as $intIndex => $strUrlkey) {
				if (
					(false === empty($strUrlkey)) &&
					(! (false === (strpos($strUrlkey, ':'))))
				) {
					if (false === isset($arrCurrentUrl['query'])) {
						$arrCurrentUrl['query'] = '';	
					}
					$arrUrlData = explode(':', $strUrlkey);
					$arrCurrentUrl['query'] .= (
						((strlen($arrCurrentUrl['query']) > 0) ? '&' : '') . $arrUrlData[0] . '=' . $arrUrlData[1]
					);
					$strUrl = str_replace($strUrlkey, '', $strUrl);
					$strUrl = str_replace('//', '/', $strUrl);
				}
			}
			*/
			
			if (false === empty($arrFriendlyUrl)) 
			{
				if (! strlen(trim($arrFriendlyUrl[0])))	{
					unset($arrFriendlyUrl[0]);
				}
				
				// 1. Set the new path...
				// Kill the first part if were in the index home page...
				// this will avoid adding unecessary double indexes for URLs such
				// as: /en/index/index which are supposed to mean 'index controller' but with an 'index' url param
				// so if we dont remove it, later when the directory index is removed, the URL will still contain an 'index'
				// and it WONT contain an 'index' URL param.
				$arrFriendlyUrl = array_values($arrFriendlyUrl); // Reset the array index keys..
				$intArraySpliceLength = 2; 
				if (true === (in_array(basename($arrFriendlyUrl[0]), array('index.htm','index.html','index.php', 'index')))) {
					$intArraySpliceLength = 1; 
				}
				$strUrl = ('/' . implode('/', array_splice($arrFriendlyUrl, 0, $intArraySpliceLength)));
				$strUrl = (str_replace('//', '/', $strUrl));
				
				// 2. now set the URL params. everything else is assumed to be URL param.
				foreach ($arrFriendlyUrl as $intIndex => $strUrlkey) 
				{
					if (false === isset($arrCurrentUrl['query'])) 
					{
						$arrCurrentUrl['query'] = '';	
					}
					
					$arrUrlData = explode(':', $strUrlkey);
					$arrCurrentUrl['query'] .= (
						((strlen($arrCurrentUrl['query']) > 0) ? '&' : '') . array_shift($arrUrlData) . '=' . array_shift($arrUrlData)
					);
				}
			}
			
			// 3. Remove the directory index
			$strUrl = trim($strUrl);
			if (true === (in_array(basename($strUrl), array('index.htm','index.html','index.php', 'index'))))
			{
				$strUrl = substr($strUrl, 0, strrpos($strUrl, basename($strUrl)));
				$strUrl = str_replace('//', '/', $strUrl);
				if (true === empty($strUrl))
				{
					$strUrl = '/';	
				}
			}
			
			// Parse again for friendly URL parameters
			$arrFriendlyUrlParams = explode('/', $strUrl);
			foreach ($arrFriendlyUrlParams as $intIndex => $strUrlParamKey) {
				if (
					(false === empty($strUrlParamKey)) &&
					(false !== strpos($strUrlParamKey, ':'))
				) {
					$arrFriendlyUrlParamKey = explode(':', $strUrlParamKey);
					$arrCurrentUrl['query'] .= (false === empty($arrCurrentUrl['query']) ? '&' : '?');
					$arrCurrentUrl['query'] .= array_shift($arrFriendlyUrlParamKey) . '=' . array_shift($arrFriendlyUrlParamKey);
					unset($arrFriendlyUrlParams[$intIndex]);
				}
			}
			
			$strUrl = '/' . implode('/', $arrFriendlyUrlParams);
			$strUrl = str_replace('//', '/', $strUrl);
		}
		
		// And finally, we add the query string to the canonical URL.
		/*
		$strReturnUrl = $strUrl;
		if (
			(true  === ($blnAddQueryString)) &&
			(false === (empty($arrCurrentUrl['query'])))
		) {
			$arrQueryString = array();
			foreach (explode('&', $arrCurrentUrl['query']) as $strIndex => $mxUrlQueryValue) {
				$arrQueryIndex 		= explode(':', $mxUrlQueryValue);
				$arrQueryParts		= explode('=', array_shift($arrQueryIndex));
				$strIndex 			= trim(array_shift($arrQueryParts));
				$mxUrlQueryValue 	= trim(array_shift($arrQueryParts));
				if (
					(true === ($blnRemoveLang)) &&
					(strtolower($strIndex) == 'lang')
				) {
					continue;	
				}
				$arrQueryString[] = $strIndex . '=' . $mxUrlQueryValue;
			}
			$strReturnUrl .= '?' . 	implode('&', $arrQueryString);
		}
		*/
		$strReturnUrl = $strUrl;
		
		if (false === empty($arrRemoveUrlParams))
		{
			$arrnewQueryString = array();
			$arrQueryString = explode('&', $arrCurrentUrl['query']);
			
			foreach($arrQueryString as $strUrlKey => $strUrlValue) {
				$arrKeyValuePairs = explode('=', $strUrlValue);
				if (false === array_key_exists($arrKeyValuePairs[0], $arrRemoveUrlParams)) {
					$arrnewQueryString[] = array_shift($arrKeyValuePairs) . '=' . array_shift($arrKeyValuePairs);
				}
			}
			$arrCurrentUrl['query'] = implode('&', $arrnewQueryString);
			
			foreach($arrRemoveUrlParams as $strUrlKey => $mxValue) {
				if (false === empty($strUrlKey)) {
					$strReturnUrl = preg_replace('/\/(' . strtolower($strUrlKey) . '):(\w*)/', "", strtolower($strReturnUrl));
				}
			}
		}
		
		
		if (
			(true  === ($blnAddQueryString)) &&
			(false === (empty($arrCurrentUrl['query'])))
		) {
			$arrQueryString = array();
			// Remove the '?' from there!
			$strQstringParse = ((substr($arrCurrentUrl['query'], 0, 1) === '?') ? (substr($arrCurrentUrl['query'], 1)) : $arrCurrentUrl['query']);
			// added array_reverse to keep the same order of the URL params
			foreach ((array_reverse(explode('&', $strQstringParse), true)) as $strIndex => $mxUrlQueryValue) {
				$arrQueryIndex 		= explode(':', $mxUrlQueryValue);
				$arrQueryParts		= explode('=', array_shift($arrQueryIndex));
				$strIndex 			= trim(array_shift($arrQueryParts));
				$mxUrlQueryValue 	= trim(array_shift($arrQueryParts));
				if (
					(true === ($blnRemoveLang)) &&
					(strtolower($strIndex) == 'lang')
				) {
					continue;	
				}
				$arrQueryString[] = $strIndex . ($blnIsFriendlyUrl ? ':' : '=') . $mxUrlQueryValue;
			}
			
			if (true === $blnIsFriendlyUrl) {
				$strReturnUrl .= '/' . implode('/', $arrQueryString);
			} else {
				$strReturnUrl .= '?' . http_build_query($arrQueryString);
			}
			//$strReturnUrl .= ((true === $blnIsFriendlyUrl) ? ('/' . implode('/', $arrQueryString)) : ('?' . implode('&', $arrQueryString)));
		}
		else if (
			(true === ($blnIsFriendlyUrl)) &&
			(false === ($blnAddQueryString))
		) {
			// Dirty trick to remove friendly URL vars
			$arrUrlParts = explode('/', $strReturnUrl);
			$arrFilteredParts = array();
			foreach ($arrUrlParts as $intIndex => $strUrlPart) {
				if (false === strpos($strUrlPart, ':')) {
					$arrFilteredParts[] = $strUrlPart;	
				}
			}
			$strReturnUrl = implode('/', $arrFilteredParts);
			
			// /\/(.+):(.+?)/
			
			/*
			$strReturnUrl = preg_replace('/(\/)(.+):(.+?)', "", $strReturnUrl);
			die('U: ' . $strReturnUrl);
			*/
		}
		
		$strReturnUrl = str_replace('//', '/', $strReturnUrl);
		$strReturnUrl = str_replace('/:', '', $strReturnUrl);
		
		if (true == $blnAddHost) {
			$strHost = $_SERVER['HTTP_HOST'];
			$strScheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
			$strReturnUrl = ($strScheme . '://' . $strHost) . $strReturnUrl;
		}
		return ($strReturnUrl);	
	}
}