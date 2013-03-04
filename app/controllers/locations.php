<?php
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");

class LOCATIONS_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
	}
}