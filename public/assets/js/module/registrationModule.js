define(['jquery','module/CSRFCheckManager'], function($,CSRFM) {
    var m = function() {

        var CSRFCheckManager = new CSRFM();

        function init(){
            CSRFCheckManager.init();
            $("#registration-form").submit(function() {
                return registration();
            });

            $("#btn-registration-ajax").click(function() {
                return registrationAjax();
            });
        }

        function registration() {
            removeFormError();
            if($("#reg-email").val().length == 0 || $("#reg-pwd1").val().length == 0 || $("#reg-pwd2").val().length == 0) {
                setFormError("You must insert username and password");
                return false;
            }
            if($("#reg-pwd1").val() != $("#reg-pwd2").val()) {
                setFormError("Passwords must be equals");
                return false;
            }
            CSRFCheckManager.addToForm($("#registration-form"));
            return true;
        }

        function registrationAjax() {
            removeFormError();
            if($("#reg-email-ajax").val().length == 0 || $("#reg-pwd-ajax").val().length == 0 || $("#reg-pwd2-ajax").val().length == 0) {
                setFormErrorAjax("You must insert email and password");
                return false;
            }
            if($("#reg-pwd-ajax").val() != $("#reg-pwd2-ajax").val()) {
                setFormErrorAjax("Passwords must be equals");
                return false;
            }
            if(!$("#register-agree").is(":checked")) {
                setFormErrorAjax("you must Agree to terms and conditions");
                return false;
            }

            CSRFCheckManager.addToForm($("#registration-form-ajax"));

            var data = {};
            data.password = $("#reg-pwd-ajax").val();
            data.password_confirm = $("#reg-pwd2-ajax").val();
            data.email = $("#reg-email-ajax").val();
            data.agree = $("#register-agree").is(":checked");
            data.BCSRFT = $("#BCSRFT").val();
            var dataToSend = JSON.stringify(data);
            var apiUrl = rootUrl+'api/registrationBasic';

            $.ajax({
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                url: apiUrl,
                data: dataToSend,
                beforeSend: function(){
                    $("#btn-registration-ajax").prop("disabled", true);
                },
                complete: function(){
                    $("#btn-registration-ajax").prop("disabled", false);
                },
                success: function(response){
                    if(response != 0){
                        if(response.error == true)
                            $("#registration-alert").text(response.data).show(); 
                    else{
                        location.reload();
                    }
                    }else{
                        $("#registration-alert").text("generic error").show();
                    }
                },
                error: function(e){
                    console.log(e);
                    if (typeof e.responseJSON.message !== 'undefined')
                        $("#registration-alert").text(e.responseJSON.message).show();
                    else
                        $("#registration-alert").text("Attention! generic error").show();
                }
            });
        }


        function setFormError(message) {
            $("#form-validation-error").text(message).show();
        }
        function setFormErrorAjax(message) {
            $("#registration-alert").text(message).show();
        }
        

        function removeFormError() {
            $("#form-validation-error").hide();
            $("#registration-alert").hide();
        }

        return {
            init: init
        };
    };

    return m;
});