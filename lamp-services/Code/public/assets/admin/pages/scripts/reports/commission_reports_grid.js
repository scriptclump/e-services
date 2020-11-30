function mygrid(url,url2){
var columnsHeading = '';
 $.ajax({
     headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
    url: url2,
    type: 'GET',                                             
    success: function (rs) 
    {
    	var columnAppending=[];
    	for(i=0;i<rs.length;i++)
    	{
    		var jsonArray = {headerText: rs[i], 
    			 key: rs[i], 
    			 dataType: 'number', width: '190px'};
    		columnAppending.push(jsonArray);
    			
    	}
		columnsHeading = columnAppending;
    },async: false
});
 if(columnsHeading != "")
 {
 	$('#reports_grid').html("");
 	$('#reports_grid').igGrid({
		    dataSource: url,
		    autoGenerateColumns: true,
		    responseDataKey: 'Records',
		    features: [
		        
		        {
		           /* recordCountKey: 'TotalRecordsCount',
		            chunkIndexUrlKey: 'page',
		            chunkSizeUrlKey: 'pageSize',
		            chunkSize:8,
		            name: 'AppendRowsOnDemand',
		            loadTrigger: 'auto',
		            type:null*/
					name: 'Paging',
		            type: 'local',
		            pageSize: 10,
		            // recordCountKey: 'TotalRecordsCount',
		            /*pageIndexUrlKey: "page",
		            pageSizeUrlKey: "pageSize"*/
		        }
		    ],
		    width: '100%',
		    height:'450px',
		    initialDataBindDepth: 0,
		    localSchemaTransform: false,
		    rendered: function (evt, ui) {
		        $("#reports_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
		        $("#reports_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
		        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
		        $("#reports_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
		        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
		        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
		    }
		});
 	$("#reports_grid").on("iggriddatarendered", function (event, args) {
               var columns = $("#reports_grid").igGrid("option", "columns");
               console.log(columns);
               for(var idx = 0; idx < columns.length; idx++){
                   
	               var isDecimal = columns[idx].headerText.substring(0,2);
	               var newText = columns[idx].headerText.replace(" ", "\\ ");
	            //   var spaceHeader = isDecimal.replace("-", " ");
	               if(isDecimal === "1_"){
	                   
	                   var withUnderscoll = columns[idx].headerText.substring(2);
	                   var withOutScollBar = withUnderscoll.replace("-", " ");
	                   $("#reports_grid_"+newText+" > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px 0px !important;padding-right:22px;'>"+withOutScollBar+"</p>");
	                   args.owner.element.find("tr td:nth-child(" + (idx+1) + ")").css("text-align", "right","!important");
	                    args.owner.element.find("tr td:nth-child(" + (idx+1) + ")").css("padding-right", "22px","!important");
	                   console.log("i m here="+idx);
	               }else
	               {
               			var withOutScollBar = newText.replace("-", " ");
		               	$("#reports_grid_"+newText+" > span.ui-iggrid-headertext").html("<p style='text-align: left !important; margin: 0px 5px 0px !important'>"+withOutScollBar+"</p>");
		               	 args.owner.element.find("tr td:nth-child(" + (idx+1) + ")").css("padding-left", "5px","!important");

	               }
               }
           });
	 		 
 }else
 {
 	$("#reports_grid").html("No Data Available...");
 	//$('#reports_grid').igGrid("destroy");
 }
}