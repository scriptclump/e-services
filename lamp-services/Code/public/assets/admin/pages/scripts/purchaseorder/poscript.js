function getPoDetail(poId) {
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/po/poDetails/" + poId,
        type: "GET",
        data: {},
        dataType: 'json',
        success: function (response) {
            $('#tab11').html(response.message);
            $(".downloaddocs").imageBox();
            zoomOption();
        },
        error: function (response) {
        }
    });
}
function poInvoiceList(po_id) {
    $("#poInvoiceList").igGrid({
                        columns: [
                                {headerText: "Invoice ID", key: "invoiceId", dataType: "string", columnCssClass: "leftAlignment", width:"150px"},
            {headerText: "Inward ID", key: "inward_id", dataType: "string", columnCssClass: "leftAlignment", width:"150px"},
                        {headerText: "Billing Name", key: "billingName", dataType: "string", width:"150px"},
                        {headerText: "Invoice Date", key: "invoiceDate", dataType: "date",format: "dd/MM/yyyy HH:mm:ss", columnCssClass: "leftAlignment", width:"150px"},
            {headerText: "Total Qty", key: "TotalQty", dataType: "string", columnCssClass: "rightAlignment", width:"80px"},
            {headerText: "Total Amount", key: "totalAmount", dataType: "float",format: "0.00", columnCssClass: "rightAlignment", width:"110px"},
            {headerText: "Status", key: "status", dataType: "string", columnCssClass: "centerAlignment", width:"150px"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "leftAlignment", width:"150px"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
					
                ]
            },			
			{
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "invoiceId",
                       allowFixing: false,
                   },
                   {
                       columnKey: "inward_id",
                       allowFixing: false,
                   },
                   {
                       columnKey: "billingName",
                       allowFixing: false,
                   },
                   {
                       columnKey: "invoiceDate",
                       allowFixing: false,
                   },
                   {
                       columnKey: "TotalQty",
                       allowFixing: false,
                   },
                   {
                       columnKey: "totalAmount",
                       allowFixing: false,
                   },
                   {
                       columnKey: "status",
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: true,
                   },                   
               ]
           },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalInvoices'
            }
                        ],
                        primaryKey: "invoiceId",
        width: '100%',
        type: 'remote',
        dataSource: "/po/ajax/invoices/" + po_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}
function purchaseReturnGrid(status,inward_id) {
    $("#prList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "5%"},
            {headerText: "PR No", key: "prId", dataType: "string", width: "14%"},
            {headerText: "GRN No", key: "inwardCode", dataType: "string", width: "14%"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "12%"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "15%"},
            {headerText: "PR Value", key: "prValue", dataType: "number",format: "0.00", width: "10%", columnCssClass: "rightAlignment"},
            {headerText: "Invoice No", key: "invoiceId", dataType: "string", columnCssClass: "rightAlignment", width:"15%"},
            {headerText: "Picker", key: "picker_name", dataType: "string", width: "10%"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "10%"},
            {headerText: "PR Date", key: "createdOn", dataType: "date",format: "dd/MM/yyyy HH:mm:ss", width: "15%", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", width: "10%"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "5%"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: "chk", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
                    {columnKey: "chk", allowFiltering: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '10px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "prId", allowSummaries: false},
                    {columnKey: "inwardCode", allowSummaries: false},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "prValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "invoiceId", allowSummaries: false},
                    {columnKey: "picker_name", allowSummaries: false},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "Status", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                ]
            },
            {
                name: 'Paging',
                recordCountKey: 'totalPurchageReturns',
                type: 'remote',
                pageSize: 50,
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "prId",
        width: '100%',
        height: '100%',
        type: 'remote',
        dataSource: "/pr/ajax/" + status+'/'+inward_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {
            $("#prList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#prList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#prList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#prList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#prList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#prList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#prList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#prList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
            }
        });
}
function purchaseOrderGrid(status,from_date,to_date) {
    if(from_date!=undefined && from_date !="" && to_date !="" && to_date!=undefined){
        var po_url="/po/ajax/" + status+'?from_date='+from_date+'&to_date='+to_date;
    }else{
        var po_url="/po/ajax/" + status;
    }
    $("#poList").igGrid({
        columns: [
            {headerText: "PO No", key: "poId", dataType: "string", width: "130px"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            {headerText: "Parent PO", key: "po_parent_link", dataType: "string", width: "110px"},
            {headerText: "Validity", key: "validity", dataType: "string", width: "80px"},
            {headerText: "PO Value&nbsp;&nbsp;", key: "poValue", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value&nbsp;&nbsp;", key: "grn_value", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "125px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "125px", columnCssClass: "centerAlignment"},
            {headerText: "Payment Mode", key: "payment_mode", dataType: "string", width: "100px", columnCssClass: "nowrap"},
            {headerText: "Payment Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "130px", columnCssClass: "nowrap"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Approval Status", key: "approval_status", dataType: "string", width: "130px", columnCssClass: "nowrap"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "120px"}
            ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
                    {columnKey: 'createdOn', allowFiltering: true},
                ]
            },
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false}
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
                            $(this).css({'text-align': 'right', 'padding-right': '10px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},
                ]
            },
            {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                {columnKey: "Actions",isFixed: true,allowFixing: false},
               ]
           },
            {
                
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "poId",
        width: '100%',
        height: '650px',
        type: 'remote',
        dataSource: po_url,
        responseDataKey: 'data',
        rendered: function (evt, ui) {
            $("#poList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#poList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
            
            
            }
        });
}
function zoomOption(){
    $('#img-preview').bind('wheel mousewheel', function(e){
        var delta;
        if (e.originalEvent.wheelDelta !== undefined)
            delta = e.originalEvent.wheelDelta;
        else
            delta = e.originalEvent.deltaY * -1;
            if(delta > 0) {
                $('#img-preview img').css({
                height: '+=10',
                width: '+=10'
               });
            }
            else{
                $('#img-preview img').css({
                height: '-=10',
                width:  '-=10'
                });
            }
    });
}



function supplierPaymentDueGrid(status) {
    $("#poList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PO No", key: "poId", dataType: "string", width: "130px"},
            {headerText: "Payment Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "130px", columnCssClass: "nowrap"},
            {headerText: "Due days", key: "duedays", dataType: "string", width: "80px"},            
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            {headerText: "Validity", key: "validity", dataType: "string", width: "80px"},
            {headerText: "PO Value&nbsp;&nbsp;", key: "poValue", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value&nbsp;&nbsp;", key: "grn_value", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "120px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "125px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy HH:mm:ss", width: "125px", columnCssClass: "centerAlignment"},
            {headerText: "Payment Mode", key: "payment_mode", dataType: "string", width: "100px", columnCssClass: "nowrap"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Approval Status", key: "approval_status", dataType: "string", width: "130px", columnCssClass: "nowrap"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "120px"}
            ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
                    {columnKey: 'createdOn', allowFiltering: true},
                    {columnKey: 'chk', allowFiltering: false},  
                ]
            },
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: "chk", allowSorting: false},
                ]
            },
            { 
                name: "Summaries",
                type: "local",
                showDropDownButton: true,
                summariesCalculated: function (evt, ui) {
                    var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                    listPricesummaryCells.each(function () {
                        if ($(this).text() != "") {
                            $(this).text($(this).text().substr(2));
                            $(this).css({'text-align': 'right', 'padding-right': '10px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},                   
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},
                ]
            },
            {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                {columnKey: "Actions",isFixed: true,allowFixing: false},
               ]
           },
            {
                
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "poId",
        width: '100%',
        height: '650px',
        type: 'remote',
        dataSource: "/supplier/paymentDueList/" + status,
        responseDataKey: 'data',
        rendered: function (evt, ui) {
            $("#poList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#poList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#poList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#poList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
            
            
            }
        });
}

function checkAll(ele) {
    var checkboxes = document.getElementsByClassName('check_box');
    if (ele.checked) {
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = true;
            }
        }
    } else {
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = false;
            }
        }
    }
}