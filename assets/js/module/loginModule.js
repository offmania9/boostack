define(['jquery','module/CSRFCheckManager'], function($,CSRFM) {
    var m = function() {

        var CSRFCheckManager = new CSRFM();

        function init(){
            CSRFCheckManager.init();
            $("#btn-login").click(function() {
                return login();
            });
        }

        function login() {
            removeFormError();
            if(typeof captchaResult != 'undefined' && captchaResult == false) {
                setFormError("You must complete reCaptcha validation");
                return false;
            }
            CSRFCheckManager.addToForm($("#loginform"));
            $("#loginform").submit();
        }

        function setFormError(message) {
            $("#form-validation-error").html(message).show();
        }

        function removeFormError() {
            $("#form-validation-error").hide();
        }

        return {
            init: init
        };
    };

    return m;
});