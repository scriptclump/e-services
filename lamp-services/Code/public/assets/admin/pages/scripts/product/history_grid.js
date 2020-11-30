function productHistoryGrid()
{

    $('#productHistoryGrid').igGrid({
        dataSource: '/producthistorygrid/' + $('#product_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            //{headerText: 'Product ID',key: 'ProductId',dataType: 'number',width: '0%'}
             
            {headerText: 'User Name', key: 'user_name', dataType: 'string', width: '10%'},
            {headerText: 'Reason Type', key: 'reason_type', dataType: 'string', width: '25%' },
            {headerText: 'Product Data', key: 'data', dataType: 'array', width: '50%'},
            {headerText: 'Updated Date', key: 'updated_at', dataType: 'string', width: '5%'},
                ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        rendered: function (evt, ui) {
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
                    $("#productHistoryGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();  
                    $(".ui-iggrid-indicatorcontainer").find(".ui-iggrid-featurechooserbutton").remove();
                }
    });

}

$("#tab_history").click(function(){
   $("#productHistoryGrid").igGrid("dataBind"); 
});