<?php

/**
 * This file defines the 'LinkedIn' class. This class is designed to be a 
 * simple, stand-alone implementation of the LinkedIn API functions.
 * 
 * COPYRIGHT:
 *   
 * Copyright (C) 2011, fiftyMission Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.  
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
 * IN THE SOFTWARE.  
 *
 * SOURCE CODE LOCATION:
 * 
 * http://code.google.com/p/simple-linkedinphp/
 *    
 * REQUIREMENTS:
 * 
 * 1. You must have cURL installed on the server and available to PHP.
 * 2. You must be running PHP 5+.  
 *  
 * QUICK START:
 * 
 * There are two files needed to enable LinkedIn API functionality from PHP; the
 * stand-alone OAuth library, and this LinkedIn class. The latest version of 
 * the stand-alone OAuth library can be found on Google Code:
 * 
 * http://code.google.com/p/oauth/
 *   
 * Install these two files on your server in a location that is accessible to 
 * the scripts you wish to use them in. Make sure to change the file 
 * permissions such that your web server can read the files.
 * 
 * Next, make sure the path to the OAuth library is correct (you can change this 
 * as needed, depending on your file organization scheme, etc).
 * 
 * Finally, test the class by attempting to connect to LinkedIn using the 
 * associated demo.php page, also located at the Google Code location
 * referenced above.                   
 *   
 * RESOURCES:
 *    
 * REST API Documentation: http://developer.linkedin.com/rest
 *    
 * @version 3.3.0 - December 10, 2011
 * @author Paul Mennega <paul@fiftymission.net>
 * @copyright Copyright 2011, fiftyMission Inc. 
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License 
 */

/**
 * Source: http://code.google.com/p/oauth/
 * 
 * Rename and move as needed, changing the require_once() call to the correct 
 * name and path.
 */
if (!extension_loaded('oauth')) {
    // the PECL OAuth extension is not present, load our third-party OAuth library
    //require_once('OAuth.php');
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::SIMPLE_OAUTH::OAUTH");		
} else {
    // the PECL extension is present, which is not compatible with this library
    throw new LinkedInException('Simple-LinkedIn: library not compatible with installed PECL OAuth extension. Please disable this extension to use the Simple-LinkedIn library.');
}

/**
 * 'LinkedInException' class declaration.
 *  
 * This class extends the base 'Exception' class.
 * 
 * @access public
 * @package classpackage
 */
class LinkedInException extends Exception
{
}


/**
 *
 * LINKEDIN_OAUTH authentication class 
 * 
 */
class OAUTH_LINKEDIN extends OBJECT_BASE_SINGLETON
{	 

	/**
	 * This is the array of requested fields when retreiving the linked in profile data
	 *
	 * @see			https://developer.linkedin.com/documents/profile-fields
	 * @relation	LinkedIn_Field	=>	SITE_USERS::DataField
	 * @var Array
	 */ 	
	 protected static $_ARR_PROFILE_REQUEST_FIELDS = array
	 (
		'id'						=>	'externalUserId',
		'first-name'				=>	'first_name',
		'last-name'					=>	'last_name', 
		'picture-url'				=>	NULL,
		'location:(name)'			=>	NULL,
		'location:(country:(code))'	=>	'countryCode',
		'summary'					=>	'comment'	
	 );
	 
	/**
	 * Main Accessor Method
	 *
	 * @param	string	$strApplicationKey 	- LinkedIn Application Key
	 * @param	string	$strApplicationKey 	- LinkedIn Application Secret Key
	 * @param	string	$strCallbackUrl 	- LinkedIn oAuth callback URL
	 * @return 	Boolean 
	 */
	public final function authenticationRequest($strApplicationKey = NULL, $strApplicationSecretKey = NULL, $strCallbackUrl = NULL)
	{
		$blnReturn 	= true;
		$objUser 	= SITE_USERS::getCurrentUser();
		 
		 // check PHP version
      	if(version_compare(PHP_VERSION, '5.0.0', '<')) 
		{
        	throw new LinkedInException('You must be running version 5.x or greater of PHP to use this library.'); 
      	} 
      	
		// check for cURL
		if(FALSE === extension_loaded('curl')) 
		{
			throw new LinkedInException('You must load the cURL extension to use this library.'); 	
		}
		
		// Api configuration arrray
		$API_CONFIG = array
		(
			'appKey'    => $strApplicationKey,
			'appSecret' => $strApplicationSecretKey
		);
  
		// Get the request
		$_REQUEST[LINKEDIN::_GET_TYPE] = (TRUE === isset($_REQUEST[LINKEDIN::_GET_TYPE])) ? $_REQUEST[LINKEDIN::_GET_TYPE] : '';
		
		// This is here just because we want to keep consitency with oAuth initiation!
		// All the oAuth requests are launched via the oAuth request parameter
		if (TRUE === isset($_GET['oAuth']))
		{
			$_REQUEST[LINKEDIN::_GET_TYPE] = 'initiate';
		}
		
		// Begin the CTA
		switch(strtolower($_REQUEST[LINKEDIN::_GET_TYPE])) 
		{
			case ('initiate') :
			{
			   /**
			   	* Handle user initiated LinkedIn connection, create the LinkedIn object.
			   	*/
				// Add the default callback URL
				$API_CONFIG['callbackUrl'] = new URL((false === (is_null($strCallbackUrl))) ? $strCallbackUrl : $objUser->getLinkedInLoginUrl());
				$API_CONFIG['callbackUrl']->clearAttribute();
				$API_CONFIG['callbackUrl']->setAttribute(LINKEDIN::_GET_TYPE, 'initiate');
				$API_CONFIG['callbackUrl']->setAttribute(LINKEDIN::_GET_RESPONSE, '1');
				
				/*
				$API_CONFIG['callbackUrl']  = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://';
				$API_CONFIG['callbackUrl'] .= $_SERVER['SERVER_NAME'];
				$API_CONFIG['callbackUrl'] .= (($_SERVER['SERVER_PORT'] != PORT_HTTP) || ($_SERVER['SERVER_PORT'] != PORT_HTTP_SSL)) ? ':' . $_SERVER['SERVER_PORT'] : '';
				$API_CONFIG['callbackUrl'] .= $_SERVER['PHP_SELF'] . '?' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';	
				*/
				
				/*
				$objCallbackUrl = new URL();
				$objCallbackUrl->setAttribute(LINKEDIN::_GET_TYPE, strtolower($_REQUEST[LINKEDIN::_GET_TYPE]));
				$objCallbackUrl->setAttribute(LINKEDIN::_GET_RESPONSE, 1);
				new dump($objCallbackUrl->build());
				$API_CONFIG['callbackUrl'] = $objCallbackUrl->build();
				*/
				
				// Load the linkedin request api object 
				$objLinkedInApi = new LinkedIn($API_CONFIG); 
				
				$_GET[LINKEDIN::_GET_RESPONSE] = (TRUE === isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
				
				if(! $_GET[LINKEDIN::_GET_RESPONSE]) 
				{
					// LinkedIn hasn't sent us a response, the user is initiating the connection
					// send a request for a LinkedIn access token
					$arrResponse = $objLinkedInApi->retrieveTokenRequest();
					
					if($arrResponse['success'] === TRUE) 
					{
					  	// store the request token
					  	$_SESSION['oauth']['linkedin']['request'] = $arrResponse['linkedin'];
					  
					  	// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
					  	header('Location: ' . LINKEDIN::_URL_AUTHENTICATE . $arrResponse['linkedin']['oauth_token']);
					} 
					else 
					{
					  	// bad token request
						$blnReturn = false;
						
						throw new Exception
						(
							"Request token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . 
							print_r($arrResponse, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($objLinkedInApi, TRUE) . "</pre>"
						);
					}
				}
				
				else
				{
					// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
					$arrLinkedResponse = $objLinkedInApi->retrieveTokenAccess(
						$_SESSION['oauth']['linkedin']['request']['oauth_token'], 
						$_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']
					);
					
					if($arrLinkedResponse['success'] === TRUE) 
					{
						// the request went through without an error, gather user's 'access' tokens
						$_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
						
						// set the user as authorized for future quick reference
						$_SESSION['oauth']['linkedin']['authorized'] = TRUE;
						
						// Lets get the user's profile data
						$arrProfileRequest = $objLinkedInApi->profile('~:(' . implode(',', array_flip(self::$_ARR_PROFILE_REQUEST_FIELDS)) . ')');
						if (TRUE === $this->getVariable('success', $arrProfileRequest))
						{
							/**
							 * ----------------------------------------
							 *  This is where we get the user's info.
							 * ----------------------------------------
							 */
							$_SESSION['linkedInProfile'] = OAUTH_LINKEDIN::xmlResponseToArray(
								new SimpleXMLElement($arrProfileRequest['linkedin'])
							);
							
							new dump($_SESSION['linkedInProfile']); die;
						}
						else
						{
							throw new LinkedInException("Error retrieving profile information:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($arrProfileRequest) . "</pre>");	
						}
						
						// redirect the user back to the demo page
						header('Location: ' . $_SERVER['PHP_SELF']);
					} 
					else 
					{
						// bad token access
						$blnReturn = false;
						
						throw new Exception(
							"Access token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . 
							"</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>"
						);
					}	
				}
				break;	
			}
			
			case 'revoke' : 
			{
				// check the session
				if (FALSE === self::linkedInOauthSessionExists())
				{
					$blnReturn = false;
					throw new LinkedInException('This script requires session support, which doesn\'t appear to be working correctly.');
				}
				
				$objLinkedInApi = new LinkedIn($API_CONFIG); 
				$objLinkedInApi->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
				$arrRevokeResponse = $objLinkedInApi->revoke();
				
				if (
					(TRUE === is_array($arrRevokeResponse)) &&
					(TRUE === array_key_exists('success', $arrRevokeResponse)) &&
					(TRUE === $this->getVariable('success', $arrRevokeResponse))
				) {
					SESSION::destroySession();
				}
				else
				{
					// echo "Error revoking user's token:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";	
					$blnReturn = false;
				}
				  
				break;	
			}
		}
		
		
		return ($blnReturn);
	}
	
	
	/**
	 * Session existance check.
	 * 
	 * Helper function that checks to see that we have a 'set' $_SESSION that we can
	 * use for the demo.   
	 *
	 * @param	none
	 * @return 	Boolean
	 */ 
	private static function linkedInOauthSessionExists()
	{
		return ((bool) ((TRUE === is_array($_SESSION)) && (TRUE === array_key_exists('oauth', $_SESSION))));
	}
	
	
	/** 
	 * Converts a simpleXML element into an array. Preserves attributes.<br/> 
	 * You can choose to get your elements either flattened, or stored in a custom 
	 * index that you define.<br/> 
	 * For example, for a given element 
	 * <code> 
	 * <field name="someName" type="someType"/> 
	 * </code> 
	 * <br> 
	 * if you choose to flatten attributes, you would get: 
	 * <code> 
	 * $array['field']['name'] = 'someName'; 
	 * $array['field']['type'] = 'someType'; 
	 * </code> 
	 * If you choose not to flatten, you get: 
	 * <code> 
	 * $array['field']['@attributes']['name'] = 'someName'; 
	 * </code> 
	 * <br>__________________________________________________________<br> 
	 * Repeating fields are stored in indexed arrays. so for a markup such as: 
	 * <code> 
	 * <parent> 
	 *     <child>a</child> 
	 *     <child>b</child> 
	 *     <child>c</child> 
	 * ... 
	 * </code> 
	 * you array would be: 
	 * <code> 
	 * $array['parent']['child'][0] = 'a'; 
	 * $array['parent']['child'][1] = 'b'; 
	 * ...And so on. 
	 * </code> 
	 * @param simpleXMLElement    $xml            the XML to convert 
	 * @param boolean|string    $attributesKey    if you pass TRUE, all values will be 
	 *                                            stored under an '@attributes' index. 
	 *                                            Note that you can also pass a string 
	 *                                            to change the default index.<br/> 
	 *                                            defaults to null. 
	 * @param boolean|string    $childrenKey    if you pass TRUE, all values will be 
	 *                                            stored under an '@children' index. 
	 *                                            Note that you can also pass a string 
	 *                                            to change the default index.<br/> 
	 *                                            defaults to null. 
	 * @param boolean|string    $valueKey        if you pass TRUE, all values will be 
	 *                                            stored under an '@values' index. Note 
	 *                                            that you can also pass a string to 
	 *                                            change the default index.<br/> 
	 *                                            defaults to null. 
	 * @return array the resulting array. 
	 */ 
	public static final function xmlResponseToArray(SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null){ 
	
		if($childrenKey && !is_string($childrenKey)){$childrenKey = '@children';} 
		if($attributesKey && !is_string($attributesKey)){$attributesKey = '@attributes';} 
		if($valueKey && !is_string($valueKey)){$valueKey = '@values';} 
	
		$return = array(); 
		$name = $xml->getName(); 
		$_value = trim((string)$xml); 
		if(!strlen($_value)){$_value = null;}; 
	
		if($_value!==null){ 
			if($valueKey){$return[$valueKey] = $_value;} 
			else{$return = $_value;} 
		} 
	
		$children = array(); 
		$first = true; 
		foreach($xml->children() as $elementName => $child){ 
			$value = OAUTH_LINKEDIN::xmlResponseToArray($child,$attributesKey, $childrenKey,$valueKey); 
			if(isset($children[$elementName])){ 
				if(is_array($children[$elementName])){ 
					if($first){ 
						$temp = $children[$elementName]; 
						unset($children[$elementName]); 
						$children[$elementName][] = $temp; 
						$first=false; 
					} 
					$children[$elementName][] = $value; 
				}else{ 
					$children[$elementName] = array($children[$elementName],$value); 
				} 
			} 
			else{ 
				$children[$elementName] = $value; 
			} 
		} 
		if($children){ 
			if($childrenKey){$return[$childrenKey] = $children;} 
			else{$return = array_merge($return,$children);} 
		} 
	
		$attributes = array(); 
		foreach($xml->attributes() as $name=>$value){ 
			$attributes[$name] = trim($value); 
		} 
		if($attributes){ 
			if($attributesKey){$return[$attributesKey] = $attributes;} 
			else{$return = array_merge($return, $attributes);} 
		} 
	
		return $return; 
	} 
}	

/**
 * 'LinkedIn' class declaration.
 *  
 * This class provides generalized LinkedIn oauth functionality.
 * 
 * @access public
 * @package classpackage
 */
class LinkedIn
{
    // api/oauth settings
    const _DEFAULT_OAUTH_REALM = 'http://api.linkedin.com';
    const _DEFAULT_OAUTH_VERSION = '1.0';
    
    // the default response format from LinkedIn
    const _DEFAULT_RESPONSE_FORMAT = 'xml';
    
    // helper constants used to standardize LinkedIn <-> API communication.  See demo page for usage.
    const _GET_RESPONSE = 'lResponse';
    const _GET_TYPE = 'lType';
    
    // Invitation API constants.
    const _INV_SUBJECT = 'Invitation to connect';
    const _INV_BODY_LENGTH = 200;
    
    // API methods
    const _METHOD_TOKENS = 'POST';
    
    // Network API constants.
    const _NETWORK_LENGTH = 1000;
    const _NETWORK_HTML = '<a>';
    
    // response format type constants, see http://developer.linkedin.com/docs/DOC-1203
    const _RESPONSE_JSON = 'JSON';
    const _RESPONSE_JSONP = 'JSONP';
    const _RESPONSE_XML = 'XML';
    
    // Share API constants
    const _SHARE_COMMENT_LENGTH = 700;
    const _SHARE_CONTENT_TITLE_LENGTH = 200;
    const _SHARE_CONTENT_DESC_LENGTH = 400;
    
    // LinkedIn API end-points
    const _URL_ACCESS = 'https://api.linkedin.com/uas/oauth/accessToken';
    const _URL_API = 'https://api.linkedin.com';
    
    /**
     * @deprecated
     */
    const _URL_AUTH = self::_URL_AUTHENTICATE;
    const _URL_AUTHENTICATE = 'https://www.linkedin.com/uas/oauth/authenticate?oauth_token=';
    const _URL_AUTHORIZE = 'https://www.linkedin.com/uas/oauth/authorize?oauth_token=';
    const _URL_REQUEST = 'https://api.linkedin.com/uas/oauth/requestToken';
    const _URL_REVOKE = 'https://api.linkedin.com/uas/oauth/invalidateToken';
    
    // library version
    const _VERSION = '3.3.0';
    
    // oauth properties
    protected $callback;
    protected $token = NULL;
    
    // application properties
    protected $application_key, $application_secret;
    
    // the format of the data to return
    protected $response_format = self::_DEFAULT_RESPONSE_FORMAT;
    
    // last request fields
    public $last_request_headers, $last_request_url;
    
    /**
     * Create a LinkedIn object, used for OAuth-based authentication and 
     * communication with the LinkedIn API.	 
     * 
     * @param arr $config
     *    The 'start-up' object properties:
     *           - appKey       => The application's API key
     *           - appSecret    => The application's secret key
     *           - callbackUrl  => [OPTIONAL] the callback URL - only used to 
     *                             retrieve the request token.
     *                 	 
     * @return obj
     *    A new LinkedIn object.   	 
     */
    public function __construct($config)
    {
        if (!is_array($config)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->__construct(): bad data passed, $config must be of type array.');
        }
		
        $this->setApplicationKey($config['appKey']);
        $this->setApplicationSecret($config['appSecret']);
        if (array_key_exists('callbackUrl', $config)) {
            $this->setCallbackUrl($config['callbackUrl']);
        } else {
            $this->setCallbackUrl(NULL);
        }
    }
    
    /**
     * The class destructor.
     * 
     * Explicitly clears LinkedIn object from memory upon destruction.
     */
    public function __destruct()
    {
        unset($this);
    }
    
    /**
     * Bookmark a job.
     * 
     * Calling this method causes the current user to add a bookmark for the 
     * specified job:
     * 
     *   http://developer.linkedin.com/docs/DOC-1323
     * 
     * @param str $jid
     *    Job ID you want to bookmark.
     *         	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     * 
     * @since 3.1.0   	 
     */
    public function bookmarkJob($jid)
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->bookmarkJob(): bad data passed, $jid must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/job-bookmarks';
        $response = $this->fetch('POST', $query, '<job-bookmark><job><id>' . trim($jid) . '</id></job></job-bookmark>');
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Get list of jobs you have bookmarked.
     * 
     * Returns a list of jobs the current user has bookmarked, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1323   
     * 	
     * @return arr
     *         Array containing retrieval success, LinkedIn response.
     *         
     * @since 3.1.0   	 
     */
    public function bookmarkedJobs()
    {
        // construct and send the request  
        $query    = self::_URL_API . '/v1/people/~/job-bookmarks';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Used to check whether a response LinkedIn object has the required http_code or not and 
     * returns an appropriate LinkedIn object.
     * 
     * @param var $http_code_required
     * 		The required http response from LinkedIn, passed in either as an integer, 
     * 		or an array of integers representing the expected values.	 
     * @param arr $response 
     *    An array containing a LinkedIn response.
     * 
     * @return boolean
     * 	  TRUE or FALSE depending on if the passed LinkedIn response matches the expected response.
     * 	  
     * @since 3.1.0
     * 
     * @deprecated
     * 
     * @see #setResponse(var, arr)         	 
     */
    private function checkResponse($http_code_required, $response)
    {
        return $this->setResponse($http_code_required, $response);
    }
    
    /**
     * Close a job.
     * 
     * Calling this method causes the passed job to be closed, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1151   
     * 
     * @param str $jid
     *    Job ID you want to close.
     *            	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.1   	 
     */
    public function closeJob($jid)
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->closeJob(): bad data passed, $jid must be of string value.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/jobs/partner-job-id=' . trim($jid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * [DEPRECATED] Network update comment posting method.
     * 
     * Post a comment on an existing connections shared content. API details can
     * be found here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1043 
     * 
     * @param str $uid 
     *    The LinkedIn update ID.   	 
     * @param str $comment 
     *    The share comment to be posted.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.1.0
     * 
     * @deprecated
     * 
     * @see #commentUpdate(str, str)                         	 
     */
    public function comment($uid, $comment)
    {
        return $this->commentUpdate($uid, $comment);
    }
    
    /**
     * [DEPRECATED] Network update comment retrieval.
     *     
     * Return all comments associated with a given network update:
     * 	 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *   
     * @since 2.1.0
     * 
     * @deprecated
     * 
     * @see #updateComments(str)                                     
     */
    public function comments($uid)
    {
        return $this->updateComments($uid);
    }
    
    /**
     * Company profile retrieval function.
     * 
     * Takes a string of parameters as input and requests company profile data 
     * from the LinkedIn Company Profile API. See the official documentation for 
     * $options 'field selector' formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1014
     *   http://developer.linkedin.com/docs/DOC-1259   
     * 
     * @param str $options
     *    Data retrieval options.	
     * @param	bool $by_email
     *    [OPTIONAL] Search by email domain?
     * 	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0    	 
     */
    public function company($options, $by_email = FALSE)
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->company(): bad data passed, $options must be of type string.');
        }
        if (!is_bool($by_email)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->company(): bad data passed, $by_email must be of type boolean.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/companies' . ($by_email ? '' : '/') . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Company products and their associated recommendations.
     * 
     * The product data type contains details about a company's product or 
     * service, including recommendations from LinkedIn members, and replies from 
     * company representatives.
     * 
     *   http://developer.linkedin.com/docs/DOC-1327   
     * 
     * @param str $cid
     *    Company ID you want the producte for.	
     * @param str $options
     *    [OPTIONAL] Data retrieval options.
     *            	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0    	 
     */
    public function companyProducts($cid, $options = '')
    {
        // check passed data
        if (!is_string($cid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->companyProducts(): bad data passed, $cid must be of type string.');
        }
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->companyProducts(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/companies/' . trim($cid) . '/products' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Connection retrieval function.
     * 
     * Takes a string of parameters as input and requests connection-related data 
     * from the Linkedin Connections API. See the official documentation for 
     * $options 'field selector' formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1014      	 
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *  
     * @since 1.2.0   	 
     */
    public function connections($options = '~/connections')
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->connections(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This creates a post in the specified group with the specified title and specified summary.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * @param str $title
     * 		The title of the post. This must be non-empty.
     * @param str $summary
     * 		[OPTIONAL] The content or summary of the post. This can be empty.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function createPost($gid, $title, $summary = '')
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->createPost(): bad data passed, $gid must be of type string.');
        }
        if (!is_string($title) || empty($title)) {
            throw new LinkedInException('LinkedIn->createPost(): bad data passed, $title must be a non-empty string.');
        }
        if (!is_string($summary)) {
            throw new LinkedInException('LinkedIn->createPost(): bad data passed, $summary must be of type string.');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
    				 <post>
    					 <title>' . $title . '</title>
    					 <summary>' . $summary . '</summary>
    				 </post>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/groups/' . trim($gid) . '/posts';
        $response = $this->fetch('POST', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * This creates a comment on a post.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The group id.
     * @param str $comment
     * 		The comment to post. This must be non-empty.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.3.0   	 
     */
    public function createPostComment($pid, $comment)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->createPostComment(): bad data passed, $pid must be of type string.');
        }
        if (!is_string($comment) || empty($comment)) {
            throw new LinkedInException('LinkedIn->createPostComment(): bad data passed, $comment must be a non-empty string.');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
    				 <comment>
    					 <text>' . $comment . '</text>
    				 </comment>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/comments';
        $response = $this->fetch('POST', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Network update comment posting method.
     * 
     * Post a comment on an existing connections shared content. API details can
     * be found here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1043 
     * 
     * @param str $uid 
     *    The LinkedIn update ID.   	 
     * @param str $comment 
     *    The share comment to be posted.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.3.0             	 
     */
    public function createUpdateComment($uid, $comment)
    {
        // check passed data
        if (!is_string($uid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->createUpdateComment(): bad data passed, $uid must be of type string.');
        }
        if (!is_string($comment)) {
            // nothing/non-string passed, raise an exception
            throw new LinkedInException('LinkedIn->createUpdateComment(): bad data passed, $comment must be a non-zero length string.');
        }
        
        /**
         * Share comment rules:
         * 
         * 1) No HTML permitted.
         * 2) Comment cannot be longer than 700 characters.     
         */
        $comment = substr(trim(htmlspecialchars(strip_tags($comment))), 0, self::_SHARE_COMMENT_LENGTH);
        $data    = '<?xml version="1.0" encoding="UTF-8"?>
                <update-comment>
  				        <comment>' . $comment . '</comment>
  				      </update-comment>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/network/updates/key=' . $uid . '/update-comments';
        $response = $this->fetch('POST', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * This deletes the specified comment if you are the owner or moderator 
     * of the comment. Otherwise, it just flags the comment as inappropriate.
     * 
     *   https://developer.linkedin.com/documents/groups-api
     * 
     * @param str $cid
     * 		The comment id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.3.0   	 
     */
    public function deletePostComment($cid)
    {
        if (!is_string($cid)) {
            throw new LinkedInException('LinkedIn->deletePostComment(): bad data passed, $cid must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/comments/' . trim($cid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * This deletes the specified post if you are the owner or moderator of 
     * that post. Otherwise, it just flags the post as inappropriate.
     * 
     *   https://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function deletePost($pid)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->deletePost(): bad data passed, $pid must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Edit a job.
     * 
     * Calling this method causes the passed job to be edited, with the passed
     * XML instructing which fields to change, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1154
     *   http://developer.linkedin.com/docs/DOC-1142      
     * 
     * @param str $jid
     *    Job ID you want to renew.
     * @param str $xml
     *    The XML containing the job fields to edit.	 
     *            	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.1   	 
     */
    public function editJob($jid, $xml)
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->editJob(): bad data passed, $jid must be of string value.');
        }
        if (is_string($xml)) {
            $xml = trim(stripslashes($xml));
        } else {
            // bad data passed
            throw new LinkedInException('LinkedIn->editJob(): bad data passed, $xml must be of string value.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/jobs/partner-job-id=' . trim($jid);
        $response = $this->fetch('PUT', $query, $xml);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Exchange user's OAuth tokens.
     *
     * Exchange the user's permanent Oauth 1.0 access token from the 
     * Linkedin API using their temporary OAuth 2.0 bearer token, per:
     * 
     *   http://developer.linkedin.com/documents/exchange-jsapi-tokens-rest-api-oauth-tokens   	 
     * 
     * @param str $token
     *    The token returned from the user authorization stage.
     * @param str $secret
     *    The secret returned from the request token stage.
     * @param str $verifier
     *    The verification value from LinkedIn.
     *    	 
     * @return arr 
     *    The Linkedin OAuth/http response, in array format.
     *    
     * @since 3.3.0            	 
     */
    public function exchangeToken($bearer_token)
    {
        // check passed data
        if (!is_string($bearer_token)) {
            // nothing passed, raise an exception
            throw new LinkedInException('LinkedIn->exchangeToken(): bad data passed, $bearer_token must be of string value.');
        }
        
        // start retrieval process
        $parameters = array(
            'xoauth_oauth2_access_token' => $bearer_token
        );
        $response   = $this->fetch(self::_METHOD_TOKENS, self::_URL_ACCESS, http_build_query($parameters), $parameters);
        parse_str($response['linkedin'], $response['linkedin']);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * General data send/request method.
     * 
     * @param str $method 
     *    The data communication method.	 
     * @param str $url 
     *    The Linkedin API endpoint to connect with.
     * @param str $data
     *    [OPTIONAL] The data to send to LinkedIn.
     * @param arr $parameters 
     *    [OPTIONAL] Addition OAuth parameters to send to LinkedIn.
     *        
     * @return arr 
     *    Array containing:
     * 
     *           array(
     *             'info'      =>	Connection information,
     *             'linkedin'  => LinkedIn response,  
     *             'oauth'     => The OAuth request string that was sent to LinkedIn	 
     *           )
     *           
     * @since 3.0.0      	 
     */
    protected function fetch($method, $url, $data = NULL, $parameters = array())
    {
        // check for cURL
        if (!extension_loaded('curl')) {
            // cURL not present
            throw new LinkedInException('LinkedIn->fetch(): PHP cURL extension does not appear to be loaded/present.');
        }
        
        try {
            // set parameters
            if (!array_key_exists('oauth_version', $parameters)) {
                $parameters['oauth_version'] = self::_DEFAULT_OAUTH_VERSION;
            }
            
            // generate OAuth request
            $oauth_token    = $this->getToken();
            $oauth_token    = (!is_null($oauth_token)) ? new OAuthToken($oauth_token['oauth_token'], $oauth_token['oauth_token_secret']) : NULL;
            $oauth_consumer = new OAuthConsumer($this->getApplicationKey(), $this->getApplicationSecret(), $this->getCallbackUrl());
            $oauth_req      = OAuthRequest::from_consumer_and_token($oauth_consumer, $oauth_token, $method, $url, $parameters);
            $oauth_req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $oauth_consumer, $oauth_token);
            
            // start cURL, checking for a successful initiation
            if (!$handle = curl_init()) {
                // cURL failed to start
                throw new LinkedInException('LinkedIn->fetch(): cURL did not initialize properly.');
            }
            
            // set cURL options, based on parameters passed
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_VERBOSE, FALSE);
            
            // configure the header we are sending to LinkedIn - http://developer.linkedin.com/docs/DOC-1203
            $header = array(
                $oauth_req->to_header(self::_DEFAULT_OAUTH_REALM)
            );
            if (is_null($data)) {
                // not sending data, identify the content type
                switch ($this->getResponseFormat()) {
                    case self::_RESPONSE_JSON:
                        $header[] = 'x-li-format: json';
                        break;
                    case self::_RESPONSE_JSONP:
                        $header[] = 'x-li-format: jsonp';
                        break;
                }
            } else {
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($handle, CURLOPT_HTTPHEADER, $header);
            
            // set the last url, headers
            $this->last_request_url     = $url;
            $this->last_request_headers = $header;
            
            // gather the response
            $return_data['linkedin']        = curl_exec($handle);
            $return_data['info']            = curl_getinfo($handle);
            $return_data['oauth']['header'] = $oauth_req->to_header(self::_DEFAULT_OAUTH_REALM);
            $return_data['oauth']['body']   = print_r($data, TRUE);
            $return_data['oauth']['string'] = $oauth_req->base_string;
            
            // check for HTTP, NO response (http_code = 0) from cURL
            if ((array_key_exists('info', $return_data)) && (array_key_exists('http_code', $return_data['info']))) {
                if ($return_data['info']['http_code'] == 0) {
                    throw new LinkedInException('LinkedIn->fetch(): connection was closed unexpectedly with endpoint.');
                }
            } else {
                // no response code from cURL
                throw new LinkedInException('LinkedIn->fetch(): cURL did not return an HTTP code.');
            }
            
            // check for throttling
            if (self::isThrottled($return_data['linkedin'])) {
                throw new LinkedInException('LinkedIn->fetch(): throttling limit for this user/application has been reached for LinkedIn resource - ' . $url);
            }
            
            // close cURL connection
            curl_close($handle);
            
            // no exceptions thrown, return the data
            return $return_data;
        }
        catch (OAuthException $e) {
            // oauth exception raised
            throw new LinkedInException('OAuth exception caught: ' . $e->getMessage());
        }
    }
    
    /**
     * This flags a specified post as specified by type.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * @param str $type
     * 		The type to flag the post as.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function flagPost($pid, $type)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->flagPost(): bad data passed, $pid must be of type string');
        }
        if (!is_string($type)) {
            throw new LinkedInException('LinkedIn->flagPost(): bad data passed, $like must be of type string');
        }
        //Constructing the xml
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        switch ($type) {
            case 'promotion':
                $data .= '<code>promotion</code>';
                break;
            case 'job':
                $data .= '<code>job</code>';
                break;
            default:
                throw new LinkedInException('LinkedIn->flagPost(): invalid value for $type, must be one of: "promotion", "job"');
                break;
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . $pid . '/category/code';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Follow a company.
     * 
     * Calling this method causes the current user to start following the 
     * specified company, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1324
     * 
     * @param str $cid
     *    Company ID you want to follow.
     *         	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function followCompany($cid)
    {
        // check passed data
        if (!is_string($cid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->followCompany(): bad data passed, $cid must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/following/companies';
        $response = $this->fetch('POST', $query, '<company><id>' . trim($cid) . '</id></company>');
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Get list of companies you follow.
     * 
     * Returns a list of companies the current user is currently following, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1324   
     * 	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function followedCompanies()
    {
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/following/companies';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Follows the specified post.
     * 
     *   https://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * @param bool $follow
     *    [DEPRECATED] No longer in use, to 'unfollow' a post, see 'unfollowPost()'.
     *    This parameter will be removed by v4 of the class.         	 
     * 			 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function followPost($pid, $follow = TRUE)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->followPost(): bad data passed, $pid must be of type string');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
				     <is-following>true</is-following>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/relation-to-viewer/is-following';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Get the application_key property.
     * 
     * @return str 
     *    The application key.
     *    
     * @since 1.0.0             	 
     */
    public function getApplicationKey()
    {
        return $this->application_key;
    }
    
    /**
     * Get the application_secret property.
     * 
     * @return str 
     *    The application secret.
     *    
     * @since 1.0.0              	 
     */
    public function getApplicationSecret()
    {
        return $this->application_secret;
    }
    
    /**
     * Get the callback property.
     * 
     * @return str 
     *    The callback url.
     *   
     * @since 1.0.0              	 
     */
    public function getCallbackUrl()
    {
        return $this->callback;
    }
    
    /**
     * Get the response_format property.
     * 
     * @return str 
     *    The response format.
     *    
     * @since 2.1.0              	 
     */
    public function getResponseFormat()
    {
        return $this->response_format;
    }
    
    /**
     * Get the token property.
     * 
     * @return arr 
     *    The access token.
     *    
     * @since 1.0.0              	 
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * [DEPRECATED] Get the token property. Will be removed in v4 of the
     * class.	 
     * 
     * @return arr 
     *    The access token.
     *    
     * @since 1.0.0
     * 
     * @deprecated 
     * 
     * @see #getToken()                         	 
     */
    public function getTokenAccess()
    {
        return $this->getToken();
    }
    
    /**
     * 
     * Get information about a specific group.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 	 	The group id.
     *  
     * @param str $options
     * 		[OPTIONAL] Field selectors for the group.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    
    public function group($gid, $options = '')
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->group(): bad data passed, $gid must be of type string.');
        }
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->group(): bad data passed, $options must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/groups/' . trim($gid) . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This returns all the groups the user is a member of.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $options
     * 		[OPTIONAL] Field selectors for the groups.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function groupMemberships($options = '')
    {
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->groupMemberships(): bad data passed, $options must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/group-memberships' . trim($options) . '?membership-state=member';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This gets a specified post made within a group.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * @param str $options
     * 		[OPTIONAL] Field selectors for the post.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function groupPost($pid, $options = '')
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->groupPost(): bad data passed, $pid must be of type string.');
        }
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->groupPost(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This returns all the comments made on the specified post within a group.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * @param str $options
     * 		[OPTIONAL] Field selectors for the post comments.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function groupPostComments($pid, $options = '')
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->groupPostComments(): bad data passed, $pid must be of type string.');
        }
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->groupPostComments(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/comments' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This returns all the posts within a group.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function groupPosts($gid, $options = '')
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->groupPosts(): bad data passed, $gid must be of type string');
        }
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->groupPosts(): bad data passed, $options must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/groups/' . trim($gid) . '/posts' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * This returns the group settings of the specified group
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * @param str $options
     * 		[OPTIONAL] Field selectors for the group.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function groupSettings($gid, $options = '')
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->groupSettings(): bad data passed, $gid must be of type string');
        }
        if (!is_string($options)) {
            throw new LinkedInException('LinkedIn->groupSettings(): bad data passed, $options must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/group-memberships/' . trim($gid) . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Send connection invitations.
     *     
     * Send an invitation to connect to your network, either by email address or 
     * by LinkedIn ID. Details on the API here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1012
     * 
     * @param str $method 
     *    The invitation method to process.	 
     * @param str $recipient 
     *    The email/id to send the invitation to.	 	 
     * @param str $subject 
     *    The subject of the invitation to send.
     * @param str $body 
     *    The body of the invitation to send.
     * @param str $type 
     *    [OPTIONAL] The invitation request type (only friend is supported at this time by the Invite API).
     * 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.0.0        	 
     */
    public function invite($method, $recipient, $subject, $body, $type = 'friend')
    {
        /**
         * Clean up the passed data per these rules:
         * 
         * 1) Message must be sent to one recipient (only a single recipient permitted for the Invitation API)
         * 2) No HTML permitted
         * 3) 200 characters max in the invitation subject
         * 4) Only able to connect as a friend at this point     
         */
        // check passed data
        if (empty($recipient)) {
            throw new LinkedInException('LinkedIn->invite(): you must provide an invitation recipient.');
        }
        switch ($method) {
            case 'email':
                if (is_array($recipient)) {
                    $recipient = array_map('trim', $recipient);
                } else {
                    // bad format for recipient for email method
                    throw new LinkedInException('LinkedIn->invite(): invitation recipient email/name array is malformed.');
                }
                break;
            case 'id':
                $recipient = trim($recipient);
                if (!self::isId($recipient)) {
                    // bad format for recipient for id method
                    throw new LinkedInException('LinkedIn->invite(): invitation recipient ID does not match LinkedIn format.');
                }
                break;
            default:
                throw new LinkedInException('LinkedIn->invite(): bad invitation method, must be one of: email, id.');
                break;
        }
        if (!empty($subject)) {
            $subject = trim(htmlspecialchars(strip_tags(stripslashes($subject))));
        } else {
            throw new LinkedInException('LinkedIn->invite(): message subject is empty.');
        }
        if (!empty($body)) {
            $body = trim(htmlspecialchars(strip_tags(stripslashes($body))));
            if (strlen($body) > self::_INV_BODY_LENGTH) {
                throw new LinkedInException('LinkedIn->invite(): message body length is too long - max length is ' . self::_INV_BODY_LENGTH . ' characters.');
            }
        } else {
            throw new LinkedInException('LinkedIn->invite(): message body is empty.');
        }
        switch ($type) {
            case 'friend':
                break;
            default:
                throw new LinkedInException('LinkedIn->invite(): bad invitation type, must be one of: friend.');
                break;
        }
        
        // construct the xml data
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		           <mailbox-item>
		             <recipients>
                   <recipient>';
        switch ($method) {
            case 'email':
                // email-based invitation
                $data .= '<person path="/people/email=' . $recipient['email'] . '">
                                     <first-name>' . htmlspecialchars($recipient['first-name']) . '</first-name>
                                     <last-name>' . htmlspecialchars($recipient['last-name']) . '</last-name>
                                   </person>';
                break;
            case 'id':
                // id-based invitation
                $data .= '<person path="/people/id=' . $recipient . '"/>';
                break;
        }
        $data .= '    </recipient>
                 </recipients>
                 <subject>' . $subject . '</subject>
                 <body>' . $body . '</body>
                 <item-content>
                   <invitation-request>
                     <connect-type>';
        switch ($type) {
            case 'friend':
                $data .= 'friend';
                break;
        }
        $data .= '      </connect-type>';
        switch ($method) {
            case 'id':
                // id-based invitation, we need to get the authorization information
                $query    = 'id=' . $recipient . ':(api-standard-profile-request)';
                $response = self::profile($query);
                if ($response['info']['http_code'] == 200) {
                    $response['linkedin'] = self::xmlToArray($response['linkedin']);
                    if ($response['linkedin'] === FALSE) {
                        // bad XML data
                        throw new LinkedInException('LinkedIn->invite(): LinkedIn returned bad XML data.');
                    }
                    $authentication = explode(':', $response['linkedin']['person']['children']['api-standard-profile-request']['children']['headers']['children']['http-header']['children']['value']['content']);
                    
                    // complete the xml        
                    $data .= '<authorization>
                                       <name>' . $authentication[0] . '</name>
                                       <value>' . $authentication[1] . '</value>
                                     </authorization>';
                } else {
                    // bad response from the profile request, not a valid ID?
                    throw new LinkedInException('LinkedIn->invite(): could not send invitation, LinkedIn says: ' . print_r($response['linkedin'], TRUE));
                }
                break;
        }
        $data .= '    </invitation-request>
                 </item-content>
               </mailbox-item>';
        
        // send request
        $query    = self::_URL_API . '/v1/people/~/mailbox';
        $response = $this->fetch('POST', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * LinkedIn ID validation.
     *	 
     * Checks the passed string $id to see if it has a valid LinkedIn ID format, 
     * which is, as of October 15th, 2010:
     * 
     *   10 alpha-numeric mixed-case characters, plus underscores and dashes.          	 
     * 
     * @param str $id 
     *    A possible LinkedIn ID.         	 
     * 
     * @return bool 
     *    TRUE/FALSE depending on valid ID format determination.
     *    
     * @since 2.0.0                        
     */
    public static function isId($id)
    {
        // check passed data
        if (!is_string($id)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->isId(): bad data passed, $id must be of type string.');
        }
        
        $pattern = '/^[a-z0-9_\-]{10}$/i';
        if ($match = preg_match($pattern, $id)) {
            // we have a match
            $return_data = TRUE;
        } else {
            // no match
            $return_data = FALSE;
        }
        return $return_data;
    }
    
    /**
     * Throttling check.
     * 
     * Checks the passed LinkedIn response to see if we have hit a throttling 
     * limit:
     * 
     * http://developer.linkedin.com/docs/DOC-1112
     * 
     * @param arr $response 
     *    The LinkedIn response.
     *                     	 
     * @return bool
     *    TRUE/FALSE depending on content of response.
     *    
     * @since 1.1.1                        
     */
    public static function isThrottled($response)
    {
        $return_data = FALSE;
        
        // check the variable
        if (!empty($response) && is_string($response)) {
            // we have an array and have a properly formatted LinkedIn response
            
            // store the response in a temp variable
            $temp_response = self::xmlToArray($response);
            if ($temp_response !== FALSE) {
                // check to see if we have an error
                if (array_key_exists('error', $temp_response) && ($temp_response['error']['children']['status']['content'] == 403) && preg_match('/throttle/i', $temp_response['error']['children']['message']['content'])) {
                    // we have an error, it is 403 and we have hit a throttle limit
                    $return_data = TRUE;
                }
            }
        }
        return $return_data;
    }
    
    /**
     * Job posting detail info retrieval function.
     * 
     * The Jobs API returns detailed information about job postings on LinkedIn. 
     * Find the job summary, description, location, and apply our professional graph 
     * to present the relationship between the current member and the job poster or 
     * hiring manager.
     * 
     *   http://developer.linkedin.com/docs/DOC-1322  
     * 
     * @param	str $jid 
     *    ID of the job you want to look up.
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function job($jid, $options = '')
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->job(): bad data passed, $jid must be of type string.');
        }
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->job(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request

        $query    = self::_URL_API . '/v1/jobs/' . trim($jid) . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Join the specified group, per: 
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0         	 
     */
    public function joinGroup($gid)
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->joinGroup(): bad data passed, $gid must be of type string.');
        }
        
        // constructing the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
  				   <group-membership>
  				   	 <membership-state>
  				  	 	 <code>member</code>
  				  	 </membership-state>
  				   </group-membership>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/group-memberships/' . trim($gid);
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 200 or 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(array(
            200,
            201
        ), $response);
    }
    
    /**
     * Returns the last request header from the previous call to the 
     * LinkedIn API.
     * 
     * @returns str
     *    The header, in string format.
     *    
     * @since 3.1.1    	 
     */
    public function lastRequestHeader()
    {
        return $this->last_request_headers;
    }
    
    /**
     * Returns the last request url from the previous call to the 
     * LinkedIn API.
     * 
     * @returns str
     *    The url, in string format.
     *    
     * @since 3.1.1   	 
     */
    public function lastRequestUrl()
    {
        return $this->last_request_url;
    }
    
    /**
     * Leave the specified group, per:.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function leaveGroup($gid)
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->leaveGroup(): bad data passed, $gid must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/group-memberships/' . trim($gid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * [DEPRECATED] Like another user's network update, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.1.0
     * 
     * @deprecated  
     * 
     * @see #likeUpdate(str)                                  
     */
    public function like($uid)
    {
        return $this->likeUpdate($uid);
    }
    
    /**
     * Likes the specified post, per:
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * @param bool $like
     *    [DEPRECATED] No longer in use, to 'unlike' a post, see 'unlikePost()'.
     *    This parameter will be removed by v4 of the class.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function likePost($pid, $like = TRUE)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->likePost(): bad data passed, $pid must be of type string');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		         <is-liked>true</is-liked>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/relation-to-viewer/is-liked';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * [DEPRECATED] Retrieve network update likes.
     *    
     * Return all likes associated with a given network update:
     * 
     * http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.1.0 
     * 
     * @deprecated
     * 
     * @see #updateLikes(str)                                   
     */
    public function likes($uid)
    {
        return $this->updateLikes($uid);
    }
    
    /**
     * Like another user's network update, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.3.0                        
     */
    public function likeUpdate($uid)
    {
        // check passed data
        if (!is_string($uid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->likeUpdate(): bad data passed, $uid must be of type string.');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		         <is-liked>true</is-liked>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/network/updates/key=' . $uid . '/is-liked';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Connection messaging method.
     * 	 
     * Send a message to your network connection(s), optionally copying yourself.  
     * Full details from LinkedIn on this functionality can be found here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1044
     * 
     * @param arr $recipients 
     *    The connection(s) to send the message to.	 	 
     * @param str $subject 
     *    The subject of the message to send.
     * @param str $body 
     *    The body of the message to send.
     * @param bool $copy_self 
     *    [OPTIONAL] Also update the teathered Twitter account.
     *    	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 1.2.0            	 
     */
    public function message($recipients, $subject, $body, $copy_self = FALSE)
    {
        /**
         * Clean up the passed data per these rules:
         * 
         * 1) Message must be sent to at least one recipient
         * 2) No HTML permitted
         */
        if (!empty($subject) && is_string($subject)) {
            $subject = trim(strip_tags(stripslashes($subject)));
        } else {
            throw new LinkedInException('LinkedIn->message(): bad data passed, $subject must be of type string.');
        }
        if (!empty($body) && is_string($body)) {
            $body = trim(strip_tags(stripslashes($body)));
        } else {
            throw new LinkedInException('LinkedIn->message(): bad data passed, $body must be of type string.');
        }
        if (!is_array($recipients) || count($recipients) < 1) {
            // no recipients, and/or bad data
            throw new LinkedInException('LinkedIn->message(): at least one message recipient required.');
        }
        
        // construct the xml data
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		           <mailbox-item>
		             <recipients>';
        $data .= ($copy_self) ? '<recipient><person path="/people/~"/></recipient>' : '';
        for ($i = 0; $i < count($recipients); $i++) {
            if (is_string($recipients[$i])) {
                $data .= '<recipient><person path="/people/' . trim($recipients[$i]) . '"/></recipient>';
            } else {
                throw new LinkedInException('LinkedIn->message(): bad data passed, $recipients must be an array of type string.');
            }
        }
        $data .= '  </recipients>
                 <subject>' . htmlspecialchars($subject) . '</subject>
                 <body>' . htmlspecialchars($body) . '</body>
               </mailbox-item>';
        
        // send request
        $query    = self::_URL_API . '/v1/people/~/mailbox';
        $response = $this->fetch('POST', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Job posting method.
     * 	 
     * Post a job to LinkedIn, assuming that you have access to this feature. 
     * Full details from LinkedIn on this functionality can be found here: 
     * 
     *   http://developer.linkedin.com/community/jobs?view=documents
     * 
     * @param str $xml 
     *    The XML defining a job to post.	 	 
     *    	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.1             	 
     */
    public function postJob($xml)
    {
        // check passed data
        if (is_string($xml)) {
            $xml = trim(stripslashes($xml));
        } else {
            throw new LinkedInException('LinkedIn->postJob(): bad data passed, $xml must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/jobs';
        $response = $this->fetch('POST', $query, $xml);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * General profile retrieval function.
     * 
     * Takes a string of parameters as input and requests profile data from the 
     * Linkedin Profile API. See the official documentation for $options
     * 'field selector' formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1014
     *   http://developer.linkedin.com/docs/DOC-1002    
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 1.0.0    	 
     */
    public function profile($options = '~')
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->profile(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Manual API call method, allowing for support for un-implemented API
     * functionality to be supported.
     * 
     * @param str $method 
     *    The data communication method.	 
     * @param str $url 
     *    The Linkedin API endpoint to connect with - should NOT include the 
     *    leading https://api.linkedin.com/v1.
     * @param str $body
     *    [OPTIONAL] The URL-encoded body data to send to LinkedIn with the request.
     * 
     * @return arr
     * 		Array containing retrieval information, LinkedIn response. Note that you
     * 		must manually check the return code and compare this to the expected 
     * 		API response to determine  if the raw call was successful.
     * 		
     * @since 3.2.0    	 
     */
    public function raw($method, $url, $body = NULL)
    {
        if (!is_string($method)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->raw(): bad data passed, $method must be of string value.');
        }
        if (!is_string($url)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->raw(): bad data passed, $url must be of string value.');
        }
        if (!is_null($body) && !is_string($url)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->raw(): bad data passed, $body must be of string value.');
        }
        
        // construct and send the request
        $query = self::_URL_API . '/v1' . trim($url);
        return $this->fetch($method, $query, $body);
    }
    
    /**
     * This removes the specified group from the group suggestions, per:
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0    	 
     */
    public function removeSuggestedGroup($gid)
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->removeSuggestedGroup(): bad data passed, $gid must be of type string');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/suggestions/groups/' . trim($gid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Renew a job.
     * 
     * Calling this method causes the passed job to be renewed, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1154   
     * 
     * @param str $jid
     *    Job ID you want to renew.
     * @param str $cid
     *    Contract ID that covers the passed Job ID.	 
     *            	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.1   	 
     */
    public function renewJob($jid, $cid)
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->renewJob(): bad data passed, $jid must be of string value.');
        }
        if (!is_string($cid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->renewJob(): bad data passed, $cid must be of string value.');
        }
        
        // construct the xml data
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		           <job>
		             <contract-id>' . trim($cid) . '</contract-id>
                 <renewal/>
               </job>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/jobs/partner-job-id=' . trim($jid);
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Access token retrieval.
     *
     * Request the user's access token from the Linkedin API.
     * 
     * @param str $token
     *    The token returned from the user authorization stage.
     * @param str $secret
     *    The secret returned from the request token stage.
     * @param str $verifier
     *    The verification value from LinkedIn.
     *    	 
     * @return arr 
     *    The Linkedin OAuth/http response, in array format.
     *    
     * @since 1.0.0            	 
     */
    public function retrieveTokenAccess($token, $secret, $verifier)
    {
        // check passed data
        if (!is_string($token) || !is_string($secret) || !is_string($verifier)) {
            // nothing passed, raise an exception
            throw new LinkedInException('LinkedIn->retrieveTokenAccess(): bad data passed, string type is required for $token, $secret and $verifier.');
        }
        
        // start retrieval process
        $this->setToken(array(
            'oauth_token' => $token,
            'oauth_token_secret' => $secret
        ));
        $parameters = array(
            'oauth_verifier' => $verifier
        );
        $response   = $this->fetch(self::_METHOD_TOKENS, self::_URL_ACCESS, NULL, $parameters);
        parse_str($response['linkedin'], $response['linkedin']);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        if ($response['info']['http_code'] == 200) {
            // tokens retrieved
            $this->setToken($response['linkedin']);
            
            // set the response
            $return_data            = $response;
            $return_data['success'] = TRUE;
        } else {
            // error getting the request tokens
            $this->setToken(NULL);
            
            // set the response
            $return_data            = $response;
            $return_data['error']   = 'HTTP response from LinkedIn end-point was not code 200';
            $return_data['success'] = FALSE;
        }
        return $return_data;
    }
    
    /**
     * Request token retrieval.
     * 
     * Get the request token from the Linkedin API.
     * 
     * @return arr
     *    The Linkedin OAuth/http response, in array format.
     *    
     * @since 1.0.0            	 
     */
    public function retrieveTokenRequest()
    {
        $parameters = array(
            'oauth_callback' => $this->getCallbackUrl()
        );
		
        $response   = $this->fetch(self::_METHOD_TOKENS, self::_URL_REQUEST, NULL, $parameters);
        parse_str($response['linkedin'], $response['linkedin']);
		
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        if (($response['info']['http_code'] == 200) && (array_key_exists('oauth_callback_confirmed', $response['linkedin'])) && ($response['linkedin']['oauth_callback_confirmed'] == 'true')) {
            // tokens retrieved
            $this->setToken($response['linkedin']);
            
            // set the response
            $return_data            = $response;
            $return_data['success'] = TRUE;
        } else {
            // error getting the request tokens
            $this->setToken(NULL);
            
            // set the response
            $return_data = $response;
            if ((array_key_exists('oauth_callback_confirmed', $response['linkedin'])) && ($response['linkedin']['oauth_callback_confirmed'] == 'true')) {
                $return_data['error'] = 'HTTP response from LinkedIn end-point was not code 200';
            } else {
                $return_data['error'] = 'OAuth callback URL was not confirmed by the LinkedIn end-point';
            }
            $return_data['success'] = FALSE;
        }
        return $return_data;
    }
    
    /**
     * User authorization revocation.
     * 
     * Revoke the current user's access token, clear the access token's from 
     * current LinkedIn object. The current documentation for this feature is 
     * found in a blog entry from April 29th, 2010:
     * 
     *   http://developer.linkedin.com/community/apis/blog/2010/04/29/oauth--now-for-authentication	 
     * 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 1.0.0         	 
     */
    public function revoke()
    {
        // construct and send the request
        $response = $this->fetch('GET', self::_URL_REVOKE);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * [DEPRECATED] General people search function. Will be removed in v4 of the
     * class.
     * 
     * Takes a string of parameters as input and requests profile data from the 
     * Linkedin People Search API.  See the official documentation for $options
     * querystring formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1191 
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.0.0
     * 
     * @deprecated 
     * 
     * @see #searchPeople(str)               	 
     */
    public function search($options = NULL)
    {
        return $this->searchPeople($options);
    }
    
    /**
     * Company search.
     * 
     * Uses the Company Search API to find companies using keywords, industry, 
     * location, or some other criteria. It returns a collection of matching 
     * companies.
     * 
     *   http://developer.linkedin.com/docs/DOC-1325  
     * 
     * @param str $options
     *    [OPTIONAL] Search options.	
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0    	 
     */
    public function searchCompanies($options = '')
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->searchCompanies(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/company-search' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Jobs search.
     * 
     * Use the Job Search API to find jobs using keywords, company, location, 
     * or some other criteria. It returns a collection of matching jobs. Each 
     * entry can contain much of the information available on the job listing.
     * 
     *   http://developer.linkedin.com/docs/DOC-1321  
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function searchJobs($options = '')
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->jobsSearch(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/job-search' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * General people search function.
     * 
     * Takes a string of parameters as input and requests profile data from the 
     * Linkedin People Search API.  See the official documentation for $options
     * querystring formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1191 
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     *            	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function searchPeople($options = NULL)
    {
        // check passed data
        if (!is_null($options) && !is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->search(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people-search' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Set the application_key property.
     * 
     * @param str $key 
     *    The application key.
     *    
     * @since 1.0.0             	 
     */
    public function setApplicationKey($key)
    {
        $this->application_key = $key;
    }
    
    /**
     * Set the application_secret property.
     * 
     * @param str $secret 
     *    The application secret.
     *    
     * @since 1.0.0             	 
     */
    public function setApplicationSecret($secret)
    {
        $this->application_secret = $secret;
    }
    
    /**
     * Set the callback property.
     * 
     * @param str $url 
     *    The callback url.
     *    
     * @since 1.0.0             	 
     */
    public function setCallbackUrl($url)
    {
        $this->callback = $url;
    }
    
    /**
     * This sets the group settings of the specified group.
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $gid
     * 		The group id.
     * @param str $xml
     * 		The group settings to set. The settings are:
     * 		  -<show-group-logo-in-profle>
     * 		  -<contact-email>
     * 		  -<email-digest-frequency>
     * 		  -<email-annoucements-from-managers>
     * 		  -<allow-messages-from-members>
     * 		  -<email-for-every-new-post>
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function setGroupSettings($gid, $xml)
    {
        if (!is_string($gid)) {
            throw new LinkedInException('LinkedIn->setGroupSettings(): bad data passed, $token_access should be in array format.');
        }
        if (!is_string($xml)) {
            throw new LinkedInException('LinkedIn->setGroupSettings(): bad data passed, $token_access should be in array format.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/group-memberships/' . trim($gid);
        $response = $this->fetch('PUT', $query, $xml);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Used to set the success component of the standard method response, based on
     * whether a passed LinkedIn response object has the required http_code or not 
     * and returns an appropriate LinkedIn object.
     * 
     * @param var $http_code_required
     * 		The required http response from LinkedIn, passed in either as an integer, 
     * 		or an array of integers representing the expected values.	 
     * @param arr $response 
     *    An array containing a LinkedIn response.
     * 
     * @return boolean
     * 	  TRUE or FALSE depending on if the passed LinkedIn response matches the expected response.
     * 	  
     * @since 3.3.0   	 
     */
    private function setResponse($http_code_required, $response)
    {
        // check passed data
        if (is_array($http_code_required)) {
            foreach ($http_code_required as $http_code) {
                if (!is_int($value)) {
                    throw new LinkedInException('LinkedIn->setResponse(): $http_code_required must be an integer or an array of integer values');
                }
            }
        } else {
            if (!is_int($http_code_required)) {
                throw new LinkedInException('LinkedIn->setResponse(): $http_code_required must be an integer or an array of integer values');
            } else {
                $http_code_required = array(
                    $http_code_required
                );
            }
        }
        if (!is_array($response)) {
            throw new LinkedInException('LinkedIn->setResponse(): $response must be an array');
        }
        
        // check for a match
        if (array_key_exists('http_code', $response['info'])) {
            if (in_array($response['info']['http_code'], $http_code_required)) {
                // response found
                $response['success'] = TRUE;
            } else {
                // response not found
                $response['success'] = FALSE;
                $response['error']   = 'HTTP response from LinkedIn end-point was not code ' . implode(', ', $http_code_required);
            }
        } else {
            // response not found
            $response['success'] = FALSE;
            $response['error']   = 'No HTTP response from LinkedIn end-point.';
        }
        return $response;
    }
    
    /**
     * Set the response_format property.
     * 
     * @param str $format 
     *    [OPTIONAL] The response format to specify to LinkedIn.
     *    
     * @since 2.1.0             	 
     */
    public function setResponseFormat($format = self::_DEFAULT_RESPONSE_FORMAT)
    {
        $this->response_format = $format;
    }
    
    /**
     * Set the token property.
     * 
     * @param arr $token 
     *    The LinkedIn OAuth token.
     *    
     * @since 1.0.0   	 
     */
    public function setToken($token)
    {
        // check passed data
        if (!is_null($token) && !is_array($token)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->setToken(): bad data passed, $token_access should be in array format.');
        }
        
        // set token
        $this->token = $token;
    }
    
    /**
     * [DEPRECATED] Set the token_access property. Will be removed in v4 of the
     * class.
     * 
     * @param arr $token_access 
     *    The LinkedIn OAuth access token.
     *    
     * @since 1.0.0
     * 
     * @deprecated 
     * 
     * @see #setToken(str)              	 
     */
    public function setTokenAccess($token_access)
    {
        $this->setToken($token_access);
    }
    
    /**
     * Post a share. 
     * 
     * Create a new or reshare another user's shared content. Full details from 
     * LinkedIn on this functionality can be found here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1212 
     * 
     *   $action values: ('new', 'reshare')      	 
     *   $content format: 
     *     $action = 'new'; $content => ('comment' => 'xxx', 'title' => 'xxx', 'submitted-url' => 'xxx', 'submitted-image-url' => 'xxx', 'description' => 'xxx')
     *     $action = 'reshare'; $content => ('comment' => 'xxx', 'id' => 'xxx')	 
     * 
     * @param str $action
     *    The sharing action to perform.	 
     * @param str $content
     *    The share content.
     * @param bool $private 
     *    [OPTIONAL] Should we restrict this shared item to connections only?	 
     * @param bool $twitter 
     *    [OPTIONAL] Also update the teathered Twitter account.
     *    	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.0.0            	 
     */
    public function share($action, $content, $private = TRUE, $twitter = FALSE)
    {
        // check the status itself
        if (!empty($action) && !empty($content)) {
            /**
             * Status is not empty, wrap a cleaned version of it in xml.  Status
             * rules:
             * 
             * 1) Comments are 700 chars max (if this changes, change _SHARE_COMMENT_LENGTH constant)
             * 2) Content/title 200 chars max (if this changes, change _SHARE_CONTENT_TITLE_LENGTH constant)
             * 3) Content/description 400 chars max (if this changes, change _SHARE_CONTENT_DESC_LENGTH constant)
             * 4a) New shares must contain a comment and/or (content/title and content/submitted-url)
             * 4b) Reshared content must contain an attribution id.
             * 4c) Reshared content must contain actual content, not just a comment.             
             * 5) No HTML permitted in comment, content/title, content/description.
             */
            
            // prepare the share data per the rules above
            $share_flag  = FALSE;
            $content_xml = NULL;
            switch ($action) {
                case 'new':
                    // share can be an article
                    if (array_key_exists('title', $content) && array_key_exists('submitted-url', $content)) {
                        // we have shared content, format it as needed per rules above
                        $content_title = trim(htmlspecialchars(strip_tags(stripslashes($content['title']))));
                        if (strlen($content_title) > self::_SHARE_CONTENT_TITLE_LENGTH) {
                            throw new LinkedInException('LinkedIn->share(): title length is too long - max length is ' . self::_SHARE_CONTENT_TITLE_LENGTH . ' characters.');
                        }
                        $content_xml .= '<content>
                               <title>' . $content_title . '</title>
                               <submitted-url>' . trim(htmlspecialchars($content['submitted-url'])) . '</submitted-url>';
                        if (array_key_exists('submitted-image-url', $content)) {
                            $content_xml .= '<submitted-image-url>' . trim(htmlspecialchars($content['submitted-image-url'])) . '</submitted-image-url>';
                        }
                        if (array_key_exists('description', $content)) {
                            $content_desc = trim(htmlspecialchars(strip_tags(stripslashes($content['description']))));
                            if (strlen($content_desc) > self::_SHARE_CONTENT_DESC_LENGTH) {
                                throw new LinkedInException('LinkedIn->share(): description length is too long - max length is ' . self::_SHARE_CONTENT_DESC_LENGTH . ' characters.');
                            }
                            $content_xml .= '<description>' . $content_desc . '</description>';
                        }
                        $content_xml .= '</content>';
                        
                        $share_flag = TRUE;
                    }
                    
                    // share can be just a comment
                    if (array_key_exists('comment', $content)) {
                        // comment located
                        $comment = htmlspecialchars(trim(strip_tags(stripslashes($content['comment']))));
                        if (strlen($comment) > self::_SHARE_COMMENT_LENGTH) {
                            throw new LinkedInException('LinkedIn->share(): comment length is too long - max length is ' . self::_SHARE_COMMENT_LENGTH . ' characters.');
                        }
                        $content_xml .= '<comment>' . $comment . '</comment>';
                        
                        $share_flag = TRUE;
                    }
                    break;
                case 'reshare':
                    if (array_key_exists('id', $content)) {
                        // put together the re-share attribution XML
                        $content_xml .= '<attribution>
                               <share>
                                 <id>' . trim($content['id']) . '</id>
                               </share>
                             </attribution>';
                        
                        // optional additional comment
                        if (array_key_exists('comment', $content)) {
                            // comment located
                            $comment = htmlspecialchars(trim(strip_tags(stripslashes($content['comment']))));
                            if (strlen($comment) > self::_SHARE_COMMENT_LENGTH) {
                                throw new LinkedInException('LinkedIn->share(): comment length is too long - max length is ' . self::_SHARE_COMMENT_LENGTH . ' characters.');
                            }
                            $content_xml .= '<comment>' . $comment . '</comment>';
                        }
                        
                        $share_flag = TRUE;
                    }
                    break;
                default:
                    // bad action passed
                    throw new LinkedInException('LinkedIn->share(): share action is an invalid value, must be one of: share, reshare.');
                    break;
            }
            
            // should we proceed?
            if ($share_flag) {
                // put all of the xml together
                $visibility = ($private) ? 'connections-only' : 'anyone';
                $data       = '<?xml version="1.0" encoding="UTF-8"?>
                       <share>
                         ' . $content_xml . '
                         <visibility>
                           <code>' . $visibility . '</code>
                         </visibility>
                       </share>';
                
                // create the proper url
                $share_url = self::_URL_API . '/v1/people/~/shares';
                if ($twitter) {
                    // update twitter as well
                    $share_url .= '?twitter-post=true';
                }
                
                // send request
                $response = $this->fetch('POST', $share_url, $data);
            } else {
                // data contraints/rules not met, raise an exception
                throw new LinkedInException('LinkedIn->share(): sharing data constraints not met; check that you have supplied valid content and combinations of content to share.');
            }
        } else {
            // data missing, raise an exception
            throw new LinkedInException('LinkedIn->share(): sharing action or shared content is missing.');
        }
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Network statistics.
     * 
     * General network statistics retrieval function, returns the number of connections, 
     * second-connections an authenticated user has. More information here:
     * 
     *   http://developer.linkedin.com/docs/DOC-1006
     * 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.0.0   	 
     */
    public function statistics()
    {
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/network/network-stats';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Companies you may want to follow.
     * 
     * Returns a list of companies the current user may want to follow, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1324   
     * 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function suggestedCompanies()
    {
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/suggestions/to-follow/companies';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Retrieves suggested groups for the user, per:
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.2.0   	 
     */
    public function suggestedGroups()
    {
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/suggestions/groups:(id,name,is-open-to-non-members)';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Jobs you may be interested in.
     * 
     * Returns a list of jobs the current user may be interested in, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1323   
     * 
     * @param str $options
     *    [OPTIONAL] Data retrieval options.	
     *          	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function suggestedJobs($options = ':(jobs)')
    {
        // check passed data
        if (!is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->suggestedJobs(): bad data passed, $options must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/suggestions/job-suggestions' . trim($options);
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Unbookmark a job.
     * 
     * Calling this method causes the current user to remove a bookmark for the 
     * specified job:
     * 
     *   http://developer.linkedin.com/docs/DOC-1323   
     * 
     * @param str $jid
     *    Job ID you want to unbookmark.
     *            	
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function unbookmarkJob($jid)
    {
        // check passed data
        if (!is_string($jid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->unbookmarkJob(): bad data passed, $jid must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/job-bookmarks/' . trim($jid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Unfollow a company.
     * 
     * Calling this method causes the current user to stop following the specified 
     * company, per:
     * 
     *   http://developer.linkedin.com/docs/DOC-1324   
     * 
     * @param str $cid
     *    Company ID you want to unfollow.	
     *         	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.1.0   	 
     */
    public function unfollowCompany($cid)
    {
        // check passed data
        if (!is_string($cid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->unfollowCompany(): bad data passed, $cid must be of string value.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/following/companies/id=' . trim($cid);
        $response = $this->fetch('DELETE', $query);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Unfollows the specified post.
     * 
     *   https://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     *
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.3.0   	 
     */
    public function unfollowPost($pid)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->unfollowPost(): bad data passed, $pid must be of type string');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
				     <is-following>false</is-following>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/relation-to-viewer/is-following';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * [DEPRECATED] Unlike a network update.
     *     
     * Unlike another user's network update:
     * 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid 
     *    The LinkedIn update ID.
     *                     	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 2.1.0 
     * 
     * @deprecated 
     * 
     * @see #unlikeUpdate(str)                                    
     */
    public function unlike($uid)
    {
        return $this->unlikeUpdate($uid);
    }
    
    /**
     * Unlikes the specified post, per:
     * 
     *   http://developer.linkedin.com/documents/groups-api
     * 
     * @param str $pid
     * 		The post id.
     * 
     * @return arr
     * 		Array containing retrieval success, LinkedIn response.
     * 		
     * @since 3.3.0   	 
     */
    public function unlikePost($pid)
    {
        if (!is_string($pid)) {
            throw new LinkedInException('LinkedIn->unlikePost(): bad data passed, $pid must be of type string');
        }
        
        // construct the XML
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		         <is-liked>false</is-liked>';
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/posts/' . trim($pid) . '/relation-to-viewer/is-liked';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 204 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(204, $response);
    }
    
    /**
     * Unlike a network update.
     *     
     * Unlike another user's network update:
     * 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid 
     *    The LinkedIn update ID.
     *                     	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.3.0 
     */
    public function unlikeUpdate($uid)
    {
        // check passed data
        if (!is_string($uid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->unlikeUpdate(): bad data passed, $uid must be of type string.');
        }
        
        // construct the xml data
        $data = '<?xml version="1.0" encoding="UTF-8"?>
		         <is-liked>false</is-liked>';
        
        // send request
        $query    = self::_URL_API . '/v1/people/~/network/updates/key=' . $uid . '/is-liked';
        $response = $this->fetch('PUT', $query, $data);
        
        /**
         * Check for successful request (a 201 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(201, $response);
    }
    
    /**
     * Network update comment retrieval.
     *     
     * Return all comments associated with a given network update:
     * 	 
     *   http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *   
     * @since 3.3.0                         
     */
    public function updateComments($uid)
    {
        // check passed data
        if (!is_string($uid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->updateComments(): bad data passed, $uid must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/network/updates/key=' . $uid . '/update-comments';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Retrieve network update likes.
     *    
     * Return all likes associated with a given network update:
     * 
     * http://developer.linkedin.com/docs/DOC-1043
     * 
     * @param str $uid
     *    The LinkedIn update ID.
     *                     	 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 3.3.0                        
     */
    public function updateLikes($uid)
    {
        // check passed data
        if (!is_string($uid)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->updateLikes(): bad data passed, $uid must be of type string.');
        }
        
        // construct and send the request
        $query    = self::_URL_API . '/v1/people/~/network/updates/key=' . $uid . '/likes';
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Post network update.
     * 
     * Update the user's Linkedin network status. Full details from LinkedIn 
     * on this functionality can be found here: 
     * 
     *   http://developer.linkedin.com/docs/DOC-1009
     *   http://developer.linkedin.com/docs/DOC-1009#comment-1077 
     * 
     * @param str $update
     *    The network update.	 
     * 
     * @return arr 
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 1.0.0             	 
     */
    public function updateNetwork($update)
    {
        // check passed data
        if (!is_string($update)) {
            // nothing/non-string passed, raise an exception
            throw new LinkedInException('LinkedIn->updateNetwork(): bad data passed, $update must be a non-zero length string.');
        }
        
        /**
         * Network update is not empty, wrap a cleaned version of it in xml.  
         * Network update rules:
         * 
         * 1) No HTML permitted except those found in _NETWORK_HTML constant
         * 2) Update cannot be longer than 140 characters.     
         */
        // get the user data
        $response = self::profile('~:(first-name,last-name,site-standard-profile-request)');
        if ($response['success'] === TRUE) {
            /** 
             * We are converting response to usable data.  I'd use SimpleXML here, but
             * to keep the class self-contained, we will use a portable XML parsing
             * routine, self::xmlToArray.       
             */
            $person = self::xmlToArray($response['linkedin']);
            if ($person === FALSE) {
                // bad xml data
                throw new LinkedInException('LinkedIn->updateNetwork(): LinkedIn returned bad XML data.');
            }
            $fields = $person['person']['children'];
            
            // prepare user data
            $first_name  = trim($fields['first-name']['content']);
            $last_name   = trim($fields['last-name']['content']);
            $profile_url = trim($fields['site-standard-profile-request']['children']['url']['content']);
            
            // create the network update 
            $update = trim(htmlspecialchars(strip_tags($update, self::_NETWORK_HTML)));
            if (strlen($update) > self::_NETWORK_LENGTH) {
                throw new LinkedInException('LinkedIn->share(): update length is too long - max length is ' . self::_NETWORK_LENGTH . ' characters.');
            }
            $user = htmlspecialchars('<a href="' . $profile_url . '">' . $first_name . ' ' . $last_name . '</a>');
            $data = '<activity locale="en_US">
    				       <content-type>linkedin-html</content-type>
    				       <body>' . $user . ' ' . $update . '</body>
    				     </activity>';
            
            // send request
            $query    = self::_URL_API . '/v1/people/~/person-activities';
            $response = $this->fetch('POST', $query, $data);
            
            /**
             * Check for successful request (a 201 response from LinkedIn server) 
             * per the documentation linked in method comments above.
             */
            return $this->setResponse(201, $response);
        } else {
            // profile retrieval failed
            throw new LinkedInException('LinkedIn->updateNetwork(): profile data could not be retrieved.');
        }
    }
    
    /**
     * General network update retrieval function.
     * 
     * Takes a string of parameters as input and requests update-related data 
     * from the Linkedin Network Updates API. See the official documentation for 
     * $options parameter formatting:
     * 
     *   http://developer.linkedin.com/docs/DOC-1006
     * 
     * For getting more comments, likes, etc, see here:
     * 
     *   http://developer.linkedin.com/docs/DOC-1043         	 
     * 
     * @param str $options 
     *    [OPTIONAL] Data retrieval options.
     * @param str $id 
     *    [OPTIONAL] The LinkedIn ID to restrict the updates for.
     *               	 
     * @return arr
     *    Array containing retrieval success, LinkedIn response.
     *    
     * @since 1.0.0   	 
     */
    public function updates($options = NULL, $id = NULL)
    {
        // check passed data
        if (!is_null($options) && !is_string($options)) {
            // bad data passed
            throw new LinkedInException('LinkedIn->updates(): bad data passed, $options must be of type string.');
        }
        if (!is_null($id) && (!is_string($id) || !self::isId($id))) {
            // bad data passed
            throw new LinkedInException('LinkedIn->updates(): bad data passed, $id must be of type string.');
        }
        
        // construct and send the request
        if (!is_null($id)) {
            $query = self::_URL_API . '/v1/people/' . $id . '/network/updates' . trim($options);
        } else {
            $query = self::_URL_API . '/v1/people/~/network/updates' . trim($options);
        }
        $response = $this->fetch('GET', $query);
        
        /**
         * Check for successful request (a 200 response from LinkedIn server) 
         * per the documentation linked in method comments above.
         */
        return $this->setResponse(200, $response);
    }
    
    /**
     * Converts passed XML data to an array.
     * 
     * @param str $xml 
     *    The XML to convert to an array.
     *            	 
     * @return arr 
     *    Array containing the XML data.     
     * @return bool 
     *    FALSE if passed data cannot be parsed to an array.
     *    
     * @since 1.0.0           	 
     */
    public static function xmlToArray($xml)
    {
        // check passed data
        if (!is_string($xml)) {
            // bad data possed
            throw new LinkedInException('LinkedIn->xmlToArray(): bad data passed, $xml must be a non-zero length string.');
        }
        
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        if (xml_parse_into_struct($parser, $xml, $tags)) {
            $elements = array();
            $stack    = array();
            foreach ($tags as $tag) {
                $index = count($elements);
                if ($tag['type'] == 'complete' || $tag['type'] == 'open') {
                    $elements[$tag['tag']]               = array();
                    $elements[$tag['tag']]['attributes'] = (array_key_exists('attributes', $tag)) ? $tag['attributes'] : NULL;
                    $elements[$tag['tag']]['content']    = (array_key_exists('value', $tag)) ? $tag['value'] : NULL;
                    if ($tag['type'] == 'open') {
                        $elements[$tag['tag']]['children'] = array();
                        $stack[count($stack)] =& $elements;
                        $elements =& $elements[$tag['tag']]['children'];
                    }
                }
                if ($tag['type'] == 'close') {
                    $elements =& $stack[count($stack) - 1];
                    unset($stack[count($stack) - 1]);
                }
            }
            $return_data = $elements;
        } else {
            // not valid xml data
            $return_data = FALSE;
        }
        xml_parser_free($parser);
        return $return_data;
    }
}




/**
 * This file is used in conjunction with the 'LinkedIn' class, demonstrating 
 * the basic functionality and usage of the library.
 * 
 * COPYRIGHT:
 *   
 * Copyright (C) 2011, fiftyMission Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.  
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
 * IN THE SOFTWARE.  
 *
 * SOURCE CODE LOCATION:
 * 
 *   http://code.google.com/p/simple-linkedinphp/
 *    
 * REQUIREMENTS:
 * 
 * 1. You must have cURL installed on the server and available to PHP.
 * 2. You must be running PHP 5+.
 * 3. You must have the Simple-LinkedIn library installed on the server.   
 *  
 * QUICK START:
 * 
 * There are two files needed to enable LinkedIn API functionality from PHP; the
 * stand-alone OAuth library, and the Simple-LinkedIn library. The latest 
 * version of the stand-alone OAuth library can be found on Google Code:
 * 
 *   http://code.google.com/p/oauth/
 * 
 * The latest versions of the Simple-LinkedIn library and these demonstation 
 * scripts can be found here:
 * 
 *   http://code.google.com/p/simple-linkedinphp/
 *   
 * Install these two files on your server in a location that is accessible to 
 * these demo scripts. Make sure to change the file permissions such that your 
 * web server can read the files.
 * 
 * Next, make sure the path to the LinkedIn class below is correct.
 * 
 * Next, insert your application API key and secret into the $API_CONFIG 
 * variable below.  
 * 
 * Finally, read and follow the 'Quick Start' guidelines located in the comments
 * of the Simple-LinkedIn library file, and the documentation located on the 
 * Google Code page:
 * 
 *   http://code.google.com/p/simple-linkedinphp/wiki/QuickStart     
 *
 * @version 3.3.0 - December 10, 2011
 * @author Paul Mennega <paul@fiftymission.net>
 * @copyright Copyright 2011, fiftyMission Inc. 
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License 




 * Session existance check.
 * 
 * Helper function that checks to see that we have a 'set' $_SESSION that we can
 * use for the demo.   
function oauth_session_exists() {
  if((is_array($_SESSION)) && (array_key_exists('oauth', $_SESSION))) {
    return TRUE;
  } else {
    return FALSE;
  }
}

try {
  // include the LinkedIn class
  require_once('core/oauth/linkedin/oauth_linkedin.php');
  
  // config variables
  $API_CONFIG = array(
    	'appKey'    => constant('__LINKEDIN_API_KEY__'),
        'appSecret' => constant('__LINKEDIN_APP_SECRET_ID__')
  );
  
  // constants
  define('DEMO_GROUP', '4010474');
  define('DEMO_GROUP_NAME', 'Simple LI Demo');
  define('PORT_HTTP', '80');
  define('PORT_HTTP_SSL', '443');

  // start the session
  if(!session_start()) {
    throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
  }
  
  // set index
  $_REQUEST[LINKEDIN::_GET_TYPE] = (isset($_REQUEST[LINKEDIN::_GET_TYPE])) ? $_REQUEST[LINKEDIN::_GET_TYPE] : '';
  switch($_REQUEST[LINKEDIN::_GET_TYPE]) {

    case 'initiate':
      // Handle user initiated LinkedIn connection, create the LinkedIn object.
      // check for the correct http protocol (i.e. is this script being served via http or https)
      if($_SERVER['HTTPS'] == 'on') {
        $protocol = 'https';
      } else {
        $protocol = 'http';
      }
      
      // set the callback url
      $API_CONFIG['callbackUrl'] = $protocol . '://' . $_SERVER['SERVER_NAME'] . ((($_SERVER['SERVER_PORT'] != PORT_HTTP) || ($_SERVER['SERVER_PORT'] != PORT_HTTP_SSL)) ? ':' . $_SERVER['SERVER_PORT'] : '') . $_SERVER['PHP_SELF'] . '?' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';
	  
      $OBJ_linkedin = new LinkedIn($API_CONFIG);
      
      // check for response from LinkedIn
      $_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
      if(!$_GET[LINKEDIN::_GET_RESPONSE]) {
        // LinkedIn hasn't sent us a response, the user is initiating the connection
        
        // send a request for a LinkedIn access token
        $response = $OBJ_linkedin->retrieveTokenRequest();
        if($response['success'] === TRUE) {
          // store the request token
          $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
          
          // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
          header('Location: ' . LINKEDIN::_URL_AUTHENTICATE . $response['linkedin']['oauth_token']);
        } else {
          // bad token request
          echo "Request token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
        }
      } else {
        // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
        $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
        if($response['success'] === TRUE) {
          // the request went through without an error, gather user's 'access' tokens
          $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
          
          // set the user as authorized for future quick reference
          $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
            
          // redirect the user back to the demo page
          header('Location: ' . $_SERVER['PHP_SELF']);
        } else {
          // bad token access
          echo "Access token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
        }
      }
      break;

    case 'revoke':
      // Handle authorization revocation.
                    
      // check the session
      if(!oauth_session_exists()) {
        throw new LinkedInException('This script requires session support, which doesn\'t appear to be working correctly.');
      }
      
      $OBJ_linkedin = new LinkedIn($API_CONFIG);
      $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
      $response = $OBJ_linkedin->revoke();
      if($response['success'] === TRUE) {
        // revocation successful, clear session
        session_unset();
        $_SESSION = array();
        if(session_destroy()) {
          // session destroyed
          header('Location: ' . $_SERVER['PHP_SELF']);
        } else {
          // session not destroyed
          echo "Error clearing user's session";
        }
      } else {
        // revocation failed
        echo "Error revoking user's token:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
      }
      break;
    default:
      // nothing being passed back, display demo page
      
      // check PHP version
      if(version_compare(PHP_VERSION, '5.0.0', '<')) {
        throw new LinkedInException('You must be running version 5.x or greater of PHP to use this library.'); 
      } 
      
      // check for cURL
      if(extension_loaded('curl')) {
        $curl_version = curl_version();
        $curl_version = $curl_version['version'];
      } else {
        throw new LinkedInException('You must load the cURL extension to use this library.'); 
      }
      ?>
      <!DOCTYPE html>
      <html lang="en">
        <head>
          <title>Simple-LinkedIn Demo</title>
          
          <meta charset="utf-8" />
          <meta name="viewport" content="width=device-width" />
          <meta name="description" content="A demonstration page for the Simple-LinkedIn PHP class." />
          <meta name="keywords" content="simple-linkedin,php,linkedin,api,class,library" />
          
          <style type="text/css">
            body {font-family: Arial, Helvetica, sans-serif;}
            footer {margin-top: 2em; text-align: center;}
            pre {font-family: Courier, monospace;}
          </style>
        </head>
        <body>
          <h1><a href="<?php echo $_SERVER['PHP_SELF'];?>">Simple-LinkedIn Demo</a></h1>
          
          <p>Copyright 2010 - 2011, Paul Mennega &lt;<a href="mailto:paul@fiftymission.net">paul@fiftymission.net</a>&gt;, fiftyMission Inc.</p>
          
          <p>Released under the MIT License - <a href="http://www.opensource.org/licenses/mit-license.php">http://www.opensource.org/licenses/mit-license.php</a></p>
          
          <p>Full source code for both the Simple-LinkedIn class and this demo script can be found at:</p>
          
          <ul>
            <li><a href="http://code.google.com/p/simple-linkedinphp/">http://code.google.com/p/simple-linkedinphp/</a></li>
          </ul>          

          <hr />
          
          <p style="font-weight: bold;">Demo using: Simple-LinkedIn v<?php echo LINKEDIN::_VERSION;?>, cURL v<?php echo $curl_version;?>, PHP v<?php echo phpversion();?></p>
          
          <ul>
            <li>Please note: 
              <ul>
                <li>The Simple-LinkedIn class requires PHP 5+ and cURL</li>
              </ul>
            </li>
          </ul>
          
          <hr />
          
          <?php
          $_SESSION['oauth']['linkedin']['authorized'] = (isset($_SESSION['oauth']['linkedin']['authorized'])) ? $_SESSION['oauth']['linkedin']['authorized'] : FALSE;
          if($_SESSION['oauth']['linkedin']['authorized'] === TRUE) {
            $OBJ_linkedin = new LinkedIn($API_CONFIG);
            $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
                $OBJ_linkedin->setResponseFormat(LINKEDIN::_RESPONSE_XML);
            ?>
            <ul>
              <li><a href="#manage">Manage LinkedIn Authorization</a></li>
              <li><a href="#application">Application Information</a></li>
              <li><a href="#profile">Your Profile</a></li>
              <li><a href="network.php">Your Network</a>
                <ul>
                  <li><a href="network.php#networkStats">Stats</a></li>
                  <li><a href="network.php#networkConnections">Your Connections</a></li>
                  <li><a href="network.php#networkInvite">Invite Others to Join your LinkedIn Network</a></li>
                  <li><a href="network.php#networkUpdates">Recent Connection Updates</a></li>
                  <li><a href="network.php#peopleSearch">People Search</a></li>
                </ul>
              </li>
              <li><a href="company.php">Company API</a>
                <ul>
                  <li><a href="company.php#companySpecific">Specific Company</a></li>
                  <li><a href="company.php#companyFollowed">Followed Companies</a></li>
                  <li><a href="company.php#companySuggested">Suggested Companies</a></li>
                  <li><a href="company.php#companySearch">Company Search</a></li>
                </ul>
              </li>
              <li><a href="jobs.php">Jobs API</a>
                <ul>
                  <li><a href="jobs.php#jobsBookmarked">Bookmarked Jobs</a></li>
                  <li><a href="jobs.php#jobsSuggested">Suggested Jobs</a></li>
                  <li><a href="jobs.php#jobsSearch">Jobs Search</a></li>
                </ul>
              </li>
              <li><a href="content.php">Creating / Sharing Content</a>
                <ul>
                  <li><a href="content.php#contentUpdate">Post Network Update</a></li>
                  <li><a href="content.php#contentShare">Post Share</a></li>
                </ul>
              </li>
              <?php
                
                // check if the viewer is a member of the test group
                $response = $OBJ_linkedin->group(DEMO_GROUP, ':(relation-to-viewer:(membership-state))');
              if($response['success'] === TRUE) {
                          $result         = new SimpleXMLElement($response['linkedin']);
                          $membership     = $result->{'relation-to-viewer'}->{'membership-state'}->code;
                          $in_demo_group  = (($membership == 'non-member') || ($membership == 'blocked')) ? FALSE : TRUE;
                      ?>
                        <li><a href="demo/groups.php">Groups API</a>
                        <ul>
                          <li><a href="demo/groups.php#groupsSuggested">Suggested Groups</a></li>
                          <li><a href="demo/groups.php#groupMemberships">Group Memberships</a></li>
                          <li><a href="demo/groups.php#manageGroup">Manage '<?php echo DEMO_GROUP_NAME;?>' Group Membership</a></li>
                          <?php 
                          if($in_demo_group) {
                            ?>
                                  <li><a href="demo/groups.php#groupSettings">Group Settings</a></li>
                                  <li><a href="demo/groups.php#groupPosts">Group Posts</a></li>
                                  <li><a href="demo/groups.php#createPost">Create a Group Post</a></li>
                                        <?php 
                                }
                                ?>
                              </ul>
                            </li>
                            <?php 
                                          } else {
                                            // request failed
                                echo "Error retrieving group membership information: <br /><br />RESPONSE:<br /><br /><pre>" . print_r ($response, TRUE) . "</pre>";
                                          }
                          ?>
            </ul>
            <?php
          } else {
            ?>
            <ul>
              <li><a href="#manage">Manage LinkedIn Authorization</a></li>
            </ul>
            <?php
          }
          ?>
          
          <hr />
          
          <h2 id="manage">Manage LinkedIn Authorization:</h2>
          <?php
          if($_SESSION['oauth']['linkedin']['authorized'] === TRUE) {
            // user is already connected
            ?>
            <form id="linkedin_revoke_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
              <input type="hidden" name="<?php echo LINKEDIN::_GET_TYPE;?>" id="<?php echo LINKEDIN::_GET_TYPE;?>" value="revoke" />
              <input type="submit" value="Revoke Authorization" />
            </form>
            
            <hr />
          
            <h2 id="application">Application Information:</h2>
            
            <ul>
              <li>Application Key: 
                <ul>
                  <li><pre><?php echo $OBJ_linkedin->getApplicationKey();?></pre></li>
                </ul>
              </li>
            </ul>
            
            <hr />
            
            <h2 id="profile">Your Profile:</h2>
            
            <?php
            $response = $OBJ_linkedin->profile('~:(id,first-name,last-name,picture-url)');
            if($response['success'] === TRUE) {
              $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
              echo "<pre>" . print_r($response['linkedin'], TRUE) . "</pre>";
            } else {
              // request failed
              echo "Error retrieving profile information:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response) . "</pre>";
            } 
          } else {
            // user isn't connected
            ?>
            <form id="linkedin_connect_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
              <input type="hidden" name="<?php echo LINKEDIN::_GET_TYPE;?>" id="<?php echo LINKEDIN::_GET_TYPE;?>" value="initiate" />
              <input type="submit" value="Connect to LinkedIn" />
            </form>
            <?php
          }
          ?>
          
          <hr />
          
          <footer>
            <div>Copyright 2010 - 2011, fiftyMission Inc. (Paul Mennega &lt;<a href="mailto:paul@fiftymission.net">paul@fiftymission.net</a>&gt;)</div>
            <div>Released under the MIT License - <a href="http://www.opensource.org/licenses/mit-license.php">http://www.opensource.org/licenses/mit-license.php</a></div>
          </footer>
        </body>
      </html>
      <?php
      break;
  }
} catch(LinkedInException $e) {
  // exception raised
  echo $e->getMessage();
}
die;

*/