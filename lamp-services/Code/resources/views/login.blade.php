<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>        
        <meta charset="utf-8"/>
        <title>Ebutor | Login</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="X-XSRF-TOKEN" content="{{ csrf_token() }}">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->

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
        <link rel="icon" href="{{{ asset('assets/admin/layout4/img/favicon.ico') }}}" type="image/x-icon" />
        <link rel="shortcut icon" href="{{{ asset('assets/admin/layout4/img/favicon.ico') }}}" type="image/x-icon" />
        <link href="{{ URL::asset('assets/admin/pages/css/login3.css') }}" rel="stylesheet" type="text/css" />

    </head>
    <!-- END HEAD -->
    <style>
        .register_bg {
            background: rgba(0, 0, 0, 0) url("../../assets/admin/layout/img/register_bg.jpg") no-repeat fixed center center / cover;
            display: block;
            width: 100%;
        }

        .form-group .has-feedback .has-error i{ margin-top: 10px !important; }

        .glyphicon-remove, .glyphicon-ok{top:1px !important;}
        .form-actions{padding:0px 0px 10px 0px !important;
                      margin: 0px !important;}
        .page-header.navbar .page-logo .logo-default {
            margin-top: 0px !important;
        }
        .forget-password {
            margin-top: 5px !important;
        }
        .btn{border-radius:0px !important;}
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
        <!-- BEGIN BODY -->
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

                    <!-- END PAGE TOP -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <div class="logo">
                <a href=""></a>
            </div>
            <div class="landingBlock">
                <div class="wrapper">
                    <div class="wrapper-inner">       
                        <div class="inner cover">
                            <div class="content">
                                <div class="alert alert-danger hide">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <span id="flash_message"></span>
                                </div>
                                <!-- BEGIN LOGIN FORM -->
                                @if(isset($errorMsg))
                                {{$errorMsg}}
                                @endif
                                <form id="form-signin" class="form-signin" method="POST" action="/login/checkAuth">
                                    {{ method_field('PUT') }}
                                    {{ csrf_field() }}
                                    <input type="hidden" name="X-XSRF-TOKEN" value="{{ csrf_token() }}" />
                                    <input type="hidden" name="redirect_url" value="{{ $redirect_url }}" />
                                    @if (Session::has('flash_message'))
                                    <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
                                    @endif 
                                        <h3 class="form-title font-blue">Login<!--<img src="../../assets/admin/layout/img/logo.png" alt=""/>--></h3>
                                    <div class="form-group">
                                        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                                        <div class="input-icon">
                                            <i class="fa fa-user"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" id="email" placeholder="Username" name="email"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                                        <div class="input-icon">
                                            <i class="fa fa-lock"></i>
                                            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="password" placeholder="Password" name="password"/>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <label class="checkbox">
                                            <input type="checkbox" name="remember" id="remember" value="1"/> Remember me </label>
                                        <button type="submit" id="login" class="btn green-haze pull-right">
                                            Login 
                                        </button>
                                    </div>

                                    <div class="forget-password">

                                        <a href="javascript:void(0);" id="forgot_password" data-toggle="modal" data-target="#forgotPasswordModal">Forgot your password? </a>

                                    </div>
                                    <div class="create-account">

                                        <a href="/register" id="register-btn">
                                            Create an account </a>

                                    </div>
                                </form>
                                <!-- END LOGIN FORM -->
                                <!-- BEGIN FORGOT PASSWORD FORM -->
                                <form class="forget-form" action="forgot" method="post">
                                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                    <h3>Forget Password ?</h3>
                                    <p>Enter your e-mail address below to reset your password.</p>
                                    <div class="form-group">
                                        <div class="input-icon">
                                            <i class="fa fa-envelope"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="button" id="back-btn" class="btn">
                                            <i class="m-icon-swapleft"></i> Back </button>
                                        <button type="submit" class="btn green-haze pull-right">
                                            Submit <i class="m-icon-swapright m-icon-white"></i>
                                        </button>
                                    </div>
                                </form>

                                <!-- END FORGOT PASSWORD FORM -->
                                <!-- BEGIN REGISTRATION FORM -->
                                <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="wizardCode" aria-hidden="true" style="display: none; margin-top:16%">
                                    <div class="modal-dialog wide">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h4 class="modal-title" id="wizardCode">Forgot Password</h4>
                                            </div>
                                            <div class="modal-body" >
                                                <div class="welcome">
                                                    <section class="error" style="color: #009900" id="section_message">
                                                    </section>  
                                                    {{Form::open(array('url'=> 'forgot', 'method'=>'post','class'=>'form-forgotpassword','id'=>'form-forgotpassword')) }} 
                                                    <section>                                
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="emailId" id="emailId" placeholder="Enter registered email address"/>
                                                        </div>
                                                    </section>  
                                                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                                    <section class="new-acc" style="text-align: center; float: none; padding-top: 10px;">
                                                        <button type="submit" id="reset_password" class="btn green-meadow">Reset Password</button>
                                                    </section>  
                                                    {{ Form::close() }}
                                                </div>

                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>
                                <!-- END REGISTRATION FORM -->
                            </div>



                        </div>     
                    </div>
                </div> 
            </div>






            <div class="page-footer signupfooter" style="position:fixed; bottom:0px;">
                <div class="page-footer-inner"> 
                    &copy; <?php echo date('Y') ?>. Ebutor Distribution Pvt. Ltd. All rights reserved.
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>

            <script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/jquery.cokie.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/admin/layout/scripts/layout.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/admin/layout/scripts/demo.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/admin/pages/scripts/login.js') }}" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js" type="text/javascript"></script>        
            <script>
    jQuery(document).ready(function () {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Login.init();
        Demo.init();
    });
    $.ajaxSetup({
        headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
    });
            </script>
            <!-- END JAVASCRIPTS -->    
            <script>
                $(function () {
                    if (window.history && window.history.pushState) {
                        window.history.pushState('', null, './');
                        $(window).on('popstate', function () {
                            // alert('Back button was pressed.');
                            document.location.href = '/login';
                        });
                    }
                });
                $(function () {
                    $('.welcome').addClass('animated bounceIn');
                })
                $(document).ready(function () {
                    $('#forgot_password').click(function () {
                        $('#reset_password').attr('disabled', false);
                    });

                    $('#reset_password').on('click', function () {
                        if ($('#form-forgotpassword').data('bootstrapValidator').isValid())
                        {
                            $('#reset_password').attr('disabled', true);
                            $.post('forgot', {emailId: $('[name="emailId"]').val(), _token: $('[name="_token"]').val()}, function (response) {
                                console.log(response);
                                $(".error").html(response);
                                $('#section_message').show();
                                $('#reset_password').attr('disabled', true);
                            });
                        }
                    });
                    $('#forgotPasswordModal').on('hide.bs.modal', function () {
                        console.log('resetForm');
                        $('#form-forgotpassword').bootstrapValidator('resetForm', true);
                        $('#form-forgotpassword')[0].reset();
                        $('#section_message').hide();
                    });


                });
            </script>
            @if(Session::has('errorMsg'))    
            <button class="btn" id="errorMsgBtn" data-toggle="modal" data-target="#wizardCodeModal"style="display: none" >
                Add New Role
            </button>  
            <!-- /tile header -->
            <div class="modal fade" id="wizardCodeModal" tabindex="-1" role="dialog" aria-labelledby="wizardCode" aria-hidden="true" style="display: none;">
                <div class="modal-dialog wide">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="wizardCode">Error Message</h4>
                        </div>
                        <div class="modal-body" style="color: #ff0000;">

                            <?PHP echo Session::get('errorMsg'); ?>
                            <?PHP Session::forget('errorMsg'); ?>  
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->  

            <script>
                $(document).ready(function () {
                    $("#errorMsgBtn").click();
                });
            </script>
            @endif

            <script type="text/javascript">
                $(document).ready(function () {
                    $('#form-signin').bootstrapValidator({
            //        live: 'disabled',
                        message: 'This value is not valid',
                        feedbackIcons: {
            //                valid: 'glyphicon glyphicon-ok',
            //                invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            email: {
                                validators: {
                                    notEmpty: {
                                        message: 'The username is required and cannot be empty'
                                    },
                                    emailAddress: {
                                        message: 'The input is not a valid email address'
                                    }
                                }
                            },
                            password: {
                                validators: {
                                    notEmpty: {
                                        message: 'The password is required and cannot be empty'
                                    },
                                    stringLength: {
                                        min: 5,
                                        max: 14,
                                        message: 'The password must be more than 4 and less than 14 characters long'
                                    },
                                    different: {
                                        field: 'username',
                                        message: 'The password cannot be the same as username'
                                    }
                                }
                            }
                        }
                    }).on('success.form.bv', function (event) {
                        event.preventDefault();
                        var $form = $("#form-signin");
                        var datastring = $form.serialize();
                        console.log(datastring);
                        $.ajax({
                            url: $form.attr('action'),
                            data: datastring,
                            type: $form.attr('method'),
                            success: function (response) {
                                var data = $.parseJSON(response);
                                var redirectUrl = $('[name="redirect_url"]').val();
                                if (data.status)
                                {
                                    if(redirectUrl == '/')
                                    {
                                        location.reload(redirectUrl);
                                    }else{
                                        window.location.href = '/' + redirectUrl;
                                    }
                                } else {
                                    $('#flash_message').text(data.message).show();
                                    $('div.alert').show();
                                    $('div.alert').removeClass('hide');
                                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                                    $('#login').removeAttr('disabled');
                                }
                            }
                        });
                    });
                });
            </script>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#form-forgotpassword').bootstrapValidator({
                        message: 'This value is not valid',
                        feedbackIcons: {
            //                valid: 'glyphicon glyphicon-ok',
            //                invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            emailId: {
                                validators: {
                                    notEmpty: {
                                        message: 'The emailId is required and cannot be empty'
                                    },
                                    emailAddress: {
                                        message: 'The input is not a valid email address'
                                    },
                                    remote: {
                                        headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
                                        url: '/resetPassword/checkEmail',
                                        data: {email: $('[name="emailId"]').val()},
                                        type: 'POST',
                                        delay: 20,
                                        message: 'Your user account isn\'t active, please contact Admin.'
                                    }

                                }
                            }
                        }
                    });
                });
                $(document).ready(function () {
                    window.setTimeout(function () {
                        $(".alert").hide();
                    }, 30000);
                });

                $('#remember').click(function(){
                    var mail = $('#email').attr("value");
                    var password = $('#password').attr("value");
                   //store into cookies
                    if ($('#remember').attr('checked')) {
                        $.cookie('mail', mail, { expires: 7 });
                        $.cookie('password', password, { expires: 7 });
                        $.cookie('remember', true, { expires: 7 });
                    } else {
                        // reset cookies
                        $.cookie('mail', null);
                        $.cookie('password', null);
                        $.cookie('remember', null);
                    }
                    
                });
                //read from cookies
                var remember = $.cookie('remember');
                if ( remember == 'true' ) {
                    var mail = $.cookie('mail');
                    var password = $.cookie('password');
                    // autofill the fields
                    $('#email').attr("value", mail);
                    $('#password').attr("value", password);
                }
            </script> 
        </body>
        <!-- END BODY -->
    </html>