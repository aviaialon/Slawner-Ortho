<?php
class MAP_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function largeAction()
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$this->setPageAsyncTrackingDisabled(true);
	}
	
	protected final function catchAllAction()
	{
		
	}
}