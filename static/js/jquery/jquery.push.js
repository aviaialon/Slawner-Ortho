$(document).ready(function(e) {
	reverseRetardedMessagesJustToComplicateThingsALitlleMore = function(n) {
		if (typeof(reverseRetardedMessagesJustToComplicateThingsALitlleMore.list[n]) == "string") return reverseRetardedMessagesJustToComplicateThingsALitlleMore.list[n].split("").reverse().join("");
		return reverseRetardedMessagesJustToComplicateThingsALitlleMore.list[n]
	};
	
	reverseRetardedMessagesJustToComplicateThingsALitlleMore.list = [
		"dnuorgkcab-hsup. ,esolc-hsup.", 
		"neddih-hsup.", 
		" > htdiWtneilc.ydob.tnemucod(noisserpxe", 
		"dnuorgkcab-hsup.", 
		"roloc-dnuorgkcab", 
		">vid/<>/ p<>\"tnetnoc-hsup\"=ssalc vid<", 
		">\"redaeh-hsup\"=ssalc vid<", 
		"thgir-mottob", 
		"p tnetnoc-hsup.", 
		".egassem noitamrofni siht daer dluohs uoY", 
		"!egassem sseccus s;ouqsr&tahT .thgir lla s;ouqsr&tI", 
		"!egassem rorre s;ouqsr&tahT .gnorw tnew gnihtemoS", 
		"1h redaeh-hsup.", 
		"h redaeh-hsup. ,p tnetnoc-hsup.", 
		"repparw-hsup.", ">a/<>\"#\"=ferh \"esolc-hsup\"=ssalc a<", 
		"redaeh-hsup.", 
		">naps/<>\"dnuorgkcab-hsup\"=ssalc naps<", 
		/Android|webOS|iPhone|iPad|iPod|BlackBerry/i, 
		";)\"otua\" : \"xp", "tnetnoc-hsup", 
		"wohsotua-hsup."
	];
	(function($) {
		$.fn.push = function(c) {
			var d = {
				success: {
					background: '#00a708',
					title: 'Yeah!',
					content: reverseRetardedMessagesJustToComplicateThingsALitlleMore(10),
				},
				
				error: {
					background: '#ff2727',
					title: 'Woops!',
					content: reverseRetardedMessagesJustToComplicateThingsALitlleMore(11),
				},
				
				info: {
					background: '#ff6600',
					title: 'Attention!',
					content: reverseRetardedMessagesJustToComplicateThingsALitlleMore(9),
				},
				
				maxWidth: 720,
				animation: 'slide',
				duration: 150,
				opacity: 0.45,
				position: 'center',
				positionMobile: 'center',
				wiggle: false,
				wiggleOnlyError: true,
				beforeShow: function() {},
				
				afterShow: function() {
					var k = push.outerWidth(true),
						l = push.outerHeight(true);
					push.css({
						'top': $(window).height() / 2 - l / 2,
						'left': $(window).width() / 2 - k / 2,
					})	
				},
				
				onClose: function() {},
				
			};
			
			//var c = $.extend(d, c);
			var c = $.extend(c, d);
			$.fn.push.dataIn = c;
	
			function f(k, l) {
				$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(12)).html(k);
				$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(8)).html(l);
				pushView()
			}
			function pushView() {
				var k = push.outerWidth(true),
					l = push.outerHeight(true);
				switch (c.position) {
					case 'center':
						push.css({
							'top': $(window).height() / 2 - l / 2,
							'left': $(window).width() / 2 - k / 2,
						});
						
						break;
					case 'bottom-right':
						push.css({
							'bottom': 25,
							'right': 25,
						});
						
						break;
					case 'top-right':
						push.css({
							'top': 25,
							'right': 25,
						});
						
						break;
					case 'bottom-left':
						push.css({
							'bottom': 25,
							'left': 25,
						});
						
						break;
					case 'top-left':
						push.css({
							'top': 25,
							'left': 25,
						});
						
						break;
					case 'top':
						push.css({
							'top': 0,
							'left': 0,
						});
						
						break;
					case 'bottom':
						push.css({
							'bottom': 0,
							'left': 0,
						});
						
						break
				}
				pushBackground.fadeIn(c.duration, c.beforeShow);
				switch (c.animation) {
					case 'fade':
						push.fadeIn(c.duration, c.afterShow);
						break;
					case 'slide':
						push.fadeTo(0).css('top', '-=25px').animate({
							top: '+=25px',
							opacity: 1,
						}, c.duration * 2, c.afterShow);
						break;
					case 'slideBottom':
						push.fadeTo(0).css('bottom', '-=25px').animate({
							bottom: '+=25px',
							opacity: 1,
						}, c.duration * 2, c.afterShow);
						break
				}
				if (c.wiggle) {
					pushWiggle(push)
				}
			}
			function g() {
				pushBackground.fadeOut(c.duration, c.onClose);
				push.fadeOut(c.duration, function() {
					$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(13)).empty()
				})
			}
			function pushWiggle(k) {
				if (c.position == 'top-right' || c.position == reverseRetardedMessagesJustToComplicateThingsALitlleMore(7)) {
					k.animate({
						right: '+=15',
					}, 45).animate({
						right: '-=30',
					}, 45).animate({
						right: '+=30',
					}, 45).animate({
						right: '-=30',
					}, 45).animate({
						right: '+=15',
					}, 45)
				} else {
					k.animate({
						left: '+=15',
					}, 45).animate({
						left: '-=30',
					}, 45).animate({
						left: '+=30',
					}, 45).animate({
						left: '-=30',
					}, 45).animate({
						left: '+=15',
					}, 45)
				}
			}
			var push = $(reverseRetardedMessagesJustToComplicateThingsALitlleMore(14)).append(
				reverseRetardedMessagesJustToComplicateThingsALitlleMore(6) + '<h1></h1>' + 
				reverseRetardedMessagesJustToComplicateThingsALitlleMore(15) + '</div>' + 
				reverseRetardedMessagesJustToComplicateThingsALitlleMore(5)
			).hide(),
				h = $(reverseRetardedMessagesJustToComplicateThingsALitlleMore(16)),
				i = h.css(reverseRetardedMessagesJustToComplicateThingsALitlleMore(4)),
				pushBackground = $('body').append(reverseRetardedMessagesJustToComplicateThingsALitlleMore(17)).find(reverseRetardedMessagesJustToComplicateThingsALitlleMore(3)).css('opacity', c.opacity).hide(),
				j = reverseRetardedMessagesJustToComplicateThingsALitlleMore(18).test(navigator.userAgent);
			if (c.wiggle) {
				c.wiggleOnlyError = false
			}
			if (!j) {
				push.css({
					'max-width': c.maxWidth + 'px',
					'width': reverseRetardedMessagesJustToComplicateThingsALitlleMore(2) + (c.maxWidth + 1) + ' ? "' + c.maxWidth + reverseRetardedMessagesJustToComplicateThingsALitlleMore(19),
				})
			}
			if ((j && c.positionMobile == 'bottom') || c.position == 'bottom-left' && c.animation == 'slide' || c.position == reverseRetardedMessagesJustToComplicateThingsALitlleMore(7) && c.animation == 'slide') {
				c.animation = 'slideBottom'
			}
			if (j) {
				c.position = c.positionMobile;
				if (!(c.positionMobile == 'center')) {
					push.css({
						'width': '100%',
					})
				} else {
					if ($(window).width() > $(window).height()) {
						push.css({
							'max-width': $(window).width() * 0.65,
						})
					} else {
						push.css({
							'max-width': $(window).width() * 0.75,
						})
					}
				}
			}
			$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(1)).hide();
			
			this.each(function() {
				var k = $(this);
				var optionsFinal = $.fn.push.dataIn;
				if (k.is('body')) {
					var l = optionsFinal.type;
					switch (l) {
						case 'success':
							h.css('background', c.success.background);
							f(optionsFinal.title, optionsFinal.content);
							break;
						case 'error':
							h.css('background', c.error.background);
							f(optionsFinal.title, optionsFinal.content);
							if (c.wiggleOnlyError) {
								pushWiggle(push)
							}
							break;
						case 'info': {
							h.css('background', c.info.background);
							f(optionsFinal.title, optionsFinal.content);
							break;
						}
					}
					$('.push-wrapper .push-header:gt(0), .push-wrapper .push-content:gt(0), .push-background:gt(0)').remove();
					var k = push.outerWidth(true),
						l = push.outerHeight(true);
					push.css({
						'top': $(window).height() / 2 - l / 2,
						'left': $(window).width() / 2 - k / 2,
					})
					return false;
				}
				else {
					k.live('click', function(event) {
						event.preventDefault();
						
						var l = k.data('push-type');
						switch (l) {
							case 'success':
								h.css('background', c.success.background);
								f(c.success.title, c.success.content);
								break;
							case 'error':
								h.css('background', c.error.background);
								f(c.error.title, c.error.content);
								if (c.wiggleOnlyError) {
									pushWiggle(push)
								}
								break;
							case 'info':
								h.css('background', c.info.background);
								f(c.info.title, c.info.content);
								break;
							case 'content-selector':
								// push-type="content-selector"
								// push-content-selector="#WhateverElementsThatHoldsTheHtml"
								var m = k.data('push-title'),
									n = $(k.data('push-content-selector')).html();
								h.css('background', i);
								f(m, n)
								break;	
							case 'ajax':
								// push-type="ajax"
								// push-content-url="/local/url/to/use.php"
								var m = k.data('push-title'),
									d = 'push-content-id-' + Math.floor(Math.random() * 100000000)
									n = '<div id="' + d + '"></div>';
								h.css('background', i);
								f(m, n);
								$.get(k.data('push-url'), function(data) {
									$('#' + d).html(data);	
								});
								break;		
							default:
								var m = k.data('push-title'),
									n = k.data(reverseRetardedMessagesJustToComplicateThingsALitlleMore(20));
								h.css('background', i);
								f(m, n)
						}
					})
				}
			});
			
			$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(0)).live('click', function(event) {
				event.preventDefault();
				g()
			});
			
			$(window).bind('keydown', function(e) {
				if (e.keyCode == 27) {
					if (push.css('display') == "block") {
						g()
					}
				}
			});
			
			$(reverseRetardedMessagesJustToComplicateThingsALitlleMore(21)).click();
			
			if (c.position == 'center') {
				$(window).resize(function() {
					var k = push.outerWidth(true),
						l = push.outerHeight(true);
					push.css({
						'top': $(window).height() / 2 - l / 2,
						'left': $(window).width() / 2 - k / 2,
					})
				})
			}
		}
	})(jQuery);
	
});



$(document).ready(function(e) {
	(function($) {
		var defaults = {
			buttons: {
				button1: {
					text: 'OK',
					danger: false,
					onclick: function() {
						$.fallr('hide')
					}
				}
			},
			
			icon: '',
			content: 'Hello',
			position: 'top',
			closeKey: true,
			closeOverlay: false,
			useOverlay: true,
			autoclose: false,
			easingDuration: 300,
			easingIn: 'swing',
			easingOut: 'swing',
			height: 'auto',
			width: '360px',
			zIndex: 100,
			bound: window,
			afterHide: function() {},
			
			afterShow: function() {}
		},
			opts, timeoutId, $w = $(window),
			methods = {
				hide: function(options, callback, self) {
					if (methods.isActive()) {
						$('#fallr-wrapper').stop(true, true);
						var $f = $('#fallr-wrapper'),
							pos = $f.css('position'),
							isFixed = (pos === 'fixed'),
							yminpos = 0;
						switch (opts.position) {
							case 'bottom':
							case 'center':
								yminpos = (isFixed ? $w.height() : $f.offset().top + $f.outerHeight()) + 10;
								break;
							default:
								yminpos = (isFixed ? (-1) * ($f.outerHeight()) : $f.offset().top - $f.outerHeight()) - 10
						}
						$f.animate({
							'top': (yminpos),
							'opacity': isFixed ? 1 : 0
						}, (opts.easingDuration || opts.duration), opts.easingOut, function() {
							if ($.browser.msie) {
								$('#fallr-overlay').css('display', 'none')
							} else {
								$('#fallr-overlay').fadeOut('fast')
							}
							$f.remove();
							
							clearTimeout(timeoutId);
							callback = typeof callback === "function" ? callback : opts.afterHide;
							callback.call(self)
						});
						
						$(document).unbind('keydown', helpers.enterKeyHandler).unbind('keydown', helpers.closeKeyHandler).unbind('keydown', helpers.tabKeyHandler)
					}
				},
				
				resize: function(options, callback, self) {
					var $f = $('#fallr-wrapper'),
						newWidth = parseInt(options.width, 10),
						newHeight = parseInt(options.height, 10),
						diffWidth = Math.abs($f.outerWidth() - newWidth),
						diffHeight = Math.abs($f.outerHeight() - newHeight);
					if (methods.isActive() && (diffWidth > 5 || diffHeight > 5)) {
						$f.animate({
							'width': newWidth
						}, function() {
							$(this).animate({
								'height': newHeight
							}, function() {
								helpers.fixPos()
							})
						});
						
						$('#fallr').animate({
							'width': newWidth - 94
						}, function() {
							$(this).animate({
								'height': newHeight - 116
							}, function() {
								if (typeof callback === "function") {
									callback.call(self)
								}
							})
						})
					}
				},
				
				show: function(options, callback, self) {
					if (methods.isActive()) {
						$('body', 'html').animate({
							scrollTop: $('#fallr').offset().top
						}, function() {
							$.fallr('shake')
						});
						
						$.error('Can\'t create new message with content: "' + options.content + '", past message with content "' + opts.content + '" is still active')
					} else {
						opts = $.extend({}, defaults, options);
						$('<div id="fallr-wrapper"></div>').appendTo('body');
						opts.bound = $(opts.bound).length > 0 ? opts.bound : window;
						var $f = $('#fallr-wrapper'),
							$o = $('#fallr-overlay'),
							isWin = (opts.bound === window);
						$f.css({
							'width': opts.width,
							'height': opts.height,
							'position': 'absolute',
							'top': '-9999px',
							'left': '-9999px'
						}).html('<div id="fallr-icon"></div>' + '<div id="fallr"></div>' + '<div id="fallr-buttons"></div>');
							$('#fallr-icon').addClass('fallr-icon-' + opts.icon).end();
							$('#fallr').html(opts.content).css({
							'height': (opts.height == 'auto') ? 'auto' : $f.height() - 116,
							'width': $f.width() - 94
						}).end();
						
						$('#fallr-buttons').html((function() {
							var buttons = '';
							var i;
							for (i in opts.buttons) {
								if (opts.buttons.hasOwnProperty(i)) {
									buttons = buttons + '<a href="#" class="fallr-button ' + (opts.buttons[i].danger ? 'fallr-button-danger' : '') + '" id="fallr-button-' + i + '">' + opts.buttons[i].text + '</a>'
								}
							}
							return buttons
						}()));
						$('.fallr-button').bind('click', function() {
							var buttonId = $(this).attr('id').substring(13);
							if (typeof opts.buttons[buttonId].onclick === 'function' && opts.buttons[buttonId].onclick != false) {
								var scope = $('#fallr');
								opts.buttons[buttonId].onclick.apply(scope)
							} else {
								methods.hide()
							}
							return false
						});
						
						var showFallr = function() {
							$f.show();
							
							var xpos = isWin ? (($w.width() - $f.outerWidth()) / 2 + $w.scrollLeft()) : (($(opts.bound).width() - $f.outerWidth()) / 2 + $(opts.bound).offset().left),
								yminpos, ymaxpos, pos = ($w.height() > $f.height() && $w.width() > $f.width() && isWin) ? 'fixed' : 'absolute',
								isFixed = (pos === 'fixed');
							switch (opts.position) {
								case 'bottom':
									yminpos = isWin ? (isFixed ? $w.height() : $w.scrollTop() + $w.height()) : ($(opts.bound).offset().top + $(opts.bound).outerHeight());
									ymaxpos = yminpos - $f.outerHeight();
									
									break;
								case 'center':
									yminpos = isWin ? (isFixed ? (-1) * $f.outerHeight() : $o.offset().top - $f.outerHeight()) : ($(opts.bound).offset().top + ($(opts.bound).height() / 2) - $f.outerHeight());
									ymaxpos = yminpos + $f.outerHeight() + (((isWin ? $w.height() : $f.outerHeight() / 2) - $f.outerHeight()) / 2);
									break;
								default:
									ymaxpos = isWin ? (isFixed ? 0 : $w.scrollTop()) : $(opts.bound).offset().top;
									yminpos = ymaxpos - $f.outerHeight()
							}
							$f.css({
								'left': xpos,
								'position': pos,
								'top': yminpos,
								'z-index': opts.zIndex + 1
							}).animate({
								'top': ymaxpos
							}, opts.easingDuration, opts.easingIn, function() {
								callback = typeof callback === "function" ? callback : opts.afterShow;
								callback.call(self);
								if (opts.autoclose) {
									timeoutId = setTimeout(methods.hide, opts.autoclose)
								}
							})
						};
						
						if (opts.useOverlay) {
							if ($.browser.msie && $.browser.version < 9) {
								$o.css({
									'display': 'block',
									'z-index': opts.zIndex
								});
								
								showFallr()
							} else {
								$o.css({
									'z-index': opts.zIndex
								}).fadeIn(showFallr)
							}
						} else {
							showFallr()
						}
						$(document).bind('keydown', helpers.enterKeyHandler).bind('keydown', helpers.closeKeyHandler).bind('keydown', helpers.tabKeyHandler);
						$('#fallr-buttons').children().eq(-1).bind('focus', function() {
							$(this).bind('keydown', helpers.tabKeyHandler)
						});
						
						$f.find(':input').bind('keydown', function(e) {
							helpers.unbindKeyHandler();
							
							if (e.keyCode === 13) {
								$('.fallr-button').eq(0).trigger('click')
							}
						})
					}
				},
				
				set: function(options, callback, self) {
					for (var i in options) {
						if (defaults.hasOwnProperty(i)) {
							defaults[i] = options[i];
							if (opts && opts[i]) {
								opts[i] = options[i]
							}
						}
					}
					if (typeof callback === "function") {
						callback.call(self)
					}
				},
				
				isActive: function() {
					return !!($('#fallr-wrapper').length > 0)
				},
				
				blink: function() {
					$('#fallr-wrapper').fadeOut(100, function() {
						$(this).fadeIn(100)
					})
				},
				
				shake: function() {
					$('#fallr-wrapper').stop(true, true).animate({
						'left': '+=20px'
					}, 50, function() {
						$(this).animate({
							'left': '-=40px'
						}, 50, function() {
							$(this).animate({
								'left': '+=30px'
							}, 50, function() {
								$(this).animate({
									'left': '-=20px'
								}, 50, function() {
									$(this).animate({
										'left': '+=10px'
									}, 50)
								})
							})
						})
					})
				}
			},
			helpers = {
				fixPos: function() {
					var $f = $('#fallr-wrapper'),
						pos = $f.css('position');
					if ($w.width() > $f.outerWidth() && $w.height() > $f.outerHeight()) {
						var newLeft = ($w.width() - $f.outerWidth()) / 2,
							newTop = $w.height() - $f.outerHeight();
						
						switch (opts.position) {
							case 'center':
								newTop = newTop / 2;
								break;
							case 'bottom':
								break;
							default:
								newTop = 0
						}
						if (pos == 'fixed') {
							$f.animate({
								'left': newLeft
							}, function() {
								$(this).animate({
									'top': newTop
								})
							})
						} else {
							$f.css({
								'position': 'fixed',
								'left': newLeft,
								'top': newTop
							})
						}
					} else {
						var newLeft = ($w.width() - $f.outerWidth()) / 2 + $w.scrollLeft();
						
						var newTop = $w.scrollTop();
						
						if (pos != 'fixed') {
							$f.animate({
								'left': newLeft
							}, function() {
								$(this).animate({
									'top': newTop
								})
							})
						} else {
							$f.css({
								'position': 'absolute',
								'top': newTop,
								'left': (newLeft > 0 ? newLeft : 0)
							})
						}
					}
				},
				
				enterKeyHandler: function(e) {
					if (e.keyCode === 13) {
						$('#fallr-buttons').children().eq(0).focus();
						
						helpers.unbindKeyHandler()
					}
				},
				
				tabKeyHandler: function(e) {
					if (e.keyCode === 9) {
						$('#fallr-wrapper').find(':input, .fallr-button').eq(0).focus();
						
						helpers.unbindKeyHandler();
						
						e.preventDefault()
					}
				},
				
				closeKeyHandler: function(e) {
					if (e.keyCode === 27 && opts.closeKey) {
						methods.hide()
					}
				},
				
				unbindKeyHandler: function() {
					$(document).unbind('keydown', helpers.enterKeyHandler).unbind('keydown', helpers.tabKeyHandler)
				}
			};
		
		$(document).ready(function() {
			$('body').append('<div id="fallr-overlay"></div>');
			$('#fallr-overlay').bind('click', function() {
				if (opts.closeOverlay) {
					methods.hide()
				} else {
					methods.blink()
				}
			})
		});
		
		$(window).resize(function() {
			//console.log(opts.bound);
			if (methods.isActive() && opts.bound === window) {
				helpers.fixPos()
			}
		});
		
		$.fallr = function(method, options, callback) {
			var self = window;
			if (typeof method === 'object') {
				options = method;
				method = 'show'
			}
			if (methods[method]) {
				if (typeof options === 'function') {
					callback = options;
					options = null
				}
				methods[method](options, callback, self)
			} else {
				$.error('Method "' + method + '" does not exist in $.fallr')
			}
		}
	}(jQuery));
});