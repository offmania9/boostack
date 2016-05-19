var exampleModuleObject = null;

var initLibrary = function() {

    // Create your own modules in "/module" directory, then call them here

    //if (getElementsByClassName("QuestionListInit").length) {
        require(["module/exampleModule"], function (object) {
            if (exampleModuleObject != null) return;
            exampleModuleObject = new object();
            exampleModuleObject.init();
        });
    //}

};

initLibrary();
