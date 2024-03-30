define(['jquery'], function($) {
    var exampleModuleObject = function() {

        var brandURL="";

        function init(){
            console.log("Example Module");
            privateFunction();
        }

        function privateFunction(){
            console.log("this is a private function not callable from outside the obj")
        }

        // define public methods
        return {
            init: init
        };
    };
    
    return exampleModuleObject;
});