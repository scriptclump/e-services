$(document).ready(function () {

    // Custom Date Formats
    $('#customDatePickerZone .input-daterange').datepicker({
        format: "mm/yyyy",
        viewMode: "months",
        minViewMode: "months"
    });

    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        dataType: 'JSON'
    });

    var load_url = (window.location.pathname == "/cnc")?"/cnc/":"/";

    $(function (){              
        getSalesTargetTab();        
    });


    $('#dashboard_filter_dates').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads 
        var filterData = $(this).val();
        var whid=$("#wh_id").val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            getSalesTargetTab(filterData,0,0,whid);
            loadDashboardData(filterData,0,0,whid);
        }else{
            $("#customDatesView").removeClass("customDateArea");
        }
    });

    $('#customDateWidthSubmit').click(function () {
        var fromDate = $('#fromDate').val();
        fromDate = fromDate;
         var whid=$("#wh_id").val();
        if(fromDate == undefined || fromDate == ''){
            alert("Please Select Valid From Date");
            $("#fromDate").val('');
        }
        else{
            fromDateCheck = new Date(fromDate);
            loadDashboardData("custom",fromDate,whid);
            getSalesTargetTab();
        }
    });

    function loadDashboardData(filterData,fromDate,whid) {

        $('[class="loader"]').show();
        $('[class="data_value"]').text(0);

        var inputData = {'filter_date': filterData, 'fromDate': fromDate, 'toDate': fromDate, 'wh_id':whid};
        var response = $.post("/salestarget",inputData);
        response.done(function (data){
            var mainGridData = {};
            mainGridData = data.order_details;

            $.each(mainGridData, function (key, value) {
                var test = key.toLowerCase();
                var temp = test.replace(/[^A-Z0-9]/ig, "_");
                if (temp == "dashboard")
                {        
                    $.each(value, function (key2, dashboard) {
                        $.each(dashboard, function (key3, dashboardData) {  
                           var key3 = dashboardData.key;
                           var val3 = dashboardData.val;
                           var per3 = dashboardData.per;
                           if(key3 != null){
                               var test3 = key3.toString().toLowerCase();
                               var temp3 = test3.replace(/[^A-Z0-9]/ig, "_");
                               $('#' + temp3).text(val3);
                               $('#data_per_' + temp3).text(per3);
                           }
                        });
                    });
                }
            });
        });

        // The below 2 Lines, hide all the tab Headings, expect the FF tab
        $('[class="loader"]').hide();
    }

    function getInputDates(){
        return {
            'wh_id':$("#wh_id").val(),
            'legal_entity_id':$("#legal_entity_id").val(),
            'filter_date': $("#dashboard_filter_dates").val(),
            'toDate': $("#toDate").val(),
            'fromDate': $("#fromDate").val()
        };
    }

    // All the general Features of all the Grids are written here
    function customigGridFeatures(customPageSize){
        return [
                    {
                        name: 'Paging',
                        type: 'local',
                        pageSize: customPageSize,
                    },
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "simple",
                        filterDialogContainment: "window",
                    },
                    {
                        name: 'Sorting',
                        type: 'local',
                        persist: false,
                    },
                    {
                        name: "Resizing",
                    },
                    {
                        name: "RowSelectors",
                    },
                    {
                        name: "Selection",
                        multipleSelection: true,
                    },
                    {
                        name: "ColumnFixing",
                    },
                    {
                        name: "Tooltips",
                        visibility: "always",
                        showDelay: 500,
                        hideDelay: 500,
                    }
                ];
    }


    function getSalesTargetTab()
    {	
        $.ajax({
            url: load_url+'getsalestarget',
            type: 'POST',
            data: getInputDates(),
            dataType:"json",                                          
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                if(response.data.length > 0){
                    $('#sales_target_error').addClass("hideError");
                    $('#sales_target_table').css("display","block");
                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: result.columnSummaries
                    });
                    $('#sales_target').igGrid({

                    dataSource: response.data,
                    autoGenerateColumns: false,
                    width:"100%",
                    columns: result.columnHeaders,
                    features: customFeatures
                    });
                }
                else{
                    $('#sales_target_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#sales_target_table')
                        .css("display","none");
                }
            },
            error: function() {
                $('#sales_target_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>Sales Target</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#sales_target_table')
                    .css("display","none");
            }
        });
    }


     // All the general Columns of all the Grids are written here
    // Summaries are also set here
    function customigGridColumns(headers) {

        var columnHeaders = [];
        var columnSummaries = [];

        for (var i = 0; i < headers.length; i++) {
            var headerDataType = "string";
            var cssClass = null;
            var customWidth = "130px";
            var customHeadText = headers[i];
            
            if (headers[i].substring(0, 2) === "1_") {
                headerDataType = "number";
                cssClass = "alignRight";
                customWidth = "60px";

                var summaryType = (headers[i].substring(headers[i].length - 4) == "_Per")?"AVG":"SUM";
                // Summaries Cols
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: true,
                    summaryOperands: [{
                        "rowDisplayLabel": "",
                        "type": summaryType,
                        "active": true
                    }]
                });
            } else {
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: false
                });
            }

            if (headers[i].substring(0, 4) === "1_N_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    },
                    width: "auto",
                });
            } else if(headers[i].substring(0, 2) === "D_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: "date",
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "date", "dd/MM/yyyy");
                    },
                    width: "auto",
                });
            } else {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    width: "auto",
                });
            }
        }

        return {
            "columnHeaders": columnHeaders,
            "columnSummaries": columnSummaries
        };
    }

    $('[class="loader"]').hide();
});