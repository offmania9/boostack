var exampleModuleObject = null;
var CSRFCheckManager = null;
var cookieMessageModule = null;

var initLibrary = function() {

    // Create your own modules in "/module" directory, then call them here

    //if (getElementsByClassName("QuestionListInit").length) {
        require(["module/exampleModule"], function (object) {
            if (exampleModuleObject != null) return;
            exampleModuleObject = new object();
            exampleModuleObject.init();
        });
    //}

    require(["module/cookieMessageModule"], function (object) {
        if (cookieMessageModule != null) return;
        cookieMessageModule = new object();
        cookieMessageModule.init();
    });

    if (getElementsByClassName("CSRFcheck").length) {
        require(["module/CSRFCheckManager"], function (ccm) {
            if (CSRFCheckManager != null) return;
            CSRFCheckManager = new ccm();
            CSRFCheckManager.init();
        });
    }

};

initLibrary();
