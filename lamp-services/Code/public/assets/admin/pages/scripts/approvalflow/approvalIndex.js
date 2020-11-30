$(function () {

$("#approvalList").igGrid({
    dataSource: '/approvalworkflow/approvallist',
    autoGenerateColumns: false,
    mergeUnboundColumns: false,
    responseDataKey: "results",
    generateCompactJSONResponse: false, 
    enableUTCDates: true, 
    width: "100%",
    height: "100%",
    columns: [
        { headerText: "S No", key: "awf_id", dataType: "number", width: "10%", template: "<center>${slno}</center>" },
        { headerText: "Approval Status Name", key: "awf_name", dataType: "string", width: "30%" },
        { headerText: "Approval Status For", key: "master_lookup_name", dataType: "string", width: "20%" },
        { headerText: "Created By", key: "CreatedBy", dataType: "string", width: "30%" },
        { headerText: "Actions", key: "CustomAction", dataTpe: "string", width: "10%"},
            ],
        features: [
        {
            name: "Sorting",
            type: "remote",
            columnSettings: [
            {columnKey: 'slno', allowSorting: false },
            {columnKey: 'appr_status_name', allowFiltering: true },
            {columnKey: 'appr_status_for', allowFiltering: true },
            {columnKey: 'CustomAction', allowSorting: false },
                            ]
        },
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'awf_id', allowFiltering: false },
                {columnKey: 'appr_status_name', allowFiltering: true },
                {columnKey: 'appr_status_for', allowFiltering: true },
                {columnKey: 'CustomAction', allowFiltering: false },
                           ]
        },
        { 
            recordCountKey: 'TotalRecordsCount', 
            chunkIndexUrlKey: 'page', 
            chunkSizeUrlKey: 'pageSize', 
            chunkSize: 20,
            name: 'AppendRowsOnDemand', 
            loadTrigger: 'auto', 
            type: 'remote' 
        }
        
        ],
    primaryKey: 'awf_id',
    width: '100%',
    height: '500px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,

  });

});

function deleteApprovalId(awf_id){
    token  = $("#csrf-token").val();
    var vr_delete = confirm("Are you sure you want to delete this version data ?"), self = $(this);

    if ( vr_delete == true ) 
    {  
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/approvalworkflow/deleteapprovalstatusid/'+awf_id,
        success: function( data ) {
                    reloadGrid();
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Approval Data deleted successfully</div></div>');
                    $(".alert-success").fadeOut(20000)
                }
        });
    }
}
function reloadGrid(){
    var gridURL = "/approvalworkflow/approvallist";
    ds = new $.ig.DataSource({
        type: "json",
        responseDataKey: "results",
        dataSource: gridURL,
        callback: function (success, error) {
            if (success) {
                $("#approvalList").igGrid({
                        dataSource: ds,
                        autoGenerateColumns: false
                });
            } else {
                alert(error);
            }
        },
    });
    ds.dataBind();
}

setTimeout(function() {
            $('#success_message').fadeOut('slow');
        }, 3000);