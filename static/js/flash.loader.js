	/**
	 * @author	Avi Aialon
	 * @Constructor
	 * @Options {
	 *		target:			String 	 - jquery selector, defaults to body,
	 *		useHtml:		Boolean  - Use the HTML version of the loader
	 *		htmlText		String 	 - the text to put as the 'loading' indicator
	 *		closeOnClick:	Boolean	 - If the overlay should be closable when the background is clicked, defaults to false,
	 *		onBeforeShow:	Function - Function called before the overlay is shown,
	 *		onShow:			Function - Function called after the overlay is shown,
	 *		onBeforeHide:	Function - Function called before the overlay is hidden,
	 *		onHide:			Function - Function called after the overlay is hidden,
	 * }	 
	 */
	 
	function FlashLoader(options)
	{
		if (typeof options == "undefined") {
			options = {};	
		}
		this.options = {};
		
		/** 
		 * Public attributes
		 */
		
		this.options.target 	  = ($(options.target).size() ? options.target : 'body'); 
		this.options.closeOnClick = false;
		this.options.useHtml 	  = false;
		this.options.htmlText 	  = 'Loading...';
		this.options.onBeforeShow = null;
		this.options.onShow 	  = null;
		this.options.onBeforeHide = null;
		this.options.onHide 	  = null;
		
		/** 
		 * Private attributes
		 */
		 
		this.options.instanceId	  = 'FlashLoaderInstance_' + Math.floor((Math.random()*1000000)+1);
		this.options.swf 		  = {};
		this.options.swf.params   = {
			menu: "false",
			bgcolor: "#1e2123"
		};
		
		this.options.swf.attributes = {
			id: "flashLoadingSwf",
			name: "flashLoadingSwf"
		};
		
		this.options = $.extend(this.options, options);	
		FlashLoaderStatic.push(this);
	} 
	
	FlashLoader.prototype.attachTo = function(strObjectSelector)
	{
		this.options.target = strObjectSelector;	
	}
	
	FlashLoader.prototype.show = function()
	{
		// Execute the before callback
		if (typeof this.options.onBeforeShow == "function") {
			this.options.onBeforeShow.call(this);
		}
		
		this.setOverlay();
		this.setFlashLoader();
		this.getFlashLoader().show();
		this.getOverlay().show();
		this.center();
		
		// Execute the after callback
		if (typeof this.options.onShow == "function") {
			this.options.onShow.call(this);
		}	
	}
	
	FlashLoader.prototype.hide = function()
	{
		// Execute the before callback
		if (typeof this.options.onBeforeHide == "function") {
			this.options.onBeforeHide.call(this);
		}
		
		this.getOverlay().hide()
		this.getFlashLoader().hide();
		
		// Execute the after callback
		if (typeof this.options.onHide == "function") {
			this.options.onHide.call(this);
		}	
	}
	
	FlashLoader.prototype.getTarget = function()
	{
		return ($(this.options.target));
	}
	
	FlashLoader.prototype.getOverlay = function()
	{
		return ($('#' + this.options.instanceId + '_overlay'));
	}
	
	FlashLoader.prototype.getFlashLoader = function()
	{
		return ($('#' + this.options.instanceId + '_FlashContainer'));
	}
	
	FlashLoader.prototype.setOverlay = function()
	{
		if ($('#' + this.options.instanceId + '_overlay').size() == 0)	
		{
			var me = this;
			
			$('<div></div>')
				.addClass('overlay')
				.css({
					width: 	this.getTarget().width(),
					height: (this.getTarget().height() > 0 ? this.getTarget().height() : '100%')
				})
				.attr('id', this.options.instanceId + '_overlay')
				.appendTo(this.options.target).hide();
				
				if (this.options.closeOnClick) {
					this.getOverlay().bind('click', function (event) {
						me.hide();	
					});
				}
				
		}
	}
	
	FlashLoader.prototype.center = function() {
		// Center the flash loader according to the container
		this.getFlashLoader().css("position","absolute");
		this.getFlashLoader().css("top", (  this.getTarget().height() - this.getFlashLoader().height() ) / 2 + this.getTarget().scrollTop() + "px");
		this.getFlashLoader().css("left", ( this.getTarget().width()  - this.getFlashLoader().width() ) / 2 + this.getTarget().scrollLeft() + "px");
		
		this.getOverlay().css({
			width: 	this.getTarget().width(),
			height: '100%'
		});
	}
	
	FlashLoader.prototype.setFlashLoader = function()
	{
		var me = this;
		
		if (this.getFlashLoader().size() <= 0) 
		{
			if (
				(new String(this.getTarget().prop("tagName")).toLowerCase() != 'body') &&
				(new String(this.getTarget().css('position')).toLowerCase() != 'absolute')
			){
				this.getTarget().css('position', 'relative');
			}
			
			var FlashDiv = $('<div></div>').attr({
				'id': this.options.instanceId + '_FlashContainer',
				'class': 'loadinfo' + (this.options.useHtml ? 'Html' : '') 	
			}).css({
				visibility: 'visible'	
			}).appendTo(this.options.target);
			
			var FlashDivContainer = $('<div></div>').attr({
				'id': this.options.instanceId + '_FlashSubContainer'
			}).appendTo(FlashDiv);
			
			if (this.options.useHtml)
			{
				FlashDivContainer.html(this.options.htmlText);
			}
			else
			{
				if (typeof swfobject == "undefined")
				{
					$.getScript('https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js', function (){
						swfobject.embedSWF
						(
							"img/loading.swf", 
							me.options.instanceId + '_FlashSubContainer', 
							"80", 
							"50", 
							"9.0.0",
							"expressInstall.swf", 
							{}, me.options.swf.params, 
							me.options.swf.attributes
						);		
					});
				}
				else
				{
					swfobject.embedSWF
					(
						"img/loading.swf", 
						this.options.instanceId + '_FlashSubContainer', 
						"80", 
						"50", 
						"9.0.0",
						"expressInstall.swf", 
						{}, this.options.swf.params, 
						this.options.swf.attributes
					);
				}		
			}
			
			FlashDiv.center();
			FlashDiv.hide();	
		}
	}
		
	FlashLoaderStatic = 
	{
		_instances: new Array(),
		
		/**
		 * Adds a FlashLoader instance to the queue
		 * @param: FlashLoader
		 */
		push: function(objFlashLoaderInstance) {
			FlashLoaderStatic._instances.push(objFlashLoaderInstance);		 
		},
		
		/**
		 * Static metod call for window resize event
		 */
		resizeEvent : function() {
			$.each(FlashLoaderStatic._instances, function(index, FlashLoaderInstance) {
				FlashLoaderInstance.center();	
			});
		}
	};
	
	$(window).resize(function(e) {
		FlashLoaderStatic.resizeEvent();
	});	
	
	// -- End of plugin
