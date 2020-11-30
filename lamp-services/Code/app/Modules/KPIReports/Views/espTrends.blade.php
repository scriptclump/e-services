@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - ESP Trends'); ?>
	<span id="success_message"></span>
	<div class="portlet light tasks-widget">
		<div class="portlet-title">
			<div class="caption">ESP Trends</div>
			<div class="actions">
                <a href="javascript:void(0);" data-toggle="tooltip" title="Filter" id="toggleFilter" ><i class="fa fa-filter fa-lg"></i></a>
                
			</div>
		</div>
		@if($espAccess == 1)
		<div  class="portlet-body" id="default-report-component" ng-app="KPIReports"  ng-controller="KpiSubController">
			<div id="filters" style="display: none;">
				<div class="row" style="padding:15px;">
				{{ Form::open(array('url' => '/kpi/espdownload', 'id' => 'downloadexcel'))}}
				<div class="col-md-3">
				  	<label>DC</label>
					<select name="kpi_dc" ng-model="kpi_dc"  id="kpi_dc"  class="form-control" ng-change="getHubSortData(kpi_dc)">
						<option selected value="">---Select DC---</option>
						<option ng-repeat="(key,value) in dc_list_data " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>
					</select>
				</div>
				<div class="col-md-3">
					<label>Select Date</label>
					<select name="date_range" ng-model="date_range" id="date_range" class="form-control" ng-change="change_date()">
						<option value="today" selected="selected">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="wtd">WTD</option>
						<option value="mtd">MTD</option>
						<option value="ytd">YTD</option>
						<option value="std">STD</option>
						<option value="date_range">Date Range</option>
				    </select>
				</div>
				<div class="custom_range" id="custom_range" style="display:none">
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
				<div class="col-md-2" >
						<button type="button" style="height:36px;margin-top: 21px;" class="btn green-meadow subBut" ng-click="loadAnalysis()">Submit</button>
						<button data-toggle="tooltip" style="height:36px;margin-top: 21px;" title="Download Excel"  type="submit" class="btn green-meadow" id="download_excel"><i class="fa fa-download fa-lg"></i></button>
				</div>
				 {{ Form::close() }}			
			</div>
			
			</div>
			<div  class="row" style="padding: 15px" ng-controller="KpiSubController"  ng-init="salesReturnsInit()">
				<div class="loader" id="salesReturnsInit"></div>
				<center id="return_grid_data"></center> 
					<div id="return_grid">
						<table id="resultGrid"></table>
					</div>
			</div>
		</div>
		@endif
	</div>
@stop
@section('style')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<style type="text/css">
	.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:410px !important; white-space: pre-wrap !important;}
	.loader {
 position:relative;
 top:40%;
 left: 40%;
 border: 5px solid #e8e8e8;
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

  .right_align{
 	text-align: right !important;
 	padding-right: 15px;
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
 .disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
.custom-control{
	width: 10px;
    position: absolute;
    left: 5px;
    color: black;
    background: #f7f7f7;
    height: 20px;
}

#resultGrid{
	    table-layout: auto !important;
}
#resultGrid_headers{
	    table-layout: auto !important;
}

.ui-iggrid .ui-iggrid-tablebody td {
    font-size: 11px !important;
    font-weight: normal;
}
</style>
@stop

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="{{ URL::asset('assets/global/scripts/angular-fusioncharts.min.js') }}"></script>
<script src="https://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/filesaver.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/Blob.js') }}" type="text/javascript"></script>


@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}

@if($espAccess == 1)
<script type="text/javascript">

	var start = new Date();
	var end = new Date();

    $('#start_date').datepicker({
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#end_date').datepicker('setStartDate', stDate);
    });

    $('#end_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        dateFormat: 'dd-mm-yy'
    }).on('changeDate', function () {
        $('#start_date').datepicker('setEndDate', new Date($(this).val()));
    });

$("#toggleFilter").click(function(){$("#filters").toggle("fast",function(){});});
var app = angular.module("KPIReports", [],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});

app.controller('KpiSubController', function ($scope,$http,$rootScope,$filter,$timeout) {
		$scope.outlet_term = "";
		$scope.DcList = angular.fromJson(<?php echo $DC_list;?>);
                angular.forEach($scope.DcList, function(value,key){
                    $scope.kpi_dc = value.le_wh_id;
                    return;
                   });
			$scope.dc_list_data = [];
			angular.forEach($scope.DcList, function(value,key){
				$scope.dc_list_data.push(value);
			});

		$scope.date_range = 'mtd';
		//$scope.kpi_dc = '4497';
		
		$scope.change_date=function(){
			var data = $('#date_range').val();
			if(data=='date_range'){
				$('#custom_range').show();
			}
			else{
				$('#custom_range').hide();
			}

		}


		$scope.salesReturnsInit = function(){
			var date = new Date();
			$rootScope.date_range = "today";
			$scope.end_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.start_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.loadAnalysis();
		}
		
		$scope.ngGridFIx = function() {
			window.dispatchEvent(new Event('resize'));
		}

		$scope.loadAnalysis = function(){
			$scope.end_date = $filter('date')($scope.end_date, "yyyy-MM-dd");
			$scope.start_date = $filter('date')($scope.start_date, "yyyy-MM-dd");
			$scope.post_data = {
				end_date:$scope.end_date,
				start_date:$scope.start_date,
				dc:$scope.kpi_dc,
				date_range:$scope.date_range,
			}
			console.log($scope.post_data);
			var req = $http.post('espdata',$scope.post_data);
			$("#default-report-component").addClass("disabledbutton");
			var espGridHeader = [];
			$("#salesReturnsInit").show();
			req.success(function(successCallback){
			$("#default-report-component").removeClass("disabledbutton");
			$("#salesReturnsInit").hide();
			$("#return_grid_data").html("No Data found!");
				$scope.espGridData = successCallback;
				if($scope.espGridData.status == "true"){
					$("#return_grid").show();
					$("#return_grid_data").html("");
        			var type = "";
					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
    				var format =  "";

        			$scope.espGridAllData = $scope.espGridData.data;
        			angular.forEach($scope.espGridAllData[0],function(val,key){
							var result = reg.test(val);
	                        var width = 150;
	                        var columnCssClass = "";
	                        var headerCssClass = "";
	                        var headerKey = key;
							headerKey = headerKey.replace(/_/g, " ");
	        				type = typeof(val);
	        				if(typeof(val) === "number" || result){
	        					 columnCssClass = "right_align";
	        					 headerCssClass = "header_right_align";
	                             width = 130;
	                             type = 'number';
	                             format = "0.00";
	                             if(key == "Product_ID"){
		                            type = 'number';
		                            format = "";
	        						columnCssClass = "center_align";
	        						headerCssClass = "header_center_align";
		                         }                             
	        				}else{
	        					format = "";
	        					columnCssClass = "center_align";        					
	        					headerCssClass = "header_center_align";
	        				}
	        				var Dateresult = dateReg.test(val);

	        				if(Dateresult){
								type = "date";
								format = "dd-MM-yyyy";
	                            width = 125;
	                            columnCssClass = "center_align";
	        					headerCssClass = "header_center_align";
	        				}
	        				
	        				espGridHeader.push({key:key,
	        					headerText:headerKey,
	        					width:width,
	        					dataType:type,
	        					format:format,
	        					columnCssClass: columnCssClass,
	        					headerCssClass: headerCssClass
							});
						});	
        			
        			$('#resultGrid').igGrid({
		                 dataSource: $scope.espGridAllData,
		                 dataSourceType: "json",
		                 width: "100%",
		                 columns: espGridHeader,
		                 initialDataBindDepth: 1,
						 primaryKey: "Product_ID",

		                 dataRendered: function() {

			                // var allData=$("#resultGrid").data("igGrid").dataSource.data();			               
			            },
		              features: [
		                     {
		                        name: "Filtering",
		                        type:"local",
		                        allowFiltering: true,
		                        caseSensitive: false
		                     }, {
		                         name: 'Sorting',
		                         type: "local",
		                     }
		                     ,{
					            name : 'Paging',
					            type: "local",
					            pageSize : 25,
					        },
					        {
					            name: "ColumnFixing"
					        },
					        {
					            name: "ColumnMoving"
					        }
		                 ],
		                width: '100%',
				        height: '500px',
		             });
        			
					}else{

					$("#salesReturnsInit").hide();
					$("#return_grid").hide();
					$("#return_grid_data").html("No Data found!");
				}
			});
			req.error(function(errorCallback){
				$("#default-report-component").removeClass("disabledbutton");

				alert(JSON.stringify(errorCallback));
			});
		}
	});


</script>
@endif
@stop
@extends('layouts.footer')