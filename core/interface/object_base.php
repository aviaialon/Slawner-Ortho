<?php
/**
 * OBJECT_BASE Interface Base Class
 * This class represents the interface base class for all objects
 * and implements some of the basic object features
 *
 * @package		{CORE}::INTERFACE
 * @subpackage	SITE_EXCEPTION
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
abstract class OBJECT_BASE
{
	/**
	 * This variable holds the class static instance
	 * 
	 * @var 	SELF
	 * @access 	protected, static
	 */
	protected static $_OBJECT_INSTANCE 	= FALSE;
	
	/**
	 * This variable holds wether or not the object was instantiated 
	 * via the getInstance static method.
	 * 
	 * @var 	boolean
	 * @access	protected, static
	 */
	protected static $_IS_GETINSTANCE 	= FALSE;
	
	/**
	 * Array of the data contained for the module
	 * 
	 * @var array
	 */
	protected $arrInfo = array();
	
	
	/**
	 * Class Constructor
	 * 
	 * @return new self
	 */
	public function __construct() 
	{
		
	}
	
	/**
	 * This method Loads an object instance of the module
	 * 
	 * @access: public, static
	 * @return:	Object - The instantiated module.
	 */
	public static function getInstance() 
	{
		// Execute the before callback
		$arrArguments = &func_get_args();
		self::__beforeCallback(__FUNCTION__, $arrArguments); 
		
		// set the instantiator wathcer to true
		self::$_IS_GETINSTANCE = true;
		
		// Get the called class
		$__STR_CALLED_CLASS__ = (
			function_exists('get_called_class') ? get_called_class() : get_class(self)
		);
		
		if (
			(FALSE === is_object(self::$_OBJECT_INSTANCE)) ||
			(
				(TRUE  === is_object(self::$_OBJECT_INSTANCE)) &&
				(FALSE === is_a(self::$_OBJECT_INSTANCE, $__STR_CALLED_CLASS__))
			)
		) {
			$objApplication = new $__STR_CALLED_CLASS__();	
			self::$_OBJECT_INSTANCE = $objApplication;
		}
		
		// Execute the post callback
		self::__callback(__FUNCTION__, $arrArguments);
		
		return (self::$_OBJECT_INSTANCE);
	}

   /**
	* This method returns a class variable. if no arguments are passed
	* all of the class variables are returned. if an argument is passes and
	* the data not found, FALSE is returned.
	* 
	* @param:	String [Optional] - $strKey - The variable name
	* @param:  Array [optional] - $arrDataContainer - The data to search from. Defaults to $this->arrInfo
	* @return:	String / array / false - The member variable
	*/
	public function getVariable($strKey=NULL, $arrDataContainer = NULL) 
	{
		// Execute the before callback
		$arrArguments = &func_get_args();
		self::__beforeCallback(__FUNCTION__, $arrArguments); 
		
		$strReturn  	= false;
		$strFindKey 	= (string) $strKey;
		$arrSearchFrom = $arrDataContainer;
		
		if (! is_array($arrDataContainer)) 
		{
			$arrSearchFrom = $this->arrInfo;
		}
			
		if (
			(! is_null($strFindKey)) &&
			(strlen($strFindKey))
		) {
			$arrInfo =  array_change_key_case($arrSearchFrom, CASE_LOWER);
			if (array_key_exists(trim(strtolower($strFindKey)), $arrInfo)) 
			{
				$strReturn = $arrInfo[strtolower($strFindKey)];
			}
		} 
		else 
		{
			$strReturn = $arrSearchFrom;
		}
		
		// Execute the post callback
		self::__callback(__FUNCTION__, $arrArguments);
		
		return($strReturn);
	}
	
	/**
	 * Class variable setter
	 * 
	 * @access public
	 * @param  $appVarName    	string - The class name thats being loaded - Required
	 * @param  $mxAppVarValue 	Mixed  - The class name thats being loaded - Optional
	 * @param  $arrDataContainer	Array  - The target array to set the variable in
	 * @return void
	 */
	 public function setVariable($appVarName, $mxAppVarValue=NULL, &$arrDataContainer = NULL) 
	 {
	 	// Execute the before callback
	 	$arrArguments = &func_get_args();
		self::__beforeCallback(__FUNCTION__, $arrArguments); 
	 	
		if (! is_array($arrDataContainer)) 
		{
			$this->arrInfo[strtolower($appVarName)] = &$mxAppVarValue;
		}
		else 
		{
			$arrDataContainer[strtolower($appVarName)] = &$mxAppVarValue;
		}
		
		// Execute the post callback
		self::__callback(__FUNCTION__, $arrArguments);
	 }
	
	/**
	 * method to return the display name of the current module
	 * @return string
	 */
	public function getName()
	{
		return (ucwords(strtolower(get_called_class())));
	}
	
   /**
	* Class Variabvle Data setter method
	* Sets object class variables by batch
	*
	* @access public
	* @param array $arrDataIn
	* @return void
	*/
	public function setVariableArray($arrDataIn = array())
	{
	 	// Execute the before callback
		$arrArguments = &func_get_args();
		self::__beforeCallback(__FUNCTION__, $arrArguments); 
		
		/*
		 * @depreciated:
		 * 		- get called class doesnt return the caller in the array_walk context
		 * 
		 *
		
		// Were using array the array_walk method here in case the children classes
		// Override the setVariable function, or do a pre / post processing of the
		// data using the __callback methods 
		 
		array_walk($arrDataIn, function($mxValue, $strKey) use(&$arrDataIn) {
			call_user_func_array(array(get_called_class(), 'setVariable'), array($strKey, $mxValue));
		});
		
		 */
		
		foreach ($arrDataIn as $strIndexKey => $mxData) 
		{
			call_user_func_array(array(get_called_class(), 'setVariable'), array($strIndexKey, $mxData));
		}
		
		// Execute the post callback
		self::__callback(__FUNCTION__, $arrArguments);
	}
	
	/**
	 * This method is called before a extended action is called
	 * 
	 * @access 	protected
	 * @param 	string $strAction - The called action
	 * @param 	array  $arrArgs	- The array of arguments
	 * @return	void
	 */
	protected function __beforeCallback ($strAction, $arrArgs) 
	{
		if (method_exists(get_called_class(), 'onBefore' . ucwords($strAction))) 
		{
			call_user_func_array(array(get_called_class(), 'onBefore' . ucwords($strAction)), (array) $arrArgs);	
		}
	}
	
	/**
	 * This method is called after a extended action is called
	 * 
	 * @access 	protected
	 * @param 	string $strAction - The called action
	 * @param 	array  $arrArgs	- The array of arguments
	 * @return	void
	 */
	protected function __callback ($strAction, $arrArgs) 
	{
		if (method_exists(get_called_class(), 'on' . ucwords($strAction))) 
		{
			call_user_func_array(array(get_called_class(), 'on' . ucwords($strAction)), (array) $arrArgs);	
		}
	}
	
   /**
	* This method returns a class variable. if no arguments are passed
	* This is a magic method implementation and should not be called directly
	* all of the class variables are returned. if an argument is passes and
	* the data not found, FALSE is returned.
	* 
	* @param:	String [Optional] - $strKey - The variable name
	* @return:	String / array / false - The member variable
	*/
		
	public function __call($strFnName, $arrArguments) 
	{
		if (strcmp(strtolower(substr($strFnName, 0, 3)), 'get') === 0) 
		{
			return ($this->getVariable(strtolower(substr($strFnName, 3))));
		}
		else if 
		(
			(strcmp(strtolower(substr($strFnName, 0, 3)), 'set') === 0) /* &&
			(isset($arrArguments[0])) -- This part is removed since we need to be able to set a value to nothing $this->setValue(null)... */
		) {
		   /*
			* On a setter method, we return the object so we can chain commands
			* EX: $object->getUser()->setFirstName('Avi')->save();
			*/
			$strValue = isset($arrArguments[0]) ? $arrArguments[0] : NULL;
			$this->setVariable(strtolower(substr($strFnName, 3)), $strValue);
		}
		else if  (strcmp(strtolower(substr($strFnName, 0, 6)), 'delete') === 0) 
		{
			unset($this->arrInfo[strtolower(substr($strFnName, 6))]);
		}
		else if 
		(
			(strcmp(strtolower(substr($strFnName, 0, 5)), 'addto') === 0) /* &&
			(isset($arrArguments[0])) -- This part is removed since we need to be able to set a value to nothing $this->setValue(null)... */
		) {
			$strOriginalValue 	= $this->getVariable(strtolower(substr($strFnName, 5)));	
			$strAddingValue 	= isset($arrArguments[0]) ? $arrArguments[0] : NULL;
			$strVariableName	= strtolower(substr($strFnName, 5));

			if (
				(TRUE === is_numeric($strOriginalValue)) &&
				(TRUE === is_numeric($strAddingValue))
			) {
				// Numerical addition
				$this->setVariable($strVariableName, ($strOriginalValue + $strAddingValue));	
			}
			else if (is_array($strOriginalValue))
			{
				// Add to an array
				$strOriginalValue[]	= $strAddingValue;
				$this->setVariable($strVariableName, $strOriginalValue);
			}
			else
			{
				// String Concat
				$this->setVariable($strVariableName, ($strOriginalValue . $strAddingValue));		
			}
		}
		else 
		{
			throw new Exception("Undefined method: " . $strFnName . "() in " . get_called_class());
		}
		
		return ($this);
	}
	
	/**
	* This method overloads the __call method
	* 
	* @param:	String [Optional] - $strKey - The variable name
	* @return:	String / array / false - The member variable
	*/
	public static function __callStatic($strFnName, $arrArguments) 
	{
		//call_user_func_array(array(self::$_OBJECT_INSTANCE, $strFnName), $arrArguments);	
		self::$_OBJECT_INSTANCE->__call($strFnName, $arrArguments);
	}
	
	/**
	 * Sleep method called prior to serialize()
	 * 
	 * @access public
	 * @return array 
	 */
	public function __sleep()
	{
		return ((array) $this->getVariable());
	}
	
	/**
	 * Wakeup method called prior to unserialize()
	 * 
	 * @access public
	 * @return void 
	 */
	public function __wakeup()
	{
	}
	
	/**
	 * toString method will return a string representation of the object
	 * 
	 * @access public
	 * @return string 
	 */
	public function __toString()
    {
    }
    
	/**
 	 * TODO: Refactor the __set / __get methods as they are necessary for a complete framework but dont really work as expected.
	 * Temporarily disabled.
	 */

	/**
	 * __set method will be called when setting a property from the object, ex: $object->value = this is a test.
	 * 
	 * @access public
	 * @return void
	 */
	/*
	public function __set($strName, $mxValue = NULL)
    {
    	$this->setVariable($strName, $mxValue);
    }
	*/
    
	/**
	 * __get method will be called when getting a property from the object, ex: print $object->value
	 * 
	 * @access public
	 * @return void
	 */
	/*
	public function __get($strName)
    {
    	return $this->getVariable($strName);
    }
	*/
    
    /**
	 * __isset method is triggered by calling isset() or empty() on inaccessible properties. Ex: isset($object->value)
	 * 
	 * @access public
	 * @return boolean
	 */
	public function __isset($strName)
    {
    	return ((bool) $this->getVariable($strName));
    }
    
	/**
	 * __unset method is invoked when unset() is used on inaccessible properties. Ex: unset($object->value)
	 * 
	 * @access public
	 * @return void
	 */
	public function __unset($strName)
    {
    	$this->setVariable($strName, FALSE);
    }
} 



/**
 * OBJECT_BASE_SIGNLETION Interface Base Class
 * This class represents the SINGLETON version of the interface base class for all objects
 * and implements some of the basic object features
 *
 * @package		{CORE}::INTERFACE
 * @subpackage	{APPLICATION_CORE
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 */	
 abstract class OBJECT_BASE_SINGLETON extends OBJECT_BASE
 { 
  /**
 	* This is a boolean pointer to the active loaded instance
 	*
 	* @var Boolean
 	*/
 	protected static $_IS_FROM_GETINSTANCE = false;
 	
 	/**
 	 * This is a pointer to the active loaded instance
 	 * 
 	 * @var Array
 	 */
 	protected static $_OBJECT_INSTANCES = array();
 
    /**
 	 * Class constructor 
 	 * 
 	 * @access 	public
 	 * @param	none
 	 * @return 	PDO_DATABASE
 	 */
 	public final function __construct()
 	{
 		if (FALSE === self::$_IS_FROM_GETINSTANCE)
 		{
 			throw new Exception("Access to " . __CLASS__ . " can only be instantiated via " . __CLASS__ . "::getInstance()");
 			return (parent::$_IS_FROM_GETINSTANCE);
 		}
 	}
 	
 	/**
	 * Main Accessor method. This method returns the singleton PDO_DATABASE instance
	 * 
	 * @access 	public, statis
	 * @param	none
	 * @return	PDO_DATABASE
 	 */
 	public static final function getInstance()
 	{
		$strCalledClass = get_called_class();
 		if (FALSE === is_object(self::$_OBJECT_INSTANCES[$strCalledClass]))
 		{
			// Execute the before callback
			$arrArguments = &func_get_args();
			self::__beforeCallback(__FUNCTION__, $arrArguments); 
			
 			self::$_IS_FROM_GETINSTANCE 				= TRUE;
 			self::$_OBJECT_INSTANCES[$strCalledClass] 	= new $strCalledClass();
			
			// Execute the post callback
			self::__callback(__FUNCTION__, $arrArguments);
 		}
 		
 		return (self::$_OBJECT_INSTANCES[$arrArguments]);
 	}
	
	/**
	* This method returns a class variable. if no arguments are passed
	* This is a magic method implementation and should not be called directly
	* all of the class variables are returned. if an argument is passes and
	* the data not found, FALSE is returned.
	* 
	* @param:	String [Optional] - $strKey - The variable name
	* @return:	String / array / false - The member variable
	*/
		
	public function __call($strFnName, $arrArguments) 
	{
		die($strFnName);
		return call_user_func(array(parent, $strFnName), array($arrArguments));
	}
 	
 	/**
	 * Prevents the singleton enforcer from cloning
	 * 
	 * @access 	public
	 * @return	void
 	 */
 	public final function __clone()
 	{
		$strCalledClass = get_called_class();
 		throw new Exception("Access to " . $strCalledClass . " can only be instantiated via " . $strCalledClass . "::getInstance()");
 	}
 	
   /**
 	* Prevents the singleton enforcer from __wakeup (unserializing)
 	*
 	* @access 	public
 	* @return	void
 	*/
 	public final function __wakeup()
 	{
		$strCalledClass = get_called_class();
 		throw new Exception("Access to " . $strCalledClass . " can only be instantiated via " . $strCalledClass . "::getInstance()");	
 	}
 }
