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
		$this->blnIsAdmin = (
			(bool) (($this->Application->getUser()->isLoggedIn()) &&
			($this->Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)))
		);
		
		// Begin minification
		$this->minificatedPath = $this->Application->getMinification()->minifyFiles(array(
			$this->strStaticResourcePath . 'js/jquery/jquery.1.8.3.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery-ui-1.8.24.custom.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.easing-1.3.js',
			$this->strStaticResourcePath . 'js/superfish.js',
			$this->strStaticResourcePath . 'js/tinynav.min.js',
			$this->strStaticResourcePath . 'js/camera.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.tooltip.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.fancyboxe45f.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.fancybox-media2c70.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.storage.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.ui.totop.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.waitforimages.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.twitter.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.push.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.jfontsize-1.0.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.touchwipe.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.bxSlider.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.center.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.jqtransform.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.flexslider.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.isotope.min.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.uploader.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.autotab.js',
			$this->strStaticResourcePath . 'js/jquery/jquery.phone.0.6.js',
			//$this->strStaticResourcePath . 'js/jquery/jquery.mobile.customized.min.js',
			$this->strStaticResourcePath . 'js/ddaccordion.js',
			$this->strStaticResourcePath . 'js/faq-functions.js',
			$this->strStaticResourcePath . 'js/social.js',
			$this->strStaticResourcePath . 'js/search.js',
			$this->strStaticResourcePath . 'js/flash.loader.js',
			$this->strStaticResourcePath . 'js/moments.js',
			$this->strStaticResourcePath . 'js/cufon-yui.js',
			$this->strStaticResourcePath . 'js/fonts/Harabara_700.font.js'
		));
	}
	
	public function render()
	{
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/base.css" id="camera-css"/>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/framework.css"/>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/style.css"/>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/media.css"/>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/noscript.css" media="screen,all" id="noscript"/>
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:100,300,400|Nobile:400,700italic,700,400italic|Maven+Pro:400,500,700|Oswald:400,700,300|Advent+Pro:600"/>
<link rel="shortcut icon" href="<?php echo($this->strStaticResourcePath); ?>images/favicon.ico"/>

<script type="text/javascript">
	var LANG = '<?php echo($this->Application->translate('en', 'fr')); ?>';
</script>
<?php /*
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery-ui-1.8.24.custom.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.easing-1.3.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/superfish.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/tinynav.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/camera.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.tooltip.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.mobile.customized.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.fancyboxe45f.js?v=2.0.6"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.fancybox-media2c70.js?v=1.0.3"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.storage.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.ui.totop.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.waitforimages.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.twitter.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.push.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.jfontsize-1.0.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.touchwipe.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.bxSlider.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.center.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.jqtransform.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.flexslider.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.isotope.min.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.uploader.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.autotab.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.phone.0.6.js?.__rnd=<?php echo(mt_rand() * mt_rand()); ?>"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/ddaccordion.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/faq-functions.js" ></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/social.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/search.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/flash.loader.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/moments.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/fonts/Harabara_700.font.js" ></script>
*/ ?>
<script type="text/javascript" src="<?php echo($this->minificatedPath); ?>" ></script>
<?php
	switch ($this->Application->getRequestDispatcher()->getController()) 
	{
				
		case 'APPOINTMENT_CONTROLLER' :
		{
?>
<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>css/jquery-ui.css"/>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.daterangepicker.js"></script>
<?php
		}
		
		case 'APPOINTMENT_CONTROLLER' : 
		case 'LOCATIONS_CONTROLLER' : 
		case 'CONTACT_CONTROLLER' : 
		{
?>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.print.js"></script>
<script type="text/javascript" src="http://www.geoplugin.net/javascript.gp"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language=<?php echo($this->Application->translate('en', 'fr')); ?>"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/mp.direction.api.js"></script>
<?php
			break;	
		}
		
		case 'NEWS_CONTROLLER' : 
		{
?>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.tagit.js"></script>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.isotope.min.js"></script>
<?php
			break;	
		}
		case 'HISTORY_CONTROLLER' : 
		{
?>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/jquery/jquery.timeline.js"></script>
<?php
			break;	
		}
	}
?>
<script type="text/javascript" src="<?php echo($this->strStaticResourcePath); ?>js/application.js?.__rnd=<?php echo(mt_rand() * mt_rand()); ?>"></script>
<script type="text/javascript">
	var objApplication = new SLAWNER.APPLICATION.ORTHO();
	objApplication.initialise(function(event) {
		objApplication.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.LANGUAGE, '<?php echo($this->Application->translate('en', 'fr')); ?>');
		objApplication.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNETAPIKEY, '<?php echo(constant('__PHONO_API_KEY__')); ?>');
		objApplication.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_DIALNUM, '<?php echo(preg_replace('/[^0-9]/', '', constant('__CONTACT_PHONE__'))); ?>');
		objApplication.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.SITE_URL, '<?php echo(constant('__SITE_URL__')); ?>');
		objApplication.startModuleCollection('<?php echo($this->Application->getRequestDispatcher()->getController()); ?>');
	});
</script>
<!-- IE Fix for HTML5 Tags -->
<!--[if lt IE 9]>
	<script src="<?php echo($this->strStaticResourcePath); ?>js/respond.js"></script>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?php echo($this->strStaticResourcePath); ?>css/social-ie7.css"><![endif]-->

<?php if (true === $this->blnIsAdmin) { ?>
	<link rel="stylesheet" href="<?php echo($this->strStaticResourcePath); ?>js/adm-editor/css/redactor.css" />
	<script src="<?php echo($this->strStaticResourcePath); ?>js/adm-editor/redactor.js" ></script>
	<script type="text/javascript">
	var admEditors = {};
	var blnIsInitiated = false;
	var objPageBlockSegments = <?php echo($this->Application->getPageBlocks()->jsonEncodePageBlockGroups()); ?>;
	var callBackUrl			 = '<?php echo($this->Application->getRequestDispatcher()->createCallbackUrl($this->Application->getPageBlocks(), 'saveEditedPageBlocks', array())); ?>';
	var canonicalUrl 		 = '<?php echo(URL::getCanonicalUrl(null, true, false, true)); ?>';
	$(document).ready(function(e) {
		$('a[rel="edit-sections"]').on('click', function(event) {
			event.preventDefault();	
			if (false == blnIsInitiated) {
				admEditors = {};
				blnIsInitiated = true;
				$('.content').each(function(index, elem) {
					var uuId		= '__ADMIN_EDIT__' + ((Math.random()).toString()).replace('.', ''),
						textarea 	= $('<textarea></textarea>'),
						target		= $(this),
						contentId	= Number($(this).attr('data-content-id')),
						segmentId	= 'content_' + contentId;
						
					target.add(textarea).attr({rel: segmentId});
					textarea.attr({id: uuId, name: uuId});
					textarea.insertBefore(target);	
					
					/*textarea.html(target.html());*/
					target.fadeOut(200, function() {
						admEditors[contentId] = $('textarea#' + uuId).redactor({ 
							focus: false,
							wym: false,
							autoresize: true,
							iframe: false,
							air: false,
							lang: '<?php echo($this->Application->translate('en', 'fr')); ?>',
							css: "<?php echo($this->strStaticResourcePath); ?>css/style.css",
							imageUpload: '/backstore/api/v.206/upload-image/output:json',
							imageUploadErrorCallback: function (obj, json)
							{
								/*alert(json.error);
								alert(json.anothermessage);	*/
							},
							callback: function(obj)
							{
								if (typeof objPageBlockSegments[contentId]['blockContent'] != "undefined")
								{
									obj.setCode(new String(objPageBlockSegments[contentId]['blockContent']));	
								}
							}
						});
					});	
				});
			}
		});
		
		$('a[rel="save-sections"]').on('click', function(event) {
			var editedPageBlocks = {};
			$.each(admEditors, function(contentId, objAdmEditorInstance) {
				var strHtml = objAdmEditorInstance.getCode();
				objAdmEditorInstance.destroyEditor();
				$('textarea[rel="content_' + contentId + '"]').remove();
				$('.content[rel="content_' + contentId + '"]').html(strHtml).show();
				editedPageBlocks[contentId] = objPageBlockSegments[contentId]['blockContent'] = strHtml;
			});
			blnIsInitiated = false;
			
			// Save the data...
			$.ajax({
				type		: "POST",
				url			: callBackUrl,
				dataType	: "json",
				timeout		: 30000,
				cache		: false,
				processData	: true,
				data		: { 'editedContentBlocks': editedPageBlocks, 'canonicalUrl': canonicalUrl},
				xhrFields	: { withCredentials: true },
				success		: function(objXHTMLResponseObject) {
					if (Boolean(objXHTMLResponseObject.SUCCESS)) 
					{
						//me.onProductPivotTableDataResponse(objXHTMLResponseObject);
						objPageBlockSegments = objXHTMLResponseObject.DATA;
						
						$.each(objPageBlockSegments, function(pageBlockId, objPageBlockData) {
							$('.content[data-content-id="' + Number(pageBlockId) + '"]').html(objPageBlockData.executedData);
						});
						
						SLAWNER.APPLICATION.ORTHO.sendAlert({
							type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.OK,
							title: 'Page Saved Successfully.',
							message: 'The Page Was Saved Successfully.'
						});
					} 
					else 
					{
						SLAWNER.APPLICATION.ORTHO.sendAlert({
							type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
							title: me.translate('Error Generating Selection', 'Erreur'),
							message: objXHTMLResponseObject.errors.join('<br />')
						});
					}
					
					if (typeof objXHTMLResponseObject.POST_ACTION !== "undefined") 
					{
						try {
							eval(objXHTMLResponseObject.POST_ACTION);	
						}	catch (e) {}
					}
				},
				error : function(jqXHR, textStatus, errorThrown) {
					/*SLAWNER.APPLICATION.ORTHO.sendAlert({
						type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
						title: me.translate('Error Generating Selection', 'Erreur'),
						message: errorThrown
					});*/
				},
				complete	: function() {
					//$('#goButton').removeAttr('disabled');
				}
			});	
		});
	});
	</script>
<?php } ?>

</head>
<body class="<?php echo( $this->Application->getRequestDispatcher()->getController() ); ?>">
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
