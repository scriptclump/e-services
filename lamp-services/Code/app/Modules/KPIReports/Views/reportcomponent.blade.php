<script type="text/javascript">
// do not change anything
$("#toggleFilter").click(function(){$("#filters").toggle("fast",function(){});});
var html = $("#default-report-component").html();
var repComp = '<div id="filters" style="display: none;">\
				<div class="row" style="padding:15px;">\
				<div class="col-md-3">\
				  	<label>DC</label>\
					<select ng-model="kpi_dc"  id="kpi_dc"  class="form-control" ng-change="getHubSortData(kpi_dc)">\
						<option selected value="">---Select DC---</option>\
						<option ng-repeat="(key,value) in dc_list_data " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>\
					</select>\
				</div>\
				<div class="col-md-3">\
				  <label>Hub</label>\
					<select ng-model="kpi_hub"  id="kpi_hub" class="form-control select2me" ng-change="getBeatSortData(kpi_hub)">\
						<option selected value="">---Select Hub---</option>\
						<option ng-repeat="(key,value) in hub_array " value="<%value.hub_id%>" ><%value.lp_wh_name%></option>\
					</select>\
				</div>\
				<div class="col-md-3">\
				  <label>Beat</label>\
					<select ng-model="kpi_beat"  id="kpi_beat" class="form-control select2me" ng-change="getSoSortData(kpi_beat)">\
						<option selected value="">---Select Beat---</option>\
						<option ng-repeat="(key,value) in beat_array  track by $index" value="<%value.pjp_pincode_area_id%>" ><%value.pjp_name%></option>\
					</select>\
				</div>\
				<div class="col-md-3">\
				  <label>SO</label>\
					<select ng-model="kpi_so"  id="kpi_so" class="form-control select2me" ng-change="getOutletSortData(kpi_so)">\ 		<option selected value="">---Select SO---</option>\					<option ng-repeat="(key,value) in so_array " value="<%value.user_id%>" ><%value.Fullname%></option>\
					</select>\
				</div>\
			</div>\
			<div class="row"  style="padding:15px;" >\
				<div class="col-md-3">\
					<label>Select Date</label>\
					<select ng-model="date_range" id="date_range" class="form-control" ng-change="change_date()">\
						<option value="today" selected="selected">Today</option>\
						<option value="yesterday">Yesterday</option>\
						<option value="wtd">WTD</option>\
						<option value="mtd">MTD</option>\
						<option value="ytd">YTD</option>\
						<option value="std">STD</option>\
						<option value="date_range">Date Range</option>\
				    </select>\
				</div>\
				<div class="custom_range" id="custom_range" style="display:none">\
					<div class="col-md-2">\
					          <label>Start Date</label>\
					          <div class="input-icon input-icon-sm right">\
					          <i class="fa fa-calendar"></i>\
					          <input type="text" ng-model="start_date" name="start_date" id="start_date" class="form-control" value="">\
					          </div>\
					 </div>\
					 <div class="col-md-2">\
					          <label>End Date</label>\
					          <div class="input-icon input-icon-sm right">\
					          <i class="fa fa-calendar"></i>\
					          <input type="text" ng-model="end_date" name="end_date" id="end_date" class="form-control" value="">\
					          </div>\
					 </div> \
				</div>\
				<div class="col-md-2" ng-controller="KpiSubController" >\
						<button type="button" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut" ng-click="loadAnalysis()">Submit</button>\
				</div>\
			</div>\
			</div>';
	$("#default-report-component").html(repComp + html);

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

	var app = angular.module("KPIReports", ['ui.grid','ui.grid.pagination', 'ui.grid.exporter'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});

	app.controller('KpiBaseController', function ($scope,$http,$rootScope,$filter,$timeout,uiGridConstants) {
			$scope.DcList = angular.fromJson(<?php echo $DC_list;?>);
			$scope.dc_list_data = [];
			angular.forEach($scope.DcList, function(value,key){
				$scope.dc_list_data.push(value);
			});

			$scope.hubList = angular.fromJson(<?php echo $HUB_list;?>);
			$scope.hublist_data = $scope.hubList;
			$scope.hub_array = [];
			
			$scope.beatList = angular.fromJson(<?php echo $Beat_List;?>);
			$scope.beatlist_data = $scope.beatList;
			$scope.beat_array = [];
			
			// $scope.outletList = angular.fromJson(<?php echo $outlet_List;?>);
			// $scope.outletlist_data = $scope.outletList;
			// $scope.outlet_array = [];
			
			$scope.soList = angular.fromJson(<?php echo $so_List;?>);
			$scope.solist_data = $scope.soList;
			$scope.so_array = [];
			
			$scope.dchubList = angular.fromJson(<?php echo $dc_hub_data;?>);
			$scope.dc_hub_list_data = $scope.dchubList;
			$scope.dc_hub_array = [];
        
		
		
		$scope.getHubSortData=function(dc_id){

			    $('#kpi_hub').select2("val","");
			    $('#custom_date').select2("val","");
			    $scope.hub_array=[];
			angular.forEach($scope.hublist_data,function(value,key){
				if(dc_id==value.dc_id){
					$scope.hub_array.push(value);
				}
			});

			//loading beat data

				$('#kpi_beat').select2("val","");
				$scope.beat_array=[];
			angular.forEach($scope.beatlist_data,function(value,key){
				if(dc_id==value.le_wh_id){
					$scope.beat_array.push(value);
				}	
			});
			angular.forEach($scope.beatlist_data,function(value,key){
				angular.forEach($scope.hub_array,function(hubvalue,hubkey){
					if(hubvalue.hub_id==value.le_wh_id){
						$scope.beat_array.push(value);
					}
				});
			});

			//loading outlet data
			// $('#kpi_outlet').select2("val","");
			// $scope.outlet_array=[];
			// angular.forEach($scope.outletlist_data,function(value,key){
			// 	if(dc_id==value.dc_id){
			// 		$scope.outlet_array.push(value);
			// 	}
			// });
			
			//loading so data
			$('#kpi_so').select2("val","");
			$scope.so_array=[];
			angular.forEach($scope.solist_data,function(value,key){
				if(dc_id==value.le_wh_id){
					$scope.so_array.push(value);
				}
			});
			// angular.forEach($scope.solist_data,function(value,key){
			// 	angular.forEach($scope.hub_array,function(hubsovalue,hubsokey){
			// 		if(hubsovalue.hub_id==value.le_wh_id){
			// 			$scope.so_array.push(value);
			// 		}
			// 	});
			// });

			//console.log($scope.outlet_array);
		}
		$scope.getBeatSortData=function(hub_id){

			if(hub_id!=""){

				$('#custom_date').select2("val","");
				$('#kpi_beat').select2("val","");
				$scope.beat_array=[];
				angular.forEach($scope.beatlist_data,function(value,key){
					if(hub_id==value.le_wh_id){
						$scope.beat_array.push(value);
					}
				});
				// $('#kpi_outlet').select2("val","");
				// $scope.outlet_array=[];
				// angular.forEach($scope.outletlist_data,function(value,key){
				// 	if(hub_id==value.hub_id){
				// 		$scope.outlet_array.push(value);
				// 	}
				// });

				$('#kpi_so').select2("val","");
				$scope.so_array=[];
				angular.forEach($scope.solist_data,function(value,key){
					if(hub_id==value.le_wh_id){
						$scope.so_array.push(value);
					}
				});
			}
		else{

			$scope.getHubSortData($scope.kpi_dc);
		}
		}
		$scope.getSoSortData=function(beat_id){

			if(beat_id!=""){
				$('#custom_date').select2("val","");
				$('#kpi_outlet').select2("val","");
				// $scope.outlet_array=[];
				// angular.forEach($scope.outletlist_data,function(value,key){
				// 	if(beat_id==value.beat_id){
				// 		$scope.outlet_array.push(value);
				// 	}
				// });

				$('#kpi_so').select2("val","");
				$scope.so_array=[];
				angular.forEach($scope.solist_data,function(value,key){
					if(beat_id==value.pjp_pincode_area_id){
						$scope.so_array.push(value);
					}
				});
			}else{
				$scope.getBeatSortData($scope.kpi_hub);
			}
		}
		$scope.getOutletSortData=function(so_id){

			if(so_id!=""){
				$('#kpi_outlet').select2("val","");
				$scope.outlet_array=[];
				angular.forEach($scope.outletlist_data,function(value,key){
					if(so_id==value.rm_id){
						$scope.outlet_array.push(value);
					}
				});	

			}else{
				$scope.getSoSortData($scope.kpi_beat);
			}		
		}
		$scope.change_date=function(){
			var data = $('#date_range').val();
			if(data=='date_range'){
				$('#custom_range').show();
			}
			else{
				$('#custom_range').hide();
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
 	text-align: right;
    padding-right: 10px;
 }
 .center_align{
 	text-align: center;
    padding-right: 10px;
 }
 .disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}

</style>
