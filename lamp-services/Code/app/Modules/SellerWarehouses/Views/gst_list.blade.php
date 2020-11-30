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
                    GST Addresses 
                </div>
                <div class="actions">
                    <a class="btn green-meadow" href="{{url('/')}}/warehouse/addGst">Add GST Address</a>
                    </div>
                    <div class="tools">
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
                            <table id="SellerGstAddressGrid"></table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<button data-toggle="modal" id="editAppVersion" class="btn btn-default" data-target="#basicvalCodeModal" style="display: none" data-url="{{URL::asset('warehouse/editGstAddress')}}"></button>
@stop

@section('style')
<style>
    .ui-iggrid .ui-iggrid-tablebody td {
        font-size: 12px !important;
        font-weight: normal;
    }
    .scroller{
        height: auto;
    }
    .rightAlignment{
        text-align: right;
    }
    .centerAlignment{
        text-align: center;
    }
</style>
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
@stop
@section('userscript')
<script type="text/javascript">
    $(document).ready(function (){
    $('#SellerGstAddressGrid').igGrid({
        dataSource: '/warehouse/gstaddresslist',
        autoGenerateColumns: false,
        mergeUnboundColumns: false,
        generateCompactJSONResponse: false,
        responseDataKey: "Records", 
        enableUTCDates: true,
        width: '100%',
        columns: [
             { headerText: "Display Name", key: "display_name", dataType: "string", width:"190px"},
             { headerText: "City", key: "city", dataType: "string", width: "90px" },
             { headerText: "State", key: "state", dataType: "string", width: "90px" },
             { headerText: "Email", key: "email", dataType: "string", width: "135px"},
             { headerText: "GSTIN", key: "gstin", dataType: "string", width: "170px" },
             { headerText: "Address", key: "address1", dataType: "string", width: "230px" },
              { headerText: "Phone Number", key: "phone_no", dataType: 'string',columnCssClass: "rightAlignment", width: "145px"},
             { headerText: "Status", key: "status",columnCssClass: "centerAlignment", dataType: "string", width: "75px" },
             { headerText: "Action", key: "actions", dataType: "string", width: "35px"},
                            
        ],
        features: [
                {
                    name:'Filtering',
                    type: "local",
                    mode: "simple",
                    allowFiltering: true,
                    filterDialogContainment: "window",
                    columnSettings: [
                     {columnKey: 'actions', allowFiltering: true },
                                                 
        ]},
                    { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging',
                    loadTrigger: 'auto',
                    type: 'local'
                     }
                            ]
                    });
    });
</script>
<script>
    function deleteEntityType(billing_id)
    {
    var decission = confirm("Are you sure you want to Delete.");
    if (decission == true)
            window.location.href = '/warehouse/deletegst/' + billing_id;
    }

</script>
<script type="text/javascript">
$(document).ready(function (){
    window.setTimeout(function(){
        $(".alert").hide();
    },3000);
});

function editGstAddress(id)
{
    alert('dbxx');
  console.log(id);
     $.get('/warehouse/editGstAddress/'+id,function(response){ 
            $("#basicvalCode").html('Edit GstAddress');
            
            $("#appVersionsDiv").html(response);
            
            $("#editWarehouse").click();
        });
}
</script>
@stop
@extends('layouts.footer')