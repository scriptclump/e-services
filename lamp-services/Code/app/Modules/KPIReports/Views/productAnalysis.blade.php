@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Sales Trends'); ?>
	<div class=" tasks-widget" style="background-color: #f7f7f7!important;">
		<div class="portlet-title" style="background-color: #f7f7f7;color:#697882;font-size: 22px;font-weight: 400;">
			<div  style="padding-top:8px;padding-bottom: 8px; ">Sales Analysis Report</div>
			
		</div>
		<div  class="portlet-body" ng-app="KPIReports"  ng-controller="ProductAnalysisController" style="background-color: #f7f7f7;" >


			<div>
				<div   ng-init="loadSalesTrends('NULL','NULL','NULL','NULL','wtd')" style="padding-left:20px;padding-right:20px;padding-bottom:20px;background-color: #ffffff">
					<div class="row" style="margin-left: 1px;margin-top: 15px;background-color: #ffffff;">
						<div class="col-md-3" style=" padding-left: 15px;font-weight: 600!important;padding-top: 10px">Sales Trends</div>
						<div class="col-md-8"></div>
						<div class="col-md-1" style="padding-left: 67px;"><a href="javascript:void(0);" id="ShowFilterChart" class="ShowFilterChart"><i class="fa fa-filter fa-lg">
						</i></a></div>
					</div>
					<div class="chart_filter" id="chart_filter" style="display:none;padding-bottom: 68px;padding-top:15px;background-color: #ffffff">
						<div class="col-md-2">
								  	<label>DC</label>
									<select ng-model="chartDC"  id="chartDC"  class="form-control" >
										<option selected value="">---Select DC---</option>
										<option ng-repeat="(key,value) in DcList " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>    
									</select>
						</div>	
						<div class="col-md-2">
						  <label>Hub</label>
							<select ng-model="chartHub"  id="chartHub" class="form-control select2me">
								<option selected value="">---Select Hub---</option>
								<option ng-repeat="(key,value) in hubList " value="<%value.hub_id%>" ><%value.lp_wh_name%></option>    
							</select>
						</div>
						<div class="col-md-2">
							<label>Select Date</label>
							<select ng-model="custom_chart_date" id="custom_chart_date" class="form-control" ng-change="change_chart_date()">
								<option value="today" >Today</option>
								<option value="yesterday">Yesterday</option>
								<option value="wtd">WTD</option>
								<option value="mtd">MTD</option>
								<option value="quarter">Quarter</option>
								<option value="ytd">YTD</option>
								<option value="chart_date_range">Date Range</option>
						    </select>
						</div>
						<div ng-show="custom_chart_date=='chart_date_range'">
							<div class="col-md-2">
							          <label>Start Date</label>
							          <div class="input-icon input-icon-sm right">
							          <i class="fa fa-calendar"></i>
							          <input type="text" ng-model="start_chart_date" name="start_chart_date" id="start_chart_date" class="form-control" value="">
							          </div>
							 </div> 
							 <div class="col-md-2">
							          <label>End Date</label>
							          <div class="input-icon input-icon-sm right">
							          <i class="fa fa-calendar"></i>
							          <input type="text" ng-model="end_chart_date" name="end_chart_date" id="end_chart_date" class="form-control" value="">
							          </div>
							 </div> 
						</div>
						<div class="col-md-2">
							<button type="button" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut" 
							ng-click="loadSalesTrends(chartDC,chartHub,start_chart_date,end_chart_date,custom_chart_date)">Submit</button>
						</div>
					</div>
					<div class="tabbable tabs-below" style="padding-bottom: 15px; background-color: #ffffff">
	                    <div class="tab-content" style="padding-bottom: 20px;">
	                        <div class="tab-pane " id="SalesPie" style="height: 350px !important; "> 
	                           <div id="chart-container"></div>   
	                        </div>
	                        <div class="tab-pane active" id="salesStacked" style="height: 350px !important;  "> 
	                           <div id="SalesStackChart"></div> 
	                        </div>
	                    </div>
	                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: relative;">
	                        <ul class="nav" style="display: flex;float: right;">
	                            <li class="custIcon "><a href="#SalesPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
	                            <li class="custIcon active"><a href="#salesStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
	                        </ul>
	                    </div>
	                </div>
				</div>

				<div style="padding: 20px">
					<div class="row" style="background-color: #ffffff;">
						<div class="col-md-3" style="font-weight: 600!important; padding-bottom: 15px;padding-top: 15px;">Sales Team</div>
						<div class="col-md-8"></div>
						<div class="col-md-1" style="padding-left: 67px;"><a href="javascript:void(0);" id="ShowFilter" class="ShowFilter"><i class="fa fa-filter fa-lg"></i></a></div>
					</div>

					<div class="row" ng-init="loadSalesByPeriodChart('NULL','NULL','NULL','NULL',0)" id="sales_filter" style="display: none;background-color: #ffffff;padding-top:15px;padding-bottom: 10px;">



			      <div class="col-md-4">
			      	<label>Select Name</label>
			      	  <select name="manf_name[]" id="manf_name[]" ng-model="manf_name" class="form-control  multi-select-search-box"  multiple="multiple ">
			      	   	@foreach($salesData as $sales_id )
							<option value="{{ $sales_id->user_id }}">{{ $sales_id->FF_Name }}</option>
						@endforeach	
					   </select>
			     </div>
			   			   

						<div class="col-md-3">
									<label>Select Date</label>
									<select id="sales_team_date" ng-model="sales_team_date" class="form-control" style="padding-bottom: 10px;" ng-change="change_sales_date()">
										<option value="today" selected="selected">Today</option>
										<option value="yesterday">Yesterday</option>
										<option value="wtd">WTD</option>
										<option value="mtd">MTD</option>
										<option value="ytd">YTD</option>
										<option value="sales_date_range">Date Range</option>
								    </select>
						</div>
						<div ng-show="sales_team_date=='sales_date_range'">
							<div class="col-md-2">
							          <label>Start Date</label>
							          <div class="input-icon input-icon-sm right">
							          <i class="fa fa-calendar"></i>
							          <input type="text" ng-model="start_sales_date" name="start_sales_date" id="start_sales_date" class="form-control" value="">
							          </div>
							 </div> 
							 <div class="col-md-2">
							          <label>End Date</label>
							          <div class="input-icon input-icon-sm right">
							          <i class="fa fa-calendar"></i>
							          <input type="text" ng-model="end_sales_date" name="end_sales_date" id="end_sales_date" class="form-control" value="">
							          </div>
							 </div> 
						</div>
						
						<button ng-click="loadSalesByPeriodChart('NULL',start_sales_date,end_sales_date,sales_team_date,manf_name)"  onClick="getFieldForceList();"class="btn green-meadow subBut" style="margin-top: 23px;">Go</button>
					</div>



					<div class="row tabs-below" style=" margin-bottom: 15px; ">
						<div class="col-md-12 tab-content" style="padding-bottom: 15px; background-color: #ffffff;">
							<div class="tab-pane "  id="sales_team_grid" style="overflow-y: auto; overflow-x:auto;height: 550px;padding: 10px;">
								<div id="ff_list_tab" style="display:none;"></div>
								<div id="table_tab">
									<table id="dashboard_ff_list"></table>
								</div>

							</div>
							<div class="tab-pane active" id="sales_team_chart" style="height: 550px;padding: 10px;">
								<div id="sales_line_chart"></div>						
							</div>
						</div>
						<div class="barChatCLass" style="border-top: 1px solid #eee;position: relative;">
							<div class="loader" loading></div>
	                        <ul class="nav" style="display: flex;float: right;padding-left: 12px;">
	                            <li class="custIcon "><a href="#sales_team_grid" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
	                            <li class="custIcon active"><a href="#sales_team_chart" data-toggle="tab" class="fa fa-line-chart fa-lg" aria-hidden="true" title="Line Chart"></a></li>
	                        </ul>
	                	</div>
					</div>
					

				</div>
			</div>


			<div style="background-color: #f7f7f7;padding-bottom:  20px;padding-left: 20px;padding-right: 20px;">
				<div class="row" style="background-color: #ffffff; padding-top: 15px; padding-bottom: 15px;">
					<div class="col-md-3" style="font-weight: 600!important;">Product Analysis Report</div>
					<div class="col-md-8"></div>
					<div class="col-md-1" style="padding-left: 67px;"><a href="javascript:void(0);" id="GridFilter" class="GridFilter"><i class="fa fa-filter fa-lg"></i></a></div>
				</div>
				<div id="product_report"  ng-init="loadProductAnalysis('NULL','NULL','NULL','NULL','NULL')" >
						{{ Form::open(array('url' => '/kpi/productSummaryexport', 'id' => 'productDownloadExcel'))}}
						<div class="grid_filter" id="grid_filter" style="display:none">

							<div class="row" style="padding:15px; background-color: #ffffff;">


								<div class="col-md-3">
								  	<label>DC</label>
									<select ng-model="productDC"  id="productDC" name="product_dc"  class="form-control" ng-change="getHubSortData(productDC)">
										<option selected value="">---Select DC---</option>
										<option ng-repeat="(key,value) in dc_list_data " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>    
									</select>
								</div>	
								<div class="col-md-3">
								  <label>Hub</label>
									<select ng-model="productHub"  id="productHub" name="product_hub" class="form-control select2me" ng-change="getBeatSortData(productHub)">
										<option selected value="">---Select Hub---</option>
										<option ng-repeat="(key,value) in hub_array " value="<%value.hub_id%>" ><%value.lp_wh_name%></option>    
									</select>
								</div>
								<div class="col-md-3">
								  <label>Beat</label>
									<select ng-model="productBeat"  id="productBeat" name="product_beat" class="form-control select2me" ng-change="getSoSortData(productBeat)">
										<option selected value="">---Select Beat---</option>
										<option ng-repeat="(key,value) in beat_array  track by $index" value="<%value.pjp_pincode_area_id%>" ><%value.pjp_name%></option>    
									</select>
								</div>				
							
								<div class="col-md-3">
								  <label>SO</label>
									<select ng-model="productSo"  id="productSo" name="product_so" class="form-control select2me" ng-change="getOutletSortData(productSo)">
										<option selected value="">---Select SO---</option>
										 <option ng-repeat="(key,value) in so_array " value="<%value.user_id%>" ><%value.Fullname%></option>     
									</select>
								</div>
								
							</div>
							<div class="row"  style="padding:15px; background-color: #ffffff;" >
								<!-- <div class="col-md-3">
								  <label>Outlet</label>
									<select ng-model="productOutlet"  id="productOutlet" class="form-control select2me" >
										<option selected value="">---Select Outlet---</option>
										 <option ng-repeat="(key,value) in outlet_array " value="<%value.legal_entity_id%>" ><%value.business_legal_name%></option>     
									</select>
								</div>	 -->
								<div class="col-md-3">
									<label>Select Date</label>
									<select ng-model="custom_date" id="custom_date" class="form-control" name="date_range" ng-change="change_date()">
										<option value="today" selected="selected">Today</option>
										<option value="yesterday">Yesterday</option>
										<option value="wtd">WTD</option>
										<option value="mtd">MTD</option>
										<option value="ytd">YTD</option>
										<option value="date_range">Date Range</option>
								    </select>
								</div>
								<div ng-show="custom_date=='date_range'">
									<div class="col-md-2">
									          <label>Start Date</label>
									          <div class="input-icon input-icon-sm right">
									          <i class="fa fa-calendar"></i>
									          <input type="text" ng-model="start_date" name="start_date" id="start_date" class="form-control" value="">
									          </div>
									 </div> 
									 <div class="col-md-2">
									          <label>End Date</label>
									          <div class="input-icon input-icon-sm right">
									          <i class="fa fa-calendar"></i>
									          <input type="text" ng-model="end_date" name="end_date" id="end_date" class="form-control" value="">
									          </div>
									 </div> 
								</div>
							  
								
								<div class="col-md-2">
										<button type="button" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut" 
										ng-click="loadProductAnalysis(productDC,productHub,productBeat,'NULL',productSo,start_date,end_date)">Submit</button>
								</div>
								<div class="col-md-2">
										<button type="submit" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut">Export</button>
								</div>
							</div>
						</div>
						{{ Form::close() }}

							<div class="row" style="padding: 15px;background-color: #ffffff;">
								<div class="loader" loading></div>
								<span id="nodata" style="text-align: center;">No Data Found!!</span>

								<div id="myGrid" style="height:620px">
									<table id="Product_result_grid"></table>
								</div>
							</div>
				</div>
			</div>

			<div style="background-color: #f7f7f7;padding: 20px;">

				<div class="row" style="background-color: #ffffff; padding-top: 15px; padding-bottom: 15px;">
					<div class="col-md-3" style="font-weight: 600!important;">New Customers</div>
					<div class="col-md-8"></div>
					<div class="col-md-1" style="padding-left: 67px;"><a href="javascript:void(0);" id="CustFilter" class="CustFilter"><i class="fa fa-filter fa-lg"></i></a></div>
				</div>
					<div class="row" id="cust_filter" style="display: none;padding-bottom: 15px;background-color: #ffffff">
						<div class="col-md-3">
								<label>Select Date</label>
								<select id="dashboard_filter_dates" ng-model="dashboard_filter_dates" class="form-control" style="padding-bottom: 10px;" ng-change="customer_date_change()" >
									<option value="today" selected="selected">Today</option>
									<option value="yesterday">Yesterday</option>
									<option value="wtd">WTD</option>
									<option value="mtd">MTD</option>
									<option value="ytd">YTD</option>
									<option value="Customer_date_range">DateRange</option>
							    </select>
						</div>

						<div ng-show="dashboard_filter_dates=='Customer_date_range'">
									<div class="col-md-2">
									          <label>Start Date</label>
									          <div class="input-icon input-icon-sm right">
									          <i class="fa fa-calendar"></i>
									          <input type="text" ng-model="customer_start_date" name="customer_start_date" id="customer_start_date" class="form-control" value="">
									          </div>
									 </div> 
									 <div class="col-md-2">
									          <label>End Date</label>
									          <div class="input-icon input-icon-sm right">
									          <i class="fa fa-calendar"></i>
									          <input type="text" ng-model="customer_end_date" name="customer_end_date" id="customer_end_date" class="form-control" value="">
									          </div>
									 </div> 
								</div>

						<button onClick="getNewOnboardOutletsList()" class="btn green-meadow subBut" style="margin-top: 23px;">Go</button>
					</div>

					<div class="row" style="padding:3px; margin-bottom: 15px;background-color: #ffffff">
						<div class="col-md-12"  id="dashboard_new_customer" style="padding-bottom: 20px;">
							<div   style="overflow-y: auto; overflow-x:auto;height: 550px;">
								<div class="tab-pane active" id="new_onboard_outlets_list_tab">
									<div id="data_render_outlet" style="display:none;"></div>
									<table id="dashboard_new_onboard_outlets_list"></table>
								</div>
							</div>
						</div>
					</div>

			</div>
				
			</div>
		</div>
	</div>
	
	

@stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript" />
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">

@stop



@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<!--<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
 <script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script> -->
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/angular-fusioncharts.min.js') }}"></script>
<script src="https://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>


<script type="text/javascript">

 $('.multi-select-search-box').SumoSelect({search: true});
 	var start = new Date();
    var end = new Date();

    $('#start_date').datepicker({
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_date').datepicker('setStartDate', stDate);
    });

    $('#end_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        $('#start_date').datepicker('setEndDate', new Date($(this).val()));
    });

     $('#start_chart_date').datepicker({
        endDate: end,
        autoclose: true,
        dateFormat: 'yy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_chart_date').datepicker('setStartDate', stDate);
    });

    $('#end_chart_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        dateFormat: 'yy-mm-dd'
    }).on('changeDate', function () {
        $('#start_chart_date').datepicker('setEndDate', new Date($(this).val()));
    });

    $('#start_sales_date').datepicker({
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_sales_date').datepicker('setStartDate', stDate);
    });

    $('#end_sales_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        $('#start_sales_date').datepicker('setEndDate', new Date($(this).val()));
    });


	$('#customer_start_date').datepicker({
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#customer_end_date').datepicker('setStartDate', stDate);
    });

    $('#customer_end_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        $('#customer_start_date').datepicker('setEndDate', new Date($(this).val()));
    });


	var app = angular.module('KPIReports', ['ui.grid','ui.grid.pagination', 'ui.grid.exporter','ui.grid.pinning'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.controller('ProductAnalysisController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {

		$scope.dashboard_filter_dates='today';
	    $scope.custom_chart_date='today';
	    $scope.sales_team_date='today';

		$scope.change_chart_date=function(){			  

			$scope.start_chart_date=null;
			$scope.end_chart_date=null;

		}

		$scope.change_sales_date=function(){
 
			$scope.start_sales_date=null;
			$scope.end_sales_date=null;

		}

		$scope.customer_date_change=function(){
			 
			$scope.customer_start_date=null;
			$scope.customer_end_date=null;			

		}


			$scope.custom_date="today";
			$scope.DcList = angular.fromJson(<?php echo $DC_list;?>);
			$scope.dc_list_data = [];
			angular.forEach($scope.DcList, function(value,key){
				$scope.dc_list_data.push(value);
			});

			$scope.hubList = angular.fromJson(<?php echo $HUB_list;?>);
			$scope.hublist_data = [];
			$scope.hub_array = [];

			/*angular.forEach($scope.hubList,function(value,key){
				$scope.hublist_data.push(value);
			});*/
			
			$scope.beatList = angular.fromJson(<?php echo $Beat_List;?>);
			$scope.beatlist_data = [];
			$scope.beat_array = [];			
			/*angular.forEach($scope.beatList,function(value,key){
				$scope.beatlist_data.push(value);
				
			});*/
			
			/*$scope.outletList = angular.fromJson(<?php echo $outlet_List;?>);
			$scope.outletlist_data = [];
			$scope.outlet_array = [];

			angular.forEach($scope.outletList,function(value,key){
				$scope.outletlist_data.push(value);	
			});*/
			$scope.soList = angular.fromJson(<?php echo $so_List;?>);
			$scope.solist_data = [];
			/*$scope.so_array = [];

			angular.forEach($scope.soList,function(value,key){
				$scope.solist_data.push(value);	
			});*/
			$scope.dchubList = angular.fromJson(<?php echo $dc_hub_data;?>);
			$scope.dc_hub_list_data = [];
			$scope.dc_hub_array = [];
			
			angular.forEach($scope.dchubList,function(value,key){
				$scope.dc_hub_list_data.push(value);	
			});
			
			

		$scope.loadSalesTrends=function(dc,hub,startDate,endDate,dateRange){
		
			
			var diffDays=0;
			if(dateRange=='wtd'){
	 
				$scope.custom_chart_date= 'wtd';
	 
			}

			if($scope.custom_chart_date=='chart_date_range'){
				var serviceFromDate = angular.element(document.querySelector('#start_chart_date')).val();
	    		var serviceToDate = angular.element(document.querySelector('#end_chart_date')).val();
	    					console.log(serviceFromDate);
	    					console.log(serviceToDate);

	    		//serviceFromDate = serviceFromDate.split("-").reverse().join("-");
	          	//serviceToDate = serviceToDate.split("-").reverse().join("-");
	       		diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
	       		console.log(diffDays);
       		}

       		console.log(diffDays);

			var data={
				chart_dc:dc,
				chart_hub:hub,
				chart_from:startDate,
				chart_to:endDate,
				date_range:dateRange,
				diff:diffDays
			};

			$scope.SalesDataPie = [];
        	$scope.salesCategoryList = [];
        	$scope.SalesDataForm = [];
        	$scope.salesDatasetData = [];
        	$scope.returnCategoryList=[];
        	var returnCheckData=[];
        	var returnTempChck=[];



			var req=$http.post('/kpi/salestrends',data);
			req.success(function(successCallback){
				if(successCallback.status=='Success'){
					angular.forEach(successCallback.data,function(succValues,succKeys){
						for(var i=0;i<= succValues.length-1;i++){
							if(succValues[i].hub!='Grand Total'||succValues[i].hub === -1){	
								returnCheckData[succValues[i].hub] = new Array();							
							}else{
								var tempp=succValues[i].values;
								for(j=0;j<=tempp.length-1;j++){
									angular.forEach(tempp[j],function(valuess,keyss){
										returnTempChck[keyss]=new Array();
									});
								}
							}
						}
					});
					angular.forEach(successCallback.data, function(succValues, succKeys){
        				if(succKeys == 'Total'){
        					angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
        						angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
        							var contsPie = {
							            label: inSecSuccKeys,
							            value: inSecSuccValue.count,
							            displayValue: inSecSuccValue.count_value
								    };
								    $scope.SalesDataPie.push(contsPie);
        						});
        					});
        				}else{
        					var categoryTemp = {
        						"label" : succKeys
        					};
        					$scope.salesCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						if(seriesValues.hub != 'Grand Total'){
        							var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
        							temp = temp.replace(/[\[\]']+/g, '<br/>');
        							temp = temp.replace(/,/g, '<br/>');
        							temp = temp.replace(/tool_tip: null/g, '');
        							temp = temp.replace(/count_value/g, 'Value');
        							temp = temp.replace(/count/g, 'Count');
		            				returnCheckData[seriesValues.hub].push({
										'value':seriesValues.order_count,
										'displayValue': seriesValues.order_value,
									 	tooltext: seriesValues.hub + "{br}{br}Total Count:"
									 			+ seriesValues.order_count +
									 			"{br}Total Value:" + seriesValues.order_value 
									 			+ "{br}"  + temp
									});     
									
        						}else{
        							angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
	            							returnTempChck[finalLinekeys].push({
	            								'value':finalLineValues.count, 
	            								'displayValue':finalLineValues.count_value
	            							});
										});     								
        							});
        						}
        					});
        				}
        			});
        		}


				for(let k in returnCheckData){
        			var data = {
        				"seriesname": k,
                		"data": returnCheckData[k]
        			}
        			if(data.seriesname != "undefined"){
        				$scope.salesDatasetData.push(data);
        			}
        		}
        		for(let k in returnTempChck){
        			var data = {
        				"seriesname": k,
        				"renderAs": "line",
        				"showValues": "0",
                		"data": returnTempChck[k]
        			}
    				$scope.salesDatasetData.push(data);
        		}


        		//Sales trends summary for pie chart
        		var propertiesSalesCount={
        			type : "pie3d",
					id : "sample-chart",
					width : "100%",
					height: "400",
					renderAt: "chart-container",
					startingangle: "120",
					dataFormat:"json", 
					dataSource: {
						chart:{
						  	showlabels: "0",
							showlegend: "1",
						    enablemultislicing: "0",
						    slicingdistance: "15",
						    showpercentvalues: "1",
						    showpercentintooltip: "0",
						    borderThickness:3,
						    exportEnabled: "1",
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.SalesDataPie
					}
        		}
        		var sampleSalesChart = new FusionCharts(propertiesSalesCount);
        		sampleSalesChart.render();

        		//sales trends summary for stack chart
        		var stackSalesCountObject = {
        			type: 'stackedColumn3DLine',
			        renderAt: 'SalesStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
                			showvalues: "0",
			                exportEnabled: "1",
			        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
			                plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
			            },
			            "categories": [
			                {
			                    "category": $scope.salesCategoryList
			                }
			            ],
			            "dataset": $scope.salesDatasetData
					}
        		}

        		var salesStackedChart = new FusionCharts(stackSalesCountObject);
        		salesStackedChart.render();
			});
			req.error(function(errorCallback){
        		console.log(errorCallback);
        	});
		}

		$scope.loadProductAnalysis = function(dcId, hubId,beatId,outletId,SOId){

			console.log("In product Analysis");

			$scope.start_date=$filter('date')($scope.start_date,"yyyy-MM-dd");
		    $scope.end_date=$filter('date')($scope.end_date,"yyyy-MM-dd");
			var data = {
				product_dc : dcId,
				product_hub : hubId,
				product_beat: beatId,
				product_outlet: outletId,
				product_so: SOId,
				product_from : $scope.start_date,
				product_to : $scope.end_date,
				date_range:$scope.custom_date
			};

			
			var req = $http.post('/kpi/productSummary', data);
			$("#product_report").addClass("disabledbutton");


			req.success(function(successCallback){

				$("#product_report").removeClass("disabledbutton");

				$scope.productReportData = successCallback;
				$scope.productData=[];				

				if($scope.productReportData.status=='true'){

					$("#myGrid").show();
					$("#nodata").hide();

					var type = "";
					var classTo = "center_align";
					var cellClass = "center_align";
					var decimalreg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
    				var format =  "";
    				var productAnalysisHeader=[];
    				var productColumns=[];
					$scope.productReportList = $scope.productReportData.data;
					
					angular.forEach($scope.productReportList[0], function(productValueList,productKeyList){
						var headerKey = productKeyList;
       					headerKey = headerKey.replace("_", " ");
						var result=decimalreg.test(productValueList);
						var width=150;
						var columnCssClass="";
						var headerCssClass="";
						type= typeof(productValueList);
						if(typeof(productValueList)==='number'||result){
							columnCssClass="product_right_align";
							headerCssClass="product_header_right_align";
							width=130;
							type="number";
							format="";
							if(productKeyList=='CFC_Sold'||productKeyList=='TBV'||productKeyList=='TBV_Contrib'||productKeyList=='TGM'||productKeyList=='TGM_Contrib'){
								format="0.00";
							}
							allowSummaries=true;
							if(productKeyList=='Product_ID'||productKeyList=='Brand_ID'||productKeyList=='Manafacture_ID'||productKeyList=='Hub_ID'||productKeyList=='DC'||productKeyList=='Category_ID'||productKeyList=='MRP'||productKeyList=='KVI'||productKeyList=='MI'||productKeyList=='DI'||productKeyList=='CI'){

								productColumns.push({columnKey:productKeyList,allowSummaries:false});
							}else{
							productColumns.push({columnKey:productKeyList,allowSummaries:true,summaryOperands: [{ "rowDisplayLabel": "SUM", "type": "SUM", "active": true}]});
							}


						}else{
							format="";
							columnCssClass="product_center_align";
							headerCssClass="product_header_center_align";
							productColumns.push({columnKey:productKeyList,allowSummaries:false});

						}
						var dateResult=dateReg.test(productValueList);
						if(dateResult){
							type="date";
							format="dd-MM-yyyy";
							width=125;
							columnCssClass="product_center_align";
							headerCssClass = "product_header_center_align";
						}
						
						productAnalysisHeader.push({
							key:productKeyList,
							headerText:headerKey,
							width:width,
							dataType:type,
							format:format,
							columnCssClass:columnCssClass,
							headerCssClass:headerCssClass

						});
					});

					console.log("productAnalysisHeader");
					console.log(productColumns);

					$('#Product_result_grid').igGrid({
						dataSource: $scope.productReportList,
		                 dataSourceType: "json",
		                 width: "100%",
		                 columns: productAnalysisHeader,
		                 // initialDataBindDepth: 1,
		                 dataRendered: function() {


		                 	 var allData=$("#Product_result_grid").data("igGrid").dataSource.data();
					  // alert(JSON.stringify(allData));
 
		                 },
		                 features:[
		                 	{
		                 		name: "Filtering",
		                        type:"local",
		                        allowFiltering: true,
		                        caseSensitive: false
		                    },
		                    {
		                        name: 'Sorting',
		                        type: "local",
		                    },
		                    {
					            name : 'Paging',
					            type: "local",
					            pageSize : 25,
					        },
					        {
					        	name:'Summaries',
					        	columnSettings: productColumns,

					        }
					    ],
					    width: '100%',
				        height: '500px',
				        defaultColumnWidth: '100px'

					});
					
					console.log(productColumns);
				}else{
					$("#myGrid").hide();
					$("#nodata").show();
					//$("#Product_result_grid").hide();
				}
			});
			req.error(function(errorCallback){

				$("#product_report").removeClass("disabledbutton");
				alert(JSON.stringify(errorCallback));

			});
		}

		$scope.loadSalesByPeriodChart=function(dc,fromdate,todate,dateRange,ff_id){

			var diffDays=0;
			console.log("ff_nam");
			console.log(ff_id);
			
			if(dateRange=='sales_date_range'){
				var serviceFromDate = angular.element(document.querySelector('#start_sales_date')).val();
	    		var serviceToDate = angular.element(document.querySelector('#end_sales_date')).val();
	       		diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
       		};

       		$scope.start_sales_date=$filter('date')($scope.start_sales_date,"yyyy-MM-dd");
		    $scope.end_sales_date=$filter('date')($scope.end_sales_date,"yyyy-MM-dd");

		    var start=$('#start_sales_date').val();
		   
			var data={
				dc:dc,
				chart_from:$scope.start_sales_date,
				chart_to:$scope.end_sales_date,
				date_range:dateRange,
				diff:diffDays,
				ff_id:ff_id

			};
			$scope.checkingPieData = [];
        	$scope.stackCheckingDataset = [];
        	$scope.stackCheckingCategoryList = [];
        	var chckDataChecking = [];
        	var tempChckDataChecking = [];
        	$scope.checkingSumDataTemp = [];
        	$scope.keysData = [];
        	$scope.gndTotalData = [];
        	$scope.contsPie = [];
        	$scope.tempseriesname = [];
        	$scope.checkingGridDefs = [];
        	var checkingGridDefstemp = [];
			var checkingGridDefsData = [];
			var checkingGridDefsDataTest = [];
			var columncheckingKeys = [];
			$scope.checkingGridData = [];

			var salesTeamRepObj = {};
			var req=$http.post('/kpi/salesbyperiod',data);
			req.success(function(successCallback){

				$("#loadCheckersData").hide();

        		if(successCallback.status == 'Success'){
        			angular.forEach(successCallback.data, function(succValues, succKeys){ 
        				for (var i = 0; i <= succValues.length-1; i++) {
        					salesTeamRepObj[succValues[i].name]  = new Array();
    					}
        			});
					
        			angular.forEach(successCallback.data, function(succValues, succKeys){       				
        					//console.log(succValues);
        					var categoryTemp = {
        						"label" : succKeys
        					};
        						
        					$scope.stackCheckingCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						var ffName = seriesValues.name;

        						var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
    							temp = temp.replace(/[\[\]']+/g, '<br/>');
    							temp = temp.replace(/,/g, '<br/>');
    							temp = temp.replace(/tool_tip: null/g, '');
    							temp = temp.replace(/count/g, 'Count');




        						salesTeamRepObj[ffName].push(
        							{
        								'value' :seriesValues.TBV,
        								tooltext: seriesValues.name + "{br}{br}TBV:" + seriesValues.TBV  + "{br}"  + temp
        						});
        					});
        			});

        		}
        		for(let k in salesTeamRepObj){
        			var data = {
        				"seriesname": k,
                		"data": salesTeamRepObj[k]
        			}
    				$scope.stackCheckingDataset.push(data);
        		}
        		console.log($scope.stackCheckingDataset);
								

				// multi series column
				var stackCheckingProperties = {
					type: 'msline',
			        renderAt: 'sales_line_chart',
			        width: '100%',
			        height: '550',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
			                //"numberPrefix": "₹",
			                showvalues: "0",
			                rotateValues: "1",
                			placeValuesInside: "1",
                			caption: 'Sales Team',
                			yaxisname: 'Value',
                			xaxisname: 'Date',
			                exportEnabled: "1",
			        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
    						toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff"/*,
					        legendScrollBgColor : "#cccccc",
            				legendScrollBarColor: "#999999"*/
			                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
			            },
			            "categories": [
			                {
			                    "category": $scope.stackCheckingCategoryList
			                }
			            ],
			            "dataset": $scope.stackCheckingDataset
					}
				}
				var checkingStackedChart = new FusionCharts(stackCheckingProperties);
				checkingStackedChart.render();
            });
            req.error(function(errorCallback){
        	$("#loadCheckersData").show();
            });


		}

		
		$scope.getHubSortData=function(dc_id){
			
			    $('#productHub').select2("val","");
			    $('#custom_date').select2("val","");
			    $scope.hub_array=[];
				angular.forEach($scope.hubList,function(value,key){
					if(dc_id==value.dc_id){
						$scope.hub_array.push(value);
					}
				});

			//loading beat data

				$('#productBeat').select2("val","");
				$scope.beat_array=[];
				angular.forEach($scope.beatList,function(value,key){
					if(dc_id==value.le_wh_id){
						$scope.beat_array.push(value);
					}	
				});
				angular.forEach($scope.beatList,function(value,key){
					angular.forEach($scope.hub_array,function(hubvalue,hubkey){
						if(hubvalue.hub_id==value.le_wh_id){
							$scope.beat_array.push(value);
						}
					});
				});

		/*	//loading outlet data
			$('#productOutlet').select2("val","");
			$scope.outlet_array=[];
			angular.forEach($scope.outletlist_data,function(value,key){
				if(dc_id==value.dc_id){
					$scope.outlet_array.push(value);
				}
			});*/
			
			//loading so data
			$('#productSo').select2("val","");
			$scope.so_array=[];
			angular.forEach($scope.soList,function(value,key){
				if(dc_id==value.le_wh_id){
					$scope.so_array.push(value);
				}
			});
			/*angular.forEach($scope.soList,function(value,key){
				angular.forEach($scope.hub_array,function(hubsovalue,hubsokey){
					if(hubsovalue.hub_id==value.le_wh_id){
						$scope.so_array.push(value);
					}
				});
			});*/


			//console.log($scope.outlet_array);
		}
		$scope.getBeatSortData=function(hub_id){

			if(hub_id!=""){

				$('#custom_date').select2("val","");
				$('#productBeat').select2("val","");
				$scope.beat_array=[];
				angular.forEach($scope.beatList,function(value,key){
					if(hub_id==value.le_wh_id){
						$scope.beat_array.push(value);
					}
				});
				/*$('#productOutlet').select2("val","");
				$scope.outlet_array=[];
				angular.forEach($scope.outletlist_data,function(value,key){
					if(hub_id==value.hub_id){
						$scope.outlet_array.push(value);
					}
				});*/

				$('#productSo').select2("val","");
				$scope.so_array=[];
				angular.forEach($scope.soList,function(value,key){
					if(hub_id==value.le_wh_id){
						$scope.so_array.push(value);
					}
				});
			}
		else{

			$scope.getHubSortData($scope.productDC);
		}
		}
		$scope.getSoSortData=function(beat_id){

			if(beat_id!=""){
				$('#custom_date').select2("val","");
				$('#productOutlet').select2("val","");
				$scope.outlet_array=[];
				angular.forEach($scope.outletlist_data,function(value,key){
					if(beat_id==value.beat_id){
						$scope.outlet_array.push(value);
					}
				});

				$('#productSo').select2("val","");
				$scope.so_array=[];
				angular.forEach($scope.soList,function(value,key){
					if(beat_id==value.pjp_pincode_area_id){
						$scope.so_array.push(value);
					}
				});
			}else{
				$scope.getBeatSortData($scope.productHub);
			}
		}/*
		$scope.getOutletSortData=function(so_id){

			if(so_id!=""){
				$('#productOutlet').select2("val","");
				$scope.outlet_array=[];
				angular.forEach($scope.outletlist_data,function(value,key){
					if(so_id==value.rm_id){
						$scope.outlet_array.push(value);
					}
				});	

			}else{
				$scope.getSoSortData($scope.productBeat);
			}		
		}*/
		$scope.change_date=function(){

			$scope.start_date=null;
			$scope.end_date=null;

		}
		
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

$("#ShowFilter").click(function(){

	$("#sales_filter").toggle("fast",function(){});

});
$("#GridFilter").click(function(){

	$("#grid_filter").toggle("fast",function(){});

});
$("#CustFilter").click(function(){

	$("#cust_filter").toggle("fast",function(){});
	
});
$("#ShowFilterChart").click(function(){

	$('#chart_filter').toggle("fast",function(){});

})


function customigGridFeatures(customPageSize){
        return [
                    {
                        name: 'Paging',
                        type: 'local',
                        pageSize: customPageSize,
                    },
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "simple",
                        filterDialogContainment: "window",
                    },
                    {
                        name: 'Sorting',
                        type: 'local',
                        persist: false,
                    },
                    {
                        name: "Resizing",
                    },
                    {
                        name: "ColumnFixing",
                    },
                    {
                        name: "Tooltips",
                        visibility: "always",
                        showDelay: 500,
                        hideDelay: 500,
                    }
                ];
    }


$(document).ready(function () {

	getFieldForceList();
	getNewOnboardOutletsList();

});


    var load_url = (window.location.pathname == "/cnc")?"/cnc/":"/";



        function getFieldForceList()
    	{

    	var load_url = (window.location.pathname == "/kpi/productAnalysis")?"/kpi/":"/";       
        var date = $("#sales_team_date").val(); 
        var from_date=$('#start_sales_date').val();
        var to_date=$('#end_sales_date').val();



        $.ajax({
            url: load_url+'newsalesdata?date='+date+'&from_date='+from_date+'&to_date='+to_date,
            type: 'GET',
            dataType:"json",                                          
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                if(response.status){


                	$("#ff_list_tab").hide();
                	$("#table_tab").show();


                    var customFeatures = customigGridFeatures(10);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings:  [
                          
                            {columnKey: "commission", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "success_rate", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "AVG", "active": true }]},
                            {columnKey: "margin", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "order_cnt", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "calls_cnt", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "tbv", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "UOB", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "ABV", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "TLC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "ULC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "ALC", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                            {columnKey: "Contribution", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "AVG", "active": true }]},
                            {columnKey: "NAME", allowSummaries: false},
                            {columnKey: "role", allowSummaries: false},
                            {columnKey: "hub_name", allowSummaries: false},
                            {columnKey: "first_order", allowSummaries: false},
                            {columnKey: "first_call", allowSummaries: false}
                        ]
                    });
                    $('#dashboard_ff_list').igGrid({
                    dataSource: response.data,
                    autoGenerateColumns: false,
                    width:"100%",
                    columns: [
                        {headerText: 'Sales Rep', key: 'NAME', dataType: 'string',width: "250px"},
                        {headerText: 'Role', key: 'role', dataType: 'string', width: "60px"},
                        {headerText: 'Commission', key: 'commission', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "120px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: 'Hub', key: 'hub_name', dataType: 'string', width: "120px"},
                        {headerText: 'TGM', key: 'margin', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: '#Orders', key: 'order_cnt', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",format:"{0}"},
                        {headerText: 'First Order', key: 'first_order', dataType: 'string', width: "120px"},
                        {headerText: '#Calls', key: 'calls_cnt', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "90px",format:"{0}"},
                        {headerText: 'First Call', key: 'first_call', dataType: 'string', width: "120px"},
                        {headerText: 'TBV', key: 'tbv', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: 'UOB', key: 'UOB', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",format:"{0}"},
                        {headerText: 'ABV', key: 'ABV', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: 'TLC', key: 'TLC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",format:"{0}"},
                        {headerText: 'ULC', key: 'ULC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",format:"{0}"},
                        {headerText: 'ALC', key: 'ALC', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "80px",format:"{0}"},
                        {headerText: 'Contribution %', key: 'Contribution', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "120px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }},
                        {headerText: 'Success %', key: 'success_rate', dataType: 'number', columnCssClass: "alignRight", headerCssClass: "right_align", width: "120px",formatter: function(val,data){
                                    return $.ig.formatter(val,"number","0.00");
                                }}
                    ],
                    features: customFeatures
                    });
                }
                else{

                	$("#table_tab").hide();
                	$("#ff_list_tab").show();
                    $('#ff_list_tab')
                        .attr("align","center")
                        .attr("display","block")
                        .html("No data found!");               
                }
            },
            error: function() {
                $('ff_list_tab')
                    .attr("align","center")
                    .html("Oops, <b><i>Sales Team</i></b> Tab is not working. Refresh the page or try again later!.");
            }
        });

		


    }

    $("#dashboard_ff_list").on("iggriddatarendered", function (event, args) {

        $("#dashboard_ff_list_Contribution > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>Contribution %</p>");
        $("#dashboard_ff_list_success_rate > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>Success %</p>");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#dashboard_ff_list_role > span.ui-iggrid-headertext").attr('title', "Role");
        $("#dashboard_ff_list_NAME > span.ui-iggrid-headertext").attr('title', "Sales Rep");
        $("#dashboard_ff_list_hub_name > span.ui-iggrid-headertext").attr('title', "Hub");
        $("#dashboard_ff_list_TLC > span.ui-iggrid-headertext").attr('title', "Total Lines Cut");
        $("#dashboard_ff_list_tbv > span.ui-iggrid-headertext").attr('title', "Total Bill Value");
        $("#dashboard_ff_list_ULC > span.ui-iggrid-headertext").attr('title', "Unique Lines Cut");
        $("#dashboard_ff_list_ALC > span.ui-iggrid-headertext").attr('title', "Average Lines Cut");
        $("#dashboard_ff_list_calls_cnt > span.ui-iggrid-headertext").attr('title', "Calls Count");
        $("#dashboard_ff_list_first_call > span.ui-iggrid-headertext").attr('title', "First Call");
        $("#dashboard_ff_list_commission > span.ui-iggrid-headertext").attr('title', "Commission");
        $("#dashboard_ff_list_order_cnt > span.ui-iggrid-headertext").attr('title', "Orders Count");
        $("#dashboard_ff_list_ABV > span.ui-iggrid-headertext").attr('title', "Average Bill Value");
        $("#dashboard_ff_list_first_order > span.ui-iggrid-headertext").attr('title', "First Order");
        $("#dashboard_ff_list_success_rate > span.ui-iggrid-headertext").attr('title', "Success Rate");
        $("#dashboard_ff_list_Contribution > span.ui-iggrid-headertext").attr('title', "Contribution");
        $("#dashboard_ff_list_UOB > span.ui-iggrid-headertext").attr('title', "Unique Outlets Billed");
        $("#dashboard_ff_list_margin > span.ui-iggrid-headertext").attr('title', "Total Gross Margin");
        
        // Sumaries related UI changes on Dashboard
        $("#dashboard_ff_list_summaries_footer_row_icon_container_sum_tbv, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_success_rate, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_commission, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_margin, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_order_cnt, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_calls_cnt, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_tbv, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_UOB, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ABV, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_TLC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ULC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_ALC, "+
            "#dashboard_ff_list_summaries_footer_row_icon_container_sum_Contribution"
            ).remove();

        var id_text = "#dashboard_ff_list_summaries_footer_row_text_container_sum_"; 
        $(id_text+"tbv").attr("class","summariesStyle").text($(id_text+"tbv").text().replace(/\s=\s/g, ''));
        $(id_text+"success_rate").attr("class","summariesStyle").text($(id_text+"success_rate").text().replace(/\s=\s/g, ''));
        $(id_text+"commission").attr("class","summariesStyle").text($(id_text+"commission").text().replace(/\s=\s/g, ''));
        $(id_text+"margin").attr("class","summariesStyle").text($(id_text+"margin").text().replace(/\s=\s/g, ''));
        $(id_text+"order_cnt").attr("class","summariesStyle").text($(id_text+"order_cnt").text().replace(/\s=\s/g, ''));
        $(id_text+"calls_cnt").attr("class","summariesStyle").text($(id_text+"calls_cnt").text().replace(/\s=\s/g, ''));
        $(id_text+"tbv").attr("class","summariesStyle").text($(id_text+"tbv").text().replace(/\s=\s/g, ''));
        $(id_text+"UOB").attr("class","summariesStyle").text($(id_text+"UOB").text().replace(/\s=\s/g, ''));
        $(id_text+"ABV").attr("class","summariesStyle").text($(id_text+"ABV").text().replace(/\s=\s/g, ''));
        $(id_text+"TLC").attr("class","summariesStyle").text($(id_text+"TLC").text().replace(/\s=\s/g, ''));
        $(id_text+"ULC").attr("class","summariesStyle").text($(id_text+"ULC").text().replace(/\s=\s/g, ''));
        $(id_text+"ALC").attr("class","summariesStyle").text($(id_text+"ALC").text().replace(/\s=\s/g, ''));
        $(id_text+"Contribution").attr("class","summariesStyle").text($(id_text+"Contribution").text().replace(/\s=\s/g, ''));

    });




 function getNewOnboardOutletsList()
    {    
    	var load_url = (window.location.pathname == "/kpi/productAnalysis")?"/kpi/":"/";       
        var date = $("#dashboard_filter_dates").val(); 
        var fromdate=$("#customer_start_date").val();
        var todate=$("#customer_end_date").val();  
        $.ajax({
            url: load_url+'newcustomersdata?date='+date+'&from_date='+fromdate+'&to_date='+todate,
            type: 'GET',
            dataType: "json",
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
            	if(response.status){

            		$('#dashboard_new_onboard_outlets_list').show();
            		$('#dashboard_new_onboard_outlets_list_pager').show();
                    $('#dashboard_new_onboard_outlets_list').igGrid({

                    	dataType: "json",
                        width: "100%",
                        dataSource: response.data,
                        columns: [
                            {headerText: 'Customer Code', key: 'le_code', dataType: 'string', width: '200px'},
                            {headerText: 'Hub', key: 'hub', dataType: 'string', width: '120px'},
                            {headerText: 'Shop Name', key: 'business_legal_name', dataType: 'string', width: '250px'},
                            {headerText: 'Customer Type', key: 'legal_entity_type', dataType: 'string', width: '120px'},
                            {headerText: 'Segment', key: 'business_type', dataType: 'string', width: '100px'},
                            {headerText: 'Customer Name', key: 'name', dataType: 'string', width: '150px'},
                            {headerText: 'Contact', key: 'mobile_no', dataType: 'number', width: '110px'},
                            {headerText: 'Area', key: 'area', dataType: 'string', width: '110px'},
                            {headerText: 'Beat', key: 'beat', dataType: 'string', width: '90px'},
                            {headerText: 'City', key: 'city', dataType: 'string', width: '90px'},
                            {headerText: 'State', key: 'state', dataType: 'string', width: '150px'},
                            {headerText: 'PIN Code', key: 'pincode', dataType: 'number', width: '80px'},
                            {headerText: '#Orders', key: 'orders', dataType: 'number',headerCssClass: "right_align", columnCssClass: "alignRight", width: '80px'},
                            {headerText: 'Last Order Date', key: 'last_order_date', dataType: 'date', width: '100px'},
                            {headerText: 'Created Date', key: 'created_at', dataType: 'date', width: '110px'},
                            {headerText: 'Created Time', key: 'created_time', dataType: 'string', width: '110px'},
                            {headerText: 'Created By', key: 'created_by', dataType: 'string', width: '110px'}
                        ],
                        features:  [
		                    {
		                        name: 'Paging',
		                        type: 'local',
		                        pageSize: 10,
		                    },
		                    {
		                        name: "Filtering",
		                        type: "local",
		                        mode: "simple",
		                        filterDialogContainment: "window",
		                    },
		                    {
		                        name: 'Sorting',
		                        type: 'local',
		                        persist: false,
		                    },
		                    {
		                        name: "Resizing",
		                    },
		                    /*{
		                        name: "RowSelectors",
		                    },*/
		                    {
		                        name: "Selection",
		                        multipleSelection: true,
		                    },
		                    {
		                        name: "ColumnFixing",
		                    },
		                    {
		                        name: "Tooltips",
		                        visibility: "always",
		                        showDelay: 500,
		                        hideDelay: 500,
		                    },
		                    {
		                        name: "Summaries",
		                        columnSettings:  [
		                            {columnKey: "orders", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
		                            {columnKey: "NAME", allowSummaries: false},
		                            {columnKey: "le_code", allowSummaries: false},
		                            {columnKey: "hub", allowSummaries: false},
		                            {columnKey: "business_legal_name", allowSummaries: false},
		                            {columnKey: "legal_entity_type", allowSummaries: false},
		                            {columnKey: "business_type", allowSummaries: false},
		                            {columnKey: "name", allowSummaries: false},
		                            {columnKey: "mobile_no", allowSummaries: false},
		                            {columnKey: "area", allowSummaries: false},
		                            {columnKey: "beat", allowSummaries: false},
		                            {columnKey: "city", allowSummaries: false},
		                            {columnKey: "state", allowSummaries: false},
		                            {columnKey: "pincode", allowSummaries: false},
		                            {columnKey: "number", allowSummaries: false},
		                            {columnKey: "last_order_date", allowSummaries: false},
		                            {columnKey: "created_at", allowSummaries: false},
		                            {columnKey: "created_time", allowSummaries: false},
		                            {columnKey: "created_by", allowSummaries: false}
		                        ]
		                    }
		                ]

                    });
                }else{

                	console.log("In else case");

                    $('#data_render_outlet')
                        .attr("align","center")
                        .attr("display","block")
                        .html("No data found!");

                        $('#dashboard_new_onboard_outlets_list').hide('');
                		$('#dashboard_new_onboard_outlets_list_pager').hide();

                }

            },
            error: function() {
                $('data_render_outlet')
                    .attr("align","center")
                    .html("Oops, <b><i>New Customers</i></b> Tab is not working. Refresh the page or try again later!.");
            }
        });
    }
    
    $('#dashboard_new_onboard_outlets_list').on("iggriddatarendered", function (event, args) {
        $("#dashboard_new_onboard_outlets_list_orders > span.ui-iggrid-headertext").html("<p style='text-align: right !important; margin: 0px 5px !important;'>#Orders</p>");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext'  title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        $("#dashboard_new_onboard_outlets_list_hub > span.ui-iggrid-headertext").attr("title","Hub");
        $("#dashboard_new_onboard_outlets_list_name > span.ui-iggrid-headertext").attr("title","Customer Name");
        $("#dashboard_new_onboard_outlets_list_area > span.ui-iggrid-headertext").attr("title","Area Name");
        $("#dashboard_new_onboard_outlets_list_beat > span.ui-iggrid-headertext").attr("title","Beat Name");
        $("#dashboard_new_onboard_outlets_list_city > span.ui-iggrid-headertext").attr("title","City");
        $("#dashboard_new_onboard_outlets_list_state > span.ui-iggrid-headertext").attr("title","State");
        $("#dashboard_new_onboard_outlets_list_le_code > span.ui-iggrid-headertext").attr("title","Customer Code");
        $("#dashboard_new_onboard_outlets_list_pincode > span.ui-iggrid-headertext").attr("title","PIN Code");
        $("#dashboard_new_onboard_outlets_list_orders > span.ui-iggrid-headertext").attr("title","Orders");
        $("#dashboard_new_onboard_outlets_list_mobile_no > span.ui-iggrid-headertext").attr("title","Contact");
        $("#dashboard_new_onboard_outlets_list_created_at > span.ui-iggrid-headertext").attr("title","Created Date");
        $("#dashboard_new_onboard_outlets_list_created_by > span.ui-iggrid-headertext").attr("title","Created By");
        $("#dashboard_new_onboard_outlets_list_created_time > span.ui-iggrid-headertext").attr("title","Created Time");
        $("#dashboard_new_onboard_outlets_list_business_type > span.ui-iggrid-headertext").attr("title","Segment Name");
        $("#dashboard_new_onboard_outlets_list_last_order_date > span.ui-iggrid-headertext").attr("title","Last Order Date");
        $("#dashboard_new_onboard_outlets_list_legal_entity_type > span.ui-iggrid-headertext").attr("title","Customer Type");
        $("#dashboard_new_onboard_outlets_list_business_legal_name > span.ui-iggrid-headertext").attr("title","Shop Name");

        var id_text = "#dashboard_new_onboard_outlets_list_summaries_footer_row_text_container_sum_";
        $("#dashboard_new_onboard_outlets_list_summaries_footer_row_icon_container_sum_orders").remove();
        $(id_text+"orders").attr("class","summariesStyle").text($(id_text+"orders").text().replace(/\s=\s/g, ''));
    });





    

</script>
<style type="text/css">
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
}/*
.ui-icon{
	display: none;
}*/

hr{
	color:#f7f7f7;
	padding:8px;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
 }
 .disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
.right_align{
  text-align: right!important;;
 }



 .head_right_align{
  text-align: right;
    padding-right: 8px;
 }

 .grid_tool_tip{
 	overflow: visible;
	z-index: 9999999;
	float: left;
 }
  .filterInput{
 	height:21px;
 	padding-top: inherit;
 	padding-left: 21px;
 }

 .ui-grid-footer-cell{
 	text-align: right;
 }
 .ui-widget-footer{

 	text-align: right;
 }
  .center_align{
 	text-align: center;
    padding-right: 10px;
 }

 .summariesStyle{
	font-weight: bold; 
}

.alignRight{
	text-align: right;
	padding-right: 15px;
}
.summariesStyle{
	text-align: right;
	padding-right: 15px;
}
.custom-control{
 width: 10px;
    position: absolute;
    left: 5px;
    color: black;
    background: #f7f7f7;
    height: 20px;
}

#Product_result_grid{
     table-layout: auto !important;
}
#Product_result_grid_headers{
     table-layout: auto !important;
}

.product_right_align{
	text-align: right !important;
	padding-right: 45px;
}
.product_header_right_align{
  text-align: right !important;
  padding-top: 2px;
 }
 .product_center_align{
  text-align: left;
 }
 .product_header_center_align{
  text-align: left;
  padding-left: 5px;
 }
 .ui-iggrid-summaries-footer-text-container{
 	font-weight: bold;
 	padding-right:43px;
 }
/*.ui-iggrid-summaries-footer-text-container {
    margin-left: 35px;
}*/
</style>
@stop
@extends('layouts.footer')