define(['jquery', 'module/CSRFCheckManager'], function ($, CSRFM) {
    var m = function () {

        var CSRFCheckManager = new CSRFM();

        function init() {
            CSRFCheckManager.init();
            $("#registration-form").submit(function () {
                return registration();
            });

            $("#btn-registration-ajax").click(function () {
                return registrationAjax();
            });
        }

        function registration() {
            removeFormError();
            if ($("#reg-email").val().length == 0 || $("#reg-pwd1").val().length == 0 || $("#reg-pwd2").val().length == 0) {
                setFormError("You must insert username and password");
                return false;
            }
            if ($("#reg-pwd1").val() != $("#reg-pwd2").val()) {
                setFormError("Passwords must be equals");
                return false;
            }
            CSRFCheckManager.addToForm($("#registration-form"));
            return true;
        }

        function registrationAjax() {
            removeFormError();
            /*
            if($("#reg-email-ajax").val().length == 0 || $("#reg-pwd-ajax").val().length == 0 || $("#reg-pwd2-ajax").val().length == 0) {
                setFormErrorAjax("You must insert email and password");
                return false;
            }
             */
            // if ($("#reg-first_name-ajax").val().length == 0) {
            //     setFormErrorAjax("You must insert First name");
            //     return false;
            // }
            // if ($("#reg-last_name-ajax").val().length == 0) {
            //     setFormErrorAjax("You must insert Last name");
            //     return false;
            // }
            if ($("#reg-email-ajax").val().length == 0 || !isEmail($("#reg-email-ajax").val())) {
                setFormErrorAjax("You must insert a valid e-mail address");
                return false;
            }
            if ($("#reg-pwd-ajax").val().length == 0) {
                setFormErrorAjax("You must insert password");
                return false;
            }
            if ($("#reg-pwd-ajax").val().length < 7) {
                setFormErrorAjax("Password too short. Use 8 characters or longer.");
                return false;
            }
            if (!checkPasswordStrength($("#reg-pwd-ajax").val())) {
                setFormErrorAjax("Password must contain both lower and uppercase characters. Password must contain at least one number and one special character");
                return false;
            }


            /*
            if($("#reg-pwd-ajax").val() != $("#reg-pwd2-ajax").val()) {
                setFormErrorAjax("Passwords must be equals");
                return false;
            }
             */
            if (!$("#register-agree").is(":checked")) {
                setFormErrorAjax("you must Agree to terms and conditions");
                return false;
            }

            function isEmail(email) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            }

            function checkPasswordStrength(password) {
                var number = /([0-9])/;
                var alphabets = /([a-z].*[A-Z])|([A-Z].*[a-z])/;
                var special_characters = /([~,!,@,#,$,%,^,&,*,-,-,_,+,=,?,>,<])/;

                return (password.match(number) && password.match(alphabets) && password.match(special_characters))
            }

            CSRFCheckManager.addToForm($("#registration-form-ajax"));

            var data = {};
            data.password = $("#reg-pwd-ajax").val();
            //data.password_confirm = $("#reg-pwd2-ajax").val();
            data.password_confirm = $("#reg-pwd-ajax").val();
            data.email = $("#reg-email-ajax").val();
            // data.first_name = $("#reg-first_name-ajax").val();
            // data.last_name = $("#reg-last_name-ajax").val();
            data.agree = $("#register-agree").is(":checked");
            data.BCSRFT = $("#BCSRFT").val();
            var dataToSend = JSON.stringify(data);
            var apiUrl = rootUrl + 'api/registrationFirstStep';

            $.ajax({
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                url: apiUrl,
                data: dataToSend,
                beforeSend: function () {
                    $("#btn-registration-ajax").prop("disabled", true);
                },
                complete: function () {
                    $("#btn-registration-ajax").prop("disabled", false);
                },
                success: function (response) {
                    if (response != 0) {
                        if (response.error == true)
                            $("#registration-alert").text(response.data).show();
                        else {
                            location.reload();
                        }
                    } else {
                        $("#registration-alert").text("generic error").show();
                    }
                },
                error: function (e) {
                    console.log(e);
                    if (typeof e.responseJSON.message !== 'undefined')
                        $("#registration-alert").text(e.responseJSON.message + ":" + e.responseJSON.data).show();
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


        function checkStrength(password) {
            // If password contains both lower and uppercase characters, increase strength value.  
            if (!password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) return false;
            // If it has numbers and characters, increase strength value.  
            if (!password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) return false;
            // If it has one special character, increase strength value.  
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) return false;
            // If it has two special characters, increase strength value.  
            //if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1  
            return true;
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
