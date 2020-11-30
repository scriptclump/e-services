@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('style')
{{HTML::style('jqwidgets/styles/jqx.base.css')}}
@stop
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption">Seller List</div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>
      </div>
      <div class="portlet-body form" id="form_wizard_1">
                <div class="form-wizard">
                    <div class="form-body">
                        <div class="box">
                            <div class="tile-body nopadding">
                                <div id="legal_entity_grid">
                                    <table id="logisticPrtnersList"></table>
                                </div>                                
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
    </div>
  </div>
</div>



<div class="box">
    <div class="col-sm-12">
        <div class="tile-body nopadding">                  
            <div id="jqxgrid"  style="width:100% !important;"></div>
            <button data-toggle="modal" id="edit" class="btn btn-default" data-target="#wizardCodeModal" style="display: none"></button>
        </div>
    </div>

</div>
<div class="modal fade" id="wizardCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">

    <div class="modal-dialog wide">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>

                <h4 class="modal-title" id="basicvalCode">Create User</h4>

            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>     
            <div class="modal-body" id="userDiv">

            </div>

        </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->

</div><!-- /.modal --> 
@stop
<style>
    .ui-iggrid-filterrow{display: none !important;}    
</style>
@include('includes.jqx')
@include('includes.validators')
@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function ()
    {
    //ajaxCall();


    $('#logisticPrtnersList').igHierarchicalGrid({

    dataSource: '/seller/showsellerlist',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            renderCheckboxes: true,
            columns: [                
            { headerText: 'Legal Entity Name', key: 'business_legal_name', dataType: 'string', width: '25%' },
            { headerText: 'Business Type', key: 'businesstype',dataType: 'string', Width: '20%'},
            { headerText: 'City Name', key: 'city', datatype: 'string', width: '25%'},
            { headerText: 'Profile Complete', key: 'profile_completed', datatype: 'string', Width: '20%'},
            { headerText: 'Actions', key: 'actions', dataType: "string", width: '15%'},
            ],
            columnLayouts: [
            {
            dataSource: '/seller/showchildsellerlist',
                    autoGenerateColumns: false,
                    autoGenerateLayouts: false,
                    mergeUnboundColumns: false,
                    responseDataKey: 'Records',
                    generateCompactJSONResponse: false,
                    enableUTCDates: true,
                    columns: [
                    {
                    headerText : 'Sc Detail Id',
                            key: 'se_detail_id',
                            dataType: 'string',
                            width:'0%',
                    },
                    {
                    headerText: 'Seller Name',
                            key: 'sellername',
                            dataType: 'string',
                            width: '20%',
                    },
                    {
                    headerText: 'Channel',
                            key: 'channel',
                            dataType: 'string',
                            width: '20%',
                           
                    },
                    {
                    headerText: 'Fulfillment Center',
                            key: 'fulfillment_name',
                            dataType: 'string',
                            width: '20%'
                    },
                    {
                    headerText: 'Last Order Sync',
                            key: 'last_order_sync',
                            dataType: 'string',
                            width: '20%'
                    },
                    {
                    headerText: 'Last Inventory Sync',
                            key: 'last_inventory_sync',
                            dataType: 'string',
                            width: '20%'
                    },
                    {
                    headerText: 'Connector Status',
                            key: 'connector_status',
                            dataType: 'string',
                            width: '15%'
                    },
                    {
                    headerText: 'Action',
                            key: 'actions',
                            dataType: 'string',
                            width: '10%'
                    }
                    ],
                    key: 'Products',
                    foreignKey: 'ID',
                    primaryKey: 'se_detail_id',
                    width: '100%',
                    features: [
                    {
                    name: 'Paging',
                            type: "local",
                            pageSize: 4
                    },
                    {
                    name: 'Filtering',
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings:[
                            {columnKey: 'channel', allowFiltering: false},
                            {columnKey: 'actions', allowFiltering: false},
							{columnKey: 'sellername', allowFiltering: false},
                            {columnKey: 'connector_status', allowFiltering: false},
                            {columnKey: 'last_inventory_sync', allowFiltering: false},
                            {columnKey: 'last_order_sync', allowFiltering: false},
                            {columnKey: 'fulfillment_name', allowFiltering: false},
                            ]
                    },
                    {
                    name: 'Sorting',
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings:[
                            {columnKey: 'channel', allowSorting: false},
                            {columnKey: 'actions', allowSorting: false}

                            ]
                    },
                    ]
            }],
            features: [
            {
            name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [{columnKey: "actions", allowFiltering: false}]
            },
            {
            name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings:[{ columnKey: 'actions', allowSorting: false}]
            },
            /*{
            recordCountKey: 'TotalRecordsCount',
                    chunkIndexUrlKey: 'page',
                    chunkSizeUrlKey: 'pageSize',
                    chunkSize: 5,
                    name: 'AppendRowsOnDemand',
                    loadTrigger: 'auto',
                    type: 'remote'
            }*/
            {
             name: 'Paging',
             type: "local",
             pageSize: 15
           }
            ],
            primaryKey: 'legal_entity_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false })
    });
    function deleteEntityType(user_id)
    {
    var decission = confirm("Are you sure you want to Delete.");
    if (decission == true)
            window.location.href = 'users/delete/' + user_id;
    }
</script>   
<script type="text/javascript">
    $(document).ready(function () {
    $("#addUser").click(function () {
    //alert($(this).attr('data-url'));
    $.get($(this).attr('data-url'), function (response) {
    $("#basicvalCode").html('Create New User');
    $("#userDiv").html(response);
    });
    });
    });
    $(document).ready(function () {
    $("#addNewUser").click(function () {
    //alert($(this).attr('data-url'));
    $.get($(this).attr('data-url'), function (response) {
    $("#basicvalCode").html('Add New User');
    $("#userDiv").html(response);
    });
    });
    });
    function editUser(id)
    {
    $.get('users/edit/' + id, function (response) {
    $("#basicvalCode").html('Edit User');
    $("#userDiv").html(response);
    $("#edit").click();
    });
    }
    
    function getLegalentity(legalEntity)
    {
        var decission = confirm("Are you sure you want to Delete.");
        if (decission == true){            
            $.ajax({                
                url:'/seller/legalentity/delete',
                data: 'legalEntity='+legalEntity,
                type:'get',
                dataType:'json',
                success:function(data)
                {
                    if(data ==1 ){
                        $("#logisticPrtnersList").igGrid("dataBind");
                    }
                    
                }                                                
            })
            //window.location.href = '/seller/legalentity/delete/' + legalEntity;
       }
      
    }
</script>
@stop