<?php 
	/*
	 * Created on 2010-02-15
	 * This class represents a database object.
	 * @package: DATABASE
	 */
	require_once(__APPLICATION_ROOT__ . '/exception/site_exception.php');	
	require_once(__APPLICATION_ROOT__ . '/utility-functions.php');	 
	
 	class DATABASE /* extends SITE_EXCEPTION */ {
 		private static $DATABASE_INSTANCE	= NULL;
 		private static $GS_DEFAULT_DATABASE = '';
 		private static $ARR_RECORDSET_ARRRAY= array();
 		private static $RS_RECORDSET 		= null;
 		private static $STR_DATABASE 		= null;
 		private static $SQL 				= null;
 		private static $IS_CONNECTED 		= false;
 		private static $OBJ_DBCONNECTION	= false;
 		private static $CACHE_QUERY			= false;
		private static $SLOW_QUERY_SECONDS	= 2;
 		private static $CACHE_TIMESPAN		= '+30 minute';
 		private static $INT_POSITION 		= -1;
 		private static $INT_RECORDCOUNT 	= -1;
 		private static $INT_EXECUTIONTIME 	= -1;
		private static $CACHE_STATUS		= -1;
 		private static $CONFIG 				= array();
		
 		const DATABASE_ENCODING_UTF8		= 'utf8';
 		const DATABASE_ENCODING_CP1251		= 'cp1251';
 		const DATABASE_ENCODING_KOI8R		= 'koi8r';
 		
 		
		const CACHED_STATUS_NONE			= 0;
		const CACHED_STATUS_CACHED			= 1;
		
 		/**
 		 * GS_DATABASE Class constructor
 		 */
 		public function DATABASE($strDatabase="") {
 			// Enforce the singleton pattern, since the instance should be null when called by getInstance
 			if (is_null(DATABASE::$DATABASE_INSTANCE)) { 
	 			self::resetObject();
				self::loadConfig();
				/**
				 * @depreciated
				 */ 
	 			switch (trim(strtolower($strDatabase))) 
	 			{
	 				default	: 
	 					{ 
	 						self::setDatabase(self::getDefaultDatabase()); 
	 						break; 
	 					}		 	
	 			} 
 			}
 			//return ($this->getDatabase()); 
 			return ($this);
 		}
 		/**
		 * @access: STATIC
 		 * @scope: 	PUBLIC - This function returns a new instance
 		 * @return: DATABASE - The instantiated object
 		 */
		 public static function getInstance() {
		 	if (is_null(DATABASE::$DATABASE_INSTANCE)) {
		 		DATABASE::$DATABASE_INSTANCE = new DATABASE();
				//DATABASE::setEncoding();
		 	}
		 	return (DATABASE::$DATABASE_INSTANCE);
		 }
		 
		 /**
		  * This method resets the database object to the default configuration set
		  * 
		  * @package CORE::DATABASE
		  * @access	 public static final
		  * @return  void
		  */
		 public static final function loadDefaultConfig()
		 {
		 	self::loadConfig();
		 }
		 
 		/**
 		 * PUBLIC - This function executes a query.
 		 * @return: array - The query results in array.
 		 */
 		public static function query($strSql="", $blnCacheQuery = false, $strCacheTime = '+30 minute') {
			self::iQuery($strSql, $blnCacheQuery, $strCacheTime);
 			return (self::getRecordSetArray());
 		}
		
		public static function iQuery($strSql="", $blnCacheQuery = false, $strCacheTime = '+30 minute') {
 			if (strlen($strSql)) {
				$objDb = DATABASE::getInstance();
				$objDb->setCacheQuery((bool) $blnCacheQuery);
				$objDb->setCacheTimeSpan($strCacheTime);
				$objDb->setCacheStatus(DATABASE::CACHED_STATUS_NONE);
				$objDb->setSql($strSql);
				
				if (! ((bool) $objDb->readFromCache())) {
					$objDb->connect();
					$intStartTime 	= getMicroTime(); /*microtime()*/	
					if (! ($rsQueryRecordSet = mysql_query($strSql, self::getDatabaseConnection()))) 
					{
						throw new Exception(
							"<b>FATAL ERROR [@QUERY]: </b><span style='color:red'>"	 . 
							mysql_error() . print_r(debug_backtrace()) . "</span>"	 .
							"<br /><br/><b>SQL:<br /><pre>" . $strSql . "</pre></b>"
						);
					}
					$intEndTime 	= getMicroTime(); /*microtime()*/		
					self::setExecutionTime($intEndTime - $intStartTime);		
					
					/**
					 * Slow Query Check
					 */
					$fltExec = round(self::getExecutionTime(), 4);
					if ($fltExec > self::getSlowQuerySeconds()) {
						$dtEnd 			= date('Y-m-d H:i:s', $intEndTime);
						$strFilePath 	= str_replace(',', '\,', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
						$intPid 		= getmypid();
						$arrProcess 	= posix_getpwuid(posix_geteuid());
						$arrBacktrace	= debug_backtrace();
						$arrBacktrace	= (sizeof($arrBacktrace) ? end($arrBacktrace) : array());
						
						
						$logFilePath 	= realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'slow-query.log';
						$rsFile 		= fopen($logFilePath, 'a+');
						
						$strLogData		= '';		
						$strLogData	   .= 'File: ' 	 		 . (isset($arrBacktrace['file']) 	 ? $arrBacktrace['file'] 	 : 'UNKNOWN') . "\n";
						$strLogData	   .= 'Line: ' 	 		 . (isset($arrBacktrace['line']) 	 ? $arrBacktrace['line'] 	 : 'UNKNOWN') . "\n";
						$strLogData	   .= 'Class: ' 		 . (isset($arrBacktrace['class']) 	 ? $arrBacktrace['class'] 	 : 'UNKNOWN') . "\n";
						$strLogData	   .= 'Function: ' 		 . (isset($arrBacktrace['function']) ? $arrBacktrace['function'] : 'UNKNOWN') . "\n";
						
						
						$strLogData	   .= 'php_uname: ' 	 . php_uname('n') . "\n";
						$strLogData	   .= 'arrProcess: ' 	 . print_r($arrProcess, true) . "\n";
						$strLogData	   .='strFilePath: ' 	 . $strFilePath . "\n";
						$strLogData	   .= 'start: ' 		 . date('Y-m-d H:i:s', $intStartTime) . "\n";
						$strLogData	   .= 'End: ' 			 . date('Y-m-d H:i:s', $intEndTime) . "\n";
						$strLogData	   .= 'Execution Time: ' . $fltExec . "\n";
						$strLogData	   .= 'Max Time: ' 		 . self::getSlowQuerySeconds() . "\n";
						$strLogData	   .= 'SQL: ' 			 . $strSql . "\n";
						$strLogData	   .= '---------------------------------------------------------------------------------------'  . "\n";
						
						fwrite($rsFile, $strLogData);
						fclose($rsFile);
					}
					/**
					 * Slow Query Check EOF
					 */
					
								
					$objDb->setRecordSet($rsQueryRecordSet);		
					$objDb->setRecordSetArray($objDb->queryToArray($objDb->getRecordSet()));
					$objDb->setRecordCount(count($objDb->getRecordSetArray()));	
					$objDb->disconnect();
					$objDb->cacheQuery();
				}
 			}
 			return ($objDb);
 		}
 		
	 	/**
	     * Set encoding for the database connection (Default: UTF-8)
	     * 
 		 * @access public static
 		 * @param  string $strEncodingValue - Encoding value
 		 * @return void
 		 */
 		public static function setEncoding($strEncodingValue = NULL) 
	    {
	        self::iQuery('SET NAMES '.  (is_null($strEncodingValue) ? DATABASE::DATABASE_ENCODING_UTF8 : $strEncodingValue) . ';');
	    }
 		
		/**
 		 * PUBLIC - This function returns the result of a CALC_FOUND_ROWS operation
 		 * @return: integer - The found_rows() results
 		 */
		 public function getFoundRows() {
			$arrFoundRows = $this->query('SELECT FOUND_ROWS() as totalRows;');	 
			$intFoundRows = 0;
			if (isset($arrFoundRows[0]['totalRows'])) {
				$intFoundRows = (int) $arrFoundRows[0]['totalRows'];	
			}
			return ($intFoundRows);
		 }
		 
		/**
 		 * PRIVATE - This method returns a cached recordset
 		 * @return: $blnCached - Boolean - If the query is cached.
 		 */
		 private function readFromCache() {
			$blnCached = false;
			// The read from cache needs to always try to
			// find the cached query
			$blnContinue = true; //((bool) self::getCacheQuery());
			if (
				($blnContinue) &&	
				(! is_null(self::getSql()))	
			) {
				$strHashKey 	= sha1(trim(self::getSql()));
				$strFilePath 	= dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cached_queries' . DIRECTORY_SEPARATOR . $strHashKey . '.objectCache';
	
				if (
					(file_exists($strFilePath)) &&
					(include($strFilePath)) 	&&
					(isset($cachedObjectsRs))	&&
					(isset($cachedObjectsRs['expire']))	&&
					(strtotime(date("Y-m-d H:i:s")) <= $cachedObjectsRs['expire'])
				) { 
					// Query is cached, return it	
					$this->setCacheStatus(DATABASE::CACHED_STATUS_CACHED);
					$this->setExecutionTime($cachedObjectsRs['executionTime']);
					$this->setRecordSetArray($cachedObjectsRs['recordSet']);
					$this->setRecordCount($cachedObjectsRs['recordCount']);
					$this->setSql($cachedObjectsRs['sql']);	
					$blnCached = true;
				}
			}
			return ($blnCached);
		 }
		 
		 /**
 		 * PUBLIC - This method releases the recorset cache
 		 * @return: $blnCached - Boolean - If the query was released successfully.
 		 */
		 public function releaseCache() {
			$strHashKey 	= sha1(trim(self::getSql()));
			$strFilePath 	= dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cached_queries' . DIRECTORY_SEPARATOR . $strHashKey . '.objectCache';
			$blnReturn 		= true;
			if (file_exists($strFilePath)) {
				$blnReturn = @unlink($strFilePath);	
			}
			return ((bool) $blnReturn);
		 }
		 
		 /**
 		 * PRIVATE - This method caches a recordset
 		 * @return: $blnCached - Boolean - If the query is cached.
 		 */
		 private function cacheQuery() {
			$blnCached = false;
			$blnContinue = ((bool) self::getCacheQuery());
			if (
				($blnContinue) &&	
				(! is_null(self::getSql()))	
			) {
				$strHashKey = sha1(trim(self::getSql()));
				$strFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cached_queries' . DIRECTORY_SEPARATOR . $strHashKey . '.objectCache';
				$arrData = array(
					'expire'			=> strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " " . self::getCacheTimeSpan()),								 
					'sql' 				=> self::getSql(),
					'recordCount' 		=> self::getRecordCount(),
					'executionTime'		=> self::getExecutionTime(),
					'recordSet'			=> self::getRecordSetArray()
				);
				
				try {
					$fh 		= fopen($strFilePath, 'w');
					$blnCached 	= fwrite($fh, '<?php $cachedObjectsRs = ' . var_export($arrData, true) . '; ?>');
					if ($blnCached) {
						chmod($strFilePath, 0644);
					}
					fclose($fh);	
				} catch(Exception $e) { }
			}
			return ($blnCached);
		 }
		
 		/**
 		 * PUBLIC - This method returns the column list for the query.
 		 * @return: array -Query column list.
 		 */
 		public static function getColumns() {
 			$arrClumnList = array();
 			if ((bool) self::getRecordCount()) {
 				$arrFirstRow = self::fetchOneRow(1);
 				foreach ($arrFirstRow as $intStrKey => $strValue) {
 					if (is_string($intStrKey)) {
 						$arrClumnList[] = $intStrKey;
 					}
 				}
 				//$arrClumnList = array_keys(self::getRecordSetArray());
 			}
 			return($arrClumnList);
 		}
 		
		
		/**
 		 * PUBLIC - This method returns the column list for a selected table
		 * @param:	string 	[Required] - The table name 
 		 * @return: array 	- Query column list.
 		 */
		 final public static function getColumnsFromTable($strTableName = NULL) {
			 $arrColumns = array();
			 if (! is_null($strTableName)) {
				 $arrColumns = self::query('SHOW COLUMNS FROM ' . $strTableName . '; ');
			 }
			 return ($arrColumns);
		 }
		
		
 		/**
 		 * PUBLIC - This method returns a row from the query.
 		 * @param: int [$intRow] - The row number to fetch.
 		 * @return array - The array query row.
 		 */
 		public static function fetchOneRow($intRow=0) {
 			$intRow				= (intval($intRow)); 
 			$arrQueryRow 		= array();
 			$intRecordCount 	= self::getRecordCount();
 			// Remove 1 to the requested row since queries start from 1, and arrays start from 0.
 			if (
				((bool) $intRecordCount) && 
				($intRow > -1) && 
				(($intRow + 1) <= $intRecordCount)
			) {
 				$arrAllRecords 	= self::getRecordSetArray();
 				$arrQueryRow 	= $arrAllRecords[(int) $intRow];	
 			}
 			return ($arrQueryRow);
 		}
 		
 		/**
 		 * PUBLIC - This method returns one value from a query
 		 * @param: string [$strColumn] - The column value fetch.
 		 * @param: int [$intRow] - The row number to fetch.
 		 * @return string - The returned value.
 		 */
 		public static function fetchOneValue($strColumn = NULL, $intRow = 0) {
 			$strReturn			= false;
 			$intRow				= (intval($intRow)); 
 			$arrQueryRow 		= self::fetchOneRow($intRow);
 			if (
 				(count($arrQueryRow)) &&
 				(! is_null($strColumn))
 			) {
 				$strReturn = (isset($arrQueryRow[$strColumn]) ? $arrQueryRow[$strColumn] : false);	
 			}
 			return ($strReturn);
 		}
 		
		/**
 		 * PUBLIC - This method returns a range of rows from the query.
 		 * @param: int [$intStartRange] - The row start number to fetch.
 		 * @param: int [$intEndRange] - The row end number to fetch.
 		 * @return array - The array query row range.
 		 */
 		public static function fetchRange($intStartRange=1, $intEndRange=0) {
 			if ((self::getRecordCount()) && 
				((bool) is_resource(self::getRecordSet())) &&
				((bool) intval($intEndRange)) &&
				((bool) intval($intStartRange))) {
				 array_slice(self::getRecordSetArray(), $intStartRange, $intEndRange, true);
 			} else return(self::getRecordSetArray());
 		}
 		
		/**
 		 * PUBLIC - This method returns a row from the current query using the current position.
 		 * @return array - The array query row range.
 		 */
		public static function fetch() {
			if (self::getPosition() <= ((int) self::getRecordCount())) {
				$intPos = self::getPosition();
				$arrRow = self::fetchOneRow(self::getPosition());
				self::setPosition($intPos+1);
				return ($arrRow);
			} else return (false);	
				
		}
 		/**
 		 * PUBLIC - This method returns a serialized version of the recordset.
 		 * @return: Serialized String - The seralied record set array.
 		 */
 		public static function serializeRecordSet() {
 			$strSerializedRs = "";
 			if (self::getRecordCount()) {
 				$strSerializedRs = serialize(self::getRecordSetArray());
 			}
 			return($strSerializedRs);
 		}
 		
 		/**
 		 * PUBLIC - This method returns the EOF value for the current recordset.
 		 * @return: boolean - true/false
 		 */
 		public static function isEOF() {
 			return (((bool) (self::getRecordCount() == self::getPosition()) && (self::getRecordCount() + self::getPosition())));
 		}
		
		/**
 		 * PUBLIC - This method seeks through the current recordset and sets the position.
 		 * @return: boolean - true/false
 		 */
		public static function seek($intSeekPos=-1) {
			if ((intval($intSeekPos)) && (intval($intSeekPos) <= self::getRecordCount())) {
				self::setPosition($intSeekPos);	
				return (self::fetchOneRow($intSeekPos));	
			} else if ($intSeekPos = -1) {
				$intSeekPos = self::getPosition();
				self::setPosition($intSeekPos++);
				return (self::fetchOneRow($intSeekPos));	
			}
			return (false);
		}
 		
		/**
 		 * PUBLIC - This method returns the last inserted ID from a table
 		 * @return: boolean - false or last inserted id
 		 */
		public static function lastInsertId($strTableName) {
			if (strlen($strTableName)) {
				if (! self::isConnected()) self::connect();
				$arrQueryResults = self::query("
					SELECT MAX(id) AS last_inserted_id FROM {$strTableName} LIMIT 1			
				");
				return (count($arrQueryResults) ? $arrQueryResults[0]['last_inserted_id'] : NULL);
			}
		}
		
 		public static function escape ($strEscapeVal) {
			if (! self::isConnected()) self::connect();
 			return(mysql_real_escape_string($strEscapeVal, self::getDatabaseConnection()));
 		}
		
		public static function close () {
			self::disconnect();	
		}
		
		public static function save ($strTblName) {
			$arrRecordSet = serialize(self::getRecordSetArray());
			$GLOBALS['recordSets'][$strTblName] = $arrRecordSet;
			return (true);
		}
		
		public static function getSavedRecordSet($strTblName) {
			return ((isset($GLOBALS['recordSets'][$strTblName])) ? unserialize($GLOBALS['recordSets'][$strTblName]) : false);	
		}
		
		public static function insertUpdateFromArray($strTableName, $arrFieldData=array(), $arrOther=array()) {
			$strSql = "";
			$intAction = 0;
			$blnReturn = false; 
			
			// Clean the arrays
			foreach($arrFieldData as $strKey => $strVal) {
				if (is_numeric($strKey)) unset($arrFieldData[$strKey]);	
			}
			foreach($arrOther as $strKey => $strVal) {
				if (is_numeric($strKey)) unset($arrOther[$strKey]);	
			}
		
			if (! count($arrFieldData))		return($blnReturn);
			if (! strlen($strTableName))	return($blnReturn);
			if (
				(array_key_exists('id', $arrFieldData)) &&
				($arrFieldData['id'] > 0)
			) {
				$arrCheck = self::query(
					"SELECT 1 FROM {$strTableName} " . 
					"WHERE id = " . (is_string($arrFieldData['id']) ? "'" . self::escape($arrFieldData['id']) . "'" : (int) $arrFieldData['id']) . " " .
					"LIMIT 1"						
				);
				$intAction = count($arrCheck);
			} else unset($arrFieldData['id']);
			
			switch ((int) $intAction) {
				case 0 : {
						// . "(" . implode(array_keys($arrFieldData), ',') . ")"
						$strSql .= "INSERT INTO " . $strTableName . " (";
						$strSqlKeys = "";
						$strSqlValues = "";
						foreach($arrFieldData as $strColumn => $strValue) {
							if (
								(! array_key_exists($strColumn, $arrOther)) &&
								(strcmp(strtolower($strColumn), 'id') <> 0)
							) {
								$strSqlKeys 	.= "`" . $strColumn . "`,";
								if (! preg_match('/[^0-9]/', $strValue)) {
									$strSqlValues 	.= (string) trim(intval($strValue)) . ",";
								} else {
									$strSqlValues 	.= "'" . self::escape(trim($strValue)) . "',";	
								}
							}
						}
						if (count($arrOther)) {
							foreach($arrOther as $strColumn => $strValue) {
								$strSqlKeys .= "`" . $strColumn .  "`,";
								$strSqlValues .= trim($strValue)  . ",";
							}
							//$strSqlValues = substr($strSqlValues, 0, strlen($strSqlValues) - 1);
						}
						$strSqlKeys = substr($strSqlKeys, 0, strlen($strSqlKeys) - 1) . ")";
						$strSqlValues = substr($strSqlValues, 0, strlen($strSqlValues) - 1);
						$strSql .= $strSqlKeys . " VALUES (" . $strSqlValues . ");";
					break;
				}
				
				case 1 : {
						$strSql .= "UPDATE " . $strTableName . " SET ";
						$blnHasColumn = false;
						foreach($arrFieldData as $strColumn => $strValue) {
							if (
								(! array_key_exists($strColumn, $arrOther)) &&
								(strcmp(strtolower($strColumn), 'id') <> 0)
							) {
								if (! preg_match('/[^0-9]/', $strValue)) {
									$strSql .= $strColumn . "=" . (string) intval($strValue) . ",";
								} else {
									$strSql .= $strColumn . "='" . self::escape($strValue) . "',";
								}
								$blnHasColumn = true;
							}
						}
						$strSql = substr($strSql, 0, strlen($strSql) - 1);
						
						if (count($arrOther)) {
							foreach($arrOther as $strColumn => $strValue) {
								$strSql .= ($blnHasColumn ? ', ' : '') . $strColumn . "=" . $strValue . ",";
							}
							$strSql = substr($strSql, 0, strlen($strSql) - 1);
						}
						$strSql .= " WHERE id = " . (is_string($arrFieldData['id']) ? "'" . self::escape($arrFieldData['id']) . "'" : (int) $arrFieldData['id']);
					break;
				}
			} 
			
			if (strlen($strSql)) self::query($strSql);
			return(true);
		}
		
		public function dbEncrypt($strData = NULL)
		{
			$objDatabase  = DATABASE::getInstance();
			$arrRecordSet = $objDatabase->query("
				SELECT ENCODE('" . $strData . "', '" . __ENCRYPTION_KEY__ . "') AS EncryptedData 							 
			");
			return (count($arrRecordSet) ? $arrRecordSet[0]['EncryptedData'] : NULL);
		}
		
		public function dbDecrypt($strData = NULL)
		{
			$objDatabase  = DATABASE::getInstance();
			$arrRecordSet = $objDatabase->query("
				SELECT DECODE('" . $strData . "', '" . __ENCRYPTION_KEY__ . "') AS EncryptedData 							 
			");
			return (count($arrRecordSet) ? $arrRecordSet[0]['EncryptedData'] : NULL);
		}
		
 		// -------------------------------------
 		//	SETTERS / GETTERS
 		// -------------------------------------
 		
 		private static function setRecordSet($rsRecordSet) 			{ self::$RS_RECORDSET = $rsRecordSet; }
 		public static  function getRecordSet() 						{ return (self::$RS_RECORDSET); }
 		
 		private static function setRecordCount($intRecordCount) 	{ self::$INT_RECORDCOUNT = $intRecordCount; }
 		public static  function getRecordCount() 					{ return ((self::$INT_RECORDCOUNT) ? self::$INT_RECORDCOUNT : 0); }
 		
 		private static function setDatabase($strDatabase) 			{ self::$STR_DATABASE = $strDatabase; }
 		private static function getDatabase() 						{ return (self::$STR_DATABASE); }
 		
 		private static function setDefaultDatabase($strDatabase) 	{ self::$GS_DEFAULT_DATABASE = $strDatabase; }
 		private static function getDefaultDatabase() 				{ return (self::$GS_DEFAULT_DATABASE); }
 		
 		private static function setIsConnected($blIsConnected) 		{ self::$IS_CONNECTED = $blIsConnected; }
 		private static function isConnected() 						{ return (self::$IS_CONNECTED); }
 		
 		private static function getDatabaseConnection()				{ return(self::$OBJ_DBCONNECTION); }
 		private static function setDatabaseConnection($objDbConn) 	{ self::$OBJ_DBCONNECTION = $objDbConn; }
 		
 		private static function setRecordSetArray($arrRecordSet) 	{ self::$ARR_RECORDSET_ARRRAY = $arrRecordSet; }
 		public static  function getRecordSetArray() 				{ return (self::$ARR_RECORDSET_ARRRAY); }
 		
 		private static function setPosition($intPos) 				{ self::$INT_POSITION = $intPos; }
 		public static  function getPosition() 						{ return (self::$INT_POSITION); }
		
 		public static function setConfig($objConfig) 				{ self::$CONFIG = $objConfig; }
		private static function getConfig() 						{ return (self::$CONFIG); }
		
 		private static function setSql($strSql) 					{ self::$SQL = $strSql; }
		public  static function getSql() 							{ return (self::$SQL); }
		
 		private static function setCacheQuery($blnCache) 			{ self::$CACHE_QUERY = (bool) $blnCache; }
		private static function getCacheQuery() 					{ return (self::$CACHE_QUERY); }
		
		private static function setCacheTimeSpan($strSpan) 			{ self::$CACHE_TIMESPAN = (string) $strSpan; }
		private static function getCacheTimeSpan() 					{ return (self::$CACHE_TIMESPAN); }
		
		private static function setExecutionTime($intTime = -1)		{ self::$INT_EXECUTIONTIME = $intTime; }
		public  static function getExecutionTime()					{ return (self::$INT_EXECUTIONTIME); }
		
		private static function setCacheStatus($intCacheStat = -1)	{ self::$CACHE_STATUS = $intCacheStat; }
		public  static function getCacheStatus()					{ return (self::$CACHE_STATUS); }
		
		public 	static function setSlowQuerySeconds($intSeconds = null) { self::$SLOW_QUERY_SECONDS = $intSeconds; }
		public	static function getSlowQuerySeconds()				{ return (self::$SLOW_QUERY_SECONDS); }
		
 		public static function getConfigData($strConfDataKey) 		{ 
			$strConfigDataReturn = "";
			if ((strlen($strConfDataKey)) && (array_key_exists($strConfDataKey, self::getConfig())))
				$strConfigDataReturn = self::$CONFIG[$strConfDataKey];
			return ($strConfigDataReturn);	
		}
 		// -------------------------------------
 		//	PRIVATE FUNCTIONS
 		// -------------------------------------
		
		/**
 		 * PRIVATE - This method loads the configurations from config.php
 		 * @return: void
 		 */
		private static function loadConfig() {
			if ((bool) __HAS_CONFIG__) { 
				self::setConfig(
					array(
						'database' 	=> constant('__DATABASE__'),
						'host'		=> constant('__DATABASE_HOST__'),
						'port'		=> constant('__DATABASE_PORT__'),
						'username'	=> constant('__DATABASE_UNAME__'),
						'password'	=> constant('__DATABASE_PASS__')
					)
				);		
				
				self::setRecordSet(null);
				self::setRecordCount(-1);
				self::setPosition(0);
				self::setDatabase(self::getConfigData('database'));
				self::setDefaultDatabase(self::getConfigData('database'));
				self::setIsConnected(false);
				self::setDatabaseConnection(false);
				self::setRecordSetArray(array());
			}
			return;
		}
		
 		/**
 		 * PRIVATE - This method resets the object.
 		 * @return: void
 		 */
 		private static function resetObject() {
 			self::setRecordSet(null);
 			self::setRecordCount(-1);
			self::setPosition(0);
 			self::setDatabase(self::getDefaultDatabase());
 			self::setDefaultDatabase(self::getDefaultDatabase());
 			self::setIsConnected(false);
 			self::setDatabaseConnection(false);
 			self::setRecordSetArray(array());
 			return;
 		}
 		
 		/**
 		 * PRIVATE - This method returns the database credentials for a connection.
 		 * @return: [array] $arrDbLink - An array containing the credentials.
 		 */
 		private static function getConnection() {
 			$arrDbLink = array();
 			switch (self::getDatabase()) {
				/*
 				case 'whatever' : { 
 					$arrDbLink = array(
 						'HOST' 		=> 	'192.168.1.104',
 						'PORT'		=>	'3306',
 						'USERNAME'	=> 	'root',
 						'PASSWORD'	=>	'merlin',
 						'DATABASE'	=>	'regentvanlines'
 					);
 					break; 
 				}
				*/
 				default	: { 
					/*
 					self::setDatabase($this->getDefaultDatabase()); 
 					$arrDbLink = self::getConnection();
 					break; 
					*/
					$arrDbLink = array(
 						'HOST' 		=> 	self::getConfigData('host'),
 						'PORT'		=>	self::getConfigData('port'),
 						'USERNAME'	=> 	self::getConfigData('username'),
 						'PASSWORD'	=>	self::getConfigData('password'),
 						'DATABASE'	=>	self::getConfigData('database')
 					);
					break;
 				}
 			}   
 			return ($arrDbLink);
 		}
 		
 		/**
 		 * PRIVATE - This method will connect to the database
 		 * @return: [resource] - The mysql database connection. returns false otherwise
 		 */
 		private static function connect() {
 			$arrDbCredentials 	= self::getConnection();
 			$resDbConnection	= false;
 			if ((is_array($arrDbCredentials)) && (count($arrDbCredentials))) {
 				try {
 					//
 					// Connect to the database
 					//
 					$resDhConnection = mysql_pconnect(
 										$arrDbCredentials['HOST'].':'.$arrDbCredentials['PORT'], 
 										$arrDbCredentials['USERNAME'], 
 										$arrDbCredentials['PASSWORD']
 									)
 									 or die("<pre>FATAL ERROR [@CONNECT]: Unable to connect to MySQL -"  . mysql_error());
 									 
					self::setDatabaseConnection($resDhConnection);
					self::setIsConnected(true);	
					 									 
 					//
 					// Select the database...
 					//				 
 					mysql_select_db($arrDbCredentials['DATABASE'], $resDhConnection) 
									or die("<pre>FATAL ERROR [@CONNECT]: Could not select " . $arrDbCredentials['DATABASE']  . ' - ' .mysql_error());
 				} catch (Exception $e) {
 					print('<pre>FATAL ERROR [@CONNECT]: ');
 					new dump($e);
 				}
 			}
 			return (self::isConnected());
 		}
 		
 		/**
 		 * PRIVATE - This method disconects the object from the server
 		 * @return: boolean - True or False;
 		 */
 		private static function disconnect() {
 			if (self::isConnected()) {
 				/*
 				$objRs = self::getRecordSet();
 				if (is_resource($objRs)) {
 					while (list($var, $objQueryRs) = each($objRs)) {
						@mysql_free_result($objQueryRs);
					}
 				}
 				mysql_free_result($objRs);
 				*/
 				if (is_resource(self::getRecordSet()))
 					mysql_free_result(self::getRecordSet());
 					
				mysql_close(self::getDatabaseConnection());
				self::setIsConnected(false);
 			}
 			return ((self::isConnected()) ? false : true);
 		}
 		
 		/**
 		 * PRIVATE - This method will convert the current query to an array
 		 * @return array - The query recordset array.
 		 */
 		private static function queryToArray() {
 			$arrRecordSetArray = array();
 			if (is_resource(self::getRecordSet())) {
 				$rsRecordSet = self::getRecordSet();
 				while($rsRow = mysql_fetch_array($rsRecordSet, MYSQL_ASSOC)){
 					$arrRecordSetArray[] = $rsRow;
				}
 			}
 			return ($arrRecordSetArray);
 		}
 		
 		/**
 		 * Singleton Enforcement Methods
 		 */
 	
		 /**
		  *! @function __clone
		  *  @abstract Prevent cloning of singleton object
		  */
		 public function __clone() 
		 {
		 	SITE_EXCEPTION::raiseException('Cloning of ' . __CLASS__ . ' prohibited. Please use ' . __CLASS__ . '::getInstance()', E_USER_ERROR);
		 }
		
		 /**
		  *! @function __wakeup
		  *  @abstract Prevent deserialization of singleton object
		  */
		 public function __wakeup() 
		 {
		 	SITE_EXCEPTION::raiseException('Deserialization of ' . __CLASS__ . ' prohibited. Please use ' . __CLASS__ . '::getInstance()', E_USER_ERROR);
		 }
		
 		 /**
		  *! @function __sleep
		  *  @abstract Prevent serialization of singleton object
		  */
		 public function __sleep() 
		 {
		 	SITE_EXCEPTION::raiseException('Serialization of of ' . __CLASS__ . ' prohibited. Please use ' . __CLASS__ . '::getInstance()', E_USER_ERROR);
		 }
		 
		 /**
		  *! @function __destruct
		  *  @abstract Release database handle and perform index functionality
		  */
		 public function __destruct()
		 {
		 	self::disconnect();
		 }
 	}