<?php
/**
	* PayPal Access 2011 | ppa_common.php
	* Loading dependencies and defining utility functions
	**/


/**
	* Setup up PHP include path
	**/

$path_extra = dirname(__FILE__);
$path = ini_get( 'include_path' );
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set( 'include_path', $path );
$isConfig = false;

/**
	* Include dependencies
	**/

function doIncludes() {
	/**
		* Require the OpenID consumer code.
		*/
	require_once 'Auth/OpenID/Consumer.php';

	/**
		* Require the "file store" module, which we'll need to store
		* OpenID information.
		*/
	require_once 'Auth/OpenID/FileStore.php';

	/**
		* Require the OpenID attribute exchange API.
		*/
	require_once 'Auth/OpenID/AX.php';

	/**
		* Require the PAPE extension module.
		*/
	require_once 'Auth/OpenID/PAPE.php';


}

doIncludes();


/**
	* Require the openID config.
	**/
if ( file_exists( './ppa_config.php' ) ) {
	require_once './ppa_config.php';
	$GLOBALS['isConfig'] = true;
} else {
	$GLOBALS['isConfig'] = false;
}



/**
	* Utility functions
	**/

function getConfig( $name ) {
	$value = NULL;
	if ( !$GLOBALS['isConfig'] || !defined ( $name ) || empty( $name ) ){
		return $value;
	}
	$value = constant( $name );
	return ( empty( $name ) ? NUll : $value );


}


function getScheme() {
	$scheme = 'http';
	if ( isset( $_SERVER['HTTPS'] ) and $_SERVER['HTTPS'] == 'on' ) {
		$scheme .= 's';
	}
	return $scheme;
}

function getURL( $endURL ) {
	$retUrl = sprintf( "%s://%s:%s%s", getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $_SERVER['SCRIPT_NAME'] );
	$retUrl = preg_replace( '/\/([^\/]*)$/', '', $retUrl );
	return sprintf( "%s/%s", $retUrl, $endURL );
}

function getRealm() {
	return sprintf( "%s://%s:%s/", getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'] );
}

function getPostParameter( $key, $default = NULL ) {
	return isset_or( $_POST[$key], $default );
}

function isset_or( &$check, $alternate = NULL )
{
	return ( isset( $check ) ) ? ( empty( $check ) ? $alternate : $check ) : $alternate;
}

function escape( $thing ) {
	return htmlentities( $thing );
}

function &getStore() {
	/**
		* This is where the example will store its OpenID information.
		* You should change this path if you want the example store to be
		* created elsewhere.  After you're done playing with the example
		* script, you'll have to remove this directory manually.
		*/
	$store_path = null;
	if ( function_exists( 'sys_get_temp_dir' ) ) {
		$store_path = sys_get_temp_dir();
	}
	else {
		if ( strpos( PHP_OS, 'WIN' ) === 0 ) {
			$store_path = $_ENV['TMP'];
			if ( !isset( $store_path ) ) {
				$dir = 'C:\Windows\Temp';
			}
		}
		else {
			$store_path = @$_ENV['TMPDIR'];
			if ( !isset( $store_path ) ) {
				$store_path = '/tmp';
			}
		}
	}
	$store_path .= DIRECTORY_SEPARATOR . 'ppa_store';

	if ( !file_exists( $store_path ) && !mkdir( $store_path ) ) {
		print "Could not create the FileStore directory '$store_path'. ".
			" Please check the effective permissions.";
		exit(0);
	}
	$r = new Auth_OpenID_FileStore( $store_path );

	return $r;
}
?>