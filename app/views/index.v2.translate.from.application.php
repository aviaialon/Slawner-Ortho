<?php
	$strApplicationStaticResPath = $this->getApplication()->getBaseStaticResourcePath();
	$intLangId = ($this->getApplication()->getSession()->get('lang') == 'fr' ? 2 : 1);
	
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
					<div class="col_container content">
						<div class="itemThumbnail"> <img src="<?php echo($strApplicationStaticResPath); ?>/images/home-2.png" alt=""></div>
						<h3><a class="moduleItemTitle" href="#"><?php echo($Application->translate('Our Expertise', 'Notre Expertise')); ?></a></h3>
						<div class="moduleItemIntrotext">
							<p>
								<?php echo($Application->translate(
									'At Slawner Ortho, we are focused on customized solutions. Using the most advanced technology ' .
									'and leading edge fabrication processes, our mission is to help patients achieve independence with the utmost comfort and greater mobility. ', 
									'Chez Slawner Ortho, nous nous concentrons sur des solutions personnalisées. Avec l\'tilisation de la technologie la plus avancée et les principaux de procédés ' .
									'de fabrication de pointe, notre mission est \'aider les patients à atteindre, avec le plus grand confort,  l\'indépendance et une plus grande mobilité'
								)); ?>
								
							</p>
							<p><?php echo($Application->translate(
								'Our multiple locations in and around Montreal serve numerous hospitals, thus providing timely and convenient service.', 
								'Nos locations multiples, autour de Montréal, servent de nombreux hôpitaux et offrent un service rapide et pratique.'
							)); ?> </p>
							<p>
								<?php echo($Application->translate(
									'Let our team of Certified Orthotists and Prosthetists assist you in reaching your goals. Contact us for more information.', 
									'Laissez notre équipe d\'orthésistes et prothésistes certifiés vous aider à atteindre vos objectifs. Contactez-nous pour plus d\'informations.'
								)); ?> 
							</p>
						</div>
						<div class="clr"></div>
						<a href="#" class="button small blue float-right"><?php echo($Application->translate('Find a Location Near You', 'Trouver un emplacement près de chez vous')); ?></a>
						<div class="clr"></div>
					</div>
				</div>
				
				<div class="colsInner three columns last">
					<div class="col_container content">
						<div class="itemThumbnail">
							<img src="<?php echo($strApplicationStaticResPath); ?>/images/home-3.png" alt=""> </div>
						<h3><a class="moduleItemTitle" href="#"><?php echo($Application->translate('Why Slawner?', 'Pourquoi Slawner?')); ?></a></h3>
						<div class="moduleItemIntrotext">
							<p>
								<?php echo($Application->translate(
									'Over 55 years of experience and expertise establishes Slawner Ortho as one of the foremost leaders in the field of Orthotics and Prosthetics.', 
									'Avec plus de 60 ans d\'expérience et d\'expertise, Slawner c\'est établit comme l\'un des leaders dans le domaine de l\'orthétique et de la prothétique.'
								)); ?>
							</p>
							<p>
								<?php echo($Application->translate(
									'Slawner Ortho has long established its position as a forerunner of innovation and as an important member of the multidisciplinary team of rehabilitation specialists.', 
									'Slawner Ortho a, depuis longtemps, établi sa candidature en tant que précurseur d\'innovation et comme un membre important de ' .
									'l\'équipe multidisciplinaire en spécialistes en réadaptation.'
								)); ?>
							</p>
						</div>
						<div class="clr"></div>
						<div class="clr"></div>
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
            	<div class="box-blue content">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon123.png" alt="" class="metro-icon img-left" />
                    <h5><?php echo($Application->translate('Schedule an Appointment Online', 'Fixer un rendez-vous en ligne')); ?></h5>
                    <div class="clear"></div>
                    <p>
						<?php echo($Application->translate(
							'Scheduling an appointment with Slawner is fast and easy. Did you know that you can now schedule your appointments online from the ' . 
							'comfort of your own home? Click on the \'Schedule an Appointment\' button to continue.', 
							'Planifier un rendez-vous chez Slawner est rapide et facile. Saviez-vous que vous pouvez désormais planifier vos rendez-vous en ligne dans le confort de votre propre maison? ' . 
							'Cliquez sur «Planifier un rendez-vous» pour continuer.'
						)); ?>
					</p>
                    <a href="#" class="button dark_blue small buttonShadow"><?php echo($Application->translate('Schedule an Appointment', 'Planifier un rendez-vous')); ?></a>
                </div>
            </div>
            <div class="four columns">
            	<div class="box-green content">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon121.png" alt="" class="metro-icon img-left" />
                    <h5><?php echo($Application->translate('Health Care Professionals', 'Professionnels de la santé')); ?></h5>
                    <div class="clear"></div>
                    <p>
						<?php echo($Application->translate(
							'Slawner is proud to be the Montreal\'s foremost provider of orthotics, prosthetics and rehabilitative services for over 60 years. <br />' . 
							'We are your partners dedicated to improving the rehabilitative outcomes for patients.', 
							'Slawner est fier d\'être l\'un des plus important fournisseur d\'orthèses, prothèses et des services de réadaptation. ' . 
							'Nous sommes vos partenaires qui se consacrent à l\'amélioration des résultats de réadaptation pour les patients.'
						)); ?>
					</p>
                    <a href="#" class="button dark_blue small buttonShadow"><?php echo($Application->translate('Learn more', 'En savoir plus')); ?></a>
                </div>
            </div>
            <div class="four columns">
            	<div class="box-yellow content">
                    <img src="<?php echo($strApplicationStaticResPath); ?>/images/icons/icon59.png" alt="" class="metro-icon img-left" />
                    <h5>This is a placeholder</h5>
                    <div class="clear"></div>
                    <p>Excepteur sint occaecat cupidatat non proident sunt in culpa eiusmod officia deserunt mollit anim id est laborum</p>
                    <a href="#" class="more-btn">Learn more</a>
                </div>
            </div>
		</div>
        
    	<div class="row">
            <div class="twelve columns">
                <div class="featured-box">
                    <div class="nine columns">
                        <h4><?php echo($Application->translate('Questions? Comments? We would love to hear from you.', 'Des questions ou commentaires? Contactez nous!')); ?></h4>
                        <p>
							<?php echo($Application->translate(
								'Our staff are available weekdays from 8:00am - 6:00pm, and 9:00am - 4:00pm on Saturday.', 
								'Notre personnel est disponible en semaine de 8h00-18h00 et le samedi de 09h00-16h00.'
							)); ?>
						</p>
                    </div>
                    <div class="three columns">
                        <a href="#" class="button large blue buttonShadow"><?php echo($Application->translate('Click to Call', 'Nous Contacter')); ?></a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        
    </section>
	
    <?php $this->renderPartial('MENU::FOOTER', $this->getRequestData()); ?>
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
</body>
</html>