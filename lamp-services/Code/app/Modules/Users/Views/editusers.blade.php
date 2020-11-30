@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')



<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('users.users_tab.user_edit')}} </div><span style="margin: 13px 10px 5px 6px;position: absolute;">
                    ( <span id="firstname_header"><?php echo $userData['firstname']; ?></span>&nbsp;<span id="lastname_header"> <?php echo $userData['lastname'];?></span>  )</span>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>            
                <div class="portlet-body">
                    <div id="form-wiz" class="portlet-body">
                        <div class="tabbable-line">
                            <input type="hidden" id="getuserid" name="getuserid"/>
                            <ul class="nav nav-tabs ">
                                <li class="active"><a href="#tab11" data-toggle="tab"> {{trans('users.users_tab.user_user')}} </a></li>
                                <li><a href="#tab22" data-toggle="tab"> {{trans('users.users_tab.users_access_level')}} </a></li>
                                <li><a href="#tab33" data-toggle="tab"> {{trans('users.users_tab.users_cash_back')}} </a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab11">                                    
                                    <form action="#" class="submit_form" id="submit_form" method="get">
                                        <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                                        <input type="hidden" id="user_id" name="user_id" value="{{(isset($userData['user_id'])) ? $userData['user_id'] : '' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.first_name')}}<span class="required">*</span></label>
                                                <input type="text" class="form-control" id="firstname" name="firstname" value="{{(isset($userData['firstname'])) ? $userData['firstname'] : '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.last_name')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="lastname" name="lastname" value="{{(isset($userData['lastname'])) ? $userData['lastname'] : '' }}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.email_id')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="email_id" value="{{(isset($userData['email_id'])) ? $userData['email_id'] : '' }}" readonly=""/>
                                                <div  id="email_error" style="color: #a94442;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.mobile_no')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="mobile_no" value="{{(isset($userData['mobile_no'])) ? $userData['mobile_no'] : '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.password')}} <span class="required">*</span></label>
                                                <input type="password" class="form-control" name="password"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.conform_password')}} <span class="required">*</span></label>
                                                <input type="password" class="form-control" name="confirm_password"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.role')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" name="role_id[]" multiple="true">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @if(isset($permanentRolesOptions) and !empty($permanentRolesOptions))
                                                        <?php echo $permanentRolesOptions; ?>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.reporting_managers')}}<span class="required">*</span> </label>
                                                <input type="hidden" id="reporting_manager_data" value="{{ $userData['reporting_manager_id'] }}" />
                                                <select class="form-control select2me" name="reporting_manager_id">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($reportingMangers as $manager)
                                                        <option value="{{$manager->user_id}}" {{ ($userData['reporting_manager_id'] == $manager->user_id) ? 'selected = "true"' : '' }}>{{$manager->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if(isset($tempRolePermission) and ($tempRolePermission == true))
                                         <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Legal Entity<span class="required">*</span> </label>
                                                <select class="form-control select2me" name="legal_entity_bu" @if($customer) disabled="disabled" @endif>
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($businessUnitsData as $buData)
                                                        <option value="{{$buData->legal_entity_id}}" {{ ($userData['legal_entity_id'] == $buData->legal_entity_id) ? 'selected = "true"' : '' }}>{{$buData->display_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @if(isset($tempRolePermission) and ($tempRolePermission == true))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.temp_role')}}</label>
                                                <select class="form-control select2me" name="temp_role_id[]" multiple="true">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @if(isset($tempRolesOptions) and !empty($tempRolesOptions))
                                                        <?php echo $tempRolesOptions; ?>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">
                                                {{trans('users.users_form_fields.temp_role_date')}}</label>
                                                <input type="date" class="form-control" min="<?php echo date("Y-m-d");?>" id="temp_role_expiry_date" name="temp_role_expiry_date" value="{{(isset($tempRolesExpiryDate[0]->expiry_date)) ? $tempRolesExpiryDate[0]->expiry_date : '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.department')}} </label>
                                                <select class="form-control select2me" name="department" id="department">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($getDepartments as $departments)
                                                    <option value="{{$departments->value}}" {{ ($userData['department'] == $departments->value) ? 'selected = "true"' : '' }}>{{$departments->master_lookup_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label"> {{trans('users.users_form_fields.designation')}} </label>
                                                <select class="form-control select2me" name="designation">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($getDesignations as $designations)
                                                    <option value="{{$designations->value}}" {{ ($userData['designation'] == $designations->value) ? 'selected = "true"' : '' }}>{{$designations->master_lookup_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span>{{trans('users.users_form_fields.emp_code')}}</span>
                                                <input type="text" class="form-control" name="emp_code"value="{{(isset($userData['emp_code'])) ? $userData['emp_code'] : '' }}" />
                                                <!-- <span>{{trans('users.users_form_fields.is_active')}}</span>
                                                <input type="checkbox" <?php //if($userData['is_active']) { ?> checked="" <?php //} ?>  name="is_active" id="is_active" /> -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.cost_center')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" name="business_unit_id">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($buCollection as $businessUnit)
                                                    <option value="{{$businessUnit->bu_id}}" {{ ($userData['business_unit_id'] == $businessUnit->bu_id) ? 'selected = "true"' : '' }}>{{$businessUnit->bu_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.otp')}}</label>
                                                <input type="text" readonly="true" class="form-control" name="otp" value="{{(isset($userData['otp'])) ? $userData['otp'] : '' }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>User Group</label>
                                                <?php $grpID = (isset($userData['group_id'])) ? explode(',', $userData['group_id']) : ''; ?>
                                                <select id="user_group" name="user_group[]" class="form-control select2me" multiple="multiple" >
                                                <option value = "">--Please Select--</option>

                                                @foreach($getGroupCollection as $data)
                                                <option value="{{$data->value}}" <?php echo in_array($data->value, $grpID) ? 'selected' : '' ?>>{{$data->master_lookup_name}}</option>
                                                @endforeach
                                            </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:50px;">
                                        <hr />
                                        <div class="col-md-12 text-center"> 
                                            <input type="submit" class="btn green-meadow saveusers" value="Update" id="saveusers"/> 
                                            <input type="button" class="btn green-meadow" value="Next" onclick="nextTab()" /> 
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab22">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-3 col-xs-3">
                                            <ul class="nav nav-tabs tabs-left">
                                                <li class="active"><a href="#tab_7_2" data-toggle="tab" style="padding-left:10px !important"> Business Units </a></li>
                                                <li><a href="#tab_7_3" data-toggle="tab" style="padding-left:10px !important"> Data </a></li>
                                                <li><a href="#tab_7_4" data-toggle="tab" style="padding-left:10px !important"> Manufactures </a></li>
                                                <li><a href="#tab_7_6" data-toggle="tab" style="padding-left:10px !important"> Brands </a></li>
                                                @if(isset($dataAccess) and !empty($dataAccess))
                                                <li><a href="#tab_7_5" data-toggle="tab" style="padding-left:10px !important"> Data Access </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9">
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_7_2">
                                                    <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">
                                                        <div id='businessUnit' style='visibility: hidden; float: left; margin-left: 20px;'>
                                                            @if(!empty($getBusinessUnitCollection))
                                                                <?php echo implode('', $getBusinessUnitCollection); ?>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" id="business_unit_data" name="business_unit_data" value="" />
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="tab_7_3">
                                                    <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">   
                                                        <div id='categoryList' style='visibility: hidden; float: left; margin-left: 20px;'>
                                                            @if(!empty($getCategoriesCollection))
                                                                <?php echo implode('', $getCategoriesCollection); ?>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" id="category_data" name="category_data" value="" />
                                                    </div>                                                    
                                                </div>

                                                <div class="tab-pane fade" id="tab_7_4">
                                                    <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">

                                                        <div id='ManufcatureList' style='visibility: hidden; float: left; margin-left: 20px;'>
                                                            @if(!empty($getBrandsCollection))
                                                                <?php echo implode('', $getBrandsCollection); ?>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" id="manufacturer_data" name="manufacturer_data" value="" />

                                                    </div>                                                    
                                                </div>


                                                <div class="tab-pane fade" id="tab_7_6">
                                                    <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">

                                                        <div id='brandsList' style='visibility: hidden; float: left; margin-left: 20px;'>
                                                            @if(!empty($getManufBrandsCollection))
                                                                <?php echo implode('', $getManufBrandsCollection); ?>
                                                            @endif
                                                        </div>
                                                        
                                                        <input type="hidden" id="brand_data" name="brand_data" value="" />
                                                    </div>                                                    
                                                </div>

                                                @if(isset($dataAccess) and !empty($dataAccess))
                                                <div class="tab-pane fade" id="tab_7_5">
                                                    <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">
                                                    <div id="userDataAccess" style="float: left; margin-left: 20px;">
                                                    @if(isset($dataAccess['userDataAccess']))
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                        <label class="control-label"><input type="checkbox" name="setUserDataAccess" id="setUserDataAccess" @if($dataAccess['userDataAccess']) checked @endif class="checkbox">Access All Users</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if(isset($dataAccess['userRoleAccess']))
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                        <label class="control-label"><input type="checkbox" name="setUserRoleAccess" id="setUserRoleAccess" @if($dataAccess['userRoleAccess']) checked @endif class="checkbox">Access All Roles</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="row" style="margin-top:42px;">
                                                    <hr />
                                                    <div class="col-md-12 text-center"> 
                                                        <button class="btn green-meadow" onclick="goBack()">Back</button>
<!--                                                        <a class="btn green-meadow" href="" onclick="saveUser()">Save</a>-->
                                                        <button class="btn green-meadow" onclick="saveUser()" name="Save">Save</button>
                                                        <!--<a class="btn green-meadow" href="/users/index">Cancel</a>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="manufacturerArray" name="manufacturers" value="{{(isset($userPermissions['manufacturer'])) ? $userPermissions['manufacturer'] : '' }}" />
                                            <input type="hidden" id="bussinessUnitArray" name="bussiness_units" value="{{(isset($userPermissions['sbu'])) ? $userPermissions['sbu'] : '' }}" />
                                            <input type="hidden" id="categoryArray" name="categories" value="{{(isset($userPermissions['category'])) ? $userPermissions['category'] : '' }}" />
                                            <input type="hidden" id="productsArray" name="products" value="{{(isset($userPermissions['products'])) ? $userPermissions['products'] : '' }}" />
                                            <input type="hidden" id="brandsArray" name="products" value="{{(isset($userPermissions['brand'])) ? $userPermissions['brand'] : '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab33">
                                    <p class="text-right" style="margin:0px;">All amounts in Rupees(<i class="fa fa-inr" aria-hidden="true"></i>)</p>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.ecash')}}</label>
                                                <input type="text" readonly="true" class="form-control" name="ecash" id="ecash" value="{{(isset($userData['ecash'])) ? $userData['ecash'] : '' }}" />
                                            </div>
                                        </div>
                                        @if(isset($redeemPermission) and ($redeemPermission==true))
                                        <form id="redeemForm" method="get">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.redeem_click')}}</label>
                                                <input type="button" class="btn green-meadow" name="redeemEcash" id="redeemEcash" value="{{trans('users.users_form_fields.users_cash_back_redeem')}}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label" id="redeemAmountLabel">{{trans('users.users_form_fields.redeem_amount')}}</label>
                                                <input type="number" min="0" max="{{(isset($userData['ecash'])) ? intval($userData['ecash']) : 0 }}" class="form-control" name="redeemAmount" id="redeemAmount"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label" id="redeemMessageLabel">{{trans('users.users_form_fields.redeem_message')}}</label>
                                                <input type="text" class="form-control" name="redeemMessage" id="redeemMessage"/>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="control-label" id="redeemSubmitBtnLabel">{{trans('users.users_form_fields.redeem_btn')}}</label>
                                                <input type="submit" class="btn green-meadow" name="redeemSubmitBtn" id="redeemSubmitBtn" value="{{trans('users.users_form_fields.redeem_btn')}}" />
                                            </div>
                                        </div>
                                        </form>
                                        @endif
                                    </div>
                                    <div class="row">
                                    	<div class="col-md-12">
                                        	<div id="cashBackHistoryGrid"></div>
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
@section('script')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    #redeemForm .has-error .control-label,
    #redeemForm .has-error .help-block,
    #redeemForm .has-error .form-control-feedback {
        color: #f39c12;
    }

    #redeemForm .has-success .control-label,
    #redeemForm .has-success .help-block,
    #redeemForm .has-success .form-control-feedback {
        color: #18bc9c;
    }
    .rightAlign{
        text-align: right;
    }
</style>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@include('includes.ignite')
@include('includes.validators') 
@include('includes.jqx')
<script>
var csrf_token = $('#csrf_token').val();
$(document).ready(function () {
    $("#redeemAmount, #redeemMessage, #redeemSubmitBtn, #redeemAmountLabel, #redeemMessageLabel, #redeemSubmitBtnLabel").hide();
    $('#businessUnit').jqxTree({ height: '100%', hasThreeStates: false, checkboxes: true, width: '350px'});
    $('#businessUnit').css('visibility', 'visible');
    $('#businessUnit').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        if(currentId == 0)
        {
//            console.log('we are in if');
//            console.log(event);
//            console.log($('#'+event.target.id).parent());
            updateData(currentId, 'business_unit_data');
        }else{
            updateData(currentId, 'business_unit_data');
        }        
    });
    $('#businessUnit').on('indeterminate', function (event) {
//        console.log(event);
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
//        updateData(currentId, 'business_unit_data');
    });
    $('#businessUnit').on('unchecked', function (event) {
//        console.log(event);
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        deleteData(currentId, 'business_unit_data');
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var target = $(e.target).attr("href") // activated tab
      if(target=="#tab33")
        getCashBackHistoryGrid("#cashBackHistoryGrid");
    });
    
    $("#redeemEcash").on('click', function (event){
        $("#redeemEcash").attr("disabled","disabled");    
        $("#redeemAmount, #redeemMessage, #redeemSubmitBtn, #redeemAmountLabel, #redeemMessageLabel, #redeemSubmitBtnLabel").show();
    });

    $('#redeemForm').formValidation({
        framework: 'bootstrap',
        fields: {
            redeemAmount: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                    },
                    regexp: {
                        regexp: /^[0-9]*$/,
                        message: "{{trans('users.users_form_validate.user_redeem_is_numbers')}}"
                    }
                }
            },
            redeemMessage: {
                validators: {
                    notEmpty: {
                        message: "{{trans('users.users_form_validate.user_redeem_is_required')}}"
                    }
                }
            }
        }
    });

    $("#redeemSubmitBtn").on('click', function (event){

        event.preventDefault();
        
        var redeemAmount = $("#redeemAmount").val();
        var redeemMessage = $("#redeemMessage").val();
        var ecash = $("#ecash").val();

        $("#redeemAmount").val('');
        $("#redeemMessage").val('');

        if(redeemAmount == "" || redeemAmount == null || redeemMessage == "" || redeemMessage == null)
            alert("Please Enter All Redeem Details");
        else
        {
            if(redeemAmount <= 0 || ecash-redeemAmount < 0)
            {
                alert("Please enter proper valid Amount");
            }
            else
            {
                var datastring = {'user_id' : $('#user_id').val(),'amount' : redeemAmount, 'message' : redeemMessage};
                $.ajax({
                    url: '/users/applyredeem',
                    data: datastring,
                    type: 'GET',
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.status) {                
                            alert("Redeem Applied Successfully");
                            $("#redeemAmount, #redeemMessage, #redeemSubmitBtn, #redeemAmountLabel, #redeemMessageLabel, #redeemSubmitBtnLabel").hide();
                            var eCashAmount = ecash-redeemAmount; 
                            $("#ecash").val(eCashAmount.toFixed(2));
                            $("#cashBackHistoryGrid").igGrid("dataBind"); 
                        } 
                        else
                        {
                            alert("Sorry, Redeem Failed. Please Try Again");
                        }
                    },
                    error: function (response) {
                        alert("Sorry, Something Went Wrong Redeem Failed. Please Try Again");
                    }
                });
            }
        }
    });

    function getCashBackHistoryGrid(grid_id)
    {
        var user_id = $("#user_id").val();
        $(grid_id).igGrid({
            dataSource: "/users/cashbackhistory/2/"+user_id,
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            recordCountKey: 'totalRecCount',
            columns: [
                {headerText: 'Order Details', key: 'order_details', dataType: 'string', width: '20%'},
                {headerText: 'Delivery Amt', key: 'delivery_amt', template: '<div class="rightAlign"> ${delivery_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: 'Commission Back Amt', key: 'cash_back_amt', template: '<div class="rightAlign"> ${cash_back_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: 'Transaction Type', key: 'transaction_type', dataType: 'string', width: '15%'},
                {headerText: 'Transaction Date', key: 'transaction_date', dataType: 'string', width: '15%'}
                ],
            features: [
                {
                    name: 'Paging',
                    type: 'local',
                    pageSize: 10,
                    recordCountKey: 'totalRecCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                }
            ],
            primaryKey: 'user_id',
            width: '100%',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            type: 'local',
            showHeaders: true,
            fixedHeaders: true
        });
    }
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    $('#categoryList').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '330px'});
    $('#categoryList').css('visibility', 'visible');
    $('#categoryList').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        updateData(currentId, 'category_data');
    });
    $('#categoryList').on('indeterminate', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
//        updateData(currentId, 'category_data');
    });
    $('#categoryList').on('unchecked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        deleteData(currentId, 'category_data');
    });
//    updateCheckBox();
    



    //brands list 


    $('#brandsList').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '330px'});
    $('#brandsList').css('visibility', 'visible');

    $('#brandsList').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            updateData(currentId, 'brand_data');
        }else{
                updateData(currentId, 'brand_data');
        }        
    });


    $('#brandsList').on('indeterminate', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            deleteData(currentId, 'brand_data');
        }else{
                deleteData(currentId, 'brand_data');
        }
    });

    $('#brandsList').on('unchecked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            deleteData(currentId, 'brand_data');
        }else{
            deleteData(currentId, 'brand_data');  
        }
    });


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    


    $('#ManufcatureList').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '330px'});

    $('#ManufcatureList').css('visibility', 'visible');

    $('#ManufcatureList').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            updateData(currentId, 'manufacturer_data');
        }else{
            
            updateData(currentId, 'manufacturer_data');
              
        }        
    });

    $('#ManufcatureList').on('indeterminate', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            deleteData(currentId, 'manufacturer_data');
        }else{
            deleteData(currentId, 'manufacturer_data');   
        }
//        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
//        if(currentTitle == 'manufacturers')
//        {
//            updateData(currentId, 'manufacturer_data');
//        }else if(currentTitle == 'brands')
//        {
//            updateData(currentId, 'brand_data');
//        }
    });

    $('#ManufcatureList').on('unchecked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            deleteData(currentId, 'manufacturer_data');
        }else{
            deleteData(currentId, 'manufacturer_data');
   
        }
    });

//    $('[name="role_id"]').trigger('change');
    updateCheckBox();    
});

function updateCheckBox()
{
    var bussinessUnitArray = $('#bussinessUnitArray').val();
    var categoryArray = $('#categoryArray').val();
    var manufacturerArray = $('#manufacturerArray').val();
    var brandsArray = $('#brandsArray').val();
    makeChecked(bussinessUnitArray, 'businessUnit');

    makeChecked(categoryArray, 'categoryList');

    makeChecked(manufacturerArray, 'ManufcatureList');

    makeChecked(brandsArray, 'brandsList');
}

function makeChecked(dataArray, dataId)
{
    if(dataArray != '' && $("#"+dataId))
    {
        dataList = dataArray.split(',');
        $.each(dataList, function(key, value){
            // console.log(dataId);
            // console.log(value);
            // console.log($("#"+dataId+",#"+value)[0]);
            $("#"+dataId).jqxTree('checkItem', $("#"+dataId).find('li#'+value)[0], true);
//            $("#"+dataId).jqxTree('checkItem', $("#"+value)[0], true);
//            
//            $.each($('#businessUnit').find('li'), function(key1, element){                
//                var currentKeyId = element.id;
//                if(currentKeyId == value)
//                {
//                    console.log('currentKeyId');
//                    console.log(currentKeyId);
////                    $(this).find('span').addClass('jqx-checkbox-check-checked');
//                }
//            });
        });
    }
}

function updateData(newFilter, filters)
{
    var businessUnitData = $('#'+filters).val();
    if(businessUnitData.length == 0)
    {
        var businessUnitArray = [];
        var businessUnitArray2 = addNew(newFilter, businessUnitArray);
        $('#'+filters).val(JSON.stringify(businessUnitArray2));
        return;
    }else{
        var businessUnitArray = JSON.parse(businessUnitData);
    }
    var found = $.inArray(newFilter, businessUnitArray);
    if (found >= 0) {
        // Element was found, remove it.
        businessUnitArray.splice(found, 1);
    } else {
        businessUnitArray.push(newFilter);
    }
    $('#'+filters).val(JSON.stringify(businessUnitArray));
    return;
//    return filters;
}

function deleteData(newFilter, filters)
{
    var businessUnitData = $('#'+filters).val();
    if(businessUnitData.length == 0)
    {
        var businessUnitArray = [];
        var businessUnitArray2 = addNew(newFilter, businessUnitArray);
        $('#'+filters).val(JSON.stringify(businessUnitArray2));
        return;
    }else{
        var businessUnitArray = JSON.parse(businessUnitData);
    }
    var found = $.inArray(newFilter, businessUnitArray);
    if (found >= 0) {
        // Element was found, remove it.
        businessUnitArray.splice(found, 1);
    }
    $('#'+filters).val(JSON.stringify(businessUnitArray));
    return;
//    return filters;
}

function addNew(newFilter, filters)
{
    filters.push(newFilter);
    return filters;
}
/* Bootsrap From Validations */
$("#submit_form").bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        firstname: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_first_name')}}"
                },                     
                stringLength: {
                    min: 1,
                    max: 50,
                    message: "{{trans('users.users_form_validate.users_firt_name_length')}}"
                },
                regexp: {
                        regexp: /^[a-z0-9\s]+$/i,
                        message: "{{trans('users.users_form_validate.users_firt_name_string')}}"
                    },   
            }
        },
        lastname: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_last_name')}}"
                },
//                stringLength: {
//                    min: 4,
//                    max: 20,
//                    message: "{{trans('users.users_form_validate.users_last_name_length')}}"
//                },
                regexp: {
                        regexp: /^[a-z0-9\s]+$/i,
                        message: "{{trans('users.users_form_validate.users_last_name_string')}}"
                    }  
            }
        },
        email_id: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_email_id')}}"
                },
                regexp: {
                    regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                    message: "{{trans('users.users_form_validate.user_email_invalid')}}"
                }, 
                remote: {
                    headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                    url: '/users/validateemail',
                    type: 'POST',
                    data: function (validator, $field, value) {
                        return  {
                            email_id: value,
                            user_id: $('#user_id').val() 
                        };
                    },
                    delay: 2000, // Send Ajax request every 2 seconds
                    message: "{{trans('users.users_form_validate.user_email_exit')}}"
                }
            }
        },
        mobile_no: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_mobile_no')}}"
                },
                stringLength: {
                    min: 10,
                    max: 10,
                    message: "{{trans('users.users_form_validate.users_mobile_max')}}"
                },
                regexp: {
                    regexp: '^[0-9]*$',
                    message: "{{trans('users.users_form_validate.users_mobile_isdigit')}}"
                },
                remote: {
                    headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                    url: '/users/validatemobileno',
                    type: 'POST',
                    data: function (validator, $field, value) {
                        return  {
                            mobile_no: value,
                            user_id: $('#user_id').val()
                        };
                    },
                    delay: 1000, // Send Ajax request every 1 seconds
                    message: "{{trans('users.users_form_validate.user_mobile_exist')}}"
                }
            }
        },        
        password: {
            stringLength: {
                min: 5,
                max: 14,
                message: "{{trans('users.users_form_validate.users_password_length')}}"
            }
        },
        confirm_password: {
            validators: {
                identical: {
                    field: 'password',
                    message: "{{trans('users.users_form_validate.user_conform_password_same')}}"
                }
            }
        },
        'role_id[]': {
            validators: {
                callback: {
                    message: "{{trans('users.users_form_validate.user_role')}}",
                    callback: function(value, validator) {
                        $('#submit_form').data('bootstrapValidator').resetField('reporting_manager_id');
                        return value != null;
                    }
                }
            }
        },
        reporting_manager_id: {
            validators: {
                callback: {
                    message: "{{trans('users.users_form_validate.user_reporting_manager')}}",
                    callback: function(value, validator) {
                        // console.log(value);
                        return value > 0;
                    }
                }
            }
        },
        business_unit_id: {
            validators: {
                callback: {
                    message: "{{trans('users.users_form_validate.business_unit')}}",
                    callback: function(value, validator) {
                        return value > 0;
                    }
                }
            }
        }
    }}).on('success.form.bv', function (event) {
    event.preventDefault();
    var datastring = '';
    var datastring = $("#submit_form").serialize();
    $.ajax({
        url: '/users/updateuser',
        data: datastring,
        type: 'get',
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.status) {                
                 $('#flass_message').text("{{trans('users.edit_users_form.edit_user')}}"); 

                    $('#firstname_header').text($('#firstname').val());
                    $('#lastname_header').text($('#lastname').val());
                    $('title').text("Edit User ("+$('#firstname').val()+' '+$('#lastname').val()+')');
                    
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350); 
                    $('html, body').animate({scrollTop: '0px'}, 500);
                $("#getuserid").val(data.user_id);
                $("#email_error").html('');
                $('a[href="#tab22"]').tab('show');
            } 
        }
    });
});
function saveUser()
{    
    var sbu_data = -1, role_access = -1;
    sbu_data = ($("#setUserDataAccess").prop("checked") == true?1:0);
    role_access = ($("#setUserRoleAccess").prop("checked") == true?1:0);
    var tempBu = $('#business_unit_data').val();
    if(tempBu.length > 0)
    {
        tempBu = JSON.parse($('#business_unit_data').val()).toString();
    }
    var tempCat = $('#category_data').val();
    if(tempCat.length > 0)
    {
        tempCat = JSON.parse($('#category_data').val()).toString();
    }
    var tempManf = $('#manufacturer_data').val();
    if(tempManf.length > 0)
    {
        tempManf = JSON.parse($('#manufacturer_data').val()).toString();
    }
    var tempBrands = $('#brand_data').val();
    if(tempBrands.length > 0)
    {
        tempBrands = JSON.parse($('#brand_data').val()).toString();
    }
    var selectedBuList = tempBu;
    var selectedCategoriesList = tempCat;
    var user_id = $("#user_id").val();
    var token = $("#csrf_token").val();
    datastring = {'_token' : token, 'business_units' : selectedBuList, 'categories' : selectedCategoriesList, 'user_id' : user_id, 'brands' : tempBrands, 'manufacturers' : tempManf, 'sbu_data':sbu_data, 'role_access':role_access};
    $.ajax({
        url: '/users/saveusersaccess',
        data: datastring,
        type: 'POST',
        success: function (response) {
            var data = $.parseJSON(response);            
            if (data.status==true) {  
                $("#getuserid").val(data.user_id);
                $("#email_error").html('');
                $('#flass_message').text('Successfully updated'); 
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    window.setTimeout(function(){
                    window.location.href='/users/index';
                    },2000);
            }
        }
    });
}
function nextTab()
{
    $('a[href="#tab22"]').tab('show');
}
function goBack()
{
    $('a[href="#tab11"]').tab('show');
}

$('[name="role_id[]"]').change(function(){
    $('[name="reporting_manager_id"]').empty();
    $('[name="reporting_manager_id"]')
                        .append($("<option></option>")
                                   .attr("value",'')
                                   .text('Please Select...'));
    $('[name="reporting_manager_id"]').select2({placeholder: "Please Select..."});
    var token = $("#csrf_token").val();
    var user_id = $("#user_id").val();
    var datastring = { '_token' : token, 'role_id' : $(this).val(), 'user_id' : user_id };
    $.ajax({
        url: '/users/getreportingmanagers',
        data: datastring,
        type: 'POST',
        success: function (response) {
            var data = $.parseJSON(response);
            if(data.length > 0)
            {
                $.each(data, function(key, value){
                    $('[name="reporting_manager_id"]')
                        .append($("<option></option>")
                                   .attr("value",value.user_id)
                                   .text(value.name));
                });
            }
        }
    });
});
</script>
@stop
