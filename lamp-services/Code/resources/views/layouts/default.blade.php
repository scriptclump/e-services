<!DOCTYPE html>
<html lang="en" class="no-js"><!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title> @if(isset($title))
        {{$title}}
    @else
        Welcome to EBUTOR
    @endif</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<script async defer src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyBXXQKDsKmVWCzUM57aKZonac-gAHaKyfc&callback&libraries=geometry" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-ebutor.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />
<!--<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />-->
<link href="{{ URL::asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/spinnerQueue.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/layout4/css/layout.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/admin/layout4/css/themes/light.css') }}" rel="stylesheet" type="text/css" />
<link rel="icon" href="{{{ asset('assets/admin/layout4/img/favicon.ico') }}}" type="image/x-icon" />
<link rel="shortcut icon" href="{{{ asset('assets/admin/layout4/img/favicon.ico') }}}" type="image/x-icon" />

<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
 
</head>
<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-sidebar-closed">

<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="{{url('/')}}">
            <img src="{{url('/')}}/assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
            <img src="{{url('/')}}/assets/admin/layout/img/small-logo.png" alt="logo" class="small-logo"/>
            </a>
            <div class="menu-toggler sidebar-toggler" id="add_Search">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        @if(Session::has('superadmin') && Session::get('userId') != 1)
        <input type="button"  id="switch_to_admin" class="btn green-meadow" value = "Switch to Admin" />
        @endif
        <?php $otherUser = Session::get('otherUser'); ?>
        @if(Session::has('otherUser') && Session::get('userId') != 1 && Session::get('userId') != $otherUser['userId'])
        <input type="button"  id="switch_to_parent" class="btn green-meadow" value = "Switch to Parent User" />
        @endif
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        @if(Session::get('userId') != Session::get('parentuser_id'))
        <input type="button" style="padding: 4px 10px;" id="back_to_admin" class="btn green-meadow" onclick="BacktoAdmin()" value = "Back To Admin" />
        @endif
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->
        
        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->

                    <div class="page-top">
                    <!-- BEGIN HEADER SEARCH BOX -->
                    <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->

                    <!-- END HEADER SEARCH BOX -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <li class="separator hide">
                            </li>
                            <li  href="" class="dropdown-toggle"><span style="color:#7FB0DA" >&#9742;Call Ebutor: </span>
                                <i style="margin-bottom: 300px;color:#7FB0DA;"></i>
                                040-66006442
                            </li>
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <?php
                                    use App\Central\Repositories\RoleRepo;
                                    $wiki_link = "";
                                    $wiki_name = "";
                                    $wiki_desc = "";
                                    $this->_roleRepo = new RoleRepo();
                                    $page_link = Request::path();
                                    $wiki_data = $this->_roleRepo->getWikiDataByLink($page_link);
                                    if(count($wiki_data)){
                                        $wiki_link = $wiki_data->wiki_url;
                                        $wiki_desc = $wiki_data->wiki_description;
                                    }

                                ?>
                                @if($wiki_link != "" || $wiki_desc !="")
                                    <a href="{{$wiki_link}}" class="dropdown-toggle" target="_blank" title="{{$wiki_desc}}">
                                        <i class="fa fa-question"></i>
                                    </a>
                                @endif
                                

                            </li>
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">

                                <a href="/approvalworkflow/approvalticket" class="dropdown-toggle">
                                    <i class="fa fa-pencil-square-o"></i>

                                    <!-- <span class="badge badge-primary" id="appr_ticket_count"> 0 </span> -->
                                </a>
                                

                            </li>

                            <!-- BEGIN NOTIFICATION DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-bell-o"></i>
                                    <!-- <span class="badge badge-success" id="notification_total_count">0</span> -->                                    
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="external">
                                        <h3><span class="bold" id="notification_count">0 pending</span> notifications</h3>
                                        <a href="javascript:void(0);" onclick="closeNotification('ALL')">Clear All</a>
                                    </li>
                                    <li>
                                        <ul class="dropdown-menu-list scroller notification-list" style=" border:2px solid #5c9dd5; height: 250px;" data-handle-color="#637283">
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <!-- END NOTIFICATION DROPDOWN -->
                            <li class="separator hide">
                            </li>
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user dropdown-dark">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    @if(Session::has('fullname'))
                                    <span id="header_username" class="username username-hide-on-mobile">
                                        {{Session::get('fullname')}}</span>
                                    @endif
                                    <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                                    @if(Session::has('userLogoPath'))
                                    <img id="profile_pic_default" class="img-circle" src="{{ URL::asset(Session::get('userLogoPath')) }}" alt /> 
                                    @else 
                                    <img class="img-circle" src="{{ URL::asset('/img/avatar5.png') }}" alt /> 
                                    @endif

                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    @if(Session::get('profileAccess') == 1)
                                    <li>
                                            @if(Session::get('legal_entity_id') != 0)
                                            <?php $id = md5(Session::get('userId')); ?>
                                            <a href="/myprofile/{{$id}}">
                                                @else
                                                <a href="">
                                                    @endif
                                                    <i class="icon-user"></i> My Profile 
                                                </a>
                                    </li>
                                    @endif
                                    @if(Session::get('attendenceAccess') == 1)
                                   
                                    <li>
                                        <a href="/myattendance">
                                            <i class="icon-user"></i> My Attendance
                                        </a>
                                    </li>

                                    @endif
                                    @if(Session::get('changepassword') == 1)
                                    <li>
                                        <a data-toggle="modal" data-target="#resetpasswordModal" id="clickChangePassword">
                                            <i class="icon-user"></i> Change Password
                                        </a>
                                    </li>

                                    @endif
                                    <li>
                                        <a href="/notification/tasks">
                                            <i class="icon-rocket"></i> My Tasks <span class="badge badge-success" id="tasks_count">0</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/logout">
                                            <i class="icon-key"></i> Log Out </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- END USER LOGIN DROPDOWN -->
                            <!-- BEGIN USER LOGIN DROPDOWN -->


                            <!-- END USER LOGIN DROPDOWN -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>

<!-- BEGIN HEADER -->

<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">

<div class="page-sidebar-wrapper">
<div class="page-sidebar navbar-collapse collapse">
    <div class="search_view search_class" id="search_view">
        <form>
<!--             <label for="inputdefault">Search</label>
 -->            <input class="form-control" id="feature_menu" autocomplete="off" placeholder="Search here.." type="text" style="border-radius: 20px !important;margin-top: 3px;">
        </form>
        <ul class="sub-menu" id="search_result" style="margin-top:3px"></ul>

    </div>
    <div class="side_view" id="side_view">
        @yield('sideview')
    </div>    
</div>
</div>  

    <div class="page-content-wrapper">
        <div class="page-content ">
            <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>
                        <div class="modal-body">
                             Widget settings form goes here
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn blue">Save changes</button>
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
@if(isset($breadCrumbs))
<ul class="page-breadcrumb breadcrumb">
<?php echo $breadCrumbs ?>
</ul>
<ul class="page-breadcrumb breadcrumb" id="success" style="display:none;">
</ul>           
@endif
@yield('style')
<style type="text/css">
    .spinnerQueue{position:absolute !important; z-index: 999999 !important;}    
</style>
@yield('content')

<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
        </div>
    </div>
    <!-- END CONTENT -->    
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
          &copy; <?php echo date( 'Y') ?>. Ebutor Distribution Pvt. Ltd. All rights reserved
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>


     <div class="modal fade" id="resetpasswordModal" tabindex="-1" role="dialog" aria-labelledby="wizardCode" aria-hidden="true" style="display: none;">
                <div class="modal-dialog wide">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="changepasswordwindowclose">×</button>
                            <h3 class="modal-title" id="wizardCode">Change Password</h3>
                        </div>
                        <div class="modal-body" >
                            <div class="welcome">
                              <form id="passwordFormReset">
                                     <div class="row form-group">
                                       <label class="control-label col-md-4">Old Password:</label>
                                       <div class="col-md-8">
                                         <input type="password" name="resetoldpassword" class="form-control"></div>
                                      </div>
                                      <div class="row form-group " id="reset_div">
                                       <label class="control-label col-md-4 new_password">New Password:</label>
                                       <div class="col-md-8">
                                            <input type="password" name="resetnewpassword" onkeyup="checkNewPassword()" id="resetnewpassword" class="form-control">
                                            <i id="reset_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="resetnewpassword" style="display: block;"></i>
                                           <p id="new_pass_msg" style="display: none;color:#a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p>
                                       </div>
                                      </div>
                                      <div class="row form-group" id="confirm_div">
                                       <label class="control-label col-md-4 confirm_password">Confirm Password:</label>
                                       <div class="col-md-8">
                                         <input type="password" name="userpasswordconfirm" id="userpasswordconfirm" onkeyup="checkConfirmPassword()" class="form-control">
                                         <i id="confirm_pass_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="userpasswordconfirm" style="display: block;"></i>
                                         <p id="confirm_pass_msg" style="display: none;color: #a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p>
                                       </div>
                                      </div>

                                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                <input type="hidden" id = "userId_update_password" name="userId_update_password" value="{{Session::get('userId')}}" />   
                              
                                <div class="margiv-top-10" align="center">          
                                    <input type="submit"  id="reset_password_button" class="btn green-meadow" value = "Change Password" />
                                </div>
                                </form>    
                            </div>
                            
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
    </div>
<style type="text/css">
    .glyphicon-remove{
        margin-right: 8px;
        color:#a94442!important;
    }
    .glyphicon-ok{
        margin-right: 8px;
        color:#3c763d!important;   
    }
    .glyphicon-refresh{
        margin-right: 8px;
        margin-top: 5px;
    }
    .search_class{
        display: none
    }
    .search_result_item{
        height:45px;
    }
    .error_validation{
        color:#a94442!important;
    }
    

</style>

<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>


<script src="{{ URL::asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/spinnerQueue.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.min.js') }}" type="text/javascript"></script>

@include('includes.validators')
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
        <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-113013308-3"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'UA-113013308-3');
            </script>


        @yield('script')
        @yield('userscript')
        
        
        
        
        
        <script>
            jQuery(document).ready(function () {
                Metronic.init(); // init metronic core componets
                Layout.init(); // init layout
                Demo.init(); // init demo features               
                //QuickSidebar.init(); // init quick sidebar
               // Index.init(); // init index page
                //Tasks.initDashboardWidget(); // init tash dashboard widget 
                //ComponentsFormTools.init();
                //ComponentsDropdowns.init();
                $(document).ajaxStart(function(){
                    if($(".ui-igloadingmsg").length>0) { 
                        $(".ui-igloadingmsg").css("visibility", 'hidden');
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
                    }                        
                });                    
                $(document).ajaxSuccess(function(){
                    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                });
                $(document).ajaxError(function(){
                   $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                });
                $(document).ajaxComplete(function(){
                    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                });
                $(document).ajaxStop(function(){
                    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                }); 
                $('#feature_menu').val('');
                //getNotifications();
                //getTasks();
                //for ticket notification
                //getApprovalTicket();

            //setInterval(function(){ getNotifications(); }, 60000);
            });
            $('#add_Search').click(function(){
                var searchcls = document.getElementsByClassName("page-sidebar-menu-closed");
                $('#search_result').html('');
                $('#side_view').css('display','block');
                $('#feature_menu').val('');
                console.log(searchcls.length);
                if(searchcls.length >0){
                    $("#search_view").removeClass("search_class");
                }else{
                    $("#search_view").addClass("search_class");
                }

            });
            $('#feature_menu').keyup(function(){
                var feature_search = $('#feature_menu').val();
                if(feature_search!=''){
                    var dataString = { 'text': feature_search, '_token' : $('#csrf_token').val() };
                    $.ajax({
                        url:'/cpmanager/searchfeatures',
                        type:'post',
                        data:dataString,
                        success:function(response){
                            $("#search_result").removeClass("search_class");
                            $('#search_result').html('');
                            $('#side_view').css('display','none');
                            $('#search_result').append(response.data);
                        }
                    });
                }else{
                    $('#side_view').css('display','block');
                    $("#search_result").addClass("search_class");
                }
                
            });
            function getNotifications()
            {
                $.ajax({
                    beforeSend: function(){
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                    },
                    url: '/notification/getmynotifications/1',
                    type: 'GET',
                    success: function (response) {
                        var responseData = $.parseJSON(response);
                        if(responseData != '')
                        {
                            var totalCount = 0;
                            if(responseData.count > 99)
                            {
                                totalCount = '99+';
                            }else{
                                totalCount = responseData.count;
                            }
                            $('#notification_total_count').text(totalCount);
                            $('#notification_count').text(responseData.count+' pending');
                            if(responseData.data.length > 0)
                            {
                                $('.notification-list').empty();
                                $.each(responseData.data, function(key, value){
                                    if(value.link != '')
                                    {
                                        $('.notification-list').append('<li><a href="'+value.link+'">\n\
                            <span class="time">'+value.time_delay+'</span>\n\
                            <span class="details">\n\
'+value.message+'</span></a><button type="button" class="notification_close" onclick="closeNotification('+"'"+value._id+"'"+')">x</button></li>');
                                    }else{
                                        $('.notification-list').append('<li><a href="javascript:void(0);">\n\
                            <span class="time">'+value.time_delay+'</span>\n\
                            <span class="details">\n\
'+value.message+'</span></a><button type="button" class="notification_close" onclick="closeNotification('+"'"+value._id+"'"+')">x</button></li>');
                                    }
                                    
                                });
                            }else{
                                $('.notification-list').empty();
                            }
                        }
                    }
                });
            }
            
            function closeNotification(id)
            {
                dataString = { '_id': id, '_token' : $('#csrf_token').val() };
                $.ajax({
                    url: '/notification/changestatus',
                    data: dataString,
                    type: 'POST',
                    success: function (response) {
                        var responseData = $.parseJSON(response);
                        console.log('response');
                        console.log(response);
                        if(responseData != '')
                        {                            
                            if(!responseData.status)
                            {
                                closeNotification(id);
                            }else{
                                getNotifications();
                            }
                        }
                    }
                });
            }
            
            function getTasks()
            {
                $.ajax({
                    beforeSend: function(){
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                    },
                    url: '/notification/getmynotifications/2',
                    type: 'GET',
                    success: function (response) {
                        var responseData = $.parseJSON(response);
                        if(responseData != '')
                        {
                            $('#tasks_count').text(responseData.count);                            
                        }
                    }
                });
            }

            

            function getApprovalTicket()
            {
                $.ajax({
                    beforeSend: function(){
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                    },
                    url: '/approvalworkflow/getuserticketcount',
                    type: 'GET',
                    success: function (response) {
                        $('#appr_ticket_count').text(response);
                        if(response==0){
                            $('#appr_ticket_count').removeClass("badge badge-danger");
                            $('#appr_ticket_count').addClass("badge badge-primary");
                        }else{
                            $('#appr_ticket_count').removeClass("badge badge-primary");
                            $('#appr_ticket_count').addClass("badge badge-danger");
                        }
                        
                    }
                });
            }


        </script> 

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});

 $('#resetpasswordModal').on('hide.bs.modal',function(){
    console.log('resetForm');
    $('#passwordFormReset').bootstrapValidator('resetForm', true);
    
    $('#passwordFormReset')[0].reset(); 
  });

$('#passwordFormReset').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#reset_password_button',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          resetoldpassword: {
              validators: {
                  notEmpty: {
                        message: 'Please enter password'
                    },
                  remote: {
                        //token : $("#csrf-token").val(),
                        headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                        url: '/employee/passwordReset',
                        data: {resetoldpassword: $('[name="resetoldpassword"]').val(),empid:$('[name="userId_update_password"]').val()},
                        type: 'POST',
                        delay: 2000,
                        message: 'Incorrect Password. Please try again..'
                  }
              }
          },
          resetnewpassword: {
              validators: {
                  notEmpty: {
                        message: 'Password cannot be empty'
                    },
                  stringLength: {
                        min: 5,
                        max: 14,
                        message: 'Password must be 4 to 14 characters in length.'
                    }
                }
            },
          userpasswordconfirm: {
              validators: {
                  notEmpty: {
                        message: 'Confirm password cannot be empty'
                    },
                    identical: {
                      field: 'resetnewpassword',
                      message: 'Password doesn\'t match.'
                    }
                }
            },
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
           console.log('here in success');
          var token  = $("#csrf-token").val();
          $('#reset_password_button').attr('disabled',false);
          $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/employee/accessSpecificChangePassword',
            data: $('#passwordFormReset').serialize(),
            type: 'POST',
            success: function (result)
            {
                var response = result;
                if(response.status == true)
                {
                    alert(response.message);
                    $('#resetpasswordModal').modal('hide');
                } else{
                    alert(response.message);
                    $('#resetpasswordModal').modal('hide');
                }
            }
          });
      });

    function BacktoAdmin(){
       var dataString = {'_token' : $('#csrf_token').val() };
        $.ajax({
            url: '/users/backtoadmin',
            data:dataString,
            type: 'POST',
            success: function (response) {
                $("#alertStatus").attr("class","alert alert-success").text("Logged in with admin").show().delay(3000).fadeOut(350);
                    location.reload();
            }
        });
    }

    $(function(){
        var token=$('#csrf-token').val();
        $.ajax({
        type:'POST',
        headers: {'X-CSRF-TOKEN': token},
        url:'/users/userpassword',
        success: function(response){
            if(response == 1){
                $('#resetpasswordModal').modal({
                    backdrop: 'static',
                    keyboard: false});
                $("#resetpasswordModal").modal("show");
                $("#changepasswordwindowclose").hide();
            }else{
                $("#changepasswordwindowclose").show();
            }
        }

        });
    });

    function checkNewPassword(){
        var new_password = $('#resetnewpassword').val();
        if(new_password == 'ebutor@123' || new_password == 'Ebutor@123'){
            $('#new_pass_msg').css('display','block');
            $('#reset_password_button').attr('disabled',true);
            $('.new_password').addClass('error_validation');
            $('#reset_wrong').css('display','block');
            $('[data-fv-icon-for="resetnewpassword"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="resetnewpassword"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#reset_div').addClass('has-error');
            $('#reset_div').removeClass('has-success');
        }else{
            $('#new_pass_msg').css('display','none');
            $('#reset_wrong').css('display','none');
            $('.new_password').removeClass('error_validation');
        }
    }

    function checkConfirmPassword(){
        var confirm_password = $('#userpasswordconfirm').val();
        if(confirm_password == 'ebutor@123' || confirm_password == 'Ebutor@123'){
            $('#confirm_pass_msg').css('display','block');
            $('#reset_password_button').attr('disabled',true);
            $('.confirm_password').addClass('error_validation');
            $('#confirm_pass_wrong').css('display','block');
            $('[data-fv-icon-for="userpasswordconfirm"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="userpasswordconfirm"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#confirm_div').addClass('has-error');
            $('#confirm_div').removeClass('has-success');
        }else{
            $('#confirm_pass_msg').css('display','none');
            $('#confirm_pass_wrong').css('display','none');
            $('.confirm_password').removeClass('error_validation');
        }
    }
    $('#userpasswordconfirm').keyup(function(){
        checkConfirmPassword();
        checkNewPassword();
    });
    $('#resetnewpassword').keyup(function(){
        checkNewPassword();
        checkConfirmPassword();
    });

    </script>

    </body>
</html>
