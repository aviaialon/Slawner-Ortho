<?php 
$Application = APPLICATION::getInstance();
$Application->bootstrap() 				/* Shouldnt need to bootstrap, its hadled on getInstance() */
			->webControllerInitiate()  	/* initiate the frontController instance */
			->getRequestDispatcher()	/* Get the request dispatcher @return: {APPLICATION_ROOT}::HYBERNAGE::OBJECTS::PAGES */
			->dispatch();	
die;