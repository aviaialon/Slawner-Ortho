<?php
	$strApplicationStaticResPath = $this->getApplication()->getBaseStaticResourcePath();
	$objPageBlocks 	= ($this->getApplication()->getPageBlocks());	
	$intLangId 		= ($this->getApplication()->translate(1, 2));
	
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
		
		$this->renderPartial('MODULES::SLIDER::HOMEPAGE_SLIDER', $this->getRequestData());
	?>
	
    <!-- content section start here -->
    <section id="content-wrapper">
    	<div class="shadow"></div>
		
		<div id="k2ModuleBox131" class="k2ItemsBlock cols four feat">
			<div class="row">    	
				<div class="colsInner three columns">
					<?php $this->renderPartial('MENU::QUICK_MENU', $this->getRequestData()); ?>
				</div>
				
				<div class="colsInner six columns">
					<div class="col_container content" data-content-id="1">
						<?php echo($objPageBlocks->getBlockGroup(1)->getExecutedBlockContent()); ?>
					</div>
				</div>
				
				<div class="colsInner three columns last">
					<div class="col_container content" data-content-id="2">
						<?php echo($objPageBlocks->getBlockGroup(2)->getExecutedBlockContent()); ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">    	
            <div class="twelve columns top2">
            	<h5 class="brands-title"><?php echo($Application->translate('Brands We Carry', 'Nos Marques')); ?></h5>
                <ul class="brands-box">
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client1.png" alt="" /></li>
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client2.png" alt="" /></li>
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client3.png" alt="" /></li>
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client4.png" alt="" /></li>
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client5.png" alt="" /></li>
                    <li><img src="<?php echo($strApplicationStaticResPath); ?>/images/sample_images/client6.png" alt="" /></li>
                </ul>                
            </div>
        </div>     
		
		
		<div class="row">
			 <div class="four columns">
            	<div class="box-blue content" data-content-id="3">
                    <?php echo($objPageBlocks->getBlockGroup(3)->getExecutedBlockContent()); ?>
                </div>
            </div>
            <div class="four columns">
            	<div class="box-green content" data-content-id="4">
                    <?php echo($objPageBlocks->getBlockGroup(4)->getExecutedBlockContent()); ?>
                </div>
            </div>
            <div class="four columns">
            	<!--<div class="box-yellow content" data-content-id="5">
                    <?php echo($objPageBlocks->getBlockGroup(5)->getExecutedBlockContent()); ?>
             </div>-->
             <!-- tets -->
		<article class="latest-news-hp">
              <figure class="post_img">
                <a href="#" title="Latest News" class="imgborder clearfix thumb listing">
                  <img src="http://liquidfolio.queldorei.com/wp-content/uploads/2013/01/olen-460x345.jpg" class="attachment-blog_small wp-post-image" alt="olen" />
                </a>
              </figure>
              <div class="post_format">
                <a href="#"></a>
              </div>
              <div class="post_area_title">
                <h2 class="entry-title">
                  <a href="#">Latest News</a>
                </h2>
                <div class="postcategories">
                  <a href="#/category/design/" title="View all posts in design" rel="category tag">
                    design
                  </a>
                  / 
                  <a href="#/category/illustration/" title="View all posts in illustration" rel="category tag">
                    illustration
                  </a>
                  / 
                  <a href="#/category/small/" title="View all posts in small" rel="category tag">
                    small
                  </a>
                </div>
              </div>
              <div class="entry-content">
                <p>
                  Donec et fermentum mauris. Quisque sit amet purus sit amet ligula ultrices ullamcorper. Quisque malesuada diam in lacus interdum luctus at lorem.
                  <br />
                  Image is by 
                  <a href="http://dunsky.ru/">
                    Fil Dunsky
                  </a>
                </p>
                <div class="aligncenter">
                  
                  <a href="#" class="more-link qd_button_small">
                    Read more
                  </a>
                </div>
              </div>
              <div class="postmeta">
                
                <span class="postdata">
                  <a href="#" rel="bookmark" title="Permanent Link to SMALL POST WITH IMAGE">
                    2013/01/20
                  </a>
                </span>
                
                <a href="##comments" class="commentslink"  title="Comment on SMALL POST WITH IMAGE">
                  4
                </a>
                
                <span class="share_box"  data-url="#"></span>
              </div>
              
            </article>
		<!-- test eof -->
             	
             	<?php /*?><div class="team_wrap patient-profile">
					<img src="/static/images/sample_images/team1.jpg" class="patient-profile-img" alt="">
				    <div class="box-blue">
				        <h5>Dave Nesbitt</h5>
				        <p class="patient-profile-desc">
							Frankie Jones, Certified Nursing Assistant (CNA), student, and unilateral congenital 
							below elbow amputee...
						</p>
				        <ul class="socials-list">
				            <li><a href="#" title="facebook" class="tooltip"><img src="/static/images/socials/facebook.gif" alt=""></a></li>
				            <li><a href="#" title="twitter" class="tooltip"><img src="/static/images/socials/twitter.gif" alt=""></a></li>
				            <li><a href="#" title="rss" class="tooltip"><img src="/static/images/socials/rss.gif" alt=""></a></li>
				            <li><a href="#" title="youtube" class="tooltip"><img src="/static/images/socials/youtube.gif" alt=""></a></li>
				        </ul>
				        <a href="#" class="dark_blue button small float-right">Read More</a>
				    </div>
				</div><?php */?>
				
            </div>
		</div>
        
    	<div class="row">
			<div class="three columns">
				<!-- testimonials -->
				<div class="widget">
					<div class="header">Testimonials</div>
					<div class="reviews-t reviewsTestimonials">
						<div class="list-carousel coda bx">
							<ul class="bx-slider" data-autoslide="0" data-autoslide_on="0">
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. Quisque dignissim, ante sit amet.
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus 
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. 
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo,
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
							</ul>
						</div>
						<div class="autor coda-author"></div>
						<div class="reviews-b"></div>
					</div>
				</div>
				<!-- testimonials EOF -->
			</div>
			
			<div class="nine columns homepage_patient_profiles">
                <?php $this->renderPartial('PATIENT_PROFILES::HOME_PAGE_PROFILES', $this->getRequestData()); ?>
            </div>
			
            <div class="twelve columns">
                <div class="featured-box content"  data-content-id="6">
                     <?php echo($objPageBlocks->getBlockGroup(6)->getExecutedBlockContent()); ?>
                </div>
            </div>
        </div>
		
		
		
		
		<?php /*?>
		<div class="row">
            <div class="twelve columns">
                <div class="featured-box content"  data-content-id="7">
                     <?php echo($objPageBlocks->getBlockGroup(7)->getExecutedBlockContent()); ?>
                </div>
            </div>
        </div>
		<?php */?>
		
		
        
		<?php /*?>
		<div class="row">
			<div class="twelve columns">
				<!-- testimonials -->
				<div class="widget">
					<div class="header">Testimonials</div>
					<div class="reviews-t reviewsTestimonials">
						<div class="list-carousel coda bx">
							<ul class="bx-slider" data-autoslide="0" data-autoslide_on="0">
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. Quisque dignissim, ante sit amet.
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus 
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. 
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
								<li>
									<div class="panel-wrapper">Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo,
										<div class="panel-author"><img class="alignleft" src="http://nimble.dream-demo.com/new/wp-content/themes/dt-nimble/timthumb.php?src=/new/wp-content/uploads/2012/02/f4.jpg&#038;zc=1&#038;w=30&#038;h=30" width="30" height="30"  style="opacity: 1; visibility: visible;" />
											<p class="author-name">Avi Aialon</p>
											<span class="author-position">developer</span></div>
									</div>
								</li>
							</ul>
						</div>
						<div class="autor coda-author"></div>
						<div class="reviews-b"></div>
					</div>
				</div>
				<!-- testimonials EOF -->
			</div>
		</div>
		<?php */?>
    </section>
	
    <?php $this->renderPartial('MENU::FOOTER', $this->getRequestData()); ?>
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
<?php $this->renderPartial('MODULES::CALL::CALL_MODULE', $this->getRequestData()); ?>
</body>
</html>
