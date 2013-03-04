<?php
	/**
	 * PATIENT_PROFILES_CATEGORY Administration Class
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
	 TABLE DEFINITION: 
		 CREATE TABLE `patient_profiles_category` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `patient_profiles_category_id` int(11) NOT NULL DEFAULT '0',
		  `patient_profiles_id` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `idx_patientProfileId_categoryId` (`patient_profiles_category_id`,`patient_profiles_id`) USING BTREE
		) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;
	*/
	class PATIENT_PROFILES_CATEGORY extends SHARED_OBJECT 
	{
		/**
			Abstraction Methods
		**/
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>