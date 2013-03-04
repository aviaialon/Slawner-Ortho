<?php
	$Application 	= $this->getApplication();
	$strApplicationStaticResPath = $Application->getBaseStaticResourcePath();
	$objPageBlocks 	= ($Application->getPageBlocks());	
	$intLangId 		= ($Application->translate(1, 2));
	$arrAttachments	= $this->getViewData('uploaded-attachments');
	$arrLocations	= $this->getViewData('location-array'); 	
	
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
        	<div class="twelve columns content" data-content-id="18">
            	<?php echo($objPageBlocks->getBlockGroup(18)->getExecutedBlockContent()); ?>
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
        
        <div class="row contact-wrap-form">
        	<div class="eight columns">
            	<div id="contact-form-area">
                    <!-- appointment Form Start //-->
					<a name="appointment"></a>
                    <form action="" id="appointmentForm" method="post"> 
						<input type="hidden" name="selectedLang" value="<?php echo($Application->translate('English', 'French')); ?>" />
                        <fieldset>
                        	<div class="label-form-inline full"> 
	                            <h4><?php echo($Application->translate('General Information', 'Informations General')); ?></h4>
                            </div>
                            <div class="label-form-inline half"> 
	                            <label><?php echo($Application->translate('Name', 'Nom')); ?> <em class="red-txt">*</em></label>                           
	                            <input type="text" name="name" class="textfield" id="name" value="<?php echo($this->getRequestParam('name')); ?>" /> 
                            </div>
                            <div class="label-form-inline half">
	                            <label>E-mail <em class="red-txt">*</em></label> 
	                            <input type="text" name="email" class="textfield" id="email" value="<?php echo($this->getRequestParam('email')); ?>" />  
                            </div>
                            <div class="label-form-inline half">                      
	                            <label><?php echo($Application->translate('Address', 'Address')); ?> <em class="red-txt">*</em></label>
	                            <input type="text" name="address" class="textfield" id="address" value="<?php echo($this->getRequestParam('subject')); ?>" />
                            </div>
                            <div class="label-form-inline half"> 
	                            <label><?php echo($Application->translate('Phone', 'Tel')); ?> <em class="red-txt">*</em></label>                           
	                            <input type="text" name="tel[0]" class="textfield" id="tel_0" maxlength="3" value="" />                            
	                            <input type="text" name="tel[1]" class="textfield" id="tel_1" maxlength="3" value="" />                            
	                            <input type="text" name="tel[2]" class="textfield" id="tel_2" maxlength="4" value="" />
                            </div>
							<div class="label-form-inline half">                      
	                            <label><?php echo(ucwords($Application->translate('Age', 'âge'))); ?> <em class="red-txt">*</em></label>
	                            <input type="text" name="age" class="textfield" id="age" value="<?php echo($this->getRequestParam('age')); ?>" />
                            </div>
							<div class="label-form-inline half"> 
	                            <label><?php echo($Application->translate('How did you hear about us?', 'Comment avez-vous entendu parler de nous?')); ?> <em class="red-txt">*</em></label>   
								<select name="cboHear" id="cboHear" size="1" class="textfield">
									<option value=""><?php echo($Application->translate('Select One', 'Choisissez-en un')); ?>...</option>
									<option value="<?php echo(base64_encode('Another Website')); ?>"><?php echo($Application->translate('Another Website', 'Un autre site web')); ?>.</option>
									<option value="<?php echo(base64_encode('A Friend')); ?>"><?php echo($Application->translate('A Friend', 'Un(e) Ami(e)')); ?>.</option>
									<option value="<?php echo(base64_encode('Family')); ?>"><?php echo($Application->translate('A Relative', 'Famille')); ?>.</option>
									<option value="<?php echo(base64_encode('Doctor')); ?>"><?php echo($Application->translate('A Doctor', 'Un Médecin')); ?>.</option>
									<option value="<?php echo(base64_encode('Therapist')); ?>"><?php echo($Application->translate('A Therapist', 'Un Thérapeute')); ?>.</option>
									<option value="<?php echo(base64_encode('Colleague')); ?>"><?php echo($Application->translate('A Colleague', 'Un Collègue')); ?>.</option>
									<option value="<?php echo(base64_encode('Phone Book')); ?>"><?php echo($Application->translate('Phone Book', 'L\'Annuaire')); ?>.</option>
									<option value="<?php echo(base64_encode('Other')); ?>"><?php echo($Application->translate('Other', 'Autre')); ?>.</option>
								</select>
							</div>
							
						</fieldset>
						<br />
						<fieldset>	
							<div class="label-form-inline full"> 
	                            <h4><?php echo($Application->translate('Appointment Information', 'Selection du rendez-vous')); ?></h4>
                            </div>
							<div class="label-form-inline half"> 
	                            <label><?php echo($Application->translate('Prefered appointment date', 'Date désirée du rendez-vous')); ?></label>                           
	                            <input type="text" name="date_range" class="textfield daterange" id="date_range" value="<?php echo($this->getRequestParam('date_range')); ?>" /> 
                            </div>
							<div class="label-form-inline half"> 
	                            <label><?php echo($Application->translate('Which location?', 'À quel endroit?')); ?> <em class="red-txt">*</em></label>                           
	                            <select name="location" id="location" class="textfield">
									<option value="">-- <?php echo($Application->translate('Choose a location', 'Choisissez un emplacement')); ?> --</option>
									<?php while (list($intIndex, $arrLocationData) = each($arrLocations)) { ?>
										<option value="<?php echo($arrLocationData['id']); ?>"><?php echo(utf8_encode($arrLocationData['name'])); ?></option>
									<?php } ?>
								</select>
                            </div>
							<div class="label-form-inline half">                      
	                            <label><?php echo($Application->translate('What is your reason for your appointment?', 'Quelle est la raison de votre rendez-vous')); ?> <em class="red-txt">*</em></label>
	                            <input type="text" name="reason" class="textfield" id="reason" value="<?php echo($this->getRequestParam('reason')); ?>" />
                            </div>
						</fieldset>
						<fieldset>	
							<label><?php echo($Application->translate('Do you have any comments or questions?', 'Avez-vous des commentaires ou des questions?')); ?></label>
							<textarea name="message" id="message" class="textarea" cols="2" rows="4"><?php echo($this->getRequestParam('message')); ?></textarea>
							<div class="clear"></div> 
							<ul id="__slawnerContactEmailAttachmentForm" class="unstyled"></ul>
							<label>&nbsp;</label>
							<!--<input type="submit" name="submit" class="buttonShadow buttoncontact" id="buttonsend" value="<?php echo($Application->translate('Send your message', 'Envoyer votre message')); ?>" />-->
							<a href="#" id="appointmentFormSubmitBtn" class="button blue medium tooltip float-left" title="<?php echo($Application->translate('Send your message', 'Envoyer votre message')); ?>">
								<?php echo($Application->translate('Request Your Appointment', 'Demandez votre rendez-vous')); ?></a>
								
							<a href="#" class="button white large attachFile tooltip" id="attachFileButton" 
								title="<?php echo($Application->translate('Attach a file (Max: 3 Files)', 'Attachez un fichier (Max: 3 Fichiers)')); ?>"><span></span></a>
							
							<span class="loading" style="display: none;"><?php echo($Application->translate('Please wait', 'Veuillez patienter')); ?>..</span>
							<div class="clear"></div>
                        </fieldset> 
                    </form>
                    <!-- Contact Form End //-->
                </div>                
            </div>
            <div class="four columns">
            	<?php $this->renderPartial('MODULES::CONTENT::ALTERNATE_CONTACT', $this->getRequestData()); ?>
            </div>
        </div>  
        
        
        
        <div class="row">
            <div class="twelve columns">
                <div class="divider"></div>
            </div>
        </div>
        
        <div class="content" data-content-id="11">
        	<?php echo($objPageBlocks->getBlockGroup(11)->getExecutedBlockContent()); ?>
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