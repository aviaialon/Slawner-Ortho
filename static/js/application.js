/**
 * Application Administration Class
 * 
 * This class controls the Application scope
 *
 * @namespace	SLAWNER
 * @package		SLAWNER.APPLICATION
 * @subpackage	ORTHO
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2012 Slawner. All Rights Reserved
 * @license		http://www.SLAWNER.com/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 AM
 *
 */	
var SLAWNER 						= SLAWNER || {};
SLAWNER.APPLICATION 				= SLAWNER.APPLICATION || {};
SLAWNER.APPLICATION.STATIC_INSTANCE	= SLAWNER.APPLICATION.STATIC_INSTANCE || {};
(SLAWNER.APPLICATION.ORTHO  = function(objParams) {
	
	/**
	 * Define Class Constants
	 * 
	 * @var String
	 */
	
	// Class configurations
	SLAWNER.APPLICATION.ORTHO.STATUS 							= {};
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE						= {};
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT						= {};
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION						= {};
	SLAWNER.APPLICATION.ORTHO.SELECTIONS						= {};
	SLAWNER.APPLICATION.ORTHO.POST_HANDLER						= {};
	SLAWNER.APPLICATION.ORTHO.MODULE							= {};
	
	// Status Responses
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR 				= 'error';
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.INFO 					= 'info';
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.OK 					= 'success';
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.MESSAGE				= 'message';
	SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.RESPONSE_OK 			= 'OK';
	
	// Event Responses
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK				= 'click';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOVER			= 'hover';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.KEYUP				= 'keyup';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.KEYDOWN				= 'keydown';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.KEYPRESS				= 'keypress';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONMOVE				= 'onmove';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOVER			= 'mouseover';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOUT				= 'mouseout';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSENTER			= 'mousenter';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSELEAVE			= 'mouseleave';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEUP				= 'mouseup';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEDOWN			= 'mousedown';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CHANGE				= 'change';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.BLUR					= 'blur';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS				= 'focus';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.HOVER				= 'hover';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.SUBMIT				= 'submit';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.WKTRANSEND			= 'webkitTransitionEnd';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.OTRANSEND			= 'oTransitionEnd';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.TRANSEND				= 'transitionend';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONHIDE				= 'onHideEvent';
	SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONSHOW				= 'onShowEvent';
	
	
	// Parameter constants (to use with setter / configure)
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.JS_DOCUMENT_ROOT		 = 'jsDocumentRoot';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.STATIC_DOCUMENT_ROOT	 = 'staticDocumentRoot';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.IMG_DOCUMENT_ROOT		 = 'imgDocumentRoot';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.WEB_DOCUMENT_ROOT		 = 'webDocumentRoot';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.SITE_URL		 		 = 'siteApplicationUrl';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.IS_MOBILE_DEVICE	 	 = 'application_boolIsMobile';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.LANGUAGE	 		 	 = 'application_configLang';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_CONFIG	 		 = 'application_configTelnetConfig';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNETAPIKEY	 		 = 'application_configTelnetKey';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_AUDIO	 		 = 'application_configTelnetAudio';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_DIALNUM	 		 = 'application_configTelnetDialNum';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_LOADER	 		 = 'application_configTelnetLoader';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.APPT_FORM_LOADER	 	 = 'application_configAppointmentLoader';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE	 		 = 'application_configTelnetInstance';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST	 	 = 'application_configTelnetPermRequest';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_READY	 		 = 'application_configTelnetReady';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.UPLOADED_ATTACHMENTS	 = 'application_configContactUploadedAttachments';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.STORE_LOCATIONS	 		 = 'application_configStoreLocations';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_LOCATION_INDEX	 = 'application_configActiveLocationIndex';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_TRAVEL_MODE	 	 = 'application_configActiveTravelMode';
	SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TIMELINE_CONTAINER	 	 = 'application_configTimelineTargetContainer';
	
	// Configuration of registered modules.
	SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT				= 'browserDetect';
	SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_MODULES				= 'pageModules';
	SLAWNER.APPLICATION.ORTHO.MODULE.SLIDER						= 'slider';
	SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_SLIDER				= 'newsSlider';
	SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_COMMENTS				= 'newsComments';
	SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_FILTER	= 'patientProfilesFilter';
	SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_COMMENTS	= 'patientProfilesComments';
	SLAWNER.APPLICATION.ORTHO.MODULE.MENU						= 'topMenu';
	SLAWNER.APPLICATION.ORTHO.MODULE.TOTOP						= 'toTop';
	SLAWNER.APPLICATION.ORTHO.MODULE.SEARCH_FORM				= 'searchForm';
	SLAWNER.APPLICATION.ORTHO.MODULE.NEWSLETTER					= 'footerNewsletter';
	SLAWNER.APPLICATION.ORTHO.MODULE.ACCOUNT_MENU				= 'accountMenu';
	SLAWNER.APPLICATION.ORTHO.MODULE.TOOLTIPS					= 'tooltips';
	SLAWNER.APPLICATION.ORTHO.MODULE.FONT_RESIZER				= 'fontResizer';
	SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_PRELOADER				= 'pagePreloader';
	SLAWNER.APPLICATION.ORTHO.MODULE.USER_LOGIN_INPUTS			= 'userLoginInputs';
	SLAWNER.APPLICATION.ORTHO.MODULE.TESTIMONIAL_SLIDER			= 'testimonialSlider';
	SLAWNER.APPLICATION.ORTHO.MODULE.CALL_MODULE				= 'callModule';
	SLAWNER.APPLICATION.ORTHO.MODULE.CONTACT_FORM				= 'contactFormModule';
	SLAWNER.APPLICATION.ORTHO.MODULE.APPOINTMENT_FORM			= 'appointmentFormModule';
	SLAWNER.APPLICATION.ORTHO.MODULE.LOCATIONS_MAP				= 'locationsMapModule';
	SLAWNER.APPLICATION.ORTHO.MODULE.HISTORY_TIMELINE			= 'historyTimelineModule';
		
	/**
	 * Application initialisation method
	 *
	 * @param	none
	 * @return	SLAWNER.APPLICATION.ORTHO
	 */
	 SLAWNER.APPLICATION.ORTHO.prototype.initialise = function(fnCallback)
	 {
		 var fnAppInitialiseCallback = (typeof fnCallback == "function" ? fnCallback : function() {});
		 var me = this;
		 
		 $(window).load(function() {
			 //SLAWNER.ANIMATOR.start();
		 });
		 
		 me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.IS_MOBILE_DEVICE, Boolean(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)));
		 
		 $(document).ready(function() {

			 // Call the onInit event handler
			 fnAppInitialiseCallback(me);
			
			 //
			 // - Event 1: JIT Browser detection
			 //
			 me.startModule(SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT);
			 
			 //
			 // - Event 2: Page Modules:
			 me.startModule(SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_MODULES);
			 //
			 
			 //
			 // Load and save the static application handler.
			 //
			 SLAWNER.APPLICATION.STATIC_INSTANCE = me;
		 });
		 
		 return (this);
	 }
	 	
	/**
	 * Variable getter method
	 *
	 * @param	strVarName 	- The variable name
	 * @return	mxResult 	- The variable value
	 */
	 SLAWNER.APPLICATION.ORTHO.prototype.getVariable = function(strVarName)
	 {
		var mxResult = false;
		if (typeof this[strVarName] !== "undefined") {
			mxResult = this[strVarName];
		}
		
		return (mxResult);
	 }
	 
	/**
	 * Message setter method
	 *
	 * @param	strVarName 	- The variable name
	 * @param	strVarValue	- The variable value
	 * @return	void
	 */
	 SLAWNER.APPLICATION.ORTHO.prototype.setVariable = function(strVarName, strVarValue)
	 {
		if (typeof strVarName !== "undefined") {
			this[strVarName] = strVarValue;
		}
	 }
	 
	/**
	 * Message setter method
	 *
	 * @param	strVarName 	- The variable name
	 * @param	strVarValue	- The variable value
	 * @return	void
	 */
	 SLAWNER.APPLICATION.ORTHO.prototype.configure = function(strVarName, strVarValue)
	 {
		this.setVariable(strVarName, strVarValue);
	 }
	 
	 /**
	  * This method returns the translated string according to the language
	  * 
	  * @param	strEngVal String - The english value
	  * @param	strFrVal  String - The french value
	  * @return String
	  */
	 SLAWNER.APPLICATION.ORTHO.prototype.translate = function(strEngVal, strFrVal)
	 {
		 var strRetData = strEngVal;
		 var strlang 	= (this.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.LANGUAGE) || ('en'));
		 if (strlang.toLowerCase() == 'fr')
		 {
			 strRetData = strFrVal;
		 }
		 
		 return (strRetData);
	 }
	 
	 /**
	  * This method initilises and starts modules from array
	  * 
	  * @param	array / Object arrModuleNames
	  * @return void
	  */
	 SLAWNER.APPLICATION.ORTHO.prototype.startModuleArray = function(arrModuleNames)
	 {
		var me = this;
		if (
			(typeof arrModuleNames !== "undefined") && 
			((typeof arrModuleNames == "array") || (typeof arrModuleNames == "object")) && 
			(Number(arrModuleNames.length) > 0)
		) {
			$.each(arrModuleNames, function(intIndex, strModuleName) {
				me.startModule(strModuleName);	
			});
		}
	 }
	 
	 


	 /**
	  * This method initilises a page based on the controller
	  * 

	  * @param	string strControllerName
	  * @return void
	  */
	 SLAWNER.APPLICATION.ORTHO.prototype.startModuleCollection = function(strControllerName)
	 {
		var me = this;
		switch (strControllerName) 
		{
			case ('INDEX_CONTROLLER') : 
			{
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT,
					SLAWNER.APPLICATION.ORTHO.MODULE.SLIDER,
					SLAWNER.APPLICATION.ORTHO.MODULE.MENU,
					SLAWNER.APPLICATION.ORTHO.MODULE.TOTOP,
					SLAWNER.APPLICATION.ORTHO.MODULE.SEARCH_FORM,
					SLAWNER.APPLICATION.ORTHO.MODULE.NEWSLETTER,
					SLAWNER.APPLICATION.ORTHO.MODULE.ACCOUNT_MENU,
					SLAWNER.APPLICATION.ORTHO.MODULE.TOOLTIPS,
					SLAWNER.APPLICATION.ORTHO.MODULE.FONT_RESIZER,
					SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_PRELOADER,
					SLAWNER.APPLICATION.ORTHO.MODULE.TESTIMONIAL_SLIDER,
					SLAWNER.APPLICATION.ORTHO.MODULE.CALL_MODULE
				]);
				break;	
			}
			
			case ('USERS_CONTROLLER') : 
			{
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT,
					SLAWNER.APPLICATION.ORTHO.MODULE.TOOLTIPS,
					SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_PRELOADER,
					SLAWNER.APPLICATION.ORTHO.MODULE.USER_LOGIN_INPUTS,
					SLAWNER.APPLICATION.ORTHO.MODULE.CALL_MODULE
				]);
				break;	
			}
			
			case ('APPOINTMENT_CONTROLLER') :
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.APPOINTMENT_FORM,
					SLAWNER.APPLICATION.ORTHO.MODULE.LOCATIONS_MAP
				]);	
				
				break;
			}
			
			case ('CONTACT_CONTROLLER') :
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.CONTACT_FORM,
					SLAWNER.APPLICATION.ORTHO.MODULE.LOCATIONS_MAP
				]);	
				break;
			}
			
			case ('NEWS_CONTROLLER') :
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_SLIDER,
					SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_COMMENTS
				]);	
				break;
			}
			
			case ('PATIENT_PROFILES_CONTROLLER') : 
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_FILTER,
					SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_COMMENTS
				]);	
				break;	
			}
			
			case ('LOCATIONS_CONTROLLER') :
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.LOCATIONS_MAP
				]);	
				break;
			}
			
			case ('HISTORY_CONTROLLER') :
			{
				// Load the defaults...
				me.startModuleCollection.call(this);
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.HISTORY_TIMELINE
				]);	
				break;
			}
			
			default :
			{
				me.startModuleArray([
					SLAWNER.APPLICATION.ORTHO.MODULE.CALL_MODULE,
					SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT,
					SLAWNER.APPLICATION.ORTHO.MODULE.MENU,
					SLAWNER.APPLICATION.ORTHO.MODULE.TOTOP,
					SLAWNER.APPLICATION.ORTHO.MODULE.SEARCH_FORM,
					SLAWNER.APPLICATION.ORTHO.MODULE.NEWSLETTER,
					SLAWNER.APPLICATION.ORTHO.MODULE.ACCOUNT_MENU,
					SLAWNER.APPLICATION.ORTHO.MODULE.TOOLTIPS,
					SLAWNER.APPLICATION.ORTHO.MODULE.FONT_RESIZER,
					SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_PRELOADER
				]);
				
				break;
			}
		}
	 }
	
	 /**
	  * This method initilises and starts modules
	  * 
	  * @param	none
	  * @return void
	  */
	 SLAWNER.APPLICATION.ORTHO.prototype.startModule = function(strModuleName)
	 {
		 var me = this;
		 console.log('Loading Module: ' + strModuleName);
		 switch (strModuleName)
		 {
			 
			 /**
			  * Browser detection
			  */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.BROWSER_DETECT) : 
			{
				if (
					(true == $.browser.msie) &&
					(Number($.browser.version) < 9)
				) {
					$('body').addClass('ie8');	
				}
				/*
				$.ajax({
						type		: "POST",
						url			: me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.WEB_DOCUMENT_ROOT) + '/index.php',
						dataType	: "json",
						timeout		: 30000,
						cache		: false,
						processData	: true,
						data		: {
							postHandler: 		SLAWNER.APPLICATION.ORTHO.POST_HANDLER.PRODUCT_PIVOT_TABLE,
							productCatId:		me.getVariable(SLAWNER.APPLICATION.ORTHO.SELECTIONS.PRODUCT_CATEGORY_ID),
							productPartySizeId:	me.getVariable(SLAWNER.APPLICATION.ORTHO.SELECTIONS.PRODUCT_PARTY_SIZE_ID),
							productBudgetId:	me.getVariable(SLAWNER.APPLICATION.ORTHO.SELECTIONS.PRODUCT_BUDGET_ID)	
						},
						xhrFields	: {
							withCredentials: true
						},
						success		: function(objXHTMLResponseObject) {
							if (Boolean(objXHTMLResponseObject.success)) 
							{
								me.onProductPivotTableDataResponse(objXHTMLResponseObject);
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
							SLAWNER.APPLICATION.ORTHO.sendAlert({
								type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
								title: me.translate('Error Generating Selection', 'Erreur'),
								message: errorThrown
							});
						},
						complete	: function() {
							$('#goButton').removeAttr('disabled');
						}
					});	
				*/
				break;	
			}
			 
			/**
			 * Loading the call module
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.CALL_MODULE) : 
			{
				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST, false);
				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_READY, false);
				if ($('div#call-module-background').length == 0)
				{
					$('<div></div>').attr({
						'id': 'call-module-background'
					}).appendTo('body');	
				}
				
				$('div#call-module-background').add('div.call-module div.head a.button:eq(0)')
				.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK)
				.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('div.call-module').trigger(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONHIDE);
				});
					
				$('div.call-module').center(true).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONHIDE, function(event) {
					event.preventDefault();
					$('div#call-module-background').hide();
					$('div.call-module').fadeOut(400);	
					try { 
						$("#status").hide().html("");
						$("#module-call-btn-hangup").hide();
						$("#module-call-btn-trigger").show();
						me.getVariable('call').hangup(); 
						$('td[role="menuitem"]').unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
						me.configure('call', null);
						$('div#keypad').hide();
					} catch (e) {}
				});
				
				$('div.call-module').center().hide().on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONSHOW, function(event) {
					event.preventDefault();
					$('div#call-module-background').fadeIn(200, function(event) {
						$('div.call-module').center().css({
							position: 'fixed',
							top: '-999px',
							display: 'block',
							opacity: '1'	
						}).animate({
							top: '25%',
							left: '50%',
							'margin-top': '50px'
						}, 300, 'easeInOutCirc', function(event) {
							$(this).animate({
								'margin-top': '0px'
							}, 200, 'easeOutCirc', function(event) {
								// Request permissions
								/*
								if (
									(false === (Boolean(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST)))) &&
									(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE)) !== "undefined") && 
									(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source) !== "undefined") && 
									(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio) !== "undefined")
									(
										(typeof me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_READY) != "undefined")
										(true === (Boolean(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_READY))))
									)
								) {
									if (false === (Boolean(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio.permission())))
									{
										me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio.showPermissionBox();	
										me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST, true);
										$('.phono_FlashHolder').css({'z-index': 9999999});	
									}
									else
									{
										$('.phono_FlashHolder').css({'visibility': 'hidden'});	
										$('.phono_FlashHolder').css({'z-index': 1});
									}
								}
								*/
							})
						});
					});
				});
				
				$('div.call-module').draggable({
					handle: 'div.head div.drg-handle',
					opacity: 0.75	
				});
				
				$(window).resize(function(e) {
					$('div.call-module').center();
				});

				
				$('a[rel="call-btn"]').unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK)
					.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
						event.preventDefault();
						$('div.call-module').trigger(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.ONSHOW);		
					});
				
				//	
				// Load the phone application....
				//
				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_LOADER, new FlashLoader({
					closeOnClick: false,
					target: 'div.call-module div.content div.section-right',
					useHtml: true,
					onBeforeShow: function() {},
					onShow: function() {}
				}));
				
				me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_LOADER).show();
				$('.call-module .content .section-right a.button, .call-module .content .headset, volume_section, span.vlm').fadeTo(150, 0.1);
				$("#module-call-btn-hangup").hide();

				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_AUDIO, (
					(navigator.javaEnabled() && (true == me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.IS_MOBILE_DEVICE))) ? 'java' : 'auto')
				);
				
				
				audioType = "pandabridged";
				
				if (audioType == "flash") {
					gw = "gw-v3.d.phono.com";
					audio = "flash";
					directP2P = false;
				}
		
				if (audioType == "panda") {
					gw = "gw-v4.d.phono.com";
					audio = "flash";
					directP2P = true;
				}
		
				if (audioType == "pandabridged") {
					gw = "gw-v4.d.phono.com";
					audio = "flash";
					directP2P = false;
				}
				
				var objPhoneApi = $.phono({
					apiKey: me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNETAPIKEY),
					gateway:gw,
					audio: {
						type:	me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_AUDIO),
      					jar:	"/static/jar/phono.audio.jar",
						swf: 	"/static/jar/phono.audio.swf"/*,
               		 	direct: directP2P,
						onPermissionBoxShow: function() {
							console.log('called: onPermissionBoxShow');
							$('.phono_FlashHolder').css({'visibility': 'visible'});
						},
						onPermissionBoxHide: function() {
							console.log('called: onPermissionBoxHide');
							$('.phono_FlashHolder').css({'visibility': 'hidden'});
						}*/
					},
					onReady: function(objPhonoInstance) {
						me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_READY, true);
						me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_LOADER).hide();	
						$('.call-module .content .section-right a.button, .call-module .content .headset, volume_section, span.vlm').fadeTo(150, 1);
						me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE, objPhonoInstance);
						$("#module-call-btn-hangup").hide();
						/*
						if (
							(true == (Boolean($('div.call-module').is(':visible')))) &&
							(false === (Boolean(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST)))) &&
							(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE)) !== "undefined") && 
							(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source) !== "undefined") && 
							(typeof (me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio) !== "undefined")
						) {
							if (false === (Boolean(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio.permission())))
							{
								me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_INSTANCE).source.audio.showPermissionBox();	
								me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_PERMSREQUEST, true);
								$('.phono_FlashHolder').css({'z-index': 9999999});
							}
							else
							{
								$('.phono_FlashHolder').css({'visibility': 'hidden'});	
								$('.phono_FlashHolder').css({'z-index': 1});
							}
						}
						*/
					},
					phone: {
						/*security: 'maditory',*/
						security: 'disabled',
						headset: false, //Boolean($('#headset').attr('checked')),
						ringTone: 'http://s.phono.com/ringtones/ringback-us.mp3',
						onIncomingCall: function (event) {
							var call = event.call;
							$("#module-call-btn-hangup").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function (event) {
								call.hangup();
								$("#module-call-btn-trigger").show();
								$("#module-call-btn-hangup").hide();
							});
						}
					}
				});
				
				/// __unam
				
				me.configure('call', null);
				var __phonoMakeCall = function(me)
				{
					console.log('Dialing: ' + me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_DIALNUM));
					$("#status").show().html("Dialing...");
					$("#module-call-btn-trigger").hide();
					$("#module-call-btn-hangup").show();
					console.log('Creating the call objects...');
					
					objPhonoApiXmppCall = objPhoneApi.phone.dial(new String(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TELNET_DIALNUM)), {
						volume: 85,
						gain: 100,
						mute: false,
						pushToTalk: false,
						onRing: function () {
							$("#status").show().html("Ringing...");
						},
						onAnswer: function () {
							$('div#keypad').fadeIn(200);
							$("#status").html("Connected.");
							$('td[role="menuitem"]').unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK)
								.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
									event.preventDefault();
									me.getVariable('call').digit($(this).attr('data-value'));	
								});
						}/*,
						onHangup: function () {
							$("#status").hide().html("");
							$("#module-call-btn-hangup").hide();
							$("#module-call-btn-trigger").show();
							me.configure('call', null);
						}*/
					});
					me.configure('call', objPhonoApiXmppCall);	
				}
				
				$("#module-call-btn-trigger").live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function (event) {
					event.preventDefault();
					__phonoMakeCall(me);
				});
				
				$("#module-call-btn-hangup").live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function (event) {
					event.preventDefault();
					me.getVariable('call').hangup();
					$("#status").hide().html("");
					$("#module-call-btn-hangup").hide();
					$("#module-call-btn-trigger").show();
					$('td[role="menuitem"]').unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
					$('div#keypad').hide();
					me.configure('call', null);
				});
				
				// Load the volume slider...
				//Store frequently elements in variables
				var slider  = $('#volume_slider'),
					tooltip = $('.volume_tooltip');
	
				//Hide the Tooltip at first
				tooltip.hide();
	
				//Call the Slider
				slider.slider({
					//Config
					range: "min",
					min: 0,
					max: 100,
					value: 85,
					start: function(event,ui) {
						tooltip.fadeIn('fast');
					},
	
					//Slider Event
					slide: function(event, ui) { //When the slider is sliding
						var value  = slider.slider('value'),
							volume = $('.volume');
							
							if (typeof call !== "undefined") {
								try { call.volume(value); }
								catch (Exception) {}
							}
							
						tooltip.css('left', value).text(ui.value);  //Adjust the tooltip accordingly
						if(value <= 5) { 
							volume.css('background-position', '0 0');
						} 
						else if (value <= 25) {
							volume.css('background-position', '0 -25px');
						} 
						else if (value <= 75) {
							volume.css('background-position', '0 -50px');
						} 
						else {
							volume.css('background-position', '0 -75px');
						};
					},
					stop: function(event,ui) {
						tooltip.fadeOut('fast');
					},
				});

					
				break;	
			}
			  
			/**
		 	 * Loading the homepage slider
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.SLIDER) : 
			{
				$('#camera-slide').camera({		
					thumbnails: 	true,
					pagination: 	true,
					autoAdvance:	true,
					mobileAutoAdvance:	true,
					hover:			false,
					loaderColor:	'#EEEEEE',
					loaderBgColor:	'#01AFEE',
					loaderPadding:	2,
					loaderStroke:	7,
					loaderOpacity: .8,
					loader:			'pie',
					piePosition:	'rightTop',
					barPosition:	'bottom',
					fx: 			'random',
					time: 			7000,
					transPeriod: 	500,
					onInitialise:	function() {
						$('iframe#locationsMap').live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOVER, function(event) {
							$('#camera-slide').cameraPause();
							//console.log('Paused: ');
						}).live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOUT, function(event) {
							$('#camera-slide').cameraResume();
							//console.log('Resumed: ');
						});
					},	
					onEndTransition: function (sliderSlide, dataIndex) {
						//console.log('Data Index: ' + dataIndex);
					}
				});	 
				
				break; 	
			}
			
			/**
		 	 * Loading the news slider
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_SLIDER) : 
			{
				$('.post-slide').each(function(index, element) {
					$(this).camera({		
						thumbnails: 	false,
						pagination: 	false,
						autoAdvance:	true,
						mobileAutoAdvance:	true,
						hover:			false,
						loaderColor:	'#FFFFFF',
						loaderBgColor:	'#01AFEE',
						loaderPadding:	1,
						loaderStroke:	4,
						loaderOpacity: .8,
						loader:			'bar',
						piePosition:	'rightTop',
						barPosition:	'bottom',
						fx: 			'random',
						time: 			7000,
						transPeriod: 	1500,
						onInitialise:	function() {
							
						},	
						onEndTransition: function (sliderSlide, dataIndex) {
							//console.log('Data Index: ' + dataIndex);
						}
					});	 
				});
				
				
				break; 	
			}
			
			
			/**
		 	 * Loading the comments system
		 	 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_COMMENTS) : 
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_COMMENTS) : 
			{
				
				var strApiCallbackUrl = (strModuleName == SLAWNER.APPLICATION.ORTHO.MODULE.NEWS_COMMENTS ? 'news' : 'patient-profile');
				$('#post-news-comment').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();	
					$(this).hide();
					$("#comment-form .loading").show();
					$("#comment-form").fadeTo(300, 0.4);
					$("#comment-form div#message").html('');
					$.ajax({
						type		: "POST",
						url			: '/api/v.206/post-' + strApiCallbackUrl + '-comment/output:json',
						dataType	: "json",
						timeout		: 30000,
						cache		: false,
						processData	: true,
						data		: $("#comment-form").serialize(),
						xhrFields	: {
							withCredentials: true
						},
						success		: function(objXHTMLResponseObject) {
							if (Boolean(objXHTMLResponseObject.success)) 
							{
								try {
									var avatarUrl = 'http://www.gravatar.com/avatar/' + objXHTMLResponseObject.data['email_hash'] + 
												'?s=64&d=' + encodeURIComponent(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.SITE_URL) + 
												'/static/images/Avatar_silhouette-150x150.jpg');
									var postHtml = '<li id="post_comment_' + objXHTMLResponseObject.data['id'] + '">' + 
														'<div class="avatar"><img src="' + avatarUrl + '" alt=""></div>' +
														'<div class="comment-text">' +
															'<h5>' + objXHTMLResponseObject.data['name'] + '</h5>' +
															'<small>' + objXHTMLResponseObject.data['post_date'] + 
															'<a class="reply" data-comment-id="' + objXHTMLResponseObject.data['id'] + '" href="#">Reply</a></small>' +
															'<p>' + objXHTMLResponseObject.data['comment'] + '</p>' +
														'</div>' +
													'</li>';
									if (Number(objXHTMLResponseObject.data['commentparentid']) > 0) {
										var parentTarget = $('li#comment_' + Number(objXHTMLResponseObject.data['commentparentid']));
										if (! parentTarget.find('ol').length) {
											$('<ol></ol>').appendTo(parentTarget);
										}
										$(postHtml).prependTo(parentTarget.find('ol'));
									} else {
										$(postHtml).prependTo('ol.commentlist');		
									}
									var postCommentId = objXHTMLResponseObject.data['id'];
									$.scrollTo($('#post_comment_' + objXHTMLResponseObject.data['id']), 360, function() {
										$('#post_comment_' + postCommentId).effect("highlight", {}, 3000);
										$('#comment-form')[0].reset();
									});			
								}
								catch (Exception) {
									console.log(Exception.toString());
									$('#post-news-comment').show();
									$("#comment-form .loading").hide();
									$("#comment-form").fadeTo(300, 1);
								}
							} 
							else
							{
								var objErrorHtml  = '<div class="warning" style="font-size:12px">' + 
													me.translate('Please correct the following errors:', 'Veuillez corriger les erreurs suivantes:') + '<br />' +
													'<div style="font-size:11px;">' + objXHTMLResponseObject.error.join('<br />') + '</div>' +
													'</div>';
								$("#comment-form div#message").html(objErrorHtml);
							}
						},
						error : function(jqXHR, textStatus, errorThrown) {
							SLAWNER.APPLICATION.ORTHO.sendAlert({
								type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
								title: me.translate('Error Posting Comment', 'Erreur d\'affichage Commentaire'),
								message: errorThrown
							});
						},
						complete	: function() {
							$('#post-news-comment').show();
							$("#comment-form .loading").hide();
							$("#comment-form").fadeTo(300, 1);
						}
					});	
				});
				
				$('.reply').live(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$("#comment-form").find('input[name="parent_id"]').remove();
					var intPostId = Number($(this).attr('data-comment-id'));
					$('<input/>').attr({
						'type': 'hidden',
						'name': 'parent_id',
						'value': intPostId	
					}).prependTo($('#comment-form'));
					
					var parentOwnerName = $('li#comment_' + intPostId).find('h5.name').html();
					if (typeof parentOwnerName == "undefined") {
						 // New comment added, and self reply...
						parentOwnerName = $('ol.commentlist li h5:eq(0)').html();	
					}
					var replyMsg 		= me.translate('Your are replying to ' + parentOwnerName + '\'s comment', 'vous répondez au commentaire de ' + parentOwnerName);
					var objMessage 		= $('<div class="info">' + replyMsg + '</div>');
								
					$('<a></a>').attr({
						'href': '#',
						'class': 'button small dark_blue float-right'
					}).css({
						'margin-top': '-3px'	
					}).html(me.translate('cancel', 'annuler')).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
						event.preventDefault();
						$("#comment-form").find('input[name="parent_id"]').remove();
						$("#comment-form div#message").html('');
						$.scrollTo($('#comment'), 360);
					}).appendTo(objMessage);			
					$("#comment-form div#message").html('');
					objMessage.appendTo($("#comment-form div#message"));
					$.scrollTo($('#comment-form'), 360);
				});
				
				
				$('a.see-more-comments').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('li#comment_' + $(this).attr('data-comment-id')).find('li.hidden').fadeIn(300);
					$('.tipsy').hide();
					$(this).remove();
				});
				
				$('a.delete').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();	
					
					if (confirm('Are you sure you want to delete this comment. This action is not un-doable.')) {
						$.ajax({
							type		: "POST",
							url			: '/backstore/api/v.206/delete-' + strApiCallbackUrl + '-comment/output:json',
							dataType	: "json",
							timeout		: 30000,
							cache		: false,
							processData	: true,
							data		: {
								newsCommentId: $(this).attr('data-comment-id')	
							},
							xhrFields	: {
								withCredentials: true
							},
							success		: function(objXHTMLResponseObject) {
								if (Boolean(objXHTMLResponseObject.success)) 
								{
									var intCommentId = Number(objXHTMLResponseObject.newscommentid);
									$('ol.commentlist li#comment_' + intCommentId).fadeOut(300, function() {
										$(this).remove();	
									});
								} 
							},
							error : function(jqXHR, textStatus, errorThrown) {
								SLAWNER.APPLICATION.ORTHO.sendAlert({
									type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
									title: me.translate('Error Deleting Comment', 'Erreur a supprimer le commentaire'),
									message: errorThrown
								});
							},
							complete	: function() {}
						});		
					}
					
				});
				break; 	
			}
			
			
			
			
			/**
		 	 * Loading the patient profiles filter system
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.PATIENT_PROFILES_FILTER) : 
			{
				$('.post-slide').each(function(index, element) {
					var targetElem 		= $(element),
						blnLoadFeatures = Boolean(targetElem.hasClass('large'));
						
					$(this).camera({		
						thumbnails: 	blnLoadFeatures,
						pagination: 	blnLoadFeatures,
						navigation: 	blnLoadFeatures,
						playPause:		blnLoadFeatures,
						autoAdvance:	true,
						mobileAutoAdvance:	true,
						hover:			false,
						loaderColor:	'#FFFFFF',
						loaderBgColor:	'#01AFEE',
						loaderPadding:	1,
						loaderStroke:	4,
						loaderOpacity: .8,
						loader:			'bar',
						piePosition:	'rightTop',
						barPosition:	'bottom',
						fx: 			'random',
						time: 			7000,
						transPeriod: 	1500,
						onInitialise:	function() {
							
						},	
						onEndTransition: function (sliderSlide, dataIndex) {
							//console.log('Data Index: ' + dataIndex);
						}
					});	 
				});
				
				$(window).load(function () {
					var $container = $('#patient_profile_container');
					$container.isotope({
						filter: '*',
						animationOptions: {
							duration: 750,
							easing: 'linear',
							queue: false
						}
					});
					$('#pf-filter a').click(function () {
						var selector = $(this).attr('data-filter');
						$container.isotope({
							filter: selector,
							animationOptions: {
								duration: 750,
								easing: 'linear',
								queue: false
							}
						});
						return false;
					});
				
					var $optionSets = $('#pf-filter'),
						$optionLinks = $optionSets.find('a');
				
					$optionLinks.click(function () {
						var $this = $(this);
						// don't proceed if already selected
						if ($this.hasClass('selected')) {
							return false;
						}
						var $optionSet = $this.parents('#pf-filter');
						$optionSet.find('.selected').removeClass('selected');
						$this.addClass('selected');
					});
				});
				break;	
			}
			
			/**
		 	 * Loading the page menu
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.MENU) : 
			{
				$('ul#menu').superfish();
				$('#menu').tinyNav({ active: 'active' });	
				$('#menu li.current:eq(0)').parents('ul li').each(function(index, element) {
					$(this).addClass('current active');
				});
				break;
			}
			
			/**
		 	 * Loading the search box
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.SEARCH_FORM) : 
			{
				$('#search-form').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.BLUR, function(event) {
					$(this).val($(this).val() == '' ? $(this).attr('placeholder') : $(this).val());
				}).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS, function(event) {
					$(this).val($(this).val() == $(this).attr('placeholder') ? '' : $(this).val());
				});
				
				$('fieldset.search-fieldset2 input#search-form').search({
					'url': $('fieldset.search-fieldset2 input#search-form').attr('ajax-url') + '?query=' + $('fieldset.search-fieldset2 input#search-form').val(),
					'param': 'query',
					'msgResultsHeader': 'Search Results',
					'msgMoreResults': 'More Results',
					'msgNoResults': 'No results found'
				}).placeholder();
				
				break;
			}
			
			/**
		 	 * Loading the newsletter footer box
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.NEWSLETTER) : 
			{
				$('#email-subscribe-field').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.BLUR, function(event) {
					$(this).val($(this).val() == '' ? $(this).attr('alt') : $(this).val());
				}).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS, function(event) {
					$(this).val($(this).val() == $(this).attr('alt') ? '' : $(this).val());
				}).trigger(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.BLUR);
				break;
			}
			
			/**
		 	 * Loading the user account menu box
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.ACCOUNT_MENU) : 
			{
				$(".header-switch span.trigger_switch").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function() {
					if (Number($(this).find('b.trigger').attr('id')) == 1) {
						// hide the menu
						$("div#header-switch-expanded ul li a").addClass('disabled');	
						$("div#header-switch-expanded").hide('drop', {direction: 'up'}, 400, function() {
							$(".header-switch").find('b.trigger').attr('id', '0');
							$("div#header-switch").show('drop', {direction: 'up'}, 500, function() {
								$("div#header-switch-expanded ul li a").removeClass('disabled');	
							});
						});
					} else {
						// Show the menu	
						$("div#header-switch-expanded ul li a").addClass('disabled');	
						$("#header-switch").hide('drop', {direction: 'up'}, 200, function() {
							$("div#header-switch-expanded").show('drop', {direction: 'up'}, 400, function(event) {
								$("div#header-switch-expanded ul li a").removeClass('disabled');	
							});
							$(".header-switch").find('b.trigger').attr('id', '1');
						});
					}
				});
				
				$(".header-switch").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEUP, function() {
					return false
				});
				$(document).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEUP, function() {
					if ($('.header-switch span').find('b.trigger').attr('id') == 1) {
						// hide the menu
						$("div#header-switch-expanded ul li a").addClass('disabled');	
						$("div#header-switch-expanded").hide('drop', {direction: 'up'}, 400, function() {
							$(".header-switch").find('b.trigger').attr('id', '0');
							$("div#header-switch").show('drop', {direction: 'up'}, 500, function() {});
						});	
					}
				});
				break;
			}
			
			/**
		 	 * Loading the font-resizer module
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.FONT_RESIZER) : 
			{
				$("#CloseThemeStylePicker").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$("#ThemeStylePicker").animate({
						opacity: 0.85,
						"padding-left": "18px",
						right: "-30px"
					}, "fast", function() {
						$(this).addClass("stylePickerClosed").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOVER, function(event) {
							$(this).css({
								opacity: 1
							})
						}).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOUT, function() {
							$(this).css({opacity: 0.85})
						}).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function() {
							$("#ThemeStylePicker").animate({
								opacity: 1,
								right: "0px"
							}, "fast", function() {
								$(this).removeClass("stylePickerClosed")
									.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK)
									.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOVER)
									.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEOUT)
							}).css("padding-left", "8px").children().animate({
								opacity: 1
							}, "fast");
							return false
						})
					}).children().animate({
						opacity: 0.01
					}, "slow");
					return false
				});
				
				$("#ThemeStylePicker").fadeIn(800);
				$(window).bind("resize", function() {
					topPos = parseInt(($(window).height() * 0.5) - ($("#ThemeStylePicker").height() * 0.5));
					$("#ThemeStylePicker").css("top", topPos - 80 + "px")
				}).resize();
				
				// Font switcher
				$('#main-wrapper, p, h1, h2, h3, h4').jfontsize();
				
				break;
			}
			
			/**
			 * Loading the user login input fields actions
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.USER_LOGIN_INPUTS) : 
			{
				$('input#username, input#password').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS, function(event) {
					$(this).parents('div.login_row').addClass(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS);	
				}).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.BLUR, function(event) {
					$(this).parents('div.login_row').removeClass(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.FOCUS);	
				}).placeholder();
				
				break;	
			}
			
		 	/**
		 	 * Loading the default modules
		 	 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.TOOLTIPS) :
			case (SLAWNER.APPLICATION.ORTHO.MODULE.TOTOP) :
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_MODULES) : 
			{	
				$('#pagetitle-wrapper h3, div.call-module h2, ol.commentlist h5').addClass('excludeReplace');

				
if (! $('body').hasClass('USERS_CONTROLLER')) {
				// Font replace
				Cufon.replace('.font-replace:not(.excludeReplace)', { hover: true });
				Cufon.replace('.header:not(.excludeReplace)', { hover: true });
				Cufon.replace('h1:not(.excludeReplace)', { hover: true });
				Cufon.replace('h2:not(.excludeReplace)', { hover: true });
				Cufon.replace('h3:not(.excludeReplace)', { hover: true });
				Cufon.replace('h4:not(.excludeReplace)', { hover: true });
				Cufon.replace('h5:not(.excludeReplace)', { hover: true });
				Cufon.replace('.locationTitle:not(.excludeReplace)', { hover: true });
				
				Cufon.now();
				
}
				// Jq Forms
				$("form.jq").jqTransform();
				
				// Moments
				$('.moment').each(function(index, element){
					moment.lang(LANG);
					var data = moment($(this).attr('data-moment'), 'YYYY-MM-DD h:mm:ss a').fromNow();
					$(this).html(data);
				});

				// tabs
				$(".tab_content").hide(); 
				$("ul._tabs li:first").addClass("active").show(); 
				$(".tab_content:first").show(); 
				$("ul._tabs li").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$("ul._tabs li").removeClass("active");
					$(this).addClass("active"); 
					$(".tab_content").hide(); 
					var activeTab = $(this).find("a").attr("href"); 
					$(activeTab).fadeIn(200); 
					return false;
				});	
				
				//Fancybox Jquery
				$(".fancybox").fancybox({
					padding: 0,
					openEffect : 'elastic',
					openSpeed  : 250,
					closeEffect : 'elastic',
					closeSpeed  : 250,
					closeClick : true,
					helpers : {
						overlay : {opacity : 0.65},
						media : {}
					}
				});	
				
				// Lang selector
				var supportsCSSAnimations = function() {
					var tmpElement = document.createElement("div");
					var feature = "AnimationName";
					var properties = [feature];
					var prefixes = ["Webkit", "Moz", "I", "ms", "Khtml"];
			
					for (var i = 0; i < prefixes.length; i++) {
						properties.push(prefixes[i] + feature);
					}
			
					for (var i = 0; i < properties.length; i++) {
						var property = properties[i];
			
						if (typeof tmpElement.style[property] !== "undefined") {
							return true;
						}
					}
			
					return false;
				}();
				
				var _scrollbarWidth = null;
				var scrollbarWidth = function() {
					if (_scrollbarWidth != null)
						return _scrollbarWidth;
			
					var body = $("body"),
						initialValue = body.css("overflow-y");
			
					// Has Scrollbar
					body.css("overflow-y", "scroll");
					withScrollbar = ($('body').innerWidth());
			
					// Does not have
					body.css("overflow-y", "hidden")
					withoutScrollbar = ($('body').innerWidth());
			
					// Reset to initial
					body.css("overflow-y", initialValue);
					_scrollbarWidth = (withoutScrollbar - withScrollbar);
			
					return (_scrollbarWidth);
				};
				
				var enableScrolling = function(shouldEnable) {
					var currentScrollbarWidth = scrollbarWidth();
			
					if ($.browser.msie) {
						$("html").css("overflow", shouldEnable ? "auto" : "hidden");
					}
					$("body").css("overflow", shouldEnable ? "auto" : "hidden");
					$("body").css("margin-right", shouldEnable ? "auto" : currentScrollbarWidth);
				}
				
				
				
			 // Country settings
				var animationDuration = 400,
					languageSelectorDropDown = $("div.lang-selector-view"),
					selectButton = $("a.select", languageSelectorDropDown);
			
				var overlay = $("div.lang-selector-overlay");
				overlay.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
				overlay.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event){
			
					// Enable scrolling
					enableScrolling(true);
			
					// Prepare
					langSelectDropdownPopover.addClass("hide-animation");
			
					var animationEnd = function() {
						$("div.lang-selector-overlay").hide();
						langSelectDropdownPopover.hide();
						langSelectDropdownPopover.removeClass("hide-animation");
						
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.WKTRANSEND);
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.OTRANSEND);
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.TRANSEND);
					};
			
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.WKTRANSEND, animationEnd);
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.OTRANSEND, animationEnd);
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.TRANSEND, animationEnd);
					
					// Run animations
					// Ludwig: this one requires a timeout in firefox for some reason.
					setTimeout(function(){
						langSelectDropdownPopover.removeClass("shown");
						overlay.removeClass("shown");
					}, 50);
			
					if (!supportsCSSAnimations) {
						setTimeout(animationEnd, 0.0);
					}
				});
				
				var topSpacing = 16;
				var arrowSize = {"width": 26, "height": 17};
				var langSelectDropdownPopover = $("div.popover", languageSelectorDropDown);
						
				selectButton.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
				selectButton.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event){
					event.preventDefault();
			
					// Disable scrolling
					enableScrolling(false);
			
					// Add overlay to DOM
					if ($.browser.msie) {
						overlay.css({
							'background': '#333',
							'opacity': 0.6
						}).delay(200).animate({opacity: 'toggle'}, 500); 
						overlay.css({'z-index': 94000});
						selectButton.css({'z-index': 94500});
						langSelectDropdownPopover.css({'z-index': 95000});
					}
					else {
						overlay.show();	
					}
			
					// Position popover
					var popoverHeight = langSelectDropdownPopover.outerHeight();
			
					// Prepare
					if ($.browser.msie) {
						langSelectDropdownPopover.delay(200).animate({opacity: 'toggle'}, 400); 
					} else {
						langSelectDropdownPopover.show()
					}
					langSelectDropdownPopover.css("top", -(popoverHeight + topSpacing));
					langSelectDropdownPopover.addClass("show-animation");
			
					// Position arrow
					var selectButtonWidth = selectButton.outerWidth();
					$("div.arrow", langSelectDropdownPopover).css("left", 20 + (selectButtonWidth / 2) - (arrowSize.width / 2));
			
					// Animation origin
					var origin = {
						"x": 20 + (selectButtonWidth / 2),
						"y": popoverHeight + (arrowSize.height - 7)
					}
					langSelectDropdownPopover.css("-webkit-transform-origin", origin.x + "px " + origin.y + "px");
					langSelectDropdownPopover.css("-moz-transform-origin", origin.x + "px " + origin.y + "px");
			
					var animationEnd = function() {
						langSelectDropdownPopover.removeClass("show-animation");
						
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.WKTRANSEND);
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.OTRANSEND);
						$(overlay).off(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.TRANSEND);
					};
			
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.WKTRANSEND, animationEnd);
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.OTRANSEND, animationEnd);
					$(overlay).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.TRANSEND, animationEnd);
			
					// Run animations
					langSelectDropdownPopover.addClass("shown");
					overlay.addClass("shown");
			
					if (!supportsCSSAnimations) {
						setTimeout(animationEnd, animationDuration);
					}
				});
				
				$(window).load(function() {
					$('.carousel-content').flexslider({
						animation: "slide",
						easing: "easeInQuart",
						
						slideshow: false,
						controlNav: false,
						/*animation: "slide",*/
						itemWidth: 237,
						itemMargin: 0,
						minItems: 1,
						maxItems: 3,
						start: function(){},
						before: function(){},
						after: function(){},
						end: function(){},
						added: function(){},
						removed: function(){} 
					});
					
					$('.carousel-content-testimonials').flexslider({
						animation: "slide",
						easing: "easeInQuart",
						
						slideshow: false,
						controlNav: false,
						/*animation: "slide",
						itemWidth: 237,*/
						itemMargin: 0,
						minItems: 1,
						maxItems: 1,
						start: function(){},
						before: function(){},
						after: function(){},
						end: function(){},
						added: function(){},
						removed: function(){} 
					});
				});
				/*
				$(".menu li").hover(function() {
					$(this).children("ul").css({
						visibility: "visible",
						display: "none"
					}).slideDown(300)
				}, function() {
					$("ul", this).css({
						visibility: "hidden"
					})
				});
				*/
					
				//Fade portfolio
				/*
				$(".fade").fadeTo(1, 1);
				$(".fade").hover(
					function () {$(this).fadeTo("fast", 0.45);},
					function () { $(this).fadeTo("slow", 1);}
				);		
				
				//Tab Jquery
				$(".tab_content").hide(); 
				$("ul.tabs li:first").addClass("active").show(); 
				$(".tab_content:first").show(); 
				$("ul.tabs li").click(function() {
					$("ul.tabs li").removeClass("active");
					$(this).addClass("active"); 
					$(".tab_content").hide(); 
					var activeTab = $(this).find("a").attr("href"); 
					$(activeTab).fadeIn(); 
					return false;
				});	
				
				//Twitter Jquery
				$("#twitter").getTwitter({
					userName: "Indoneztheme",
					numTweets: 1,
					loaderText: "Loading tweets...",
					slideIn: true,
					slideDuration: 750
				});
				
				//Fancybox Jquery
				$(".fancybox").fancybox({
					padding: 0,
					openEffect : 'elastic',
					openSpeed  : 250,
					closeEffect : 'elastic',
					closeSpeed  : 250,
					closeClick : true,
					helpers : {
						overlay : {opacity : 0.65},
						media : {}
					}
				});	
				*/
				
					
				
				//To top Jquery
				$().UItoTop({ easingType: 'easeOutQuart' });
				
				// Toolips
				var tooltipDefaultSettings = {
					fade: true,
					live: true,
					html: true,
					opacity: 0.8,
					offset: 15,
					gravity: 'se'	
				}
				$('.tooltip').tipsy(tooltipDefaultSettings);
				$('.tooltip_east').tipsy($.extend({}, tooltipDefaultSettings, {gravity: 'e'}));
				
				// Remove noscript support
				$('#noscript').remove();
				
				break;	
			} 
		 	
			/**
			 * Loading the testimonial slider
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.TESTIMONIAL_SLIDER) : 
			{
				// testimonials.
				$(document).ready(function(e) {
					//Call BxSlider
					if ( $('.bx-slider').length > 0 ) {
						$('.bx-slider').each(function(){
							var	$this = $(this),
								autoslideOn			= $(this).attr("data-autoslide_on") || 0,
								autoslideInterval	= parseInt($(this).attr("data-autoslide") || 7000);			
				
				
							if(autoslideOn == "0") {
								autoslideOn = false;
							} else if (autoslideOn == "1") {
								autoslideOn = true;
							}
				
							var mySlider = $this.bxSlider({
								displaySlideQty: 6,
								auto : autoslideOn,
								pause	: autoslideInterval,
								autoHover	: true,
								moveSlideQty: 1,
								onInit: function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) {
									var $this = currentSlideHtmlObject;
									
									var $activePanel	= $(currentSlideHtmlObject).find(".panel-author").clone().hide();
				
									if ($activePanel.length < 1) return false;
									if ($this.parents(".reviews-t").next().hasClass("autor")) return false;
				
									var $bigPapa = $this.parents(".reviews-t");
									var $authorPapa = $bigPapa.find(".autor").detach();
									
									$bigPapa.after($authorPapa);
									
									$authorPapa.append($activePanel);
									$activePanel.show();
				
									$authorPapa = null;
									$activePanel = null;
								},
								onDestroy: function() {
									$this.parents(".reviews-t").next().find(".panel-author").remove();
								},
								onBeforeSlide: function() {
									$this.parents(".reviews-t").next().find(".panel-author").fadeOut(150, function() { $(this).remove(); });
									$this.find(".loading-image > img").each(function() {
										$(this).unwrap().animate({"opacity" : 1}, 500);
									});
								},
								onAfterSlide: function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) {
									var $this = currentSlideHtmlObject;
				
									if ($this.parents(".list-carousel").hasClass("coda")) {
										$this.parents(".bx-window").css({
											height: currentSlideHtmlObject.height()
										});
									}
				
									var $activePanel	= $(currentSlideHtmlObject).find(".panel-author").clone().hide();
									if ($activePanel.length < 1) return false;
				
									var $authorPapa = $this.parents(".reviews-t").next();
										
									$authorPapa.append($activePanel);
									$activePanel.delay(180).fadeIn(230, function() { $activePanel = null; });
									
									$authorPapa = null;	
								}
								
							});
						
							$this.data("carousel", mySlider);
						 });
					}
				});
				
				break;	
			}
			
			/**
			 * Locations Map
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.LOCATIONS_MAP) : 
			{
				 //
				 // - Configure application vars
				 //
				 me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.STORE_LOCATIONS, [{ 
					'lat': 45.49907,
					'long': -73.62784,
					'title': 'Siège Social, Montréal ',
					'sub_title': "13, ch. de la Côte-des-Neiges Montréal,<br /> QC, H3S 1Y7"
				}, {
					'lat': 45.57449,
					'long': -73.57418,
					'title': 'Hôpital Santa Cabrini - Département d\'Orthopédie',
					'sub_title': "5655, St-Zotique Est, Montréal, QC, H1T 1P7"
				}, {
					'lat': 45.34631,
					'long': -73.76585,
					'title': 'Centre Hospitalier Anna-Laberge',
					'sub_title': "200, rue Brisebois, Châteauguay, QC, J6K 4W8"
				}, {
					'lat': 45.39732,
					'long': -73.56860,
					'title': 'St-Catherine',
					'sub_title': "5320 Boul. St Laurent local 160 Saint-Catherine, QC, J5C 1A7"
				}, {
					'lat': 45.35207,
					'long': -73.71995,
					'title': 'CRMSSO (Châteauguay)',
					'sub_title': "185, St-Jean-Baptiste, local 300 Châteauguay, QC, J6K 3B4"
				}, {
					'lat': 45.43435,
					'long': -73.69374,
					'title': 'Clinique Lachine',
					'sub_title': "3360, rue Notre-Dame, local 15 Lachine, QC, H8T 3E2"
				}]);
				
				$('#travelMode').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					if ($(this).hasClass('show')) {
						$('div#travelModeSelection').fadeOut();
						$(this).removeClass('show')
					} else {
						$('div#travelModeSelection').fadeIn();
						$(this).addClass('show');	
					}
				}); 
				
				$('#travelModeSelection ul li').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.stopPropagation();
					$('#travelModeSelection ul li').removeClass('selected');
					$(this).addClass('selected');
					var selectedTravelMode = $(this).attr('rel-travel-mode');
					var objTravelModes = {
						driving: google.maps.DirectionsTravelMode.DRIVING,
						transit: google.maps.DirectionsTravelMode.TRANSIT,
						walking: google.maps.DirectionsTravelMode.WALKING
					};
					me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_TRAVEL_MODE, objTravelModes[selectedTravelMode]);
				}); 
				
				
				$(document).on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.MOUSEUP, function(event) {
					if ($('#travelMode').hasClass('show')) {
						$('div#travelModeSelection').fadeOut();
						$('#travelMode').removeClass('show')
					}
				});
				
				/*- Begin Map direction API - */
				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_LOCATION_INDEX, me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.STORE_LOCATIONS).getAt(0));
				objMapDirectionApi = new MAP_DIRECTION_API();
				objMapDirectionApi.setDestination(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_LOCATION_INDEX).sub_title, function() {
					objMapDirectionApi.setDirectionPanel('directionsPanelContainer');
					objMapDirectionApi.setMapPanel('map_canvas');
					objMapDirectionApi.setAutoCompleteFieldInput('mapSearchTextField');	
					objMapDirectionApi.setVariable('totalDistancePanel', 'total');
					objMapDirectionApi.setVariable('moreInfoPanel', 'more_info');
				});
				
				/*- Begin the map tabs -*/
				$('#map-wrapper div.row div.systabspane ul li a').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('#map-direction-wrapper').fadeOut(400);	
					$("#x").fadeOut();
					$("#travelMode").fadeIn();
					$("#mapSearchTextField").val("");
					
					var intLocationIndex = Number($(this).attr('rel-loc-index'));
					var locationObject	 = me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.STORE_LOCATIONS).getAt(intLocationIndex);
					me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_LOCATION_INDEX, locationObject);	
					
					$('div.map_overlay').hide("slide", { direction: "left" }, 500);
					$('#map-wrapper div.row div.systabspane ul li').removeClass('current');
					$(this).parents('li').addClass('current');
					//$('div.map_overlay p.locationAddress, div.map_overlay h3.locationTitle').fadeIn(300);
					$('div.map_overlay').show("slide", { direction: "left" }, 500);
					$('.locationTitle').html(locationObject.title);
					$('.locationAddress').html(locationObject.sub_title);
					objMapDirectionApi.setDestination(locationObject.sub_title);
				});
				
				
				$('a#direction_submit').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					if ($('#mapSearchTextField').val() !== "") {
						$('#map-direction-wrapper').show();
						$("#travelMode").fadeOut();
						objMapDirectionApi.getDirectionsFromAddress($('#mapSearchTextField').val(), 'directionsPanelContainer', function() {
							$.scrollTo($('#more_info'), 360);
						}, me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_TRAVEL_MODE));	
					} else {
						$('#mapSearchTextField').focus();
					}
				});
				
				$('a#useLoc').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('#map-direction-wrapper').show();
					objMapDirectionApi.getGeoLocation(function(objGeoLocation) {
						objMapDirectionApi.getAddressReverseGeocode(
							objGeoLocation.coords.latitude,
							objGeoLocation.coords.longitude,
							function(strFormattedAddress) {
								$('#mapSearchTextField').val(strFormattedAddress);
								$("#x").fadeIn();
								$("#travelMode").fadeOut();
								$.scrollTo($('#more_info'), 360);
							}
						);
						
						objMapDirectionApi.getDirectionsFromAddress(
							objGeoLocation.coords.latitude + ',' + objGeoLocation.coords.longitude,
							'directionsPanelContainer', function() {
								$.scrollTo($('#more_info'), 360);	
							}, me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.ACTIVE_TRAVEL_MODE)
						);	
					});	
				});
				
				// if text input field value is not empty show the "X" button
				$("#mapSearchTextField").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.KEYUP, function(event) {
					$("#x").fadeIn();
					if ($.trim($("#mapSearchTextField").val()) == "") {
						$("#x").fadeOut();
						$("#travelMode").fadeIn();
					}
				});
				
				$("#mapSearchTextField").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CHANGE, function(event) {
					$("#x").fadeIn();
					if ($.trim($("#mapSearchTextField").val()) == "") {
						$("#x").fadeOut();
						$("#travelMode").fadeIn();
					}
				});
				
				$("#mapSearchTextField").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.KEYPRESS, function(event) {
					if(event.which == 13) {
						$('#direction_submit').trigger(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
					}
				});
				
				// on click of "X", delete input field value and hide "X"
				$("#x").on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					$("#mapSearchTextField").val("");
					$(this).hide();
					$("#travelMode").fadeIn();
					$('#map-direction-wrapper').hide("drop");
				});
				
				$('#mapDirectionDone').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('#map-direction-wrapper').fadeOut(400);	
					$("#x").fadeOut();
					$("#travelMode").fadeIn();
					$("#mapSearchTextField").val("");
				});
				
				$('#mapDirectionPrint').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$('div#directionsPanelContainer').print();
				});
				
				break; 
			}
			
			/**
			 * Conatct us form
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.CONTACT_FORM) :
			{
				$('form#contactform').submit(function(event) {
					$('input[type="submit"]', this).hide();
					$('span.loading', this).show();
					return (true);
				});
				
				$('#contactFormSubmitBtn').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					$('form#contactform').submit();
				});
				
				// Load the uploader...
				var objSlawnerFileUploader = SLAWNER.APPLICATION.SLAWNER_UPLOADER.initialise ({
					element: $('#__slawnerContactEmailAttachmentForm')[0],
					debug: false,
					button: $('a#attachFileButton')[0],
					multiple: true,
					autoUpload: true,
					fileLimit: 3,
					allowedExtensions: ['gif', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'pdf'],
					retry : {
						enableAuto: false,
						showButton: true,
					},
					request: {
						endpoint: "/api/v.206/upload-image/output:json"
					},
					callbacks: {
						onReady: function() {
							$.each(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.UPLOADED_ATTACHMENTS), function(index, objUploadedFileData) {
								var li = $('<li></li>').addClass('qq-upload-success').attr({
									'id': 	objUploadedFileData.fileSource
								}),
								fileSpan = $('<span></span>').addClass('qq-upload-file').html(objUploadedFileData.fileName),	
								fileSize = $('<span></span>').addClass('qq-upload-size').css({display: 'inline'}).html(objUploadedFileData.fileSize),	
								deleteBtn = $('<a></a>').addClass('button small red uploadDeleteButton').attr({
									'data-rel-file': objUploadedFileData.fileName	
								}).html('x');	
								
								fileSpan.appendTo(li);
								fileSize.appendTo(li);
								deleteBtn.appendTo(li);
								li.appendTo($('ul.qq-upload-list'));
								deleteBtn.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
								deleteBtn.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) { 
									event.preventDefault();
									var fileHashId = $(this).attr('data-rel-file');
									
									// Remove from attachment list
									$('form#contactform input[value="' + fileHashId + '"]').remove();
									
									// Delete the file on the server...
									// --------------------------------
									$.ajax({
										type		: "POST",
										url			: '/api/v.206/delete-image/output:json',
										dataType	: "json",
										timeout		: 30000,
										cache		: false,
										processData	: true,
										data		: { token: fileHashId},
										xhrFields	: { withCredentials: true },
										success		: function(objXHTMLResponseObject) {
											if (true == objXHTMLResponseObject.success)
											{
												$('ul.qq-upload-list li[id="' + fileHashId + '"]').remove();	
												
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles--;
												if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
													$('#attachFileButton').show();
												}
											}
										},
										error : function(jqXHR, textStatus, errorThrown) { },
										complete	: function() { }
									});	
									// --------------------------------
								});
								
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount++;
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles++;
								if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount > SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
									$('#attachFileButton').hide();
									$('.qq-upload-drop-area').hide();
								}
							});
						},
						onError: function(event, id, filename, reason) {
							//uh oh....
						},
						onSubmit: function(id, fileName) {
							$('.tipsy').hide();
							SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount++;
							if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount > SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
								$('#attachFileButton').hide();
								$('.qq-upload-drop-area').hide();
								return false;
							}
						},
						
						onCancel: function(id, fileName) {
							SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
							if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
								$('#attachFileButton').show();
							}
						},
						
						onComplete: function(id, fileName, responseJSON) {
							if (responseJSON.success) {
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles++;
								if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles >= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
									$('#attachFileButton').hide();
									$('.qq-upload-drop-area').hide();
								}
								
								
								$('<input/>').attr({
									type: 'hidden',
									name: 'attachments[' + fileName + ':' + responseJSON.filesize + ']',
									value: responseJSON.deletetoken	
								}).appendTo('form#contactform');
								
								
								var deleteButton = $('<a></a>').attr({
									'class': 'button small red uploadDeleteButton',
									'data-rel-file': responseJSON.deletetoken		
								}).html('x');
								deleteButton.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
								deleteButton.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) { 
									event.preventDefault();
									var fileHashId = $(this).attr('data-rel-file');
									
									// Remove from attachment list
									$('form#contactform input[value="' + fileHashId + '"]').remove();
									
									// Delete the file on the server...
									// --------------------------------
									$.ajax({
										type		: "POST",
										url			: '/api/v.206/delete-image/output:json',
										dataType	: "json",
										timeout		: 30000,
										cache		: false,
										processData	: true,
										data		: { token: fileHashId},
										xhrFields	: { withCredentials: true },
										success		: function(objXHTMLResponseObject) {
											if (true == objXHTMLResponseObject.success)
											{
												$('ul.qq-upload-list li[id="' + fileHashId + '"]').remove();	
												
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles--;
												if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
													$('#attachFileButton').show();
												}
											}
										},
										error : function(jqXHR, textStatus, errorThrown) { },
										complete	: function() { }
									});	
									// --------------------------------
								});
								
								// Fill in the IDs
								$('div.qq-uploader ul.qq-upload-list li.qq-upload-success:last').attr({
									'id': responseJSON.deletetoken	
								}).append(deleteButton);
							}
						}
					},
				});
				
				break;	
			}
			
			
			/**
			 * Conatct us form
			 */
			case (SLAWNER.APPLICATION.ORTHO.MODULE.APPOINTMENT_FORM) :
			{
				$('#tel_0, #tel_1, #tel_2').autotab_magic().autotab_filter('numeric');
				 
				$('input.daterange').daterangepicker({}, me); 

				$('form#appointmentForm').submit(function(event) {
					$('input[type="submit"]', this).hide();
					$('span.loading', this).show();
					return (true);
				});
				
				try {
					$('<input/>').attr({type: 'hidden', name: 'lat'}).val(geoplugin_latitude()).prependTo($('form#appointmentForm'));
					$('<input/>').attr({type: 'hidden', name: 'lng'}).val(geoplugin_longitude()).prependTo($('form#appointmentForm'));
				} catch (e) {}
				
				
				// Load the uploader...
				var objSlawnerFileUploader = SLAWNER.APPLICATION.SLAWNER_UPLOADER.initialise ({
					element: $('#__slawnerContactEmailAttachmentForm')[0],
					debug: false,
					button: $('a#attachFileButton')[0],
					multiple: true,
					autoUpload: true,
					fileLimit: 3,
					allowedExtensions: ['gif', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'pdf'],
					retry : {
						enableAuto: false,
						showButton: true,
					},
					request: {
						endpoint: "/api/v.206/upload-image/output:json"
					},
					callbacks: {
						onReady: function() {
							$.each(me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.UPLOADED_ATTACHMENTS), function(index, objUploadedFileData) {
								var li = $('<li></li>').addClass('qq-upload-success').attr({
									'id': 	objUploadedFileData.fileSource
								}),
								fileSpan = $('<span></span>').addClass('qq-upload-file').html(objUploadedFileData.fileName),	
								fileSize = $('<span></span>').addClass('qq-upload-size').css({display: 'inline'}).html(objUploadedFileData.fileSize),	
								deleteBtn = $('<a></a>').addClass('button small red uploadDeleteButton').attr({
									'data-rel-file': objUploadedFileData.fileName	
								}).html('x');	
								
								fileSpan.appendTo(li);
								fileSize.appendTo(li);
								deleteBtn.appendTo(li);
								li.appendTo($('ul.qq-upload-list'));
								deleteBtn.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
								deleteBtn.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) { 
									event.preventDefault();
									var fileHashId = $(this).attr('data-rel-file');
									
									// Remove from attachment list
									$('form#appointmentForm input[value="' + fileHashId + '"]').remove();
									
									// Delete the file on the server...
									// --------------------------------
									$.ajax({
										type		: "POST",
										url			: '/api/v.206/delete-image/output:json',
										dataType	: "json",
										timeout		: 30000,
										cache		: false,
										processData	: true,
										data		: { token: fileHashId},
										xhrFields	: { withCredentials: true },
										success		: function(objXHTMLResponseObject) {
											if (true == objXHTMLResponseObject.success)
											{
												$('ul.qq-upload-list li[id="' + fileHashId + '"]').remove();	
												
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles--;
												if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
													$('#attachFileButton').show();
												}
											}
										},
										error : function(jqXHR, textStatus, errorThrown) { },
										complete	: function() { }
									});	
									// --------------------------------
								});
								
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount++;
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles++;
								if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount > SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
									$('#attachFileButton').hide();
									$('.qq-upload-drop-area').hide();
								}
							});
						},
						onError: function(event, id, filename, reason) {
							//uh oh....
						},
						onSubmit: function(id, fileName) {
							$('.tipsy').hide();
							SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount++;
							if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount > SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
								$('#attachFileButton').hide();
								$('.qq-upload-drop-area').hide();
								return false;
							}
						},
						
						onCancel: function(id, fileName) {
							SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
							if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
								$('#attachFileButton').show();
							}
						},
						
						onComplete: function(id, fileName, responseJSON) {
							if (responseJSON.success) {
								SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles++;
								if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles >= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
									$('#attachFileButton').hide();
									$('.qq-upload-drop-area').hide();
								}
								
								
								$('<input/>').attr({
									type: 'hidden',
									rel: 'attachment', 
									name: 'attachments[' + fileName + ':' + responseJSON.filesize + ']',
									value: responseJSON.deletetoken	
								}).appendTo('form#appointmentForm');
								
								
								var deleteButton = $('<a></a>').attr({
									'class': 'button small red uploadDeleteButton',
									'data-rel-file': responseJSON.deletetoken		
								}).html('x');
								deleteButton.unbind(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK);
								deleteButton.on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) { 
									event.preventDefault();
									var fileHashId = $(this).attr('data-rel-file');
									
									// Remove from attachment list
									$('form#appointmentForm input[value="' + fileHashId + '"]').remove();
									
									// Delete the file on the server...
									// --------------------------------
									$.ajax({
										type		: "POST",
										url			: '/api/v.206/delete-image/output:json',
										dataType	: "json",
										timeout		: 30000,
										cache		: false,
										processData	: true,
										data		: { token: fileHashId},
										xhrFields	: { withCredentials: true },
										success		: function(objXHTMLResponseObject) {
											if (true == objXHTMLResponseObject.success)
											{
												$('ul.qq-upload-list li[id="' + fileHashId + '"]').remove();	
												
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount--;
												SLAWNER.APPLICATION.SLAWNER_UPLOADER.addedFiles--;
												if (SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileCount <= SLAWNER.APPLICATION.SLAWNER_UPLOADER.fileLimit) {
													$('#attachFileButton').show();
												}
											}
										},
										error : function(jqXHR, textStatus, errorThrown) { },
										complete	: function() { }
									});	
									// --------------------------------
								});
								
								// Fill in the IDs
								$('div.qq-uploader ul.qq-upload-list li.qq-upload-success:last').attr({
									'id': responseJSON.deletetoken	
								}).append(deleteButton);
							}
						}
					},
				});
				
				// Form submit
				me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.APPT_FORM_LOADER, new FlashLoader({
					closeOnClick: false,
					target: 'form#appointmentForm',
					useHtml: true,
					onBeforeShow: function() {},
					onShow: function() {}
				}));
				$('#appointmentFormSubmitBtn').on(SLAWNER.APPLICATION.ORTHO.STATUS.EVENT.CLICK, function(event) {
					event.preventDefault();
					$(this).hide();
					$('.errorField').removeClass('errorField');
					me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.APPT_FORM_LOADER).show();
					$.ajax({
						type		: "POST",
						url			: '/api/v.206/request-appointment/output:json',
						dataType	: "json",
						timeout		: 30000,
						cache		: false,
						processData	: true,
						data		: $('form#appointmentForm').serialize(),
						xhrFields	: { withCredentials: true },
						success		: function(objXHTMLResponseObject) {
							if (true == objXHTMLResponseObject.success)
							{
								$('form#appointmentForm')[0].reset();
								$('input[rel="attachment"]').remove();
								$('ul.qq-upload-list li').remove();
								SLAWNER.APPLICATION.ORTHO.sendAlert({
									type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.OK,
									title: me.translate('Appointment request was successful', 'succès'),
									message: me.translate(
										'Your appointment request was sent. A representative will be in contact with you shortly. <br />Thank you', 
										'Votre demande pour un rendez-vous a été envoyée avec succès. Un représentant sera en contact avec vous sous peu. <br /> Merci'
									)
								});
							}
							else
							{
								SLAWNER.APPLICATION.ORTHO.sendAlert({
									type: SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR,
									title: me.translate('Error Deleting Comment', 'Erreur a supprimer le commentaire'),
									message: objXHTMLResponseObject.error
								});
								
								$.each(objXHTMLResponseObject.field, function(index, fElement) {
									$('form#appointmentForm').find('[name="' + fElement + '"]').addClass('errorField');	
								});
								
								$.scrollTo($('form#appointmentForm').find('[name="' + objXHTMLResponseObject.field[0] + '"]'), {
									duration: 360,
									offset: {top: -90}
								});
								$('form#appointmentForm').find('[name="' + objXHTMLResponseObject.field[0] + '"]')[0].focus();
							}
						},
						error : function(jqXHR, textStatus, errorThrown) { },
						complete	: function() {
							me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.APPT_FORM_LOADER).hide();
							$('#appointmentFormSubmitBtn').show();
						}
					});	
				});
				
				break;	
			}
			
			
			/**
			 * Loading the history timeline
			 */
			 case (SLAWNER.APPLICATION.ORTHO.MODULE.HISTORY_TIMELINE) : 
			 {
				 me.configure(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TIMELINE_CONTAINER, $('#timeline'));
				 arrTimelineData = [
				 	{
						type:     'slider',
						date:     '2013-02-28',
						width:    400,
						height:   150,
						images:   [
							'/static/images/history/media/site-1.jpg', 
							'/static/images/history/media/site-2.jpg', 
							'/static/images/history/media/site-3.jpg', 
							'/static/images/history/media/site-4.jpg', 
							'/static/images/history/media/site-5.jpg'
						],
						speed:    5000
					},
					
					{
						type:     'gallery',
						date:     '2013-01-01',
						title:    me.translate('Slawner Ortho\'s new website', 'Nouveau site de Slawner'),
						width:    400,
						height:   150,
						images:   [
							'/static/images/history/media/site-1.jpg', 
							'/static/images/history/media/site-2.jpg', 
							'/static/images/history/media/site-3.jpg', 
							'/static/images/history/media/site-4.jpg', 
							'/static/images/history/media/site-5.jpg'
						]
					},
										
				 	{
						type:     'blog_post',
						date:     '2013-02-10',
						title:    me.translate('Slawner Ortho Launches a New Website', 'Slawner Ortho lance un nouveau site Web'),
						width:    400,
						content:  me.translate('<p>After many months of planning we are delighted to announce the launch of our new website - do let us know what you think.</p>' +
											   '<p>We hope that you find our new Slawner website has a fresh new look with improved navigation which allows you to find the ' +
											   'information you need more quickly and easy.We have reviewed and updated all our information so that you can be confident that everything '	+
											   'you are reading is as current as possible.', '<p>Après plusieurs mois de planification, nous sommes ravis d\'annoncer le lancement de notre nouveau ' +
											   'site - faites-nous savoir ce que vous pensez.</p><p>Nous espérons que vous trouverez notre site Slawner nouvelle a un nouveau look avec' +
											   'une navigation améliorée qui vous permet de trouver l\'information dont vous avez besoin plus rapidement et plus facilement. ' +
											   'Nous avons passé en revue et mis à jour toutes nos informations afin que vous pouvez être sûr que tout ce que vous lisez est aussi actuelle que possible.'),
						image:    '/static/images/uploads/10/183.png',
						readmore: '/news/post:20'
					},
					
					
					
					{
						type:     'iframe',
						date:     '2012-09-03',
						title:    me.translate('Slawner Ortho Reaches 6 locations!', 'Slawner Ortho atteint 6 emplacements!'),
						width:    400,
						height:   300,
						//url:      'https://maps.google.com.au/?ie=UTF8&amp;ll=-27.40739,153.002859&amp;spn=1.509276,2.515869&amp;t=v&amp;z=9&amp;output=embed'
						url:      '/map/large'
					},
					
					
					
					
					{
						type:     'blog_post',
						date:     '2012-08-12',
						title:    'Blog Post',
						width:    400,
						content:  '<b>Lorem Ipsum</b> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
						image:    'http://www.sebmenard.com/images/sebmenard-slawner.jpg',
						readmore: 'http://www.slawner.com'
					},
					{
						type:     'iframe',
						date:     '2012-08-12',
						title:    'Video',
						width:    400,
						height:   300,
						url:      'http://www.youtube.com/embed/XbYo3j9AfzA?wmode=opaque' // http://player.vimeo.com/video/30491762?byline=0&amp;portrait=0
					}
				];
				
				
				var objSlawnerHistoryTimeline = new SLAWNER.APPLICATION.TIMELINE_HISTORY(
					me.getVariable(SLAWNER.APPLICATION.ORTHO.CONFIGURATION.TIMELINE_CONTAINER), arrTimelineData, me
				);
				
				objSlawnerHistoryTimeline.setOptions({
					animation:   true,
					lightbox:    true,
					showYear:    true,
					allowDelete: false,
					columnMode:  'dual'
				});
				
				objSlawnerHistoryTimeline.display();
				break; 
			 }
			
		 	/**
		 	 * Loading the page preloader module
		 	 */
		 	case (SLAWNER.APPLICATION.ORTHO.MODULE.PAGE_PRELOADER) :
	 		{
	 			if ($.browser.msie  && parseInt($.browser.version, 10) === 7) {
	 				$("#jf-preloader").remove();
	 				return (true);
	 			}
				if (false == Boolean($.Storage.get("imagesLoaded"))) {
					// Break the page preloader.
					var jfwindowpreloaderheight = jQuery(document).height();
					$('#jf-preloader-logo').animate({top: '170px'}, 1200, 'easeInOutBack', function() {
						$(document).css("overflow", "hidden").waitForImages({
							finished: function() {
								$("#jf-preloader").css({
									"display": "block",
									"height": jfwindowpreloaderheight
								}).delay(1500).fadeOut(500, function() {
									$.Storage.set("imagesLoaded", 'true');
									$("#jf-preloader").remove();
								})
							},
							
							each: function(n, c) {
								$("#jf-preloader #jf-indicator").css({
									"height": "3px",
									"background": "#01AFEE"
								}).stop(true).animate({
									width: parseInt(n * 100 / c) + "%"
								}, "fast", 'easeInElastic');
								$("#jf-preloader-logo").fadeIn(1500);
								$("#jf-preloader #jf-progress").css({
									"color": "#666",
									"font-size": "28px"
								}).text(parseInt(n * 100 / c) + "%")
							},
							waitForAll: true
						});
					});
				}
				else
				{
					$("#jf-preloader").hide();	
				}
				
				break;	
			}
		 }
	 }
});


/**
 * This method throws an alert message to the user
 * 
 * @access 	Static
 * @param	Object objParams - Parameter settings:
 * 	objParams.type [
 * 		SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.ERROR
 * 		SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.INFO
 * 		SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.OK
 * 	]
 * 
 * 	objParams.title 	- Alert box title
 * 	objParams.message	- Alert box message
 * @return void
 */
SLAWNER.APPLICATION.ORTHO.sendAlert = function(objParams)
{
	$('body').push({
		'type': 	(typeof objParams.type !== "undefined" ? objParams.type : SLAWNER.APPLICATION.ORTHO.STATUS.TYPE.INFO),
		'title':	(typeof objParams.title !== "undefined" ? objParams.title : 'Message Alert'),
		'content': 	'<div style="width: 350px;">' + (typeof objParams.message !== "undefined" ? objParams.message : '') + '</div>'
	});
}


if (typeof window.console == "undefined") {
	window.console = { log: function() {} };	
}

Array.prototype.first = function () {
    return this[0];
};

Array.prototype.getAt = function (intIndex) {
    return this[Number(intIndex)];
};

window.onerror = function()
{
	with (document) {
		try {
			getElementById('jf-preloader').style.display = 'none';
		}
		catch (e) {}
	}
}
