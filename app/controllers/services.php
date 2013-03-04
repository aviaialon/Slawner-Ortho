<?php
/**
 * Services handling controller class
 */
class SERVICES_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function catchAllAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$strRequestedAction 	= $this->getAction_Name();
		$arrAvailableActions	= array(
			'index', 
			'whatever', 
			'orthotics'
		);
		(true === in_array($strRequestedAction, $arrAvailableActions)) ||
			$this->pageNotFound();
	}
}