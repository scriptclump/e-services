$('#inventory_status_reports_grid').igGrid({
    dataSource: '/getInventoryStatusReports',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    expandColWidth: 0,
    enableUTCDates: true,
    columns: [
        //{headerText: 'product_id', key: 'product_id', dataType: 'string', width: '0%'},
        {headerText: 'Product Title', key: 'product_title', dataType: 'string', width: '12%'},
        {headerText: 'Product Class', key: 'product_class_name', dataType: 'string', width: '10%'},
        {headerText: 'Brand Name', key: 'brand_name', dataType: 'string', width: '10%'},
        {headerText: 'Warehouse', key: 'whname', dataType: 'string', width: '8%'},
        {headerText: 'Manufacturer', key: 'manufacturer_name', dataType: 'string', width: '10%'},        
        {headerText: 'Variant Value1', key: 'variant_value1', dataType: 'string', width: '8%'},        
        {headerText: 'Parent Product', key: 'parent_id', dataType: 'string', width: '9%'},        
        {headerText: 'SKU', key: 'sku', dataType: 'string', width: '8%'},
        {headerText: 'LP', key: 'elp', dataType: 'string', width: '5%'},
        {headerText: 'SP', key: 'esp', dataType: 'string', width: '5%'},
        {headerText: 'SU', key: 'esu', dataType: 'string', width: '5%'},
        {headerText: 'MRP', key: 'mrp', dataType: 'string', width: '5%'},
        {headerText: 'Sellable', key: 'is_sellable', dataType: 'string', width: '5%'},
        {headerText: 'CP', key: 'cp_enabled', dataType: 'string', width: '5%'},		
        {headerText: 'Inv', key: 'available_inventory', dataType: 'string', width: '5%'},
    ],
	
	rowsRendered: function (evt, ui) {
				modalMessage = new GridModalMessage(ui.owner);
				if (ui.owner.dataSource.dataView().length === 0) {
					modalMessage.show("Records not found.");
				}
				else
				{
					modalMessage.hide();
				}
	},	
	
    features: [
        {
            name: "Filtering",
            type: 'local',
            mode: "simple",
            filterDialogContainment: "window",
         
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'product_title', allowSorting: false},
                {columnKey: 'brand_name', allowSorting: false},
                {columnKey: 'manufacturer_name', allowSorting: false},
                {columnKey: 'esu', allowSorting: false},
                {columnKey: 'sku', allowSorting: false},
                {columnKey: 'elp', allowSorting: false},
                {columnKey: 'esp', allowSorting: false},
                {columnKey: 'mrp', allowSorting: false},
                {columnKey: 'available_inventory', allowSorting: false},
                {columnKey: 'product_class_name', allowSorting: false},
            ],
        },
 
        {
            name: 'Paging',
            type: "local",
            pageSize: 10
        }
    ],
    primaryKey: 'product_id',
    width: '100%',
    height: '420px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#inventory_status_reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});



