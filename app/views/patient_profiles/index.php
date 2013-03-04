<?php
	$Application 					= $this->getApplication();
	$strApplicationStaticResPath 	= $Application->getBaseStaticResourcePath();
	$objPageBlocks 					= ($Application->getPageBlocks());	
	$intLangId 						= ($Application->translate(1, 2));
	$objPagination  				= $this->getViewData('objPagination');
	$arrFilterCategories			= $this->getViewData('arrProfilesCategories');
	$objCommPagination				= $this->getViewData('objProfilesCommentPagination');
	/*
	$arrCategories		= $this->getViewData('arrNewsCategories');
	$arrPopularPosts	= $this->getViewData('arrPopularNewsPost');
	$arrArchivedPosts	= $this->getViewData('arrArchivedNewsPost');
	$arrNewsTags		= $this->getViewData('arrNewsTag');
	$arrLatestComments	= $this->getViewData('arrLatestComments');
	*/
	$arrPaginationData  = ((true === is_object($objPagination)) ? $objPagination->getPageData() : (($this->getViewData('arrPreviewData')) ? $this->getViewData('arrPreviewData') : array()));
	$arrPaginationLinks = ((true === is_object($objPagination)) ? $objPagination->getPaginationLinks() : array());
	$arrCommPageLinks   = ((true === is_object($objCommPagination)) ? $objCommPagination->getPaginationLinks() : array());
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
        	<div class="twelve columns content" data-content-id="13">
            	<?php echo($objPageBlocks->getBlockGroup(13)->getExecutedBlockContent()); ?>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="index-2.html">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page">
							<a href="<?php echo($this->getViewData('canonicalUrl')); ?>"><?php echo($Application->translate('Patient Profiles', 'Profils des patients')); ?></a>
						</li>
						<?php if (true === $this->getViewData('blnCategoryActive')) { ?>
							<li><?php echo($Application->translate('Category', 'Catégorie') . ': ' . $this->getViewData('selectedCategory')); ?></li>
						<?php } ?>
						<?php if (true === $this->getViewData('blnSinglePostActive')) { ?>
							<?php $strPostTitle = $arrPaginationData[0]['name']; ?>
							<li><?php echo($strPostTitle); ?></li>
						<?php } ?>
                    </ul>
                </div>
            </div>
        </div>    
    </section>
	
	<?php 
		/* TOP LAYERED CATEGORIES FILTER 
		if (false === empty($arrFilterCategories)) { ?>
		<?php if (
			(false === ((bool) $this->getViewData('selectedCategory'))) &&
			(true === (count($arrPaginationData) > 1))
		) { ?>
			<div id="pf-filter">
				<ul>
				<li><a href="#" class="selected" data-filter="*"><?php echo($Application->translate('All', 'Tous')); ?></a></li>
				<?php foreach($arrFilterCategories as $intIndex => $arrCategoryData) { ?>
					<li><a href="#" data-filter=".<?php echo(preg_replace('/[^A-Za-z0-9\s_]/', '', str_replace(' ', '_', strtolower($arrCategoryData['category_name'])))); ?>">
						<?php echo(ucwords(strtolower($arrCategoryData['category_name']))); ?></a></li>
				<?php } ?>
				</ul>
			</div>
		<?php } ?>	
	<?php } */ ?>
	<section id="content-wrapper">
		<?php if (true === ((bool) $this->getViewData('strTitleSubHeader'))) { ?>
		<div class="row">
			<div class="twelve columns">
				<h4 class="highlighttext2"><?php echo( $this->getViewData('strTitleSubHeader')); ?></h4>
				<hr class="sil" />
			</div>
		</div>
		<?php } ?>
        <div class="row">
            <div class="twelve columns">
				<?php reset($arrPaginationData); ?>
				<?php if (true === empty($arrPaginationData)) { ?>
					<div class="row">
						<div class="twelve columns">
							<div class="warning"><?php echo($Application->translate(
								'Sorry, no news profiles to display at this time.', 
								'Désolé, aucun profile à afficher pour le moment.'
							)); ?></div>
						</div>
					</div>
				<?php } ?>
				
				<?php
					/*
						PATIENT PROFILE SINGLE POST BEGIN
					*/
				?>
				<?php if (true === $this->getViewData('blnSinglePostActive')) { ?>
					<?php $arrPaginationData = array_shift($arrPaginationData); ?>
					<?php if (false === empty($arrPaginationData)) { ?>
					<div class="row">
						<div class="twelve columns">
							<div class="three columns">                                   	
								<div class="post-info">
									<div class="post-date">
										<img src="<?php echo($strApplicationStaticResPath); ?>images/post_type_img.png" alt="" />
										<p><?php echo($arrPaginationData['post_date']); ?></p>
									</div>                                
									<ul>                       
										<li class="comment-icon"><a href="#">
											<?php echo($arrPaginationData['comment_count']); ?> <?php echo($Application->translate('Comments', 'Commentaires')); ?></a></li>
										
										<?php if(false === empty($arrPaginationData['patient_profiles_categories'])) { ?>
											<li class="sil"></li>
											<?php foreach(explode(',', $arrPaginationData['patient_profiles_categories']) as $intIndex => $strNewsCategoryData) { ?>
												<?php 
													$arrNewsCategoryData 	= explode(':', $strNewsCategoryData);
													$strCategoryName 		= array_shift($arrNewsCategoryData);
													$intCategoryId 			= array_shift($arrNewsCategoryData);
												?>
												<li class="tag-icon" style="width:100%">
													<a href="<?php echo($this->getViewData('canonicalUrl') . '/category:' . ((int) $intCategoryId)); ?>"><?php echo($strCategoryName); ?></a></li>
											<?php } ?>		
										<?php } ?>
									</ul>
								</div>
							</div>
							<div class="nine columns"> 
								<h4><?php echo($arrPaginationData['name']); ?></h4>
								<?php if (false === empty($arrPaginationData['imagePosition15'])) { ?>
									<?php $arrPostImages = explode(',', $arrPaginationData['imagePosition15']); ?>
									<?php if(count($arrPostImages) > 1) { ?>
										<div class="post-slide large">	
											<?php while (list($intIndex, $strImageUrl) = each($arrPostImages)) { ?>
												<?php if (false === empty($strImageUrl)) { ?>
													<div data-src="<?php echo($strImageUrl); ?>" data-thumb="<?php echo($strImageUrl); ?>"></div>
												<?php } ?>
											<?php } ?>	
										</div>
									<?php } else { ?>
										<img src="<?php echo($arrPostImages[0]); ?>" alt="" />
									<?php } ?>	
								<?php } ?>	
                                             
                                <p><?php echo($arrPaginationData['content']); ?></p>
								<?php if (true === $this->getViewData('blnIsAdmin')) { ?>
									<?php if (false === $this->getViewData('blnPreviewPostActive')) { ?>
									<a class="button small green float-left" 
										href="<?php echo($this->getViewData('canonicalUrl') . '/manage/profile-id:' . ((int) $arrPaginationData['id'])); ?>">
										<?php echo($Application->translate('Edit', 'Modifier')); ?>
									</a>
									<a class="button small red float-left" id="delete_profile" href="#" style="margin-left:8px;">
										<?php echo($Application->translate('Delete', 'Supprimer')); ?>
									</a>
									<?php } else { ?>
									<a class="button small red float-right" onclick="javascript: window.close()" href="#" style="margin-left:8px;">
										<?php echo($Application->translate('Close Preview', 'Fermer')); ?>
									</a>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
						<div class="clear"></div>
						<div class="clear"></div>
						<div class="twelve columns">
							<!--Begin sharing box-->
							<div class="sharing-box">
								<div class="share-facebook">
									<!--[if !IE]>--> 
									<iframe id="facebookIframe-single" 
										src="http://www.facebook.com/plugins/like.php?href=<?php echo(urlencode($this->getViewData('canonicalShareUrl'))); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;locale=<?php echo($Application->translate('en_US', 'fr_FR')); ?>" 
										style="border:none; overflow:hidden; width:300px; height:45px;" >
									</iframe>
									<!--<![endif]--> 
								</div>
								<div class="share-social">
									<ul class="sharesocial-bloglist">
										<li><a href="#" title="share with google+" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/googleplus.gif" alt="" /></a></li>
										<li><a href="#" title="share with twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
										<li><a href="#" title="share with digg" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/digg.gif" alt="" /></a></li>
										<li><a href="#" title="share with stumbleupon" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/stumbleupon.gif" alt="" /></a></li>
										<li><a href="#" title="share with delicious" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/delicious.gif" alt="" /></a></li>
										<li><a href="#" title="share with technorati" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/technorati.gif" alt="" /></a></li>
										<li><a href="#" title="share with rss feed" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="eight columns"> 
							<!-- Begin Comments -->
							<div id="comment">
								<?php if(((int) $arrPaginationData['comment_count']) > 0) { ?>
									<h5><?php echo($arrPaginationData['comment_count'] . ' ' . $Application->translate('responses for', 'commentaires pour')); ?> <?php echo($strPostTitle); ?></h5>
								<?php } else { ?>		
									<h5><?php echo($Application->translate('Be the first to share your comments', 'Soyez le premier à partager vos commentaires')); ?></h5>
								<?php }  ?>		
								<ol class="commentlist">
									<?php foreach(($this->getViewData('arrProfilesComments')) as $intCommentId => $arrCommentData) { ?>	
									<li id="comment_<?php echo($arrCommentData['id']); ?>">
										<?php 
											$strImageUrl = 'http://www.gravatar.com/avatar/' . 
																$arrCommentData['email_hash'] . '?s=64&d=' . 
																urlencode(constant('__SITE_URL__') . '/' .$strApplicationStaticResPath . 'images/Avatar_silhouette-150x150.jpg'); 
										?>		
										<div class="avatar"><img src="<?php echo($strImageUrl); ?>" alt="" /></div>
										<div class="comment-text" >
											<h5 class="name"><?php echo($arrCommentData['name']); ?></h5>
											<small>
												<?php echo($arrCommentData['post_date']); ?> 
												<?php if ($Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)) { ?>
													<a class="delete" href="#" data-comment-id="<?php echo($arrCommentData['id']); ?>">
														<?php echo($Application->translate('delete', 'supprimer')); ?>
													</a>
												<?php } ?>
												<a class="reply" href="#" data-comment-id="<?php echo($arrCommentData['id']); ?>">
													<?php echo($Application->translate('Reply', 'Répondre')); ?>
												</a>
											</small>
											<p><?php echo($arrCommentData['comment']); ?></p>
										</div>
										
										<?php if (false === empty($arrCommentData['replies'])) { ?>
											<?php $intReplyCount = 0; ?>
											<?php $intReplyTotal = count($arrCommentData['replies']); ?>
											<ol>
												<?php foreach($arrCommentData['replies'] as $intIndex => $arrReplyCommentData) { ?>	
													<?php 
														$intReplyCount++;
														$strImageUrl = 'http://www.gravatar.com/avatar/' . 
																		$arrReplyCommentData['email_hash'] . '?s=64&d=' . 
																		urlencode(constant('__SITE_URL__') . '/' .$strApplicationStaticResPath . 'images/Avatar_silhouette-150x150.jpg'); 
														$strClass = ($intReplyCount >= 4 ? 'hidden' : '');				
													?>	
														<?php if (($intReplyCount == 4) && ($intReplyTotal > 3)) { ?>
															<li class="see-more-comments">
																<a href="#" style="padding:0px 10px 5px 10px" data-comment-id="<?php echo($arrCommentData['id']); ?>" 
																	class="button small white float-left see-more-comments tooltip" 
																	title="<?php echo($Application->translate('Load more replies', 'Charger plus de réponses')); ?>">...</a>
															</li>
														<?php } ?>
														<li class="<?php echo($strClass); ?>" id="comment_<?php echo($arrReplyCommentData['id']); ?>">
															<div class="avatar"><img src="<?php echo($strImageUrl); ?>" alt="" /></div>
															<div class="comment-text" >
																<h5><?php echo($arrReplyCommentData['name']); ?></h5>
																<small>
																	<?php echo($arrReplyCommentData['post_date']); ?> 
																	<?php if ($Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)) { ?>
																		<a class="delete" href="#" data-comment-id="<?php echo($arrReplyCommentData['id']); ?>">
																			<?php echo($Application->translate('delete', 'supprimer')); ?>
																		</a>
																	<?php } ?>
																	<a class="reply" href="#" data-comment-id="<?php echo($arrCommentData['id']); ?>">
																		<?php echo($Application->translate('Reply', 'Répondre')); ?>
																	</a>
																</small>
																<p><?php echo($arrReplyCommentData['comment']); ?></p>
															</div>
														</li>
												<?php } ?>
											</ol>
										<?php } ?>
									</li>
									<?php } ?>
								</ol>
								
								 <!-- begin comment pagination -->
								<?php if ((true === is_object($objCommPagination)) && ($objCommPagination->getTotalPages() > 1)) { ?>
								<br />
								<div class="blog-pagination float-right comment_pagination">
									<div class="pages blogpages">
										<?php while (list($intIndex, $arrPageLinkData) = each($arrCommPageLinks)) { ?>
											<?php
												$strTitle = '';
												switch ($arrPageLinkData['link_type']) {
													case ('first_page') : { $strTitle = $Application->translate('First page', 'Première page'); break;	}	
													case ('next_page') 	: { $strTitle = $Application->translate('Next page', 'Page suivante'); break;	}	
													case ('prev_page') 	: { $strTitle = $Application->translate('Previous page', 'Page précédente'); break;	}	
													case ('last_page') 	: { $strTitle = $Application->translate('Last page', 'Dernière page'); break;	}	
												}
												$strTooltipClass = (false === empty($strTitle) ? ' tooltip' : '');
											?>
											<a 	href="<?php echo($arrPageLinkData['href']); ?>" 
												class="<?php echo($arrPageLinkData['class'] . $strTooltipClass); ?>"
												title="<?php echo($strTitle); ?>"><?php echo($arrPageLinkData['text']); ?></a>
										<?php } ?>
									</div>
								</div>
								<?php } ?>
								<!-- end of comment pagination -->            
							</div>
							
							<!-- Begin of Comment Form -->
							<div id="commentform-wrap">	
								<h5><?php echo($Application->translate('Leave a comment', 'Laissez un commentaire')); ?></h5> 
								<form action="#" id="comment-form"> 
									<div id="message"></div>
									<input type="hidden" name="profile_id" value="<?php echo((int) $this->getRequestParam('profile-id')); ?>" />
									<input type="hidden" name="captchaChallenge" value="<?php echo($this->getViewData('captchaChallenge')); ?>" />
									<fieldset> 
										<label><?php echo($Application->translate('Name', 'Nom')); ?> <em>*</em></label>                           
										<input type="text" name="name" class="textfield" id="name" value="" /> 
										<label>E-mail <em>*</em></label> 
										<input type="text" name="email" class="textfield" id="email" value="" />                        
										<label><?php echo($Application->translate('Subject', 'Sujet')); ?></label>
										<input type="text" name="subject" class="textfield" id="subject" value="" /> 
										<textarea name="message" id="message" class="textarea" cols="2" rows="4"></textarea>
										<div class="clear"></div> 
										<label>&nbsp;</label>
										<div class="captcha">
											<em>*</em> <?php echo($Application->translate('Are you human', 'Êtes-vous humain')); ?>?&nbsp;&nbsp;
											<strong class="blue_text"><?php echo($this->getViewData('captchaFormula')); ?></strong> 
											<input type="text" name="captcha" id="captcha" value="<?php echo($intValue); ?>" />   
										</div>
										<a href="#" id="post-news-comment" class="button blue small float-right"><?php echo($Application->translate('Post your comment', 'Ajouter vos commentaire')); ?></a>
										<span class="loading" style="display: none;">Please wait..</span>
										<div class="clear"></div>
									</fieldset>
								</form>
							</div>
							<!-- End of Comment Form -->
						</div>
					</div>
					<?php } ?>
				<?php } else { ?>
					<?php
						/*
							PATIENT PROFILE LISTING BEGIN
						*/
					?>
					<?php if (false === empty($arrFilterCategories)) { ?>
						<?php if (
							(false === ((bool) $this->getViewData('selectedCategory'))) &&
							(true === (count($arrPaginationData) > 1))
						) { ?>
							<div id="pf-filter">
								<ul>
								<li><a href="#" class="selected" data-filter="*"><?php echo($Application->translate('All', 'Tous')); ?></a></li>
								<?php foreach($arrFilterCategories as $intIndex => $arrCategoryData) { ?>
									<li><a href="#" data-filter=".<?php echo(preg_replace('/[^A-Za-z0-9\s_]/', '', str_replace(' ', '_', strtolower($arrCategoryData['category_name'])))); ?>">
										<?php echo(ucwords(strtolower($arrCategoryData['category_name']))); ?></a></li>
								<?php } ?>
								</ul>
							</div>
						<?php } ?>	
					<?php } ?>
					<div class="clear"></div>
					<div id="patient_profile_container">
						<?php foreach($arrPaginationData as $intIndex => $arrPaginationData) { ?>
						<?php
							$arrCategoryFilters = array();
							$arrCategoryData = explode(',', $arrPaginationData['patient_profiles_categories']);
							array_walk(explode(',', $arrPaginationData['patient_profiles_categories']), function($strCategoryData, $intIndex) use(&$arrCategoryFilters){ 
								$arrCategoryFilters[] = preg_replace('/[^A-Za-z0-9\s_]/', '', str_replace(' ', '_', array_shift(explode(':', $strCategoryData))));
							});
							
							$strFrNameUrl	 = str_replace(' ', '_', ucwords($arrPaginationData['name']));
							$strFrNameUrl	 = preg_replace('/[^A-Za-z0-9\s_]/', '', $strFrNameUrl);
							$strProfileLink  = $this->getViewData('canonicalUrl') . '/' . $strFrNameUrl;
							$strProfileLink .= ':' . (int) $arrPaginationData['id'];
						?>
						<div class="three columns mobile-two patient-profile <?php echo(strtolower(implode(' ', $arrCategoryFilters))); ?>">
							<div class="team_wrap"><div class="profile_image_container">
								<a href="<?php echo($strProfileLink); ?>" title="<?php echo($Application->translate(
										'Click here to read ' . ucwords($arrPaginationData['name']) . ' profile', 
										'Cliquez ici pour lire le profil a ' . ucwords($arrPaginationData['name']))); ?>">
									<?php if (false === empty($arrPaginationData['imagePosition15'])) { ?>
										<?php $arrPostImages = explode(',', $arrPaginationData['imagePosition14']); ?>
											<?php if(count($arrPostImages) > 1) { ?>
												<div class="post-slide">	
													<?php while (list($intIndex, $strImageUrl) = each($arrPostImages)) { ?>
														<?php if (false === empty($strImageUrl)) { ?>
															<div data-src="<?php echo($strImageUrl); ?>"></div>
														<?php } ?>
													<?php } ?>	
												</div>
											<?php } else { ?>		   
												<img src="<?php echo($arrPostImages[0]); ?>" alt="" />
											<?php } ?>
										</a>
									<?php } else { ?>
										<img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/team<?php echo(number_format(mt_rand(1, 6), 0)); ?>.jpg" alt="" />
									<?php } ?>
								</a></div>
								<div class="box-blue smallpadding">
									<h5><?php echo($arrPaginationData['name']); ?></h5>
									<p class="patient-profile-desc">
										<?php echo(substr(strip_tags($arrPaginationData['content']), 0, 80)); ?>
										<?php echo(strlen(strip_tags($arrPaginationData['content'])) > 80 ? '...' : ''); ?>
									</p>
									<ul class="socials-list">
										<li><a href="#" title="facebook" class="tooltip"><img src="/static/images/socials/facebook.gif" alt="" /></a></li>
										<li><a href="#" title="twitter" class="tooltip"><img src="/static/images/socials/twitter.gif" alt="" /></a></li>
									</ul>
									<span class="float-left white"><?php echo($arrPaginationData['comment_count']); ?> <?php echo($Application->translate('Comments', 'Commentaires')); ?></span>
									<?php if (true === $this->getViewData('blnIsAdmin')) { ?>
                                        <a class="button small green"  style="left:0px;"  
                                        	href="<?php echo($this->getViewData('canonicalUrl') . '/manage/profile-id:' . ((int) $arrPaginationData['id'])); ?>">
                                            <?php echo($Application->translate('Edit', 'Modifier')); ?>
                                        </a>
                                        <a class="button small red float-left" id="delete_profile" href="#" style="margin-right:90px;">
                                            <?php echo($Application->translate('Delete', 'Supprimer')); ?>
                                        </a>
                                    <?php } ?>
									<a href="<?php echo($strProfileLink); ?>" class="dark_blue button small float-right"><?php echo($Application->translate('Read More', 'En Lire Plus')); ?></a>
								</div>
							</div>
						</div>
						<?php } ?>    
					</div>
					
					 <!-- begin of pagination -->
					<?php if ((true === is_object($objPagination)) && ($objPagination->getTotalPages() > 1)) { ?>
					<div class="blog-pagination">
						<div class="pages blogpages">
							<span class="pageof">
								Page <?php echo($objPagination->getCurrentPage() . ' ' . $Application->translate('of ', 'de ') . $objPagination->getTotalPages()); ?>
							</span>
							<?php while (list($intIndex, $arrPageLinkData) = each($arrPaginationLinks)) { ?>
								<?php
									$strTitle = '';
									switch ($arrPageLinkData['link_type']) {
										case ('first_page') : { $strTitle = $Application->translate('First page', 'Première page'); break;	}	
										case ('next_page') 	: { $strTitle = $Application->translate('Next page', 'Page suivante'); break;	}	
										case ('prev_page') 	: { $strTitle = $Application->translate('Previous page', 'Page précédente'); break;	}	
										case ('last_page') 	: { $strTitle = $Application->translate('Last page', 'Dernière page'); break;	}	
									}
									$strTooltipClass = (false === empty($strTitle) ? ' tooltip' : '');
								?>
								<a 	href="<?php echo($arrPageLinkData['href']); ?>" 
									class="<?php echo($arrPageLinkData['class'] . $strTooltipClass); ?>"
									title="<?php echo($strTitle); ?>"><?php echo($arrPageLinkData['text']); ?></a>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					<!-- end of pagination -->            
				<?php } ?>
            </div>
        </div> 
    </section>
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
<?php if (true === $this->getViewData('blnIsAdmin')) { ?>
<script type="text/javascript">
	$('#delete_profile').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
		event.preventDefault();
		if (confirm('Are you sure you want to delete this profile?')) {
			$.ajax({
				type		: "POST",
				url			: '/backstore/api/v.206/deactivate-patient-profile/output:json',
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 
					news_id: <?php echo((int) $this->getRequestParam('profile-id')); ?>
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
</script>
<?php } ?>
</body>
</html>