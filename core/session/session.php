<?php
/**
 * Session Class for storing session data in mysql
 * 
 * Features :
 * This class help you to Store Data on mysql server.
 * 
 * How to use :
 *  Create your database in MySQL, and create a table in which
 *  to store your session information.  The example code below
 *  uses a table called "session".  Here is the SQL command
 *  which created it:
 * 
 *  CREATE TABLE sessions (id varchar(32) NOT NULL,access
 *  int(10) unsigned,data text,PRIMARY KEY (id));


 * 
 * @version 0.1 20100602
 * 
 	 * Session Administration Class
	 * This class represents the CRUD behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	
	EXAMPLE:
	--------	
	require_once "session.php";
	$oSession = new Session();
	print_r($_SESSION); // First
	$_SESSION['hi'] = "Hello"; // Comment this Once sessoin is set
	$_SESSION['test'] = "great"; // Comment this Once sessoin is set

 */ 

class SESSION /* implements SessionHandlerInterface */ {
	protected $objDb = false;
	protected $savePath;
    protected $sessionName;

    public function __construct(){
		SESSION::collectGarbage();
		if (
			(! session_id()) ||	
			(FALSE === isset($_SESSION))
		) { 
			#ini_set('session.save_handler', 'user');
			session_set_save_handler(
				array(&$this, 'open'),
				array(&$this, 'close'),
				array(&$this, 'read'),
				array(&$this, 'write'),
				array(&$this, 'destroy'),
				array(&$this, 'clean')
			);
			register_shutdown_function('session_write_close');
			session_name(constant('__SESSION_NAME__'));
			session_cache_expire(((int) constant('__SESSION_EXPIRATION_SECONDS__')) / 60);
			session_start();	
		}
		
		return ($_SESSION);
    }
	
	public function open($savePath, $sessionName) {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        return true;
    }
	
    public function write($id, $data) {
    	$this->objDb = self::getDatabaseObject();	
    	
        $access = time();
        $id 	= $this->objDb->escape($id);
        $access = $this->objDb->escape($access);
        $data 	= $this->objDb->escape($data);
		
		$exData	 = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "") . " | ";
		$exData	.= (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : "") . " | ";
		$exData .= (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "");
		
		$ipAddr  = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "");
		$intSiteUserId = (isset($_SESSION['USER_ID']) ? (int) $_SESSION['USER_ID'] : 0); 		
		
		// Write the data to session
        $sql 	= "REPLACE INTO sessions VALUES  ('$id', '$access', '$data', '" . $exData . "', '" . $ipAddr . "', " . (int) $intSiteUserId . ")";
        
        $this->objDb->query($sql);
        
        // reset database default config once done
		$this->objDb->loadDefaultConfig();
		
		return(true);
		//return mysql_query($sql, $this->mysql) or die(mysql_error());
    }

    public function read($id) {
    	$this->objDb = self::getDatabaseObject();	
    	
        $id 	= $this->objDb->escape($id);
        $sql 	= "SELECT data FROM  sessions WHERE  id = '{$id}'";
		
		/*
        if ($result = mysql_query($sql, $this->mysql)) {
            if (mysql_num_rows($result)) {
                $record = mysql_fetch_assoc($result);
                return $record['data'];
            }
        }
        return '';
		*/
		$result = $this->objDb->query($sql);
		if (sizeof($result)) {
			return $result[0]['data'];
		}
		
		// reset database default config once done
		$this->objDb->loadDefaultConfig();
		
		return '';
    }
	
    public function destroy($id) {
    	$this->objDb = self::getDatabaseObject();	
    	
        $id 	= $this->objDb->escape($id);
        $sql 	= "DELETE FROM sessions WHERE  id = '" . $id . "'";
		$this->objDb->query($sql);
		
		// reset database default config once done
		$this->objDb->loadDefaultConfig();
		
		return(true);
    }
	
    public function clean($max) {
    	$this->objDb = self::getDatabaseObject();	
    	
        $old = time() - $max;
        $old = $this->objDb->escape($old);
        $sql = "DELETE FROM   sessions WHERE  access < '$old'";
        //return mysql_query($sql, $this->mysql);
		$this->objDb->query($sql);
		
		// reset database default config once done
		$this->objDb->loadDefaultConfig();
		
		return(true);
    }

    public function close() {
        //mysql_close($this->mysql);
		//$this->objDb->close();
		session_write_close();
		return true;
    }
	
	public function set($strName, $mxValue) {
		$_SESSION[$strName] = $mxValue;
	}
	
	public function get($strName) {
		return (isset($_SESSION[$strName]) ? $_SESSION[$strName] : false);
	}
	
	public function remove($strName = NULL) {
		if (
			(! is_null($strName)) &&
			(isset($_SESSION[$strName]))
		) {
			unset($_SESSION[$strName]);
		}
	}
	
	public static function collectGarbage() {
		// This methid is called in a cron job (every 15 minutes) to clean old sessions
		$objDb = self::getDatabaseObject();	
		$objDb->query("DELETE FROM sessions WHERE UNIX_TIMESTAMP(NOW()) - sessions.access >= " . __SESSION_EXPIRATION_SECONDS__);
		
		// reset database default config once done
		$objDb->loadDefaultConfig();
		
	}
	
	/**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public function gc($max) {
		$objDb = self::getDatabaseObject();	
        $sql = sprintf("DELETE FROM `sessions` WHERE `timestamp` < '%s'", $objDb->escape(time() - $max));
		$objDb->query($sql);
        return true;
    }
	
	public static function getInstance() {
		return (self::getSession());
	}
	
	public static function getSession() {
		$strSessionClassName = __CLASS__;
		$objSession = new $strSessionClassName();	
		return ($objSession);
	}
	
	public static function destroySession() {
		$objSession = SESSION::getSession();
		$objSession->destroy(session_id());
		unset($_SESSION);
		session_unset();
		session_destroy();	
	}
	
	public static function closeWrite() {
		if (session_id())	
			session_write_close();
	}
	
	public static function getId() {
		return (session_id());
	}
	
	public static function getName() {
		return (session_name());
	}
	
	/**
	 * Returns an instance of the database object
	 * 
	 * @access protected static final
	 * @return DATABASE::DATABASE::instance 
	 */
	protected static final function getDatabaseObject()
	{
		$objDb = DATABASE::getInstance();	
		
		if (
			(true === defined('__ROOT_DATABASE_UNAME__')) 	&&
			(true === defined('__ROOT_DATABASE_PASS__')) 	&&
			(strlen(constant('__ROOT_DATABASE_UNAME__')))	&&
			(strlen(constant('__ROOT_DATABASE_PASS__')))	
		) {
			$objDb->setConfig($arrDabaseData = array(
				'database' 	=> constant('__DATABASE__'),
				'host'		=> constant('__DATABASE_HOST__'),
				'port'		=> constant('__DATABASE_PORT__'),
				'username'	=> constant('__ROOT_DATABASE_UNAME__'),
				'password'	=> constant('__ROOT_DATABASE_PASS__')
			));
		}
		
		return ($objDb);
	}
}
