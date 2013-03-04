<?php
	require_once(__APPLICATION_ROOT__ . '/debug/dump.php');
	require_once(__APPLICATION_ROOT__ . '/content/savecontent.php');
	require_once(__APPLICATION_ROOT__ . '/url/url.php');
	require_once(__APPLICATION_ROOT__ . '/hybernate/shared_object.' . __APPLICATION_VERSION__ . '.php');
	
	class SITE_EXCEPTION extends Exception {
		public function SITE_EXCEPTION() { }
		 
		public static function supressException() 
		{
			// Use only if the custom error handler is enabled.
			if (TRUE ===  constant('__USE_CUSTOM_ERROR_HANDLER__'))
			{
				set_error_handler('SITE_EXCEPTION_SILENT_ERROR');
				set_exception_handler('SITE_EXCEPTION_SILENT_ERROR');
			}
		}
		 
		public static function clearExceptionSupress() 
		{
			if (TRUE ===  constant('__USE_CUSTOM_ERROR_HANDLER__'))
			{
				restore_error_handler();
				restore_exception_handler();
			}
		}
		
		public static function supress() {
			self::supressException();
		}
		
		public static function restore() {
			self::clearExceptionSupress();
		}
		
		public static function raiseException($ExceptionString=NULL) {
			throw new Exception($ExceptionString);
		}
		
		public static function raiseSilentException() {
			$args = func_get_args();
			SITE_EXCEPTION_SILENT_ERROR($args);
		}
		
		public static function EXCEPTION ($errno, $errstr, $errfile, $errline) {
			 switch ($errno) {
				case E_USER_ERROR: {
					if ($errstr == "(SQL)"){
						// handling an sql error
						echo "<b>SQL Error</b> [$errno] " . SQLMESSAGE . "<br />\n";
						echo "Query : " . SQLQUERY . "<br />\n";
						echo "On line " . SQLERRORLINE . " in file " . SQLERRORFILE . " ";
						echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
						echo "Aborting...<br />\n";
					} else {
						echo "<b>ERROR</b> [$errno] $errstr<br />\n";
						echo "  Fatal error on line $errline in file $errfile";
						echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
						echo "Aborting...<br />\n";
					}
					exit(1);
					break;
				}
				case E_USER_WARNING:
				case E_USER_NOTICE: {
					
				}
			}
			/* Don't execute PHP internal error handler */
			return (true);
		}
		
		public static function throwNewException () {
			$strError = "";
			$arrArgs = func_get_args();
			
			/**
			 * Build the error message that will be stored in the database
			 */
			foreach ($arrArgs as $mxObject => $mxValue) {
				if (
					(is_object($mxValue)) &&
					(strtoupper(get_class($mxValue)) == "EXCEPTION")
				) {
					$strError .= "<h1><strong>ERROR</strong></h1>";
					$strError .= "<p><strong>ERROR MESSAGE</strong>: " . $mxValue->getMessage() . "</p>";
					$strError .= "<p><strong>ERROR FILE</strong>: " . $mxValue->getFile() . "</p>";
					$strError .= "<p><strong>LINE</strong>: " . $mxValue->getLine() . "</p>";
					$strError .= "<p><strong>TRACE</strong>: " . $mxValue->getTraceAsString() . "</p>";
					$strError .= "<p><strong>PREVIOUS</strong>: " . $mxValue->getPrevious() . "</p>"; 
					$strError .= "<p><strong>ERROR MESSAGE</strong>: <pre>" . print_r(debug_backtrace(), true) . "</pre></p>";	
					
					
				} else { 
					$strError .= "<h1><strong>ERROR</strong></h1>";
					$strError .= "<p><strong>ERROR MESSAGE</strong>: <pre>" . print_r($mxValue, true) . "</pre></p>";	
					$strError .= "<p><strong>ERROR MESSAGE</strong>: <pre>" . print_r(debug_backtrace(), true) . "</pre></p>";	
				}
			}
			
			// Mail the error report,
			if (TRUE === constant('__ERROR_MAIL__')) 
			{
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
				$objMailer = new MAIL();
				$objMailer->setTo(__ERROR_EMAILS__);
				$objMailer->setSubject('Error occured in ' . __SITE_NAME__ . ' on ' . date("F j, Y"));
				$objMailer->setMessage($strError);
				$objMailer->send();
			}
			
			// Queue Mail error report
			if (TRUE === constant('__QUEUE_ERROR_MAIL__')) 
			{	
				SITE_EXCEPTION_SILENT_ERROR($strError);
			}
			
			// Error report and defect file report
			if (TRUE === constant('__ERROR_DISPLAY__')) 
			{
				//echo ("<pre>" . $strError . "</pre>");
				
				$strErrorMessage = NULL;
			
				if (
					(is_object($arrArgs[0])) && 
					(is_a($arrArgs[0], 'Exception'))
				) {
					// Exception object passed..
					$strErrorMessage = $arrArgs[0]->getMessage();
				}
				else if (
					(is_object($arrArgs[0][0])) && 
					(is_a($arrArgs[0][0], 'Exception'))
				) {
					// Exception object passed..
					$strErrorMessage = $arrArgs[0][0]->getMessage();
				} 
				else if (is_string($arrArgs[0][0]['message'])) {
					// String message passed
					$strErrorMessage = $arrArgs[0][0]['message'];
				}
				
				
				if (strlen($strErrorMessage))
				{
					if (
						(isset($_GET['defect'])) &&
						($_GET['defect'] == md5($strErrorMessage))
					) {
						print $strErrorMessage . "<br />";
						echo ('<br /><hr /><h1>BACKTRACE</h1><br />');
						new dump($arrBacktrace = debug_backtrace());
						print "<hr /><h1>DETAILS</h1><hr /><pre>"; print_r(debug_backtrace());
						
					}
					else
					{
						/**
						 * Here, we echo the error to the browser - if error display is on in the config
						 */
						 $strDetailedErrorMsg = ob_get_clean();
						 @ob_end_flush();
						 @ob_end_clean();
						 @ob_start();
						 
						 echo (
						 	'<!DOCTYPE html>' .
							'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' .	
							'<head>' .
								'<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' .
								'<title>Internal Server Error</title>' .
								'<style type="text/css">' .
									'/*<![CDATA[*/' .
										'body {font-family:"Verdana";font-weight:normal;color:black;background-color:white;} '.
										'h1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red } '.
										'h2 { font-family:"Verdana";font-weight:normal;font-size:14pt;color:maroon } '.
										'h3 {font-family:"Verdana";font-weight:bold;font-size:11pt} '.
										'p {font-family:"Verdana";font-weight:normal;color:black;font-size:9pt;margin-top: -5px} '.
										'.version {color: gray;font-size:8pt;border-top:1px solid #aaaaaa;} '.
									'/*]]>*/ '.
								'</style> '.
							'</head> '.
									'<body> '.
										'<h1>Internal Server Error</h1> '.
										'<h2>' . $strErrorMessage . '</h2> '.
										'<p><b>' . $strDetailedErrorMsg . '</b></p>'.
										'<p> '.
											'An internal error occurred while the Web server was processing your request.<br />'.
											'Please contact the webmaster to report this problem.<br /><br />A Defect File <a href="?defect=' . 
											md5($strErrorMessage) . '">' . md5($strErrorMessage) . '</a> Created'.
										'</p> '.
										'<p> Thank you. </p> '.
										'<p> The ' . __SITE_NAME__ . ' Team. </p> '.
										'<div class="version">' . now() . '</div> '.
									'</body> '.
							'</html> '
						 );
						//echo ('<hr /><br /><br/><h1><i><u>SITE ERROR DETECTED</u></i></h1><b style="color:red">ERROR:</b> ' . $strErrorMessage);
						//echo ('<br /><hr />Defect File <a href="?defect=' . md5($strErrorMessage) . '">' . md5($strErrorMessage) . '</a> Created');	
					}
				}
				
				die;
				
			} 
			else 
			{
				$objUrl = new URL(__ERROR_PAGERED__);
				if (ob_get_length() && (true === __ERROR_DISPLAY__)) {            
					@ob_flush();
					@flush();
					@ob_end_flush();
				}    
				@ob_start();
				require_once(__ERROR_PAGERED__);
			}
			return (true);
		}
	}
	
	/**
	 * Caller Function
	 */
	 function SITE_EXCEPTION_SILENT_ERROR() 
	 {
		try 
		{ 
			if (TRUE === constant('__QUEUE_ERROR_MAIL__'))
			{
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL_QUEUE");
				$arrArguments = func_get_args();
				$objMailer = new MAIL_QUEUE();
				$objMailer->setTo(__ERROR_EMAILS__);
				$objMailer->setSubject('A Error occured on ' . date("F j, Y H:i:s T"));
				$objMailer->setMessage("
					<p>An error occured and was handled in SITE_EXCEPTION:</p>
					<h1>Below is the error ARGUMENT object:</h1><br />
					<p><pre>" . print_r($arrArguments, true) . "</pre></p>
					<p>Below is the stack trace:</p><br /><br />
					<p><pre>" . print_r(debug_backtrace(), true) . "</pre></p>
				");
				$objMailer->queueMail();
			}
		}

		catch (Exception $exception)
		{
			/**
			 * This is executed when all else fails. If theres a connection issue with the database
			 * Or otherwise, we then fall back to sending an email to the error group.
			 */
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
			$objMailer = new MAIL();
			$objMailer->setTo(__ERROR_EMAILS__);
			$objMailer->setSubject('.:: SITE_EXCEPTION_SILENT_ERROR Failed at ' . __SITE_NAME__ . ' on ' . date("F j, Y") . ' ::.');
			$objMailer->setMessage(debug_backtrace());
			$objMailer->send();
		}
		
		return (true);
	 }
	 
	 function callSiteException()
	 {
		$arrArguments = func_get_args();
		$intErrorNumber = (int) $arrArguments[0];
		$blnReturn = true; /* in some cases we return false because we want PHP to log it in the error logs */

		switch ($intErrorNumber) {
			case E_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_USER_ERROR :
			case E_PARSE :
			case E_RECOVERABLE_ERROR :  
				SITE_EXCEPTION::throwNewException($arrArguments);	 
				break;
		
        	case E_USER_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :  
			case E_WARNING : // For some odd reasons.. fatal errors are in the E_WARNING category..
				//echo "<b>Error - WARNING</b> [$errno] $errstr<br />\n";
				// whoohooooo!
				$blnReturn = true;
				break;
		
			case E_USER_NOTICE :
			case E_NOTICE : 
			case E_STRICT :  
				//echo "<b>Error - NOTICE</b> [$errno] $errstr<br />\n";
				$blnReturn = true;
				break;
				
			case E_RECOVERABLE_ERROR :
			case E_DEPRECATED : 
			case E_USER_DEPRECATED :  
				//echo "<b>Error - RECOVERABLE</b> [$errno] $errstr<br />\n";
				$blnReturn = true;
				break;	
		
			default:
				// Unknown type:
				SITE_EXCEPTION::throwNewException($arrArguments);	 
				break;
			}
		
		
		
		/**
			It is important to remember that the standard PHP error handler is completely bypassed for the error types specified by error_types unless the 
			callback function returns FALSE. error_reporting() settings will have no effect and your error handler will be called regardless - however you 
			are still able to read the current value of error_reporting and act appropriately. Of particular note is that this value will be 0 if the statement 
			that caused the error was prepended by the @ error-control operator.
			
			** Returning true will not execute the internal error handler.
		**/
		return ($blnReturn);
	 }
	 
	 function applicationFatalError() { 
		# Getting last error
		$error = error_get_last();

		# Checking if last error is a fatal error 
		if (! is_null($error))
			callSiteException($error);
	 }
?>