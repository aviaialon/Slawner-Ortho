<?php
	/**
	 * GITHUB OAUTH_AUTHENTICATION Administration Class
	 * This class manages oauth authentication 
	 * Documentation can be found here: http://developer.github.com/v3/oauth
	 *
	 * @package		{__APPLICATION_CLASS_PATH__}::OAUTH::GITHUB
	 * @subpackage	GITHUB
	 * @extends		OAUTH_AUTHENTICATION
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2012 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	

class OAUTH_GITHUB extends OAUTH_AUTHENTICATION 
{
	
	const API_AUTHORIZATION_REQUEST_URL = 'https://github.com/login/oauth/authorize';
	const API_TOKEN_REQUEST_URL 		= 'https://github.com/login/oauth/access_token';  
	const API_REST_SERVER_URL 			= 'https://api.github.com';  
	
	const OAUTH_GITHUB_STATUS_SUCCESS 	= 1;
	const OAUTH_GITHUB_STATUS_CANCEL 	= 2;
	const OAUTH_GITHUB_STATUS_ERROR 	= 3;
		
	
	protected static 	$objOauthParent 	= NULL;	
	private static 		$blnIsInstace 	 	= false;
	protected static	$arrError 			= array
	(
		'code'		=>	NULL,
		'message'	=>	NULL,
		'data'		=>	NULL
	);
	
	protected static	$arrErrorMessages	= array
	(
		'bad_verification_code'		=> 'The verification code is expired or invalid.',
		'Not Found'					=> 'The requested API rest call does not exists or is erronous.',
		'Requires authentication'	=> 'The user is required to authenticate himself.',
		'unknown'					=> 'An unknown error has occured.',
		'Email Collect Error'		=> 'An unknown error has occured while retreiving the users email.',
		'Problems parsing JSON'		=> 'The JSON data returned by the API server was invalid.'
	);
	/**
	 * Class constructor
	 * 
	 * @throws	SITE_EXCEPTION
	 * @access	public
	 * @param	none
	 * return 	OAUTH_GITHUB
	 */		
	public function __construct() 
	{
		if (! (bool) self::$blnIsInstace)	
		{
			throw new Exception(__CLASS__ . " requires loading from static method " . __CLASS__ . "::getInstanceFromParent(...)");	
			exit();
		}
	}
	
	/**
	 * This method will load an instance from the parent (se we can access the same parameters)
	 *
	 * @param: OAUTH_AUTHENTICATION - The parent oAuth Object
	 * @return: OAUTH_GITHUB 		- Instance of
	 */
	public static function getInstanceFromParent(OAUTH_AUTHENTICATION $objParent)
	{
		OAUTH_GITHUB::$blnIsInstace = true;
		OAUTH_GITHUB::$objOauthParent = $objParent; 
		$objSelf = new self();
		return ($objSelf);
	}
	
	/**
	 * Returns the OAUTH_AUTHENTICATION parent object used in the OAUTH_GITHUB instantiation
	 * 
	 * @throws 	SITE_EXCEPTION
	 * @access	private final
	 * @param 	none
	 * @return 	OAUTH_AUTHENTICATION
	 */
	private final function getOauthParentRequestObject()
	{
		return (OAUTH_GITHUB::$objOauthParent);
	}
	
	/**
	 * 
	 * 
	 */
	public final function authenticate()
	{
		$intAuthStatus = NULL;
		
		/**
		 * 1. Build the authorize request according to oauth2
		 */
		if (
			(TRUE ===  $this->hasAuthRequest()) &&
			(FALSE === $this->getVariable('code', $_GET))
		) {
			// Build the return URL
			$objAuthReturnUrl = new URL($this->getOauthParentRequestObject()->getOpenIdEndPointUrl());
			$objAuthReturnUrl->setAttribute('oAuth', md5(time()));
			$objAuthReturnUrl->addSessionAttributes();
			
			// Build the request auth URL 
			$objReqAuthUrl = new URL(OAUTH_GITHUB::API_AUTHORIZATION_REQUEST_URL);
			$objReqAuthUrl->clearAttribute();
			$objReqAuthUrl->setAttribute('client_id', constant('__GITHUB_CLIENT_ID__'));
			$objReqAuthUrl->setAttribute('redirect_uri', $objAuthReturnUrl->build());
			$objReqAuthUrl->setAttribute('scope', 'user');
			$objReqAuthUrl->forward();
		}
		else 
		{
			/**
			 * 2. 	Here, we have a return code given to us by github, now we need 
			 * 		to exchange this code for an access token.
			 */
			// Build the token request URL
			$strRequestData			= NULL;
			$objTokenRequestUrl 	= new URL(OAUTH_GITHUB::API_TOKEN_REQUEST_URL);
			$arrTokenRequestData 	= array
			(
				'client_id'			=> constant('__GITHUB_CLIENT_ID__'),
				'client_secret' 	=> constant('__GITHUB_APP_SECRET_ID__'),
				'code' 				=> urlencode($this->getVariable('code', $_GET))
			);
			
			array_walk($arrTokenRequestData, function($strKey, $strVal) use(&$arrTokenRequestData, &$strRequestData){ 
			    $strRequestData .= $strVal . '=' . $strKey . '&';
			});
			$ch = curl_init($objTokenRequestUrl->build());
			curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequestData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$objResponse = curl_exec($ch);
			curl_close($ch);
			
			/**
			 * 3.	Here, we parse the reponse given to us by GitHub and we check for errors
			 */
			preg_match('/error=(.+)/', $objResponse, $arrTransferError);
			preg_match('/access_token=([0-9a-f]+)/', $objResponse, $arrTransferReturn);

			if (FALSE === empty($arrTransferError))
			{
				/**
				 * Here, we received an error message from github, usualy due to an expired verification code.
				 */
				$intAuthStatus = OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR;
				OAUTH_GITHUB::$arrError['code'] 	= $arrTransferError[1];
				OAUTH_GITHUB::$arrError['message'] 	= OAUTH_GITHUB::$arrErrorMessages[OAUTH_GITHUB::$arrError['code']];
				OAUTH_GITHUB::$arrError['data']		= array($arrTransferError, $arrTransferReturn);
			}
			
			
			if (FALSE === ($intAuthStatus === OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR))
			{
				/**
				 * 4.	Here, we got a positive response from github, we now have to extract the user's 
				 * 		information from github in 2 steps. the first step consists of the user's general
				 * 		information, the second, the users email address.
				 */
				$objUserApiRequestDataUrl = new URL(OAUTH_GITHUB::API_REST_SERVER_URL . '/user');
				$objUserApiRequestDataUrl->clearAttribute();
				$objUserApiRequestDataUrl->setAttribute('access_token', $arrTransferReturn[1]);
				$objResponse 	= file_get_contents($objUserApiRequestDataUrl->build());
				$objApiResponse = json_decode($objResponse);
				
				// Here we check if we may have got an unknown error
				if (FALSE === is_object($objApiResponse))
				{
					$intAuthStatus = OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR;
					OAUTH_GITHUB::$arrError['code'] 	= 'unknown';
					OAUTH_GITHUB::$arrError['message'] 	= OAUTH_GITHUB::$arrErrorMessages['unknown'];
					OAUTH_GITHUB::$arrError['data']		= array($objResponse, $objResponse);
				}
				
				// Here, we got a response from the API server, lets check for errors
				if (TRUE === isset($objApiResponse->message)) 
				{
					$intAuthStatus = OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR;
					OAUTH_GITHUB::$arrError['code'] 	= $objApiResponse->message;
					OAUTH_GITHUB::$arrError['message'] 	= OAUTH_GITHUB::$arrErrorMessages[$objApiResponse->message];
					OAUTH_GITHUB::$arrError['data']		= array($objApiResponse, $objResponse, $strAccessToken);
				}
				
				// Here, we did not receive any errors, lets parse the data
				if (FALSE === ($intAuthStatus === OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR))
				{
					
					// Now we need to get the user's email!
					// This is done via a seperate API request call
					$objUserApiUserEmailUrl = new URL(OAUTH_GITHUB::API_REST_SERVER_URL . '/user/emails');
					$objUserApiUserEmailUrl->clearAttribute();
					$objUserApiUserEmailUrl->setAttribute('access_token', $arrTransferReturn[1]);
					$objUserEmailData 		= file_get_contents($objUserApiUserEmailUrl->build());
					$arrUserEmailResponse 	= json_decode($objUserEmailData);
					
					// Here we check if we may have got an unknown error while retreiving the email
					if (
						(FALSE === is_array($arrUserEmailResponse)) ||
						(TRUE  === empty($arrUserEmailResponse))
					) {
						$intAuthStatus = OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR;
						OAUTH_GITHUB::$arrError['code'] 	= 'Email Collect Error';
						OAUTH_GITHUB::$arrError['message'] 	= OAUTH_GITHUB::$arrErrorMessages['Email Collect Error'];
						OAUTH_GITHUB::$arrError['data']		= array($objUserEmailData, $arrUserEmailResponse);
					}
					
					// Ok! Everything is peachy! Lets login amd/or create the user.
					if (FALSE === ($intAuthStatus === OAUTH_GITHUB::OAUTH_GITHUB_STATUS_ERROR))
					{
						$objApiResponse->email = filter_var($arrUserEmailResponse[0], FILTER_VALIDATE_EMAIL);

						// Set the status to success.	
						$intAuthStatus = OAUTH_GITHUB::OAUTH_GITHUB_STATUS_SUCCESS;	
						
						// Create the user if he doesnt exists
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'email' => $objApiResponse->email
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
	
							// If we dont have a user, we create one..
						if (FALSE === ($objUser->getId() > 0)) 
						{
							$objUser->setVariable('creation_date', 		date("Y-m-d H:i:s"));
							$objUser->setVariable('activation_date', 	date("Y-m-d H:i:s"));
							$objUser->setVariable('username', 			$objApiResponse->email);
							$objUser->setVariable('email', 				$objApiResponse->email);
							$objUser->setVariable('password', 			$objUser->generatePassword());
							$objUser->setVariable('oauth_provider', 	$this->getOauthParentRequestObject()->getAuthenticationMode());
							$objUser->setVariable('oauth_uid', 			$objApiResponse->id);
							$objUser->setVariable('ipAddress', 			$_SERVER['REMOTE_ADDR']);
							$objUser->setVariable('active', 			ACTIVE_STATUS_ENABLED);
							$objUser->setVariable('access_level', 		1);
							$objUser->save();
							 
							$objUser->sendWelcomeEmail($objUser);
						}
						
						// Finally, log the user into the site and we're done!
						$this->getOauthParentRequestObject()->oauthUserObjectLogin($objUser);
					}
				}
			}
		}
		
		// Store the exception if any onto the parent if any...
		$this->getOauthParentRequestObject()->setLastException(OAUTH_GITHUB::$arrError);
		
		return ($intAuthStatus);
	}
}