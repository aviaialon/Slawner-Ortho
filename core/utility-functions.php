<?php
/**
 * Copyright (c) 2008, David R. Nadeau, NadeauSoftware.com.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *	* Redistributions of source code must retain the above copyright
 *	  notice, this list of conditions and the following disclaimer.
 *
 *	* Redistributions in binary form must reproduce the above
 *	  copyright notice, this list of conditions and the following
 *	  disclaimer in the documentation and/or other materials provided
 *	  with the distribution.
 *
 *	* Neither the names of David R. Nadeau or NadeauSoftware.com, nor
 *	  the names of its contributors may be used to endorse or promote
 *	  products derived from this software without specific prior
 *	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 */

/*
 * This is a BSD License approved by the Open Source Initiative (OSI).
 * See:  http://www.opensource.org/licenses/bsd-license.php
 */



function now() {
	return (date("Y-m-d H:i:s"));	
}


function is_bot() {
	/* This function will check whether the visitor is a search engine robot */
	
	$botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
	"looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
	"Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
	"crawler", "www.galaxy.com", "Googlebot", "BingBot", "Scooter", "Slurp",
	"msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
	"Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
	"Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
	"Butterfly","Twitturls","Me.dium","Twiceler", "UptimeRobot");

	foreach($botlist as $bot)
	{
		if (
			(isset($_SERVER['HTTP_USER_AGENT'])) &&
			(strpos($_SERVER['HTTP_USER_AGENT'],$bot)!==false)
		) {
			return true;	// Is a bot
		}	
	}

	return false;	// Not a bot
}
	
	
function trimText ( $text, $trimNumber )
{	// trim the text to trimNumber amount of words
	$text = str_replace("  ", " ", $text);
	$trimmed = NULL;
	$string = explode(" ", $text);
	if ( count($string) > $trimNumber )
	{	for ( $wordCounter = 0 ; $wordCounter <= $trimNumber ; $wordCounter++ ){
			$trimmed .= $string[$wordCounter];
			if ( $wordCounter < $trimNumber ){ $trimmed .= " "; }
			else { $trimmed .= "..."; }
		}
	}
	else
	{	$trimmed = $text;	}
	$trimmed = stripslashes(trim($trimmed));
	return ($trimmed);
}

/*
-- DONT WANT TO RELY ON mcrypt library to be installed on the server
function __encrypt($strKey = NULL) {
	return (base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(__ENCRYPTION_KEY__), $strKey, MCRYPT_MODE_CBC, md5(md5(__ENCRYPTION_KEY__)))));	
}

function __decrypt($strKey = NULL) {
	return (rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(__ENCRYPTION_KEY__), base64_decode($strKey), MCRYPT_MODE_CBC, md5(md5(__ENCRYPTION_KEY__))), "\0"));	
}
*/
function __encrypt($string) 
{
	$result = '';
	for($i=1; $i<=strlen($string); $i++) {
		$char = substr($string, $i-1, 1);
		$keychar = substr(__ENCRYPTION_KEY__, ($i % strlen(__ENCRYPTION_KEY__))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return (base64_encode($result));
}

function __decrypt($Encodedtring = NULL) 
{
	$result = '';
	$string = (strlen($Encodedtring) ? base64_decode($Encodedtring) : '');
	for($i=1; $i<=strlen($string); $i++) {
		$char = substr($string, $i-1, 1);
		$keychar = substr(__ENCRYPTION_KEY__, ($i % strlen(__ENCRYPTION_KEY__))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	return ($result);
}

/**
 * This method implodes an array with the key.
 */
function implode_with_key($arrTarget, $strInGlue = '>', $strOutGlue = ',') 
{
    $strReturn = '';
	$arrJoin = array();
 
    foreach ($arrTarget as $strKey => $mxValue) {
		$arrJoin[] = $strKey . $strInGlue . $mxValue;
    }
    return (implode($strOutGlue, $arrJoin));
}

function getMicrotime() 
{
	list($uSec, $sec) = explode(" ", microtime());
	return ((float)$uSec + (float)$sec);
}


/**
 * Strip out (X)HTML tags and invisible content.  This function
 * is useful as a prelude to tokenizing the visible text of a page
 * for use in a search engine or spam detector/remover.
 *
 * Unlike PHP's built-in strip_tags() function, this function will
 * remove invisible parts of a web page that normally should not be
 * indexed or passed through a spam filter.  This includes style
 * blocks, scripts, applets, embedded objects, and everything in the
 * page header.
 *
 * In anticipation of tokenizing the visible text, this function
 * detects (X)HTML block tags (such as divs, paragraphs, and table
 * cells) and inserts a carriage return before each one.  This
 * insures that after tags are removed, words before and after the
 * tag are not erroneously joined into a single word.
 *
 * Parameters:
 * 	text		the (X)HTML text to strip
 *
 * Return values:
 * 	the stripped text
 *
 * See:
 * 	http://nadeausoftware.com/articles/2007/09/php_tip_how_strip_html_tags_web_page
 */
function strip_html_tags( $text )
{
	// PHP's strip_tags() function will remove tags, but it
	// doesn't remove scripts, styles, and other unwanted
	// invisible text between tags.  Also, as a prelude to
	// tokenizing the text, we need to insure that when
	// block-level tags (such as <p> or <div>) are removed,
	// neighboring words aren't joined.
	$text = preg_replace(
		array(
			// Remove invisible content
			'@<head[^>]*?>.*?</head>@siu',
			'@<style[^>]*?>.*?</style>@siu',
			'@<script[^>]*?.*?</script>@siu',
			'@<object[^>]*?.*?</object>@siu',
			'@<embed[^>]*?.*?</embed>@siu',
			'@<applet[^>]*?.*?</applet>@siu',
			'@<noframes[^>]*?.*?</noframes>@siu',
			'@<noscript[^>]*?.*?</noscript>@siu',
			'@<noembed[^>]*?.*?</noembed>@siu',
			/*'@<input[^>]*?>@siu',*/
			'@<form[^>]*?.*?</form>@siu',

			// Add line breaks before & after blocks
			'@<((br)|(hr))>@iu',
			'@</?((address)|(blockquote)|(center)|(del))@iu',
			'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
			'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
			'@</?((table)|(th)|(td)|(caption))@iu',
			'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
			'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
			'@</?((frameset)|(frame)|(iframe))@iu',
		),
		array(
			" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", 
			" ", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
			"\n\$0", "\n\$0",
		),
		$text );

  // remove empty lines
	$text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);
	// remove leading spaces
	$text = preg_replace("/\n( )*/", "\n", $text);

	// Remove all remaining tags and comments and return.
	return strip_tags( $text );
}


/**
 * ------------------------------------------------------------------------------
 * MySQL AES Encryption replication in PHP
 * These methods will replicate MySQL's AES encryption methods
 * MySQL Equivalent: SELECT AES_ENCRYPT('test', '<SECRET KEY HERE>') AS enc
 * ------------------------------------------------------------------------------
 *	
 * EXAMPLE :	
 
	// Group 1
	$a = mysql_aes_encrypt('test');
	echo base64_encode($a).'<br>';
	
	$result = $this->getApplication()->getDatabase()->query("SELECT AES_ENCRYPT('test', '" . constant('__ENCRYPTION_KEY__') ."') AS enc");
	$b = $result[0]['enc'];
	echo base64_encode($b).'<br>';
	
	// Group 2
	$result = $this->getApplication()->getDatabase()->query("SELECT AES_DECRYPT('" . $a . "', '". constant('__ENCRYPTION_KEY__') ."') AS decc");
	$c = $result[0]['decc'];
	echo $c."<br>";
	
	$d = mysql_aes_decrypt($b);
	echo $d."<br>";
	
	// Comparison
	var_dump($a===$b);
	var_dump($c===$d);
 *
 */


/**
 * this method will generate the AES encryption salt from a key
 * @param 	string $key - The secret encryption key used to encrypt / decrypt
 * @return  string The MySQL Encryption salt	
 */
function genetate_mysql_aes_key($key)
{
	$new_key = str_repeat(chr(0), 16);
	for($i=0,$len=strlen($key);$i<$len;$i++)
	{
		$new_key[$i%16] = $new_key[$i%16] ^ $key[$i];
	}
	return $new_key;
}

/**
 * This method encrypts a value in the MySQL AES methods
 * @param 	string $val - The string to encrypt
 * @return 	string
 */
function mysql_aes_encrypt($val)
{
	$key = genetate_mysql_aes_key(constant('__ENCRYPTION_KEY__'));
	$pad_value = 16-(strlen($val) % 16);
	$val = str_pad($val, (16*(floor(strlen($val) / 16)+1)), chr($pad_value));
	return (mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)));
}

/**
 * This method decrpts a value in the MySQL AES methods
 * @param 	string $val - The string to encrypt
 * @return 	string
 */
function mysql_aes_decrypt($val)
{
	$key = genetate_mysql_aes_key(constant('__ENCRYPTION_KEY__'));
	$val = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
	return rtrim($val, "\0..\16");
}
?>