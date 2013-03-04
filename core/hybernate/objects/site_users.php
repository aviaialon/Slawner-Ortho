<?php
	/**
	 * SITE_USERS Administration Class
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
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::DATABASE::DATABASE");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::GEOLOCATION::GEO_LOCATOR");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::URL::URL");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::USERS::USERLOGIN_LOG");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::CRYPT::AES_CRYPTO");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::IMAGE::SITE_USERS_IMAGE");	
 	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");	
		 
	 define('SITE_USERS_ROLE_GUEST_MEMBER', 1);
	 define('SITE_USERS_ROLE_MOD_USER', 	2);
	 define('SITE_USERS_ROLE_ADMIN_USER', 	5);
	 define('SITE_USERS_ROLE_FULL_ADMIN', 	10);
	 
	 class SITE_USERS extends SHARED_OBJECT {
		private $restrictedDirectories = array(); 
		
		public function __construct() 
		{
			$this->Application = APPLICATION::getInstance();
			$this->objCrypt = AES_CRYPTO::getInstance();
			$this->objCrypt->setEncryptionKey(constant('__ENCRYPTION_KEY__'));
			$this->objCrypt->setEncryptionBits(AES_CRYPTO::AES_CRYPTO_ENCRYPT_CIPHER_BITS_128);	
		}
		
		/**
		 * This method initialises the current user based
		 * on another user's configurations
		 * @return:	SITE_USERS - The current user object
		 */
		 /*
		public function initialiseConfigFromCurrentUser() {
			$objCloneUserData 	= SITE_USERS::getCurrentUser();
			if (
				(is_object($objCloneUserData)) &&
				is_a($objCloneUserData, __CLASS__)
			) {
				$this->setLoginUrl($objCloneUserData->getLoginUrl());						# REWRITE ENGINE -> /login/index.php?rel=login
				$this->setSignupUrl($objCloneUserData->getSignupUrl());						# REWRITE ENGINE -> /login/index.php?rel=register
				$this->setPassReminderUrl($objCloneUserData->getPassReminderUrl());			# REWRITE ENGINE -> /login/index.php?rel=reminder
				$this->setChangePassUrl($objCloneUserData->getChangePassUrl()); 			# REWRITE ENGINE -> /login/index.php?rel=change-password
				$this->setConfirmMembershipUrl($objCloneUserData->getConfirmMembershipUrl()); # REWRITE ENGINE -> /login/index.php?rel=confirm-membership
				$this->setFacebookLoginUrl($objCloneUserData->getFacebookLoginUrl()); 		# REWRITE ENGINE -> /login/index.php?rel=facebook
				$this->setTwitterLoginUrl($objCloneUserData->getTwitterLoginUrl()); 		# REWRITE ENGINE -> /login/index.php?rel=twitter
				$this->setNoticeUrl($objCloneUserData->getNoticeUrl());
				$this->setProfileUrl($objCloneUserData->getProfileUrl());	
			}
			return ($this);
		}
		*/
		/**
		 * This method returns the current session user
		 * @return:	SITE_USERS - The current user object
		 */
		public static function getCurrentUser() {
			$intUserId = (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : 0);
			$objUser = (
				$intUserId ? SITE_USERS::getInstance((int) $intUserId) : new SITE_USERS()			
			);
			return ($objUser);	
		}
		
		/**
		 * This method returns the current ID (backwards compatibility)
		 */
		 public function getUserId() {
			return ($this->getId());	 
		 }
		 
		/**
		 * This method logs the user out
		 * @param: 	$blnRedirect - Boolean 	- Redirect the user?
		 * @return:	Void
		 */
		public function logout($blnRedirect = true) {
			$objUser 	= SITE_USERS::getCurrentUser(); 
			$objDb	 	= DATABASE::getInstance();
			if ((bool) $objUser->getId()) {
				$objDb->query("
					UPDATE 	site_users 
					SET 	site_users.time_spent_last_login = (
							UNIX_TIMESTAMP(NOW()) - " . (int) strtotime($this->getVariable('last_access_date')) . "
					)		
					WHERE 	site_users.id = " . (int) $this->getId()
				);
			}
			//unset($_GET);
			//unset($_POST);
			$strLang = SESSION::get('lang');
			SESSION::destroySession();
			SESSION::getSession();
			SESSION::set('lang', $strLang);
			if ($blnRedirect) {
				SESSION::set('info', $this->Application->translate('You have successfully logged out of the system.', 'Vous avez réussi à vous connecter sur le système.'));
				URL::redirect($_SERVER['PHP_SELF']);
			}
		}
		
		/**
		 * This method logs the user in
		 * @param: 	$strUserName 	- String 	- The Username
		 * @param: 	$strPassword 	- String 	- The Password
		 * @param: 	$blnUseRedirect - Boolean 	- Redirect the user after login?
		 * @param:	$intAccessLevel - int		- Optional: The minimum required access level to login (default SITE_USERS_ROLE_GUEST_MEMBER)
		 */
		public function login($strUserName="", $strPassword="", $blnUseRedirect = true, $intAccessLevel = SITE_USERS_ROLE_GUEST_MEMBER) {
			$this->logout(false);
			$_GET['ref'] = (
				isset($_GET['ref']) ? $_GET['ref'] : ($blnUseRedirect ? __ROOT_URL__ : "")				
			);
			
			if (! strlen($strUserName)) {
				unset($_GET);
				SESSION::set('err', $this->Application->translate('Please provide your username.', 'Veuillez fournir votre nom d\'utilisateur.'));
				$_REQUEST['err_field'] = 'username';
				return (false);
			}
			
			if (! strlen($strPassword)) {
				unset($_GET);
				SESSION::set('err', $this->Application->translate('Please provide your password.', 'Veuillez fournir votre mot de passe.'));
				$_REQUEST['err_field'] = 'password';
				return (false);
			}
			
			$objUser = SITE_USERS::getInstanceFromKey(array(
				'username'		=>	trim($strUserName),
				'password'		=> 	$this->objCrypt->mysqlEncrypt(trim($strPassword)),
				'active'		=> 	ACTIVE_STATUS_ENABLED
			), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			
			if (
				((bool) $objUser->getId()) &&
				((bool) $objUser->fitsInRole((int) $intAccessLevel))
			) {
				$objUser->setVariable('last_access_date', date("Y-m-d H:i:s"));
				$objUser->save();
				
				$objSession = SESSION::getSession();
				$objSession->set('USER_ID', (int) $objUser->getId());
				
				// Log the user login
				$objUserLoginLog = new USERLOGIN_LOG();
				$objUserLoginLog->setVariable('userId', 		$objUser->getId());
				$objUserLoginLog->setVariable('oauth_userid', 	$objUser->getVariable('oauth_uid'));
				$objUserLoginLog->setVariable('oauth_provider', $objUser->getVariable('oauth_provider'));
				$objUserLoginLog->setVariable('ipAddr', 		$_SERVER['REMOTE_ADDR']);
				$objUserLoginLog->save();
				
				// Save the user to Application.
				// This is done here because otherwise
				// we have to wait for a page refresh
				// se we can get the userId from the sesssion
				// to load the proper USER object and bind it
				// to the current applciation instance
				if (TRUE === class_exists('APPLICATION'))
				{
					$Application = APPLICATION::getInstance();
					$Application->setUser($objUser);	
				}
				
				if (
					(isset($_POST['remember'])) &&	
					((bool) $_POST['remember'])	
				) {
					setcookie(constant('__SESSION_NAME__') . '__USERNAME__', (string) $strUserName, strtotime("+7 day"), "/");
				}
				else
				{
					unset($_COOKIE[constant('__SESSION_NAME__') . '__USERNAME__']);	
				}
				/* $_GET['sec'] = "You have successfuly logged in, " . 
							(strlen($objUser->getVariable('first_name')) ? $objUser->getVariable('first_name') : $objUser->getVariable('username')); */
				
				SESSION::set('ok', $this->Application->translate("You have successfuly logged in, ", "Vous avez connecté avec succès, ") . 
							(strlen($objUser->getVariable('first_name')) ? $objUser->getVariable('first_name') : $objUser->getVariable('username')));		

				if ((isset($_GET['ref']) && strlen($_GET['ref'])) || (isset($_POST['ref']) && strlen($_POST['ref']))) {
					$objUrl = new URL(isset($_POST['ref']) ? $_POST['ref'] : $_GET['ref']);
					$objUrl->clearAttribute();
					$objUrl->addSessionAttributes();
					$objUrl->setAttribute('ok', $_GET['ok']);
					empty($_POST);
					unset($_POST);
					URL::redirect($objUrl->build());
					die;
				}
			} 
			else 
			{
				if (ctype_upper($strUserName) || ctype_upper($strPassword))
				{
					unset($_GET);
					SESSION::set('err', $this->Application->translate('Sorry Login Failed: Please check your <a>CAPS LOCKS</b>.', 'Désolé Connexion Échec: Veuillez vérifiez vos <a> SERRURES CAPS </ b>'));
				}
				else if (
					((bool) $objUser->getId()) &&
					(! ((bool) $objUser->fitsInRole((int) $intAccessLevel)))
				) {
					unset($_GET);
					SESSION::set('err', $this->Application->translate(
						'Access Denied: Please ask your administrator to grant you access.', 
						'Accès refusé: Veuillez demander à votre administrateur de vous accorder l\'accès.'
					));
				}
				else
				{
					unset($_GET);
					SESSION::set('err', $this->Application->translate('Sorry username and / or password do not match.', 'Désolé nom d\'utilisateur et / ou mot de passe ne correspondent pas.'));
				}
				return (false);	
			}
			return ($objUser);
		}
		
		/**
		 * This method generates a random password
		 * @param: 	$length - Integer 	- The Password length
		 * @param: 	$level 	- String 	- The Password level
		 * @return:	$password - String 	- The random generated password
		 */
		public function generatePassword($length=6,$level=2){
		   list($usec, $sec) = explode(' ', microtime());
		   srand((float) $sec + ((float) $usec * 100000));
		   $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
		   $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		   $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";
		
		   $password  = "";
		   $counter   = 0;
		
		   while ($counter < $length) {
			 $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
		
			 // All character must be different
			 if (!strstr($password, $actChar)) {
				$password .= $actChar;
				$counter++;
			 }
		   }
		   return ($password);
		}
		
		/**
		 * This method checks if a current user is a defined role
		 * @param: 	$intAccessLevel - Integer 	- The requested role
		 * @return:	$blnIsInRole 	- Boolean 	- If the current user fits the role
		 */
		public function isInRole($intAccessLevel=NULL) {
			$blnIsInRole = (
				((! is_null($intAccessLevel)) && (strcmp($this->getVariable('access_level'), $intAccessLevel) === 0))			
			);
			return ($blnIsInRole);
		}
		
		/**
		 * This method checks if a current user FITS a defined role
		 * @param: 	$intAccessLevel - Integer 	- The requested role
		 * @return:	$blnIsInRole 	- Boolean 	- If the current user fits the role
		 */
		public function fitsInRole($intAccessLevel=NULL) {
			$blnFitsInRole = (
				((! is_null($intAccessLevel)) && ($this->getVariable('access_level') >= $intAccessLevel))			
			);
			return ((bool) $blnFitsInRole);
		}
		
		/**
		 * This method checks if a user exists by the email
		 * @param: 	$strEmail 		- String 	- The user's email
		 * @return:	$rsUser 		- Boolean 	- If the user's email  exist
		 */
		public function isUser($strEmail) {
			if (is_null($strEmail)) return(false);
			$objDb = new DATABASE();
			$rsUser = $objDb->query(
				'SELECT	`site_users`.id
				FROM	`site_users`
				WHERE	`site_users`.email="' . $objDb->escape($strEmail) . '"  
				LIMIT 	1'
			);
			return ((bool) count($rsUser));
		}
		
		/**
		 * This method checks if a user exists by the username
		 * @param: 	$strUserName 	- String 	- The user's username
		 * @return:	$rsUser 		- Boolean 	- If the user's username exist
		 */
		public function userNameExists($strUserName) {
			if (is_null($strUserName)) return(false);
			$objDb = new DATABASE();
			$rsUser = $objDb->query(
				'SELECT	`site_users`.id
				FROM	`site_users`
				WHERE	`site_users`.username="' . $objDb->escape($strUserName) . '"  
				LIMIT 	1'
			);
			return ((bool) count($rsUser));
		}
		
		/**
		 * This method sends a password reset to a user
		 * @param: 	$strEmail 								- String 	- The user's email
		 * @param: 	REQUEST_DISPATCHER	$objCallbackClass 	- Object 	- The callback object that will receive a response
		 * @return:	$blContinue - Boolean 					- If the execution was successful
		 */
		public function sendResetPassword($strEmail, REQUEST_DISPATCHER $objCallbackClass) {
			$blContinue = true;
			$Application = APPLICATION::getInstance();
			
			// Validate the input
			if (false === VALIDATOR::email($strEmail))
			{
				SESSION::set('err', $Application->translate(
					'Please enter a valid email address.', 
					'Veuillez entrer une adresse email valide.'
				));
				$blContinue = false;
			}
			// 
			
			// Make sure the user exists
			if ((true === $blContinue) && (! $this->isUser($strEmail)))	{
				SESSION::set('info', $Application->translate(
					'Thank you, instructions have been sent to reset your password.', 
					'Merci, des instructions ont été envoyées pour réinitialiser votre mot de passe.'
				));
				$blContinue = false;
			}
			
			if (true === $blContinue) 
			{
				$objUser = 	SITE_USERS::getInstanceFromKey(array(
					'email' => $strEmail												 
				));
				// Buils the reset password link
				$strResetPasswordLink = $Application->getRequestDispatcher()->createCallbackUrl($objCallbackClass, 'onPasswordResetCallback', array(
					'authToken' => $objUser->getEmail() . ':' . $objUser->getPassword(),
					'lang'		=> $Application->translate('en', 'fr')
				));
				
				// Send the confirmation email.
				$objMailer  = new MAIL();
				$objSession = SESSION::getSession();
				$objMailer->setData(array(
					'SITE_LINK'			=> __ROOT_URL__,
					'ROOT'				=> __ROOT_URL__	,
					'SITE_NAME'			=> __SITE_NAME__,
					'DATE'				=> 	date("F j, Y"),
					'YEAR'				=> 	date("Y"),
					'ROOT_URL' 			=> __ROOT_URL__, 
					'SITE_NAME' 		=> __SITE_NAME__, 
					'TITLE' 			=> $this->Application->translate('Password Reminder', 'Rappel mot de passe')	
				));
				$objMailer->setTo(filter_var($strEmail, FILTER_VALIDATE_EMAIL));
				$objMailer->setFrom(ucwords(__SITE_NAME__) .  (' <reminder@' . __SITE_DOMAIN__ . '>'));
				$objMailer->setSubject($Application->translate("Password Reminder From ", "Rappel mot de passe de ") . ucwords(__SITE_NAME__));
				$objMailer->setTemplate('static/templates/email/reset-password.tmpl');
				$objMailer->setMessage($Application->translate(
					"<h2>Dear " . $objUser->getFullName() . "</h2><br />" .
					"<p>You have requested a password reminder for your account at <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_NAME__) . "</a></p>" .
					"<p>Below, you will find a link to reset your password.</p>" .
					"<p>If you have not requested this email, please disregard it.</p>" .
					'<br /><p><div style="padding-left:20px;">'.
					'<a href="' . $strResetPasswordLink . '">' . $strResetPasswordLink . '</a></p>' .
					 "<br /><br />Thanks again and best regards,<br />The <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_NAME__) . "</a> Team.",
					 "<h2>Cher " . $objUser->getFullName() . "</h2><br />" .
					"<p>Vous avez demandé un rappel de mot de passe pour votre compte sur <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_NAME__) . "</a></p>" .
					"<p>Ci-dessous, vous trouverez un lien pour réinitialiser votre mot de passe.</p>" .
					"<p>Si vous n'avez pas demandé cet e-mail, s'il vous plaît l'ignorer.</p>" .
					'<br /><p><div style="padding-left:20px;">'.
					'<a href="' . $strResetPasswordLink . '">' . $strResetPasswordLink . '</a></p>' .
					 "<br /><br />Bien a vous,<br /> L'équipe <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_NAME__) . "</a> ."	
				));
				
				$blContinue = $objMailer->send();

				if ($blContinue) 
				{
					SESSION::set('info', $Application->translate("An email reminder has been sent to: " . $strEmail, 'Un courriel de rappel a été envoyée à ' . $strEmail));
				} 
				else 
				{
					SESSION::set('err', $Application->translate(
						"Sorry, we are experiencing technical difficulties.<br />Please try again later.",
						"Désolé, nous éprouvons des difficultés techniques. <br /> Veuillez réessayer plus tard."
					));
				}
				
				//require_once(__SITE_ROOT__ . '/templates/email/email-template.php');
				//$_GET['ok'] = "An email reminder has been sent to: <b>" . $strEmail . "</b>";
				empty($_POST);
				unset($_POST);
			}
		}
		
		/**
		 * This method sends a password reminder to a user
		 * @param: 	$strEmail 	- String 	- The user's email
		 * @return:	$blContinue - Boolean 	- If the execution was successful
		 */
		public function sendPasswordReminder($strEmail, $blnRedirect=true) {
			$blContinue = true;
			// Make sure the user exists
			if (! $this->isUser($strEmail))	{
				SESSION::set('info', $this->Application->translate('Sorry, no account seem to be affiliated with this email.', 'Désolé, aucun compte semblent être affiliés à ce email.'));
				$blContinue = false;
			}
			if ($blContinue) {
				$objUser = 	SITE_USERS::getInstanceFromKey(array(
					'email' => $strEmail												 
				));
				
				// Send the confirmation email.
				$objMailer  = new MAIL();
				$objSession = SESSION::getSession();
				$objMailer->setData(array(
					'SITE_LINK'			=> __ROOT_URL__,
					'ROOT'				=> __ROOT_URL__	,
					'SITE_NAME'			=> __SITE_NAME__,
					'DATE'				=> 	date("F j, Y"),
					'ROOT_URL' 			=> __ROOT_URL__, 
					'SITE_NAME' 		=> __SITE_NAME__	
				));
				$objMailer->setTo(filter_var($strEmail, FILTER_VALIDATE_EMAIL));
				$objMailer->setFrom(ucwords(__SITE_NAME__) .  (' <reminder@' . __SITE_DOMAIN__ . '>'));
				$objMailer->setSubject("Password Reminder From " . ucwords(__SITE_NAME__));
				$objMailer->setTemplate('templates/email/template.tmpl');
				$objMailer->setMessage(
					"<h2>Dear " . $objUser->getFullName() . "</h2><br />" .
					"<p>You have requested a password reminder for your account at <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_DOMAIN__) . "</a></p>" .
					"<p>Below, you will find your login credentials.<br />Please safeguard this information carefully as it is sensitive and personal.</p>" .
					'<br /><div style="padding-left:20px;">'.
						'<table width="450" border="0" cellspacing="0" cellpadding="0">' .
							'<tr>'.
								'<td width="200">Your Username: </td>'.
								'<td><b>' . $objUser->getVariable('username') . '</b></td>' .
							'</tr>'.
							'<tr>'.
								'<td width="200">Your Password: </td>'.
								'<td><b>' . $objUser->getVariable('password') . '</b></td>'.
							'</tr>'.
						'</table></div><br />'.
					 "<br />Thanks again and best regards,<br />The <a href='" . __ROOT_URL__ . "'>" . ucwords(__SITE_NAME__) . "</a> Team."	
				);
				
				$blContinue = $objMailer->send();

				if ($blnRedirect) {
					$objRedirectUrl = new URL($this->getNoticeUrl());
					$objRedirectUrl->clearAttribute();
					$objRedirectUrl->addSessionAttributes();
					
					if ($blContinue) {
						$objRedirectUrl->setAttribute("ok", "An email reminder has been sent to: <b>" . $strEmail . "</b>");
						// Create a message to the user
						$objSession->set(
							'infoMessage',
							'Your ' . ucwords(__SITE_DOMAIN__) . ' login credentials associated with this account ' .
							'have been emailed to you.<br />' .
							'<br />Please do not hesitate to <a href="' . __ROOT_URL__ . '/contact">contact us</a> if you are still experiencing difficulties.<br />'.
							'Thanks Again!'
						);
					} else {
						$objRedirectUrl = new URL();
						$objRedirectUrl->setAttribute("err", "Sorry, an error occured.");
						$objSession->set(
							'infoMessage',
							'<div style="text-align:left"><p><b>A Error Occured.</b></p>'.
							'<p>Sorry, we are experiencing technical difficulties.<br />Please try again later.</p>'.
							'<p>Thanks again.</p></div>'
						);
					}
					URL::redirect($objRedirectUrl->build());
				}
				//require_once(__SITE_ROOT__ . '/templates/email/email-template.php');
				//$_GET['ok'] = "An email reminder has been sent to: <b>" . $strEmail . "</b>";
				empty($_POST);
				unset($_POST);
			}
			return ($blContinue);
		}
	
		/**
		 * This method registers and sends a new user the confirmation email for the registration.
		 * @param: 	$arrInfo - Users information.
		 * @return: Void
		 */	
		public function registerUser($arrInfo = array()) {
			if (! $this->validateRegistration($arrInfo)) return;
			ignore_user_abort();
			$this->logout(false);
			$objMailer 		= new MAIL();
			$objSession		= SESSION::getInstance();
			$objGeoLocator 	= GEO_LOCATOR::getInstance($_SERVER['REMOTE_ADDR'], SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE); 
			$objNewUser 	= SITE_USERS::getInstance(0, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$objNewUser->setVariable('active', ACTIVE_STATUS_PENDING);
			$objNewUser->setVariable('access_level', SITE_USERS_ROLE_GUEST_MEMBER);
			$objNewUser->setVariable('username', $arrInfo['user_name']);
			$objNewUser->setVariable('password', $arrInfo['password']);
			$objNewUser->setVariable('email', $arrInfo['user_email']);
			$objNewUser->setVariable('username', $arrInfo['user_name']);
			$objNewUser->setVariable('ref_page', $_SERVER['HTTP_REFERER']);
			$objNewUser->setVariable('creation_date', date("Y-m-d H:i:s"));
			$objNewUser->setVariable('last_access_date', date("Y-m-d H:i:s"));
			$objNewUser->setVariable('ipAddress', $objGeoLocator->getVariable('id'));
			$objNewUser->setVariable('activationKey', sha1(uniqid($objNewUser->getVariable('email'))));
			$objNewUser->setVariable('funds', 0);
			$objNewUser->setVariable('owedFunds', 0);
			
			if ($objNewUser->save()) {
				// Create the confirmnation url..
				$objConfirmationUrl = new URL($this->getConfirmMembershipUrl());
				$objConfirmationUrl->clearAttribute();
				$objConfirmationUrl->setAttribute('ukey', $objNewUser->getVariable('activationkey'));
				$objConfirmationUrl->addSessionAttributes();
				
				// Send the confirmation email.
				$objMailer->setData(array(
					'USERNAME' 			=> $objNewUser->getVariable('username'),
					'PASSWORD' 			=> $objNewUser->getVariable('password'),
					'ACTIVATION_LINK'	=> $objConfirmationUrl->build(),
					'SITE_LINK'			=> __ROOT_URL__,
					'ROOT'				=> __ROOT_URL__	,
					'SITE_NAME'			=> __SITE_NAME__,
					'DATE'				=> 	date("F j, Y")
				));
				$objMailer->setTo($objNewUser->getVariable('email'));
				$objMailer->setSubject('Welcome to %%SITE_NAME%% - Activation Email');
				$objMailer->setTemplate('templates/email/register-template.tmpl');
				
				if ($objMailer->send()) {				
					$objRedirectUrl = new URL($this->getNoticeUrl());
					$objRedirectUrl->clearAttribute();
					$objRedirectUrl->setAttribute('ok', 'Your registration was successful');
					$objRedirectUrl->addSessionAttributes();
					// Create a message to the user
					$objSession->set(
						'infoMessage',
						'<div style="text-align:left"><p><b>Thank you, ' . $objNewUser->getVariable('username') . '</b></p>'.
						'<p>Your registration is almost complete. You should receive a confirmation ' .
						'email. Please click on the confirmation link in order to activate your account ' .
						'with ' . ucwords(__SITE_NAME__) . '</p>'.
						'<p>Thanks again!</p></div>'
					);
					
					URL::redirect($objRedirectUrl->build());
					return(true);
				} else {
					$_GET['err'] = $objMailer->getError();
					// an error occured during the send mail process
				}
			} else {
				// an error occured during the saving process
			}
		}
		
		/**
		 * This method validates the iformation providede for a new user registration
		 * @param: 	$arrInfo 	- Users information.
		 * @return: $blContinue	- If the provided info passes validation.
		 */	
		public function validateRegistration($arrInfo=array()) {
			$blContinue = true;
			$arrRequiredFields = array(
				'user_name'		=> 'Please select a username.',
				'password'		=> 'Please enter a password.',
				'confpassword'	=> 'Please confirm your password.',
				'user_email'	=> 'Please enter your email address.'				   
			);						   
			foreach ($arrRequiredFields as $strFormKey => $strErrorMsg) {
				if (
					(! isset($arrInfo[$strFormKey])) ||
					(! strlen($arrInfo[$strFormKey]))
				) {
					SESSION::set('err', $strErrorMsg);
					$blContinue 	= false;
					break;
				}
			}
			if (((bool) $blContinue) && (! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $_POST['user_email']))) {
				SESSION::set('msg', 'Please enter a valid email address below.');
				$blContinue = false;
			}
			
			if (((bool) $blContinue) && (strcmp($_POST['password'], $_POST['confpassword']) !==0)) {
				SESSION::set('msg', 'Your selected passwords dont match.');
				$blContinue = false;
			}
			
			if (((bool) $blContinue) && (strcmp(strtolower($_POST['password']), (strtolower($_POST['user_name']))) !==0)) {
				SESSION::set('msg', 'Your password and username cannot be the same.');
				$blContinue = false;
			}
			
			if (((bool) $blContinue) && ($this->isUser($_POST['user_email']))) {
				SESSION::set('msg', 'Sorry, this email is already affiliated with an account.');
				$blContinue = false;
			}
			if (((bool) $blContinue) && ($this->userNameExists($_POST['user_name']))) {
				SESSION::set('msg', 'Sorry, this username is already taken.');
				$blContinue = false;
			}
			
			// Optional CAPTCHA fields
			if (((bool) $blContinue) && (isset($arrInfo["recaptcha_response_field"]))) {
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::RECAPTCHA::RECAPTCHALIB");
				$arrResponse = recaptcha_check_answer (
					__CAPTCHA_PRIVATE_KEY__,
					$_SERVER["REMOTE_ADDR"],
					$arrInfo["recaptcha_challenge_field"],
					$arrInfo["recaptcha_response_field"]
				);
				
				if (! ($arrResponse->is_valid)) {
					SESSION::set('msg', 'Please enter the <b>CAPTCHA</b> fields exactly as shown.');
					$blContinue = false;
				}
			}
			
			return ($blContinue);
		}
		
		/**
		 * This method sends a welcome email
		 * @param: 		$objUser  	- SITE_USERS - The current user
		 * @return: 	$blnReturn  - Boolean - If the mailer went out
		 */	
		public function sendWelcomeEmail(SITE_USERS $objUser) {
			$blnReturn = false;
			if (
				(is_object($objUser)) &&
				(is_a($objUser, 'SITE_USERS'))
			) {
				// Send the welcome email.
				$objMailer = new MAIL();
				$objMailer->setData(array(
					'USERNAME' 			=> $objUser->getVariable('username'),
					'PASSWORD' 			=> $objUser->getVariable('password'),
					'SITE_LINK'			=> __ROOT_URL__,
					'ROOT'				=> __ROOT_URL__	,
					'SITE_NAME'			=> __SITE_NAME__,
					'DATE'				=> 	date("F j, Y")
				));
				$objMailer->setTo($objUser->getVariable('email'));
				$objMailer->setSubject('Welcome to %%SITE_NAME%%');
				//$objMailer->setTemplate('templates/email/welcome-template.tmpl');	
				$blnReturn = $objMailer->send();
			}
			return ($blnReturn);
		}
		
		/**
		 * -------------------------------------------------------------
		 * REPORTING METHODS
		 * -------------------------------------------------------------
		 */		
		
		/**
		 * This method returns a list (array) of online users
		 * 
		 * @param  boolean $blnValidateIps 	if the user ip addresses should be grouped in the select		
		 * 									Defaults to TRUE, it provides a more accurate report.
		 * @return array 					The online users list
		 */
		public static function getOnlineUsersList($blnValidateIps = TRUE)
		{
			$objSession		= SESSION::getInstance(); // Start a session so that the calling user is counted
			$objDatabase 	= DATABASE::getInstance();
			$arrReturn 		= $objDatabase->query("
				SELECT 		s.id sessionId,
							s.ipAddress,
							gl.CountryCode,
							gl.CountryName,
							gl.RegionName,
							gl.city,
							su.username,
							su.id userId,
							su.access_level accessLevel
				FROM		sessions s
			
				LEFT JOIN	geo_locator gl
				ON			gl.id = s.ipAddress

				LEFT JOIN	site_users su
				ON			su.id = s.site_users_id
				
				WHERE 		UNIX_TIMESTAMP(NOW()) - s.access <= " . __SESSION_EXPIRATION_SECONDS__ . " 
				GROUP BY 	" . ($blnValidateIps ? " s.ipAddress, " : "") . " IF(su.id IS NOT NULL, su.id, NULL) "
			); 
			
			return ($arrReturn);
		}
		
		/**
			Securing Directories
		**/
		
		/**
		 * This method compares direcoty paths
		 * @param: 	$currentDir 	- The current directory
		 * @param: 	$securedDir 	- The secured directory
		 * @return: $blContinue		- If the provided info passes validation.
		 */	
		public static function compareDirectoryPaths($currentDir, $securedDir) {
			/**
			 * This method was changed since path with double that same folder
			 * name returned false. Example /secure-path/noaccess/ => /secure-path/noaccess/test/noaccess
			 * The comparison array would add "noaccess" twice and because of that, the string compare would
			 * fail. We now loop the secured array till the end. if all the folders are contained within the
			 * current dir path, then we return true.
			 */
			
			/**
			 * OLD METHOD
			 *
			$arrCurrentDir = explode(DIRECTORY_SEPARATOR, $currentDir);
			$arrSecuredDir = explode(DIRECTORY_SEPARATOR, $securedDir);
			return(
				strcmp(implode(DIRECTORY_SEPARATOR, array_intersect($arrCurrentDir, $arrSecuredDir)), $securedDir) == 0
			);
			*/
			
			$arrCurrentDir  = explode(DIRECTORY_SEPARATOR, $currentDir);
			$arrSecuredDir  = explode(DIRECTORY_SEPARATOR, $securedDir);
			$blnReturn 	 	= ((bool) (count($arrCurrentDir) >= count($arrSecuredDir)));

			if ($blnReturn) 
			{
				foreach ($arrSecuredDir as $intIndex => $strDirectory)
				{
					if (
						(is_array($arrCurrentDir)) && 
						(isset($arrCurrentDir[$intIndex]))
					) {
						$blnReturn &= ((bool) strcmp($strDirectory, $arrCurrentDir[$intIndex]) == 0);
					}
				}
			}
			
			return ((bool) $blnReturn);
		}
		
		/**
		 * This method secure the "Securable' directories specified in the restrictedDirectories array
		 * @param: 	none
		 * @return: Void
		 */	
		public function secure() {
			try {
				$arrSecuredDirs = (array) $this->getSecuredDirectories();
				$strCurrentPath = (string) dirname($_SERVER['SCRIPT_FILENAME']);
				
				$strRequestUrlPath = str_replace(
					DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, 
					DIRECTORY_SEPARATOR, 
					__SITE_ROOT__ . DIRECTORY_SEPARATOR . 
					(isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') )
				);
				
				$blIsRestricted = false;
				if (count($arrSecuredDirs)) {
					foreach($arrSecuredDirs as $intDirectoryId => $arrDirectory) {
						$strDirectory 	= $arrDirectory[0];
						$intAccessLevel	= $arrDirectory[1];
						$strSecuredDirectoryLength = strlen($strDirectory); 
						$strSecuredDirectory = (
							(strcmp(substr($strDirectory, strlen($strDirectory) - 1, strlen($strDirectory)), "/") == 0) ?
								substr($strDirectory, 0, strlen($strDirectory) - 1) : $strDirectory
						);

						if (
							(
								($this->compareDirectoryPaths($strRequestUrlPath, $strSecuredDirectory)) ||
								($this->compareDirectoryPaths($strCurrentPath, $strSecuredDirectory))
							) &&
							((! $this->isLoggedIn()) || ($this->getInfo("access_level") < $intAccessLevel))
						) {
							$msg= (
								(($this->isLoggedIn()) && ($this->getInfo("access_level") < $intAccessLevel)) ? 
									"Access Denied: Please ask your administrator to grant you access." : 
										"Please Login To View This Page."
							);	   
							
							SESSION::set('msg', $msg);
							$objUrl = new URL($this->getLoginUrl());
							$objUrl->clearAttribute();
							$objUrl->addSessionAttributes();
							$objUrl->setAttribute('ref', URL::getCurrentUrl());
							URL::redirect($objUrl->build());	
							$blIsRestricted = true;
							break;
						}
					}
				}
				if ((bool) $blIsRestricted) {
					$objUrl = new URL($this->getLoginUrl());
					$objUrl->clearAttribute();
					$objUrl->addSessionAttributes();
					$objUrl->setAttribute('ref', URL::getCurrentUrl());
					SESSION::set('msg', $msg);
					URL::redirect($objUrl->build());	
				}
			} catch (Exception $e) {
				new dump($e);	
				die; 
			}
		}
		
		/**
		 * This method add a directory to be secured in the restrictedDirectories array
		 * @param: 	$dir 			- String 	- The directory to secure
		 * @param: 	$intAccessLevel - Integer 	- Minimum access level required
		 * @param: 	$blnValidateDirectory - Boolean - Validate that the directory exists. (can be overwritten for rewite directories)
		 * @return: Void
		 */	
		public function secureDirectory($dir, $intAccessLevel=NULL, $blnValidateDirectory = true) {
			if ((bool) $blnValidateDirectory)
			{
				if (! is_dir($dir)) 
				{
					return(false);
				}
			}
			
			$this->restrictedDirectories[] =  array(
				((bool) $blnValidateDirectory ? realpath($dir) : $dir), 
				$intAccessLevel
			);
			return (true);
		}
		
		/**
		 * This method secures the specific accessed file (To be used only in the file itself)
		 * @param: 	none
		 * @return: Void
		 */	
		public function requireLogin($intAccessLevel = NULL, $blnValidateDirectory = FALSE) {
			$this->secureDirectory(dirname($_SERVER['SCRIPT_FILENAME']), $intAccessLevel, (bool) $blnValidateDirectory);
			$this->secure();
		}
		
		/**
			Access Settings Methods
		**/
		
		/**
		 * This sets the access URL for users to login
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setLoginUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$objUrl->setAttribute('ref', URL::getCurrentUrl(false));
			$this->strLoginUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to view their profile
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setProfileUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strProfileUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to signup
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setSignupUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$objUrl->setAttribute('ref', URL::getCurrentUrl(false));
			$this->strSignupUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to get a password reminder
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setPassReminderUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strPassReminderUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to change their passwords
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setChangePassUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strChangePassUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to confirm their memberships
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setConfirmMembershipUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strConfirmMembershipUrl = $objUrl->build(); 
		}
		
		/**
		 * This sets the access URL for users to get a notice
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setNoticeUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strNoticeUrl = $objUrl->build(); 
		}
		
		
		/**
		 * This sets the FACEBOOK login
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setFacebookLoginUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strFacebookLoginUrl = $objUrl->build(); 
		}
		
		
		/**
		 * This sets the TWITTER login
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setTwitterLoginUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strTwitterLoginUrl = $objUrl->build(); 
		}
		
		
		
		/**
		 * This sets the GOOGLE login
		 * @param: 	$strUrl - [String] - The URL
		 * @return: Void
		 */	
		public function setGoogleLoginUrl($strUrl) {
			$objUrl = new URL($strUrl, SCHEME_SAFEMODE_HTTP);
			//$objUrl->clearAttribute();
			$objUrl->addSessionAttributes();
			$this->strGoogleLoginUrl = $objUrl->build(); 
		}
		
		
		/**
			Getters
		**/
		
		/**
		 * This method checks if a user is logged in 
		 * @return: $blnIsLoggedIn - Boolean - If the user is logged in
		 */	
		public function isLoggedIn() { 
			$objSession = SESSION::getSession();
			$blnIsLoggedIn = ((bool) (int) $objSession->get('USER_ID'));
			return($blnIsLoggedIn); 
		} 
		
		/**
		 * This method returns the login URL
		 * @return: $objLoginUrl->build() - String - The login URL
		 */
		public function getLoginUrl() { 
			/*
			$pageURL = 'http' . ((isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) ? "s" : "") . "://";
			$pageURL .= ($_SERVER["SERVER_PORT"] != "80") ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			*/
			$objUrl = new URL(URL::getCurrentUrl());
			$objUrl->clearAttribute();
			//$objUrl->setAttribute(session_name(), session_id());
			
			$objLoginUrl = new URL($this->strLoginUrl);
			//$objLoginUrl->clearAttribute();
			$objLoginUrl->addSessionAttributes();
			$objLoginUrl->setAttribute('ref', $objUrl->build());
			return($objLoginUrl->build()); 
		} 
		
		/**
		 * This method returns the logout URL
		 * @return: $objLogoutUrl->build() - String - The logout URL
		 */
		public function getLogoutUrl() { 
			$objLogoutUrl = new URL(URL::getCanonicalUrl());
			$objLogoutUrl->clearAttribute();
			$objLogoutUrl->setAttribute('logout', 'true');
			return($objLogoutUrl->build()); 
		}
		
		/**
		 * This method returns the user's profile image
		 *
		 * @param:	Integer - The profile image position Id  
		 * @param:	Integer - The Default Image Path 
		 * @param:	Integer - The Default Image Size  
		 * @return: String 	- The user's profile image
		 */
		public function getProfileImage($intImagePosition = 4, $strDefaultImagePath = NULL, $intDefaultSize = NULL) 
		{
			$strProfileImage = false;
			$Application = false;
			if (true === class_exists('APPLICATION')) {
				$Application = APPLICATION::getInstance();
			}
			// Get the user's profile image:
			$objProfileImage = SITE_USERS_IMAGE::getInstanceFromKey(array(
				'site_user_id' 			=> $this->getId(),
				'active_status_id'		=> ACTIVE_STATUS_ENABLED,
				'image_position_id'		=> (int) $intImagePosition
			), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			
			
			if (
				(false === ((bool) $objProfileImage->getId() > 0)) ||
				(false === $objProfileImage->getBase64DisplayData())
			) {
				$strUserEmail	 = $this->getEmail();
				if (false === empty($strUserEmail)) {
					$strProfileImage = 	'https://www.gravatar.com/avatar/' . md5(strtolower(trim($strUserEmail))) . 
										'?d=' . urlencode($strDefaultImagePath) . ($intDefaultSize ? '&s=' . (int) $intDefaultSize : '');
				}		
			}
			else
			{
				$strProfileImage = $objProfileImage->getBase64DisplayData();
			}
			
			return ($strProfileImage);
		}
		
		/**
		 * Backwards compatibility methods
		 */
		 public function getInfo ($strInfo=NULL)		{ return($this->getVariable($strInfo)); }
		 public function redirect($strUrl)				{ return(URL::redirect($strUrl)); }
		 
		/**
		 * This method returns the change password URL
		 * @return: $this->strChangePassUrl - String - The login URL
		 */
		public function getChangePassUrl() 	 		{ return(isset($this->strChangePassUrl) ? $this->strChangePassUrl : NULL); } 
		
		/**
		 * This method returns the signup URL
		 * @return: $this->strSignupUrl - String - The Signup URL
		 */
		public function getSignupUrl() 		 		{ return(isset($this->strSignupUrl) ? $this->strSignupUrl : NULL); } 
		
		/**
		 * This method returns the view your profile URL
		 * @return: $this->strProfileUrl - String - The Profile URL
		 */
		public function getProfileUrl() 	 		{ return(isset($this->strProfileUrl) ? $this->strProfileUrl : NULL); }
		
		/**
		 * This method returns the password reminder URL
		 * @return: $this->strPassReminderUrl - String - The Password Reminder URL
		 */
		public function getPassReminderUrl() 		{ return(isset($this->strPassReminderUrl) ? $this->strPassReminderUrl : NULL); }
		
		/**
		 * This method returns the membership confirmation URL
		 * @return: $this->strConfirmMembershipUrl - String - The Membership Confirmation URL
		 */
		public function getConfirmMembershipUrl() 	{ return(isset($this->strConfirmMembershipUrl) ? $this->strConfirmMembershipUrl : NULL); }
		
		/**
		 * This method returns the user notice URL
		 * @return: $this->strNoticeUrl - String - The User Notice URL
		 */
		public function getNoticeUrl() 				{ return(isset($this->strNoticeUrl) ? $this->strNoticeUrl : NULL); }
		
		/**
		 * This method returns the facebook login URL
		 * @return: $this->strNoticeUrl - String - The Facebook URL
		 */
		public function getFacebookLoginUrl() 		{ return(isset($this->strFacebookLoginUrl) ? $this->strFacebookLoginUrl : NULL); }
		
		/**
		 * This method returns the Google login URL
		 * @return: $this->strGoogleLoginUrl - String - The Google Login URL
		 */
		public function getGoogleLoginUrl() 		{ return(isset($this->strGoogleLoginUrl) ? $this->strGoogleLoginUrl : NULL); }
		
				
		/**
		 * This method returns the twitter login URL
		 * @return: $this->strTwitterLoginUrl - String - The Twitter Login
		 */
		public function getTwitterLoginUrl() 		{ return(isset($this->strTwitterLoginUrl) ? $this->strTwitterLoginUrl : NULL); }
		
		
		/**
		 * This method returns the user full name
		 * @return: $this->getVariable - String - The User Full Name
		 */
		public function getFullName() {
			return (trim($this->getVariable('first_name') . " " . $this->getVariable('last_name')));	
		}
		
		/**
		 * This method returns the secured directories array
		 * @return: $this->restrictedDirectories - Array - The secured directories array
		 */
		private function getSecuredDirectories() { return((array) $this->restrictedDirectories); }
		
		/**
		 * This method returns the saved cookie username
		 * @param	none
		 * @return 	String
		 */
		public final function getCookieUserName()
		{
			
			return (
				((true === isset($_COOKIE[constant('__SESSION_NAME__') . '__USERNAME__'])) && 
				(false === empty($_COOKIE[constant('__SESSION_NAME__') . '__USERNAME__']))) ?
				$_COOKIE[constant('__SESSION_NAME__') . '__USERNAME__'] : NULL
			);		 
		}
		
		/**
			Abstraction Methods
		**/
		/*
		public static function getInstance($intId = 0) {
			$__strObjClassName__ = __CLASS__;
			$objReturn = new $__strObjClassName__((int) $intId);
			return ($objReturn->_getInstance((int)  $intId));
		}
		*/
		protected function getClassPath()  	 { return (__FILE__); }
		
	 	/**
	 	 * Abstraction method used to set the current objects settings
	 	 * 1 - Object cache is on
	 	 * 2 - Object cache is stored in session
	 	 * 3 - Objects uses AES-128 encryption for password storing
	 	 * 
	 	 * @return void
	 	 */
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_SESSION); // Cache only to session!
		}
		
		/**
		 * This is implemented to protect the password field, but
		 * make it available for get / set instances.
		 * Since this onEvent handler is called after the cache set,
		 * the decrypted password is not set in the cache.
		 */
		protected function on_getInstance()
		{
			// Decrypt and set that password
			$this->setPassword(
				$this->objCrypt->mysqlDecrypt(
					$this->getPassword()
				)
			);
		}
		
		/**
		 * Re-encrypt the password
		 */
		protected function onBeforeSave()
		{
			// Encrypt and set that password
			$this->setPassword(
				$this->objCrypt->mysqlEncrypt(
					$this->getPassword()
				)
			);
		}

		/**
		 * Re-encrypt the password
		 */
		protected function onSave()
		{
			// Decrypt and set that password
			$this->on_getInstance();
		}
	}