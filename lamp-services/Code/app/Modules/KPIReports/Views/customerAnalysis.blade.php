@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Customer Trends'); ?>
	<div class="portlet light tasks-widget">
		<div class="portlet-title">
			<div class="caption">Customer Trends</div>
			<div class="actions">

                <a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a>

			</div>
		</div>
		<div  class="portlet-body" id="default-report-component" ng-app="KPIReports" ng-controller="KpiBaseController"  >
			
			<div  class="row" style="padding: 15px" ng-controller="KpiSubController"  ng-init="customerInit()">
				<div class="loader" loading ></div>
				<div style="height: 600px;" ui-grid-pagination ui-grid="gridOptions" ui-grid-exporter class="myGrid"></div>
			</div>
		</div>
	</div>
@stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript" />
@stop

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>

@include('KPIReports::reportcomponent')

<script type="text/javascript">

app.controller('KpiSubController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {
		$rootScope.gridOptions = {
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
		    exporterPdfMaxGridWidth: 500,
			columnDefs: [
      			{field: 'S No',enableFiltering: true,width:'50'},
				{ field: 'Outlet Name', width: '140', enableFiltering: true},
				{ field: 'DC',displayName: 'DC', width: '140' },
				{ field: 'HUB', displayName: 'HUB',width: '140' },
				{ field: 'Beat', width: '140' },
				{ field: 'SO',displayName: 'SO', width: '140' },
				{ displayName: 'TBV', field: 'TBV',headerCellClass: 'right_align', 
					width: '165' ,aggregationType: function (){
						if(!$scope.grid1Api)
							return 0;
						var sum = 0;
						for (var i = 0; i < $scope.grid1Api.grid.api.grid.rows.length; i++){
							sum = parseFloat(sum) + parseFloat($scope.grid1Api.grid.api.grid.rows[i].entity.TBV);
						}
						return sum;
					},cellClass: "right_align",footerCellFilter: 'number:2',type:"number",footerCellClass: "right_align",footerCellTemplate: '<div data-toggle="tooltip" title="<%col.getAggregationValue() | number:2 %>" class="ui-grid-cell-contents">Total: <%col.getAggregationValue() | number:2 %></div>'
				},
				{ displayName: 'TBV Contrib %', field: 'TBV Contrib',headerCellClass: 'right_align', cellClass: "right_align",
					width: '140' ,type:"number"
				},				
				{ displayName: 'TGM', field: 'TGM', headerCellClass: 'right_align',
					width: '165' ,aggregationType: function (){
						if(!$scope.grid1Api)
							return 0;
						var sum = 0;
						for (var i = 0; i < $scope.grid1Api.grid.api.grid.rows.length; i++){
							sum = parseFloat(sum) + parseFloat($scope.grid1Api.grid.api.grid.rows[i].entity.TGM);
						}
						return sum;
					},cellClass: "right_align",footerCellFilter: 'number:2',type:"number",footerCellClass: "right_align",footerCellTemplate: '<div data-toggle="tooltip" title="<%col.getAggregationValue() | number:2 %>"  class="ui-grid-cell-contents">Total: <%col.getAggregationValue() | number:2 %></div>' 
				},
				{ displayName: 'TGM Contrib %', field: 'TGM Contrib', 
					width: '140' ,type:"number",cellClass: "right_align",headerCellClass: 'right_align'
				},
				{ field: 'Rating', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Total Issued Cheques', width: '140',type:"number",headerCellClass: 'right_align',cellClass: "right_align"},
				{ field: 'Total Cleared', width: '140', type:"number",headerCellClass: 'right_align',cellClass: "right_align"},
				{ field: 'Bounced', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number",headerCellClass: 'right_align'},
				{ field: 'Multiple attempts', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Total Orders', displayName: 'No of Orders', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Cancels', displayName: 'No of Cancelled', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Returns', displayName: 'No of Returns',width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Partial returns', width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Highest Order'  , width: '140',headerCellClass: 'right_align',cellClass: "right_align" ,type:"number"},
				{ field: 'Avg Order'   , width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Smallest Order'   , width: '140',headerCellClass: 'right_align',cellClass: "right_align",type:"number"},
				{ field: 'Last Visit Date'   , width: '140', type:"date"},
				{ field: 'Last Visit Duration' , width: '140', type:"number"},
				],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};


		$scope.customerInit = function(){
			var date = new Date();
			$rootScope.date_range = "today";
			$scope.end_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.start_date = $filter('date')(date, "yyyy-MM-dd");
			$scope.loadAnalysis();
		}

		$scope.loadAnalysis = function(){
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


			var req = $http.post('getcustomers',$scope.post_data);
			$("#default-report-component").addClass("disabledbutton");

			req.success(function(successCallback){
			$("#default-report-component").removeClass("disabledbutton");

				$scope.customerdata = successCallback;
				$scope.customerListData = [];
				if($scope.customerdata.status == "true"){
					$scope.customerList = $scope.customerdata.data;
					angular.forEach($scope.customerList, function(customerList, KeyList){

							
							$scope.customerListData.push(
								{ 
								'S No':KeyList + 1,
								'Outlet Name' : customerList.Outlet_Name, 
								'DC': customerList.DC, 
								'HUB': customerList.Hub, 
								'Beat': customerList.Beat,
								'SO': customerList.SO, 
								'TBV': customerList.TBV, 
								'TBV Contrib': customerList.TBV_Contrib, 
								'TGM': customerList.TGM,
								'TGM Contrib': customerList.TGM_Contrib,
								'Rating': customerList.Rating,
								'Total Issued Cheques': customerList.Total_Issues_Checks,
								'Total Cleared': customerList.Total_Cleared,
								'Bounced': customerList.Bounced,
								'Multiple attempts': customerList.Multiple_Attempts,
								'Cancels': customerList.No_Of_Cancelled,
								'Returns': customerList.No_of_Returns,
								'Partial returns': customerList.No_of_Partial_Returns,
								'Highest Order': customerList.Highest_Order,
								'Avg Order': customerList.Avg_Order,
								'Smallest Order': customerList.Smallest_Order,
								'Last Visit Date': customerList.Last_Visit_Date,
								'Last Visit Duration': customerList.Last_Visit_Duration_Days,
								'Total Orders': customerList.No_Of_Orders,

								});

								$rootScope.gridOptions.data = $scope.customerListData;
						});
				}else{
					$scope.gridOptions.data = [];
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