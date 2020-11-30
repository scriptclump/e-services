@extends('layouts.default') 
@extends('layouts.header') 
@extends('layouts.sideview') 
@section('content')
<span id="success_message_ajax"></span>
<?php View::share('title','DC Retailer Config - Ebutor'); ?>
    <div class="row">
        <div class="col-md-12">
            <ul class="page-breadcrumb breadcrumb">
                <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
                <li>DC Retailer Config</li>
            </ul>
        </div>
    </div>
    <div class="portlet light portlet-fit">
        <div class="portlet-title">
            <div class="caption lowercase">DC Retailer Config</div>
            <div class="tools uppercase">&nbsp;</div>
            <div class="actions">
                <div class="btn-group">
                    <a href="#addEcashCreditlimit" data-toggle="modal" class="btn green-meadow" id="addEcashCreditlimit_id">Config </a>
                </div>
            </div>
        </div>
    </div>
    <span id="successCreated_message_ajax"></span>
    <span id="successUpdated_message_ajax"></span>
    <div class="portlet-body">
        <div class="col-md-12" style="height:0px">
            <table id="customersMovGridId"></table>
        </div>
    </div>
<div class="modal modal-scroll fade in" id="ecashCreditLimitmodalID" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Edit DC Retailer Config</h4>
            </div>
            <div class="modal-body">
                <form id="ecashCreditLimitForm"  method="POST">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                    <input type="hidden" name="ecash_id" id="ecash_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Customer Type</label>
                              <select name="Customer_id" id="Customer_id"class="form-control select2me " placeholder="Select Customer">
                                  <option value=""> </option>  
                                    @foreach($customerDetails as $value)
                                    <option value="{{$value->value }}">{{ $value->master_lookup_name}}</option>
                                    @endforeach
                             </select>
                            </div>
                        <div class="col-md-6">
                            <label>Please Select DC</label>
                                <div class="form-group">
                                  <select  name="DCName_id" id="DCName_id" class="form-control select2me " placeholder="Please Select DC " required="required">
                                    <!-- <option value="0" >All DC'S</option> -->
                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                 </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group"><label>Self Order Mov</label>
                                    <input type="text" class="form-control" id="self_order_mov" name="self_order_mov" placeholder="" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>FF Order MOV</label>
                                    <input type="text" class="form-control" id="minimum_order_value" name="minimum_order_value" placeholder="" autocomplete="nope">
                                </div>
                            </div>
                      
                           
                            <div class="col-md-6">
                                <div class="form-group"><label>MOV Order Count</label>
                                    <input type="text" class="form-control" id="mov_ordercount" name="mov_ordercount" placeholder="" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Select State</label>
                             <select name="StateName_id" id="StateName_id" class="form-control select2me " placeholder="Select State">
                                  <option value=""></option>  
                                    @foreach($stateCode as $value)
                                    <option value="{{$value->zone_id }}">{{ $value->name}}</option>
                                    @endforeach
                             </select>
                            </div> 
                        </div>
<!--                             <div class="col-md-6">
                                <div class="form-group"><label>Credit Limit</label>
                                    <input type="text" class="form-control" id="Credit_Limit_id" name="Credit_Limit_id" placeholder="">
                                </div>
                            </div> -->
                        
<!--                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group"><label>Order Value</label>
                                    <input type="text" class="form-control" id="Order_Value_id" name="Order_Value_id" placeholder="">
                                </div>
                            </div>
                        </div> -->
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" id="Edit_Each_limit" class="btn green-meadow">Update</button>
                            </div>
                        </div>
                     </div>
                  </div>
                </div>
             </form>
        </div>
    </div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
@include('CustomerFeedback::ecashcreditPopup')

    @stop 
    @section('userscript')
    <script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/admin/pages/scripts/payments/payments_grid.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/admin/pages/scripts/payments/approvalscript.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 


<script type="text/javascript">
    $("#customersMovGridId").igGrid({
    primaryKey: "ecash_id",
    dataSource: '/ecashLimit/gridData',
    columns: [
        {headerText: "Customer Type", key: "master_lookup_name", dataType: "string", width: "20%"},
        {headerText: "DC Name", key: "lp_wh_name", dataType: "string", width: "18%"},
        {headerText: "Self Order MOV", key: "self_order_mov", dataType: "float", width: "15%"},
        {headerText: "FF Order MOV", key: "minimum_order_value", dataType: "float", width: "15%"},
        {headerText: "MOV Order Count", key: "mov_ordercount", dataType: "string", width: "15%"},
        {headerText: "State", key: "state_name", dataType: "string", width: "17%"},
        { headerText: "Action", key: "actions", dataType: "string", width: "10%" }
    ],
    responseDataKey: "results",
    features: [
        {
            name: "Sorting",
            sortingDialogContainment: "window",
            columnSettings: [
                {columnKey: "master_lookup_name", allowSorting: true},
                {columnKey: "lp_wh_name", allowSorting: true},
                {columnKey: "state_name", allowSorting: true},
                {columnKey: "creditlimit", allowSorting: true},
                {columnKey: "mov_ordercount", allowSorting: true},
                {columnKey: "self_order_mov", allowSorting: true},
                {columnKey: "minimum_order_value", allowSorting: true},
                {columnKey: "actions", allowSorting: false}
            ]
        },
        {
            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        },
        {
            name: "Filtering",
            allowFiltering: true,
            type: "remote",
            mode: "simple",
            columnSettings: [
                {columnKey: "master_lookup_name", allowSorting: true},
                {columnKey: "lp_wh_name", allowSorting: true},
                {columnKey: "creditlimit", allowSorting: true},
                {columnKey: "mov_ordercount", allowSorting: true},
                {columnKey: "self_order_mov", allowSorting: true},
                {columnKey: "minimum_order_value", allowSorting: true},
                {columnKey: "state_name", allowSorting: true},
                {columnKey: "actions", allowSorting: false}
            ]
        }
    ],
    width: '100%',
    // height: '500px',

});
</script>
<script type="text/javascript">
function editEcashCreditlimit(id){ 
    var token  = $("#csrf-token").val();
    $("#ecashCreditLimitmodalID").modal("toggle");  
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ecashLimit/editecashCreditLimit/' + id,
        success: function (data){   
        data=JSON.parse(data)
        console.log(data);
            $("#ecash_id").val(data.ecash_id);
            $("#DCName_id").select2('val',data.dc_id);
            $("#Customer_id").select2('val',data.customer_type);
            $("#Customer_id").select2('val',data.customer_type);
            $("#Credit_Limit_id").val(data.creditlimit);
            $("#minimum_order_value").val(data.minimum_order_value);
            $("#self_order_mov").val(data.self_order_mov);
            $("#master_lookup_id").val(data.master_lookup_id);
            $("#mas_cat_id").val(data.mas_cat_id);
            $("#master_lookup_name").val(data.master_lookup_name);
            $("#description").val(data.description);
            $("#le_wh_id").val(data.le_wh_id);
            $("#dc_type").val(data.dc_type);
            $("#lp_wh_name").val(data.lp_wh_name);
            $("#StateName_id").select2('val',data.state_id);
            $("#Pincode_id").val(data.pincode);
            $("#City_id").val(data.city);
            $("#zone_id").val(data.zone_id);
            $("#mov_ordercount").val(data.mov_ordercount);
        }
    });
}

$('#ecashCreditLimitForm').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        Customer_id: {
            validators: {
            notEmpty: {
                message: 'Please Select Any Option'
                },
            }
        },
        DCName_id: {
            validators: {
                notEmpty: {
                message: "Please Select Any Option"
                },           
            }
        },     
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

var frmData = $('#ecashCreditLimitForm').serialize();
var token  = $("#csrf-token").val();
$.ajax({
    headers: {'X-CSRF-TOKEN': token},
    type: "POST",
    url: '/ecashLimit/updateEcashLimit',
    data: frmData,
    success: function (respData){
        var message = "Updated Succesfully";
        var text_class = "success";
        if(respData == 2){
            message="Updated Succesfully";
            text_class="danger";
        }
        $('#ecashCreditLimitmodalID').modal('toggle');
        $("#successUpdated_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+message+' </div></div>' );
        $('div.alert').show();
        $('div.alert').removeClass('hide');
        $(".alert-success").fadeOut(20000);
        reloadGridData();
        $('#Edit_Each_limit').removeClass('disabled');
        $('#Edit_Each_limit').attr('disabled',false);
          
      }
    });
});
function reloadGridData(){
    $("#customersMovGridId").igGrid("dataBind");
}   
</script>

<script type="text/javascript">
    
$('#addingEcashCreditLimitForm').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        cust_mas_id: {
            validators: {
            notEmpty: {
                message: 'Please Select Customer Type'
                },
            }
        },
        dcDetail_id: {
            validators: {
                notEmpty: {
                message: "Please Select DC."
                },           
            }
        },     
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

var frmData = $('#addingEcashCreditLimitForm').serialize();
var token  = $("#csrf-token").val();
$.ajax({
    headers: {'X-CSRF-TOKEN': token},
    type: "POST",
    url: '/ecashLimit/add',
    data: frmData,
    success: function (respData){
        var success = "Created Succesfully";
        var text_message = "success";
        if(respData == 2){
            success="Updated Succesfully";
            text_message="danger";
        }
        $("#successCreated_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+success+'</div></div>' );
        $('div.alert').show();
        $('div.alert').removeClass('hide');
        $(".alert-success").fadeOut(20000);
        $('#addEcashCreditlimit').modal('toggle');
        reloadGridData();
      }
    });
});

$('#addEcashCreditlimit_id').click(function(){
    
      $('#addingEcashCreditLimitForm')[0].reset();
      $("#cust_mas_id").select2("val", "");
      $("#dcDetail_id").select2("val", "");
      $("#add_state_id").select2("val", "");
 });

</script>
    @stop 
    @section('style')

    <link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

    <style type="text/css">
     .avoid-clicks {
          pointer-events: none;
        }
    </style>