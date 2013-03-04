<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_QUICK_MENU extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		SHARED_OBJECT::loadSharedObject('MENU::MENU');
		
		$this->Application 			 		= APPLICATION::getInstance();
		$this->strApplicationStaticResPath 	= $this->Application->getBaseStaticResourcePath();
		$this->strQuickMenuHtml				= MENU::getSiteMenuHtml(
			(int) $this->Application->translate(5, 6), ('class="pen"'), NULL
		);
	}
	
	public function render()
	{
?>
<div class="col_container">
	<div class="itemThumbnail"><img src="<?php echo($this->strApplicationStaticResPath); ?>images/home-4.png" alt=""></div>
	<h3><a class="moduleItemTitle" href="#"><?php echo($this->Application->translate('Our Services', 'Nos Services')); ?></a></h3>
	<div class="moduleItemIntrotext">
		<?php echo ($this->strQuickMenuHtml); ?>	
	</div>
</div>
<?php	
	}
	
	/**
	 * OVEVRRIDE: This method outputs the partial's signature as a comments
	 */
	public static function signature()
	{
		echo ("\n <!-- partial: " . str_replace('PARTIAL_', '', __CLASS__) . " | cached: " . (MENU::isCached() ? 'true' : 'false') . " --> \n");
	}
}