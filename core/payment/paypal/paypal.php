<?php
	require_once(__APPLICATION_ROOT__ . '/database/database.php');
	/**
	 * PAYPAL - This class represents a paypal payment gateway via IPN
	 */ 
	define('PAYPAL_HTTPS_LIVE_GATEWAY', 	1);
	define('PAYPAL_HTTPS_SANDBOX_GATEWAY', 	2);
	
	/**
	 * @package: CLASSES::PAYMENT::PAYPAL
	 */ 
	class PAYPAL {
		
		#		define variables here
		# -----------------------------------
		protected  $arrPaypalVariables = array(
			'SANBOX' => array(
				'connect_url'	=> 'https://www.sandbox.paypal.com/cgi-bin/webscr', // PayPal's SandBox URL		 
				'auth_token' 	=> 'M9X9sCIwGQetDfOLfAYZ2Gq_pdN_yCe3u7QQQoXSwlhqm4cziZqgJfLBHXK', // The PayPal auth token			
				'return_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/success.php', 	// Return URL on Success
				'cancel_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/cancelled.php',	// Return URL on Cancel
				'notify_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/success.php',	// IPN Notification URL.
				'account_email' => 'sales_1272505245_biz@regentvanlines.com', // Business account holder email
				'header_image'	=> 'https://secure.hostmonster.com/~regentva/index_files/images/paypal-header.jpg' // Header image URL
			),
			'LIVE' 	 => array(
				'connect_url'	=> 'https://www.paypal.com/cgi-bin/webscr', // PayPal's Live URL		 
				'auth_token' 	=> 'uS_bwm6ti5jxm4It5YpNQXosDItAqNS6DiCV7f8oak_S71X2ljaYU73hNe8', // The PayPal auth token			
				'return_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/success.php', 	// Return URL on Success
				'cancel_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/cancelled.php',	// Return URL on Cancel
				'notify_url'	=> 'https://secure.hostmonster.com/~regentva/paypal/success.php',	// IPN Notification URL.
				'account_email' => 'sales@regentvanlines.com', // Business account holder email
				'header_image'	=> 'https://secure.hostmonster.com/~regentva/index_files/images/paypal-header.jpg' // Header image URL		 			 
			)
		);
		# -----------------------------------
		#
		
		# Public Members
		public  	$arrErrors 		= array();
		public  	$arrIpnResults 	= array();
		
		# protected members
		protected	$objDb 					= NULL;
		protected  	$PAYPAL_PROCESSING_ENV	= NULL;
		protected  	$PAYPAL_AUTH_TOKEN 		= NULL;
		protected  	$PAYPAL_CONNECT_URL 	= NULL;
		protected  	$PAYPAL_RETURN_URL 		= NULL;
		protected  	$PAYPAL_CANCEL_URL 		= NULL;
		protected  	$PAYPAL_IPN_NOTIFY_URL 	= NULL;
		protected  	$PAYPAL_ACCOUNT_EMAIL 	= NULL;
		protected  	$PAYPAL_HEADER_IMAGE 	= NULL;
		protected  	$PAYPAL_PAYMENT_STATUS 	= NULL;
		protected  	$PAYPAL_IS_SUCCESS 		= false;
		protected  	$PAYPAL_CONFIG_DATA 	= array();
		
		# FORM CONFIG PAYMENT DATA 
		protected  	$PAYMENT_AMOUNT 	= 0.00;
		protected  	$SHIPPING			= 0.00;
		protected  	$SHIPPING2			= 0.00;
		protected  	$HANDLING			= 0.00;
		protected  	$NO_SHIPPING		= 0;
		protected  	$NO_NOTE			= 0;
		protected  	$ITEM_QTY			= 0;
		protected  	$RECEIVER_EMAIL		= NULL;
		protected  	$ITEM_NAME 			= NULL;
		protected  	$ITEM_NUMBER		= NULL;
		protected  	$CURRENCY_CODE		= 'CAD';
		
		# -----------------------------------
		# Public methods
		# -----------------------------------
		public function PAYPAL($intConstructType=PAYPAL_HTTPS_LIVE_GATEWAY) {
			# Database
			$this->objDb = new DATABASE();
			
			# Set the processing environment
			$this->setProcessingEnvironment(
				((intval($intConstructType) !== PAYPAL_HTTPS_LIVE_GATEWAY)	&&
			  	(intval($intConstructType) 	!== PAYPAL_HTTPS_SANDBOX_GATEWAY) ? PAYPAL_HTTPS_LIVE_GATEWAY : $intConstructType)								
			);	
			# Set the confog
			$this->setPayPalConfig();
		}
		
		/**
		 * This method processes an incommin IPN request from paypal
		 * @return: 
		 * @param:  VOID
		 */
		public function processNotifySyncRequest() {
			// Make sure the tx id is in the URL
			
			// read the post from PayPal system and add 'cmd'
			//{
				$tx_token 		= $this->getTxToken();
				$auth_token 	= $this->getAuthToken();
				$req 			= 'cmd=_notify-synch';
				$req 		   	.= "&tx={$tx_token}&at={$auth_token}";
			//}
			
			// post back to PayPal system to validate
			//{
				$header 		.= "POST /cgi-bin/webscr HTTP/1.0\r\n";
				$header 		.= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header 		.= "Content-Length: " . strlen($req) . "\r\n\r\n";
				$fp 			 = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
			//}
			// If possible, securely post back to paypal using HTTPS
			// Your PHP server will need to be SSL enabled
			// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
			
			if (! ($fp)) {
				// HTTP ERROR
				$this->setError("HTTP ERROR: " . $errstr);
				return (false);
				
			} else {
				fputs ($fp, $header . $req);
				
				// read the body data
				$res 		= '';
				$headerdone = false;
				
				// Parse the file....
				while (! ((bool) feof($fp))) {
					$line = fgets ($fp, 1024);
					if (strcmp($line, "\r\n") == 0) { $headerdone = true; }
					else if ($headerdone) { $res .= $line; }
				}
				
				// parse the data
				$lines 			= explode("\n", $res);
				$arrKeyArray 	= array();
				for ($i=1; $i< count($lines);$i++){
					list($key,$val) = explode("=", $lines[$i]);
					$arrKeyArray[trim(urldecode($key))] = urldecode($val);
				}
				
				// Set the IPN info
				$this->setIpnInfo(array_change_key_case($arrKeyArray, CASE_LOWER));
				
				// Find an error if aplicable
				$arrTempError = explode(":", $lines[1]);
				if (strcmp(strtolower($arrTempError[0]), 'error') == 0)
					$this->setError('PayPal Error [' . $lines[1] . ']');
				
				// Set the success value ...
				$this->setSuccess(
					((strcmp(strtolower($this->getIpnInfo('payment_status')), 'completed') == 0) &&
					(strcmp(strtolower($this->getIpnInfo('receiver_email')), $this->getAccountEmail()) == 0) &&
					$this->getIpnInfo('payment_gross') == $this->arrIpnResults('mc_gross')) ? true : false			  
				);	
				
				// Set the payment status
				$this->setPaymentStatus(
					(strlen($this->getIpnInfo('payment_status')) ? $this->getIpnInfo('payment_status') : 'Failed')					
				);
			}
			fclose ($fp);
		}
		
		/**
		 * This method returns a specific value from the IPD notify Sinc result.
		 * If the parameter is null or empty, all the values are returned.
		 * @return: string/array - IPN RESULT ARRAY VALUE
		 * @param:  $strIpnKey String - The key value to return [default: NULL]
		 */
		public function getIpnInfo($strIpnKey=NULL) {
			if (
				(is_null($strIpnKey)) ||
				(! strlen($strIpnKey))
			) {
				return ($this->arrIpnResults);	
			} else if (array_key_exists(strtolower($strIpnKey), array_change_key_case($this->arrIpnResults, CASE_LOWER))) {
				return ($this->arrIpnResults[strtolower($strIpnKey)]);
			} else { 
				return (false); 
			} 
		}
		
		public function getPayPalFormData() {
			print ('<form action="' . $this->getConnectUrl() . '" method="post" target="_blank">');
				print ('<input type="hidden" name="cmd" 			value="_xclick" />'  . "\n");
				print ('<input type="hidden" name="image_url" 		value="' . $this->getHeaderImage() . '" />'  . "\n");
				print ('<input type="hidden" name="return" 			value="' . $this->getReturnUrl() . '" />' 	 . "\n");
				print ('<input type="hidden" name="cancel_return" 	value="' . $this->getReturnUrl() . '" />' 	 . "\n");
				print ('<input type="hidden" name="notify_url" 		value="' . $this->getIpnNotifyUrl() . '" />' . "\n");
				print ('<input type="hidden" name="business" 		value="' . $this->getAccountEmail() . '" />' . "\n");
				
				print ('<input type="hidden" name="item_name" 			value="' . $this->getItemName() . '" />' . "\n");
				print ('<input type="hidden" name="item_number"			value="' . $this->getItemNumber() . '" />' . "\n");
				print ('<input type="hidden" name="amount" 				value="' . $this->getPaymentAmount() . '" />' . "\n");
				print ('<input type="hidden" name="currency_code"		value="' . $this->getCurrencyCode() . '" />' . "\n");
				print ('<input type="hidden" name="shipping" 			value="' . $this->getShipping1Amount() . '" />' . "\n");
				print ('<input type="hidden" name="shipping2" 			value="' . $this->getShipping2Amount() . '" />' . "\n");
				print ('<input type="hidden" name="handling" 			value="' . $this->getHandlingAmount() . '" />' . "\n");
				print ('<input type="hidden" name="undefined_quantity" 	value="' . $this->getQty() . '" />' . "\n");
				print ('<input type="hidden" name="receiver_email" 		value="' . $this->getReceiverEmail() . '" />' . "\n");
				print ('<input type="hidden" name="no_shipping" 		value="' . $this->getNoShipping() . '" />' . "\n");
				print ('<input type="hidden" name="no_note" 			value="' . $this->getNoNote() . '" />' . "\n");
				print ('<input type="image" name="submit" src="http://images.paypal.com/images/x-click-but6.gif" border="0" alt="Make payments with PayPal, it\'s fast, free, and secure!" />');
			print ("</form>");		
		}
		
		public function getSerializedIpnInfo() 	{ return(serialize($this->getIpnInfo())); }
		public function getConfigData() 		{ return ($this->PAYPAL_CONFIG_DATA); }
		public function getPaymentStatus() 		{ return ($this->PAYPAL_PAYMENT_STATUS); }
		public function isError() 				{ return ((bool) count($this->arrErrors)); }
		public function isSuccess() 			{ return ((bool) $this->PAYPAL_IS_SUCCESS); }
		public function getErrors() 			{ return ($this->arrErrors); }
		
		# -----------------------------------
		# protected methods [getters]
		# -----------------------------------
		protected  function getConfig() { 
			return((intval($this->PAYPAL_PROCESSING_ENV) ? $this->PAYPAL_PROCESSING_ENV : PAYPAL_HTTPS_LIVE_GATEWAY)); 
		}
		protected  function getTxToken() 		{ return ((isset($_GET['tx']) && (strlen($_GET['tx'])) ? $_GET['tx'] : "")); }
		protected  function getAuthToken() 		{ return ($this->PAYPAL_AUTH_TOKEN); }
		protected  function getConnectUrl() 	{ return ($this->PAYPAL_CONNECT_URL); }
		protected  function getReturnUrl() 		{ return ($this->PAYPAL_RETURN_URL); }
		protected  function getCancelUrl() 		{ return ($this->PAYPAL_CANCEL_URL); }
		protected  function getIpnNotifyUrl() 	{ return ($this->PAYPAL_IPN_NOTIFY_URL); }
		protected  function getAccountEmail() 	{ return ($this->PAYPAL_ACCOUNT_EMAIL); }
		protected  function getHeaderImage() 	{ return ($this->PAYPAL_HEADER_IMAGE); }
		protected function getPaymentAmount() 	{ return($this->PAYMENT_AMOUNT); }
		protected function getShipping1Amount() { return($this->SHIPPING); }
		protected function getShipping2Amount() { return($this->SHIPPING2); }
		protected function getHandlingAmount() 	{ return($this->HANDLING); }
		protected function getNoShipping() 		{ return($this->NO_SHIPPING); }
		protected function getReceiverEmail() 	{ return($this->RECEIVER_EMAIL); }
		protected function getItemName() 		{ return($this->ITEM_NAME); }
		protected function getItemNumber() 		{ return($this->ITEM_NUMBER); }
		protected function getQty() 			{ return($this->ITEM_QTY); }
		protected function getNoNote() 			{ return($this->NO_NOTE); }
		protected function getCurrencyCode() 	{ return($this->CURRENCY_CODE); }
		
		# -----------------------------------
		# protected / Public methods [setters]
		# -----------------------------------
		protected  function setProcessingEnvironment($intEnvironmentType=PAYPAL_HTTPS_LIVE_GATEWAY) {
			$this->PAYPAL_PROCESSING_ENV = (intval($intEnvironmentType) ? intval($intEnvironmentType) : PAYPAL_HTTPS_LIVE_GATEWAY);
		}
		
		protected  function setAuthToken($strAuthToken) 	{ $this->PAYPAL_AUTH_TOKEN 	= $strAuthToken; }
		protected  function setConnectUrl($strConnectUrl) 	{ $this->PAYPAL_CONNECT_URL = $strConnectUrl; }
		protected  function setReturnUrl($strReturnUrl) 	{ $this->PAYPAL_RETURN_URL 	= $strReturnUrl; }
		protected  function setCancelUrl($strCancelUrl) 	{ $this->PAYPAL_CANCEL_URL 	= $strCancelUrl; }
		protected  function setIpnNotifyUrl($strIpnUrl) 	{ $this->PAYPAL_IPN_NOTIFY_URL 	= $strIpnUrl; }
		protected  function setAccountEmail($strAccEmail) 	{ $this->PAYPAL_ACCOUNT_EMAIL 	= $strAccEmail; }
		protected  function setHeaderImage($strImage) 		{ $this->PAYPAL_HEADER_IMAGE 	= $strImage; }
		protected  function setPaymentStatus($strStatus) 	{ $this->PAYPAL_PAYMENT_STATUS = $strStatus; }
		protected  function setSuccess($blSuccess) 			{ $this->PAYPAL_IS_SUCCESS = (bool) $blSuccess; }
		protected  function setConfigData($arrData) 		{ $this->PAYPAL_CONFIG_DATA = $arrData; }
		protected  function setError($strError) 			{ $this->arrErrors[] = $strError; }
		protected  function setIpnInfo($arrIpnInfo) 		{ $this->arrIpnResults = $arrIpnInfo; }
		public function setPaymentAmount($fltAmount) 		{ $this->PAYMENT_AMOUNT = $fltAmount; }
		public function setShipping1Amount($fltAmount) 		{ $this->SHIPPING = $fltAmount; }
		public function setShipping2Amount($fltAmount) 		{ $this->SHIPPING2 = $fltAmount; }
		public function setHandlingAmount($fltAmount) 		{ $this->HANDLING = $fltAmount; }
		public function setNoShipping($blVal) 				{ $this->NO_SHIPPING = $blVal; }
		public function setReceiverEmail($strEmail) 		{ $this->RECEIVER_EMAIL = filter_var($strEmail, FILTER_VALIDATE_EMAIL); }
		public function setItemName($strItemName) 			{ $this->ITEM_NAME = $strItemName; }
		public function setItemNumber($strItemNumber)		{ $this->ITEM_NUMBER = $strItemNumber; }
		public function setItemQty($intQty)					{ $this->ITEM_QTY = $intQty; }
		public function setNoNote($intNoNote)				{ $this->NO_NOTE = $intNoNote; }
		public function setCurrencyCode($strCurrency) 		{ $this->CURRENCY_CODE = strtoupper($strCurrency); }
		
		// @return: void
		protected  function setPayPalConfig() {
			$arrPayPalConfig = array();
			$arrPayPalConfig = &$this->arrPaypalVariables['LIVE'];
			switch ((int) $this->getConfig()) {
				case PAYPAL_HTTPS_LIVE_GATEWAY : {
					$arrPayPalConfig = &$this->arrPaypalVariables['LIVE'];
					break;
				}
				case PAYPAL_HTTPS_SANDBOX_GATEWAY : {
					$arrPayPalConfig = &$this->arrPaypalVariables['SANBOX'];
					break;
				}
			}
			
			// Set the data ...
			$this->setConfigData($arrPayPalConfig);
			$this->setConnectUrl($arrPayPalConfig['connect_url']); 		// PayPal's Live/SandBox URL	
			$this->setAuthToken($arrPayPalConfig['auth_token']);		// The PayPal auth token	
			$this->setReturnUrl($arrPayPalConfig['return_url']);		// Return URL on Success
			$this->setCancelUrl($arrPayPalConfig['cancel_url']);		// Return URL on Cancel
			$this->setIpnNotifyUrl($arrPayPalConfig['notify_url']);		// IPN Notification URL.
			$this->setAccountEmail($arrPayPalConfig['account_email']);	// Business account holder email
			$this->setHeaderImage($arrPayPalConfig['header_image']);	// Header image URL	
		}
	}