/**
 * Created by Riccardo on 30/03/2017.
 */

$(document).ready(function(){
    if($(".setup tr.danger").size()>0){
        $(".setup .setupInstaller, .setup #initsetup-btn").hide();
    }
    var tooltip = $('[data-toggle="tooltip"]').tooltip();
    $(tooltip).on('show.bs.tooltip', function() {
        tooltip.not(this).tooltip("hide")
    });

    var loginStrategyItems = $("input[name^='db-loginLock-']").parents(".form-group");
    loginStrategyItems.hide();

    $("input[name$='db-active']").click(function() {
        var dbItems = $("input[name^='db-']").parents(".form-group:not('.noHide')");
        dbItems.push($("#btnCheckDB").closest(".form-group")[0]);
        dbItems.push($("#driver-pdo").closest(".form-group")[0]);
        if($(this).val()=="true"){
            dbItems.show();
            $("input[id$='db-session-true']").trigger("click");
            $("input[id$='db-cookie-true']").trigger("click");
            $("input[id$='db-log-true']").trigger("click");
            $("input[id$='lockStrategy-on-false']").trigger("click");
        }
        else{
            dbItems.hide();
            $("input[id$='db-session-false']").trigger("click");
            $("input[id$='db-cookie-false']").trigger("click");
            $("input[id$='db-log-false']").trigger("click");
            $("input[id$='lockStrategy-on-false']").trigger("click");
        }
    });
    $("input[name$='db-session-active']").click(function() {
        if($(this).val()=="false")
            $("input[id$='db-cookie-false']").trigger("click");
    });
    $("input[name$='db-cookie-active']").click(function() {
        var dbItems = $("input[name^='db-cookie']").parents(".form-group:not('.noHideCookie')");
        if($(this).val()=="true")
            dbItems.show();
        else
            dbItems.hide();
    });

    $("#lockStrategy-on-true").click(function (){
        loginStrategyItems.show();
        $("#recaptcha").trigger("click");
    });
    $("#lockStrategy-on-false").click(function (){
        loginStrategyItems.hide();
    });
    $("#recaptcha").click(function() {
        $("#recaptcha_public").closest(".form-group").show();
        $("#recaptcha_private").closest(".form-group").show();
        $("#timer_seconds").closest(".form-group").hide();
    });
    $("#timer").click(function() {
        $("#recaptcha_public").closest(".form-group").hide();
        $("#recaptcha_private").closest(".form-group").hide();
        $("#timer_seconds").closest(".form-group").show();
    })
});

function checkDB(){
    event.preventDefault();
    var data = {"host" : $("#db-host").val(),
        "driver_pdo" : $("#driver-pdo").val(),
        "dbname" : $("#db-name").val(),
        "username" : $("#db-username").val(),
        "password" : $("#db-password").val()};
    $.ajax({
        type: "POST",
        url: "dbTest.php",
        data: data,
        dataType: "json",
        cache: false,
        complete: function (response, status) {
            if(response.responseText=="success") {
                $("#dbStatus").text(" Success");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-ok");
            }
            else {
                $("#dbStatus").text(" Failure: " + response.responseText);
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-remove");
            }
        }
    })
}
