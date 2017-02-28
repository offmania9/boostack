define(['jquery','module/CSRFCheckManager'], function($,CSRFM) {
    var m = function() {

        var CSRFCheckManager = new CSRFM();

        function init(){
            CSRFCheckManager.init();
            $("#registration-form").submit(function() {
                return registration();
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