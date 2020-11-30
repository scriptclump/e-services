@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget" style="height:650px;">
<div class="portlet-title">
<div class="caption">
App Version
</div>
<div class="tools">
<span class="badge bg-blue"><a  class="fullscreen" data-toggle="tooltip" title="Hi, This is help Tooltip!" style="color:#fff;"><i class="fa fa-question"></i></a></span>
</div>
</div>
<div class="portlet-body">
<div class="row">
<div class="col-md-6">
<div class="caption">
<span class="caption-helper sorting">
</span>
</div>
</div>
<div class="col-md-6 pull-right text-right">
<a href="/mobapp/addappversion" class="btn green-meadow">Add App Version Template</a>
</div>
</div>
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
<div class="row">
<div class="col-md-12">
<div class="table-scrollable">
<table id="appVersionlist"></table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
@stop
@section('userscript')

<style type="text/css">
.dataTables_filter{display:none;}
.dataTables_length{display:none;}
.dataTables_paginate .paging_bootstrap_number{display:none;}
#sample_editable_1_paginate{display:none;}
#sample_2_paginate{display:none;}
#sample_3_paginate{display:none;}
.dataTables_info{display:none;}
.tooltip-grid-notes { white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}


code {
    color: #5b98ce !important;
}
.codepadright{padding-right: 20px;}
.actionss{padding-left: 22px !important;}
.sorting a{ list-style-type:none !important;text-decoration:none !important;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#ddd !important;}
</style>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>

@extends('layouts.footer')
<script>
        $(function () {
            
            var url = window.location.href;
            var urlArr = url.split("/");
            var status  = urlArr[5];
            if(status == null)
            {
                status == "";
            }

            $("#appVersionlist").igGrid({
                dataSource: '/mobapp/appversionlist',
                autoGenerateColumns: false,
                mergeUnboundColumns: false,
                responseDataKey: "results",
                generateCompactJSONResponse: false, 
                enableUTCDates: true, 
                width: "100%",
                height: "100%",
                columns: [
                    { headerText: "Version ID", key: "slno", dataType: "number", width: "15%", template: "<center>${slno}</center>" },
                    { headerText: "Version Name", key: "version_name", dataType: "string", width: "30%" },
                    { headerText: "Version Number", key: "version_number", dataType: "number", width: "30%" },
                    { headerText: "App Type", key: "app_type", dataType: "string", width: "30%" },
                    { headerText: "Actions", key: "CustomAction", dataTpe: "string", width: "15%"},
                        ],
                features: [
                    {
                        name: "Sorting",
                        type: "remote",
                        columnSettings: [
                        {columnKey: 'slno', allowSorting: false },
                        {columnKey: 'version_name', allowFiltering: true },
                        {columnKey: 'version_number', allowFiltering: true },
                        {columnKey: 'app_type', allowFiltering: true },
                        {columnKey: 'CustomAction', allowSorting: false },
                                        ]
                    },
                    {
                        name: "Filtering",
                        type: "remote",
                        mode: "simple",
                        filterDialogContainment: "window",
                        columnSettings: [
                            {columnKey: 'slno', allowFiltering: false },
                            {columnKey: 'version_name', allowFiltering: true },
                            {columnKey: 'version_number', allowFiltering: true },
                            {columnKey: 'app_type', allowFiltering: true },
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
                primaryKey: 'version_id',
                width: '100%',
                height: '500px',
                initialDataBindDepth: 0,
                localSchemaTransform: false,

          //Removing filter columns in Grid
          
            rendered: function(evt, ui) {
           // $("#appVersionlist_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
           // $("#appVersionlist_container").find(".ui-iggrid-filtericonendswith").closest("li").remove(); 

              }
            
            });

        });     

        function deleteVersion(versionId){

            token  = $("#csrf-token").val();
            var vr_delete = confirm("Are you sure you want to delete this version data ?"), self = $(this);
            if ( vr_delete == true ) 
            {  
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'versionId='+versionId,
                type: "POST",
                url: '/mobapp/deleteappversion',
                success: function( data ) {
                        reloadGrid();
                        }
             });
         }
      }
        function reloadGrid()
        {
            var gridURL = "/mobapp/appversionlist";
            
            ds = new $.ig.DataSource({
                type: "json",
                responseDataKey: "results",
                dataSource: gridURL,
                callback: function (success, error) {
                    if (success) {
                        $("#appVersionlist").igGrid({
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
      function updateVersion(updateId){
        window.location = "/mobapp/updateappversion/"+updateId;
                                     }
</script>
@stop
@extends('layouts.footer')