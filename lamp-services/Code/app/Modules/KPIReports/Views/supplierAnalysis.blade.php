@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Supplier Analysis'); ?>

<div class="portlet light tasks-widget">
	<div class="portlet-title">
		<div class="caption">
		Supplier Analysis Report
		</div>
		<div class="actions">
			
		</div>
	</div>
	<div class="portlet-body" ng-app="KPIReports" ng-controller="supllierController" >
		<div class="row" style="padding: 15px">

			<div class="col-md-3">
				<label>DC</label>
				<select ng-model="dc"  id="dc"  class="form-control">
					<option selected value="">--Select Dc--</option>
					<option ng-repeat="(key,value) in dc_list_data " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>    
				</select>
			</div>

			<div class="col-md-2">
			<label>Select Date  </label>
				<select ng-model="custom_date" id="custom_date" class="form-control" ng-change="change_date()">
					<option value="today" selected="selected">Today</option>
					<option value="yesterday">Yesterday</option>
					<option value="wtd">WTD</option>
					<option value="mtd">MTD</option>
					<option value="ytd">YTD</option>
					<option value="std">STD</option>
					<option value="custom_date">Date Range</option>

				</select>
			</div>
			<div id="date_show" style="display: none;">
				<div class="col-md-3">
					<label>Start Date</label>
					<div class="input-icon input-icon-sm right">
						<i class="fa fa-calendar"></i>
						<input type="text" ng-model="from_date" name="from_date" id="from_date" class="form-control" value="">
					</div>
				</div> 
				<div class="col-md-3">
					<label>End Date</label>
					<div class="input-icon input-icon-sm right">
						<i class="fa fa-calendar"></i>
						<input type="text" ng-model="to_date" name="to_date" id="to_date" class="form-control" value="">
					</div>
				</div>
			</div>
			

			<div class="col-md-1" style="margin-top: 22px;">
				<button type="submit" value="Submit"  ng-click="getSuppliers()"  class="btn green-meadow">Submit</button>
			</div>
		</div>				
		<div class="row" style="padding: 15px;height: 30px;">
			<div class="loader" loading></div>
			<div  style="height: 500px;" ng-init="supplierInit()">
				<table id="supplierGrid"></table>
			</div>
		</div>
	</div>
</div>



@stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
@stop

@section('script')

<!-- Angular Material requires Angular.js Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>


<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-animate.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-aria.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-messages.min.js"></script>

<!-- Angular Material Library -->
<script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.js"></script>
  

<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>

@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}

<script type="text/javascript">

	var start = new Date();
    var end = new Date();

    $('#from_date').datepicker({
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#to_date').datepicker('setStartDate', stDate);
    });

    $('#to_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        $('#from_date').datepicker('setEndDate', new Date($(this).val()));
    });
	var app = angular.module('KPIReports', 
		['ui.grid','ui.grid.pagination', 'ui.grid.exporter','ngMaterial','ngMessages'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.controller('supllierController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {

		$scope.DcList = angular.fromJson(<?php echo $dc_list;?>);
                angular.forEach($scope.DcList, function(value,key){
                    $scope.dc = value.le_wh_id;
                    return;
               });
		$scope.dc_list_data = [];
		angular.forEach($scope.DcList, function(value,key){
			$scope.dc_list_data.push(value);
		});

		$scope.gridOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 15,
        	enableGridMenu: true,
        	showColumnFooter: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
			columnDefs: [
      			{field: 'S No',enableFiltering: true,width:'50'},
				{ field: 'Supplier Name', width: '140', enableFiltering: true,aggregationType: uiGridConstants.aggregationTypes.sum ,footerCellClass: "right_align" },
				{ field: 'Supplier Type', width: '140' },
				{ field: 'Credit Period', width: '140' },
				{ displayName: 'TPV', field: 'TPV', 
					width: '140' ,cellClass: "right_align",headerCellClass: 'right_align',aggregationType: uiGridConstants.aggregationTypes.avg,footerCellClass: "right_align",type:'number',footerCellFilter: 'number:2'
				},
				{ field: 'Last Purchased', width: '140',type:'date'},
				{ field: 'Outstanding', width: '140', aggregationType: uiGridConstants.aggregationTypes.avg,cellClass: "right_align",headerCellClass: 'right_align',footerCellClass: "right_align",type:'number' ,footerCellFilter: 'number:2'},
				{ displayName: 'GRN Errors',field: 'GRN Errors', width: '140' },
				{ field: 'Contact'   }

				],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};


		$scope.supplierInit = function(){
			var date = new Date();
			$scope.custom_date = "today";
			//$scope.dc = "4497";
			$scope.to_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.from_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.getSuppliers();
		}


		$scope.getSuppliers = function(){
			

			$scope.post_data = {
				to_date:$scope.to_date,
				start_date:$scope.from_date,
				custom_date:$scope.custom_date,
				dc:$scope.dc

			}

			var req = $http.post('getSuppliers',$scope.post_data);
			req.success(function(successCallback){
				$scope.supplierdata = successCallback;
				$scope.supplierListData = [];
				if($scope.supplierdata.status == "true"){

					var type = "";
					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{4}\-\d{2}\-\d{2}$/;
    				var format =  "";
    				var supplierGridHeader = [];

					$scope.supplierList = $scope.supplierdata.data;
					angular.forEach($scope.supplierList[0],function(val,key){
						var result = reg.test(val);
                        var width = 150;
                        var columnCssClass = "";
                        var headerCssClass = "";
                        var headerKey = key;
						headerKey = headerKey.replace(/_/g, " ");
        				type = typeof(val);
        				if(type === "number" || result){
        					 columnCssClass = "right_align";
        					 headerCssClass = "header_right_align";
                             width = 130;
                             type = 'number';
                             format = "0.00";      
        				}else{
        					format = "";
        					columnCssClass = "center_align";        					
        					headerCssClass = "header_center_align";
        				}
        				var dateResult = dateReg.test(val);

        				if(dateResult){
							type = "date";
							format = "dd/MM/yyyy";
                            width = 125;
                            columnCssClass = "center_align";
        					headerCssClass = "header_center_align";
        				}
        				
        				supplierGridHeader.push({key:key,
        					headerText:headerKey,
        					width:width,
        					dataType:type,
        					format:format,
        					columnCssClass: columnCssClass,
        					headerCssClass: headerCssClass
						});
					});	
					
					$('#supplierGrid').igGrid({
						dataSource: $scope.supplierList,
						dataSourceType: "json",
						width: "100%",
						columns: supplierGridHeader,
						features: [
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
								pageSize : 10,
					        },
					        {
					            name: "ColumnFixing"
					        },
					        {
					            name: "ColumnMoving"
					        }
						],
		                width: '100%',
		             });

				}else{
					$scope.gridOptions.data = $scope.supplierListData;
				}
			});
			req.error(function(errorCallback){
				alert(JSON.stringify(errorCallback));
			});
		}

		$scope.change_date = function(){
			var option = $("#custom_date").val();

			if(option == "custom_date"){
				$("#date_show").show();
			}else{
				$("#date_show").hide();


			}

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
}

@-webkit-keyframes spin {
		0% { -webkit-transform: rotate(0deg); }
		100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
 .right_align{
 	text-align: right;
    padding-right: 10px;
 }
 .header_right_align{
 	text-align: right !important;
 	    padding-right: 5px;
 }
 .center_align{
 	text-align: left;
 	padding-left: 5px;
 }
 
 .header_center_align{
 	text-align: left;
 }
</style>
@stop
@extends('layouts.footer')