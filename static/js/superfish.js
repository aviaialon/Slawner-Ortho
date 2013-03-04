
/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */

;(function($){
	$.fn.superfish = function(op){

		var sf = $.fn.superfish,
			c = sf.c,
			$arrow = $(['<span class="',c.arrowClass,'"> &#187;</span>'].join('')),
			over = function(){
				var $$ = $(this), menu = getMenu($$);
				clearTimeout(menu.sfTimer);
				$$.showSuperfishUl().siblings().hideSuperfishUl();
			},
			out = function(){
				var $$ = $(this), menu = getMenu($$), o = sf.op;
				clearTimeout(menu.sfTimer);
				menu.sfTimer=setTimeout(function(){
					o.retainPath=($.inArray($$[0],o.$path)>-1);
					$$.hideSuperfishUl();
					if (o.$path.length && $$.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.$path);}
				},o.delay);	
			},
			getMenu = function($menu){
				var menu = $menu.parents(['ul.',c.menuClass,':first'].join(''))[0];
				sf.op = sf.o[menu.serial];
				return menu;
			},
			addArrow = function($a){ $a.addClass(c.anchorClass).append($arrow.clone()); };
			
		return this.each(function() {
			var s = this.serial = sf.o.length;
			var o = $.extend({},sf.defaults,op);
			o.$path = $('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){
				$(this).addClass([o.hoverClass,c.bcClass].join(' '))
					.filter('li:has(ul)').removeClass(o.pathClass);
			});
			sf.o[s] = sf.op = o;
			
			$('li:has(ul)',this)[($.fn.hoverIntent && !o.disableHI) ? 'hoverIntent' : 'hover'](over,out).each(function() {
				if (o.autoArrows) addArrow( $('>a:first-child',this) );
			})
			.not('.'+c.bcClass)
				.hideSuperfishUl();
			
			var $a = $('a',this);
			$a.each(function(i){
				var $li = $a.eq(i).parents('li');
				$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});
			});
			o.onInit.call(this);
			
		}).each(function() {
			var menuClasses = [c.menuClass];
			if (sf.op.dropShadows  && !($.browser.msie && $.browser.version < 7)) menuClasses.push(c.shadowClass);
			$(this).addClass(menuClasses.join(' '));
		});
	};

	var sf = $.fn.superfish;
	sf.o = [];
	sf.op = {};
	sf.IE7fix = function(){
		var o = sf.op;
		if ($.browser.msie && $.browser.version > 6 && o.dropShadows && o.animation.opacity!=undefined)
			this.toggleClass(sf.c.shadowClass+'-off');
		};
	sf.c = {
		bcClass     : 'sf-breadcrumb',
		menuClass   : 'sf-js-enabled',
		anchorClass : 'sf-with-ul',
		arrowClass  : 'sf-sub-indicator',
		shadowClass : 'sf-shadow'
	};
	sf.defaults = {
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		pathLevels	: 1,
		delay		: 0,
		animation	: {opacity:'show'} /*{opacity:'drop',height:'show'}*/,
		speed		: 'normal',
		autoArrows	: false,
		dropShadows : true,
		disableHI	: false,		// true disables hoverIntent detection
		onInit		: function(){}, // callback functions
		onBeforeShow: function(){},
		onShow		: function(){},
		onHide		: function(){}
	};
	$.fn.extend({
		hideSuperfishUl : function(){
			var o = sf.op,
				not = (o.retainPath===true) ? o.$path : '';
			o.retainPath = false;
			var $ul = $(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass)
					.find('>ul').hide().css('visibility','hidden');
			o.onHide.call($ul);
			return this;
		},
		showSuperfishUl : function(){
			var o = sf.op,
				sh = sf.c.shadowClass+'-off',
				$ul = this.addClass(o.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			sf.IE7fix.call($ul);
			o.onBeforeShow.call($ul);
			$ul.animate(o.animation,o.speed,function(){ sf.IE7fix.call($ul); o.onShow.call($ul); });
			return this;
		}
	});

})(jQuery);


/*
$(document).ready(function () {
 
    //transitions
    var style = 'easeOutQuint';
     
    //Retrieve the selected item position and width
    var default_left = Math.round($('#mainmenu li.selected').offset().left - $('#mainmenu').offset().left);
    var default_width = $('#mainmenu li.selected').width();
 
    //Set the floating bar position and width
    $('#box').css({left: default_left});
    $('#box .head').css({width: default_width});
 
    //if mouseover the menu item
    $('#mainmenu li.parent').hover(function () {
         
        //Get the position and width of the menu item
        left = Math.round($(this).offset().left - $('#mainmenu').offset().left);
        width = $(this).width(); 
 
        //Set the floating bar position, width and transition
        $('#box').stop(false, true).animate({left: left},{duration:1000, easing: style});   
        $('#box .head').stop(false, true).animate({width:width},{duration:1000, easing: style});    
     
    //if user click on the menu
    }).click(function () {
         
        //reset the selected item
        $('#mainmenu li.parent').removeClass('selected');  
         
        //select the current item
        $(this).addClass('selected');
 
    });
     
    //If the mouse leave the menu, reset the floating bar to the selected item
    $('#mainmenu').mouseleave(function () {
 
        //Retrieve the selected item position and width
        default_left = Math.round($('#mainmenu li.selected').offset().left - $('#mainmenu').offset().left);
        default_width = $('#mainmenu li.selected').width();
         
        //Set the floating bar position, width and transition
        $('#box').stop(false, true).animate({left: default_left},{duration:1500, easing: style});   
        $('#box .head').stop(false, true).animate({width:default_width},{duration:1500, easing: style});        
         
    }); 
});

setTimeout(function(e) {
	// set the selected item
	left = Math.round($('#menu li.selected').offset().left - $('#mainmenu').offset().left);
	width = $('#menu li.selected').width(); 
	//Set the floating bar position, width and transition
	$('#box').stop(false, true).animate({left: left},{duration:1000, easing: 'easeOutQuint'});   
	$('#box .head').stop(false, true).animate({width:width},{duration:1000, easing: 'easeOutQuint'}); 		
}, 1000);
*/
