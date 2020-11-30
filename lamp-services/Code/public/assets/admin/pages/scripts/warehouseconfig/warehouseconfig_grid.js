$('#warehouse_config_grid').igTreeGrid({
    dataSource: 'getwarehouseconfig',
    responseDataKey: 'Records',
    autoGenerateColumns: false,
    primaryKey: "wh_loc_id",
    height:"520px",
    columns: [
        {headerText: 'Warehouse Loc ID', key: 'wh_loc_id', dataType: 'number', hidden:'true',width:'50px'},
        {headerText: 'Warehouse ID', key: 'le_wh_id', dataType: 'string', hidden:'true',width:'50px'},
        {headerText: 'Warehouse Location', key: 'wh_location', dataType: 'string',width:'350px'},
        {headerText: 'Location Type', key: 'wh_location_types', dataType: 'string',width:'120px'},
        {headerText: 'Res prod Grp', key: 'res_prod_grp_id', dataType: 'string',width:'200px'},
        {headerText: 'Pref Product', key: 'pref_prod_id', dataType: 'string',width:'200px'},
        {headerText: 'Bin Type', key: 'bin_type', dataType: 'string',width:'100px'},
        {headerText: 'Length', key: 'length', dataType: 'string',width:'80px'},
        {headerText: 'Breadth', key: 'breadth', dataType: 'string',width:'80px'},
        {headerText: 'Height', key: 'height', dataType: 'string',width:'80px'},
       // {headerText: 'Sort Order', key: 'sort_order', dataType: 'number',width:'80px'},
        {headerText: 'Sort Order', key: 'sort_order', dataType: 'string',width:'100px'},
        {headerText: 'Action', key: 'Actions', dataType: 'string',width:'80px'},
    ],	
    childDataKey: "locations",
    initialExpandDepth: 0,
    features: [
        {
            name: "Filtering",
            type:'local',
            columnSettings: [
                    {columnKey: 'Actions', allowFiltering: false},
            ]
        },

/*        {
            name: 'Paging',
            type: "local",
            pageSize: 10,
        },
*/		
        {
            name: "ColumnFixing",
            fixingDirection: "right",
            columnSettings: [
                {
                    columnKey: "Actions",
                    isFixed: true,
                    //allowFixing: false
                }
            ]
        },
    ],
    width: '100%',
    //initialDataBindDepth: 0,
    //localSchemaTransform: false,
	rendered: function (evt, ui) {
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#warehouse_config_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});


