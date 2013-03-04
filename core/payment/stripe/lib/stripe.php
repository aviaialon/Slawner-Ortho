<?php
/**
 * STRIPE Administration Class
 * This class represents the CRUD [Hybernate] behaviors implemented 
 * with the Hybernate framework 
 *
 * @package		CLASSES::PAYMENT::STRIPE
 * @subpackage	none
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");	
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAYMENT::PROCESSOR::STRIPE::STRIPE_REMOTE_CUSTOMER");

/**
 * Required Parameters
 * 
 * @param	CreditCardNumber	-	The Credit Card Number
 * @param	CreditCardExpMonth	-	The Credit Card Expiry Month
 * @param	CreditCardExpYear	-	The Credit Card Expiry Year
 * @param	CreditChargeAmount	-	The Credit Charge Amount
 */


/*
 * Example Usage:
 * --------------------------------------------------------------

	STANDARD CREDIT CARD CHARGE USAGE:
	----------------------------------
	
	/**
	 * Required Parameters
	 * 
	 * @param	CreditCardNumber	-	The Credit Card Number
	 * @param	CreditCardExpMonth	-	The Credit Card Expiry Month
	 * @param	CreditCardExpYear	-	The Credit Card Expiry Year
	 * @param	CreditChargeAmount	-	The Credit Charge Amount
	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PAYMENT::STRIPE::LIB::STRIPE");
	if (FALSE === empty ($_POST)) {
		$objPaymentProcessor = STRIPE_PROCESSOR::getInstance();
		$objPaymentProcessor->setVariable(
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_PROCESSING_MODE, 
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_LIVE
		);
		$objPaymentProcessor->setCreditCardNumber($_POST['card_number']);
		$objPaymentProcessor->setCreditCardExpYear($_POST['expiry_year']);
		$objPaymentProcessor->setCreditCardExpMonth($_POST['expiry_month']);
		$objPaymentProcessor->setCreditChargeAmount($_POST['amount']);
		$objPaymentProcessor->setCreditCardCVVNumber($_POST['card_cvv']);
		$objPaymentProcessor->processPayment();
		
		new dump($objPaymentProcessor->getTransaction());
		new dump($objPaymentProcessor->getErrors());
		print "Was the transaction successfull? - " . ((bool) $objPaymentProcessor->getIsTransactionSuccess());
	}	
	
	STRIPE EXTERNAL CLIENT CREDIT CARD CHARGE USAGE:
	------------------------------------------------
	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PAYMENT::STRIPE::LIB::STRIPE");
	$objPaymentProcessor = STRIPE_PROCESSOR::getInstance();
	$objPaymentProcessor->setVariable(
		STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_PROCESSING_MODE, 
		STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_LIVE
	);
	$objPaymentProcessor->setCreditChargeAmount(10.00);
	$objPaymentProcessor->getStripeExternalClientId('cus_0QBzPu0Y4ZkTp1');
	$objPaymentProcessor->processStripeExternalClientPayment();
	
	new dump($objPaymentProcessor->getTransaction());
	new dump($objPaymentProcessor->getErrors());
 
 * --------------------------------------------------------------	
 */

class STRIPE_PROCESSOR extends OBJECT_BASE 
{
	
	/**
	 * Class Config Constants
	 * 
	 * @var String
	 */
	const STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_LIVE 		= 'Live';
	const STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_SANDBOX 	= 'Test';
	const STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_TEST 		= 'Test';
	const STRIPE_PROCESSOR_CONFIG_CURRENCY_USD 				= 'usd';
	const STRIPE_PROCESSOR_CONFIG_CURRENCY_CA 				= 'ca';
	
	/**
	 * Class Param Constants
	 * 
	 * @var String
	 */
	const STRIPE_PROCESSOR_PARAM_PROCESSING_MODE 			= 'ProcessingMode';
	const STRIPE_PROCESSOR_PARAM_CURRENCY					= 'Currency';
	
	/**
	 * Overload method called after getInstance(). Used to set defaults.
	 * 
	 * @access	public, static, final
	 * @param	none
	 * @return  void
	 */
	public final function onGetInstance()
	{
		// Processing Mode
		$_objSelfInstance = self::$_OBJECT_INSTANCE;
		$_objSelfInstance->setVariable(
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_PROCESSING_MODE, 
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_LIVE
		);
		
		// Currency
		$_objSelfInstance->setVariable(
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_CURRENCY, 
			STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_CURRENCY_USD
		);
		
		$_objSelfInstance->setVariable('Errors', array());
	}
	
	/**
	 * This method sets up the processing environment
	 * 
	 * @access	protected, static, final
	 * @param	none
	 * @return  Boolean
	 */
	protected final function initialiseEnvironment()
	{
		$blnReturn = true;
		switch ($this->getVariable(STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_PROCESSING_MODE)) 
		{
			case (STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_LIVE) : 
			{
				Stripe::setApiKey(trim(constant('__STRIPE_API_LIVE_SECRET_KEY__')));
				break;
			}	
			case (STRIPE_PROCESSOR::STRIPE_PROCESSOR_CONFIG_PROCESSING_MODE_SANDBOX) : 
			{
				Stripe::setApiKey(trim(constant('__STRIPE_API_TEST_SECRET_KEY__')));
				break;
			}	
			default : {
				$this->addToErrors('Processing Environment is not defined or is invalid.');
				$blnReturn = false;
				break;
			}
		}
		
		return ((bool) $blnReturn);
	}
	
	
	/**
	 * This method process the credit card...
	 * 
	 * @access	public, static, final
	 * @param	none
	 * @return  Boolean | Array (Stripe processing response.)
	 */
	public final function processPayment()
	{
		$blnReturn = $this->initialiseEnvironment();
		if (TRUE === $blnReturn)
		{
			// Begin Validation		
			$blnReturn &= ((bool) $this->getCreditCardNumber()) 		|| ($this->addToErrors('Please provide a Credit Card Number') & false);
			$blnReturn &= ((bool) $this->getCreditCardExpMonth()) 		|| ($this->addToErrors('Please provide a Credit Card Expiry Month') & false);
			$blnReturn &= ((bool) $this->getCreditCardExpYear()) 		|| ($this->addToErrors('Please provide a Credit Card Expiry Year') & false);
			$blnReturn &= ((bool) $this->getCreditChargeAmount()) 		|| ($this->addToErrors('Please provide a Charge Amount') & false);
			$blnReturn &= ((bool) $this->getCreditCardCVVNumber()) 		|| ($this->addToErrors('Please provide a Credit Card CVV Number') & false);
			$blnReturn &= (((int) $this->getCreditChargeAmount() > 0)) 	|| ($this->addToErrors('Charge Amount Must Be a Valid Amount') & false);
			
			// Phase 2 validation..
			if (true === ((bool) $blnReturn)) 
			{
				$blnReturn &= (
					((bool) VALIDATOR::creditCard($this->getCreditCardNumber())) || 
					($this->addToErrors('Please Provide a Valid Credit Card Number') & false)
				);
				
				$blnReturn &= ((FALSE === ((bool) preg_match('/[^0-9]/', $this->getCreditCardExpMonth()))) 	|| ($this->addToErrors('Please provide a Valid Credit Card Expiry Month') & false));
				$blnReturn &= ((FALSE === ((bool) preg_match('/[^0-9]/', $this->getCreditCardExpYear()))) || ($this->addToErrors('Please provide a Valid Credit Card Expiry Year') & false));
				$blnReturn &= ((FALSE === ((bool) preg_match('/[^0-9]/', $this->getCreditCardCVVNumber()))) || ($this->addToErrors('Please provide a Valid Credit Card CVV Number') & false));
				$blnReturn &= ((TRUE  === ((int) $this->getCreditCardExpMonth() <= 12)) || ($this->addToErrors('Please provide a Valid Credit Card Expiry Month') & false));
			}
			
			// Phase 3 validation
			if (true === ((bool) $blnReturn)) 
			{
				$blnReturn &= (($this->getCreditCardExpYear() >= date('Y'))  || ($this->addToErrors('This Credit Card has Expired. (Year)') & false));
				$blnReturn &= (
					(TRUE  === (mktime(0, 0, 0, $this->getCreditCardExpMonth(), 32, $this->getCreditCardExpYear()) > strtotime(date('Y-m')))) || 
					($this->addToErrors('This Credit Card has Expired.') & false)
				);
			}
			
			if (true === ((bool) $blnReturn))
			{
				// Begin Processing...
				try 
				{
					$this->setStripeServerResponse(Stripe_Charge::create(array(
						'card' 			=> 	array (
							'number' 	=> 	$this->getCreditCardNumber(),
							'exp_month'	=>	$this->getCreditCardExpMonth(),
							'exp_year'	=>	$this->getCreditCardExpYear()
						), 
						'currency' 		=> 	$this->getVariable(STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_CURRENCY),
						'amount' 		=> 	($this->getCreditChargeAmount() * 100) /* Amount is required in cents..*/, 
					))->__toArray());
					
					
					// Serialize the response to array
					$blnReturn = $this->parseStripeServerPaymentResponse();
				}
				/**
				 * Try to find a Stripe error here
				 * @var Exception
				 */
				catch (Exception $objException)
				{
					$blnReturn = false;
					$this->addToErrors($objException->getMessage());
				}
			}
		}
		
		return ($blnReturn);
	}

	/**
	 * This method process the credit card for a stripe external client...
	 * 
	 * @access	public, final
	 * @param	none
	 * @return  Boolean | Array (Stripe processing response.)
	 */
	public final function processStripeExternalClientPayment()
	{
		$blnReturn = $this->initialiseEnvironment();
		if (TRUE === $blnReturn)
		{
			// Begin Validation		
			$blnReturn &= ((bool) $this->getCreditChargeAmount()) 		|| ($this->addToErrors('Please provide a Charge Amount') & false);
			$blnReturn &= ((bool) $this->getStripeExternalClientId()) 	|| ($this->addToErrors('Please provide a Stripe External Client Id') & false);
			$blnReturn &= (((int) $this->getCreditChargeAmount() > 0)) 	|| ($this->addToErrors('Charge Amount Must Be a Valid Amount') & false);
		}
		
		if (TRUE === ((bool) $blnReturn))
		{
			try {
				// charge the Customer instead of the card
				$this->setStripeServerResponse(Stripe_Charge::create(array (
					"amount" 	=> ($this->getCreditChargeAmount() * 100), /* Amount is required in cents..*/
					"currency" 	=> $this->getVariable(STRIPE_PROCESSOR::STRIPE_PROCESSOR_PARAM_CURRENCY),
					"customer" 	=> $this->getStripeExternalClientId()
				))->__toArray());
				
				$blnReturn = $this->parseStripeServerPaymentResponse();
			}
			/**
			 * Try to find a Stripe error here
			 * @var Exception
			 */
			catch (Exception $objException)
			{
				$blnReturn = false;
				$this->addToErrors($objException->getMessage());
			}
		}
		
		return ($blnReturn);
	}
	
	/**
	 * This method parses the Stripe payment request server response
	 * 
	 * @access	protected, final
	 * @param	none
	 * @return  Boolean | Array (Stripe processing response.)
	 */
	protected final function parseStripeServerPaymentResponse()
	{
		$arrStripeServerResponse = $this->getStripeServerResponse();
		
		if (false === empty($arrStripeServerResponse))
		{
			// Extract all the nested objects from the response.
			array_walk($arrStripeServerResponse, function($objResponseData, $strKey) use(&$arrStripeServerResponse) {
				$objResponseData = (true === is_array($objResponseData) ? array_shift($objResponseData) : $objResponseData);
				if (
					(true === (is_object($objResponseData))) &&
					(true === (method_exists($objResponseData, '__toArray')))
				) {
					$arrStripeServerResponse[$strKey] = call_user_func_array(array($objResponseData, "__toArray"), array());
				}
			});
			
			
			// Begin success message
			$arrStripeServerResponse['SUCCESS'] = (
				((bool) ((true === isset($arrStripeServerResponse['paid'])) && (true  === $arrStripeServerResponse['paid']))) &&
				((bool) ((true === isset($arrStripeServerResponse['refunded'])) && (false === $arrStripeServerResponse['refunded'])) || (false === isset($arrStripeServerResponse['refunded']))) &&
				((bool) ((true === isset($arrStripeServerResponse['amount_refunded'])) && (0 === ((int) $arrStripeServerResponse['amount_refunded']))) || (false === isset($arrStripeServerResponse['amount_refunded']))) &&
				((bool) ((true === isset($arrStripeServerResponse['disputed'])) && (false === $arrStripeServerResponse['disputed'])) || (false === isset($arrStripeServerResponse['disputed']))) 
			);
			
			// Here, we create a stripe external customer if the current
			// Payment process isnt already from  stripe external client
			if (false === ((bool) $this->getStripeExternalClientId()))
			{
				$objApplication = APPLICATION::getInstance();
				if (true === ((bool) $objApplication->getUser()->getId()))
				{
					$objStripeCustomer = STRIPE_REMOTE_CUSTOMER::getInstanceFromKey(array (
						'site_user_id'	=>	$objApplication->getUser()->getId()
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					if (false === ((bool) ($objStripeCustomer->getId() > 0))) 
					{
						try 
						{
							// 1. Create a stripe token.
							$objStripeToken = Stripe_Token::create(array (
								"card" 	=> array 
								(
									"number" 		=> $this->getCreditCardNumber(),
									"exp_month" 	=> $this->getCreditCardExpMonth(),
									"exp_year" 		=> $this->getCreditCardExpYear(),
									"cvc" 			=> $this->getCreditCardCVVNumber()
								)
							));
							if (
								(true === is_object($objStripeToken)) &&
								(true === isset($objStripeToken->id))
							) {
								$objStripeRemoteUser = Stripe_Customer::create(array (
									"card" 			=> $objStripeToken->id,
									"description" 	=> $objApplication->getUser()->getEmail()
								));
		
								if (true === is_object($objStripeRemoteUser)) 
								{
									$objStripeCustomer->setSite_user_Id($objApplication->getUser()->getId());
									$objStripeCustomer->setStripe_Customer_Id($objStripeRemoteUser->id);
									$objStripeCustomer->setCreation_Date(now());
									$objStripeCustomer->save();
								}
							}	
						}
						catch (Exception $objException)
						{
							// Continue silently on 'External Customer' creation fail.
							// TODO: Maybe log this error. 
						}
					}
					
					$arrStripeServerResponse['stripe_customer'] = $objStripeCustomer->getVariable();
				}
			}

			$this->setTransaction($arrStripeServerResponse);
			$this->setIsTransactionSuccess((true === isset($arrStripeServerResponse['SUCCESS'])) ? ((bool) $arrStripeServerResponse['SUCCESS']) : false);
		}
		
		return ($arrStripeServerResponse);
	}
}


// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Stripe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Stripe needs the JSON PHP extension.');
}


abstract class Stripe
{
  public static $apiKey;
  public static $apiBase = 'https://api.stripe.com/v1';
  public static $verifySslCerts = true;
  const VERSION = '1.7.7';

  public static function getApiKey()
  {
    return self::$apiKey;
  }

  public static function setApiKey($apiKey)
  {
    self::$apiKey = $apiKey;
  }

  public static function getVerifySslCerts() {
    return self::$verifySslCerts;
  }

  public static function setVerifySslCerts($verify) {
    self::$verifySslCerts = $verify;
  }
}


// Utilities
require(dirname(__FILE__) . '/Stripe/Util.php');
require(dirname(__FILE__) . '/Stripe/Util/Set.php');

// Errors
require(dirname(__FILE__) . '/Stripe/Error.php');
require(dirname(__FILE__) . '/Stripe/ApiError.php');
require(dirname(__FILE__) . '/Stripe/ApiConnectionError.php');
require(dirname(__FILE__) . '/Stripe/AuthenticationError.php');
require(dirname(__FILE__) . '/Stripe/CardError.php');
require(dirname(__FILE__) . '/Stripe/InvalidRequestError.php');

// Plumbing
require(dirname(__FILE__) . '/Stripe/Object.php');
require(dirname(__FILE__) . '/Stripe/ApiRequestor.php');
require(dirname(__FILE__) . '/Stripe/ApiResource.php');
require(dirname(__FILE__) . '/Stripe/SingletonApiResource.php');

// Stripe API Resources
require(dirname(__FILE__) . '/Stripe/Account.php');
require(dirname(__FILE__) . '/Stripe/Charge.php');
require(dirname(__FILE__) . '/Stripe/Customer.php');
require(dirname(__FILE__) . '/Stripe/Invoice.php');
require(dirname(__FILE__) . '/Stripe/InvoiceItem.php');
require(dirname(__FILE__) . '/Stripe/Plan.php');
require(dirname(__FILE__) . '/Stripe/Token.php');
require(dirname(__FILE__) . '/Stripe/Coupon.php');
require(dirname(__FILE__) . '/Stripe/Event.php');
require(dirname(__FILE__) . '/Stripe/Transfer.php');
