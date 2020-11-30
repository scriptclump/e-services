

$(document).ready(function () {


     // Custom Date Formats
     $('#customDatePickerZone .input-daterange').datepicker({
          format: "dd/mm/yyyy",
          endDate: "today",
          todayHighlight: true
     });



     // Custom Date Formats
     $('#customDatePicker .input-daterange').datepicker({
          format: "yyyy/mm/dd",
          endDate: "today",
          todayHighlight: true
     });
     //setting default value for further ajax request
     $.ajaxSetup({
          headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
          dataType: 'JSON'
     });

     //Date select from custom filter
     $('#dashboard_filter_dates').change(function () {// date filter 
          // If the thing(date) is not Custom,
          // then the predefined Dates loads 
          var filterData = $(this).val();
          //fetching buid details 
          var buid = $('#dc_all_legalentity').val();
          //checking weather user have select custom date filter
          if (filterData != "custom") {
               $("#customDatesView").addClass("customDateArea");
               $("#fromDate, #toDate").val('');
               loadDashboardDataForBusinessPartners(filterData, 0, 0, buid);
          } else {
               // if user have selected custom date filter then we were removing customDateFIlter 
               $("#customDatesView").removeClass("customDateArea");
          }
     });

     $('#dashboard_export').click(function () {// date filter 
          console.log("hello")
          //checking weather user have select custom date filter
          // $('#dashboard_export').css("display", "none");
          $('#customDateArea').css("display", "block");
          //  $("#dashboard_export").addClass("customDateArea");
          $("#fromDate, #toDate").val('');
     });

     //used to return filtered date with buid
     function getInputDates() {
          return {
               'filter_date': $("#dashboard_filter_dates").val(),
               'toDate': $("#toDate").val(),
               'fromDate': $("#fromDate").val(),
               //'sales_type': primary_secondary_sales,
               'buid': $('#dc_all_legalentity').val()
          };
     }

     function getInputDatesForExport() {
          return {
               'filter_date': 'nothing',
               'toDate': $("#todate").val(),
               'fromDate': $("#fromdate").val(),
               //'sales_type': primary_secondary_sales,
               'buid': $('#dc_all_legalentity').val()
          };
     }

     //Load data based on custom date 
     $('#customDateWidthSubmit').click(function () {
          var toDate = $('#toDate').val();
          var fromDate = $('#fromDate').val();
          var buid = $('#dc_all_legalentity').val();
          if ((toDate == undefined || toDate == '') || (fromDate == undefined || fromDate == '')) {
               alert("Please Select Valid To & From Dates");
               $("#fromDate, #toDate").val('');
          } else {
               toDateCheck = new Date(toDate);
               fromDateCheck = new Date(fromDate);
               if (fromDateCheck > toDateCheck) {
                    alert("Please Select Proper Date Range");
                    $("#fromDate, #toDate").val('');
               } else {
                    loadDashboardDataForBusinessPartners("custom", toDate, fromDate, buid);
               }
          }
     });

     // //Load data based on custom date 
     // $('#ExportFilter').click(function () {
     //      var toDate = $('#todate').val();
     //      var fromDate = $('#fromdate').val();
     //      var buid = $('#dc_all_legalentity').val();
     //      console.log("buid", buid);
     //      if ((toDate == undefined || toDate == '') || (fromDate == undefined || fromDate == '')) {
     //           alert("Please Select Valid To & From Dates");
     //           $("#fromDate, #toDate").val('');
     //      } else {
     //           toDateCheck = new Date(toDate);
     //           fromDateCheck = new Date(fromDate);
     //           if (fromDateCheck > toDateCheck) {
     //                alert("Please Select Proper Date Range");
     //                $("#fromDate, #toDate").val('');
     //           } else {
     //                reloadGridDataForExport();
     //           }
     //           // reloadGridData();
     //      }
     // });

     //This function will return response from businessPartners api based  filterdata , toDate , fromDate and buid
     function loadDashboardDataForBusinessPartners(filterData, toDate, fromDate, buid) {
          $('[class="data_value"]').text(0);
          var inputData = { 'filter_date': filterData, 'fromDate': fromDate, 'toDate': toDate, 'buid': buid };
          var custom_load_url = (window.location.pathname == "/businessPartners") ? "/businessPartners" : "/";
          var response = $.post(custom_load_url, inputData);
          response.done(function (data) {
               var mainGridData = {};
               if (data.BusinessPartnerDatails != undefined) {
                    mainGridData = data.BusinessPartnerDatails;
               }

               $.each(mainGridData, function (key, value) {
                    var test = key.toLowerCase();
                    var temp = test.replace(/[^A-Z0-9]/ig, "_");
                    if (temp == "dashboard") {
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
               reloadGridData();
          });

          // The below 2 Lines, hide all the tab Headings, expect the FF tab
          $("#dashboard_partners_list").parent().addClass("active");
          $('[class="loader"]').hide();
     }


     $(function () {
          reloadGridData();
          // reloadGridDataForExport();
     });

     //block of code used to get salesDetails for tabular data from /businessPartners/stockistsales api
     function reloadGridData() {
          $.ajax({
               url: '/businessPartners/GridData',
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
                    if ($("#dashboard_partners_list").data("igGrid") != null) {
                         $("#dashboard_partners_list").igGrid("destroy");
                    }
                    $("#dashboard_partners_list").igGrid({
                         autoGenerateColumns: false,
                         renderCheckboxes: true,
                         columns: [
                              { headerText: "DC Name", key: "dc", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${dc} </div>' },
                              { headerText: "FC Name", key: "fc", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${fc} </div>' },
                              { headerText: "State", key: "state", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${state} </div>' },
                              { headerText: "City", key: "city", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${city} </div>' },
                              { headerText: "Date", key: "date", dataType: "date", format: "MM-dd-yyyy", width: "125px", template: '<div class="textCenterAlign"> ${date} </div>' },
                              { headerText: "Opening Stock", key: "opening_stock_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${opening_stock_value} </div>' },
                              { headerText: "Missing Stock", key: "missing_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${missing_value} </div>' },
                              { headerText: "Damage Stock", key: "damage_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${damage_value} </div>' },
                              { headerText: "Total Sales", key: "total_sale", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${total_sale} </div>' },
                              { headerText: "Total Invoiced", key: "invoice_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${invoice_total} </div>' },
                              { headerText: "Total Returned", key: "return_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${return_total} </div>' },
                              { headerText: "Total Collection", key: "collected_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${collected_total} </div>' },
                              { headerText: "GRN", key: "grn", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${grn} </div>' },
                              { headerText: "Debit", key: "debit", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${debit} </div>' },
                              { headerText: "Credit", key: "credit", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${credit} </div>' },
                              { headerText: "Closing Balance", key: "closing_balance", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${closing_balance} </div>' },
                              { headerText: "Closing Stock", key: "Closing_Stock", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${Closing_Stock} </div>' },
                              { headerText: "Profit Loss", key: "Profit_Loss", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${Profit_Loss} </div>' }

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
                                        { columnKey: 'StateName', allowFiltering: true },
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

     //block of code used to get salesDetails for tabular data from /businessPartners/stockistsales api
     function reloadGridDataForExport() {
          $.ajax({
               url: '/businessPartners/GridData',
               type: 'POST',
               data: getInputDatesForExport(),
               dataType: "json",
               beforeSend: function () {
                    $('#loader').show();
               },
               complete: function () {
                    $('#loader').hide();
               },
               success: function (response) {
                    if ($("#dashboard_partners_list").data("igGrid") != null) {
                         $("#dashboard_partners_list").igGrid("destroy");
                    }
                    $("#dashboard_partners_list").igGrid({
                         autoGenerateColumns: false,
                         renderCheckboxes: true,
                         columns: [
                              { headerText: "DC Name", key: "dc", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${dc} </div>' },
                              { headerText: "FC Name", key: "fc", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${fc} </div>' },
                              { headerText: "State", key: "state", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${state} </div>' },
                              { headerText: "City", key: "city", dataType: "string", width: "125px", template: '<div class="textCenterAlign"> ${city} </div>' },
                              { headerText: "Date", key: "date", dataType: "Date", format: "MM-dd-yyyy", width: "125px", template: '<div class="textCenterAlign"> ${date} </div>' },
                              { headerText: "Opening Stock", key: "opening_stock_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${opening_stock_value} </div>' },
                              { headerText: "Missing Stock", key: "missing_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${missing_value} </div>' },
                              { headerText: "Damage Stock", key: "damage_value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${damage_value} </div>' },
                              { headerText: "Total Sales", key: "total_sale", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${total_sale} </div>' },
                              { headerText: "Total Invoiced", key: "invoice_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${invoice_total} </div>' },
                              { headerText: "Total Returned", key: "return_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${return_total} </div>' },
                              { headerText: "Total Collection", key: "collected_total", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${collected_total} </div>' },
                              { headerText: "GRN", key: "grn", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${grn} </div>' },
                              { headerText: "Debit", key: "debit", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${debit} </div>' },
                              { headerText: "Credit", key: "credit", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${credit} </div>' },
                              { headerText: "Closing Stock", key: "Closing_Stock_Value", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${Closing_Stock_Value} </div>' },
                              { headerText: "Profit_Loss", key: "Profit_Loss", dataType: "number", width: "125px", template: '<div class="textCenterAlign"> ${Profit_Loss} </div>' }
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
                                        { columnKey: 'StateName', allowFiltering: true },
                                        { columnKey: 'CustomAction', allowFiltering: false },
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
                                                  $(this).css({ 'font-weight': '800' });
                                             }
                                        });
                                   },
                                   columnSettings: [
                                        { columnKey: "fc", allowSummaries: false },
                                        { columnKey: "dc", allowSummaries: false },
                                        { columnKey: "state", allowSummaries: false },
                                        { columnKey: "city", allowSummaries: false },
                                        { columnKey: "date", allowSummaries: false },
                                        {
                                             columnKey: "opening_stock_value", allowSummaries: false
                                        },
                                        {
                                             columnKey: "missing_value", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "damage_value", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "total_sale", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "invoice_total", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "return_total", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "collected_total", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "grn", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "debit", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "credit", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "closing_balance", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
                                        {
                                             columnKey: "Closing_Stock_Value", allowSummaries: false
                                        },
                                        {
                                             columnKey: "Profit_Loss", allowSummaries: true, summaryOperands:
                                                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]
                                        },
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

                    console.log("exporting to Excel");
                    $.ig.GridExcelExporter.exportGrid($("#dashboard_partners_list"), {
                         fileName: "BusinessReport",
                         gridFeatureOptions: { "sorting": "applied", "paging": "currentPage", "summaries": "applied" },
                    });
               }, error: function (response) {
                    alert('Error ');
               }
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



     $("#dashboard_partners_list").on("iggriddatarendered", function (event, args) {
          // $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
          $("#dashboard_partners_list_Parent > span.ui-iggrid-headertext").attr('title', "Parent");
          $("#dashboard_partners_list_Stockist_Name > span.ui-iggrid-headertext").attr('title', "Stockist Name");
          $("#dashboard_partners_list_Total_Invoiced > span.ui-iggrid-headertext").attr('title', "Total Invoiced");
          $("#dashboard_partners_list_Total_Returned > span.ui-iggrid-headertext").attr('title', "Total Returned");
          $("#dashboard_partners_list_Total_Cancelled > span.ui-iggrid-headertext").attr('title', "Total Cancelled");
          $("#dashboard_partners_list_Delivered > span.ui-iggrid-headertext").attr('title', "Delivered");
          $("#dashboard_partners_list_Total_Delivered > span.ui-iggrid-headertext").attr('title', "Total Delivered");
          $("#dashboard_partners_list_GRN > span.ui-iggrid-headertext").attr('title', "GRN");
          $("#dashboard_partners_list_Collected > span.ui-iggrid-headertext").attr('title', "Total Collected");
          $("#dashboard_partners_list_ClosingBalance > span.ui-iggrid-headertext").attr('title', "Closing Balance");
          $("#dashboard_partners_list_Opening_Stock > span.ui-iggrid-headertext").attr('title', "Opening Stock");
          $("#dashboard_partners_list_Closing_Stock_Value > span.ui-iggrid-headertext").attr('title', "Closing Stock");
          $("#dashboard_partners_list_Profit_Loss > span.ui-iggrid-headertext").attr('title', "Profit_Loss");
          $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
          var columns = $("#dashboard_partners_list").igGrid("option", "columns");
          formatigGridContent(columns, "dashboard_partners_list");
     });

     $('#dc_all_legalentity').change(function () {
          // If the thing(date) is not Custom,
          $('[class="loader"]').show();
          var buid = $('#dc_all_legalentity').val();
          var filterData = $('#dashboard_filter_dates').val();
          if (filterData != "custom") {
               $("#customDatesView").addClass("customDateArea");
               $("#fromDate, #toDate").val('');
               loadDashboardDataForBusinessPartners(filterData, 0, 0, buid);
          } else {
               $("#customDatesView").removeClass("customDateArea");
               var toDate = $('#toDate').val();
               var fromDate = $('#fromDate').val();
               loadDashboardDataForBusinessPartners(filterData, toDate, fromDate, buid);
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

     $('[class="loader"]').hide();

});