$('#hub_inventory_grid').igHierarchicalGrid({
    dataSource: 'gethubinventory',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    //expandColWidth: true,
    //enableUTCDates: true,
    columns: [
        {headerText: 'pid', key: 'pid', dataType: 'string', width: '0px'},
        {headerText: '', key: 'primary_image', columnCssClass: "imgalign", dataType: 'string', width: '40px', template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${primary_image}'/></center>"},
        {headerText: 'Product Title', key: 'product_title', dataType: 'string',width: '400px'},
        {headerText: 'SKU Code', key: 'sku', dataType: 'string',width: '100px'},
        {headerText: 'MRP', key: 'mrp', dataType: 'string',width: '80px',template: '<div class="rightAlign"> ${mrp} </div>'},
        {headerText: 'Hold Qty', key: 'sum_hid_qty', dataType: 'string',width: '100px',template: '<div class="rightAlign"> ${sum_hid_qty} </div>'}, 
        {headerText: 'Return Qty', key: 'sum_ret_qty', dataType: 'string',width: '100px',template: '<div class="rightAlign"> ${sum_ret_qty} </div>'}, 		
        {headerText: 'DND Qty', key: 'sum_dnd_qty', dataType: 'string',width: '100px',template: '<div class="rightAlign"> ${sum_dnd_qty} </div>'},                 
        {headerText: 'DIT Qty', key: 'sum_dit_qty', dataType: 'string',width: '100px',template: '<div class="rightAlign"> ${sum_dit_qty} </div>'}, 
	{headerText: 'Total', key: 'total', dataType: 'string', width: '100px',template: '<div class="rightAlign"> ${total} </div>'},
    ],
    columnLayouts: [
        {
            dataSource: 'gethuborderinventory',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            renderCheckboxes: true,
            columns: [
                {headerText: 'Order ID', key: 'order_id', dataType: 'string', width: '585px'},
                //{headerText: 'product_id', key: 'product_id', dataType: 'number', width: '10%'},
                {headerText: 'Hold Qty', key: 'hld_qty', dataType: 'string', width: '100px'},
                {headerText: 'Return Qty', key: 'ret_qty', dataType: 'string', width: '100px'},
                {headerText: 'DND Qty', key: 'dnd_qty', dataType: 'string', width: '100px'},
                {headerText: 'DIT Qty', key: 'dit_qty', dataType: 'string', width: '100px'},
                {headerText: 'Total', key: 'total', dataType: 'string', width: '100px'},
                     
            ],
           features: [         {
            name: 'Paging',
            type: "local",
            pageSize: 5
        }],
            //key: 'productids',
            foreignKey: 'pid',
            primaryKey: 'product_id',
            width: '100%'
        }],		
	
    features: [
        {
            name: "Filtering",
            type: 'local',
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                    {columnKey: 'primary_image', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'primary_image', allowSorting: false},
            ],
        },
 
        {
            name: 'Paging',
            type: "local",
            pageSize: 10
        }
    ],
    primaryKey: 'pid',
    width: '100%',
    height: '540px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#hub_inventory_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});



