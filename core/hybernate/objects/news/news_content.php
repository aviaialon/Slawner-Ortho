<?php
	/**
	 * NEWS_CONTENT Administration Class
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
	 /**
	  * TABLE DEFINITION:
	  
	 	 CREATE TABLE `news_content` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `news_id` int(11) NOT NULL DEFAULT '0',
		  `lang` varchar(2) NOT NULL DEFAULT 'en',
		  `title` varchar(600) NOT NULL,
		  `content` text,
		  PRIMARY KEY (`id`),
		  KEY `idx_newsid_lang` (`news_id`,`lang`) USING BTREE
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	  */	
	 class NEWS_CONTENT extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>