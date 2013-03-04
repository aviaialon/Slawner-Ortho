<?php
	/**
	 * OAUTH_AUTHENTICATION Administration Class
	 * This class manages oauth authentication
	 *
	 * @package		{__APPLICATION_CLASS_PATH__}::OAUTH
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 
 	/*------------------------------------------
	
	EXAMPLE USAGE: 
	==============
	
	
	PayPal Login:
	_____________
	
		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");		
		$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_PAYPAL);
		
		$objUrl = new URL(URL::getCurrentUrl());
		$objUrl->setScheme(SCHEME_HTTP);
		$objUrl->clearAttribute();
		
		$objOauthLogin->setOpenIdEndPointUrl($objUrl->build());
		$objOauthLogin->setCancelUrl($objUrl->build());
		$objOauthLogin->authenticate(false);	// <--- use false if the page is NOT a dedicated login page so that users will not get recursively logged out
		
	
	
	Facebook Login:
	_____________
	
		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");		
		$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_FACEBOOK);
		$objOauthLogin->setUser($objUser);
		$objOauthLogin->setUrlData($_GET);
		$objOauthLogin->setPostData($_POST);
		$objOauthLogin->authenticate(false);
		
		
	DEPENDENCIES:
		- Make sure the oAuth get parameter is present to lauch the authentication process.	
			
	--------------------------------------------*/
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::SITE_USERS");	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::FACEBOOK::OAUTH_V2::FACEBOOK");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::TWITTER::OAUTH_TWITTER");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OPEN_ID");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::PAYPAL::OAUTH_PAYPAL");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::LINKEDIN::OAUTH_LINKEDIN");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::FOURSQUARE::OAUTH_FOURSQUARE");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::GITHUB::OAUTH_GITHUB");
	
	class OAUTH_AUTHENTICATION extends OBJECT_BASE 
	{
		
	   /*
		* Auth Mode user defined constants
		*/
		const OAUTH_AUTHENTICATION_AUTHMODE_FACEBOOK 	= 'Facebook';
		const OAUTH_AUTHENTICATION_AUTHMODE_TWITTER 	= 'Twitter'; 
		
		// OpenId login systems (Using OPEN_ID)
		//	- http://code.google.com/p/lightopenid/
		// 	- http://code.google.com/p/lightopenid/wiki/GettingMoreInformation
		const OAUTH_AUTHENTICATION_AUTHMODE_GOOGLE 			= 'Google'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_YAHOO 			= 'Yahoo'; 
		
		// OpenId - Alternate login systems (Using OPEN_ID)
		// 	- These openId login systems require a username added to the Post-To URL
		//	- http://code.google.com/p/lightopenid/
		// 	- http://code.google.com/p/lightopenid/wiki/GettingMoreInformation
		// 	- OpenId Providers: http://en.wikipedia.org/wiki/OpenID
		const OAUTH_AUTHENTICATION_AUTHMODE_PAYPAL			= 'PayPal'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_AOL 			= 'Aol';  
		const OAUTH_AUTHENTICATION_AUTHMODE_MYOPENID 		= 'MyOpenId'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_OPENID 			= 'OpenId'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_LIVEJOURNAL		= 'Livejournal'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_WORDPRESS		= 'Wordpress'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_BLOGGER			= 'Blogger';  
		const OAUTH_AUTHENTICATION_AUTHMODE_VERISIGN		= 'Verisign';  
		const OAUTH_AUTHENTICATION_AUTHMODE_CLAIMID			= 'ClaimID';  
		const OAUTH_AUTHENTICATION_AUTHMODE_CLICKPASS		= 'ClickPass';  
		const OAUTH_AUTHENTICATION_AUTHMODE_GOOGLE_PROFILE	= 'GoogleProfile'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_MYSPACE			= 'MySpace'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_LINKEDIN		= 'LinkedIn'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_FOURSQUARE		= 'FourSquare'; 
		const OAUTH_AUTHENTICATION_AUTHMODE_GITHUB			= 'GitHub'; 
		
		private $strAuthMode 		= false; // Authentication Mode Currently Supports Facebook, Twitter
		private $objUser 	 		= false;
		private $arrGet				= array();
		private $arrPost			= array();
		private $cancelUrl			= NULL; // The cancel URL (used for oAuth authentication (only paypal for now)
		private $openIdEndPointUrl	= NULL; // This sets the openId end point URL
		private $openIdAltUsername	= NULL; // This is the userId added to the Post-To OpenId URL ex: Wordpress, Blogger etc..
											// These sites require a login post-to URL like http://{USERNAME}.{SITE}.com, EX: http://avi.blogger.com
	   /**
		* Provider URL:: These are the post-to URLs for OpenId authebtication.
		* Each provider has their own URLs to call to.
		*/
		protected static $OPENID_SERVICE_AUTH_PROVIDERS_CONNECT_URL = array(
			self::OAUTH_AUTHENTICATION_AUTHMODE_GOOGLE			=>	'https://www.google.com/accounts/o8/id',
			self::OAUTH_AUTHENTICATION_AUTHMODE_YAHOO			=>	'http://me.yahoo.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_AOL				=>	'http://openid.aol.com/{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_MYOPENID		=>	'http://{USERNAME}.myopenid.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_OPENID			=>	'{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_LIVEJOURNAL		=>	'http://{USERNAME}.livejournal.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_WORDPRESS		=>	'http://{USERNAME}.wordpress.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_BLOGGER			=>	'http://{USERNAME}.blogspot.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_VERISIGN		=>	'http://{USERNAME}.pip.verisignlabs.com/',
			self::OAUTH_AUTHENTICATION_AUTHMODE_CLAIMID			=>	'http://claimid.com/{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_CLICKPASS		=>	'http://clickpass.com/public/{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_GOOGLE_PROFILE	=>	'http://www.google.com/profiles/{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_MYSPACE			=>	'http://www.myspace.com/{USERNAME}',
			self::OAUTH_AUTHENTICATION_AUTHMODE_PAYPAL			=>	'https://www.paypal.com/webapps/auth/server',
			self::OAUTH_AUTHENTICATION_AUTHMODE_LINKEDIN		=>	'https://www.linkedin.com/uas/oauth/authorize',
			self::OAUTH_AUTHENTICATION_AUTHMODE_GITHUB			=>	'https://github.com/login/oauth/authorize'
		);
		
		public function __contruct(
			$authMode = NULL,
			SITE_USERS $objSiteUser = NULL
		) {
			if (! is_null($authMode)) {
				$this->setAuthenticationMode($authMode);	
			}
			
			if (
				is_object($objSiteUser) &&
				is_a($objSiteUser, 'SITE_USERS')
			) {
				$this->setUser($objSiteUser);	
			}
		}
		
		public static function getInstance(
			$authMode = NULL,
			SITE_USERS $objSiteUser = NULL								   
		) {
			$objAuthUser = (is_object($objSiteUser) ? $objSiteUser : SITE_USERS::getCurrentUser());
			$objOauth = new self();
			$objOauth->setAuthenticationMode($authMode);	
			$objOauth->setUser($objAuthUser);	
			$objOauth->setUrlData((array) $_GET);
			$objOauth->setPostData((array) $_POST);
			return ($objOauth);
		}
		// 
		// Setters
		//
		
		// OpenId Specific:: Sets the openId username to use in the gateway login proxy
		public function setOpenIdUserName($strUserName = NULL) { 
			$this->openIdAltUsername = trim($strUserName);
		}
		
		// OpenId Specific:: Sets the openId end point URL. once Login Complete
		public function setOpenIdEndPointUrl($strUrl = NULL) { 
			$this->openIdEndPointUrl = $strUrl;
		}
		
		public function setCancelUrl($strUrl = NULL) { 
			$this->cancelUrl = $strUrl;
		}
		
		public function setAuthenticationMode($authMode) {
			$this->strAuthMode = ucwords($authMode);	
		}
		
		public function setUser(SITE_USERS $objSiteUser) {
			$this->objUser = $objSiteUser;	
		}
		
		public function setUrlData($arrGet = array()) {
			$this->arrGet = (array) $arrGet;	
		}
		
		public function setPostData($arrPost = array()) {
			$this->arrPost = (array) $arrPost;	
		}
		// 
		// Getters
		//
		public    function getAuthMode() 				{ return ($this->getAuthenticationMode()); } // Wrapper for private method getAuthenticationMode()
		protected function getAuthenticationMode() 		{ return ($this->strAuthMode); }
		protected function getUser() 					{ return ($this->objUser); }
		protected function getUrlParams()				{ return ($this->arrGet); }
		protected function getPostParams()				{ return ($this->arrPost); }
		// OpenId specific getters
		protected function getOpenIdUserName()			{ return ($this->openIdAltUsername); } // Gets the openId username to use in the gateway login proxy
		protected function getOpenIdEndPointUrl()		{ return ($this->openIdEndPointUrl); } // Gets the openId end point URL, once the login is complete
		protected function getCancelUrl()				{ return ($this->cancelUrl); } // Gets the openId cancel URL, currently only supported by paypal
		
		public static function getAvailableAuthMethods() {
			$arrMethods 	= array();
			$objOauth 		= new self();
			$objReflection 	= new ReflectionObject($objOauth);
			foreach($objReflection->getConstants() as $strConstName => $strAuthMethod) {
				if (strcmp(substr($strConstName, 0, 29),  'OAUTH_AUTHENTICATION_AUTHMODE') === 0) {
					$arrMethods[$strConstName] = $strAuthMethod;
				}
			}
			return ($arrMethods);
		}

		// 
		// Executors 
		//
		
		public function hasAuthRequest() {
			/*
			$arrGetData 	= $this->getUrlParams();
			$arrPostData 	= $this->getPostParams();
			*/
			$arrGetData 	= $_GET;
			$arrPostData 	= $_POST;
			
			$blnHasRequest = false;
			
			// Twitter Specific
			$blnHasRequest |= 	(isset($arrPostData['twAuthToken'])) 		||
								(isset($arrPostData['twAuthUid']))			||
								(isset($arrPostData['openid_claimed_id']))	||
								(isset($arrPostData['oauth_pptoken']))		||
								(isset($arrGetData['oauth_token']))			||
								(isset($arrGetData['twAuthToken'])) 		||
								(isset($arrGetData['janrain_nonce'])) 		||
								(isset($arrGetData['requestStart'])) 		||
								(isset($arrGetData['exp']));
			// OAuth Specific
			$blnHasRequest |=	(isset($arrGetData['oAuth'])) ||
								(isset($arrGetData['openid_ns']))	||
								(isset($arrGetData['authMethod'])) 	;
			
			// LinkedIn Specific
			$blnHasRequest |=	(isset($arrGetData[LinkedIn::_GET_TYPE])) ||
								(isset($arrGetData[LinkedIn::_GET_RESPONSE]));																
								
			return ((bool) $blnHasRequest);		
		}
		
		/**
		 * Main initiation method - this method starts the authentication process.
		 * 
		 * @param 	Boolean blnLogUsersOutIfLoggedIn - 	Will log a user out if already logged in, default to true
		 												use false if the page is NOT a dedicated login page so that 
														users will not get recursively logged out
		 * @return 	void
		 */
		public function authenticate($blnLogUsersOutIfLoggedIn = true) {
			if (
				($this->getAuthenticationMode()) &&
				(method_exists($this, 'authenticate_' . $this->getAuthenticationMode()))
			){
				if (! $this->getUser()) 
				{
					$this->setUser(SITE_USERS::getCurrentUser());	
				}
				
				// Log the user out if the $blnLogUsersOutIfLoggedIn
				// is set to anything other than FALSE
				if (! (FALSE === $blnLogUsersOutIfLoggedIn))
				{
					//$this->getUser()->logout(false);	
				}
				
				/**
				 * Here, we need to reset in the session
				 * the auth mode, because by calling the
				 * logout method on the user object, we
				 * cleared the current session.
				 */
				 $objSession = SESSION::getInstance();
				 $objSession->set('authMethod', $this->getAuthenticationMode());
				 
				 call_user_func_array(array($this, 'authenticate_' . $this->getAuthenticationMode()), array());
			} 
			else 
			{
				if (! $this->getAuthenticationMode()) 
				{
					// No auth mode defined
					throw new Exception('An authentication mode has not been defined.');	
				}
				else
				{
					throw new Exception('The OpenId Auth Method: authenticate_' . $this->getAuthenticationMode() . ' is not defined.');	
				}
			}
		}
		
		/**
		 * This method stores the last exception handled by an oauth object
		 * Retreiving the error is done by using the getLastException() magic
		 * methog (because OAUTH_AUTHENTICATION extends OBJECT_BASE)
		 * 
		 * @throws 	SITE_EXCEPTION
		 * @access	public, final
		 * @param 	array $arrException - The last exception artay containers, Keys should include {code, message, data}
		 * @return 	void
		 */
		public final function setLastException(array $arrException = NULL)
		{
			$this->setVariable('LastException', $arrException);
		}
		
		/**
		 * This method returns weather the current auth mode needs a username
		 **/
		 public function requiresOpenIdUsername() {
			return(
				(bool) (($this->getAuthenticationMode()) &&
				(isset(self::$OPENID_SERVICE_AUTH_PROVIDERS_CONNECT_URL[$this->getAuthenticationMode()])) &&
				(strpos(self::$OPENID_SERVICE_AUTH_PROVIDERS_CONNECT_URL[$this->getAuthenticationMode()], '{USERNAME}') !== FALSE))
			);
		 }
		
		/**
		 * This method logs the user in and redirects
		 * @param:	$objUser [SITE_USERS] - The user object
		 * @return:	void
		 */
		protected function oauthUserObjectLogin(SITE_USERS $objUser) 
		{
			$this->getUser()->login(
				$objUser->getVariable('username'),
				$objUser->getVariable('password'),
				false
			);
			$strMessage = "You have successfully signed in " . $objUser->getVariable('username');
			
			$objRedirectUrl = new URL(__ROOT_URL__);
			$objRedirectUrl->setPath("/");
			$objRedirectUrl->clearAttribute();
			$objRedirectUrl->setAttribute('ok', $strMessage);
			$objRedirectUrl->addSessionAttributes();
			URL::redirect($objRedirectUrl->build());	
		}
		
		
		//
		// Authetication Methods
		//
		/**
		 * This method is the generic OpenId authentication proxy
		 * @return: Void
		 */
		 
		private function openIdAuthenticate() {	
			SITE_EXCEPTION::supressException();
			
			if (! $this->getOpenIdEndPointUrl()) {
				$this->setOpenIdEndPointUrl(__SITE_URL__);	
			}
			
			try {
				// 1. Get the openId and session objects
				$objOpenId 	= OPEN_ID::getInstance($this->getOpenIdEndPointUrl());
				$objSession	= SESSION::getInstance();
				// Initial workflow (begin the auth process)
				if (! $objOpenId->getMode()) {
					// 2. Get the OpenId gateway login proxy URL, and set auth data
					$strOpenIdProxyUrl = str_replace(
						'{USERNAME}', $this->getOpenIdUserName(), 
						self::$OPENID_SERVICE_AUTH_PROVIDERS_CONNECT_URL[$this->getAuthenticationMode()]
					);
					$objOpenId->setIdentity($strOpenIdProxyUrl);
					$objOpenId->setRequired(array(
						'namePerson/friendly',
						'contact/email',
						'namePerson',
						'contact/country/home',
						'pref/language',
						'pref/timezone'
					));
					// 3. Redirect the user to the auth location
					URL::redirect($objOpenId->getAuthenticationUrl());
					exit();
				} elseif($objOpenId->getMode() == OPEN_ID::OPEN_ID_AUTHENTICATION_MODE_CANCEL) {
					// User canceled the authentication process....
					SESSION::destroySession();
					// ... Do whatever else here ...
				} else {
					// mode: id_res
					// User logged in successfully, make sure we have the email address...
					$arrData = $objOpenId->getAttributes();
					if (
						(sizeof($arrData)) &&
						(isset($arrData['contact/email'])) &&
						(strlen($arrData['contact/email']))
					) {
						// 4. Now we try to load the user object corresponding to the
						// currently authenticated user.
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'email' =>	$arrData['contact/email']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);

						// If we dont have a user, we create one..
						if (! $objUser->getId()) {
							$objUser->setVariable('creation_date', 		date("Y-m-d H:i:s"));
							$objUser->setVariable('activation_date', 	date("Y-m-d H:i:s"));
							$objUser->setVariable('username', 			$arrData['contact/email']);
							$objUser->setVariable('email', 				$arrData['contact/email']);
							$objUser->setVariable('password', 			$this->getUser()->generatePassword());
							$objUser->setVariable('oauth_provider', 	$this->getAuthenticationMode());
							$objUser->setVariable('ipAddress', 			$_SERVER['REMOTE_ADDR']);
							$objUser->setVariable('active', 			ACTIVE_STATUS_ENABLED);
							$objUser->setVariable('access_level', 		1);
							$objUser->save();
							 
							$this->getUser()->sendWelcomeEmail($objUser);
						}
							
						// 5. Finally, log the user into the site and we're done!
						$this->oauthUserObjectLogin($objUser);
							
					} else {
						// ... an error occured here... we dont have the email,
						// or a permission issue prevents us from getting it.
					}
				}
				
				// $this->getAuthenticationMode()
			} catch (Exception $OpenIdAuthException) {
				SESSION::destroySession();
				$objSession = SESSION::getInstance();
				$objSession->set('authMethod', $this->getAuthenticationMode());
				$objUrl = new URL(__SITE_URL__);
				$objUrl->clearAttribute();
				$objUrl->setAttribute('err', 'Sorry, an error has occured. Please try again later. [' . $OpenIdAuthException->getMessage() . ']');
				URL::redirect($objUrl->build());
			}
			SITE_EXCEPTION::clearExceptionSupress(); 
		}
		
		/**
		 * This method logs the user in via Paypal API
		 * 	- 	An application is required for use of this API. --> https://devportal.x.com
		 *		The application set in https://devportal.x.com has to have https even if the site
		 *		your going to use it in doesnt. Alsom make sure that the Return URl sett in https://devportal.x.com
		 *		is blank so that you can set a custom one.
		 
		 		EXAMPLE IMPLEMENTTION:
				
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");		
				$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_PAYPAL);
				
				$objUrl = new URL(URL::getCurrentUrl());
				$objUrl->setScheme(SCHEME_HTTP);
				$objUrl->clearAttribute();
				
				$objOauthLogin->setOpenIdEndPointUrl($objUrl->build());
				$objOauthLogin->setCancelUrl($objUrl->build());
				$objOauthLogin->authenticate();	
		 */
		 private function authenticate_PayPal() {
		 	$Application	 = APPLICATION::getInstance();
			$objSession 	 = SESSION::getInstance();
			$objOAuthPayPal  = OAUTH_PAYPAL::getInstanceFromParent($this);

			if (
				(! $objSession->get('ppa_data')) &&
				(! $this->hasAuthRequest())
			) {
				/**
				 * Step 1: This segment displays PayPal's login button
				 */
				$objOAuthPayPal->loadOauthRequestForm();
			 }
			 else if($this->hasAuthRequest())
			 {	
				if (
					($Application->getForm()->paramValue('oauth_pptoken')) &&
					($Application->getForm()->paramValue('oauth_pptoken') == 'requestStart')
				) {
					/**
					 * Step 2: In this segment, the use submitted the form.
					 */
					$objOAuthPayPal->oAuth_step1();
				}
				else if (
					$Application->getForm()->paramValue('openid_claimed_id') ||
					$Application->getForm()->getOrPost('janrain_nonce')
				) { 
					// We have an answer from paypal here!		
					switch($objOAuthPayPal->oAuth_step2())
					{
						case OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_SUCCESS : 
						{
							// Successful itteration, log the user in
							$arrData = $objSession->get('oauth_user_data');
							if (
								(sizeof($arrData)) &&
								(isset($arrData['http://axschema.org/contact/email'])) &&
								(strlen($arrData['http://axschema.org/contact/email']))
							) {
								$strFirstName = (
									isset($arrData['http://axschema.org/namePerson/first']) ?
									$arrData['http://axschema.org/namePerson/first'] : ''
								);
								
								$strLastName = (
									isset($arrData['http://axschema.org/namePerson/last']) ?
									$arrData['http://axschema.org/namePerson/last'] : ''
								);
								
								// 4. Now we try to load the user object corresponding to the
								// currently authenticated user.
								$objUser = SITE_USERS::getInstanceFromKey(array(
									'email' =>	$arrData['http://axschema.org/contact/email']
								), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
		
								// If we dont have a user, we create one..
								if (! $objUser->getId()) {
									$objUser->setVariable('creation_date', 		date("Y-m-d H:i:s"));
									$objUser->setVariable('activation_date', 	date("Y-m-d H:i:s"));
									$objUser->setVariable('username', 			$arrData['http://axschema.org/contact/email']);
									$objUser->setVariable('email', 				$arrData['http://axschema.org/contact/email']);
									$objUser->setVariable('first_name', 		$strFirstName);
									$objUser->setVariable('last_name', 			$strLastName);
									$objUser->setVariable('password', 			$Application->getUser()->generatePassword());
									$objUser->setVariable('ipAddress', 			$_SERVER['REMOTE_ADDR']);
									$objUser->setVariable('active', 			ACTIVE_STATUS_ENABLED);
									$objUser->setVariable('access_level', 		1);
									$objUser->save();
									 
									$this->getUser()->sendWelcomeEmail($objUser);
								}
								
								// 5. Finally, log the user into the site and we're done!
								$objUser->setOauth_provider($this->getAuthenticationMode())->save();
								$this->oauthUserObjectLogin($objUser);
									
							}
							break;	
						}
						
						case OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_CANCEL : 
						{
							SESSION::destroySession();
							$objSession = SESSION::getInstance();
							$objUrl = new URL($Application->getUser()->getLoginUrl());
							$objUrl->clearAttribute();
							$objUrl->setAttribute('info', 'You have successfuly canceled your ' . $this->getAuthenticationMode() . ' login.');
							URL::redirect($objUrl->build());
							break;	
						}
						
						case OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_ERROR : 
						{
							SITE_EXCEPTION::raiseException($objOAuthPayPal->getErrorMessage());
							break;
						}
						
						default : 
						{
							// Unknown return status	
						}
					}
				}
				else {
					/**
				 	 * Back to step 1: But auto submit
				 	 */
					$objOAuthPayPal->loadOauthRequestForm(true);
				}
			 }
		 }
		
		/**
		 * This method logs the user in via Twitter API
		 * @param: 	$strUserName 	- String 	- The Username
		 * @param: 	$strPassword 	- String 	- The Password
		 * @param: 	$blnUseRedirect - Boolean 	- Redirect the user after login?
		 */
		private function authenticate_Twitter() {
			OAUTH_TWITTER::loadLibrary();
			// Load GET and POST data
			$arrGetData 	= $this->getUrlParams();
			$arrPostData 	= $this->getPostParams();
			
			// Load the authentication object
			$objTwitterOAuth = new EpiTwitter(
				__TWITTER_CONSUMER_KEY__, 
				__TWITTER_SECRET_KEY__
			);  
			
			if (
				(! isset($arrGetData['oauth_token'])) &&
				(! isset($arrGetData['twAuthToken'])) &&
				(! isset($arrGetData['twAuthUid'])) &&
				(! isset($arrPostData['twAuthToken']))
			) {
				// Start the twitter auth process
				$objTwitterAuthURL = new URL($objTwitterOAuth->getAuthenticateUrl());  
				URL::redirect($objTwitterAuthURL->build());
			} 
			else if (
				(isset($arrPostData['twAuthToken'])) &&
				(isset($arrPostData['twAuthUid']))
			) {
				$blnContinue = true;
				// Validate 
				if (
					(! isset($arrPostData['email'])) ||
					(! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $arrPostData['email']))
				) {
					$blnContinue = false;
					$objRedirectUrl = new URL(URL::getCurrentUrl());
					$objRedirectUrl->deleteAttribute('twAuthToken');
					$objRedirectUrl->deleteAttribute('twAuthUid');
					$objRedirectUrl->clearAttribute();
					$objRedirectUrl->addSessionAttributes();
					$objRedirectUrl->setAttribute('err', 'Please enter a valid email.');
					$objRedirectUrl->setAttribute('twAuthToken', $arrPostData['twAuthToken']);
					$objRedirectUrl->setAttribute('twAuthUid', $arrPostData['twAuthUid']);
					URL::redirect($objRedirectUrl->build());
				}
				// The user submitted his/her email address.
				// 1. Check if this is a current user on our site
				if ($blnContinue) {
					// Check for an existsing user
					$objCurrUser = SITE_USERS::getInstanceFromKey(array(
						'email' => $arrPostData['email']
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// Get the new user
					$objNewUser = SITE_USERS::getInstanceFromKey(array(
						'oauth_provider' => strtolower($this->getAuthenticationMode()),
						'oauth_uid'		 => (int) $arrPostData['twAuthUid']
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					if (! $objNewUser->getId()) {
						URL::redirect(__SITE_URL__);	
					}
					// If we have an existing user
					if ($objCurrUser->getId()) {
						$objCurrUser->setVariable('oauth_provider', strtolower($this->getAuthenticationMode()));
						$objCurrUser->setVariable('oauth_uid', (int) $arrPostData['twAuthUid']);
						$objCurrUser->setVariable('active', ACTIVE_STATUS_ENABLED);
						$objCurrUser->setVariable('activationkey', $objNewUser->getVariable('activationkey'));
						$objCurrUser->save();
						$objCurrUser->delete();
						
						$this->oauthUserObjectLogin($objCurrUser);
					}
				}
				// 2. Check if the user exists
				
			} else if (isset($arrGetData['oauth_token'])) {
				// Begin the creation and login process....
				try {
					$objTwitterOAuth->setToken($arrGetData['oauth_token']);  
					$objAuthToken = $objTwitterOAuth->getAccessToken();  
					$objTwitterOAuth->setToken(
						$objAuthToken->oauth_token, 
						$objAuthToken->oauth_token_secret
					);  
					
					// Get the user info
					$objUserInfo = $objTwitterOAuth->get_accountVerify_credentials();  
					// Make sure we have data
					if (
						(isset($objUserInfo->headers['Status'])) &&
						($objUserInfo->headers['Status'] == "200 OK")
					) {
						// Lets see if we have an existing user
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'oauth_provider' => strtolower($this->getAuthenticationMode()),
							'oauth_uid'		 => (int) $objUserInfo->response['id_str']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						
						if (
							(! $objUser->getId()) ||
							(! strlen($objUser->getVariable('email')))
						) {
							$arrName 		= explode($objUserInfo->name," ");
							$strFirstName 	= (isset($arrName[0]) ? $arrName[0] : "");
							$strLastName 	= (isset($arrName[1]) ? $arrName[1] : "");
							
							// Create a username..
							$strUserName 	= $objUserInfo->response['screen_name'];
							$intCuName 		= 1;
							if ($objUser->userNameExists($strUserName)) {
								do {
									$strUserName = $objUserInfo->response['screen_name'] . (string) $intCuName;
									++$intCuName;
								} 	while($objUser->userNameExists($strUserName));
							}
							
							$objUser->setVariable('oauth_provider', strtolower($this->getAuthenticationMode()))	;
							$objUser->setVariable('oauth_uid', (int) $objUserInfo->response['id_str']);
							$objUser->setVariable('creation_date', date("Y-m-d H:i:s"));
							$objUser->setVariable('username', $strUserName);
							$objUser->setVariable('first_name', $strFirstName);
							$objUser->setVariable('last_name', $strLastName);
							$objUser->setVariable('password', $this->getUser()->generatePassword());
							$objUser->setVariable('access_level', 1);
							$objUser->setVariable('active', ACTIVE_STATUS_PENDING);
							$objUser->setVariable('ipAddress', $_SERVER['REMOTE_ADDR']);
							$objUser->setVariable('activationKey', $arrGetData['oauth_token'] . "|" . $arrGetData['oauth_verifier']);
							$objUser->save();
							
							// Now redirect to get the user's email
							$objRedirectUrl = new URL('/users/twitter-oauth/');
							$objRedirectUrl->clearAttribute();
							$objRedirectUrl->setAttribute('twAuthToken', $objUser->getVariable('activationkey'));
							$objRedirectUrl->setAttribute('twAuthUid', $objUser->getVariable('oauth_uid'));
							$objRedirectUrl->addSessionAttributes();
							URL::redirect($objRedirectUrl->build());	
							
						} else if (strlen($objUser->getVariable('email'))) {
							// Returning user, log him in
							// Login the user in the site.
							$this->oauthUserObjectLogin($objUser);
						}
					}
				} 
				//
				// Catch EpiTwitter Exception, possilbly due to session conflicts.
				//
				catch(EpiOAuthUnauthorizedException  $EpiUnauthorizedExcpetion) {
					$intExceptionsNum = (isset($arrGetData['exp']) ? (int) $arrGetData['exp'] : 1);
					// Give it 3 tries and go to hell
					if ($intExceptionsNum <= 3) {
						// Clear the session
						SESSION::destroySession();
						$objSession = SESSION::getInstance();
						$objSession->set('authMethod', $this->getAuthenticationMode());
						$objUser = SITE_USERS::getCurrentUser();
						$objRedirectUrl = new URL(
							$objUser->getTwitterLoginUrl()
						);
						$objRedirectUrl->clearAttribute();
						$objRedirectUrl->addSessionAttributes();
						$objRedirectUrl->setAttribute('exp', ++$intExceptionsNum);
						URL::redirect(
							$objRedirectUrl->build()
						);
					} else {
						$objRedirectUrl = new URL(__SITE_ROOT__);
						$objRedirectUrl->setAttribute('err', 'Sorry, twitter login seemed to get stuck. Please try again later or use a different login method.');
					}
				} 
				//
				// Catch EpiTwitter Exception, possilbly due to session conflicts.
				//
				catch(EpiTwitterException $EpiSessionExcpetion) {
					if (! class_exists('SITE_EXCEPTION')) {
						SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	
					}
					SITE_EXCEPTION::throwNewException($e);
				} 
				//
				// Catch All Hell Breaks Loose Exceptions, due my my fuck up! ;)
				//
				catch (Exception $e) {
					if (! class_exists('SITE_EXCEPTION')) {
						SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	
					}
					SITE_EXCEPTION::throwNewException($e);
				}
			}
		}
		
		/**
		 * This method logs the user in via Facebook API
		 * Example Usage:
		 	if (! $Application->getUser()->getId()) 
			{
		 		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");		
				$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_FACEBOOK);
				$objOauthLogin->setUser($Application->getUser());
				$objOauthLogin->setUrlData($_GET);
				$objOauthLogin->setPostData($_POST);
				$objOauthLogin->authenticate();
			}
			
			Make sure that the redirect URL is set properly in the Facebook Developer Section:
				- https://developers.facebook.com/apps
		 */
		private function authenticate_Facebook() {
			$objDb 			= DATABASE::getInstance();
			$objSession 	= SESSION::getSession();
			$Application 	= APPLICATION::getInstance();
			
			// Load GET and POST data
			$arrGetData 	= $this->getUrlParams();
			$arrPostData 	= $this->getPostParams();
			
			$arrGetData['ref'] = "/";
			
			# Creating the facebook object
			$facebook = new Facebook(array(
				'appId'  => constant('__FB_APPLICATION_ID__'),
				'secret' => constant('__FB_APP_SECRET_ID__'),
				'cookie' => constant('__FB_USE_COOKIES__')
			));
			
			# Let's see if we have an active session
			$user = $facebook->getUser();
			
			if(false === empty($user)) {
				# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
				try
				{
					//$uid = $facebook->getUser();
					$user = $facebook->api('/me');
				} 
				catch (FacebookApiException $fbException)
				{
					/**
					 * Here, we have a cURL error.
					 * So we'll try to decipher the users
					 * info manually
					 */
					 /*
					SITE_EXCEPTION::raiseSilentException($fbException);
					
					if (
						($arrSessionData = $facebook->getUser()) &&
						(isset($arrSessionData['authMethod']))
					) {
						$strUserDataUrl = $facebook->getUrl('graph', '/me', array('access_token' => $arrSessionData['access_token']));
						$objFileData = file_get_contents($strUserDataUrl);	
						$objUser = json_decode($objFileData);
						
						if (is_object($objUser))
						{
							$user = get_object_vars($objUser);
						}
						else 
						{
							// Try to reload.
							SESSION::destroySession();
							$facebook->setSession(NULL);
							$objUrl = new URL($Application->getUser()->getLoginUrl());
							$objUrl->clearAttribute();
							$objUrl->setAttribute('err', $fbException->getMessage());
							URL::redirect($objUrl->build());
							die;
						}
					} 
					*/
				}
				catch (Exception $e){
					require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php'); 
					SITE_EXCEPTION::throwNewException($e);
				}
				
				if(! empty($user) && isset($user['email'])) {
					# We have an active session, let's check if we have already registered the user
					$objUser = SITE_USERS::getInstanceFromKey(array(
						'oauth_provider' =>	'facebook',
						'oauth_uid'		 => (int) $user['id']
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// if we dont have a oauth user, lets try to find a site user ...
					if (! ($objUser->getId()))  {
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'email' =>	$user['email']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					}
					
					# If not, let's add missing values to the database
					# Create the new user:
					if(
						(! ($objUser->getId())) 
					){
						$objUser->setVariable('creation_date', date("Y-m-d H:i:s"));
						$objUser->setVariable('activation_date', date("Y-m-d H:i:s"));
						$objUser->setVariable('username', $user['email']);
						$objUser->setVariable('password', $this->getUser()->generatePassword());
						$objUser->setVariable('oauth_uid', $user['id']);
						$objUser->setVariable('first_name', $user['first_name']);
						$objUser->setVariable('last_name', $user['last_name']);
						$objUser->setVariable('email', $user['email']);
						$objUser->setVariable('ipAddress', $_SERVER['REMOTE_ADDR']);
						$objUser->setVariable('active', ACTIVE_STATUS_ENABLED);
						$objUser->setVariable('access_level', 1);
						$this->getUser()->sendWelcomeEmail($objUser);
					}

					$objUser->setVariable('oauth_provider', 'facebook');
					$objUser->save();
					
					// this sets variables in the session 
					$objSession->set('oauth_uid', $objUser->getVariable('oauth_uid'));
					$objSession->set('oauth_provider', $objUser->getVariable('oauth_provider'));
					
					/*
					if (
						(! isset($user['email'])) ||
						(is_null($user['email']))			  
					) {
						// Here, we dont have extened permission from the user, which we need!
						// Now, well need to get extended permissions
						$url = $facebook->getLoginUrl(array(
							'req_perms' 	=> 'email,user_birthday,status_update,publish_stream,user_photos,user_videos',
							'cancel_url' 	=> __ROOT_URL__ . '/fb-connect/sorry.php',
							'next' 			=> URL::getCurrentUrl()
						));
						
						URL::redirect($url);
					} else {
						
					}
					*/
					
					// Here, we have the user's extended permissions, so we have to check for site user
					// Get the site user:
					$objUser = SITE_USERS::getInstanceFromKey(array(
						'email' =>	$user['email']
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					if ((bool) $objUser->getId()) {
						// Here, we have a site user, log him im	
						if (strcmp($objUser->getVariable('oauth_uid'), $user['id']) <> 0) {
							// Update the oauth_uid
							$objUser->setVariable('oauth_uid', $user['id']);
							$objUser->save();
						}
						
						// Login the user in the site.
						$this->oauthUserObjectLogin($objUser);
						
					} else {
						// Here, the user is not a site user, we need to create a site user
						$strPassWord = $this->getUser()->generatePassword();
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'oauth_uid' 	 =>	(int) $uid,
							'oauth_provider' => 'facebook'
						));
						// Create the use rif he isn't already created... 
						$objUser->setVariable('oauth_provider', 'facebook');
						$objUser->setVariable('oauth_uid', (int) $user['id']);
						$objUser->setVariable('active', ACTIVE_STATUS_ENABLED);
						$objUser->setVariable('access_level', 1);
						$objUser->setVariable('username', $user['email']);
						$objUser->setVariable('password', $strPassWord);
						$objUser->setVariable('email', $user['email']);
						$objUser->setVariable('first_name', $user['first_name']);
						$objUser->setVariable('last_name', $user['last_name']);
						$objUser->setVariable('ipAddress', $_SERVER['REMOTE_ADDR']);
						$objUser->save();
						// Send the welcome email
						$this->getUser()->sendWelcomeEmail($objUser);
						// Login the user in the site.
						$this->oauthUserObjectLogin($objUser);
					}
					
				} else {
					# For testing purposes, if there was an error, let's kill the script 
					//die("There was an error.");
					URL::redirect($facebook->getLoginUrl(array(
						'scope' => 'email,publish_stream,read_friendlists'
					)));
				}
			} else {
				# There's no active session, let's generate one 
				//$login_url = $facebook->getLoginUrl();
				URL::redirect($facebook->getLoginUrl(array(
					'scope' => 'email,publish_stream,read_friendlists'
				)));
			}	
		} 
		
		
		/**
		 * This method logs the user in via the older Facebook API (oauth v1)
		 * Example Usage:
		 	if (! $Application->getUser()->getId()) 
			{
		 		SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");		
				$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_FACEBOOK);
				$objOauthLogin->setUser($Application->getUser());
				$objOauthLogin->setUrlData($_GET);
				$objOauthLogin->setPostData($_POST);
				$objOauthLogin->authenticate();
			}
			
			Make sure that the redirect URL is set properly in the Facebook Developer Section:
				- https://developers.facebook.com/apps
		 */ 
		private function v1_authenticate_Facebook() {
			$objDb 			= DATABASE::getInstance();
			$objSession 	= SESSION::getSession();
			$Application 	= APPLICATION::getInstance();
			
			// Load GET and POST data
			$arrGetData 	= $this->getUrlParams();
			$arrPostData 	= $this->getPostParams();
			
			$arrGetData['ref'] = "/";
			
			# Creating the facebook object
			$facebook = new Facebook(array(
				'appId'  => constant('__FB_APPLICATION_ID__'),
				'secret' => constant('__FB_APP_SECRET_ID__'),
				'cookie' => constant('__FB_USE_COOKIES__')
			));
			
			# Let's see if we have an active session
			$user = $facebook->getUser();
			if(false === empty($session)) {
				# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
				try
				{
					//$uid = $facebook->getUser();
					$user = $facebook->api('/me');
				} 
				catch (FacebookApiException $fbException)
				{
					/**
					 * Here, we have a cURL error.
					 * So we'll try to decipher the users
					 * info manually
					 */ 
					SITE_EXCEPTION::raiseSilentException($fbException);
					
					if (
						($arrSessionData = $facebook->getUser()) &&
						(isset($arrSessionData['authMethod']))
					) {
						$strUserDataUrl = $facebook->getUrl('graph', '/me', array('access_token' => $arrSessionData['access_token']));
						$objFileData = file_get_contents($strUserDataUrl);	
						$objUser = json_decode($objFileData);
						
						if (is_object($objUser))
						{
							$user = get_object_vars($objUser);
						}
						else 
						{
							// Try to reload.
							SESSION::destroySession();
							$facebook->setSession(NULL);
							$objUrl = new URL($Application->getUser()->getLoginUrl());
							$objUrl->clearAttribute();
							$objUrl->setAttribute('err', $fbException->getMessage());
							URL::redirect($objUrl->build());
							die;
						}
					} 
				}
				catch (Exception $e){
					require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php'); 
					SITE_EXCEPTION::throwNewException($e);
				}
				
				if(! empty($user) && isset($user['email'])){
					# We have an active session, let's check if we have already registered the user
					$objUser = SITE_USERS::getInstanceFromKey(array(
						'oauth_provider' =>	'facebook',
						'oauth_uid'		 => (int) $user['id']
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// if we dont have a oauth user, lets try to find a site user ...
					if (! ($objUser->getId()))  {
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'email' =>	$user['email']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					}
					
					# If not, let's add missing values to the database
					# Create the new user:
					if(
						(! ($objUser->getId())) 
					){
						$objUser->setVariable('creation_date', date("Y-m-d H:i:s"));
						$objUser->setVariable('activation_date', date("Y-m-d H:i:s"));
						$objUser->setVariable('username', $user['email']);
						$objUser->setVariable('password', $this->getUser()->generatePassword());
						$objUser->setVariable('oauth_uid', $user['id']);
						$objUser->setVariable('first_name', $user['first_name']);
						$objUser->setVariable('last_name', $user['last_name']);
						$objUser->setVariable('email', $user['email']);
						$objUser->setVariable('ipAddress', $_SERVER['REMOTE_ADDR']);
						$objUser->setVariable('active', ACTIVE_STATUS_ENABLED);
						$objUser->setVariable('access_level', 1);
						$this->getUser()->sendWelcomeEmail($objUser);
					}

					$objUser->setVariable('oauth_provider', 'facebook');
					$objUser->save();
					
					// this sets variables in the session 
					$objSession->set('oauth_uid', $objUser->getVariable('oauth_uid'));
					$objSession->set('oauth_provider', $objUser->getVariable('oauth_provider'));
					if (
						(! isset($user['email'])) ||
						(is_null($user['email']))			  
					) {
						// Here, we dont have extened permission from the user, which we need!
						// Now, well need to get extended permissions
						$url = $facebook->getLoginUrl(array(
							'req_perms' 	=> 'email,user_birthday,status_update,publish_stream,user_photos,user_videos',
							'cancel_url' 	=> __ROOT_URL__ . '/fb-connect/sorry.php',
							'next' 			=> URL::getCurrentUrl()
						));
						
						URL::redirect($url);
					} else {
						// Here, we have the user's extended permissions, so we have to check for site user
						// Get the site user:
						$objUser = SITE_USERS::getInstanceFromKey(array(
							'email' =>	$user['email']
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						
						if ((bool) $objUser->getId()) {
							// Here, we have a site user, log him im	
							if (strcmp($objUser->getVariable('oauth_uid'), $user['id']) <> 0) {
								// Update the oauth_uid
								$objUser->setVariable('oauth_uid', $user['id']);
								$objUser->save();
							}
							
							// Login the user in the site.
							$this->oauthUserObjectLogin($objUser);
							
						} else {
							// Here, the user is not a site user, we need to create a site user
							$strPassWord = $this->getUser()->generatePassword();
							$objUser = SITE_USERS::getInstanceFromKey(array(
								'oauth_uid' 	 =>	(int) $uid,
								'oauth_provider' => 'facebook'
							));
							/** Create the use rif he isn't already created... **/
							$objUser->setVariable('oauth_provider', 'facebook');
							$objUser->setVariable('oauth_uid', (int) $user['id']);
							$objUser->setVariable('active', ACTIVE_STATUS_ENABLED);
							$objUser->setVariable('access_level', 1);
							$objUser->setVariable('username', $user['email']);
							$objUser->setVariable('password', $strPassWord);
							$objUser->setVariable('email', $user['email']);
							$objUser->setVariable('first_name', $user['first_name']);
							$objUser->setVariable('last_name', $user['last_name']);
							$objUser->setVariable('ipAddress', $_SERVER['REMOTE_ADDR']);
							$objUser->save();
							// Send the welcome email
							$this->getUser()->sendWelcomeEmail($objUser);
							// Login the user in the site.
							$this->oauthUserObjectLogin($objUser);
						}
					}
				} else {
					# For testing purposes, if there was an error, let's kill the script 
					//die("There was an error.");
					URL::redirect($facebook->getLoginUrl());
				}
			} else {
				# There's no active session, let's generate one 
				//$login_url = $facebook->getLoginUrl();
				URL::redirect($facebook->getLoginUrl());
			}	
		}
		
		/**
		 * This method authenticates the user via the FourSquare network
		 */
		  private function authenticate_FourSquare() 
		  {
				if ($this->hasAuthRequest()) 
			  	{
					OAUTH_FOURSQUARE::authenticationRequest
					(
						constant('__FOURSQUARE_API_KEY__'),
						constant('__FOURSQUARE_APP_SECRET_ID__')
					);  
			  	}
		  }
 
		/**
		 * This method authenticates the user via the LinkedIn network
		 */
		  private function authenticate_LinkedIn() 
		  {
			/*
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
			 */
			try
			{
				if ($this->hasAuthRequest()) 
			  	{
					OAUTH_LINKEDIN::authenticationRequest
					(
						constant('__LINKEDIN_API_KEY__'),
						constant('__LINKEDIN_APP_SECRET_ID__')
					);
				}
			}
			catch(LinkedInException $e)
			{
				
			}
		 }
		 
		/**
		 * This method authenticates the user via the GitHub network
		 */ 
		private function authenticate_GitHub() {
			$objOAuthGitHub = OAUTH_GITHUB::getInstanceFromParent($this);
			if ($this->hasAuthRequest()) 
			{
				if (FALSE === (((int) $objOAuthGitHub->authenticate()) == OAUTH_GITHUB::OAUTH_GITHUB_STATUS_SUCCESS))
				{
					// Something went wrong.. print the error or do something else!
					// new dump($this->getLastException());
				}
			}
		}
				
		/**
		 * This method logs the user in via Yahoo API
		 */
		 private function authenticate_OpenId() {
			if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_Yahoo() { 
			if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_Google() { 
			if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_GoogleProfile() {
			 if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_MyOpenId() {
			 if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_MySpace() {
			 if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }
		 
		 private function authenticate_Blogger() {
			 if ($this->hasAuthRequest())
				$this->openIdAuthenticate(); 
		 }

		 private function authenticate_Aol() {
			 if ($this->hasAuthRequest())
					$this->openIdAuthenticate();
		 }
	}