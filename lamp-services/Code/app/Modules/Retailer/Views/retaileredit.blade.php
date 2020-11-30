@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>

<input type="hidden" name="_token" id="csrf-token_hidden" value="{{ Session::token() }}" />
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{ trans('retailers.title.edit_page_title') }} ({{$retailers->le_code}})</div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Click here for Fullscreen"><i class="fa fa-question-circle-o"></i></a></span> </div>
            </div>
            <div class="portlet-body">
                <div id="loadReturnSumData" style="display: none" class="loader" ></div>

                <form id="retailersinfo" action="/retailers/update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" value="{{$retailers->legal_entity_id}}" id="legal_entity_id" name="legalEntityId" />
                    @include('Retailer::retailerview')
                    <input type="hidden" id="csrf_token" name="_Token" value="{{ csrf_token() }}">
                    <input type="text" id="supplier_id" value="<?php echo Session::get('supplier_id'); ?>" hidden />
                    <input type="text" id="legalentity_id" value="<?php echo Session::get('legalentity_id'); ?>" hidden />
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_ecash_modal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editStateModalLabel">Edit E-Cash</h4>
                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editEcashForm">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="row">
                        <div class="col-lg-6">
                                <label for="ecash">Available E-Cash</label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="edit_Ecash" name="edit_Ecash" value="{{$ecash}}" style="margin-top: -1px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="height: 10px;">
                        <button type="submit" id="saveData" class="btn btn-primary" style=" margin-top: -10px;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-top:10px;">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light">
            <div class="portlet-body">
                <div class="tabbable-line">                        
                    <ul class="nav nav-tabs" >
                        <li class="active"><a href="#tab_11" data-toggle="tab">{{ trans('retailers.tab.users') }}</a></li>
                        <li><a href="#tab_22" data-toggle="tab">{{ trans('retailers.tab.documents') }}</a></li>
                        <li><a href="#tab_44" data-toggle="tab">{{ trans('retailers.tab.orders') }}</a></li>
                        <li><a href="#tab_55" data-toggle="tab">{{ trans('retailers.tab.collections_details') }}</a></li>
                        <li><a href="#tab_77" data-toggle="tab">{{ trans('retailers.tab.warehouse_mapped') }}</a></li>
                        <li><a href="#tab_88" data-toggle="tab">{{ trans('retailers.tab.e_cash') }}</a></li>
                        <li><a href="#tab_99" data-toggle="tab">{{ trans('retailers.tab.credit_approval_history') }}</a></li>
                        <li><a href="#tab_12" data-toggle="tab">{{trans('retailers.tab.Lender')}}</a></li>
                        <li><a href="#tab_24" data-toggle="tab">{{ trans('retailers.tab.feedback') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_11">  
                            @include('Retailer::users')
                        </div>
                        <div class="tab-pane" id="tab_22">
                            @include('Retailer::documents')
                        </div>
                        <div class="tab-pane" id="tab_44">
                            @include('Retailer::orders')
                        </div>
                        <div class="tab-pane" id="tab_55">
                            @include('Retailer::collection_details')
                        </div>
                        <div class="tab-pane" id="tab_77">
                            @include('Retailer::warehouse_mapping')
                        </div>
                        <div class="tab-pane" id="tab_88">
                            @include('Retailer::cash_back')
                        </div>
                        <div class="tab-pane" id="tab_99">
                            <?php echo $creditApprovalHistory; ?>
                        </div>
                        <div class="tab-pane" id="tab_12">  
                            @include('Retailer::mfc_mapping')
                        </div>
                        <div class="tab-pane" id="tab_24">
                            @include('Retailer::feedback')
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">{{ trans('retailers.button.update') }}</button>
			<button type="button" onclick="window.location.replace('/retailers/index');"  id="cancelretailerinfo" class="btn green-meadow">{{ trans('retailers.button.cancel') }}</button>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
<div class="modal fade" id="retailers_edit_user" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">{{ trans('retailers.title.edit_user') }}</h4>
            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>
            <div class="modal-body" id="userDiv">
                <form action="#" class="submit_form" id="user_edit_form" method="post">
                    <input type="hidden" id="csrf_token" value="{{ Session::token() }}">
                    <input type="hidden" id="user_id" name="user_id" value="">
					<input type="hidden" id="legal_entity_id" name="legal_entity_id" value="{{$retailers->legal_entity_id}}" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.first_name')}}<span class="required">*</span></label>
                                <input type="text" class="form-control" name="firstname" id="firstname" value=""/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.last_name')}} <span class="required">*</span></label>
                                <input type="text" class="form-control" name="lastname" id="lastname" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.email_id')}}</label>
                                <input type="text" class="form-control" id="email_id" value="" readonly="true"/>
                                <div  id="email_error" style="color: #a94442;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.mobile_no')}} <span class="required">*</span></label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" value="" />
                            </div>
                        </div>
                    </div>
                    <!--div class="row">
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
                    </div-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.aadhar_id')}}</label>
                                <input type="text" class="form-control" id="aadhar_id" 
                                name="aadhar_id" value="" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{trans('users.users_form_fields.otp')}}</label>
                                <input type="text" readonly="true" class="form-control" id="otp" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span>{{trans('users.users_form_fields.is_active')}}</span>
                                <input type="checkbox" name="user_is_active" id="user_is_active" />
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:50px;">
                        <hr />
                        <div class="col-md-12 text-center"> 
                            <!--<input type="button" class="btn green-meadow saveusers" value="Update" id="saveusers"/>-->
                            <button class="btn green-meadow" name="Update">{{ trans('retailers.button.update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <button data-toggle="modal" id="edit" class="btn btn-default" data-target="#wizardCodeModal" style="display: none"></button>
</div><!-- /.modal --> 
<div class="modal fade" id="edit_Order_Code" tabindex="-1" role="dialog" aria-labelledby="editOrderCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="editOrderCode">Add Cashback</h4>
            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>
            <div class="modal-body" id="userDiv">
                <form action="#" class="submit_form" id="order_edit_form" method="post">
                    <input type="hidden" id="csrf_token" value="{{ Session::token() }}">
                    <input type="hidden", class="cust_le_id" id="cust_le_id" value="/">
                    <input type="hidden", class="user_id" id="user_id" value="/">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Order Id</label>
                                <input type="text" class="form-control" name="edit_order_id" id="edit_order_id" readonly="readonly" value=""/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Order Code</label>
                                <input type="text" class="form-control" name="edit_order_code" id="edit_order_code" readonly="readonly" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Order Status</label>
                                <input type="text" class="form-control" name="edit_order_status" id="edit_order_status" readonly="readonly" value=""/>
                                <input type="hidden", class="order_status_id" id="order_status_id" value="/">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Cash Back</label>
                                <input type="text" class="form-control" name="add_cash_back" id="add_cash_back" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-check">
                                <label class="form-check-label"><br>
                                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" onchange="getcashback();">Promotions
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <hr />
                        <div class="col-md-12 text-center">
                            <button class="btn green-meadow" name="Update">{{ trans('retailers.button.update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('style')
<style type="text/css">
    .rightAlignText{text-align: right !important;}
    .parent_cat{font-size:14px !important;}
    .sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
    .prod_class{padding-left:10px !important; background:#fff !important; }


    .select2-disabled{font-weight:bold !important;}

   .rowlinht{ line-height:30px !important;}
    .thumbnail {
        padding: 0px !important;
        margin-bottom: 0px !important;
    }
    .fileinput-filename{word-wrap: break-word !important;}

    .fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

    h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

    .pac-container .pac-logo{    z-index: 9999999 !important;}
    .pac-container{    z-index: 9999999 !important;}
    .pac-logo{    z-index: 9999999 !important;}
    
    #dvMap{height:304px !important; width:100% !important;}
    .fileinput-exists .fileinput-new, .fileinput-new .fileinput-exists{
        display: run-in !important;
    }
    label {
        margin-bottom: 0px !important;
        padding-bottom: 0px !important;
    }
    .modal-header {
        padding: 5px 15px !important;
    }

    .modal .modal-header .close {
        margin-top: 8px !important;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .radio input[type=radio]{ margin-left:0px !important;}
	.thumbnail  {
    
	height: 350px;
	width: 350px;
}
.fileinput .thumbnail > img{padding: 10px;}
.profile-sub{
	margin-top: -56px !important; width:350px; height:45px;  position: absolute;opacity: 0;z-index: 9;
}
.profile-sub-title{
	background-color: #000;
    bottom: 20px;
    color: #fff;
    left: 42px;
    line-height: 45px;
    opacity: 0.60;
    position: absolute;
    text-align: center;
    width: 330px;
    
}

.loader {
    position:relative;
    top:40%;
    left: 40%;
    border: 5px solid #f3f3f3;
    border-radius: 50%;
    border-top: 5px solid #d3d3d3;
    width: 50px;
    height: 50px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

.page-content{background:none !important;}
.rowbotmarg{margin-bottom:10px !important;}
.rightAlign{text-align: right;}
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
{{HTML::script('assets/global/plugins/select2/select2.min.js') }}
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#edit_ecash_available").click(function(){
            $("#edit_ecash_modal").modal("show");
            $("#edit_ecash_modal").modal({backdrop:'static', keyboard:false});
            $("#edit_Ecash").val($("#ecash_edit").val());
        });
        $('#edit_ecash_modal').on('hide.bs.modal', function () {
            $("#editEcashForm").bootstrapValidator('resetForm', true);
            $('.modal-backdrop').remove();
        });
        $("#modalClose").click(function(){
            $('.modal-backdrop').remove();
        });
        $('#editEcashForm').bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                edit_Ecash: {
                    validators: {
                        notEmpty: {
                            message: "Should Not Be Empty",
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(event){
            event.preventDefault();
            var newData = {
                ecash_available: $("#edit_Ecash").val(),
                legal_entity_id: $("#legal_entity_id").val(),
            };
            var token=$("#_token").val();
            $.post('/retailers/ecashupdate',newData,function(response){
                $("#edit_ecash_modal").modal("hide");
                if(response != 'NULL'){
                    $("#ecash_edit").attr('value',response);
                }
            });            
        });
        $(document).on('click', '#update_loc', function () {
            if (confirm('Do you want to Submit?')) {
                $(this).attr('disabled',true);
                var legal_entity_id = $('#legal_entity_id').val();
                var creditlimit = $('#creditlimit').val();
                $('.loderholder').show();
                var url = '/retailers/updateCreditLimit';
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: url,
                    type: 'POST',
                    data: {legal_entity_id: legal_entity_id, creditlimit: creditlimit},
                    dataType: 'JSON',
                    success: function (data) {
                        $(this).attr('disabled', false);
                        if (data.status == 200) {
                            location.reload();
                        } else {
                            $('#appr_error-msg').html(data.message).show();
                            $('.loderholder').hide();
                        }
                    },
                    error: function (response) {

                    }
                });
            }
        });
        
        $('#business_end_time').timepicker({
//            locale: 'in'
        });
        $('#business_start_time').timepicker({
//            locale: 'in'
        });
        $('#supp_info').click(function(){
            $('#retailersinfo').submit();
        });

        getUserList();
        getAreaList();
        getOrderList();
        feedback();
        getCollectionDetails();
        getCashBackHistoryGrid("#cash_back_list");

//        getDocuemntList();
        $('#retailersinfo').formValidation({
            message: 'This value is not valid',
            feedbackIcons: {
//                valid: 'glyphicon glyphicon-ok',
//                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                retailer_name: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_name') }}",
                        }
                    }
                },
                business_type_id: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_segment') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                volume: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_volume') }}",
                            callback: function (value, validator) {
                                var items = document.getElementById('customer_type').value;
                                console.log(items);
                                if(items != 3028){
                                    return value > 0;
                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },                
                org_state: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_state') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                org_country: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_country') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                shutters: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_shutters') }}",
                            callback: function (value, validator) {
                                var cst_type = document.getElementById('customer_type').value;
                                console.log(typeof(cst_type));
                                if(cst_type == '3028'){
                                    return value >= 0;
                                }else{
                                    return value >0;
                                }
                            }
                        }
                    }
                },
                file: {
                    validators: {
                        file: {
                            extension: 'jpeg,png,jpg',
                            type: 'image/jpeg,image/png',
                            maxSize: 2097152, // 2048 * 1024
                            message: "{{ trans('retailers.form_validate.file_invalid') }}",
                        }
                    },
                    onSuccess: function (e, data) {
                        var srcData = $('.org_edit_file').attr('src');
                        if ( srcData != '' )
                        {
                            $('#product_creation').formValidation('addField', $('[name="file"]', 'blank'));
                            return false;
                        } else {
                            $('#upload_field').removeClass('has-error');
                            $('#upload_field').children('div.col-sm-10').children('small').hide();
                        }
                    }
                },
                org_address1: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_address') }}",
                        }
                    }
                },
                org_pincode: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_pincode') }}",
                        },
                        zipCode: {
                            country: 'IN',
                            message: "{{ trans('retailers.form_validate.empty_pincode') }}",
                        },
//                        callback: {
//                            message: 'Wrong answer',
//                            callback: function(value, validator) {
//                                getAreas(validator);
//                                return true;
//                            }
//                        }
//                        callback: function (value, validator, $field) {
//                            console.log('we are in callback');
////                            getAreas();
//                        }
                    }
                },
                hub_id: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_hub') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                parent_le:{
                    validators: {
                        notEmpty:{
                            message:"Parent Legal Entity is required"
                        }
                    }
                },
                spoke_id: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.spoke') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                beat: {
                    validators: {
                        callback: {
                            message: "{{ trans('retailers.form_validate.empty_beat') }}",
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                org_city: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_city') }}",
                        }
                    }
                },
                locality: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_locality') }}",
                        }
                    }
                },
                landmark: {
                    validators: {
                        notEmpty: {
                            message: "{{ trans('retailers.form_validate.empty_landmark') }}",
                        }
                    }
                },
                fssai: {
                    validators: {
                        regexp: {
                           regexp: '^[a-z0-9]{14,14}$',
                           message: "{{ trans('retailers.form_validate.fssai_length') }}",
                        },remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                            url: '/retailers/validatefssai/'+$('#legal_entity_id').val(),
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    fssai: value
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "{{ trans('retailers.form_validate.unique_fssai') }}"
                        }
                    }
                },
				gstin: {
					validators: {
						regexp: {
                           regexp:'^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$',  
                            message:'Enter Valid GSTIN.'
                        },
                        remote: {
                            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                            url: '/retailers/checkgstin',
                            data: function (validator, $field, value){
                                return{
                                   gstin_number: $('[name="gstin"]').val()
                                };
                              },
                            type: 'POST',
                            delay: 1000,
                            message: 'GSTIN Number already exists  or Invalid State code'
                        }
                    }
				}					
            }
        }).on('success.form.fv', function (e) {
            e.preventDefault();
            var retailersinfo = $('#retailersinfo');
            // Find disabled inputs, and remove the "disabled" attribute
            var disabled = retailersinfo.find(':input:disabled').removeAttr('disabled');
            var serialized = retailersinfo.serialize();
            // re-disabled the set of inputs that you previously enabled
            disabled.attr('disabled','disabled');
            $.ajax({
                url: '/retailers/update',
                data: serialized,
                type: 'POST',
                success: function (response) {
                    responseData = $.parseJSON(response);
                    if (responseData.status == 0) {
                        $('#flass_message').text(responseData.message);
                        $('div.alert').show();
                        $('div.alert').removeClass('hide');
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                        return false;
                    } else {
                        $('#flass_message').text(responseData.message);
                        $('div.alert').show();
                        $('div.alert').removeClass('hide');
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                        $('html, body').animate({scrollTop: '0px'}, 800);
                        window.setTimeout(function () {
                            window.location.href = '/retailers/index';
                        }, 2000);
                    }
                }
            });
        });    
        $("#user_edit_form").formValidation({
            message: 'This value is not valid',
            feedbackIcons: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                firstname: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('users.users_form_validate.user_first_name')}}"
                        },                     
                        stringLength: {
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
                        regexp: {
                                regexp: /^[a-z0-9\s]+$/i,
                                message: "{{trans('users.users_form_validate.users_last_name_string')}}"
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
                aadhar_id: {
                    validators: {
                        /*notEmpty: {
                            message: "{{trans('users.users_form_validate.user_aadhar_no')}}"
                        },*/
                        stringLength: {
                            min: 12,
                            max: 12,
                            message: "{{trans('users.users_form_validate.users_aadhar_max')}}"
                        },
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('users.users_form_validate.users_aadhar_isdigit')}}"
                        },
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                            url: '/retailers/validateaadharno',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    aadhar_id: value,
                                    user_id: $('#user_id').val()
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "{{trans('users.users_form_validate.user_aadhar_exist')}}"
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            var $form = $(event.target);
//            var fv = $form.data('formValidation');
            var datastring = '';
            datastring = $form.serialize();            
            $.ajax({
                url: '/retailers/updateuser',
                data: datastring,
                type: 'post',
                headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                success: function (response) {
                    console.log(response);
                    var data = $.parseJSON(response);
                    if (data.status) {
                        $('#flass_message').text(data.message); 
                        $('div.alert').show(); 
                        $('div.alert').removeClass('hide'); 
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350); 
//                        $('html, body').animate({scrollTop: '0px'}, 500);
//                        $("#getuserid").val(data.user_id);
                        $("#email_error").html('');
//                        $('a[href="#tab22"]').tab('show');
                        $('#retailers_edit_user').modal('hide');
//                        getUserList();
                        $("#users_list").igGrid("dataBind"); 
                    }
                }
            });
        });
        $("#order_edit_form").formValidation({
            fields: {
                add_cash_back: {
                    validators: {
                        notEmpty: {
                            message: "Please enter cashback amount"
                        },
                    }
                },
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            var newOrderData = {
                cashback: $("#add_cash_back").val(),
                order_id: $("#edit_order_id").val(),
                order_code: $("#edit_order_code").val(),
                order_status: $("#edit_order_status").val(),
                order_status_id: $("#order_status_id").val(),
                cust_le_id: $("#cust_le_id").val(),
                user_id: $("#user_id").val(),
                is_active: $("#is_active").prop('checked'),
            };
            var token=$("#csrf_token").val();
            $.post('/retailers/addcashback',newOrderData,function(response){
                $("#edit_Order_Code").modal("hide");
                console.log(response);
                if(response == 1){
                    $("#alertStatus").attr("class","alert alert-success").text("Cashback is added succesfully!").show().delay(3000).fadeOut(350);
                }
                else if(response == 2)
                    $("#alertStatus").attr("class","alert alert-danger").text("Cashback already exists!").show().delay(3000).fadeOut(350);
                else if(response == 3)
                    $("#alertStatus").attr("class","alert alert-danger").text("Enter a valid cashback amount!").show().delay(3000).fadeOut(350);
                else
                    $("#alertStatus").attr("class","alert alert-danger").text("Failed to update cashback!").show().delay(3000).fadeOut(350);
            });
        });
        $('#edit_Order_Code').on('hide.bs.modal', function () {
            $("#add_cash_back").val("");
            $("#is_active").prop('checked',false);
            $("#add_cash_back").prop('readonly',false);
            $('#order_edit_form').formValidation('resetForm', true);
        });
		$('#beat').change(function(){
			var beatId = $(this).val();
			if(beatId > 0){
				var hubsSpokesCollection = JSON.parse($('#hubsSpokesCollection').val());
				if(hubsSpokesCollection[beatId] != 'undefined'){
					console.log(hubsSpokesCollection[beatId]);
					$('[name="hub_id"]').val(hubsSpokesCollection[beatId].le_wh_id);
					$('[name="spoke_id"]').val(hubsSpokesCollection[beatId].spoke_id);
				}
			}
		});	
    });
    function getcashback()	{
        var newOrderData = {
                cashback: $("#add_cash_back").val(),
                order_id: $("#edit_order_id").val(),
                order_code: $("#edit_order_code").val(),
                order_status: $("#edit_order_status").val(),
                order_status_id: $("#order_status_id").val(),
                cust_le_id: $("#cust_le_id").val(),
                user_id: $("#user_id").val(),
                is_active: $("#is_active").prop('checked'),
            };
        if(newOrderData.is_active){
            var token=$("#csrf_token").val();
                $.post('/retailers/getcashback',newOrderData,function(response){
                    if(response){
                        $('#add_cash_back').val(response);
                        $("#add_cash_back").prop("readonly", true);
                        $('#order_edit_form').formValidation('revalidateField', 'add_cash_back');
                    }
                    else{
                        $('#add_cash_back').val(0);
                        $("#add_cash_back").prop("readonly", true);
                        $('#order_edit_form').formValidation('revalidateField', 'add_cash_back');
                    }
                });
        }else{
            $("#add_cash_back").prop("readonly", false);
            $('#order_edit_form').formValidation('revalidateField', 'add_cash_back');
        }
    }
    function getUserList()
    {
        let cu_mobile_no = $('#cu_mobile_no').val();
        let business_type = $('#business_type').val();
        let le_id=$('#legal_entity_id').val()
        let input_data={
            mobile_no:cu_mobile_no,
            business_type:business_type,
            le_id: le_id
        };
                console.log('ínputdata',input_data);

        input_data=JSON.stringify(input_data);
        console.log('ínputdata',input_data);
        $('#users_list').igGrid({
        dataSource: '/retailers/getUsersList?data='+input_data,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: "{{trans('retailers.grid.user_id')}}", key: 'user_id', dataType: 'string', width: '0%'},
            {headerText: "{{trans('retailers.grid.name')}}", key: 'name', dataType: 'string', width: '40%'},
            {headerText: "{{trans('retailers.grid.mobile')}}", key: 'mobile_no', dataType: 'string', width: '20%'},            
            {headerText: "{{trans('retailers.grid.email')}}", key: 'email_id', dataType: 'string', width: '20%'},
            {headerText: "{{trans('retailers.grid.aadhar_id')}}", key: 'aadhar_id', dataType: 'string', width: '20%'},            
            {headerText: "{{trans('retailers.grid.role')}}", key: 'rolename', dataType: 'string', width: '10%'},            
            {headerText: "{{trans('retailers.grid.otp')}}", key: 'otp', dataType: 'string', width: '10%'},            
            {headerText: "{{trans('retailers.grid.actions')}}", key: 'action', dataType: 'string', width: '10%'}            
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'is_approved', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'user_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
    function getAreaList()
    {
        $('#warehouse_list').igGrid({
        dataSource: '/retailers/getServicableList/'+$('#pincode').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: "{{trans('retailers.grid.warehouse_name')}}", key: 'lp_wh_name', dataType: 'string', width: '15%'},
            {headerText: "{{trans('retailers.grid.contact_name')}}", key: 'contact_name', dataType: 'string', width: '15%'},
            {headerText: "{{trans('retailers.grid.mobile')}}", key: 'phone_no', dataType: 'string', width: '7%'},            
            {headerText: "{{trans('retailers.grid.email')}}", key: 'email', dataType: 'string', width: '15%'},            
            {headerText: "{{trans('retailers.grid.address1')}}", key: 'address1', dataType: 'string', width: '20%'},            
            {headerText: "{{trans('retailers.grid.address2')}}", key: 'address2', dataType: 'string', width: '10%'},            
            {headerText: "{{trans('retailers.grid.city')}}", key: 'city', dataType: 'string', width: '7%'},            
            {headerText: "{{trans('retailers.grid.state')}}", key: 'state', dataType: 'string', width: '10%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'is_approved', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'lp_wh_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
    //collection_details
    function getCollectionDetails()
    {
        $('#collection_details').igGrid({
        dataSource: '/retailers/getCollectionDetails/'+$('#legal_entity_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
        // Order ID , Delivery Boy, Amount Colected, Payement Type, Handover Boy, Due Amount, Due Amount Reason, Item Recieved Date, Payment Received Date...
            {headerText: "{{trans('retailers.grid.payment_date')}}", key: 'date', dataType: 'date', width: '8%'},
            {headerText: "{{trans('retailers.grid.order_id')}}", key: 'order_code', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.collection_id')}}", key: 'collection_code', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.amount')}}", key: 'amount', dataType: 'string', width: '10%', template: "<div class='rightAlignText'>${amount}</div>"},
            {headerText: "{{trans('retailers.grid.paid_by')}}", key: 'paid_by', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.delivered_by')}}", key: 'delivered_by', dataType: 'string', width: '20%'},
            {headerText: "{{trans('retailers.grid.collected_amount')}}", key: 'collected_amount', dataType: 'string', width: '10%', template: "<div class='rightAlignText'>${collected_amount}</div>"},
            {headerText: "{{trans('retailers.grid.payment_mode')}}", key: 'Payment_Mode', dataType: 'string', width: '7%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window"
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'mp_order_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }

    function getOrderList()
    {
        $('#orders_list').igGrid({
        dataSource: '/retailers/getOrderList/'+$('#legal_entity_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: "{{trans('retailers.grid.order_id')}}", key: 'order_code', dataType: 'string', width: '15%'},
            {headerText: "{{trans('retailers.grid.shop_name')}}", key: 'shop_name', dataType: 'string', width: '15%'},
            {headerText: "{{trans('retailers.grid.order_status')}}", key: 'master_lookup_name', dataType: 'string', width: '10%'},            
            {headerText: "Order Date", key: 'order_date',dataType:'string',width: '15%'},
            {headerText: "{{trans('retailers.grid.total')}}", key: 'total', dataType: 'string', width: '15%', template: "<div class='rightAlignText'>${total}</div>"},
            {headerText: "{{trans('retailers.grid.email')}}", key: 'email', dataType: 'string', width: '20%'},
            {headerText: "{{trans('retailers.grid.mobile')}}", key: 'phone_no', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.beat')}}", key: 'beat', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.area_name')}}", key: 'areaname', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.hub')}}", key: 'hub', dataType: 'string', width: '10%'},
            {headerText: "Actions", key: 'actions', dataType: 'string', width: '7%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
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
                persist: false

            }
        ],
            primaryKey: 'mp_order_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
	$('#pincode').keyup(function(){
		var formValidation = $('#retailersinfo').data('formValidation');
		var isValid = formValidation.isValidField('org_pincode');
		if(isValid != false)
		{
			getAreas(formValidation);
	//        getBeats(formValidation);
		}
	});
    $('#parent_le').change(function(){
            var parentId = $('#parent_le').val();
            $('#beat').empty();
            $.ajax({
                url: '/retailers/getBeats',
                data: {'parentId' : parentId},
                type: 'POST',
                success: function(data) {
                        $('#beat').append($("<option></option>")
                                               .attr("value",'')
                                               .text("Please Select"));
                        $('#beat').select2({placeholder: "Select Beat"});
                        if(data.length > 0)
                        {
                            $.each(data, function(key, value){
                                console.log(value.pjp_name);
                                $('#beat').append($("<option></option>")
                                               .attr("value",value.pjp_pincode_area_id)
                                               .text(value.pjp_name));
                            });
                        }
                }
            })
            $('#retailersinfo').formValidation('revalidateField', 'beat');
        });
	function getAreas(validator)
	{
	//    var formValidation = $('#retailersinfo').data('formValidation');
	//    var isValid = formValidation.isValidField('org_pincode');
		var isValid = validator.isValidField('org_pincode');
		var previous_area_pincode = $('#previous_area_pincode').val();
		var pincode = $('#pincode').val();
		if(isValid != false && previous_area_pincode != pincode)
		{
			$('#hubs').empty();
			if(pincode.length == 6)
			{
				$.ajax({
					url: '/retailers/getAreaList',
					data: {'_token' : $('#csrf-token').val(), 'pincode' : pincode},
					type: 'POST',
					success: function (response) {
						var data = $.parseJSON(response);
						$('#hubs').append($("<option></option>")
											   .attr("value",'')
											   .text("{{trans('retailers.form_validate.default_hub')}}"));
						$('#hubs').select2({placeholder: "{{trans('retailers.form_validate.default_hub')}}"});
						if(data.length > 0)
						{
							$.each(data, function(key, value){
								$('#hubs').append($("<option></option>")
											   .attr("value",value.le_wh_id)
											   .text(value.lp_wh_name));
							});
						}
					}
				});
                //$("#loadReturnSumData").show();
                // $.ajax({
                //     url: '/retailers/getBeatDataPincode',
                //     data: {'_token' : $('#csrf-token').val(),'pincode' : pincode},
                //     type: 'POST',
                //     success: function (response) {
                //         var data = response;
                //         console.log(response);
                //         console.log($('#beat').val());
                //         $('#beat').html(response);
                //         $("#beat").select2("val", "");
                //         console.log($('#beat').val());
                //         $("#loadReturnSumData").hide();
                //     }
                // });
			}
			$('#previous_area_pincode').val(pincode);
		}
	}
   

    function getCashBackHistoryGrid(grid_id)
    {
        var legal_entity_id = $('#legal_entity_id').val();
        $(grid_id).igGrid({
            dataSource: "/users/cashbackhistory/"+legal_entity_id+"/-1",
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            recordCountKey: 'totalRecCount',
            columns: [
                {headerText: "{{trans('retailers.grid.order_details')}}", key: 'order_details', headerCssClass: 'rightAlign', dataType: 'string', width: '20%'},
                {headerText: "{{trans('retailers.grid.delivery_amt')}}", key: 'delivery_amt', template: '<div class="rightAlign"> ${delivery_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: "{{trans('retailers.grid.cash_back_amt')}}", key: 'cash_back_amt', template: '<div class="rightAlign"> ${cash_back_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: "{{trans('retailers.grid.transaction_type')}}", key: 'transaction_type', dataType: 'string', width: '20%'},
                {headerText: "{{trans('retailers.grid.transaction_date')}}", key: 'transaction_date', dataType: 'string', width: '15%'}
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
            width: '80%',
            height: '80%',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            type: 'local',
            showHeaders: true,
            fixedHeaders: true
        });
    }
	
	function editUser(userId)
	{
		$.get('/retailers/editusers/' + userId, function (response) {
			$("#basicvalCode").html("{{trans('retailers.title.edit_user')}}");
	//        $("#edit").click();
			if(typeof response  != "undefined")
			{
				var userData = $.parseJSON(response);
	//            console.log(userData);
				$.each(userData, function(key, value){
	//                console.log(key);
	//                console.log(value);
					if(key == 'is_active')
					{
                        console.log('in this function');
						if(value == 1)
						{
                            console.log('in active function');
							//$('#is_active').prop('checked', true);
                            $('#user_is_active').attr('checked','checked');
                            //$ ('#is_active').prop(':checked');
						}else{
							$('#'+'user_is_active').prop('checked', false);
						}
					}else{
						$('#'+key).val(value);
					}
				});
			}
			$('#retailers_edit_user').modal('show');
	//        $("#userDiv").html(response);
		});
	}
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
    function editOrderCode(orderId)
    {
        $('#edit_Order_Code').modal('show');
        $('#edit_Order_Code').modal({backdrop:'static', keyboard:false});
        $.post('/retailers/editorders/' + orderId,function(response){
            if(response){
                $("#edit_order_id").attr('value',response.gds_order_id);
                $("#edit_order_code").attr('value',response.order_code);
                $("#edit_order_status").attr('value',response.gds_order_status);
                $('#order_status_id').attr('value',response.order_status_id);
                $('#cust_le_id').attr('value',response.cust_le_id);
                $('#user_id').attr('value',response.user_id);
            }
        });
    }



$(document).ready(function () {
          $("#update_lender_grid").igGrid({
            dataSource: '/retailers/Lender/partner',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
            {headerText: "Lending", key: 'name', dataType: 'string', width: '30%'},       
            {headerText: "Credit Limit", key: 'credit_limit', dataType: 'string', width: '30%'}, 
            {headerText: "Active", key: 'is_active', dataType: 'string', width: '20%'},          
            {headerText: "Action", key:'actions', dataType: 'string', width: '20%'}  
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'mfc_id', allowSorting: true },
                    {columnKey: 'actions', allowSorting: false },
                    {columnKey: 'credit_limit', allowSorting: true },
                   
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'actions', allowFiltering: false },
                        {columnKey: 'mfc_id', allowFiltering: true },
                        {columnKey: 'credit_limit', allowFiltering: true },
                        {columnKey: 'role', allowFiltering: true },
                     
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                }
                
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });

    });


    $('#mfc_mapping').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         b_name: {
                validators: {
                    notEmpty: {
                        message: 'Select An Option'
                    },                  
                }
            }, 
         c_limit: {
            validators: {
                notEmpty: {
                    message: 'Enter credit Limit'
                },              
            }
        },     
      },
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#mfc_mapping').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/retailers/mfcMapping',
        data: frmData,
        success: function (respData)
        {
            $("#state_id").prop('selectedIndex',0);
            $('#mfc_mapping').formValidation('resetForm', true);            
            $('.close').trigger('click');
            $('#success_message_ajax').text("Succesfully Inserted");
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            reloadGridData();
        }
    });

    function reloadGridData(){
    $("#update_lender_grid").igGrid("dataBind");
     }
});


function editDetails(user_id){ 
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/retailers/editMfc' + user_id,
            success: function (data)
            {
                if(data.is_active == 1){
                $("#mfc_is_active").attr("checked","checked");
                $("#mfc_is_active").parent().addClass("checked");
                }                
                $("#edit_mfc_id").val(data.cust_mfc_id);
                $("#edit_c_limit").val(data.credit_limit);
                $("#mfc_is_active").val(data.is_active);
                $("#mfc_mapping_dropdown").select2().select2('val',data.mfc_id);
                $('#mfcdetailsUpdate').modal('toggle');
                reloadGridData();
            }
    });

    function reloadGridData(){
    $("#update_lender_grid").igGrid("dataBind");
     }
    // $('#legalentity_view_data').modal('toggle');
     }

 var token  = $("#csrf-token_hidden").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/retailers/mfc_bussiness_names',
            success: function (data)
            {
                $("#mfc_mapping_dropdown").html(data);
            }
    });






    $('#mfc_mapping_edit_user').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         b_name: {
                validators: {
                    notEmpty: {
                        message: 'Select An Option'
                    },                  
                }
            }, 
         c_limit: {
            validators: {
                notEmpty: {
                    message: 'Enter credit Limit'
                },              
            }
        },     
      },
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#mfc_mapping_edit_user').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/retailers/updateUser',
        data: frmData,
        success: function (respData)
        {
            $("#state_id").prop('selectedIndex',0);
            $('#mfc_mapping_edit_user').formValidation('resetForm', true);            
            $('.close').trigger('click');
            $('#success_message_ajax').text("Succesfully Inserted");
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
             reloadGridData();
        }
    });

    function reloadGridData(){
    $("#update_lender_grid").igGrid("dataBind");
     }
});

     //feedback grid
    
    $(function () {
        feedback();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
        $('#feedback_dd_created_at').find('.ui-iggrid-filtericonnoton').parents('li').remove();
        $("#feedback_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#feedback_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#feedback_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
    });
    
    $('#addnewfeedbackmodal').on('hide.bs.modal', function () {
        $("#addnewfeedbackform").bootstrapValidator('resetForm', true);
        $("#add_feedback_group").select2("val", "");
        $("#add_feedback_type").select2("val", "");
        $("#add_comments").select2("val", "");
        $("#retailer_comments").val("");
        $("#feedbackimage").val("");
        $("#feedbackaudio").val("");
        $('.modal-backdrop').remove();

    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#addnewfeedback").click(function(){
        $("#addnewfeedbackmodal").modal("show");
        $("#addnewfeedbackmodal").modal({backdrop:'static', keyboard:false});
        $('.modal-backdrop').remove();
    });
    // Hiding the Alert on Page Load
    $("#modalAlert").hide();
    $("#alertStatus_feedback").hide();
    
    function feedback(){
        $('#feedback').igGrid({
            dataSource: '/retailers/getfeedbackhistory/'+$('#legal_entity_id').val(),
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'results',
            generateCompactJSONResponse: false,
            enableUTCDates: true, 
            renderCheckboxes: true,
            columns: [
                {headerText: "{{trans('retailers.grid.business_legal_name')}}", key: 'legal_entity_id', dataType: 'string', width: '25%'},
                {headerText: "{{trans('retailers.grid.feedback_group_type')}}", key: 'feedback_group_type', dataType: 'string', width: '25%'},
                {headerText: "{{trans('retailers.grid.feedback_type')}}", key: 'feedback_type', dataType: 'string', width: '25%'},
                {headerText: "{{trans('retailers.grid.comments')}}", key: 'comments', dataType: 'string', width: '25%'},            
                {headerText: "{{trans('retailers.grid.created_by')}}", key: 'created_by', dataType: 'string', width: '25%'},
                {headerText: "{{trans('retailers.grid.created_at')}}", key: 'created_at', dataType: 'date', width: '25%'},
                {headerText: "{{trans('retailers.grid.media')}}",key: 'picture', dataType: 'string',width: '20%'},
                {headerText: "{{trans('retailers.grid.audio')}}",key: 'audio',dataType: 'string', width: '20%'},
                {headerText: "{{trans('retailers.grid.actions')}}", key: 'actions', dataType: "string", width: '15%'}        
            ],
            features: [
                {
                    name: "Filtering",
                    mode: "simple",
                    columnSettings: [
                        {columnKey: 'actions', allowFiltering: false},
                    ]
                },
                {
                    name: "Sorting",
                    type: "remote",
                    persist: false,
                    columnSettings: [
                        {columnKey: 'actions', allowSorting: false},
                    ],
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
                    name: "Resizing",
                }
            ],
                primaryKey: 'fid',
                width: '100%',
                initialDataBindDepth: 0,
                localSchemaTransform: false
        });
    }

    $('#addnewfeedbackform')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_feedback_group: {
                    validators: {
                        notEmpty: {
                            message: "Please select feedback group"
                        },
                    }
                },
                add_feedback_type: {
                    validators: {
                        notEmpty: {
                            message: "Please select feedback type"
                        },
                    }
                },
                add_comments: {
                    validators: {
                        notEmpty: {
                            message: "Please select comments"
                        },
                    }
                }
                
            }
        })

    
    .on('success.form.bv', function(event) {
        event.preventDefault();
        var feedbackimage = document.getElementById("feedbackimage");
        var feedbackaudio = document.getElementById("feedbackaudio");
        var add_feedback_group = $('#add_feedback_group').val();
        var legal_entity_id = $('#legal_entity_id').val();
        var add_feedback_type = $('#add_feedback_type').val();
        var add_comments = $('#add_comments').val();
        var retailer_comments = $('#retailer_comments').val();

        feedbackimage = feedbackimage.files[0];
        feedbackaudio = feedbackaudio.files[0];
        formData= new FormData();
        formData.append("feedbackimage", feedbackimage);
        formData.append("feedbackaudio", feedbackaudio);
        formData.append("add_feedback_group", add_feedback_group);
        formData.append("legal_entity_id", legal_entity_id);
        formData.append("add_feedback_type", add_feedback_type);
        formData.append("add_comments", add_comments);
        formData.append("retailer_comments", retailer_comments);
        var token=$("#_token").val();
        $('#addfeedbackdata').attr('disabled', true);
        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/retailers/addfeedback",
                type:"POST",
                data: formData,
                 processData: false,
                 contentType: false,            
                success:function(response){
                $("#addnewfeedbackmodal").modal("hide");
                if(response.status){
                        $("#addnewfeedbackform").bootstrapValidator('resetForm', true);
                        $("#alertStatus_feedback").attr("class","alert alert-success").text("New feedback added successfully").show().delay(3000).fadeOut(350);
                        $('#addfeedbackdata').attr('disabled', false);
                        $('#feedback').igGrid("dataBind");
                    }
                    else{
                        $("#addnewfeedbackform").bootstrapValidator('resetForm', true);
                        $("#alertStatus_feedback").attr("class","alert alert-danger").text("Failed to add new feedback").show().delay(3000).fadeOut(350);
                    }

                }
        });       
    });


    $("#add_comments").change(function(){
        $("#retailer_comments").val($('#add_comments option:selected').html())
    });

    $('#add_feedback_group').change(function(){
        let token  = $("#csrf-token").val();
        $("#add_feedback_type").select2("val", "");
        $('#loaddata').show();
        $.ajax({
            type:"GET",
            headers: {'X-CSRF-TOKEN':token},
            url:"/retailers/groupspecific/"+$('#add_feedback_group').val(),
            success: function(result){
                $('#add_feedback_type').html('');
                if(result.status){
                    $('#add_feedback_type').append(`<option value=''>Please select</option>`);
                    result['data'].forEach(function(data){
                        $('#add_feedback_type').append(`<option value=${data['value']}>${data['name']}</option>`);
                    });
                    $('#addnewfeedbackform').bootstrapValidator('revalidateField', 'add_feedback_type');
                    $('#loaddata').hide();
                  
                }else{
                    $('#loaddata').hide();
                }
            }
        });
    });

    function deletefeedbackrecord(id) {
        var decision = confirm("Are you sure you want to delete");
        if(decision){
            $.post('/retailers/delete/'+id,function(response){
                if(response.status){
                    $("#alertStatus_feedback").attr("class","alert alert-info").text("Feedback deleted successfully").show().delay(3000).fadeOut(350);
                    $('#feedback').igGrid("dataBind");
                }
                else
                    $("#alertStatus_feedback").attr("class","alert alert-danger").text("Failed to delete feedback").show().delay(3000).fadeOut(350);
            });
        }
    }

    $("#frmUpload").formValidation({
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            documentType: {
                validators: {
                    notEmpty: {
                        message: "Please select document type"
                    }
                }
            }
    }}).on('success.form.fv', function (event) {
        event.preventDefault();
        var form = document.getElementById("frmUpload");
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/retailers/uploadDoc",
            type: "POST",
            data: new FormData(form),
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            beforeSend: function (xhr) {
                $('input[name="btnUpload"]').attr('disabled', true);
                $('.loderholder').show();
            },
            success: function (response) {
                $("#ajaxResponseDoc").attr("class","alert alert-success").text(response.message).show().delay(3000).fadeOut(350);
                $('#leDocList').append(response.docText);
                $('#frmUpload')[0].reset();
                $('input[name="btnUpload"]').attr('disabled', false);
                $('.loderholder').hide();
                location.reload();

            },
            error: function (response) {
                $('#ajaxResponseDoc').removeClass('alert-success').addClass('alert-danger').html("Unable to save file").show();
                $('.loderholder').hide();
            }
        });
    });

    $(document).on('click', '.le-del-doc', function () {
        var docId = $(this).attr("id");
        if ( confirm("{{Lang::get('inward.alterDelete')}}") ) {
            deleteDoc(docId);
            $(this).closest('tr').remove();
            var docCount = $('#leDocList tbody').find('tr').length;
            if(!docCount)
            {
                $('[name="is_document_required"]').val(1);
            }
        }
    });


    function deleteDoc(id) {
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/retailers/deleteDoc",
        type: "POST",
        data: {id: id},
        dataType: 'json',
        success: function (response) {
            $("#ajaxResponseDoc").attr("class","alert alert-success").text(response.message).show().delay(3000).fadeOut(350);
        },
        error: function (response) {
            $("#ajaxResponseDoc").attr("class","alert alert-success").text('Failed to delete the record').show().delay(3000).fadeOut(350);
        }
    });
}
</script>
@stop
@extends('layouts.footer')