/**
 * Boostack: custom.js
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
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

function checkDB(){
    event.preventDefault();
    var data = {"host" : $("#db-host").val(),
                "driver_pdo" : $("#driver-pdo").val(),
                "dbname" : $("#db-name").val(),
                "username" : $("#db-username").val(),
                "password" : $("#db-password").val()};
    $.ajax({
        type: "POST",
        url: "/setup/dbTest.php",
        data: data,
        dataType: "json",
        cache: false,
        complete: function (response, status) {
            if(response.responseText=="success") {
                $("#dbStatus").text(" Success");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-ok");
            }
            else {
                $("#dbStatus").text(" Failure");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-remove");
            }
        }
    })
}