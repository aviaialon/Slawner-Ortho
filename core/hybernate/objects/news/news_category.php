<?php
	/**
	 * NEWS_CATEGORY Administration Class
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
	 TABLE DEFINITION: 
	 CREATE TABLE `news_category` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `news_category_id` int(11) NOT NULL DEFAULT '0',
	  `news_id` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `idx_newsid_categoryId` (`news_category_id`,`news_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1
	 */
	 class NEWS_CATEGORY extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>