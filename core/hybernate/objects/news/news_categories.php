<?php
	/**
	 * NEWS_CATEGORIES Administration Class
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
		CREATE TABLE `news_categories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(500) NOT NULL,
		  `lang` varchar(2) NOT NULL DEFAULT 'en',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
	 */
	 class NEWS_CATEGORIES extends SHARED_OBJECT {
		 
		public 	  function __construct() {  }
		
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>