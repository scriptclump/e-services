@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Expenses Details Report'); ?>
<div class="portlet light tasks-widget">
		<div class="portlet-title">
			<div class="caption">Expenses Details Report</div>
			<div class="actions">
				<a href="javascript:void(0);" id="ShowFilter" class="ShowFilter"><i class="fa fa-filter fa-lg"></i></a>
			</div>
		</div>
		<div  class="portlet-body" id="expenses_report" ng-app="KPIReports" ng-controller="ExpensesDetailsController"  ng-init="loadExpensesDetails('NULL')" >
			
		<div id="expense_filter" class="expense_filter" style="display: none;">
			<div class="row" style="padding:15px;">
				<div class="col-md-3">
				  	<label>Business Unit</label>
					<select ng-model="expenseDC"  id="expenseDC"  class="form-control select2me" >
						<option selected value="">---Select BU---</option>	
						<option selected value="NULL">ALL</option>	
						<option ng-repeat="(key,value) in dc_Expenselist_data " value="<%value.bu_id%>"><%value.bu_name%></option>    				
					</select>
				</div>	
				
				<div class="col-md-3">
					<label>Select Date</label>
					<select ng-model="custom_expense_date" id="custom_expense_date" class="form-control" ng-change="change_expense_date()">
						<option value="today" selected="selected">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="wtd">WTD</option>
						<option value="mtd">MTD</option>
						<option value="ytd">YTD</option>
						<option value="std">STD</option>
						<option value="expense_date_range">Date Range</option>
				    </select>
				</div>
				<div class="custom_range" id="custom_range" style="display:none">
					<div class="col-md-2">
					          <label>Start Date</label>
					          <div class="input-icon input-icon-sm right">
					          <i class="fa fa-calendar"></i>
					          <input type="text" ng-model="start_expense_date" name="start_expense_date" id="start_expense_date" class="form-control" value="">
					          </div>
					 </div> 
					 <div class="col-md-2">
					          <label>End Date</label>
					          <div class="input-icon input-icon-sm right">
					          <i class="fa fa-calendar"></i>
					          <input type="text" ng-model="end_expense_date" name="end_expense_date" id="end_expense_date" class="form-control" value="">
					          </div>
					 </div> 
				</div>
				<div class="col-md-2">
						<button type="button" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut" 
						ng-click="loadExpensesDetails(expenseDC)">Submit</button>
				</div>
			</div>
		</div>
			<div class="row" style="padding: 15px">
				<div class="loader" loading></div>
				<span id="nodata" >No Data Found!</span>

				<div id="myGrid" style="height:800px">
					<table id="expense_result_grid"></table>
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

    $('#start_expense_date').datepicker({
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_expense_date').datepicker('setStartDate', stDate);
    });

    $('#end_expense_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        $('#start_expense_date').datepicker('setEndDate', new Date($(this).val()));
    });



   /* $("#start_expense_date").datepicker({
        maxDate:'0',
        dateFormat:'dd-mm-yy',
        onSelect: function (selected) {
            var dt = new Date(selected);
            $("#end_expense_date").datepicker("option", "minDate", dt);
        }
    });
    $("#end_expense_date").datepicker({
    	dateFormat:'dd-mm-yy',
        onSelect: function (selected) {
             var dt = new Date(selected);
             $("#start_expense_date").datepicker("option", "maxDate", dt);
        }
    });*/


	var app = angular.module('KPIReports', ['ui.grid','ui.grid.pagination', 'ui.grid.exporter'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.controller('ExpensesDetailsController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {

		$scope.custom_expense_date="today";
		$scope.DcExpenseList = angular.fromJson(<?php echo $DC_list;?>);
		$scope.dc_Expenselist_data = [];
		angular.forEach($scope.DcExpenseList, function(value,key){
			$scope.dc_Expenselist_data.push(value);
			});
			
		$scope.loadExpensesDetails = function(dcId){

			$startPeriod=$('#start_expense_date').val();
			$endPeriod=$('#end_expense_date').val();

			var data = {
				expense_dc : dcId,		
				expense_from : $startPeriod,
				expense_to : $endPeriod,
				expense_date_range:$scope.custom_expense_date
			};		

			
			var req = $http.post('/kpi/expensesData', data);
			$("#expenses_report").addClass("disabledbutton");


			req.success(function(successCallback){

				$("#expenses_report").removeClass("disabledbutton");


				$scope.expenseReportData=successCallback;
				$scope.expenseData=[];
				if($scope.expenseReportData.status=='true'){
					$("#myGrid").show();
					$("#nodata").hide();
					var type = "";
					var classTo = "center_align";
					var cellClass = "center_align";
					var decimalreg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
    				var format =  "";
    				var expenseAnalysisHeader=[];
    				var expenseColumns=[];
					$scope.expenseReportDataList = $scope.expenseReportData.data;

					console.log($scope.expenseReportDataList);

					angular.forEach($scope.expenseReportDataList[0],function(expenseValueList,expenseKeyList){
						var headerKey = expenseKeyList;
      			 		headerKey = headerKey.replace("_", " ");

						var result=decimalreg.test(expenseValueList);
						var width=150;
						var columnCssClass="";
						var headerCssClass="";
						type= typeof(expenseValueList);
						if(typeof(expenseValueList)==='number'||result){
							columnCssClass="expense_right_align";
							headerCssClass="expense_header_right_align";
							width=130;
							type="number";
							format="";
							allowSummaries=true;
							expenseColumns.push({columnKey:expenseKeyList,allowSummaries:true,summaryOperands: [{ "rowDisplayLabel": "SUM", "type": "SUM", "active": true}]});


						}else{
							format="";
							columnCssClass="expense_center_align";
							headerCssClass="expense_header_center_align";
							expenseColumns.push({columnKey:expenseKeyList,allowSummaries:false});

						}
						var dateResult=dateReg.test(expenseValueList);
						if(dateResult){
							type="date";
							format="dd-MM-yyyy";
							width=125;
							columnCssClass="expense_center_align";
							headerCssClass = "expense_header_center_align";
						}
						
						expenseAnalysisHeader.push({
							key:expenseKeyList,
							headerText:headerKey,
							width:width,
							dataType:type,
							format:format,
							columnCssClass:columnCssClass,
							headerCssClass:headerCssClass

						});
					});

					console.log("expenseAnalysisHeader");

					$('#expense_result_grid').igGrid({
						dataSource: $scope.expenseReportDataList,
		                 dataSourceType: "json",
		                 width: "100%",
		                 columns: expenseAnalysisHeader,
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
					       /* {
					        	name:'ColumnFixing'
					        },
					        {
					        	name:'ColumnMoving'
					        },*/
					        {
					        	name:'Summaries',
					        	columnSettings: expenseColumns,

					        }
					    ],
					    width: '100%',
				        height: '700px',
				        defaultColumnWidth: '100px'

					});
					
					console.log(expenseColumns);
				}else{
					$("#myGrid").hide();
					$("#nodata").show();
				}
			});
			req.error(function(errorCallback){
				$("#expenses_report").removeClass("disabledbutton");
				console.log(errorCallback);
			});

		}
			
			$scope.change_expense_date=function(){
			var data = $('#custom_expense_date').val();

			if(data=='expense_date_range'){
				$scope.start_expense_date=null;
				$scope.end_expense_date=null;
				$('#custom_range').show();
			}
			else{
				$('#custom_range').hide();
			}

		}

	});
	$("#ShowFilter").click(function(){
	$("#expense_filter").toggle("fast",function(){});
});
</script>
<style type="text/css">
 .disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}

 .ui-grid-footer-cell{
 	text-align: right;
 }
  .filterInput{
 	height:21px;
 	padding-top: inherit;
 	font-size: 13px;
    padding-left: 1px;
 }
 .ui-iggrid-summaries-footer-text-container {
    margin-left: 35px;
}

#expense_result_grid{
     table-layout: auto !important;
}
#expense_result_grid_headers{
     table-layout: auto !important;
}

.expense_right_align{
	text-align: right !important;
	padding-right: 45px;
}
.expense_header_right_align{
  text-align: right !important;
  padding-top: 2px;
 }
 .expense_center_align{
  text-align: left;
 }
 .expense_header_center_align{
  text-align: left;
  padding-left: 5px;
 }
</style>
@stop
@extends('layouts.footer')