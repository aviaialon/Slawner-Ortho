<?php
/**
 * PDO_DATABASE_SINGLETON Class File
 * This is the base PDO database management class. 
 *
 * @category   PHP5
 * @package    DATABASE
 * @subpackage {APPLICATION_CORE}
 * @author     Avi Aialon <aviaialon@gmail.com>
 * @copyright  2012 DeviantLogic. Inc. All rights reserved
 * @license    http://www.deviantlogic.ca/license
 * @version    SVN: $Id: pdo_database.php 290967 2011-10-19 21:46:23Z crinu $
 * @link       SVN: $HeadURL: svn+ssh://ubuntu.dns05.com/var/www/svn-repositories/platform $
 * @since      2012-04-18
 */

/**
 * PDO_DATABASE Management Class 
 *
 * @category   PHP5
 * @package    DATABASE
 * @subpackage APPLICATION_CORE
 * @author     Avi Aialon <aviaialon@gmail.com>
 */
abstract class PDO_DATABASE_SINGLETON_INTERFACE extends OBJECT_BASE 	
{
	/**
	 * Database encoding types 
	 *
	 * @var String
	 */ 
	const DATABASE_ENCODING_UTF8		= 'utf8';
	const DATABASE_ENCODING_CP1251		= 'cp1251';
	const DATABASE_ENCODING_KOI8R		= 'koi8r';
	
	/**
	 * Main database connection
	 *
	 * @var 	PDO RESOURCE
	 */
	protected static $_PDO_OLEDB_CONNECTION = NULL;
	
	/**
	 * This method is loads the PDO connection and creates the connection string, it then
	 * initiates and launches the persistent connection between pdo and the database
	 *
	 * @access 	protected, static, final
	 * @param	array $arrRconnectionsConfig - Connection info array
	 * @return 	PDO DATABASE RESOURCE
	 */
	protected static final function ___connect (array $arrRconnectionsConfig = array())
	{
			
		/// TO USE
		/*
			http://php.net/manual/fr/book.pdo.php - transactions
			http://www.php.net/manual/en/pdo.rollback.php - transactions
			http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
			http://php.net/manual/en/pdo.setattribute.php
			http://php.net/manual/en/pdo.quote.php
			
			http://php.net/manual/en/pdo.connections.php
			http://stackoverflow.com/questions/1742066/why-is-pdo-better-for-escaping-mysql-queries-querystrings-than-mysql-real-escape
			
		*/	
		
		// Set the default configurations
		if (TRUE === empty($arrRconnectionsConfig))
		{
			$arrRconnectionsConfig = array
			(
				'driver'	=>		constant('__DATABASE_DRIVER__'),
				'host'		=>		constant('__DATABASE_HOST__'),
				'port'		=>		constant('__DATABASE_PORT__'),
				'dbname'	=>		constant('__DATABASE__'),
				'username'	=>		constant('__DATABASE_UNAME__'),
				'password'	=>		constant('__DATABASE_PASS__')
			);
		}
		
		// Validate the driver
		if (TRUE === empty($arrRconnectionsConfig['driver']))	
		{
			throw new EXCEPTION('PDO CONNECTION ERROR: Please provide a valid database driver.');  		
		}
		
		// Validate the host
		if (TRUE === empty($arrRconnectionsConfig['host']))	
		{
			throw new EXCEPTION('PDO CONNECTION ERROR: Please provide a connection host.');  		
		}
		
		// Validate the database name
		if (TRUE === empty($arrRconnectionsConfig['dbname']))	
		{
			throw new EXCEPTION('PDO CONNECTION ERROR: Please provide a database name.');  		
		}
		
		// Build the connection string
		$objPdoDbInstance = parent::$_OBJECT_INSTANCE;
		$objPdoDbInstance->addToConnectionString($arrRconnectionsConfig['driver'] . ':');
		$objPdoDbInstance->addToConnectionString($objPdoDbInstance->getVariable('host', $arrRconnectionsConfig));
		$objPdoDbInstance->addToConnectionString($objPdoDbInstance->getVariable('port', $arrRconnectionsConfig));
		$objPdoDbInstance->addToConnectionString($objPdoDbInstance->getVariable('dbname', $arrRconnectionsConfig));
		$objPdoDbInstance->setDatabaseUserName($objPdoDbInstance->getVariable('username', $arrRconnectionsConfig));
		$objPdoDbInstance->setDatabasePassword($objPdoDbInstance->getVariable('password', $arrRconnectionsConfig));
		
		// Connect to the database
		try 
		{ 
			// Connect to the database
			self::$_PDO_OLEDB_CONNECTION = new PDO (
				$objPdoDbInstance->getConnectionString(), 
				$objPdoDbInstance->getDatabaseUserName(), 
				$objPdoDbInstance->getDatabasePassword(), 
				array( 
					PDO::ATTR_PERSISTENT 				 => TRUE,
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY	 => TRUE
				)
			); 
			
			// Delete Sensitive Data
			$objPdoDbInstance->deleteDatabaseUserName();
			$objPdoDbInstance->deleteDatabasePassword();
			
			// Configure the database connection
			$objPdoDbInstance->getDatabaseConnection()->exec("SET CHARACTER SET " . 			self::DATABASE_ENCODING_UTF8);
			$objPdoDbInstance->getDatabaseConnection()->setAttribute(PDO::ATTR_CASE, 			PDO::CASE_NATURAL);
			$objPdoDbInstance->getDatabaseConnection()->setAttribute(PDO::ATTR_ERRMODE, 		PDO::ERRMODE_EXCEPTION);
			$objPdoDbInstance->getDatabaseConnection()->setAttribute(PDO::ATTR_ORACLE_NULLS , 	PDO::NULL_NATURAL);
		}
		catch(PDOException $e) 
		{  
			throw new EXCEPTION('PDO CONNECTION ERROR: ' . $e->getMessage());  
		}  
		
		return ($objPdoDbInstance->getDatabaseConnection());
	}
	
	
	 
	/**
	 * This method returns the current database connection
	 *
	 *	@access 	protected, final
	 * @param		none
	 * @return		RESOURCE PDO
	 */
	 protected final function getDatabaseConnection() 
	 {
		return (self::$_PDO_OLEDB_CONNECTION);  
	 }
}