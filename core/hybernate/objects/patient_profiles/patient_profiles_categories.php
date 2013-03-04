<?php
	/**
	 * PATIENT_PROFILES_CATEGORIES Administration Class
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
	 /*
	 	CREATE TABLE `patient_profiles_categories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(500) NOT NULL,
		  `lang` varchar(2) NOT NULL DEFAULT 'en',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;


	 */
	 class PATIENT_PROFILES_CATEGORIES extends SHARED_OBJECT 
	 {
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
