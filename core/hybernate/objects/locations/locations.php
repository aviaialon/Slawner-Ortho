<?php
/**
 * LOCATIONS Administration Class
 * This class represents the CRUD [Hybernate] behaviors implemented 
 * with the Hybernate framework 
 *
 * @package		CLASSES::HYBERNATE::OBJECTS::PAYMENT
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
 CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `address` varchar(500) NOT NULL,
  `lat` decimal(8,5) NOT NULL DEFAULT '0.00000',
  `lng` decimal(8,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
 */
 class LOCATIONS extends SHARED_OBJECT 
 {
	 
	public function __construct() {  }
	
	/**
		Abstraction Methods
	**/
	protected function getClassPath()  	 { return (__FILE__); }
}