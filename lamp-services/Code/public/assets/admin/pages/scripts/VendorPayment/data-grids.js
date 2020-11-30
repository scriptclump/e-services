/**
 * Render the vendor payment due grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function initiationGrid(status) {
    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Due Days", key: "duedays", dataType: "string", width: "80px", columnCssClass: "centerAlignment"},            
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Payable", key: "payable", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "requested_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approve Amt", key: "pending_amount", dataType: "string", width: "100px", columnCssClass: "rightAlignment"},
            {headerText: "Comment", key: "comments", dataType: "string", width: "200px"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" }, 
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false}, 
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},                    
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "payable", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "requested_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},

                    {columnKey: "approved_amount", allowSummaries: false},
                    {columnKey: "pending_amount", allowSummaries: false},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "comments", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false},                    
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},                   
                    {columnKey: "payment_status", allowSummaries: false},           
                    {columnKey: "approval_status", allowSummaries: false}
                ]
            }
        ],
        primaryKey: 'poId',
        //width: '100%',
        dataRendered: function(evt, ui) {
            
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}


/**
 * Render the vendor payment process with bank grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function bankPaymentCompleteGrid(status, from_date = null, to_date = null, sup_name = null) {
   
    if( from_date  && to_date  || sup_name) {
        url = "/vendor/purchaseOrder/" + status + "?from_date=" + from_date+ "&to_date=" + to_date + "&sup_name=" + sup_name;
    }else{
         url = "/vendor/purchaseOrder/" + status;
     
    }

    $('#poList').igHierarchicalGrid({
        dataSource: url,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "230px", columnCssClass: "spaceLeft"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Amount", key: "approved_amount", dataType: "string", width: "100px", columnCssClass: "rightAlignment"},
            {headerText: "Ebutor A/c", key: "bank_account", dataType: "string", width: "100px", columnCssClass: "spaceLeft"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},
            {headerText: "UTR No", key: "utr", dataType: "string", width: "230px"}, 
            {headerText: "Bank Payment Date", key: "payment_date", dataType: "string", width: "230px"},           
            {headerText: "Comment", key: "comments", dataType: "string", width: "200px"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px", columnCssClass: "spaceLeft"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false}
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false}, 
                    {columnKey: "Actions", allowSummaries: false},                    
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "le_code", allowSummaries: false},                    
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false}, 
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},        
                    {columnKey: "approved_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]    }, 
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "bank_account", allowSummaries: false}, 
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "utr", allowSummaries: false},
                    {columnKey: "comments", allowSummaries: false},
                    {columnKey: "payment_date", allowSummaries: false}                   
                   
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
          
       //  populateTotals();
         // $('.showConsolidate').show();
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}


/**
 * Render the vendor payment process with bank grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function bankProcessGrid(status) {

    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "130px", columnCssClass: "spaceLeft"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "80px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "120px"},
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approved Amt", key: "appr_amt", dataType: "string", width: "100px", columnCssClass: "rightAlignment"},
            {headerText: "Paid Through", key: "bank_account", dataType: "string", width: "80px"},
            {headerText: "Transaction Type", key: "payment_type", dataType: "string", width: "80px"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "80px"},
            {headerText: "UTR No", key: "utr", dataType: "string", width: "130px"}, 
            {headerText: "Bank Payment Date", key: "payment_date", dataType: "string", width: "100px"},           
            {headerText: "Comment", key: "comments", dataType: "string", width: "150px"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "120px", columnCssClass: "spaceLeft"},
            {headerText: "City", key: "city", dataType: "string", width: "80px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "80px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_code", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},                     
                    {columnKey: "Supplier", allowSummaries: false}, 
                    {columnKey: "le_code", allowSummaries: false}, 
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},             
                    {columnKey: "appr_amt", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]    }, 
                    
                    {columnKey: "bank_account", allowSummaries: false}, 
                    {columnKey: "payment_type", allowSummaries: false},
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "utr", allowSummaries: false},                    
                    {columnKey: "payment_date", allowSummaries: false},
                    {columnKey: "comments", allowSummaries: false} 
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
          
       //  populateTotals();
         // $('.showConsolidate').show();
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}

/**
 * Render the vendor payment approved grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function approvedGrid(status) {

    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap spaceLeft"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "nowrap"},
            {headerText: "Due Days", key: "duedays", dataType: "string", width: "60px", columnCssClass: "centerAlignment"},            
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "req_amt", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approved Amt", key: "appr_amt", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            //{headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            //{headerText: "Validity", key: "validity", dataType: "string", width: "80px"},            
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "Payment Mode", key: "payment_mode", dataType: "string", width: "80px", columnCssClass: "nowrap"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Approval Status", key: "approval_status", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "req_amt", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},                    
                    {columnKey: "appr_amt", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},                    
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false}, 
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},                   
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
         
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}


/**
 * Render the vendor payment approved grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function rejectedGrid(status) {

    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap spaceLeft"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "nowrap centerAlignment"},
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "rejected_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Rejected Amt", key: "rejected_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            //{headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            //{headerText: "Validity", key: "validity", dataType: "string", width: "80px"},            
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "Payment Mode", key: "payment_mode", dataType: "string", width: "100px", columnCssClass: "nowrap"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Approval Status", key: "approval_status", dataType: "string", width: "130px", columnCssClass: "nowrap"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},                    
                    {columnKey: "requested_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "rejected_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "approved_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},                    
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false}, 
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},                   
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
           
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}

/**
 * Render the vendor payment approved grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function failedAtBankGrid(status) {

    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap spaceLeft"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "125px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "nowrap centerAlignment"},
            {headerText: "Due Days", key: "duedays", dataType: "string", width: "60px", columnCssClass: "centerAlignment"},            
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "req_amt", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approved Amt", key: "appr_amt", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            //{headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            //{headerText: "Validity", key: "validity", dataType: "string", width: "80px"},            
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "Payment Mode", key: "payment_mode", dataType: "string", width: "100px", columnCssClass: "nowrap"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Approval Status", key: "approval_status", dataType: "string", width: "130px", columnCssClass: "nowrap"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},                    
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "req_amt", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},                    
                    {columnKey: "appr_amt", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},                    
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "validity", allowSummaries: false}, 
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "payment_mode", allowSummaries: false},                   
                    {columnKey: "payment_status", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
         
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}

/**
 * Render the vendor payment all po list
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function allPOGrid(status) {
    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/" + status,
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Due Days", key: "duedays", dataType: "string", width: "60px"},            
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Payable", key: "payable", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "requested_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approved Amt", key: "approved_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            //{headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            //{headerText: "Validity", key: "validity", dataType: "string", width: "80px"},            
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            {headerText: "Payment Status", key: "payment_status", dataType: "string", width: "90px", columnCssClass: "nowrap"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: "Actions", key: "Actions", dataType: "string", width: "10%", columnCssClass: "nowrap"},
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                        {columnKey: 'Actions', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                        {columnKey: 'Actions', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false}
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},
                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "payable", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "requested_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "approved_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},                    
                    {columnKey: "validity", allowSummaries: false},                    
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                    {columnKey: "payment_status", allowSummaries: false},
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
          
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}

/**
 * Render the vendor payment due grid
 * @param  string status  status of the purchase order
 * @return HTML        Render the HTML conternt in tabular format
 */
function pendingPOGrid() {
    $('#poList').igHierarchicalGrid({
        dataSource: "/vendor/purchaseOrder/pending",
        type: 'remote',
        responseDataKey: 'data',
        width: '100%',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'poId', dataType: 'number', width: '0px'},
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "20px"},
            {headerText: "Supplier", key: "Supplier", dataType: "string", width: "210px", columnCssClass: "nowrap"},
            {headerText: "PO Code", key: "po_code", dataType: "string", width: "130px"},
            {headerText: "Due Date", key: "payment_due_date", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Due Days", key: "duedays", dataType: "string", width: "60px"},            
            {headerText: "PO Value", key: "poValue", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "GRN Value", key: "grn_value", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Payable", key: "payable", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Requested Amt", key: "requested_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Approved Amt", key: "approved_amount", dataType: "number", width: "100px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "Request Balance Amt", key: "pending_amount", dataType: "string", width: "100px"},
            {headerText: "Supplier No", key: "le_code", dataType: "string", width: "130px"},
            {headerText: "DC Name", key: "shipTo", dataType: "string", width: "110px"},
            {headerText: "City", key: "city", dataType: "string", width: "110px"},
            {headerText: "State", key: "state_name", dataType: "string", width: "110px"},
            //{headerText: "SO No", key: "po_so_order_link", dataType: "string", width: "110px"},
            //{headerText: "Validity", key: "validity", dataType: "string", width: "80px"},            
            {headerText: "PO&GRN Diff&nbsp;&nbsp;", key: "po_grn_diff", dataType: "number", width: "80px", format: "0.00", columnCssClass: "rightAlignment"},
            {headerText: "PO Date", key: "createdOn", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "GRN Date", key: "grn_created", dataType: "date", format: "dd/MM/yyyy", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Created By", key: "createdBy", dataType: "string", width: "120px", columnCssClass: "nowrap"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "70px", columnCssClass: "nowrap"},
        ],

        columnLayouts: [
        {
            dataSource: '/vendor/po-request-list/',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Requested Amt', key: 'requested_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Amount', key: 'approved_amount', dataType: 'number', width: '10%' ,columnCssClass: "leftAlignment" , format: "0.00"},
                {headerText: 'Status', key: 'approval_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Bank Status', key: 'bank_status', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested At', key: 'requested_at', dataType: "date", format: "dd/MM/yyyy" , width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved At', key: 'approved_at', dataType: "date", format: "dd/MM/yyyy", width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Requested By', key: 'requested_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: 'Approved By', key: 'approved_by', dataType: 'string', width: '10%' ,columnCssClass: "leftAlignment" },
                {headerText: "Actions", key: "Actions", dataType: "string", width: "10%", columnCssClass: "nowrap"},
            ],
            foreignKey: 'poId',
            width: '60%',
            features: [
                {
                    name: 'Paging',
                    recordCountKey: 'totalPurchageReturns',
                    type: 'remote',
                    pageSize: 50,
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'requested_amount', allowFiltering: false},
                        {columnKey: 'approved_amount', allowFiltering: false},
                        {columnKey: 'approval_status', allowFiltering: false},
                        {columnKey: 'bank_status', allowFiltering: false},
                        {columnKey: 'requested_at', allowFiltering: false},
                        {columnKey: 'approved_at', allowFiltering: false},
                        {columnKey: 'requested_by', allowFiltering: false},
                        {columnKey: 'approved_by', allowFiltering: false},
                        {columnKey: 'Actions', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'requested_amount', allowSorting: false},
                        {columnKey: 'approved_amount', allowSorting: false},
                        {columnKey: 'approval_status', allowSorting: false},
                        {columnKey: 'bank_status', allowSorting: false},
                        {columnKey: 'requested_at', allowSorting: false},
                        {columnKey: 'approved_at', allowSorting: false},
                        {columnKey: 'requested_by', allowSorting: false},
                        {columnKey: 'approved_by', allowSorting: false},
                        {columnKey: 'Actions', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalPurchageOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                   {columnKey: 'chk', allowFiltering: false},
                   {columnKey: 'Actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                columnSettings: [
                  {columnKey: "chk", allowSorting: false},
                  {columnKey: "Actions", allowSorting: false},
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
                            $(this).css({'text-align': 'right', 'padding-right': '100px', 'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "chk", allowSummaries: false},
                    {columnKey: "Actions", allowSummaries: false},
                    {columnKey: "po_code", allowSummaries: false},                     
                    {columnKey: "payment_due_date", allowSummaries: false},
                    {columnKey: "duedays", allowSummaries: false},
                    {columnKey: "le_code", allowSummaries: false},  

                    {columnKey: "poValue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Supplier", allowSummaries: false},
                    {columnKey: "payable", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "requested_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "approved_amount", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "pending_amount", allowSummaries: false},
                    {columnKey: "shipTo", allowSummaries: false},
                    {columnKey: "city", allowSummaries: false},
                    {columnKey: "state_name", allowSummaries: false},
                    {columnKey: "po_so_order_link", allowSummaries: false},                    
                    {columnKey: "validity", allowSummaries: false},                    
                    {columnKey: "grn_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "po_grn_diff", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "createdOn", allowSummaries: false},
                    {columnKey: "grn_created", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "approval_status", allowSummaries: false},
                ]
            }
        ],
        primaryKey: 'poId',
        dataRendered: function(evt, ui) {
          
        },
        initialDataBindDepth: 0,
       // localSchemaTransform: false
    });
}


/**
 * Validate maximum allowed amount for a vendor 
 * request against the PO
 * @param  Integer value Value in input box
 * @param  Integer max   Maximum limit allowed 
 * @param  Integer min   Minimum limit allowed 
 * @return Integer       MIN|MAX|CURRENT_VALUE
 */
function validateMaximum(value, max, min = 0){
  if( isNaN(value) ){
    alert('Please enter only positive numbers');
  }  
  if(value < min || isNaN(parseInt(value))){
    return '';
  } else if( value > max) {
    return max; 
  } else {
    return value;
  } 
}

/**
 * Check & uncheck all the checkboxes based on one checkbox
 * @param  DOM ele Checkbox Document Object Model
 */
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
