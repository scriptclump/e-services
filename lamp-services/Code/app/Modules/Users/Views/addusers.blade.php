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
                <div class="caption"> {{trans('users.users_tab.user_add')}} </div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>            
                <div class="portlet-body">
                    <div id="form-wiz" class="portlet-body">
                        <div class="tabbable-line">                            
                            <ul class="nav nav-tabs ">
                                <li class="active"><a href="#tab11" data-toggle="tab">{{trans('users.users_tab.user_user')}}</a></li>
                                <li><a href="#tab22" data-toggle="tab">{{trans('users.users_tab.users_access_level')}} </a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab11">                                    
                                    <form action="#" class="submit_form" id="submit_form" method="get">
                                        <input type="hidden" id="getuserid" name="user_id" value="" />
                                        <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">                                        
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.first_name')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="firstname"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label"> {{trans('users.users_form_fields.last_name')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="lastname"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.email_id')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="email_id" id="email_id" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.mobile_no')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" name="mobile_no"/>
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
                                                <label class="control-label"> {{trans('users.users_form_fields.conform_password')}} <span class="required">*</span></label>
                                                <input type="password" class="form-control" name="confirm_password"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label"> {{trans('users.users_form_fields.role')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" name="role_id[]" multiple="multiple">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($roles as $role)
                                                    <option value="{{$role->role_id}}">{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.reporting_managers')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" name="reporting_manager_id" id="reporting_manager_id">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Legal Entity<span class="required">*</span> </label>
                                                <select class="form-control select2me" name="legal_entity_bu" id="legal_entity_bu">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($businessUnitsData as $buData)
                                                        <option value="{{$buData->legal_entity_id}}">{{$buData->display_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.department')}} </label>
                                                <select class="form-control select2me" name="department" id="department">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($getDepartments as $departments)
                                                    <option value="{{$departments->value}}">{{$departments->master_lookup_name}}</option>
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
                                                    <option value="{{$designations->value}}">{{$designations->master_lookup_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.emp_code')}}</label>
                                                <input type="text" class="form-control" name="emp_code" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('users.users_form_fields.cost_center')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" name="business_unit_id">
                                                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                                    @foreach($buCollection as $businessUnit)
                                                    <option value="{{$businessUnit->bu_id}}">{{$businessUnit->bu_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label for="multiple">User Group</label>
                                            <select id="user_group" name="user_group[]" class="form-control select2me" multiple="multiple" >
                                                <option value = "">--Please Select--</option>
                                                @foreach($userGroupData as $userData)
                                                <option value="{{$userData->value}}">{{$userData->master_lookup_name}}</option>
                                                @endforeach
                                            </select>


                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        <input type="checkbox" checked="" name="is_active" id="is_active"/><sapn>{{trans('users.users_form_fields.is_active')}}</sapn>
                                    </div>
                                    </div>
                                    <div class="row" style="margin-top:100px;">
                                        <hr />
                                        <div class="col-md-12 text-center"> 
                                            <input type="submit" class="btn green-meadow saveusers" value="Save & Continue" id="saveusers"/> </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab22">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-3 col-xs-3">
                                            <ul class="nav nav-tabs tabs-left">
                                                <li class="active"><a href="#tab_7_2" data-toggle="tab" style="padding-left:10px !important"> Business Units </a></li>
                                                <li><a href="#tab_7_3" data-toggle="tab" style="padding-left:10px !important"> Data </a></li>
                                                <li><a href="#tab_7_4" data-toggle="tab" style="padding-left:10px !important"> Brands </a></li>
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
                                                        <span class="error" id="error_code"></span>
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
                                                        <div id='brandsList' style='visibility: hidden; float: left; margin-left: 20px;'>
                                                            @if(!empty($getBrandsCollection))
                                                                <?php echo implode('', $getBrandsCollection); ?>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" id="manufacturer_data" name="category_data" value="" />
                                                        <input type="hidden" id="brand_data" name="category_data" value="" />
                                                    </div>                                                    
                                                </div>
                                                <div class="row" style="margin-top:42px;">
                                                    <hr />
                                                    <div class="col-md-12 text-center"> 
                                                        <button class="btn green-meadow" onclick="goBack()">Back</button>
                                                        <button class="btn green-meadow" onclick="saveUser()" name="Save">Save</button>
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
        </div>
    </div>
</div>
@stop
@section('script')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>

@include('includes.validators')
@include('includes.jqx')
<script type="text/javascript">
$(document).ready(function () {   
    //////////////////////////////////////////////////////////////////////////////////////////////////
    $('#businessUnit').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '90%'});
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
        updateData(currentId, 'business_unit_data');
    });
    $('#businessUnit').on('unchecked', function (event) {
//        console.log(event);
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        deleteData(currentId, 'business_unit_data');
    });
    
    
    $('#categoryList').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '90%'});
    $('#categoryList').css('visibility', 'visible');
    $('#categoryList').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        updateData(currentId, 'category_data');
    });
    $('#categoryList').on('indeterminate', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        updateData(currentId, 'category_data');
    });
    $('#categoryList').on('unchecked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        deleteData(currentId, 'category_data');
    });
    
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    $('#brandsList').jqxTree({ height: '400px', hasThreeStates: true, checkboxes: true, width: '90%'});
    $('#brandsList').css('visibility', 'visible');
    $('#brandsList').on('checked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            updateData(currentId, 'manufacturer_data');
            updateData(currentId, 'brand_data');
        }else{
            if(currentTitle == 'manufacturers')
            {
                updateData(currentId, 'manufacturer_data');
            }else if(currentTitle == 'brands')
            {
                updateData(currentId, 'brand_data');
            }   
        }        
    });
    $('#brandsList').on('indeterminate', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
//        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
//        if(currentTitle == 'manufacturers')
//        {
//            updateData(currentId, 'manufacturer_data');
//        }else if(currentTitle == 'brands')
//        {
//            updateData(currentId, 'brand_data');
//        }
    });
    $('#brandsList').on('unchecked', function (event) {
        var currentId = parseInt($('#'+event.target.id).parent().attr('id'));
        var currentTitle = ($('#'+event.target.id).parent().attr('title'));
        if(currentId == 0)
        {
            updateData(currentId, 'manufacturer_data');
            updateData(currentId, 'brand_data');
        }else{
            if(currentTitle == 'manufacturers')
            {
                updateData(currentId, 'manufacturer_data');
            }else if(currentTitle == 'brands')
            {
                updateData(currentId, 'brand_data');
            }   
        }
    });
});

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
                }, stringLength: {
                    min: 4,
                    max: 20,
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
                            email_id: value
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
                            mobile_no: value
                        };
                    },
                    delay: 1000, // Send Ajax request every 1 seconds
                    message: "{{trans('users.users_form_validate.user_mobile_exist')}}"
                }
            }
        },
        password: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_password')}}"
                }
            },
            stringLength: {
                min: 4,
                max: 14,
                message: "{{trans('users.users_form_validate.users_password_length')}}"
            }
        },
        confirm_password: {
            validators: {
                notEmpty: {
                    message: "{{trans('users.users_form_validate.user_conform_password')}}"
                },
                identical: {
                    field: 'password',
                    message: "{{trans('users.users_form_validate.user_conform_password_same')}}"
                }
            }
        },
        reporting_manager_id: {
            validators: {
                callback: {
                    message: "{{trans('users.users_form_validate.user_reporting_manager')}}",
                    callback: function(value, validator) {
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
        },
        legal_entity_bu:{
            validators:{
                notEmpty:{
                    message:"legalentity is required"
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
        }
    }
    }).on('success.form.bv', function (event) {
    event.preventDefault();
    var datastring = '';
    var getuserId = '';    
    var datastring = $("#submit_form").serialize();


    console.log(datastring);
    $.ajax({
        url: '/users/saveusers',
        data: datastring,
        type: 'get',
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.status) {
                $("#getuserid").val(data.user_id);
                $("#email_error").html('');
                $('a[href="#tab22"]').tab('show');
                $('#email_id').prop('readonly', true);
            }
            $('#flass_message').text(data.message); 
            $('div.alert').show(); 
            $('div.alert').removeClass('hide'); 
            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            $('html, body').animate({scrollTop: '0px'}, 500);            
        }
    });
});
function saveUser()
{
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
    var user_id = $("#getuserid").val();
    var token = $("#csrf_token").val();
    datastring = {'_token' : token, 'business_units' : selectedBuList, 'categories' : selectedCategoriesList, 'user_id' : user_id, 'brands' : tempBrands, 'manufacturers' : tempManf };
    $.ajax({
        url: '/users/saveusersaccess',
        data: datastring,
        type: 'POST',
        success: function (response) {
            var data = $.parseJSON(response);
            console.log(data.status);
            if (data.status==true)
            {
                $('#flass_message').text('Successfully added'); 
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    window.setTimeout(function(){
                    window.location.href='/users/index';
                    },3000);
                   $("#getuserid").val(data.user_id);
                   $("#email_error").html('');
                   $('a[href="#tab22"]').tab('show');
            }
        }
    });
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
    var datastring = { '_token' : token, 'role_id' : $(this).val() };
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