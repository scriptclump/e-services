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

<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/global/css/custom-ebutor.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/spinnerQueue.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/layout4/css/layout.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/layout4/css/themes/light.css') }}" rel="stylesheet" type="text/css" />
<link rel="icon" href="{{{ asset('img/favicon.png') }}}" type="image/x-icon" />
<link rel="shortcut icon" href="{{{ asset('img/favicon.png') }}}" type="image/x-icon" />

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
			<div class="menu-toggler sidebar-toggler">
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
                    
                    
					<!-- BEGIN NOTIFICATION DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
					<li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="fa fa-bell-o"></i>
						<span class="badge badge-success">
						7 </span>
						</a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3><span class="bold">12 pending</span> notifications</h3>
								<a href="extra_profile.html">view all</a>
							</li>
							<li>
								<ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
									<li>
										<a href="javascript:;">
										<span class="time">just now</span>
										<span class="details">
										<span class="label label-sm label-icon label-success">
										<i class="fa fa-plus"></i>
										</span>
										New user registered. </span>
										</a>
									</li>
									<li>
										<a href="javascript:;">
										<span class="time">3 mins</span>
										<span class="details">
										<span class="label label-sm label-icon label-danger">
										<i class="fa fa-bolt"></i>
										</span>
										Server #12 overloaded. </span>
										</a>
									</li>									
								</ul>
							</li>
						</ul>
					</li>
					<!-- END NOTIFICATION DROPDOWN -->
					<li class="separator hide">
					</li>
					<!-- BEGIN INBOX DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
					<li class="dropdown dropdown-extended dropdown-inbox dropdown-dark" id="header_inbox_bar">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="fa fa-envelope-o"></i>
						<span class="badge badge-danger">
						4 </span>
						</a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3>You have <span class="bold">7 New</span> Messages</h3>
								<a href="inbox.html">view all</a>
							</li>
							<li>
								<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
									<li>
										<a href="inbox.html?a=view">
										<span class="photo">
										<img src="{{url('/')}}/assets/admin/layout3/img/avatar2.jpg" class="img-circle" alt="">
										</span>
										<span class="subject">
										<span class="from">
										Lisa Wong </span>
										<span class="time">Just Now </span>
										</span>
										<span class="message">
										Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
										</a>
									</li>
									
								</ul>
							</li>
						</ul>
					</li>
					<!-- END INBOX DROPDOWN -->
					<li class="separator hide">
					</li>
					<!-- BEGIN TODO DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
					<li class="dropdown dropdown-extended dropdown-tasks dropdown-dark" id="header_task_bar">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="fa fa-calendar"></i>
						<span class="badge badge-primary">
						3 </span>
						</a>
						<ul class="dropdown-menu extended tasks">
							<li class="external">
								<h3>You have <span class="bold">12 pending</span> tasks</h3>
								<a href="page_todo.html">view all</a>
							</li>
							<li>
								<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
									<li>
										<a href="javascript:;">
										<span class="task">
										<span class="desc">New release v1.2 </span>
										<span class="percent">30%</span>
										</span>
										<span class="progress">
										<span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">40% Complete</span></span>
										</span>
										</a>
									</li>
									
								</ul>
							</li>
						</ul>
					</li>
					<!-- END TODO DROPDOWN -->
					<!-- BEGIN USER LOGIN DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-dark">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						@if(Session::has('fullname'))
						<span class="username username-hide-on-mobile">
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
							<li>
								@if(Session::get('legal_entity_id') != 0)
								<?php $id = md5(Session::get('legal_entity_id'));?>
								<a href="/legalentity/viewProfile/{{$id}}">
								@else
								<a href="">
								@endif
								<i class="icon-user"></i> My Profile </a>
							</li>
							
							
							<li>
								<a href="">
								<i class="icon-rocket"></i> My Tasks <span class="badge badge-success">
								7 </span>
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

@yield('sideview')

</div>
</div>	

	<div class="page-content-wrapper">
		<div class="page-content">
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
	
@yield('content')
			
		</div>
	</div>
	<!-- END CONTENT -->
	<!-- BEGIN QUICK SIDEBAR -->

	
	<!-- END QUICK SIDEBAR -->
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













<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<?php /*<script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>*/?>



<script src="{{ URL::asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>






<script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/spinnerQueue.js') }}" type="text/javascript"></script>
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
            });
        </script>       
    </body>
    </html>
