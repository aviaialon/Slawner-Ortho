jQuery.fn.center = function (absolute, objOffset) {
	return this.each(function () {
		var t = jQuery(this),
			objOffsetXY = (typeof objOffset != "undefined") ? objOffset : {top: 0, left: 0};
		t.css({
			position:	absolute ? 'absolute' : 'fixed', 
			left:		(50 - Number(objOffsetXY.left)) + '%', 
			top:		(40 - Number(objOffsetXY.top)) + '%'/*, 
			zIndex:		'99'*/
		}).css({
			marginLeft:	'-' + (t.outerWidth() / 2) + 'px', 
			marginTop:	'-' + (t.outerHeight() / 2) + 'px'
		});

		if (absolute) {
			t.css({
				marginTop:	parseInt(t.css('marginTop'), 10) + jQuery(window).scrollTop(), 
				marginLeft:	parseInt(t.css('marginLeft'), 10) + jQuery(window).scrollLeft()
			});
		}
	});
};