<?php
	/**
	 * NEWS_TAG Administration Class
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
	  * TABLE DEFINITION
	  
	  CREATE TABLE `news_tag` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `newsId` int(11) NOT NULL,
		  `tag` varchar(250) NOT NULL,
		  `lang` varchar(2) NOT NULL DEFAULT 'en',
		  PRIMARY KEY (`id`),
		  KEY `idx_newsId_lang` (`newsId`,`lang`) USING BTREE
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	  */
	  
	 class NEWS_TAG extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
		protected function onBefore_getInstance() {
			$this->setObjectCacheType(SHARED_OBJECT::SHARED_OBJECT_CACHE_NONE); // No Cache!
		}
	}
?>