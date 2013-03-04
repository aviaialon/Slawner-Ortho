<?php
	/**
	 * SHOUT_ENGINE_LANG Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::SHOUT_ENGINE::SHOUT_ENGINE_LANG
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
 class SHOUT_ENGINE_LANG {
	private $ShoutCloud_Lang=array();
	public function __construct($strLang='en') {
		###### LANGUAGE CONFIGURATION #################################
		# If you need to change aspects of ShoutCloud to match your   #
		# spoken language, you may do so in this file.                #
		###############################################################
		
		### ADMIN MESSAGES ############################################
		$this->ShoutCloud_Lang['en']['Admin-Error-BanList']       = '<strong>You must be an Administrator to view the ban list.</strong>';
		$this->ShoutCloud_Lang['en']['Admin-Error-NoBans']        = '<em>There are no bans in the list...</em>';
		$this->ShoutCloud_Lang['en']['Admin-Error-Clear']         = '<strong>You must be an Administrator to clear messages.</strong>';
		$this->ShoutCloud_Lang['en']['Admin-Error-InvalidUser']   = 'Incorrect Username and/or Password!';
		$this->ShoutCloud_Lang['en']['Admin-Error-Admin']         = 'You are not an Administrator!';
		$this->ShoutCloud_Lang['en']['Admin-Error-Cannot-Delete'] = 'Unable to delete that message. Please try again.';
		$this->ShoutCloud_Lang['en']['Admin-Clear-Description']   = 'Are you sure you want to clear all of the messages?';
		
		### USER ERRORS ##############################################
		$this->ShoutCloud_Lang['en']['Error-Invalid-Name']        = 'You cannot use that name!';
		$this->ShoutCloud_Lang['en']['Error-Name-Length']         = 'Your name is too long!';
		$this->ShoutCloud_Lang['en']['Error-Msg-Length']          = 'Your message is too long! Limit 500 characters.';
		$this->ShoutCloud_Lang['en']['Error-Empty-Name']          = 'Please enter your name!';
		$this->ShoutCloud_Lang['en']['Error-Empty-Msg']           = 'Please enter a message!';
		$this->ShoutCloud_Lang['en']['Error-Banned']              = 'You are banned from this ShoutBox.';
		$this->ShoutCloud_Lang['en']['Error-Msg-Flood']           = 'Please do not spam the messages! Wait 5 seconds in between posts.';
		$this->ShoutCloud_Lang['en']['Error-Cannot-Post']         = 'Your message could not be posted at this time.';
		$this->ShoutCloud_Lang['en']['Error-Not-Logged-In']       = 'Please login to shout!';
		$this->ShoutCloud_Lang['en']['Logged-In-or-Register']     = 'Login and start SHOUTING today!';
		
		### TITLES ###################################################
		$this->ShoutCloud_Lang['en']['BanList-Title']             = 'Ban List';
		$this->ShoutCloud_Lang['en']['Clear-Title']               = 'Clear All Messages';
		$this->ShoutCloud_Lang['en']['BanUser-Title']             = 'Ban';
		
		### MISC #####################################################
		$this->ShoutCloud_Lang['en']['BanList-Expires-Text']      = 'Expires';
		$this->ShoutCloud_Lang['en']['BanList-Expires-In']        = 'in';
		$this->ShoutCloud_Lang['en']['BanList-Expires-Never']     = 'never';
		$this->ShoutCloud_Lang['en']['BanList-Remove-Ban']        = 'Remove Ban';
		$this->ShoutCloud_Lang['en']['Clear-All-Msgs']            = 'Clear All Messages';
		$this->ShoutCloud_Lang['en']['Choose-Color-Text']         = 'Choose Color';
		$this->ShoutCloud_Lang['en']['Characters-Text']           = 'characters';
		$this->ShoutCloud_Lang['en']['Shout-Btn']                 = 'Shout!';
		$this->ShoutCloud_Lang['en']['Admin-Btn-Shout']           = 'Shout';
		$this->ShoutCloud_Lang['en']['Admin-Btn-Bans']            = 'Bans';
		$this->ShoutCloud_Lang['en']['Admin-Btn-ClearAll']        = 'Clear All';
		$this->ShoutCloud_Lang['en']['Admin-Btn-Logout']          = 'Logout';
		$this->ShoutCloud_Lang['en']['System-Default-Name']       = 'Admin';
		$this->ShoutCloud_Lang['en']['System-Default-Msg']        = 'Type [!help] for more information.';
		$this->ShoutCloud_Lang['en']['BanUser-1min']              = '1 Min';
		$this->ShoutCloud_Lang['en']['BanUser-10mins']            = '10 Mins';
		$this->ShoutCloud_Lang['en']['BanUser-1hour']             = '1 Hour';
		$this->ShoutCloud_Lang['en']['BanUser-1day']              = '1 Day';
		$this->ShoutCloud_Lang['en']['BanUser-Forever']           = 'Forever';
		$this->ShoutCloud_Lang['en']['Shout-Delete-Btn']          = 'Delete';
		$this->ShoutCloud_Lang['en']['Shout-Reply-Btn']           = 'Reply';
		$this->ShoutCloud_Lang['en']['Admin-Open-User-Msg']       = 'Open Admin User Control';
		$this->ShoutCloud_Lang['en']['Reply-To-Msg']              = 'Reply to';
	}
	/**
	 * This method returns the language definition for the shout engine
	 * @access: static
	 * @param: $strLang String {default: en} - The language selection
	 * @return $arrLang Array - The language definition
	 */
	public static function getDefinition($strLang='en') {
		$objLang = new SHOUT_ENGINE_LANG();
		return (isset($objLang->ShoutCloud_Lang[$strLang]) ? $objLang->ShoutCloud_Lang[$strLang] : array());
	}
 }