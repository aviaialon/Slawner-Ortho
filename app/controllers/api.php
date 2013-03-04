<?php
	/**
	 * Admin API Administration Class
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
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::IO::FILE::FILE_UPLOAD_V2");		
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");	
	
	class API_CONTROLLER extends REQUEST_DISPATCHER
	{
		/**
		 * The following constants define the output methods for the API
		 */
		const API_CONTROLLER_OUTPUT_TYPE_JSON 	= 'json';
		const API_CONTROLLER_OUTPUT_TYPE_XML 	= 'xml';
		const API_CONTROLLER_OUTPUT_TYPE_HTML 	= 'html';
		const API_CONTROLLER_OUTPUT_TYPE_TEXT 	= 'text';
		
		/**
		 * This variable holds the API's return data
		 * 
		 * @var  	Mixted
		 * @access	protected static
		 */
		protected static $mxReturnData;
		
		/**
		 * This variable holds the API's output format return type
		 * 
		 * @var  	String
		 * @access	protected static
		 */
		protected static $strOutputFormat = API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_JSON;
		
		/**
		 * This variable is an array of the secure routes and access level
		 * in the form: Route => access_level, for example Client_Info => SITE_USERS_ROLE_ADMIN_USER
		 * will require the user to have admin rights in order to call this API method
		 * 
		 * @var 	Array
		 * @access	protected static
		 */
		protected static $arrSecuredRoutes = array
		(
			/*'upload_image'	=> 	SITE_USERS_ROLE_ADMIN_USER*/
			'add_news_category'	=> 	SITE_USERS_ROLE_ADMIN_USER
		);
		
		/**
		 * Class contructor
		 * @access public
		 * @return API_CONTROLLER
		 */
		public function __construct()
		{

		}
		
		/**
		 * This method returns the current requested API version
		 * Ex: http://www.deviantlogic.ca/api/v2.06/test/testing will 
		 * return API version 2.06
		 * 
		 * @access protected final static
		 * @return float
		 */
		protected final static function getApiVersion()
		{
			$strApiVersion 	= false;
			$arrRequestData = self::getRequestData();
			if (
				(FALSE === empty($arrRequestData)) &&
				(count($arrRequestData) >= 1)
			) {
				$strApiVersion = preg_replace("/[^0-9.]/", "", $arrRequestData[1]);
				$strApiVersion = ((substr($strApiVersion, 0, 1) == '.') ? substr($strApiVersion, 1) : $strApiVersion);
			}
			
			return ($strApiVersion);
		}
		
		/**
		 * This method handles all the API's call
		 * 
		 * @access  protected
		 * @param 	array $arrParams - Request Parameters, see REQUEST_DISPATCHER
		 * @return 	void
		 */
		protected function catchAllAction($arrParams = NULL)
		{
			$this->enableViewCache(false);
			$this->useCompression(false);
			$this->assignNoView();
			$strCanonicalUrl  = URL::getCanonicalUrl(NULL, true, true, true);
			$arrMapApiData	  = array_reverse(array_slice(explode('/', substr($strCanonicalUrl, 1)), 2), true); // Build a request array and remove the 'api' part
			$blnContinue	  = true;
			$fltApiVersion 	  = (float) self::getApiVersion();
			$arrParameters	  = $arrParams['parameters'];
			$strMethodDefault = str_replace(':', '', strtolower(array_shift($arrMapApiData))); // in case the api request URL methog is called without a param like -> https://.../api/v2.06/getApiVersion/
			$strRequestMethod = NULL; //self::neutralise(trim(strtolower(array_shift($arrMapApiData))))  // in case the api request URL methog is called with a param like -> https://.../api/v2.06/method:getApiVersion/
			$strRequestMethod = str_replace('-', '_', $strRequestMethod);  // in case the api request URL methog is called with function-call instead of function_call
			$strMethodDefault = str_replace('-', '_', $strMethodDefault);  // in case the api request URL methog is called with function-call instead of function_call
			$strRequestMethod = (false === empty($strRequestMethod) ? $strRequestMethod : $strMethodDefault);
			$mxReturnData  	  = NULL;	

			// set the output method
			if (
				(true === array_key_exists('output', $arrParameters)) &&
				(true === in_array($arrParameters['output'], array(
					API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_JSON,
					API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_HTML,
					API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_TEXT,
					API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_XML
				)))
			) {
				self::$strOutputFormat = ($arrParameters['output']);	
			}
			
			// Validate Proper API use
			if (
				(! $strRequestMethod) ||
				(! method_exists(__CLASS__, $strRequestMethod))
			) {
				self::setReturnData('Invalid Use Of The API. Method ' . ($strRequestMethod ? '[' . $strRequestMethod . '] is an invalid method.' : ''));
				$blnContinue = false;
			}
			
			// Validate user permissions
			if (TRUE === array_key_exists($strRequestMethod, self::$arrSecuredRoutes))
			{
				$intRequiredAccessLevel = (int) self::$arrSecuredRoutes[$strRequestMethod];
				
				// Validate Logged In user
				if (FALSE === $this->getApplication()->getUser()->getId())
				{
					self::setReturnData('Unauthorized use of the API. Please Contact Your Administrator.');
					$blnContinue = false;
				}
				
				// Validate User privileges
				else if (FALSE ===  $this->getApplication()->getUser()->fitsInRole($intRequiredAccessLevel))
				{
					self::setReturnData('You Do Not Have Enough Privileges To Use The API. Please Contact Your Administrator.');
					$blnContinue = false;
				}
			}
			
			// Error Output
			if (FALSE === $blnContinue)
			{
				self::write(self::$strOutputFormat);
			}
			
			/*
			$strRequestNeutralisedMethod = self::neutralise($strRequestMethod);
			self::setReturnData(
				self::$strRequestNeutralisedMethod($arrParameters)
			);
			*/
			self::setReturnData(self::$strRequestMethod($arrParameters));
			self::write(self::$strOutputFormat);
		}
		
		/**
		 * Sets the API's return data
		 * 
		 * @access protected static final
		 * @param  mixed $mxRData 
		 * @return void
		 */
		protected static final function setReturnData($mxRData = NULL)
		{
			self::$mxReturnData = $mxRData;
		}
		
		/**
		 * This method outputs the API's return data bank. It also halts further executions
		 * 
		 * @access protected static final
		 * @param  string $strOutputType - The API's output buffer type [default: JSON]
		 * @return void
		 */
		protected static final function write($strOutputType = API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_JSON)
		{
			switch ($strOutputType)
			{
				default: 
				case API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_JSON :
					{
						//header('Content-type: application/json');
						echo json_encode(self::$mxReturnData);
						break;
					} 
					
				case API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_XML :
					{
						header('Content-type: text/xml');
						$objXmlOutput = new SimpleXMLElement('<ApiReturnData/>');
						if (true === is_array(self::$mxReturnData)) 
						{
							array_walk_recursive(self::$mxReturnData, array ($objXmlOutput, 'addChild'));	
						}
						else
						{
							$objXmlOutput->addChild('data', self::$mxReturnData);
						}
						echo ($objXmlOutput->asXML());
						break;
					} 
					
				case API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_HTML :
					{
						header('Content-type: text/html');
						echo (string) self::$mxReturnData;
						break;
					} 	

				case API_CONTROLLER::API_CONTROLLER_OUTPUT_TYPE_TEXT :
					{
						header('Content-type: text/plain');
						
						if (is_string(self::$mxReturnData)) 
						{
							echo self::$mxReturnData;
						}
						else 
						{
							echo print_r(self::$mxReturnData, false);
						}
						
						break;
					} 	
			}
			
			exit;
		}
		
		/* 
		 * --------------------------------------------------------------------------
		 * 	BEGIN API CALL METHODS
		 * -------------------------------------------------------------------------
		 */
		
		/**
		 * This method uploads an image
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function upload_image(array $arrRequestParams)
		{
			$Application 	 = APPLICATION::getInstance();
			$objFileUploader = FILE_UPLOAD_V2::getInstance();
			$objFileUploader->setAllowedExtensions(array('gif', 'png', 'jpeg', 'jpg', 'bmp'));
			$objFileUploader->setUploadDirectory(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads');
			$objFileUploader->setUploadWebPath('/static/tmp/user-uploads');
			$objFileUploader->setUploadFormInputName('qqfile');
			$objFileUploader->setSizeLimit(10 * 1024 * 1024); // in Bytes
			if (true === ((bool) $objFileUploader->processImageUpload())) 
			{
				$intFileSize = filesize(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . basename($objFileUploader->getUploadedFileWebPath()));
				$intFileSize = number_format($intFileSize / 1024, 1) . 'KB';
				$arrReturn = array(
					'filelink' 			=> $objFileUploader->getUploadedFileWebPath(), 
					'fileuploadname' 	=> basename($objFileUploader->getUploadedFileWebPath()), 
					'filesize' 			=> $intFileSize, 
					'success' 			=> true, 
					'deletetoken' 		=> $Application->getCrypto()->encrypt(basename($objFileUploader->getUploadedFileWebPath()))
				);
			} 
			else 
			{
				$arrErrors = $objFileUploader->getErrors();
				$arrReturn = array('error' => array_shift($arrErrors), 'success' => false);
			}
			return ($arrReturn);
		}
		
		/**
		 * This method deletes an user uploaded image
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function delete_image(array $arrRequestParams)
		{
			$Application 	= APPLICATION::getInstance();
			$strFileName	= $Application->getCrypto()->decrypt($Application->getRequestDispatcher()->getRequestParam('token'));
			$arrReturn		= array('success' => false);
			
			if (
				(false === empty($strFileName)) &&
				(true  === file_exists(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $strFileName))
			) {
				$arrReturn['success'] = unlink(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $strFileName);
			}
			return ($arrReturn);
		}
		
		
		/**
		 * This method posts a news comment
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function post_news_comment(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_COMMENTS");
			
			$Application 	 		= APPLICATION::getInstance();
			$objRequestDispatcher	= $Application->getRequestDispatcher();
			$arrReturn		 		= array('success' => true, 'error' => array());
			$blnContinue	 		= $arrReturn['success'];
			$strUserName			= strip_tags($objRequestDispatcher->getRequestParam('name'));
			$strUserEmail			= strip_tags($objRequestDispatcher->getRequestParam('email'));
			$strSubject				= strip_tags($objRequestDispatcher->getRequestParam('subject'));
			$strMessage				= strip_tags($objRequestDispatcher->getRequestParam('message'));
			$intCaptcha				= (int) $objRequestDispatcher->getRequestParam('captcha');
			$intNewsPostId			= (int) $objRequestDispatcher->getRequestParam('postId');
			$intPostParentId		= (int) $objRequestDispatcher->getRequestParam('parent_id');
			$intCaptchaChallenge	= ((int) $Application->getCrypto()->decrypt($objRequestDispatcher->getRequestParam('captchaChallenge')));
			$objNewsPost			= NEWS::getInstance($intNewsPostId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$objNewsComment			= NEWS_COMMENTS::newInstance();
			
			$blnContinue &= (false === empty($strUserName)) || (($arrReturn['error'][] = $Application->translate('Please enter your name', 'Veuillez entrer votre nom')) & false); 
			$blnContinue &= (false === empty($strUserEmail)) || (($arrReturn['error'][] = $Application->translate('Please enter your email', 'Veuillez entrer votre email')) & false); 
			$blnContinue &= (false === empty($strMessage)) || (($arrReturn['error'][] = $Application->translate('Please enter your message', 'Veuillez entrer votre message')) & false); 
			$blnContinue &= (trim($intCaptcha) !== "") || (($arrReturn['error'][] = $Application->translate('Please answer the math question', 'Veuillez répondre à la question mathématique')) & false); 
			$blnContinue &= (true === ((bool) VALIDATOR::email($strUserEmail))) || (($arrReturn['error'][] = $Application->translate(
				'Please provide a valid email', 'Veuillez fournir une adresse email valide')) & false); 
			$blnContinue &= ($intCaptcha == $intCaptchaChallenge) || (($arrReturn['error'][] = $Application->translate(
				'Your math answer was incorrect', 'Votre réponse mathématique est incorrecte')) & false); 
			$blnContinue &= (((int) $objNewsPost->getId()) > 0) || (($arrReturn['error'][] = $Application->translate(
				'An error occured. Please try again later', 'Une erreur s\'est produite. S\'il vous plaît réessayer plus tard')) & false);	
				
			if ($blnContinue)
			{
				$objNewsComment->setNewsId((int) $objNewsPost->getId());
				$objNewsComment->setActive(ACTIVE_STATUS_ENABLED);		
				$objNewsComment->setEmail($strUserEmail);			
				$objNewsComment->setName($strUserName);			
				$objNewsComment->setSubject($strSubject);				
				$objNewsComment->setComment($strMessage);				
				$objNewsComment->setCommentParentId($intPostParentId);					
				$objNewsComment->setPost_Date(date('F j, Y j:i A', time()));		
				$objNewsComment->setUserIp($_SERVER['REMOTE_ADDR']);
				$objNewsComment->setEmail_Hash(md5($objNewsComment->getEmail()));		
				$blnContinue = $objNewsComment->save();
				if (true === $blnContinue) {
					$arrReturn['data'] = $objNewsComment->get();
				}
			}
			
			$arrReturn['success'] = $blnContinue;
			
			return ($arrReturn);
		}
		
		
		/**
		 * This method posts a patient profile comment
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function post_patient_profile_comment(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_COMMENTS");
			
			$Application 	 		= APPLICATION::getInstance();
			$objRequestDispatcher	= $Application->getRequestDispatcher();
			$arrReturn		 		= array('success' => true, 'error' => array());
			$blnContinue	 		= $arrReturn['success'];
			$strUserName			= strip_tags($objRequestDispatcher->getRequestParam('name'));
			$strUserEmail			= strip_tags($objRequestDispatcher->getRequestParam('email'));
			$strSubject				= strip_tags($objRequestDispatcher->getRequestParam('subject'));
			$strMessage				= strip_tags($objRequestDispatcher->getRequestParam('message'));
			$intCaptcha				= (int) $objRequestDispatcher->getRequestParam('captcha');
			$intProfileId			= (int) $objRequestDispatcher->getRequestParam('profile_id');
			$intPostParentId		= (int) $objRequestDispatcher->getRequestParam('parent_id');
			$intCaptchaChallenge	= ((int) $Application->getCrypto()->decrypt($objRequestDispatcher->getRequestParam('captchaChallenge')));
			$objProfile				= PATIENT_PROFILES::getInstance($intProfileId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$objProfileComment		= PATIENT_PROFILES_COMMENTS::newInstance();
			
			$blnContinue &= (false === empty($strUserName)) || (($arrReturn['error'][] = $Application->translate('Please enter your name', 'Veuillez entrer votre nom')) & false); 
			$blnContinue &= (false === empty($strUserEmail)) || (($arrReturn['error'][] = $Application->translate('Please enter your email', 'Veuillez entrer votre email')) & false); 
			$blnContinue &= (false === empty($strMessage)) || (($arrReturn['error'][] = $Application->translate('Please enter your message', 'Veuillez entrer votre message')) & false); 
			$blnContinue &= (trim($intCaptcha) !== "") || (($arrReturn['error'][] = $Application->translate('Please answer the math question', 'Veuillez répondre à la question mathématique')) & false); 
			$blnContinue &= (true === ((bool) VALIDATOR::email($strUserEmail))) || (($arrReturn['error'][] = $Application->translate(
				'Please provide a valid email', 'Veuillez fournir une adresse email valide')) & false); 
			$blnContinue &= ($intCaptcha == $intCaptchaChallenge) || (($arrReturn['error'][] = $Application->translate(
				'Your math answer was incorrect', 'Votre réponse mathématique est incorrecte')) & false); 
			$blnContinue &= (((int) $objProfile->getId()) > 0) || (($arrReturn['error'][] = $Application->translate(
				'An error occured. Please try again later', 'Une erreur s\'est produite. S\'il vous plaît réessayer plus tard')) & false);	
				
			if ($blnContinue)
			{
				$objProfileComment->setPatient_Profiles_Id((int) $objProfile->getId());
				$objProfileComment->setActive(ACTIVE_STATUS_ENABLED);		
				$objProfileComment->setEmail($strUserEmail);			
				$objProfileComment->setName($strUserName);			
				$objProfileComment->setSubject($strSubject);				
				$objProfileComment->setComment($strMessage);				
				$objProfileComment->setCommentParentId($intPostParentId);					
				$objProfileComment->setPost_Date(date('F j, Y j:i A', time()));		
				$objProfileComment->setUserIp($_SERVER['REMOTE_ADDR']);
				$objProfileComment->setEmail_Hash(md5($objProfileComment->getEmail()));		
				$blnContinue = $objProfileComment->save();
				if (true === $blnContinue) {
					$arrReturn['data'] = $objProfileComment->get();
				}
			}
			
			$arrReturn['success'] = $blnContinue;
			
			return ($arrReturn);
		}
		
		/**
		 * This method posts a appointment request, used with callbacks
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected static final function request_appointment(array $arrRequestParams) 
		{
			$Application 	 		= APPLICATION::getInstance();
			$objRequestDispatcher	= $Application->getRequestDispatcher();
			$blnContinue 			= true;
			$arrAttachments 		= array();
			$arrReturn 				= array('success' => true, 'error' => '', 'field' => array());
			
			if (true === $Application->getForm()->isPost())
			{
				$intPhoneNumber = implode('', $objRequestDispatcher->getRequestParam('tel'));
				$arrValidation = array(
					'name' 		=> $Application->translate('Please enter your name.', 'Veuillez enter votre nom.'),
					'email' 	=> $Application->translate('Please enter your email.', 'Veuillez enter votre email.'),
					'address' 	=> $Application->translate('Please enter your address.', 'Veuillez enter votre address.'),
					'cboHear' 	=> $Application->translate('Please tell us how you heard of us.', 'Veuillez s\'il vous plaît nous dire comment vous avez entendu parler de nous.'),
					'location' 	=> $Application->translate('Please select a location.', 'Veuillez sélectionner un emplacement.'),
					'reason' 	=> $Application->translate('Please indicate the reason of your visit.', 'Veuillez indiquer le motif de votre visite.'),
					'age' 		=> $Application->translate('Please indicate your age.', 'Veuillez indiquer votre âge')
				);
				
				reset($arrValidation);
				while (list($strFormKey, $strErrorMessage) = each($arrValidation))
				{
					$blnContinue = ((bool) $objRequestDispatcher->getRequestParam($strFormKey)) || (($arrReturn['error'] = $strErrorMessage) & false);	
					if (false === $blnContinue) {
						$arrReturn['field'][] = $strFormKey;
						break;	
					}
				}
				
				if (true === $blnContinue)
				{
					$blnContinue = 	(true === ((bool) VALIDATOR::email($objRequestDispatcher->getRequestParam('email')))) || 
									(($arrReturn['error'] = $Application->translate('Please enter a valid email address.', 'Veuillez entrer une address email valid.')) & false);	
					if (false === $blnContinue) 
						$arrReturn['field'][] = 'email';					
				}
				
				if (true === $blnContinue)
				{
					$blnContinue = 	(true === ((bool) VALIDATOR::numberRange($objRequestDispatcher->getRequestParam('age'), array('min' => 1, 'max' => 120)))) || 
									(($arrReturn['error'] = $Application->translate('Please enter a valid age.', 'Veuillez entrer un âge valid.')) & false);	
					if (false === $blnContinue) 
						$arrReturn['field'][] = 'age';					
				}
				
				if (true === $blnContinue)
				{
					if (
						(true === preg_match('/[^0-9]/', $intPhoneNumber)) ||
						(strlen($intPhoneNumber) < 10)
					) {
						$blnContinue = false;
						$arrReturn['error'] = $Application->translate('Please enter a valid phone number.', 'Veuillez entrer un numéro de téléphone valide.');
						$arrReturn['field'][] = 'tel[0]';
						$arrReturn['field'][] = 'tel[1]';
						$arrReturn['field'][] = 'tel[2]';
					}
				}
				
				if (true === $blnContinue)
				{
					// Get the location
					SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::LOCATIONS::LOCATIONS");
					$objLocation = LOCATIONS::getInstance(array(
						(int) $objRequestDispatcher->getRequestParam('location')
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// Parse the attachments:
					$arrUploadedAttachments = $objRequestDispatcher->getRequestParam('attachments');
					while (list($strAttachmentKey, $strAttachmentEncryptedData) = each($arrUploadedAttachments))
					{
						list($strLocalFileName, $strFileUploadSize) = explode(':', $strAttachmentKey);
						$arrAttachments[] = array(
							'localFileName'		=> $strLocalFileName,
							'fileSize'			=> $strFileUploadSize,
							'encryptedSource'	=> $strAttachmentEncryptedData,
							'fileName'			=> $Application->getCrypto()->decrypt($strAttachmentEncryptedData),
							'filePath'			=> '/static/tmp/user-uploads/' . $Application->getCrypto()->decrypt($strAttachmentEncryptedData)
						);
					}
					
					// Check if the user has a geo location instance
					$blnUserHasGeoTarget = (
						(false === (false === $objRequestDispatcher->getRequestParam('lat'))) &&
						(false === (false === $objRequestDispatcher->getRequestParam('lng'))) 
					);
					
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
						'TITLE' 			=> $Application->translate("Sent on ", "Envoyée le ") . date("F j, Y H:i:s")
					));
					
					$strEmailMessage = 	'<h2><strong>' . $Application->translate("Appointment request from ", "Demande de rendez-vous chez ") . ucwords(__SITE_NAME__) . 
					' on ' .  date("F j, Y H:i:s") . '</strong></h2><br /><p><strong>[User IP Address: ' . $_SERVER['REMOTE_ADDR'] . ']</strong></p>' . '<br />' .
					'<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial;font-size:12px;color:#000">' .
						'<tr>' .
							'<td align="left" valign="top" colspan="2" style="padding-bottom: 10px"><h2>General Information:</h2></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Language: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . $Application->translate('English', 'French') . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Name: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('name'))) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Age: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('age'))) . ' yo</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Email: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('email'))) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Address: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('address'))) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Phone: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities(implode('.', $objRequestDispatcher->getRequestParam('tel')))) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">This user heard of us via: </td>' .
							'<td align="left" valign="top" style="padding-bottom: 10px">' . 
								'<strong>' . strip_tags(htmlentities(base64_decode($objRequestDispatcher->getRequestParam('cboHear')))) . '</strong>' .
							'</td>' .
						'</tr>' .
						'<tr>' .
							'<td align="left" valign="top" colspan="2" style="padding-bottom: 10px"><h2>Appointment Information:</h2></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Prefered appointment date: </td>' . 
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('date_range'))) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Location: </td>' . 
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . utf8_encode($objLocation->getName()) . '</strong></td>' .
						'</tr>' .
						'<tr>' .
							'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Reason for appointment: </td>' . 
							'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('reason'))) . '</strong></td>' .
						'</tr>' .
						(
							($objRequestDispatcher->getRequestParam('message')) ? 
							'<tr>' .
								'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Message: </td>' . 
								'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($objRequestDispatcher->getRequestParam('message'))) . '</strong></td>' .
							'</tr>' : ''
						);
										 
					if (false === empty($arrAttachments))
					{
						$strEmailMessage .= '<tr>' .
												'<td align="left" valign="top" colspan="2" style="padding-bottom: 10px"><h2>Attachments (' . count($arrUploadedAttachments) . '):</h2></td>' .
											'</tr>';
							
						while(list($intIndex, $arrUploadedElements) = each($arrAttachments))
						{
							$strEmailMessage .= '<tr><td colspan="2" align="left" valign="top" style="padding-bottom: 10px">';
							$strEmailMessage .= '<a href="' . constant('__SITE_URL__') . '/' . $arrUploadedElements['filePath'] . '">' . $arrUploadedElements['localFileName'] . '</a>';
							$strEmailMessage .= '</td></tr>';
						}
					}
					
					if (true === $blnUserHasGeoTarget)
					{
						$strEmailMessage .= '<tr>' .
												'<td colspan="2" ><br /><hr /><strong>User Location</strong><br /><center>' . 
													'<a href="https://maps.google.com/maps?q=' . $objRequestDispatcher->getRequestParam('lat') . ',' . $objRequestDispatcher->getRequestParam('lng') . '">' .
													'<img src="http://maps.googleapis.com/maps/api/staticmap?sensor=false&maptype=roadmap&center=' . 
													$objRequestDispatcher->getRequestParam('lat') . ',' . $objRequestDispatcher->getRequestParam('lng') . 
													'&markers=color:blue%7Clabel:S%7C' . $objRequestDispatcher->getRequestParam('lat') . ',' . $objRequestDispatcher->getRequestParam('lng') . '&' .
													'&zoom=15&size=600x300" border="0" /></a></center><hr /><br /></td>' .
											'</tr>';
					}
					
							
					$strEmailMessage .= '</table>';
					
					$objMailer->setTo(filter_var(constant('__ADMIN_EMAIL__'), FILTER_VALIDATE_EMAIL));
					$objMailer->setFrom(ucwords(__SITE_NAME__) .  (' <reminder@' . __SITE_DOMAIN__ . '>'));
					$objMailer->setSubject($Application->translate("Appointment request from ", "Demande de rendez-vous chez ") . ucwords(__SITE_NAME__));
					$objMailer->setTemplate('static/templates/email/contact-us.tmpl');
					$objMailer->setMessage($strEmailMessage);
					$blContinue = $objMailer->send();
					
					if ($blnUserHasGeoTarget)
					{
						$strInstructions 	= '';
						if ($objLocation->getId() > 0) {
							$strServerEnpoint = 'http://maps.googleapis.com/maps/api/directions/json?';
							$arrParams   	  = array(
								'origin'      => $objRequestDispatcher->getRequestParam('lat') . ',' . $objRequestDispatcher->getRequestParam('lng'),
								'destination' => utf8_encode($objLocation->getAddress()),
								'mode'        => 'driving',
								'sensor'      => 'false',
								'language'    => $Application->translate('en', 'fr')
							);
							
							// Fetch and decode JSON string into a PHP object
							$objJsonResponse 	= file_get_contents($strServerEnpoint . http_build_query($arrParams));
							$objDirectionData 	= json_decode($objJsonResponse);
						}
						
						if (
							(true === is_object($objDirectionData)) &&
							(false === empty($objDirectionData->routes))
						) {
							$strInstructions .= '<p>' . $Application->translate('Directions to ', 'Directions Pour ') . '<b>' .
												$objDirectionData->routes[0]->legs[0]->end_address . '</b><p>';
							$strInstructions .= '<p>' . $Application->translate('Start Point ', 'Point de Depart ') . '<b>' . 
												$objDirectionData->routes[0]->legs[0]->start_address . '</b><p><ol>';	
																
							while (list($intIndex, $objDirection) = each(array_shift($objDirectionData->routes)->legs)) {
								if (false === empty($objDirection->steps)) {
									$intTotalDirections = count($objDirection->steps);
									$intIndexCounter 	= 0;
									foreach ($objDirection->steps as $intIndex => $objDataStepData) 
									{
										$intIndexCounter++;
										$strInstructions .= '<li>' . utf8_encode(strip_tags($objDataStepData->html_instructions));
										if (false === ($intIndexCounter === $intTotalDirections))
										{
											$strInstructions .= $Application->translate(' For ', ' Pour ') . 
																$objDataStepData->distance->text . 
																' (' . $objDataStepData->duration->text . ')' . 
																'</li>';		
										}				
									}
								}
							}
							
							$strInstructions .= '</ol>';
						}
					}
					
					// Send the user's confirmation email.
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
						'TITLE' 			=> ''
					));
					
					$strEmailMessage = 	'<h2><strong>' . $Application->translate("Your appointment request at ", "Votre demande de rendez-vous chez ") . 
										ucwords(__SITE_NAME__) . '</strong></p>' . '<br />' .
					'<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial;font-size:12px;color:#000">' .
						'<tr>' .
							'<td align="left" valign="top" colspan="2" style="padding-bottom: 10px">' . 
								'<br /><br /><p>' . $Application->translate('Dear', 'Cher(e)') . ' ' . $objRequestDispatcher->getRequestParam('name') . ',<p>' . 
								$Application->translate(
									'We have received your request for an appointment and a ' . __SITE_NAME__ . ' representative will be in contact ' . 
									'with you shortly.<br />In the interim, should you have any questions or concerns, please feel free to contact us.' . 
									'<br /><br /><br />Sincerely,<br />The ' . constant('__SITE_NAME__') . ' Team. ' .
									'<br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial;font-size:12px;color:#000">' . 
									'<tr>' . 
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon123.png" /></td>' .
										'<td valign="top" width="250"><h2>Office Hours:</h2> Monday to Friday / 8:00am - 6:00pm Sunday / 9:00am - 4:00pm</td>' .
										'<td valign="middle" width="50">&nbsp;</td>' .
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon108.png" /></td>' .
										'<td valign="top" width="250"><h2>Head Office: </h2> 5713, ch. de la Côte-des-Neiges Montréal, QC, H3S 1Y7</td>' .
										'<td valign="middle" width="50">&nbsp;</td>' .
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon231.png" /></td>' .
										'<td valign="top" width="250"><h2>Contact Info:</h2> Phone : ' . constant('__CONTACT_PHONE__') . ' <br />Email : ' . constant('__INFO_EMAIL__') . '</td>' . 
									'<tr></table>', 
									
									'Nous avons reçu votre demande de rendez-vous et un représentant ' . __SITE_NAME__ . ' sera  en contact avec vous sous peu.<br /> ' . 
									'En attendant, si vous avez des questions ou des préoccupations, s\'il vous plaît n\'hésitez pas à nous contacter.' . 
									'<br /><br /><br />Sincèrement,<br />L\'équipe ' . constant('__SITE_NAME__') . '. ' .
									'<br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial;font-size:12px;color:#000">' . 
									'<tr>' . 
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon123.png" /></td>' .
										'<td valign="top" width="250"><h2>Heures d\'ouverture:</h2> Du Lundi au Vendredi / 08h00-18h00 Dimanche / 09h00-16h00</td>' .
										'<td valign="middle" width="50">&nbsp;</td>' .
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon108.png" /></td>' .
										'<td valign="top" width="250"><h2>Siège Social :  </h2> 5713, ch. de la Côte-des-Neiges Montréal, QC, H3S 1Y7</td>' .
										'<td valign="middle" width="50">&nbsp;</td>' .
										'<td valign="middle" width="90"><img src="' . constant('__ROOT_URL__') . '/static/images/icons/icon231.png" /></td>' .
										'<td valign="top" width="250"><h2>Contact:</h2> Tel : ' . constant('__CONTACT_PHONE__') . ' <br />E-mail : ' . constant('__INFO_EMAIL__') . '</td>' . 
									'<tr></table>'
								) .
								(
									(false === empty($strInstructions)) ? 
									'<br /><br /><br /><h2>Directions (' . utf8_encode($objLocation->getName()) . ')</h2>' . $strInstructions : ''
								) .
							'</p></td>' .
						'</tr>';
										 
					$strEmailMessage .= '</table>';
					$objMailer->setTo(filter_var($objRequestDispatcher->getRequestParam('email'), FILTER_VALIDATE_EMAIL));
					$objMailer->setFrom(ucwords(__SITE_NAME__) .  (' <info@' . __SITE_DOMAIN__ . '>'));
					$objMailer->setSubject($Application->translate("Your appointment request at ", "Votre demande de rendez-vous chez ") . ucwords(__SITE_NAME__));
					$objMailer->setTemplate('static/templates/email/contact-us.tmpl');
					$objMailer->setMessage($strEmailMessage);
					$blContinue = $objMailer->send();
					unset($_POST);	
				}
			}
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
	}