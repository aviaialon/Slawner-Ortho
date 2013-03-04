(function (f) {
    function e(a) {
        var b = a || window.event,
            d = [].slice.call(arguments, 1),
            e = 0,
            i = 0,
            h = 0,
            a = f.event.fix(b);
        a.type = "mousewheel";
        a.wheelDelta && (e = a.wheelDelta / 120);
        a.detail && (e = -a.detail / 3);
        h = e;
        b.axis !== void 0 && b.axis === b.HORIZONTAL_AXIS && (h = 0, i = -1 * e);
        b.wheelDeltaY !== void 0 && (h = b.wheelDeltaY / 120);
        b.wheelDeltaX !== void 0 && (i = -1 * b.wheelDeltaX / 120);
        d.unshift(a, e, i, h);
        return f.event.handle.apply(this, d)
    }
    var a = ["DOMMouseScroll", "mousewheel"];
    f.event.special.mousewheel = {
        setup: function () {
            if (this.addEventListener) for (var c = a.length; c;) this.addEventListener(a[--c], e, !1);
            else this.onmousewheel = e
        },
        teardown: function () {
            if (this.removeEventListener) for (var c = a.length; c;) this.removeEventListener(a[--c], e, !1);
            else this.onmousewheel = null
        }
    };
    f.fn.extend({
        mousewheel: function (a) {
            return a ? this.bind("mousewheel", a) : this.trigger("mousewheel")
        },
        unmousewheel: function (a) {
            return this.unbind("mousewheel", a)
        }
    })
})(jQuery);




(function (e) {
    function f(a) {
        var c = {}, b = /^jQuery\d+$/;
        e.each(a.attributes, function (a, d) {
            if (d.specified && !b.test(d.name)) c[d.name] = d.value
        });
        return c
    }
    function a() {
        var a = e(this);
        a.val() === a.attr("placeholder") && a.hasClass("placeholder") && (a.data("placeholder-password") ? a.hide().next().show().focus() : a.val("").removeClass("placeholder"))
    }
    function b() {
        var c, b = e(this);
        if (b.val() === "" || b.val() === b.attr("placeholder")) {
            if (b.is(":password")) {
                if (!b.data("placeholder-textinput")) {
                    try {
                        c = b.clone().attr({
                            type: "text"
                        })
                    } catch (d) {
                        c = e("<input>").attr(e.extend(f(b[0]), {
                            type: "text"
                        }))
                    }
                    c.removeAttr("name").data("placeholder-password", !0).bind("focus.placeholder", a);
                    b.data("placeholder-textinput", c).before(c)
                }
                b = b.hide().prev().show()
            }
            b.addClass("placeholder").val(b.attr("placeholder"))
        } else b.removeClass("placeholder")
    }
    var c = "placeholder" in document.createElement("input"),
        d = "placeholder" in document.createElement("textarea");
    e.fn.placeholder = c && d ? function () {
        return this
    } : function () {
        return this.filter((c ? "textarea" : ":input") + "[placeholder]").bind("focus.placeholder", a).bind("blur.placeholder", b).trigger("blur.placeholder").end()
    };
    e(function () {
        e("form").bind("submit.placeholder", function () {
            var c = e(".placeholder", this).each(a);
            setTimeout(function () {
                c.each(b)
            }, 10)
        })
    });
    e(window).bind("unload.placeholder", function () {
        e(".placeholder").val("")
    })
})(jQuery);


(function (d) {
    var e = function () {};
    d.extend(e.prototype, {
        name: "search",
        options: {
            url: document.location.href,
            param: "search",
            method: "post",
            minLength: 3,
            delay: 300,
			searchSubmitSelector: '#search-submit',
            match: ":not(li.skip)",
            skipClass: "skip",
            loadingClass: "searching",
            filledClass: "filled",
            resultClass: "result",
            resultsHeaderClass: "results-header",
            moreResultsClass: "more-results",
            noResultsClass: "no-results",
            listClass: "results",
            hoverClass: "selected",
            msgResultsHeader: "Search Results",
            msgMoreResults: "More Results",
            msgNoResults: "No results found"
        },
        initialize: function (b,
        a) {
            this.options = d.extend({}, this.options, a);
            var c = this;
            this.value = this.timer = null;
            this.form = b.parent("form:first");
            this.input = b;
            this.submitBtn = $(this.options.searchSubmitSelector);
            this.input.attr("autocomplete", "off");
            this.input.bind({
                keydown: function (a) {
                    c.form[c.input.val() ? "addClass" : "removeClass"](c.options.filledClass);
                    if (a && a.which && !a.shiftKey) switch (a.which) {
                    case 13:
                        c.done(c.selected);
                        a.preventDefault();
                        break;
                    case 38:
                        c.pick("prev");
                        a.preventDefault();
                        break;
                    case 40:
                        c.pick("next");
                        a.preventDefault();
                        break;
                    case 27:
                    case 9:
                        c.hide()
                    }
                },
                keyup: function () {
                    c.trigger()
                },
                blur: function (a) {
                    c.hide(a)
                }
            });
            this.form.find("button[type=reset]").bind("click", function () {
                c.form.removeClass(c.options.filledClass);
                c.value = null;
                c.input.focus()
            });
            this.choices = d("<ul>").addClass(this.options.listClass).hide().insertAfter(this.input)
        },
        request: function (b) {
            var a = this;
            this.form.addClass(this.options.loadingClass);
			this.submitBtn.addClass(this.options.loadingClass);
            d.ajax(d.extend({
                url: this.options.url,
                type: this.options.method,
                dataType: "json",
                success: function (b) {
                    a.form.removeClass(a.options.loadingClass);
					a.submitBtn.removeClass(a.options.loadingClass);
                    a.suggest(b)
                }
            }, b))
        },
        pick: function (b) {
            var a = null;
            typeof b !== "string" && !b.hasClass(this.options.skipClass) && (a = b);
            if (b == "next" || b == "prev") a = this.selected ? this.selected[b](this.options.match) : this.choices.children(this.options.match)[b == "next" ? "first" : "last"]();
            if (a != null && a.length) this.selected = a, this.choices.children().removeClass(this.options.hoverClass), this.selected.addClass(this.options.hoverClass)
        },
        done: function (b) {
            if (b) {
                if (b.hasClass(this.options.moreResultsClass)) this.input.parent("form").submit();
                else if (b.data("choice")) window.location = b.data("choice").url;
                this.hide()
            } else this.input.parent("form").submit()
        },
        trigger: function () {
            var b = this.value,
                a = this;
            this.value = this.input.val();
            if (this.value.length < this.options.minLength) return this.hide();
            if (this.value != b) this.timer && window.clearTimeout(this.timer), this.timer = window.setTimeout(function () {
                var b = {};
                b[a.options.param] = a.value;
                a.request({
                    data: b
                })
            }, this.options.delay, this)
        },
        suggest: function (b) {
            if (b) {
                var a = this,
                    c = {
                        mouseover: function () {
                            a.pick(d(this))
                        },
                        click: function () {
                            a.done(d(this))
                        }
                    };
                b === !1 ? this.hide() : (this.selected = null, this.choices.empty(), d("<li>").addClass(this.options.resultsHeaderClass + " " + this.options.skipClass).html(this.options.msgResultsHeader).appendTo(this.choices).bind(c), b.results && b.results.length > 0 ? (d(b.results).each(function () {
                    d("<li>").data("choice", this).addClass(a.options.resultClass).append(d("<h3>").html(this.title)).append(d("<div>").html(this.text)).appendTo(a.choices).bind(c)
                }), d("<li>").addClass(a.options.moreResultsClass + " " + a.options.skipClass).html(a.options.msgMoreResults).appendTo(a.choices).bind(c)) : d("<li>").addClass(this.options.resultClass + " " + this.options.noResultsClass + " " + this.options.skipClass).html(this.options.msgNoResults).appendTo(this.choices).bind(c), this.show())
            }
        },
        show: function () {
            if (!this.visible) this.visible = !0, this.choices.fadeIn(200)
        },
        hide: function () {
            if (this.visible) this.visible = !1, this.choices.removeClass(this.options.hoverClass).fadeOut(200)
        }
    });
    d.fn[e.prototype.name] = function () {
        var b = arguments,
            a = b[0] ? b[0] : null;
        return this.each(function () {
            var c = d(this);
            if (e.prototype[a] && c.data(e.prototype.name) && a != "initialize") c.data(e.prototype.name)[a].apply(c.data(e.prototype.name), Array.prototype.slice.call(b, 1));
            else if (!a || d.isPlainObject(a)) {
                var f = new e;
                e.prototype.initialize && f.initialize.apply(f, d.merge([c], b));
                c.data(e.prototype.name, f)
            } else d.error("Method " + a + " does not exist on jQuery." + e.name)
        })
    }
})(jQuery);

/*
$(document).ready(function(e) {
	$('fieldset.search-fieldset2 input#search-form').search({
		'url': $('fieldset.search-fieldset2 input#search-form').attr('ajax-url') + '?query=' + $('fieldset.search-fieldset2 input#search-form').val(),
		'param': 'query',
		'msgResultsHeader': 'Search Results',
		'msgMoreResults': 'More Results',
		'msgNoResults': 'No results found'
	}).placeholder();
});
*/
