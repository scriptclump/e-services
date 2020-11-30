$('#attendance_grid').igGrid({
    dataSource: '/attendreports/getattendancereports',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    expandColWidth: 0,
    enableUTCDates: true,
    columns: [
        {headerText: 'Name', key: 'user_name', dataType: 'string'},
        {headerText: 'Role Name', key: 'role_id', dataType: 'string'},
        {headerText: 'First Check in', key: 'first_checkin_time', dataType: 'string'},
        {headerText: 'Last Check out', key: 'last_checkout_time', dataType: 'string'},
    ],
    features: [
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
        },
        {
            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        },
    ],
    //primaryKey: 'product_id',
    width: '100%',
    height:'100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#attendance_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#attendance_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#attendance_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#attendance_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#attendance_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#attendance_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});



