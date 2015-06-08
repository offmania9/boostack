/**
 * Boostack: custom.js
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
$(document).ready(function(){

    $(window).resize(fixContentHeight());
    $(window).trigger('resize');
});
function fixContentHeight(){
    var documentHeight = window.document.height;
    var bodyHeight = $("body").height();
    var deltaHeight = documentHeight - bodyHeight;
    if(deltaHeight > 0) {
        var newHeight = $(".centerContent").height() + deltaHeight;
        $(".centerContent").css("min-height", newHeight + "px");
    }
}
function hidescreen(){
	  $('html, body').animate({ scrollTop: 0 }, 0);
	  $('html, body').css("overflow","hidden");
	  $(".overlay").fadeIn("slow");
	  $(".loading").fadeIn("slow");
}
function showscreen(){
	  $('html, body').css("overflow","auto");
		$(".alert").fadeOut("slow");
		$(".overlay").fadeOut("slow");
		$(".loading").fadeOut("slow");
}