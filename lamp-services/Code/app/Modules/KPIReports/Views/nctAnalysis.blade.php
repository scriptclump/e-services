@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'NCT Trends - Ebutor'); ?>
	<div class="portlet light tasks-widget">
		<div class="portlet-title">
			<div class="caption">NCT Trends</div>
			<div class="actions">
				<a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a>

			</div>
		</div>
		<div  class="portlet-body" id="default-report-component" ng-app="KPIReports" ng-controller="KpiBaseController"  >
			
			<div  class="row" style="padding: 15px" ng-controller="KpiSubController"  ng-init="customerInit()">
				<div class="loader" id="nctLoader"></div>
					<center id="return_grid_data"></center> 
					<div id="return_grid">
						<table id="resultGrid"></table>
					</div>
			</div>
		</div>
	</div>
@stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}


<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<style type="text/css">
	
 .row_right_align{
 	text-align: right !important;
 	padding-right: 15px;
 }
 .header_right_align{
 	text-align: right !important;
 	    padding-right: 5px;
 }
 .row_center_align{
 	text-align: left;
 }
 .header_center_align{
 	text-align: left;
 }
#resultGrid{
	    table-layout: auto !important;
}
#resultGrid_headers{
	    table-layout: auto !important;
}
</style>
@include('KPIReports::reportcomponent')

<script type="text/javascript">

app.controller('KpiSubController', function ($scope,$http,$rootScope,$filter,$timeout) {
		$scope.customerInit = function(){
			var date = new Date();
			$rootScope.date_range = "today";
			$scope.end_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.start_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.loadAnalysis();
		}

		$scope.loadAnalysis = function(){
			$scope.start_date = $("#start_date").val();
			$scope.end_date = $("#end_date").val();

			$scope.post_data = {
				end_date:$scope.end_date,
				start_date:$scope.start_date,
				dc:$scope.kpi_dc,
				date_range:$scope.date_range,
				hub_id:$scope.kpi_hub,
				beat_id:$scope.kpi_beat,
				so_id:$scope.kpi_so,
				outlet_id:$scope.kpi_outlet

			}
			console.log($scope.post_data);
		var req = $http.post('getnct',$scope.post_data);
		$("#default-report-component").addClass("disabledbutton");
		$("#nctLoader").show();

		req.success(function(successCallback){
		$("#default-report-component").removeClass("disabledbutton");
			$("#nctLoader").hide();

			$scope.nctData = successCallback;
			$scope.nctListData = [];
			var nctHeader = [];
			if($scope.nctData.status == "true"){

				$scope.nctList = $scope.nctData.data;
				$("#return_grid").show();
					$("#return_grid_data").html("");
        			var type = "";
					var classTo = "center_align";
					var cellClass = "center_align";
					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
    				var format =  "";

        			$scope.nctData = $scope.nctData.data;
        			angular.forEach($scope.nctData[0],function(val,key){
						var result = reg.test(val);
                        var width = 170;
                        var columnCssClass = "";
                        var headerCssClass = "";
                        var headerKey = key;
						headerKey = headerKey.replace(/_/g, " ");
        				type = typeof(val);
        				if(typeof(val) === "number" || result){
        					 columnCssClass = "row_right_align";
        					 headerCssClass = "header_right_align";
                             width = 130;
                             type = 'number';
                             format = "0.00";
                        	if(key == "Product_ID" || key == "Cheque_No"){
	                            type = 'string';
	                            format = "";
								width = 130;
        						columnCssClass = "row_center_align";
        						headerCssClass = "header_center_align";
	                        }                             
        				}else{
        					format = "";
        					columnCssClass = "row_center_align";        					
        					headerCssClass = "header_center_align";
        				}
        				var Dateresult = dateReg.test(val);

        				if(Dateresult){
							type = "date";
							format = "dd-MM-yyyy";
                            width = 155;
                            columnCssClass = "center_align";
        					headerCssClass = "header_center_align";
        				}
        				
        				nctHeader.push({key:key,
        					headerText:headerKey,
        					width:width,
        					dataType:type,
        					format:format,
        					columnCssClass: columnCssClass,
        					headerCssClass: headerCssClass
						});

        			});
        			console.log(nctHeader);
        			$('#resultGrid').igGrid({
		                 dataSource: $scope.nctData,
		                 dataSourceType: "json",
		                 width: "100%",
		                 columns: nctHeader,
		                 initialDataBindDepth: 1,
						 primaryKey: "Cheque_No",

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
				$("#nctLoader").hide();
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

@stop
@extends('layouts.footer')