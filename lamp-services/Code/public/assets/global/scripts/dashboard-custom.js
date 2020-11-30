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

    var load_url = (window.location.pathname == "/cnc")?"/cnc/":"/";
    console.log("load url "+load_url);
    var new_onboard_outlets_reload = false;
    var self_orders_reload = false;
    var delivery_reload = false;
    var pickers_reload = false;
    var verification_reload = false;
    var shrinkage_reload = false;
    var collections_reload = false;
    var vehicles_reload = false;
    var logistics_reload = false;
    var inventory_reload = false;

    $('#loader').hide();    

    $("#new_onboard_outlets_list").click(function(){
        if(new_onboard_outlets_reload == false){
            new_onboard_outlets_reload = true;
            getNewOnboardOutletsList();
        }
    });

    $("#self_orders_list").click(function(){
        if(self_orders_reload == false){
            self_orders_reload = true;

            makeAjaxCallForigGrid("selforders","self_orders_list","Self Orders");

            $("#dashboard_self_orders_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_self_orders_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_self_orders_list");
            });
        }
    });

    $("#delivery_list").click(function(){
        if(delivery_reload == false){
            delivery_reload = true;
            
            makeAjaxCallForigGrid("deliverydashboard","delivery_list","Delivery Team");

            $("#dashboard_delivery_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_delivery_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_delivery_list");
            });
        }
    });

    $("#pickers_list").click(function(){
        if(pickers_reload == false){
            pickers_reload = true;
            
            makeAjaxCallForigGrid("pickersdashboard","pickers_list","Picking Team");
            
            $("#dashboard_pickers_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_pickers_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_pickers_list");
            });
        }
    });

    $("#verification_list").click(function(){
        if(verification_reload == false){
            verification_reload = true;

            makeAjaxCallForigGrid("verificationdashboard","verification_list","Verification Team");

            $("#dashboard_verification_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_verification_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_verification_list");
            });
        }
    });

    $("#shrinkage_list").click(function(){
        if(shrinkage_reload == false){
            shrinkage_reload = true;
    
            makeAjaxCallForigGrid("shrinkagedashboard","shrinkage_list","Shrinkage");

            $("#dashboard_shrinkage_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_shrinkage_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_shrinkage_list");
            });
        }
    });

    $("#collections_list").click(function(){
        if(collections_reload == false){
            collections_reload = true;
    
            makeAjaxCallForigGrid("collectionsdashboard","collections_list","Collections");

            $("#dashboard_collections_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_collections_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_collections_list");
            });
        }
    });

    $("#vehicles_list").click(function(){
        if(vehicles_reload == false){
            vehicles_reload = true;

            makeAjaxCallForigGrid("vehiclesdashboard","vehicles_list","Vehicles");

            $("#dashboard_vehicles_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_vehicles_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_vehicles_list");
            });
        }
    });

    $("#logistics_list").click(function(){
        if(logistics_reload == false){
            logistics_reload = true;
            getLogisticsList();

            $("#dashboard_logistics_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_logistics_list").igGrid("option", "columns");
                for(var idx = 1; idx < columns.length; idx++){
                    if (columns[idx].dataType == "number" || columns[idx].dataType == "double"){
                        $("#dashboard_logistics_list_"+columns[idx].key+" > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>"+columns[idx].headerText+"</p>");
                    }
                }
            });
        }
    });

    $("#inventory_list").click(function(){
        if(inventory_reload == false){
            inventory_reload = true;
            getInventoryList();

            $("#dashboard_inventory_list").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#dashboard_inventory_list").igGrid("option", "columns");
                formatigGridContent(columns,"dashboard_inventory_list");
            });
        }
    });

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

    $('#customDateWidthSubmit').click(function () {
        var toDate = $('#toDate').val();
        var fromDate = $('#fromDate').val();
        var categoryid =$('#category').val();
        var buid =$('#dc_all_legalentity').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
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
                loadDashboardData("custom",toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid);
            }
        }
    });

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

    $('#dashboard_filter_dates').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads 
        var filterData = $(this).val();
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid);
        }else{
            $("#customDatesView").removeClass("customDateArea");
        }
    });

    function loadDashboardData(filterData,toDate,fromDate,buid,brandid,manufid,productgrpid,categoryid) {

        $('[class="loader"]').show();
        $('[class="data_value"]').text(0);

        var inputData = {'filter_date': filterData, 'fromDate': fromDate, 'toDate': toDate, 'buid':buid,
                         'brandid':brandid,'manufid':manufid,'productgrpid':productgrpid,'categoryid':categoryid};
        var custom_load_url = (window.location.pathname == "/cnc")?"/cnc":"/";
        var response = $.post(custom_load_url,inputData);
        response.done(function (data){
            var mainGridData = {};
            if(data.cncData != undefined){
                mainGridData = data.cncData;    
            }else if(data.order_details != undefined){
                mainGridData = data.order_details;
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

        getFieldForceList();

        // The below 2 Lines, hide all the tab Headings, expect the FF tab
        $("#ff_list").parent().addClass("active");
        $("#new_onboard_outlets_list, #self_orders_list, #delivery_list, #pickers_list, #shrinkage_list, #verification_list, #collections_list, #vehicles_list, #logistics_list, #inventory_list").parent().removeClass("active");
        
        // The below 2 Lines, hide all the tabs, expect the FF tab
        $("#ff_list_tab").addClass("active");
        $("#new_onboard_outlets_list_tab, #self_orders_list_tab, #delivery_list_tab, #pickers_list_tab, #shrinkage_list_tab, #verification_list_tab, #collections_list_tab, #vehicles_list_tab, #logistics_list_tab, #inventory_list").removeClass("active");

        //$('[class="loader"]').hide();
    }

    /*$(function (){              
        getFieldForceList();        
    });*/

    function getInputDates(){
        return {
            'buid':$('#dc_all_legalentity').val(),
            'filter_date': $("#dashboard_filter_dates").val(),
            'toDate': $("#toDate").val(),
            'fromDate': $("#fromDate").val()
        };
    }

    function getFieldForceList()
    {
        makeAjaxCallForigGrid("gettodayffuserslist","ff_list","Sales Team");

        $("#dashboard_ff_list").on("iggriddatarendered", function (event, args) {
            $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
            var columns = $("#dashboard_ff_list").igGrid("option", "columns");
            formatigGridContent(columns,"dashboard_ff_list");
        });
    }

    /*function getFieldForceList()
    {
        $.ajax({
            url: load_url+'gettodayffuserslist',
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
                if(response.length > 0){
                    $('#ff_list_error').addClass("hideError");
                    $('#ff_list_table').css("display","block");
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings:  [
                            {columnKey: "commission", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "margin", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "delivered_margin", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "order_cnt", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "calls_cnt", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "tbv", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "UOB", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "TLC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "Contribution", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "AVG", "active": true }]},
                            {columnKey: "success_rate", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "AVG", "active": true }]},
                            {columnKey: "ABV", allowSummaries: false},
                            {columnKey: "ULC", allowSummaries: false},
                            {columnKey: "ALC", allowSummaries: false},
                            {columnKey: "NAME", allowSummaries: false},
                            {columnKey: "role", allowSummaries: false},
                            {columnKey: "hub_name", allowSummaries: false},
                            {columnKey: "first_order", allowSummaries: false},
                            {columnKey: "first_call", allowSummaries: false}
                        ]
                    });
                    $('#dashboard_ff_list').igGrid({

                    dataSource: response,
                    autoGenerateColumns: false,
                    width:"100%",
                    columns: [
                        {headerText: 'Sales Rep', key: 'NAME', dataType: 'string',width: "250px"},
                        {headerText: 'Role', key: 'role', dataType: 'string', width: "60px"},
                        {headerText: 'Commission', key: 'commission', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px",formatter: function(val,data){
                            return $.ig.formatter(val,"number","0.00");
                        }},
                        {headerText: 'Hub', key: 'hub_name', dataType: 'string', width: "120px"},
                        {headerText: 'TGM', key: 'margin', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px",formatter: function(val,data){
                            return $.ig.formatter(val,"number","0.00");
                        }},
                        {headerText: 'Del. TGM', key: 'delivered_margin', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "100px",formatter: function(val,data){
                            return $.ig.formatter(val,"number","0.00");
                        }},
                        {headerText: '#Orders', key: 'order_cnt', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px", formatter: function(val,data){
                            return $.ig.formatter(val,"number","0");
                        }},
                        {headerText: 'First Order', key: 'first_order', dataType: 'string', width: "120px"},
                        {headerText: '#Calls', key: 'calls_cnt', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "90px", formatter: function(val,data){
                            return $.ig.formatter(val,"number","0");
                        }},
                        {headerText: 'First Call', key: 'first_call', dataType: 'string', width: "120px"},
                        {headerText: 'TBV', key: 'tbv', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px",formatter: function(val,data){
                            return $.ig.formatter(val,"number","0.00");
                        }},
                        {headerText: 'UOB', key: 'UOB', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px", formatter: function(val,data){
                            return $.ig.formatter(val,"number","0");
                        }},
                        {headerText: 'ABV', key: 'ABV', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px",formatter: function(val,data){
                            return $.ig.formatter(val,"number","0.00");
                        }},
                        {headerText: 'TLC', key: 'TLC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px", formatter: function(val,data){
                            return $.ig.formatter(val,"number","0");
                        }},
                        {headerText: 'ULC', key: 'ULC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px"},
                        {headerText: 'ALC', key: 'ALC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "80px"},
                        {headerText: 'Contribution %', key: 'Contribution', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: 'Success %', key: 'success_rate', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }}
                    ],
                    features: customFeatures
                    });
                }
                else{
                    $('#ff_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#ff_list_table')
                        .css("display","none");
                }
            },
            error: function() {
                $('ff_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>Sales Team</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#ff_list_table')
                    .css("display","none");
            }
        });
    }*/

    /*$("#dashboard_ff_list").on("iggriddatarendered", function (event, args) {

        $("#dashboard_ff_list_Contribution > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>Contribution %</p>");
        $("#dashboard_ff_list_success_rate > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>Success %</p>");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#dashboard_ff_list_role > span.ui-iggrid-headertext").attr('title', "Role");
        $("#dashboard_ff_list_NAME > span.ui-iggrid-headertext").attr('title', "Sales Rep");
        $("#dashboard_ff_list_hub_name > span.ui-iggrid-headertext").attr('title', "Hub");
        $("#dashboard_ff_list_TLC > span.ui-iggrid-headertext").attr('title', "Total Lines Cut");
        $("#dashboard_ff_list_tbv > span.ui-iggrid-headertext").attr('title', "Total Bill Value");
        $("#dashboard_ff_list_ULC > span.ui-iggrid-headertext").attr('title', "Unique Lines Cut");
        $("#dashboard_ff_list_ALC > span.ui-iggrid-headertext").attr('title', "Average Lines Cut");
        $("#dashboard_ff_list_calls_cnt > span.ui-iggrid-headertext").attr('title', "Calls Count");
        $("#dashboard_ff_list_first_call > span.ui-iggrid-headertext").attr('title', "First Call");
        $("#dashboard_ff_list_commission > span.ui-iggrid-headertext").attr('title', "Commission %");
        $("#dashboard_ff_list_order_cnt > span.ui-iggrid-headertext").attr('title', "Orders Count");
        $("#dashboard_ff_list_ABV > span.ui-iggrid-headertext").attr('title', "Average Bill Value");
        $("#dashboard_ff_list_first_order > span.ui-iggrid-headertext").attr('title', "First Order");
        $("#dashboard_ff_list_success_rate > span.ui-iggrid-headertext").attr('title', "Success Rate");
        $("#dashboard_ff_list_Contribution > span.ui-iggrid-headertext").attr('title', "Contribution");
        $("#dashboard_ff_list_UOB > span.ui-iggrid-headertext").attr('title', "Unique Outlets Billed");
        $("#dashboard_ff_list_margin > span.ui-iggrid-headertext").attr('title', "Total Gross Margin");
        $("#dashboard_ff_list_delivered_margin > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;' title='Delivered Total Gross Margin'>Del. TGM</p>");
        
        // Sumaries related UI changes on Dashboard
        $("#dashboard_ff_list_summaries_footer_row_icon_container_sum_tbv, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_success_rate, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_commission, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_margin, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_delivered_margin, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_order_cnt, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_calls_cnt, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_tbv, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_UOB, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ABV, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_TLC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ULC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ALC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_Contribution"
            ).remove();

        var id_text = "#dashboard_ff_list_summaries_footer_row_text_container_sum_"; 
        $(id_text+"tbv").attr("class","summariesStyle").text($(id_text+"tbv").text().replace(/\s=\s/g, ''));
        $(id_text+"success_rate").attr("class","summariesStyle").text($(id_text+"success_rate").text().replace(/\s=\s/g, ''));
        $(id_text+"commission").attr("class","summariesStyle").text($(id_text+"commission").text().replace(/\s=\s/g, ''));
        $(id_text+"margin").attr("class","summariesStyle").text($(id_text+"margin").text().replace(/\s=\s/g, ''));
        $(id_text+"delivered_margin").attr("class","summariesStyle").text($(id_text+"delivered_margin").text().replace(/\s=\s/g, ''));
        $(id_text+"order_cnt").attr("class","summariesStyle").text($(id_text+"order_cnt").text().replace(/\s=\s/g, ''));
        $(id_text+"calls_cnt").attr("class","summariesStyle").text($(id_text+"calls_cnt").text().replace(/\s=\s/g, ''));
        $(id_text+"tbv").attr("class","summariesStyle").text($(id_text+"tbv").text().replace(/\s=\s/g, ''));
        $(id_text+"UOB").attr("class","summariesStyle").text($(id_text+"UOB").text().replace(/\s=\s/g, ''));
        $(id_text+"ABV").attr("class","summariesStyle").text($(id_text+"ABV").text().replace(/\s=\s/g, ''));
        $(id_text+"TLC").attr("class","summariesStyle").text($(id_text+"TLC").text().replace(/\s=\s/g, ''));
        $(id_text+"ULC").attr("class","summariesStyle").text($(id_text+"ULC").text().replace(/\s=\s/g, ''));
        $(id_text+"ALC").attr("class","summariesStyle").text($(id_text+"ALC").text().replace(/\s=\s/g, ''));
        $(id_text+"Contribution").attr("class","summariesStyle").text($(id_text+"Contribution").text().replace(/\s=\s/g, ''));
    });*/

    function getNewOnboardOutletsList()
    {           
        $.ajax({
            url: load_url+'getnewcustomers',
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
                if(response.data.length > 0){
                    $('#new_onboard_outlets_list_error').addClass("hideError");
                    $('#new_onboard_outlets_list_table').css("display","block");
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings:  [
                            {columnKey: "orders", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "NAME", allowSummaries: false},
                            {columnKey: "le_code", allowSummaries: false},
                            {columnKey: "hub", allowSummaries: false},
                            {columnKey: "business_legal_name", allowSummaries: false},
                            {columnKey: "legal_entity_type", allowSummaries: false},
                            {columnKey: "business_type", allowSummaries: false},
                            {columnKey: "name", allowSummaries: false},
                            {columnKey: "mobile_no", allowSummaries: false},
                            {columnKey: "area", allowSummaries: false},
                            {columnKey: "beat", allowSummaries: false},
                            {columnKey: "city", allowSummaries: false},
                            {columnKey: "state", allowSummaries: false},
                            {columnKey: "pincode", allowSummaries: false},
                            {columnKey: "number", allowSummaries: false},
                            {columnKey: "last_order_date", allowSummaries: false},
                            {columnKey: "created_at", allowSummaries: false},
                            {columnKey: "created_time", allowSummaries: false},
                            {columnKey: "created_by", allowSummaries: false}
                        ]
                    });
                    $('#dashboard_new_onboard_outlets_list').igGrid({
                        width: "100%",
                        dataSource: response.data,
                        columns: [
                            {headerText: 'Customer Code', key: 'le_code', dataType: 'string', width: '200px'},
                            {headerText: 'Hub', key: 'hub', dataType: 'string', width: '120px'},
                            {headerText: 'Shop Name', key: 'business_legal_name', dataType: 'string', width: '250px'},
                            {headerText: 'Customer Type', key: 'legal_entity_type', dataType: 'string', width: '120px'},
                            {headerText: 'Segment', key: 'business_type', dataType: 'string', width: '100px'},
                            {headerText: 'Customer Name', key: 'name', dataType: 'string', width: '150px'},
                            {headerText: 'Contact', key: 'mobile_no', dataType: 'number', width: '110px'},
                            {headerText: 'Area', key: 'area', dataType: 'string', width: '110px'},
                            {headerText: 'Beat', key: 'beat', dataType: 'string', width: '90px'},
                            {headerText: 'City', key: 'city', dataType: 'string', width: '90px'},
                            {headerText: 'State', key: 'state', dataType: 'string', width: '150px'},
                            {headerText: 'PIN Code', key: 'pincode', dataType: 'number', width: '80px'},
                            {headerText: '#Orders', key: 'orders', dataType: 'number', columnCssClass: "alignRight", width: '80px'},
                            {headerText: 'Last Order Date', key: 'last_order_date', dataType: 'date', formatter: function(val, data) { return $.ig.formatter(val, "date", "dd/MM/yyyy"); }, width: '100px'},
                            {headerText: 'Created Date', key: 'created_at', dataType: 'date', formatter: function(val, data) { return $.ig.formatter(val, "date", "dd/MM/yyyy"); }, width: '110px'},
                            {headerText: 'Created Time', key: 'created_time', dataType: 'string', width: '110px'},
                            {headerText: 'Created By', key: 'created_by', dataType: 'string', width: '110px'}
                        ],
                        features: customFeatures
                    });
                }else{
                    $('#new_onboard_outlets_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#new_onboard_outlets_list_table')
                        .css("display","none");
                }
            },
            error: function() {
                $('new_onboard_outlets_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>New Customers</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#new_onboard_outlets_list_table')
                    .css("display","none");
            }
        });
    }
    
    $('#dashboard_new_onboard_outlets_list').on("iggriddatarendered", function (event, args) {
        $("#dashboard_new_onboard_outlets_list_orders > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>#Orders</p>");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#dashboard_new_onboard_outlets_list_hub > span.ui-iggrid-headertext").attr("title","Hub");
        $("#dashboard_new_onboard_outlets_list_name > span.ui-iggrid-headertext").attr("title","Customer Name");
        $("#dashboard_new_onboard_outlets_list_area > span.ui-iggrid-headertext").attr("title","Area Name");
        $("#dashboard_new_onboard_outlets_list_beat > span.ui-iggrid-headertext").attr("title","Beat Name");
        $("#dashboard_new_onboard_outlets_list_city > span.ui-iggrid-headertext").attr("title","City");
        $("#dashboard_new_onboard_outlets_list_state > span.ui-iggrid-headertext").attr("title","State");
        $("#dashboard_new_onboard_outlets_list_le_code > span.ui-iggrid-headertext").attr("title","Customer Code");
        $("#dashboard_new_onboard_outlets_list_pincode > span.ui-iggrid-headertext").attr("title","PIN Code");
        $("#dashboard_new_onboard_outlets_list_orders > span.ui-iggrid-headertext").attr("title","Orders");
        $("#dashboard_new_onboard_outlets_list_mobile_no > span.ui-iggrid-headertext").attr("title","Contact");
        $("#dashboard_new_onboard_outlets_list_created_at > span.ui-iggrid-headertext").attr("title","Created Date");
        $("#dashboard_new_onboard_outlets_list_created_by > span.ui-iggrid-headertext").attr("title","Created By");
        $("#dashboard_new_onboard_outlets_list_created_time > span.ui-iggrid-headertext").attr("title","Created Time");
        $("#dashboard_new_onboard_outlets_list_business_type > span.ui-iggrid-headertext").attr("title","Segment Name");
        $("#dashboard_new_onboard_outlets_list_last_order_date > span.ui-iggrid-headertext").attr("title","Last Order Date");
        $("#dashboard_new_onboard_outlets_list_legal_entity_type > span.ui-iggrid-headertext").attr("title","Customer Type");
        $("#dashboard_new_onboard_outlets_list_business_legal_name > span.ui-iggrid-headertext").attr("title","Shop Name");

        var id_text = "#dashboard_new_onboard_outlets_list_summaries_footer_row_text_container_sum_";
        $("#dashboard_new_onboard_outlets_list_summaries_footer_row_icon_container_sum_orders").remove();
        $(id_text+"orders").attr("class","summariesStyle").text($(id_text+"orders").text().replace(/\s=\s/g, ''));
    });

    function getLogisticsList()
    {              
        $.ajax({
            url: load_url+'logisticsdashboard',
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
                var logisticsHeaders = [];
                if(response.data.length == 0){
                    $('#logistics_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#logistics_list_table')
                        .css("display","none");
                }else{
                    
                    // Intital Push for the 1st Column Only
                    logisticsHeaders.push({
                        headerText: response.headers[0],
                        key: "Warehouse_00",
                        dataType: "string",
                        width: "auto",
                    });

                    for(var i=1;i<response.headers.length;i++)
                    {
                        logisticsHeaders.push({
                            headerText: response.headers[i],
                            key: "Warehouse_0"+i,
                            dataType: "double",
                            columnCssClass: "alignRight",
                            headerCssClass: "alignRight",
                            width: "auto",
                        });
                    }
                }
                
                if(response.data.length > 0){
                    $('#logistics_list_error').addClass("hideError");
                    $('#logistics_list_table').css("display","block");
                    $('#dashboard_logistics_list').igGrid({
                        dataSource: response.data,
                        width: "100%",
                        columns: logisticsHeaders,
                        autoGenerateColumns: false,
                        features: customigGridFeatures(25)
                    });
                }
            },
            error: function() {
                $('#logistics_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>Logistics</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#logistics_list_table')
                    .css("display","none");
            }
        });
    }

    // Inventory list Tab contains only 1 Record as of now 1 DC
    // and this tab doesn`t need dates
    function getInventoryList() {
        $.ajax({
            url: load_url + 'inventorydashboard',
            type: 'GET',
            dataType: "json",
            data:getInputDates(),
            beforeSend: function() {
                $('#loader').show();
            },
            complete: function() {
                $('#loader').hide();
            },
            success: function(response) {
                
                if (response.headers.length > 0 && response.data.length > 0) {
                    $('#inventory_list_error').addClass("hideError");
                    $('#inventory_list_table').css("display","block");
                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: result.columnSummaries
                    });

                    $('#dashboard_inventory_list').igGrid({

                        dataSource: response.data,
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures 
                    });
                }
                else{
                    $('#inventory_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#inventory_list_table')
                        .css("display","none");
                }
            },
            error: function() {
                $('#inventory_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>Inventory</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#inventory_list_table')
                    .css("display","none");
            }
        });
    }

    // Custom Ajax for all the General igGrid Table Calls
    function makeAjaxCallForigGrid(customUrl,selectedId,selectedTabName) {
        $.ajax({
            url: load_url+customUrl,
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
                if (response.headers.length > 0 && response.data.length > 0) {
                    // Hiding Error as Data is Present
                    $('#'+selectedId+'_error').addClass("hideError");
                    $('#'+selectedId+'_table').css("display","block");

                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: result.columnSummaries
                    });

                    $('#dashboard_'+selectedId).igGrid({
                        dataSource: response.data,
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures,
                    });
                }
                else{
                    $('#'+selectedId+'_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#'+selectedId+'_table')
                        .css("display","none");
                }
            },
            error: function() {
                $('#'+selectedId+'_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>"+selectedTabName+"</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#'+selectedId+'_table')
                    .css("display","none");
            }
        });
    }
    

      var token=$('#csrf-token').val();
            var buid=$('#bu_id').val(); 
            $.ajax({
            type:'get',
            headers: {'X-CSRF-TOKEN': token},
            url:'/inventory/getbu',
            success: function(res){
                res.forEach(data=>{
                    $('#dc_all_legalentity').append(data);
                });
                $("#dc_all_legalentity").select2("val", buid);
                 getFieldForceList();
            }

        });

       $('#manufacturer_le_ids').change(function () {
       var manufid=$(this).val();
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/getbrands",
                type:"POST",
                data: 'manufid='+manufid,
                dataType:'json',
                success:function(response){   
                 $("#brands_le_id").empty();
                 var option = $("<option/>", {value: '', text: 'Please Select Product Group'});
                 $('#product_group_ids').append(option);
                 $("#product_group_ids").select2("val", '');   
                 $("#product_group_ids").empty(); 
                 $("#brands_le_id").html(response.res);
                 $("#brands_le_id").select2("val", '');
                 $('[class="loader"]').hide();

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
                url:"/getproductgroupbybrand",
                type:"POST",
                data: 'brandid='+brandid,
                dataType:'json',
                success:function(response){
                    //console.log(response.res);
                   
                 $("#product_group_ids").empty(); 
                 $("#product_group_ids").html(response.res);
                 $("#product_group_ids").select2("val", ''); 
                 $('[class="loader"]').hide();

                },

                error: function(res) { 
                alert("Status: " + err);
                $('[class="loader"]').hide();

                 } 
        }); 
    });

    /*$('#dc_all_legalentity').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var brandid ='';
        var manufid ='';
        var productgrpid ='';
        $("#product_group_ids").select2("val", ''); 
         $("#brands_le_id").select2("val", '');   
         $('#manufacturer_le_ids').select2("val", '');
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid);
        }else{
            $("#customDatesView").removeClass("customDateArea");
        }
    });*/

    $('#brands_le_id').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid ='';
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid);
        }else{
            var todate=$("#toDate").val();
            var fromdate=$("#fromDate").val();
            loadDashboardData(filterData,todate,fromdate,buid,brandid,manufid,productgrpid,categoryid);
            $("#customDatesView").removeClass("customDateArea");
        }
    });


     $('#category').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show(); 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid ='';
        var manufid ='';
        var productgrpid ='';
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
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid);
        }else{
            var todate=$("#toDate").val();
            var fromdate=$("#fromDate").val();
            loadDashboardData(filterData,todate,fromdate,buid,brandid,manufid,productgrpid,categoryid);
            $("#customDatesView").removeClass("customDateArea");
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
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid);
        }else{
            var todate=$("#toDate").val();
            var fromdate=$("#fromDate").val();
            loadDashboardData(filterData,todate,fromdate,buid,brandid,manufid,productgrpid,categoryid);
            $("#customDatesView").removeClass("customDateArea");
        }
    });

    $('#product_group_ids').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads 
        var buid =$('#dc_all_legalentity').val();
        var categoryid =$('#category').val();
        var brandid =$('#brands_le_id').val();
        var manufid =$('#manufacturer_le_ids').val();
        var productgrpid =$('#product_group_ids').val();
        var filterData = $('#dashboard_filter_dates').val();
        if(filterData != "custom"){
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardData(filterData,0,0,buid,brandid,manufid,productgrpid,categoryid);
        }else{
            var todate=$("#toDate").val();
            var fromdate=$("#fromDate").val();
            loadDashboardData(filterData,todate,fromdate,buid,brandid,manufid,productgrpid,categoryid);
            $("#customDatesView").removeClass("customDateArea");
        }
    }); 

    $('#dc_all_legalentity').change(function () {
      var buid=$(this).val();
      var url = '/warehouse/' + buid;
        window.location.href = url;        
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
    $('[class="loader"]').hide();
});