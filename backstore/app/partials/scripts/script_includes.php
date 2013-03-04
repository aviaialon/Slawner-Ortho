<?php 
/**
 * Menu Partial Class
 */	
class PARTIAL_SCRIPT_INCLUDES extends PARTIAL_BASE
{
	protected  $strData = NULL;
	
	public function __construct()
	{
		
	}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strStaticResourcePath = $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo($this->strStaticResourcePath); ?>css/styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/ui.spinner.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.mousewheel.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/jquery.flot.pie.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/jquery.flot.resize.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/charts/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/tables/jquery.sortable.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/tables/jquery.resizable.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/autogrowtextarea.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.uniform.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.autotab.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.chosen.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.cleditor.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.ibutton.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/forms/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/uploader/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/wizards/jquery.form.wizard.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/wizards/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/wizards/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.breadcrumbs.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.colorpicker.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.jgrowl.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.fileTree.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.sourcerer.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/others/jquery.fullcalendar.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/others/jquery.elfinder.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/plugins/ui/jquery.easytabs.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/files/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/files/functions.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/charts/chart.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/charts/hBar_side.js"></script>
</head>
<body>
<?php		
		
	}
	
	/**
	 * OVEVRRIDE: 	This method outputs the partial's signature as a comments - 
	 * 				We override this method because we cant have a signature
	 *				above the HTML tags.
	 */
	public static function signature()
	{
		
	}
}