@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Inventory Analysis Report'); ?>
<div class="portlet light tasks-widget">
		<div class="portlet-title">
			<div class="caption">Inventory Analysis Report</div>
			<div class="actions">
				<a href="javascript:void(0);" id="ShowFilter" class="ShowFilter"><i class="fa fa-filter fa-lg"></i></a>
			</div>
		</div>
		<div  class="portlet-body" id="inventory_report" ng-app="KPIReports" ng-controller="InventoryAnalysisController"  ng-init="loadInventoryDetails('NULL')" >

			<div id="inventory_filter" class="inventory_filter" style="display: none;">
			<div class="row" style="padding:15px;">
				<div class="col-md-3">
				  	<label>DC</label>
					<select ng-model="inventoryDC"  id="inventoryDC"  class="form-control select2me" >
						<option selected value="">---Select DC---</option>	
						<option ng-repeat="(key,value) in dc_Inventorylist_data " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>     				
					</select>
				</div>	
				
				<div class="col-md-3">
					<label>Select Date</label>
					<select ng-model="custom_inventory_date" id="custom_inventory_date" class="form-control" ng-change="change_inventory_date()">
						<option value="today" selected="selected">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="wtd">WTD</option>
						<option value="mtd">MTD</option>
						<option value="ytd">YTD</option>
						<option value="std">STD</option>
						<option value="inventory_date_range">Date Range</option>
				    </select>
				</div>
				<div class="custom_range" id="custom_range" style="display:none">
					<div class="col-md-2">
					          <label>Start Date</label>
					          <div class="input-icon input-icon-sm right">
					          <i class="fa fa-calendar"></i>
					          <input type="text" ng-model="start_inventory_date" name="start_inventory_date" id="start_inventory_date" class="form-control" value="">
					          </div>
					 </div> 
					 <div class="col-md-2">
					          <label>End Date</label>
					          <div class="input-icon input-icon-sm right">
					          <i class="fa fa-calendar"></i>
					          <input type="text" ng-model="end_inventory_date" name="end_inventory_date" id="end_inventory_date" class="form-control" value="">
					          </div>
					 </div> 
				</div>
				<div class="col-md-2">
						<button type="button" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut" 
						ng-click="loadInventoryDetails(inventoryDC)">Submit</button>
				</div>
			</div>
		</div>
			<div class="row" style="padding: 15px">
				<div class="loader" loading></div>
				<span id="nodata" >NO Data Found!!</span>

				<div id="myGrid" style="height:800px">
					<table id="inventory_result_grid"></table>
			    </div>
			</div>
		</div>
	</div>
@stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">

@stop

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script type="text/javascript">

	var start = new Date();
    var end = new Date();

    $('#start_inventory_date').datepicker({
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_inventory_date').datepicker('setStartDate', stDate);
    });

    $('#end_inventory_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        $('#start_inventory_date').datepicker('setEndDate', new Date($(this).val()));
    });

	var app = angular.module('KPIReports', ['ui.grid','ui.grid.pagination', 'ui.grid.exporter','ui.grid.pinning'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.controller('InventoryAnalysisController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {

		$scope.custom_inventory_date="today";
		$scope.DcInventoryList = angular.fromJson(<?php echo $DC_list;?>);
		$scope.dc_Inventorylist_data = [];
		angular.forEach($scope.DcInventoryList, function(value,key){
			$scope.dc_Inventorylist_data.push(value);
		});

		$scope.loadInventoryDetails = function(dcId){

			$startPeriod=$('#start_inventory_date').val();
			$endPeriod=$('#end_inventory_date').val();

			var data ={
				inventory_Dc:dcId,
				inventory_from:$startPeriod,
				inventory_to:$endPeriod,
				inventory_date_range:$scope.custom_inventory_date
			};
			
			var req = $http.post('/kpi/inventoryData', data);
			$("#inventory_report").addClass("disabledbutton");

			req.success(function(successCallback){

				$("#inventory_report").removeClass("disabledbutton");
				$scope.inventoryReportData=successCallback;
				$scope.inventoryData=[];
				if($scope.inventoryReportData.status=='true'){
					$("#myGrid").show();
					$("#nodata").hide();
					var type = "";
					var classTo = "center_align";
					var cellClass = "center_align";
					var decimalreg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
    				var format =  "";
    				var inventoryAnalysisHeader=[];
    				var inventoryColumns=[];
					$scope.inventoryReportDataList = $scope.inventoryReportData.data;

					console.log($scope.inventoryReportDataList);

					angular.forEach($scope.inventoryReportDataList[0],function(inventoryValueList,inventoryKeyList){
						var headerKey = inventoryKeyList;
      			 		headerKey = headerKey.replace("_", " ");

						var result=decimalreg.test(inventoryValueList);
						var width=150;
						var columnCssClass="";
						var headerCssClass="";
						type= typeof(inventoryValueList);
						if(typeof(inventoryValueList)==='number'||result){
							columnCssClass="inventory_right_align";
							headerCssClass="inventory_header_right_align";
							width=150;
							type="number";
							format="";
							allowSummaries=true;
							inventoryColumns.push({columnKey:inventoryKeyList,allowSummaries:true,summaryOperands: [{ "rowDisplayLabel": "SUM", "type": "SUM", "active": true}]});


						}else{
							format="";
							columnCssClass="inventory_center_align";
							headerCssClass="inventory_header_center_align";
							inventoryColumns.push({columnKey:inventoryKeyList,allowSummaries:false});

						}
						var dateResult=dateReg.test(inventoryValueList);
						if(dateResult){
							type="date";
							format="dd-MM-yyyy";
							width=125;
							columnCssClass="inventory_center_align";
							headerCssClass = "inventory_header_center_align";
						}
						
						inventoryAnalysisHeader.push({
							key:inventoryKeyList,
							headerText:headerKey,
							width:width,
							dataType:type,
							format:format,
							columnCssClass:columnCssClass,
							headerCssClass:headerCssClass

						});
					});

					console.log("inventoryAnalysisHeader");

					$('#inventory_result_grid').igGrid({
						dataSource: $scope.inventoryReportDataList,
		                 dataSourceType: "json",
		                 width: "100%",
		                 columns: inventoryAnalysisHeader,
		                 // initialDataBindDepth: 1,
		                 dataRendered: function() { 
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
					        	name:'ColumnFixing'
					        },
					        {
					        	name:'ColumnMoving'
					        },
					        {
					        	name:'Summaries',
					        	columnSettings: inventoryColumns,

					        },{
					            name: "Tooltips",
					            columnSettings: [
					                { columnKey: "Purchase_Return", allowTooltips: true }
					                
					            ],
					            visibility: "always",
					            showDelay: 1000,
					            hideDelay: 500
					        }
					    ],
					    width: '100%',
				        height: '620px',
				        defaultColumnWidth: '100px'

					});
					
					console.log(inventoryColumns);
				}else{
					$("#myGrid").hide();
					$("#nodata").show();
				}

			});
			req.error(function(errorCallback){
				$("#inventory_report").removeClass("disabledbutton");
				console.log(errorCallback);
			});

		}

    $scope.change_inventory_date=function(){
			var data = $('#custom_inventory_date').val();
			if(data=='inventory_date_range'){
				$scope.start_inventory_date=null;
				$scope.end_inventory_date=null;
				$('#custom_range').show();
			}
			else{
				$('#custom_range').hide();
			}

		}

	});
	$("#ShowFilter").click(function(){
	$("#inventory_filter").toggle("fast",function(){});
});
	
</script>
<style type="text/css">
 .disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
.right_align{
  text-align: right;
    padding-right: 10px;
 }
 .head_right_align{
  text-align: right;
    padding-right: 8px;
 } 
 .ui-grid-footer-cell{
 	text-align: right;
 } 
  .filterInput{
 	height:21px;
 	padding-top: inherit;
 }

 .ui-iggrid-summaries-footer-text-container {
    margin-left: 25px;
    font-size: .9em;
    font-weight: bold;
    padding-right: 43px;
}

#inventory_result_grid{
     table-layout: auto !important;
}
#inventory_result_grid_headers{
     table-layout: auto !important;
}

.inventory_right_align{
	text-align: right !important;
	padding-right: 45px;
}
.inventory_header_right_align{
  text-align: right !important;
  padding-top: 2px;
 }
 .inventory_center_align{
  text-align: left;
 }
 .inventory_header_center_align{
  text-align: left;
  padding-left: 5px;
 }
</style>
@stop
@extends('layouts.footer')
