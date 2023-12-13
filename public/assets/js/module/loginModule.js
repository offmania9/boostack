define(['jquery','module/CSRFCheckManager'], function($,CSRFM) {
    var m = function() {

        var CSRFCheckManager = new CSRFM();

        function init(){
            CSRFCheckManager.init();
            $("#btn-login").click(function() {
                return login();
            });
            $("#btn-login-ajax").click(function() {
                return loginAjax();
            });

            
        }

        function login() {
            removeFormError();
            if($("#btk_usr").val().length == 0 || $("#btk_pwd").val().length == 0) {
                setFormError("Inserisci username e password");
                return false;
            }
            if(typeof captchaResult != 'undefined' && captchaResult == false) {
                setFormError("You must complete reCaptcha validation");
                return false;
            }
            CSRFCheckManager.addToForm($("#loginform"));
            $("#loginform").submit();
        }

        function loginAjax() {
            removeFormError();
            if($("#btk_usr-ajax").val().length == 0 || $("#btk_pwd-ajax").val().length == 0) {
                setFormErrorAjax("Inserisci username e password");
                return false;
            }
            CSRFCheckManager.addToForm($("#loginform-ajax"));

            var data = {};
            data.username = $("#btk_usr-ajax").val();
            data.password = $("#btk_pwd-ajax").val();
            data.rememberme = $("#login-remember-ajax").is(":checked");
            var dataToSend = JSON.stringify(data);
            var apiUrl = rootUrl+'api/login';

            $.ajax({
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                url: apiUrl,
                data: dataToSend,
                beforeSend: function(){
                    $("#btn-login-ajax").prop("disabled", true);
                },
                complete: function(){
                    $("#btn-login-ajax").prop("disabled", false);
                },
                success: function(response){
                    if(response != 0){
                        if(response.error == true)
                        $("#form-validation-error-ajax").text(response.data); 
                    else{
                        location.reload();
                    }
                    }else{
                        $("#form-validation-error-ajax").text("generic error");
                    }
                },
                error: function(e){
                    console.log(e);
                }
            });
        }

        function setFormError(message) {
            $("#form-validation-error").text(message).show();
        }
        function setFormErrorAjax(message) {
            $("#form-validation-error-ajax").text(message).show();
        }

        function removeFormError() {
            $("#form-validation-error").hide();
            $("#form-validation-error-ajax").hide();
        }

        return {
            init: init
        };
    };

    return m;
});