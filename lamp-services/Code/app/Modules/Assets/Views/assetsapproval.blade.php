@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'ApprovalAsset'); ?>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">ASSETS APPROVAL DASHBOARD</div>

                <div class="actions">
                        <a href="#" data-id="#" data-toggle="modal" data-target="#approve_asset_product" class="btn green-meadow">Asset Request</a>
                </div>
            </div>
        </div>

            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <table id="assetsapprovalgrid"></table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="approve_asset_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="#">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Asset Request</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => '/assets/addAsset', 'id' => 'approval_asset_data'))}}


                                        <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Manufacturer</label>

                                                <select id="mdl_manufac"  name="mdl_manufac" class="form-control select2me" onchange="loadBrand();" >
                                                        <option value = "">--Please select--</option>
                                                    @foreach($getManufactureDetails as $details)
                                                        <option value = "{{$details->legal_entity_id}}">{{$details->business_legal_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Brand</label>
                                                <select id ="mdl_brand"  name ="mdl_brand" class="form-control select2me" onchange="loadProduct();">
                                                        <option value = "">--Please select--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6" >
                                            <div class="form-group" id="approve_category_div">
                                                <label class="control-label">Category</label>
                                                <select id="mdl_category"  name="mdl_category" class="form-control select2me"  onchange="loadProduct();">
                                                        <option value = "">--Please select--</option>
                                                    @foreach($getCategoryDetails as $categ)
                                                        <option value = "{{$categ->category_id}}">{{$categ->cat_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <div id="approve_product_check" class="form-group">
                                            <label for="multiple" class="control-label">Asset Name</label>
                                                <select name="approve_product" id="approve_product" class="form-control select2me">
                                                   
                                                </select>
                                        </div> 
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Allocate To</label>
                                                <select id="asset_allocate_to"  name="asset_allocate_to" class="form-control select2me">
                                                        <option value = "">--Please select--</option>
                                                    @foreach($allocationNames as $name)
                                                        <option value = "{{$name->user_id}}">{{$name->firstname}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="multiple" class="control-label">Notes</label>
                                            <textarea id="appr_notes" name="appr_notes" class="form-control"></textarea>
                                        </div> 
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                            <button type="submit" class="btn green-meadow" id="asset-save-button">Save Request</button>
                                            </div>
                                        </div>
                                                {{ Form::close() }}                                         
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>


        <div class="modal fade" id="approve_asset_process" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Approve Data</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '/Assetsapproval/approveasset', 'id' => 'approve_asset'))}}
                                
                                        

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Asset Name</label>
                                                <input type="text" id="show_asset_name" name="show_asset_name" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="multiple" class="control-label">Allocated To</label>
                                                <input type="text" name="request_allocate_name" id="request_allocate_name" class="form-control" readonly>
                                                
                                        </div> 
                                        </div>
                                    </div>

                                            <div id="apprFlagSection" style = "display:none;">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Approval Status</label>
                                                        <select id ="NextStatusID" name="NextStatusID" class="form-control">
                                                            
                                                        </select>
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Comment</label>
                                                        <textarea id ="approve_comment" name="approve_comment" class="form-control"></textarea>
                                                        <input type="hidden" id="CurrentStatusID" name="CurrentStatusID"/>
                                                        <input type="hidden" id="hidden_approval_id" name="hidden_approval_id"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                <button type="submit" class="btn green-meadow" id="price-save-button">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



                    <!-- View History expenses -->

        <div class="modal fade" id="view-approval-history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel"> VIEW HISTORY  ASSETS APPROVAL</h4>
                        </div>
                        <div class="modal-body">

                    <!-- <div class="row">
                        <div class="col-md-6">
                            
                            <div class="row">
                            <div class="col-md-6"><strong>Requested For :</strong></div>
                            <div class="col-md-6" id = "requested_for"></div>
                            </div>

                            <div class="row">
                            <div class="col-md-6"><strong>Amount :</strong></div>
                            <div class="col-md-6" id="req_amount"></div>
                            </div>

                            <div class="row">
                            <div class="col-md-6"><strong>Date :</strong></div>
                            <div class="col-md-6" id ="req_date"></div>
                            </div>
                        </div>
                    </div> -->
                        <div class="row">

                            <div class="col-md-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Asset Approval History</h3>
                                        
                                    </div>
                                    <table class="table table-hover" id="dev-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Role</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assetshistoryContainer">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        </div>
                    </div>
                </div>
        </div>





        </div>
    </div>
</div>

@stop

@section('style')

<style type="text/css">
.timline_style {
    padding: 0 5px 5px 13px !important;
    margin-bottom:-32px !important;
}
.timeline-body {
    position: relative !important;
    padding: 13px !important;
    margin-top: 19px !important;
    margin-left: 71px !important;
}
.changedByName{margin-left:-71px !important;}
.modal-content {
    padding-bottom: 20px;
}
.push_right {
    margin-left: 30px !important;
}
.timeline {
margin-bottom: 0px !important;
}

.timeline-body {
    font-weight: normal !important;
}

.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:410px !important; white-space: pre-wrap !important;}
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>

<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />


@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/select2-promotions/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>


<script type="text/javascript">
$(function () {

     //for date 
    var date = new Date();
    $('#date').datepicker({
        dateFormat: 'yy/mm/dd'
    });
    
    // Load Grid Data 
    AssetApprovalGrid();
});

function loadBrand(){
    var manufac = $("#mdl_manufac").val();
    manufac = manufac=="" ? "0" : manufac;
    token  = $("#csrf-token").val(); 
    $('#mdl_brand').val('');
    // prepare the ajax call to get the brand information
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/getbrandsasmanufac/'+manufac,
        success: function( data ) {
                if(data){
                    var brand = $('#mdl_brand');
                    brand.find('option').remove().end();
                    for(var i=0; i<data.length; i++){
                        brand.append(
                            $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                        );
                    }
                }
                $('#mdl_brand').val('');
            }
    });
}


function loadProduct(){
    var category = $("#mdl_category").val();
    var brand = $("#mdl_brand").val();
    if(category==""){
        category = "0";
    }
    if(brand==""){
        brand = "0";
    }

    token  = $("#csrf-token").val(); 
    $('#approve_product').val('');

    // prepare the ajax call to get the brand information
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/loadproductinlist/'+category+'/'+brand,

        success: function( data ) {
                if(data){
                    var brand = $('#approve_product');
                    brand.find('option').remove().end();
                    
                    for(var i=0; i<data.length; i++){
                        brand.append(
                            $('<option></option>').val(data[i].product_id).html(data[i].product_title)
                        );
                    }
                }
                $('#approve_product').val('');
            }
    });
}

// for show the grid
function AssetApprovalGrid()
{  
       $("#assetsapprovalgrid").igGrid({
            dataSource: '/assets/approvaldata',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: true, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
                { headerText: "Product Name", key: "product_title", dataType: "string", width: "20%" },
                { headerText: "Allocated To", key: "AllocatedName", dataType: "string", width: "20%" },
                { headerText: "Comment", key: "asset_comment", dataType: "string", width: "20%" },
                { headerText: "Status", key: "master_lookup_name", dataType: "string", width: "10%" },
                { headerText: "Appr.Created At", key: "created_at", dataType: "date",format:"dd-MM-yyyy", width: "20%" },
                { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'product_title', allowSorting: true },
                    {columnKey: 'AllocatedName', allowSorting: true },
                    {columnKey: 'created_at', allowSorting: true },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'product_title', allowFiltering: true },
                        {columnKey: 'AllocatedName', allowFiltering: true },
                        {columnKey: 'created_at', allowFiltering: true },
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 10,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'local' 
                }                
            ]
            
        });

    var URL = window.location.href;
    var prd_id = URL.split("/openpopup/");
       if(typeof prd_id[1] != 'undefined'){
           updateApprove(prd_id[1]);  }
    }


function updateApprove(id){
    var approvaltableid  = $('#hidden_approval_id').val(id);
    $('#approve_asset_process').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/approveasset/' + id,
        success: function (data)
        {
           var apprData = data['apprData'];
           var AssetData = data['prodInfrom'];

           $("#show_asset_name").val(AssetData[0].product_title);
           $("#request_allocate_name").val(AssetData[0].AllocatedName);

                // TO FILLUP THE APPROVAL DATA
                var apprStaus = $('#NextStatusID');
                apprStaus.find('option').remove().end();

                var apprFlag = 0;
                for(var i=0; i<apprData.length; i++){
                    apprFlag =1;
                    apprStaus.append(
                        $('<option></option>').val( apprData[i].nextStatusId + "," + apprData[i].isFinalStep ).html(apprData[i].condition)
                    );

                    $("#CurrentStatusID").val(apprData[0].currentStatusID);
                }

                if(apprFlag==0){
                    $('#apprFlagSection').hide();
                }else{
                    $('#apprFlagSection').show();
                }
        }
    });
}



$('#approve_asset_product').on('show.bs.modal', function (e) {
    $("#mdl_manufac").select2('val', '');
    $("#mdl_category").select2('val', '');
    $("#mdl_brand").select2('val', '');
    $("#approve_product").select2('val', '');
    $("#asset_allocate_to").select2('val', '');
    $("#appr_notes").val("");
    $("#mdl_brand").empty();
    // Revalidating the control
    $("#approve_product_check").removeClass('has-success');
    $("#approve_category_div").removeClass('has-success');
    $("#asset-save-button").click(function(){
        $('#approval_asset_data').formValidation('revalidateField', 'approve_product');
        $('#approve_category_div').formValidation('revalidateField', 'mdl_category');

    });

});


function viewapproval(id){
    $('#view-approval-history').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/assets/gethistoryexpensesdetails/' + id,
            success: function (data)
            {
                $('#assetshistoryContainer').html(data.historyHTML);
            }
    });

}

function reloadGridData(){

    $("#assetsapprovalgrid").igGrid("dataBind");
}

$('#approval_asset_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {

        approve_product: {
                validators: {
                    notEmpty: {
                        message: ' Please Select AssetName'
                    }
                }
            },

            mdl_category: {
                validators: {
                    notEmpty: {
                        message: ' Please Select Category'
                    }
                }
            },
        
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#approval_asset_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/assets/saveapprovaldata',
        data: frmData,
        success: function (respData)
        {

            $('#approve_asset_product').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000);

            reloadGridData();
            
        }
    });
});


$('#approve_asset').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#approve_asset').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/assets/updateapprovestatus',
        data: frmData,
        success: function (respData)
        {

            $('#approve_asset_process').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000);

            reloadGridData();
            
        }
    });
});

</script>
@stop
@extends('layouts.footer') 
