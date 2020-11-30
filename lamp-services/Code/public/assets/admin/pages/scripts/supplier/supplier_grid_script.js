$('#supplier_list_grid').igHierarchicalGrid({
    dataSource: '/suppliers/getSuppliers',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Name', key: 'user_name', dataType: 'string', width: '12%'},
        {headerText: 'Ref Code', key: 'le_code', dataType: 'string', width: '10%'},
        {headerText: 'State', key: 'state_name', dataType: 'string', width: '8%'},
        {headerText: 'GST Number', key: 'gst_no', dataType: 'string', width: '12%'},
        {headerText: 'Contact', key: 'Contact', dataType: 'string', width: '8%'},

        {headerText: 'SRM', key: 'SRM', dataType: 'string', width: '8%'},
        {headerText: '<i class="fa fa-tags" alt="Brands"></li>', key: 'Brands', dataType: 'number', width: '2%',template: '<div class="rightAlign"> ${Brands} </div>'},
        {headerText: '<i class="fa fa-cube" alt="Products"></i>', key: 'Products', dataType: 'number', width: '2%',template: '<div class="rightAlign"> ${Products} </div>'},
        //{headerText: '<i class="fa fa-building" alt="Warehouse"></i>', key: 'Warehouses', dataType: 'number', width: '4%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div style="text-align:center"> ${Documents_count} </div>'},
        {headerText: 'Approval Status', key: 'Status', dataType: 'string', width: '10%'},
        /*{headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '8%'},*/
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '8%'},
        /*{headerText: 'Approved By', key: 'Approved_By', dataType: 'string', width: '8%'},*/
        {headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '8%'},        
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    columnLayouts: [
        {
            dataSource: 'suppliers/getBrandsGrid',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Brand ID', key: 'BrandID', dataType: 'number', width: '0%'},
                {headerText: 'Logo', key: 'BrandLogo', dataType: 'string', width: '10%'},
                {headerText: 'Brand', key: 'BrandName', dataType: 'string', width: '40%'},
                //{headerText: '<spna title="Trademarked Authorized">TM</sapn>', key: 'Trademarked', dataType: 'string', width: '6%'},
                //{headerText: '<i class="fa fa-user" title="Is Authorized"></i>', key: 'is_authorised', dataType: 'string', width: '7%'},
                {headerText: '<i class="fa fa-cube alt="Products" title="Products"> </i>', key: 'Products', dataType: 'number', width: '7%'},
                {headerText: '<i class="fa fa-camera" title="Products with Images"></i>', key: 'With_Images', dataType: 'number', width: '7%'},
                {headerText: '<img src="/assets/admin/pages/img/noimage.png" width="22" height="22" title="Products without Images"/>', key: 'Without_Images', dataType: 'number', width: '7%'},
                {headerText: '<i class="fa fa-cubes" title="Products with Inventory"></i>', key: 'With_Inventory', dataType: 'sting', width: '7%'},
                {headerText: '<img src="/assets/admin/pages/img/noinventroy.png" width="22" headerTextight="22" title="Products without Inventory"/>', key: 'Without_Inventory', dataType: 'string', width: '7%'},
                {headerText: '<img src="/assets/admin/pages/img/approved.png" width="22" height="22" title="Approved"/>', key: 'Approved', dataType: 'string', width: '7%'},
                {headerText: '<img src="/assets/admin/pages/img/pending.png" width="22" height="22" title="Pending for Approval"/>', key: 'Pending', dataType: 'string', width: '7%'},
                //{headerText: 'Actions',key: 'Action',dataType: 'string',width: '5%'},
            ],
            columnLayouts: [
                {
                    dataSource: 'suppliers/getProductsGrid',
                    autoGenerateColumns: false,
                    autoGenerateLayouts: false,
                    mergeUnboundColumns: false,
                    responseDataKey: 'Records',
                    generateCompactJSONResponse: false,
                    enableUTCDates: true,
                    renderCheckboxes: true,
                    columns: [
                        //{headerText: 'Product ID', key: 'Product_ID', dataType: 'number', width: '0%'},
                        {headerText: 'Image', key: 'ProductLogo', dataType: 'string',width: '10%'},
                        {headerText: 'Category', key: 'Category', dataType: 'string',width: '15%'},
                        {headerText: 'Product Name', key: 'Product_Name', dataType: 'string', width: '20%'},
                        //{headerText: 'Currency', key: 'Currency', dataType: 'string'},
                        {headerText: 'MRP', key: 'MRP', dataType: 'string', width: '10%', template: "${Currency}  ${MRP}"},
                        {headerText: 'Warehouse', key: 'whname', dataType: 'string', width: '15%'},
                        {headerText: 'BasePrice', key: 'BasePrice', dataType: 'number', width: '10%', template: "${Currency}  ${BasePrice}"},
                        {headerText: 'Tax', key: 'Tax', dataType: 'string', width: '9%'},
                        {headerText: 'LP', key: 'Elp', dataType: 'string',width: '9%'},
                        {headerText: 'Margin', key: 'Emargin', dataType: 'string',width: '9%'},
                        {headerText: 'Inv', key: 'Inv', dataType: 'string',width: '9%'},
                        {headerText: 'ATP', key: 'Atp', dataType: 'string',width: '9%'},
                        {headerText: 'Eff Date', key: 'effective_date', dataType: 'string',width: '9%'},
                        {headerText: 'Subscribe', key: 'Subscribe', dataType: 'string',width: '12%'},
                        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '9%'},
                    ],
                    features: [
                        {
                            name: 'Paging',
                            type: "remote",
                            pageSize: 10,
                            recordCountKey: 'TotalRecordsCount',
                            pageIndexUrlKey: "page",
                            pageSizeUrlKey: "pageSize"
                        }],
                    key: 'Products',
                    foreignKey: 'BrandID',
                    primaryKey: 'Product_ID',
                    width: '100%'
                }


            ],
            features: [
            {
                name: 'Paging',
                type: "remote",
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }],
            key: 'Brands',
            foreignKey: 'SupplierID',
            primaryKey: 'BrandID',
            width: '100%'
        }],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#supplier_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    },
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});


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
                persist: false,
                columnSettings: [
                {columnKey: 'Tax_Amt', allowSorting: false},
            ]

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


$('#hr_list_grid').igGrid({
    dataSource: '/suppliers/gethrproviders',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Human Resource Provider', key: 'user_name', dataType: 'string', width: '20%'},
        {headerText: 'Contact', key: 'Contact', dataType: 'string', width: '12%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '20%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div class="rightAlign"> ${Documents_count} </div>'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '18%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        //{headerText: 'Approved By', key: 'Approved_By', dataType: 'string', width: '8%'},
        //{headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '7%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});


$('#veh_pro_list_grid').igGrid({
    dataSource: '/suppliers/getvehproviders',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Vehicle Provider', key: 'user_name', dataType: 'string', width: '20%'},
        {headerText: 'Contact', key: 'Contact', dataType: 'string', width: '12%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '20%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div class="rightAlign"> ${Documents_count} </div>'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '18%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        //{headerText: 'Approved By', key: 'Approved_By', dataType: 'string', width: '8%'},
        //{headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '7%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#veh_pro_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    }, 
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});


$('#veh_list_grid').igGrid({
    dataSource: '/suppliers/getvehicleslist',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        {headerText: 'Vehicle Model Name', key: 'user_name', dataType: 'string', width: '15%'},
        {headerText: 'Vehicle Type', key: 'vehicletype', dataType: 'string', width: '10%'},
        {headerText: 'Vehicle Provider', key: 'veh_provider', dataType: 'string', width: '12%'},
        {headerText: 'Registration #', key: 'reg_no', dataType: 'string', width: '15%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '15%'},
        {headerText: 'Hub Name', key: 'Warehouse', dataType: 'string', width: '12%'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '15%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '10%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'Status', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'reg_no', allowFiltering: true},
                {columnKey: 'is_active', allowFiltering: false},
                {columnKey: 'Created_On', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#veh_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    },
        
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});

$('#ser_pro_list_grid').igGrid({
    dataSource: '/suppliers/getserviceprovider',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Service Provider Name', key: 'user_name', dataType: 'string', width: '20%'},
        {headerText: 'Contact', key: 'Contact', dataType: 'string', width: '12%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '20%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div class="rightAlign"> ${Documents_count} </div>'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '18%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        //{headerText: 'Approved By', key: 'Approved_By', dataType: 'string', width: '8%'},
        //{headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '7%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#ser_pro_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    },
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});
$('#space_list_grid').igGrid({
    dataSource: '/suppliers/getspace',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Space Name', key: 'user_name', dataType: 'string', width: '20%'},
        {headerText: 'Owner', key: 'Contact', dataType: 'string', width: '12%'},
        {headerText: 'Space Provider', key: 'space_provider', dataType: 'string', width: '15%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '15%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div class="rightAlign"> ${Documents_count} </div>'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '18%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        //{headerText: 'Space Provider', key: 'space_provider', dataType: 'string', width: '8%'},
        //{headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '7%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#space_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#space_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#space_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#space_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#space_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#space_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    },
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});
$('#space_pro_list_grid').igGrid({
    dataSource: '/suppliers/getspaceprovider',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true,
    renderCheckboxes: true,
    columns: [
        //{headerText: 'SupplierID', key: 'SupplierID', dataType: 'number', width: '0%'},
        //{headerText: 'Logo', key: 'SupplierLogo', dataType: 'number', width: '10%'},
        {headerText: 'Space Provider Name', key: 'user_name', dataType: 'string', width: '20%'},
        {headerText: 'Contact', key: 'Contact', dataType: 'string', width: '12%'},
        {headerText: 'Relationship Manager', key: 'SRM', dataType: 'string', width: '20%'},
        {headerText: '<i class="fa fa-paperclip" alt="Documents"></i>', key: 'Documents_count', dataType: 'number', width: '4%',template: '<div class="rightAlign"> ${Documents_count} </div>'},
        {headerText: 'Created By', key: 'Created_By', dataType: 'string', width: '18%'},
        {headerText: 'Created On', key: 'Created_On', dataType: 'date', format: "dateTime", width: '14%'},
        //{headerText: 'Approved By', key: 'Approved_By', dataType: 'string', width: '8%'},
        //{headerText: 'Approved On', key: 'Approved_On', dataType: 'date', format: "dateTime", width: '7%'},
        {headerText: 'Status', key: 'Status', dataType: 'string', width: '8%'},
        {headerText: 'Is Active', key: 'is_active', dataType: 'string', width: '6%'},
        {headerText: 'Action', key: 'Action', dataType: 'string', width: '6%'}
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'Action', allowFiltering: false},
                {columnKey: 'SupplierLogo', allowFiltering: false},
                {columnKey: 'SRM', allowFiltering: true},
                {columnKey: 'Status_checked', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'Action', allowSorting: false},
                {columnKey: 'is_active', allowSorting: false},
            ]
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
    primaryKey: 'SupplierID',
    dataRendered: function(evt, ui) {
          
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();         
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
        $("#space_pro_list_grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();   
    $('.showConsolidate').show();
    },
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false});
