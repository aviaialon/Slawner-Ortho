<?php
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::IMAGE::SITE_USERS_IMAGE");	
 	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
	
	class REMOTE_CALL_CONTROLLER extends REQUEST_DISPATCHER
	{
		
		public function __construct()
		{
			$this->Application = APPLICATION::getInstance();
		}
		
		public function sendEmailMessage($arrArgs)
		{
			$objMailer  = MAIL::getInstance();
			$objMailer->setTo(constant('__SMTP_USER__'));
			$objMailer->setFrom($this->getApplication()->getUser()->getEmail());
			$objMailer->setSubject($this->Application->getUser()->getFullName() . " Sent a message from the client interface.");
			$objMailer->setMessage(
				"<b>" . $this->Application->getUser()->getFullName() . 
				"  - [" . $this->Application->getUser()->getAffiliatedGmailFolder() . 
				"] Sent the following Message:</b> " . $_POST['message'])
			;
			$blContinue = $objMailer->send();
			if (false === $blContinue) {
				$arrMailerErrors = $objMailer->getError();
				echo (implode('<br />', $arrMailerErrors));	
			} else {
				echo "OK";	
			}
		}
		
		public function getEmailMessageInbox($arrArgs)
		{
			$arrEmails		= array('folder' => $arrArgs['parameters']['messageFolder'], 'totalCount' => 0, 'inbox' => array()); // Final email listings
			$Application	= APPLICATION::getInstance();
			if (
				(true === ($Application->getUser()->getId() > 0)) /*&& 
				(true === $Application->getRequestDispatcher()->isXHTTPRequest())*/
			) {
				$strHost 		= '{imap.gmail.com:993/imap/ssl}' . $arrArgs['parameters']['messageFolder'];
				$strUserName 	= constant('__SMTP_USER__');
				$strPassword	= constant('__SMTP_PASS__');
				
				// get the imap stream
				$objImapStream	= imap_open($strHost, $strUserName,$strPassword) or die('Cannot connect to Gmail: ' . imap_last_error());
				
				/* grab emails */
				if (
					(true === isset($_POST['emailKeyword'])) &&
					(strlen($_POST['emailKeyword']) > 0)
				) {
					$arrEmailList 	= imap_search($objImapStream, 'TEXT "' .$_POST['emailKeyword'] . '"');
				} else {
					$arrEmailList 	= imap_search($objImapStream, 'ALL');
				}
				
				if (false === empty($arrEmailList)) {
					rsort($arrEmailList); // Newest first.
					reset($arrEmailList);
					$arrEmailList = array_slice($arrEmailList, 0, 4);
					$arrEmails['totalCount'] = count($arrEmailList);
					
					// Get the admin's avatar
					$strAdminProfileImage = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(constant('__SMTP_USER__')))) . 
											'?d=' . urlencode($Application->getStaticResourcePath() . 'images/userLogin2.png') . '&s=72';
											
					// Get the user's profile image:
					$objProfileImage = SITE_USERS_IMAGE::getInstanceFromKey(array(
						'site_user_id' 			=> $Application->getUser()->getId(),
						'active_status_id'		=> ACTIVE_STATUS_ENABLED,
						'image_position_id'		=> 2 // 75x75 Thumbnail 
					), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
					
					// 1. Try to load the user's profile image												
					if (true === ((bool) $objProfileImage->getId())) {
						$strProfileImage = $objProfileImage->getBase64DisplayData();	
					}
					// 2. Otherwise, load the image saved in the user database (affiliated with a mailbox image...)
					else if (true === ((bool) $Application->getUser()->getMailboxUserAvatarUrl())) {
						$strProfileImage = $Application->getUser()->getMailboxUserAvatarUrl() . '?sz=72';
					}
					// All else failed... try to load a gravatar
					else {
						$strProfileImage = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($Application->getUser()->getEmail()))) . 
											'?d=' . urlencode($Application->getStaticResourcePath() . 'images/userLogin2.png') . '&s=72';	
					}
					
					while (list($intIndex, $intEmailId) = each($arrEmailList)) {
						$objHeaderInformation 	= imap_headerinfo($objImapStream, $intEmailId);
						$strSenderName			= ucwords($objHeaderInformation->from[0]->personal);
						$strSenderEmail			= strtolower($objHeaderInformation->from[0]->mailbox . '@' . $objHeaderInformation->from[0]->host);
						$strReceiverEmail		= strtolower($objHeaderInformation->to[0]->mailbox . '@' . $objHeaderInformation->to[0]->host);
						$strEmailDate			= ucwords($objHeaderInformation->date);
						$strSubject				= ucwords($objHeaderInformation->subject);
						
						
						
						// if the sender isnt the admin or the logged in user. use gravatar...
						if (
							(false === ($strSenderEmail == strtolower(trim(constant('__SMTP_USER__'))))) &&
							(false === ($strSenderEmail == strtolower(trim($Application->getUser()->getEmail())))) 
						) {
							$strProfileImage = 	'https://www.gravatar.com/avatar/' . md5(strtolower(trim($strSenderEmail))) . 
												'?d=' . urlencode($Application->getStaticResourcePath() . 'images/userLogin2.png') . '&s=72';	
						}
						 //nl2br(self::getMessageContent($objImapStream, $intEmailId)),//trim(imap_body($objImapStream, $intEmailId, 1)),
						$arrEmails['inbox'][] = array (
							'header'		=>	$objHeaderInformation,
							'senderName'	=>	$strSenderName,
							'senderEmail'	=> 	$strSenderEmail,
							'receiverEmail'	=> 	$strReceiverEmail,
							'emailDate'		=> 	$strEmailDate,
							'subject'		=>	$strSubject,
							'message'		=>	self::getMessageContent($objImapStream, $intEmailId)->Message,
							'message_full'	=>	quoted_printable_decode(imap_fetchbody($objImapStream,$intEmailId, FT_PEEK)),
							'overview'		=>	imap_fetch_overview($objImapStream, $intEmailId, 1),
							'userAvatar'	=>	(($strSenderEmail == constant('__SMTP_USER__')) ? $strAdminProfileImage : $strProfileImage)
						);
					}
				}
				
				imap_close($objImapStream);
			}
			
			echo json_encode($arrEmails);
		} 
		
		public static final function getMessageContent($mbox, $id) {
			// Get content of text message.
			$mid = $id;
		
			$struct = imap_fetchstructure($mbox, $mid);
		
			$parts = $struct -> parts;
			$i = 0;
		
			if (!$parts) {
				// Simple message, only 1 piece	
				$attachment = array(); //No attachments
				$content = imap_body($mbox, $mid);
			} else {
				// Complicated message, multiple parts
				$endwhile = false;
				
				$stack = array(); // Stack while parsing message
				$content = ""; // Content of message
				$attachment = array(); // Attachments
		
				while (!$endwhile) {
					if (!$parts[$i]) {
						if (count($stack) > 0) {
							$parts = $stack[count($stack) - 1]["p"];
							$i = $stack[count($stack) - 1]["i"] + 1;
							array_pop($stack);
						} else {
							$endwhile = true;
						}
					}
		
					if (!$endwhile) {
						// Create message part first (example '1.2.3')	
						$partstring = "";
						foreach($stack as $s) {
							$partstring .= ($s["i"] + 1).".";
						}
						$partstring .= ($i + 1);
		
						if (strtoupper($parts[$i] -> disposition) == "ATTACHMENT") { //Attachment
							$attachment[] = array("filename" => $parts[$i] -> parameters[0] -> value, "filedata" => imap_fetchbody($mbox, $mid, $partstring));
						}
						elseif(strtoupper($parts[$i] -> subtype) == "PLAIN") { // Message
							$content .= imap_fetchbody($mbox, $mid, $partstring, FT_PEEK);
						}
					}
		
					if ($parts[$i] -> parts) {
						$stack[] = array("p" => $parts, "i" => $i);
						$parts = $parts[$i] -> parts;
						$i = 0;
					} else {
						$i++;
					}
				} // while
			} // complicated message
			
			$objReturn = new StdClass();
			$objReturn->Attachments = $attachment;
			$objReturn->Message 	= quoted_printable_decode($content); //nl2br(quoted_printable_decode($content));
			return ($objReturn);
		
		}	
		
			
		protected final function update_profile_imageAction($arrArgs)
		{
			if (true === $this->isXHTTPRequest())
			{
				$this->enableViewCache(false);
				$this->assignNoView();
				
				$arrReturn = array(
					'base64'	=> NULL,
					'error'		=> FALSE
				);
				$Application = APPLICATION::getInstance();
				
				if (
					/*($this->isXHTTPRequest()) &&*/
					($Application->getUser()->getId())
				) {	
					// Execute the creation of the image positions
					$blnImageCreation = SITE_USERS_IMAGE::uploadAndCreateNewProfileImage();	
					
					if ($blnImageCreation) {
						// If successful, we'll return image position 4 (200 x 260 Item Image)
						$objImagePos4 = SITE_USERS_IMAGE::getInstanceFromKey(array(
							'site_user_id' 		=> (int) $Application->getUser()->getId(),
							'image_position_id' => 4
						), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
						
						if ($objImagePos4->getId()) {
							$arrReturn['base64'] = $objImagePos4->getBase64DisplayData(); //$objImagePos4->getImage_Path()
						}
					} else {
						$arrReturn['error']	= SITE_USERS_IMAGE::getError();
					}
				} 
				else 
				{
					$this->catchAllAction();
				}	
				
				
				echo json_encode($arrReturn);
			}
			else 
			{
				$this->catchAllAction();
			}	
		} 
		
		protected final function catchAllAction($arrRequest = array(), $arrRequestData = array()) 
		{
			die('Unauthorized access.');
		}
	}
?>