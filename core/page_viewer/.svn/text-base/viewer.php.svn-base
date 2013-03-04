<?php
SHARED_OBJECT::loadSharedObject("PAGES::PAGES");

if (
	(FALSE === isset($_GET['path-url'])) ||
	(FALSE === strlen($_GET['path-url']))
) {
	require_once(__APPLICATION_ROOT__ . '/http-errors/404.php');
	die;
}

$Application 	= APPLICATION::getInstance();
$objDb 	 		= DATABASE::getInstance();
$objPage 		= PAGES::getPageFromPath($_GET['path-url']);
$objUser		= $Application->getUser();

if (
	(TRUE === is_object($objPage)) &&
	($objPage->getId())
) {
	if (TRUE === ($objPage->getSecure_Level() > 0)) 
	{
		$objUser->requireLogin((int) $objPage->getSecure_Level(), false);
	}
	
	$objReqDispatcher = $Application->getRequestDispatcher();
	$objReqDispatcher->setController('PAGE_CONTROLLER'); // setting the required controller because the script_include uses the controller name to dispatch
	$objReqDispatcher->setViewData('Quick_Menu', PAGES::getQuickViewMenu());
	$objReqDispatcher->setViewData('Page_Data', $objPage->getVariable());
	$objReqDispatcher->enableViewCache($objPage->getSecure_Level() ? false : true);
	$objReqDispatcher->useCompression(true);
	//
	// TODO: Set the default view file path here. 
	//
	$objReqDispatcher->renderView(constant('__APP_VIEW_DIR__') . DIRECTORY_SEPARATOR . "site/pages.php");
	print "<!-- Generated:  " . date('l jS \of F Y h:i:s A', strtotime($objPage->getDate_Changed())) . " [" . $objPage->getId() . "] -->"; 	
}

else
{
	require_once(__APPLICATION_ROOT__ . '/http-errors/404.php');
	die;
}


