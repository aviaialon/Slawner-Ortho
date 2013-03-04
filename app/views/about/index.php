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
        	<div class="twelve columns content" data-content-id="9">
            	<?php echo($objPageBlocks->getBlockGroup(9)->getExecutedBlockContent()); ?>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="/">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page"><a href="#"><?php echo($Application->translate('About', 'A propos')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>    	       
    </section>
    
		
	<section id="content-wrapper">
        <div class="row">
            <div class="six columns content" data-content-id="10">
                <?php echo($objPageBlocks->getBlockGroup(10)->getExecutedBlockContent()); ?>
            </div>
            <div class="six columns">
                <h5 class="highlight-blue highlight-padded">
                	<?php echo($Application->translate('Founder note', 'Note du fondateur')); ?>
                </h5>
                <div class="content" data-content-id="12">
	                <?php echo($objPageBlocks->getBlockGroup(12)->getExecutedBlockContent()); ?>
	            </div>
                <div class="panel">
                	<blockquote>
                		<p>In any situation, the best thing you can do is the right thing; 
                			the next best thing you can do is the wrong thing; the worst thing 
                			you can do is nothing.</p>
                			<cite>Theodore Roosevelt</cite>
                	</blockquote>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="twelve columns">
                <div class="divider"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="three columns">       	
                <div class="content" data-content-id="16">
					<?php echo($objPageBlocks->getBlockGroup(16)->getExecutedBlockContent()); ?>
				</div>
            </div>
			
            <div class="nine columns">
                <div class="row">
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team1.jpg" alt="" />
                            <div class="box-blue">
                                <h5>Dave Nesbitt</h5>
                                <p class="job-position">Aeolus C.E.O</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team2.jpg" alt="" />
                            <div class="box-blue">
                                <h6>Mike Lancaster</h6>
                                <p class="job-position">Project Manager</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team3.jpg" alt="" />
                            <div class="box-blue">
                                <h6>Paul Savvides</h6>
                                <p class="job-position">Marketing Manager</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team4.jpg" alt="" />
                            <div class="box-blue">
                                <h5>Brian Warren</h5>
                                <p class="job-position">Developer</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team5.jpg" alt="" />
                            <div class="box-blue">
                                <h5>Tony Straver</h5>
                                <p class="job-position">UX Designer</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="four columns mobile-two">
                    	<div class="team_wrap">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/sample_images/team6.jpg" alt="" />
                            <div class="box-blue">
                                <h6>Chris Seymour</h6>
                                <p class="job-position">Front end Developer</p>
                                <ul class="socials-list">
                                    <li><a href="#" title="facebook" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/facebook.gif" alt="" /></a></li>
                                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/twitter.gif" alt="" /></a></li>
                                    <li><a href="#" title="rss" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/rss.gif" alt="" /></a></li>
                                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($strApplicationStaticResPath); ?>images/socials/youtube.gif" alt="" /></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>                           
                </div>
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
        
        <?php /*
        <div class="row contact-wrap-info">
            <div class="four columns mobile-two">
            	<img src="<?php echo($strApplicationStaticResPath); ?>images/icons/icon123.png" alt="" class="img-left" />
            	<h5><?php echo($Application->translate('Office Hours', 'Heures d\'ouverture')); ?></h5>
                <p><?php echo($Application->translate('Monday to Friday', 'Lun. au Ven.')); ?> / 8:00am - 6:00pm<br/><?php echo($Application->translate('Sunday', 'Dim.')); ?> / 9:00am - 4:00pm</p>
            </div>
            <div class="four columns mobile-two">            
            	<img src="<?php echo($strApplicationStaticResPath); ?>images/icons/icon108.png" alt="" class="img-left" />
                <h5><?php echo($Application->translate('Head Office', 'Siège social')); ?></h5>
                <p>5713, ch. de la Côte-des-Neiges<br />Montréal, QC, H3S 1Y7</p>
            </div>
            <div class="four columns mobile-two">
            	<img src="<?php echo($strApplicationStaticResPath); ?>images/icons/icon231.png" alt="" class="img-left" />
                <h5><?php echo($Application->translate('Contact Info', 'Contact')); ?></h5>
                <p><?php echo($Application->translate('Phone', 'Tel')); ?> : 1-855-731-8989<br/>Email : info@slawner.com </p>
            </div>
        </div>
		 */ ?>
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