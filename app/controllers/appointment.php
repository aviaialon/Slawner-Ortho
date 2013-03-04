<?php
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::LOCATIONS::LOCATIONS");

class APPOINTMENT_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$this->setViewData('location-array', LOCATIONS::getObjectClassView(array(
			'columns' 	=> array('a.*'),
			'orderBy'	=> 'a.id',
			'direction'	=> 'ASC'
		)));
	}
}