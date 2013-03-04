<?php
	/**
	 * PATIENT_PROFILES_COMMENTS Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS
	 * @subpackage	PATIENT_PROFILES
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
	  * 
	  * 
	  	CREATE TABLE `patient_profiles_comments` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `patient_profiles_id` int(11) NOT NULL,
		  `name` varchar(550) NOT NULL DEFAULT '',
		  `commentParentId` int(11) NOT NULL DEFAULT '0',
		  `post_date` varchar(250) NOT NULL,
		  `timedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `active` tinyint(1) NOT NULL DEFAULT '1',
		  `email_hash` varchar(600) DEFAULT NULL,
		  `email` varchar(300) NOT NULL,
		  `subject` varchar(500) NOT NULL DEFAULT '',
		  `comment` text,
		  `userIp` varchar(500) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `idx_newsId_ActiveStatus` (`patient_profiles_id`,`active`),
		  KEY `idx_parentId` (`commentParentId`)
		) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

	  * 
	  */
	 class PATIENT_PROFILES_COMMENTS extends SHARED_OBJECT 
	 {
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>