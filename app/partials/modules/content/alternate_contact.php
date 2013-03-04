<?php 
/**
 * Call module Partial Class
 */	
class PARTIAL_ALTERNATE_CONTACT extends PARTIAL_BASE
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
<h5><?php echo($this->Application->translate('Alternate ways to connect with us', 'Autres moyens de se connecter avec nous')); ?></h5>
<p><?php echo($this->Application->translate('If you\'re looking for more ways to connect with Slawner Ortho', 'Si vous êtes à la recherche d\'autres façons de se connecter avec Slawner Ortho')); ?></p>
<ul class="button-socials">
	<li><a rel="call-btn" href="#" 
		title="<?php echo($this->Application->translate('Click to call us', 'Cliquez pour nous appeler')); ?>" 
		class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>images/button_call.png" alt="" /></a></li>
	<li><a href="<?php echo(constant('__FACEBOOK_PAGE_URL__')); ?>" target="_blank" 
			title="<?php echo($this->Application->translate('Join us on Facebook', 'Joinez nous sur Facebook')); ?>" 
			class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>images/socials/button_facebook.png" alt="" /></a></li>
	<li><a href="<?php echo(constant('__TWITTER_PAGE_URL__')); ?>" target="_blank" 
			title="<?php echo($this->Application->translate('Join us on Twitter', 'Joinez nous sur Twitter')); ?>" 
			class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>images/socials/button_twitter.png" alt="" /></a></li>
</ul>
<?php		
	}
}