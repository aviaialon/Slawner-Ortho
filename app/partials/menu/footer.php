<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_FOOTER extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{	
		$this->Application 			 		= APPLICATION::getInstance();
		$this->strApplicationStaticResPath 	= $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
<!-- footer start here -->
<footer>
	<div class="row">
		<div class="two columns mobile-two">
			<ul class="footer-list">
				<li><a href="#"><?php echo($this->Application->translate('Home', 'Acceuil')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Ask the Expert', 'Demandez à l\'expert')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Our Locations', 'Nos emplacements')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Testimonials', 'Témoignages')); ?></a></li>
			</ul>
		</div>
		<div class="two columns mobile-two">
			<ul class="footer-list no-border">
				<li><a href="#"><?php echo($this->Application->translate('About Us', 'À propos de nous')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Newsroom', 'Rédaction')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Jobs (Hiring!)', 'Offres d\'emploi')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Contact Us', 'Contactez-nous')); ?></a></li>
			</ul>             
		</div>
		<div class="two columns mobile-two">
			<ul class="footer-list">
				<li><a href="#"><?php echo($this->Application->translate('Press Kit', 'Dossier de presse')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Support', 'Support')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Privacy', 'Drois')); ?></a></li>
				<li><a href="#"><?php echo($this->Application->translate('Legal', 'Légal')); ?></a></li>
			</ul>   
		</div>
		<div class="three columns mobile-two">
			<ul class="footer-list-address">
				<li>
					<p><span class="bold"><?php echo($this->Application->translate('Head Office', 'Siège social')); ?>:</span><br /></p>
					<div class="icon_loc">5713, ch. de la Côte-des-Neiges<br/>Montréal, QC, H3S 1Y7</div>
				</li>
				<li>
					<div class="icon_phone">
						<a href="tel:<?php echo(constant('__CONTACT_PHONE__')); ?>"><?php echo(constant('__CONTACT_PHONE__')); ?></a><br />
						<a href="tel:<?php echo(constant('__CONTACT_PHONE__')); ?>"><?php echo(constant('__CONTACT_PHONE__')); ?></a>
					</div>
					<div class="icon_mail"><a href="mailto:<?php echo(constant('__INFO_EMAIL__')); ?>"><?php echo(constant('__INFO_EMAIL__')); ?></a></div>
				</li>
			</ul>   
		</div>
		<div class="three columns">
			<div class="copyright">
				<div class="g-plusone" data-annotation="none" data-width="300"></div>
				<p>&copy; Copyright <?php echo(date('Y') . ' ' . constant('__SITE_NAME__')); ?><br/><?php echo($this->Application->translate('All Rights Reserved', 'Tous droits réservés')); ?></p>
			</div>
		</div>	
	</div>
	
	
	<div class="social">
		<!-- data-angle="160" -->
		<div class="social_bubble">
			<div class="supersocialshare" 
				 data-networks="facebook,google,twitter" 
				 data-url="<?php echo(constant('__SITE_URL__') . $this->Application->translate($this->Application->getEnglishCanonicalUrl(), $this->Application->getFrenchCanonicalUrl())); ?>" 
				 data-angle="180"
				 data-orientation="line"></div>
		</div>
	</div>
	<div class="lang-selector-view"> 
		<a class="select">
			<span><img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/<?php echo($this->Application->translate('ca', 'ca')); ?>_small.png" width="18" height="14"  /> 
				<strong><?php echo($this->Application->translate('English', 'Français')); ?></strong></span>
		</a>
		<div class="popover">
			<div class="arrow"></div>
			<h4><?php echo($this->Application->translate('Select your Language', 'Choisissez votre language')); ?></h4>
			<ul>
				<li><a href="<?php echo($this->Application->getEnglishCanonicalUrl()); ?>"<?php echo($this->Application->translate(' class="selected"', '')); ?>>
					<img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/ca_big.png" width="34" height="25" /> <span>English</span></a></li>
				<li><a href="<?php echo($this->Application->getFrenchCanonicalUrl()); ?>"<?php echo($this->Application->translate('', ' class="selected"')); ?>>
					<img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/ca_big.png" width="34" height="25" /> <span>Français</span></a></li>
			</ul>
		</div>
		<div class="lang-selector-overlay"></div>
	</div>
	<form action="#subscribe" method="post" id="subscribe">
		<input type="text" value="" name="email-subscribe" 
			alt="<?php echo($this->Application->translate('Subscribe, get the latest news!', 'Abonnez-vous')); ?>" 
			title="<?php echo($this->Application->translate('Enter your e-mail', 'Entrez votre e-mail')); ?>" class="input-off" id="email-subscribe-field">
		<input type="submit" value="" title="Subscribe" name="subscribe" class="">
	</form>
</footer>
<!-- footer end here -->
<?php	
	}
}