@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Track Executive'); ?>

<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/routingadmin" class="bread-color">Route</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><span class="bread-color">Track Executive</span></li>
		</ul>
	</div>
</div>
<div ng-app="mapRouting" ng-controller="viewTrackExCtrl">
	<div class="mainTabs">
		<ul class="nav nav-tabs">
		    <li class="active"><a data-toggle="tab" href="#home">Last Captured</a></li>
		    <li><a data-toggle="tab" href="#menu1">History Captured</a></li>
		    <li><a data-toggle="tab" href="#historyReport">History Reports</a></li>
		</ul>
		<div class="tab-content">
			<!--Last captured tab-->
		    <div id="home" class="tab-pane fade in active">
	      		<div class="row" style="padding: 15px;">
	      			<div class="col-md-3">
	      				<select class="form-control select2me" ng-model="selectedLastHUB" ng-change="getHubDetails(selectedLastHUB)">
							<option selected value="">---- Select Hub ----</option>
							<option value="all">All</option>
							<option ng-repeat="(key,value) in hubListData | orderBy:value" value="<%key%>"><%value%></option>
						</select>
	      			</div>
	      			<div class="col-md-3">
	      				<select class="form-control select2me" id="lastCaptureDE" ng-disabled="selectedLastHUB == 'all'" ng-model="selectedLastFF" ng-click="getLastExTrack(selectedLastFF)">
							<option selected value="">---- Select Delivery Executive ----</option>
							<option ng-repeat="(key,value) in selectedDEList | orderBy:'deValue'" value="<%value.deId%>"><%value.deValue%></option>
						</select>
	      			</div>
		     	</div>
		     	<div class="row">
					<div class="col-md-12">
						<div id="map_wrapper">
							<div id="map_canvas" class="mapping"></div>
						</div>
					</div>
				</div>
		    </div>
			<!--History captured tab-->
			<!-- ng-if="value != 'all'" to disable all option-->
		    <div id="menu1" class="tab-pane fade">
		      		<div class="row" style="padding: 15px;">
		      			<div class="col-md-3">
		      				<select class="form-control select2me" ng-model="selectedHistHUB" ng-change="getHubDetails(selectedHistHUB)">
								<option selected value="">---- Select Hub ----</option>
								<option ng-repeat="(key,value) in hubListData | orderBy:'name'" value="<%key%>"><%value%></option>
							</select>
		      			</div>
		      			<div class="col-md-3">
		      				<select class="form-control select2me" id="historyCaptureDe" ng-model="selectedFF">
								<option selected value="">---- Select Delivery Executive ----</option>
								<option ng-repeat="(key,value) in selectedDEList | orderBy:'deValue'" ng-if="value.deValue != 'all'" value="<%value.deId%>"><%value.deValue%></option>
							</select>
		      			</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class='input-group date' id='datetimepicker6' >
									<input type='text' class="form-control" style="border: 1px solid #e5e5e5;" placeholder="Date" ng-model="date" id="reqDate"/ >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-1">
							<button type="submit" value="Submit"  ng-click="getTrackExecutive(selectedFF,selectedHistHUB)"  class="btn trackButtons">Submit</button>
						</div>
						<div class="col-md-2">
							<button class="btn trackButtons"  id="create_pdf">Generate PDF</button>
						</div>
					</div>
				
				<form id="deHistoryScreen" class="ui form" style="width: 1300px;">	
					
					<div ng-click="showDEDetailsToggle()" ng-if="distance" class="showDeBut">
						Show Delivery Details
					</div>
					<div ng-show="showdeDetails" ng-if="distance">
						<div class="row" style="margin-top: 15px;margin-bottom: 15px;padding-left: 15px;">
							<div class="col-md-6">
								<span style="font-size: 18px; color: #aaa; font-weight: bold;">Trip Detials</span>
							</div>
							<div class="col-md-6">
								<span style="font-size: 18px; color: #aaa; font-weight: bold;padding-left: 15px;">Outlet Status</span>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<span style="font-size: 16px;color: #aaa;padding-left: 15px;">Executive Name </span>
								<span style="font-size: 18px;color: #7373a9;padding-left: 10px;">: <%executiveDispName%></span><br/>
								<span style="font-size: 16px;color: #aaa;padding-left: 15px;">Orders Attempted </span>
								<span style="font-size: 18px;color: #7373a9;">: <%orderCodeDECount%></span>
							</div>
							<div class="col-md-6">
								<div class="item">
								    <img src="/img/google_markers/marker_black.png"/>
								    <span class="caption"><%holdCount%> - HOLD</span>
								</div>
								<div class="item">
								    <img src="/img/google_markers/marker_blue.png"/>
								    <span class="caption"><%OFDCount%> - OUT FOR DELIVERY</span>
								</div>
								<div class="item">
								    <img src="/img/google_markers/marker_grey.png"/>
								    <span class="caption"><%partialDCount%> - PARTIALLY DELIVERED</span>
								</div>
								<div class="item">
								    <img src="/img/google_markers/marker_green.png"/>
								    <span class="caption"><%deliveryCount%> - DELIVERED</span>
								</div>
								<div class="item">
								    <img src="/img/google_markers/marker_red.png"/>
								    <span class="caption"><%returnDCount%> - RETURNED</span>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top: 0px;margin-bottom: 5px;padding-left: 15px;">
							<div class="col-md-12">
								<span style="font-size: 18px; color: #aaa;">Address</span>
							</div>	
						</div>
						<div class="row" style="padding-left: 15px;width: 101%;">
							<div class="col-md-12">
								<div class="timeline-centered">
									<article class="timeline-entry">
							            <div class="timeline-entry-inner">
							                <div class="timeline-icon bg-success">
							                    <i class="entypo-suitcase"></i>
							                </div>
							                <div class="timeline-label">
							                    <h2>Start Point</h2>
							                    <p><%executiveStartTime%> | <%startPoint%>.</p>
							                </div>
							            </div>
							        </article>
							        <article class="timeline-entry">
							            <div class="timeline-entry-inner">
							                <div class="timeline-icon bg-secondary">
							                    <i class="entypo-feather"></i>
							                </div>
							                <div class="timeline-label">
							                    <h2>End Point</h2>
							                    <p><%executiveEndTime%> | <%endPoint%>.</p>
							                </div>
							            </div>
							        </article>
							    </div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<span style="font-size: 16px;color: #aaa;padding-left: 15px;">Total Distance : </span>
								<span style="font-size: 18px;color: #7373a9;"><%distance%> KM</span>
								<div id="img-out" style="width: 400px;height: 400px;"></div>
							</div>
						</div>
					</div>

					<div class="row" ng-hide="showdeDetails == true">
						<div class="col-md-12">
							<div id="map_canvas2"></div>
						</div>
					</div>
				</form>

		    </div>
		    <div id="historyReport" class="tab-pane fade">
		    	<div class="row" style="padding: 15px;">
	      			<div class="col-md-3">
	      				<select class="form-control select2me" ng-model="selectedReportHUB" ng-change="getHubDetails(selectedReportHUB)">
							<option selected value="">---- Select Hub ----</option>
							<!-- <option selected value="all">All</option> -->
							<option ng-repeat="(key,value) in hubListData | orderBy:value" value="<%key%>"><%value%></option>
						</select>
	      			</div>
	      			<div class="col-md-3">
	      				<select class="form-control select2me" id="reportCaptureDe" ng-model="selectedReportFF">
							<option selected value="">---- Select Delivery Executive ----</option>
							<option value="<%selectedReportHUB%>">All</option>
							<option ng-repeat="(key,value) in selectedDEList | orderBy:'deValue'"  ng-if="value.deValue != 'all'" value="<%value.deId%>"><%value.deValue%></option>
						</select>
	      			</div>
	      			<div class="col-md-2">
						<div class="form-group">
							<div class='input-group date' id='datetimepickerfromDate' >
								<input type='text' class="form-control" style="border: 1px solid #e5e5e5;" placeholder="From Date" ng-model="date" id="reqFromDate"/ >
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<div class='input-group date' id='datetimepickertoDate' >
								<input type='text' class="form-control" style="border: 1px solid #e5e5e5;" placeholder="To Date" ng-model="date" id="reqToDate"/ >
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-1">
						<button type="submit" value="Submit" ng-click="getReportExTrack(selectedReportFF)" class="btn trackButtons">Submit</button>
					</div>
					<div class="col-md-1">
						<button type="submit" class="btn trackButtons" ng-click="exportToExcel(selectedReportFF)"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
					</div>
		     	</div>
		     	<div class="row" id="tableToExport">
		     		<div class="col-md-12 reportTable" style="margin-left: 15px;">
			     		<div class="loader" loading></div>
			     		<div ng-show="deReportData.status == true">
			     			<div class="row">
			     				<div class="col-md-12" style="width: 97.8%;bottom: 15px;">
				     				<p style="float: right;color: #009fda;">* All Values will be in the form of Rupee (&#8377;).</p>
			     				</div>
			     			</div>
			     			
			     			<div ui-grid-pagination ng-if="deReportData.status == true" ng-init="ngGridFIx()" ui-grid="gridOptions" ui-grid-exporter class="myGrid"></div>
			     		</div>
		     		</div>
		     	</div>
			    <span style="padding-left: 15px;" ng-show="deReportData.status == false">No data found in mongo!</span>
		    </div>
		</div>
	</div>
</div>
@stop

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">

<style type="text/css">
.ui-grid-menu {
	width: 225px !important;
}

.ui-grid-menu-button {
    z-index: 2;
    position: absolute;
    right: 0;
    top: 0;
    background: #f3f3f3;
    border: 1px solid #d4d4d4;
    cursor: pointer;
    height: 23px !important;
    width: 19px !important;
    font-weight: normal;
}
.testingClass{
	text-align: right !important;
}
.myGrid {
	width: 97.5%;
	height: 410px;
}
.ui-grid-pager-control button {
    height: 10px !important;
}
.ui-grid-pager-control input {
    height: 12px !important;
}
.ui-grid-pager-control .ui-grid-pager-max-pages-number {
    vertical-align: inherit !important;
}
.ui-grid-pager-row-count-picker select {
    height: 12px !important;
}


.loadicon {
	margin-left: 8px!important;
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
	z-index : 9999999;
}

.reportTable th{
	background: #a7b9bf;
}

.reportTable tr:nth-child(even){background-color: #eee}

td, th {
    /*width: 100px;
    text-align: center;*/
    border: 1px solid #e5e5e5;
    font-size: 12px;
    padding: 10px;
}

i.fa.fa-file-excel-o {
    font-size: 18px;
    top: 2px;
    position: relative;
}

canvas{
	width: 700px;
	position: relative;
	left: 15px;
	top: 15px;
}

.trackButtons {
    background: transparent;
    border: 1px solid #e5e5e5;
}

.input-group .input-group-addon {
    border-color: #e5e5e5;
    background: rgba(229, 229, 229, 0.28) !important;
    min-width: 39px;
}

.showDeBut{
	background: transparent;
    border: none;
    padding-left: 15px;
    color: #009fda;
    font-size: 15px;
    cursor: pointer;
}

.item {
    vertical-align: top;
    display: inline-block;
    text-align: center;
    width: 100px;
}
.item img{
	width: 15px;
}
.caption {
	display: block;
    color: #737881;
    font-family: "Noto Sans",sans-serif;
    font-size: 12px;
    line-height: 1.428571;
}


img {
    vertical-align: middle;
}

.img-responsive {
    display: block;
    height: auto;
    max-width: 100%;
}

.img-rounded {
    border-radius: 3px;
}

.img-thumbnail {
    background-color: #fff;
    border: 1px solid #ededf0;
    border-radius: 3px;
    display: inline-block;
    height: auto;
    line-height: 1.428571429;
    max-width: 100%;
    moz-transition: all .2s ease-in-out;
    o-transition: all .2s ease-in-out;
    padding: 2px;
    transition: all .2s ease-in-out;
    webkit-transition: all .2s ease-in-out;
}

.img-circle {
    border-radius: 50%;
}

.timeline-centered {
    position: relative;
    margin-bottom: 10px;
}

.timeline-centered:before, .timeline-centered:after {
    content: " ";
    display: table;
}

.timeline-centered:after {
    clear: both;
}

.timeline-centered:before, .timeline-centered:after {
    content: " ";
    display: table;
}

.timeline-centered:after {
    clear: both;
}

.timeline-centered:before {
    content: '';
    position: absolute;
    display: block;
    width: 4px;
    background: #f5f5f6;
    /*left: 50%;*/
    top: 20px;
    bottom: 20px;
    margin-left: 18px;
}

.timeline-centered .timeline-entry {
    position: relative;
    /*width: 50%;
    float: right;*/
    margin-top: 5px;
    margin-left: 30px;
    margin-bottom: 10px;
    clear: both;
}
.timeline-centered .timeline-entry:before, .timeline-centered .timeline-entry:after {
    content: " ";
    display: table;
}

.timeline-centered .timeline-entry:after {
    clear: both;
}

.timeline-centered .timeline-entry:before, .timeline-centered .timeline-entry:after {
    content: " ";
    display: table;
}

.timeline-centered .timeline-entry:after {
    clear: both;
}

.timeline-centered .timeline-entry.begin {
    margin-bottom: 0;
}

.timeline-centered .timeline-entry.left-aligned {
    float: left;
}
.timeline-centered .timeline-entry.left-aligned .timeline-entry-inner {
    margin-left: 0;
    margin-right: -18px;
}

    .timeline-centered .timeline-entry.left-aligned .timeline-entry-inner .timeline-time {
        left: auto;
        right: -100px;
        text-align: left;
    }

    .timeline-centered .timeline-entry.left-aligned .timeline-entry-inner .timeline-icon {
        float: right;
    }

    .timeline-centered .timeline-entry.left-aligned .timeline-entry-inner .timeline-label {
        margin-left: 0;
        margin-right: 70px;
    }

.timeline-centered .timeline-entry.left-aligned .timeline-entry-inner .timeline-label:after {
    left: auto;
    right: 0;
    margin-left: 0;
    margin-right: -9px;
    -moz-transform: rotate(180deg);
    -o-transform: rotate(180deg);
    -webkit-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    transform: rotate(180deg);
}

.timeline-centered .timeline-entry .timeline-entry-inner {
position: relative;
margin-left: -20px;
}

.timeline-centered .timeline-entry .timeline-entry-inner:before, .timeline-centered .timeline-entry .timeline-entry-inner:after {
    content: " ";
    display: table;
}

.timeline-centered .timeline-entry .timeline-entry-inner:after {
    clear: both;
}

.timeline-centered .timeline-entry .timeline-entry-inner:before, .timeline-centered .timeline-entry .timeline-entry-inner:after {
content: " ";
display: table;
}

.timeline-centered .timeline-entry .timeline-entry-inner:after {
clear: both;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-time {
position: absolute;
left: -100px;
text-align: right;
padding: 10px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-time > span {
display: block;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-time > span:first-child {
font-size: 15px;
font-weight: bold;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-time > span:last-child {
font-size: 12px;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon {
background: #fff;
color: #737881;
display: block;
width: 20px;
height: 20px;
-webkit-background-clip: padding-box;
-moz-background-clip: padding;
background-clip: padding-box;
-webkit-border-radius: 20px;
-moz-border-radius: 20px;
border-radius: 20px;
text-align: center;
-moz-box-shadow: 0 0 0 5px #f5f5f6;
-webkit-box-shadow: 0 0 0 5px #f5f5f6;
box-shadow: 0 0 0 5px #f5f5f6;
line-height: 40px;
font-size: 15px;
float: left;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-primary {
background-color: #303641;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-secondary {
background-color: #ee4749;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-success {
background-color: #00a651;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-info {
background-color: #21a9e1;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-warning {
background-color: #fad839;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-icon.bg-danger {
background-color: #cc2424;
color: #fff;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label {
position: relative;
background: #f5f5f6;
padding: 0.5em;
margin-left: 45px;
-webkit-background-clip: padding-box;
-moz-background-clip: padding;
background-clip: padding-box;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label:after {
content: '';
display: block;
position: absolute;
width: 0;
height: 0;
border-style: solid;
border-width: 9px 9px 9px 0;
border-color: transparent #f5f5f6 transparent transparent;
left: 0;
top: 0px;
margin-left: -9px;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label h2, .timeline-centered .timeline-entry .timeline-entry-inner .timeline-label p {
color: #737881;
font-family: "Noto Sans",sans-serif;
font-size: 12px;
margin: 0;
line-height: 1.428571429;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label p + p {
margin-top: 10px;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label h2 {
font-size: 16px;
margin-bottom: 10px;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label h2 a {
color: #303641;
}

.timeline-centered .timeline-entry .timeline-entry-inner .timeline-label h2 span {
-webkit-opacity: .6;
-moz-opacity: .6;
opacity: .6;
-ms-filter: alpha(opacity=60);
filter: alpha(opacity=60);
}


.redDotClass{
	background: red;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    margin-top: 10px;
}
.greenDotClass{
	background: green;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    margin-top: 10px;
}

.mainTabs{
	padding: 15px;
}
.mainTabs li{
	width: 150px;
}
.nav-tabs>li>a{
	text-align: center;
	border: 1px solid #ddd;
	color: #009fda;
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    color: #777 !important;
    background-color: #fff;
    border: 1px solid #009fda;
    border-bottom-color: transparent;
    cursor: default;
}
.nav-tabs, .nav-pills {
    margin-bottom: 10px;
    margin-left: 15px;
    margin-right: 15px;
}
/*#map_wrapper {
    height: 400px;
}*/
#map_canvas {
    width: 97.5%;
    height: 600px;
    left: 15px;
    margin-bottom: 15px;
}
/*#map {
  height: 400px;
}*/
#map_canvas2{
	width: 97.5%;
    height: 600px;
    left: 15px;
    top: 15px;
    margin-bottom: 15px;
}
.page-content {
    height: auto;
}
.custom_txt{
	font-size: 14px;
    font-weight: 300;
    line-height: 1.5;
    color: #141f5f;
}


	@-webkit-keyframes spin {
		0% { -webkit-transform: rotate(0deg); }
		100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>

@stop

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.1/lodash.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.2/socket.io.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-socket-io/0.7.0/socket.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>


<script type="text/javascript" src="https://cdn.rawgit.com/niklasvh/html2canvas/0.5.0-alpha2/dist/html2canvas.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
<script src="{{ URL::asset('assets/global/scripts/deliveryPDF.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>


<script type="text/javascript">
	$('#datetimepicker6').datetimepicker({
		format: "YYYY-MM-DD"
	});

	$('#datetimepickerfromDate').datetimepicker({
		format: "YYYY-MM-DD"
		// for setting min and max date
		/*minDate: moment().add(-30, 'days'),
		maxDate: moment().add(0, 'days') */
	});

	$('#datetimepickertoDate').datetimepicker({
		format: "YYYY-MM-DD"
	});
</script>
<script type="text/javascript">
	var retailMarker;
	var newmap;
	var map;
	var app = angular.module('mapRouting', ['btford.socket-io','ui.grid','ui.grid.pagination', 'ui.grid.exporter'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.factory('socket', function (socketFactory) {
	  return socketFactory({
	    prefix: '',
	    ioSocket: io.connect('<?php echo env('DE_SOCKET_IO') ?>')
	  });
	});
	app.service('getAddress', function($http){
		this.addressData = function(latitude,longitude){
			var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latitude+','+longitude+'&key=AIzaSyB_FK8ri9X2s4_ba8zT3O1fkpziAicdaGM';
			return $http({
				method : 'GET',
				url : url,
			}).then(function(response){
				return (response.data.results.formatted_address == undefined ? response.data.results[0].formatted_address : response.data.status);
			}).catch(function(err){
				throw err;
			});
		}
	});
	
	app.controller('viewTrackExCtrl', function ($scope,$http,$rootScope, $location, $filter,getAddress,socket,$timeout) {

		$scope.executiveTrackData = angular.fromJson(<?php echo $de_list;?>);
		$scope.hubListData = angular.fromJson(<?php echo $hub_list;?>);

		$scope.getHubDetails = function(value){
			$scope.selectedDEList = [];
			$('#lastCaptureDE').select2('val','');
			$('#historyCaptureDe').select2('val','');
			$('#reportCaptureDe').select2('val','');
			//$scope.selectedHubValue = value;
			angular.forEach($scope.executiveTrackData, function(filtValue, filtKey){
				if(filtKey == value){
					angular.forEach(filtValue, function(finalValue, finalKey){
						var temp = {
							'deId':finalKey,
							'deValue':finalValue
						}
						$scope.selectedDEList.push(temp);
					});
				}
			});
			//$scope.selectedDEList = $scope.executiveTrackData[value];
			if(value == 'all'){
				$scope.getLastExTrack(value);
			}
		}

		$scope.showDEDetailsToggle = function(){
			$scope.showdeDetails = !$scope.showdeDetails;
			convertMap();
		}

		$scope.exportToExcel=function(tableValues){ // ex: '#my-table'
			angular.forEach($scope.selectedDEList, function(valueDE,keyDE){
				if(tableValues == valueDE.deId){
					$scope.selectedDeOption = valueDE.deId;
				}
			});
			$scope.selectedFromDate = angular.element(document.querySelector('#reqFromDate')).val();
			$scope.selectedToDate = angular.element(document.querySelector('#reqToDate')).val();
			if(tableValues == $scope.selectedDeOption){
				window.open('/routingadmin/exportTrackDataToExcel?de_id='+tableValues+'&from_date='+$scope.selectedFromDate+'&to_date='+$scope.selectedToDate);
			}else{
				window.open('/routingadmin/exportTrackDataToExcel?hub_id='+tableValues+'&from_date='+$scope.selectedFromDate+'&to_date='+$scope.selectedToDate);
			}
			//var exportHref=Excel.tableToExcel(tableId,'Delivery Report');
            //$timeout(function(){location.href=exportHref;},100); // trigger download
        }

        // ui-grid section

        $scope.gridOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
			columnDefs: [
				{ field: 'Hub', width: '140' },
				{ field: 'DO`s', width: '150' },
				{ field: 'Vehicle No', width: '110' },
				{ field: 'Attempted Date', width: '135', cellClass: 'testingClass' },
				{ field: 'Distance', width: '85' , cellClass: 'testingClass'},
				{ field: 'Orders Assigned', width: '145' , cellClass: 'testingClass'},
				{ field: 'Invoice Value', width: '115' , cellClass: 'testingClass'},
				{ field: 'DIP', width: '55' , cellClass: 'testingClass'},
				{ field: 'Delivered', width: '90' , cellClass: 'testingClass'},
				{ field: 'PR', width: '50' , cellClass: 'testingClass'},
				{ field: 'FR', width: '50' , cellClass: 'testingClass'},
				{ field: 'Hold', width: '60' , cellClass: 'testingClass'},
				{ field: 'Collected Value', width: '140' , cellClass: 'testingClass'},
				{ field: 'Returned Value', width: '130' , cellClass: 'testingClass'}
			],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		$scope.ngGridFIx = function() {
			window.dispatchEvent(new Event('resize'));
		}
		// ui-grid end

		var mapStyleCss = [
		    {
		        "featureType": "administrative",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#d6e2e6"
		            }
		        ]
		    },
		    {
		        "featureType": "administrative",
		        "elementType": "geometry.stroke",
		        "stylers": [
		            {
		                "color": "#cfd4d5"
		            }
		        ]
		    },
		    {
		        "featureType": "administrative",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#7492a8"
		            }
		        ]
		    },
		    {
		        "featureType": "administrative.neighborhood",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "lightness": 25
		            }
		        ]
		    },
		    {
		        "featureType": "landscape.man_made",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#dde2e3"
		            }
		        ]
		    },
		    {
		        "featureType": "landscape.man_made",
		        "elementType": "geometry.stroke",
		        "stylers": [
		            {
		                "color": "#cfd4d5"
		            }
		        ]
		    },
		    {
		        "featureType": "landscape.natural",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#dde2e3"
		            }
		        ]
		    },
		    {
		        "featureType": "landscape.natural",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#7492a8"
		            }
		        ]
		    },
		    {
		        "featureType": "landscape.natural.terrain",
		        "elementType": "all",
		        "stylers": [
		            {
		                "visibility": "off"
		            }
		        ]
		    },
		    {
		        "featureType": "poi",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#dde2e3"
		            }
		        ]
		    },
		    {
		        "featureType": "poi",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#588ca4"
		            }
		        ]
		    },
		    {
		        "featureType": "poi",
		        "elementType": "labels.icon",
		        "stylers": [
		            {
		                "saturation": -100
		            }
		        ]
		    },
		    {
		        "featureType": "poi.park",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#a9de83"
		            }
		        ]
		    },
		    {
		        "featureType": "poi.park",
		        "elementType": "geometry.stroke",
		        "stylers": [
		            {
		                "color": "#bae6a1"
		            }
		        ]
		    },
		    {
		        "featureType": "poi.sports_complex",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#c6e8b3"
		            }
		        ]
		    },
		    {
		        "featureType": "poi.sports_complex",
		        "elementType": "geometry.stroke",
		        "stylers": [
		            {
		                "color": "#bae6a1"
		            }
		        ]
		    },
		    {
		        "featureType": "road",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#41626b"
		            }
		        ]
		    },
		    {
		        "featureType": "road",
		        "elementType": "labels.icon",
		        "stylers": [
		            {
		                "saturation": -45
		            },
		            {
		                "lightness": 10
		            },
		            {
		                "visibility": "on"
		            }
		        ]
		    },
		    {
		        "featureType": "road.highway",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#c1d1d6"
		            }
		        ]
		    },
		    {
		        "featureType": "road.highway",
		        "elementType": "geometry.stroke",
		        "stylers": [
		            {
		                "color": "#a6b5bb"
		            }
		        ]
		    },
		    {
		        "featureType": "road.highway",
		        "elementType": "labels.icon",
		        "stylers": [
		            {
		                "visibility": "on"
		            }
		        ]
		    },
		    {
		        "featureType": "road.highway.controlled_access",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#9fb6bd"
		            }
		        ]
		    },
		    {
		        "featureType": "road.arterial",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#ffffff"
		            }
		        ]
		    },
		    {
		        "featureType": "road.local",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#ffffff"
		            }
		        ]
		    },
		    {
		        "featureType": "transit",
		        "elementType": "labels.icon",
		        "stylers": [
		            {
		                "saturation": -70
		            }
		        ]
		    },
		    {
		        "featureType": "transit.line",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#b4cbd4"
		            }
		        ]
		    },
		    {
		        "featureType": "transit.line",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#588ca4"
		            }
		        ]
		    },
		    {
		        "featureType": "transit.station",
		        "elementType": "all",
		        "stylers": [
		            {
		                "visibility": "off"
		            }
		        ]
		    },
		    {
		        "featureType": "transit.station",
		        "elementType": "labels.text.fill",
		        "stylers": [
		            {
		                "color": "#008cb5"
		            },
		            {
		                "visibility": "on"
		            }
		        ]
		    },
		    {
		        "featureType": "transit.station.airport",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "saturation": -100
		            },
		            {
		                "lightness": -5
		            }
		        ]
		    },
		    {
		        "featureType": "water",
		        "elementType": "geometry.fill",
		        "stylers": [
		            {
		                "color": "#a6cbe3"
		            }
		        ]
		    }
		];

		$scope.getReportExTrack = function(value){
			angular.forEach($scope.selectedDEList, function(valueDE,keyDE){
				if(value == valueDE.deId){
					$scope.selectedDeOption = valueDE.deId;
				}
			});
			$scope.selectedFromDate = angular.element(document.querySelector('#reqFromDate')).val();
			$scope.selectedToDate = angular.element(document.querySelector('#reqToDate')).val();
			$scope.users = [];
			if(value == $scope.selectedDeOption){
				var req = $http.get('/routingadmin/getTrackHistoryByDE?de_id='+value+'&from_date='+$scope.selectedFromDate+'&to_date='+$scope.selectedToDate);
				req.success(function(successCallback){
					$scope.deReportData = successCallback;
					if($scope.deReportData.status == true){
						$scope.deReportList = $scope.deReportData.message;
						angular.forEach($scope.deReportList, function(deValueList, deKeyList){
							$scope.attempeddateFilt = $filter('date')(deValueList.attempted_date, "dd-MM-y");
							$scope.invoicedAmtFilt = $filter('currency')(deValueList.invoiced_amount, '' , 2);
							$scope.collectedAmtFilt = $filter('currency')(deValueList.collected_amount, '' , 2);
							$scope.returnedAmtFilt = $filter('currency')(deValueList.returned_total, '' , 2);
							$scope.users.push(
								{ 'Hub' : deValueList.hub_name, 'DO`s': deValueList.de_name, 'Vehicle No': deValueList.order_data[0].vehicle_no, 'Attempted Date': $scope.attempeddateFilt, 'Distance': deValueList.distance, 'Orders Assigned': deValueList.order_status.order_attempted, 'Invoice Value': $scope.invoicedAmtFilt, 'DIP': deValueList.order_status.OUT_FOR_DELIVERY, 'Delivered': deValueList.order_status.DELIVERED, 'PR': deValueList.order_status.PARTIALLY_DELIVERED, 'FR': deValueList.order_status.RETURNED, 'Hold': deValueList.order_status.HOLD, 'Collected Value': $scope.collectedAmtFilt, 'Returned Value': $scope.returnedAmtFilt
								});
								$scope.gridOptions.data = $scope.users;
						});
					}else{
						console.log($scope.deReportData.message);
					}
				});
				req.error(function(errorCallback){
					alert(JSON.stringify(errorCallback));
				});
			}else{
				var req = $http.get('/routingadmin/getTrackHistoryByHub?hub_id='+value+'&from_date='+$scope.selectedFromDate+'&to_date='+$scope.selectedToDate);
				req.success(function(successCallback){
					$scope.deReportData = successCallback;
					if($scope.deReportData.status == true){
						$scope.deReportList = $scope.deReportData.message;
						angular.forEach($scope.deReportList, function(deValueList, deKeyList){
							$scope.attempeddateFilt = $filter('date')(deValueList.attempted_date, "dd-MM-y");
							$scope.invoicedAmtFilt = $filter('currency')(deValueList.invoiced_amount, '' ,2);
							$scope.collectedAmtFilt = $filter('currency')(deValueList.collected_amount, '' , 2);
							$scope.returnedAmtFilt = $filter('currency')(deValueList.returned_total, '' , 2);
							$scope.users.push(
								{ 'Hub' : deValueList.hub_name, 'DO`s': deValueList.de_name, 'Vehicle No': deValueList.order_data[0].vehicle_no, 'Attempted Date': $scope.attempeddateFilt, 'Distance': deValueList.distance, 'Orders Assigned': deValueList.order_status.order_attempted, 'Invoice Value': $scope.invoicedAmtFilt, 'DIP': deValueList.order_status.OUT_FOR_DELIVERY, 'Delivered': deValueList.order_status.DELIVERED, 'PR': deValueList.order_status.PARTIALLY_DELIVERED, 'FR': deValueList.order_status.RETURNED, 'Hold': deValueList.order_status.HOLD, 'Collected Value': $scope.collectedAmtFilt, 'Returned Value': $scope.returnedAmtFilt
								});
								$scope.gridOptions.data = $scope.users;
						});
					}else{
						console.log($scope.deReportData.message);
					}
				});
				req.error(function(errorCallback){
					alert(JSON.stringify(errorCallback));
				});
			}
		}

		$scope.getLastExTrack = function(lastExDetail){
			var req = $http.get('getlastknownlocation?user_list='+lastExDetail);
			req.success(function(successCallback){
				$scope.loadExecutiveTrack = successCallback;
				if($scope.loadExecutiveTrack.status == false){
					alert('user data not available');
				}else if($scope.loadExecutiveTrack.status == true){
				// Map Initialize
				var markersArr = [];
				var bounds = new google.maps.LatLngBounds();
			    var mapOptions = {
			    		center : {lat:17.385044,lng:78.486671},
	        			mapTypeId: 'roadmap',
					    styles: mapStyleCss,
					    fullscreenControl: true
			    };
			    	// Display a map on the page
			    	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			    	map.setTilt(45);
			    	$rootScope.address ;
			    	$rootScope.markersData = [];
			    	console.log($scope.loadExecutiveTrack.message.hubs);
					angular.forEach($scope.loadExecutiveTrack.message, function(value,key){
						var address = getAddress.addressData(value.latitude,value.longitude)
						.then(function(data){
							$scope.address = data;
							var markers = [value.latitude,value.longitude,value.last_seen,value.name,value.user_id,$scope.address];
							$rootScope.markersData.push(markers);
							// Info Window Content
							var infoWindowContent = 
								'<div class="info_content">' +
								'<p><b>Delivery Executive Details<span style="padding-left:50px;"> </b></p>' +
								'<p><b>Executive Name <span style="padding-left:13.8px;">: </b>' + markers[3] + '</span></p>' +
								'<p><b>Last Seen <span style="padding-left:50px;">: </b>' + markers[2] + '</span></p>' +
								'</div>';
							var infowindow = new google.maps.InfoWindow({maxWidth: 300});
							var car = "M38 68l0 0 0 7c0,1 -1,1 -1,1l-33 0c0,0 -1,0 -1,-1l0 -7 0 0c-1,0 -1,-1 -1,-1l0 -8c0,-1 0,-1 1,-1l0 0 0 -26c0,0 1,-1 1,-1l33 0c0,0 1,1 1,1l0 26 0 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1zm-32 -38c0,0 -1,1 -1,-1 0,0 0,-1 0,-1l-2 0c0,0 -1,-1 -1,-1l0 -8c0,-1 1,-1 1,-1l2 0 0 -4 0 0c0,0 0,0 -1,-1 0,0 0,0 0,0 -1,0 -4,2 -4,0 0,-2 4,-2 4,-2 0,1 1,0 1,0 0,1 0,1 0,1l0 -8c0,0 1,-1 4,-2 4,-2 8,-2 11,-2l0 0c0,0 0,0 1,0 0,0 1,0 1,0l0 0c2,0 6,0 11,2 3,1 3,2 3,2l0 7c0,0 1,1 1,0 0,0 4,0 4,2 1,2 -3,0 -3,0 0,0 -1,0 -1,0 0,1 -1,1 -1,1l0 4 2 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1l-2 0c0,0 0,1 0,1 0,2 -1,1 -1,1l-14 0 -15 0zm5 -2l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -11 0 0 0 11 0 0zm2 0l0 -11 0 0 0 11 0 0zm1 0l0 -11 1 0 0 11 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm5 -15l0 -8c0,0 -4,-2 -14,-2 -4,0 -10,0 -14,2l0 8c0,0 6,-1 14,-1 8,0 14,1 14,1z";
							var icon = {
							  path: car,
							  scale: .5,
							  strokeColor: 'white',
							  strokeWeight: .10,
							  fillOpacity: 1,
							  fillColor: '#4d4d4d',
							  offset: '5%',
							  rotation: parseInt((value.heading == null ? 0 : value.heading)),
							  //anchor: new google.maps.Point(22, 50) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
							};
							var position = new google.maps.LatLng(value.latitude,value.longitude);
							bounds.extend(position);
							marker = new google.maps.Marker({
								position: position,
								map: map,
								title: value.name,
								id:value.user_id,
								icon : icon
							});
							markersArr[marker.id] = marker;
							google.maps.event.addListener(marker,'click', (function(marker,infoWindowContent,infowindow){ 
							return function() {
								infowindow.setContent(infoWindowContent);
								infowindow.open(map,marker);
							};
							})(marker,infoWindowContent,infowindow)); 

							// Automatically center the map fitting all markers on the screen
							map.fitBounds(bounds);
							
						});

						angular.forEach($scope.loadExecutiveTrack.message.hubs, function(hubValues, hubKeys){
			    			var new_markers = [hubValues.bu_name,hubValues.latitude,hubValues.longitude,hubValues.address];

							var position = new google.maps.LatLng(hubValues.latitude,hubValues.longitude);
					        bounds.extend(position);
					        new_marker = new google.maps.Marker({
					            position: position,
					            map: map,
					            title: hubValues.bu_name+ '\n Address: '+hubValues.address,
					            icon: '/img/google_markers/pointer-multicolor.png'
					        });
			    		});
						var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
					        this.setZoom(12);
					        google.maps.event.removeListener(boundsListener);
					    });
					}); 
					socket.on("deSocket", function(message){   
						$scope.sockUpdate = message.data.location_data;
						console.log($scope.sockUpdate);
						$scope.changeMark($scope.sockUpdate);
					});
					$scope.changeMark = function(value){
						$scope.chngMarkVal = value;
						var latlng = new google.maps.LatLng(value.latitude, value.longitude);
						if (markersArr[value.user_id] != undefined) {
							// move marker in 1000ms and with linear animation.
							var car = "M38 68l0 0 0 7c0,1 -1,1 -1,1l-33 0c0,0 -1,0 -1,-1l0 -7 0 0c-1,0 -1,-1 -1,-1l0 -8c0,-1 0,-1 1,-1l0 0 0 -26c0,0 1,-1 1,-1l33 0c0,0 1,1 1,1l0 26 0 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1zm-32 -38c0,0 -1,1 -1,-1 0,0 0,-1 0,-1l-2 0c0,0 -1,-1 -1,-1l0 -8c0,-1 1,-1 1,-1l2 0 0 -4 0 0c0,0 0,0 -1,-1 0,0 0,0 0,0 -1,0 -4,2 -4,0 0,-2 4,-2 4,-2 0,1 1,0 1,0 0,1 0,1 0,1l0 -8c0,0 1,-1 4,-2 4,-2 8,-2 11,-2l0 0c0,0 0,0 1,0 0,0 1,0 1,0l0 0c2,0 6,0 11,2 3,1 3,2 3,2l0 7c0,0 1,1 1,0 0,0 4,0 4,2 1,2 -3,0 -3,0 0,0 -1,0 -1,0 0,1 -1,1 -1,1l0 4 2 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1l-2 0c0,0 0,1 0,1 0,2 -1,1 -1,1l-14 0 -15 0zm5 -2l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -11 0 0 0 11 0 0zm2 0l0 -11 0 0 0 11 0 0zm1 0l0 -11 1 0 0 11 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm5 -15l0 -8c0,0 -4,-2 -14,-2 -4,0 -10,0 -14,2l0 8c0,0 6,-1 14,-1 8,0 14,1 14,1z";

							var icon = {
								  path: car,
								  scale: .5,
								  strokeColor: 'white',
								  strokeWeight: .10,
								  fillOpacity: 1,
								  fillColor: '#4d4d4d',
								  offset: '5%',
								  rotation: parseInt((value.heading == null ? 0 : value.heading)),
								  anchor: new google.maps.Point(22, 50) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
							};
							markersArr[value.user_id].setIcon(icon);
							markersArr[value.user_id].animateTo(latlng); 
							/*$scope.selectedLastFF != 'all' || */
							if($scope.selectedLastHUB != 'all'){
								map.panTo(new google.maps.LatLng(value.latitude, value.longitude));
							}

						}else{
							if($scope.selectedLastFF == 'all'){
								markersArr[value.user_id] = [];
								bounds.extend(latlng);
								var car = "M38 68l0 0 0 7c0,1 -1,1 -1,1l-33 0c0,0 -1,0 -1,-1l0 -7 0 0c-1,0 -1,-1 -1,-1l0 -8c0,-1 0,-1 1,-1l0 0 0 -26c0,0 1,-1 1,-1l33 0c0,0 1,1 1,1l0 26 0 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1zm-32 -38c0,0 -1,1 -1,-1 0,0 0,-1 0,-1l-2 0c0,0 -1,-1 -1,-1l0 -8c0,-1 1,-1 1,-1l2 0 0 -4 0 0c0,0 0,0 -1,-1 0,0 0,0 0,0 -1,0 -4,2 -4,0 0,-2 4,-2 4,-2 0,1 1,0 1,0 0,1 0,1 0,1l0 -8c0,0 1,-1 4,-2 4,-2 8,-2 11,-2l0 0c0,0 0,0 1,0 0,0 1,0 1,0l0 0c2,0 6,0 11,2 3,1 3,2 3,2l0 7c0,0 1,1 1,0 0,0 4,0 4,2 1,2 -3,0 -3,0 0,0 -1,0 -1,0 0,1 -1,1 -1,1l0 4 2 0c1,0 1,0 1,1l0 8c0,0 0,1 -1,1l-2 0c0,0 0,1 0,1 0,2 -1,1 -1,1l-14 0 -15 0zm5 -2l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -11 0 0 0 11 0 0zm2 0l0 -11 0 0 0 11 0 0zm1 0l0 -11 1 0 0 11 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 1 0 0 10 -1 0zm2 0l0 -10 0 0 0 10 0 0zm2 0l0 -10 0 0 0 10 0 0zm5 -15l0 -8c0,0 -4,-2 -14,-2 -4,0 -10,0 -14,2l0 8c0,0 6,-1 14,-1 8,0 14,1 14,1z";
								var icon = {
								  path: car,
								  scale: .5,
								  strokeColor: 'white',
								  strokeWeight: .10,
								  fillOpacity: 1,
								  fillColor: '#4d4d4d',
								  offset: '5%',
								  rotation: parseInt((value.heading == null ? 0 : value.heading)),
								  anchor: new google.maps.Point(22, 50) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
								};
								var executiveCar = 'https://cdn1.iconfinder.com/data/icons/business-finance-vol-10-2/512/33-32.png';
								new_marker = new google.maps.Marker({
									position: latlng,
									map: map,
									title: 'new_'+value.user_id,
									id:value.user_id,
									icon:icon
								});
								new_marker.setIcon(icon);
								// Info Window Content
								var infoWindowContentNew = 
								'<div class="info_content">' +
								'<p><b>Delivery Executive Details<span style="padding-left:50px;"> </b></p>' +
								'<p><b>Executive Name <span style="padding-left:13.8px;">: </b>' + value.user_id + '</span></p>' +
								'</div>';
								var infowindowNew = new google.maps.InfoWindow({maxWidth: 300});
								google.maps.event.addListener(new_marker,'click', (function(new_marker,infoWindowContentNew,infowindowNew){ 
									return function() {
										infowindowNew.setContent(infoWindowContentNew);
										infowindowNew.open(map,new_marker);
									};
									})(new_marker,infoWindowContentNew,infowindowNew));
								markersArr[value.user_id] = new_marker;
							}
							
						}
						
					}          
				}
			});
			req.error(function(errorCallback){
				alert(JSON.stringify(errorCallback));
			});
		}

		$scope.getTrackExecutive =  function(ffDetails,hubValue){
			var hubSValue = $scope.hubListData[hubValue];
			var bounds = new google.maps.LatLngBounds();
			var mapOptions_new = {
					center : {lat:17.385044,lng:78.486671},
					mapTypeId: 'roadmap',
					zoom: 15,
					styles: mapStyleCss,
					fullscreenControl: true
					/*scrollwheel: false,
			        disableDoubleClickZoom: true,
			        zoomControl: false*/
			    };

	    	/* Display a map on the page */ 
	    	newmap = new google.maps.Map(document.getElementById("map_canvas2"), mapOptions_new);
	    	newmap.setTilt(45);
			$scope.selectedDate = angular.element(document.querySelector('#reqDate')).val();
			var res = $http.post('/routingadmin/getgeotrack?user_id='+ ffDetails +'&date='+$scope.selectedDate);
			res.success(function(data, status, headers, config) {
				$scope.loadHistoryExData = data.message;
				$scope.loadHistoryRetialData = data.order_data;
				var triangleCoords = [];
				var polylineLength = 0;
				var executiveName;
				$scope.startPoint;
				$scope.endPoint;
				var tempPath = [];
				if ($scope.loadHistoryExData == 'data not found') {
					alert('!error: '+$scope.loadHistoryExData);
				}else{
					angular.forEach($scope.loadHistoryExData, function(value, key){
						if (key == 0) {
							$scope.startTripTime = value.created_at;
							var a = $scope.startTripTime.split(" ");
							$scope.executiveStartTime = a[1];
							var startAddress = getAddress.addressData(value.latitude,value.longitude)
							.then(function(data){
								$scope.startPoint = data;
							});
							var position = new google.maps.LatLng(value.latitude,value.longitude);
							bounds.extend(position);
							markerNew = new google.maps.Marker({
									position: position,
									map: newmap,
									title: value.name,
									icon: {
								      path: google.maps.SymbolPath.CIRCLE,
								      strokeColor: "green",
								      scale: 5
								    }
							});
						}
						if (key == $scope.loadHistoryExData.length -1) {
							$scope.endTripTime = value.created_at;
							var a = $scope.endTripTime.split(" ");
							$scope.executiveEndTime = a[1];

							var endAddress = getAddress.addressData(value.latitude,value.longitude)
							.then(function(data){
								$scope.endPoint = data;
							});
								var position = new google.maps.LatLng(value.latitude,value.longitude);
								bounds.extend(position);
								markerNew = new google.maps.Marker({
										position: position,
										map: newmap,
										title: value.name,
										icon: {
									      path: google.maps.SymbolPath.CIRCLE,
									      strokeColor: "red",
									      scale: 5
									    }
								});
						}

						// Define the LatLng coordinates for the polygon.
						var polyDraw = {lat: value.latitude, lng: value.longitude};
						triangleCoords.push(polyDraw);
						var pointPath = new google.maps.LatLng(value.latitude,value.longitude);
						tempPath.push(pointPath);
						if (key > 0) {
							polylineLength += google.maps.geometry.spherical.computeDistanceBetween(tempPath[key], tempPath[key-1]);
						}
						executiveName = value.name;
					});

					var tempDistance = polylineLength / 1000; // into KM.
					$scope.distance = Math.round(tempDistance * 100)/100;
					$scope.executiveDispName = executiveName;
					var holdUnitCount = 0;
					var ofdCount = 0;
					var deliveredCount = 0;
					var returnedCount = 0;
					var pDeliveredCount = 0;
					var orderDECount = 0;

					// Retailer Shop Markers
					angular.forEach($scope.loadHistoryRetialData, function(retailValue,retailKey){
						orderDECount++;
						var colorURL;
					  	var infowindow = new google.maps.InfoWindow({
						    maxWidth: 300
						});
						if(retailValue.order_status == 'HOLD'){
							holdUnitCount++;
							colorURL = '/img/google_markers/marker_black.png';
						}else if(retailValue.order_status == 'OUT FOR DELIVERY'){
							colorURL = '/img/google_markers/marker_blue.png';
							ofdCount++;
						}else if(retailValue.order_status == 'DELIVERED'){
							colorURL = '/img/google_markers/marker_green.png';
							deliveredCount++;
						}else if(retailValue.order_status == 'RETURNED'){
							colorURL = '/img/google_markers/marker_red.png';
							returnedCount++
						}else if(retailValue.order_status == 'PARTIALLY DELIVERED'){
							colorURL = '/img/google_markers/marker_grey.png';
							pDeliveredCount++
						}else{
							colorURL = '/img/google_markers/marker_purple.png';
						}

						var contentString = '<div id="content">'+
					      '<p><b>Retailer Shop <span style="padding-left:20px;">: </b>' +  retailValue.business_legal_name +'</span></p>'+
					      '<p><b>Hub <span style="padding-left:76px;">: </b>' +  retailValue.hub_name +'</span></p>'+
					      '<p><b>Order Code <span style="padding-left:34px;">: </b>' +  retailValue.order_code +'</span></p>'+
					      '<p><b>Order Status <span style="padding-left:27px;">: </b>' +  retailValue.order_status +'</span></p>'+
					      '<p><b>Order Date <span style="padding-left:38px;">: </b>' +  retailValue.order_date +'</span></p>'+
					      '<p><b>Vehicle Number <span style="padding-left:7px;">: </b>' +  retailValue.vehicle_no +'</span></p>'+
					      '<p><b>Delivered At <span style="padding-left:29px;">: </b>' +  retailValue.delivery_date +'</span></p>'+
					      '</div>';

						var myLatlng = new google.maps.LatLng(retailValue.latitude,retailValue.longitude);
						retailMarker = new google.maps.Marker({
						    map: newmap,
						    position: myLatlng,
						    title:retailValue.business_legal_name,
						    icon:colorURL
						});

					google.maps.event.addListener(retailMarker,'click', (function(retailMarker,contentString,infowindow){ 
						return function() {
							infowindow.setContent(contentString);
							infowindow.open(map,retailMarker);
						};
						})(retailMarker,contentString,infowindow));
					});
					$scope.holdCount = holdUnitCount;
					$scope.OFDCount = ofdCount;
					$scope.deliveryCount = deliveredCount;
					$scope.partialDCount = pDeliveredCount;
					$scope.returnDCount = returnedCount;
					$scope.orderCodeDECount = orderDECount;
					/**
					 * executivePath holding executive path and drawing
					 * @type {google}
					 */
					newmap.fitBounds(bounds);
					var executivePath = new google.maps.Polyline({
						map: newmap,
						path: triangleCoords,
						geodesic: true,
						strokeColor: "#000000",
						strokeOpacity: 1.0,
						strokeWeight: 4
					});
					var boundsListener = google.maps.event.addListener((newmap), 'bounds_changed', function(event) {
						this.setZoom(13);
						google.maps.event.removeListener(boundsListener);
					});
				}
			});
			res.error(function(data, status, headers, config) {
				alert( "failure message: " + JSON.stringify({data: data}));
			});
		}
	});

//Bind click handlers - Here's the important part
$('a[href=#home], a[href=#menu1]').on('click', function() {
    setTimeout(function(){
    	if (map != undefined) {
    		google.maps.event.addListenerOnce(map, 'idle', function() {
			   google.maps.event.trigger(map, 'resize');
			});
    	}
    	if (newmap != undefined) {
    		google.maps.event.addListenerOnce(newmap, 'idle', function() {
			   google.maps.event.trigger(newmap, 'resize');
			});
    	}
    }, 50);
});

app.directive('loading',   ['$http' ,function ($http)
{
	return {
		restrict: 'A',
		link: function (scope, elm, attrs)
		{
			scope.isLoading = function () {
				return $http.pendingRequests.length > 0;
			};

			scope.$watch(scope.isLoading, function (v)
			{
				if(v){
					elm.css('display', 'block');
				}else{
					elm.css('display', 'none');
				}
			});
		}
	};

}]);
</script>

@stop
@extends('layouts.footer')