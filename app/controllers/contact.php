<?php
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::MAIL::MAIL");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::VALIDATION::VALIDATOR");

class CONTACT_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction(array $arrRequestParams, array $arrRequestDispatcherDispatchData)
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$Application = $this->getApplication();
		$blnContinue = true;
		$arrAttachments = array();
		
		if (true === $Application->getForm()->isPost())
		{
			$arrValidation = array(
				'name' 		=> $Application->translate('Please enter your name', 'Veuillez enter votre nom'),
				'email' 	=> $Application->translate('Please enter your email', 'Veuillez enter votre email'),
				'subject' 	=> $Application->translate('Please enter a subject', 'Veuillez enter un sujet'),
				'message' 	=> $Application->translate('Please enter a subject', 'Veuillez enter un message')
			);
			
			reset($arrValidation);
			while (list($strFormKey, $strErrorMessage) = each($arrValidation))
			{
				$blnContinue = ((bool) $this->getRequestParam($strFormKey)) || (SESSION::set('err', $strErrorMessage) & false);	
				if (false === $blnContinue) break;
			}
			
			if (true === $blnContinue)
			{
				$blnContinue = 	(true === ((bool) VALIDATOR::email($this->getRequestParam('email')))) || 
								(SESSION::set('err', $Application->translate('Please enter a valid email address', 'Veuillez entrer une address email valid.')) & false);		
			}
			
			// Parse the attachments:
			$arrUploadedAttachments = $this->getRequestParam('attachments');
			while (list($strAttachmentKey, $strAttachmentEncryptedData) = each($arrUploadedAttachments))
			{
				list($strLocalFileName, $strFileUploadSize) = explode(':', $strAttachmentKey);
				$arrAttachments[] = array(
					'localFileName'		=> $strLocalFileName,
					'fileSize'			=> $strFileUploadSize,
					'encryptedSource'	=> $strAttachmentEncryptedData,
					'fileName'			=> $this->getApplication()->getCrypto()->decrypt($strAttachmentEncryptedData),
					'filePath'			=> '/static/tmp/user-uploads/' . $this->getApplication()->getCrypto()->decrypt($strAttachmentEncryptedData)
				);
			}
			
			if (true === $blnContinue)
			{
				// Send the confirmation email.
				$objMailer  = new MAIL();
				$objSession = SESSION::getSession();
				$objMailer->setData(array(
					'SITE_LINK'			=> __ROOT_URL__,
					'ROOT'				=> __ROOT_URL__	,
					'SITE_NAME'			=> __SITE_NAME__,
					'DATE'				=> 	date("F j, Y"),
					'YEAR'				=> 	date("Y"),
					'ROOT_URL' 			=> __ROOT_URL__, 
					'SITE_NAME' 		=> __SITE_NAME__, 
					'TITLE' 			=> $Application->translate("Sent on ", "Envoyée le ") . date("F j, Y H:i:s")
				));
				
				$strEmailMessage = 	'<h2><strong>' . $Application->translate("Contact-us request from ", "Rappel de client chez ") . ucwords(__SITE_NAME__) . 
				' on ' .  date("F j, Y H:i:s") . '</strong></h2><br /><p><strong>[User IP Address: ' . $_SERVER['REMOTE_ADDR'] . ']</strong></p>' . '<br />' .
				'<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial;font-size:12px;color:#000">' .
					'<tr>' .
						'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Name: </td>' .
						'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($this->getRequestParam('name'))) . '</strong></td>' .
					'</tr>' .
					'<tr>' .
						'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Email: </td>' .
						'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($this->getRequestParam('email'))) . '</strong></td>' .
					'</tr>' .
					'<tr>' .
						'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Subject: </td>' .
						'<td align="left" valign="top" style="padding-bottom: 10px"><strong>' . strip_tags(htmlentities($this->getRequestParam('subject'))) . '</strong></td>' .
					'</tr>' .
					'<tr>' .
						'<td width="160" align="left" valign="top" style="padding-bottom: 10px">Message: </td>' .
						'<td align="left" valign="top" style="padding-bottom: 10px">' . 
							'<strong>' . strip_tags(htmlentities($this->getRequestParam('message'))) . '</strong>' .
						'</td>' .
					'</tr>';
									
				if (false === empty($arrAttachments))
				{
					$strEmailMessage .= '<tr><td colspan="2" align="left" valign="top" style="padding-bottom: 10px">Attachments (' . count($arrUploadedAttachments) . '): </td></tr>';
						
					while(list($intIndex, $arrUploadedElements) = each($arrAttachments))
					{
						$strEmailMessage .= '<tr><td colspan="2" align="left" valign="top" style="padding-bottom: 10px">';
						$strEmailMessage .= '<a href="' . constant('__SITE_URL__') . '/' . $arrUploadedElements['filePath'] . '">' . $arrUploadedElements['localFileName'] . '</a>';
						$strEmailMessage .= '</td></tr>';
					}
				}
				$strEmailMessage .= '</table>';
				
				$objMailer->setTo(filter_var(constant('__ADMIN_EMAIL__'), FILTER_VALIDATE_EMAIL));
				$objMailer->setFrom(ucwords(__SITE_NAME__) .  (' <reminder@' . __SITE_DOMAIN__ . '>'));
				$objMailer->setSubject($Application->translate("Contact-us request from ", "Rappel de client chez ") . ucwords(__SITE_NAME__));
				$objMailer->setTemplate('static/templates/email/contact-us.tmpl');
				$objMailer->setMessage($strEmailMessage);
				$blContinue = $objMailer->send();

				if ($blContinue) 
				{
					$arrAttachments = array();
					SESSION::set('info', $Application->translate("Thank you. Your message has been sent", 'Merci. Un courriel a été envoyée'));
				} 
				else 
				{
					SESSION::set('err', $Application->translate(
						"Sorry, we are experiencing technical difficulties.<br />Please try again later.",
						"Désolé, nous éprouvons des difficultés techniques. <br /> Veuillez réessayer plus tard."
					));
				}
				
				unset($_POST);	
			}
		}
		
		
		$this->setViewData('uploaded-attachments', $arrAttachments);
	}
}