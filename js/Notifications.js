(function($) {
    $.fn.showMsg = function(options) {

        "use strict";

        // Plugin options default values
        var settings = $.extend({
            bgColor: "rgba(42,45,50,0.6)",
            textColor: "rgba(250,251,255,0.95)",
            fontSize: "18px",
            msg: "",
        }, options);

        var msgHolder = '<div class="top-msg-ico">!</div><div class="top-msg-inner"><p>' + settings.msg + '</p></div><div class="top-msg-close" style="  cursor: pointer;">&#10005;</div>';


        if ($(".top-msg-ico").length > 0) {
            $('.top-msg-ico, .top-msg-inner, .top-msg-close').remove();
        }
        
        return this.each(function() {
            $(this).append(msgHolder);


            $(this).css({
                "color": settings.textColor,
                "background-color": settings.bgColor,
                "font-size": settings.fontSize
            });

            $(this).fadeIn(600).delay(5000).fadeOut(300, function() { $('.top-msg-ico, .top-msg-inner, .top-msg-close').remove()});
        });
    } // END showMsg

}(jQuery)); // END plugin
