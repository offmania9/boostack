var exampleModuleObject = null;
var CSRFCheckManager = null;
var cookieMessageModule = null;
var loginModule = null;
var registrationModule = null;
var documentationModule = null;
var logListModule = null;
var uploadProfilePicModule = null;
var editModule = null;

var initLibrary = function() {

    /** 
    require(["module/cookieMessageModule"], function (object) {
        if (cookieMessageModule != null) return;
        cookieMessageModule = new object();
        cookieMessageModule.init();
    });
    */
    if (getElementsByClassName("CSRFcheck").length) {
        require(["module/CSRFCheckManager"], function (ccm) {
            if (CSRFCheckManager != null) return;
            CSRFCheckManager = new ccm();
            CSRFCheckManager.init();
        });
    }

    if (getElementsByClassName("login").length) {
        require(["module/loginModule"], function (m) {
            if (loginModule != null) return;
            loginModule = new m();
            loginModule.init();
        });
    }

    if (getElementsByClassName("registration").length) {
        require(["module/registrationModule"], function (m) {
            if (registrationModule != null) return;
            registrationModule = new m();
            registrationModule.init();
        });
    }


    if (getElementsByClassName("logList").length) {
        require(["module/logListModule"], function (m) {
            if (logListModule != null) return;
            logListModule = new m();
            logListModule.init();
        });
    }

    if (getElementsByClassName("editSection").length) {
        require(["module/uploadProfilePicModule"], function (m) {
            if (uploadProfilePicModule != null) return;
            uploadProfilePicModule = new m();
            uploadProfilePicModule.init();
        });
    }
    if (getElementsByClassName("editSection").length) {
        require(["module/editModule"], function (m) {
            if (editModule != null) return;
            editModule = new m();
            editModule.init();
        });
    }

};

initLibrary();