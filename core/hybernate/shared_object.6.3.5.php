<?php
	/**
	 * SHARED_OBJECT Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework - All (Except for the constructor) methods get a $this->__callback(__FUNCTION__); call
	 * and $this->__beforeCallback(__FUNCTION__, $arrArguments); before callBacks
	 * The child objects can access this callback by just creating a protected method (in ucwords): on{method call back name}()
	 * The arguments passed to the event listened method are passed as an array
	 * example, the following method will be fired on save: protected function onSave($arrArgs) {}
	 * 
	 * IMPROVMENTS FROM VERSION 6.3.4
	 * 		- Removed Cast Objects from Shared Objects
	 * 		- Added Improved Multi Instance instantiator
	 * 		- Added 'search in array' feature for getVariable() method
	 * 		- Added $this->loadFromArray when loading from a cached object.
	 *
	 * @package		CLASSES::HYBERNATE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php'); 
	require_once(__APPLICATION_ROOT__ . '/memcache/memcache_manager.php'); 
	require_once(__APPLICATION_ROOT__ . '/database/database.php'); 
	
	abstract class SHARED_OBJECT extends SITE_EXCEPTION {
		/*
		 * The following constances determines if the object instace will be strict
		 * or soft. a strict instance will throw an error if the object doesnt exist
		 * a soft instace will return a new copy of the object. the default is strict
		 */
		const		SHARED_OBJECT_STRICT_INSTANCE 	= true;
		const		SHARED_OBJECT_SOFT_INSTANCE 	= false;
		const		SHARED_OBJECT_DEFAULT_MAP_KEY	= 'id';
		/**
		 * Object load types: These constants will display what type
		 * Of load type the current object is.
		 **/
		const		SHARED_OBJECT_LOADED_FROM_CACHE = 'CACHED';
		const		SHARED_OBJECT_LOADED_FROM_DB 	= 'DATABASE';
		const		SHARED_OBJECT_NEW_INSTANCE 		= 'NEW_OBJECT';
		const		SHARED_OBJECT_CASTED_INSTANCE	= 'CAST_OBJECT';
		
		/**
		 * The following constants determine what type of cacheing 
		 * the object will have:
		 * SHARED_OBJECT_CACHE_FILE: Object will be cached to file
		 * SHARED_OBJECT_CACHE_SESSION: Object will be cached to session
		 * SHARED_OBJECT_CACHE_MEMCACHE: Object will be cached using memecache
		 * SHARED_OBJECT_CACHE_NONE: Object will not be cached
		 **/
		const		SHARED_OBJECT_CACHE_FILE 		= 1;
		const		SHARED_OBJECT_CACHE_SESSION 	= 2;
		const		SHARED_OBJECT_CACHE_NONE 		= 3;
		const		SHARED_OBJECT_CACHE_MEMCACHE	= 4;
		
		/**
		 * Method properties
		 **/
		protected	$strInstanceKeyArray	= false;			 
		protected 	$strClassId 	  		= false;
		protected 	$intId 		  			= 0;
		protected 	$className 		  		= NULL;
		protected 	$arrInfo 		  		= array();
		private 	$arrChanges		  		= array();
		private 	$arrColumnsFields  		= array(); 	// The columns avaialable in the object DB
		protected 	$arrNonUpdateFields  	= array('timestamp_update');	// These are fields that should not be updated in the DB
		protected 	$blnCanSave 	  		= true;  	// If the object can be saved to the database? 
		protected 	$strObjectInstanceSql	= NULL; 	// This is a copy of the instance SQl used to load the object. used in refresh() method.
		protected 	$defaultMapKey			= SHARED_OBJECT::SHARED_OBJECT_DEFAULT_MAP_KEY; // The default map key for inatance load
		protected	$strClassKey			= NULL; // The class key OBECTS::PACKAGE::CLASS
		protected	$intClassKeyId			= 0; // The class key id - see CLASS_REGISTRY
		/**
		 * Instance type: These determine what type of instance the object will be
		 * SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE : If a match is not found, an exception is thrown
		 * SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE : If a match is not found, a new instance (id=0) is returned
		 **/
		protected 	$constructIntegrity		= SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE;
		private 	$strCurrentLoadType		= SHARED_OBJECT::SHARED_OBJECT_NEW_INSTANCE;
		protected 	$intObjectCacheType		= SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE; // Changed from SHARED_OBJECT::SHARED_OBJECT_CACHE_FILE;
		
		
		/**
		 * Public methods
		 **/
		public function SHARED_OBJECT() {}
		
		/**
		 * This method gets the return Class Id
		 * @return:	String - $this->strClassId
		 */
		public function getClassId() { 
			$arrArguments = func_get_args();
			$this->__callback(__FUNCTION__, $arrArguments);
			return($this->strClassId); 
		}		
		
		/**
		 * This method returns the class key
		 *
		 * @access 	public
		 * @param 	none
		 * @return 	String
		 */
		public function getClassKey()
		{
			return ($this->strClassKey);
		}
		
		/**
		 * This method returns the class key ID
		 *
		 * @access 	public
		 * @param 	none
		 * @return 	Int
		 */
		public function getClassKeyId()
		{
			if (
				(((int) $this->intClassKeyId) <= 0) &&
				(false === empty($this->strClassKey))
			) {
				$objClassKey = CLASS_REGISTRY::getInstanceFromKey(array(
					'className'	=> $this->getClassKey()
				), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
				
				if (false === ((bool) $objClassKey->getId()))
				{
					$objClassKey->setClassName($this->getClassKey());
					$objClassKey->setDescription('Auto Generated Class Registry Key [' . date('Y-m-d H:i:s', time()) . ']');
					$objClassKey->save(); 
				}
				
				$this->intClassKeyId = (int) $objClassKey->getId();
			}
			
			return ((int) $this->intClassKeyId);
		}
		
		/**
		 * This method returns a class variable. if no arguments are passed
		 * all of the class variables are returned. if an argument is passes and
		 * the data not found, FALSE is returned.
		 * @param:	String [Optional] - $strKey - The variable name 
		 * @param:  Array [optional] - $arrDataContainer - The data to search from. Defaults to $this->arrInfo
		 * @return:	String / array / false - The member variable
		 */
		public function getVariable($strKey=NULL, $arrDataContainer = NULL) {
			$arrArguments 	= func_get_args();
			$strReturn  	= false;
			$strFindKey 	= (string) $strKey;
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			
			$arrSearchFrom = $arrDataContainer;
			if (! is_array($arrDataContainer)) {
				$arrSearchFrom = $this->arrInfo;
			} 
			
			if (
				(! is_null($strFindKey)) &&
				(strlen($strFindKey)) 
			) { 
				$arrInfo =  array_change_key_case($arrSearchFrom, CASE_LOWER);
				if (array_key_exists(trim(strtolower($strFindKey)), $arrInfo)) {
					$strReturn = $arrInfo[strtolower($strFindKey)];	
				}
			} else $strReturn = $this->arrInfo;
			$this->__callback(__FUNCTION__, $arrArguments);
			return($strReturn);
		}
		
		/**
		 * This method wraps the get_class method so the autoload can work
		 * @param $objClass the class to introspect
		 * @return string The class name
		 * @access private
		 */
		 private function getClassName($objClass) {
			 $strClassName = NULL;
			 if (is_object($objClass)) {
				 $strClassName = (isset($objClass->class_name) ? $objClass->class_name : strtolower(get_class($objClass)));
			 }
			 return ($strClassName);	 
		 }
		 
		
		/**
		 * This method returns a class variable. if no arguments are passed
		 * This is a magic method implementation and should not be called directly
		 * all of the class variables are returned. if an argument is passes and
		 * the data not found, FALSE is returned.
		 * @param:	String [Optional] - $strKey - The variable name 
		 * @return:	String / array / false - The member variable
		 */
		 
		 public function __call($strFnName, $arrArguments) {
			if (strcmp(strtolower(substr($strFnName, 0, 3)), 'get') === 0) {
				return ($this->getVariable(strtolower(substr($strFnName, 3))));	
			}
			else if (
				(strcmp(strtolower(substr($strFnName, 0, 3)), 'set') === 0) /* &&
				(isset($arrArguments[0])) -- This part is removed since we need to be able to set a value to nothing $this->setValue(null)... */ 
			) {
				/*
				 * On a setter method, we return the object so we can chain commands
				 * EX: $object->getUser()->setFirstName('Avi')->save();
				 */
				$this->setVariable(strtolower(substr($strFnName, 3)), isset($arrArguments[0]) ? $arrArguments[0] : NULL);
				return ($this);	
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
			else {
				throw new Exception("Undefined method: " . $strFnName . "() in " . get_called_class());
			}
		 }
		
		/**
		 * This method sets a class variable. 
		 * @param:	String 	- $strKey - The variable name 
		 * @param:	Mixed 	- $strValue - The variable value 
		 * @return:	Void
		 */
		public function setVariable($strKey=NULL, $strValue=NULL) {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$this->arrInfo[strtolower($strKey)] = $strValue;	
			
			if (strcmp(strtolower($strKey), 'id') === 0) {
				$this->intId = $strValue;
			}
			
			if (strcmp(strtolower($strKey), 'id') <> 0) {
				$this->arrChanges[strtolower($strKey)] = $strValue;		
			}
			
			$this->arrChanges[strtolower($strKey)] = $strValue;		
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		public function getFields() {
			if (! sizeof($this->arrColumnsFields)) {
				$objDb = DATABASE::getInstance();
				$this->arrColumnsFields = $objDb->getColumnsFromTable($this->getClassName($this));
			}
			return ((array) $this->arrColumnsFields);
		}
		
		/**
		 * This method sets class variable from an array. 
		 * @param:	Array 	- $arrVars - The variable array
		 * @return:	Void
		 */
		public function setVariableFromArray($arrVars = array()) {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			
			foreach ($arrVars as $strKey => $strValue) {
				$this->setVariable($strKey, $strValue);	
			}
			
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		
		
		/**
		 * This function will load an object from array, same as loadFromArray but it
		 * Will validate that the fields in the array passed are valid for the object.
		 * The main difference between setVariableFromDirtyArray() and [setVariableFromArray() / loadFromDirtyArray()]
		 * is that info that are already set will NOT get overriden and all changed fields are marked as changed
		 *
		 * @param: Array $arrInfo - The array info
		 * @return: Void
		 */
		public function setVariableFromDirtyArray(array $arrInfo=array()) {
			$arrArguments = func_get_args();
			$arrValidFields = $this->getFields();
			$arrCleanData = array();
			foreach($arrValidFields as $intIndex => $arrFieldInfo) {
				if (isset($arrInfo[$arrFieldInfo['Field']])) {
					$this->setVariable($arrFieldInfo['Field'], $arrInfo[$arrFieldInfo['Field']]);
				}
			}
			
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		
		/**
		 * This method saves class variable in the database and according 
		 * Cacheing method. it excludes explicit non updateable fields
		 * @return:	Boolean - If the transaction was successful
		 */
		public function save() { 
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			
			$objDb 			= DATABASE::getInstance(); 
			$arrUpdate 		= $this->arrChanges;
			$blnTransaction = $this->isObjectCanSave();
			
			if ($blnTransaction && count($this->arrChanges)) {
				// set the id is if exists
				if (((bool) strlen($this->getId())) && ((int) $this->getId() > 0)) {
					$arrUpdate['id'] = $this->getId();
				}
				else {
					// Remove the ID param if it exists
					unset($arrUpdate['id']);
					// Call the onBefore_getInstance callback if its a new object
					$this->__beforeCallback('_getInstance', $arrArguments);
				}
				// Uset non-updateable fields..
				foreach ($this->arrNonUpdateFields as $intIndex => $strNonUpdateField) {
					if (isset($arrUpdate[$strNonUpdateField])) 
					{
						unset($arrUpdate[$strNonUpdateField]);	
					}
				}
				
				$strSelfObjectName = $this->getClassName($this);	
				$blnTransaction = $objDb->insertUpdateFromArray(
					$strSelfObjectName,
					$arrUpdate				 
				); 
				
				if ($blnTransaction) {
					$this->intId = (
						(($this->intId !== 0) && ($this->intId !== '0')) ? $this->intId : $objDb->lastInsertId($strSelfObjectName)
					);
					$this->setVariable('id', (is_string($this->intId) ? (string) $this->intId : (int) $this->intId));
					$this->setClassPath($this->getClassPath());
					$this->cacheObject();
				}
				
				// Reset the changes....
				$this->arrChanges = array();
			}
			$this->__callback(__FUNCTION__, $arrArguments);
			return((bool) $blnTransaction);
		}
		
		/**
		 * This method deletes class variable in the database and according 
		 * Cacheing. It is recommended to call this method when manually removing 
		 * from the database.
		 * @return:	Void
		 */
		public function delete() {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			if (
				($this->getId() !== 0) &&
				($this->getId() !== '0') 
			) {
				$objDb = DATABASE::getInstance();
				$objDb->query(
					'DELETE FROM ' . $this->getClassName($this) . ' ' .
					'WHERE id=' . (is_string($this->getId()) ? "'" . $objDb->escape($this->getId()) . "'" : (int) $this->getId())
				); 
				$this->clearCache();
				$this->arrInfo = array('id' => 0);
				$this->arrChanges = array();
			}
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		public function getId() { 
			return($this->getVariable('id')); 
		}
		
		/**
		 * This method Loads an object from a package string
		 * The include path start from the $_SERVER['DOCUMENT_ROOT']
		 * @param: 	$strObjectClassPath - String [Optional] - The package to load 
		 * @usage: 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_LAYOUT");
		 * @return:	$blnContinue - Boolean - If the load was successful	
		 */
		public static function getObjectFromPackage($strObjectClassPath=NULL) {
			$blnContinue = (is_null($strObjectClassPath = trim($strObjectClassPath)) ? false : true);	
			if ((bool) $blnContinue) {
				// Process the path
				$strClassIncludePath = __SITE_ROOT__ . DIRECTORY_SEPARATOR . str_replace(array('::', __SITE_ROOT__), array(DIRECTORY_SEPARATOR, ''), strtolower($strObjectClassPath)) . '.php';
				$arrClassSegments = explode('::', $strObjectClassPath); 
				$strClassName = strtoupper(end($arrClassSegments));
				$blnContinue = @require_once($strClassIncludePath);
				if (! $blnContinue) {
					throw new Exception('Class path: ' . $strObjectClassPath . ' not found. | Include Path: ' . $strClassIncludePath);	
				}
				//$backtrace = debug_backtrace();
				//print 'included: ' . $strClassIncludePath . ' | <b style="color:red">' .  $backtrace[0]['file'] . '</b><br />';
				return ($blnContinue);
			}
		}
		
		/**
		 * This method Loads a SHARED object from a package string
		 * The include path start from the {__APPLICATION_CLASS_PATH__}/hybernate/objects/
		 * @param: 	$strObjectClassPath - String [Optional] - The package to load 
		 * @usage: 	SHARED_OBJECT::loadSharedObject("PAGES::PAGE_LAYOUT"); would map to "{__APPLICATION_CLASS_PATH__}::HYBERNATE::OBJECTS::PAGES::PAGE_LAYOUT"
		 * @return:	$blnContinue - Boolean - If the load was successful	
		 */
		public static function loadSharedObject($strObjectClassPath=NULL) {
			return (SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::" . $strObjectClassPath));
		}
		
		/**
		 * This method is asyncronous to SHARED_OBJECT::getObjectFromPackage() except
		 * That it will not modify the include path information. Its used when file 
		 * names are not all in lower cases.
		 * 
		 * @access  public, static
		 * @param 	string  $strObjectClassPath - The include library path
		 * @return 	boolean
		 */
		public static function requireLibrary($strObjectClassPath=NULL) {
			$blnContinue = (is_null($strObjectClassPath = trim($strObjectClassPath)) ? false : true);	
			if ((bool) $blnContinue) 
			{
				// Process the path
				$strClassIncludePath = __SITE_ROOT__ . DIRECTORY_SEPARATOR . str_replace('::', DIRECTORY_SEPARATOR, $strObjectClassPath) . '.php';
				$arrClassSegments = explode('::', $strObjectClassPath);
				$blnContinue = @require_once($strClassIncludePath);
				if (! $blnContinue) 
				{
					throw new Exception('Class path: ' . $strObjectClassPath . ' not found. | Include Path: ' . $strClassIncludePath);	
				}
			}
			return ($blnContinue);	
		}
		
		/**
		 * This method Loads an object instance from an entity ID
		 * @access: public
		 * @param: 	$intId - Interger [Optional] - The entity ID
		 * @param: 	blnInstanceIntegrity - Integer [Optional] [Default: SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE] - The instance integerity (Strict|Soft)
		 * @usage: 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_LAYOUT");
		 * @return:	$blnContinue - Boolean - If the load was successful	
		 */
		public static function getInstance(
			$intId = 0,
			$blnInstanceIntegrity = SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE	
		) {
			$strCalledClass 	= get_called_class();
			$objReturn 			= new $strCalledClass(); 
			$objReturn->setConstructIntegrity($blnInstanceIntegrity);
			$objReturn->setInstanceKeyArray(array('id' => $intId));
			$objReturn->setCalledClass($strCalledClass);
				
			return ($objReturn->_getInstance());
		}
		
		public static function newInstance() {
			// Go through the _getInstance method to 
			// ensure that the before and after callbacks
			// are used
			return (self::getInstance(0, SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE));
		}
		
		/**
		 * This method returns a class object view of object instances. It also makes sure that the loaded instances
		 * Are of type SHARED_OBJECT. The isDirty parameter will load the objects if the arrView parameters contain 
		 * Joins or other filters that render the data 'dirty'
		 * 
		 * @param:	$arrView [Array] 			- View parameters
		 * @param: 	$blnIsDirty [Boolean]		- The the request should be loaded using loadFromDirtyArray()
		 * @param: 	$blnUseCache [Boolean]		- Should the multiinstance be cached
		 * @return: $arrObjectView - [Array] 	- The returned object view
		 */
		public static final function getMultiInstance($arrView = array(), $blnIsDirty = false, $blnUseCache = false) 
		{
			$arrReturn		= array();
			$strClassName 	= get_called_class();
			$objCalledClass	= new $strClassName();
			if (
				(is_object($objCalledClass)) &&
				(is_subclass_of($objCalledClass, __CLASS__))
			) {
				$arrData 		= $objCalledClass->getObjectClassView($arrView, $blnUseCache);
				
				foreach ($arrData as $intIndex => $arrRow) 
				{
					$objInstance 	= $objCalledClass->newInstance();
					if ($blnIsDirty) 
					{
						$objInstance->loadFromDirtyArray($arrRow);
					} 
					else 
					{
						 $objInstance->loadFromArray($arrRow);
					}
					
					$arrReturn[] = $objInstance; 
				}
			}
			
			return ($arrReturn);
		}
		
		/**
		 * The following method will load an instance from a specified key
		 * NB:: Cache cannot be implemented because the class ID is not
		 * yet defined, so even if cache exists for that object, it will not be used.
		 * @param $strObjectTableKey 	string	- The object key to load from
		 * @param $strCallerKeyId		int 	- The object key
		 * @return void
		 */
		 public static function getInstanceFromKey (
			$arrKeyValues = array(),
			$blnInstanceIntegrity = SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE	
		 ){ 		
		 	$strClassName = get_called_class();
			$objInstance = new $strClassName();
			$objInstance->setConstructIntegrity($blnInstanceIntegrity);
			$objInstance->setInstanceKeyArray($arrKeyValues);
			$objInstance->setCalledClass($strClassName);
		 	return ($objInstance->_getInstance());
		 }
		 
		public function _getInstance() 
		{
			// Load the callback functions
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			
			// Set the instance type
			$this->setInstanceType(SHARED_OBJECT::SHARED_OBJECT_NEW_INSTANCE);
			
			// Load the instance...
			$arrLoadInstaceParams = $this->getInstanceKeyArray();
			$intId = null;
			if (
				(isset($arrLoadInstaceParams['id']) 	&& 
				(count($arrLoadInstaceParams) == 1))		   
			) {
				// We check for string IDs for objects like geo_locator which have a string id (the ip)
				$intId	= (is_string($arrLoadInstaceParams['id']) ? $arrLoadInstaceParams['id'] : (int) $arrLoadInstaceParams['id']);
				unset($arrLoadInstaceParams['id']);
			}
			if (
				((int) $intId > 0) || (strlen($intId)) ||
				(is_array($arrLoadInstaceParams) && sizeof($arrLoadInstaceParams))
			) {
				// Set the class id here because the cache system needs the ID
				$this->setVariable('id', $intId);
				
				// Load from the cache if it exists
				SITE_EXCEPTION::supressException();
				$objCachedObject = $this->loadFromCache();
				SITE_EXCEPTION::clearExceptionSupress();
				
				if (! is_object($objCachedObject)) {
					$objDb 		= DATABASE::getInstance(); 
					
					// Build the instance SQL
					$strInstanceSql = "SELECT SQL_NO_CACHE * FROM " . $this->getClassName($this) . " WHERE ";
					if (sizeof($arrLoadInstaceParams)) {
						$strInstanceSql .= " 1=1 ";
						foreach ($arrLoadInstaceParams as $strColumn => $mxValue) {
							$strInstanceSql .=	" AND " . $strColumn . " = " . (is_string($mxValue) ? "'" . $objDb->escape($mxValue) . "'" : intval($mxValue));
						}
					} else {
						$strInstanceSql .= $this->getDefaultMapKey() . " = " . 
										 (is_string($intId) ? "'" . $objDb->escape($intId) . "'" : intval($intId)) . " ";
					}
					$strInstanceSql .= " GROUP BY " . $this->getDefaultMapKey();
					$strInstanceSql .= " ORDER BY " . $this->getDefaultMapKey() . " DESC ";
					$strInstanceSql .= " LIMIT 1 ";
					$this->strObjectInstanceSql = $strInstanceSql;
					$arrInfo = $objDb->query($this->strObjectInstanceSql); 
									
					// If the object exists
					if (count($arrInfo)) {
						// We have a valid object
						$nIntId = (isset($arrInfo[0]['id']) ? $arrInfo[0]['id'] : 0);
						$this->setVariable('id', (intval($nIntId)? intval($nIntId) : $nIntId));
						$this->loadFromArray((array) $arrInfo[0]);
						// Set the class path
						self::setClassPath($this->getClassPath());
						
						// Set the instance type
						$this->setInstanceType(SHARED_OBJECT::SHARED_OBJECT_LOADED_FROM_DB);
						
						// Save the object to cache
						SITE_EXCEPTION::supressException();
						$this->cacheObject();
						SITE_EXCEPTION::clearExceptionSupress();
						
					} else {
						// We dont have a valid object
						if ($this->getConstructIntegrity() === SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE)
							throw new Exception('Class ' . $this->getClassName($this) . ' {' . $this->getClassId() . '} Does not exist.');
						else {
							// We dont have a valid object and return type is NOT strict,
							// So we return a new object
							$this->setVariable('id', 0);
							// if we loaded from getInstanceFromKey, we need to set these values
							if (sizeof($arrLoadInstaceParams)) {
								foreach($arrLoadInstaceParams as $strVar => $mxValue) {
									$this->setVariable($strVar, $mxValue);	
								}
							}	
						}
					}
				} else if (
					(is_object($objCachedObject)) &&
					($objCachedObject instanceof  self)
				){
					// Object exists in the session, load from session
					//$this->loadFromArray(unserialize(base64_decode($_SESSION[$this->getClassId()])));
					// Set the instance type
					$this->setInstanceType(SHARED_OBJECT::SHARED_OBJECT_LOADED_FROM_CACHE);
					$this->loadFromArray($objCachedObject->getVariable());
				}
			}
			
			$this->__callback(__FUNCTION__, $arrArguments);
			return ($this);	
		}
		
		/**
		 * This method returns an cached object from the file server
		 * @return: $objReturn - [Object] 	- The cached object
		 */
		private function loadFromCache() {
			$objReturn = false;
			self::setClassPath($this->getClassPath());
			
			// Get the object from the cache if it exists
			switch ((int) $this->getObjectCacheType()) {
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_FILE) : {
					// -------------------------------------
					// File Cache Section
					$strCachedObjectFile = $this->getCacheFileNamePath();
					if (file_exists($strCachedObjectFile)) {
						$cachedObjectSignture = file_get_contents($strCachedObjectFile);
						if (
							($objUnserialized = unserialize($cachedObjectSignture)) &&
							(is_object($objUnserialized)) &&
							(strtoupper(get_class($objUnserialized)) == strtoupper(get_called_class()))
						) {
							$this->loadFromArray($objUnserialized->getVariable());
							$objReturn = $objUnserialized;
						}
					}
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_SESSION) : {
					// -------------------------------------
					// Session Cache Section
					if (isset($_SESSION[$this->getClassId()])) {
						$this->loadFromArray(unserialize(base64_decode($_SESSION[$this->getClassId()])));
						$objReturn = $this;
					}
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE) : {
					// -------------------------------------
					// Memcache Cache Section
					SITE_EXCEPTION::supressException();
					$objMemcache = MEMCACHE_MANAGER::getInstance();
					if ($arrData = $objMemcache->get($this->getClassId()))		
					{
						$strClassName = get_called_class();
						$objReturn  = new $strClassName();
						$objReturn->loadFromArray($arrData);
					}
					SITE_EXCEPTION::clearExceptionSupress();
					//$objReturn = $objMemcache->get($this->getClassId());		
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE) : {
					break;
				}
			}
			return ($objReturn);
		}
		
		/**
		 * This method clears an cached object from the file server
		 * @return: void
		 */
		public function clearCache() {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			
			// Clear the object cache
			switch ((int) $this->getObjectCacheType()) {
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_FILE) : {
					// -------------------------------------
					// File Cache Section
					$strCachedObjectFile = $this->getCacheFileNamePath();
					//$strCachedObjectFile = $this->getCacheFilePath();
					
					SITE_EXCEPTION::supressException();
					if (file_exists($strCachedObjectFile)) {
						unlink($strCachedObjectFile);
					}
					SITE_EXCEPTION::clearExceptionSupress();
					
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_SESSION) : {
					// -------------------------------------
					// Session Cache Section
					if (isset($_SESSION[$this->getClassId()]))
						unset($_SESSION[$this->getClassId()]);
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE) : {
					// -------------------------------------
					// Memcache Cache Section
					$objMemcache = MEMCACHE_MANAGER::getInstance();
					$objReturn = $objMemcache->delete($this->getClassId());
					// -------------------------------------
					break;
				}
				
				case (SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE) : {
					break;
				}
			} 
			
			// Invalidate the multi instance memcache key
			$objMemcache = MEMCACHE_MANAGER::getInstance();	
			if (
				(true === is_object($objMemcache)) &&
				(true === $objMemcache->isServerOnline())
			) {
				SITE_EXCEPTION::supressException();
				$objMemcache->resetKeyVersionNumber();
				SITE_EXCEPTION::clearExceptionSupress();
			}
			
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		/**
		 * This method caches an object to the file server
		 * @return: $blnReturn - Boolean - if the cacheing was a success
		 */
		public function cacheObject() {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$blnReturn = false;
			if (
				((int) $this->getVariable('id')  !== 0) &&
				(strlen(trim($this->getVariable('id')))) &&
				(! is_null($this->getVariable('id'))) &&
				(strlen(trim($this->getClassId())))
			) {
				// Clear the cache.
				$this->clearCache();	
				
				// Save the object to cache
				switch ((int) $this->getObjectCacheType()) {
					case (SHARED_OBJECT::SHARED_OBJECT_CACHE_FILE) : {
						// -------------------------------------
						// File Cache Section
						$strCachedObjectFile = $this->getCacheFileNamePath();
						// dirname(__FILE__) . DIRECTORY_SEPARATOR . 'object_cache' . DIRECTORY_SEPARATOR . $this->getClassId() . '.objectCache'
						//$strCachedObjectFile = $this->getCacheFilePath();
						
						$fh = fopen($strCachedObjectFile, 'w'); //or die("can't open file");
						$strObjectData = serialize($this);
						$blnReturn = fwrite($fh, $strObjectData);
						if ($blnReturn && file_exists($strCachedObjectFile)) {
							@chmod($strCachedObjectFile, 0644);
						}
						fclose($fh);
						// -------------------------------------
						break;
					}
					
					case (SHARED_OBJECT::SHARED_OBJECT_CACHE_SESSION) : {
						// -------------------------------------
						// Session Cache Section
						$_SESSION[$this->getClassId()] = base64_encode(serialize($this->getVariable()));
						// -------------------------------------
						break;
					}
					
					
					case (SHARED_OBJECT::SHARED_OBJECT_CACHE_MEMCACHE) : {
						// -------------------------------------
						// Memcache Cache Section
						$objMemcache = MEMCACHE_MANAGER::getInstance();
						
						$this->clearCache(); // Delete the cached object
						$objMemcache->set($this->getClassId(), $this->getVariable()); // Lifetime cache in memcache!
						// -------------------------------------
						break;
					}
					
					case (SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE) : {
						break;
					}
				}
			}
			$this->__callback(__FUNCTION__, $arrArguments);
			return ($blnReturn);
		}
		
		/**
		 * @depreciated: FIXME: Need add directory creation support
		 * This method caches an objects cache file path on the server
		 * @return: $strRealClassFilePath - String - The cache file path
		 */
		/*
		protected function getCacheFilePath() {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$strRealClassFilePath 	 = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(realpath($this->getClassPath())) ) . DIRECTORY_SEPARATOR; // Class package path
			$strRealClassFilePath	.= 'object_cache' . DIRECTORY_SEPARATOR . $this->getClassName($this) . DIRECTORY_SEPARATOR . get_class($this) . '[' . $this->getId() . '].objectCache';	
			$this->__callback(__FUNCTION__, $arrArguments);
			return ($strRealClassFilePath);
		}
		*/
		
		/**
		 * This method gets the objects cache file name
		 * @return: The cache file name and path
		 */
		protected function getCacheFileNamePath() {
			/**
			 * Version 7.0.1  	- 	Cache is not set as files name ex: CLASSES::DATABASE::DATABASE
			 * 						We now cache objects using a real path file. This will improve performance
			 *						on a unix based server which has a limit on files per directory. We still
			 *						keep the original classId file name.
			 */
			return (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'object_cache' . DIRECTORY_SEPARATOR . $this->getClassId() . '.objectCache');
		}
		
		/**
		 * This method returns an object view
		 * @param:	$arrView [Array] 			- View parameters
		 * @param:	$arrmappedOperators [Array] - View parameters operators, to control selection [=, >, <] etc...
		 * @param:	$intLimit [Integer] 		- The limit amount of records to return [0 returns all records]
		 * @param:	$strOrderBy [String] 		- The order by column name
		 * @param:	$strAscDesc [String] 		- The order by column direction [ASC, DESC]
		 * @return: $arrObjectView - [Array] 	- The returned object view
		 */
		public static function getObjectClassView($arrView = array(), $blnIsCachable = false) 
		{
			// Define a default set of view arguments
			$arrDefaultView  = array(
				'ret_object' => false,			# Returns an iQuery Object instead of an array
				'return_sql' => false,			# If the current request should return a recirset or the SQL
				'columns'	 =>	'*',			# The columns to be selected, can be an array as well
				'filter'	 =>	array(),		# Filter to use in the where clause (ex: id=1) 
				'filter_unescaped'	=>	array(),# Filter to use in the where clause but its unescaped, useful for unescaping one of many filter values
				'operator'	 =>	array(),		# The operator to use in the filtering, ex: array('=', '>') :: First param will be id=1 second id > 1 (mapped with the filter value)
				'limit'		 =>	false,			# Max amount of rows
				'orderBy'	 => 'a.id',			# Order by value
				'direction'  => 'DESC',			# Filtering direction ASC/DESC
				'groupBy'	 =>	NULL,			# Group by data,
				'escapeData' =>	true,			# Escape filter data.
				'inner_join' =>	array(),		# Inner join query array
				'left_join'  =>	array(),		# Left join query array
				'having'  	 => array(),			# Filtering using HAVING claus
				'debug'		 =>	false,			# DEBUG true/false
				'forceClass' =>	false,			# Force the class | Used in emulation
				'cacheQuery' =>	false,			# Cache the query true/false
				'cacheTime'  =>	'+30 minute',	# Query cache length
				'search_type'=>	'OR',			# search type: [AND | OR]
				'search'	 =>	array()			# search columns: array({column name} => {search keyword}) - The difference 
												# between search and filter, is that search will perform a regexp filter
												# for example searching "Test"  will match "Test, Testing, Tested" etc...		
			);
			
			// Merge the arguments
			$arrViewParams = array_merge(
				(array) $arrDefaultView,
				(array) $arrView
			);			
			
			$strMappedClassName = (((bool) $arrViewParams['forceClass']) ? strtolower($arrViewParams['forceClass']) : strtolower(get_called_class()));
			
			
			//
			// EOF - Get from cache
			//
			
			$objDb = DATABASE::getInstance();
			
			// Add the columns
			$strQueryColumns = $arrViewParams['columns'];
			if (is_array($arrViewParams['columns'])) 
			{
				$strQueryColumns = '';
				$intFirst = true;
				foreach ($arrViewParams['columns'] as $intIndex => $strColumnName)
				{
					$strQueryColumns .= ($intFirst ? $strColumnName : ', ' . $strColumnName);		
					$intFirst = false;
				}
			}
			
			$strViewSql = 	"SELECT SQL_CALC_FOUND_ROWS SQL_NO_CACHE " .  $strQueryColumns . " FROM " . $strMappedClassName .  " a "; 
			/*.
 				$strViewSql = 	"SELECT SQL_CALC_FOUND_ROWS SQL_NO_CACHE " .  $strQueryColumns . " FROM " . $strMappedClassName .  " a " .
								(count($arrViewParams['inner_join']) ? ' INNER JOIN ' .implode(" INNER JOIN ", $arrViewParams['inner_join']) : '') . " " . 
								(count($arrViewParams['left_join'])  ? ' LEFT JOIN ' .implode(" LEFT JOIN ", $arrViewParams['left_join']) : '') .  " " .
								"WHERE 1=1 ";
			*/
			
			// Add the inner joins joins
			array_walk($arrViewParams['inner_join'], function($strInnerJoinClause, $strTable) use(&$strViewSql) {
				// Backwards compatibility:
				// if the inner_join join array is passed directly as teh array value
				// Ex inner_join = array('table_a ta = on ta.id = tb.tableId')
				// Instead of inner_join = array('table_a ta' => 'ta.id = tb.tableId') 
				$strViewSql .= (
					(TRUE === is_string($strTable)) ?
					(' INNER JOIN ' . $strTable . ' ON (' . $strInnerJoinClause . ')') :
					(' INNER JOIN ' . $strInnerJoinClause)
				);
			}); 
			
			// Add the left joins
			array_walk($arrViewParams['left_join'], function($strLeftJoinClause, $strTable) use(&$strViewSql) {
				// Backwards compatibility:
				// if the left join array is passed directly as teh array value
				// Ex left_join = array('table_a ta = on ta.id = tb.tableId')
				// Instead of left_join('table_a ta' => 'ta.id = tb.tableId') 
			   	$strViewSql .= (
			   		(true === is_string($strTable)) ? 
			   		(' LEFT JOIN ' . $strTable . ' ON (' . $strLeftJoinClause . ')') : 
			   		(' LEFT JOIN ' . $strLeftJoinClause )
			   	);
			}); 
			
			// Add the 'Where' Clause!
			$strViewSql .= " WHERE 1=1 ";
			
			// Add the filter
			$intCount = ((int) sizeof($arrViewParams['filter']));		
			$intIndex = 0;
			foreach ($arrViewParams['filter'] as $strColumn => $mxValue) {
				if ($intCount > 0) {
					$strViewSql .= " AND ";
				}
				$strViewSql .= 	$strColumn . (isset($arrViewParams['operator'][$intIndex]) ? " " . $arrViewParams['operator'][$intIndex] . " ": " = ");
				$strViewSql .= 	(is_numeric($mxValue) ? $mxValue : ($arrViewParams['escapeData'] ? "'" . $objDb->escape($mxValue) . "' " : $mxValue . " "));
				--$intCount;
				++$intIndex;
			}
			
			// Add the unescaped filter exception
			$intCount = ((int) sizeof($arrViewParams['filter_unescaped']));		
			// $intIndex = 0; <-- We do not reset the intIndex because we need a continuance for the operator array since $arrViewParams['filter'] comes first
			foreach ($arrViewParams['filter_unescaped'] as $strColumn => $mxValue) {
				if ($intCount > 0) {
					$strViewSql .= " AND ";
				}
				$strViewSql .= 	$strColumn . (isset($arrViewParams['operator'][$intIndex]) ? " " . $arrViewParams['operator'][$intIndex] . " ": " = ");
				$strViewSql .= 	(is_numeric($mxValue) ? $mxValue : $mxValue);
				--$intCount;
				++$intIndex;
			}
			
			// Add the search by keyword
			$strViewSql    .= ((FALSE === empty($arrViewParams['search'])) ? ' AND (' : '');
			$intCount 		= ((int) sizeof($arrViewParams['search']));		
			$strSearchType 	= ($arrViewParams['search_type'] ? (' ' . $arrViewParams['search_type'] . ' ') : ' OR '); 
			while (list($strSearchColumn, $strSearchKeyword) = each($arrViewParams['search']))
			{
				-- $intCount;
				$strViewSql .= '(' . $strSearchColumn . " REGEXP '" . $objDb->escape($strSearchKeyword) . "') ";
				$strViewSql .= ($intCount > 0 ? $strSearchType : '');
			}	
			$strViewSql .= (FALSE === empty($arrViewParams['search']) ? ') ' : '');
			
			// Add Group By
			$strViewSql .= 	(strlen($arrViewParams['groupBy']) ? " GROUP BY " . $arrViewParams['groupBy'] . " " : "");
			
			// Add Having
			$strViewSql .= 	(false === empty($arrViewParams['having']) ? " HAVING " . implode(' AND ', $arrViewParams['having']) . " " : "");
			
			// Add Order By
			$strViewSql .= 	(strlen($arrViewParams['orderBy']) ? " ORDER BY " . $arrViewParams['orderBy'] . (strlen($arrViewParams['direction']) ? " " . $arrViewParams['direction'] . " " : "") : "");
			
			// Add Limit
			$strViewSql .= 	((strlen($arrViewParams['limit']) && ((string) trim($arrViewParams['limit']) !== '0')) ? " LIMIT " . $arrViewParams['limit'] : "");
			
			// Debug
			if ((bool) $arrViewParams['debug']) {
				new dump($strViewSql);
				die;
			}
			
			//
			// Get from cache
			//	
			/*
			$_blnMemcacheEnabled = false;
			$strCacheKey = NULL;
			if (
				(false === $arrViewParams['debug']) &&
				(true === $blnIsCachable)
			) {
				$_objMemcache = MEMCACHE_MANAGER::getInstance();	
				if (
					(true === is_object($_objMemcache)) &&
					(true === $_objMemcache->isServerOnline())
				) {
					$_blnMemcacheEnabled 	= true;
					$intKeyVersionNumber 	= $_objMemcache->getKeyVersionNumber();
					$strCacheKey 			= 'OBJECT_CLASS_VIEW[' . $intKeyVersionNumber . '][' . strtoupper($strMappedClassName) . '][' . base64_encode($strViewSql) . ']';	
					$arrCachedData			= $_objMemcache->get($strCacheKey);
					if (false === empty($arrCachedData)) {
						return ($arrCachedData);	
					}
				}
			}
			
			
			//
			// Save in cache
			//
			if (
				(false === $arrViewParams['debug']) &&
				(true === $blnIsCachable) &&
				(true === $_blnMemcacheEnabled)
			) {
				SITE_EXCEPTION::supressException();
				$arrData = $objDb->query($strViewSql, $arrViewParams['cacheQuery'], $arrViewParams['cacheTime']);
				$_objMemcache->set($strCacheKey, $arrData);
				SITE_EXCEPTION::clearExceptionSupress();
				
				return ($arrData);
			}
			else
			{
				// Return the view or SQL 
				return(
					(TRUE === $arrViewParams['return_sql']) ? ($strViewSql) : 
					(
						(TRUE === $arrViewParams['ret_object']) ? 
						$objDb->iQuery($strViewSql, $arrViewParams['cacheQuery'], $arrViewParams['cacheTime']) : 
						$objDb->query($strViewSql, $arrViewParams['cacheQuery'], $arrViewParams['cacheTime'])
					)
				);
			}
			*/
			
			return(
				(TRUE === $arrViewParams['return_sql']) ? ($strViewSql) : 
				(
					(TRUE === $arrViewParams['ret_object']) ? 
					$objDb->iQuery($strViewSql, $arrViewParams['cacheQuery'], $arrViewParams['cacheTime']) : 
					$objDb->query($strViewSql, $arrViewParams['cacheQuery'], $arrViewParams['cacheTime'])
				)
			);
		}
		
		
		
		/**
		 * @DEPRECIATED: Please remove this function when no other objects use it
		 * This method returns an object view
		 * @param:	$arrView [Array] 			- View parameters
		 * @param:	$arrmappedOperators [Array] - View parameters operators, to control selection [=, >, <] etc...
		 * @param:	$intLimit [Integer] 		- The limit amount of records to return [0 returns all records]
		 * @param:	$strOrderBy [String] 		- The order by column name
		 * @param:	$strAscDesc [String] 		- The order by column direction [ASC, DESC]
		 * @return: $arrObjectView - [Array] 	- The returned object view
		 */
		public static function getObjectView(
			$arrView			= array(), 
			$arrMappedOperators = array(),
			$intLimit			= 0,
			$strOrderBy			= 'id',
			$strAscDesc			= 'DESC'
		) {
			return(self::getObjectClassView(
				array(
					'columns'	=>	'*',			
					'filter'	=>	$arrView,		
					'operator'	=>	$arrMappedOperators,
					'limit'		=>	$intLimit,		
					'orderBy'	=> 	$strOrderBy,	
					'direction'	=> 	'DESC',			
					'groupBy'	=>	'id'	
				)
			));
		}
		
		public function getConstructIntegrity() {
			return ($this->constructIntegrity);
		}
		
		public function getInstanceType() { 
			return ($this->strCurrentLoadType); 
		}
		
		protected function getCalledClass() {
			$strCalledClass = (isset($this->strCalledClass) ? $this->strCalledClass : get_called_class());
			return ($strCalledClass); 
		}
		
		protected function getInstanceKeyArray() {
			return ($this->strInstanceKeyArray);
		}
		
		public function getObjectCacheType() {
			return ($this->intObjectCacheType);
		}
		
		public function getDefaultMapKey() {
			return ($this->defaultMapKey);
		}
		
		/**
		 * Setters
		 */
		public function setDefaultMapKey($strKey = SHARED_OBJECT::SHARED_OBJECT_DEFAULT_MAP_KEY) {
			$this->defaultMapKey = $strKey;
		}
		
		public function setConstructIntegrity($blnConstructInetgrity = SHARED_OBJECT::SHARED_OBJECT_STRICT_INSTANCE) {
			$this->constructIntegrity = (bool) $blnConstructInetgrity;
		}
		
		public function setObjectCacheType($intCacheType = SHARED_OBJECT::SHARED_OBJECT_CACHE_FILE) {
			$this->intObjectCacheType = (int) $intCacheType;
		}
		
		public function setInstanceKeyArray($arrKeys = array()) {
			$this->strInstanceKeyArray = (array) $arrKeys;	
		}
		
		protected function setNonUpdateableColumn($strColumn = NULL) {
			if ((strlen($strColumn)) && (is_string($strColumn)))
				$this->arrNonUpdateFields[$strColumn] = true;
		}
		
		protected function setCanSaveObject($blnCanSave=NULL) {
			$this->blnCanSave = (bool) $blnCanSave;	
		}
		
		protected function setClassPath($strClassPath=NULL) {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments); 
			$strRealClassPackage 	 = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(realpath($strClassPath))) . DIRECTORY_SEPARATOR; // Class package path
			$strRealClassPackage	 = (
				substr($strRealClassPackage, 0, 1) == DIRECTORY_SEPARATOR ? 
				substr($strRealClassPackage, 1, strlen($strRealClassPackage)) : 
				$strRealClassPackage
			); // Remode trailing '/' from t he begining of the package name to prevent class names like ::CLASSES:: ...
			$strClassPackage 		 = strtoupper(str_replace(DIRECTORY_SEPARATOR, "::", $strRealClassPackage)); // replace with package seperators
			$strClassPackage		.= get_class($this); 			// add the constructor name.
			$strClassKey			 = $strClassPackage;
			$strClassPackage		.= '[' . $this->getVariable('id') . ']'; 	// add the object ID.
			$this->className		 = get_class($this);
			$this->strClassId 		 = $strClassPackage;
			$this->strClassKey		 = $strClassKey;
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		public function loadFromArray(array $arrInfo=array()) {
			$arrArguments = func_get_args();
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$this->intId 	= (array_key_exists('id', $arrInfo) ? (int) $arrInfo['id'] : 0) ;
			$this->arrInfo 	= (array) $arrInfo;
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		/**
		 * This function will load an object from array, same as loadFromArray but it
		 * Will validate that the fields in the array passed are valid for the object.
		 * @param: Array $arrInfo - The array info
		 * @return: Void
		 */
		public function loadFromDirtyArray(array $arrInfo=array()) {
			$arrArguments = func_get_args();
			$arrValidFields = $this->getFields();
			$arrCleanData = array();
			foreach($arrValidFields as $intIndex => $arrFieldInfo) {
				if (isset($arrInfo[$arrFieldInfo['Field']])) {
					$arrCleanData[$arrFieldInfo['Field']] = $arrInfo[$arrFieldInfo['Field']];
				}
			}
			if (sizeof($arrCleanData)) {
				$this->loadFromArray($arrCleanData);	
			}
			$this->__beforeCallback(__FUNCTION__, $arrArguments);
			$this->__callback(__FUNCTION__, $arrArguments);
		}
		
		/**
		 * This method force refreshes an object,
		 * This method is useful should data change
		 * manually in the database
		 * @access 	protected
		 * @param 	none
		 * @return 	void
		 */
		protected  function refresh() {
			if ($this->getId()) {
				$objDb 		= DATABASE::getInstance(); 	
				$arrInfo 	= array_shift($objDb->query($this->strObjectInstanceSql)); 
				$this->loadFromArray($arrInfo);
				/*
				$this->clearCache();
				$this->setInstanceKeyArray(array('id' => $this->getId()));
				$this->setCalledClass($this->getClassName($this));
				$this->_getInstance();
				*/
			}
		}
		
		protected function __callback ($strAction, $arrArgs) {
			if (method_exists($this, 'on' . ucwords($strAction))) {
				call_user_func_array(array($this, 'on' . ucwords($strAction)), (array) $arrArgs);	
			}
		}
		
		protected function __beforeCallback ($strAction, $arrArgs) {
			if (method_exists($this, 'onBefore' . ucwords($strAction))) {
				call_user_func_array(array($this, 'onBefore' . ucwords($strAction)), (array) $arrArgs);	
			}
		}
		
		protected function isObjectCanSave() {
			return ((bool) $this->blnCanSave);	
		}
		
		
		protected function setInstanceType($strLoadType = SHARED_OBJECT::SHARED_OBJECT_NEW_INSTANCE) {
			$this->strCurrentLoadType = $strLoadType; 
		}
		
		public function setCalledClass($strCalledClass = __CLASS__) {
			$this->strCalledClass = $strCalledClass; 
			$this->setDatabaseTargetClass($strCalledClass); // set the database transaction table as well.
		}
		
		/**
		 * This method maps a certain object to a different table name, where all database
		 * transaction should be routed to, For example, if t class demo_class_s{} need to be affiliated
		 * with table demo_table_b, then the $this->setCalledClass('DEMO_TABLE_B', $this);
		 * would be called to ensure that all queries are ran agains the demo_table_b table
		 * 
		 * @access 	public
		 * @param 	string 	$strCalledClass 	The new class name to map
		 * @param	object	$objTargetObject	The class thats affiliated with the new class name
		 * @return 	void
		 */
		public function setDatabaseTargetClass($strCalledClass = __CLASS__) 
		{
			$this->class_name = strtolower($strCalledClass);
		}
		
		/**
		 * Error Reporting (SILENT)
		 */
		 
		/*
		 public static function SHARED_OBJECT_ERROR($e, $strMessage = NULL) 
		 {
			// We'll use the mail_queue object to prevent lague in load time 
			SITE_EXCEPTION_SILENT_ERROR($e, $strMessage);
			return (true);
		 }
		
		 private static function supressFromException() 
		 {
			SITE_EXCEPTION::supressException();
		 }
		 
		 private static function clearExceptionSupression() 
		 {
			SITE_EXCEPTION::clearExceptionSupress();
		 }
		 */
		 
		 
		 /*	 
		 public static function SHARED_OBJECT_ERROR($e, $strMessage = NULL) 
		 {
			
			// We'll use the mail_queue object to prevent lague in load time 
			SITE_EXCEPTION_SILENT_ERROR($e, $strMessage);
			return (true);
		 }
		
		 private static function supressFromException() {
			set_error_handler('SHARED_OBJECT_RECOVERABLE_ERROR');
			set_exception_handler('SHARED_OBJECT_RECOVERABLE_ERROR');
		 }
		 
		 private static function clearExceptionSupression() {
			restore_error_handler();
			restore_exception_handler();
		 }
		 */
		 
		/**
		 * Absstract methods
		 **/
		 abstract protected 	function  getClassPath();
	}
	
	/******************************** 
	 * Retro-support of get_called_class() 
	 * Tested and works in PHP 5.2.4 
	 * http://www.sol1.com.au/ 
	 ********************************/ 	
	if(!function_exists('get_called_class')) { 
		function get_called_class($bt = false,$l = 1) { 
			if (!$bt) $bt = debug_backtrace();
			if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep."); 
			if (!isset($bt[$l]['type'])) { 
				throw new Exception ('type not set'); 
			} 
			else switch ($bt[$l]['type']) { 
				case '::': 
					$lines = file($bt[$l]['file']); 
					$i = 0; 
					$callerLine = ''; 
					do { 
						$i++; 
						$callerLine = $lines[$bt[$l]['line']-$i] . $callerLine; 
					} while (stripos($callerLine,$bt[$l]['function']) === false); 
					
					preg_match(
						'/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', 
						$callerLine, 
						$matches
					); 
					
					if (!isset($matches[1])) { 
					
						/**
						 * This segment will fix an issue with PHP 5.2.17 where the backtrace returned gets convoluted
						 * The edge cast returns a -> instead of a static call ::
						 */
						preg_match(
							'/([a-zA-Z0-9\_]+)->'.$bt[$l]['function'].'/', 
							$callerLine, 
							$matches
						); 
						if (!isset($matches[1])) { 
							// must be an edge case. 
							throw new Exception ("Could not find caller class: originating method call is obscured."); 
						}
						// must be an edge case. 
						throw new Exception ("Could not find caller class: originating method call is obscured."); 
					} 
					switch ($matches[1]) { 
						case 'self': 
						case 'parent': 
							return get_called_class($bt,$l+1); 
						default: 
							return $matches[1]; 
					} 
					// won't get here. 
					case '->': switch ($bt[$l]['function']) { 
						case '__get': 
							// edge case -> get class of calling object 
							if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object."); 
							return get_class($bt[$l]['object']); 
						default: return $bt[$l]['class']; 
					} 
		
				default: throw new Exception ("Unknown backtrace method type"); 
			} 
		} 
	} 
	
	// SHARED OBJECT CATCHABLE ERRORS
	/*
	function SHARED_OBJECT_RECOVERABLE_ERROR() {
		$arrArguments = func_get_args();
		SHARED_OBJECT::SHARED_OBJECT_ERROR($arrArguments);
		return (true);
	}
	*/	

	/**
	 * CLASS Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 /*
	 	CREATE TABLE `class_registry` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `className` varchar(250) NOT NULL,
		  `description` varchar(600) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `idk_classKey` (`className`)
		) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
	 */
	
	class CLASS_REGISTRY extends SHARED_OBJECT 
	{
		protected function getClassPath()  	 { return (__FILE__); }
	}