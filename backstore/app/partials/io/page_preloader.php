<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_PAGE_PRELOADER extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
	}
	
	public function render()
	{
?>
<div id="jf-preloader">
	<div id="jf-indicator"></div>
	<div id="jf-preloader-logo"></div>
	<div id="jf-progress"></div>
</div>
<?php	
	}
}