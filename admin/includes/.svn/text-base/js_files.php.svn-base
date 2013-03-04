<?php 
/**
 * The order of presedence set below for teh JS includes are inportant and they are NOT random.
 * If the order is change, the application may fail to work in production.
 */

$Application 		= ADMIN_APPLICATION::getInstance();
$blnIsDebug 		= $Application->getForm()->getUrlParam('debug');
$strAuthToken		= $Application->getAuthToken();
$strCurrentUrl 		= $Application->getCurrentUrl();
$strCurrentBaseUrl 	= $Application->getCurrentBaseUrl();
?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<?php PAGE_META::getPageMeta(true); ?>
<link type="text/css" rel="stylesheet" href="<?php echo (__ROOT_URL__); ?>/admin/static/css/main.css" />
<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cuprum" />
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery-1.4.4.js" type="text/javascript"></script>
<!-- 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
-->
<!--[if lte IE 8]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">
<!--
	var _BASE_URL 		= '<?php echo $strCurrentBaseUrl; ?>';
	var _AUTH_TOKEN		= '<?php echo $strAuthToken; ?>';
	var	_CONTROLLER		= '<?php ECHO $Application->getRequest_Controller()?>';
	var	_ACTION			= '<?php ECHO $Application->getRequest_Action()?>';
//-->
</script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/legacy/net.url.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/spinner/jquery.mousewheel.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/spinner/ui.spinner.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/fileManager/elfinder.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/wysiwyg/jquery.wysiwyg.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/wysiwyg/wysiwyg.image.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/wysiwyg/wysiwyg.link.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/wysiwyg/wysiwyg.table.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/flot/jquery.flot.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/flot/jquery.flot.pie.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/flot/excanvas.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/dataTables/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/dataTables/colResizable.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/forms.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/autogrowtextarea.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/autotab.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/jquery.validationEngine-en.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/jquery.validationEngine.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/jquery.dualListBox.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/forms/jquery.filestyle.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/colorPicker/colorpicker.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/uploader/plupload.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/uploader/plupload.html5.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/uploader/plupload.html4.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/uploader/jquery.plupload.queue.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/ui/progress.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/ui/jquery.jgrowl.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/ui/jquery.tipsy.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/ui/jquery.alerts.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jBreadCrumb.1.1.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/cal.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.smartWizard.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.collapsible.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.ToTop.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.listnav.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.sourcerer.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.timeentry.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.timeentry.min.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/jquery.jnotifier.js" type="text/javascript"></script>
<script src="<?php echo (__ROOT_URL__); ?>/admin/static/js/custom.js" type="text/javascript"></script>
<link rel="icon" type="image/ico" href="/admin/favicon.ico" />	
<?php 
	// Echo the jnotifier partial
	$Application->getRequestDispatcher()->renderPartial(__SITE_ROOT__ . '::ADMIN::MVC::PARTIAL::JNOTIFIER_NOTIFICATIONS', array(), false);
?>
<?php 
	if ($blnIsDebug) {
?>
<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>
<script type="text/javascript">
var errorStack = new Array();
var ERROR = {
	stack 		: new Array(),
	docLoaded	: false,

	__init		: function() 
	{
		ERROR.docLoaded = true;
		ERROR.__render();
	},
	
	__handle	: function (errorMsg, url, lineNumber) 
	{
		ERROR.stack.push('<b>' + errorMsg + '</b>' + " <br />" + url + ": <b>" + lineNumber + '</b>');
		ERROR.__render();
	},
	
	__render 	: function() 
	{
		if (Boolean(ERROR.docLoaded)) 
		{
			if (! $('div#error_console').length) 
			{
				$('body').prepend(
					$('<div></div>').attr('id', 'error_console').addClass('error').css({
						'font-family': 'arial',
						'font-size': '12px',
						'width': '800px',
						'color': '#CC0000',
						'position': 'absolute',
						'top': '0',
						'left': '0',
						'z-index': '9500',
						'height': '200px',
						'overflow': 'auto',
						'background': '#FFF'
					})
				);
			}

			// Display the error.
			$('#error_console').html(ERROR.stack.join('<hr /><br />'));
		}
	}
}

$(document).ready(function($) {
	ERROR.__init();
});

window.onerror = (function(errorMsg, url, lineNumber) {
	ERROR.__handle(errorMsg, url, lineNumber);
});
</script>
<?php } ?>