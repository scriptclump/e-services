$(document).ready(function () {

    // Custom Date Formats
    $('#customDatePickerZone .input-daterange').datepicker({
        format: "dd/mm/yyyy",
        endDate: "today",
        todayHighlight: true
    });

    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        dataType: 'JSON'
    });
        


    $('#dashboard_filter_dates').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads 
        var filterData = $(this).val();
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        if($('#primary_secondary_sales').length){
            var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            $('#filter_date').val(filterData);
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
        }
    });

    function getInputDates(){
        if($('#primary_secondary_sales').length){
            var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        return {
            'filter_date': $("#dashboard_filter_dates").val(),
            'toDate': $("#toDate").val(),
            'fromDate': $("#fromDate").val(),
            'sales_type':primary_secondary_sales,
            'buid' :$('#dc_all_legalentity').val()
        };
    }



     $('#customDateWidthSubmit').click(function () {
        var toDate = $('#toDate').val();
        var fromDate = $('#fromDate').val();
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        if($('#primary_secondary_sales').length){
            var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        if((toDate == undefined || toDate == '') || (fromDate == undefined || fromDate == '')){
            alert("Please Select Valid To & From Dates");
            $("#fromDate, #toDate").val('');
        }
        else{
            toDateCheck = new Date(toDate);
            fromDateCheck = new Date(fromDate);
            if(fromDateCheck>toDateCheck){
                alert("Please Select Proper Date Range");
                $("#fromDate, #toDate").val('');
            }else{
                $('#fromDate_export').val(fromDate);
                $('#toDate_export').val(toDate);
                $('#filterData_export').val("custom");
                loadDashboardDataForStockist("custom",toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
            }
        }
    });

     function loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales) {

        //$('[class="loader"]').show();
        $('[class="data_value"]').text(0);

        var inputData = {'filter_date': filterData, 'fromDate': fromDate, 'toDate': toDate,'buid':buid,"brandid":brandid,"manufid":manufid,'productgrpid':productgrpid,'categoryid':categoryid,'primary_secondary_sales':primary_secondary_sales};
        var custom_load_url = (window.location.pathname == "/stockist")?"/stockist":"/";
        var response = $.post(custom_load_url,inputData);
        response.done(function (data){
            console.log("Data ");
            console.log(data);
            var mainGridData = {};    
            if(data.BFILData != undefined){
                mainGridData = data.BFILData;
            }
            
            if(data.primarysalesenable!= undefined && data.primarysalesenable==1){
                console.log('apob===========primary sales enable');
                if($("#primary_secondary_sales option[value='2']").length > 0){
                    $("#primary_secondary_sales option[value='2']").remove();
                }
                if($("#primary_secondary_sales option[value='3']").length > 0){
                    $("#primary_secondary_sales option[value='3']").remove();   
                }
                if($("#primary_secondary_sales option[value='1']").length <= 0){
                    var option1 = $("<option/>", {value: '1', text: 'Primary Sales'});
                    $('#primary_secondary_sales').append(option1,[0]);
                }
            }else if(data.primarysalesenable!= undefined && data.primarysalesenable==2){
                console.log('Intermediate===========secondary sales enable');
                $("#primary_secondary_sales option[value='1']").remove();
                if($("#primary_secondary_sales option[value='3']").length <= 0){
                    var option1 = $("<option/>", {value: '3', text: 'Intermediate Sales'});
                    $('#primary_secondary_sales').append(option1,[0]);
                }
                if($("#primary_secondary_sales option[value='2']").length <= 0){
                    var option2 = $("<option/>", {value: '2', text: 'Secondary Sales'});
                    $('#primary_secondary_sales').append(option2,[1]);
                }
            }else if(data.primarysalesenable!= undefined && data.primarysalesenable==3){
                console.log('secondary sales===========enable');
                if($("#primary_secondary_sales option[value='3']").length > 0){

                    $("#primary_secondary_sales option[value='3']").remove();
                }
                if($("#primary_secondary_sales option[value='1']").length > 0){

                    $("#primary_secondary_sales option[value='1']").remove();
                }
            }else if(data.primarysalesenable!= undefined && data.primarysalesenable==0){
                console.log('enable all sales');
                if($("#primary_secondary_sales option[value='1']").length <= 0){
                    var option1 = $("<option/>", {value: '1', text: 'Primary Sales'});
                    $('#primary_secondary_sales').append(option1,[0]);
                }
                if($("#primary_secondary_sales option[value='3']").length <= 0){
                    var option2 = $("<option/>", {value: '3', text: 'Intermediate Sales'});
                    $('#primary_secondary_sales').append(option2,[1]);
                }
                if($("#primary_secondary_sales option[value='2']").length <= 0){
                    var option3 = $("<option/>", {value: '2', text: 'Secondary Sales'});
                    $('#primary_secondary_sales').append(option3,[2]);
                }
            }else if($("#primary_secondary_sales option[value='1']").length <= 0){
                var option1 = $("<option/>", {value: '1', text: 'Primary Sales'});
               $('#primary_secondary_sales').append(option1,[0]);
            }
            if(data.primary_secondary_sales != undefined && $('#primary_secondary_sales').length>0){
                $("#primary_secondary_sales").select2('val',data.primary_secondary_sales);
            }
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
            $('[class="loader"]').hide();
            reloadGridDatastockist();
        });

        // Here I`m adding the update for the 1st FF tabs.
        new_onboard_outlets_reload = false;
        self_orders_reload = false;
        delivery_reload = false;
        pickers_reload = false;
        verification_reload = false;
        shrinkage_reload = false;
        collections_reload = false;
        vehicles_reload = false;
        logistics_reload = false;
        // No need of Reload, as there is no date validation here
        // closing this here 
        // inventory_reload = true;

        //reloadGridDatastockist();

        // The below 2 Lines, hide all the tab Headings, expect the FF tab
        $("#dashboard_stockist_list").parent().addClass("active");

        $('[class="loader"]').hide();
    }

    $(function (){              
        reloadGridDatastockist();        
    });

    function reloadGridDatastockist()
    {           
        $.ajax({
            url: '/stockist/stockistsales',
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                var res1=getInputDates();
                if ($("#dashboard_stockist_list").data("igGrid") !=null) {

     
                    $("#dashboard_stockist_list").igGrid("destroy");

                }
                    
                    $('#dashboard_stockist_list').igGrid({
                        width: "100%",
                        dataSource: response.data,
                        
                columns: [
                { headerText: "FC Name", key: "Stockist_Name", dataType: "string", width: "125px" },
                { headerText: "DC Name", key: "Parent", dataType: "string", width: "125px" },
/*                { headerText: "Code", key: "Stockist_Code", dataType: "string", width: "125px" },
*/                { headerText: "State", key: "State", dataType: "string", width: "125px" },
                { headerText: "City", key: "City", dataType: "string", width: "125px" },
                { headerText: "Total Orders", key: "Total_Orders", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${Total_Orders} </div>' },
                { headerText: "TBV", key: "TBV", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${TBV} </div>' },
                { headerText: "Opening Stock", key: "Opening_Stock", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Opening_Stock} </div>' },
                { headerText: "Invoiced", key: "Invoiced", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${Invoiced} </div>' },
                { headerText: "Total Invoiced", key: "Total_Invoiced", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${Total_Invoiced} </div>' },
                /*{ headerText: "Order Date", key: "Order_Date", dataType: "date",format:"dd-MM-yyyy", width: "125px",template: '<div class="textRightAlign"> ${Order_Date} </div>' },*/
                { headerText: "Returns", key: "Returns", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${Returns} </div>' },
                { headerText: "Total Returned", key: "Total_Returned", dataType: "number", width: "125px",template: '<div class="textCenterAlign"> ${Total_Returned} </div>' },
                { headerText: "Cancel", key: "Cancel", dataType: "number", width: "100px",template: '<div class="textCenterAlign"> ${Cancel} </div>' },
                { headerText: "Total Cancelled", key: "Total_Cancelled", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Total_Cancelled} </div>' },
                { headerText: "Delivered", key: "Delivered", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Delivered} </div>' },     
                { headerText: "Total Delivered", key: "Total_Delivered", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Total_Delivered} </div>'},
                { headerText: "Pending GRN", key: "Pending_GRN", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Pending_GRN} </div>' },
                { headerText: "Pending GRN Value", key: "Pending_GRN_Value", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Pending_GRN_Value} </div>' },
                { headerText: "Collected", key: "Collected", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Collected} </div>' },
                { headerText: "Outstanding", key: "Outstanding", dataType: "number",width: "100px",template: '<div class="textCenterAlign"> ${Outstanding} </div>' },
                { headerText: "Cashback Orders", key: "Cashback_Orders", dataType: "string", width: "125px" },

                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "local",
                    columnSettings: [
                    {columnKey: 'Stockist_Name', allowSorting: false },
/*                    {columnKey: 'Stockist_Code', allowSorting: true },
*/                    {columnKey: 'Total_Orders', allowSorting: true },
                    {columnKey: 'upc', allowSorting: true },
                    {columnKey: 'StateName', allowSorting: true },
                    {columnKey: 'CustomerTypeName', allowSorting: true },
                    {columnKey: 'price', allowFiltering: true },
                    {columnKey: 'ptr', allowFiltering: true },
                    {columnKey: 'effective_date', allowSorting: true },
                    //{columnKey: 'cpEnabled', allowSorting: false },
                    ]
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'SNO', allowFiltering: false },
                        {columnKey: 'product_title', allowFiltering: true },
                        {columnKey: 'seller_sku', allowFiltering: true },
                        {columnKey: 'upc', allowFiltering: true },
                        {columnKey: 'ptr', allowFiltering: true },
                        {columnKey: 'StateName', allowFiltering: true },
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'effective_date', allowFiltering: true },
                      //  {columnKey: 'cpEnabled', allowFiltering: false },
                        /*{columnKey: 'DI', allowFiltering: false },
                        {columnKey: 'MI', allowFiltering: false },
                        {columnKey: 'CI', allowFiltering: false },*/
                    ]
                },
                  { 
                name: "Summaries",
                type: "local",
                showDropDownButton: false,
                summariesCalculated: function (evt, ui) {
                    var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                    listPricesummaryCells.each(function () {
                        if ($(this).text() != "") {
                            $(this).text($(this).text().substr(2));
                            $(this).css({'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "Stockist_Name", allowSummaries: false},
                    {columnKey: "Parent", allowSummaries: false},
/*                    {columnKey: "Stockist_Code", allowSummaries: false},
*/                    {columnKey: "State", allowSummaries: false},
                    {columnKey: "City", allowSummaries: false},
                    {columnKey: "Cashback_Orders", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Total_Orders", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "TBV", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Opening_Stock", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Invoiced", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Total_Invoiced", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Order_Date", allowSummaries: false},

                    {columnKey: "Returns", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},

                    {columnKey: "Total_Returned", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Cancel", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Total_Cancelled", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Delivered", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Total_Delivered", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Pending_GRN", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Pending_GRN_Value", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Collected", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Outstanding", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    
                    
                ]
            },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    //name: 'AppendRowsOnDemand', 
                    name: 'Paging',
                    loadTrigger: 'auto', 
                    //type: 'remote'
                    type: 'local' 
 
                },
                {
                    name : "RowSelectors",
                    enableRowNumbering: true
                },
                {
                    name: "Selection"
                },
                {
                    name: "ColumnFixing",
                },
                
            ],
                    });
                if(res1.sales_type==2){
                    $("#dashboard_stockist_list").igGrid("showColumn", 0);
                    //$("#dashboard_stockist_list").igGrid("showColumn", 2);
                    $("#dashboard_stockist_list").igGrid("showColumn", 6);
                    $("#dashboard_stockist_list").igGrid("showColumn", 15);
                    $("#dashboard_stockist_list").igGrid("showColumn", 16);
                    $("#dashboard_stockist_list").igGrid("showColumn", 18);
                    $("#dashboard_stockist_list").igGrid("showColumn", 19);
                }else if(res1.sales_type==1  || res1.sales_type==3){
                    $("#dashboard_stockist_list").igGrid("hideColumn", 0);
                    $("#dashboard_stockist_list").igGrid("hideColumn", 6);
                    $("#dashboard_stockist_list").igGrid("hideColumn", 15);
                    $("#dashboard_stockist_list").igGrid("hideColumn", 16);
                    $("#dashboard_stockist_list").igGrid("hideColumn", 18);
                    $("#dashboard_stockist_list").igGrid("hideColumn", 19);
                }
            },
        });
    }


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


    function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;
            
            // Summaries UI changes
            var id_text = "_summaries_footer_row_text_container_sum_";
            $("#"+ selectedId +"_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#"+ selectedId + id_text + newText).attr("class", "summariesStyle").text($("#"+ selectedId + id_text + newText).text().replace(/\s=\s/g, ''));

            // S.No and Column Title Adjustments below
            if (columns[idx].dataType == "number" || columns[idx].dataType == "double") {
                var isDecimal = columns[idx].headerText.substring(0, 2);
                if (isDecimal === "1_") {
                    var columnText =
                        (columns[idx].headerText.substring(columns[idx].headerText.length - 4) === "_Per") ? columns[idx].headerText.replace("_Per", " %").substring(2) : columns[idx].headerText.substring(2);
                    columnText = (columnText.substring(0, 2) === "N_") ? columnText.substring(2) : columnText;
                    $("#"+ selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                        .html("<p style='text-align: right !important; margin: 0px 5px !important;'>" + columnText.replace(/_/g, ' ') + "</p>")
                        .attr('title', columnText.replace(/_/g,' '));
                }
            } else if (columns[idx].dataType == "string") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.replace(/_/g,' '))
                    .text(newText.replace(/_/g,' '));
            } else if (columns[idx].dataType == "date") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.substring(2).replace(/_/g, ' '))
                    .text(newText.substring(2).replace(/_/g, ' '));
            }

        }
    }


    $("#dashboard_stockist_list").on("iggriddatarendered", function (event, args) {
        $("#dashboard_stockist_list_Stockist_Name > span.ui-iggrid-headertext").attr('title', "Stockist Name");
/*        $("#dashboard_stockist_list_Stockist_Code > span.ui-iggrid-headertext").attr('title', "Stockist Code");
*/        $("#dashboard_stockist_list_Total_Orders > span.ui-iggrid-headertext").attr('title', "Total Orders");
        $("#dashboard_stockist_list_TBV > span.ui-iggrid-headertext").attr('title', "TBV");
        $("#dashboard_stockist_list_Invoiced > span.ui-iggrid-headertext").attr('title', "Invoiced");
        $("#dashboard_stockist_list_Total_Invoiced > span.ui-iggrid-headertext").attr('title', "Total Invoiced");
        $("#dashboard_stockist_list_Order_Date > span.ui-iggrid-headertext").attr('title', "Order Date");
        $("#dashboard_stockist_list_Returns > span.ui-iggrid-headertext").attr('title', "Returns");
        $("#dashboard_stockist_list_Total_Returned > span.ui-iggrid-headertext").attr('title', "Total Returned");
        $("#dashboard_stockist_list_Cancel > span.ui-iggrid-headertext").attr('title', "Cancel");
        $("#dashboard_stockist_list_Total_Cancelled > span.ui-iggrid-headertext").attr('title', "Total Cancelled");
        $("#dashboard_stockist_list_Delivered > span.ui-iggrid-headertext").attr('title', "Delivered");
        $("#dashboard_stockist_list_Total_Delivered > span.ui-iggrid-headertext").attr('title', "Total Delivered");
         $("#dashboard_stockist_list_Pending_GRN > span.ui-iggrid-headertext").attr('title', "Pending GRN");
          $("#dashboard_stockist_list_Pending_GRN_Value > span.ui-iggrid-headertext").attr('title', "Pending GRN Value");
           $("#dashboard_stockist_list_Collected > span.ui-iggrid-headertext").attr('title', "Collected");
           $("#dashboard_stockist_list_Outstanding > span.ui-iggrid-headertext").attr('title', "Outstanding");
           $("#dashboard_stockist_list_Opening_Stock > span.ui-iggrid-headertext").attr('title', "Opening Stock");
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_stockist_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_stockist_list");
            });

     $('#dc_all_legalentity').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        $('#exportbuId').val(buid);
        var categoryid ='';
        var brandid ='';
        var manufid ='';
        var productgrpid ='';
        if($('#primary_secondary_sales').length){
        var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
        var primary_secondary_sales =2;    
        }
        $('#exportflag').val(primary_secondary_sales);
        $("#brands_le_id").empty();
        $("#product_group_ids").empty();
        $('#category').select2("val", 0);
         var option1 = $("<option/>", {value: '', text: 'Please Select Product Group'});
        var option2 = $("<option/>", {value: '', text: 'Please Select Brands'});
        $('#product_group_ids').append(option1);
        $('#brands_le_id').append(option2);
        $("#product_group_ids").select2("val", ''); 
        $("#brands_le_id").select2("val", '');   
        $('#manufacturer_le_ids').select2("val", '');
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            $('#filterData_export').val(filterData);
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            $('#fromDate_export').val(fromDate);
            $('#toDate_export').val(toDate);
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
        var token=$('#csrf-token').val();
        $.ajax({
           headers: {'X-CSRF-TOKEN': token},
            url: "/salesorders/setbuid",
            type: 'POST',
            data:{
              buid:buid,
            },
            dataType: 'json', // added data type
            success: function(res) {
               
            }
          });
    });

    $('#brands_le_id').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid ='';
        if($('#primary_secondary_sales').length){
            var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
    });

    $('#category').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid=$(this).val();
        var brandid ='';
        var manufid ='';
        var productgrpid ='';
        if($('#primary_secondary_sales').length){
           var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        var option1 = $("<option/>", {value: '', text: 'Please Select Product Group'});
        var option2 = $("<option/>", {value: '', text: 'Please Select Brands'});
        $('#product_group_ids').append(option1);
        $('#brands_le_id').append(option2);
        $('#manufacturer_le_ids').select2("val", '');
        $('#brands_le_id').select2("val", '');
        $('#product_group_ids').select2("val", '');
        //$("#manufacturer_le_ids").empty();
        $("#brands_le_id").empty();
        $("#product_group_ids").empty();
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
    });

    $('#manufacturer_le_ids').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads      
        $('[class="loader"]').show();
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid ='';
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid ='';
        if($('#primary_secondary_sales').length){
           var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
    });

    $('#product_group_ids').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        if($('#primary_secondary_sales').length){
           var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
    });  

    $('#manufacturer_le_ids').change(function () {
       var manufid=$(this).val();
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/stockist/getbrands",
                type:"POST",
                data: 'manufid='+manufid,
                dataType:'json',
                success:function(response){
                    
                 $("#brands_le_id").empty();   
                 $("#product_group_ids").empty(); 
                 $("#brands_le_id").html(response.res);
                 var option = $("<option/>", {value: '', text: 'Please Select Product Group'});
                 $('#product_group_ids').append(option);
                 $("#brands_le_id").select2("val", '');
                 $("#product_group_ids").select2("val", '');
                 //$('[class="loader"]').hide();

                },
                error: function(response){
                alert("Status: " + response);
                $('[class="loader"]').hide();

                }

        }); 
    }); 

    $('#brands_le_id').change(function () {
       var brandid=$(this).val();
       var token = $('#csrf-token').val();
       $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/stockist/getproductgroupbybrand",
                type:"POST",
                data: 'brandid='+brandid,
                dataType:'json',
                success:function(response){
                  
                 $("#product_group_ids").empty(); 
                 $("#product_group_ids").html(response.res);
                 $("#product_group_ids").select2("val", '');  
                 //$('[class="loader"]').hide();

                },

                error: function(res) { 
                alert("Status: " + err);
                $('[class="loader"]').hide();

                 } 
        }); 
    });

     $.ajax({
             headers: {'X-CSRF-TOKEN': $('#csrf-token').val()},
            url: '/getCategoryList',
            type: 'GET',  
            dataType: "text",                                           
            success: function (rs) 
            {
                $("#category").html(rs);
                $('.prod_class').css('color','#0174DF !important');
                $("#category").select2().select2('val',0);
            },
            error: function(res) { 
                alert("Status: " + res);

                 } 
        });
     $('#primary_secondary_sales').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        if($('#primary_secondary_sales').length){
            var primary_secondary_sales =$('#primary_secondary_sales').val();
        }else{
            var primary_secondary_sales=2;
        }
        $('#exportflag').val(primary_secondary_sales);
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForStockist(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }else{
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForStockist(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid,primary_secondary_sales);
        }
    });
    $('[class="loader"]').hide();
});