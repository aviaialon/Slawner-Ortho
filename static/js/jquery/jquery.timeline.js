function Timeline(j, l, app) {
    var a = this,
    m = $(document.body);
	this.Application = app;
    this._lightbox = this._overlay = this._column_center = this._column_right = this._column_left = this._spine = this._container = null;
    this._data = l;
    this._options = {
        animation: !0,
        lightbox: !0,
        showYear: !0,
        columnMode: "dual",
        allowDelete: !1,
        order: "desc"
    };
    this._years = [];
    this._readmore_text = this.Application.translate("Read More", "Lire la suite");
    this._max_element_width = 0;
    this._spint_margin = 100;
    this._elements = [];
    this._separators = [];
    this._gallery_queue = [];
    this._iframe_queue = [];
    this._use_css3 = function() {
        var a = document.body.style;
        if ("string" == typeof a.transition)
            return ! 0;
        for (var d = ["Webkit", "Moz", "Khtml", "O", "ms"], c = 0; c < d.length; c++)
            if ("string" == typeof a[d[c] + "Transition"])
            return ! 0;
        return ! 1
    } ();
    this._default_element_data = {
        type: "blog_post",
        date: "2000-01-01",
        width: 400,
        title: null,
        content: null,
        image: null,
        readmore: null,
        height: 300,
        images: [],
        speed: 5E3,
        url: null
    };
    this._createElement = function(b, d) {
        b = $.extend({}, a._default_element_data, b);
        /*var c = $("<div>").addClass("timeline_element " + b.type).width(b.width);*/
		var c = $("<div>").addClass("timeline_element " + b.type).css({
			'max-width': b.width + 'px',
			'width' : '100%'	
		});
        a._options.animation || c.addClass("animated");
        null !== b.title ? $("<div>").addClass("title").html('<span class="label">' + b.title + '</span><span class="date">' + a._getDateString(b.date) + "</span>").appendTo(c) : c.addClass("notitle");
        switch (b.type) {
        case "iframe":
            var e = $("<div>").addClass("content loading").height(b.height).appendTo(c);
            a._iframe_queue.push({
                element: e,
                url: b.url
            });
            break;
        case "blog_post":
            null !== b.image && (e = $("<div>").addClass("img_container").append($("<img>").attr("src", b.image)).appendTo(c), a._options.lightbox && e.append($("<div>").addClass("img_overlay").html('<span class="magnifier" data-type="blog_post" data-img="' + b.image + '"></span>')));
            null !== b.content && $("<div>").addClass("content").html(b.content).appendTo(c);
            null !== b.readmore && $("<div>").addClass("readmore").html('<a href="' + b.readmore + '" class="button small dark_blue" style="text-decoration: none">' + a._readmore_text + "</a>").appendTo(c);
            break;
        case "gallery":
            if (b.images.length) {
                var e = $("<div>").addClass("scroll_container").appendTo(c),
                g = $("<div>").addClass("scroller").height(b).appendTo(e).bind("gallery_onload", a._galleryOnLoad),
                f = "",
                k = 0,
                j = b.images.length;
                $(b.images).each(function(d, c) {
                    f += '<div class="img_container"><img height="' + b.height + '" src="' + c + '" />';
                    a._options.lightbox && (f += '<div class="img_overlay"><span class="magnifier" data-total="' + b.images.length + '" data-order="' + d + '" data-type="gallery" data-img="' + c + '"></span></div>');
                    f += "</div>";
                    var e = function() {
                        k++;
                        k === j && g.trigger("gallery_onload")
                        },
                    h = new Image;
                    h.onload = e;
                    h.onerror = e;
                    h.src = c
                });
                g.html('<div class="ruler">' + f + "</div>")
                }
            break;
        case "slider":
            var h = "";
            $(b.images).each(function(c, d) {
                h += '<div data-total="' + b.images.length + '" data-order="' + c + '" class="img_container' + (0 === c ? " active": "") + '" style="display:' + (0 === c ? "block": "none") + ';"><img src="' + d + '" />';
                a._options.lightbox && (h += '<div class="img_overlay"><span class="magnifier" data-total="' + b.images.length + '" data-order="' + c + '" data-type="slider" data-img="' + d + '"></span></div>');
                h += "</div>"
            });
            1 < b.images.length && (h += '<span class="slider_prev"></span><span class="slider_next"></span>');
            /*$("<div>").addClass("content").width(b.width).height(b.height).html(h).appendTo(c);*/
			$("<div>").addClass("content").css('width', '100%').height(b.height).html(h).appendTo(c);
            1 < b.images.length && (c.data("speed", b.speed), setTimeout(function() {
                a._updateSlider(c, "next")
                }, b.speed))
            }
        a._options.allowDelete && $("<div>").addClass("del").data("timeline_element", c).text("Delete").appendTo(c);
        c.appendTo(d);
        a._max_element_width = Math.max(a._max_element_width, b.width);
        a._elements.push(c);
        return c
    };
    this._deleteElement = function(a) {
        var d = a.parent();
        a.remove();
        d.children(".timeline_element").length || d.remove()
        };
    this._updateSlider = function(b, d) {
        b.data("timeout_id") && clearTimeout(b.data("timeout_id"));
        if (!a._overlay.hasClass("open")) {
            var c = b.find(".img_container.active").removeClass("active"),
            e = "next" === d ? c.data("order") === c.data("total") - 1 ? b.find(".img_container:first").addClass("active") : c.next().addClass("active") : 0 === c.data("order") ? b.find(".img_container:last").addClass("active") : c.prev().addClass("active");
            c.fadeOut();
            e.fadeIn()
            }
        c = setTimeout(function() {
            a._updateSlider(b, d)
            }, b.data("speed"));
        b.data("timeout_id", c)
        };
    this._startAnimation = function(b) {
        $(window).width();
        a._use_css3 ? a._spine.addClass("animated") : a._spine.animate({
            bottom: "0%"
        }, 500, function() {
            a._spine.addClass("animated")
            });
        a._options.showYear && setTimeout(function() {
            $(a._separators).each(function(b, c) {
                a._use_css3 ? c.addClass("animated") : c.children("span").animate({
                    opacity: 1,
                    top: "50%"
                }, 300, function() {
                    c.addClass("animated")
                    })
                })
            }, 500);
        $(a._elements).each(function(d, c) {
            setTimeout(function() {
                a._use_css3 ? c.addClass("animated") : c.hide().addClass("animated").fadeIn();
                d === a._elements.length - 1 && setTimeout(b, 200)
                }, (a._options.showYear ? 1E3: 500) + 100 * d)
            });
        return ! 0
    };
    this._getDateString = function(a) {
        a = a.split("-");
        return a[2] + " " + " January February March April May June July August September October November December".split(" ")[parseInt(a[1], 10)] + " " + a[0]
        };
    this._setGalleryWidth = function(a) { ! 0 !== a.data("loaded") && (a.parent().addClass("loaded"), a.data("loaded", !0).width(a.children("div.ruler").width()))
        };
    this._openLightBox = function(b, d) {
        b.parent().addClass("loading");
        "gallery" === b.data("type") || "slider" === b.data("type") ? (a._lightbox.children("span").show(), a._lightbox.data("magnifier", b), a._toggleLightBoxControl(parseInt(b.data("total"), 10), parseInt(b.data("order"), 10))) : a._lightbox.children("span").hide();
        setTimeout(function() {
            var c = new Image;
            c.onload = function() {
                b.parent().removeClass("loading");
                a._overlay.addClass("open");
                $("<img>").attr("src", d).appendTo(a._lightbox);
                var e = a._getLightboxSize(c.width, c.height),
                e = {
                    width: e.width,
                    height: e.height,
                    margin: "-" + e.height / 2 + "px 0px 0px -" + e.width / 2 + "px"
                };
                a._use_css3 ? a._lightbox.addClass("loaded").css(e) : a._lightbox.css(e).animate({
                    top: "50%",
                    opacity: 1
                }, 300, function() {
                    a._lightbox.addClass("loaded")
                    })
                };
            c.src = d
        }, 1E3);
        return d
    };
    this._closeLightBox = function() {
        a._use_css3 ? a._lightbox.removeClass("loaded") : a._lightbox.animate({
            top: 0,
            opacity: 0
        }, 300, function() {
            a._lightbox.removeClass("loaded")
            });
        setTimeout(function() {
            a._overlay.removeClass("open");
            a._lightbox.removeAttr("style").children("img").remove()
            }, 300)
        };
    this._getLightboxSize = function(a, d) {
        var c = 0.9 * $(window).width(),
        e = 0.9 * $(window).height(),
        g = a,
        f = d;
        if (a > c || d > e)
            a > c && d <= e ? (g = c, f = d / (a / g)) : d > e && a <= c ? (f = e, g = a / (d / f)) : (g = c, f = d / (a / g), f > e && (f = e, g = a / (d / f)));
        return {
            width: g,
            height: f
        }
    };
    this._navLightBox = function(b, d) {
        var c = "next" === d ? a._lightbox.data("magnifier").parents(".img_container:first").next().find("span.magnifier") : a._lightbox.data("magnifier").parents(".img_container:first").prev().find("span.magnifier"),
        e = c.data("img"),
        g = new Image;
        g.onload = function() {
            a._lightbox.data("magnifier", c).addClass("updating");
            a._lightbox.children("img").attr("src", e);
            var b = a._getLightboxSize(g.width, g.height),
            b = {
                width: b.width,
                height: b.height,
                margin: "-" + b.height / 2 + "px 0px 0px -" + b.width / 2 + "px"
            };
            a._use_css3 ? a._lightbox.css(b) : a._lightbox.animate(b, 500);
            a._toggleLightBoxControl(parseInt(c.data("total"), 10), parseInt(c.data("order"), 10));
            setTimeout(function() {
                a._lightbox.removeClass("updating")
                }, 500)
            };
        g.src = e
    };
    this._toggleLightBoxControl = function(b, d) {
        1 >= b ? a._lightbox.children("span").hide() : (0 === d ? a._lightbox.children("span.prev").hide() : a._lightbox.children("span.prev").show(), d === b - 1 ? a._lightbox.children("span.next").hide() : a._lightbox.children("span.next").show())
        };
    this._processGalleryOnloadQueue = function() {
        $(a._gallery_queue).each(function(b, d) {
            a._setGalleryWidth(d)
            });
        a._gallery_queue = []
        };
    this._processIframeQueue = function() {
        $(a._iframe_queue).each(function(a, d) {
            d.element.removeClass("loading").html('<iframe frameborder="0" src="' + d.url + '"></iframe>')
            })
        };
    this._galleryOnLoad = function() {
        var b = $(this); ! 0 === a._container.data("loaded") ? a._setGalleryWidth(b) : a._gallery_queue.push(b);
        return ! 0
    };
    this._handleClick = function(b) {
        var d = $(b.target);
        d.hasClass("timeline_overlay") ? a._closeLightBox(b) : d.hasClass("magnifier") ? a._openLightBox(d, d.data("img")) : d.hasClass("prev") ? a._navLightBox(d, "prev") : d.hasClass("next") ? a._navLightBox(d, "next") : d.hasClass("slider_prev") ? a._updateSlider(d.parents(".timeline_element:first"), "prev") : d.hasClass("slider_next") ? a._updateSlider(d.parents(".timeline_element:first"), "next") : d.hasClass("del") && a._deleteElement(d.data("timeline_element"));
        return ! 0
    };
    this._handleKeyDown = function(b) {
        switch (parseInt(b.which, 10)) {
        case 27:
            a._overlay.hasClass("open") && a._closeLightBox(b);
            break;
        case 37:
            if (a._lightbox.hasClass("loaded") && a._lightbox.children("span.prev").is(":visible"))
                return a._lightbox.children("span.prev").click(),
            !1;
            break;
        case 39:
            if (a._lightbox.hasClass("loaded") && a._lightbox.children("span.next").is(":visible"))
                return a._lightbox.children("span.next").click(),
            !1
        }
    };
    this.setOptions = function(b) {
        a._options = $.extend(a._options, b);
        return a._options
    };
    this.display = function() { ! 0 !== $(document).data("timeline_events_binded") && $(document).data("timeline_events_binded", !0).click(a._handleClick).keydown(a._handleKeyDown);
        a._data.sort(function(b, d) {
            return "desc" === a._options.order ? parseInt(d.date.replace(/-/g, ""), 10) - parseInt(b.date.replace(/-/g, ""), 10) : parseInt(b.date.replace(/-/g, ""), 10) - parseInt(d.date.replace(/-/g, ""), 10)
            });
        a._options.lightbox && (a._overlay = $(".timeline_overlay"), a._overlay.length ? a._lightbox = a._overlay.children(".lightbox") : (a._overlay = $("<div>").addClass("timeline_overlay"), a._lightbox = $("<div>").addClass("lightbox").html('<span class="prev"></span><span class="next"></span>').appendTo(a._overlay), a._overlay.appendTo(m)));
        a._container = $("<div>").addClass("timeline " + a._options.columnMode);
        $.support.opacity || a._container.addClass("opacityFilter");
        a._use_css3 || a._container.addClass("noneCSS3");
        a._spine = $("<div>").addClass("spine").appendTo(a._container);
        a._options.animation || a._spine.addClass("animated");
        $(a._data).each(function(b, d) {
            var c = d.date.split("-")[0];
            if ( - 1 === $.inArray(c, a._years)) {
                a._years.push(c);
                if (/*a._options.showYear && 1 < a._years.length*/ true) {
                    var e = $("<div>").addClass("date_separator").html("<span>" + c + "</span>").appendTo(a._container);
                    a._options.animation || e.addClass(animated);
                    a._separators.push(e)
                    }
                if (a._options.showYear || 0 === b)"dual" === a._options.columnMode ? (a._column_left = $("<div>").addClass("column_left year_" + c).appendTo(a._container), a._column_right = $("<div>").addClass("column_right year_" + c).appendTo(a._container)) : "left" === a._options.columnMode ? a._column_left = $("<div>").addClass("column_left year_" + c).appendTo(a._container) : "right" === a._options.columnMode ? a._column_right = $("<div>").addClass("column_right year_" + c).appendTo(a._container) : "center" === a._options.columnMode && (a._column_center = $("<div>").addClass("column_center year_" + c).appendTo(a._container))
                }"dual" === a._options.columnMode ? a._createElement(d, 0 === b % 2 ? a._column_left: a._column_right) : "left" === a._options.columnMode ? a._createElement(d, a._column_left) : "right" === a._options.columnMode ? a._createElement(d, a._column_right) : "center" === a._options.columnMode && a._createElement(d, a._column_center)
            });
        /*a._max_element_width && ("dual" === a._options.columnMode ? a._container.width(2 * a._max_element_width + a._spint_margin) : a._container.width(a._max_element_width + a._spint_margin));*/
		a._max_element_width && ("dual" === a._options.columnMode ? a._container.css('width', '100%') : a._container.width(a._max_element_width + a._spint_margin));
        a._container.data("loaded", !0).appendTo(j);
        a._processGalleryOnloadQueue();
        a._options.animation ? setTimeout(function() {
            a._startAnimation(a._processIframeQueue)
            }, 200) : a._processIframeQueue();
        return ! 0
    }
};


var SLAWNER 						= SLAWNER || {};
SLAWNER.APPLICATION 				= SLAWNER.APPLICATION || {};
SLAWNER.APPLICATION.TIMELINE_HISTORY = function(objJqueryTarget, objOptions, objApplication) {
	return 	(new Timeline(objJqueryTarget, objOptions, objApplication));
};