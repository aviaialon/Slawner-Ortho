<?php

	/*
	 *	KMail class	ver 1.0
	 */

	/*
	 * Copyright (C) <2011>  <Eper Kalman>
	 *
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 *	KMail class for sending mail to smpt server directly with utf-8 and
	 *	multiple attachments support.
	 */
	 
	/*
	 *	KMail requirements & limitations:	
	 *	-	PHP 4
	 * 	-	maximum number of recepients 100;
	 */
	 
	/*
	 *	KMail history:
	 * 	-	Ver 1.0 First release
	 */
	 
	/*
	 *	KMail public functions
	 *	new KMail()		-> 	initialize the class
	 *	host()			->	define smtp host
	 *	port()			->	define port to connect (default: 25);
	 *	secure()		->	define secure connection type (default: none);
	 *	user()			->	define username to connect
	 *	password()		->	define password to connect
	 *	from()			->	define senders mail
	 *	sender_name()	->	define senders name to show
	 *	reply()			->	define reply to (default: no-reply)
	 *	to()			->	define recipents (one or more, max 100)
	 *	subject()		->	define mails subject
	 *	message()		->	define message
	 *	txt()			->	send mail as text/plain (default: html)
	 *	attach()		->	define attachment(one or more)
	 *	send()			->	mail send (returns TRUE if mail is sended for one recipients)
	 *	report()		->	report warnings and errors
	 *	debug()			->	show sended commands and responses for debug
	 *
	 */
	 
	 /**
	  	EXAMPLE USAGE:
		
		$mail=new KMail(); 
    	//$mail->debug(); 
        $mail->host('smtp.mail.com'); 
        $mail->port(25); 
        $mail->secure('ssl'); 
        $mail->user('username'); 
        $mail->password('password'); 
        $mail->sender_name('name'); 
        $mail->from('yourmail@mail.com'); 
        $mail->reply(); 
        $mail->to('recipient@mail.com'); 
        $mail->subject('subject'); 
        $mail->message('message'); 
        $mail->txt(); 
        $mail->attach('file.ext'); 
        if (! $mail->send()) echo 'Some error occurs...'; 
        echo $mail->report(); 
	  */

	if (! class_exists('KMail', false))	{
	
		if ((! function_exists('checkdnsrr')) && 
			(strpos(strtolower(PHP_OS), 'win') !== FALSE))	{
			
			function checkdnsrr($host, $type)	{
				$dns_recs=array('ANY', 'SOA', 'A', 'AAAA', 'A6', 'NS', 'MX', 'CNAME', 'PTR', 'TXT', 'SPF');
				if (! in_array(strtoupper($type), $dns_recs))	{
					trigger_error('Unkown DNS record type defined.', E_WARNING);
					return FALSE;
				}
				@exec('nslookup -type='.$type.' '.escapeshellcmd($host), $result);
				foreach($result as $line){
					if (preg_match('/^'.$host.'/',$line)) return TRUE;
				}
				return FALSE;
			}
		}
		
		class KMail	{
		
			//	smtp parameters
			var $host			=	'';
			var $port			=	25;
			var $secure			=	'';
			var $username		=	'';
			var $password		=	'';
			
			//	mail
			var $from			=	'postmaster@kmail.com';
			var $to				=	array();
			var $subject		=	'';
			var $message		=	'';
			var $text			=	FALSE;
			var $files			=	array();
			var $sender			=	'';
			var $noreply		=	TRUE; 
			
			//	debug
			var $debug			=	FALSE;
			
			//	report & error & auth
			var $report_str		=	'';
			var $server_auth	=	FALSE;
			var $error			=	FALSE;
			var	$time_limit		=	0;
			
			// constants
			var $EOL			=	"\r\n";
		
			public function host($host)	{
				$this->host=$host;
			}
			
			public function	port($port)	{
				if ((integer)$port<=0)	{
					$this->rep("Invalid port number: $port.");
					$this->error=TRUE;
					return;
				}
				$this->port=(integer)$port;
			}
			
			public function secure($type)	{
				if (strpos(strtolower($type), 'ssl') !== FALSE)	{
					$this->secure='ssl://';
				}	elseif (strpos(strtolower($type), 'tsl') !== FALSE)	{
					$this->secure='tsl://';
				}	elseif (strpos(strtolower($type), 'tls') !== FALSE)	{
					$this->secure='tls://';
				}	else	{
					$this->rep("Invalid secure connection type defined: $type.");
				}
			}
			
			public function debug()	{
				$this->debug=TRUE;
			}
			
			public function user($user)	{
				$this->username=$user;
			}
			
			public function password($pass)	{
				$this->password=$pass;
			}
			
			public function from($mail)	{
				// basic check for validity
				if (function_exists('filter_var'))	{
					$valid=filter_var($mail, FILTER_VALIDATE_EMAIL);
				}	else	{
					$valid=@preg_match("/^[^@]*@[^@]*\.[^@]*$/", $mail);
				}
				if (! $valid)	{
					$this->rep("Invalid senders mail defined: $mail. Switched to default $this->from.");
					return;
				}
				$this->from=$mail;
			}
			
			public function reply()	{
				$this->noreply=FALSE;
			}
			
			public function to($mails)	{
				if (! isset($mails))	return;
				if (! is_array($mails))	$mails=array($mails);
				$this->to=$mails;
			}
			
			public function sender_name($name)	{
				if (strlen($name))	$this->sender=$this->utf8($name);
			}
			
			public function txt()	{
				$this->text=TRUE;
			}
			
			public function subject($subject)	{
				// strip invalid chars
				if (! strlen($subject)) return;
				$this->subject=preg_replace('/\r+|\t+|\n+|\=/', '', $this->utf8($subject));
			}
			
			public function message($message)	{
				$this->message=$this->utf8($message);
			}

			public function attach($files)	{
				if (! $files)	return;
				if (! is_array($files))	$files=array($files);
				$this->files=$files;
			}
			
			public function report()	{
				if (! $this->report_str)	$this->report_str="\n<br>Mail succesfuly sent without any warning or error.";
				return $this->report_str;
			}
			
			private function rep($line)	{
				$this->report_str.=$line."<br>\n";
			}
			
			private function utf8($string)	{
				if (! function_exists('mb_detect_encoding'))	return $string;
				$in_charset=mb_detect_encoding($string);
				if (strtolower($in_charset)=='utf-8') return $string;
				return mb_convert_encoding($string, 'utf-8', $in_charset); 
			}
			
			private function get_mime_type($file)	{
			
				if (function_exists('finfo_open')) {
					$finfo = finfo_open(FILEINFO_MIME);
					$mime_type = finfo_file($finfo, $file);
					finfo_close($finfo);
					
				}	elseif (function_exists('mime_content_type'))	{
				
					$mime_type = @mime_content_type($file);
					
				}	else	{
					$mime_types = array (	'txt' 	=>	'text/plain',
											'htm' 	=>	'text/html',
											'html' 	=>	'text/html',
											'php'	=>	'text/html',
											'css' 	=>	'text/css',
											'js' 	=>	'application/javascript',
											'json' 	=>	'application/json',
											'xml' 	=>	'application/xml',
											'swf' 	=>	'application/x-shockwave-flash',
											'flv' 	=>	'video/x-flv',
											'png' 	=>	'image/png',
											'jpe' 	=>	'image/jpeg',
											'jpeg' 	=>	'image/jpeg',
											'jpg' 	=>	'image/jpeg',
											'gif' 	=>	'image/gif',
											'bmp' 	=>	'image/bmp',
											'ico' 	=>	'image/vnd.microsoft.icon',
											'tiff'	=>	'image/tiff',
											'tif' 	=>	'image/tiff',
											'svg' 	=>	'image/svg+xml',
											'svgz' 	=>	'image/svg+xml',
											'zip'	=>	'application/zip',
											'rar' 	=>	'application/x-rar-compressed',
											'exe' 	=>	'application/x-msdownload',
											'msi' 	=>	'application/x-msdownload',
											'cab' 	=>	'application/vnd.ms-cab-compressed',
											'mp3' 	=>	'audio/mpeg',
											'qt' 	=>	'video/quicktime',
											'mov' 	=>	'video/quicktime',
											'pdf' 	=>	'application/pdf',
											'psd'	=>	'image/vnd.adobe.photoshop',
											'ai' 	=> 	'application/postscript',
											'eps' 	=> 	'application/postscript',
											'ps' 	=> 	'application/postscript',
											'doc' 	=> 	'application/msword',
											'rtf' 	=> 	'application/rtf',
											'xls' 	=> 	'application/vnd.ms-excel',
											'ppt' 	=> 	'application/vnd.ms-powerpoint',
											'odt' 	=> 	'application/vnd.oasis.opendocument.text',
											'ods' 	=> 	'application/vnd.oasis.opendocument.spreadsheet'
										);
										
					@preg_match("/\.([^\.]+)$/s", $file, $ext);    
					if (array_key_exists(strtolower($ext[1]), $mime_types))	{
						$mime_type=$mime_types[$ext[1]];
					}	else	{
						$mime_type='unknown/'.$ext[1];
					}
				}
				
				return @preg_replace('/\;(.*?)$/','', $mime_type);
			}			
			
			private function check_recipients()	{
				$checked=array();
				foreach ($this->to as $mail)	{
					list($adress, $domain)=explode('@', $mail);
					if (! checkdnsrr($domain, 'A'))	{
						if (! checkdnsrr($domain, 'ANY')) continue;
					}
					if (! in_array($mail, $checked))	array_push($checked, $mail);
				}
				if (count($checked))	{
					asort($checked);
					return $checked;
				}
				return FALSE;
			}
			
			private function mail_create()	{
				
				$EOL=$this->EOL;
				
				// basic header
				if (! $this->noreply)	{
					// reply-to
					if (! $this->sender)	$this->sender=preg_replace('/@(.*?)$/is','', $this->from);
					$header		= 	 "From: =?UTF-8?B?".base64_encode($this->sender)."?= <$this->from>".$EOL
									."Reply-To: =?UTF-8?B?".base64_encode($this->sender)."?= <$this->from>".$EOL
									."Return-Path: =?UTF-8?B?".base64_encode($this->sender)." <$this->from>".$EOL;
									
				}	else	{
					// noreply
					if (! $noreply_host=$_SERVER ['HTTP_HOST'])	$noreply_host=gethostbyaddr($_SERVER ['REMOTE_ADDR']);
					$noreply	=	'<no-reply@'.str_replace('www','', strtolower($noreply_host)).'>';
					$header		= 	 "From: $noreply_host $noreply".$EOL
									."Return-Path: $noreply".$EOL;
				}
				
				if (count($this->to) > 1)	{
					$header		.=	"To: undisclosed-recipients <undisclosed-recipients>".$EOL;
				}	else	{
					$header		.=	"To: <".$this->to[0].">".$EOL;
				}
				
				if ($this->subject)	{
					$header		.=	 "Subject: =?UTF-8?B?".base64_encode($this->subject).'?='.$EOL;
				}
				
				$header 		.=	"Message-ID: <".time()." KMail@".$_SERVER['SERVER_NAME'].">".$EOL
									."X-Mailer: " . ucwords(__SITE_NAME__) . " PHP v".phpversion().$EOL
									.'MIME-Version: 1.0'.$EOL;
									
				// attachment
				if (count($this->files))	{
					foreach (array_keys($this->files) as $key)	{
						if (! is_readable($this->files[$key]))	{
							$this->rep("File not found or file is not readable: $this->files[$key].");
							unset($this->files[$key]);
						}
					}
				}
				// content-type
				if ($this->text)	{$contenttype='text/plain';}	else	{$contenttype='text/html';}
				// write
				if ($sum=count($this->files))	{
					$hash_id	=	md5(time());
					// additional header
					$header	  .= "Content-Type: multipart/related; boundary=\"".$hash_id."\"".$EOL;
					// mail body
					$body	=	 "--".$hash_id.$EOL
								."Content-Type: $contenttype; charset=\"utf-8\"".$EOL
								."Content-Transfer-Encoding: base64".$EOL.$EOL
								.base64_encode($this->message).$EOL.$EOL
								."--".$hash_id.$EOL; 
					
					$count=1;
					foreach ($this->files as $file)	{
						$filename=basename($file);
						$mimetype=$this->get_mime_type($file);
						if (! $file_content=@file_get_contents($file))	{
							$this->rep("File not found or file is not readable: $file.");
							return FALSE;
						}
						$body.=  "Content-Type: $mimetype; name=\"$filename\"".$EOL
								."Content-Transfer-Encoding: base64".$EOL
								."Content-Disposition: attachment; filename=\"$filename\"".$EOL.$EOL
								.chunk_split(base64_encode($file_content));
						if ($sum != $count)	{
							$body .= "--".$hash_id.$EOL;
							$count++;
						}	else	{
							// close
							$body	.=	"--".$hash_id."--".$EOL.$EOL;						
						}
					}

				}	else	{
					$header		.= "Content-Type: $contenttype; charset=\"utf-8\"".$EOL
								  ."Content-Transfer-Encoding: base64".$EOL.$EOL;
					$body		= base64_encode($this->message).$EOL.$EOL;
				}
				
				return $header.$body;
			}
			
			private function command_send($command, $expected_code)	{
				if ((! $command) or (! $expected_code) or (! $this->smtp))	return FALSE;
				if (! @fputs($this->smtp, $command)) return FALSE;
				$response=$this->get_response();
				//	debug
				if ($this->debug) echo "<br>Command:<br>$command<br>Response:".nl2br($response)."\n";
				// check for auth
				if (strpos($command, 'EHLO') !== FALSE)	{
					if (strpos(strtoupper($response), 'AUTH') !== FALSE)	$this->server_auth=TRUE;
				}
				// response code
				$code=substr($response, 0, 3);
				if (! is_array($expected_code))	$expected_code=array($expected_code);
				foreach($expected_code as $expected)	{
					if ($code==$expected)	return TRUE;
				}
				$this->rep("Invalid response code: <br>$response");
				return FALSE;
			}
			
			private function get_response()	{
				if (! $this->smtp)	return FALSE;
				if (! $response=@fread($this->smtp, 1)) return FALSE;
				if (! $state = socket_get_status($this->smtp)) return FALSE;
				if ($state['timed_out']) {
					$this->rep('Connection response timeout.');
					return FALSE;
				}
				if (! $left=@fread($this->smtp, $state['unread_bytes'])) return FALSE;
				return $response.=$left;
			}
			
			private function connect()	{
				if (! $this->smtp=@fsockopen($this->secure.$this->host, $this->port, $errno, $errstr, 10)) {
					$this->rep("Can not connect to host: $this->secure$this->host: $this->port. ($errstr)");
					return FALSE;
				}
				// if connection is established get and set time limit
				$this->time_limit=ini_get('max_execution_time');
				set_time_limit(0);
				
				return TRUE;
			}
			
			private function disconnect($err=FALSE)	{
				if ($err)	{
					@fputs($this->smtp, 'RSET'.$this->EOL);
					@fputs($this->smtp, 'QUIT'.$this->EOL);
				}
				@fclose($this->smtp);
				if ($this->debug)	echo "\n<br>Disconnected.";
				// restore time limit
				set_time_limit($this->time_limit);
			}
			
			public function send()	{
				
				// basic checks
				if ((! $this->host) or (! $this->port))	{
					$this->rep('Invalid smtp host or port defined.');
					return FALSE;
				}
				
				if (((! strlen($this->username)) &&	(strlen($this->password)))	or
					 ((strlen($this->username))	&& (! strlen($this->password))))	{
						$this->rep('Invalid user name or password defined.');
						return FALSE;
				}
				
				if ((! strlen($this->subject))	&& 
					(! strlen($this->message)) && 
					(! count($this->files)))	{
						$this->rep('Nothing to send.');
						return FALSE;
				}
				
				if (! $this->to=$this->check_recipients())	{
					$this->rep('No valid recipients found.');
					return FALSE;
				}
				
				if (count($this->to) > 100)	{
					$this->rep('Maximum number of recipients (100) error.');
					return FALSE;
				}
				
				// write the message
				if (! $mail=$this->mail_create()) return FALSE;
				
				$EOL=$this->EOL;
				
				// smtp connect
				if (! $this->connect())	return FALSE;
				// empty the first response
				$this->get_response();
				
				// say hello	
				if (! $this->command_send('EHLO '.$_SERVER['SERVER_NAME'].$EOL, 250))	{
					if (! $this->command_send('HELO '.$_SERVER['SERVER_NAME'].$EOL, 250)) {
						$this->disconnect(TRUE);
						return FALSE;
					}
				}
				
				
				// $this->command_send('STARTTLS'.$EOL, 250);
				
				// auth
				if (($this->server_auth) && (! strlen($this->username) or (! strlen($this->password))))	{
					$this->rep('Server require authentication.');
					$this->disconnect(TRUE);
					return FALSE;
				}
				if (strlen($this->username))	{
					if (! $this->command_send("AUTH LOGIN".$EOL, 334))	return FALSE;
					if (! $this->command_send(base64_encode($this->username).$EOL, 334))	return FALSE;
					if (! $this->command_send(base64_encode($this->password).$EOL, 235))	{
						$this->disconnect(TRUE);
						return FALSE;
					}
				}
				// from
				if (! $this->command_send("MAIL FROM:<$this->from>".$EOL, 250))	{
					$this->disconnect(TRUE);
					return FALSE;
				}
				// to
				foreach ($this->to as $send_to)	{
					if (! $this->command_send("RCPT TO:<$send_to>".$EOL, array(250, 251, 450, 550, 551, 553)))	{
						$this->disconnect(TRUE);
						return FALSE;
					}
				}
				// message
				if (! $this->command_send("DATA".$EOL, 354))	{
					$this->disconnect(TRUE);
					return FALSE;
				}
				if (! $this->command_send($mail.$EOL.'.'.$EOL, 250))	{
					$this->disconnect(TRUE);
					return FALSE;
				}
				// quit
				if (! $this->command_send("QUIT".$EOL, 221)) return FALSE;
				
				// smtp diconnect
				$this->disconnect();
				
				return TRUE;
			}
		}
	}

?>