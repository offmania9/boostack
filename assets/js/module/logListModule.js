define(['jquery'], function($) {

    var logListModuleObj = function() {
        var maxFilters = 5;
        var filterNumber = 1;
        var globalCurrentPage;
        var globalOrderField;
        var currentOrderByField;
        var currentOrderByType;
        var countOrder;
        var url = "ajax/ajaxGetFilteredData.php";
        var ajaxCall;
        var listnameAction;
        var defaultParam;
        var i = 0;
        var jsonFilters = JSON.parse($("#filterJson").val());
        var rules = {
            "=": "è uguale",
            "like": "contiene",
            "not like": "non contiene",
            "<>": "è diverso",
            "<": "è minore",
            "<=": "è minore o uguale",
            ">": "è maggiore",
            ">=": "è maggiore o uguale"
        };

        function init(){
            listnameAction = "logList";
            countOrder=0;
            globalCurrentPage = 1;
            currentOrderByType= "DESC";
            currentOrderByField= "id";
            globalOrderField="id";
            ajaxCall = false;
            defaultParam = {
                field: "id",
                rule : ">=",
                input : '0'
            };
            renderFilterField();
            bindChangeForLimitQuery();
            bindFilterButton();
            bindClickForResetQuery();
            bindClickForOrderBy();
            bindClickForNextPage();
            bindFilterEnterButton();
            bindSubmitForm();
            bindDynamicFilter();
            deleteFilter();
        }

        function submitFilters(){
            var queryArray = [];
            $(".filterToClone").each(function(){
                var fieldOptionSelected = $(this).find(".form-control[id*='field']");
                var conditionOptionSelected = $(this).find(".form-control[id*='condition']");
                var valueAndInput = $(this).find(".form-control[id*='value']");
                var query = {};
                if(!(fieldOptionSelected.val() != undefined && fieldOptionSelected.val().trim() != "")){
                    query.field = defaultParam.field;
                }else{
                    query.field = fieldOptionSelected.val().trim();
                }
                if(!(conditionOptionSelected.val() != undefined && conditionOptionSelected.val().trim() != "")){
                    query.rule = defaultParam.rule;
                }else{
                    query.rule = conditionOptionSelected.val().trim();

                }
                if(!(valueAndInput.val() != undefined && valueAndInput.val().trim() != "")){
                    query.input = defaultParam.input;
                }else{
                    query.input = valueAndInput.val().trim();
                    if(valueAndInput.hasClass("hasDatepicker")) {
                        var timestampFromDate = new Date(query.input);
                        query.input = timestampFromDate.getTime()/1000;
                    }
                }
                queryArray.push(query);
            });
            var dataToSend = {};
            var sortingTable = $(".sortingTable");
            if (sortingTable.val() != "" || sortingTable.val() != null )
                dataToSend.perPage = $(".sortingTable").val();
            dataToSend.orderBy = currentOrderByField;
            dataToSend.orderType = currentOrderByType;
            dataToSend.currentPage = globalCurrentPage;
            dataToSend.filterPage = listnameAction;
            dataToSend.filters = queryArray;
            return dataToSend;
        }

        function addFilter(toClone){
            $(document).on("click", ".addFilter", function(){
                if(filterNumber < 5) {
                    $("#container_filter").append(toClone.clone());
                    var clonedElements = $(".filterToClone");
                    if ($(clonedElements).length > 1)
                        $(".deleteFilter").removeClass("hidden");
                    i++;
                    var lastFilter = $(clonedElements).last();
                    $(lastFilter).find("#field").attr("id", "field" + i);
                    $(lastFilter).find("#condition").attr("id", "condition" + i);
                    $(lastFilter).find("#value").attr("id", "value" + i);
                    filterNumber++;
                }
            })
        }

        function deleteFilter(){
            $(document).on("click", ".deleteFilter", function(){
                $(this).closest(".filterToClone").remove();
                filterNumber--;
                if($(".filterToClone").length == 1)
                    $(".deleteFilter").addClass("hidden")
            })
        }

        function bindSubmitForm(){
            $(document).on("click", ".btn-edit", function(){
                CSRFCheckManager.addToForm(".modifyAction");
            });
            $(document).on("submit", "#downloadMap", function(){
                CSRFCheckManager.addToForm("#downloadMap");
            });
        }

        function bindFilterEnterButton() {
            $(document).on("keypress", "[id^='value']", function( e ) {
                if ( e.which == 13 ) {
                    globalCurrentPage = 1;
                    $("#filterData").trigger("click");
                }
            });
        }

        function bindDynamicFilter(){
            $(document).on("change", ".selectField", function (e) {
                var fieldFormGroup = $(this).parent();
                var filter_name = $(this).val();
                $(fieldFormGroup).siblings(".input").find("input").val("");
                var select = $(fieldFormGroup).siblings(".rules").find(".select").empty();
                for(x in jsonFilters){
                    if(x == filter_name)
                        if(jsonFilters[x][0]["canFilter"]){
                            var optionRules = jsonFilters[x][0]["rule"].split(",");
                            for(y in optionRules)
                                $(select).append("<option value='"+optionRules[y]+"'>"+rules[optionRules[y]]+"</option>")
                            if(jsonFilters[x][0]["valueType"] == "select"){
                                var inputId = $(fieldFormGroup).siblings(".input").find("input").attr("id");
                                $(fieldFormGroup).siblings(".input").empty();
                                $(fieldFormGroup).siblings(".input").append("<select class='form-control select-xs " +
                                    "noSelectize select selectCondition' id='"+ inputId +"' name='value' ></select>");
                                var options = jsonFilters[x][0]["option"].split("/")
                                var optionsValue = jsonFilters[x][0]["optionValue"].split("/");
                                for(z in options)
                                    $(fieldFormGroup).siblings(".input").find("select").append("<option value='"+ optionsValue[z] +"'>"+options[z]+"</option>");
                            }
                            else if($(fieldFormGroup).siblings(".input").find("input").length == 0) {
                                var inputId = $(fieldFormGroup).siblings(".input").find("select").attr("id");
                                $(fieldFormGroup).siblings(".input").empty();
                                $(fieldFormGroup).siblings(".input").append("<input type='text' name='value' placeholder='Valore' class='form-control input-xs' id='"+inputId+"' >");
                            }
                            if(jsonFilters[x][0]["max_length"] != null)
                                $(fieldFormGroup).siblings(".input").find("input").attr("maxlength", jsonFilters[x][0]["max_length"]);
                            else
                                $(fieldFormGroup).siblings(".input").find("input").removeAttr("maxlength");
                        }
                }
            })
        }

        function bindFilterButton() {
            $(document).on("click", "#filterData", function (e) {
                ajaxGetFilteredData();
            });
        }

        function ajaxGetFilteredData() {
            $('.removeDuplicate').remove();
            if (ajaxCall)
                return false;
            else
                ajaxCall = true;

            var dataLimit;
            if ($(".sortingTable").val() != "" || $(".sortingTable").val() != null ){
                dataLimit = $(".sortingTable").val();
            }
            var dataToSend = submitFilters();
            dataToSend = CSRFCheckManager.concatToOjb(dataToSend);
            $.ajax({
                type: "POST",
                url: url,
                data: dataToSend,
                dataType: "json",
                cache:false,
                success: function(response){
                    if(!response.error){
                        renderTable(response);
                        renderPagination(response,dataLimit);
                    }else{
                        renderPagination(response,dataLimit);
                        $(".tableToPaste .noData").closest("td").removeClass("hidden");
                    }
                    ajaxCall = false;
                },
                error: function(response) {
                    ajaxCall = false;
                },
                failure: function(response){
                    ajaxCall = false;
                }
            });
        }

        function bindClickForResetQuery(){
            $(document).on("click", "#filterReset", function (e) {
                globalCurrentPage = 1;
                $(".input-xs").val("");
                $(".deleteFilter").trigger("click");
                $(".addFilter").trigger("click");
                ajaxGetFilteredData();
            });
        }

        function bindChangeForLimitQuery(){
            $(".sortingTable").on('change', function() {
                globalCurrentPage = 1;
                ajaxGetFilteredData();
            });
        }

        function bindClickForOrderBy() {
            $(".table th").not($(".noBindClick")).on('click', function() {
                calcParamForOrderby($(this));
                ajaxGetFilteredData();
            });
        }

        function bindClickForNextPage() {
            $(document).on("click", ".customPagination li", function (e) {
                var p = parseInt($(this).attr("page"));
                if (p!=globalCurrentPage) {
                    globalCurrentPage = p;
                    ajaxGetFilteredData();
                }
            });
            $(document).on("change",".customPaginationDrop", function(e){
                var p = parseInt($('option:selected', this).attr("page"));
                if (p!=globalCurrentPage) {
                    globalCurrentPage = p;
                    ajaxGetFilteredData();
                }

            })
        }

        function renderPagination (response,dataLimit){
            var i=1;
            var html = '';
            var tableInfo = '';
            var dropdown = '';

            if(response.error)
            {
                tableInfo += "0/0";
                html += '';
                dropdown += '';
            }
            else
            {
                var elemDataJson = response.data;
                if(elemDataJson.totalitem <=dataLimit)
                    tableInfo += ''+elemDataJson.totalitem + ' elementi su ' +elemDataJson.totalitem ;
                else
                    tableInfo += ''+dataLimit+ ' elementi su ' +elemDataJson.totalitem ;

                if(elemDataJson.totalitem > dataLimit)
                {
                    var maxPage = Math.floor(elemDataJson.totalitem / dataLimit) +1;
                    if (elemDataJson.totalitem % dataLimit==0)
                        maxPage--;
                    if(globalCurrentPage>1)
                    {
                        html += '<li page="'+(globalCurrentPage-1)+'"><span aria-hidden="true"> &laquo; </span></li>';
                    }
                    for (i; i<=maxPage; i++) {
                        if(i==globalCurrentPage)
                            dropdown += '<option page="'+i+'" selected><span aria-hidden="true">' +i+'</span></option>';
                        else
                            dropdown += '<option  page="'+i+'"><span aria-hidden="true">' +i+'</span></option>';
                    }
                    html += '<select class="customPaginationDrop noSelectize">'+dropdown+'</select>';
                    if (globalCurrentPage<maxPage)
                    {
                        html += '<li page="'+(globalCurrentPage+1)+'"><span aria-hidden="true"> &raquo; </span></li>';
                    }
                    else
                        html += '<li style="visibility:hidden"><span aria-hidden="true">  &raquo; </span></li>';

                }
            }
            $(".limitElem").html(tableInfo);
            $(".customPagination").html(html);
        }

        function calcParamForOrderby(obj){
            $("th .glyphicon").removeClass("glyphicon-sort-by-"+currentOrderByType.toLowerCase());

            currentOrderByField = obj.attr("fieldname");

            if (currentOrderByField!=globalOrderField )
            {
                countOrder=0;
                currentOrderByType = "DESC";
            }
            else
            {
                countOrder++;
                currentOrderByType = (countOrder%2) ? "ASC" : "DESC";
            }
            obj.find(".sortingOrder").addClass("glyphicon-sort-by-"+currentOrderByType.toLowerCase());

            globalOrderField = currentOrderByField;

        }

        function renderTable(response){
            if (response.data != null) {
                var elemDataJson = response.data.items;
                var form_data = null;
                if (elemDataJson.length == 0) {
                    $(".tableToPaste .noData").closest("td").removeClass("hidden");
                } else {
                    $(".tableToPaste .noData").closest("td").addClass("hidden");
                    $.each(elemDataJson, function () {
                        var elem = $('.tableToCopy').clone();
                        $(elem).removeClass('tableToCopy hidden');

                        $('.id',elem).html(this.id);
                        $('.level',elem).html(this.level);
                        $('.datetime',elem).html(this.datetime);
                        $('.ip',elem).html(this.ip);
                        $('.useragent',elem).html(this.useragent);
                        $('.referrer',elem).html(this.referrer);
                        $('.query',elem).html(this.query);
                        $('.message',elem).html(this.message);

                        $(elem).appendTo('.tableToPaste');
                        $(elem).addClass('removeDuplicate');
                    });
                }
            }else{
                $(".tableToPaste .noData").closest("td").removeClass("hidden");
            }

            ajaxCall = false;
        }

        function renderFilterField() {
            var target = $(".tableHeader th").not(".notFilter");
            var fieldname = null;
            var fieldnamefriendly = "Field" ;
            var filterType = null;
            $(document).ready(function () {
                $('#field').append('<option value="" selected>Field</option>');
                $.each(target, function () {
                    fieldname = $(this).attr('fieldname');
                    fieldnamefriendly = $(this).attr("title");
                    filterType = $(this).attr("filterType");
                    if (fieldname == '') {
                        $('#field').append('<option filterType="'+ filterType +'" class="'+fieldname+'" value="'+fieldname+'" hidden>'+fieldnamefriendly+'</option>');
                    } else {
                        $('#field').append('<option filterType="'+ filterType +'" class="'+fieldname+'" value="'+fieldname+'">'+fieldnamefriendly+'</option>');
                    }
                });
                var toClone = $(".filterToClone").clone();
                addFilter(toClone);
                initializeFilterFromSession($("#log_filter"));
            });
        }

        function initializeFilterFromSession(element) {
            var sessionFilters = JSON.parse($(element).val());
            if(sessionFilters == "") {
                ajaxGetFilteredData();
                return false;
            }
            var filterName = null;
            var filterCondition = null;
            var filterValue = null;

            for(var i=1; i < sessionFilters.fields.length; i++) {
                $(".addFilter").trigger("click");
            }

            var fields = $("#container_filter select[name='field']");
            var conditions = $("#container_filter select[name='condition']");
            var values = $(".input").children();

            $("#sortingTable option[value='"+sessionFilters.perPage+"']").attr("selected","selected");
            globalCurrentPage = parseInt(sessionFilters.currentPage);
            globalOrderField = sessionFilters.orderBy;
            if($(".tableHeader th[fieldname='"+sessionFilters.orderBy+"']").length) {
                countOrder = 0;
                if(sessionFilters.orderType == "DESC") countOrder = 1;
                calcParamForOrderby($(".tableHeader th[fieldname='"+sessionFilters.orderBy+"']"));
            }
            for(var i=0; i<sessionFilters.fields.length; i++) {
                filterName = sessionFilters.fields[i][0];
                filterCondition = sessionFilters.fields[i][1];
                filterValue = sessionFilters.fields[i][2];
                if(filterName == defaultParam.field && filterCondition == defaultParam.rule && filterValue == defaultParam.input) continue;
                $(fields[i]).val(filterName).trigger("change");
                $(conditions[i]).val(filterCondition).trigger("change");
                $(values[i]).val(filterValue);
            }
            ajaxGetFilteredData()
        }

        return {
            init: init
        };
    };

    return logListModuleObj;
});