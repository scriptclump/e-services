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


    function getInputDates() {
        return {
            'PaytoDate': $("#PaytoDate").val(),
            'PayfromDate': $("#PayfromDate").val(),
            'CollecttoDate': $("#CollecttoDate").val(),
            'CollectfromDate': $("#CollectfromDate").val(),
        };
    }

    //Load data based on custom date 
    $('#PaymentVsCollections').click(function () {
        var toDate = $('#PaytoDate').val();
        var fromDate = $('#PayfromDate').val();
        var CollecttoDate = $("#CollecttoDate").val();
        var CollectfromDate = $("#CollectfromDate").val();
        if ((toDate == undefined || toDate == '') || (fromDate == undefined || fromDate == '') && (CollecttoDate == undefined || CollecttoDate == '') || (CollectfromDate == undefined || CollectfromDate == '')) {
            alert("Please select valid dates:");
            $("#fromDate, #toDate").val('');
        } else {
            toDateCheck = new Date(toDate);
            fromDateCheck = new Date(fromDate);
            collectToDateCheck = new Date(CollecttoDate)
            collectFromDateCheck = new Date(CollectfromDate)
            if ((fromDateCheck > toDateCheck) || (collectFromDateCheck > collectToDateCheck)) {
                alert("Please Select Proper Date Range");
                $("#PayfromDate,#PaytoDate ,#CollecttoDate ,#CollectfromDate").val('');
            } else {
                reloadGridData();
            }
        }
    });

    // $('#salesexport').click(function () {
    //     loadExcelData();
    // })


    // function loadExcelData() {
    //     $.ajax({
    //         url: '/collections/getexportdetails',
    //         type: 'POST',
    //         data: getInputDates(),
    //         dataType: "json",
    //         beforeSend: function () {
    //             $('#loader').show();
    //         },
    //         complete: function () {
    //             $('#loader').hide();
    //         },
    //     });
    // }
    $(function () {
        reloadGridData();
        // reloadGridDataForExport();
    });

    //block of code used to get salesDetails for tabular data from /businessPartners/stockistsales api
    function reloadGridData() {
        $.ajax({
            url: '/collections/GridData',
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
                if ($("#dashboard_list").data("igGrid") != null) {
                    $("#dashboard_list").igGrid("destroy");
                }
                $("#dashboard_list").igGrid({
                    autoGenerateColumns: false,
                    renderCheckboxes: true,
                    columns: [
                        { headerText: "State", key: "State", dataType: "string", width: "150px", template: '<div class="textCenterAlign"> ${State} </div>' },
                        { headerText: "City", key: "City", dataType: "string", width: "150px", template: '<div class="textCenterAlign"> ${City} </div>' },
                        { headerText: "Warehouse Type", key: "Warehouse_Type", dataType: "string", width: "150px", template: '<div class="textCenterAlign"> ${Warehouse_Type} </div>' },
                        { headerText: "Warehouse Name", key: "WarehouseName", dataType: "string", width: "200px", template: '<div class="textCenterAlign"> ${WarehouseName} </div>' },
                        { headerText: "Total Payment", key: "PaymentTotal", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${PaymentTotal} </div>' },
                        { headerText: "Total Invoiced", key: "InvoiceTotal", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${InvoiceTotal} </div>' },
                        { headerText: "Total Returned", key: "ReturnTotal", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${ReturnTotal} </div>' },
                        { headerText: "Net Sales", key: "NetSales", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${NetSales} </div>' },

                    ],
                    dataSource: response.data,
                    dataSourceType: "json",
                    responseDataKey: "results",
                    width: "100%",
                    tabIndex: 1,
                    features: [
                        {
                            name: "Sorting",
                            type: "local",
                            columnSettings: [
                                { columnKey: 'Stockist_Name', allowSorting: false },
                            ]
                        },
                        {
                            name: "Filtering",
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings: [
                                { columnKey: 'SNO', allowFiltering: false },
                                { columnKey: 'State', allowFiltering: true },
                                { columnKey: 'CustomAction', allowFiltering: false },
                            ]
                        },
                        {
                            recordCountKey: 'TotalRecordsCount',
                            chunkIndexUrlKey: 'page',
                            chunkSizeUrlKey: 'pageSize',
                            chunkSize: 10,
                            //name: 'AppendRowsOnDemand', 
                            name: 'Paging',
                            type: 'local',
                            pageSize: 10,
                            loadTrigger: 'auto',
                            //type: 'remote'


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

    //
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



    $("#dashboard_list").on("iggriddatarendered", function (event, args) {
        // $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#dashboard_list_Warehouse_Name > span.ui-iggrid-headertext").attr('title', "Warehouse Name");
        $("#dashboard_list_Total_Invoiced > span.ui-iggrid-headertext").attr('title', "Total Invoiced");
        $("#dashboard_list_Total_Returned > span.ui-iggrid-headertext").attr('title', "Total Returned");
        $("#dashboard_list_Total_Delivered > span.ui-iggrid-headertext").attr('title', "Total Delivered");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        var columns = $("#dashboard_list").igGrid("option", "columns");
        formatigGridContent(columns, "dashboard_list");
    });



    $('[class="loader"]').hide();


});