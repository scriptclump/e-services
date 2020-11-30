@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
 @if (Session::has('message'))
     <div class="flash alert">
         <p>{{ Session::get('message') }}</p>
     </div>
     @endif
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Warehouse List 
                </div>
                <div class="actions">
                <a class="btn green-meadow" href="{{url('/')}}/warehouse/create">Select Warehouse</a>
                    <a class="btn green-meadow" href="{{url('/')}}/warehouse/addCustom">Add Custom Warehouse</a>
                    </div>
                    <div class="tools">
                    <!-- <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> -->
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                </div>
            </div>
 @if (Session::has('flash_message'))            
              <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
@endif

            <div class="portlet-body">
                <div style="height: auto; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="SellerWarehouseGrid"></table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<button data-toggle="modal" id="editAppVersion" class="btn btn-default" data-target="#basicvalCodeModal" style="display: none" data-url="{{URL::asset('warehouse/editCustom')}}"></button>
<button data-toggle="modal" id="editWarehouse" class="btn btn-default" data-target="#basicvalCodeModal" style="display: none" data-url="{{URL::asset('warehouse/editWarehouse')}}"></button>

<div class="modal fade" id="basicvalCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog wide">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
        <h4 class="modal-title" id="basicvalCode">Create New Warehouse</h4>
      </div>
      <div class="modal-body">                         
          <div class="modal-body" id="appVersionsDiv">
          </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('style')
<style>
    .ui-iggrid .ui-iggrid-tablebody td {
        font-size: 12px !important;
        font-weight: normal;
        text-align: center;
    }
    .scroller{
        height: auto;
    }
</style>
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
@stop
@section('userscript')
<script type="text/javascript">
    $(document).ready(function ()
    {
    $('#SellerWarehouseGrid').igHierarchicalGrid({

    dataSource: 'warehouse/logisticspartners',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: false,
            renderCheckboxes: true,
            //width: "1200px",
            //height: "100%",
            columns: [
            { headerText: "le_wh_id", key: "le_wh_id", dataType: "number", width: "0%", hidden: 'true' },
            /*{ headerText: "Warehouse Provider", key: "lp_name", dataType: "string", width: "25%" },*/
            { headerText: "Warehouse Name", key: "display_name", dataType: "string", width: "115px" },
            { headerText: "Warehouse Code", key: "le_wh_code", dataType: "string", width: "110px" },
            { headerText: "Warehouse Type", key: "dc_type", dataType: "string", width: "108px" },
            { headerText: "DC/FC", key: "description", dataType: "string", width: "90px"},
            { headerText: "Parent", key: "parent", dataType: "string", width: "115px"},
            { headerText: "City", key: "city", dataType: "string", width: "90px" },
            { headerText: "State", key: "state", dataType: "string", width: "90px" },
            { headerText: "GSTIN", key: "tin_number", dataType: "string", width: "170px" },
            { headerText: "FSSAI", key: "fssai", dataType: "string", width: "170px" },
            { headerText: "Address", key: "address", dataType: "string",columnCssClass:"aligncentre", width: "190px" },
            { headerText: "Margin", key: "margin", dataType: "number", width: "70px"},
            { headerText: "Credit Limit Check", key: "credit_limit_check", dataType: "string",width: "117px" },
            { headerText: "Status", key: "status", dataType: "string", width: "75px" },
            { headerText: "Action", key: "actions", dataType: "string", width: "108px" },
            ],
            columnLayouts: [
            {
                dataSource: "/warehouse/getallspokesbeats/",
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'Records',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                recordCountKey: 'totalSpokesCount',
                columns: [
                    { headerText: "pjp_pincode_area_id", key: "pjp_pincode_area_id", id:"pjp_pincode_area_id", dataType: "number", width: "10%", hidden:"true" },
                    { headerText: "Spoke", key: "spoke_name", dataType: "string", width: "25%" },
                    { headerText: "Beat", key: "pjp_name", dataType: "string", width: "25%" },
                    { headerText: "Service days", key: "days", dataType: "string", width: "20%" },
                    { headerText: "Relationship Manager", key: "rm_name", dataType: "string", width: "30%" },
//                    { headerText: "Actions", key: "actions", dataType: "string", width: "10%" }
                ],
                features: [
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "simple",
                        filterDialogContainment: "window",
                        columnSettings: [
                            {columnKey: 'actions', allowFiltering: false},
                            {columnKey: 'profile_picture', allowFiltering: false}
                        ]
                    },
                    {
                        name: 'Sorting',
                        type: 'local',
                        persist: false,
                        columnSettings: [
                            {columnKey: 'Phone', allowSorting: false},
                            {columnKey: 'Action', allowSorting: false}
                        ]

                    },
                    {
                        recordCountKey: 'TotalRecordsCount',
    //                    chunkIndexUrlKey: 'page',
    //                    chunkSizeUrlKey: 'pageSize',
    //                    chunkSize: 20,
                        name: 'AppendRowsOnDemand',
                        loadTrigger: 'auto',
                        type: 'local',
                        initialDataBindDepth: 0,
                        localSchemaTransform: false,
                        showHeaders: true,
                        fixedHeaders: true,
                        name: 'Paging',
    //                    type: "local",
                        pageSize: 20
                    }
                ]
            }],
            features: [
            {
                name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                    {columnKey: 'actions', allowFiltering: false },
                    ]
            },
            {
            name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                    {columnKey: 'action', allowSorting: false },
                    ]

            },
            {
            name: 'Paging',
                    type: "local",
                    pageSize: 10
            }            
            ],
            primaryKey: 'le_wh_id',
            width: '100%',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false

    });
    });
</script>
<script>
    function deleteEntityType(le_wh_id)
    {
    var decission = confirm("Are you sure you want to Delete.");
    if (decission == true)
            window.location.href = '/warehouse/delete/' + le_wh_id;
    }

</script>
<script type="text/javascript">
$(document).ready(function (){
    window.setTimeout(function(){
        $(".alert").hide();
    },3000);
});


function editCustomWarehouse(id)
{
  console.log(id);
     $.get('/warehouse/editCustom/'+id,function(response){ 
            $("#basicvalCode").html('Edit Custom Warehouse');
            
            $("#appVersionsDiv").html(response);
            
            $("#editAppVersion").click();
        });
}

function editWarehouse(id)
{
  console.log(id);
     $.get('/warehouse/editWarehouse/'+id,function(response){ 
            $("#basicvalCode").html('Edit Warehouse');
            
            $("#appVersionsDiv").html(response);
            
            $("#editWarehouse").click();
        });
}
</script>
@stop
@extends('layouts.footer')