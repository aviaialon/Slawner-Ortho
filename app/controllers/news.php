<?php
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_COMMENTS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORY");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CONTENT");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::UTIL::PAGINATION");

class NEWS_CONTROLLER extends REQUEST_DISPATCHER
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
		$arrViewFilter	= array(
			'imagePositionId' => array(10)
		);
		
		switch (true) {
			case 	(
				((bool) $this->getRequestParam('post')) &&
				((int) $this->getRequestParam('post'))
			) : {
				// Single Post
					$arrViewFilter['operator'][] = '=';
					$arrViewFilter['filter']['a.id'] = (int) $this->getRequestParam('post');
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
					$arrViewFilter['filter']['ncat.news_category_id'] = (int) $this->getRequestParam('category');
					$objNewsCategory = NEWS_CATEGORIES::getInstance(
						(int)  $this->getRequestParam('category'), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
					);
					$this->setViewData('blnCategoryActive', true);
					$this->setViewData('strTitleSubHeader', $Application->translate(
						'News posts for category: ', 'Articles sous la catégorie: ') . 
						'<u>' . $objNewsCategory->getName() . '</u>'
					);
					$this->setViewData('selectedCategory', $objNewsCategory->getName());
				break;	
			}
			
			case (
				((bool) $this->getRequestParam('tag')) &&
				(strlen($this->getRequestParam('tag')))
			) : {
				// Tag Filter
					$strTag 	= $this->getRequestParam('tag');
					$arrTagId 	= array_shift(NEWS_TAG::getObjectClassView(array(
						'columns'	=>	array('a.id'),
						'filter'	=>	array(
							'lang'	=>	$Application->translate('en', 'fr'),
							'tag'	=>	trim($strTag)
						),
						'limit'		=>	1
					)));
					
					if (
						(false === empty($arrTagId)) &&
						(true  === isset($arrTagId['id'])) &&
						(((int) $arrTagId['id']) > 0)
					) {
						$arrViewFilter['operator'][] = '=';
						$arrViewFilter['filter']['ntgs.id'] = (int) $arrTagId['id'];
						$this->setViewData('strTitleSubHeader', $Application->translate(
							'News posts for tag: ', 'Articles sous le tag: ') . 
							'<u>' . ucwords(strtolower(trim($strTag))) . '</u>'
						);
						$this->setViewData('TAG', ucwords(strtolower(trim($strTag))));
						$this->setViewData('blnTagActive', true);
					}
				break;	
			}
			
			case ((bool) $this->getRequestParam('archive')) : 
			{
				// Archive Filter
					$arrArchiveDate = explode('-', $this->getRequestParam('archive'));
					if (count($arrArchiveDate) == 2) 
					{
						$strArchiveDate = date('F Y', mktime(
							0, 0, 0, 
							(((int) $arrArchiveDate[1]) + 1), 0, 
							(int) $arrArchiveDate[0])
						);
						
						$strDisplayDate = strftime('%B %Y', mktime(
							0, 0, 0, 
							(((int) $arrArchiveDate[1]) + 1), 0, 
							(int) $arrArchiveDate[0])
						);
						
						$arrViewFilter['operator'][] = '=';
						$arrViewFilter['filter']['DATE_FORMAT(a.post_date, "%M %Y")'] = $strArchiveDate;
						$this->setViewData('strTitleSubHeader', $Application->translate('Archives for ', 'Archives ') . $strArchiveDate);
						$this->setViewData('blnArchiveActive', true);
						$this->setViewData('blnHasTitle', true);
						$this->setViewData('archiveDate', $strDisplayDate);
					}
				break;	
			}
		}
		
		
		// Get the news posts
		$objPagination = UTIL_PAGINATION::getInstance();
		$objPagination->setDefaultItemsPerPage(3);
		$objPagination->setIsFriendlyUrl(true);
		$objPagination->setBaseUrl(URL::getCanonicalUrl(NULL, false, true, true, array('page')));
		$objPagination->paginateFromClassObjectView('NEWS', NEWS::getItemObjectClassView($arrViewFilter, true));
		$this->loadDefaultViewData();
		$this->setViewData('objPagination', $objPagination);
		
	}
	
	protected final function postAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$Application 	= $this->getApplication();
		$intNewsPostId	= (int) $this->getRequestParam('id');
		$blnContinue	= (
			(false === empty($intNewsPostId)) &&
			($intNewsPostId > 0)
		);
		if (true === $blnContinue) {
			$arrNewsData = NEWS::getItemObjectClassView(array(
				'imagePositionId' 	=> array(10),
				'operator' 			=> array("="),
				'filter'			=> array('a.id' => $intNewsPostId)
			));	
			
			$blnContinue = (false === empty($arrNewsData));
		}
		
		if (false === $blnContinue) {
			$this->pageNotFound();	
		}
		
		$this->loadDefaultViewData();
	}
	
	
	protected final function previewAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$this->assignView('news/index.php');

		$Application 	= $this->getApplication();
		$blnContinue	= (false === empty($_POST));
		if (true === $blnContinue) {
			$Application->getForm()->setUrlParam('lang', $_POST['activeLang']);
			$Application->getForm()->setUrlParam('post', (int) $_POST['news-id']);
			$arrPreviewData = array();
			$arrPreviewData[0] = $_POST;
			$arrPreviewData[0]['post_date'] = date("M j, Y", time());
			$arrPreviewData[0]['title'] = $_POST['title'][$_POST['activeLang']];  
			$arrPreviewData[0]['tags'] 	= implode(',', $_POST['tags'][$_POST['activeLang']]);  
			$arrPreviewData[0]['ownerName'] = $Application->getUser()->getUserName();
			$arrPreviewData[0]['comment_count'] = 0;
			$arrImages = array();
			foreach ($_POST['attachments'] as $strImageUploadUrl) {
				$arrImages[] = '/static/tmp/user-uploads/' . $strImageUploadUrl;
			}
			$arrPreviewData[0]['imagePosition10'] = implode(',', $arrImages);
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
	
	protected final function loadDefaultViewData()
	{
		$Application = $this->getApplication();
		
		// Get the news categories (only categories that have news posts)
		$this->setViewData('arrNewsCategories', NEWS_CATEGORIES::getObjectClassView(array(
			'columns'		=> array('a.name category_name', 'a.id category_id', 'COUNT(DISTINCT nc.news_id) post_count'),
			'filter' 		=> array('lang'	=> $Application->translate('en', 'fr'), 'n.active' => 1),
			'inner_join'	=> array(
				'news_category nc' => 'nc.news_category_id = a.id', 
				'news n' => 'n.id = nc.news_id AND n.active = 1'
			),
			'groupBy'		=> 'a.id',
			'orderBy'		=> 'post_count',
			'direction'		=> 'DESC'
		)));
		
		// Get the random tags
		$this->setViewData('arrNewsTag', NEWS_TAG::getObjectClassView(array(
			'columns'		=> array('a.tag'),
			'filter' 		=> array('lang'	=> $Application->translate('en', 'fr')),
			/*'inner_join'	=> array(
				'news nc' => 'nc.id = a.newsId AND nc.active = 1'
			),*/
			'groupBy'		=> 'a.tag',
			'orderBy'		=> 'RAND()',
			'limit'			=> '12'
		)));
		
		// Get the popular posts
		$this->setViewData('arrPopularNewsPost', NEWS::getItemObjectClassView(array(
			'imagePositionId' 	=> array(11),
			'having'			=> array('imagePosition11 IS NOT NULL'),
			'limit' 			=> 3,
			'orderBy'			=> 'comment_count DESC, RAND()',
			'direction'			=> NULL
		)));
		
		// Get the archives
		$this->setViewData('arrArchivedNewsPost', NEWS::getItemObjectClassView(array(
			'columns' 			=> array('DATE_FORMAT(a.post_date, "%M %Y") archived_date', 'COUNT(DISTINCT a.id) post_count'),
			'having'			=> array('post_count > 0'),
			'limit' 			=> 5,
			'groupBy'			=> 'DATE_FORMAT(a.post_date, "%M %Y")',
			'orderBy'			=> 'DATE_FORMAT(a.post_date, "%M %Y")',
			'direction'			=> 'DESC'
		)));
		
		// Get the latest comments
		$this->setViewData('arrLatestComments', NEWS_COMMENTS::getObjectClassView(array(
			'columns' 			=> array(
				'DATE_FORMAT(a.timedate, "%Y-%m-%d %H:%i:%s %r") post_date', 'a.name', 'a.comment', 'a.newsId', 'a.email_hash'
			),
			'limit' 			=> 4,
			'orderBy'			=> 'a.timedate',
			'direction'			=> 'DESC'
		)));
		
		// Load the comments
		if (true === $this->getViewData('blnSinglePostActive'))
		{
			$objCommentPagination = UTIL_PAGINATION::getInstance();
			$objCommentPagination->setDefaultItemsPerPage(3);
			$objCommentPagination->setPageUrlVariableName('cp');
			$objCommentPagination->setIsFriendlyUrl(true);
			$objCommentPagination->setBaseUrl(URL::getCanonicalUrl(NULL, false, true, true, array('page')));
			$objCommentPagination->paginateFromClassObjectView('NEWS_COMMENTS', array(
				'filter' 	=> array('a.newsId' => (int) $this->getRequestParam('post'), 'commentParentId' => 0),
				'orderBy'	=> 'a.id',
				'direction'	=> 'DESC'
			));
			$arrNewsFilteredComments = array();
			$arrComments = $objCommentPagination->getPageData();
			foreach ($arrComments as $intIndex => $arrCommentData) {
				$arrNewsFilteredComments[(int) $arrCommentData['id']] = $arrCommentData;
				$arrNewsFilteredComments[(int) $arrCommentData['id']]['replies'] = NEWS_COMMENTS::getObjectClassView(array(
					'filter'	=>	array('commentParentId' => (int) $arrCommentData['id']),
					'orderBy'	=> 'a.id',
					'direction'	=> 'DESC'
				));
				unset($arrCommentData[$intIndex]);
			}
			
			$arrCommentCount = array_shift(NEWS_COMMENTS::getObjectClassView(array(
				'columns'	=>	array('IFNULL(COUNT(DISTINCT a.id), 0) comment_count'),
				'filter' 	=> array('a.newsId' => (int) $this->getRequestParam('post'))
			)));
			
			$this->setViewData('objNewsCommentPagination', $objCommentPagination);	
			$this->setViewData('arrNewsComments', $arrNewsFilteredComments);	
			$this->setViewData('intCommentCount', (int) $arrCommentCount['comment_count']);	
		}
		
		$this->setViewData('blnIsAdmin', (true === $Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)));
		$this->setViewData('canonicalUrl', URL::getCanonicalUrl(NULL, false, false, true));
		$this->setViewData('canonicalShareUrl', URL::getCanonicalUrl(NULL, false, true, true, array(), true));
	}
}