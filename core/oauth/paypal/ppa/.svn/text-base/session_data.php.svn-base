<?php
SESSION::getInstance();
class Session_Data {

	static function isValidSession() {
		$respObj = array( 'valid' => isset( $_SESSION['ppa_data'] ) );
		return json_encode( $respObj );
	}

	static function getBasicAttributes() {
		$openIdAx = isset( $_SESSION['ppa_data']) ?  $_SESSION['ppa_data'] : NULL;	
		$attributes = array();
		if (! empty($openIdAx))
		{
			$attributes = array( 'firstName' => $openIdAx['attributes']['http://axschema.org/namePerson/first'], 'lastName' => $openIdAx['attributes']['http://axschema.org/namePerson/last'], 'email' => $openIdAx['attributes']['http://axschema.org/contact/email'] );
		}
		$respObj = array( 'valid' => isset($_SESSION['ppa_data']), 'attributes' => $attributes );	
		return json_encode( $respObj );
	}


}

?>	