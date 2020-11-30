/*$('#reports_grid').igGrid({
    dataSource: '/ffreportsdata/getreports',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    expandColWidth: 0,
    enableUTCDates: true,
    columns: [
        {headerText: 'log_id', key: 'log_id', dataType: 'number',hidden:'true'},
        {headerText: 'FF Name', key: 'NAME', dataType: 'string'},
        {headerText: 'Call Date', key: 'created_at', dataType: 'string'},
        {headerText: 'Check In', key: 'Check_In', dataType: 'string'},
        {headerText: 'Check Out', key: 'Check_Out', dataType: 'string'},
        {headerText: 'Time Spent', key: 'Duration', dataType: 'string'},
    ],
    features: [

        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'order_date', allowSorting: false},
            ],
        },
        {
            name: 'Paging',
            type: 'local',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        },
    ],
    //primaryKey: 'product_id',
    width: '100%',
    height:'100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#reports_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#reports_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});*/
/*
$("#exportButton").on("click", function () {
    $.ig.GridExcelExporter.exportGrid($("#reports_grid"), {
        fileName: "Reports",
        dataExportMode: "allRows",
        gridFeatureOptions: {
        paging: "allRows",
        sorting: "applied"
        }
        //gridFeatureOptions: {"sorting": "applied", "filtering": "applied",  "summaries": "applied"}        
    });
});
*/

$(function () {

    makeAjaxCallForigGrid("/ffreportsdata/getreports","reports_grid");

            $("#reports_grid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#reports_grid").igGrid("option", "columns");
                formatigGridContent(columns,"reports_grid");
            });
});

function makeAjaxCallForigGrid(customUrl,selectedId) {
 $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: customUrl,
            type: 'POST',
            dataType:"json",                                          
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                if (response.headers.length > 0 && response.Records.length > 0) {
                    
                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                   
                    $('#'+selectedId).igGrid({
                        dataSource: response.Records,
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures,
                    });
                }
                /*else{
                    $('#'+selectedId+'_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#'+selectedId+'_table')
                        .css("display","none");
                }*/
            }
           
        });
}

function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;
            
            // Summaries UI changes
            /*var id_text = "_summaries_footer_row_text_container_sum_";
            $("#"+ selectedId +"_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#"+ selectedId + id_text + newText).attr("class", "summariesStyle").text($("#"+ selectedId + id_text + newText).text().replace(/\s=\s/g, ''));
*/
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
                cssClass = "alignCenter";
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
                if(headers[i].substring(0, 20) != "Legal_Entity_ID" && headers[i].substring(0, 20) != "State_ID"  && headers[i].substring(0, 20) != "Contact_Name" && headers[i].substring(0, 20) != "Phone_No" && headers[i].substring(0, 20) != "FullName" && headers[i].substring(0, 20) != "Is_Active" && headers[i].substring(0, 20) != "dc_id"){
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
                        columnSettings: [
                            { columnKey: "CustomAction", allowTooltips: false }
                        ]
                    }
                ];
    }