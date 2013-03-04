<?php
class ABOUT_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$Application = $this->getApplication();
		
	}
}