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

<style type="text/css">
     .login .content {
        background-color: #fff;
        margin: 240px auto 10px;
        overflow: hidden;
        padding: 30px 30px 0px 30px;
        position: relative;
        width: 400px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
    }
    .login .content h3 {
        color: #4db3a5;
        font-size: 23px !important;
        font-weight: 600 !important;
        text-align: center;
        margin-top: 0px !important;
        margin-bottom:15px;
    }
    .login .content .form-group {
        text-align: right;
    }
     .form-control {
        margin-bottom: 15px !important;
        border-radius: 0px !important;
    }
    .register_bg {
        background: rgba(0, 0, 0, 0) url("../../assets/admin/layout/img/register_bg.jpg") no-repeat fixed center center / cover;
        display: block;
        width: 100%;
    }
    .page-header.navbar .page-logo .logo-default {
    margin-top: 11px !important;
}
    .has-feedback label~.form-control-feedback {top: 11px !important}
    .help-block{text-align: left !important;}

    .glyphicon-remove{
        color:#a94442!important;
    }
    .glyphicon-ok{
        color:#3c763d!important;   
    }
    .error_validation{
        color:#a94442!important;
    }
</style>
<body class="login register_bg">
     <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner">
            <div class="page-logo">
                <a href="#">
                 <img src="{{url('/')}}/assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
                </a>
            </div>
            <div class="page-top">
                <div class="top-menu">
                    <h4 class="signin-signup">
                                <span>
                                    <a href="/login">Signin</a>
                                </span>
                            </h4>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <div class="signupmargintop ">
        <div class="content" id="form_call" style="margin-top:200px">
		{!! Form::open(array('url'=> 'passwordreset', 'method'=>'post','class'=>'form-resetpassword','id'=>'submit_form')) !!}

		<h3 class="form-title font-blue">Password Reset </h3>	 
                 <div class="form-group" id="reset_div">
                    <label class="control-label visible-ie8 visible-ie9 new_password">Reset Password: </label>
                       
                            <input type="password" class="form-control" id="resetpswd" name="resetpswd" placeholder="Reset Password" onkeyup="checkResetPassword()" />
                            <i id="reset_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="resetpswd" style="display: block;"></i>
                            <p id="reset_pass_msg" style="display: none;color: #a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p>
                </div>
                <div class="form-group" id="confirm_div">
                    <label class="control-label visible-ie8 visible-ie9 confirm_password">Confirm Password: </label> 
                                                
                            <input type="password" class="form-control" onkeyup="checkConfirmPassword()" name="confirmpswd" id="confirmpswd" placeholder="Confirm Password"/>
                            <i id="confirm_pass_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="confirmpswd" style="display: block;"></i>
                            <p id="confirm_pass_msg" style="display: none;color: #a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p> 
                        
                </div>
                        
                <input type="hidden" class="form-control" name="user_id" value="{{$user->user_id}}"/>
                
                <div class="form-actions" style="text-align: center; padding-bottom:0px !important;">                    
                    <button class="btn green-meadow" id="submit_password" type="submit">Submit</button>                  
                    
                </div>
            </div>
        </div>

    {!! Form::close() !!}
    <div class="page-footer signupfooter" style="bottom:0;position: absolute;">
            <div class="page-footer-inner"> <!--Copyright &copy;
                <?php echo date( 'Y') ?> eSealCentral. All rights reserved.-->
                 &copy; 2016. Sunera eSeal India Pvt. Ltd. All rights reserved.
            </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
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
        {{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
        {{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
        {{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}

     
<script type="text/javascript">

$('#submit_form').formValidation({
    //        live: 'disabled',
            framework: 'bootstrap',
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {

            resetpswd: {
                validators: {
                    notEmpty: {
                        message: 'Password is required and can\'t be empty'
                    },
                    stringLength: {
                        min: 5,
                        max: 14,
                        message: 'Password must be 4 to 14 characters in length.'
                    }
                }
            },
            confirmpswd: {
                validators: {
                    notEmpty: {
                        message: 'Confirm password is required and can\'t be empty'
                    },
                     identical: {
                      field: 'resetpswd',
                      message: 'Password doesn\'t match.'
                    }
                }
            }

        }
    });
    function checkResetPassword(){
        var reset_password = $('#resetpswd').val();
        if(reset_password == 'ebutor@123' || reset_password == 'Ebutor@123'){
            $('#reset_pass_msg').css('display','block');
            $('#submit_password').attr('disabled',true);
            $('.new_password').addClass('error_validation');
            $('#reset_wrong').css('display','block');
            $('[data-fv-icon-for="resetpswd"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="resetpswd"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#reset_div').addClass('has-error');
            $('#reset_div').removeClass('has-success');
        }else{
            $('#reset_pass_msg').css('display','none');
            $('#reset_wrong').css('display','none');
            $('.new_password').removeClass('error_validation');
        }
    }
    function checkConfirmPassword(){
        var confirm_password = $('#confirmpswd').val();
        if(confirm_password == 'ebutor@123' || confirm_password == 'Ebutor@123'){
            $('#confirm_pass_msg').css('display','block');
            $('#submit_password').attr('disabled',true);
            $('.confirm_password').addClass('error_validation');
            $('#confirm_pass_wrong').css('display','block');
            $('[data-fv-icon-for="confirmpswd"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="confirmpswd"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#confirm_div').addClass('has-error');
            $('#confirm_div').removeClass('has-success');

        }else{
            $('#confirm_pass_msg').css('display','none');
            $('#confirm_pass_wrong').css('display','none');
            $('.confirm_password').removeClass('error_validation');
        }
    }

     $('#confirmpswd').keyup(function(){
        checkConfirmPassword();
        checkResetPassword();
    });
    $('#resetpswd').keyup(function(){
        checkResetPassword();
        checkConfirmPassword();
    });
</script>	
</body>
