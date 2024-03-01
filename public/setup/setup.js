$(document).ready(function () {


    function updateURL() {
        var protocol = $('#protocol').val();
        var dn = $('#dn').val();
        var port = $('#port').val();
        var rootpath = $('#rootpath').val();

        if($.isNumeric(port) && Math.floor(port) == port) {
            var url = protocol+'://'+ dn + ':' + port + rootpath;
            $('#url').val(url);
        } else {
            alert('Il valore del campo Port deve essere un intero.');
            $('#url').val('');
            $('#port').val('');
        }

    }

    $('#port, #protocol, #dn, #rootpath').on('change input', function() {
        updateURL();
    });


    if ($(".setup tr.bg-danger").length > 0) {
        $(".setup .setupInstaller, .setup #initsetup-btn").hide();
    }
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

$("form").submit(function (e) {
    e.preventDefault();  // Previene sempre il submit automatico del form

    if ($("#db-true").is(":checked")) {
        // Passa una funzione callback che invia il form se il controllo DB è ok
        checkDB(function(db_ok) {
            if (db_ok){
                $(e.target).unbind('submit').submit();
            } else {
                scrollToDiv("btnCheckDB");
            }
        });
    } else {
        $(this).unbind('submit').submit();  // Se db-true non è selezionato, procedi con il submit
    }
});

function checkDB(callback) {
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

                if(typeof callback === 'function') {
                    callback(true);
                }
            }
            else {
                $("#dbStatus").text(" Failure: " + response.responseText);
                $("#dbStatus").attr("class", "text-danger");
                $("#dbStatusIcon").attr("class", "glyphicon glyphicon-remove");

                if(typeof callback === 'function') {
                    callback(false);
                }
            }
        }
    });
}

function scrollToDiv(divID) {
    $('html, body').scrollTop($(document).height() - $('#' + divID).offset().top);
}