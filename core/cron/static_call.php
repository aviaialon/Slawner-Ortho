<?php
	 ini_set('display_errors','On'); 
	 error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	 set_error_handler(___callCronStackException);
 	// 
	// This file runs the cron job staticly. 
	// It allows classes to be instantiated [staticly].
	// Example useage: /usr/bin/php /var/www/www.adzaro.com/classes/cron/static_call.php DHL::TEST
	// /usr/bin/php /var/www/www.adzaro.com/classes/cron/static_call.php classes::session::session::collectGarbage > /var/www/www.adzaro.com/log_test.log
	// Set the base path
	$strBasePath = str_replace('/application/cron/static_call.php', '', $_SERVER['SCRIPT_NAME']);
	
	$_SERVER['DOCUMENT_ROOT'] 	= $strBasePath;
	$_SERVER['CRON_ACTIVE'] 	= true;
	
	// Require needed objects
	require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '/application/config/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '/application/database/database.php');
	#require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '/aplication/utility-functions.php');
	
	// Set this field here because we need the config.php file. DO NOT MOVE.
	$_SERVER['HTTP_HOST'] 		= "http://www." . __SITE_DOMAIN__;
	
	// Begin parseing
	$arrClassFile  = explode("::", strtolower($_SERVER['argv'][1]));
	$strMethodName = $arrClassFile[sizeof($arrClassFile) - 1];
	unset($arrClassFile[sizeof($arrClassFile) - 1]);
	$strClassName = $arrClassFile[sizeof($arrClassFile) - 1];
	$strClassPath  = $strBasePath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arrClassFile) . '.php';

	if (file_exists($strClassPath)) {
		try {
			print "\n\nLoading: " . $strClassPath . "\n\n"; 
			require_once($strClassPath);
			print "Calling: " . strtoupper($strClassName) . "::" . $strMethodName . "();\n";
			eval(strtoupper($strClassName) . "::" . $strMethodName . "(" . $_SERVER['argv'][2] . ");");
			print "\n\n";
			___log('Cron: ' . $_SERVER['argv'][1] . ' Executed Successfully at ' . date("F j, Y, g:i a"));
			___shortLog('Cron: ' . $_SERVER['argv'][1] . ' Executed Successfully at ' . date("F j, Y, g:i a"), $strClassName);
		}  catch (Exception $e) {
			___log('ERROR Cron: ' . $_SERVER['argv'][1] . ' \n\n' . debug_backtrace(), 1);	
		}
	} else {
		___log('Class File: ' . $strClassPath . ' does not exists.', 1);	
		throw new Exception('Class File: ' . $strClassPath . ' does not exists.');	
	}
	
	function ___log($strLogOut, $intIsError = 0) {
		$strBasePath = str_replace('/application/cron/static_call.php', '', $_SERVER['SCRIPT_NAME']);
		$_SERVER['DOCUMENT_ROOT'] 	= $strBasePath;
		$_SERVER['CRON_ACTIVE'] 	= true;
		
		/*
		$class = $_SERVER['argv'][1];
		$objDb = new DATABASE();
		$objDb->query("
			INSERT INTO crontab (`class`,`output`,`is_error`, `time_date`)		
			VALUES (
				'" . $objDb->escape($class) . "',
				'" . $objDb->escape($strLogOut) . "',
				" . (int) $intIsError . ",
				NOW()
			)		
		");
		*/
		$fh = fopen($_SERVER['DOCUMENT_ROOT'] . "/logs/cron.log", 'a+');
		$logTxt = $strLogOut . "\n";
		fputs($fh, $logTxt, strlen($logTxt));
		fclose($fh);
	}
	
	function ___shortLog($strMessage, $strShortClassExtraFileName=NULL) {
		$strBasePath = str_replace('/application/cron/static_call.php', '', $_SERVER['SCRIPT_NAME']);
		$_SERVER['DOCUMENT_ROOT'] 	= $strBasePath;
		$_SERVER['CRON_ACTIVE'] 	= true;
		require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '/application/config/config.php');
		
		$fh = fopen($_SERVER['DOCUMENT_ROOT'] . "/logs/short-cron.log", 'a+');
		$logTxt = now() . "\n" . $strMessage .  "\n---------------------------------------------------------------------\n";
		fputs($fh, $logTxt, strlen($logTxt));
		fclose($fh);
		
		// Extra file name...
		if (
			(! is_null($strShortClassExtraFileName)) /* &&
			(file_exists($_SERVER['DOCUMENT_ROOT'] . "/logs/cron-" . $strShortClassExtraFileName . '.log')) -- Removed so that the file is created if it doesnt exists*/
		) {
			$fh = fopen($_SERVER['DOCUMENT_ROOT'] . "/logs/cron-" . $strShortClassExtraFileName . '.log', 'a+');
			$logTxt = now() . "\n" . $strMessage .  "\n---------------------------------------------------------------------\n";
			fputs($fh, $logTxt, strlen($logTxt));
			fclose($fh);	
		}
	}
	
	function ___callCronStackException($e) {
		// base64_encode(serialize(debug_backtrace())) 
		___log(
			'*** ERROR in Cron: ' . $_SERVER['argv'][1] . ' Execute at ' . date("F j, Y, g:i a") . "\n      " . "----->" . print_r(func_get_args(), true) . "\n\n",
			1
		);	
		$arrError = func_get_args();
		___shortLog($arrError[1] . "\n" . $arrError[2] . " | Line: " . $arrError[3]);
	}
?>