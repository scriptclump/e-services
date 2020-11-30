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
        <div class="caption"> {{trans('retailers.title.edit_page_title')}} </div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>
      </div>
      <div class="portlet-body">
          <div class="tabbable-line">
            <ul class="nav nav-tabs ">
              <li class="active"><a href="#tab_11" data-toggle="tab"> {{trans('retailers.tab.retailer_info')}} </a></li>              
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_11">  
                    <div class="tab-content headings">
                        @include('Retailer::retailerinfo')                                
                    </div>
                </div>                
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">

<input type="text" id="supplier_id" value="<?php echo Session::get('supplier_id');?>" hidden />
<input type="text" id="legalentity_id" value="<?php echo Session::get('legalentity_id');?>" hidden />
@stop
@section('style')
<style type="text/css">
.parent_cat{font-size:14px !important;}
.sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
.prod_class{padding-left:10px !important; background:#fff !important; }


.select2-disabled{font-weight:bold !important;}


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
#dvMap{height:304px !important; width:269px !important;}

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

</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
@include('includes.validators')
<script type="text/javascript"> 
    $(document).ready(function () {
        $('#retailersinfo').bootstrapValidator({
            message: "{{trans('retailers.form_validate.invalid')}}",
            feedbackIcons: {
//                valid: 'glyphicon glyphicon-ok',
//                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                retailer_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('retailers.form_validate.empty_name')}}"
                        }
                    }
                },
                business_type_id: {
                    validators: {
                        callback: {
                            message: "{{trans('retailers.form_validate.empty_segment')}}",
                            callback: function(value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                 gst_state_codes: {
                    validators: {
                        callback: {
                            message: "{{trans('retailers.form_validate.empty_segment')}}",
                            callback: function(value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                volume: {
                    validators: {
                        callback: {
                            message: "{{trans('retailers.form_validate.empty_volume')}}",
                            callback: function(value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                org_state: {
                    validators: {
                        callback: {
                            message: "{{trans('retailers.form_validate.empty_state')}}",
                            callback: function(value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                org_country: {
                    validators: {
                        callback: { 
                            message: "{{trans('retailers.form_validate.empty_country')}}",
                            callback: function(value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                shutters: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('retailers.form_validate.empty_shutters')}}",
                        }
                    }
                },
                org_address1: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('retailers.form_validate.empty_address')}}",
                        }
                    }
                },
                org_pincode: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('retailers.form_validate.empty_pincode')}}",
                        }
                    }
                },
                org_city: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('retailers.form_validate.empty_city')}}",
                        }
                    }
                }
            }
//        }).on('success.form.bv', function (e) {
//            e.preventDefault();
//            $.ajax({
//                url: '/retailers/update',
//                data: $('#retailersinfo').serialize(),
//                type: 'POST',
//                success: function (response) {
//                    responseData = $.parseJSON(response);
//                    if (responseData.status == 0) {
//                        $('#flass_message').text(responseData.message);
//                        $('div.alert').show();
//                        $('div.alert').removeClass('hide');
//                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
//                        return false;
//                    } else {
//                        $('#flass_message').text('Approval comments updated sucessfully.');
//                        $('div.alert').show();
//                        $('div.alert').removeClass('hide');
//                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
//                        $('html, body').animate({scrollTop: '0px'}, 800);
//                        window.setTimeout(function () {
//                            window.location.href = '/retailers/index';
//                        }, 2000);
//                    }
//                }
//            });
        });
    });
</script>
@stop
@extends('layouts.footer')