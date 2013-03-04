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
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");	
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::IO::FILE::FILE_UPLOAD_V2");	
	
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
			'upload_image'	=> 	SITE_USERS_ROLE_ADMIN_USER
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
				(count($arrRequestData) > 1)
			) {
				$strApiVersion = preg_replace("/[^0-9.]/", "", $arrRequestData[1]);
			}
			
			return ((float) $strApiVersion);
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
			$arrMapApiData	  = array_slice(explode('/', substr($strCanonicalUrl, 1)), 3); // Build a request array and remove the '/backstore/api' part
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
		 * This method handles all the API's call
		 * 
		 * @access  protected
		 * @param 	array $arrParams - Request Parameters, see REQUEST_DISPATCHER
		 * @return 	void
		 */
		protected function ___catchAllAction($arrParams = NULL)
		{ 
			$this->enableViewCache(false);
			$this->useCompression(false);
			$this->assignNoView();
			
			$blnContinue	  = true;
			$fltApiVersion 	  = (float) self::getApiVersion();
			$arrParameters 	  = (array) $arrParams['parameters'];
			$strMethodDefault = strtolower(key($arrParameters)); // in case the api request URL methog is called without a param like -> https://.../api/v2.06/getApiVersion/
			$strRequestMethod = self::neutralise(trim(strtolower(array_shift($arrParameters))));  // in case the api request URL methog is called with a param like -> https://.../api/v2.06/method:getApiVersion/
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
						header('Content-type: application/json');
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
			$objFileUploader = FILE_UPLOAD_V2::getInstance();
			$objFileUploader->setAllowedExtensions(array('gif', 'png', 'jpeg', 'jpg', 'bmp'));
			$objFileUploader->setUploadDirectory(constant('__DEV_NULL_PATH__'));
			$objFileUploader->setUploadWebPath('/static/tmp');
			$objFileUploader->setUploadFormInputName('file');
			$objFileUploader->setSizeLimit(10 * 1024 * 1024); // in Bytes
			if (true === ((bool) $objFileUploader->processImageUpload())) 
			{
				$arrReturn = array('filelink' => $objFileUploader->getUploadedFileWebPath());
			} 
			else 
			{
				$arrReturn = array('error' => $objFileUploader->getErrors());
			}
			return ($arrReturn);
		}
		
		/**
		 * This method deletes a news image
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
				$objSiteImage = SITE_IMAGE::getInstanceFromKey(array(
					'originalFileName' => constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $strFileName
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				if ($objSiteImage->getId()) {
					$objSiteImage->delete();
				}	
				$arrReturn['success']  = unlink(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $strFileName);
			}
			return ($arrReturn);
		}
		
		
		/**
		 * This adds a patient profile category
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function add_patient_profiles_category(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORIES");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$strCatName				= trim(ucwords($objRequestDispatcher->getRequestParam('category')));
			$strCatLang				= trim(strtolower($objRequestDispatcher->getRequestParam('lang')));
			
			$blnContinue			= true;
			if (true === empty($strCatName)) 
			{
				$arrReturn['errors'][] = 'Please enter a category';	
				$blnContinue = false;
			}
			
			if (true === empty($strCatLang)) 
			{
				$arrReturn['errors'][] = 'Please enter a language';	
				$blnContinue = false;
			}
			
			if (true === ($blnContinue)) 
			{
				// check if the news category already exists
				$objProfileCategory = PATIENT_PROFILES_CATEGORIES::getInstanceFromKey(array(
					'name'	=>	$strCatName,
					'lang'	=>	$strCatLang
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				if ($objProfileCategory->getId() > 0)
				{
					$blnContinue = false;
					$arrReturn['errors'][] = 'The category ' . $strCatName . ' already exists.';
				}
				else
				{
					$objProfileCategory->setLang($strCatLang);	
					$objProfileCategory->setName($strCatName);	
					$blnContinue = $objProfileCategory->save();
					$arrReturn['data'] = $objProfileCategory->get();
				}		
			}
			
			
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
		
		/**
		 * This method deactivates a patient profile
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function deactivate_patient_profile(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$intProfileId			= (int) $objRequestDispatcher->getRequestParam('profile_id');
			$arrReturn				= array('success' => false);
			
			$objPatientProfile 	    = PATIENT_PROFILES::getInstance($intProfileId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			if (((int) $objPatientProfile->getId()) > 0) 
			{
				$objPatientProfile->disable(); 
				SESSION::set('ok', $Application->translate('Profile was successfully deleted', 'Ce profile a été supprimé avec succès'));
				$arrReturn['success']  = (true);
			}
			return ($arrReturn);
		}
		
		/**
		 * This method deletes a patient profile comment
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function delete_patient_profile_comment(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_COMMENTS");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$intCommentId			= (int) $objRequestDispatcher->getRequestParam('newsCommentId');
			$arrReturn				= array('success' => false, 'newscommentid' => $intNewsCommentId);
			
			$objProfileComment 	   	= PATIENT_PROFILES_COMMENTS::getInstance($intCommentId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$objProfileComment->delete();
			$arrReturn['success']  = (true);
			return ($arrReturn);
		}
		
		/**
		 * This method deletes a patient profile category
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function delete_patient_profile_category(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORIES");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORY");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$intCategoryId			= (int) $objRequestDispatcher->getRequestParam('category_id');
			$blnContinue			= ((bool) ($intCategoryId > 0));
			
			if (true === $blnContinue) 
			{
				// check if the news category already exists
				$objCategory = PATIENT_PROFILES_CATEGORIES::getInstance($intCategoryId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				if ($objCategory->getId())
				{
					$objCategory->delete();
					
					// Get all the affiliated categories
					$arrObjCategory = PATIENT_PROFILES_CATEGORY::getMultiInstance(array(
						'filter' => array('a.patient_profiles_category_id' => $intCategoryId)
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					while (list($intIndex, $objSubCategory) = each($arrObjCategory)) {
						$objSubCategory->delete();
					}
				}
				else
				{
					$arrReturn['success']['errors'][] = 'Invalid category id';
				}
			}
			else
			{
				$arrReturn['success']['errors'][] = 'Invalid category id';	
			}
			
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
		
		
		
		/**
		 * This method deletes a news category
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function delete_news_category(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORY");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$intCategoryId			= (int) $objRequestDispatcher->getRequestParam('category_id');
			$blnContinue			= ((bool) ($intCategoryId > 0));
			
			if (true === $blnContinue) 
			{
				// check if the news category already exists
				$objCategory = NEWS_CATEGORIES::getInstance($intCategoryId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				if ($objCategory->getId())
				{
					$objCategory->delete();
					
					// Get all the affiliated categories
					$arrObjCategory = NEWS_CATEGORY::getMultiInstance(array(
						'filter' => array('a.news_category_id' => $intCategoryId)
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					while (list($intIndex, $objSubCategory) = each($arrObjCategory)) {
						$objSubCategory->delete();
					}
				}
				else
				{
					$arrReturn['success']['errors'][] = 'Invalid category id';
				}
			}
			else
			{
				$arrReturn['success']['errors'][] = 'Invalid category id';	
			}
			
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
		
		/**
		 * This adds a new News category
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function add_news_category(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$strCatName				= trim(ucwords($objRequestDispatcher->getRequestParam('category')));
			$strCatLang				= trim(strtolower($objRequestDispatcher->getRequestParam('lang')));
			
			$blnContinue			= true;
			if (true === empty($strCatName)) 
			{
				$arrReturn['errors'][] = 'Please enter a category';	
				$blnContinue = false;
			}
			
			if (true === empty($strCatLang)) 
			{
				$arrReturn['errors'][] = 'Please enter a language';	
				$blnContinue = false;
			}
			
			if (true === ($blnContinue)) 
			{
				// check if the news category already exists
				$objNewsCategory = NEWS_CATEGORIES::getInstanceFromKey(array(
					'name'	=>	$strCatName,
					'lang'	=>	$strCatLang
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				if ($objNewsCategory->getId() > 0)
				{
					$blnContinue = false;
					$arrReturn['errors'][] = 'The category ' . $strCatName . ' already exists.';
				}
				else
				{
					$objNewsCategory->setLang($strCatLang);	
					$objNewsCategory->setName($strCatName);	
					$blnContinue = $objNewsCategory->save();
					$arrReturn['data'] = $objNewsCategory->get();
				}		
			}
			
			
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
		
		/**
		 * This method deactivates a news post
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function deactivate_news_post(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$intNewsId				= (int) $objRequestDispatcher->getRequestParam('news_id');
			$arrReturn				= array('success' => false);
			
			$objNewsPost	 	    = NEWS::getInstance($intNewsId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			if (((int) $objNewsPost->getId()) > 0) 
			{
				$objNewsPost->disable(); 
				SESSION::set('ok', $Application->translate('News post was successfully deleted', 'L\'archive a été supprimé avec succès'));
				$arrReturn['success']  = (true);
			}
			return ($arrReturn);
		}
		
		/**
		 * This method deletes a news comment
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function delete_news_comment(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_COMMENTS");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$intNewsCommentId		= (int) $objRequestDispatcher->getRequestParam('newsCommentId');
			$arrReturn				= array('success' => false, 'newscommentid' => $intNewsCommentId);
			
			$objNewsComment 	   = NEWS_COMMENTS::getInstance($intNewsCommentId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
			$objNewsComment->delete();
			$arrReturn['success']  = (true);
			return ($arrReturn);
		}
		
		
		/**
		 * This method updates category name
		 * 
		 * @access protected final
		 * @param  array $arrRequestParams - Standard REQUEST_DISPATCHER request data
		 * @return void
		 */
		protected final function update_category_name(array $arrRequestParams)
		{
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORIES");
			
			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$objCategory			= NULL;
			$objCategories			= NULL;
			$intCategoryId			= (int) $objRequestDispatcher->getRequestParam('category_id');
			$strCategoryName		= $objRequestDispatcher->getRequestParam('category_name');
			$strCategoryController	= $objRequestDispatcher->getRequestParam('category_type');
			
			$blnContinue			= (
				(false === empty($intCategoryId)) &&
				($intCategoryId > 0) &&
				(false === empty($strCategoryName)) &&
				(false === empty($strCategoryController))
			);
			
			if (true === $blnContinue) 
			{
				switch ($strCategoryController) 
				{
					case ('PATIENT_PROFILES_CONTROLLER') : 
					{
						$objCategory = PATIENT_PROFILES_CATEGORIES::getInstance($intCategoryId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						break;	
					}
					case ('NEWS_CONTROLLER') : 
					{
						$objCategory = NEWS_CATEGORIES::getInstance($intCategoryId, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						break;	
					}
				}
				
				$blnContinue = ($objCategory->getId() > 0);
				if ($blnContinue) {
					$objCategory->setName($strCategoryName);
					$objCategory->save();
				}
			}
			
			$arrReturn['success'] = $blnContinue;
			return ($arrReturn);
		}
		
		
		/**
		 * This method is executed by the request controller in a callback to update / create a news post
		 * 
		 * @author	Avi Aialon <aviaialon@gmail.com>
		 * @access	protected final
		 * @param	none
		 * @return	void
		 */
		 protected final function manage_news_post(array $arrRequestParams)
		 {
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_COMMENTS");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORY");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CONTENT");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
			SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");

			$Application 			= APPLICATION::getInstance();
			$objRequestDispatcher 	= $Application->getRequestDispatcher();
			$arrReturn				= array('success' => false, 'errors' => array(), 'data' => array());
			$arrErrors				= array();
			
			// Form submit
			if ($Application->getForm()->isPost())
			{
				$arrPostData = $objRequestDispatcher->getRequestParams();
				
				$objNewsPost = NEWS::getInstance(
					(int) $arrPostData['news-id'],
					SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
				);
				
				$objNewsPostContentEnglish = NEWS_CONTENT::getInstanceFromKey(array(
					'news_id' 	=> (int) $arrPostData['news-id'],
					'lang'		=>	'en'	
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				$objNewsPostContentFrench = NEWS_CONTENT::getInstanceFromKey(array(
					'news_id' 	=> (int) $arrPostData['news-id'],
					'lang'		=>	'fr'	
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				
				// 
				//	Validation ...
				//
				
				// 1. Check if there is a news post to save
				$blnHasEnglishPost = ((bool) strlen(
					trim($arrPostData['content']['en']) . 
					trim($arrPostData['title']['en']) . 
					(sizeof($arrPostData['tags']['en']) ? '__tags__' : '') . 
					(sizeof($arrPostData['category']['en']) ? '__category__' : '')
				));
				$blnHasFrenchPost  = ((bool) strlen(
					trim($arrPostData['content']['fr']) . 
					trim($arrPostData['title']['fr']) . 
					(sizeof($arrPostData['tags']['fr']) ? '__tags__' : '') . 
					(sizeof($arrPostData['category']['fr']) ? '__category__' : '')
				));
				
				$blnContinue	   = (true === $blnHasEnglishPost) || (true === $blnHasFrenchPost);
	
				if (true === $blnContinue)
				{
					// begin validation [english]...
					if (true === $blnHasEnglishPost)
					{
						// Validate the content..
						$blnContinue &= (false === empty($arrPostData['title']['en'])) || (($arrErrors[] = 'Please enter a news title for the english version') & false);
						$blnContinue &= (false === empty($arrPostData['content']['en'])) || (($arrErrors[] = 'Please enter news content for the english version') & false);
						$blnContinue &= (false === empty($arrPostData['category']['en'])) || (($arrErrors[] = 'Please select at least one category for the english version') & false);
						$blnContinue &= (false === empty($arrPostData['tags']['en'])) || (($arrErrors[] = 'Please enter at least one tag for the english version') & false);
						$blnContinue &= (false === empty($arrPostData['attachments'])) || (($arrErrors[] = 'Please add at least one image to the news post') & false);
					}
					
					if (true === $blnHasFrenchPost)
					{
						// Validate the content..
						$blnContinue &= (false === empty($arrPostData['title']['fr'])) || (($arrErrors[] = 'Please enter a news title for the french version') & false);
						$blnContinue &= (false === empty($arrPostData['content']['fr'])) || (($arrErrors[] = 'Please enter news content for the french version') & false);
						$blnContinue &= (false === empty($arrPostData['category']['fr'])) || (($arrErrors[] = 'Please select at least one category for the french version') & false);
						$blnContinue &= (false === empty($arrPostData['tags']['fr'])) || (($arrErrors[] = 'Please enter at least one tag for the french version') & false);
						$blnContinue &= (false === empty($arrPostData['attachments'])) || (($arrErrors[] = 'Please add at least one image to the news post') & false);	
					}
					
					if (true === ((bool) $blnContinue))
					{
						if (
							(true === $blnHasEnglishPost) ||
							(true === $blnHasFrenchPost)
						) {
							// Save the news post
							$objNewsPost->setOwnerUserId($Application->getUser()->getId());
							$objNewsPost->setActive(ACTIVE_STATUS_ENABLED);
						}
						
						// Create the news post....
						if (true === $blnHasEnglishPost)
						{
							// Save the tags
							$Application->getDatabase()->query("DELETE FROM news_tag WHERE newsId = " . (int) $objNewsPost->getId() . " AND lang='en'");
							reset($arrPostData['tags']['en']);
							while(list($intIndex, $strItemTag) = each($arrPostData['tags']['en']))
							{
								$objNewsPostTag = NEWS_TAG::newInstance(); 	
								$objNewsPostTag->setLang('en');
								$objNewsPostTag->setNewsId((int) $objNewsPost->getId());
								$objNewsPostTag->setTag($strItemTag);
								$blnContinue &= $objNewsPostTag->save();
							}
							
							// Save the categories
							$arrNewsCategories = NEWS_CATEGORY::getMultiInstance(array(
								'columns'		=> 	array('a.id'),
								'inner_join'	=>	array('news_categories ncs' => 'ncs.id = a.news_category_id AND ncs.lang = "en"'),
								'filter'		=>	array('a.news_id' => (int) $objNewsPost->getId())		
							));
							
							while(list($intIndex, $objNewsCategory) = each($arrNewsCategories)) {
								$objNewsCategory->delete();
							}
							
							reset($arrPostData['category']['en']);
							while(list($intIndex, $intNewsCategoryId) = each($arrPostData['category']['en']))
							{
								$objNewsCategory = NEWS_CATEGORY::newInstance(); 	
								$objNewsCategory->setNews_Category_Id((int) $intNewsCategoryId);
								$objNewsCategory->setNews_Id((int) $objNewsPost->getId());
								$blnContinue &= $objNewsCategory->save();
							}
							
							// Save the content
							$objNewsPostContentEnglish->setTitle($arrPostData['title']['en']);
							$objNewsPostContentEnglish->setContent($arrPostData['content']['en']);
							$objNewsPostContentEnglish->setNews_Id((int) $objNewsPost->getId());
							$blnContinue &= $objNewsPostContentEnglish->save();
							
							$arrMessage[] = 'English news saved successfully.';
						}
						
						
						if (true === $blnHasFrenchPost)
						{
							// Save the tags
							$Application->getDatabase()->query("DELETE FROM news_tag WHERE newsId = " . (int) $objNewsPost->getId() . " AND lang='fr'");
							reset($arrPostData['tags']['fr']);
							while(list($intIndex, $strItemTag) = each($arrPostData['tags']['fr']))
							{
								$objNewsPostTag = NEWS_TAG::newInstance(); 	
								$objNewsPostTag->setLang('fr');
								$objNewsPostTag->setNewsId((int) $objNewsPost->getId());
								$objNewsPostTag->setTag($strItemTag);
								$blnContinue &= $objNewsPostTag->save();
							}
							
							// Save the categories
							$arrNewsCategories = NEWS_CATEGORY::getMultiInstance(array(
								'columns'		=> 	array('a.id'),
								'inner_join'	=>	array('news_categories ncs' => 'ncs.id = a.news_category_id AND ncs.lang = "fr"'),
								'filter'		=>	array('a.news_id' => (int) $objNewsPost->getId())		
							));
							
							while(list($intIndex, $objNewsCategory) = each($arrNewsCategories)) {
								$objNewsCategory->delete();
							}
							
							reset($arrPostData['category']['fr']);
							while(list($intIndex, $intNewsCategoryId) = each($arrPostData['category']['fr']))
							{
								$objNewsCategory = NEWS_CATEGORY::newInstance(); 	
								$objNewsCategory->setNews_Category_Id((int) $intNewsCategoryId);
								$objNewsCategory->setNews_Id((int) $objNewsPost->getId());
								$blnContinue &= $objNewsCategory->save();
							}
							
							// Save the content
							$objNewsPostContentFrench->setTitle($arrPostData['title']['fr']);
							$objNewsPostContentFrench->setContent($arrPostData['content']['fr']);
							$objNewsPostContentFrench->setNews_Id((int) $objNewsPost->getId());
							$blnContinue &= $objNewsPostContentFrench->save();
							
							$arrMessage[] = 'French news saved successfully.';
						}
						
						// Create the images....
						SITE_IMAGE::clearImagesFromOwner(
							(int) $objNewsPost->getClassNumericId(), 
							(int) $objNewsPost->getId()
						);
						
						if (
							(isset($_POST['attachments'])) &&
							(false === empty($_POST['attachments']))
						) {
							foreach($_POST['attachments'] as $strFileName => $imgPath) {
								$objItemImage = SITE_IMAGE::createPosition0Image(
									constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $imgPath, 
									(int) $objNewsPost->getClassNumericId(), 
									(int) $objNewsPost->getId()
								);
								if ($objItemImage->getId() > 0) {
									SITE_IMAGE::createItemImagePositions($objItemImage->getId());
								}
							}
						}
						
						
						if ($blnContinue) {
							$objNewsPost->save();		
							$arrReturn['news_id'] = $objNewsPost->getId();
						}
					}
				}
			}
			
			$arrReturn['errors'] 	= implode('|', $arrErrors);
			$arrReturn['message'] 	= implode('|', $arrMessage);
			$arrReturn['success'] 	= ((bool) $blnContinue);
			
			return ($arrReturn);
		 }
	}