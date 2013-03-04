<?php
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::EXCEPTION::SITE_EXCEPTION");	
	class OAUTH_TWITTER extends SITE_EXCEPTION {
		public function __construct() {}
		public static function loadLibrary() {
			/**
			 * Added 
			 */
			SHARED_OBJECT::requireLibrary(__APPLICATION_CLASS_REAL_PATH__ . "::oauth::twitter::EpiCurl");
			SHARED_OBJECT::requireLibrary(__APPLICATION_CLASS_REAL_PATH__ . "::oauth::twitter::EpiOAuth");
			SHARED_OBJECT::requireLibrary(__APPLICATION_CLASS_REAL_PATH__ . "::oauth::twitter::EpiTwitter");
		}
	}
?>