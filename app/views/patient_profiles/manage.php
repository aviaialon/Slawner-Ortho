<?php
	$Application 		= $this->getApplication();
	$strApplicationStaticResPath = $Application->getBaseStaticResourcePath();
	$objPageBlocks 		= ($Application->getPageBlocks());	
	$intLangId 			= ($Application->translate(1, 2));
	
	// Get the news categories
	$arrProfilesCategories['en'] = PATIENT_PROFILES_CATEGORIES::getObjectClassView(array('filter' => array('lang' => 'en')));
	$arrProfilesCategories['fr'] = PATIENT_PROFILES_CATEGORIES::getObjectClassView(array('filter' => array('lang' => 'fr')));
	$arrPostData			 = $_POST;
	$blnContinue			 = true;
	$arrErrors 				 = array();
	$arrMessage				 = array();
	$objProfilePost = PATIENT_PROFILES::getInstance(
		(int) $this->getRequestParam('profile-id'),
		SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE
	);
	
	$objProfilePostContentEnglish = PATIENT_PROFILES_CONTENT::getInstanceFromKey(array(
		'patient_profiles_id' 	=> (int) $this->getRequestParam('profile-id'),
		'lang'		=>	'en'	
	), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
	
	$objProfilePostContentFrench = PATIENT_PROFILES_CONTENT::getInstanceFromKey(array(
		'patient_profiles_id' 	=> (int) $this->getRequestParam('profile-id'),
		'lang'		=>	'fr'	
	), SHARED_OBJECT::SHARED_OBJECT_SOFT_INSTANCE);
	
	
	$strPagename = ($objProfilePost->getId() ? $Application->translate(
		'Editing Profile: ' . $objProfilePostContentEnglish->getName(),
		'Modification du profile de ' . $objProfilePostContentFrench->getName()
	) : $Application->translate(
		'New Patient Profile',
		'Nouveau Profile de Patient'
	));
	
	// get the categories
	$arrFrCategories = PATIENT_PROFILES_CATEGORY::getObjectClassView(array(
		'columns'		=> array('b.id id'),
		'filter' 		=> array('patient_profiles_id' => (int) $objProfilePost->getId()),
		'inner_join'	=> array('patient_profiles_categories b' => 'b.id = a.patient_profiles_category_id AND b.lang = "fr"')
	));
	reset($arrFrCategories);
	while (list($intRowIndex, $arrICategoryData) = each($arrFrCategories)) {
		$arrPostData['category']['fr'][] = (int) $arrICategoryData['id'];
	}
	
	$arrEnCategories = PATIENT_PROFILES_CATEGORY::getObjectClassView(array(
		'columns'		=> array('b.id id'),
		'filter' 		=> array('patient_profiles_id'	=> (int) $objProfilePost->getId()),
		'inner_join'	=> array('patient_profiles_categories b' => 'b.id = a.patient_profiles_category_id AND b.lang = "en"')
	));
	
	reset($arrEnCategories);
	while (list($intRowIndex, $arrICategoryData) = each($arrEnCategories)) {
		$arrPostData['category']['en'][] = (int) $arrICategoryData['id'];
	}
	
	
	// get the content
	$arrPostData['name']['fr'] 		= $objProfilePostContentFrench->getName();
	$arrPostData['content']['fr'] 	= $objProfilePostContentFrench->getContent();
	$arrPostData['name']['en'] 		= $objProfilePostContentEnglish->getName();
	$arrPostData['content']['en'] 	= $objProfilePostContentEnglish->getContent();
	
	// Get the images...	
	$arrPatientProfileImages = SITE_IMAGE::getItemImageClassView(
		(int) $objProfilePost->getClassKeyId(), 
		(int) $objProfilePost->getId()
	);
	while (list($intRowIndex, $arrImageData) = each($arrPatientProfileImages)) {
		$arrPostData['attachments'][pathinfo($arrImageData['originalFileWebPath'], PATHINFO_BASENAME)] = pathinfo($arrImageData['originalFileWebPath'], PATHINFO_BASENAME);
	}
	
	// Form submit
	if ($Application->getForm()->isPost())
	{
		$arrPostData = $_POST;
		
		// 
		//	Validation ...
		//
		
		// 1. Check if there is a news post to save
		$blnHasEnglishPost = ((bool) strlen(
			trim($arrPostData['content']['en']) . 
			trim($arrPostData['name']['en']) . 
			(sizeof($arrPostData['category']['en']) ? '__category__' : '')
		));
		$blnHasFrenchPost  = ((bool) strlen(
			trim($arrPostData['content']['fr']) . 
			trim($arrPostData['name']['fr']) . 
			(sizeof($arrPostData['category']['fr']) ? '__category__' : '')
		));
		
		$blnContinue	   = (true === $blnHasEnglishPost) || (true === $blnHasFrenchPost);

		if (true === $blnContinue)
		{
			// begin validation [english]...
			if (true === $blnHasEnglishPost)
			{
				// Validate the content..
				$blnContinue &= (false === empty($arrPostData['name']['en'])) || (($arrErrors[] = 'Please enter a profile name for the english version') & false);
				$blnContinue &= (false === empty($arrPostData['content']['en'])) || (($arrErrors[] = 'Please enter profile content for the english version') & false);
				$blnContinue &= (false === empty($arrPostData['category']['en'])) || (($arrErrors[] = 'Please select at least one category for the english version') & false);
			}
			
			if (true === $blnHasFrenchPost)
			{
				// Validate the content..
				$blnContinue &= (false === empty($arrPostData['name']['fr'])) || (($arrErrors[] = 'Please enter a profile name for the french version') & false);
				$blnContinue &= (false === empty($arrPostData['content']['fr'])) || (($arrErrors[] = 'Please enter profile content for the french version') & false);
				$blnContinue &= (false === empty($arrPostData['category']['fr'])) || (($arrErrors[] = 'Please select at least one category for the french version') & false);
			}
			
			if (true === ((bool) $blnContinue))
			{
				if (
					(true === $blnHasEnglishPost) ||
					(true === $blnHasFrenchPost)
				) {
					// Save the news post
					$objProfilePost->setOwnerUserId($Application->getUser()->getId());
					$objProfilePost->setActive(ACTIVE_STATUS_ENABLED);
				}
				
				// Create the news post....
				if (true === $blnHasEnglishPost)
				{
					// Save the categories
					$arrPatientProfilesCategories = PATIENT_PROFILES_CATEGORY::getMultiInstance(array(
						'columns'		=> 	array('a.id'),
						'inner_join'	=>	array('patient_profiles_categories ncs' => 'ncs.id = a.patient_profiles_category_id AND ncs.lang = "en"'),
						'filter'		=>	array('a.patient_profiles_id' => (int) $objProfilePost->getId())		
					));
					
					while(list($intIndex, $objProfileCategory) = each($arrPatientProfilesCategories)) {
						$objProfileCategory->delete();
					}
					
					reset($arrPostData['category']['en']);
					while(list($intIndex, $intPatientProfilesCategoryId) = each($arrPostData['category']['en']))
					{
						$objProfileCategory = PATIENT_PROFILES_CATEGORY::newInstance(); 	
						$objProfileCategory->setPatient_Profiles_Category_Id((int) $intPatientProfilesCategoryId);
						$objProfileCategory->setPatient_Profiles_Id((int) $objProfilePost->getId());
						$blnContinue &= $objProfileCategory->save();
					}
					
					// Save the content
					$objProfilePostContentEnglish->setName($arrPostData['name']['en']);
					$objProfilePostContentEnglish->setContent($arrPostData['content']['en']);
					$objProfilePostContentEnglish->setPatient_Profiles_Id((int) $objProfilePost->getId());
					$blnContinue &= $objProfilePostContentEnglish->save();
					
					$arrMessage[] = 'English profile saved successfully.';
				}
				
				
				if (true === $blnHasFrenchPost)
				{
					// Save the categories
					$arrPatientProfilesCategories = PATIENT_PROFILES_CATEGORY::getMultiInstance(array(
						'columns'		=> 	array('a.id'),
						'inner_join'	=>	array('patient_profiles_categories ncs' => 'ncs.id = a.patient_profiles_category_id AND ncs.lang = "fr"'),
						'filter'		=>	array('a.patient_profiles_id' => (int) $objProfilePost->getId())		
					));
					
					while(list($intIndex, $objProfileCategory) = each($arrPatientProfilesCategories)) {
						$objProfileCategory->delete();
					}
					
					reset($arrPostData['category']['fr']);
					while(list($intIndex, $intPatientProfilesCategoryId) = each($arrPostData['category']['fr']))
					{
						$objProfileCategory = PATIENT_PROFILES_CATEGORY::newInstance(); 	
						$objProfileCategory->setPatient_Profiles_Category_Id((int) $intPatientProfilesCategoryId);
						$objProfileCategory->setPatient_Profiles_Id((int) $objProfilePost->getId());
						$blnContinue &= $objProfileCategory->save();
					}
					
					// Save the content
					$objProfilePostContentFrench->setname($arrPostData['name']['fr']);
					$objProfilePostContentFrench->setContent($arrPostData['content']['fr']);
					$objProfilePostContentFrench->setPatient_Profiles_Id((int) $objProfilePost->getId());
					$blnContinue &= $objProfilePostContentFrench->save();
					
					$arrMessage[] = 'French profile saved successfully.';
				}
				
				// Create the images....
				SITE_IMAGE::clearImagesFromOwner(
					(int) $objProfilePost->getClassKeyId(), 
					(int) $objProfilePost->getId()
				);
				
				if (
					(isset($_POST['attachments'])) &&
					(false === empty($_POST['attachments']))
				) {
					foreach($_POST['attachments'] as $strFileName => $imgPath) {
						$objItemImage = SITE_IMAGE::createPosition0Image(
							constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $imgPath, 
							(int) $objProfilePost->getClassKeyId(), 
							(int) $objProfilePost->getId()
						);
						if ($objItemImage->getId() > 0) {
							SITE_IMAGE::createItemImagePositions($objItemImage->getId());
						}
					}
					
					$_POST['attachments'] = NULL;
				}
				
				
				if ($blnContinue) {
					$objProfilePost->save();			
					$Application->getForm()->setUrlParam('profile-id', $objProfilePost->getId());
				}
			}
		}
	}

		
		
	$this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData());
	$this->renderPartial('IO::PAGE_PRELOADER', $this->getRequestData());
	$this->renderPartial('MENU::FONT_RESIZER', $this->getRequestData());
?>
<div id="main-wrapper">
	<?php 
		$arrTopMenuParams = array(
			'menuGroupId'	=> 	(int) $intLangId,
			'menuAttribute'	=>	'id="menu"'
		);
		
		if ($Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)) 
		{
			$arrTopMenuParams['extraLinks'][] = array(
				'text' 	=> '<i class="icon icon-list-alt"></i> ' . $Application->translate('Add a profile', 'Cree un profile'),
				'href'	=> '/patient-profiles/manage/'
			);
			
			if (true === $this->getViewData('blnSinglePostActive')) {
				$arrTopMenuParams['extraLinks'][] = array(
					'text' 	=> '<i class="icon icon-th-list"></i> ' . $Application->translate('Edit this profile', 'Modifier ce profile'),
					'href'	=> '/patient-profiles/manage/profile-id:' . (int) $this->getRequestParam('profile-id')
				);
			}
		}
		$this->renderPartial('MENU::TOP_MENU', $arrTopMenuParams); 
	?>
	
	
	<section id="pagetitle-wrapper">
    	<div class="row">
        	<div class="twelve columns">
            	<h3><?php echo($strPagename); ?></h3>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="index-2.html">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page">
							<a href="<?php echo($this->getViewData('canonicalUrl')); ?>"><?php echo($Application->translate('Patient Profiles', 'Profils de Patients')); ?></a>
						</li>
						<li><?php echo($strPagename); ?></li>
                    </ul>
                </div>
            </div>
        </div>    
    </section>
	
	
	<div class="clear"></div>
	<?php if (false === empty($arrErrors)) { ?>
		<div class="row">
			<div class="twelve columns">
				<div class="warning">
					The following errors occured:<br />
					<div style="font-size:11px;"><?php echo(implode('<br />', $arrErrors)); ?></div>
				</div>	
			</div>
		</div>
		<div class="clear"></div>
	<?php } ?>
	<?php if (false === empty($arrMessage)) { ?>
		<div class="row">
			<div class="twelve columns">
				<div class="success">
					<?php echo(implode('<br />', $arrMessage)); ?>
				</div>	
			</div>
		</div>
		<div class="clear"></div>
	<?php } ?>
	<form method="post" style="margin:25px" id="patientProfileForm">
		<a href="#" id="preview_btn" class="button small blue float-right" style="padding-left:10px;">
				<span class="icon icon-zoom-in"></span>&nbsp;
				<?php echo($Application->translate('Preview', 'Visionner')); ?>
			</a>
	<input type="hidden" name="profile-id" value="<?php echo((int) $Application->getRequestDispatcher()->getRequestParam('profile-id')); ?>" />
	<input type="hidden" name="activeLang" id="frm_activeLang" value="en" />
	<?php reset($arrPostData['attachments']); ?>
	<?php while (list($strFileName, $strFileUploadName) = each($arrPostData['attachments'])) { ?>
		<input type="hidden" name="attachments[<?php echo($strFileName); ?>]" value="<?php echo($strFileUploadName); ?>" />
	<?php } ?>
		<br />
		<div class="row">
			<div class="twelve columns">
				<ul class="_tabs">
					<li><a href="#english" data-lang="en">English Profile</a></li>
					<li><a href="#french" data-lang="fr">French Profile</a></li>         
				</ul>
				<div class="tab_container">
					<div id="english" class="tab_content">                            
						<label><strong>name:</strong></label> <input type="text" name="name[en]" value="<?php echo($arrPostData['name']['en']); ?>" />  <br />
						<label><strong>Content:</strong></label> <textarea class="editor" name="content[en]"><?php echo($arrPostData['content']['en']); ?></textarea>  <br />
						<div class="row">
							<div class="eight columns cat_container" data-lang-rel='en'>
								<label><strong>Categories:</strong></label>
								<?php $arrSelectedCategories = array_flip($arrPostData['category']['en']); ?>
								<?php reset($arrProfilesCategories['en']); ?>
								<?php while (list($intIndex, $arrPatientProfileCategory) = each($arrProfilesCategories['en'])) { ?>
									<div class="float-left" style="padding-right:30px;display:inline; width:210px;" data-rel-profile-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>">
										<input <?php echo(array_key_exists($arrPatientProfileCategory['id'], $arrSelectedCategories) ? ' checked="checked" ' : ''); ?> 
												type="checkbox" name="category[en][]" 
												value="<?php echo($arrPatientProfileCategory['id']); ?>" 
												id="patient_profiles_cat_<?php echo($arrPatientProfileCategory['id']); ?>" /> 
										<label for="patient_profiles_cat_<?php echo($arrPatientProfileCategory['id']); ?>" style="display:inline">
											<span class="cat_name"><?php echo($arrPatientProfileCategory['name']); ?></span>
											<a href="#" class="tooltip delete_cat float-right" title="Delete Category <?php echo($arrPatientProfileCategory['name']); ?>" 
												data-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>"><i class="icon icon-trash"></i></a>
											<span class="float-right">&nbsp;&nbsp;|&nbsp;&nbsp;</span>	
											<a href="#" class="tooltip edit_cat float-right" title="Edit Category <?php echo($arrPatientProfileCategory['name']); ?>" 
												data-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>" data-cat-name="<?php echo($arrPatientProfileCategory['name']); ?>">
												<i class="icon icon-edit"></i></a></label>
									</div>
								<?php } ?>
							</div>
							<div class="four columns cat_container">	
								<div class="float-right">
									<input type="text" name="new_cat[en]" class="inline float-right" value=""  />
									<a href="#" class="button small blue add_patient_profiles_cat float-right inline">Add Category</a>
								</div>
							</div>
						</div><br />                        
					</div>									
					
					<div id="french" class="tab_content">                            
						<label><strong>name:</strong></label> <input type="text" name="name[fr]" value="<?php echo($arrPostData['name']['fr']); ?>" />  <br />
						<label><strong>Content:</strong></label> <textarea class="editor" name="content[fr]"><?php echo($arrPostData['content']['fr']); ?></textarea>  <br />
						<div class="row">
							<div class="eight columns cat_container" data-lang-rel='fr'>
								<label><strong>Categories:</strong></label>
								<?php $arrSelectedCategories = array_flip($arrPostData['category']['fr']); ?>
								<?php reset($arrProfilesCategories['fr']); ?>
								<?php while (list($intIndex, $arrPatientProfileCategory) = each($arrProfilesCategories['fr'])) { ?>
									<div class="float-left" style="padding-right:30px;display:inline; width:210px;" data-rel-profile-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>">
										<input  <?php echo(array_key_exists($arrPatientProfileCategory['id'], $arrSelectedCategories) ? ' checked="checked" ' : ''); ?> 
												type="checkbox" 
												name="category[fr][]" 
												value="<?php echo($arrPatientProfileCategory['id']); ?>" 
												id="patient_profiles_cat_<?php echo($arrPatientProfileCategory['id']); ?>" /> 
										<label for="patient_profiles_cat_<?php echo($arrPatientProfileCategory['id']); ?>" style="display:inline">
											<span class="cat_name"><?php echo($arrPatientProfileCategory['name']); ?></span>
											<a href="#" class="tooltip delete_cat float-right" title="Delete Category <?php echo($arrPatientProfileCategory['name']); ?>" 
												data-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>"><i class="icon icon-trash"></i></a>
											<span class="float-right">&nbsp;&nbsp;|&nbsp;&nbsp;</span>	
											<a href="#" class="tooltip edit_cat float-right" title="Edit Category <?php echo($arrPatientProfileCategory['name']); ?>" 
												data-cat-id="<?php echo($arrPatientProfileCategory['id']); ?>" data-cat-name="<?php echo($arrPatientProfileCategory['name']); ?>">
												<i class="icon icon-edit"></i></a></label>
									</div>
								<?php } ?>
							</div>
							<div class="four columns cat_container">	
								<div class="float-right">
									<input type="text" name="new_cat[fr]" class="inline float-right" value=""  />
									<a href="#" class="button small blue add_patient_profiles_cat float-right inline">Add Category</a>
								</div>
							</div>
						</div><br />                      
					</div>                                                                  
				</div>
		
			</div>
		</div>
		<div class="row">
			<div class="twelve columns" style="padding-top:20px;">
				<div class="float-left inline tooltip" name="Click here to upload images" id="jquery-wrapped-fine-uploader"></div>
			</div>
			<?php reset($arrPostData['attachments']); ?>
			<?php if (false === empty($arrPostData['attachments'])) { ?>
				<div class="twelve columns">
					<ul class="pf-container block-grid five-up">
						<?php while (list($strFileName, $strFileUploadName) = each($arrPostData['attachments'])) { ?>
						<li class="news-img-thumb">
							<span class="link-zoom"> 
								<a 	class="fancybox" href="/static/tmp/user-uploads/<?php echo($strFileUploadName); ?>" 
									data-fancybox-group="gallery"><img src="/static/tmp/user-uploads/<?php echo($strFileUploadName); ?>" 
									class="fade" width="214" height="175" style="width:214px; height:175px;"/></a>   
							</span>
							<div class="box-grey">
								<p><?php echo(substr($strFileUploadName, strlen($strFileUploadName)-18, strlen($strFileUploadName))); ?></p>
								<p><?php echo(number_format(filesize(constant('__DEV_NULL_PATH__') . DIRECTORY_SEPARATOR . 'user-uploads' . DIRECTORY_SEPARATOR . $strFileUploadName) / 1024 / 1024, 2)); ?> MB</p>
								<a href="#" class="img-delete float-right button small blue" data-file-name="<?php echo($strFileUploadName); ?>" 
									data-img-id="<?php echo($Application->getCrypto()->encrypt($strFileUploadName)); ?>" style="position:relative">delete</a>
								<div class="clear"></div>
							</div>
						</li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		
		<div class="row">
			
			<div class="twelve columns">
				<div class="float-right">	
					<?php if ($objProfilePost->getId()) { ?>
						<input id="delete_patient_profiles_segment" type="submit" class="inline button large red" style="border:none;" value="Delete Patient Profile" />	
					<?php } ?>
					<input type="submit" class="inline button large green" style="border:none" value="<?php echo($objProfilePost->getId() ? 'Update Profile' : 'Save Profile'); ?>" />	
				</div>
			</div>
			<div class="clear"></div>
		</div>
		
	
		
	</form>
	<br />
	<!-- content section end here -->

    
	
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
<script type="text/javascript" src="/static/js/jquery/jquery.uploader.js"></script>
<script type="text/javascript">
	var activeLang		= LANG;
	var objApplication 	= new SLAWNER.APPLICATION.ORTHO();
	objApplication.initialise(function(event) {
		var objSlawnerFileUploader = SLAWNER.APPLICATION.SLAWNER_UPLOADER.initialise ({
			element: $('#jquery-wrapped-fine-uploader')[0],
			multiple: true,
			autoUpload: true,
			allowedExtensions: ['gif', 'png', 'jpg', 'jpeg'],
			request: {
				endpoint: "/api/v.206/upload-image/output:json"
			},
			callbacks: {
				onComplete: function(id, fileName, responseJSON) {
					if (responseJSON.success) {
						$('<input/>').attr({
							type: 'hidden',
							name: 'attachments[' + fileName + ']',
							value: responseJSON.fileuploadname	
						}).appendTo('form#patientProfileForm');
						
						var li = $('<li></li>').addClass('news-img-thumb').addClass('isotope-item');
						var span = $('<span></span>').addClass('link-zoom').appendTo(li);
						var imgLink = $('<a></a>').addClass('fancybox').attr({
							'href': '/static/tmp/user-uploads/' + responseJSON.fileuploadname,
							'data-fancybox-group': 'gallery' 
						}).appendTo(span);
						$('<img/>').attr({
							'src': '/static/tmp/user-uploads/' + responseJSON.fileuploadname,
							'class': 'fade',
							'width': 214,
							'height': 175,
							'style': 'width:214px; height:175px;'	
						}).appendTo(imgLink);
						var contDiv = $('<div></div>').addClass('box-grey').appendTo(li);
						$('<p></p>').html(responseJSON.fileuploadname.substring(responseJSON.fileuploadname.length - 18, responseJSON.fileuploadname.length)).appendTo(contDiv);
						strFileSize = responseJSON.filesize;
						if (! isNaN(responseJSON.filesize)) {
							strFileZise = (Number(responseJSON.filesize) / 1028) + ' MB';
						}
						$('<p></p>').html(strFileSize).appendTo(contDiv);
						$('<a></a>').attr({
							'class': 'img-delete float-right button small blue',
							'data-file-name': responseJSON.fileuploadname,
							'data-img-id': 	responseJSON.deletetoken,
							'style': 'position:relative'
						}).html('delete').appendTo(contDiv);
						$('<div></div>').addClass('clear').appendTo(contDiv);
						$('.pf-container').isotope('insert', li);
						$('.pf-container').isotope('reLayout');
					}
				}	
			},
			debug: true
		});
		
		
		$('.img-delete').live('click', function(e) {
			e.preventDefault();
			var c = $(this);
			$.ajax({
				type		: "POST",
				url			: '/backstore/api/v.206/delete-image/output:json',
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 
					token: $(this).attr('data-img-id')
				},
				xhrFields	: { withCredentials: true },
				success		: function(objXHTMLResponseObject) {
					$('.pf-container').isotope('remove', c.parents('li.news-img-thumb'));
					$('.pf-container').isotope('reLayout');
					c.parents('li.news-img-thumb').remove();
					$('form#patientProfileForm').find('input[value="' + c.attr('data-file-name') + '"]').remove();
				},
				error : function(jqXHR, textStatus, errorThrown) { },
				complete	: function() { }
			});	
		});
		
		$("form.jq").jqTransform();
		
		
		//Tab Jquery
		$(".tab_content").hide(); 
		$("ul._tabs li:first").addClass("active").show(); 
		$(".tab_content:first").show(); 
		$("ul._tabs li").click(function() {
			$("ul._tabs li").removeClass("active");
			$(this).addClass("active"); 
			$(".tab_content").hide(); 
			var activeTab = $(this).find("a").attr("href"); 
			$(activeTab).fadeIn(); 
			activeLang = $(this).find("a").attr("data-lang");
			$('#frm_activeLang').val(activeLang);
			return false;
		});	
		
		
		$('textarea.editor').redactor({ 
			focus: false,
			wym: false,
			autoresize: true,
			iframe: false,
			air: false,
			lang: 'en',
			css: "/static/css/style.css",
			imageUpload: '/backstore/api/v.206/upload-image/output:json',
			minHeight: 350,
			imageUploadErrorCallback: function (obj, json)
			{
				/*alert(json.error);
				alert(json.anothermessage);	*/
			},
			callback: function(obj)
			{
				/*if (typeof objPageBlockSegments[contentId]['blockContent'] != "undefined")
				{
					obj.setCode(new String(objPageBlockSegments[contentId]['blockContent']));	
				}*/
			}
		});
		
		$('.add_patient_profiles_cat').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
			event.preventDefault();
			var targetInput = $(this).parents('div').find('input[type="text"]:eq(0)');
			var inputButton = $(this);
			var selectedLang = activeLang;
			$.ajax({
				type		: "POST",
				url			: '/backstore/api/v.206/add-patient-profiles-category/output:json',
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 
					category: targetInput.val(),
					lang: selectedLang
				},
				xhrFields	: { withCredentials: true },
				success		: function(objXHTMLResponseObject) {
					if (true == objXHTMLResponseObject.success)
					{
						targetInput.val('');
						
						var divContainer = $('<div></div>').attr({
							'class': 'float-left',	
							'data-rel-profile-cat-id': objXHTMLResponseObject.data.id
						}).css({'padding-right': '30px', 'display': 'inline', 'min-width': '185px'});
						
						$('<input/>').attr({
							'type': 'checkbox',
							'checked': 'checked',
							'name': 'category[' + selectedLang + '][]',
							'value': objXHTMLResponseObject.data.name,
							'id':  'patient_profiles_cat_' + objXHTMLResponseObject.data.id
						}).appendTo(divContainer);
						
						var inputLabel = $('<label></label>').attr({
							'for': 'patient_profiles_cat_' + objXHTMLResponseObject.data.id
						}).css({'display': 'inline'}).html('<span class="cat_name">' + objXHTMLResponseObject.data.name + '</span>');
						
						inputLabel.appendTo(divContainer);
						
						var deleteLink = $('<a></a>').addClass('tooltip delete_cat float-right')
							.attr({
								'title': 'Delete Category ' + objXHTMLResponseObject.data.name,
								'data-cat-id': 	objXHTMLResponseObject.data.id
							});
							
						$('<i></i>').addClass('icon icon-trash').appendTo(deleteLink);	
						deleteLink.appendTo(inputLabel);
												
						$('<span></span>').addClass('float-right').html('&nbsp;&nbsp;|&nbsp;&nbsp;').appendTo(inputLabel);
							
						var editLink = $('<a></a>').addClass('tooltip edit_cat float-right')
							.attr({
								'title': 'Edit Category ' + objXHTMLResponseObject.data.name,
								'data-cat-id': 	objXHTMLResponseObject.data.id,
								'data-cat-name': objXHTMLResponseObject.data.name
							});	
							
						$('<i></i>').addClass('icon icon-edit').appendTo(editLink);	
						editLink.appendTo(inputLabel);
						
						divContainer.appendTo($('div.cat_container[data-lang-rel="' + selectedLang + '"]'));
					}
					else
					{
						alert(objXHTMLResponseObject.errors.join("\n"));	
					}
				},
				error : function(jqXHR, textStatus, errorThrown) { },
				complete	: function() { }
			});	
		});
		
		$('.edit_cat').live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
			event.preventDefault();
			var intCategoryId 	= Number($(this).attr('data-cat-id')),
				strCatName 		= $(this).attr('data-cat-name');
			$.fallr('show', {
				position: 'center',
				icon: 'pen',
				content: '<p>Edit Category: <b>' + strCatName + '</b></p>'
						+ '<form>'
						+     '<input id="cat_name" placeholder="Category Name" value="' + strCatName + '" type="text"/'+'>'
						+     '<input id="cat_id" value="' + intCategoryId + '"  type="hidden"/'+'>'
						+ '</form>',
				buttons : {
					button1 : {text: 'Submit', onclick: function() {
						intCatId 		= 	Number($(this).children('form').children('input#cat_id').val());
						strNewCatName 	=	$(this).children('form').children('input#cat_name').val();
						
						$.ajax({
							type		: "POST",
							url			: '/backstore/api/v.206/update-category-name/output:json',
							dataType	: "json",
							timeout		: 30000,
							cache		: false,
							processData	: true,
							data		: { 
								category_id: intCatId,
								category_name: strNewCatName,
								category_type: '<?php echo($Application->getRequestDispatcher()->getController()); ?>'
							},
							xhrFields	: { withCredentials: true },
							success		: function(objXHTMLResponseObject) {
								if (true == objXHTMLResponseObject.success)
								{
									$('div.tipsy').hide();
									$('label[for="patient_profiles_cat_' + intCatId + '"]  span.cat_name').html(strNewCatName);
									$('label[for="patient_profiles_cat_' + intCatId + '"]').effect("highlight", {}, 3000);
									$.fallr('hide');
								}
								else
								{
									alert(objXHTMLResponseObject.errors.join("\n"));	
								}
							},
							error : function(jqXHR, textStatus, errorThrown) { },
							complete	: function() { }
						});	
						
					}},
					button4 : {text: 'Cancel'}
				}		
			});
		});
		
		$('.delete_cat').live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
			event.preventDefault();
			var intCategoryId 	= Number($(this).attr('data-cat-id'));
			var parentObject 	= $('div[data-rel-profile-cat-id="' + intCategoryId + '"]');
			if (! confirm('Are your sure you want to delete this category?\nThis action is not undoable and will affect all other profiles with the same category.')) {
				return false;	
			}
			$.ajax({
				type		: "POST",
				url			: '/backstore/api/v.206/delete-patient-profile-category/output:json',
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 
					category_id: intCategoryId
				},
				xhrFields	: { withCredentials: true },
				success		: function(objXHTMLResponseObject) {
					if (true == objXHTMLResponseObject.success)
					{
						$('div.tipsy').hide();
						parentObject.remove();
					}
					else
					{
						alert(objXHTMLResponseObject.errors.join("\n"));	
					}
				},
				error : function(jqXHR, textStatus, errorThrown) { },
				complete	: function() { }
			});	
		});
		
		$('#delete_patient_profiles_segment').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
			event.preventDefault();
			if (confirm('Are you sure you want to delete this patient profile?')) {
				$.ajax({
					type		: "POST",
					url			: '/backstore/api/v.206/deactivate-patient-profile/output:json',
					dataType	: "json",
					timeout		: 30000,
					cache		: false,
					processData	: true,
					data		: { 
						profile_id: <?php echo((int) $this->getRequestParam('profile-id')); ?>
					},
					xhrFields	: { withCredentials: true },
					success		: function(objXHTMLResponseObject) {
						if (true == objXHTMLResponseObject.success)
						{
							window.location.href = '/' + activeLang.toLowerCase() + '/news';
						}
						else
						{
							alert(objXHTMLResponseObject.errors.join("\n"));	
						}
					},
					error : function(jqXHR, textStatus, errorThrown) { },
					complete	: function() { }
				});	
			}
			return (false);
		});
		
		
		$('a#preview_btn').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
			event.preventDefault();
			$('#patientProfileForm').attr({
				'action': '/patient-profiles/preview',
				'target': '_blank'
			});
			$('#patientProfileForm').submit();
			$('#patientProfileForm').removeAttr('action').removeAttr('target');
			return (false);
		});
		
		$(".fancybox").fancybox({
			padding: 0,
			openEffect : 'elastic',
			openSpeed  : 250,
			closeEffect : 'elastic',
			closeSpeed  : 250,
			closeClick : true,
			maxWidth: 560,
			maxHeight: 350,
			scrolling: 'no',
			centerOnScroll: true,
			autoCenter: true,
			helpers : {
				overlay : {opacity : 0.65},
				media : {}
			},
			afterLoad: function() {
				
			}
		});	
		$('.fancybox-wrap ').css({
			width: '500px',
			height: '350px'	
		}).center();	
		
		$(window).load(function(){
			var $container = $('.pf-container');
			$container.isotope({
				filter: '*',
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: false
				}
			});
		});
	});
</script>	
</body>
</html>