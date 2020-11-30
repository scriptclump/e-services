<html lang="en" class="no-js">
<!--
    <![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>@if(isset($title)) {{$title}} @else Welcome to EBUTOR @endif
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    
    <link href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />
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
    .register_bg {
        background: rgba(0, 0, 0, 0) url("../../assets/admin/layout/img/register_bg.jpg") no-repeat fixed center center / cover;
        display: block;
        width: 100%;
    }
    .login .content {
        background-color: #fff;
        margin: 0px auto 10px;
        overflow: hidden;
        padding: 0px 30px;
        position: relative;
        width: 360px;
		 box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
    }
    .login .content h3 {
        color: #4db3a5;
        font-size: 28px;
        font-weight: 400 !important;
        text-align: center;
    }
    .login .content .form-group {
        text-align: right;
    }
    .form-control {
        margin-bottom: 15px !important;
        border-radius: 0px !important;
    }
    .signupmargintop {
        margin-top: 75px;
    }
    .btn {
        border-radius: 0px;
    }
   .glyphicon-remove, .glyphicon-ok{top:10px !important;} 
   .glyphicon-refresh{margin-top:-14px !important;}
   .page-header.navbar .page-logo .logo-default {
    margin-top: 11px !important;
}
</style>
<style>
html, body {
	height: 100%;
}
.landingBlock {
	width: 100%;
	height: 600px; /* For at least Firefox */
	min-height: 95%;  
}
.landingBlock .wrapper{
	display: table;	 
	width: 100%;
	height: 100%;
}
.wrapper-inner {
	display: table-cell;
	vertical-align: top;
}
@media (min-width: 768px) {
.wrapper-inner {
	vertical-align: middle;
}
</style>

<body class="login register_bg">
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="#">
                   <img src="{{url('/')}}/assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
                </a>
            </div>
            <div class="page-top">
                <!-- BEGIN HEADER SEARCH BOX -->
                <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
                <!-- END HEADER SEARCH BOX -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <h4 class="signin-signup">Already registered?
                                <span>
                                    <a href="login">Sign in</a>
                                </span>
                            </h4>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <div class="landingBlock">
			<div class="wrapper">
			  <div class="wrapper-inner">       
				  <div class="inner cover">
				  
        <div class="content" id="form_call" style="margin-top:80px">
            <!-- BEGIN LOGIN FORM -->
            <form method="post" action="legalentity/save" id="submit_form" class="login-form" >
                <h3 class="form-title font-blue">Sign up </h3>
                <div class="alert alert-danger display-hide">
                    <button data-close="alert" class="close"></button>
                    <span> Enter any username and password. </span>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">First Name</label>
                    <input type="text" name="firstname" placeholder="First Name" class="form-control placeholder-no-fix">
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Last Name</label>
                    <input type="text" name="lastname" placeholder="Last Name" class="form-control placeholder-no-fix">
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <input type="text" id="email" name="email" placeholder="Email" class="form-control placeholder-no-fix">
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Phone Number</label>
                    <input type="text" name="phone_number" placeholder="Phone Number" class="form-control placeholder-no-fix">
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                </div>
                <div class="form-actions">
                    <button class="btn green-meadow" id="register-submit-btn" type="submit">Sign up</button>
                    <label class="mt-checkbox mt-checkbox-outline pull-right" style="margin-top: 8px;"> Already registered?
                        <a href="login">Sign in </a>
                        <span></span>
                    </label>
                </div>
            </form>
          </div>

            <!-- END LOGIN FORM -->
            <!-- Second LOGIN FORM START-->

				  
            <div class="content display-none" id="wellsignup" style="margin-top:60px;">
                <p style="margin-top:40px;">A confirmation mail has been sent to your mailbox</p>
                <p>
                    <a href="#" id="email_populate">sample@xyz.com</a>
                </p>
                <p>Please check your email box and continue your registration within 24 hours.</p>
                <a href="#" id="check_email" class="btn green-meadow">Go Check email</a>
                <p style="margin-top:10px; margin-bottom:40px;">Having problem receiving email ? <a href="javascript:void(0);" id="resend_email">click here</a> to resend</p>
            </div>
			
			</div>
			</div>
</div>

            <!-- Second LOGIN FORM END-->
        </div>
        <div class="page-footer signupfooter" style="bottom:0;position: fixed;">
            <div class="page-footer-inner"> 
                 &copy; <?php echo date( 'Y') ?>. Ebutor Distribution Pvt. Ltd. All rights reserved.
            </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
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
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
        {{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
        {{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
        {{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}

     
<script type="text/javascript">

 $(document).ready(function(){

    $('#submit_form').formValidation({
    //        live: 'disabled',
            framework: 'bootstrap',
            button: {
            selector: '#register-submit-btn',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
            fields: {
             email: {
                validators: {
                    notEmpty: {
                        message: 'Email is required'
                    },
                    remote: {
                        url: '/signup/checkUnique',
                        data: {email: $('[name="email"]').val()},
                        type: 'POST',
                        delay: 2000,
                        message: 'This email is already registered.'
                  },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: 'The value is not a valid email address'
                    }                 
               }
            },
            firstname: {
              validators: {
                 notEmpty: {
                        message: 'First Name is required'
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: 'Invalid characters for First Name'
                }
              }
            },
             lastname: {
              validators: {
                 notEmpty: {
                        message: 'Last Name is required'
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: 'Invalid characters for Last Name'
                }
              }
            },
            phone_number: {
              validators: {
                 notEmpty: {
                        message: 'Phone Number is required'
                },
                 regexp: {
                  regexp: /^\d{10}$/ ,
                        message: 'Please enter valid Mobile Number'
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

    $('#register-submit-btn').click(function (){
      var formValid = $('#submit_form').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
    //event.preventDefault();
    if(formValid != 0){
      return false;
    }
      else{
       var form = $('#submit_form').serialize();
       console.log(form);
       $.ajax({
        url: 'legalentity/save',
        data: form,
        type: 'POST',
        success: function (result){
            var response = JSON.parse(result);
            //console.log(response);
            if(response['status'] == true){
              $('#wellsignup').show();
              $('#form_call').hide();
            }
        }
      });
       //alert('Hello');
       }
    });
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
                var form = $('#submit_form');
                $.ajax({
                    url: 'legalentity/resend',
                    data: form.serialize(),
                    type: 'POST',
                    success: function (result)
                    {
                        var response = JSON.parse(result);
                        console.log(response);

                        console.log(response['status']);
                        console.log(response['message']);
                        if (response['status'] == 1)
                        {
                            alert(response['message']);
                        } else {
                            alert(response['message']);
                        }
                    }
                });
            });
          $('#email').blur(function () {
             $('#email_populate').text($(this).val());
          });
        </script>
<script type="text/javascript">
    $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });

</script> 

</body>
</html>
