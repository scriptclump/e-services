function childproductlist(parent_id) {
    $('#productsListGrid').igGrid({
        dataSource: '/productlist/getRepoProducts?paent_id='+parent_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'productData',
        generateCompactJSONResponse: false,
        //expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'productId', key: 'product_id', dataType: 'number', width: '0%'},
            {headerText: ' ', key: 'primary_image', dataType: 'string', width: '6%'},
            {headerText: 'Product Title', key: 'product_title', dataType: 'string', width: '20%'},
            {headerText: 'MRP', key: 'mrp', dataType: 'string', width: '10%',template: '<div style="text-align:right"> ${mrp} </div>'},
            {headerText: 'Variant1', key: 'Varient1', dataType: 'string', width: '25%'},
            {headerText: 'Variant2', key: 'Varient2', dataType: 'string', width: '25%'},
            {headerText: 'Variant3', key: 'Varient3', dataType: 'string', width: '25%'},            
            {headerText: 'Is Parent', key: 'is_parent', dataType: 'string', width: '10%'},
            {headerText: 'CP Enabled', key: 'cp_enabled', dataType: 'string', width: '10%'},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'},
        ],
       
        features: [
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [                    
                    {columnKey: 'actions', allowFiltering: false},
                    {columnKey: 'primary_image', allowFiltering: false},
                    {columnKey: 'is_parent', allowFiltering: false},
                    {columnKey: 'Varient1', allowFiltering: false},
                    {columnKey: 'Varient2', allowFiltering: false},
                    {columnKey: 'Varient3', allowFiltering: false},
                    {columnKey: 'mrp', allowFiltering: false},
                    {columnKey: 'product_title', allowFiltering: false},
                    {columnKey: 'cp_enabled', allowFiltering: false},
                ]

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                 columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                    {columnKey: 'primary_image', allowSorting: false},
                    {columnKey: 'is_parent', allowSorting: false},
                    {columnKey: 'Varient1', allowSorting: false},
                    {columnKey: 'Varient2', allowSorting: false},
                    {columnKey: 'Varient3', allowSorting: false},
                    {columnKey: 'mrp', allowSorting: false},
                    {columnKey: 'product_title', allowSorting: false},
                    {columnKey: 'cp_enabled', allowSorting: false},
                ]

            },
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
            },
        ],
        primaryKey: 'product_id',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });


}


