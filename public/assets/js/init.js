var exampleModuleObject = null;
var CSRFCheckManager = null;
var cookieMessageModule = null;
var loginModule = null;
var registrationModule = null;
var documentationModule = null;
var logListModule = null;
var uploadProfilePicModule = null;
var editModule = null;


function getElementsByClassName(className, tag) {
    var testClass = new RegExp("(^|\\s)" + className + "(\\s|$)"),
        tag = tag || "*",
        elm = document,
        elements = elm.getElementsByTagName(tag),
        returnElements = [],
        current,
        length = elements.length;
    for (var i = 0; i < length; i++) {
        current = elements[i];
        if (testClass.test(current.className))
            returnElements.push(current);
    }
    return returnElements;
}

function getElementsByAttribute(attrName) {
    var arr_elms = document.body.getElementsByTagName("*"),
        elms_len = arr_elms.length,
        returnElements = [];

    for (var i = 0; i < elms_len; i++) {
        if (arr_elms[i].getAttribute(attrName) != null) {
            returnElements.push(arr_elms[i]);
            return returnElements;
        }
    }
    return returnElements;
}

function getQuerystring(key, default_) {
    if (default_ == null) default_ = "";
    key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + key + "=([^&#]*)", 'i'),
        qs = regex.exec(window.location.href);
    if (qs == null)
        return default_;
    return decodeURIComponent(qs[1]);
}

function getElementByID(id) {
    return document.getElementById(id);
}

var initLibrary = function () {

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

};

initLibrary();
