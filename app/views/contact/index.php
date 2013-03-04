<?php
	$Application 	= $this->getApplication();
	$strApplicationStaticResPath = $Application->getBaseStaticResourcePath();
	$objPageBlocks 	= ($Application->getPageBlocks());	
	$intLangId 		= ($Application->translate(1, 2));
	$arrAttachments	= $this->getViewData('uploaded-attachments');
	
	$this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData());
	$this->renderPartial('IO::PAGE_PRELOADER', $this->getRequestData());
	$this->renderPartial('MENU::FONT_RESIZER', $this->getRequestData());
?>
<div id="main-wrapper">
	<?php 
		$this->renderPartial('MENU::TOP_MENU', array(
			'menuGroupId'	=> 	(int) $intLangId,
			'menuAttribute'	=>	'id="menu"'
		)); 
	?>
	
	<section id="pagetitle-wrapper">
    	<div class="row">
        	<div class="twelve columns content" data-content-id="7">
            	<?php echo($objPageBlocks->getBlockGroup(7)->getExecutedBlockContent()); ?>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="index-2.html">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page"><a href="#">contact</a></li>
                    </ul>
                </div>
            </div>
        </div>    
		       
    </section>
    
	
	<?php $this->renderPartial('MODULES::DIRECTIONS::MAP_DIRECTIONS', $this->getRequestData()); ?>
    <!-- content section start here 
    <section id="content-wrapper" style="padding-top:37px">
		<div class="shadow"></div>
    </section>-->
	
	<section id="content-wrapper">
        
        <div class="content" data-content-id="11">
        	<?php echo($objPageBlocks->getBlockGroup(11)->getExecutedBlockContent()); ?>
        </div>
        
		
        <div class="row">
            <div class="twelve columns">
                <div class="divider"></div>
            </div>
        </div>
        
        <div class="row contact-wrap-form">
        	<div class="eight columns">
            	<div id="contact-form-area">
                    <!-- Contact Form Start //-->
					<a name="contact"></a>
                    <form action="#contact" id="contactform" method="post"> 
                        <fieldset>
                            <div class="label-form-inline"> 
                            <label><?php echo($Application->translate('Name', 'Nom')); ?> <em>*</em></label>                           
                            <input type="text" name="name" class="textfield" id="name" value="<?php echo($this->getRequestParam('name')); ?>" /> 
                            </div>
                            <div class="label-form-inline">
                            <label>E-mail <em>*</em></label> 
                            <input type="text" name="email" class="textfield" id="email" value="<?php echo($this->getRequestParam('email')); ?>" />  
                            </div>
                            <div class="label-form-inline-last">                      
                            <label><?php echo($Application->translate('Subject', 'Sujet')); ?> <em>*</em></label>
                            <input type="text" name="subject" class="textfield" id="subject" value="<?php echo($this->getRequestParam('subject')); ?>" />
                            </div>
                        <label>Message <em>*</em></label>
                        <textarea name="message" id="message" class="textarea" cols="2" rows="4"><?php echo($this->getRequestParam('message')); ?></textarea>
                        <div class="clear"></div> 
						<ul id="__slawnerContactEmailAttachmentForm" class="unstyled"></ul>
                        <label>&nbsp;</label>
                        <!--<input type="submit" name="submit" class="buttonShadow buttoncontact" id="buttonsend" value="<?php echo($Application->translate('Send your message', 'Envoyer votre message')); ?>" />-->
						<a href="#" id="contactFormSubmitBtn" class="button blue medium tooltip float-left" title="<?php echo($Application->translate('Send your message', 'Envoyer votre message')); ?>">
							<?php echo($Application->translate('Send your message', 'Envoyer votre message')); ?></a>
							
						<a href="#" class="button white large attachFile tooltip" id="attachFileButton" 
							title="<?php echo($Application->translate('Attach a file (Max: 3 Files)', 'Attachez un fichier (Max: 3 Fichiers)')); ?>"><span></span></a>
						
                        <span class="loading" style="display: none;"><?php echo($Application->translate('Please wait', 'Veuillez patienter')); ?>..</span>
                        <div class="clear"></div>
                        </fieldset> 
						<?php reset($arrAttachments); ?>
						<?php $arrAppAttachments = array(); ?>
						<?php while (list($intIndex, $arrUploadElements) = each($arrAttachments)) { ?>
							<input type="hidden" name="attachments[<?php echo($arrUploadElements['localFileName']); ?>:<?php echo($arrUploadElements['fileSize']); ?>]" 
									value="<?php echo($arrUploadElements['encryptedSource']); ?>" />
							<?php 
								$arrAppAttachments[] = array(
									'fileName' 		=> $arrUploadElements['localFileName'],
									'fileSize'		=> $arrUploadElements['fileSize'],
									'fileSource'	=> $arrUploadElements['encryptedSource']
								);
							?>		
						<?php } ?>		
                    </form>
					<script type="text/javascript">
						objApplication.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.UPLOADED_ATTACHMENTS, <?php echo(json_encode($arrAppAttachments)); ?>);
					</script>
                    <!-- Contact Form End //-->
                </div>                
            </div>
            <div class="four columns">
            	<?php $this->renderPartial('MODULES::CONTENT::ALTERNATE_CONTACT', $this->getRequestData()); ?>
            </div>
        </div>  

    </section>
    <div class="row">
		<div class="twelve columns">
			<div class="featured-box content"  data-content-id="6">
				 <?php echo($objPageBlocks->getBlockGroup(6)->getExecutedBlockContent()); ?>
			</div>
		</div>
	</div>
	
	
    <?php $this->renderPartial('MENU::FOOTER', $this->getRequestData()); ?>
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
<?php $this->renderPartial('MODULES::CALL::CALL_MODULE', $this->getRequestData()); ?>
</body>
</html>