function groupedProductsGrid()
{

    $('#groupedProducts').igHierarchicalGrid({
        dataSource: '/groupedProducts/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'}
             
            {headerText: '', key: 'product_image', dataType: 'string', width: '3%'},
            {headerText: 'Duplicate Product Name', key: 'product_title', dataType: 'string', width: '25%'},
            {headerText: 'SKU', key: 'sku', dataType: 'string', width: '10%'},
            {headerText: 'MRP', key: 'mrp', dataType: 'string', width: '5%',template: '<div style="text-align:right"> ${mrp} </div>'},
            {headerText: 'CP Enabled', key: 'cp_enabled', dataType: 'string', width: '10%'},
            {headerText: 'Created By', key: 'created_by', dataType: 'string', width: '10%'},             
            {headerText: 'Updated By', key: 'updated_by', dataType: 'string', width: '10%'},       
            {headerText: 'Created Date', key: 'created_at', dataType: 'string', width: '10%'},            
            {headerText: 'Updated Date', key: 'updated_at', dataType: 'string', width: '10%'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '7%'},
                ],
        features: [
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
