$(document).ready(function () {

    // Custom Date Formats
    $('#customDatePickerZone .input-daterange').datepicker({
        format: "dd/mm/yyyy",
        endDate: "today",
        todayHighlight: true
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        dataType: 'JSON'
    });


    var load_url = '/purchase/';

    var grn_details_reload = false;

    var inventory_details_reload = false;

    $("#grn_details_list").click(function () {
        if (grn_details_reload == false) {
            grn_details_reload = true;
            getGRNDetailsList();
        }
    });

    $("#inventory_details_list").click(function () {
        if (inventory_details_reload == false) {
            inventory_details_reload = true;
            getInventoryDetailsList();
        }
    });


    //date filter
    $('#dashboard_filter_dates').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads 
        var filterData = $(this).val();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = $('#brands_le_id').val();
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = $('#product_group_ids').val();
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            $('#filter_date').val(filterData);
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
        }
    });

    //custom date filter
    $('#customDateWidthSubmit').click(function () {
        var toDate = $('#toDate').val();
        var fromDate = $('#fromDate').val();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = $('#brands_le_id').val();
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = $('#product_group_ids').val();
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        if ((toDate == undefined || toDate == '') || (fromDate == undefined || fromDate == '')) {
            alert("Please Select Valid To & From Dates");
            $("#fromDate, #toDate").val('');
        }
        else {
            toDateCheck = new Date(toDate);
            fromDateCheck = new Date(fromDate);
            if (fromDateCheck > toDateCheck) {
                alert("Please Select Proper Date Range");
                $("#fromDate, #toDate").val('');
            } else {
                $('#fromDate_export').val(fromDate);
                $('#toDate_export').val(toDate);
                $('#filterData_export').val("custom");
                loadDashboardDataForPurchase("custom", toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
            }
        }
    });

    //used to fetch date from date filters
    function getInputDates() {
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        return {
            'filter_date': $("#dashboard_filter_dates").val(),
            'toDate': $("#toDate").val(),
            'fromDate': $("#fromDate").val(),
            'sales_type': primary_secondary_sales,
            'buid': $('#dc_all_legalentity').val()
        };
    }



    // All the general Features of all the Grids are written here
    function customigGridFeatures(customPageSize) {
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

    //used to update dashboard value if any of filter selected 
    function loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales) {
        $('[class="loader"]').show();
        $('[class="data_value"]').text(0);

        var inputData = { 'filter_date': filterData, 'fromDate': fromDate, 'toDate': toDate, 'buid': buid, "brandid": brandid, "manufid": manufid, 'productgrpid': productgrpid, 'categoryid': categoryid, 'primary_secondary_sales': primary_secondary_sales };
        console.log(inputData);
        var custom_load_url = (window.location.pathname == "/purchase/") ? "/purchase" : "/purchase";
        var response = $.post(custom_load_url, inputData);
        response.done(function (data) {
            console.log("Data ");
            console.log(data);
            var mainGridData = {};
            if (data.PCDData != undefined) {
                mainGridData = data.PCDData;
            }
            //sales type 
            if (data.primarysalesenable != undefined && data.primarysalesenable == 1) {
                console.log('apob===========primary sales enable');
                if ($("#primary_secondary_sales option[value='2']").length > 0) {
                    $("#primary_secondary_sales option[value='2']").remove();
                }
                if ($("#primary_secondary_sales option[value='3']").length > 0) {
                    $("#primary_secondary_sales option[value='3']").remove();
                }
                if ($("#primary_secondary_sales option[value='1']").length <= 0) {
                    var option1 = $("<option/>", { value: '1', text: 'Primary Sales' });
                    $('#primary_secondary_sales').append(option1, [0]);
                }
            } else if (data.primarysalesenable != undefined && data.primarysalesenable == 2) {
                console.log('Intermediate===========secondary sales enable');
                $("#primary_secondary_sales option[value='1']").remove();
                if ($("#primary_secondary_sales option[value='3']").length <= 0) {
                    var option1 = $("<option/>", { value: '3', text: 'Intermediate Sales' });
                    $('#primary_secondary_sales').append(option1, [0]);
                }
                if ($("#primary_secondary_sales option[value='2']").length <= 0) {
                    var option2 = $("<option/>", { value: '2', text: 'Secondary Sales' });
                    $('#primary_secondary_sales').append(option2, [1]);
                }
            } else if (data.primarysalesenable != undefined && data.primarysalesenable == 3) {
                console.log('secondary sales===========enable');
                if ($("#primary_secondary_sales option[value='3']").length > 0) {

                    $("#primary_secondary_sales option[value='3']").remove();
                }
                if ($("#primary_secondary_sales option[value='1']").length > 0) {

                    $("#primary_secondary_sales option[value='1']").remove();
                }
            } else if (data.primarysalesenable != undefined && data.primarysalesenable == 0) {
                console.log('enable all sales');
                if ($("#primary_secondary_sales option[value='1']").length <= 0) {
                    var option1 = $("<option/>", { value: '1', text: 'Primary Sales' });
                    $('#primary_secondary_sales').append(option1, [0]);
                }
                if ($("#primary_secondary_sales option[value='3']").length <= 0) {
                    var option2 = $("<option/>", { value: '3', text: 'Intermediate Sales' });
                    $('#primary_secondary_sales').append(option2, [1]);
                }
                if ($("#primary_secondary_sales option[value='2']").length <= 0) {
                    var option3 = $("<option/>", { value: '2', text: 'Secondary Sales' });
                    $('#primary_secondary_sales').append(option3, [2]);
                }
            } else if ($("#primary_secondary_sales option[value='1']").length <= 0) {
                var option1 = $("<option/>", { value: '1', text: 'Primary Sales' });
                $('#primary_secondary_sales').append(option1, [0]);
            }
            if (data.primary_secondary_sales != undefined && $('#primary_secondary_sales').length > 0) {
                $("#primary_secondary_sales").select2('val', data.primary_secondary_sales);
            }
            //End of sales filter
            $.each(mainGridData, function (key, value) {
                var test = key.toLowerCase();
                var temp = test.replace(/[^A-Z0-9]/ig, "_");
                if (temp == "purchasedashboard") {
                    $.each(value, function (key2, dashboard) {
                        $.each(dashboard, function (key3, dashboardData) {
                            var key3 = dashboardData.key;
                            var val3 = dashboardData.val;
                            var per3 = dashboardData.per;
                            if (key3 != null) {
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
            reloadPurchaseGridData();

        });

        // Here I`m adding the update for the 1st PO tabs.
        grn_details_reload = false;
        inventory_details_reload = false;


        // The below 2 Lines, hide all the tab Headings, expect the FF tab
        $("#dashboard_purchase_list").parent().addClass("active");
        $('[class="loader"]').hide();
    }

    $(function () {
        // getPOOrderDetailsList();
        reloadPurchaseGridData();
    });


    function reloadPurchaseGridData() {
        $.ajax({
            url: '/purchase/salesDetails',
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
                $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) {
                var res1 = getInputDates();
                if ($("#dashboard_purchase_list").data("igGrid") != null) {


                    $("#dashboard_purchase_list").igGrid("destroy");

                }

                $('#dashboard_purchase_list').igGrid({
                    width: "100%",
                    dataSource: response.data,

                    columns: [
                        { headerText: "DC Name", key: "dc", dataType: "string", width: "125px" },
                        { headerText: "FC Name", key: "fc", dataType: "string", width: "125px" },
                        { headerText: "State", key: "state", dataType: "string", width: "125px" },
                        { headerText: "City", key: "city", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${city} </div>' },
                        // { headerText: "Date", key: "date", dataType: "date", format: "MM-dd-yyyy", width: "125px", template: '<div class="textCenterAlign"> ${date} </div>' },
                        {
                            headerText: 'PO Value', key: 'po_value', dataType: 'number', columnCssClass: "alignLeft", headerCssClass: "alignLeft", width: "120px", formatter: function (val, data) {
                                return $.ig.formatter(val, "number", "0");
                            }
                        },
                        {
                            headerText: 'GRN Value', key: 'grn_value', dataType: 'number', columnCssClass: "alignLeft", headerCssClass: "alignLeft", width: "120px", formatter: function (val, data) {
                                return $.ig.formatter(val, "number", "0");
                            }
                        },
                        { headerText: 'GRN Count', key: 'grn_count', dataType: 'string', width: "100px" },
                        { headerText: 'PO Count', key: 'po_count', dataType: 'string', width: "100px" },
                        { headerText: "Pending Po Count", key: "pending_po_count", dataType: "number", width: "150px" },
                        { headerText: "Pending Po Value", key: "pending_po_value", dataType: "number", width: "150px" },
                        { headerText: "Opening Stock", key: "opening_stock_value", dataType: "number", width: "150px" },
                        // { headerText: "Credit Limit", key: "Credit_limit", dataType: "number", width: "100px", template: '<div class="textCenterAlign"> ${Credit_limit} </div>' },
                        // { headerText: "Outstanding", key: "Outstanding", dataType: "number", width: "100px", template: '<div class="textCenterAlign"> ${Outstanding} </div>' },
                        // { headerText: "Cashback", key: "Cashback_Orders", dataType: "number", width: "100px", template: '<div class="textCenterAlign"> ${Cashback} </div>' },
                        // { headerText: 'Created On', key: 'Created_On', dataType: 'date', formatter: function (val, data) { return $.ig.formatter(val, "date", "dd/MM/yyyy"); }, width: '110px' },
                        // { headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '150px' },
                    ],
                    features: [
                        {
                            name: "Sorting",
                            type: "local",
                            columnSettings: [
                                { columnKey: 'Stockist_Name', allowSorting: false },
/*                    {columnKey: 'Stockist_Code', allowSorting: true },
*/                              { columnKey: 'Total_Orders', allowSorting: true },
                                { columnKey: 'upc', allowSorting: true },
                                { columnKey: 'StateName', allowSorting: true },
                                { columnKey: 'CustomerTypeName', allowSorting: true },
                                { columnKey: 'price', allowFiltering: true },
                                { columnKey: 'ptr', allowFiltering: true },
                                { columnKey: 'effective_date', allowSorting: true },
                                //{columnKey: 'cpEnabled', allowSorting: false },
                            ]
                        },
                        {
                            name: "Filtering",
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings: [
                                { columnKey: 'SNO', allowFiltering: false },
                                { columnKey: 'product_title', allowFiltering: true },
                                { columnKey: 'seller_sku', allowFiltering: true },
                                { columnKey: 'upc', allowFiltering: true },
                                { columnKey: 'ptr', allowFiltering: true },
                                { columnKey: 'StateName', allowFiltering: true },
                                { columnKey: 'CustomAction', allowFiltering: false },
                                { columnKey: 'effective_date', allowFiltering: true },
                            ]
                        },
                        // {
                        //     name: "Summaries",
                        //     type: "local",
                        //     showDropDownButton: false,
                        //     summariesCalculated: function (evt, ui) {
                        //         var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                        //         listPricesummaryCells.each(function () {
                        //             if ($(this).text() != "") {
                        //                 $(this).text($(this).text().substr(2));
                        //                 $(this).css({ 'font-weight': '800' });
                        //             }
                        //         });
                        //     },
                        //     columnSettings: [
                        //         { columnKey: "FC Name", allowSummaries: false },
                        //         { columnKey: "DC Name", allowSummaries: false },
                        //         { columnKey: "State", allowSummaries: false },
                        //         {
                        //             columnKey: "Opening_Stock", allowSummaries: true, summaryOperands:
                        //                 [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                        //         },
                        //         {
                        //             columnKey: "Pending_PO", allowSummaries: true, summaryOperands:
                        //                 [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                        //         },
                        //         {
                        //             columnKey: "Pending_GRN_Value", allowSummaries: true, summaryOperands:
                        //                 [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                        //         },
                        //         {
                        //             columnKey: "Outstanding", allowSummaries: true, summaryOperands:
                        //                 [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                        //         },
                        //         {
                        //             columnKey: "Cashback_Orders", allowSummaries: true, summaryOperands:
                        //                 [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                        //         },
                        //         { columnKey: "GRN", allowSummaries: false },
                        //         { columnKey: "PO", allowSummaries: false },
                        //         { columnKey: "Created_On", allowSummaries: false },
                        //         { columnKey: "Created_By", allowSummaries: false },
                        //         { columnKey: "Credit_limit", allowSummaries: true },
                        //         { columnKey: "PO_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                        //         { columnKey: "GRN_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                        //         { columnKey: "Item_Discounted", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },


                        //     ]
                        // },
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
                            name: "RowSelectors",
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

            },
        });
    }

    function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;

            // Summaries UI changes
            var id_text = "_summaries_footer_row_text_container_sum_";
            $("#" + selectedId + "_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#" + selectedId + id_text + newText).attr("class", "summariesStyle").text($("#" + selectedId + id_text + newText).text().replace(/\s=\s/g, ''));

            // S.No and Column Title Adjustments below
            if (columns[idx].dataType == "number" || columns[idx].dataType == "double") {
                var isDecimal = columns[idx].headerText.substring(0, 2);
                if (isDecimal === "1_") {
                    var columnText =
                        (columns[idx].headerText.substring(columns[idx].headerText.length - 4) === "_Per") ? columns[idx].headerText.replace("_Per", " %").substring(2) : columns[idx].headerText.substring(2);
                    columnText = (columnText.substring(0, 2) === "N_") ? columnText.substring(2) : columnText;
                    $("#" + selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                        .html("<p style='text-align: right !important; margin: 0px 5px !important;'>" + columnText.replace(/_/g, ' ') + "</p>")
                        .attr('title', columnText.replace(/_/g, ' '));
                }
            } else if (columns[idx].dataType == "string") {
                $("#" + selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.replace(/_/g, ' '))
                    .text(newText.replace(/_/g, ' '));
            } else if (columns[idx].dataType == "date") {
                $("#" + selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.substring(2).replace(/_/g, ' '))
                    .text(newText.substring(2).replace(/_/g, ' '));
            }

        }
    }


    $("#dashboard_purchase_list").on("iggriddatarendered", function (event, args) {
        $("#dashboard_purchase_list_FC_Name > span.ui-iggrid-headertext").attr('title', "FC Name");
        $("#dashboard_purchase_list_Pending_GRN > span.ui-iggrid-headertext").attr('title', "Pending GRN");
        $("#dashboard_purchase_list_Pending_GRN_Value > span.ui-iggrid-headertext").attr('title', "Pending GRN Value");
        $("#dashboard_purchase_list_GRN > span.ui-iggrid-headertext").attr('title', "GRN");
        $("#dashboard_purchase_list_PO > span.ui-iggrid-headertext").attr('title', "PO");
        $("#dashboard_purchase_list_PO_Value > span.ui-iggrid-headertext").attr('title', "PO_Value");
        $("#dashboard_purchase_list_GRN_Value > span.ui-iggrid-headertext").attr('title', "GRN_Value");
        $("#dashboard_purchase_list_Outstanding > span.ui-iggrid-headertext").attr('title', "Outstanding");
        $("#dashboard_purchase_list_Opening_Stock > span.ui-iggrid-headertext").attr('title', "Opening Stock");

        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        var columns = $("#dashboard_purchase_list").igGrid("option", "columns");
        formatigGridContent(columns, "dashboard_purchase_list");
    });



    function getPOOrderDetailsList() {
        $.ajax({
            url: load_url + 'getpoorderdetails',
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
                $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) {
                if (response.length > 0) {
                    $('#po_order_details_list_error').addClass("hideError");
                    $('#po_order_details_list_table').css("display", "block");
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: [
                            { columnKey: "PO_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },

                            { columnKey: "PO_Code", allowSummaries: false },
                            { columnKey: "Supplier", allowSummaries: false },
                            { columnKey: "DC", allowSummaries: false },
                            { columnKey: "Validity", allowSummaries: false },
                            { columnKey: "PO_Date", allowSummaries: false },
                            { columnKey: "PO_Status", allowSummaries: false }
                        ]
                    });
                    $('#purchasedashboard_po_order_details_list').igGrid({

                        dataSource: response,
                        autoGenerateColumns: false,
                        width: "100%",
                        columns: [
                            { headerText: 'PO Code', key: 'PO_Code', dataType: 'string', width: "150px" },
                            { headerText: 'Supplier', key: 'Supplier', dataType: 'string', width: "150px" },
                            { headerText: 'DC', key: 'DC', dataType: 'string', width: "120px" },
                            { headerText: 'Validity', key: 'Validity', dataType: 'string', width: "150px" },
                            { headerText: 'PO Date', key: 'PO_Date', dataType: 'string', width: "150px" },
                            {
                                headerText: 'PO Value', key: 'PO_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "150px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            { headerText: 'PO Status', key: 'PO_Status', dataType: 'string', width: "150px" }

                        ],
                        features: customFeatures
                    });
                }
                else {

                    $('#po_order_details_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#po_order_details_list_table')
                        .css("display", "none");
                }
            },
            error: function () {
                $('po_order_details_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>PO Order Details</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#po_order_details_list_table')
                    .css("display", "none");
            }
        });

    }

    $("#purchasedashboard_po_order_details_list").on("iggriddatarendered", function (event, args) {
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#purchasedashboard_po_order_details_list_PO_Code > span.ui-iggrid-headertext").attr('title', "PO Code");
        $("#purchasedashboard_po_order_details_list_Supplier> span.ui-iggrid-headertext").attr('title', "Supplier");
        $("#purchasedashboard_po_order_details_list_DC> span.ui-iggrid-headertext").attr('title', "DC");
        $("#purchasedashboard_po_order_details_list_Validity> span.ui-iggrid-headertext").attr('title', "Validity");
        $("#purchasedashboard_po_order_details_list_PO_Date > span.ui-iggrid-headertext").attr('title', "PO Date");
        $("#purchasedashboard_po_order_details_list_PO_Value > span.ui-iggrid-headertext").attr('title', "PO Value");
        $("#purchasedashboard_po_order_details_list_PO_Status > span.ui-iggrid-headertext").attr('title', "PO Status");
        // Sumaries related UI changes on Dashboard
        $("#purchasedashboard_po_order_details_list_summaries_footer_row_icon_container_sum_PO_Value"


        ).remove();

        var id_text = "#purchasedashboard_po_order_details_list_summaries_footer_row_text_container_sum_";

        $(id_text + "PO_Value").attr("class", "summariesStyle").text($(id_text + "PO_Value").text().replace(/\s=\s/g, ''));

    });

    function getGRNDetailsList() {
        $.ajax({
            url: load_url + 'getgrndetails',
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
                $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) {
                if (response.length > 0) {
                    $('#grn_details_list_error').addClass("hideError");
                    $('#grn_details_list_table').css("display", "block");
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: [
                            { columnKey: "PO_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "GRN_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "Item_Discounted", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "GRN", allowSummaries: false },
                            { columnKey: "PO", allowSummaries: false },
                            { columnKey: "Supplier", allowSummaries: false },
                            { columnKey: "Created_On", allowSummaries: false },
                            { columnKey: "Created_By", allowSummaries: false },
                            { columnKey: "Ref", allowSummaries: false },
                            { columnKey: "Invoice", allowSummaries: false }
                        ]
                    });
                    $('#purchasedashboard_grn_details_list').igGrid({

                        dataSource: response,
                        autoGenerateColumns: false,
                        width: "100%",
                        columns: [
                            { headerText: 'GRN', key: 'GRN', dataType: 'string', width: "150px" },
                            { headerText: 'PO', key: 'PO', dataType: 'string', width: "150px" },
                            { headerText: 'Supplier', key: 'Supplier', dataType: 'string', width: "150px" },
                            { headerText: 'Created On', key: 'Created_On', dataType: 'date', formatter: function (val, data) { return $.ig.formatter(val, "date", "dd/MM/yyyy"); }, width: '110px' },
                            { headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '150px' },
                            { headerText: 'Ref', key: 'Ref', dataType: 'string', width: "120px" },
                            { headerText: 'Invoice', key: 'Invoice', dataType: 'string', width: "120px" },
                            {
                                headerText: 'PO Value', key: 'PO_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'GRN Value', key: 'GRN_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Item Discounted', key: 'Item_Discounted', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            }


                        ],
                        features: customFeatures
                    });
                }
                else {

                    $('#grn_details_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#grn_details_list_table')
                        .css("display", "none");
                }
            },
            error: function () {
                $('grn_details_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>GRN Details</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#grn_details_list_table')
                    .css("display", "none");
            }
        });

    }

    $("#purchasedashboard_grn_details_list").on("iggriddatarendered", function (event, args) {

        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#purchasedashboard_grn_details_list_GRN > span.ui-iggrid-headertext").attr('title', "GRN");
        $("#purchasedashboard_grn_details_list_PO> span.ui-iggrid-headertext").attr('title', "PO");
        $("#purchasedashboard_grn_details_list_Supplier > span.ui-iggrid-headertext").attr('title', "Supplier");
        $("#purchasedashboard_grn_details_list_Created_On > span.ui-iggrid-headertext").attr('title', "Created On");
        $("#purchasedashboard_grn_details_listt_Created_By > span.ui-iggrid-headertext").attr('title', "Created By");
        $("#purchasedashboard_grn_details_list_Ref > span.ui-iggrid-headertext").attr('title', "Ref");
        $("#purchasedashboard_grn_details_list_Invoice > span.ui-iggrid-headertext").attr('title', "Invoice");
        $("#purchasedashboard_grn_details_list_PO_Value > span.ui-iggrid-headertext").attr('title', "PO Value");
        $("#purchasedashboard_grn_details_list_GRN_Value > span.ui-iggrid-headertext").attr('title', "GRN Value");
        $("#purchasedashboard_grn_details_list_Item_Discounted > span.ui-iggrid-headertext").attr('title', "Item Discounted");


        // Sumaries related UI changes on Dashboard
        $("#purchasedashboard_grn_details_list_summaries_footer_row_icon_container_sum_PO_Value , " +
            "#purchasedashboard_grn_details_list_summaries_footer_row_icon_container_sum_GRN_Value , " +
            "#purchasedashboard_grn_details_list_summaries_footer_row_icon_container_sum_Item_Discounted "
        ).remove();

        var id_text = "#purchasedashboard_grn_details_list_summaries_footer_row_text_container_sum_";

        $(id_text + "PO_Value").attr("class", "summariesStyle").text($(id_text + "PO_Value").text().replace(/\s=\s/g, ''));
        $(id_text + "GRN_Value").attr("class", "summariesStyle").text($(id_text + "GRN_Value").text().replace(/\s=\s/g, ''));
        $(id_text + "Item_Discounted").attr("class", "summariesStyle").text($(id_text + "Item_Discounted").text().replace(/\s=\s/g, ''));

    });

    function getInventoryDetailsList() {
        $.ajax({
            url: load_url + 'getinventorydetails',
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
                $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) {
                if (response.length > 0) {
                    $('#inventory_details_list_error').addClass("hideError");
                    $('#inventory_details_list_table').css("display", "block");
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: [
                            { columnKey: "DC", allowSummaries: false },
                            { columnKey: "1_N_Opening_Stock", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Stock_In_DC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Invoiced", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Missing_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_SIT_DC_Hub", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_SIT_Hub_DC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Stock_in_Hub", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Out_for_Delivery", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Hold_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_Damaged_Value", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] },
                            { columnKey: "1_N_PRAD", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }] }

                        ]
                    });
                    $('#purchasedashboard_inventory_details_list').igGrid({

                        dataSource: response,
                        autoGenerateColumns: false,
                        width: "100%",
                        columns: [
                            { headerText: 'DC', key: 'DC', dataType: 'string', width: "150px" },
                            {
                                headerText: 'Opening Stock', key: '1_N_Opening_Stock', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Stock In DC', key: '1_N_Stock_In_DC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Invoiced', key: '1_N_Invoiced', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Missing Value', key: '1_N_Missing_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'SIT DC-HUB', key: '1_N_SIT_DC_Hub', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'SIT HUB-DC', key: '1_N_SIT_Hub_DC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Stock In HUB', key: '1_N_Stock_in_Hub', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Out For Delivery', key: '1_N_Out_for_Delivery', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Hold Value', key: '1_N_Hold_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },
                            {
                                headerText: 'Damaged Value', key: '1_N_Damaged_Value', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            },

                            {
                                headerText: 'PRAD', key: '1_N_PRAD', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "alignRight", width: "120px", formatter: function (val, data) {
                                    return $.ig.formatter(val, "number", "0.00");
                                }
                            }


                        ],
                        features: customFeatures
                    });
                }
                else {

                    $('#inventory_details_list_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#inventory_details_list_table')
                        .css("display", "none");
                }
            },
            error: function () {
                $('inventory_details_list_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>Inventory Details</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#inventory_details_list_table')
                    .css("display", "none");
            }
        });

    }

    $("#purchasedashboard_inventory_details_list").on("iggriddatarendered", function (event, args) {

        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#purchasedashboard_inventory_details_list_DC> span.ui-iggrid-headertext").attr('title', "DC");
        $("#purchasedashboard_inventory_details_list_1_N_Opening_Stock> span.ui-iggrid-headertext").attr('title', "Opening Stock");
        $("#purchasedashboard_inventory_details_list_1_N_Stock_In_DC> span.ui-iggrid-headertext").attr('title', "Stock In DC");
        $("#purchasedashboard_inventory_details_list_1_N_Invoiced> span.ui-iggrid-headertext").attr('title', "Invoiced");
        $("#purchasedashboard_inventory_details_list_1_N_Missing_Value> span.ui-iggrid-headertext").attr('title', "Missing Value");
        $("#purchasedashboard_inventory_details_list_1_N_SIT_DC_Hub> span.ui-iggrid-headertext").attr('title', "SIT DC-HUB");
        $("#purchasedashboard_inventory_details_list_1_N_SIT_Hub_DC> span.ui-iggrid-headertext").attr('title', "SIT HUB-DC");
        $("#purchasedashboard_inventory_details_list_1_N_Stock_in_Hub> span.ui-iggrid-headertext").attr('title', "Stock In HUB");
        $("#purchasedashboard_inventory_details_list_1_N_Out_for_Delivery> span.ui-iggrid-headertext").attr('title', "Out For Delivery");
        $("#purchasedashboard_inventory_details_list_1_N_Hold_Value> span.ui-iggrid-headertext").attr('title', "Hold Value");
        $("#purchasedashboard_inventory_details_list_1_N_Damaged_Value> span.ui-iggrid-headertext").attr('title', "Damaged Value");
        $("#purchasedashboard_inventory_details_list_1_N_PRAD> span.ui-iggrid-headertext").attr('title', "PRAD");
        // Sumaries related UI changes on Dashboard
        $("#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Opening_Stock , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Stock_In_DC , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Invoiced , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Missing_Value , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_SIT_DC_Hub , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_SIT_Hub_DC , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Stock_in_Hub , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Out_for_Delivery , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Hold_Value , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_Damaged_Value , " +
            "#purchasedashboard_inventory_details_list_summaries_footer_row_icon_container_sum_1_N_PRAD "
        ).remove();

        var id_text = "#purchasedashboard_inventory_details_list_summaries_footer_row_text_container_sum_";

        $(id_text + "1_N_Opening_Stock").attr("class", "summariesStyle").text($(id_text + "1_N_Opening_Stock").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Stock_In_DC").attr("class", "summariesStyle").text($(id_text + "1_N_Stock_In_DC").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Invoiced").attr("class", "summariesStyle").text($(id_text + "1_N_Invoiced").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Missing_Value").attr("class", "summariesStyle").text($(id_text + "1_N_Missing_Value").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_SIT_DC_Hub").attr("class", "summariesStyle").text($(id_text + "1_N_SIT_DC_Hub").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_SIT_Hub_DC").attr("class", "summariesStyle").text($(id_text + "1_N_SIT_Hub_DC").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Stock_in_Hub").attr("class", "summariesStyle").text($(id_text + "1_N_Stock_in_Hub").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Out_for_Delivery").attr("class", "summariesStyle").text($(id_text + "1_N_Out_for_Delivery").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Hold_Value").attr("class", "summariesStyle").text($(id_text + "1_N_Hold_Value").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_Damaged_Value").attr("class", "summariesStyle").text($(id_text + "1_N_Damaged_Value").text().replace(/\s=\s/g, ''));
        $(id_text + "1_N_PRAD").attr("class", "summariesStyle").text($(id_text + "1_N_PRAD").text().replace(/\s=\s/g, ''));

    });

    // Custom Ajax for all the General igGrid Table Calls
    function makeAjaxCallForigGrid(customUrl, selectedId, selectedTabName) {
        $.ajax({
            url: load_url + customUrl,
            type: 'POST',
            data: getInputDates(),
            dataType: "json",
            beforeSend: function () {
                $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) {
                if (response.headers.length > 0 && response.data.length > 0) {
                    // Hiding Error as Data is Present
                    $('#' + selectedId + '_error').addClass("hideError");
                    $('#' + selectedId + '_table').css("display", "block");

                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: result.columnSummaries
                    });

                    $('#dashboard_' + selectedId).igGrid({
                        dataSource: response.data,
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures,
                    });
                }
                else {
                    $('#' + selectedId + '_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#' + selectedId + '_table')
                        .css("display", "none");
                }
            },
            error: function () {
                $('#' + selectedId + '_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>" + selectedTabName + "</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#' + selectedId + '_table')
                    .css("display", "none");
            }
        });
    }

    //business unit filter
    $('#dc_all_legalentity').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        $('#exportbuId').val(buid);
        var categoryid = '';
        var brandid = '';
        var manufid = '';
        var productgrpid = '';
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        $('#exportflag').val(primary_secondary_sales);
        $("#brands_le_id").empty();
        $("#product_group_ids").empty();
        $('#category').select2("val", 0);
        var option1 = $("<option/>", { value: '', text: 'Please Select Product Group' });
        var option2 = $("<option/>", { value: '', text: 'Please Select Brands' });
        $('#product_group_ids').append(option1);
        $('#brands_le_id').append(option2);
        $("#product_group_ids").select2("val", '');
        $("#brands_le_id").select2("val", '');
        $('#manufacturer_le_ids').select2("val", '');
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            $('#filterData_export').val(filterData);
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            $('#fromDate_export').val(fromDate);
            $('#toDate_export').val(toDate);
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
        var token = $('#csrf-token').val();
        $.ajax({
            headers: { 'X-CSRF-TOKEN': token },
            url: "/salesorders/setbuid",
            type: 'POST',
            data: {
                buid: buid,
            },
            dataType: 'json', // added data type
            success: function (res) {

            }
        });
    });

    //brand filter
    $('#brands_le_id').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = $('#brands_le_id').val();
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = '';
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
    });

    //category filter 
    $('#category').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $(this).val();
        var brandid = '';
        var manufid = '';
        var productgrpid = '';
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        var option1 = $("<option/>", { value: '', text: 'Please Select Product Group' });
        var option2 = $("<option/>", { value: '', text: 'Please Select Brands' });
        $('#product_group_ids').append(option1);
        $('#brands_le_id').append(option2);
        $('#manufacturer_le_ids').select2("val", '');
        $('#brands_le_id').select2("val", '');
        $('#product_group_ids').select2("val", '');
        //$("#manufacturer_le_ids").empty();
        $("#brands_le_id").empty();
        $("#product_group_ids").empty();
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
    });

    //manufacture filter
    $('#manufacturer_le_ids').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads      
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = '';
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = '';
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
    });

    //product group filter
    $('#product_group_ids').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = $('#brands_le_id').val();
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = $('#product_group_ids').val();
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
    });

    //sales option filter
    $('#primary_secondary_sales').change(function () {
        // If the thing(date) is not Custom,
        // then the predefined Dates loads
        $('[class="loader"]').show();
        var buid = $('#dc_all_legalentity').val();
        var categoryid = $('#category').val();
        var brandid = $('#brands_le_id').val();
        var manufid = $('#manufacturer_le_ids').val();
        var productgrpid = $('#product_group_ids').val();
        if ($('#primary_secondary_sales').length) {
            var primary_secondary_sales = $('#primary_secondary_sales').val();
        } else {
            var primary_secondary_sales = 2;
        }
        $('#exportflag').val(primary_secondary_sales);
        var filterData = $('#dashboard_filter_dates').val();
        if (filterData != "custom") {
            $("#customDatesView").addClass("customDateArea");
            $("#fromDate, #toDate").val('');
            loadDashboardDataForPurchase(filterData, 0, 0, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        } else {
            $("#customDatesView").removeClass("customDateArea");
            var toDate = $('#toDate').val();
            var fromDate = $('#fromDate').val();
            loadDashboardDataForPurchase(filterData, toDate, fromDate, buid, brandid, manufid, productgrpid, categoryid, primary_secondary_sales);
        }
    });

    //after selecting manufacturer we 
    $('#manufacturer_le_ids').change(function () {
        var manufid = $(this).val();
        var token = $('#csrf-token').val();
        $('[class="loader"]').show();

        $.ajax({
            headers: { 'X-CSRF-TOKEN': token },
            url: "/stockist/getbrands",
            type: "POST",
            data: 'manufid=' + manufid,
            dataType: 'json',
            success: function (response) {

                $("#brands_le_id").empty();
                $("#product_group_ids").empty();
                $("#brands_le_id").html(response.res);
                var option = $("<option/>", { value: '', text: 'Please Select Product Group' });
                $('#product_group_ids').append(option);
                $("#brands_le_id").select2("val", '');
                $("#product_group_ids").select2("val", '');
                //$('[class="loader"]').hide();

            },
            error: function (response) {
                alert("Status: " + response);
                $('[class="loader"]').hide();

            }

        });
    });

    $('#brands_le_id').change(function () {
        var brandid = $(this).val();
        var token = $('#csrf-token').val();
        $('[class="loader"]').show();

        $.ajax({
            headers: { 'X-CSRF-TOKEN': token },
            url: "/stockist/getproductgroupbybrand",
            type: "POST",
            data: 'brandid=' + brandid,
            dataType: 'json',
            success: function (response) {

                $("#product_group_ids").empty();
                $("#product_group_ids").html(response.res);
                $("#product_group_ids").select2("val", '');
                //$('[class="loader"]').hide();

            },

            error: function (res) {
                alert("Status: " + err);
                $('[class="loader"]').hide();

            }
        });
    });

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('#csrf-token').val() },
        url: '/getCategoryList',
        type: 'GET',
        dataType: "text",
        success: function (rs) {
            $("#category").html(rs);
            $('.prod_class').css('color', '#0174DF !important');
            $("#category").select2().select2('val', 0);
        },
        error: function (res) {
            alert("Status: " + res);

        }
    });

    $('[class="loader"]').hide();

});