<?php
	@ob_end_flush();
	@ob_flush();
	
	/**
	 * SERVICE_PROXY Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 * 
	 * This class represents the http service proxy. It binds the request
	 * during the service period and 'pushed' data to the standard out as the
	 * data changes.
	 * 
	 * The service proxy will auto timeout after a preset amount of time or 1 minute.
	 * 
	 * Example Usage:
	 * --------------------------------------------------------------
	 * 
	 * 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::HTTP::SERVICE_PROXY");
	 * 	$objServiceProxy = SERVICE_PROXY::getInstanceFromKey(array(
	 *			'clientId'	=> 	100562
	 * 	), 		SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
	 * 
	 * 	$objServiceProxy->configure(SERVICE_PROXY::PARAM_CONFIGURATION_TIMEOUT_SECS, 90);
	 * 	$objServiceProxy->configure(
	 *	  	SERVICE_PROXY::PARAM_CONFIGURATION_REQUEST_TYPE, $strServiceType
	 * 	);
	 * 	$objServiceProxy->run();
	 *
	 * @package		CLASSES::HYBERNATE::HTTP::SERVICE_PROXY
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 * Table Defition:
	 	CREATE TABLE `service_proxy` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `data` blob,
		  `received` tinyint(1) NOT NULL DEFAULT '0',
		  `timeDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `forceClose` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `clientId` int(11) NOT NULL DEFAULT '0',
		  `terminalId` int(11) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `idx_clientId_terminalId` (`clientId`,`terminalId`)
		) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1
	 */	
	
	class SERVICE_PROXY extends SHARED_OBJECT {
		/**
		 * These constants define the service type requested
		 * 
		 * @var string
		 */
		const SERVICE_PROXY_REQUEST_TYPE_SERVICE 	= 'service'; 	// Service proxy
		const SERVICE_PROXY_REQUEST_TYPE_STATUS 	= 'status'; 	// Alias to response
		const SERVICE_PROXY_REQUEST_TYPE_RESPONSE 	= 'response'; 	// Returns only the response
		const SERVICE_PROXY_REQUEST_TYPE_TERMINATE	= 'terminate';	// Alias to close connection
		const SERVICE_PROXY_REQUEST_TYPE_CLOSE 		= 'close';		// Closes the current connection
		
		/**
		 * These constants define the PUBLIC configuration variables types
		 * Example: $objServiceProxy->configure(SERVICE_PROXY::PARAM_CONFIGURATION_REQUEST_TYPE, SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_SERVICE);
		 * Which is equivalent to $objServiceProxy->configure('serviceProxyRequestType', 'service');  
		 * 
		 * @var String
		 */
		const PARAM_CONFIGURATION_REQUEST_TYPE			= 'serviceProxyRequestType';
		const PARAM_CONFIGURATION_SHOW_FIRST_OUTPUT		= 'blnShowFirstOutput';
		const PARAM_CONFIGURATION_SLEEP_TIMEOUT_SECS	= 'intSleepRequestSeconds';
		const PARAM_CONFIGURATION_TIMEOUT_SECS			= 'intTimeOutSeconds';
		
		const PARAM_CONFIGURATION_IS_INITIALISED		= 'blnIsInitialized';
		const PARAM_CONFIGURATION_CURRENT_EXEC_TIME		= 'intCurrentExecutionTime';
		
		
		/**
		 * This array holds the configuraton data for the service proxy
		 * @var array
		 */
		protected $arrConfigData = array
		(	
			/**
			 * PUBLIC variables 
			 */
		
			// This variable will determine if we flush the first output @var: blnShowFirstOutput
			SERVICE_PROXY::PARAM_CONFIGURATION_SHOW_FIRST_OUTPUT 	=>	true,
			
			// This variable holds the type of service requested @var: serviceProxyRequestType
			SERVICE_PROXY::PARAM_CONFIGURATION_REQUEST_TYPE			=> SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_SERVICE,
			
			// The amount of seconds to sleep between requests @var: intSleepRequestSeconds
			SERVICE_PROXY::PARAM_CONFIGURATION_SLEEP_TIMEOUT_SECS 	=> 1, 	
			
			// The amount of seconds to throw a execution timeout @var: intTimeOutSeconds
			SERVICE_PROXY::PARAM_CONFIGURATION_TIMEOUT_SECS 		=> 120, 	
			
			
			/**
			 * PRIVATE variables
			 */	
			
			// This variable holds the initialized state of the object @var: blnIsInitialized
			SERVICE_PROXY::PARAM_CONFIGURATION_IS_INITIALISED 		=> false, 	
			
			// This variable keeps track of executon time amount with NO activity @var: intCurrentExecutionTime
			SERVICE_PROXY::PARAM_CONFIGURATION_CURRENT_EXEC_TIME 	=> null
		); 
		
		/**
		 * Class constructor:: it'll configure defaults
		 * @return SERVICE_PROXY
		 */
		public final function __construct()
		{
			if (! $this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_IS_INITIALISED)) 
			{
				// Initialize the object if we have a valid request
				$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_CURRENT_EXEC_TIME, time());
				$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_SLEEP_TIMEOUT_SECS, 1);
				$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_TIMEOUT_SECS, 120);
				$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_IS_INITIALISED, true);
			}
		}
		
		/**
		 * 
		 * Sets config data for the SERVICE_PROXY
		 * @access public final
		 * @param String $strConfigName - The config name
		 * @param mixed $mxConfigData - the config data
		 * @return void
		 */
		public final function configure($strConfigName, $mxConfigData = NULL) 
		{
			$this->arrConfigData[$strConfigName] = $mxConfigData;
		}
		
		/**
		 * 
		 * Returns a config data
		 * @access public final
		 * @param String $strConfig - The config name, if NULL, returns all config.
		 * @return mixed = the config valud
		 */
		public final function getConfig($strConfig = NULL) 
		{
			return ($this->getVariable($strConfig, $this->arrConfigData));
		}
		
		/**
		 * This is the main service method, it locks the proxy 
		 * and runs until completion.
		 * @access public, final
		 * @param none
		 * @return void
		 */
		public final function run() 
		{
			switch ($this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_REQUEST_TYPE))
			{
				case SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_SERVICE : 
					{
						/**
						 * Here, were looking for service. 
						 * So... run!
						 */
						$this->execute();
						break;
					}
					
				case SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_STATUS	:	
				case SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_RESPONSE : 
					{
						/**
						 * Here, were just looking for the status, so output it
						 * We also dont need to call the refresh() method.
						 * because this is running on a non shared thread
						 */
							echo $this->getData();
						break;
					}
					
				case SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_TERMINATE : 
				case SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_CLOSE : 
					{
						/**
						 * Here, the user agent requested to terminate 
						 * the current active connection
						 */
							$this->setForceClose(1);
							$this->save();
						break;
					}	
				default :
					{
						/**
						 * Default service is treated as a 
						 * SERVICE_PROXY::SERVICE_PROXY_REQUEST_TYPE_SERVICE
						 * service request type.
						 */
						// $this->execute();
						echo "Please select a service type.";
					}
			}
		}
		
		/**
		 * This is the internal service method, it locks the proxy 
		 * and runs until completion.
		 * @access public, final
		 * @param none
		 * @return Boolean - If the proxy is complete
		 */
		protected final function execute() 
		{
			@ob_end_flush();
			@ob_flush();
	
			$blnContinue = FALSE;
			
			if ($blnContinue = ((bool) $this->getVariable('id')))
			{
				/**
				 * Reset the php internal time limit.
				 */
				set_time_limit(0);
				
				/**
				 * Set to active status
				 */
				 $this->setIsActive(1);
				 $this->save();
				 
				/**
				 * If we have data to display to the receiving 
				 * program, we display it here, we need to reload the 
				 * objects data array since multiple objects can edit it.
				 * so we do a getInstance on myself
				 */
				$this->refresh();
				
				$blnFlushOutpout = (bool) (
					($this->getVariable('received') == FALSE) ||
					($this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_SHOW_FIRST_OUTPUT) == TRUE)
				);
				
				if ($blnFlushOutpout) {
					// Set the first output to true
					// Echo the data to the receiving program
					if ($this->getVariable('received') == FALSE) 
					{
						echo ($this->getVariable('data'));
						
						// Reset the object
						$this->setReceived(1);
						$this->save();
						$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_CURRENT_EXEC_TIME, time());
					}
					else if ($this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_SHOW_FIRST_OUTPUT) == TRUE) 
					{
						echo ('Connection Successful. [Thread: ' . $this->getId() . ']');
					}
					
					
					$this->configure(SERVICE_PROXY::PARAM_CONFIGURATION_SHOW_FIRST_OUTPUT, FALSE);

					// Output the buffer to continue.
					@ob_flush();
			        @flush();
			        @ob_end_flush();  
				    @ob_start();
				} 
				else 
				{
					ob_clean();
				}
				
				/**
				 * Next, we check for idle timeout
				 */
				$blnContinue = (
					(time() - $this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_CURRENT_EXEC_TIME)) < 
					($this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_TIMEOUT_SECS))
				);
				
				if (! $blnContinue)
				{
					echo 'Idle Time. Closing Connection.';	
				}
				
				/**
				 * Next, we check for 'Forced Closed' proxy termination
				 */
				$blnContinue &= (! $this->getVariable('forceClose'));
				
				/**
				 * Sleep between requests to not overload the server
				 */
				if ($blnContinue) 
				{
			    	sleep((int) $this->getConfig(SERVICE_PROXY::PARAM_CONFIGURATION_SLEEP_TIMEOUT_SECS));
			    }
			    /**
			     * And.... recurse!
			     */
			     return ($blnContinue ? $this->run() : $blnContinue);
			}
		}
		
		/**
			Abstraction Methods
			see: SHARED_OBJECTS
		**/
		protected function onBefore_getInstance() 
		{
			$this->setConstructIntegrity(SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);	
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE);
		}
		
		/**
		 * This method will override the setVariable parent function to keep a history of
		 * the data transfer or object changes 
		 * 
		 * @var $strKey    String - The key of the member variable to set
		 * @var $strValue  String - The key of the member value to set
		 * @return void
		 * 
		 * (non-PHPdoc)
		 * @see SHARED_OBJECT::setVariable()
		 */
		public function setVariable($strKey=NULL, $strValue=NULL) 
		{
			if (
				($this->getId()) &&
				($this->getVariable($strKey) != $strValue)
			) {
				// Save the history
				SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::HTTP::SERVICE_PROXY_HISTORY");	
				$objUser = APPLICATION::getInstance()->getUser();
				$objServiceProxyHistory = SERVICE_PROXY_HISTORY::newInstance();
				$objServiceProxyHistory->setServiceProxyId($this->getId());
				$objServiceProxyHistory->setSiteUserId($objUser->getId());
				$objServiceProxyHistory->setTimeDate(now());
				$objServiceProxyHistory->setVarName($strKey);
				$objServiceProxyHistory->setOldData($this->getVariable($strKey));
				$objServiceProxyHistory->setNewData($strValue);
				// $objServiceProxyHistory->setRawData(serialize($this->getVariable()));
				$objServiceProxyHistory->save();
			}
			
			parent::setVariable($strKey, $strValue);
		}
		
		/**
		 * (non-PHPdoc)
		 * @see SHARED_OBJECT::getClassPath()
		 */
		protected function getClassPath()  	 { return (__FILE__); }
	}