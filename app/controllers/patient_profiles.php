<?php
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORIES");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_COMMENTS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CATEGORY");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES_CONTENT");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::UTIL::PAGINATION");

class PATIENT_PROFILES_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function manageAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
	}
	
	protected final function indexAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$Application 	= $this->getApplication();
		$intLangId		= $Application->translate(1, 2);
		$intProfileId	= (int) $this->getRequestParam('profile-id');
		$arrViewFilter	= array(
			'imagePositionId' => array(14, 15)
		);
		
		unset($arrRequestParams['parameters']['lang']);
		//$strRequestedKey	= key(array_shift(array_flip($arrRequestParams['parameters'])));
		$strRequestedKey	= key($arrRequestParams['parameters']);
		$intSeoUrlProfileId = (int) $this->getRequestParam($strRequestedKey);
		if (
			(false === empty($intSeoUrlProfileId)) &&
			(false === in_array($strRequestedKey, array('category', 'page', 'cp')))
		) {
			$intProfileId = $intSeoUrlProfileId;
			$this->setRequestParam('profile-id', $intSeoUrlProfileId);
		}
		
		switch (true) {
			case 	($intProfileId > 0) : {
				// Single Post
					$arrViewFilter['operator'][] = '=';
					$arrViewFilter['filter']['a.id'] = $intProfileId;
					// Create the captcha
					$arrOperators	= array('+', '-', '+', '+');
					$intDigit1 		= mt_rand(1, 10);
					$intDigit2 		= mt_rand(1, 10);
					$strOperator 	= $arrOperators[array_rand($arrOperators)];
					$strFunction 	= ($strOperator == '+' ? 'bcadd' : 'bcsub');
					$intDigit1 		= ($strOperator == '-' ? max($intDigit1, $intDigit2) : $intDigit1);
					$intDigit2 		= ($strOperator == '-' ? min($intDigit1, $intDigit2) : $intDigit2);
					$intValue 		= $strFunction($intDigit1, $intDigit2);
					$this->setViewData('captchaFormula', $intDigit1 . ' ' . $strOperator . ' ' . $intDigit2 . ' =');
					$this->setViewData('captchaChallenge', $Application->getCrypto()->encrypt($intValue));
					$this->setViewData('blnSinglePostActive', true);
				break;
			}
			
			case (
				((bool) $this->getRequestParam('category')) &&
				((int) $this->getRequestParam('category'))
			) : {
				// Category Filter
					$arrViewFilter['operator'][] = '=';
					$arrViewFilter['filter']['ncat.patient_profiles_category_id'] = (int) $this->getRequestParam('category');
					$objPatientProfileCategory = PATIENT_PROFILES_CATEGORIES::getInstance(
						(int)  $this->getRequestParam('category'), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
					);
					$this->setViewData('blnCategoryActive', true);
					$this->setViewData('strTitleSubHeader', $Application->translate(
						'Profiles for category: ', 'Profiles sous la catégorie: ') . 
						'<u>' . $objPatientProfileCategory->getName() . '</u>'
					);
					$this->setViewData('selectedCategory', $objPatientProfileCategory->getName());
				break;	
			}
		}
		
		
		// Get the news posts
		$this->loadDefaultViewData();
		$objPagination = UTIL_PAGINATION::getInstance();
		$objPagination->setDefaultItemsPerPage(8);
		$objPagination->setIsFriendlyUrl(true);
		$objPagination->setBaseUrl(URL::getCanonicalUrl(NULL, false, true, true, array('page')));
		$objPagination->paginateFromClassObjectView('PATIENT_PROFILES', PATIENT_PROFILES::getItemObjectClassView($arrViewFilter, true));
		$this->setViewData('objPagination', $objPagination);
	}
	
	protected final function postAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$Application 		= $this->getApplication();
		$intProfilePostId	= (int) $this->getRequestParam('id');
		$blnContinue		= (
			(false === empty($intProfilePostId)) &&
			($intProfilePostId > 0)
		);
		if (true === $blnContinue) {
			$arrPatientProfileData = PATIENT_PROFILES::getItemObjectClassView(array(
				'imagePositionId' 	=> array(10),
				'operator' 			=> array("="),
				'filter'			=> array('a.id' => $intProfilePostId)
			));	
			
			$blnContinue = (false === empty($arrPatientProfileData));
		}
		
		if (false === $blnContinue) {
			$this->pageNotFound();	
		}
		
		$this->loadDefaultViewData();
	}
	
	protected final function loadDefaultViewData()
	{
		$Application = $this->getApplication();
		
		// Get the profile categories (only categories that have patient profiles)
		$this->setViewData('arrProfilesCategories', PATIENT_PROFILES_CATEGORIES::getObjectClassView(array(
			'columns'		=> array('a.name category_name', 'a.id category_id', 'COUNT(DISTINCT nc.patient_profiles_id) post_count'),
			'filter' 		=> array('lang'	=> $Application->translate('en', 'fr'), 'n.active' => 1),
			'inner_join'	=> array(
				'patient_profiles_category nc' => 'nc.patient_profiles_category_id = a.id', 
				'patient_profiles n' => 'n.id = nc.patient_profiles_id AND n.active = 1'
			),
			'groupBy'		=> 'a.id',
			'orderBy'		=> 'post_count',
			'direction'		=> 'DESC'
		)));

		// Load the comments
		if (true === $this->getViewData('blnSinglePostActive'))
		{
			$objCommentPagination = UTIL_PAGINATION::getInstance();
			$objCommentPagination->setDefaultItemsPerPage(3);
			$objCommentPagination->setPageUrlVariableName('cp');
			$objCommentPagination->setIsFriendlyUrl(true);
			$objCommentPagination->setBaseUrl(URL::getCanonicalUrl(NULL, false, true, true, array('cp')));
			$objCommentPagination->paginateFromClassObjectView('PATIENT_PROFILES_COMMENTS', array(
				'filter' 	=> array('a.patient_profiles_id' => (int) $this->getRequestParam('profile-id'), 'commentParentId' => 0),
				'orderBy'	=> 'a.id',
				'direction'	=> 'DESC'
			));

			$arrProfileFilteredComments = array();
			$arrComments = $objCommentPagination->getPageData();
			foreach ($arrComments as $intIndex => $arrCommentData) {
				$arrProfileFilteredComments[(int) $arrCommentData['id']] = $arrCommentData;
				$arrProfileFilteredComments[(int) $arrCommentData['id']]['replies'] = PATIENT_PROFILES_COMMENTS::getObjectClassView(array(
					'filter'	=>	array('commentParentId' => (int) $arrCommentData['id']),
					'orderBy'	=> 'a.id',
					'direction'	=> 'DESC'
				));
				unset($arrCommentData[$intIndex]);
			}
			
			$this->setViewData('objProfilesCommentPagination', $objCommentPagination);	
			$this->setViewData('arrProfilesComments', $arrProfileFilteredComments);	
		}
		
		$this->setViewData('blnIsAdmin', (true === $Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)));
		$this->setViewData('canonicalUrl', URL::getCanonicalUrl(NULL, false, false, true));
		$this->setViewData('canonicalShareUrl', URL::getCanonicalUrl(NULL, false, true, true, array(), true));
	}
	
	
	
	protected final function previewAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$this->assignView('patient_profiles/index.php');

		$Application 	= $this->getApplication();
		$blnContinue	= (false === empty($_POST));
		if (true === $blnContinue) {
			$Application->getForm()->setUrlParam('lang', $_POST['activeLang']);
			$Application->getForm()->setUrlParam('post', (int) $_POST['profile-id']);
			$Application->getRequestDispatcher()->setRequestParam('profile-id', (int) $_POST['profile-id']);
			$arrPreviewData = array();
			$arrPreviewData[0] = $_POST;
			$arrPreviewData[0]['post_date'] = date("M j, Y", time());
			$arrPreviewData[0]['name'] = $_POST['name'][$_POST['activeLang']];  
			$arrPreviewData[0]['ownerName'] = $Application->getUser()->getUserName();
			$arrPreviewData[0]['comment_count'] = 0;
			$arrImages = array();
			foreach ($_POST['attachments'] as $strImageUploadUrl) {
				$arrImages[] = '/static/tmp/user-uploads/' . $strImageUploadUrl;
			}
			$arrPreviewData[0]['imagePosition15'] = implode(',', $arrImages);
			$arrPreviewData[0]['content'] = $_POST['content'][$_POST['activeLang']];
			$arrPreviewData[0]['category_id'] = 0;
			// Captcha...
			$arrOperators	= array('+', '-', '+', '+');
			$intDigit1 		= mt_rand(1, 10);
			$intDigit2 		= mt_rand(1, 10);
			$strOperator 	= $arrOperators[array_rand($arrOperators)];
			$strFunction 	= ($strOperator == '+' ? 'bcadd' : 'bcsub');
			$intDigit1 		= ($strOperator == '-' ? max($intDigit1, $intDigit2) : $intDigit1);
			$intDigit2 		= ($strOperator == '-' ? min($intDigit1, $intDigit2) : $intDigit2);
			$intValue 		= $strFunction($intDigit1, $intDigit2);
			
			$this->setViewData('captchaFormula', $intDigit1 . ' ' . $strOperator . ' ' . $intDigit2 . ' =');
			$this->setViewData('captchaChallenge', $Application->getCrypto()->encrypt($intValue));
			$this->setViewData('arrPreviewData', $arrPreviewData);
			$this->setViewData('blnSinglePostActive', true);
			$this->setViewData('blnPreviewPostActive', true);
			/*
			$arrNewsData = NEWS::getItemObjectClassView(array(
				'imagePositionId' 	=> array(10),
				'operator' 			=> array("="),
				'filter'			=> array('a.id' => $intNewsPostId)
			));	
			*/
		}
		$this->loadDefaultViewData();
	}
}