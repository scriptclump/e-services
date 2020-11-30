$('#manufacturer_list_grid').igHierarchicalGrid({ 

				dataSource: 'brands/getManfBrandsGrid',
				autoGenerateColumns: false,
				autoGenerateLayouts: false,
				mergeUnboundColumns: false,
				responseDataKey: 'Records',
				generateCompactJSONResponse: false,
				enableUTCDates: true,

	columns: [
        {headerText: 'BrandID', key: 'BrandID', dataType: 'number', width: '0%'},
        {headerText: 'Brand Name', key: 'BrandName', dataType: 'string', width: '25%'},
        //{headerText: 'Brand', key: 'BrandName', dataType: 'string', width: '20%'},
        //{headerText: 'Manufacturer', key: 'ManufacturerLogo', dataType: 'string', width: '10%'},
        {headerText: 'Manufacturer Name', key: 'ManufacturerName', dataType: 'string', width: '25%'},
        //{headerText: 'TM',key: 'Trademarked',dataType: 'string',width: '6%'}, 
        //{headerText: '<i class="fa fa-user"></i>',key: 'is_authorised',dataType: 'string',width: '5%'}, 
        {headerText: '<i class="fa fa-cube alt="Products"i>', key: 'Products', dataType: 'string', width: '6%',
        template:'<span style=" margin-left: 22px !important;position:absolute">  ${Products} </span>'},
        {headerText: '<i class="fa fa-camera"></i>', key: 'With_Images', dataType: 'string', width: '6%',         
        template:'<span style=" margin-left: 22px !important;position:absolute">  ${With_Images} </span>'},
    
        {headerText: '<img src="/assets/admin/pages/img/noimage.png" width="22" height="22"/>', key: 'Without_Images', dataType: 'string', width: '6%', template:'<span style=" margin-left: 22px !important;position:absolute">  ${Without_Images} </span>'},
        
        {headerText: '<i class="fa fa-cubes" ></i>', key: 'With_Inventory', dataType: 'string', width: '6%', template:'<span style=" margin-left: 22px !important;position:absolute">  ${With_Inventory} </span>'},
        
        {headerText: '<img src="/assets/admin/pages/img/noinventroy.png" width="22" headerTextight="22"/>', key: 'Without_Inventory', dataType: 'string', width: '6%', template:'<span style=" margin-left: 22px !important;position:absolute">  ${Without_Inventory} </span>'},
        {headerText: '<img src="/assets/admin/pages/img/approved.png" width="22" height="22"/>', key: 'Approved', dataType: 'string', width: '6%', template:'<span style=" margin-left: 22px !important;position:absolute">  ${Approved} </span>'},
        {headerText: '<img src="/assets/admin/pages/img/pending.png" width="22" height="22"/>', key: 'Pending', dataType: 'string', width: '6%',template:'<span style=" margin-left: 22px !important;position:absolute">  ${Pending} </span>'},
        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '8%', columnCssClass: "actionsaling"},
    ],
							columnLayouts: [
									{ 
										dataSource: 'brands/getManfProductsGrid',
										autoGenerateColumns: false,
										autoGenerateLayouts: false,
										mergeUnboundColumns: false,
										responseDataKey: 'Records',
										generateCompactJSONResponse: false,
										enableUTCDates: true,
										renderCheckboxes: true,

										columns: [
													{headerText: 'Product ID',key: 'Product_ID',dataType: 'number',width: '0%'},
													{headerText: '',key: 'ProductLogo',dataType: 'string',width: '9%'},
													//{headerText: 'Brand',key: 'BrandLogo',dataType: 'string',width: '17%'},
													//{headerText: 'Product Name',key: 'Product_Name',dataType: 'string',width: '24%'}, 
													{headerText: 'Product Title',key: 'Product_Title',dataType: 'string',width: '15%'}, 
													{headerText: 'Manufacturer SKU',key: 'SKU',dataType: 'string',width: '20%'}, 
													{headerText: 'UPC/EAN',key: 'UPC',dataType: 'number',width: '20%'}, 
													{headerText: 'Weight',key: 'Weight',dataType: 'string',width: '10%'}, 
													{headerText: 'Supplier Count',key: 'Supplier_Count',dataType: 'number',width: '15%'}, 
													{headerText: 'Created By',key: 'Created_By',dataType: 'string',width: '15%'}, 
													{headerText: 'Created On',key: 'Created_On',dataType: 'date',format: "dateTime", width: '15%'},
													{headerText: 'Approved By',key: 'Approved_By',dataType: 'string',width: '15%'},
													{headerText: 'Approved On',key: 'Approved_On',dataType: 'date',format: "dateTime", width: '15%'},
													{headerText: 'Actions',key: 'Action',dataType: 'string',width: '12%'},
													],
										features: [
										{
											name: 'Paging',
											type: "local",
											pageSize: 10
										}],
										key: 'Products',
										foreignKey: 'BrandID',
										primaryKey: 'Product_ID',
										width: '100%'
									}
										
									
									],
					
				key: 'Brands',
				foreignKey: 'SupplierID',
				primaryKey: 'BrandID',
				width: '100%',

			features: [
			{
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window", 
                columnSettings: [
                   {columnKey: 'business_legal_name', allowFiltering: true},                     
                   {columnKey: 'BrandLogo', allowFiltering: false},                     
                   {columnKey: 'ManufacturerLogo', allowFiltering: false},                     
                   {columnKey: 'Action', allowFiltering: false},   
                   {columnKey: 'BrandID', allowFiltering: false},
                ]               

            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                {columnKey: 'Action', allowSorting: false },
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
			primaryKey: 'BrandID',
			width: '100%',
			height: '500px',
			initialDataBindDepth: 0,
			localSchemaTransform: false ,
    rendered: function (evt, ui) {
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#manufacturer_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }                    
    }
);
		
			function brandsGrid(legal_entity_id)
	{


	$('#manufacturer_brands_grid').igHierarchicalGrid({ 

		dataSource: '/manufacturer/getBrands/'+legal_entity_id,
		autoGenerateColumns: false,
		autoGenerateLayouts: false,
		mergeUnboundColumns: false, 
		responseDataKey: 'Records', 
		generateCompactJSONResponse: false, 
		expandColWidth: 0,
		enableUTCDates: true, 
        renderCheckboxes: true,
		columns: [ 

			{headerText: 'BrandID',key: 'BrandID',dataType: 'number',width: '0%'},
			{headerText: 'Brand Name',key: 'BrandName',dataType: 'string',width: '15%'},
			{headerText: 'Description',key: 'Description',dataType: 'string',width: '45%'}, 
			{headerText: '#Products',key: 'Products',dataType: 'number',width: '15%'}, 
			{headerText: 'Authorized',key: 'Authorized',dataType: 'bool',width: '15%'}, 
			{headerText: 'Trademark',key: 'Trademark',dataType: 'bool',width: '15%'}, 
			{headerText: 'Action',key: 'Action',dataType: 'string',width: '15%'}
                        ],

			features: [
			{
				name: "Filtering",
				type: "remote",
				mode: "simple",
				filterDialogContainment: "window",
				columnSettings: [
                {columnKey: 'Products', allowFiltering: false },
                {columnKey: 'Action', allowFiltering: false },
				]	
			},
			{
				name: 'Sorting',
				type: 'remote',
				persist: false,
				columnSettings: [
                {columnKey: 'Products', allowSorting: false },
                {columnKey: 'Action', allowSorting: false },
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
			localSchemaTransform: false });
	}		
function totGrid(supplier_id)	
	{

	$('#manufacturer_product_grid').igHierarchicalGrid({ 

		dataSource: '/manufacturer/getProducts/'+supplier_id,
		autoGenerateColumns: false,
		autoGenerateLayouts: false,
		mergeUnboundColumns: false, 
		responseDataKey: 'Records', 
		generateCompactJSONResponse: false, 
		expandColWidth: 0,
		enableUTCDates: true, 
		columns: [ 

			{headerText: '',key: 'Logos',dataType: 'string',width: '9%'},
			//{headerText: 'Manufacturer',key: 'Manufacturer',dataType: 'string',width: '12%'},
			{headerText: 'Brand',key: 'BrandLogo',dataType: 'string',width: '9%'}, 
			{headerText: 'Product Name',key: 'ProductName',dataType: 'string',width: '12%'}, 
			{headerText: 'Product Title',key: 'ProductTitle',dataType: 'string',width: '12%'}, 
			{headerText: 'SKU',key: 'SKU',dataType: 'string',width: '8%'}, 
			{headerText: 'UPC/EAN',key: 'UPC',dataType: 'string',width: '8%'},
			{headerText: 'Weight',key: 'Weight',dataType: 'string',width: '5%'},
			{headerText: 'Created By',key: 'CreatedBy',dataType: 'string',width: '8%'}, 
			{headerText: 'Created On',key: 'CreatedOn',dataType: 'date',format: "dateTime", width: '8%'}, 
			{headerText: 'Approved By',key: 'ApprovedBy',dataType: 'string',width: '8%'}, 
			{headerText: 'Approved On',key: 'ApprovedOn',dataType: 'date',format: "dateTime", width: '8%'}, 
			{headerText: 'Action',key: 'Action',dataType: 'string',width: '5%'}
                        ],

			features: [
			{
				name: "Filtering",
				type: "remote",
				mode: "simple",
				filterDialogContainment: "window",
				columnSettings: [
                {columnKey: 'BrandLogo', allowFiltering: false },
                {columnKey: 'Logos', allowFiltering: false },
                {columnKey: 'Action', allowFiltering: false },
				]
			},
			{
				name: 'Sorting',
				type: 'remote',
				persist: false,
				columnSettings: [
                {columnKey: 'BrandLogo', allowSorting: false },
                {columnKey: 'Logos', allowSorting: false },
                {columnKey: 'Action', allowSorting: false },
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
			primaryKey: 'ProductID',
			width: '100%',
			initialDataBindDepth: 0,
			localSchemaTransform: false });
	}
	