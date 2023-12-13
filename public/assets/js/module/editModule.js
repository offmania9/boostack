define(['jquery', 'module/CSRFCheckManager'], function ($, CSRFM) {
    var m = function () {

        var CSRFCheckManager = new CSRFM();

        function init() {
            CSRFCheckManager.init();
        }

        $('.auto-checked').on('change paste keyup', function () {
            var parentchackboxID = $(this).attr('id') + "-visible"
            if ($(this).val().length == 0) {
                $("#" + parentchackboxID).prop('checked', false);
                $(this).removeClass("bg-white").addClass("bg-light")
            }
            else {
                $("#" + parentchackboxID).prop('checked', true);
                $(this).removeClass("bg-light").addClass("bg-white")
            }
        })

        $('.edit-form input:checkbox').on('change', function () { 
            var f = $("#" + $(this).attr("for")+"-"+ $(this).attr("for"));
            if (!$(this).prop("checked"))
                f.removeClass("bg-white").addClass("bg-light");
            else
                f.removeClass("bg-light").addClass("bg-white");
        })
        return {
            init: init
        };
    };

    return m;
});

