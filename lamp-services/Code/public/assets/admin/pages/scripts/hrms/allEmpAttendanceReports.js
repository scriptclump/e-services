$('#all_emp_attendance_grid').igGrid({
    dataSource: 'getAllAttendancegridedata',
    responseDataKey: 'Records',
    autoGenerateColumns: false,
    height:"465px",
    columns: [
        {headerText: 'Date', key: 'db_date', dataType: 'date',width:'400px',format: "d-MMM-yyyy (dddd)"},
        {headerText: 'In Time', key: 'in_time', dataType: 'time', width:'200px', columnCssClass: "timeGridAlignment1"},
        {headerText: 'Out Time', key: 'out_time', dataType: 'time',width:'200px', columnCssClass: "timeGridAlignment2"},
        {headerText: 'Total Hours', key: 'total_hours', dataType: 'string',width:'200px', columnCssClass: "timeGridAlignment3"},
        {headerText: 'Productive Hours', key: 'productive_hours', dataType: 'string',width:'200px', columnCssClass: "timeGridAlignment3"}
    ],	
    features: [
        {
            name: "Paging",
            type: "remote",
            pageSize: 10,            
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        },
        {
            name: "Sorting",
            sortingDialogContainment:"window",
            columnSettings: [
                {
                    columnKey: "db_date",
                    allowSorting: false
                },
                {
                    columnKey: "in_time",
                    allowSorting: true
                },
                {
                    columnKey: "out_time",
                    allowSorting: true
                },
                {
                    columnKey: "total_hours",
                    allowSorting: false
                },
                {
                    columnKey: "productive_hours",
                    allowSorting: false
                }
            ]
        }
    ],
    width: '100%',
    //initialDataBindDepth: 0,
    //localSchemaTransform: false,
	rendered: function (evt, ui) {
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#all_emp_attendance_grid_table_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});


