<?php
/**
 * CONTACT_IMPORTER_GOOGLE Class
 * This class is used to import google contacts. You will need to get a google oAuth API access.
 * Make sure to add authorised redirect URLs here in the Legacy.Contact.Importer Application.
 * Go to https://code.google.com/apis/console, select the "Google Contact Importer" Application
 * Then click on "Edit Settings" and add the new domain.
 *
 * @package		{CORE}::COMPONENT
 * @subpackage	CONTACT_IMPORTER
 * @throws		SITE_EXCEPTION
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
 
 /*
 	EXAMPLE USE:
	
	* Remember to add the 'Example' url to the list of allowed callback URLS at: 
		- https://code.google.com/apis/console
	
	* Also make sure that the correct API constants are defined in config.php (Site version)
	
	require_once('core/components/contact_importer/contact_importer_google.php');
	$objGoogleContactImporter 	= CONTACT_IMPORTER_GOOGLE::getInstance();
	$arrContacts 				= $objGoogleContactImporter->importContacts();
	new dump($arrContacts);
	new dump($objGoogleContactImporter->getError());
	
 */
class CONTACT_IMPORTER_GOOGLE extends OBJECT_BASE
{
	/**
	 * This is where we'll send the client to get authenticated
	 * 
	 * @var string
	 */
	const CONTACT_IMPORTER_GOOGLE_OAUTH_REQ_URL 	= 'https://accounts.google.com/o/oauth2/auth';
	
	/**
	* This is the google scope url
	*
	* @var string
	*/
	const CONTACT_IMPORTER_GOOGLE_OAUTH_SCOPE_URL	= 'https://www.google.com/m8/feeds/';
	
	/**
	 * This is where we request the curl auth token for curl requests
	 * 
	 * @var string
	 */
	const CONTACT_IMPORTER_GOOGLE_OAUTH_TOKEN_URL 	= 'https://accounts.google.com/o/oauth2/token';
	
	/**
	 * This is API contact import URL
	 * 
	 * @var string
	 */
	const CONTACT_IMPORTER_GOOGLE_CONTACT_IMPORT_URL = 'https://www.google.com/m8/feeds/contacts/default/full';
	
	
	/**
	 * Main Accessor method. this is the method that will return the
	 * 
	 * @access	public, static, final
	 * @package	CONTACT_IMPORTER
	 * @throws	SITE_EXCEPTION
	 * @param	none
	 * @return 	array 
	 */
	public final function importContacts()
	{
		$this->Application 			= APPLICATION::getInstance();
		
		// Set some return variables
		$this->setGoogleContacts(array());
		$this->setError(NULL);
		$this->setCanContinue(TRUE);
		
		
		// Validation
		if (FALSE === function_exists('curl_setopt'))
		{
			//throw new Exception('The CURL Extension is required for ' . __CLASS__ . ' to function.');	
			$this->setError('The CURL Extension is required for ' . __CLASS__ . ' to function.');
			$this->setCanContinue(false);
		}
		
		if ($this->getCanContinue())
		{
			// Set some variables
			$objRedirectUrl = new URL(((false === empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . URL::getCanonicalUrl()); 
			$objRedirectUrl->clearAttribute();
			$objRedirectUrl->deleteAttribute(session_name());
			
			// This is the google client Id, see https://code.google.com/apis/console
			$this->setGoogleApiClientId(constant('__GOOGLE_CLIENT_ID__'));
			// Client API Secret Key, see https://code.google.com/apis/console
			$this->setGoogleApiSecretId(constant('__GOOGLE_CLIENT_SECRET_KEY__'));
			// This is the redirect URL, we'll use the current one
			$this->setRedirectUrl($objRedirectUrl->build());
			// This is the maximum amount of results to pull, i set it to a high amount
			$this->setMaxResults(500);					
			
			// Step 1, redirect the user to the oAuth segment	
			if (strlen($this->Application->getForm()->getOrPost('code')) <= 0)
			{	
				$objOauthGoogleRedirectUrl = new URL(self::CONTACT_IMPORTER_GOOGLE_OAUTH_REQ_URL);
				$objOauthGoogleRedirectUrl->setAttribute('scope', self::CONTACT_IMPORTER_GOOGLE_OAUTH_SCOPE_URL);
				$objOauthGoogleRedirectUrl->setAttribute('client_id', $this->getGoogleApiClientId());
				$objOauthGoogleRedirectUrl->setAttribute('redirect_uri', $this->getRedirectUrl());
				$objOauthGoogleRedirectUrl->setAttribute('response_type', 'code');
				$objOauthGoogleRedirectUrl->forward();
			}
			else
			{	
				// Lets ping google for a oAuth access token
				$strPostRequest = '';
				$arrPostRequest = array(
					'code'			=>	urlencode($this->Application->getForm()->getOrPost('code')),
					'client_id'		=>  urlencode($this->getGoogleApiClientId()),
					'client_secret'	=>  urlencode($this->getGoogleApiSecretId()),
					'redirect_uri'	=>  urlencode($this->getRedirectUrl()),
					'grant_type'	=>  urlencode('authorization_code')
				);
				
				while(list($strPostkey, $strPostData) = each($arrPostRequest)) 
				{
					$strPostRequest .= $strPostkey. '=' . $strPostData . '&';
				}
				$strPostRequest = rtrim($strPostRequest,'&');
				
				// Begin the CURL Request to get the access token
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, self::CONTACT_IMPORTER_GOOGLE_OAUTH_TOKEN_URL);
				curl_setopt($curl, CURLOPT_POST, 5);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $strPostRequest);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				$strJsonResults = curl_exec($curl);
				curl_close($curl);
				
				// OK, now we have an access token
				$objResponse =  json_decode($strJsonResults);
				$this->setAccessRequestToken($objResponse->access_token);
				
				// Now, the fun part, lets get the user's contacts!
				$objContactApiRequestUrl = new URL(self::CONTACT_IMPORTER_GOOGLE_CONTACT_IMPORT_URL);
				$objContactApiRequestUrl->setAttribute('max-results', $this->getMaxResults());
				$objContactApiRequestUrl->setAttribute('oauth_token', $this->getAccessRequestToken());
				
				$curl 			= curl_init();
				$strUserAgent 	= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
				
				curl_setopt($curl,CURLOPT_URL, $objContactApiRequestUrl->build());	//The URL to fetch. This can also be set when initializing a session with curl_init().
				curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);	//TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
				curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);	//The number of seconds to wait while trying to connect.	
				
				curl_setopt($curl, CURLOPT_USERAGENT, $strUserAgent);	//The contents of the "User-Agent: " header to be used in a HTTP request.
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);	//To follow any "Location: " header that the server sends as part of the HTTP header.
				curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);	//To automatically set the Referer: field in requests where it follows a Location: redirect.
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);	//The maximum number of seconds to allow cURL functions to execute.
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);	//To stop cURL from verifying the peer's certificate.
				
				$objXmlResponse = curl_exec($curl);
				curl_close($curl);
				
				// Lets check for errors....
				if (
					(strlen(stristr($objXmlResponse, 'Authorization required')) > 0) ||
					(strlen(stristr($objXmlResponse, 'Error')) > 0) ||
					(strlen(stristr($objXmlResponse, '401. That\'s an error.')) > 0)
				) {
					$this->setError('Session Expired or isn\'t valid. Please try reloading the page.');
					$this->setCanContinue(false);
				}
				
				// Otherwise, we have the contacts, lets parse the XML and return the contacts
				if ($this->getCanContinue())
				{
					$objContactXml =  new SimpleXMLElement($objXmlResponse);
					$objContactXml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
					$objContactResult = $objContactXml->xpath('//gd:email');
					
					foreach ($objContactResult as $objEmailContact) 
					{
						$this->addToGoogleContacts((string) $objEmailContact->attributes()->address[0]);
					}
				}
			}	
		}
		
		return ((array) $this->getGoogleContacts());
	}

	/**
	 * This method returns a list of "Friends" by importing a user's gmail contacts and location mutual friends
	 * 
	 * @access	public final
	 * @package	CONTACT_IMPORTER
	 * @throws	SITE_EXCEPTION
	 * @param	none
	 * @return 	array
	 */
	 public final function getMutualFriends()
	 {
		 $Application 				= APPLICATION::getInstance();
		 $objDatabase				= DATABASE::getInstance();
		 $arrContacts 				= $this->getGoogleContacts();
		 $arrMutualFriends 			= array();
		 
		 // 1. Get the user's contact list
		 if (TRUE === empty($arrContacts))
		 {
		 	$arrContacts = $this->importContacts();
		 }
		 
		 $lstContacts = '';
		 array_walk($arrContacts, function($strContactEmail,$intIndex) use(&$arrContacts, &$objDatabase, &$lstContacts){ 
			 $lstContacts .= "'" . $objDatabase->escape($strContactEmail) . "',";
		});
		$lstContacts = rtrim($lstContacts, ',');
		 
		 // 2. Converge with our current user base.
		 if (FALSE === empty($lstContacts))
		 {
			$arrMutualFriends = SITE_USERS::getObjectClassView(array(
				'columns' 		=> 'a.*',
				'filter'		=> array(
					'email'		=> "(" . $lstContacts . ")"
				),
				'operator'		=> array('IN'),
				'escapeData'	=> false
			));	 
		 }

		 return ($arrMutualFriends); 
	 }
	
	/**
	 * Override method: This method is called after the getInstance method has been applied
	 * 
	 * @access	protected
	 * @package	CONTACT_IMPORTER
	 * @throws	SITE_EXCEPTION
	 * @param	none
	 * @return 	void
	 */
	protected final function onBefore_GetInstance()
	{
	}
}
