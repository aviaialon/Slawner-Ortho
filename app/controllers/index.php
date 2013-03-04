<?php
class INDEX_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction()
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
	}
}