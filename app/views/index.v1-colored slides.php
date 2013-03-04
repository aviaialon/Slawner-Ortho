<?php
	$strApplicationStaticResPath = $this->getApplication()->getBaseStaticResourcePath();
	$intLangId = ($this->getApplication()->getSession()->get('lang') == 'fr' ? 2 : 1);
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
<?php $this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData()); ?>
</head>
<body>
<!-- Body preloader -->
<div id="jf-preloader">
	<div id="jf-indicator"></div>
	<div id="jf-preloader-logo"></div>
	<div id="jf-progress"></div>
</div>

<!-- Front resizer -->
<div id="ThemeStylePicker">
	<a id="jfontsize-plus" class="rslnk plus_minus tooltip_east" title="Increase the font size" href="#">+</a>
	<a id="jfontsize-default" class="rslnk tooltip_east" title="Reset the font size" href="#">A</a>
	<a id="jfontsize-minus" class="rslnk plus_minus tooltip_east" title="Decrease the font size" href="#">-</a>
	<a href="#" id="CloseThemeStylePicker">x</a>
</div>


<div id="main-wrapper">
	<?php $this->renderPartial('MENU::TOP_MENU', array(
		'menuGroupId'	=> 	(int) $intLangId,
		'menuAttribute'	=>	'id="menu"'
	)); ?>
	
    <!-- slideshow start here -->
	<section id="slideshow-wrapper">
        <div class="camera_wrap nocolor" id="camera-slide">
        	
			 <!-- slide 1 here -->
            <div data-src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/bg_metro_blue.jpg" data-thumb="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/slide-1.png">
                <div class="caption-image-left-people-1 moveFromBottom">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/slide-1.png" alt="" />
                </div>
                <div class="caption-text-right people moveFromTop">
                    <h1 style="color:#FFF">Part of the Rehabilitation Team Since 1952</h1>
                    <p style="color:#FFF"><strong>With over 60 years of experience</strong>, Slawner Ortho is a foremost leader in the field of Orthotics and Prosthetics.</p>
                    <p style="color:#FFF">At Slawner, we are focused on customized solutions using the most advanced technology and leading edge fabrication process.</p>
                    <a href="#" class="button large dark_blue buttonShadow">Find Out More</a>            
                </div>                
            </div>
			
            <!-- slide 2 here -->
            <div data-src="<?php echo($strApplicationStaticResPath); ?>/images/blank.png" data-thumb="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/map-thumb.jpg">                
                <div class="caption-text-left moveFromLeft">
                    <h1>6 Locations to Better Meet Your Needs.</h1>
                    <p>With 6 locations accross the greater Quebec area, Its easy to find a Slawner Ortho clinic near you.</p>
                    <p>Our multiple locations in and around Montreal serve numerous hospitals, thus providing timely and convenient service.</p>
					<a href="#" class="button large dark_blue buttonShadow">Find a Location Near You</a>   
                </div> 
                <iframe id="locationsMap" width="100%" height="100%" allowtransparency="1" frameborder="0" marginheight="0" marginwidth="0" scrolling="0" src="/map/large" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>               
            </div>
            
            <!-- slide 3 here -->
            <div data-src="<?php echo($strApplicationStaticResPath); ?>/images/blank.png" data-thumb="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/calendar.png">
            	<div class="caption-text-center moveFromTop">
                    <h1>Scheduling an Appointment has Never Been Easier!</h1>
                    <p>You can now schedule your appointment online!.</p>
					<p style="z-index:9800"><a href="#" class="button large dark_blue buttonShadow">Schedule an Appointment</a></p>        
                </div>
				<div class="caption-image-center moveFromBottom">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/slide3.png" alt=""  />
                </div>   
				<div class="caption-image-center moveFromTop">
					<img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/calendar.png" alt=""class="extra" style="float: left;margin-bottom: 3%;" />
                </div>
				<div class="caption-image-center moveFromRight">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/clock_yellow.png" alt=""class="extra" style="float:right;margin-bottom: 3%;" />
                </div>                             
            </div>
			
            <!-- slide 4 here -->
            <div data-src="<?php echo($strApplicationStaticResPath); ?>/images/blank.png" data-thumb="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/slide-1.png">
                <div class="caption-image-left-people-1 moveFromBottom">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/slide-1.png" alt="" />
                </div>
                <div class="caption-text-right people moveFromTop">
                    <h1>Part of the Rehabilitation Team Since 1952</h1>
                    <p><strong>With over 60 years of experience</strong>, Slawner Ortho is a foremost leader in the field of Orthotics and Prosthetics.</p>
                    <p>At Slawner, we are focused on customized solutions using the most advanced technology and leading edge fabrication process.</p>
                    <a href="#" class="button large dark_blue buttonShadow">Find Out More</a>            
                </div>                
            </div>
			
            <!-- slide 5 here -->
            <div data-src="<?php echo($strApplicationStaticResPath); ?>/images/blank.png" data-thumb="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/60_yr_badge.png">
				<div class="caption-text-left moveFromTop">
                    <h1>Celebrating Over 60 Years of Service.</h1>
                    <p>Slawner is celebrating over 60 years of leading edge fabrication, helping patients acheive independence with the utmost comfort and ease possible.</p>
					<a href="#" class="button large dark_blue buttonShadow">About Us</a>      
                </div>            
				<div class="caption-image-right-people moveFromRight">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/image-flash.png" alt="" style="margin-top: 8%;float: right;" />
                </div>
				<div class="caption-image-right-people moveFromBottom">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/slideshow/60_yr_badge.png" alt="" style="margin-top: 10%;float: right;margin-right: -15%;" />
                </div>
            </div>
        </div>
        <!-- <div id="slideshow-noscript"><h4>Hi, your javascript is off..!! for optimal results on this site please enable javascript in your browser</h4></div> -->       
    </section>
    <!-- slideshow end here -->
    
    <!-- content section start here -->
    <section id="content-wrapper">
    	<div class="shadow"></div>
    	
		<div class="row">
            <div class="four columns">
            	<div class="box-blue">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon56.png" alt="" class="metro-icon img-left" />
                    <div class="clear"></div>
                    <h5>Solution for Your Business</h5>
                    <p>Excepteur sint occaecat cupidatat non proident sunt in culpa eiusmod officia deserunt mollit anim id est laborum</p>
                    <a href="#" class="more-btn">Learn more</a>
                </div>
            </div>
            <div class="four columns">
            	<div class="box-green">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon27.png" alt="" class="metro-icon img-left" />
                    <div class="clear"></div>
                    <h5>Great Work with Low Cost</h5>
                    <p>Excepteur sint occaecat cupidatat non proident sunt in culpa eiusmod officia deserunt mollit anim id est laborum</p>
                    <a href="#" class="more-btn">Learn more</a>
                </div>
            </div>
            <div class="four columns">
            	<div class="box-yellow">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon59.png" alt="" class="metro-icon img-left" />
                    <div class="clear"></div>
                    <h5>Deliver with Complete of Idea</h5>
                    <p>Excepteur sint occaecat cupidatat non proident sunt in culpa eiusmod officia deserunt mollit anim id est laborum</p>
                    <a href="#" class="more-btn">Learn more</a>
                </div>
            </div>
            <!-- 
            <div class="six columns">         
                <aside>
                    <h5>Categories</h5>
                    <ul class="sidebar-list">
                        <li><a href="#">National News</a></li>                    
                        <li><a href="#">World of Sport</a></li>                                
                        <li><a href="#">Travel &amp; Accomodation</a></li>
                        <li><a href="#">Technology News</a></li>
                        <li><a href="#">Entertainment</a></li>
                    </ul>
                </aside>
            </div>
            
            <div class="six columns">
            	<h5>Recent Project</h5>
                <div class="carousel-content">
                    <ul class="slides">
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel1.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>                     
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel2.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel3.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel1.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>
                        
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel2.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>                    
                        <li><div class="link-zoom">
                            <a class="fancybox" href="images/portfolio_big/pf-big.jpg" title="Aeolus - Corporate Business Responsive">
                                <img src="images/sample_images/carousel3.jpg" alt="" class="fade" />
                            </a>                    
                        </div></li>                        
                    </ul>
                </div>
            </div>
            -->
        </div>
        
        
    	<div class="row">
            <div class="twelve columns">
                <div class="featured-box">
                    <div class="nine columns">
                        <h4>Aeolus is Clean, Minimal, and Simple theme to meet your needs</h4>
                        <p>Excepteur sint occaecat cupidatat non proident sunt in culpa qui officia deserunt mollit.</p>
                    </div>
                    <div class="three columns">
                        <a href="#" class="button large blue">Get a Quote</a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        
		<!-- experimental -->
		<!--
		<div id="k2ModuleBox131" class="k2ItemsBlock cols four feat">
			<ul>
				<li class="even firstItem">
					<div class="colsInner">
						<div class="itemThumbnail"> <img src="http://livedemo00.template-help.com/joomla_40996//images/home-1.png" alt=""> </div>
						<h3><a class="moduleItemTitle" href="/joomla_40996/index.php/component/k2/item/43-technologies">technologies</a></h3>
						<div class="moduleItemIntrotext">
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt laoreet dolore magna.
								aliquam erat volutpat. Ut wisi enim ad minim veniam.</p>
						</div>
						<div class="clr"></div>
						<div class="clr"></div>
						<a class="moduleItemReadMore" href="/joomla_40996/index.php/component/k2/item/43-technologies"> More </a> 
						<div class="clr"></div>
					</div>
				</li>
				<li class="odd">
					<div class="colsInner">
						<div class="itemThumbnail"> <img src="http://livedemo00.template-help.com/joomla_40996//images/home-2.png" alt=""></div>
						<h3><a class="moduleItemTitle" href="/joomla_40996/index.php/component/k2/item/44-medicine">medicine</a></h3>
						<div class="moduleItemIntrotext">
							<p>Ut wisi enim minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate.</p>
						</div>
						<div class="clr"></div>
						<div class="clr"></div>
						<a class="moduleItemReadMore" href="/joomla_40996/index.php/component/k2/item/44-medicine"> More </a> 
						<div class="clr"></div>
					</div>
				</li>
				<li class="even">
					<div class="colsInner">
						<div class="itemThumbnail"> <img src="http://livedemo00.template-help.com/joomla_40996//images/home-4.png" alt=""> </div>
						<h3><a class="moduleItemTitle" href="/joomla_40996/index.php/component/k2/item/45-energy">energy</a></h3>
						<div class="moduleItemIntrotext">
							<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per   inceptos himenaeos. Maecenas venenatis sollicitudin neque, vel rhoncus   sem suscipit id. Sed eleifend</p>
						</div>
						<div class="clr"></div>
						<div class="clr"></div>
						<a class="moduleItemReadMore" href="/joomla_40996/index.php/component/k2/item/45-energy"> More </a> 
						<div class="clr"></div>
					</div>
				</li>
				<li class="odd lastItem">
					<div class="colsInner">
						<h4 style="font-family:Oswald;font-size: 18px;line-height: 24px;text-transform: uppercase;
font-weight: normal; text-align:left;margin: 15px 0 18px 28px">Our Services:</h4>
						<ul class="itemList">
							<li class="firstItem"><a href="#">Hospitals</a></li>
							<li><a href="#">Physician Offices</a></li>
							<li><a href="#">Schools &amp; Athletics</a></li>
							<li><a href="#">Corporations</a></li>
							<li><a href="#">Government</a></li>
							<li><a href="#">Military</a></li>
							<li class="lastItem"><a href="#">Community</a></li>
						</ul>
						<div class="clr"></div>
					</div>
				</li>
			</ul>
		</div>
		-->
		
		<div class="colsInner six columns">
			<h4 style="font-family:Oswald;font-size: 18px;line-height: 24px;text-transform: uppercase;
font-weight: normal; text-align:left;margin: 15px 0 18px 28px">Our Services:</h4>
			<ul class="itemList">
				<li class="firstItem"><a href="#">Hospitals</a></li>
				<li><a href="#">Physician Offices</a></li>
				<li><a href="#">Schools &amp; Athletics</a></li>
				<li><a href="#">Corporations</a></li>
				<li><a href="#">Government</a></li>
				<li><a href="#">Military</a></li>
				<li class="lastItem"><a href="#">Community</a></li>
			</ul>
			<div class="clr"></div>
		</div>
		
		<div class="colsInner six columns">
			<h4 style="font-family:Oswald;font-size: 18px;line-height: 24px;text-transform: uppercase;
font-weight: normal; text-align:left;margin: 15px 0 18px 28px">Our Services:</h4>
			<ul class="itemList">
				<li class="firstItem"><a href="#">Hospitals</a></li>
				<li><a href="#">Physician Offices</a></li>
				<li><a href="#">Schools &amp; Athletics</a></li>
				<li><a href="#">Corporations</a></li>
				<li><a href="#">Government</a></li>
				<li><a href="#">Military</a></li>
				<li class="lastItem"><a href="#">Community</a></li>
			</ul>
			<div class="clr"></div>
		</div>
		
		
		<div class="colsInner six columns">
			<h4 style="font-family:Oswald;font-size: 18px;line-height: 24px;text-transform: uppercase;
font-weight: normal; text-align:left;margin: 15px 0 18px 28px">Our Services:</h4>
			<ul class="itemList">
				<li class="firstItem"><a href="#">Hospitals</a></li>
				<li><a href="#">Physician Offices</a></li>
				<li><a href="#">Schools &amp; Athletics</a></li>
				<li><a href="#">Corporations</a></li>
				<li><a href="#">Government</a></li>
				<li><a href="#">Military</a></li>
				<li class="lastItem"><a href="#">Community</a></li>
			</ul>
			<div class="clr"></div>
		</div>
		<!-- experimental EOF -->
		
        <div class="row">    	
            <div class="twelve columns top2">
            	<h5 class="client-title">As Seen On</h5>
                <ul class="client-box">
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
            <div class="three columns">
                <h1>Informative</h1>
            </div>
            <div class="nine columns">
            	<div class="front-desc">
                	<p>At vero eos et accusamus et iusto odio dignissimos ducimus blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi id est laborum et dolorum fuga harum quidem rerum facilis.</p>
                </div>
            </div>
            
            <div class="three columns">
                <h1>Effective</h1>
            </div>
            <div class="nine columns">
            	<div class="front-desc">
                	<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus emporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae.</p>
                </div>
            </div>
            
            <div class="three columns">
                <h1>Innovative</h1>
            </div>
            <div class="nine columns">
            	<div class="front-desc">
                	<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem utenim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam nisi ut aliquid commodi consequatur.</p>
                </div>
            </div>
    	</div> 
    </section>
    <!-- content section end here -->
    
    <section id="bottom-wrap">
    	<div class="row">
        	<div class="twelve columns text-center">
            	<img src="<?php echo($strApplicationStaticResPath); ?>/images/twitter-icon.png" alt="" />
                <!-- Begin of Twitter Box -->
                <div id="twitter-box">
                    <div id="twitter"></div>	
                </div>
                <!-- End of Twitter Box -->
                
                <!-- Noscript Notification when your Javascript not active -->
                <div id="twitter-noscript">                	
                    <p>Hi, your javascript is off..!! for optimal results on this site please enable javascript in your browser</p>
                </div>
                <!-- End of Noscript Notification when your Javascript not active -->
            </div>
        </div>
    </section>
        
    <!-- footer start here -->
    <footer>
    	<div class="row">
        	<div class="two columns mobile-two">
                <ul class="footer-list">
                	<li><a href="#">Developers</a></li>
                    <li><a href="#">Publisher</a></li>
                    <li><a href="#">Advertisers</a></li>
                    <li><a href="#">Internal</a></li>
                </ul>
            </div>
            <div class="two columns mobile-two">
                <ul class="footer-list no-border">
                	<li><a href="#">About Us</a></li>
                    <li><a href="#">Environment</a></li>
                    <li><a href="#">Jobs (Hiring!)</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>             
            </div>
            <div class="two columns mobile-two">
             	<ul class="footer-list">
                	<li><a href="#">Press Kit</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Legal</a></li>
                </ul>   
            </div>
            <div class="three columns mobile-two">
             	<ul class="footer-list-address">
                	<li><p>Cost Avenue. Silver Spring<br/>Jacko City, MD 20910, Indonesia</p></li>
                    <li><p>Phone : (111) 234 5678</p></li>
                </ul>   
            </div>
            <div class="three columns">
            	<div class="copyright">
                    <div class="g-plusone" data-annotation="none" data-width="300"></div>
                    <p>&copy; Copyright 2012 Aeolus Design<br/>All Rights Reserved</p>
                </div>
            </div>	
        </div>
		
		
		<div class="social">
			<!--<div class="main-wrapper"> 
				<a class="responsive-on-demand"></a>
				<div style="text-align:center">
					<div class="copyright">Copyright 2012 Anps | All Rights Reserved</div>
				</div>
				<div class="social-icons-wrapper">
					<div class="social-icons"><span class="announce clearfix">socialise</span><span class="social-icons-wrap"> <a target="_blank" href="http://twitter.com/AstudioTweet" class="twitter"><img alt="Twitter social icon" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/twitter.png"></a> <a target="_blank" href="facebook" class="facebook"><img alt="Facebook social icon" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/facebook.png"></a> <a target="_blank" href="linkedin" class="linkedin"><img alt="Linkedin social icon" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/linkedin.png"></a> <a target="_blank" href="Vimeo" class="vimeo"><img alt="Vimeo" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/vimeo.png"></a> <a target="_blank" href="Youtube" class="youtube"><img alt="Youtube social icon" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/youtube.png"></a> <a target="_blank" href="Envato" class="flickr"><img alt="Flickr social icon" src="http://anpsthemes.com/coolblue/wp-content/themes/coolblue/images/social-icons/flickr.png"></a> </span> </div>
				</div>
			</div>-->
			<!-- data-angle="160" -->
			<div class="social_bubble">
				<div class="supersocialshare" 
					 data-networks="facebook,google,twitter,linkedin,pinterest" 
					 data-url="http://codecanyon.net/" 
					 data-angle="180"
					 data-orientation="line"></div>
			</div>
		</div>


    </footer>
    <!-- footer end here -->
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
</body>
</html>