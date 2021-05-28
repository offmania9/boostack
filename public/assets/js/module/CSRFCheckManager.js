define(['jquery'], function($,ccm) {
    var o = function() {
        
        var CSRFToken = null;

        function init(){
            CSRFToken = $("#BCSRFT").val();
        }

        function concatToOjb(obj){
            if(CSRFToken!=null && CSRFToken!='' && CSRFToken!='undefined')
                obj.BCSRFT = CSRFToken;
            return obj;
        }
        function concatToArray(arr){
            if(CSRFToken!=null && CSRFToken!='' && CSRFToken!='undefined')
                arr['BCSRFT'] = CSRFToken;
            return arr;
        }
        function addToForm(form){
            if(CSRFToken!=null && CSRFToken!='' && CSRFToken!='undefined')
                $('<input>').attr({
                    type: 'hidden',
                    id: 'BCSRFT',
                    name: 'BCSRFT',
                    value: CSRFToken,
                }).appendTo(form);
            return true;
        }
        return {
            init: init,
            concatToOjb:concatToOjb,
            concatToArray:concatToOjb,
            addToForm:addToForm
        };
    };
    return o;
});
