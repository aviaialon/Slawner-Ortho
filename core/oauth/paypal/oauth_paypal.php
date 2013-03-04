<?php
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	
	class OAUTH_PAYPAL extends OAUTH_AUTHENTICATION {
		
		const OAUTH_PAYPAL_STATUS_SUCCESS 	= 1;
		const OAUTH_PAYPAL_STATUS_CANCEL 	= 2;
		const OAUTH_PAYPAL_STATUS_ERROR 	= 3;
		
		protected static $objOauthParent = NULL;	
		private static $blnIsInstace 	 = false;
		private $strErrorMessage 		 = NULL;
				
		public function __construct() {
			if (! (bool) self::$blnIsInstace)	
			{
				throw new Exception(__CLASS__ . " requires loading from static method " . __CLASS__ . "::getInstanceFromParent(...)");	
				exit();
			}
		}
		/**
		 * Private setters
		 */
		
		/**
		 * This method will load an instance from the parent (se we can access the same parameters)
		 * @param: OAUTH_AUTHENTICATION - The parent oAuth Object
		 * @return: OAUTH_PAYPAL - Instance of
		 */
		public static function getInstanceFromParent(OAUTH_AUTHENTICATION $objParent)
		{
			OAUTH_PAYPAL::$blnIsInstace = true;
			OAUTH_PAYPAL::$objOauthParent = $objParent; 
			$objSelf = new self();
			return ($objSelf);
		}
		
		/**
		 * This method returns the initial login form
		 * $blnAutoSubmit - Boolean : Auto submit the form on load [default: false]
		 * @return void
		 */
		public final function loadOauthRequestForm($blnAutoSubmit = false) {
			$objParent = $this->getParent();
			$objSession = SESSION::getInstance();
		?>
			<form action="<?php echo($objParent->getOpenIdEndPointUrl()); ?>" method="post" id="PayPal_Login_Form" target="_blank">
				<input name="oauth_pptoken" value="requestStart" type="hidden" />
				<input name="cancelURL" value="<?php echo($objParent->getCancelUrl()); ?>" type="hidden">
				<input name="cancelURL_d" value="<?php echo($objParent->getCancelUrl()); ?>" type="hidden">
				<!--<input name="endJSLoc" value="https://www.x.com/sites/default/files/webform/identity/ppa_endjs.js" type="hidden">-->
				<input name="endURL" value="<?php echo($objParent->getOpenIdEndPointUrl()); ?>" type="hidden">
				
				<!-- This value determines the app id. cant use sub path ex https://www.(..).com/subfolder and no trailing slashes -->
				<input name="realm" value="<?php echo(__PAYPAL_API_RELM_URL__); ?>" type="hidden">
				<input name="realm_d" value="<?php echo(__ROOT_URL__); ?>" type="hidden">
				
				<input name="required" value="http://axschema.org/namePerson/first,http://axschema.org/namePerson/last,http://schema.openid.net/contact/fullname,http://axschema.org/contact/email,http://axschema.org/birthDate,http://axschema.org/contact/postalCode/home,http://axschema.org/contact/country/home,http://axschema.org/pref/language,http://axschema.org/pref/timezone,http://schema.openid.net/contact/street1,http://schema.openid.net/contact/street2,http://axschema.org/contact/city/home,http://axschema.org/contact/state/home,http://axschema.org/contact/phone/default,https://www.paypal.com/webqapps/auth/schema/verifiedAccount,https://www.paypal.com/webapps/auth/schema/payerID,https://www.paypal.com/webapps/auth/schema/accountType,https://www.paypal.com/webapps/auth/schema/accountCreationDate" type="hidden">
                
				<input name="returnURL" value="<?php echo($objParent->getOpenIdEndPointUrl()); ?>" type="hidden">
				<input name="returnURL_d" value="<?php echo($objParent->getOpenIdEndPointUrl()); ?>" type="hidden">
				<input type="image" src="/static/images/paypal-icon.png" class="tooltip" title="Login via payPal" 
				width="20" height="20" style="display:inline;border:0px;width:20px;height:20px;<?php echo($blnAutoSubmit ? 'display:none;' : ''); ?>" id="PayPal_Login_Form_Submit_Btn" />
			</form>
            <?php if ($blnAutoSubmit) { ?>
            	<script type="text/javascript">
					window.onload = function() {
						document.getElementById('PayPal_Login_Form').submit();
						window.setTimeout(function() {
							document.getElementById('PayPal_Login_Form_Submit_Btn').style.display='block';
						}, 3000);
					}
				</script>
            <?php } ?>
		<?php
		}
		
		/**
		 * Step 1 in the oAuth process.
		 */
		public final function oAuth_step1()
		{
			/**
			 * PayPal Access 2011 | ppa_start_auth.php
			 * 
			 * The PayPal Access button will post to this code to initiate the login process.
			 * 
			 * OVERVIEW
			 *
			 *   1. Consume post parameters from the login button and set some
			 *      parameters in the session for use by ppa_end_auth.php.
			 *      
			 *   2. Create an OpenID Authentication request object by calling begin. This
			 *      will start the openID discovery process. 
			 *      
			 *   3. Setup up attributes and PAPE extensions on Authentication request
			 *      
			 *   4. Post to the authentication request to the identity server.  
			 *
			 **/
			
			
			/** 
			 * $oid_identifier 
			 * OpenID identifier, also called an OpenID URL or simply an OpenID. Points to your openID provider (OP). 
			 **/
			
			$oid_identifier = 'https://www.paypal.com/webapps/auth/server';
			
			
			/** ppa_common.php
			 * Utilities and includes for openID library. 
			 **/ 
			require_once __APPLICATION_ROOT__ . "/oauth/paypal/ppa/common.php";
			
			
			/**
			 * Creates a session or resumes the current one based on a session identifier passed via a GET or POST request, or passed via a cookie. 
			 **/
			
			$objSession = SESSION::getInstance();
			
			
			/**
			 * Consume post parameters from login button
			 * 
			 * $max_auth_age > Some description of what this is
			 * $realm > http://*.mydomain.com 
			 * $endURL > Location of helper php code ppa_end_auth.php
			 * $returnURL > When authentication is complete url to return to 
			 * $cancelURL > If you cancels during authentication flow when to send them
			 * $debug > Flag to show debug information
			 * $version > Version of bundle
			 * $endJSLoc > Location of javascript used to define behavior of end page (ppa_end_auth.php)
			 * 
			**/
			
			$max_auth_age = getConfig( 'PPA_MAX_AUTH_AGE' ); 
			if ( empty( $max_auth_age ) || !isset( $max_auth_age ) ) {
				$max_auth_age = NULL;
			}
			
			$realm = getPostParameter( 'realm' );
			if ( !isset( $realm ) ) {
				$realm =  getConfig( 'PPA_REALM' );
				if ( empty( $realm ) || !isset( $realm ) ) {
					$realm = getPostParameter( 'realm_d' );
				}
			} 
			
			$required = getPostParameter( 'required' );
			if ( !isset( $required ) ) {
				$required = getConfig( 'PPA_REQUIRED_ATTRS' );
			}
			 
			// $endURL = getPostParameter( 'endURL', getURL( 'ppa_end_auth.php' ) ); 
			$endURL = getPostParameter( 'endURL', $this->getParent()->getOpenIdEndPointUrl()); 
			
			$endJSLoc = getPostParameter( 'endJSLoc' );
			
			$returnURL = getPostParameter( 'returnURL' );
			if ( !isset($returnURL) ) {
				$returnURL =  getConfig( 'PPA_RETURN_URL' );
				if ( empty( $returnURL ) || !isset( $returnURL ) ) {
					$returnURL = getPostParameter( 'returnURL_d' );
				}
			} 
			
			$cancelURL = getPostParameter( 'cancelURL' );
			if ( !isset( $cancelURL ) ) {
				$cancelURL = getConfig( 'PPA_CANCEL_URL' );
				if ( empty( $cancelURL ) || !isset( $cancelURL ) ) {
					$cancelURL = getPostParameter( 'cancelURL_d' );
				}
			} 
			
			$debug = getPostParameter( 'debug', false );
			
			$version = getPostParameter( 'version', '1' );
			
			
			/** Pass Thru Parameters
			 *  Put some of the parameters in the session
			 *  to be consumed by end page (ppa_end_auth.php) 
			**/
			$objSession->set('ppa_endJSLoc', $endJSLoc);
			$objSession->set('ppa_returnURL', $returnURL);
			$objSession->set('ppa_endURL', $endURL);
			$objSession->set('ppa_cancelURL', $cancelURL);
			$objSession->set('ppa_version', $version);
			$objSession->set('ppa_debug', $debug);
						
			$consumer = new Auth_OpenID_Consumer();
			$auth = $consumer->begin( $oid_identifier );
			
			/** PAPE extensions
			 *  Phishing-Resistant Authentication
			 *  http://schemas.openid.net/pape/policies/2007/06/phishing-resistant
			 *   
			 * An authentication mechanism where a party potentially under the control of
			 * the Relying Party can not gain sufficient information to be able to successfully
			 * authenticate to the End User's OpenID Provider as if that party were the End User.
			 * (Note that the potentially malicious Relying Party controls where the User-Agent is
			 * redirected to and thus may not send it to the End User's actual OpenID Provider).
			 *
			 * Multi-Factor Authentication
			 * http://schemas.openid.net/pape/policies/2007/06/multi-factor
			 *
			 * An authentication mechanism where the End User authenticates to the OpenID Provider
			 * by providing more than one authentication factor. Common authentication factors are something
			 * you know, something you have, and something you are. An example would be authentication using
			 * a password and a software token or digital certificate.
			 *
			 * Physical Multi-Factor Authentication
			 * http://schemas.openid.net/pape/policies/2007/06/multi-factor-physical
			 *
			 * An authentication mechanism where the End User authenticates to the OpenID Provider
			 * by providing more than one authentication factor where at least one of the factors is a physical
			 * factor such as a hardware device or biometric. Common authentication factors are something you know,
			 * something you have, and something you are. This policy also implies the Multi-Factor Authentication
			 * policy (http://schemas.openid.net/pape/policies/2007/06/multi-factor) and both policies MAY BE specified
			 * in conjunction without conflict. An example would be authentication using a password and a hardware token.
			 * 
			 * 
			**/
			
			$pape_policy_uris = array (
				PAPE_AUTH_PHISHING_RESISTANT,
				PAPE_AUTH_MULTI_FACTOR,
				PAPE_AUTH_MULTI_FACTOR_PHYSICAL
			);
			
			$pape_request = new Auth_OpenID_PAPE_Request( $pape_policy_uris, $max_auth_age );
			if ( $pape_request && method_exists($auth, 'addExtension')) {
				$auth->addExtension( $pape_request );
			}
			
			if ( isset( $required ) ) {
			  $attribute = array_map( 'attrMap', explode( ',', $required ));
			  $ax = new Auth_OpenID_AX_FetchRequest;

			  foreach ( $attribute as $attr ){
				  $ax->add( $attr );
			  }
			  
			  if (! is_object($auth)) {
			  	// Failed Curl Response from server
			  	// Log the error and forward,
			  	// the request to a busy URL
			  	$objBusyUrl = new URL($this->getParent()->getOpenIdEndPointUrl());
			  	$objBusyUrl->clearAttribute();
			  	$objBusyUrl->setAttribute('err', 'The system is currently busy.');
			  	$objBusyUrl->setAttribute('desc', 'No response received from ' . $this->getParent()->getAuthMode() . '. Please try again in a few moments.');
			  	$objBusyUrl->addSession();
			  	$objBusyUrl->forward();
			  }
			  $auth->addExtension( $ax );;
			}
			
			
			/**
			 * Generate form to send authentication request to identity server   
			**/
			$form_id = 'paypal_oauth_openId_form';
			$submit_text = 'Continue';
			$form_tag_attrs = array( 'id' => $form_id );
			$message = $auth->getMessage( $realm, $endURL, false );
			$action_url = $auth->endpoint->server_url;
			$form = null;
			$form_html = '';       
			
			if ( Auth_OpenID::isFailure( $message ) ) {
			   $form = $message;
			}
			$form  = '';
			$form .= '<!DOCTYPE html><html><body><style type="text/css" media="screen">html, body {margin: 0; padding: 0; height: 100%;} body {font: 11px/1.7 Verdana, Geneva, Arial, sans-serif;} #wrap { position: relative; width: 400px; height: 100%; margin: 0 auto; background: #FFF; } #content { background: #fff; text-align: center; position: absolute; top: 40%; left: 38%; height: 50px; width: 110px; margin:0 auto;} #content span {font-weight: bold;font-size: 24px} .submit_btn {background-color:#3d94f6;border:1px solid #337fed;display:inline-block;color:#ffffff;font-family:arial;font-size:13px;font-weight:normal;padding:6px 24px;text-decoration:none;text-shadow:1px 1px 0px #1570cd;}.submit_btn:hover {background-color:#1e62d0;}.submit_btn:active {position:relative;top:1px;}</style><script type="application/javascript" src="http://fgnass.github.com/spin.js/dist/spin.min.js"></script>';
			$form .= '<div id="wrap"><div id="content" align="center"><div id="__LOADING__"></div><br /><br />Please Wait While We Proceed.<br /> ';
			$form .= '<script type="application/javascript">window.onload = (function() { var opts = { '.
						  'lines: 13, length: 7, width: 4, radius: 10, corners: 1, '.
						  'rotate: 0, color: \'#004D84\', speed: 1, trail: 60, shadow: false, '.
						  'hwaccel: false, className: \'spinner\', zIndex: 2e9, top: \'auto\', left: \'auto\' '.
						'}; ' .
						'var target = document.getElementById(\'__LOADING__\'); ' .
						'var spinner = new Spinner(opts).spin(target); });</script>';
			$form .= '<form accept-charset="UTF-8" enctype="application/x-www-form-urlencoded"';
			
			if ( !$form_tag_attrs ) {
				$form_tag_attrs = array();
			}
			
			$form_tag_attrs['action'] = $action_url;
			$form_tag_attrs['method'] = 'post';
			
			unset( $form_tag_attrs['enctype'] );
			unset( $form_tag_attrs['accept-charset'] );
			
			if ( $form_tag_attrs ) {
				foreach ( $form_tag_attrs as $name => $attr ) {
					$form .= sprintf( " %s=\"%s\"", $name, $attr );
				}
			}
			
			$form .= ">\n";
			
			foreach ( $message->toPostArgs() as $name => $value ) {
				$form .= sprintf(
					"<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",
					$name, urldecode($value));
			}
			
			$form .= sprintf(
				"<center><br /><input class='submit_btn' style='display:none;' id='mmsSubmitPPa' type=\"submit\" value=\"%s\" /></center>\n",
				$submit_text);
			
			$form .= "</form></center></p></div></div>\n";
			
			// clear the stream in case page output has started...
			$strpageOut = ob_get_clean();
			
			print ($form);   
			if (! Auth_OpenID::isFailure( $form ) ) {
				print (
					'<script type="text/javascript">window.setTimeout(function() ' .
					'{document.forms["paypal_oauth_openId_form"]["mmsSubmitPPa"].style.display = "block";}, 4000); ' .
					'window.setTimeout(function() {document.forms["paypal_oauth_openId_form"].submit(); }, 1500)</script>'
				);
				/*print ("<script>".
					   "var elements = document.forms['paypal_oauth_openId_form'].elements;".
					   "for (var i = 0; i < elements.length; i++) {".
					   "  elements[i].style.display = \"none\";".
					   "}".
					   "</script>");*/
			}
			print ('</body></html>');
			die;
			/*
			if ( Auth_OpenID::isFailure( $form ) ) {
				$form_html = $form;
			} else {
				$form_html = Auth_OpenID::autoSubmitHTML( $form, 'PayPal OpenID Login', 'paypal_oauth_openId_form');
			}
			print ($form_html);   
			*/
		}
		
		/**
		 * Step 2 in the oAuth process.
		 */
		public final function oAuth_step2()
		{
			/**
			 * PayPal Access 2011 | ppa_end_auth.php
			 *  
			 * 
			 *
			 **/
			
			 
			/** ppa_common.php
			 * Utilities and includes for openID library. 
			 **/ 
			
			require_once __APPLICATION_ROOT__ . "/oauth/paypal/ppa/common.php";
			
			
			/**
			 * Creates a session or resumes the current one based on a session identifier passed via a GET or POST request, or passed via a cookie. 
			 **/
			
			$objSession = SESSION::getInstance();
			
			
			/**
			 * Grab session data from the start page (ppa_start_auth.php)
			 **/
			
			$returnURL 	= $objSession->get('ppa_returnURL');
			$cancelURL 	= $objSession->get('ppa_cancelURL');
			$endURL 	= $objSession->get('ppa_endURL');
			$endJSLoc 	= $objSession->get('ppa_endJSLoc');
			
			/**
			 * Create an OpenID response object by calling complete. This
			 * will start the openID verify process. 
			 **/
			$consumer = new Auth_OpenID_Consumer( getStore() );
			$response = $consumer->complete( $this->getParent()->getOpenIdEndPointUrl() );
			
			
			/**
			 * status of authentication request
			 * success, cancel, error
			 **/
			$status = null;
			
			
			/**
			 * if success
			 * Storing the openid response in the session and 
			 * redirecting the user to the returnURL
			 * 
			 * if cancel
			 * setting status to cancel so the user will be redirected to the cancelURL
			 * 
			 **/
			if ( $response->status == Auth_OpenID_SUCCESS ) {
				$ax = new Auth_OpenID_AX_FetchResponse();
				$obj = $ax->fromSuccessResponse( $response );
				$reqData = array_merge( $_POST, $_GET );
				
				$dataArr = array();
				$attributeArr = array();
				
				foreach ($obj->data as $key=>$val) {
					$attributeArr[$key] = $val[0]; 	
				}
				
				$dataArr['attributes'] = $attributeArr;
				$dataArr['openid_claimed_id'] = $reqData['openid_claimed_id'];
				$dataArr['openid_identity'] = $reqData['openid_identity'];
				$objSession->set('ppa_data', $dataArr);
				$objSession->set('oauth_user_data', $attributeArr);
				
				$status = OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_SUCCESS;
			
				
			
			} elseif ( $response->status == Auth_OpenID_CANCEL ) {
				$this->strErrorMessage = 'User aborted oAuth.';
				$status = OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_CANCEL;
			} else {
				$this->strErrorMessage = $response->message;
				$status = OAUTH_PAYPAL::OAUTH_PAYPAL_STATUS_ERROR;
			}		
			return ($status);
		}
		
		/**
		 * This method returns the parent instance once initiated.
		 * @return OAUTH_PAYPAL - Instance of
		 */
		private final function getParent()
		{
			return (OAUTH_PAYPAL::$objOauthParent);
		}
		
		public function getErrorMessage()
		{
			return ($this->strErrorMessage);
		}
	}
	
	/** OpenID Attributes 
	 *  List of Attributes the Relying party is requesting to be 
	 *  shared from the identity provider 
	 *  POST ['required'] 
	 * 
	**/
	
	function attrMap( $attr ) {
	  return Auth_OpenID_AX_AttrInfo::make( $attr, 1, 1);
	}
?>