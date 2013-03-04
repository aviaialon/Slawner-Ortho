<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_HOMEPAGE_SLIDER extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strApplicationStaticResPath = $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
<section id="slideshow-wrapper">
	<div class="camera_wrap nocolor" id="camera-slide">
		
		<!-- slide 1 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/blank.png" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/60_yr_badge.png">
			<div class="caption-text-left moveFromTop">
				<h1><?php echo($this->Application->translate('Celebrating Over 60 Years of Service', 'Slawner célèbre de plus de 60 années de service')); ?>.</h1>
				<p>
					<?php echo($this->Application->translate(
						'Slawner is celebrating over 60 years of leading edge fabrication, helping patients achieve independence with the utmost comfort and ease possible.', 
						'Slawner célèbre plus de 60 ans de fabrication de pointe, aidant ces patients à atteindre l\'indépendance avec le maximum de confort et de facilité possible.'
					)); ?>
				</p>
				<a href="/about" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('About Us', 'À propos de nous')); ?></a>&nbsp;&nbsp;  
				<a href="/history" class="button large green buttonShadow"><?php echo($this->Application->translate('Our History', 'Notre Histoire')); ?></a>      
			</div>            
			<div class="caption-image-right-people moveFromRight">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/image-flash.png" id="slider-img-4-banner" alt="" style="margin-top: 2%;float: right;" />
			</div>
			<div class="caption-image-right-people moveFromBottom">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/60_yr_badge.png" id="slider-img-5-banner" alt="" style="margin-top: 10%;float: right;margin-right: -15%;" />
			</div>
		</div>
		
		<!-- slide 1 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/bg_metro_blue.jpg" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/slide-1.png">
			<div class="caption-image-left-people-1 moveFromBottom">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/slide-1.png" alt="" />
			</div>
			<div class="caption-text-right people moveFromTop">
				<h1 style="color:#FFF"><?php echo($this->Application->translate('Part of the Rehabilitation Team Since 1952', 'L\'équipe de réadaptation de choix depuis 1952')); ?></h1>
				<p style="color:#FFF"><strong><?php echo($this->Application->translate('With over 60 years of experience', 'Avec plus de 60 ans d\'expérience')); ?></strong>, 
					<?php echo($this->Application->translate(
						'Slawner Ortho is a foremost leader in the field of Orthotics and Prosthetics', 
						'Slawner c\'est établit comme l\'un des leaders dans le domaine de l\'orthétique et de la prothétique.'
					)); ?>.</p>
				<p style="color:#FFF">
					<?php echo($this->Application->translate(
						'At Slawner, we are focused on customized solutions using the most advanced technology and leading edge fabrication process.', 
						'Chez Slawner, on se consacrent a des solutions personnalisées à l\'aide de la technologie de fabrication la plus avancée.'
					)); ?>
				</p>
				<a href="/about" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('Find Out More', 'En savoir plus')); ?></a>            
			</div>                
		</div>
		
		<!-- slide 2 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/blank.png" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/map-thumb.jpg">                
			<div class="caption-text-left moveFromLeft">
				<h1><?php echo($this->Application->translate('6 Locations to Better Meet Your Needs', '6 Emplacements pour mieux répondre à vos besoins')); ?>.</h1>
				<p>
					<?php echo($this->Application->translate(
						'With 6 locations accross the greater Montreal area, Its easy to find a Slawner Ortho clinic near you.', 
						'Avec 6 emplacements dans la région de Montreal, il est facile de trouver une clinique Slawner Ortho près de chez vous.'
					)); ?>
				</p>
				<p>
					<?php echo($this->Application->translate(
						'Our multiple locations in and around Montreal serve numerous hospitals, thus providing timely and convenient service.', 
						'Nos emplacements multiples a Montréal servent de nombreux hôpitaux, offrant ainsi un service rapide et pratique.'
					)); ?>
				</p>
				<a href="/locations" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('Find a Location Near You', 'Trouver un emplacement près de chez vous')); ?></a>   
			</div> 
			<iframe id="locationsMap" width="100%" height="100%" allowtransparency="1" frameborder="0" marginheight="0" marginwidth="0" scrolling="0" src="/map/large" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>               
		</div>
		
		<!-- slide 3 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/blank.png" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/calendar.png">
			<div class="caption-text-center moveFromTop">
				<h1><?php echo($this->Application->translate('Scheduling an Appointment has Never Been Easier!', 'Planifier un rendez-vous a jamais été aussi facile!')); ?></h1>
				<p><?php echo($this->Application->translate('You can now schedule your appointment online!', 'Vous pouvez maintenant planifier votre rendez-vous en ligne!')); ?></p>
				<p style="z-index:9800"><a href="/appointment" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('Schedule an Appointment', 'Fixer un rendez-vous')); ?></a></p>        
			</div>
			<div class="caption-image-center moveFromBottom">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/slide3.png" width="65%" style="width:65%" alt=""  />
			</div>   
			<div class="caption-image-center moveFromTop">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/calendar.png" alt=""class="extra" style="float: left;margin-bottom: 3%;" />
			</div>
			<div class="caption-image-center moveFromRight">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/clock_yellow.png" alt=""class="extra" style="float:right;margin-bottom: 3%;" />
			</div>                             
		</div>
		
		<!-- slide 4 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/blank.png" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/slide-1.png">
			<div class="caption-image-left-people-1 moveFromBottom">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/slide-1.png" alt="" />
			</div>
			<div class="caption-text-right people moveFromTop">
				<h1><?php echo($this->Application->translate('Part of the Rehabilitation Team Since 1952', 'L\'équipe de réadaptation de choix depuis 1952')); ?></h1>
				<p>
					<strong><?php echo($this->Application->translate('With over 60 years of experience', 'Avec plus de 60 ans d\'expérience')); ?></strong>, 
					<?php echo($this->Application->translate(
						'Slawner Ortho is a foremost leader in the field of Orthotics and Prosthetics', 
						'Slawner c\'est établit comme l\'un des leaders dans le domaine de l\'orthétique et de la prothétique.'
					)); ?>.
				</p>
				<p>
					<?php echo($this->Application->translate(
						'At Slawner, we are focused on customized solutions using the most advanced technology and leading edge fabrication process.', 
						'Chez Slawner, on se consacrent a des solutions personnalisées à l\'aide de la technologie de fabrication la plus avancée.'
					)); ?>
				</p>
				<a href="/about" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('Find Out More', 'En savoir plus')); ?></a>            
			</div>                
		</div>
		
		<!-- slide 5 here -->
		<div data-src="<?php echo($this->strApplicationStaticResPath); ?>images/blank.png" data-thumb="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/60_yr_badge.png">
			<div class="caption-text-left moveFromTop">
				<h1><?php echo($this->Application->translate('Celebrating Over 60 Years of Service', 'Slawner célèbre de plus de 60 années de service')); ?>.</h1>
				<p>
					<?php echo($this->Application->translate(
						'Slawner is celebrating over 60 years of leading edge fabrication, helping patients achieve independence with the utmost comfort and ease possible.', 
						'Slawner célèbre plus de 60 ans de fabrication de pointe, aidant ces patients à atteindre l\'indépendance avec le maximum de confort et de facilité possible.'
					)); ?>
				</p>
				<a href="/about" class="button large dark_blue buttonShadow"><?php echo($this->Application->translate('About Us', 'À propos de nous')); ?></a>&nbsp;&nbsp;  
				<a href="/history" class="button large green buttonShadow"><?php echo($this->Application->translate('Our History', 'Notre Histoire')); ?></a>      
			</div>            
			<div class="caption-image-right-people moveFromRight">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/image-flash.png" id="slider-img-4-banner" alt="" style="margin-top: 2%;float: right;" />
			</div>
			<div class="caption-image-right-people moveFromBottom">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>images/slideshow/60_yr_badge.png" id="slider-img-5-banner" alt="" style="margin-top: 10%;float: right;margin-right: -15%;" />
			</div>
		</div>
	</div>
	<!-- <div id="slideshow-noscript"><h4>Hi, your javascript is off..!! for optimal results on this site please enable javascript in your browser</h4></div> -->       
</section>
<?php	
	}
}