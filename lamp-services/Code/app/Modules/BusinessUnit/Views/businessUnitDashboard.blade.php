@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<?php View::share('title', 'Business unit'); ?>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="portlet-title">
                <div class="caption">BUSINESS UNIT DASHBOARD</div>
                <input type="hidden" name="bu_id" value="" id="bu_id">
                 <div class="actions">
                 @if($addBusinessUnitAccess==1)
                 <a href="#" data-id="#" id="add_business" data-toggle="modal"  class="btn green-meadow">Add Business Unit</a>
                 @endif
                 </div>
            </div>

             <div class="addEditBusinessSection" id = "businessid">
                    @include('BusinessUnit::addeditBusinessUnitSection')
                </div>
</div>


            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <!-- <div id="treeGrid"></div> -->
                            <table id="treeGriddata"></table>
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
.ui-widget-content {
    font-size: 14px !important;
}

.fa-times { 
    color: red !important;
}

.jqx-widget-header {
    background: #f2f2f2 !important;
}

.fc-field {
    cursor:pointer !important;
}

.up-down {
    width:264px !important; 
    margin:3px !important;
}

.portlet.light > .portlet-title > .tools {
        padding:0px !important;
}

.portlet > .portlet-title > .tools > a {
        height: auto !important;
}

.has-feedback label~.form-control-feedback {
        top: 40px !important;
        right:10px !important;
}
.fa-plus{
    font-size: 14px !important;
}

.fa-pencil{
    font-size: 14px !important;
}

.fa-trash-o{
    font-size: 14px !important;
}

</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getbusinesslist.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

var updateBusinessID = 0;

$('#business_form_id').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            business_name: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                      }
                    }
                },
                   
            description: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                    }
                }
            },

            status: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                    }
                }
            },

        }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#business_form_id').serialize();
    var businessID = $("#add_business_id").val();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/businessunit/saveeditbusiness',
        data: frmData,
        success: function (respData)
        {
            $('#business_data').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000)
            categoryGrid();
        }
    });
});

$(document).ready(function(){
    $('#business_data').on('show.bs.modal', function (e) {

        var token  = $("#csrf-token").val();
        if(updateBusinessID!=0){
            // UPDATE PART
            $("#add_edit_flag").val("1");
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "GET",
                url: '/businessunit/getbusinesslist/' + updateBusinessID,
                success: function (data)
                {
                    //latoad Business Unit data in Dropdown
                    var parent_id = $('#parent_id');
                    parent_id.find('option').remove().end();
                    $("#parent_id").html(data);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        type: "GET",
                        url: '/businessunit/updatebusinessdata/' + updateBusinessID,
                        success: function (data)
                        {  
                            if(data[0]){
                                $("#business_name").val(data[0].bu_name);
                                $("#description").val(data[0].description);
                                $("#add_business_id").val(data[0].bu_id);
                                $("#status").val(data[0].is_active);
                                $("#parent_id").select2().select2('val',parseInt(data[0].parent_bu_id));
                                $("#parent_bu_id").val(data[0].parent_bu_id);
                                $("#cost_center").val(data[0].cost_center);
                                $("#tally_company_name").val(data[0].tally_company_name);
                                $("#sales_ledger_name").val(data[0].sales_ledger_name);
                                $("#legal_entity_bu").select2("val", data[0].legal_entity_id);

                                // Revalidate all the fields at the time of modification
                                $('#business_form_id').formValidation('revalidateField', 'business_name');
                                $('#business_form_id').formValidation('revalidateField', 'description');
                                $('#business_form_id').formValidation('revalidateField', 'status');
                            }else{
                                $('#business_data').modal('toggle');
                            } 
                        }
                    });
                }
            });
        }else{
            $("#business_name").val('');
            $("#description").val('');
            $("#add_business_id").val('');
            $("#parent_id").val("0");
            $("#status").val('');
            $("#cost_center").val('');
            $("#tally_company_name").val('');
            $("#sales_ledger_name").val('');
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "GET",
                url: '/businessunit/getbusinesslist/0',
                success: function (data)
                {
                    //latoad Business Unit data in Dropdown
                    var parent_id = $('#parent_id');
                    parent_id.find('option').remove().end();
                    $("#parent_id").html(data);
                    $("#parent_id").select2().select2('val',0);
                    // Revalidate all the fields at the time of modification
                    $('#business_form_id').formValidation('revalidateField', 'business_name');
                    $('#business_form_id').formValidation('revalidateField', 'description');
                    $('#business_form_id').formValidation('revalidateField', 'status');
                }
            });
        }
    });

});

$('#add_business').on('click', function(){
    updateBusinessID = 0;
    $('#business_data').modal('toggle');

});

//update the price (for price)
function editBusinessData(businessId){
    updateBusinessID = businessId;
    $('#business_data').modal('toggle');
}

function deleteBusinessData(businessId){
   token  = $("#csrf-token").val();
        var business_delete = confirm("Are you sure you want to delete this Business Data ?"), self = $(this);
            if ( business_delete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+businessId,
                type: "POST",
                url: '/businessunit/deletebusinessdata',
                success: function( data ) {
                    categoryGrid();
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Business Data deleted successfully</div></div>');
                $(".alert-success").fadeOut(20000)
                }
            });  
        }    
}

$(function () { 
    categoryGrid();
});

function categoryGrid(){
    var token = $("#token_value").val();
    $.ajax({
        url:"/businessunit/businesstree?_token=" + token,
        type:"GET",
        dataType:"json",
        success:function(data)
        {
            console.log(typeof(data));
            //catDetails = data;
            $('#treeGriddata').igTreeGrid({
                //dataSource: '/categories/treeCats',
                dataSource: data,
                //responseDataKey: 'result',
                autoGenerateColumns: false,
                primaryKey: "bu_id",
                height:"100%",
                columns: [
                    { headerText: "Business ID", key: "bu_id", width: "10%", dataType: "string", hidden: 'true' },
                    { headerText: "Business Unit Name", key: "bu_name", width: "42%", dataType: "string" },
                    { headerText: "Business Description", key: "description", width: "30%", dataType: "string" },
                    { headerText: "Parent", key: "parent_name", width: "30%", dataType: "string" },
                    { headerText: "Status", key: "is_active", width: "15%", dataType: "string"},
                    { headerText: "Cost Center", key: "cost_center", width: "20%", dataType: "string"},
                    { headerText: "Tally Company Name", key: "tally_company_name", width: "20%", dataType: "string"},
                    { headerText: "Sales Ledger Name", key: "sales_ledger_name", width: "20%", dataType: "string"},
                    { headerText: "Actions", key: "actions", width: "10%", dataType: "string" }
                ],
                childDataKey: "businessChild",
                initialExpandDepth: 0,
                features: [
                {
                    name: "Sorting",
                    columnSettings: [
                    {columnKey: 'bu_name', allowSorting: false },
                    {columnKey: 'description', allowSorting: false },
                    {columnKey: 'is_active', allowSorting: false },
                    {columnKey: 'actions', allowSorting: false },
                    ]
                },
                {
                    name: "Filtering",
                    columnSettings: [
                        {columnKey: 'bu_name', allowFiltering: true},
                        {columnKey: 'description', allowFiltering: false},
                        {columnKey: 'is_active', allowFiltering: false},
                        {columnKey: 'actions', allowFiltering: false},
                        
                    ]
                },
                {
                    name: "Paging",
                    pageSize: 10
                }]

            });           
        }

    });
}
</script>    
@stop   