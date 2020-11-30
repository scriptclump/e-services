@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', ' Ebutor - DC/FC Partner'); ?>
<div><span id="success_message_ajax"></span></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">DC/FC Dashboard</div> 
             
            <div class="actions">  
           @if(isset($hasAccess) && $hasAccess==1)    
           <a class="btn green-meadow" data-toggle="modal" data-target="#addMfc" id="add_stockist">Add DC/FC</a> <span data-placement="top"></span> 
           @endif
         </div>
         
            </div>                   
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="legalentityallgrid"></table>
                    </div>
                </div>
            </div>           
        </div>
 <div class="modal fade" id="legalentity_view_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="legalentityallgrid">DC/FC Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/legalentity', 'id' => 'update_legalentity_data'))}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">First Name</label>
                                                    <input type="text" name="f_name" id="f_name" class="form-control">
                                                    <input type="hidden" name="le_hidden_id" id="le_hidden_id" class="form-control">
                                                    <input type="hidden" name="user_id" id="user_id" class="form-control">
                                                </div>
                                                
                                            </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name</label>
                                                    <input type="text" name="l_name" id="l_name" class="form-control">
                                                </div>
                                            </div>                                           
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Pincode</label>
                                                    <input type="text" name="pincode" id="pincode" class="form-control">
                                                </div>
                                            </div>
                                         <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">City</label>
                                                    <input type="text" name="city_name" id="city_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number</label>
                                                    <input type="text" name="mobile_number" id="mobile_number" class="form-control">
                                                </div>
                                            </div> 
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Email</label>
                                                    <input type="text" name="email" id="email_name" class="form-control">
                                                </div>
                                            </div>                                          
                                        </div>
                                      <div class="row">
                                                                                                       
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">State</label>
                                                   <select name = "satename" id="state_id" class="form-control select2me">
                                                            @foreach($state as $value)
                                                            <option value = "{{$value->zone_id}}">{{$value->name}}</option>
                                                       @endforeach                                                    
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Gstin</label>
                                                    <input type="text" name="gstin_name" id="gstin_name" class="form-control">
                                                </div>
                                            </div> 
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Business Legal Name</label>
                                                    <input type="text" name="bu_le_name" id="bu_le_name" class="form-control">
                                                </div>
                                            </div>                                             
                                        </div>
                                <div class="form-group">
                                    <input type="checkbox" name="le_check_active" name="le_check_active"  class="form-control" value="1">
                                    Active
                               </div>
                           
                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow">Update</button>
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




     <div class="modal fade" id="addMfc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="mfCcompanyallgrid">Add DC/FC Partner</h4>
                    </div>


                    <div class="modal-body" id="addMfc">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => 'legalentity/addUsers', 'id' => 'stockistpartner'))}}
                                <input type="hidden" name="legalentityid" id='hidden_legalentityid'>
                                <input type="hidden" name="city" id="hidden_cityname">
                                         <div class="row">

                                              <div class="col-md-4" id="changetocolmd3">
                                                <div class="form-group">
                                                    <label class="control-label">Business Type<span class="required">*</span></label>
                                                   <select name = "DC_FC_id" id="DC_FC_id" class="form-control select2me">
                                                        <option value=""></option>
                                                            @foreach($dcFcTypes as $value)
                                                            <option value = "{{$value->value}}">{{$value->master_lookup_name}}</option>
                                                       @endforeach                                                    
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-md-3" style="display:none" id="dclist">
                                            <div class="form-group">
                                                <label class="control-label">DC's</label>
                                                <select name="dcs" id="dcs" class="form-control select2me" >
                                                    <option value="">--Select Dc--</option>
                                                    @if(isset($fcDc))
                                                        @foreach($fcDc as $dc)
                                                        <option value="{{$dc->le_wh_id}}">{{ $dc->business_legal_name }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                            <div class="col-md-4" id="changetocolmd3_1">
                                                <div class="form-group">
                                                    <label class="control-label">First Name<span class="required">*</span></label>
                                                 <input type="text" name="first_name" id="first_name" class="form-control" autocomplete="nope">
                                                </div>
                                                
                                            </div>
                                             <div class="col-md-4" id="changetocolmd3_2">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name<span class="required">*</span></label>
                                                    <input type="text" name="last_name" id="last_name" class="form-control" autocomplete="nope">
                                                </div>
                                            </div>                                           
                                        </div>
                                    <div class="row">
                                     <div class="col-md-3"  id="Parent_BU_Unit">
                                            <div class="form-group">
                                                <label class="control-label">Parent Business Unit<span class="required">*</span></label>
                                                <select name="parent_bu" id="parent_bu" autocomplete="nope" class="form-control select2me" ></select>
                                            </div>
                                        </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Business Legal Name<span class="required">*</span></label>
                                                    <input type="text" name="business_legal_name" id="business_legal_name" class="form-control" autocomplete="nope">

                                                </div>
                                                
                                            </div>
                                             <div class="col-md-3">
                                                <div class="form-group">                                                  
                                                    <label class="control-label">Display Name<span class="required">*</span></label>
                                                    <input type="text" name="display_Name" id="display_Name" class="form-control" autocomplete="nope">
                                                </div>
                                            </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                    <label class="control-label">FSSAI Number</label>
                                                    <input type="text" name="lic_num" id="lic_num" class="form-control" autocomplete="nope" >
                                                </div>
                                            </div>                                           
                                        </div>
                                 
                                        <div class="row">
 
                                            <div class="col-md-4">
                                            <div class="form-group">
                                               <label class="control-label">Email ID<span class="required">*</span></label>
                                                <input type="text" name="email" id="email" class="form-control" autocomplete="nope">
                                             </div> 
                                            </div> 
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">GSTIN Number</label>
                                                    <input type="text" name="gstin_number" id="gstin_number" class="form-control">
                                                </div>
                                            </div> 

                                              <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">State<span class="required">*</span></label>
                                                   <select name = "state_id" id="state_add_id" class="form-control select2me">
                                                          <option value="">Select State Name</option>
                                                            @foreach($state as $value)
                                                            <option value = "{{$value->zone_id}}">{{$value->name}}</option>
                                                       @endforeach                                                    
                                                    </select>
                                                </div>
                                            </div>                                         
                                        </div>                                      
                                          <div class="row">
                                           <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Pincode<span class="required">*</span></label>
                                                    <input type="text" name="pincode" id="pincode" class="form-control" autocomplete="nope">
                                                </div>
                                            </div> 
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Address1<span class="required">*</span></label>
                                                    <input type="text" name="address" id="address" autocomplete="nope" class="form-control">
                                                </div>
                                            </div>
                                             <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Address2</label>
                                                    <input type="text" name="address_2" id="address_2" autocomplete="nope" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                          <div class="row">
                                             <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">City<span class="required">*</span></label>
                                                    <!-- <input type="text" name="city" id="city" class="form-control" autocomplete="nope"> -->
                                                    <select name = "city_id" id="city_state_id" class="form-control select2me">
                                                          
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number<span class="required">*</span></label>
                                                    <input type="text" name="phone_number" id="phone_number" class="form-control" autocomplete="nope">
                                                </div>
                                            </div>
                                           <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Cost Center<span class="required">*</span></label>
                                                    <input type="text" name="cost_center" id="cost_center" class="form-control" autocomplete="nope" readonly="readonly">
                                                </div>
                                            </div> 
                                        <div class="col-md-3">
<!--                                                 <div class="form-group">
                                                    <label class="control-label">Image</label>
                                                 <input type="file" name="logo" id="logo" accept="image/gif, image/jpeg, image/png">
                                                </div> -->
                                            <div class="form-group">
                                                <label class="control-label">Warehouse Code<span class="required">*</span></label>
                                                <input type="text" name="Warehouse_Code" id="Warehouse_Code" class="form-control" autocomplete="nope" readonly="readonly">
                                            </div>
                                            <span id="duplicate_dcfccodeerror" style="display: none;color: red">Duplicate Warehouse code </span>
                                        </div>
 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3" id="show_virtual" style="display: none">
                                            <div class="form-group">
                                                <div class="mt-checkbox-list">
                                                    <label class="mt-checkbox">
                                                        <input type="checkbox" id="is_virtual" name="is_virtual">Is Virtual
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                     <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="mt-checkbox-list">
                                                    <label class="mt-checkbox">
                                                        <input type="checkbox" id="is_self_tax" name="is_self_tax" checked>Self Billing
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                                    <label class="control-label">Image</label>
                                                 <input type="file" name="logo" id="logo" accept="image/gif, image/jpeg, image/png">
                                                </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit"  class="btn green-meadow" id="dcfc_submit">Submit</button>
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
</div>
@stop
@section('style')
<style type="text/css">
.modal-dialog{width:81% !important;}
.row voucher_table{width:103 !important;}
timeline-badge {
    height: 80px;
    padding-right: 30px;
    position: relative;
    width: 80px;
    z-index: 1111 !important;
}
.fa-eye{
    color: cornflowerblue;
}
.amount-right{ text-align: right; padding-right: 4px;}
.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:300px !important; white-space: pre-wrap !important;}

.ui-iggrid-footer{ height: 30px !important; padding-top: 30px !important; padding-left: 10px !important;}
.timeline-body {
    font-weight: 600;
    margin-bottom: -9px !important;
    margin-left: 75px !important;
    margin-top: -45px !important;
}.amount {
    height : 40px;
}
.timline_style .timeline-badge-userpic {
        border-radius: 30px !important;}

        .timeline::before {
    background: #f5f6fa none repeat scroll 0 0;
    bottom: 0;
    content: "";
    display: block;
    margin-left: 54px;
    position: absolute;
    top: 0;
    width: 4px;
    top:62px !important;
}

#modal_padding{
    padding-top: 5px !important;
    font-size: 12px !important;

}
.ui-iggrid-summaries-footer-text-container{
   
    font-weight: bold;
    padding-left: 30px;
}
.alignCenter{
    text-align: center;
}
</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>

@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script>
/*$(function () {
    $("#legalentityallgrid").igGrid({
        dataSource: '/legalentity/legalentity',
        responseDataKey: "results",
        columns: [
            { headerText:"Logo", key:"logo", dataType: "string", width: "12%",template: "<img style=\"height:50px;\" src=\"${logo}\"/>"},
            { headerText: "Display Name", key: "name_Display", dataType: "string", width: "15%"},
            { headerText: "Business Name", key: "business_legal_name", dataType: "string", width: "20%"},
            { headerText: "Contact Name", key: "contact_name", dataType: "string", width: "20%"},
            { headerText: "Mobile",key:"phone_no",dataType: "string", width: "18%"},
            { headerText: "Email", key: "email", dataType: "string", width: "28%"},
            { headerText: "Business Type", key: "Warehouse", dataType: "string", width: "15%"},
            { headerText: "State", key: "StateName", dataType: "string", width: "15%"},
            { headerText: "City", key: "city", dataType: "string", width: "15%"},
            { headerText: "GSTIN", key: "gstin", dataType: "string", width: "15%"},
            { headerText: "Action", key: "CustomAction", dataType: "string", width: "8%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'name_Display', allowSorting: true },
                {columnKey: 'business_legal_name', allowSorting: true },   
                {columnKey: 'phone_no', allowSorting: true },                
                {columnKey: 'Email', allowSorting: true },
                {columnKey: 'Warehouse', allowSorting: true },
                {columnKey: 'pincode', allowSorting: true },
                {columnKey: 'StateName', allowSorting: true },
                {columnKey: 'City', allowSorting: true },
                {columnKey: 'gstin', allowSorting: true },
                {columnKey: 'CustomAction', allowSorting: false },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                {columnKey: 'FullName', allowFiltering: true },
                {columnKey: 'business_legal_name', allowFiltering: true },   
                {columnKey: 'phone_no', allowFiltering: true },                
                {columnKey: 'Email', allowFiltering: true },
                {columnKey: 'Warehouse', allowFiltering: true },
                {columnKey: 'pincode', allowFiltering: true },
                {columnKey: 'StateName', allowFiltering: true },
                {columnKey: 'City', allowFiltering: true },
                {columnKey: 'gstin', allowFiltering: true },
                {columnKey: 'CustomAction', allowFiltering: false },
                ]
            },
            { 
                recordCountKey: 'TotalRecordsCount', 
                pageIndexUrlKey: 'page', 
                pageSizeUrlKey: 'pageSize', 
                pageSize: 10,
                name: 'Paging', 
                loadTrigger: 'auto', 
                type: 'remote' 
            },
                            
        ],
        primaryKey: 'legal_entity_id',
        width: '100%',
        height: '400px',
        defaultColumnWidth: '100px'
    }); 

    
});*/



$(function () {

    makeAjaxCallForigGrid("/legalentity/legalentity","legalentityallgrid");

            $("#legalentityallgrid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#legalentityallgrid").igGrid("option", "columns");
                formatigGridContent(columns,"legalentityallgrid");
            });


    /*$("#legalentityallgrid").igGrid({
        dataSource: '/legalentity/legalentity',
        responseDataKey: "results",
        columns: [
            { headerText:"Logo", key:"logo", dataType: "string", width: "15%",template: "<img style=\"height:50px;\" src=\"${logo}\"/>"},
            { headerText: "Name", key: "FullName", dataType: "string", width: "15%"},
            { headerText: "Business Name", key: "business_legal_name", dataType: "string", width: "15%"},
            { headerText: "Mobile",key:"phone_no",dataType: "string", width: "15%"},
            { headerText: "Email", key: "email", dataType: "string", width: "15%"},
            { headerText: "Pincode", key: "pincode", dataType: "string", width: "10%"},
            { headerText: "State", key: "StateName", dataType: "string", width: "15%"},
            { headerText: "City", key: "city", dataType: "string", width: "15%"},
            { headerText: "GSTIN", key: "gstin", dataType: "string", width: "15%"},
            { headerText: "Action", key: "CustomAction", dataType: "string", width: "10%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'FullName', allowSorting: true },
                {columnKey: 'business_legal_name', allowSorting: true },   
                {columnKey: 'phone_no', allowSorting: true },                
                {columnKey: 'Email', allowSorting: true },
                {columnKey: 'pincode', allowSorting: true },
                {columnKey: 'StateName', allowSorting: true },
                {columnKey: 'City', allowSorting: true },
                {columnKey: 'gstin', allowSorting: true },
                {columnKey: 'CustomAction', allowSorting: false },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                {columnKey: 'FullName', allowFiltering: true },
                {columnKey: 'business_legal_name', allowFiltering: true },   
                {columnKey: 'phone_no', allowFiltering: true },                
                {columnKey: 'Email', allowFiltering: true },
                {columnKey: 'pincode', allowFiltering: true },
                {columnKey: 'StateName', allowFiltering: true },
                {columnKey: 'City', allowFiltering: true },
                {columnKey: 'gstin', allowFiltering: true },
                {columnKey: 'CustomAction', allowFiltering: false },
                ]
            },
            { 
                recordCountKey: 'TotalRecordsCount', 
                pageIndexUrlKey: 'page', 
                pageSizeUrlKey: 'pageSize', 
                pageSize: 10,
                name: 'Paging', 
                loadTrigger: 'auto', 
                type: 'remote' 
            },
                            
        ],
        primaryKey: 'legal_entity_id',
        width: '100%',
        height: '400px',
        defaultColumnWidth: '100px'
    });*/ 

    
});

function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;
            
            // Summaries UI changes
            /*var id_text = "_summaries_footer_row_text_container_sum_";
            $("#"+ selectedId +"_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#"+ selectedId + id_text + newText).attr("class", "summariesStyle").text($("#"+ selectedId + id_text + newText).text().replace(/\s=\s/g, ''));
*/
            // S.No and Column Title Adjustments below
            if (columns[idx].dataType == "number" || columns[idx].dataType == "double") {
                var isDecimal = columns[idx].headerText.substring(0, 2);
                if (isDecimal === "1_") {
                    var columnText =
                        (columns[idx].headerText.substring(columns[idx].headerText.length - 4) === "_Per") ? columns[idx].headerText.replace("_Per", " %").substring(2) : columns[idx].headerText.substring(2);
                    columnText = (columnText.substring(0, 2) === "N_") ? columnText.substring(2) : columnText;
                    $("#"+ selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                        .html("<p style='text-align: right !important; margin: 0px 5px !important;'>" + columnText.replace(/_/g, ' ') + "</p>")
                        .attr('title', columnText.replace(/_/g,' '));
                }
            } else if (columns[idx].dataType == "string") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.replace(/_/g,' '))
                    .text(newText.replace(/_/g,' '));
            } else if (columns[idx].dataType == "date") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.substring(2).replace(/_/g, ' '))
                    .text(newText.substring(2).replace(/_/g, ' '));
            }

        }
    }

function makeAjaxCallForigGrid(customUrl,selectedId) {
 $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: customUrl,
            type: 'POST',
            dataType:"json",                                          
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                if (response.headers.length > 0 && response.data.length > 0) {
                    
                    var result = customigGridColumns(response.headers);
                    var customFeatures = customigGridFeatures(10);
                   
                    $('#'+selectedId).igGrid({
                        dataSource: response.data,
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures,
                    });
                }
                /*else{
                    $('#'+selectedId+'_error')
                        .removeClass("hideError")
                        .html("No data found!");
                    $('#'+selectedId+'_table')
                        .css("display","none");
                }*/
            }
           /* error: function() {
                $('#'+selectedId+'_error')
                    .removeClass("hideError")
                    .html("Oops, <b><i>"+selectedTabName+"</i></b> Tab is not working. Refresh the page or try again later!.");
                $('#'+selectedId+'_table')
                    .css("display","none");
            }*/
        });
}


function customigGridColumns(headers) {

        var columnHeaders = [];
        var columnSummaries = [];

        for (var i = 0; i < headers.length; i++) {
            var headerDataType = "string";
            var cssClass = null;
            var customWidth = "130px";
            var customHeadText = headers[i];
            
            if (headers[i].substring(0, 2) === "1_") {
                headerDataType = "number";
                cssClass = "alignCenter";
                customWidth = "60px";

                var summaryType = (headers[i].substring(headers[i].length - 4) == "_Per")?"AVG":"SUM";
                // Summaries Cols
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: true,
                    summaryOperands: [{
                        "rowDisplayLabel": "",
                        "type": summaryType,
                        "active": true
                    }]
                });
            } else {
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: false
                });
            }

            if (headers[i].substring(0, 4) === "1_N_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    },
                    width: "auto",
                });
            } else if(headers[i].substring(0, 2) === "D_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: "date",
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "date", "dd/MM/yyyy");
                    },
                    width: "auto",
                });
            } else {
                if(headers[i].substring(0, 20) != "Legal_Entity_ID" && headers[i].substring(0, 20) != "State_ID"  && headers[i].substring(0, 20) != "Contact_Name" && headers[i].substring(0, 20) != "Phone_No" && headers[i].substring(0, 20) != "FullName" && headers[i].substring(0, 20) != "Is_Active" && headers[i].substring(0, 20) != "dc_id"){
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    width: "auto",
                });
            }
            }
        }

        return {
            "columnHeaders": columnHeaders,
            "columnSummaries": columnSummaries
        };
    }

    function customigGridFeatures(customPageSize){
        return [
                    {
                        name: 'Paging',
                        type: 'local',
                        pageSize: customPageSize,
                    },
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "simple",
                        filterDialogContainment: "window",
                    },
                    {
                        name: 'Sorting',
                        type: 'local',
                        persist: false,
                    },
                    {
                        name: "Resizing",
                    },
                    {
                        name: "RowSelectors",
                    },
                    {
                        name: "Selection",
                        multipleSelection: true,
                    },
                    {
                        name: "ColumnFixing",
                    },
                    {
                        name: "Tooltips",
                        visibility: "always",
                        showDelay: 500,
                        hideDelay: 500,
                        columnSettings: [
                            { columnKey: "CustomAction", allowTooltips: false }
                        ]
                    }
                ];
    }



$('#update_legalentity_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        f_name: {
                validators: {
                    notEmpty: {
                        message: ' Enter First Name '
                    },
                    regexp: {
                        regexp: '^[a-zA-Z]*$',
                                message: "Name  must be string only."
                        },
                }
            },
       pincode: {
                validators: {
                notEmpty: {
                message: "Pincode is required."
                },
                        stringLength: {
                        min: 6,
                                max: 6,
                                message: "Pincode  should be 6 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Pincode  must be digits only."
                        },
                    
                    }
                },
         //  gstin_name: {
         //    validators: {
         //        notEmpty: {
         //            message: ' Enter GSTIN Name '
         //        }
         //    }
         // },
        l_name: {
            validators: {
                notEmpty: {
                    message: 'Enter First Name'
                },
                 regexp: {
                    regexp:'^[a-zA-Z]*$',
                            message: "Name  must be string only."
                    },
            }
        },
          email_name: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    }
                }
            },
        /*city_name: {
            validators: {
                notEmpty: {
                    message: 'Enter City Name'
                },
             regexp: {
                regexp:'^[a-zA-Z]*$',
                        message: "Name  must be string only."
                },
            }
        },*/

        'city': {
            validators: {
                notEmpty: {
                    message: "Please Select City"
                },  
            }
        },
       mobile_number: {
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                            stringLength: {
                            min: 10,
                                    max: 10,
                                    message: "'Mobile number should be 10 digit."
                            },
                            regexp: {
                            regexp: '^[0-9]*$',
                                    message: "Mobile number must be digits only."
                            },
                    
                    }
                },
        state_id: {
            validators: {
                notEmpty: {
                    message: ' Enter State Name'
                }
            }
        },

        bu_le_name: {
            validators: {
                notEmpty: {
                    message: ' Enter Business Name'
                }
            }
        },     
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#update_legalentity_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/updateintotable',
        data: frmData,
        success: function (respData)
        {
            $('#success_message_ajax').text("Updated Succesfully");
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            
        }
    });
});

function updateDetailsData(legalid){   
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/legalentity/editData/' + legalid,
            success: function (data)
            {
              alert(data.state_id);
                if(data.is_active == 1){
                    $("#le_check_active").attr("checked","checked");
                    $("#le_check_active").parent().addClass("checked");
                }                
                $("#le_hidden_id").val(data.legal_entity_id);
                $("#user_id").val(data.user_id);
                $("#f_name").val(data.firstname);
                $("#l_name").val(data.lastname);
                $("#city_name").val(data.city);
                $("#mobile_no").val(data.phone_no);
                $("#email_id").val(data.email);
                $("#gstin_name").val(data.gstin);
                $("#pincode").val(data.pincode);
                $("#state_id").select2('val',data.state_id);
                $("#bu_le_name").val(data.business_legal_name);              
            }
    });
    $('#legalentity_view_data').modal('toggle');
}
</script> 

<script type="text/javascript">

    $('#stockistpartner').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         first_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter First Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
        gstin_number: {
            validators: {
                regexp: {
                   regexp:'^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$',  
                    message:'Enter Valid GSTIN.'
                },
                remote: {
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/legalentity/checkgstin',
                    data: function (validator, $field, value){
                        return{
                           gstin_number: $('[name="gstin_number"]').val()
                        };
                      },
                    type: 'POST',
                    delay: 1000,
                    message: 'GSTIN Number already exists  or Invalid State code'
                }
            }
        },
          state_id: {
            validators: {
                notEmpty: {
                        message: ' Enter Name '
                    }
                }
             },
            // tally_id: {
            //     validators: {
            //         notEmpty: {
            //             message: 'Enter Tally Company Id'
            //         },
            //         regexp: {
            //             regexp: '^[a-zA-Z_ ]*$',
            //                     message: "Name  must be string only."
            //             },
            //     }
            // },
              
            last_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Last Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
            /*city: {
                validators: {
                    notEmpty: {
                        message: 'Enter City Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },*/

            city_id: {
                validators: {
                    notEmpty: {
                        message: 'Select City'
                    },
                }
            },

            pincode: {
                validators: {
                notEmpty: {
                message: "Pincode is required."
                },
                        stringLength: {
                        min: 6,
                                max: 6,
                                message: "Pincode  should be 6 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Pincode  must be digits only."
                        },
                    
                    }
                },
                lic_num: {
                validators: {
             
                        stringLength: {
                        min: 14,
                                max: 14,
                                message: "License  should be 14 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "License  must be digits only."
                        },
                    
                    }
                }, 
             address: {
                validators: {
                    notEmpty: {
                        message: 'Enter Address'
                    },
                }
            },
            state_add_id: {
                validators: {
                    notEmpty: {
                        message: 'Select State'
                    },
                }
            },
          business_legal_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Business Legal Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
          email: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    },
                    remote: {
                    headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                    url: '/legalentity/emailValidator',
                    type: 'POST',
                    data: function (validator, $field, value) {
                        return  {
                            email: value
                        };
                    },
                    delay: 1000, // Send Ajax request every 1 seconds
                    message: "Email already exist!"
                    },
                }
            },
      phone_number:{
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                    stringLength: {
                    min: 10,
                            max: 10,
                            message: "'Mobile number should be 10 digit."
                    }, 

                    remote: {
                    headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                    url: '/legalentity/validator',
                    type: 'POST',
                    data: function (validator, $field, value) {
                        return  {
                            phone_number: value
                        };
                    },
                    delay: 1000, // Send Ajax request every 1 seconds
                    message: "{{trans('users.users_form_validate.user_mobile_exist')}}"
                },                   
              }
         },

    DC_FC_id: {
        validators: {
            notEmpty: {
                message: 'Select Business Type'
            },
             callback: {
                        callback: function(value, validator, $field) {
                            
                            if($('#DC_FC_id').val()==1014 ){

                                $('#stockistpartner').formValidation('enableFieldValidators', 'dcs', true);
                                return true;
                            }else{
                                $('#stockistpartner').formValidation('enableFieldValidators', 'dcs', false);
                                return true;
                            }
                            
                       }
                    }
         }
       }, 

       dcs: {
                enabled: false,
                validators: {
                    notEmpty: {
                        message: 'Select DC'
                    },
                    
                }
            },
    display_Name: {
        validators: {
            notEmpty: {
                message: 'Enter Display Name'
            }
         }
       },
  // Warehouse_Code:{
  //               validators: {
  //               remote: {
  //               headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
  //               url: '/legalentity/warehouseValidator',
  //               type: 'POST',
  //               data: function (validator, $field, value) {
  //                   return  {
  //                       Warehouse_Code: value
  //                   };
  //               },
  //               delay: 1000, // Send Ajax request every 1 seconds
  //               message: "Duplicate Warehouse"
  //           },                   
  //         }
  //    },
    parent_bu: {
        validators: {
            notEmpty: {
                message: 'Select Business Unit'
            }
         }
       },   
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#stockistpartner').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/savevianodeapilegalentity',
        data: frmData,
        success: function (respData)
        {
            if (respData.Status==200){
                $("#state_id").prop('selectedIndex',0);
                $('#stockistpartner').formValidation('resetForm', true);            
                $('.close').trigger('click');
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+ respData.message +'</div></div>' );
                $(".alert-success").fadeOut(30000);
                reloadGridData();
           }else{
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Failed to Create DC/FC</div></div>' );
           }                        
        }
    });
});

   function reloadGridData(){

    makeAjaxCallForigGrid("/legalentity/legalentity","legalentityallgrid");

}

$('#add_stockist').click(function(){
    $('#stockistpartner')[0].reset();
    $('#DC_FC_id').select2('val',"");
    $('#state_add_id').select2('val',"");
    $("#is_virtual").prop('checked',false);
    $("#is_virtual").closest('span').removeClass('checked');      
});
$('#DC_FC_id').change(function(){
    var val=$('#DC_FC_id').val();
    console.log(val);
    if(val == 1016){
        $('#show_virtual').show();
    }else{
        $('#show_virtual').hide();
    }
});


$(document).on('click', '.block_users', function (event) {
        var checked = $(this).is(":checked");
        var leId = $(this).val();
        blockLegalEntity(leId, checked, event);
    });

    function blockLegalEntity(leId, isChecked, event) {
        if(!isChecked)
        {
            var decission = confirm("Are you sure you want to In-Active the Legal Entity.");
            isChecked = 0;
        }else{
            var decission = confirm("Are you sure you want to Active the Legal Entity.");
            isChecked = 1;
        }
        event.preventDefault();
        if (decission == true) {
            $.ajax({
                method: "GET",
                url: '/legalentity/blockLegalEntity',
                data: "leId=" + leId+"&status="+isChecked,
                success: function (response) {
                   if (response==1) {
                        $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Legal Entity Status Updated Successfully</div></div>');
                         $(".alert-success").fadeOut(20000);
                          makeAjaxCallForigGrid("/legalentity/legalentity","legalentityallgrid");

            $("#legalentityallgrid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#legalentityallgrid").igGrid("option", "columns");
                formatigGridContent(columns,"legalentityallgrid");
            });
                        
                    }else {
                        alert('Unable to In-Active Legal Entity, please contact admin.');
                    }
                },
                statusCode: {
                    500: function() {
                      alert("Sorry! you cannot Active/Inactive for this Legalentity.");
                    }
                }
            });                 
        }
    }

    $('#state_add_id').on('change', function() {
        var state_id=$(this).val();
        var token  = $("#csrf-token").val();
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getcitiesbystateid',
        data:{
            state_id:state_id,
        },
        success: function (respData)
        { 
        $("#city_state_id").html(respData);  
        $("#city_state_id").select2("val", ''); 
        $('#stockistpartner').formValidation('revalidateField', 'city_id');
        }
    });
});

   $('#DC_FC_id,#city_state_id,#dcs').change(function (){

        var dcfcid=$('#DC_FC_id').val();
        var ctyid=$('#city_state_id').val();
        var stateid=$('#state_add_id').val();
        var token  = $("#csrf-token").val();
        var dcs ='';
        if(dcfcid==1014){
         dcs = $("#dcs").val();
        }
      if(dcfcid!='' && ctyid!='' && ctyid!=null && dcfcid!=null){  
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getdcfccode',
        data:{
            dcfcid:dcfcid,
            ctyid:ctyid,
            stateid:stateid,
            dcs:dcs,
        },
        success: function (respData)
        { 
         respData=JSON.parse(respData); 
        if(respData.status==200){
            $("#Warehouse_Code").val(respData.code); 
            $('#dcfc_submit').prop('disabled',false); 
            $('#duplicate_dcfccodeerror').css('display','none');
        }else{
            console.log(respData.code+'console.log(respData.code)console.log(respData.code)');
            $("#Warehouse_Code").val(respData.code);  
            $('#dcfc_submit').prop('disabled',true);
            $('#duplicate_dcfccodeerror').css('display','block');
            }
        }
    });
 }
    });

   $('#DC_FC_id').on('change', function() {
     var dcfcid=$(this).val();

     if(dcfcid==1014){

        $("#changetocolmd3").attr('class', 'col-md-3');
        $("#changetocolmd3_1").attr('class', 'col-md-3');
        $("#changetocolmd3_2").attr('class', 'col-md-3');
        $("#dclist").css('display','block');
     }else{
        $("#changetocolmd3").attr('class', 'col-md-4');
        $("#changetocolmd3_1").attr('class', 'col-md-4');
        $("#changetocolmd3_2").attr('class', 'col-md-4'); 
        $("#dclist").css('display','none'); 
     }

   });

   $('#dcs').change(function (){
      var dcs=$(this).val();
        var token  = $("#csrf-token").val();
      if(dcs!='' && dcs!=null){  
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getlegalentityidfordc',
        data:{
            dcs:dcs,
        },
        success: function (respData)
        { 
        $("#hidden_legalentityid").val(respData);  
        }
    });
 }

   });

   $('#city_state_id').change(function (){
      var city_state_id=$(this).val();
        var token  = $("#csrf-token").val();
      if(city_state_id!='' && city_state_id!=null){  
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getcityname',
        data:{
            city_state_id:city_state_id,
        },
        success: function (respData)
        { 
        $("#hidden_cityname").val(respData);  
        }
    });
 }

   });

$('#parent_bu,#DC_FC_id').change(function() {
    var option;
    $("#parent_bu option").each(function() {
        option = $(this).val();
        $('#parent_bu').find('option[value="' + option + '"]').prop('disabled', false);

    });
});

   $('#parent_bu,#DC_FC_id').change(function(){
    var token  = $("#csrf-token").val();
    var bu_id = $('#parent_bu').val();
    var dc_fc = $("#DC_FC_id").val();
    console.log(bu_id);
    console.log(dc_fc);
     $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            data:{bu_id:bu_id,dc_fc:dc_fc},
            url: '/legalentity/generateCostcenter',
            type: 'POST',
            dataType:'json',                                             
            success: function (rs) 
            {
                //rs=JSON.stringify(rs);
                console.log(rs.flag);console.log(rs.bu_list);
                if(rs.status==200 && rs.flag=='0')
                {
                  $("#cost_center").val(rs.costcenter);    
                }else{
                    $("#parent_bu").html(rs.bu_list);
                }
                
            }
        });



   })
</script>    
@stop   