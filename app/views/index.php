<?php
	$Application 	= $this->getApplication(); 
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
            	<div class="box-yellow content" data-content-id="5">
                    <?php echo($objPageBlocks->getBlockGroup(5)->getExecutedBlockContent()); ?>
             	</div>
            </div>
		</div>
        
    	<div class="row">
			<div class="three columns">
				<h5 class="header_title"><?php echo($Application->translate('Latest News', 'Nouvelles Récentes')); ?></h5>		
				<?php $this->renderPartial('MODULES::CONTENT::HP_LATEST_NEWS', $this->getRequestData()); ?>
			</div>
			
			
			<div class="nine columns homepage_patient_profiles">
                <?php $this->renderPartial('PATIENT_PROFILES::HOME_PAGE_PROFILES', $this->getRequestData()); ?>
                <div class="twelve columns">
                	
					
					<h5 class="header_title">Testimonials</h5>	
					<!-- backstore/static/images/userLogin2.png -->
					<div class="carousel-content-testimonials">
						<ul class="slides">
							<li>
									
								<div class="testi-container">                        	
				                    <div class="testi-text">
				                        <blockquote>
				                            <p>
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo,
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et 
												cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. Quisque dignissim, 
												ante sit amet.
											</p>
				                        </blockquote>                              
				                    </div>                        
				                    <div class="clear"></div>                                                                     
				                </div>
								<div class="testi-baloon"></div>
								<div class="testi-image">
				                    <img src="/static/images/sample_images/testi-people4.jpg" alt="">                                              
				                </div>
								<div class="testi-name">
									Avi Aialon<br>
									<span class="company-name">SpaceShip Ltd</span>
								</div>
								
							</li>
							
							<li>
									
								<div class="testi-container">                        	
				                    <div class="testi-text">
				                        <blockquote>
				                            <p>
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo,
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et 
												cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. Quisque dignissim, 
												ante sit amet.
											</p>
				                        </blockquote>                              
				                    </div>                        
				                    <div class="clear"></div>                                                                     
				                </div>
								<div class="testi-baloon"></div>
								<div class="testi-image">
				                    <img src="/static/images/sample_images/testi-people4.jpg" alt="">                                              
				                </div>
								<div class="testi-name">
									Avi Aialon<br>
									<span class="company-name">SpaceShip Ltd</span>
								</div>
								
							</li>
							
							<li>
									
								<div class="testi-container">                        	
				                    <div class="testi-text">
				                        <blockquote>
				                            <p>
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo,
												Lorem dignissim, ante sit amet imperdiet ultricies, felis sit enim luctus leo, et 
												cursus leo libero in nisi. Donec sit amet ipsum velit, a faucibus. Quisque dignissim, 
												ante sit amet.
											</p>
				                        </blockquote>                              
				                    </div>                        
				                    <div class="clear"></div>                                                                     
				                </div>
								<div class="testi-baloon"></div>
								<div class="testi-image">
				                    <img src="/static/images/sample_images/testi-people4.jpg" alt="">                                              
				                </div>
								<div class="testi-name">
									Avi Aialon<br>
									<span class="company-name">SpaceShip Ltd</span>
								</div>
								
							</li>
						</ul>
					</div>		
					<!-- -->
					
	            </div>	
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
