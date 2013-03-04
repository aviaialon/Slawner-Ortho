<?php
/**
 * PAYPAL_IPN Administration Class
 * This class represents the CRUD [Hybernate] behaviors implemented 
 * with the Hybernate framework 
 *
 * @package		CLASSES::PAYMENT::PAYPAL_API
 * @subpackage	OBJECT_BASE
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::CRYPT::AES_CRYPTO");

class PAYPAL_IPN extends OBJECT_BASE 
{
   /**
	*  Class instance configuration type sandbox for testing
	*  
	*  @var String
	*/
	const PAYPAL_IPN_CONFIG_TYPE_SANDBOX = 'Sandbox';	
	
   /**
	*  Class instance configuration type production
	*  
	*  @var String
	*/
	const PAYPAL_IPN_CONFIG_TYPE_PRODUCTION = 'Production_Live';	
	
   /**
	* Array Container for errors
	*
	* @var 		Array
	* @access 	Protected
	*/
	public static $_PAYPAL_API_ERROR_BIN = array();
	
   /**
	* Defined the current transaction environent
	* Defaults to 'Production' see PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION
	*
	* @var 		Array
	* @access 	Protected
	*/
	protected static $_PAYPAL_API_ENVIRONMENT = PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION;
	
   /**
	* Array Container for PayPal API request parameters
	*
	* @var 		Array
	* @access 	Protected
	*/
	protected static $_PAYPAL_API_REQUEST_PARAMS = array();
	
   /**
	* Array Container for PayPal API Response
	*
	* @var 		Array
	* @access 	Protected
	*/
	protected static $_PAYPAL_API_RESPONSE = array();
	
   /**
	* AES Crypto tool for data transfer within object
	*
	* @var CRYPT::AES_CRYPTO
	* @access Protected
	*/
	protected static $_PAYPAL_AES_CRYPT 	= NULL;
	
	
  /**
	* Array Container which denotes required transaction form fieldsfor PayPal API transaction.
	*
	* @var 		Array
	* @access 	Protected
	*/
	protected static $_PAYPAL_API_REQUIRED_FIELDS = array
	(
		'paypal_post_url'	=>	'Please define the PayPal post URL',
		'business'			=>	'Please add the PayPal seller\'s account email',
		'image_url'			=>	'Please add the business logo URL',
		'return'			=>	'Please set a return URL', 
		'cancel_return'		=>	'Please set a cancel URL',
		'notify_url'		=>	'Please set a IPN notification URL', 
		'cmd'				=>	'Please define a PayPal transaction command type [_xclick | _cart]',
		'currency_code'		=>	'Please define the currency code',
		'invoice'			=> 	'Internal Pre-TransactionId Not set.',
		'quantity'			=>	'Item Quantity Not Set.',
		'item_name'			=>	'Item name not Set',
		'amount'			=>	'Transaction amount not set'
	);
	
	
	/**
	* Array Container for PayPal API transaction config presets / per 
	* Paypal API transaction config presets. all configs are overridable
	* by using setVariable()
	* 
	*	Here, we create a few configuration presets
	* 		-- Production 	: see PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION (Default)
	* 		-- Sandbox		: see PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_SANDBOX 
	* 	There is no limits to the preset congurations avvailable,
	*	Simply add it to the array (ex, staging, integration presets etc...)
	*
	* @var 		Array
	* @access 	Protected
	*/
	protected static $_PAYPAL_IPN_CONFIGURATION_PRESETS_GROUP = array
	(
			PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION	=>	array
			(
				'paypal_post_url'	=>	'https://www.paypal.com/cgi-bin/webscr',
				'business'			=>	'dangerzone514@yahoo.com',
				'image_url'			=>	'https://secure.hostmonster.com/~regentva/index_files/images/paypal-header.jpg',
				'return'			=>	'', // Will be set when loaded
				'cancel_return'		=>	'', // Will be set when loaded
				'notify_url'		=>	'', // Will be set when loaded
				'cmd'				=>	'_xclick',
				'currency_code'		=>	'USD',
				'quantity'			=>	1,
				'cs'				=> 	1,
				'no_shipping'		=> 	1,
				'no_note'			=> 	0,
				'shipping'			=> 	0,
				'shipping2'			=>	0,	
				'handling'			=>	0,
				'tax'				=>	0,
				'cn'				=> 	'',
				'custom'			=> 	'',
				'invoice'			=> 	''
			),
			
			PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_SANDBOX	=>	array
			(
				'paypal_post_url'	=>	'https://www.sandbox.paypal.com/cgi-bin/webscr',
				'business'			=>	'danger_1332857874_biz@yahoo.com',
				'image_url'			=>	'https://secure.hostmonster.com/~regentva/index_files/images/paypal-header.jpg',
				'return'			=>	'', // Will be set when loaded
				'cancel_return'		=>	'', // Will be set when loaded
				'notify_url'		=>	'', // Will be set when loaded
				'cmd'				=>	'_xclick',
				'currency_code'		=>	'USD',
				'quantity'			=>	1,
				'cs'				=> 	1,
				'no_shipping'		=> 	1,
				'no_note'			=> 	0,
				'shipping'			=> 	0,
				'shipping2'			=>	0,	
				'handling'			=>	0,
				'tax'				=>	0,
				'cn'				=> 	'',
				'custom'			=> 	'',
				'invoice'			=> 	''
			)
	);

	/**
	 * Returns the paypal errors
	 * 
	 * @access 	public
	 * @param	none
	 * @return  array
	 */
	public final function getErrors()
	{
		return (self::$_PAYPAL_API_ERROR_BIN);
	}

	
   /**
    * Main Execution Method
	* This method loads a configuration preset
	* 
	* @example 	of available configuration values
	* 			Parameters decriptions can be found here
	* 			https://www.paypal.com/cgi-bin/webscr?cmd=p/pdn/howto_checkout-outside
	* 
	* <form method="post" name="paypal_form" action="https://www.sandbox.paypal.com/cgi-bin/webscr">
		<input type="submit" />
	    <input type="hidden" name="business" value="danger_1332857874_biz@yahoo.com" />
	    <input type="hidden" name="cmd" value="_xclick" />
		
	    <!-- the next three need to be created -->
	    <input type="hidden" name="image_url" value="https://secure.hostmonster.com/~regentva/index_files/images/paypal-header.jpg" />
	    <input type="hidden" name="return" value="http://momtrade.dns05.com/core/payment/paypal_api/ipn/success.php" />
	    <input type="hidden" name="cancel_return" value="http://momtrade.dns05.com/core/payment/paypal_api/ipn/cancelled.php" />
	    <input type="hidden" name="notify_url" value="http://momtrade.dns05.com/core/payment/paypal_api/ipn/ipn.php" />
	    <input type="hidden" name="rm" value="2" />
	    <input type="hidden" name="currency_code" value="USD" />
	    <input type="hidden" name="lc" value="US" />
	    <input type="hidden" name="bn" value="toolkit-php" />
	    <input type="hidden" name="cbt" value="Continue" />
	    
	    <!-- Payment Page Information -->
	    <input type="hidden" name="no_shipping" value="" />
	    <input type="hidden" name="no_note" value="1" />
	    <input type="hidden" name="cn" value="Comments" />
	    <input type="hidden" name="cs" value="" />
	    
	    <!-- Product Information -->
	    <!-- 
	    	Multiple items can be passed here in the form item_name_1, item_name_2 etc.. for all the params 
	    	For each of the following item-specific parameters, define a new set of values that correspond to 
	    	each item that was purchased via your 3rd party cart. Append "_x" to the variable name, where x is 
	    	the item number, starting with 1 and increasing by one for each item that is added.	
	    	
	    		item_name_x		(Required for item #x) Name of item #x in the cart. Must be alpha-numeric, with a 127 character limit
				item_number_x	Optional pass-through variable associated with item #x in the cart. Must be alpha-numeric, with a 127 character limit
				amount_x		(Required for item #x) Price of the item #x
				shipping_x		The cost of shipping the first piece (quantity of 1) of item #x
				shipping2_x		The cost of shipping each additional piece (quantity of 2 or above) of item #x
				handling_x		The cost of handling for item #x
				on0_x			First option field name for item #x. 64 character limit
				os0_x			First set of option value(s) for item #x. 200 character limit. "on0_x" must be defined in order for "os0_x" to be recognized.
				on1_x			Second option field name for item #x. 64 character limit
				os1_x			Second set of option value(s) for item #x. 200 character limit. "on1_x" must be defined in order for "os1_x" to be recognized.
	    -->
	    <input type="hidden" name="item_name" 	value="Your order at domain.com" />
	    <input type="hidden" name="item_number" value="<?php echo mt_rand() * 100000; ?>" />
	    <input type="hidden" name="amount" 		value="98.75" />
	    <input type="hidden" name="quantity" 	value="1" />
	    <input type="hidden" name="on0" value="Order ID" />
	    <input type="hidden" name="os0" value="12345-345" />
	    <input type="hidden" name="on1" value="Test Data" />
	    <input type="hidden" name="os1" value="This is the tets data value" />
	    
	    <!-- Shipping and Misc Information -->
	    <!-- Multiple items can be passed here in the form shipping_1, shipping_2 etc.. for all the params -->
	    <input type="hidden" name="shipping" value="10" />
	    <input type="hidden" name="shipping2" value="" />
	    <input type="hidden" name="handling" value="" />
	    
	    
	    
	    <input type="hidden" name="tax" value="" />
	    <input type="hidden" name="custom" value="" />
	    <input type="hidden" name="invoice" value="" />
	    
	    <!-- Customer Information -->
	    <input type="hidden" name="first_name" value="Mr. X" />
	    <input type="hidden" name="last_name" value="" />
	    <input type="hidden" name="address1" value="Street no. 1" />
	    <input type="hidden" name="address2" value="" />
	    <input type="hidden" name="city" value="MyTown" />
	    <input type="hidden" name="state" value="" />
	    <input type="hidden" name="zip" value="10004" />
	    <input type="hidden" name="email" value="ship@to-me.com" />
	    <input type="hidden" name="night_phone_a" value="" />
	    <input type="hidden" name="night_phone_b" value="" />
	    <input type="hidden" name="night_phone_c" value="" />
	* </form>
	* 	
	* 							AVAILABLE FORM PARAMETERS
	* 	_________________________________________________________________________________
	* 
	 	business			Email address on your PayPal account
	 	
		quantity			Number of items. This will multiply the amount if greater than one
		
		item_name			Name of the item (or a name for the shopping cart). Must be alpha-numeric, with a 127character limit
		
		item_number			Optional pass-through variable for you to track payments. Must be alpha-numeric, with a 127 character limit
		
		amount				Price of the item (the total price of all items in the shopping cart)
		
		shipping			The cost of shipping the item
		
		shipping2			The cost of shipping each additional item
		
		handling			The cost of handling
		
		tax					Transaction-based tax value. If present, the value passed here will override any profile 
							tax settings you may have (regardless of the buyer's location).
							
		no_shipping			Shipping address. If set to "1," your customer will not be asked for a shipping address. 
							This is optional; if omitted or set to "0," your customer will be prompted to include a shipping address
							
		cn					Optional label that will appear above the note field (maximum 40 characters)
		no_note				Including a note with payment. If set to "1," your customer will not be prompted to include a note. 
							This is optional; if omitted or set to "0," your customer will be prompted to include a note.
							
		on0					First option field name. 64 character limit
		
		os0					First set of option value(s). 200 character limit. "on0" must be defined for "os0" to be recognized.
		
		on1					Second option field name. 64 character limit
		
		os1					Second set of option value(s). 200 character limit. "on1" must be defined for "os1" to be recognized.
		
		custom				Optional pass-through variable that will never be presented to your customer. Can be used to track inventory
		
		invoice				Optional pass-through variable that will never be presented to your customer. Can be used to track invoice numbers
		
		notify_url			Only used with IPN. An internet URL where IPN form posts will be sent
		
		return				An internet URL where your customer will be returned after completing payment
		
		cancel_return		An internet URL where your customer will be returned after cancelling payment
		
		image_url			The internet URL of the 150 X 50 pixel image you would like to use as your logo
		
		cs					Sets the background color of your payment pages. If set to "1," the background color will be black. 
							This is optional; if omitted or set to "0," the background color will be white
		
		-----------------------------------------------------------
		- Extended Variables:	if cmd is set to _xclick -	(default)
		-----------------------------------------------------------
								
		email				Customer's email address
		
		first_name			Customer's first name. Must be alpha-numeric, with a 32 character limit
		
		last_name			Customer's last name. Must be alpha-numeric, with a 64 character limit
		
		address1			First line of customer's address. Must be alpha-numeric, with a 100 character limit
		
		address2			Second line of customer's address. Must be alpha-numeric, with a 100 character limit
		
		city				City of customer's address. Must be alpha-numeric, with a 100 character limit
		
		state				State of customer's address. Must be official 2 letter abbreviation
		
		zip					Zip code of customer's address
		
		night_phone_a		Area code of customer's night telephone number
		
		night_phone_b		First three digits of customer's night telephone number
		
		day_phone_a			Area code of customer's daytime telephone number
		
		day_phone_b			First three digits of customer's daytime telephone number		
		
		-----------------------------------------------------------			
	 	- IF mcd is set to cart - 	
	 	-----------------------------------------------------------
	 	item_name_x		(Required for item #x) Name of item #x in the cart. Must be alpha-numeric, with a 127 character limit
		item_number_x	Optional pass-through variable associated with item #x in the cart. Must be alpha-numeric, with a 127 character limit
		amount_x		(Required for item #x) Price of the item #x
		shipping_x		The cost of shipping the first piece (quantity of 1) of item #x
		shipping2_x		The cost of shipping each additional piece (quantity of 2 or above) of item #x
		handling_x		The cost of handling for item #x
		on0_x			First option field name for item #x. 64 character limit
		os0_x			First set of option value(s) for item #x. 200 character limit. "on0_x" must be defined in order for "os0_x" to be recognized.
		on1_x			Second option field name for item #x. 64 character limit
		os1_x			Second set of option value(s) for item #x. 200 character limit. "on1_x" must be defined in order for "os1_x" to be recognized.

	* 
	* @access 	public, final
	* @param 	String  $strConfigPresetGroup : The configuration preset group, defaults to Production
	* @return 	boolean
	*/
	public final function executeTransaction()
	{
		// 
		// Validate form fields
		//
		$arrFormData 		= $this->getVariable();
		$arrRequiredFields 	= self::$_PAYPAL_API_REQUIRED_FIELDS;
		array_walk($arrRequiredFields, function($strErrorMessage, $strRequiredKey) use($arrRequiredFields, $arrFormData) {
				if (false === isset($arrFormData[$strRequiredKey]))
				{
					PAYPAL_IPN::$_PAYPAL_API_ERROR_BIN[] = $strErrorMessage;
				}
		});

		if (! count($this->getErrors()))
		{
			// Build the payal request form
			$strPayPalForm  = '<h2>Processing Transaction...</h2>';
			$strPayPalForm .= '<p><strong>Processing... please do not close this window.</strong></p>';
			$strPayPalForm .= '<form method="post" id="paypal_form" name="paypal_form" action="' . $this->getPaypal_post_url() . '" dir="ltr" lang="en-US">';
			reset($arrFormData);
			while (list($strFormKey, $strFormData) = each($arrFormData))
			{
				$strPayPalForm .= '<input type="hidden" name="' . $strFormKey . '" value="' . $strFormData . '" />' . "\n"; 
			}
			
			$strPayPalForm .= '</form>';
			$strPayPalForm .= '<noscript>';
			$strPayPalForm .= 	'<p>Your browser doesn\'t support Javscript, click the button below to process the transaction.</p>';
			$strPayPalForm .= 	'<input type="submit" name="Submit" value="Process Payment" />';
			$strPayPalForm .= '</noscript>';
			$strPayPalForm .= '<script type="text/javascript" charset="UTF-8">';
			$strPayPalForm .= '		window.onload = function() { document.getElementById("paypal_form").submit(); } ';
			$strPayPalForm .= '</script>';
			echo($strPayPalForm);
		}
		return (FALSE === ((bool) count($this->getErrors())));
	}

	
	/**
	 * This is the main method that executes the REST call to the PayPal server API
	 * 
	 * @access	public, final
	 * @param	string 	$strConfigPresetGroup  : The environment, defaults to PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION
	 * @return  array
	 */
	public final function setEnvironment($strConfigPresetGroup = PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION)
	{
		self::$_PAYPAL_API_ENVIRONMENT = $strConfigPresetGroup;
		
		// Set the preset data
		$Application 			= APPLICATION::getInstance();
		$objRequestDispatcher	= $Application->getRequestDispatcher();
		
		// Create the default Callback Url's
		$strSuccessUrl 			= $objRequestDispatcher->createCallbackUrl($this, 'onIpnSuccess', 	array()); 
		$strCancelUrl			= $objRequestDispatcher->createCallbackUrl($this, 'onIpnCancel', 	array());
		$strIpnNotificationUrl	= $objRequestDispatcher->createCallbackUrl($this, 'onIpnNotify', 	array());
		
		// Set the variables
		$this->setVariableArray($this->getConfigurationPreset());
		$this->setReturn($strSuccessUrl);
		$this->setCancel_Return($strCancelUrl);
		$this->setNotify_Url($strIpnNotificationUrl);
	}
	
    /**
	 * Returns the current execution environment
	 * 
	 * @access	public, final
	 * @param	none
	 * @return  array
	 */
	public final function getEnvironment()
	{
		return (self::$_PAYPAL_API_ENVIRONMENT);
	}
	
   /**
    * Class Callback method: Called when a successful sale is finished
	* 
	* @access 	public, static, final
	* @param 	Array  $arrDispatchRequest : The request dispatcher request data
	* @return 	void
	*/
	public static final function onIpnSuccess($arrDispatchRequest = array())
	{
		self::onIpnNotify();
	}
	
  /**
    * Class Callback method: Called when a ipn sale is canceled
	* 
	* @access 	public, static, final
	* @param 	Array  $arrDispatchRequest : The request dispatcher request data
	* @return 	void
	*/
	public static final function onIpnCancel($arrDispatchRequest = array())
	{
		new dump($_GET);
		new dump($_POST);
	}
	
   /**
    * Class Callback method: Called by paypal to declare an IPN notification sale
	* 
	* @access 	public, static, final
	* @param 	Array  $arrDispatchRequest : The request dispatcher request data
	* @return 	void
	*/
	public static final function onIpnNotify($arrDispatchRequest = array())
	{
		// $url = 'https://www.paypal.com/cgi-bin/webscr';
		$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		
		$postdata = '';
		foreach($_POST as $i => $v) {
			$postdata .= $i.'='.urlencode($v).'&';
		}
		$postdata .= 'cmd=_notify-validate';
		
		$web = parse_url($url);
		if ($web['scheme'] == 'https') { 
			$web['port'] = 443;  
			$ssl = 'ssl://'; 
		} else { 
			$web['port'] = 80;
			$ssl = ''; 
		}
		$fp = @fsockopen($ssl.$web['host'], $web['port'], $errnum, $errstr, 30);
		
		if (!$fp) { 
			echo $errnum.': '.$errstr;
		} else {
			fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$web['host']."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $postdata . "\r\n\r\n");
		
			while(!feof($fp)) { 
				$info[] = @fgets($fp, 1024); 
			}
			fclose($fp);
			
			/*
			 * Log the response here...
			 */
			$myFile = "./log.html";
			$fh = fopen($myFile, 'a'); //or die("can't open file");
			
			new dump($info);
			new dump($_POST);
			$stringData = '<h1>' . date('Y-m-d H:i:s') . '</h1><br />';
			$stringData .= ob_get_clean();
			fwrite($fh, $stringData . '<hr />');
			fclose($fh);
			
			$strInfo = implode(',', $info);
			
			$o = MAIL_QUEUE::getInstance();
			$o->setMessage($stringData);
			$o->queueMail();
			
			if (eregi('VERIFIED', $strInfo)) { 
				// yes valid, f.e. change payment status  
				//echo 'Verified.';
			} else {
				// invalid, log error or something
				//echo 'Ooops.';
				//new dump($info);
			}
		}
	}
	
	/**
	 * Returns the current configuration preset
	 * 
	 * @access	public, final
	 * @param	none
	 * @return  array
	 */
	public final function getConfigurationPreset()
	{
		return ((array) PAYPAL_IPN::$_PAYPAL_IPN_CONFIGURATION_PRESETS_GROUP[$this->getEnvironment()]);
	}
	
	
   /**
    * Overload Method
	* After Class constructor
	* 
	* @access 	public, static, final
	* @param 	String  $strInstanceType : The instance configuration type, defaults to Production
	* @return 	PAYPAL_IPN
	*/
	public static final function getInstance($strProductionMethod = PAYPAL_IPN::PAYPAL_IPN_CONFIG_TYPE_PRODUCTION)
	{
		if (FALSE === is_object(parent::$_OBJECT_INSTANCE))
		{
			$objPayPalIpnInstance 	= new PAYPAL_IPN();
			$objPayPalIpnInstance->setEnvironment($strProductionMethod);
			
			parent::$_OBJECT_INSTANCE = &$objPayPalIpnInstance;
		}
		
		return (parent::$_OBJECT_INSTANCE);
	}
}