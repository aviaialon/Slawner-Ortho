var sliderMap = null;
if (typeof Object.create !== "function") {function C() {}
    Object.create = function (a) {
        C.prototype = a;
        return new C()
    }
}(function ($, m, n, o) {
    var p = {
        options: {},
        init: function (a, b) {
            var c = this;
            this.options = $.extend({}, $.fn.StylizedMap.options, b);
            this.container = a;
            this.extendInfoWindow()
        },
        render: function () {
            var c = this;
            this.createMap();
            $.each(this.options.locations, function (a, b) {
                c.createMarker(a, b)
            })
        },
        createMap: function () {
            var a = {
                zoom: this.options.zoomLevel,
                styles: this.options.styles,
                mapTypeControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            if (this.options.center) a.center = new google.maps.LatLng(this.options.center.lat, this.options.center.long);
            this.map = new google.maps.Map(this.container[0], a);
			sliderMap = this.map;
        },
        createMarker: function (a, b) {
            var c = this;
            var d = b.slides;
            var e = "";
            var f = this.container.attr("id") + "_marker_" + a;
            var g = true;
            var h = new google.maps.LatLng(b.lat, b.long);
            if (d != o) {
                for (var i = 0; i < d.length; i++) {
                    e += '<div><img src="' + d[i] + '"/></div>';
                    g = false
                }
            }
            var j = '';
            j += '<div id="' + f + '" class="window-imgcnt ' + (g ? "window-noslides" : "") + '">';
            j += '  <div class="slides_container">';
            j += e;
            j += '  </div>';
            j += '</div>';
            j += '<h3 class="window-title">' + b.title + '</h3>';
            j += '<p class="window-desc">' + b.sub_title + '</p>';
            b.window = new this.StylizedWindow({
                content: j,
                position: h,
                pixelOffset: new google.maps.Size(-157, - 68),
                pinURL: this.options.arrowUrl,
                boxClass: "window"
            });
			/*
            var k = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|52B552|ff0000', new google.maps.Size(20, 32), new google.maps.Point(0, 0), new google.maps.Point(10, 32));
            var l = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow', new google.maps.Size(37, 32), new google.maps.Point(0, 0), new google.maps.Point(14, 33));
            b.marker = new google.maps.Marker({
                position: h,
                map: this.map,
                shadow: l,
                icon: k,
                title: a.toString()
            });
			*/
			
			var k = new google.maps.MarkerImage('http://logistics.gatech.pa/bundles/images/icons/map/marker-s.png', new google.maps.Size(20, 30), new google.maps.Point(0, 0), new google.maps.Point(10, 32));
            var l = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow', new google.maps.Size(37, 32), new google.maps.Point(0, 0), new google.maps.Point(14, 33));
            b.marker = new google.maps.Marker({
                position: h,
                map: this.map,
                shadow: l,
                icon: k,
                title: a.toString()
            });
			google.maps.event.addListener(b.window, 'domready', function () {
                $("#" + f).slides({
                    preload: true,
                    crossfade: true,
                    fadeSpeed: c.options.slideSpeed,
                    slideSpeed: c.options.slideSpeed,
                    play: c.options.animationDelay
                })
            });
            google.maps.event.addListener(b.window, 'closeclick', function () {
                c.closeActiveLocation()
            });
            google.maps.event.addListener(b.marker, 'click', function () {
                c.activateLocaton(b)
            });
			
            if (b.active != o && b.active) this.activateLocaton(b)
        },
        activateLocaton: function (a) {
            this.closeActiveLocation();
            this.setActiveLocation(a);
            a.window.open(this.map, a.marker);
            a.marker.setVisible(false)
        },
        setStyle: function (a) {
            this.map.setOptions({
                styles: a
            })
        },
        setActiveLocation: function (a) {
            this.activeLocation = a
        },
        closeActiveLocation: function () {
            if (this.activeLocation != o) {
                this.activeLocation.window.close();
                this.activeLocation.marker.setVisible(true)
            }
        },
        extendInfoWindow: function () {
            this.StylizedWindow = function (a) {
                a = a || {};
                google.maps.OverlayView.apply(this, arguments);
                this.content_ = a.content || "";
                this.disableAutoPan_ = a.disableAutoPan || false;
                this.maxWidth_ = a.maxWidth || 0;
                this.pixelOffset_ = a.pixelOffset || new google.maps.Size(0, 0);
                this.position_ = a.position || new google.maps.LatLng(0, 0);
                this.zIndex_ = a.zIndex || null;
                this.boxClass_ = a.boxClass || "infoBox";
                this.boxStyle_ = a.boxStyle || {};
                this.closeBoxMargin_ = a.closeBoxMargin || "2px";
                this.pinURL_ = a.pinURL;
                this.closeBoxURL_ = a.closeBoxURL || "";
                if (a.closeBoxURL === "") {
                    this.closeBoxURL_ = ""
                }
                this.infoBoxClearance_ = a.infoBoxClearance || new google.maps.Size(1, 1);
                this.isHidden_ = a.isHidden || false;
                this.isRollover_ = a.isRollover || false;
                this.alignBottom_ = a.alignBottom || false;
                this.pane_ = a.pane || "floatPane";
                this.enableEventPropagation_ = a.enableEventPropagation || false;
                this.div_ = null;
                this.closeListener_ = null;
                this.eventListener1_ = null;
                this.eventListener2_ = null;
                this.eventListener3_ = null;
                this.moveListener_ = null;
                this.contextListener_ = null;
                this.fixedWidthSet_ = null
            };
            this.StylizedWindow.prototype = {
                createInfoBoxDiv_: function () {
                    var a, me = this,
                        cancelHandler = function (e) {
                            e.cancelBubble = true;
                            if (e.stopPropagation) {
                                e.stopPropagation()
                            }
                        },
                        ignoreHandler = function (e) {
                            e.returnValue = false;
                            if (e.preventDefault) {
                                e.preventDefault()
                            }
                            if (!me.enableEventPropagation_) {
                                cancelHandler(e)
                            }
                        };
                    if (!this.div_) {
                        this.div_ = n.createElement("div");
                        this.setBoxStyle_();
                        if (typeof this.content_.nodeType === "undefined") {
                            this.div_.innerHTML = this.getCloseBoxImg_() + this.getPinImg_() + this.content_
                        } else {
                            this.div_.innerHTML = this.getCloseBoxImg_() + this.getPinImg_();
                            this.div_.appendChild(this.content_)
                        }
                        this.getPanes()[this.pane_].appendChild(this.div_);
                        this.addClickHandler_();
                        if (this.div_.style.width) {
                            this.fixedWidthSet_ = true
                        } else {}
                        this.panBox_(this.disableAutoPan_);
                        if (!this.enableEventPropagation_) {
                            this.eventListener1_ = google.maps.event.addDomListener(this.div_, "mousedown", cancelHandler);
                            this.eventListener2_ = google.maps.event.addDomListener(this.div_, "click", cancelHandler);
                            this.eventListener3_ = google.maps.event.addDomListener(this.div_, "dblclick", cancelHandler)
                        }
                        this.contextListener_ = google.maps.event.addDomListener(this.div_, "contextmenu", ignoreHandler);
                        google.maps.event.trigger(this, "domready")
                    }
                },
                getPinImg_: function () {
                    return '<div class="window-arrow"></div>'
                },
                getCloseBoxImg_: function () {
                    return '<div class="window-close"></div>'
                },
                addClickHandler_: function () {
                    var a = this.div_.firstChild;
                    this.closeListener_ = google.maps.event.addDomListener(a, "click", this.getCloseClickHandler_())
                },
                getCloseClickHandler_: function () {
                    var a = this;
                    return function (e) {
                        e.cancelBubble = true;
                        if (e.stopPropagation) {
                            e.stopPropagation()
                        }
                        a.close();
                        google.maps.event.trigger(a, "closeclick")
                    }
                },
                panBox_: function (a) {
                    var b, bounds, mapDiv, mapWidth, mapHeight, iwOffsetX, iwOffsetY, iwWidth, iwHeight, padX, padY, pixPosition, c, xOffset = 0,
                        yOffset = 0;
                    if (!a) {
                        b = this.getMap();
                        if (!b.getBounds().contains(this.position_)) {
                            b.setCenter(this.position_)
                        }
                        bounds = b.getBounds();
                        mapDiv = b.getDiv();
                        mapWidth = mapDiv.offsetWidth;
                        mapHeight = mapDiv.offsetHeight;
                        iwOffsetX = this.pixelOffset_.width;
                        iwOffsetY = this.pixelOffset_.height;
                        iwWidth = this.div_.offsetWidth;
                        iwHeight = this.div_.offsetHeight;
                        padX = this.infoBoxClearance_.width;
                        padY = this.infoBoxClearance_.height;
                        pixPosition = this.getProjection().fromLatLngToContainerPixel(this.position_);
                        if (pixPosition.x < -iwOffsetX + padX) {
                            xOffset = pixPosition.x + iwOffsetX - padX
                        } else if (pixPosition.x + iwWidth + iwOffsetX + padX > mapWidth) {
                            xOffset = pixPosition.x + iwWidth + iwOffsetX + padX - mapWidth
                        }
                        if (this.alignBottom_) {
                            if (pixPosition.y < -iwOffsetY + padY + iwHeight) {
                                yOffset = pixPosition.y + iwOffsetY - padY - iwHeight
                            } else if (pixPosition.y + iwOffsetY + padY > mapHeight) {
                                yOffset = pixPosition.y + iwOffsetY + padY - mapHeight
                            }
                        } else {
                            if (pixPosition.y < -iwOffsetY + padY) {
                                yOffset = pixPosition.y + iwOffsetY - padY
                            } else if (pixPosition.y + iwHeight + iwOffsetY + padY > mapHeight) {
                                yOffset = pixPosition.y + iwHeight + iwOffsetY + padY - mapHeight
                            }
                        }
                        if (this.isRollover_ === true) {
                            c = b.getCenter();
                            b.panBy(xOffset, yOffset)
                        } else {
                            c = b.getCenter();
                            b.panTo(this.position_)
                        }
                    }
                },
                setBoxStyle_: function () {
                    var i, boxStyle;
                    if (this.div_) {
                        this.div_.className = this.boxClass_;
                        this.div_.style.cssText = "";
                        boxStyle = this.boxStyle_;
                        if (typeof this.div_.style.opacity !== "undefined" && this.div_.style.opacity !== "") {
                            this.div_.style.filter = "alpha(opacity=" + this.div_.style.opacity * 100 + ")"
                        }
                        this.div_.style.position = "absolute";
                        this.div_.style.visibility = "hidden";
                        if (this.zIndex_ !== null) {
                            this.div_.style.zIndex = this.zIndex_
                        }
                    }
                },
                getBoxWidths_: function () {
                    var a, bw = {
                        top: 0,
                        bottom: 0,
                        left: 0,
                        right: 0
                    },
                        box = this.div_;
                    if (n.defaultView && n.defaultView.getComputedStyle) {
                        a = box.ownerDocument.defaultView.getComputedStyle(box, "");
                        if (a) {
                            bw.top = parseInt(a.borderTopWidth, 10) || 0;
                            bw.bottom = parseInt(a.borderBottomWidth, 10) || 0;
                            bw.left = parseInt(a.borderLeftWidth, 10) || 0;
                            bw.right = parseInt(a.borderRightWidth, 10) || 0
                        }
                    } else if (n.documentElement.currentStyle) {
                        if (box.currentStyle) {
                            bw.top = parseInt(box.currentStyle.borderTopWidth, 10) || 0;
                            bw.bottom = parseInt(box.currentStyle.borderBottomWidth, 10) || 0;
                            bw.left = parseInt(box.currentStyle.borderLeftWidth, 10) || 0;
                            bw.right = parseInt(box.currentStyle.borderRightWidth, 10) || 0
                        }
                    }
                    return bw
                },
                onRemove: function () {
                    if (this.div_) {
                        this.div_.parentNode.removeChild(this.div_);
                        this.div_ = null
                    }
                },
                draw: function () {
                    var a;
                    this.createInfoBoxDiv_();
                    a = this.getProjection().fromLatLngToDivPixel(this.position_);
                    this.div_.style.left = a.x + this.pixelOffset_.width + "px";
                    if (this.alignBottom_) {
                        this.div_.style.bottom = -(a.y + this.pixelOffset_.height) + "px"
                    } else {
                        this.div_.style.top = a.y + this.pixelOffset_.height + "px"
                    }
                    if (this.isHidden_) {
                        this.div_.style.visibility = "hidden"
                    } else {
                        this.div_.style.visibility = "visible"
                    }
                },
                setOptions: function (a) {
                    if (typeof a.boxClass !== "undefined") {
                        this.boxClass_ = a.boxClass;
                        this.setBoxStyle_()
                    }
                    if (typeof a.boxStyle !== "undefined") {
                        this.boxStyle_ = a.boxStyle;
                        this.setBoxStyle_()
                    }
                    if (typeof a.content !== "undefined") {
                        this.setContent(a.content)
                    }
                    if (typeof a.disableAutoPan !== "undefined") {
                        this.disableAutoPan_ = a.disableAutoPan
                    }
                    if (typeof a.maxWidth !== "undefined") {
                        this.maxWidth_ = a.maxWidth
                    }
                    if (typeof a.pixelOffset !== "undefined") {
                        this.pixelOffset_ = a.pixelOffset
                    }
                    if (typeof a.position !== "undefined") {
                        this.setPosition(a.position)
                    }
                    if (typeof a.isRollover !== "undefined") {
                        this.setisRollover(a.isRollover)
                    }
                    if (typeof a.zIndex !== "undefined") {
                        this.setZIndex(a.zIndex)
                    }
                    if (typeof a.closeBoxMargin !== "undefined") {
                        this.closeBoxMargin_ = a.closeBoxMargin
                    }
                    if (typeof a.closeBoxURL !== "undefined") {
                        this.closeBoxURL_ = a.closeBoxURL
                    }
                    if (typeof a.infoBoxClearance !== "undefined") {
                        this.infoBoxClearance_ = a.infoBoxClearance
                    }
                    if (typeof a.isHidden !== "undefined") {
                        this.isHidden_ = a.isHidden
                    }
                    if (typeof a.enableEventPropagation !== "undefined") {
                        this.enableEventPropagation_ = a.enableEventPropagation
                    }
                    if (this.div_) {
                        this.draw()
                    }
                },
                setContent: function (a) {
                    this.content_ = a;
                    if (this.div_) {
                        if (this.closeListener_) {
                            google.maps.event.removeListener(this.closeListener_);
                            this.closeListener_ = null
                        }
                        if (!this.fixedWidthSet_) {
                            this.div_.style.width = ""
                        }
                        if (typeof a.nodeType === "undefined") {
                            this.div_.innerHTML = this.getCloseBoxImg_() + a
                        } else {
                            this.div_.innerHTML = this.getCloseBoxImg_();
                            this.div_.appendChild(a)
                        }
                        if (!this.fixedWidthSet_) {
                            this.div_.style.width = this.div_.offsetWidth + "px";
                            this.div_.innerHTML = this.getCloseBoxImg_() + a
                        }
                        this.addClickHandler_()
                    }
                    google.maps.event.trigger(this, "content_changed")
                },
                setPosition: function (a) {
                    this.position_ = a;
                    if (this.div_) {
                        this.draw()
                    }
                    google.maps.event.trigger(this, "position_changed")
                },
                setisRollover: function (a) {
                    this.isRollover_ = a
                },
                setZIndex: function (a) {
                    this.zIndex_ = a;
                    if (this.div_) {
                        this.div_.style.zIndex = a
                    }
                    google.maps.event.trigger(this, "zindex_changed")
                },
                getContent: function () {
                    return this.content_
                },
                getisRollover: function () {
                    return this.isRollover_
                },
                getPosition: function () {
                    return this.position_
                },
                getZIndex: function () {
                    return this.zIndex_
                },
                show: function () {
                    this.isHidden_ = false;
                    this.div_.style.visibility = "visible"
                },
                hide: function () {
                    this.isHidden_ = true;
                    this.div_.style.visibility = "hidden"
                },
                open: function (a, b) {
                    var c = this;
                    if (b) {
                        this.position_ = b.getPosition();
                        this.moveListener_ = google.maps.event.addListener(b, "position_changed", function () {
                            c.setPosition(this.getPosition())
                        })
                    }
                    this.setMap(a);
                    if (this.div_) {
                        this.panBox_()
                    }
                },
                close: function () {
                    if (this.closeListener_) {
                        google.maps.event.removeListener(this.closeListener_);
                        this.closeListener_ = null
                    }
                    if (this.eventListener1_) {
                        google.maps.event.removeListener(this.eventListener1_);
                        google.maps.event.removeListener(this.eventListener2_);
                        google.maps.event.removeListener(this.eventListener3_);
                        this.eventListener1_ = null;
                        this.eventListener2_ = null;
                        this.eventListener3_ = null
                    }
                    if (this.moveListener_) {
                        google.maps.event.removeListener(this.moveListener_);
                        this.moveListener_ = null
                    }
                    if (this.contextListener_) {
                        google.maps.event.removeListener(this.contextListener_);
                        this.contextListener_ = null
                    }
                    this.setMap(null)
                }
            };
            this.StylizedWindow.prototype = $.extend({}, new google.maps.OverlayView, this.StylizedWindow.prototype)
        }
    };
    $.fn.StylizedMap = function (b) {
        return this.each(function () {
            var a = Object.create(p);
            a.init($(this), b);
            a.render()
        })
    };
    $.fn.StylizedMap.styles = {
        standard: {},
        rainbow: [{
            featureType: "water",
            stylers: [{
                color: "#7EACF2"
            }]
        }, {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{
                color: "#ABCF06"
            }]
        }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
                color: "#FF8C10"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "labels.text.stroke",
            stylers: [{
                color: "#ffffff"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "geometry.fill",
            stylers: [{
                color: "#FF6C5E"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#000000"
            }]
        }, {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#000000"
            }]
        }, {
            featureType: "road.highway",
            elementType: "labels.text.stroke",
            stylers: [{
                color: "#ffffff"
            }]
        }],
        gray: [{
            stylers: [{
                invert_lightness: false
            }, {
                saturation: -700
            }, {
                lightness: -20
            }, {
                gamma: 0.9
            }]
        }],
        red: [{
            stylers: [{
                hue: "#FF0000"
            }, {
                invert_lightness: false
            }, {
                saturation: 20
            }, {
                lightness: 20
            }, {
                gamma: 0.525
            }]
        }],
        blue: [{
            stylers: [{
                hue: "#435158"
            }, {
                invert_lightness: true
            }, {
                saturation: 10
            }, {
                lightness: 30
            }, {
                gamma: 0.5
            }]
        }],
		navy: [{
            stylers: [{
                hue: "#004E84"
            }, {
                invert_lightness: false
            }, {
                saturation: 10
            }, {
                lightness: 10
            }, {
                gamma: 0.5
            }]
        }],
		light_blue: [{
            stylers: [{
                hue: "#01AFEE"
            }, {
                invert_lightness: false
            }, {
                saturation: 10
            }, {
                lightness: 30
            }, {
                gamma: 0.5
            }]
        }],
		green: [{
            stylers: [{
                hue: "#5EA919"
            }, {
                invert_lightness: false
            }, {
                saturation: 100
            }, {
                lightness: 10
            }, {
                gamma: 0.5
            }]
        }]
    };
    $.fn.StylizedMap.options = {
        zoomLevel: 12,
        styles: $.fn.StylizedMap.styles.standard,
        locations: [],
        center: false,
        slideSpeed: 1000,
        animationDelay: 3000,
        arrowUrl: "images/arrow.png"
    }
})($, window, document);

/*
* Slides, A Slideshow Plugin for jQuery
* Intructions: http://slidesjs.com
* By: Nathan Searles, http://nathansearles.com
* Version: 1.1.9
* Updated: September 5th, 2011
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
(function(a){a.fn.slides=function(b){return b=a.extend({},a.fn.slides.option,b),this.each(function(){function w(g,h,i){if(!p&&o){p=!0,b.animationStart(n+1);switch(g){case"next":l=n,k=n+1,k=e===k?0:k,r=f*2,g=-f*2,n=k;break;case"prev":l=n,k=n-1,k=k===-1?e-1:k,r=0,g=0,n=k;break;case"pagination":k=parseInt(i,10),l=a("."+b.paginationClass+" li."+b.currentClass+" a",c).attr("href").match("[^#/]+$"),k>l?(r=f*2,g=-f*2):(r=0,g=0),n=k}h==="fade"?b.crossfade?d.children(":eq("+k+")",c).css({zIndex:10}).fadeIn(b.fadeSpeed,b.fadeEasing,function(){b.autoHeight?d.animate({height:d.children(":eq("+k+")",c).outerHeight()},b.autoHeightSpeed,function(){d.children(":eq("+l+")",c).css({display:"none",zIndex:0}),d.children(":eq("+k+")",c).css({zIndex:0}),b.animationComplete(k+1),p=!1}):(d.children(":eq("+l+")",c).css({display:"none",zIndex:0}),d.children(":eq("+k+")",c).css({zIndex:0}),b.animationComplete(k+1),p=!1)}):d.children(":eq("+l+")",c).fadeOut(b.fadeSpeed,b.fadeEasing,function(){b.autoHeight?d.animate({height:d.children(":eq("+k+")",c).outerHeight()},b.autoHeightSpeed,function(){d.children(":eq("+k+")",c).fadeIn(b.fadeSpeed,b.fadeEasing)}):d.children(":eq("+k+")",c).fadeIn(b.fadeSpeed,b.fadeEasing,function(){a.browser.msie&&a(this).get(0).style.removeAttribute("filter")}),b.animationComplete(k+1),p=!1}):(d.children(":eq("+k+")").css({left:r,display:"block"}),b.autoHeight?d.animate({left:g,height:d.children(":eq("+k+")").outerHeight()},b.slideSpeed,b.slideEasing,function(){d.css({left:-f}),d.children(":eq("+k+")").css({left:f,zIndex:5}),d.children(":eq("+l+")").css({left:f,display:"none",zIndex:0}),b.animationComplete(k+1),p=!1}):d.animate({left:g},b.slideSpeed,b.slideEasing,function(){d.css({left:-f}),d.children(":eq("+k+")").css({left:f,zIndex:5}),d.children(":eq("+l+")").css({left:f,display:"none",zIndex:0}),b.animationComplete(k+1),p=!1})),b.pagination&&(a("."+b.paginationClass+" li."+b.currentClass,c).removeClass(b.currentClass),a("."+b.paginationClass+" li:eq("+k+")",c).addClass(b.currentClass))}}function x(){clearInterval(c.data("interval"))}function y(){b.pause?(clearTimeout(c.data("pause")),clearInterval(c.data("interval")),u=setTimeout(function(){clearTimeout(c.data("pause")),v=setInterval(function(){w("next",i)},b.play),c.data("interval",v)},b.pause),c.data("pause",u)):x()}a("."+b.container,a(this)).children().wrapAll('<div class="slides_control"/>');var c=a(this),d=a(".slides_control",c),e=d.children().size(),f=d.children().outerWidth(),g=d.children().outerHeight(),h=b.start-1,i=b.effect.indexOf(",")<0?b.effect:b.effect.replace(" ","").split(",")[0],j=b.effect.indexOf(",")<0?i:b.effect.replace(" ","").split(",")[1],k=0,l=0,m=0,n=0,o,p,q,r,s,t,u,v;if(e<2)return a("."+b.container,a(this)).fadeIn(b.fadeSpeed,b.fadeEasing,function(){o=!0,b.slidesLoaded()}),a("."+b.next+", ."+b.prev).fadeOut(0),!1;if(e<2)return;h<0&&(h=0),h>e&&(h=e-1),b.start&&(n=h),b.randomize&&d.randomize(),a("."+b.container,c).css({overflow:"hidden",position:"relative"}),d.children().css({position:"absolute",top:0,left:d.children().outerWidth(),zIndex:0,display:"none"}),d.css({position:"relative",width:f*3,height:g,left:-f}),a("."+b.container,c).css({display:"block"}),b.autoHeight&&(d.children().css({height:"auto"}),d.animate({height:d.children(":eq("+h+")").outerHeight()},b.autoHeightSpeed));if(b.preload&&d.find("img:eq("+h+")").length){a("."+b.container,c).css({background:"url("+b.preloadImage+") no-repeat 50% 50%"});var z=d.find("img:eq("+h+")").attr("src")+"?"+(new Date).getTime();a("img",c).parent().attr("class")!="slides_control"?t=d.children(":eq(0)")[0].tagName.toLowerCase():t=d.find("img:eq("+h+")"),d.find("img:eq("+h+")").attr("src",z).load(function(){d.find(t+":eq("+h+")").fadeIn(b.fadeSpeed,b.fadeEasing,function(){a(this).css({zIndex:5}),a("."+b.container,c).css({background:""}),o=!0,b.slidesLoaded()})})}else d.children(":eq("+h+")").fadeIn(b.fadeSpeed,b.fadeEasing,function(){o=!0,b.slidesLoaded()});b.bigTarget&&(d.children().css({cursor:"pointer"}),d.children().click(function(){return w("next",i),!1})),b.hoverPause&&b.play&&(d.bind("mouseover",function(){x()}),d.bind("mouseleave",function(){y()})),b.generateNextPrev&&(a("."+b.container,c).after('<a href="#" class="'+b.prev+'">Prev</a>'),a("."+b.prev,c).after('<a href="#" class="'+b.next+'">Next</a>')),a("."+b.next,c).click(function(a){a.preventDefault(),b.play&&y(),w("next",i)}),a("."+b.prev,c).click(function(a){a.preventDefault(),b.play&&y(),w("prev",i)}),b.generatePagination?(b.prependPagination?c.prepend("<ul class="+b.paginationClass+"></ul>"):c.append("<ul class="+b.paginationClass+"></ul>"),d.children().each(function(){a("."+b.paginationClass,c).append('<li><a href="#'+m+'">'+(m+1)+"</a></li>"),m++})):a("."+b.paginationClass+" li a",c).each(function(){a(this).attr("href","#"+m),m++}),a("."+b.paginationClass+" li:eq("+h+")",c).addClass(b.currentClass),a("."+b.paginationClass+" li a",c).click(function(){return b.play&&y(),q=a(this).attr("href").match("[^#/]+$"),n!=q&&w("pagination",j,q),!1}),a("a.link",c).click(function(){return b.play&&y(),q=a(this).attr("href").match("[^#/]+$")-1,n!=q&&w("pagination",j,q),!1}),b.play&&(v=setInterval(function(){w("next",i)},b.play),c.data("interval",v))})},a.fn.slides.option={preload:!1,preloadImage:"/img/loading.gif",container:"slides_container",generateNextPrev:!1,next:"next",prev:"prev",pagination:!0,generatePagination:!0,prependPagination:!1,paginationClass:"pagination",currentClass:"current",fadeSpeed:350,fadeEasing:"",slideSpeed:350,slideEasing:"",start:1,effect:"slide",crossfade:!1,randomize:!1,play:0,pause:0,hoverPause:!1,autoHeight:!1,autoHeightSpeed:350,bigTarget:!1,animationStart:function(){},animationComplete:function(){},slidesLoaded:function(){}},a.fn.randomize=function(b){function c(){return Math.round(Math.random())-.5}return a(this).each(function(){var d=a(this),e=d.children(),f=e.length;if(f>1){e.hide();var g=[];for(i=0;i<f;i++)g[g.length]=i;g=g.sort(c),a.each(g,function(a,c){var f=e.eq(c),g=f.clone(!0);g.show().appendTo(d),b!==undefined&&b(f,g),f.remove()})}})}})(jQuery)
