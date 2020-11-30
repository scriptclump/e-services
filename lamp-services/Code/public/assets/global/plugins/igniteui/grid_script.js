$('#hierarchicalGrid').igHierarchicalGrid({
    dataSource: 'cockpitProducts',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    expandColWidth: 0,
    columns: [
        {headerText: "Product ID", key: "ProductID", dataType: "number", width: "0%"},
        {headerText: "Product Logo", key: "ProductLogo", dataType: "string", width: "30%"},
        {headerText: "Product", key: "Name", dataType: "string", width: "40%"},
        {headerText: "MRP", key: "mrp", dataType: "number", width: "17%"},
        {headerText: "ED Enabled", key: "ED_Enabled", dataType: "bool", width: "25%"},
        {headerText: "ERP Sync", key: "ED_Enabled", dataType: "bool", width: "25%"},
        {headerText: "All", key: "Temp", dataType: "bool", width: "20%"},
        {headerText: "Pending", key: "Temp", dataType: "bool", width: "20%"},
        {headerText: "Completed", key: "Temp", dataType: "bool", width: "20%"},
        {headerText: "Approvals", key: "Approvals", dataType: "bool", width: "30%"},
        {headerText: "Channels Sales", key: "Temp", dataType: "bool", width: "30%"},
        {headerText: "Sales Control", key: "SalesControl", dataType: "bool", width: "30%"},
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'ProductLogo', allowFiltering: false}
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false

        },
        {
            recordCountKey: 'TotalRecordsCount',
            chunkIndexUrlKey: 'page',
            chunkSizeUrlKey: 'pageSize',
            chunkSize: 5,
            name: 'AppendRowsOnDemand',
            loadTrigger: 'auto',
            type: 'remote'
        }
    ],
    primaryKey: 'ProductID',
    width: '100%',
    height: '500px',
    initialDataBindDepth: 0,
    localSchemaTransform: false});




$('#logisticPrtnersList').igHierarchicalGrid({
    dataSource: 'logisticpartners/getLpList',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        {headerText: "SNO", key: "LpID", dataType: "number", width: "6%", columnCssClass: "centerAlignment"},
        {headerText: "Logo", key: "LpLogo", dataType: "string", width: "10%"},
        {headerText: "Name", key: "LpName", dataType: "string", width: "10%"},
        {headerText: "Address", key: "LpAddress", dataType: "string", width: "30%"},
        {headerText: "Warehouses", key: "Warehouses", dataType: "number", width: "10%", columnCssClass: "centerAlignment"},
        {headerText: "Fulfilment", key: "LpFullService", dataType: "bool", width: "8%"},
        {headerText: "Forwarding", key: "LpForService", dataType: "bool", width: "10%"},
        {headerText: "COD", key: "LpCODService", dataType: "bool", width: "8%"},
        {headerText: "Action", key: "Action", dataType: "string", width: "8%", columnCssClass: "centerAlignment"},
    ],
    columnLayouts: [
        {
            dataSource: 'logisticpartners/getWarehouseList',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {
                    headerText: 'WarehouseId',
                    key: 'WarehouseId',
                    dataType: 'number',
                    width: '0%',
                },
                {
                    headerText: 'Warehouse Name',
                    key: 'WarehouseName',
                    dataType: 'string',
                    width: '15%',
                },
                {
                    headerText: 'Area',
                    key: 'WarehouseArea',
                    dataType: 'string',
                    width: '15%',
                },
                {
                    headerText: 'City',
                    key: 'WarehouseCity',
                    dataType: 'string',
                    width: '15%'
                },
                {
                    headerText: 'Email ID',
                    key: 'WarehouseEmail',
                    dataType: 'string',
                    width: '20%'
                },
                {
                    headerText: 'Phone',
                    key: 'WarehousePhone',
                    dataType: 'string',
                    width: '15%'
                },
                {
                    headerText: 'Action',
                    key: 'Action',
                    dataType: 'string',
                    width: '15%'
                }
            ],
            key: 'Products',
            foreignKey: 'LpID',
            primaryKey: 'WarehouseId',
            width: '100%',
            features: [
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 2
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'Action', allowFiltering: false},
                        {columnKey: 'LpLogo', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'LpLogo', allowSorting: false},
                        {columnKey: 'LpLogo', allowSorting: false},
                    ]

                }]
        }],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Warehouses', allowFiltering: false},
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'LpAddress', allowFiltering: false},
                {columnKey: 'LpLogo', allowFiltering: false},
                {columnKey: 'WarehouseArea', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'Warehouses', allowSorting: false},
                {columnKey: 'LpAddress', allowSorting: false},
                {columnKey: 'LpLogo', allowSorting: false},
                {columnKey: 'WarehouseArea', allowSorting: false},
            ]

        },
        {
            name: 'Paging',
            type: 'remote',
            pageSize: 8,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"


        }
    ],
    primaryKey: 'LpID',
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});


function addWarehouseGrid()
{
    $('#addWarehouseGrid').igHierarchicalGrid({
        dataSource: '/logisticpartners/getWarehouseList/' + $('#lpId').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //o{headerText: 'Warehouse ID',key: 'WarehouseId',dataType: 'number',width: '0%'},
            {headerText: 'Warehouse Name', key: 'WarehouseName', dataType: 'string', width: '25%'},
            {headerText: 'Area', key: 'WarehouseArea', dataType: 'string', width: '15%'},
            {headerText: 'City', key: 'WarehouseCity', dataType: 'string', width: '15%'},
            {headerText: 'Email ID', key: 'WarehouseEmail', dataType: 'string', width: '20%'},
            {headerText: 'Phone', key: 'WarehousePhone', dataType: 'string', width: '20%'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '5%', columnCssClass: "actionscolors"}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'Action', allowFiltering: false},
                    {columnKey: 'Phone', allowFiltering: false},
                    {columnKey: 'WarehouseArea', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'Phone', allowSorting: false},
                    {columnKey: 'Action', allowSorting: false},
                    {columnKey: 'WarehouseArea', allowSorting: false},
                ]

            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 5,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'WarehouseId',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}

function addSupplierWarehouseGrid()
{

    $('#addSupplierWarehouseGrid').igHierarchicalGrid({
        dataSource: '/suppliers/getWarehouseList',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'Warehouse ID', key: 'WarehouseId', dataType: 'number', width: '0%'},
            {headerText: 'Warehouse Name', key: 'WarehouseName', dataType: 'string', width: '25%'},
            {headerText: 'Area', key: 'WarehouseArea', dataType: 'string', width: '15%'},
            {headerText: 'City', key: 'WarehouseCity', dataType: 'string', width: '15%'},
            {headerText: 'Email ID', key: 'WarehouseEmail', dataType: 'string', width: '20%'},
            {headerText: 'Phone', key: 'WarehousePhone', dataType: 'string', width: '20%'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '5%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'Action', allowFiltering: false},
                    {columnKey: 'Phone', allowFiltering: false},
                    {columnKey: 'WarehouseArea', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'Phone', allowSorting: false},
                    {columnKey: 'Action', allowSorting: false},
                    {columnKey: 'WarehouseArea', allowSorting: false},
                ]

            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 13,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'WarehouseId',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}

function totGrid(supplier_id)
{

    $('#supplier_tot_grid').igHierarchicalGrid({
        dataSource: '/suppliers/getProducts/' + supplier_id,
        autoGenerateColumns: true,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        //expandColWidth: 12,
        enableUTCDates: true,
        columns: [
            {headerText: 'Warehouse', key: 'WarehouseName', dataType: 'string',width:'11%'},
            //{headerText: 'Brand', key: 'BrandName', dataType: 'string',width:'10%'},
            {headerText: 'Product Name', key: 'ProductName', dataType: 'string',width:'11%'},
            {headerText: 'MRP', key: 'MRP', dataType: 'number',width:'6%'},
            {headerText: 'Base Price', key: 'Bestprice', dataType: 'number', template: "${Bestprice}",width:'6%'},
            {headerText: 'Tax (%)', key: 'Tax', dataType: 'string', template: "${Tax}(${TaxType})",width:'8%'},
            {headerText: 'Tax', key: 'Tax_Amt', dataType: 'number',width:'6%'},
            {headerText: 'LP', key: 'ELP', dataType: 'number',width:'6%'},
            {headerText: 'Margin', key: 'EbutorMargin', dataType: 'number',width:'6%'},
            
            {headerText: 'Article No', key: 'sku', dataType: 'string',width:'10%'},
            {headerText: 'Seller Sku', key: 'seller_sku', dataType: 'string',width:'8%'},
            
            //{headerText: 'PTR', key: 'PTR', dataType: 'number',width:'6%'},
            //{headerText: 'R-Margin', key: 'margin', dataType: 'number',width:'6%'},
            {headerText: 'Inv', key: 'InventoryMode', dataType: 'string',width:'6%'},
            {headerText: 'ATP', key: 'ATP', dataType: 'string',  template: "${ATP} ${ATPPeriod}",width:'6%'},
            {headerText: 'Eff Date', key: 'EffectiveDate', dataType: "date", columnCssClass: "centerAlignment", format: "date",width:'8%'},
            {headerText: 'Subscribe', key: 'subscribe', dataType: 'string',width:'5%'},
            {headerText: 'Action', key: 'action', dataType: 'string',width:'5%'}
        ],
        columnLayouts: [
        {
            dataSource: '/suppliers/getpurhistory',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
               // {headerText: 'Pur Price Id', key: 'PurPriceId', dataType: 'number'},
                {headerText: 'LP', key: 'ELP', dataType: 'string',width:'20%'},
                {headerText: 'Effective date', key: 'EffectiveDate',width:'40%', dataType: "date", columnCssClass: "centerAlignment", format: "date"},  
				{headerText: 'Created At', key: 'CreatedAt',width:'40%', dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},	
               // {headerText: 'Actions',key: 'Action',dataType: 'string',width: '5%'},
            ],
            key: 'PurPriceId',
            foreignKey: 'PurPriceId',
            primaryKey: 'prod_price_id',
            width: '100%',
            features: [
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 20
                }]
        }],

        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window"

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 13,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            
        ],
        primaryKey: 'prod_price_id',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});
    var product_array = new Array();
    $(document).delegate("#supplier_tot_grid", "iggridrowselectorscheckboxstatechanged", function (evt, ui) {
        var product_id = "";
        if (ui.state === 'on') {
            product_id = ui.row.attr("data-id");
            product_array.push(product_id);
        } else {
            Array.prototype.remove = function (value) {
                if (this.indexOf(value) !== -1) {
                    this.splice(this.indexOf(value), 1);
                    return true;
                } else {
                    return false;
                }
                ;
            }
            product_id = ui.row.attr("data-id");
            product_array.remove(product_id);
        }
        $("#totproducts").val(JSON.stringify(product_array));
        console.log(product_array);
    });
}



function dcInventoryGrid(supplier_id) {
    $('#supplier_dcmapping_grid').igGrid({
        dataSource: '/suppliers/dcInventory/' + supplier_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'ProductID', key: 'product_id', dataType: 'number', width: '0%'},
            {headerText: 'Product Name', key: 'product_title', dataType: 'string', width: '40%'},
            {headerText: 'DC Name', key: 'lp_wh_name', dataType: 'string', width: '30%'},
            {headerText: 'ATP', key: 'atp', dataType: 'string', width: '30%'},
        ],
        features: [
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window"

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

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
        primaryKey: 'product_id',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });


}




function brandsGrid(legal_entity_id)
{


    $('#brands_grid').igHierarchicalGrid({
        dataSource: '/suppliers/getBrands/' + legal_entity_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        renderCheckboxes: true,
        columns: [
            {headerText: 'BrandID', key: 'BrandID', dataType: 'number', width: '0%'},
            {headerText: 'Brand Name', key: 'BrandName', dataType: 'string', width: '15%'},
            {headerText: 'Description', key: 'Description', dataType: 'string', width: '45%'},
            {headerText: '#Products', key: 'Products', dataType: 'number', width: '15%'},
            {headerText: 'Authorized', key: 'Authorized', dataType: 'bool', width: '15%'},
            {headerText: 'Trademark', key: 'Trademark', dataType: 'bool', width: '15%'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '15%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'Products', allowFiltering: false},
                    {columnKey: 'Action', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'Products', allowSorting: false},
                    {columnKey: 'Action', allowSorting: false},
                ]

            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 12,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'BrandID',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});
}


$('#hierarchicalGrid').on('ighierarchicalgridchildrenpopulated', function () {
    if ($(".sparkbar").length) {
        $(".sparkbar").each(function () {
            var $this = $(this);
            if (!$this.find('canvas').length) {
                $this.sparkline('html', {type: 'bar', height: '2.0em'});
            }
        });
    }

});

function shipmnentList(orderid) {
    $("#shipmentList").igGrid({
                        columns: [
                                {headerText: "Shipment ID", key: "shipmentId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
                        {headerText: "Order Date", key: "orderDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
                        {headerText: "Shipped Date", key: "shipmentDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Shop Name", key: "shippedTo", dataType: "string"},
            {headerText: "Shipped Qty", key: "shippedQty", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "shipmentActions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'shipmentActions', allowFiltering: false },
             ]
                                 },*/                       
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalShipments'
            }
                        ],
                        primaryKey: "shipmentId",
        width: '100%',
        type: 'remote',
        dataSource: "/salesorders/ajax/shipment/" + orderid,
        responseDataKey: 'data',
        rendered: function (evt, ui) {
            //$("#shipmentList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            //$("#shipmentList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove(); 
                    }
        });
}

function invoiceList(order_id) {
    $("#invoiceList").igGrid({
                        columns: [
                                {headerText: "Invoice ID", key: "invoiceId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
                        {headerText: "Billing Name", key: "billingName", dataType: "string"},
                        {headerText: "Invoice Date", key: "invoiceDate", dataType: "date", columnCssClass: "centerAlignment"},
            {headerText: "Total Qty", key: "TotalQty", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Total Amount", key: "totalAmount", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
                               /* {
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/
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
        dataSource: "/salesorders/ajax/invoices/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}


function cancelList(order_id) {
    $("#cancelList").igGrid({
                        columns: [
                                {headerText: "Cancel ID", key: "cancelId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Order Date", key: "orderDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Cancelled Date", key: "cancelDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Cancelled Qty", key: "qtyCancelled", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Cancelled Amount", key: "cancelledAmt", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalCancelled'
            }
                        ],
                        primaryKey: "cancelId",
        width: '100%',
        type: 'remote',
        dataSource: "/salesorders/ajax/orderCancelList/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}


function getOrderPickList() {
    var filterURL = "/salesorders/getOrderPicking";

    $("#pickList").igGrid({
                        columns: [
                                {headerText: "SNo", key: "SNo", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Picking ID", key: "pickingID", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Picking Date", key: "pickingDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
                                {headerText: "Picking Location", key: "pickingLocation", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
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
                name: 'Paging',
                type: "remote",
                pageSize: 10,
                recordCountKey: 'totalOrders'
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        height: '100%',
        type: 'remote',
        dataSource: filterURL,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: false,
        rendered: function (evt, ui) {
            $("#orderList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#orderList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
            }
                });
}

function getSalesOrderList(status) {
    var filterURL = "/salesorders/ajax/index/" + status;

    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "dateTime", width: "100px"},
            {headerText: "Sch Del Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Business Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Order Value(Rs.)", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Invoice Value(Rs.)", key: "InvoiceValue", dataType: "number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Cancel Value(Rs.)", key: "CancelValue", dataType: "number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Return Value(Rs.)", key: "ReturnValue", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Exp Date", key: "OrderExpDate", dataType: "date", format: "date", width: "80px"},
            {headerText: "Fill Rate (%)", key: "FillRate", dataType: "string", columnCssClass: "centerAlignment", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "80px"}
                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    // {columnKey: 'ChannelName', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Status', allowSorting: false},
                    {columnKey: 'Area', allowSorting: false},
                    {columnKey: "InvoiceValue", allowSorting: false},
                    {columnKey: "CancelValue", allowSorting: false},
                    {columnKey: "ReturnValue", allowSorting: false},
                    {columnKey: "FillRate", allowSorting: false},
                    {columnKey: "SDT", allowSorting: false},
                    {columnKey: "ADT", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [

                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "CancelValue", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},

                ]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "FillRate",
                       isFixed: true,
                       allowFixing: false
                   },
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },


                   {
                       columnKey: "ReturnValue",
                       allowFixing: false
                   },
                   {
                       columnKey: "CancelValue",
                       allowFixing: false
                   },
                   {
                       columnKey: "InvoiceValue",
                       allowFixing: false
                   },
                   {
                       columnKey: "OrderValue",
                       allowFixing: false
                   },
                   {
                       columnKey: "Area",
                       allowFixing: false
                   },
                   {
                       columnKey: "User",
                       allowFixing: false
                   },
                   {
                       columnKey: "Customer",
                       allowFixing: false
                   },
                   {
                       columnKey: "OrderExpDate",
                       allowFixing: false
                   },
                   {
                       columnKey: "OrderDate",
                       allowFixing: false
                   },
                   {
                       columnKey: "OrderID",
                       allowFixing: false
                   },
                   {
                       columnKey: "ChannelName",
                       allowFixing: false
                   },
                   {
                       columnKey: "chk",
                       allowFixing: false
                   },
                   {
                    columnKey: "SDT",
                       allowFixing: false
                   },
                   {
                       columnKey: "ADT",
                       allowFixing: false
                   },
               ]
           },

            {
                recordCountKey: 'totalOrders',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 20,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote'
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        height: '800px',
        type: 'remote',
        dataSource: filterURL,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        rendered: function (evt, ui) {
            $("#orderList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#orderList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#orderList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
            $("#orderList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
            }
                });
}



function ProductsBasedOnBrand(brand_id, wh_id) {

    $('#product_choose_grid').igGrid({
        dataSource: '/suppliers/getBrandProducts/' + brand_id + '/' + wh_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        //expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'ProductID', key: 'ProductID', dataType: 'number', hidden: 'true'},
            //{headerText: 'Image', key: 'primary_image', dataType: "image", width: "5%", template: "<img class='img-responsive' width='48px' height='48px' src='${primary_image}'>"},
            {headerText: 'Brand', key: 'brand_name', dataType: 'string', width: '8%'},
            {headerText: 'Category', key: 'Category', dataType: 'string', width: '13%'},
            {headerText: 'Product Title', key: 'ProductName', dataType: 'string', width: '15%'},            
            {headerText: 'Article Number', key: 'sku', dataType: 'string', width: '15%'},
            //{headerText: 'MRP', key: 'MRP', dataType: 'string', width: '6%'},
            {headerText: 'LP', key: 'ELP', dataType: 'string', width: '6%'},
            {headerText: 'Base Price', key: 'Bestprice', dataType: 'string', width: '6%', template: "${Currency}  ${Bestprice}"},
            {headerText: 'Tax Type', key: 'TaxType', dataType: 'string', width: '5%'},
            {headerText: 'Tax (%)', key: 'Tax', dataType: 'string', width: '5%'},
            {headerText: 'PTR', key: 'PTR', dataType: 'string', width: '6%'},
            {headerText: 'ATP', key: 'ATP', dataType: 'string', width: '6%'},
            {headerText: 'ATP Period', key: 'ATPPeriod', dataType: 'string', width: '6%'},
            {headerText: 'Inventory Mode', key: 'InventoryMode', dataType: 'string', width: '15%'},
            //{headerText: 'Subscription', key: 'Subc_status', dataType: 'bool', width: '5%'},
            {headerText: 'Action', key: 'actions', dataType: 'string', width: '6%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    /*{columnKey: 'primary_image', allowFiltering: false},
                    {columnKey: 'group_repo', allowFiltering: true},
                     {columnKey: 'Count', allowFiltering: true},
                     {columnKey: 'brand_name', allowFiltering: true},
                     {columnKey: 'cat_name', allowFiltering: true},*/
                ]

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },
            {
                recordCountKey: 'TotalRecordsCount',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 10,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote'
            },
        ],
        primaryKey: 'product_id',
        width: '100%',
        height: '500px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });

//    $('#product_choose_grid').igHierarchicalGrid({
//        dataSource: '/suppliers/getBrandProducts/' + brand_id +'/'+ wh_id,
//        autoGenerateColumns: false,
//        autoGenerateLayouts: false,
//        mergeUnboundColumns: false,
//        responseDataKey: 'Records',
//        generateCompactJSONResponse: false,
//        //expandColWidth: 0,
//        enableUTCDates: true,
//        columns: [
//            {headerText: 'ProductID', key: 'product_id', dataType: 'number', width: '0%'},
//          {headerText: 'Group Repo', key: 'group_repo', dataType: 'string', width: '30%'},
//          {headerText: 'Number Of Products', key: 'Count', dataType: 'string', width: '20%', template:"<span style='margin-left: 50px !important;'> ${Count}"},
//            //{headerText: 'logo', key: 'logo', dataType: 'string', width: '15%'},
//            {headerText: 'Business Legal Name', key: 'business_legal_name', dataType: 'string', width: '40%'},
//            //{headerText: 'Brand Logo', key: 'logo_url', dataType: 'string', width: '15%'},
//            {headerText: 'Brand Name', key: 'brand_name', dataType: 'string', width: '15%'},            
//            {headerText: 'Category', key: 'cat_name', dataType: 'string', width: '20%'},
//            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '7%'},
//        ],
//        columnLayouts: [
//            {
//                dataSource: '/suppliers/childprodutList/' + wh_id,
//                autoGenerateColumns: false,
//                autoGenerateLayouts: false,
//                mergeUnboundColumns: false,
//                responseDataKey: 'productData',
//                generateCompactJSONResponse: false,
//                enableUTCDates: true,
//                columns: [
//                    {
//                        headerText: 'productId',
//                        key: 'product_id',
//                        dataType: 'number',
//                        width: '0%',
//                    },
//                    {
//                        headerText: 'Image',
//                        key: 'primary_image',
//                        dataType: 'string',
//                        width: '12%',
//                    },
//                    {
//                        headerText: 'Product Title',
//                        key: 'product_title',
//                        dataType: 'string',
//                        width: '25%',
//                    },
//                    {
//                        headerText: 'Mrp',
//                        key: 'mrp',
//                        dataType: 'string',
//                        width: '10%',
//                        template: "Rs. ${mrp}",
//                    },
//                    {
//                        headerText: 'variant1',
//                        key: 'variant_value1',
//                        dataType: 'string',
//                        width: '20%',
//                    },
//                    {
//                        headerText: 'variant2',
//                        key: 'variant_value2',
//                        dataType: 'string',
//                        width: '30%',
//                    },
//                    {
//                        headerText: 'variant3',
//                        key: 'variant_value3',
//                        dataType: 'string',
//                        width: '20%',
//                    },
//                    {
//                        headerText: 'Actions',
//                        key: 'actions',
//                        dataType: 'string',
//                        width: '10%',
//                    },
//                ],
//                key: 'brands',
//                foreignKey: 'product_id',
//                primaryKey: 'product_id',
//                width: '100%',
//            }],
//        features: [
//            {
//                name: "Filtering",
//                type: "remote",
//                mode: "simple",
//                filterDialogContainment: "window",
//                columnSettings: [
//                    /*{columnKey: 'business_legal_name', allowFiltering: true},
//                    {columnKey: 'group_repo', allowFiltering: true},
//                    {columnKey: 'Count', allowFiltering: true},
//                    {columnKey: 'brand_name', allowFiltering: true},
//                    {columnKey: 'cat_name', allowFiltering: true},*/
//                ]
//
//            },
//            {
//                name: 'Sorting',
//                type: 'remote',
//                persist: false
//
//            },
//            {
//                recordCountKey: 'TotalRecordsCount',
//                chunkIndexUrlKey: 'page',
//                chunkSizeUrlKey: 'pageSize',
//                chunkSize: 10,
//                name: 'AppendRowsOnDemand',
//                loadTrigger: 'auto',
//                type: 'remote'
//            },
//        ],
//        
//        primaryKey: 'product_id',
//        width: '100%',
//        height: '500px',
//        initialDataBindDepth: 0,
//        localSchemaTransform: false
//    });
}

function returnOrderList(order_id) {
    $("#returnList").igGrid({
                        columns: [
                                {headerText: "Return ID", key: "returnId", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Order Date", key: "orderDate", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Order Return Date", key: "returnDate", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Order Return Value", key: "returnValue", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Returned Qty", key: "qtyReturned", dataType: "string", columnCssClass: "centerAlignment"},
                                {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalReturns'
            }
                        ],
                        primaryKey: "returnId",
        width: '100%',
        type: 'remote',
        dataSource: "/salesorders/getreturns/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}


function refundOrderList(order_id) {
    $("#refundList").igGrid({
                        columns: [
                                {headerText: "Refund ID", key: "refundId", dataType: "number"},
            {headerText: "Order ID", key: "orderId", dataType: "number"},
                                {headerText: "Total Amount (Rs.)", key: "totAmount", dataType: "string"},
            {headerText: "Refund Amount (Rs.)", key: "refundAmount", dataType: "string"},
            {headerText: "Refund Date", key: "refundDate", dataType: "date", format: "dateTime"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalRefunds'
            }
                        ],
                        primaryKey: "returnId",
        width: '100%',
        type: 'remote',
        dataSource: "/salesorders/getrefunds/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function commentHistoryList(order_id) {
    $("#commentList").igGrid({
                        columns: [
                                {headerText: "SNo", key: "SNo", dataType: "number", width: "5%", columnCssClass: "centerAlignment"},
            {headerText: "Comment Type", key: "commentType", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Comment Date", key: "commentDate", dataType: "date", columnCssClass: "centerAlignment", format: "dateTime"},
                                {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "By", key: "commentBy", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Comment", key: "Comment", dataType: "string"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalComment'
            }
                        ],
                        primaryKey: "returnId",
        width: '100%',
        type: 'remote',
        dataSource: "/salesorders/commentHistory/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}
function getOrderIndentList() {
    var filterURL = "/indents/getOrderIndent";

    $("#pickList").igGrid({
                        columns: [
                    {headerText: "Indent No", key: "indentID", dataType: "string", columnCssClass: "centerAlignment", width:"200px"},
                    {headerText: "Indent Type", key: "indentType", dataType: "string", columnCssClass: "centerAlignment", format: "dateTime", width:"100px"},
                    {headerText: "Raised On", key: "indentDate", dataType: "date", format: "dateTime", columnCssClass: "centerAlignment", width:"200px"},
                    {headerText: "Supplier Name", key: "supplier_id", dataType: "string", width:"250px"},
                    {headerText: "DC Name", key: "indentLocation", dataType: "string", width:"100px"},
                    {headerText: "Manufacturer", key: "manufacturer", dataType: "string", width:"100px"},
                    {headerText: "Indent Qty", key: "qty", dataType: "number", columnCssClass: "centerAlignment", width:"100px"},
                    {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment", width:"100px"},
                    {headerText: "Action", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width:"100px"}
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
                name: "Filtering",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
                ]
            },
                 
            {
                name: 'Paging',
                type: "remote",
                pageSize: 10,
                recordCountKey: 'totalIndents'
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        height: '100%',
        type: 'remote',
        dataSource: filterURL,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: false,
        rendered: function (evt, ui) {
                    $("#pickList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
                    $("#pickList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
                    $("#pickList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
                    $("#pickList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
                    $("#pickList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
                    $("#pickList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
                    $("#pickList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
                    $("#pickList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
                }
            });
}

/*function relatedProductsGrid()
 {
 $('#relatedProductsGrid').igHierarchicalGrid({
 dataSource: '/relatedproducts/' + $('#product_id').val(),
 autoGenerateColumns: false,
 autoGenerateLayouts: false,
 mergeUnboundColumns: false,
 responseDataKey: 'Records',
 generateCompactJSONResponse: false,
 expandColWidth: 0,
 enableUTCDates: true,
 columns: [
 //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'},
 {headerText: 'Product', key: 'PrimaryImage', dataType: 'string', width: '11%'},
 {headerText: 'Product Name', key: 'ProductName', dataType: 'string', width: '25%'},
 {headerText: 'ProductTitle ', key: 'ProductTitle', dataType: 'string', width: '25%'},
 {headerText: 'Seller SKU', key: 'SellerSKU', dataType: 'string', width: '20%'},
 {headerText: 'UPC', key: 'UPC', dataType: 'string', width: '14%'},
 {headerText: 'Pack Size', key: 'PackSize', dataType: 'string', width: '10%'},
 //{headerText: 'Pack Size UOM',key: 'PackSizeUOM',dataType: 'string',width: '15%'}, 
 {headerText: 'Supplier Count', key: 'SupplierCount', dataType: 'string', width: '15%'},
 {headerText: 'Created By', key: 'CreatedBy', dataType: 'string', width: '15%'},
 {headerText: 'Created On', key: 'CreatedOn', dataType: 'date',format: "dateTime", width: '15%'},
 {headerText: 'Approved By', key: 'ApprovedBy', dataType: 'string', width: '15%'},
 {headerText: 'Approved On', key: 'ApprovedOn', dataType: 'date',format: "dateTime", width: '15%'},
 {headerText: 'Action', key: 'Action', dataType: 'string', width: '10%', columnCssClass: "actionscolors"}
 ],
 features: [
 /*{
 name: "Filtering",
 type: "remote",
 mode: "simple",
 filterDialogContainment: "window",
 columnSettings: [
 {columnKey: 'Action', allowFiltering: false },
 {columnKey: 'Phone', allowFiltering: false },
 {columnKey: 'WarehouseArea', allowFiltering: false },
 ]
 },
 {
 name: 'Sorting',
 type: 'remote',
 persist: false,
 columnSettings: [
 {columnKey: 'Phone', allowSorting: false },
 {columnKey: 'Action', allowSorting: false },
 {columnKey: 'WarehouseArea', allowSorting: false },
 ]
 
 },
 {
 name: 'Paging',
 type: 'remote',
 pageSize: 5,
 recordCountKey: 'TotalRecordsCount',
 pageIndexUrlKey: "page",
 pageSizeUrlKey: "pageSize"
 }
 ],
 primaryKey: 'ProductId',
 width: '100%',
 initialDataBindDepth: 0,
 localSchemaTransform: false});
 
 }
 */
function packingConfigGrid()
{
    $('#packingConfigGrid').igHierarchicalGrid({
        dataSource: '/packingproducts/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'},
            {headerText: 'Level', key: 'Level', dataType: 'string', width: '11%'},
         {headerText: 'Pack SKU Code', key: 'PackSkuCode', dataType: 'string', width: '25%',template: '<div style="text-align:right"> ${PackSkuCode} </div>'},
            {headerText: '# Eaches', key: 'Eaches', dataType: 'string', width: '10%',template: '<div style="text-align:right"> ${Eaches} </div>'},
            {headerText: '# Inners', key: 'Inners', dataType: 'string', width: '8%',template: '<div style="text-align:right"> ${Inners} </div>'},
            {headerText: 'SU', key: 'esu', dataType: 'string', width: '5%',template: '<div style="text-align:right"> ${esu} </div>'},
            {headerText: 'Star', key: 'star', dataType: 'string', width: '5%'},
            {headerText: 'LxBxH', key: 'LBH', dataType: 'string', width: '25%'},
            {headerText: 'Weight', key: 'Weight', dataType: 'string', width: '10%'},
            /*{headerText: 'Vol Weight', key: 'VolWeight', dataType: 'string', width: '15%',template: '<div style="text-align:right"> ${VolWeight} </div>'},
            {headerText: 'Stack Height', key: 'StackHeight', dataType: 'string', width: '13%',template: '<div style="text-align:right"> ${StackHeight} </div>'},
            {headerText: 'Packing Material', key: 'PackMaterial', dataType: 'string', width: '15%'},*/
            {headerText: 'Is Sellable', key: 'is_sellable', dataType: 'string', width: '14%'},
			{headerText: 'Is Cratable', key: 'is_cratable', dataType: 'string', width: '14%'},
			{headerText: 'Effective Date', key: 'effectiveDate',dataType: "date", columnCssClass: "centerAlignment", format: "dateTime",width: '15%'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '10%', columnCssClass: "actionscolors"}
        ],
        features: [
            /*{
             name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Action', allowFiltering: false },
             {columnKey: 'Phone', allowFiltering: false },
             {columnKey: 'WarehouseArea', allowFiltering: false },
             ]
             },
             {
             name: 'Sorting',
             type: 'remote',
             persist: false,
             columnSettings: [
             {columnKey: 'Phone', allowSorting: false },
             {columnKey: 'Action', allowSorting: false },
             {columnKey: 'WarehouseArea', allowSorting: false },
             ]
             
             },*/
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 5,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'ProductId',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}
function freeBieConfigGrid()
{

    $('#freeBieConfigGrid').igHierarchicalGrid({
        dataSource: '/freeBieProducts/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'}
             
            {headerText: 'MPQ', key: 'mpq', dataType: 'string', width: '5%'},
            {headerText: 'Freebie Product Name', key: 'free_prd_id', dataType: 'string', width: '15%'},
             {headerText: 'Freebie Description', key: 'free_prd_des', dataType: 'string', width: '15%'},
            {headerText: 'Article No', key: 'article_no', dataType: 'string', width: '10%'},
            {headerText: 'Free Qty', key: 'qty', dataType: 'string', width: '7%',template: '<div style="text-align:right"> ${qty} </div>'},             
            {headerText: 'SL Status', key: 'is_stock_limit', dataType: 'string', width: '7%'},       
            {headerText: 'Stock Limit', key: 'stock_limit', dataType: 'string', width: '6%'},            
            {headerText: 'State Name', key: 'state_id', dataType: 'string', width: '10%'},
            {headerText: 'Warehouse Name', key: 'le_wh_id', dataType: 'string', width: '9%'},
            {headerText: 'Start Date', key: 'start_date', dataType: 'string', width: '7%'},
            {headerText: 'End Date', key: 'end_date', dataType: 'string', width: '7%'},           
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '7%', columnCssClass: "actionscolors"}
        ],
        features: [
            /*{
             name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Action', allowFiltering: false },
             {columnKey: 'Phone', allowFiltering: false },
             {columnKey: 'WarehouseArea', allowFiltering: false },
             ]
             },
             {
             name: 'Sorting',
             type: 'remote',
             persist: false,
             columnSettings: [
             {columnKey: 'Phone', allowSorting: false },
             {columnKey: 'Action', allowSorting: false },
             {columnKey: 'WarehouseArea', allowSorting: false },
             ]
             
             },*/
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'ProductId',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}



function productSuppliersGrid()
{
    $('#productSuppliersGrid').igHierarchicalGrid({
        dataSource: '/productsuppliers/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
       columns: [
            {headerText: 'ProductID', key: 'ProductID', dataType: 'number', width: '0%'},
            {headerText: 'Warehouse', key: 'WarehouseName', dataType: 'string', width: '10%'},
            //{headerText: 'Brand', key: 'BrandName', dataType: 'string', width: '7%'},
            {headerText: 'Supplier Name', key: 'supplier', dataType: 'string', width: '11%'},
            {headerText: 'Product Name', key: 'ProductName', dataType: 'string', width: '11%'},
            {headerText: 'MRP', key: 'MRP', dataType: 'string', width: '6%'},
            {headerText: 'Base Price', key: 'Bestprice', dataType: 'string', width: '7%', template: "${Bestprice}"},
//            {headerText: 'Tax Type', key: 'TaxType', dataType: 'string', width: '5%'},
            {headerText: 'Tax (%)', key: 'Tax', dataType: 'string', width: '10%', template: "${Tax}(${TaxType})"},
            {headerText: 'Tax', key: 'Tax_Amt', dataType: 'string', width: '7%'},
            {headerText: 'LP', key: 'ELP', dataType: 'string', width: '6%'},
            {headerText: 'Margin', key: 'EbutorMargin', dataType: 'string', width: '6%'},
            
            {headerText: 'Article No', key: 'sku', dataType: 'string', width: '10%'},
            {headerText: 'Seller Sku', key: 'seller_sku', dataType: 'string', width: '8%'},
            
            //{headerText: 'PTR', key: 'PTR', dataType: 'string', width: '6%'},
            //{headerText: 'R-Margin', key: 'margin', dataType: 'string', width: '6%'},
            {headerText: 'Inventory Mode', key: 'InventoryMode', dataType: 'string', width: '8%'},
            {headerText: 'ATP', key: 'ATP', dataType: 'string', width: '7%', template: "${ATP} ${ATPPeriod}"},
//            {headerText: 'ATP Period', key: 'ATPPeriod', dataType: 'string', width: '6%'},
            {headerText: 'Eff Date', key: 'EffectiveDate', dataType: 'string', width: '8%'},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '5%'},
            //{headerText: 'Subscribe', key: 'subscribe', dataType: 'string', width: '5%'},
            /*{headerText: 'Action', key: 'actions', dataType: 'string', width: '2%',template:"<a data-toggle='modal' class='editProduct' href='${ProductID}'> <i class='fa fa-pencil'></i> </a>&nbsp;&nbsp;<a class='deleteProduct' href='${ProductID}'> <i class='fa fa-trash-o'></i> </a>"}*/
            //{headerText: 'Action', key: 'action', dataType: 'string', width: '5%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window"

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: 'Selection',
                multipleSelection: true
            },
        ],
        primaryKey: 'prod_price_id',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}
function getGrnList(status) {
    var filterURL = "/grn/getgrn/"+status;

    $("#grn").igGrid({
        columns: [
            {headerText: "GRN#", key: "grnId", dataType: "string", columnCssClass: "leftAlignment", width: '130px'},
            {headerText: "PO#", key: "poId", dataType: "string", columnCssClass: "leftAlignment", width: '150px'},
            {headerText: "Supplier", key: "legalsuplier", dataType: "string", width: '125px'},
            {headerText: "DC Name", key: "dcname", dataType: "string", width: '125px'},
            {headerText: "Created On", key: "grnDate", dataType: "date",format: "dd/MM/yyyy HH:mm:ss", columnCssClass: "centerAlignment", width: '130px'},
            {headerText: "Created By", key: "createdBy", dataType: "string", columnCssClass: "leftAlignment", width: '130px'},
            {headerText: "Ref No", key: "ref_no", dataType: "string", columnCssClass: "rightAlignment", width: '100px'},
            {headerText: "Invoice No", key: "invoice_no", dataType: "string", columnCssClass: "rightAlignment", width: '100px'},
            {headerText: "PO Value", key: "povalue", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: '100px'},
            {headerText: "GRN Value", key: "grnvalue", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: '100px'},
            {headerText: "Item Discount", key: "item_discount_value", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: '100px'},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "110px"}
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
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
                    {columnKey: "grnId", allowSummaries: false},
                    {columnKey: "poId", allowSummaries: false},
                    {columnKey: "legalsuplier", allowSummaries: false},
                    {columnKey: "dcname", allowSummaries: false},
                    {columnKey: "grnDate", allowSummaries: false},
                    {columnKey: "createdBy", allowSummaries: false},
                    {columnKey: "ref_no", allowSummaries: false},
                    {columnKey: "invoice_no", allowSummaries: false},
                    {columnKey: "povalue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "grnvalue", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "item_discount_value", allowSummaries: true, summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Actions", allowSummaries: false}
                ]
            },
            {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "grnId",                       
                       allowFixing: false
                   },
                   {
                    columnKey: "poId",                    
                       allowFixing: false
                   },
                   {
                    columnKey: "legalsuplier",                    
                       allowFixing: false
                   },
                    {
                       columnKey: "dcname",
                      
                       allowFixing: false
                   },
                   {
                       columnKey: "grnDate",
                       
                       allowFixing: false
                   },
                   {
                       columnKey: "createdBy",
                       
                       allowFixing: false
                   },
                   {
                       columnKey: "ref_no",
                       allowFixing: false
                   },
                   {
                       columnKey: "invoice_no",
                       allowFixing: false
                   },
                   {
                       columnKey: "povalue",
                       allowFixing: false
                   },
                    {
                       columnKey: "grnvalue",
                       allowFixing: false
                   },
                   {
                       columnKey: "item_discount_value",
                       allowFixing: false
                   },
                   
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
                   
                ]
           },
            {
                name: 'Paging',
                recordCountKey: 'totalGrn',
                type: 'remote',
                pageSize: 50,
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }

                        ],
                        primaryKey: "grnId",
        width: '100%',
        height: '500px',
        type: 'remote',
        dataSource: filterURL,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        rendered: function (evt, ui) {
            $("#grn_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#grn_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#grn_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#grn_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#grn_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#grn_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#grn_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#grn_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();            
        }
    });
}

function getDispute(txnId) {
    $("#dispute_list").igGrid({
                        columns: [
            {headerText: "User", key: "commentBy", dataType: "string"},
            {headerText: "Date", key: "commentDate", dataType: "date", format: "dateTime"},
            {headerText: "Comment", key: "Comment", dataType: "string"}
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
                name: 'Paging',
                type: "remote",
                pageSize: 10,
                recordCountKey: 'totalComment'
            }
                        ],
                        primaryKey: "SNo",
        width: '100%',
        type: 'remote',
        dataSource: "/grn/getDisput/" + txnId,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function slabPrices(productId) {    
    $('#slabprices').igGrid({
        dataSource: '/products/slabPrices?product_id='+productId,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [                     
            {headerText: 'MOQ', key: 'pack_size', dataType: 'number', width: '15%', template: "<span style='margin-left:10px'>  ${pack_size} </span>"},
            {headerText: 'SP', key: 'unit_price', dataType: 'string', width: '10%',template: '<div style="text-align:right"> ${unit_price} </div>'},                       
            {headerText: 'DC Name', key: 'dc', dataType: 'string', width: '35%'},
            {headerText: 'State', key: 'state', dataType: 'string', width: '35%'},            
            {headerText: 'Customer Type', key: 'customer_name', dataType: 'string', width: '30%'},            
            {headerText: 'Margin (%)', key: 'margin', dataType: 'string', width: '10%',template: '<div style="text-align:right"> ${margin} </div>'}, 
			{headerText: 'Action', key: 'actions', dataType: 'action', width: '10%'},	
        ],
        features: [
            {
                name: "Filtering",
                type:"local",
                allowFiltering: true,
                caseSensitive: false,
                columnSettings: [
                    {columnKey: 'actions', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: "local",
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                ]
            },
            {
                name : 'Paging',
                type: "local",
                pageSize : 10,
            }
        ],
        primaryKey: 'pack_size',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}


function cpEnableDcorFc()
{

    $('#cpEnableTableGrid').igHierarchicalGrid({
        dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [                     
            {headerText: 'DC-FC Name', key: 'display_name', dataType: 'string', width: '20%'},
            {headerText: 'CP Enabled', key: 'cp_dcfc_enable', dataType: 'string', width: '20%'},                       
            {headerText: 'Is Sellable', key: 'sellable_dcfc_enable', dataType: 'string', width: '20%'},
            {headerText: 'ESU', key: 'esu', dataType: 'string', width: '20%'},                       
 
        ],
        features: [  
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                allowFiltering: true,
                filterDialogContainment: "window",
                columnSettings: [
                             {headerText: 'DC-FC Name', key: 'display_name', dataType: 'string', width: '20%'},
                             //{headerText: 'CP Enabled', key: 'cp_dcfc_enable', dataType: 'string', width: '20%'},                       
                             //{headerText: 'Is Sellable', key: 'sellable_dcfc_enable', dataType: 'string', width: '20%'},
                             {headerText: 'ESU', key: 'esu', dataType: 'string', width: '20%'},
                                                         
                    ]

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },         
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 5,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'product_id',
        width: '100%'/*,
        initialDataBindDepth: 0,
        localSchemaTransform: false*/});

}
function productELPHistory()
{

    $('#productElpHistoryGrid').igHierarchicalGrid({
        dataSource: '/products/getelphistorybyproductid?product_id=' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [    
            {headerText: 'PO Code', key: 'PO_Code', dataType: 'string', width: '20%'},
            {headerText: 'Supplier', key: 'Supplier', dataType: 'string', width: '20%'},     
            {headerText: 'State', key: 'State', dataType: 'string', width: '20%'},           
            {headerText: 'APOB-DC', key: 'DC', dataType: 'string', width: '20%'}, 
            {headerText: 'Warehouse', key: 'FC', dataType: 'string', width: '20%'},
            {headerText: 'DLP-FLP', key: 'Dlp_Flp', dataType: 'string', width: '20%'},
            {headerText: 'Actual ELP', key: 'Actual_Elp', dataType: 'string', width: '20%'},
            {headerText: 'Effective Date', key: 'Effective_Date', dataType: 'date',format:"dd/MM/yyyy", width: '20%'},                       
 
        ],
        features: [  
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                allowFiltering: true,
                filterDialogContainment: "window",
                columnSettings: [
                             
                    ]

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },         
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'product_id',
        width: '100%',/*,
        initialDataBindDepth: 0,
        localSchemaTransform: false*/
        rendered: function (evt, ui) {
           
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#productElpHistoryGrid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        }
    });

}

$("#exportproductelps").click(function(e){
    e.preventDefault();
     $("#exportproductelps_form").submit();
});

function customerTypeEsu(){
    $('#customerTypeEsuGrid').igHierarchicalGrid({
        dataSource: '/products/customertypeesu?product_id=' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [  
            {headerText: 'Customer Type', key: 'master_lookup_name', dataType: 'string', width: '20%'},
            {headerText: 'ESU', key: 'esu', dataType: 'string', width: '20%'},                       
 
        ],
        features: [  
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                allowFiltering: true,
                filterDialogContainment: "window",
                columnSettings: [
                             {headerText: 'Customer Type', key: 'master_lookup_name', dataType: 'string', width: '20%'},
                             {headerText: 'ESU', key: 'esu', dataType: 'string', width: '20%'},
                                                         
                    ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false

            },         
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 5,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'product_id',
        width: '100%'});
}