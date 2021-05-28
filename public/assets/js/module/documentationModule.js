define(['jquery','highlight'], function($,highlight) {
    var obj = function() {

        function init(){
            $(document).ready(function() {
                $('pre code').each(function(i, block) {
                    highlight.highlightBlock(block);
                });
            });
        }

        return {
            init: init
        };
    };

    return obj;
});