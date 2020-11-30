function warehouseBinConfigGrid()
{

    $('#product_wh_config').igHierarchicalGrid({
        dataSource: '/getWhBinConfig/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'}
             
            {headerText: 'Warehouse Name', key: 'wh_name', dataType: 'string', width: '25%'},
            {headerText: 'Bin Type', key: 'bin_type', dataType: 'text', width: '15%'},
            {headerText: 'Pack Type', key: 'pack_conf_id', dataType: 'text', width: '10%'},
          {headerText: 'Min Capacity', key: 'min_capacity', dataType: 'text', width: '15%',template: '<div style="text-align:right"> ${min_capacity} </div>'},
          {headerText: 'Max Capacity', key: 'max_capacity', dataType: 'text', width: '15%',template: '<div style="text-align:right"> ${max_capacity} </div>'},
            {headerText: 'Action', key: 'Action', dataType: 'string', width: '20%'},
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
