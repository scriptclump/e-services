$('#warehouse_dashboard_grid').igGrid({
    dataSource: '/warehouseconfig/getInvdata',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    expandColWidth: 0,
    enableUTCDates: true,
    columns: [
        {headerText: 'Warehouse Name', key: 'warehouse_name', dataType: 'string',width: "150px"},
        {headerText: 'Aisle Name', key: 'aisle_type', dataType: 'string',width: "100px"}, 
        {headerText: 'Bin Code', key: 'bin_code', dataType: 'string',width: "100px"},       
        {headerText: 'Bin Inv', key: 'bin_inv', dataType: 'number',width: "80px" ,template: '<div style="text-align:right"> ${bin_inv} </div>'},
        {headerText: 'Bin Type', key: 'bin_type', dataType: 'string',width: "80px"},
        {headerText: 'Length', key: 'bin_length', dataType: 'string',width: "80px" ,template: '<div style="text-align:right"> ${bin_length} </div>'},
        {headerText: 'Breadth', key: 'bin_breadth', dataType: 'string',width: "80px",template: '<div style="text-align:right"> ${bin_breadth} </div>'},
        {headerText: 'Height', key: 'bin_height', dataType: 'string',width: "80px",template: '<div style="text-align:right"> ${bin_height} </div>'},
        {headerText: 'Pack Type', key: 'pack_type_name', dataType: 'string',width: "120px"},
        {headerText: 'Bin Min Qty', key: 'bin_min_qty', dataType: 'number',width: "100px",template: '<div style="text-align:right"> ${bin_min_qty} </div>'},
        {headerText: 'Bin Max Qty', key: 'bin_max_qty', dataType: 'number',width: "100px",template: '<div style="text-align:right"> ${bin_max_qty} </div>'},
        {headerText: 'Product Title', key: 'product_title', dataType: 'string',width: "200px"},
        {headerText: 'SKU', key:'sku', dataType: 'string',width: "150px"},
        {headerText: 'Created Date', key: 'created_at', dataType: 'string',width: "150px"},
    ],
    features: [
        {
            name: "ColumnFixing",
            fixingDirection: "right",
            columnSettings: [
                {
                    columnKey: "created_at",
                    isFixed: true,
                    allowFixing: false
                }
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'created_at', allowSorting: false},
            ],
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
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [{columnKey:'warehouse_name',allowFiltering: false},
                            {columnKey:'bin_breadth',allowFiltering: false},
                            {columnKey:'bin_height',allowFiltering: false}, 
                            {columnKey:'bin_length',allowFiltering: false},
                            {columnKey:'created_at',allowFiltering: false},
                            {columnKey:'bin_min_qty',allowFiltering: false},
                            {columnKey:'bin_max_qty',allowFiltering: false},
                            ]
        },
    ],
    //primaryKey: 'product_id',
    width: '100%',
    height:'540px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
        rendered: function (evt, ui) {
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#warehouse_dashboard_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }

});


