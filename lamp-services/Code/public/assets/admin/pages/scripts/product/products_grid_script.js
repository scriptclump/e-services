$(document).ready(function(){
$('#extendedProductsGrid').igGrid({
    dataSource: 'getProducts',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    rowHeight:12,
    enableUTCDates: true,
    expandColWidth: 0,
    renderCheckboxes: true,
    columns: [
        {headerText: 'Product ID', key: 'Product_ID', dataType: 'number', width: '0px'},
        {headerText: '', key: 'ProductLogo', columnCssClass: "imgalign", dataType: 'string', width: '40px', template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${ProductLogo}'/></center>"},
        {headerText: 'Product Title', key: 'Product_Title', dataType: 'string', width: '300px'},
        {headerText: 'SKU', key: 'SKU', dataType: 'string', width: '100px'},
		{headerText: 'MRP', key: 'MRP', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${MRP} </div>'},
		{headerText: 'LP', key: 'ELP', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${ELP} </div>'},
		{headerText: 'SP', key: 'ESP', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${ESP} </div>'},
		{headerText: 'PTR', key: 'PTR', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${PTR} </div>'},
        {headerText: 'CFC', key: 'CFC', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${CFC} </div>'},
		{headerText: 'Inventory', key: 'INV', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${INV} </div>'},
		{headerText: 'Tax(%)', key: 'TAX', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${TAX} </div>'},
        {headerText: 'Pack Size', key: 'pack_size', dataType: 'number', width:'100px',template: '<div class="rightAlign"> ${pack_size} </div>'},
		{headerText: 'KVI', key: 'KVI', dataType: 'string', width: '50px',template: '<div class="centerAlign"> ${KVI} </div>'},
        {headerText: 'Manf Name', key: 'ManfName', dataType: 'string', width: '200px'},
        {headerText: 'Brand', key: 'Brand', dataType: 'string', width: '150px'},
        {headerText: 'Category', key: 'category_name', dataType: 'string', width: '100px'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '100px'},
        {headerText: 'CP', key: 'cp_enabled', dataType: 'string', width: '50px'},
        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '110px'},
    ],
    features: [
                    {
                        name: "ColumnFixing",
                        fixingDirection: "right",
                        columnSettings: [
                            {
                                columnKey: "Action",
                                isFixed: true,
                                allowFixing: false
                            }
                        ]
                    },
        
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'ProductLogo', allowFiltering: false},
                {columnKey: 'Schemes', allowFiltering: false},
				{columnKey: 'Statuss', allowFiltering: false},
                {columnKey: 'Action', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'ProductLogo', allowSorting: false},
                {columnKey: 'Schemes', allowSorting: false},
				{columnKey: 'Statuss', allowSorting: false},
                {columnKey: 'Action', allowSorting: false},
            ]

        },
        {
           /* recordCountKey: 'TotalRecordsCount',
            chunkIndexUrlKey: 'page',
            chunkSizeUrlKey: 'pageSize',
            chunkSize: 8,
            name: 'AppendRowsOnDemand',
            loadTrigger: 'auto',
            type: 'remote'*/
            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        }
    ],
    primaryKey: 'Product_ID',
    width: '100%',
    height: '520px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
                    $("#extendedProductsGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();                    
                } 
});


$('#productsGrid').igGrid({
    dataSource: 'getProducts',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    rowHeight:12,
    enableUTCDates: true,
    expandColWidth: 0,
    renderCheckboxes: true,
    columns: [
        {headerText: 'Product ID', key: 'Product_ID', dataType: 'number', width: '0px'},
        {headerText: '', key: 'ProductLogo', columnCssClass: "imgalign", dataType: 'string', width: '40px', template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${ProductLogo}'/></center>"},
        {headerText: 'Product Title', key: 'Product_Title', dataType: 'string', width: '300px'},
        {headerText: 'SKU', key: 'SKU', dataType: 'string', width: '100px'},
		{headerText: 'MRP', key: 'MRP', dataType: 'number', width: '100px',template: '<div class="rightAlign"> ${MRP} </div>'},
        {headerText: 'Pack Size', key: 'pack_size', dataType: 'number', width:'100px'},
		{headerText: 'KVI', key: 'KVI', dataType: 'string', width: '50px'},
        {headerText: 'Manf Name', key: 'ManfName', dataType: 'string', width: '200px'},
        {headerText: 'Brand', key: 'Brand', dataType: 'string', width: '150px'},
        {headerText: 'Category', key: 'category_name', dataType: 'string', width: '100px'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '100px'},
        {headerText: 'CP', key: 'cp_enabled', dataType: 'string', width: '50px'},
        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '110px'},
    ],
    features: [
                    {
                        name: "ColumnFixing",
                        fixingDirection: "right",
                        columnSettings: [
                            {
                                columnKey: "Action",
                                isFixed: true,
                                allowFixing: false
                            }
                        ]
                    },
        
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'ProductLogo', allowFiltering: false},
                {columnKey: 'Schemes', allowFiltering: false},
				{columnKey: 'Statuss', allowFiltering: false},
                {columnKey: 'Action', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'ProductLogo', allowSorting: false},
                {columnKey: 'Schemes', allowSorting: false},
				{columnKey: 'Statuss', allowSorting: false},
                {columnKey: 'Action', allowSorting: false},
            ]

        },
        {
           /* recordCountKey: 'TotalRecordsCount',
            chunkIndexUrlKey: 'page',
            chunkSizeUrlKey: 'pageSize',
            chunkSize: 8,
            name: 'AppendRowsOnDemand',
            loadTrigger: 'auto',
            type: 'remote'*/

            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"  
        }
    ],
    primaryKey: 'Product_ID',
    width: '100%', 
    height: '520px',
    initialDataBindDepth: 0,
    localSchemaTransform: false}); 
});