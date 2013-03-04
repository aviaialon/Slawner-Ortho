<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_FONT_RESIZER extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
	}
	
	public function render()
	{
?>
<!-- Front resizer -->
<div id="ThemeStylePicker">
	<a id="jfontsize-plus" class="rslnk plus_minus tooltip_east" title="<?php echo($this->Application->translate('Increase the font size', 'Augmenter la taille du texte')); ?>" href="#">+</a>
	<a id="jfontsize-default" class="rslnk tooltip_east" title="<?php echo($this->Application->translate('Reset the font size', 'Réinitialiser la taille du texte')); ?>" href="#">A</a>
	<a id="jfontsize-minus" class="rslnk plus_minus tooltip_east" title="<?php echo($this->Application->translate('Decrease the font size', 'Réduire la taille du texte')); ?>" href="#">-</a>
	<a href="#" id="CloseThemeStylePicker">x</a>
</div>
<?php	
	}
}