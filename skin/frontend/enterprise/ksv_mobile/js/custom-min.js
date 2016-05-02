var jQuery = jQuery.noConflict(); 
jQuery(document).ready(function(){
	var bag = jQuery("ul#headerbag li:nth-child(5)").html();
	if(bag!==undefined){
		if(bag.match(/\((.*?)\)/)){
			bag = bag.replace(/"/g, "").replace(/'/g, "").replace(/\(|\)/g, "");
			jQuery("ul#headerbag li:nth-child(5)").html(bag);
		}else{
			bag = bag.replace('><','>0<');
			jQuery("ul#headerbag li:nth-child(5)").html(bag);
		}
	}
});


jQuery(document).bind("mobileinit", function () {
    jQuery.mobile.ajaxEnabled = !1;
    jQuery.mobile.button.prototype.options.initSelector = ".jqm-button"
});

jQuery(document).delegate(".ui-navbar ul li > a", "click", function () {
    function r(e, t, n) {
        for (var r = t; r < n; r++) e.eq(r).addClass("hidden")
    }
    jQuery(this).closest(".ui-navbar").find("a").removeClass("ui-navbar-btn-active");
    jQuery(this).addClass("ui-navbar-btn-active");
    jQuery("#" + jQuery(this).attr("data-href")).css({
        height: "auto",
        visibility: "visible"
    }).siblings(".content_div").css({
        height: "0",
        visibility: "hidden"
    });
    var e = 3,
        t = 2;
    for (a = 1; a <= 100; a++) var n = a % 2 == 0 ? "even" : "odd";
    jQuery(".reviews-list").carouFredSel({
        direction: "up",
        circular: !1,
        infinite: !1,
        height: "variable",
        items: {
            visible: 1,
            height: "variable"
        },
        scroll: {
            duration: 600,
            fx: "fade"
        },
        auto: !1,
        prev: "#review_prev",
        next: "#review_next",
        pagination: "#pagination"
    });
    jQuery(".reviews-list").bind("updatePageStatus.cfs", function () {
        var n = jQuery("#pagination").children();
        n.removeClass("hidden").removeClass("ellipsis");
        var i = jQuery(this).children().length;
        console.log(i);
        var s = jQuery(this).triggerHandler("currentPosition");
        console.log(s);
        if (i > e + t * 2 + 2) {
            console.log("true1");
            if (s < Math.floor(e / 2) + t + 2) {
                var o = t + e + 1,
                    u = i - t - 1;
                r(n, o, u);
                n.eq(u).addClass("ellipsis");
                console.log(o);
                console.log(u)
            } else if (s > i - (Math.ceil(e / 2) + t) - 2) {
                var o = t + 1,
                    u = i - (t + e + 1);
                r(n, o, u);
                n.eq(o - 1).addClass("ellipsis");
                console.log("true-b")
            } else {
                var o = t + 1,
                    u = s - Math.floor(e / 2);
                r(n, o, u);
                n.eq(o - 1).addClass("ellipsis");
                var o = s + Math.ceil(e / 2),
                    u = i - t - 1;
                r(n, o, u);
                n.eq(u).addClass("ellipsis");
                console.log("true-c")
            }
        }
    }).trigger("updatePageStatus.cfs")
});
jQuery(document).ready(function () {
    jQuery(document).live("pagechange", function (e, t) {
        jQuery(document).ajaxSuccess(function () {
            alert("An individual AJAX call has completed successfully")
        });
        jQuery("#popupDialog").popup("open");
        jQuery("[placeholder]").focus(function () {
            var e = jQuery(this);
            if (e.val() === e.attr("placeholder")) {
                e.val("");
                e.removeClass("placeholder")
            }
        }).blur(function () {
            var e = jQuery(this);
            if (e.val() === "" || e.val() === e.attr("placeholder")) {
                e.addClass("placeholder");
                e.val(e.attr("placeholder"))
            }
        }).blur();
        jQuery("[placeholder]").parents("form").submit(function () {
            jQuery(this).find("[placeholder]").each(function () {
                var e = jQuery(this);
                e.val() === e.attr("placeholder") && e.val("")
            })
        });
        jQuery(".rating-links a").click(function () {
            target = jQuery("#product-collateral").offset().top;
            jQuery(".ui-block-b a").trigger("click");
            jQuery.mobile.silentScroll(target);
            return !1
        })
    })
});

jQuery(document).live("pageinit", function () {
    jQuery(".ui-collapsible[data-allow-collapse=false]").unbind("expand collapse");
    jQuery(".ui-collapsible-heading-toggle").bind("click", function () {
        var e = jQuery(this).find(".collapsible-header-link"),
            t = e.attr("href");
        e.length > 0 && jQuery.mobile.changePage(t)
    })
});

jQuery(".ui-page-active").live("pageshow", function (e) {
    function s() {
        if (jQuery("#searchHolder").css("display") === "none") {
            jQuery("#searchHolder").css("height", "auto").show();
            r = jQuery("#search_mini_form").height() + 21;
            i = r;
            t = n;
            document.getElementById("searchHolder").style.webkitTransitionProperty = "height";
            document.getElementById("searchHolder").style.webkitTransitionTimingFunction = "default";
            document.getElementById("searchHolder").style.webkitTransitionDuration = "0.5s";
			document.getElementById("searchHolder").style.transitionProperty = "height";
			document.getElementById("searchHolder").style.transitionTimingFunction = "default";
            document.getElementById("searchHolder").style.transitionDuration = "0.5s";
            document.getElementById("searchHolder").style.height = r + "px"
        } else {
            r = jQuery("#search_mini_form").height() + 21;
            t = r;
            jQuery("#searchHolder").show();
            document.getElementById("searchHolder").style.webkitTransitionProperty = "height";
            document.getElementById("searchHolder").style.webkitTransitionTimingFunction = "default";
            document.getElementById("searchHolder").style.webkitTransitionDuration = "0.5s";
			document.getElementById("searchHolder").style.transitionProperty = "height";
			document.getElementById("searchHolder").style.transitionTimingFunction = "default";
            document.getElementById("searchHolder").style.transitionDuration = "0.5s";
            document.getElementById("searchHolder").style.height = n + "px";
            document.getElementById("searchHolder").addEventListener("webkitTransitionEnd", o)
			document.getElementById("searchHolder").addEventListener("transitionend", o)
        }
    }

    function o(e) {
        jQuery("#searchHolder").css("display", "none");
        document.getElementById("searchHolder").removeEventListener("webkitTransitionEnd", o)
		document.getElementById("searchHolder").removeEventListener("transitionend", o)
    }

    function u() {
        if (jQuery("#loginHolder").css("display") === "none") {
            jQuery("#loginHolder").css("height", "auto").show();
            r = jQuery("#loginHolder form").height() + 21;
            i = r;
            t = n;
            document.getElementById("loginHolder").style.webkitTransitionProperty = "height";
            document.getElementById("loginHolder").style.webkitTransitionTimingFunction = "default";
            document.getElementById("loginHolder").style.webkitTransitionDuration = "0.5s";
			document.getElementById("loginHolder").style.transitionProperty = "height";
			document.getElementById("loginHolder").style.transitionTimingFunction = "default";
            document.getElementById("loginHolder").style.transitionDuration = "0.5s";
            document.getElementById("loginHolder").style.height = r + "px"
        } else {
            r = jQuery("#loginHolder form").height() + 21;
            t = r;
            jQuery("#loginHolder").show();
            document.getElementById("loginHolder").style.webkitTransitionProperty = "height";
            document.getElementById("loginHolder").style.webkitTransitionTimingFunction = "default";
            document.getElementById("loginHolder").style.webkitTransitionDuration = "0.5s";
			document.getElementById("loginHolder").style.transitionProperty = "height";
			document.getElementById("loginHolder").style.transitionTimingFunction = "default";
            document.getElementById("loginHolder").style.transitionDuration = "0.5s";
            document.getElementById("loginHolder").style.height = n + "px";
            document.getElementById("loginHolder").addEventListener("webkitTransitionEnd", a)
			document.getElementById("loginHolder").addEventListener("transitionend", a)
        }
    }

    function a(e) {
        jQuery("#loginHolder").css("display", "none");
        document.getElementById("loginHolder").removeEventListener("webkitTransitionEnd", o)
		document.getElementById("loginHolder").removeEventListener("transitionend", o)
    }
    jQuery(".ui-page-active #featured-slider").length !== 0 && (jQueryflexslider2 = jQuery(".ui-page-active #featured-slider").flexslider({
        animation: "slide",
        slideshow: !0,
        directionNav: !0,
        controlNav: !1
    }));
    jQuery(".ui-page-active .product-gallery").length !== 0 && (jQueryflexslider3 = jQuery(".ui-page-active .product-gallery").flexslider({
        animation: "slide",
        slideshow: !1,
        directionNav: !0,
        controlNav: !1
    }));
    var t = 0,
        n = 0,
        r = 0,
        i = 0;
    jQuery("#top-link-search").click(function () {
        s();
        return !1
    });
    jQuery(".top-link-login a").click(function () {
        u();
        return !1
    });
    jQuery(".login-link").click(function () {
        jQuery.mobile.silentScroll(0);
        u();
        return !1
    });
    var f = jQuery(".entry-content iframe[src^='http://www.youtube.com']"),
        l = jQuery("body");
    f.each(function () {
        jQuery(this).data("aspectRatio", this.height / this.width).removeAttr("height").removeAttr("width")
    });
    jQuery(window).resize(function () {
        var e = l.width() - 32;
        f.each(function () {
            var t = jQuery(this);
            t.width(e).height(e * t.data("aspectRatio"))
        })
    }).resize();
    jQuery("td.order").hide();
    jQuery(".subscription-view a").bind("click", function (t) {
        var n = jQuery(this).attr("href");
        jQuery("td.active").hide().removeClass("active");
        jQuery(n.slice(n.indexOf("#"))).show().addClass("active");
        e.preventDefault()
    })
});
jQuery("#katesomerville").live("pagebeforecreate", function (e) {
    var t = jQuery("#collapseMe");
    t.data("collapsed", !1);
    t.data("theme", "b")
});

//show the configure product price in the category page
/*
jQuery(document).ready(function() {
	jQuery('.catalog-category-view .minimal-price-link').each(function(){
		jQuery(this).hide();
		var low = jQuery(this).find('.price').text();
		var high = jQuery(this).prev().text();
		var e = low + " - " + high;
		jQuery(this).prev().text(e);
	});
});
*/

