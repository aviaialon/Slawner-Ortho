<?php
	$Application 		= $this->getApplication();
	$strApplicationStaticResPath = $Application->getBaseStaticResourcePath();
	$objPageBlocks 		= ($Application->getPageBlocks());	
	$intLangId 			= ($Application->translate(1, 2));
	$arrCategories		= $this->getViewData('arrNewsCategories');
	$objPagination  	= $this->getViewData('objPagination');
	$objCommPagination	= $this->getViewData('objNewsCommentPagination');
	$arrPopularPosts	= $this->getViewData('arrPopularNewsPost');
	$arrArchivedPosts	= $this->getViewData('arrArchivedNewsPost');
	$arrNewsTags		= $this->getViewData('arrNewsTag');
	$arrLatestComments	= $this->getViewData('arrLatestComments');
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
				'text' 	=> '<i class="icon icon-list-alt"></i> ' . $Application->translate('Add a news post', 'Cree un article'),
				'href'	=> '/news/manage/'
			);
			
			if (true === $this->getViewData('blnSinglePostActive')) {
				$arrTopMenuParams['extraLinks'][] = array(
					'text' 	=> '<i class="icon icon-th-list"></i> ' . $Application->translate('Edit this news post', 'Modifier cette article'),
					'href'	=> '/news/manage/news-id:' . (int) $this->getRequestParam('post')
				);
			}
		}
		$this->renderPartial('MENU::TOP_MENU', $arrTopMenuParams); 
	?>
	
	<section id="pagetitle-wrapper">
    	<div class="row">
        	<div class="twelve columns content" data-content-id="8">
            	<?php echo($objPageBlocks->getBlockGroup(8)->getExecutedBlockContent()); ?>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="index-2.html">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page">
							<a href="<?php echo($this->getViewData('canonicalUrl')); ?>"><?php echo($Application->translate('News', 'Nouvelles')); ?></a>
						</li>
						<?php if (true === $this->getViewData('blnArchiveActive')) { ?>
							<li><?php echo($Application->translate('Archive', 'Archives') . ' (' . $this->getViewData('archiveDate') . ')'); ?></li>
						<?php } ?>
						<?php if (true === $this->getViewData('blnCategoryActive')) { ?>
							<li><?php echo($Application->translate('Category', 'Catégorie') . ': ' . $this->getViewData('selectedCategory')); ?></li>
						<?php } ?>
						<?php if (true === $this->getViewData('blnTagActive')) { ?>
							<li>Tag: <?php echo($this->getViewData('tag')); ?></li>
						<?php } ?>
						<?php if (true === $this->getViewData('blnSinglePostActive')) { ?>
							<?php $strPostTitle = $arrPaginationData[0]['title']; ?>
							<li><?php echo($strPostTitle); ?></li>
						<?php } ?>
                    </ul>
                </div>
            </div>
        </div>    
    </section>
	
	
	
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
            <div class="eight columns">  
				<?php reset($arrPaginationData); ?>
				<?php if (true === empty($arrPaginationData)) { ?>
					<div class="row">
						<div class="twelve columns">
							<div class="warning"><?php echo($Application->translate(
								'Sorry, no news posts to display at this time.', 
								'Désolé, aucun article de presse à afficher pour le moment.'
							)); ?></div>
						</div>
					</div>
				<?php } ?>
				<?php foreach($arrPaginationData as $intIndex => $arrPaginationData) { ?>
			    <div class="blog-post">
                	<div class="row">
                    	<div class="three columns">                                   	
                            <div class="post-info">
                            	<div class="post-date">
                                	<img src="<?php echo($strApplicationStaticResPath); ?>images/post_type_img.png" alt="" />
                                	<p><?php echo($arrPaginationData['post_date']); ?></p>
                                </div>                                
                                <ul>
                                    <li class="author-icon"><a href="#"><?php echo($arrPaginationData['ownerName']); ?></a></li>                            
                                    <li class="comment-icon"><a href="#"><?php echo($arrPaginationData['comment_count']); ?> <?php echo($Application->translate('Comments', 'Commentaires')); ?></a></li>
                                    <li class="tag-icon">
										<?php
											$arrTagsLinks = array();
											$arrTags = explode(',', $arrPaginationData['tags']);
											while (list($intIndex, $strTag) = each($arrTags)) {
												$arrTagsLinks[] = '<a href="' . $this->getViewData('canonicalUrl') . '/tag:' . $strTag . '">' . $strTag . '</a>';
											}
											echo (implode(', ', $arrTagsLinks));
										?>
									</li>
									<?php if(false === empty($arrPaginationData['news_categories'])) { ?>
										<li class="sil"></li>
										<li class="tag-icon"><?php echo($Application->translate('Categories', 'Catégories')); ?>:</li>
										<?php foreach(explode(',', $arrPaginationData['news_categories']) as $intIndex => $strNewsCategoryData) { ?>
											<?php 
												$arrNewsCategoryData 	= explode(':', $strNewsCategoryData);
												$strCategoryName 		= array_shift($arrNewsCategoryData);
												$intCategoryId 			= array_shift($arrNewsCategoryData);
											?>
											<li>
												<a style="margin-left:20px" 
													href="<?php echo($this->getViewData('canonicalUrl') . '/category:' . ((int) $intCategoryId)); ?>">- <?php echo($strCategoryName); ?></a></li>
										<?php } ?>		
									<?php } ?>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="nine columns">
                            <div class="post-content">
                                <h4><a href="<?php echo($this->getViewData('canonicalUrl') . '/post:' . ((int) $arrPaginationData['id'])); ?>">
									<?php echo($arrPaginationData['title']); ?></a></h4>
								<?php if (false === empty($arrPaginationData['imagePosition10'])) { ?>
									<?php $arrPostImages = explode(',', $arrPaginationData['imagePosition10']); ?>
									<?php if(count($arrPostImages) > 1) { ?>
										<div class="post-slide large">	
											<?php while (list($intIndex, $strImageUrl) = each($arrPostImages)) { ?>
												<?php if (false === empty($strImageUrl)) { ?>
													<div data-src="<?php echo($strImageUrl); ?>"></div>
												<?php } ?>
											<?php } ?>	
										</div>
									<?php } else { ?>		   
										<img src="<?php echo($arrPostImages[0]); ?>" alt="" />
									<?php } ?>
								<?php } ?>	
                                             
                                <p><?php echo(
									(true === $this->getViewData('blnSinglePostActive') ? $arrPaginationData['content'] : strip_tags(substr($arrPaginationData['content'], 0, 150), '<a>') . '...')
								); ?></p>
                                <?php if (true === $this->getViewData('blnIsAdmin')) { ?>
                                    <a class="button small green float-left" 
                                        href="<?php echo($this->getViewData('canonicalUrl') . '/manage/news-id:' . ((int) $arrPaginationData['id'])); ?>">
                                        <?php echo($Application->translate('Edit', 'Modifier')); ?>
                                    </a>
                                    <a class="button small red float-left" id="delete_news_segment" href="#" style="margin-left:8px;">
                                        <?php echo($Application->translate('Delete', 'Supprimer')); ?>
                                    </a>
                                <?php } ?>
								<?php if (false === $this->getViewData('blnSinglePostActive')) { ?>
                                	<a href="<?php echo($this->getViewData('canonicalUrl') . '/post:' . ((int) $arrPaginationData['id'])); ?>" class="button small blue float-right">
										<?php echo($Application->translate('Continue Reading', 'En lire plus')); ?>
									</a>
								<?php } ?>	
                            </div>
                        </div> 
                        
                    </div>   
                </div>
				<?php } ?>    
                
				<?php if (true === $this->getViewData('blnSinglePostActive')) { ?>
					<div class="row">
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
							
							<!-- Begin of Comment -->
							<div id="comment">
								<?php if($this->getViewData('intCommentCount') > 0) { ?>
									<h5><?php echo($this->getViewData('intCommentCount') . ' ' . $Application->translate('responses to', 'commentaires pour')); ?> 
										"<?php echo($strPostTitle); ?>"</h5>
								<?php } else { ?>		
									<h5><?php echo($Application->translate('Be the first to share your comments', 'Soyez le premier à partager vos commentaires')); ?></h5>
								<?php }  ?>		
								<ol class="commentlist">
									<?php foreach($this->getViewData('arrNewsComments') as $intCommentId => $arrCommentData) { ?>	
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
									<input type="hidden" name="postId" value="<?php echo((int) $this->getRequestParam('post')); ?>" />
									<input type="hidden" name="captchaChallenge" value="<?php echo($this->getViewData('captchaChallenge')); ?>" />
									<fieldset>                        
										<input type="text" name="name" class="textfield" id="name" value="" placeholder="<?php echo($Application->translate('Enter your name', 'Entrez votre nom')); ?>" /> 
										<label><?php echo($Application->translate('Name', 'Nom')); ?> <em>*</em></label>    
										<input type="text" name="email" class="textfield" id="email" value="" placeholder="<?php echo($Application->translate('Enter your email', 'Entrez votre email')); ?>" />  
										<label>Email <em>*</em></label>     
										<input type="text" name="subject" class="textfield" id="subject" value="" placeholder="<?php echo($Application->translate('Enter a subject', 'Entrez un sujet')); ?>" />
										<label><?php echo($Application->translate('Subject', 'Sujet')); ?></label>
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
				
                <!-- begin pagination -->
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
            </div>
			
			
                    
            <div class="four columns">         
                <aside>
                    <h5><?php echo($Application->translate('Categories', 'Catégories')); ?></h5>
                    <ul class="sidebar-list">
						<?php while(list($intIndex, $arrCategoryData) = each($arrCategories)) { ?>
							<?php $strClass = ((((int) $this->getRequestParam('category')) == ((int) $arrCategoryData['category_id']) && (false === $this->getVIewData('blnPreviewPostActive'))) ? 'blue_text' : ''); ?>
                        	<li><a class="<?php echo($strClass); ?>" href="<?php echo($this->getViewData('canonicalUrl')); ?>/category:<?php echo($arrCategoryData['category_id']); ?>">
								<?php echo($arrCategoryData['category_name']); ?>
							</a></li>                    
						<?php } ?>
                    </ul>
                </aside>
                
                <aside>
                    <h5><?php echo($Application->translate('Popular Post', 'Nouvelles populaire')); ?></h5>
                    <ul class="popular-list">
						<?php while(list($intIndex, $arrPopularNewsPostData) = each($arrPopularPosts)) { ?>
						<?php
							$arrImages 			= explode(',', $arrPopularNewsPostData['imagePosition11']);
							$strImagePath 		= array_rand(array_flip($arrImages), 1);
							$strNewsPpostUrl 	= $this->getViewData('canonicalUrl') . '/post:' . ((int) $arrPopularNewsPostData['id']);
							$strStripedContent 	= strip_tags($arrPopularNewsPostData['content']);
						?>
                        <li>
                            <a href="<?php echo($this->getViewData('canonicalUrl') . '/post:' . ((int) $arrPopularNewsPostData['id'])); ?>">
								<img src="<?php echo($strImagePath); ?>" alt="" border="0" /></a>                                                
                            <p class="popular-title">
								<a href="<?php echo($strNewsPpostUrl); ?>">
									<?php echo($arrPopularNewsPostData['title'] . ' - ' . substr($strStripedContent, 0, 20) . '...'); ?></a>
							</p>                        
                            <p class="comment-count"><a href="<?php echo($strNewsPpostUrl); ?>">
								<?php echo($arrPopularNewsPostData['comment_count'] . ' ' . $Application->translate('Comments', 'Commentaires')); ?></a></p>   
                        </li>
						<?php } ?>             
                    </ul>
                </aside>
                
                <aside>
                    <h5><?php echo($Application->translate('Options', 'Options')); ?></h5>
                    <ul class="_tabs">
                        <li><a href="#archives">Archives</a></li>
                        <li><a href="#comments"><?php echo($Application->translate('Comments', 'Commentaires')); ?></a></li>
                        <li><a href="#text"><?php echo($Application->translate('Share', 'Social')); ?></a></li>                
                    </ul>
                    <div class="tab_container">
                        <div id="archives" class="tab_content">                            
                            <ul class="archive-list">
								<?php while(list($intIndex, $arrArchivedNewsPostData) = each($arrArchivedPosts)) { ?>
									<?php $strArchiveLink = $this->getViewData('canonicalUrl') . '/archive:' . date('y-m', strtotime($arrArchivedNewsPostData['archived_date'])); ?>
                                	<li><p class="month-arch"><a href="<?php echo($strArchiveLink); ?>">
										<?php echo($arrArchivedNewsPostData['archived_date']); ?></a></p>
										<p class="post-count"><a href="<?php echo($strArchiveLink); ?>">
										<?php echo($arrArchivedNewsPostData['post_count']); ?> 
										<?php echo($Application->translate('posts', 'articles')); ?></a></p></li>
								<?php } ?>
                            </ul>                                  
                        </div>
                                                            
                        <div id="comments" class="tab_content">                            
                        	<?php if (false === empty($arrLatestComments)) { ?>
                            <ul class="comments-list">
                            	<?php while(list($intIndex, $arrCommentPostData) = each($arrLatestComments)) { ?>
                                <?php $strTagLink = $this->getViewData('canonicalUrl') . '/post:' . $arrCommentPostData['newsId']; ?>
                                <li>
                                    <p class="comment-info">
                                    	<span class="moment" data-moment="<?php echo($arrCommentPostData['post_date']); ?>"></span> 
                                        <?php echo($Application->translate('by', 'par')); ?> <a href="<?php echo($strTagLink); ?>"><?php echo($arrCommentPostData['name']); ?></a> 
                                        <?php echo($Application->translate('on', '')); ?>
                                    </p>
                                    <p class="title-with-comment"><a href="<?php echo($strTagLink); ?>"><?php echo(substr($arrCommentPostData['comment'], 0, 50)); ?>...</a></p>
                                </li>
                                <?php } ?>
                            </ul>       
                            <?php } ?>                            
                        </div>
                                                            
                        <div id="text" class="tab_content">                     	                          
                            <p>Lorem ipsum dolor amet, consectetur adipite scinelit vestibulum vel quam sitare amet odio ultricies dapbus acer vitae augue duis nulla nunc dignissim</p>                                                                 
                        </div>                                                                  
                    </div>
                </aside>
                
                <aside>
                    <h5><?php echo($Application->translate('Tag Cloud', 'Nuage de Tags')); ?></h5>
                    <div class="tag-cloud"> 
                    	        
                    	<?php while(list($intIndex, $arrNewsTagData) = each($arrNewsTags)) { ?>
							<?php $strTagLink = $this->getViewData('canonicalUrl') . '/tag:' . $arrNewsTagData['tag']; ?>
                        	<a href="<?php echo($strTagLink); ?>">
								<?php echo(ucwords(strtolower($arrNewsTagData['tag']))); ?> </a>
						<?php } ?>                                  
                    </div>
                </aside>
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
	$('#delete_news_segment').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
		event.preventDefault();
		if (confirm('Are you sure you want to delete this news post?')) {
			$.ajax({
				type		: "POST",
				url			: '/backstore/api/v.206/deactivate-news-post/output:json',
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 
					news_id: <?php echo((int) $this->getRequestParam('news-id')); ?>
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