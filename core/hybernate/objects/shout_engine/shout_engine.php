<?php
	/**
	 * SHOUT_ENGINE Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::HYBERNATE::OBJECTS::SHOUT_ENGINE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::SHOUT_ENGINE::SHOUT_ENGINE_LANG");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::SITE_USERS");	
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
	 SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::SESSION::SESSION");
	 
	 class SHOUT_ENGINE extends SHARED_OBJECT {
		// Language definition
		private $lang = array();
		
		// Path to the folder with the smilies in it 	
		private $smiliesFolder 	= '/assets/images/shout-cloud/smilies/'; 
		
		// The configuration for each smilie
		private $arrSmilies 	= array(
			':)' 		=> 'happy.png',
			':D' 		=> 'grin.png',
			'=D' 		=> 'lol.png',
			':O' 		=> 'surprise.png',
			':p' 		=> 'razz.png',
			':(' 		=> 'sad.png',
			':}' 		=> 'kitty.png',
			';)' 		=> 'wink.png',
			':|' 		=> 'neutral.png',
			'(blush)' 	=> 'blush.png',
			':{' 		=> 'confuse.png',
			'B)' 		=> 'cool.png',
			':,(' 		=> 'cry.png',
			'8|' 		=> 'eek.png',
			'(evil)' 	=> 'evil.png',
			'(fat)' 	=> 'fat.png',
			'(green)' 	=> 'green.png',
			':*' 		=> 'kiss.png',
			'(angry)' 	=> 'mad.png',
			'(rolleyes)'=> 'roll.png',
			'(zzz)' 	=> 'sleep.png',
			'(yell)' 	=> 'yell.png',
			'(zipper)' 	=> 'zipper.png',
			'&lt;3' 	=> 'heart.png',
			'&lt;/3' 	=> 'broken-heart.png'
		);
		// Badwords to strip from shouts 
		// TODO: Get from database	
		private $badwords = array(
			'fuck',
			'fucker',
			'shit',
			'piss',
			'cunt',
			'pussy',
			'dick',
			'asshole',
			'bitch',
			'bitches',
			'whore',
			'nigga',
			'nigger',
			'ahole',
			'anus',
			'ash0le',
			'ash0les',
			'asholes',
			'ass',
			'Ass Monkey',
			'Assface',
			'assh0le',
			'assh0lez',
			'asshole',
			'assholes',
			'assholz',
			'asswipe',
			'azzhole',
			'bassterds',
			'bastard',
			'bastards',
			'bastardz',
			'basterds',
			'basterdz',
			'Biatch',
			'bitch',
			'bitches',
			'Blow Job',
			'boffing',
			'butthole',
			'buttwipe',
			'c0ck',
			'c0cks',
			'c0k',
			'Carpet Muncher',
			'cawk',
			'cawks',
			'Clit',
			'cnts',
			'cntz',
			'cock',
			'cockhead',
			'cock-head',
			'cocks',
			'CockSucker',
			'cock-sucker',
			'crap',
			'cum',
			'cunt',
			'cunts',
			'cuntz',
			'dick',
			'dild0',
			'dild0s',
			'dildo',
			'dildos',
			'dilld0',
			'dilld0s',
			'dominatricks',
			'dominatrics',
			'dominatrix',
			'dyke',
			'enema',
			'f u c k',
			'f u c k e r',
			'fag',
			'fag1t',
			'faget',
			'fagg1t',
			'faggit',
			'faggot',
			'fagit',
			'fags',
			'fagz',
			'faig',
			'faigs',
			'fart',
			'flipping the bird',
			'fuck',
			'fucker',
			'fuckin',
			'fucking',
			'fucks',
			'Fudge Packer',
			'fuk',
			'Fukah',
			'Fuken',
			'fuker',
			'Fukin',
			'Fukk',
			'Fukkah',
			'Fukken',
			'Fukker',
			'Fukkin',
			'g00k',
			'gay',
			'gayboy',
			'gaygirl',
			'gays',
			'gayz',
			'God-damned',
			'h00r',
			'h0ar',
			'h0re',
			'hells',
			'hoar',
			'hoor',
			'hoore',
			'jackoff',
			'jap',
			'japs',
			'jerk-off',
			'jisim',
			'jiss',
			'jizm',
			'jizz',
			'knob',
			'knobs',
			'knobz',
			'kunt',
			'kunts',
			'kuntz',
			'Lesbian',
			'Lezzian',
			'Lipshits',
			'Lipshitz',
			'masochist',
			'masokist',
			'massterbait',
			'masstrbait',
			'masstrbate',
			'masterbaiter',
			'masterbate',
			'masterbates',
			'Motha Fucker',
			'Motha Fuker',
			'Motha Fukkah',
			'Motha Fukker',
			'Mother Fucker',
			'Mother Fukah',
			'Mother Fuker',
			'Mother Fukkah',
			'Mother Fukker',
			'mother-fucker',
			'Mutha Fucker',
			'Mutha Fukah',
			'Mutha Fuker',
			'Mutha Fukkah',
			'Mutha Fukker',
			'n1gr',
			'nastt',
			'nigger;',
			'nigur;',
			'niiger;',
			'niigr;',
			'orafis',
			'orgasim;',
			'orgasm',
			'orgasum',
			'oriface',
			'orifice',
			'orifiss',
			'packi',
			'packie',
			'packy',
			'paki',
			'pakie',
			'paky',
			'pecker',
			'peeenus',
			'peeenusss',
			'peenus',
			'peinus',
			'pen1s',
			'penas',
			'penis',
			'penis-breath',
			'penus',
			'penuus',
			'Phuc',
			'Phuck',
			'Phuk',
			'Phuker',
			'Phukker',
			'polac',
			'polack',
			'polak',
			'Poonani',
			'pr1c',
			'pr1ck',
			'pr1k',
			'pusse',
			'pussee',
			'pussy',
			'puuke',
			'puuker',
			'queer',
			'queers',
			'queerz',
			'qweers',
			'qweerz',
			'qweir',
			'recktum',
			'rectum',
			'retard',
			'sadist',
			'scank',
			'schlong',
			'screwing',
			'semen',
			'sex',
			'sexy',
			'Sh!t',
			'sh1t',
			'sh1ter',
			'sh1ts',
			'sh1tter',
			'sh1tz',
			'shit',
			'shits',
			'shitter',
			'Shitty',
			'Shity',
			'shitz',
			'Shyt',
			'Shyte',
			'Shytty',
			'Shyty',
			'skanck',
			'skank',
			'skankee',
			'skankey',
			'skanks',
			'Skanky',
			'slut',
			'sluts',
			'Slutty',
			'slutz',
			'son-of-a-bitch',
			'tit',
			'turd',
			'va1jina',
			'vag1na',
			'vagiina',
			'vagina',
			'vaj1na',
			'vajina',
			'vullva',
			'vulva',
			'w0p',
			'wh00r',
			'wh0re',
			'whore',
			'xrated',
			'xxx',
			'b!+ch',
			'bitch',
			'blowjob',
			'clit',
			'arschloch',
			'fuck',
			'shit',
			'ass',
			'asshole',
			'b!tch',
			'b17ch',
			'b1tch',
			'bastard',
			'bi+ch',
			'boiolas',
			'buceta',
			'c0ck',
			'cawk',
			'chink',
			'cipa',
			'clits',
			'cock',
			'cum',
			'cunt',
			'dildo',
			'dirsa',
			'ejakulate',
			'fatass',
			'fcuk',
			'fuk',
			'fux0r',
			'hoer',
			'hore',
			'jism',
			'kawk',
			'l3itch',
			'l3i+ch',
			'lesbian',
			'masturbate',
			'masterbat*',
			'masterbat3',
			'motherfucker',
			's.o.b.',
			'mofo',
			'nazi',
			'nigga',
			'nigger',
			'nutsack',
			'phuck',
			'pimpis',
			'pusse',
			'pussy',
			'scrotum',
			'sh!t',
			'shemale',
			'shi+',
			'sh!+',
			'slut',
			'smut',
			'teets',
			'tits',
			'boobs',
			'b00bs',
			'teez',
			'testical',
			'testicle',
			'titt',
			'w00se',
			'jackoff',
			'wank',
			'whoar',
			'whore',
			'*damn',
			'*dyke',
			'*fuck*',
			'*shit*',
			'@$$',
			'amcik',
			'andskota',
			'arse*',
			'assrammer',
			'ayir',
			'bi7ch',
			'bitch*',
			'bollock*',
			'breasts',
			'butt-pirate',
			'cabron',
			'cazzo',
			'chraa',
			'chuj',
			'Cock*',
			'cunt*',
			'd4mn',
			'daygo',
			'dego',
			'dick*',
			'dike*',
			'dupa',
			'dziwka',
			'ejackulate',
			'Ekrem*',
			'Ekto',
			'enculer',
			'faen',
			'fag*',
			'fanculo',
			'fanny',
			'feces',
			'feg',
			'Felcher',
			'ficken',
			'fitt*',
			'Flikker',
			'foreskin',
			'Fotze',
			'Fu(*',
			'fuk*',
			'futkretzn',
			'gay',
			'gook',
			'guiena',
			'h0r',
			'h4x0r',
			'hell',
			'helvete',
			'hoer*',
			'honkey',
			'Huevon',
			'hui',
			'injun',
			'jizz',
			'kanker*',
			'kike',
			'klootzak',
			'kraut',
			'knulle',
			'kuk',
			'kuksuger',
			'Kurac',
			'kurwa',
			'kusi*',
			'kyrpa*',
			'lesbo',
			'mamhoon',
			'masturbat*',
			'merd*',
			'mibun',
			'monkleigh',
			'mouliewop',
			'muie',
			'mulkku',
			'muschi',
			'nazis',
			'nepesaurio',
			'nigger*',
			'orospu',
			'paska*',
			'perse',
			'picka',
			'pierdol*',
			'pillu*',
			'pimmel',
			'piss*',
			'pizda',
			'poontsee',
			'poop',
			'porn',
			'p0rn',
			'pr0n',
			'preteen',
			'pula',
			'pule',
			'puta',
			'puto',
			'qahbeh',
			'queef*',
			'rautenberg',
			'schaffer',
			'scheiss*',
			'schlampe',
			'schmuck',
			'screw',
			'sh!t*',
			'sharmuta',
			'sharmute',
			'shipal',
			'shiz',
			'skribz',
			'skurwysyn',
			'sphencter',
			'spic',
			'spierdalaj',
			'splooge',
			'suka',
			'b00b*',
			'testicle*',
			'titt*',
			'twat',
			'vittu',
			'wank*',
			'wetback*',
			'wichser',
			'wop*',
			'yed',
			's.h.i.t'					  
		);
		
		// Tag colors
		private $tagColors 	= array(
			'Pink', 
			'Purple', 
			'Blue', 
			'LightBlue', 
			'Teal', 
			'Green', 
			'DarkGreen', 
			'Lime', 
			'Yellow', 
			'Orange', 
			'Red', 
			'Default'
		);
		
		private $arrRestrictedUserNames = array(
			'admin',
			'Administrator',
			'administrator',
			'ADMIN'
		); 
		
		// Time format for shouts based on PHP's date function
		private $timeFormat = 'g:i:sa'; 
		
		public 	function __construct() {
			$this->lang = SHOUT_ENGINE_LANG::getDefinition('en');	
		}
		
		/**
		 * This function builds the shout panel, smielies and all!
		 * @param: 	none
		 * @return: void
		 */
		public	function buildShoutPanel() { 
			$objUser 		= SITE_USERS::getCurrentUser();
			$strUserName	=  $objUser->getVariable('username');
			$tagColor 		= 'Default';
			$strHtml		= '';
			$strAllSmilies 	= ''; 
			$strSwatches	= '';
			
			
			// Get the smilies.
			foreach($this->arrSmilies as $acii => $img) { 
				$strAllSmilies .= '<img src="'.$this->smiliesFolder.$img.'" class="ShoutCloud-Smilie" id="'.$acii.'" title="'.$acii.'" />'; 
			}
			
			// Get the swatches...
			foreach($this->tagColors as $k=>$color) { 
				$strSwatches.='<span class="ShoutCloud-Swatch ShoutCloud-Swatch-' .$color .(($color==$tagColor) ? ' sel' : '').'" title="'.$color.'"></span>'; 
			}
			
			// Create the container...
			$strHtml .= '<div id="ShoutCloud-Container">';
			$strHtml .= 	'<div id="ShoutCloud-MsgBox">'. SHOUT_ENGINE::loadMessages(). '</div>';
			$strHtml .= 	'<div id="ShoutCloud-InputBox">';
			$strHtml .= 		'<div id="ShoutCloud-Error"></div>';
			$strHtml .= 		'<div id="ShoutCloud-Wrapper">';
			
			if ($objUser->isLoggedIn()) {
				$strHtml .= 		'<div id="ShoutCloud-Smilies-Menu">'. $strAllSmilies .	'</div>';
				$strHtml .= 		'<div class="ShoutCloud-Swatches">'	. $strSwatches.	'<div class="clear"></div></div>';
			}
			
			$strHtml .= 			'<div id="ShoutCloud-Input-Wrapper">';
			
			if ($objUser->isLoggedIn()) {
				$strHtml .= 			'<span id="ShoutCloud-Color" title="'. $this->lang['Choose-Color-Text']. '"></span>';
				$strHtml .= 			'<input type="text" name="ShoutCloud-User" id="ShoutCloud-User" maxlength="25" value="'. utf8_decode($strUserName) .'" />';	
				$strHtml .= 			'<input type="text" name="ShoutCloud-Msg" id="ShoutCloud-Msg" value="" /></div>';
				$strHtml .= 			'<input type="button" name="ShoutCloud-Shout" id="ShoutCloud-Shout" value="'.$this->lang['Shout-Btn'].'" />';
				$strHtml .= 			'<div id="ShoutCloud-Counter">0/500 '.$this->lang['Characters-Text'].'</div>';
			} else {
				$strHtml .=				'<center>';
				$strHtml .= 			'<span name="ShoutCloud-User" id="ShoutCloud-User"><h2>';
				$strHtml .=				'<a target="_top" class="street" style="color:#333" href="/users/login?ref=' . urlencode(__SITE_URL__) . '">' .  $this->lang['Logged-In-or-Register'] . '</a>';
				$strHtml .=				'</h2></span>';
				$strHtml .=				'</center>';
			}
			
			$strHtml .= 			'</div>';
			$strHtml .= 			'<div class="clear"></div>';
			$strHtml .= 		'</div>';
			$strHtml .= 	'</div>';
			/*
			if ($this->isAdmin()) {
				$strHtml .= '<div id="ShoutCloud-Admin-Panel">';
				$strHtml .= 	'<span class="admin-btn shout-on" id="ShoutCloud-InputsPage">'.$this->lang['Admin-Btn-Shout'].'</span>';
				$strHtml .= 	'<span class="admin-btn" id="ShoutCloud-BanList">'.$this->lang['Admin-Btn-Bans'].'</span>';
				$strHtml .= 	'<span class="admin-btn" id="ShoutCloud-ClearChat">'.$this->lang['Admin-Btn-ClearAll'].'</span>';
				$strHtml .= 	'<span class="admin-btn" id="ShoutCloud-Admin-Logout">'.$this->lang['Admin-Btn-Logout'].'</span>';
				$strHtml .= '</div>';
				$strHtml .= '<div class="clear"></div>';
			}
			*/
			$strHtml .= '</div>';
			echo ($strHtml);
		}
		
		
		public function loadMessages($output='html', $intLastPost=0) { 
			$arrShouts 	= SHOUT_ENGINE::getShouts(array('id' => $intLastPost), array('>'));
			$objUser 	= SITE_USERS::getCurrentUser();
			$strHtml 	= "";
			
			foreach($arrShouts as $intPos => $arrData) {
				$strHtml .= '<div class="'. (($arrData["status"] == 0) ? 'shout-deleted' : 'shout-msg').'" id="shoutid-'. $arrData["id"] . '">';
				$strHtml .= 	'<strong id="' . utf8_decode($arrData['senderUserName']) . '" class="';
				$strHtml .= 	(strlen($arrData['messageColor']) ? ' ShoutCloud-Swatch-' . $arrData['messageColor'] : ''); // Swatch Color
				$strHtml .= 	($this->isAdmin() ? ' shout-admin' : '');
				$strHtml .= 	((strcmp(strtolower($objUser->getVariable('username')), utf8_decode($arrData['senderUserName'])) <> 0) ? ' ShoutCloud-Reply' : '') . '" ';
				$strHtml .= 	((strcmp(strtolower($objUser->getVariable('username')), utf8_decode($arrData['senderUserName'])) <> 0) ? 
								'title="'.$this->lang['Reply-To-Msg'].' '. utf8_decode($arrData['senderUserName']). '"' : '') . '>';
				
				$strHtml .= utf8_decode($arrData['senderUserName']). '</strong>';
				$strHtml .= (strlen($arrData['timeDate']) ? '<em>'. date('g:i:sa', $arrData['timeDate']) .'</em>' : '');
				$strHtml .= $this->formatMessage($arrData['messageText']);
				$strHtml .= '</div>';
			}
			
			$dataout['msgs'] = $strHtml;
			if(($output=='html') || ($output=='adminhtml')) { 
				$htmlout=''; 
				foreach($dataout as $k => $v) { 
					$htmlout .= $v; 
				} 
				return $htmlout; 
			} else { 
				if(!empty($dataout)) { 
					echo $this->jsonEncode($dataout); 
				} else { 
					return $this->jsonEncode(array('msgs' => '')); 
				}
			} 
		}
		
		# getShouts function
		# returns a view for the shout engine
		public static function getShouts(
			$arrView=array(), 
			$arrMappedOperators = array(),
			$intLimit=150,
			$strOrderBy='id',
			$strAscDesc='DESC'
		) {
			$objDb 		= DATABASE::getInstance();
			$objUser 	= SITE_USERS::getCurrentUser();
			$strViewSql = 	"SELECT 	se.id,"	.
							"			se.senderUserId, " .
							"			se.receiverUserId, " .
							"			se.messageTitle, " .
							"			se.messageText, " .
							"			se.timeDate, " . 
							"			se.status, " .
							"			se.messageColor, " .
							"			sus.userName senderUserName, " .
							"			sur.userName receiverUserName, " .
							"			UNIX_TIMESTAMP(se.timeDate) timeDate " .
							"FROM 		"	. strtolower(strtolower(get_called_class())) .  " se " .
							"INNER JOIN	" 	. strtolower(get_class($objUser)) . " sus " .
							"ON			sus.id = se.senderUserId " .
							"LEFT JOIN	" 	. strtolower(get_class($objUser)) . " sur " .
							"ON			sur.id = se.receiverUserId " .
							"AND		sur.id IS NOT NULL " .
							"WHERE 		1=1 ";
			// Build the query				
			$intCount = ((int) sizeof($arrView));		
			$intIndex = 0;
			foreach ($arrView as $strColumn => $mxValue) {
				if ($intCount > 0) {
					$strViewSql .= " AND ";
				}
				$strViewSql .= 	'se.' . $strColumn . (isset($arrMappedOperators[$intIndex]) ? " " . $arrMappedOperators[$intIndex] . " ": " = ");
				$strViewSql .= 	(is_numeric($mxValue) ? $mxValue : "'" . $objDb->escape($mxValue) . "'");
				--$intCount;
				++$intIndex;
			}
			
			// Add Order By
			$strViewSql .= 	" ORDER BY se." . $strOrderBy . " " . $strAscDesc;
			
			// Add Limit
			$strViewSql .= 	" LIMIT " . $intLimit;
			
			// Get the resultset
			$objRs = $objDb->iQuery($strViewSql);

			// Flip the resultset from oldest to newest
			$arrRecords = array_reverse($objRs->fetchRange(), true);
			
			// Return
			return ($arrRecords);
		}
		
		# addMessage function
		# Cleans and applies new submitted shout
		public function addMessage($arrInfo=array()) {
			$objSession	 = SESSION::getSession();
			$objUser 	 = SITE_USERS::getCurrentUser();
			$arrReturn	 = array();
			$blnContinue = true;
			$objNewPost  = SHOUT_ENGINE::getInstance(0,
				SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
			);
			$objReceiver = SITE_USERS::getInstance(
				(isset($arrInfo['receiverUserId']) ? (int) $arrInfo['receiverUserId'] : 0),
				SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
			);
			// Logged in
			if (! $objUser->isLoggedIn()) {
				$arrReturn['error']	= $this->lang['Error-Not-Logged-In'];
				$blnContinue = false;
			}
			// Message Flooding
			if ($blnContinue) {
				if(
				   	($objSession->get('ShoutCloud-User-Flood')) &&
					($objSession->get('ShoutCloud-User-Flood') > time())
				) { 
					$arrReturn['error']	= $this->lang['Error-Msg-Flood'];
					$blnContinue = false;
				} 
				$objSession->set('ShoutCloud-User-Flood', time() + 5);
			}
			
			if (
				($blnContinue) &&
				((! isset($arrInfo['msg']))	||
				(! strlen($arrInfo['msg'])))
			) {
				$arrReturn['error']	= $this->lang['Error-Empty-Msg'];
				$blnContinue = false;
			}
			
			if ($blnContinue) {
				$objNewPost->setVariable('senderUserId', 	$objUser->getId());	
				$objNewPost->setVariable('receiverUserId', 	$objReceiver->getId());	
				$objNewPost->setVariable('messageText', 	$arrInfo['msg']);	
				$objNewPost->setVariable('timeDate', 		date("Y-m-d H:i:s"));	
				$objNewPost->setVariable('ipAddress', 		$_SERVER['REMOTE_ADDR']);
				$objNewPost->setVariable('status', 			ACTIVE_STATUS_ENABLED);	
				$objNewPost->setVariable('messageTitle', 	(isset($arrInfo['title']) ? $arrInfo['title'] : ""));	
				$objNewPost->setVariable('messageColor', 	(isset($arrInfo['color']) ? $arrInfo['color'] : ""));	
				if ($objNewPost->save()) {
					$arrReturn['status'] = 'posted';
				} else {
					$arrReturn['error']	 = $this->lang['Error-Cannot-Post'];
				}
			}
			return ($this->jsonEncode($arrReturn));
		}
		
		
		# formatMessage function 
		# Removes bad words and adds smilies
		# Options: [message]
		protected function formatMessage($msg) {
			$msg = str_ireplace($this->badwords, '****', $msg); 
			foreach($this->arrSmilies as $acii => $img) { 
				$msg = str_ireplace($acii, '<img src="'.$this->smiliesFolder.$img.'" width="16" height="16" align="absmiddle" />', $msg); 
			}
			$patterns = array(
				'/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i',
				'/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i',
				'/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i',
				'~\[@([^\]]*)\]~',
				'~\[([^\]]*)\]~',
				'~{([^}]*)}~',
				'~_([^_]*)_~',
				'/\s{2}/'
			);
			$replacements = array(
				'$1http://$2',
				'<a href=\"$1\">$1</a>',
				'<a href=\"mailto:$1\">$1</a>',
				'<b class="reply">@\\1</b>',
				'<b>\\1</b>',
				'<i>\\1</i>',
				'<u>\\1</u>',
				'<br />'
			);
			$msg = preg_replace($patterns, $replacements, $msg); 
			return stripslashes(stripslashes(utf8_decode($msg)));
		}
	
		# isAdmin function
		# Checks if user is an admin
		# Options: none
		protected function isAdmin() {
			$objUser = SITE_USERS::getCurrentUser();
			return ((bool) $objUser->isInRole(SITE_USERS_ROLE_FULL_ADMIN));
		}
		
		# banUser function
		# Handles user bans by admins
		# Options: [user's name], [ip address], [expire time]
		protected function banUser($name, $ip, $expire) {}
		
		# unbanUser function
		# Handles unbanning users
		# Options: [ip address], [type]
		protected function unbanUser($ip,$type='box') {}
		
		# isBanned function
		# Checks if user's IP is banned by an admin
		# Options: [ip address]
		protected function isBanned($ip) {}
	
		# deleteMessage function
		# Deletes specific message from the shout box
		# Options: [shout id]
		protected function deleteMessage($id) {}
		
		# checkUsername function
		# Removes badwords and cleans up a user's name
		# Options: [user's name]
		protected function checkUsername($name) {
			$name = utf8_encode(strip_tags($name)); 
			foreach($this->arrRestrictedUserNames as $k=>$n) { 
				if($name==$n) { 
					return false; 
				} 
			} 
			return str_ireplace($this->badwords, '' , $name);
		}
	
		# formatTime function
		# Formats time for the Ban List
		# Options: [timestamp]
		protected function formatTime($ts) {
			$current = time(); $seconds = $ts-$current; if($seconds < 1) { return false; }
			switch($seconds) {
				case($seconds < 60): $unit=$var=$seconds; $var.=" second"; break;
				case($seconds < 3600): $unit=$var=round($seconds/60); $var.=" minute"; break;
				case($seconds < 86400): $unit=$var=round($seconds/3600); $var.=" hour"; break;
				case($seconds < 2629744): $unit=$var=round($seconds/86400); $var.=" day"; break;
				case($seconds < 31556926): $unit=$var=round($seconds/2629744); $var.=" month"; break;
				default: $unit=$var=round($seconds/31556926); $var.=" year";
			}
			if($unit > 1) {  $var.="s"; } return $var;
		}
		
		protected function jsonEncode($var) { return json_encode($var); }
		
		// Abstraction Methods.
		protected function getClassPath()  	 { return (__FILE__); }
	}
?>





