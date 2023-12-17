$(document).ready(function () {
    if ($(".setup tr.bg-danger").length > 0) {
        $(".setup .setupInstaller, .setup #initsetup-btn").hide();
    }

    $("form").submit(function () {
        if ($("#db-true").is(":checked")) {
            var db_ok = checkDB();
            if (db_ok)
                return true;
            else {
                scrollToDiv("btnCheckDB");
                return false;
            }
        }
        return true;
    });

    var loginStrategyItems = $("input[name^='db-loginLock-']").parents(".form-group");
    loginStrategyItems.hide();

    $("input[name$='db-active']").click(function () {
        var dbItems = $("[rel='db-active']");
        if ($(this).val() == "true") {
            dbItems.show();
            $("input[id$='db-session-true']").trigger("click");
            $("input[id$='db-cookie-true']").trigger("click");
            $("input[id$='db-log-true']").trigger("click");
            $("input[id$='lockStrategy-on-false']").trigger("click");
        }
        else {
            dbItems.hide();
            $("input[id$='db-session-false']").trigger("click");
            $("input[id$='db-cookie-false']").trigger("click");
            $("input[id$='db-log-false']").trigger("click");
            $("input[id$='lockStrategy-on-false']").trigger("click");
        }

    });
    $("input[name$='db-session-active']").click(function () {
        if ($(this).val() == "false")
            $("input[id$='db-cookie-false']").trigger("click");
    });
    $("input[name$='db-cookie-active']").click(function () {
        var dbItems = $("input[name^='db-cookie']").parents(".form-group:not('.noHideCookie')");
        if ($(this).val() == "true") {
            $("input[id$='db-session-true']").trigger("click");
            dbItems.show();
        }
        else
            dbItems.hide();
    });

    $("#lockStrategy-on-true").click(function () {
        loginStrategyItems.show();
        $("#recaptcha").trigger("click");
    });
    $("#lockStrategy-on-false").click(function () {
        loginStrategyItems.hide();
    });
    $("#recaptcha").click(function () {
        $("#recaptcha_public").closest(".form-group").show();
        $("#recaptcha_private").closest(".form-group").show();
        $("#timer_seconds").closest(".form-group").hide();
    });
    $("#timer").click(function () {
        $("#recaptcha_public").closest(".form-group").hide();
        $("#recaptcha_private").closest(".form-group").hide();
        $("#timer_seconds").closest(".form-group").show();
    })
});

function checkDB() {
    var data = {
        "host": $("#db-host").val(),
        "driver_pdo": $("#driver-pdo").val(),
        "dbname": $("#db-name").val(),
        "port": $("#db-port").val(),
        "username": $("#db-username").val(),
        "password": $("#db-password").val()
    };
    $.ajax({
        type: "POST",
        url: "dbTest.php",
        data: data,
        dataType: "json",
        cache: false,
        complete: function (response, status) {
            if (response.responseText == "success") {
                $("#dbStatus").text(" Success");
                $("#dbStatus").attr("class", "text-success");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-ok");

                return true;
            }
            else {
                $("#dbStatus").text(" Failure: " + response.responseText);
                $("#dbStatus").attr("class", "text-danger");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-remove");
                return false;
            }
        }
    })
}

function scrollToDiv(divID) {
    $('html, body').scrollTop($(document).height() - $('#' + divID).offset().top);
}