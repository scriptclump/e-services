<html lang="en" class="no-js">
<!--
    <![endif]-->

<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>@if(isset($title))
            {{$title}}
            @else
            Welcome to EBUTOR
            @endif</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>

<link href="{{ URL::asset('assets/global/css/custom-ebutor.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
<!--<link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/admin/layout4/css/layout.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/layout4/css/themes/light.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/layout4/css/custom.css') }}" rel="stylesheet" type="text/css" />
<link rel="icon" href="{{{ asset('img/favicon.png') }}}" type="image/x-icon" />
<link rel="shortcut icon" href="{{{ asset('img/favicon.png') }}}" type="image/x-icon" />
</head>
<style>
.form-wizard .steps > li > a.step > .desc{font-weight: 600 !important;}
.form .form-body {
    height: 93% !important;
    position: relative !important;
}

.form-wizard .steps > li.active > a.step .number {
        background-color:#fff !important;
        color: #5b9bd1;
        border: 2px solid #5d9ad0 !important;
        color:#5d9ad0 !important;
    }
	 .form-wizard .steps > li.active > a.step .desc {
        color: #5b9bd1 !important;
    }
.form-wizard .steps > li > a.step > .number {
        z-index: 9 !important;
        position: relative !important;
        border:2px solid #dfdfdf !important;
        color:#dfdfdf !important;
    }
.progress > .progress-bar-success {
    background-color: #75a9d7 !important;
}
.form-wizard .steps > li > a.step > .desc {color: #e2e2e2 !important;}
.progress1 {
        height: 2px !important;
        margin-bottom: 0px !important;
        overflow: hidden  !important;
        background-color: #f5f5f5 !important;
        border-radius: 4px !important;
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
        position: relative !important;
        top: -76px !important;
        width: 65% !important;
        margin: 0 auto !important;
    }
#upload_file, #pan_file {
  background-color: #fff !important;
  border: 0px;
  float: left;
  position: absolute;
  margin-left: 20px;
  line-height: 30px;
  right:-300px;
  top: 0;
}
.signupmargintop {
    margin-top: 84px !important;
}
.page-content  .col-md-12{padding-left: 10px;padding-right: 10px;}
.page-header.navbar .page-logo .logo-default {
    margin-top: 11px !important;
}
.portlet-body {position: relative;min-height: 79%; }
</style>
<body class="page-header-fixed">
<div class="page-header navbar navbar-fixed-top"> 
  <!-- BEGIN HEADER INNER -->
  <div class="page-header-inner"> 
    <!-- BEGIN LOGO -->
    <div class="page-logo"> <a href="#"> <img src="{{url('/')}}/assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/></a> </div>
    <div class="page-top"> 
      <!-- BEGIN HEADER SEARCH BOX --> 
      <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box --> 
      <!-- END HEADER SEARCH BOX --> 
      <!-- BEGIN TOP NAVIGATION MENU -->
      <div class="top-menu">
        <h4 class="signin-signup">Already Registered ? <span> <a href="/login">Signin</a> </span> </h4>
      </div>
      <!-- END TOP NAVIGATION MENU --> 
    </div>
    <!-- END PAGE TOP --> 
  </div>
  <!-- END HEADER INNER --> 
</div>
<div class="page-content signupmargintop">
  <div class="">
    <div class="">
      <div class="col-md-12">
        <div class="portlet box" id="form_wizard_1">
          <div class="portlet-body form">
            <div class="form-wizard">
              <div class="form-body">
                <ul class="nav nav-pills nav-justified steps">
                  <li> <a href="#tab1" data-toggle="tab" class="step"> <span class="number"> <i class="fa fa-info" aria-hidden="true"></i></span> <span class="desc">Set Password </span> </a> </li>
                  <li> <a href="#tab2" data-toggle="tab" class="step"> <span class="number"> <i class="fa fa-building-o" aria-hidden="true"></i> </span> <span class="desc"> Business Information </span> </a> </li>
                  <li> <a href="#tab3" data-toggle="tab" class="step active"> <span class="number"> <i class="fa fa-check" aria-hidden="true"></i> </span> <span class="desc"> Complete </span> </a> </li>
                </ul>
                <div id="bar" class="progress progress1 progress-striped" role="progressbar">
                  <div class="progress-bar progress-bar-success"></div>
                </div>
                <div class="tab-content">
                  <div class="alert alert-danger display-none">
                    <button class="close" data-dismiss="alert"></button>
                    You have some errors. Please check below. </div>
                  <div class="alert alert-success display-none">
                    <button class="close" data-dismiss="alert"></button>
                    Your form validation is successful! </div>
                  <div class="tab-pane" id="tab1"> 
                    <form  class="form-horizontal" id="submit_form2" method="POST">
                      <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                      <div class="row" id="wellsignup">
                        <h3>Congrats!</h3>
                        <p>You have successfully verified your email, Please set your password to continue</p><br>
                        <div class="col-md-5 col-md-offset-3">
                          <div class="form-group">
                            <label class="control-label col-md-3" style="position:inherit; right: -13px;">Set&nbsp;Password<span class="required">*</span> </label>
                            <div class="col-md-9">
                              <input type="password" placeholder="Set Password" class="form-control" name="set_password" id="set_password"/>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3">Confirm&nbsp;Password<span class="required">&nbsp;*</span> </label>
                            <div class="col-md-9">
                              <input type="password" placeholder="Confirm Password" class="form-control" name="confirm_password" id="confirm_password"/>
                            </div>
                          </div>
                          <input type="hidden" name="active" id="active" value="{!! $active !!}">
                          <input type="hidden" name="user_id" id="user_id" value="{!! $id !!}">
                          <input type="hidden" name="legal_id" id="legal_id" value="{!! $legal_id !!}">
                          <div class="form-group" id="signup_button_1">
                              <div class="col-md-3">&nbsp;</div>
                                <div class="col-md-9" >
                                  <input type="button" name="signup_1" class="btn green-meadow btn-block " id="signup_1" value="Continue">  
                                </div>                    
                            </div>
                        </div>
                      </div>
                      
                    </form>
                   </div>
                  <div class="tab-pane" id="tab2">
                  <div class="col-md-5 col-md-offset-3">
                  
                  <form class="form-horizontal bussiness-form" id="submit_form1" role="form" method="POST"  files="true" enctype ="multipart/form-data">
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                    <input type="hidden" name="legal_entity_id" value="{!! $legal_id !!}" />
                    <div class="form-group">
                      <label class="control-label col-md-3">Business Name&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="businessname" placeholder="Business Name"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">Business Type&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <select class="form-control" name="business_type">
                          <option value="" selected>--Select--</option>
                         @foreach($entity_type as $key => $entity)
                            <option value="{!! $entity->entity_type_id !!}"> {!! $entity->entity_type_name !!} </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">Address 1&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="address1" placeholder="Address 1"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">Address 2 </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="address2" placeholder="Address 2"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">City&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="city" placeholder="City"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">State&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <select name="state_id" class ="form-control" id="state_id">
                          <option value="">Please Select..</option>
                          @foreach ($states as $key => $value)
                           <option value="{!! $value->state_id !!}">{!! $value->state !!}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">Pincode&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="pincode" placeholder="Pincode"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">PAN Number&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="pan" placeholder="Pan"/>
                      </div>
                      <div class="col-md-1">
                        <div class="fileUpload btn green-meadow"> <span>Upload PAN Proof</span>
                          <input id="uploadBtn1" name="doc_files" type="file" class="upload" />
                        </div>
                        <input name="doc_files" id="pan_file" placeholder="Choose File" disabled="disabled" />
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-3">TIN Number&nbsp;<span class="required">*</span> </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="tin" placeholder="TIN Number"/>
                      </div>
                      <div class="col-md-1">
                        <div class="fileUpload btn green-meadow"> <span>Upload TIN Proof</span>
                          <input id="uploadBtn2" name="tin_file" type="file" class="upload" />
                        </div>
                        <input id="upload_file" name="tin_file" placeholder="Choose File" disabled="disabled" />
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-8" >
                          <button class="btn green-meadow btn-block " id="signup_2" type="submit">Continue</button>
                        </div>                    
                    </div>
                    
                    </div>
                    </div>

                      <div class="row">
                        <div class="col-md-12 text-center"> 
                          <!--<a href="javascript:;" class="btn blue button-submit">Submit <i class="m-icon-swapright m-icon-white"></i></a>
                          <a href="javascript:;" class="btn blue button-next" id="signup_2">Submit <i class="m-icon-swapright m-icon-white"></i> </a> </div>--> 
                      </div>
       
                  </form>
                 </div>
                <div class="tab-pane" id="tab3">
                  <h3 style="text-align:center; color:#31708f;">Thank You</h3>
                  <p class="block" style="text-align:center;">You have successfully completed the signup process with <span style="color:#1bbc9b;">Seller ERP</span>, Please <a href="/login">login</a> to your account </p>
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
<div class="page-footer signupfooter">
  <div class="page-footer-inner">&copy; 2016. Sunera eSeal India Pvt. Ltd. All rights reserved. <!--Copyright &copy; <?php echo date('Y') ?> Ebutor. All rights reserved. --></div>
  <div class="scroll-to-top"> <i class="icon-arrow-up"></i> </div>
</div>
<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery.cokie.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script> 
<!--<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>--> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script> --> 
<!-- <script src="{{ URL::asset('assets/global/plugins/morris/raphael-min.js') }}" type="text/javascript"></script> --> 
<script src="{{ URL::asset('assets/global/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/layout2/scripts/quick-sidebar.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-registration.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/index3.js') }}" type="text/javascript"></script> --> 
<!--<script src="{{ URL::asset('assets/admin/pages/scripts/tasks.js') }}" type="text/javascript"></script>--> 
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script type="text/javascript">
jQuery(document).ready(function () {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
    Demo.init(); // init demo features
    FormWizard.init();
    QuickSidebar.init(); // init quick sidebar
    // Index.init(); // init index page
//    Tasks.initDashboardWidget(); // init tash dashboard widget 

    console.log('we are here');
    var pathname = window.location.pathname.split("/");
    var filename = pathname[pathname.length - 1];
    if (filename > 0)
    {
        $('[href="#tab2"]').parent().addClass('active');
        $('.progress-bar').width('50%');
        $('#tab2').show();
        $('#tab3').hide();
    }
});

$(document).ready(function(){
	$('.progress-bar-success').width('0%');
  var active = $('#active').val();
  console.log('active:'+active);
  if(active == 1){
  $('#tab1').hide();
  $('#tab2').show();
  $('[href="#tab2"]').parent('li').addClass('active');
  $('.progress-bar').width('50%');
  }
  else{
  $('#tab1').show();
  $('#tab2').hide();
  }
});

$(document).ready(function() {
    $('#submit_form2').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#signup_1',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          set_password: {
              validators: {
                  notEmpty: {
                        message: 'Please enter password'
                    },
                  stringLength: {
                        min: 5,
                        max: 14,
                        message: 'The password must be more than 4 and less than 14 characters long'
                    }
              }
          },
          confirm_password: {
              validators: {
                  notEmpty: {
                        message: 'Confirm password cannot be empty'
                    },
                  identical: {
                      field: 'set_password',
                      message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
          //document.getElementById("calculate").disabled = false;
          //return false;
      });

$('#signup_1').click(function () {
     var formValid = $('#submit_form2').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        console.log('inif');
        return false;
      }
   else{
    console.log('inelse');
    $.ajax({
        url: '/legalentity/savePassword',
        data: $('#submit_form2').serialize(),
        type: 'POST',
        success: function (result)
        {
            var response = JSON.parse(result);
            console.log(response);
            console.log(response.status);
            if (response.status == true)
            {
                console.log('here');
                $('#tab2').show();
                $('#tab1').hide();
                $('.progress-bar').width('50%');
            }
            else{
              alert(response.message);
            }
        },
        failed: function ()
        {
            $('#signup_button_1').show();
        }
    });
  }
    console.log('we areh after ajax');
});
});
  $(document).ready(function (){
    $('#submit_form1').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#signup_2',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          businessname: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
              }
          },
          business_type: {
              validators: {
                  notEmpty: {
                        message: ' '
                    }
                }
            },
          address1: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/]+$/,
                            message: ' '
                        },
                }
            },
          address2: {
              validators: {
                  regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/]+$/,
                            message: ' '
                    },
                }
            },
          city: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
                }
            },
          state_id: {
              validators: {
                  notEmpty: {
                        message: ' '
                    }
                }
            },
          pincode:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        },
                }
            },
          pan:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[a-zA-Z0-9]+$/i,
                            message: ' '
                    },
                   stringLength: {
                        min: 10,
                        max: 10,
                        message: ' '
                  }
                }
            },
          tin:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[0-9]+$/i,
                            message: ' '
                    },
                  stringLength: {
                        min: 11,
                        max: 11,
                        message: ' '

                  }
                }
            },
          doc_files:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 10*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            },
            tin_file:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 10*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            }
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
          //document.getElementById("calculate").disabled = false;
          //return false;
      });

$('#signup_2').click(function () {
   var formValid = $('#submit_form1').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
    //event.preventDefault();
    if(formValid != 0){
      return false;
    }
      else{
      var form = document.forms.namedItem("submit_form1"); 
      var formdata = new FormData(form);
    //console.log(form);
    
    $.ajax({
        url: '/signup/savebusinessinfo',
        data: formdata,
        type: $(form).attr('method'),
        processData :false,
        contentType:false,
        success: function (result)
        {
            var response = result;
            //nsole.log(response);
            if (response['status'] == true)
            {
                console.log(response);
				
               
				 $('[href="#tab2"]').parent('li').addClass('active');
				  $('#tab2').hide();
				 $('[href="#tab3"]').parent('li').addClass('active');
                $('.progress-bar').width('150%');
                $('#tab3').show();
            }
            else {
                console.log(response);
                alert('Invalid Try!!!');
            }
        }
    });
    return false;
}
});
});

$('#email').blur(function () {
    $('#email_populate').text($(this).val());
});
        </script> 
<script type="text/javascript">
            $(document).ready(function () {
                $('#check_email').click(function () {
                    var email = $('#email').val();
                    console.log(email);
                    var domain = email.substr(email.indexOf("@") + 1);
                    console.log(domain);
                    window.open("http://www." + domain, 'windowName', "height=500,width=800");
                });
            });
            $('#resend_email').click(function () {
                var form = $('#submit_form2');
                $.ajax({
                    url: 'legalentity/resend',
                    data: form.serialize(),
                    type: 'GET',
                    success: function (result)
                    {
                        var response = result;
                        console.log(response);
                        console.log(response['status']);
                        console.log(response['message']);
                        if (response['status'] == 1)
                        {
                            alert(response['message']);
                        } else {
                            alert('Unable to send mail.');
                        }
                    }
                });
            })
        </script> 
<script type="text/javascript">
    $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });

</script> 
<script type="text/javascript">
  document.getElementById("uploadBtn1").onchange = function () {
  document.getElementById("pan_file").value = getFile($(this).attr("id"));
};

  document.getElementById("uploadBtn2").onchange = function () {
  document.getElementById("upload_file").value = getFile($(this).attr("id"));
};

 function getFile(id){
    var str = '';
    var files = document.getElementById(id).files;
    for (var i = 0; i < files.length; i++){
        str += files[i].name;
    }
    return str;
}
</script>
</body>
</html>
