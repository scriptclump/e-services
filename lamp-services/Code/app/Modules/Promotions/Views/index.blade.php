@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget" style="height:650px;">
<div class="portlet-title">
<div class="caption">
PROMOTIONS
</div>
<div class="actions">
<a href="/promotions/addpromotion" class="btn green-meadow">Add Promotion Template</a>
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
</div>
</div>
<div class="portlet-body">

<div class="row">
<div class="col-md-6">
<div class="caption">
 
<span class="caption-subject bold font-blue uppercase"> SORT BY :</span>
<span class="caption-helper sorting">
<a href="javascript:;" id="all"  onclick = "filterdata('all')" class="active">All</a> &nbsp;&nbsp;
<a href="javascript:;" id="Active" onclick = "filterdata('Active')">Active</a> &nbsp;&nbsp;
<a href="javascript:;" id="In Active" onclick = "filterdata('In Active')">In Active</a> &nbsp;&nbsp;


</span>
</div>
</div>
<div class="col-md-6 pull-right text-right">

</div>
</div>

<div class="row">
<div class="col-md-12">
<div class="table-scrollable">
<table id="promotionlist"></table>
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
.fa-pencil {
    color: #3598dc !important;
}
.fa-trash-o {
    color: #3598dc !important;
}
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
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
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

            $("#promotionlist").igGrid({
                dataSource: '/promotions/promotiondata',
                autoGenerateColumns: false,
                mergeUnboundColumns: false,
                responseDataKey: "results",
                generateCompactJSONResponse: false, 
                enableUTCDates: true, 
                width: "100%",
                height: "100%",
                columns: [
                    { headerText: "S No", key: "SNO", dataType: "number", width: "10%", template: "<center>${SNO}</center>" },
                    { headerText: "Promotion Name", key: "prmt_tmpl_name", dataType: "string", width: "20%" },
                    { headerText: "Offer Type", key: "offer_type", dataType: "string", width: "20%" },
                    { headerText: "Offer On", key: "offer_on", dataType: "string", width: "25%" },
                    { headerText: "Status", key: "status", dataType: "string", width: "25%" },
                    { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
                     ],
                 features: [
                     {
                        name: "Sorting",
                        type: "remote",
                        columnSettings: [
                        {columnKey: 'SNO', allowSorting: false },
                        {columnKey: 'CustomAction', allowSorting: false },
                        {columnKey: 'prmt_tmpl_name', allowSorting: true },
                        {columnKey: 'offer_type', allowSorting: true },
                        {columnKey: 'offer_on', allowSorting: true },
                        ]
                    },
                    {
                        name: "Filtering",
                        type: "remote",
                        mode: "simple",
                        filterDialogContainment: "window",
                        columnSettings: [
                            {columnKey: 'SNO', allowFiltering: false },
                            {columnKey: 'prmt_tmpl_name', allowFiltering: true },
                            {columnKey: 'offer_type', allowFiltering: true },
                            {columnKey: 'offer_on', allowFiltering: true },
                            {columnKey: 'status', allowFiltering: false },
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
                primaryKey: 'prmt_tmpl_Id',
                width: '100%',
                height: '500px',
                initialDataBindDepth: 0,
                localSchemaTransform: false,
                
            });
        });     


    var lastActiveClass = "all";

    function filterdata(status)
    {
        var sortURL = "/promotions/promotiondata?filterStatusType="+status;
        
        ds = new $.ig.DataSource({
            type: "json",
            responseDataKey: "results",
            dataSource: sortURL,
            callback: function (success, error) {
                if (success) {
                    $("#promotionlist").igGrid({
                            dataSource: ds,
                            autoGenerateColumns: false
                    });
                } else {
                    alert(error);
                }
            },
        });
        ds.dataBind();

        //change the active class
        $('#'+status).addClass('active');
        $('#'+lastActiveClass).removeClass('active');
        lastActiveClass = status;
    }
    function deleteData(deleteData){
        
        token  = $("#csrf-token").val();

        var promotion_delete = confirm("Are you sure you want to delete this promotion Data ?"), self = $(this);
            if ( promotion_delete == true )
            {
            
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+deleteData,
                type: "POST",
                url: '/promotions/deletedata',
                success: function( data ) {
                        reloadGrid();
                    }
            });  
        }    
    }

    function reloadGrid(){
            var gridURL = "/promotions/promotiondata";
            ds = new $.ig.DataSource({
                type: "json",
                responseDataKey: "results",
                dataSource: gridURL,
                callback: function (success, error) {
                    if (success) {
                        $("#promotionlist").igGrid({
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

    function updateData(updateId){
        window.location = "/promotions/updatepromotion/"+updateId;
    }
    </script>

@stop
@extends('layouts.footer')