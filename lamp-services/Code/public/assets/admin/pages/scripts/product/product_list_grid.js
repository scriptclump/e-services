function productList() {
    $('#productsListGrid').igHierarchicalGrid({
        dataSource: '/productlist/getproductList',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        //expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'ProductID', key: 'product_id', dataType: 'number', width: '0%'},
            {headerText: 'Manufacturer', key: 'mfg_name', dataType: 'string', width: '30%'},
            {headerText: 'Group Repo', key: 'group_repo', dataType: 'string', width: '20%'},           
            {headerText: 'Brand Name', key: 'brand_name', dataType: 'string', width: '15%'},
            {headerText: 'Category', key: 'cat_name', dataType: 'string', width: '15%'},
            {headerText: 'CP Enabled ', key: 'cp_enabled', dataType: 'string', width: '10%'},
             {headerText: 'Number Of Products', key: 'Count', dataType: 'string', width: '15%', template: "<span style='margin-left: 50px !important;'> ${Count}"},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '7%'},
        ],
        columnLayouts: [
            {
                dataSource: '/productlist/childprodutList',
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'productData',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {
                        headerText: 'productId',
                        key: 'product_id',
                        dataType: 'number',
                        width: '0%',
                    },
                    {
                        headerText: 'Image',
                        key: 'primary_image',
                        dataType: 'string',
                        width: '6%',
                    },
                    {
                        headerText: 'Product Title',
                        key: 'product_title',
                        dataType: 'string',
                        width: '25%',
                    },
                    {
                        headerText: 'MRP',
                        key: 'mrp',
                        dataType: 'string',
                        width: '10%',
						template: '<div style="text-align:right"> ${mrp} </div>',
                    },
                    {
                        headerText: 'Variant 1',
                        key: 'Varient1',
                        dataType: 'string',
                        width: '15%',
                    },
                    {
                        headerText: 'Variant 2',
                        key: 'Varient2',
                        dataType: 'string',
                        width: '25%',
                    },
                    {
                        headerText: 'Variant 3',
                        key: 'Varient3',
                        dataType: 'string',
                        width: '20%',
                    },
                    {
                        headerText: 'Is Parent',
                        key: 'is_parent',
                        dataType: 'string',
                        width: '15%',
                    },
                    {
                        headerText: 'CP Enabled',
                        key: 'cp_enabled',
                        dataType: 'string',
                        width: '15%',
                    },
                    {
                        headerText: 'Actions',
                        key: 'actions',
                        dataType: 'string',
                        width: '10%',
                    },
                ],
                key: 'brands',
                foreignKey: 'product_id',
                primaryKey: 'product_id',
                width: '100%',
            }],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'cp_enabled', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false},
                    {columnKey: 'Count', allowFiltering: false},
                    /*{columnKey: 'business_legal_name', allowFiltering: true},
                     {columnKey: 'group_repo', allowFiltering: true},
                     {columnKey: 'Count', allowFiltering: true},
                     {columnKey: 'brand_name', allowFiltering: true},
                     {columnKey: 'cat_name', allowFiltering: true},*/
                ]

            },
            {
                name: 'Sorting',
                type: 'remote',
                mode: "simple",
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                    {columnKey: 'Count', allowSorting: false},
                ]

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
        localSchemaTransform: false,
        rendered: function (evt, ui) {
                    $("#productsListGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                    $("#productsListGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#productsListGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                    $("#productsListGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
                    $("#productsListGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
                    $("#productsListGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();  
                    $(".ui-iggrid-indicatorcontainer").find(".ui-iggrid-featurechooserbutton").remove();
                }
    });
}