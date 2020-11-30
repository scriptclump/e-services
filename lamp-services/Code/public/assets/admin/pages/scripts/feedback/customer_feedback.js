$('#customer_feedback_grid').igGrid({
    dataSource: 'getcustomerfeedback',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    expandColWidth: 0,
    enableUTCDates: true,
    columns: [
        //{headerText: 'fid', key: 'fid', dataType: 'string', width: '0%'},
        {headerText: 'Business Legal Name', key: 'legal_entity', dataType: 'string',width: '200px'},
        {headerText: 'Feedback Group', key: 'feedback_group_type', dataType: 'string',width: '100px'},
        {headerText: 'Feedback Type', key: 'feedback_type', dataType: 'string',width: '100px'},
        {headerText: 'Comments', key: 'comments', dataType: 'string',width: '200px'}, 
        {headerText: 'Created By', key: 'created_by', dataType: 'string',width: '100px'}, 		
        {headerText: 'Image', key: 'picture', dataType: 'string',width: '80px'},                 
        {headerText: 'Audio', key: 'audio', dataType: 'string',width: '250px'}, 
	{headerText: 'Created At', key: 'created_at', dataType: 'string', columnCssClass: "centerAlignment", format: "date",width: '150px'},
        {headerText: 'Action', key: 'Actions', dataType: 'string',width: '50px'},
    ],
	
	rowsRendered: function (evt, ui) {
				modalMessage = new GridModalMessage(ui.owner);
				if (ui.owner.dataSource.dataView().length === 0) {
					modalMessage.show("Records not found.");
				}
				else
				{
					modalMessage.hide();
				}
	},	
	
    features: [
        {
            name: "Filtering",
            type: 'local',
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                    {columnKey: 'audio', allowFiltering: false},
                    {columnKey: 'Actions', allowFiltering: false},
					{columnKey: 'picture', allowFiltering: false},
					//{columnKey: 'created_at', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'audio', allowSorting: false},
                {columnKey: 'Actions', allowSorting: false},
                {columnKey: 'picture', allowSorting: false},
            ],
        },
 
        {
            name: 'Paging',
            type: "local",
            pageSize: 10
        }
    ],
    primaryKey: 'fid',
    width: '100%',
    height: '420px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#customer_feedback_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});



