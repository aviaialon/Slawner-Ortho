<?php
	/**
	 * Main Users Administration Class
	 * This class represents a service controller called by request dispatcher
	 * see {application root}/http/request_dispatcher.php  
	 *
	 * @package		{APPLICATION_ROOT}::CONTROLLER
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	

	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::OAUTH::OAUTH_AUTHENTICATION");	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::IMAGE::SITE_USERS_IMAGE");	
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");	
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::STATE::STATE");		
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::COUNTRY::COUNTRY");	
	
	class USERS_CONTROLLER extends REQUEST_DISPATCHER
	{
		public function __construct()
		{

		}
		
		/**
		 * This is the default entry point for login
		 * @param REQUEST_DISPATCHER::PARAMS $arrParams
		 * @return void
		 */
		protected function loginAction($arrParams = NULL)
		{
			$this->enableViewCache(false);
			$this->useCompression(false);
			$Application = APPLICATION::getInstance();
			if ($Application->getUser()->getId())
			{
				$objUrl = new URL(constant('__SITE_URL__'));
				$objUrl->forward();
			}
			
			/**
			 * Handle oauth authentication
			 */
			// Create the return URL
			$objReturnUrl = new URL($Application->getUser()->getLoginUrl());
			$objReturnUrl->clearAttribute();
			
			if ($this->getRequestParam('oauth')) 
			{
				$strRequestedAuthMode 	= $this->getRequestParam('oauth');
				$arrAvailableAuthModes 	= OAUTH_AUTHENTICATION::getAvailableAuthMethods();
				$arrAvailableAuthModes	= array_change_key_case(array_flip($arrAvailableAuthModes), CASE_LOWER);
				if (TRUE === array_key_exists(strtolower($strRequestedAuthMode), $arrAvailableAuthModes))
				{
					$Application->getForm()->setUrlParam('authMethod', ucwords($strRequestedAuthMode));
					$Application->getForm()->setUrlParam('oAuth', true);
					$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(ucwords($strRequestedAuthMode));
					
					if ($objOauthLogin->hasAuthRequest())
					{
						$this->assignView(false);
						// Set the return URL
						$objReturnUrl->setPath('/users/auth/oauth:' . $strRequestedAuthMode);
						$objOauthLogin->setOpenIdEndPointUrl($objReturnUrl->build());
						$objOauthLogin->setCancelUrl($objReturnUrl->build());
						$objOauthLogin->authenticate();
					}
				}
			}
			else
			{
				// 1. Get the paypal login button HTML
				$objOauthLogin = OAUTH_AUTHENTICATION::getInstance(OAUTH_AUTHENTICATION::OAUTH_AUTHENTICATION_AUTHMODE_PAYPAL);
				$objOauthLogin->setOpenIdEndPointUrl($objReturnUrl->build());
				$objOauthLogin->setCancelUrl($objReturnUrl->build());
				$objOauthLogin->authenticate();	
				$Application->getContent()->stop();
				
				// 2. Assign the data to the view
				$this->setViewData('payPalOAuthData',  $Application->getContent()->getData());	
			}
			
			// Set view data
			$objUrl = new URL($Application->getUser()->getLoginUrl());
			$objUrl->clearAttribute();
			$this->setViewData('userLoginUrl', $objUrl->build());
			$this->setViewData('userLoginToken', md5(time()));
		}
		
		/**
		 * Controller for the profile action - User's profile
		 *
		 * @param REQUEST_DISPATCHER::PARAMS $arrParams
		 * @return void
		 */
		protected function profileAction($arrParams = NULL)
		{ 
			$this->enableViewCache(false);
			$Application = $this->getApplication();
			
			// Begin form processing..
			if (
				($Application->getRequestDispatcher()->getRequestParam('form')) &&
				(false === empty($_POST))
			) {
				$arrReturn = array('SUCCESS' => false, 'ERROR' => NULL);
				
				if (false === ((bool) $Application->getUser()->getId())) {
					$arrReturn['ERROR'] = 'Unable to process your request. Session timed out.';	
				}
				else 
				{
					switch ($Application->getRequestDispatcher()->getRequestParam('form')) {
						case ('general') : {
							while (list($strKey, $strPostValue) = each($_POST)) {
								if (strlen($strPostValue)) {
									$Application->getUser()->setVariable($strKey, $strPostValue);	
								}
							}
							if ($Application->getUser()->save()) {
								$arrReturn = array('SUCCESS' => true, 'ERROR' => NULL);	
							} else {
								$arrReturn = array('SUCCESS' => false, 'ERROR' => 'Error Saving Profile Data. Please Try Again.');	
							}
							break;	
						}
						
						case ('account') : {
							$blnContinue = true;
							if (
								((false === $Application->getForm()->getPostParam('current_password')) ||
								(false === ((bool) strlen($Application->getForm()->getPostParam('current_password')) > 0))) &&
								(true === $blnContinue)
							) {
								$blnContinue = false;
								$arrReturn['ERROR'] = 'Please provide your password';
							}
							
							if (
								((false === $Application->getForm()->getPostParam('new_password1')) || 
								(false === ((bool) strlen($Application->getForm()->getPostParam('new_password1')) > 0))) &&
								(true === $blnContinue)
							) {
								$blnContinue = false;
								$arrReturn['ERROR'] = 'Please provide your new password';
							}
							
							if (
								((false === $Application->getForm()->getPostParam('new_password2')) ||
								(false === ((bool) strlen($Application->getForm()->getPostParam('new_password2')) > 0))) &&
								(true === $blnContinue)
							) {
								$blnContinue = false;
								$arrReturn['ERROR'] = 'Please re-enter your new password';
							}
							
							if (strcmp($Application->getForm()->getPostParam('new_password1'), $Application->getForm()->getPostParam('new_password2')) <> 0) {
								$blnContinue = false;
								$arrReturn['ERROR'] = 'Your new passwords do not match. Please check the spelling and try again.';
							}
							
							if (strcmp($Application->getForm()->getPostParam('current_password'), $Application->getUser()->getPassword()) <> 0) {
								$blnContinue = false;
								$arrReturn['ERROR'] = 'Current password is invalid.';	
							}
							
							if (true === ((bool) $blnContinue)) {
								$arrReturn['SUCCESS'] = $Application->getUser()->setPassword($Application->getForm()->getPostParam('new_password1'))->save();	
								$arrReturn['MESSAGE'] = 'Your password has been change. You will be prompted to re-login in 5 seconds.';
								$arrReturn['POST_ACTION'] = 'setTimeout(function() {window.location.href="' . $Application->getUser()->getLoginUrl() . '";}, 5000)';
								$Application->getUser()->logout(false);
							}
							break; 
						}
					}	
				}
				echo (json_encode($arrReturn));
				die;
			}
			
			$this->setViewData('profileImage', $Application->getUser()->getProfileImage(4, $Application->getStaticResourcePath() . 'images/userLogin2.png'), 72);
			$this->setViewData('applicationStaticPath', $Application->getStaticResourcePath());
			$this->setViewData('objUser', $Application->getUser());
			$this->setViewData('postUrlGeneral', $this->createRoute(array(
				'controller' => $this->getController_Name(),
				'action'	 => $this->getAction_Name(),
				'params'	 =>	array(
					'form'	 => 'general'
				)
			)));
			$this->setViewData('postUrlAccount', $this->createRoute(array(
				'controller' => $this->getController_Name(),
				'action'	 => $this->getAction_Name(),
				'params'	 =>	array(
					'form'	 => 'account'
				)
			)));
			$this->setViewData('postUrlSettings', $this->createRoute(array(
				'controller' => $this->getController_Name(),
				'action'	 => $this->getAction_Name(),
				'params'	 =>	array(
					'form'	 => 'settings'
				)
			)));
			
			$this->setViewData('arrState', STATE::getObjectClassView(array(
				'columns'	=>	array('a.id', 'a.state_name', 'a.state_code'),
				'filter'	=>	array('a.active' => ACTIVE_STATUS_ENABLED),
				'order_by'	=> 	'a.id',
				'direction'	=>	'ASC'
			)));
			
			$this->setViewData('arrCountry', COUNTRY::getObjectClassView(array(
				'columns'	=>	array('a.id', 'a.country_name', 'a.country_code'),
				'filter'	=>	array('a.active' => ACTIVE_STATUS_ENABLED),
				'order_by'	=> 	'a.id',
				'direction'	=>	'ASC'
			)));
			
			$this->setViewData('changeProfileImageUrl', $this->createCallbackUrl(
				constant('__APP_CONTROLLER_DIR__') . '::REMOTE_CALL::REMOTE_CALL_CONTROLLER', 'update_profile_imageAction', array())
			);
		}
		
		/**
		 * This method resets a user's password
		 * @param REQUEST_DISPATCHER::PARAMS $arrParams
		 * @return void
		 */
		protected final function reset_passwordAction(array $arrRequestDispatcherParams)
		{
			$strUserEmail = $this->getRequestParam('user_email');
			if (false === empty($strUserEmail))
			{
				$this->getApplication()->getUser()->sendResetPassword($strUserEmail, $this);
			}
		}
		
		/**
		 * This method receives the reset password callback
		 * @param REQUEST_DISPATCHER::PARAMS $arrParams
		 * @return void
		 */
		protected final function onPasswordResetCallback(array $arrRequestDispatcherParams)
		{
			$blnContinue 			= true;
			$strAuthToken 			= $this->getRequestParam('authToken');
			$this->assignNoView();
			
			if (false === empty($strAuthToken))
			{
				list($strUserEmail, $strUserPassword) = explode(':', $strAuthToken);	
				$blnContinue = ((false === empty($strUserEmail)) && (false === empty($strUserPassword)));
				if (true === $blnContinue)
				{
					// validate the user...
					$objUser = 	SITE_USERS::getInstanceFromKey(array(
						'email' 	=> 	$strUserEmail,
						'password'	=>	$this->getApplication()->getCrypto()->mysqlEncrypt(trim($strUserPassword))												 
					));	
					
					$blnContinue = ((bool) $objUser->getId() > 0);
					if (true === $blnContinue)
					{
						// If the user already posted the new passwords...
						if ((true === isset($_POST['user_password'])) && (true === isset($_POST['user_password2']))) 
						{
							list($strUserPassword1, $strUserPassword2) = array($this->getRequestParam('user_password'), $this->getRequestParam('user_password2'));
							
							$blnContinue =  ((bool) strlen($strUserPassword1)) || 
											(SESSION::set('err', $this->getApplication()->translate('Please enter a new password.', 'Veuillez entrer un nouveau mot de passe')) & false);
							
							if (true === $blnContinue) {
								$blnContinue =  ((bool) strlen($strUserPassword2)) || 
												(SESSION::set('err', $this->getApplication()->translate('Please enter your password again.', 'Veuillez entrer votre mot de passe encore')) & false);
							}
							
							if (
								(false === empty($strUserPassword1)) &&
								(false === empty($strUserPassword2)) &&
								(true  === $blnContinue)
							) {
								$blnContinue = (strcmp($strUserPassword1, $strUserPassword2) === 0) || 
												(SESSION::set('err', $this->getApplication()->translate('Both passwords must be identical', 'Les mots de passe doivent être identiques')) & false);
												
								
							}	
							
							if (true === $blnContinue) 
							{
								$objUser->setPassword($strUserPassword1);	
								$objUser->save();
								$objUser->login($objUser->getUserName(), $objUser->getPassword(), false);
								SESSION::set('ok', $this->getApplication()->translate('Your new password has been updated successfully.', 'Votre nouveau mot de passe a été mis à jour avec succès.'));
								$objRedirect = new URL(constant('__SITE_URL__'));
								$objRedirect->setAttribute('lang', $this->getApplication()->translate('en', 'fr'));
								$objRedirect->forward();
							}
							else
							{
								$this->renderView(constant('__APP_VIEW_DIR__') . '/users/change_password.php');		
								exit();
							}
						}
						else
						{
							$this->setViewData('postDataUrl', $this->createCallbackUrl($this, __FUNCTION__, $arrRequestDispatcherParams['parameters']));
							$this->renderView(constant('__APP_VIEW_DIR__') . '/users/change_password.php');	
							exit();
						}
					}
				}
			}
			
			if (false === $blnContinue)
			{
				URL::redirect(constant('__SITE_URL__'));	
			}
		}
		
		protected function catchAllAction($arrParams = NULL)
		{ 
			$Application = APPLICATION::getInstance();
			$objRedirectUrl = new URL($Application->getUser()->getLoginUrl());
			$objRedirectUrl->forward();
		}
	}
?>