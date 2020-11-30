@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Kpis'); ?>
<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/logistics" class="bread-color">Working Capital</a></li>
			<!-- <li><span class="bread-color">Create New Route</span></li> -->
		</ul>
	</div>
</div>

<div ng-app="workingCapitalApp" ng-controller="workingCapitalController" style="background-color: #f7f7f7;">
	<!-- <div class="loader" loading></div> -->
	<div class="row" >
		@if($checkAccess == 1)
		<div class="col-md-6 selectBox" ng-init="workingCaptialAnalysis(DefaultDcList)" style="margin-bottom: 15px !important;">
			
			<div class="portlet-body" style="width: 103%;">
        		<div id="workingCaptialAnalysis" style="display: none" class="loader" ></div>
				<div class="row" style="padding:10px;">
					<div class="col-md-12" ng-click="toggleWorkSummary()">
						<span style="font-weight: 600;">Working Capital Summary</span>
						<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
					</div>
					<div ng-show="toggleWorkVariable">
						<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
							<select ng-model="logisticsDC">
								 <option selected value="">---- Select DC ----</option>
								 <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
							</select>
						</div>
						<div style="margin-top: 8px;" class="col-md-1">
							<button ng-click="workingCaptialAnalysis(logisticsDC)">Go</button>
						</div>
					</div>
				</div>
				<div class="tabbable tabs-below">
					<div class="tab-content" style="float:center!important;align-content: center;">
						<div class="tab-pane active"  style="height: 775px !important;padding: 10px;overflow: scroll;">
							<table class="search-table" id="working_capital" style="width: 100%;text-align: center;">
								<thead>
									<tr><th colspan="2" style="text-align: center;color: black;background-color: #f7f7f7;">Working Capital</th></tr>
									<tr>
										<th>Particulars</th>
										<th style="text-align: right;">Amount</th>
									</tr>
								</thead>
								<tbody> 
									<tr ng-repeat="(key, data) in WorkingDataValueArray[0]">
										<td style="text-align: left"><%key%></td>
										<td style="text-align: right;"><%data%></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>	
				</div>
			</div>
			
		</div>
		@endif
		@if($DcLeadAccessSummary == 1)
		<div class="col-md-6 selectBox" ng-init="dcLeaderAnalyse(DefaultDcList)" style="margin-bottom: 15px !important;">
			
			<div class="portlet-body">
        		<div id="dcLeaderAnalyse" style="display: none" class="loader" ></div>
				<div class="row" style="padding:10px;">
					<div class="col-md-12" ng-click="toggleDcLeaderSummary()">
						<span style="font-weight: 600;">DC Leader Summary </span>
						<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
					</div>
					<div ng-show="toggleDcLeaderVariable">
						<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
							<select ng-model="selectDcLeader">
								 <option selected value="">---- Select DC ----</option>
								 <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
							</select>
						</div>
						<div style="margin-top: 8px;" class="col-md-1">
							<button ng-click="dcLeaderAnalyse(selectDcLeader)">Go</button>
						</div>
					</div>
				</div>
				<div class="tabbable tabs-below">
					<div class="tab-content" style="float:center!important;align-content: center;">
						<div class="tab-pane active"  style="height: 775px !important;padding: 10px;overflow: scroll;">
							<table class="search-table" style="width: 100%;text-align: center;">
								<thead>
									<tr><th colspan="2" style="text-align: center;color: black;background-color: #f7f7f7;">DC Leader</th></tr>
									<tr>
										<th>Particulars</th>
										<th style="text-align: right;">Value</th>
									</tr>
								</thead>
								<tbody> 
									<tr ng-repeat="(key, data) in dcLeaderValueArray[0]">
										<td style="text-align: left"><%key%></td>
										<td style="text-align: right;"><%data%></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		@endif
    	@if($salesAccessSummary == 1)
		<div class="col-md-6 selectBox" ng-init="salesLeaderAnalyse(DefaultDcList)" style="margin-bottom: 15px !important;">
			
			<div class="portlet-body" style="width: 103%;">
        		<div id="salesLeaderAnalyse" style="display: none" class="loader" ></div>
				<div class="row" style="padding:10px;">
					<div class="col-md-12" ng-click="togglesalesLeaderSummary()">
						<span style="font-weight: 600;">Sales Leads Summary</span>
						<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
					</div>
					<div ng-show="togglesalesLeader">
						<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
							<select ng-model="logisticsDC">
								 <option selected value="">---- Select DC ----</option>
								 <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
							</select>
						</div>
						<div style="margin-top: 8px;" class="col-md-1">
							<button ng-click="salesLeaderAnalyse(logisticsDC)">Go</button>
						</div>
					</div>
				</div>
				<div class="tabbable tabs-below">
					<div class="tab-content" style="float:center!important;align-content: center;">
						<div class="tab-pane active"  style="height: 775px !important;padding: 10px;overflow: scroll;">
							<table class="search-table" style="width: 100%;text-align: center;">
								<thead>
									<tr><th colspan="2" style="text-align: center;color: black;background-color: #f7f7f7;">Sales Lead</th></tr>
									<tr>
										<th>Particulars</th>
										<th style="text-align: right;">Value</th>
									</tr>
								</thead>
								<tbody> 
									<tr ng-repeat="(key, data) in salesLeaderValueArray[0]">
										<td style="text-align: left"><%key%></td>
										<td style="text-align: right;"><%data%></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		@endif
		<div class="col-md-6 selectBox" ng-init="deliveryLeaderAnalyse(DefaultDcList,'current')" style="margin-bottom: 15px !important;">
			<div class="portlet-body">
				<div id="deliveryLeaderAnalyse" style="display: none" class="loader" ></div>
				<div class="row" style="padding:10px;">
					<div class="col-md-12" ng-click="toggleDeliveryLeaderSummary()">
						<span style="font-weight: 600;">Delivery Leader Summary</span>
						<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
					</div>
					<div ng-show="toggleDeliveryLeader">
						<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
							<select ng-model="deliveryDc" ng-change="loadHubData(deliveryDc)">
								 <option selected value="">---- Select DC ----</option>
								 <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
							</select>
						</div>
						<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
							<select ng-model="deliveryHub">
								<option disabled="disabled" value="">---- Select HUB ----</option>
	                            <option value="allHub">All</option>
	                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
							</select>
						</div>
						<div style="margin-top: 8px;" class="col-md-1">
							<button ng-click="deliveryLeaderAnalyse(deliveryDc,deliveryHub)">Go</button>
						</div>
					</div>
				</div>
				<div class="tabbable tabs-below">
					<div class="tab-content" style="float:center!important;align-content: center;">
						<div class="tab-pane active"  style="height: 775px !important;padding: 10px;overflow: scroll;">
							<table class="search-table" style="width: 100%;text-align: center;">
								<thead>
									<tr>
										<th style="background: #f7f7f7;">Particulars</th>
										<th ng-repeat="hub in deliveryHubList" style="text-align: center;background: #f7f7f7;">
											<%hub%>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="(key, value) in deliveryLeaderValueArray[0]">
										<td style="text-align: left;"><%key%></td>
										<td ng-repeat="(keyss, valuess) in deliveryLeaderValueArray" style="text-align: right;">
											<%valuess[key]%>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 selectBox" ng-init="purchaseLeaderAnalyse(DefaultDcList)" style="margin-bottom: 15px !important;">
            @include('AngularLogistics::purchaseLeaderReport')
		</div>
    </div>
</div>

    @stop
@section('style')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nvd3/1.8.1/nv.d3.min.css"/>   -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">

<style type="text/css">
#working_capital>tbody>tr:last-child {
	font-weight: bold; 
}
.portlet-body{
    /*border: 1px solid #ddd;
    border-radius: 5px;*/
    background: #fdfdfd;
}
.selectBox{
    /*padding-top: 10px;
    padding-left: 25px;*/
}
.selectBox select{
    height: 30px;
    width: 100%;
    /*border-radius: 3px;*/
    background: white;
    border: 1px solid #e5e5e5;
}
.selectBox button{
    background: white;
    border: 1px solid #e5e5e5;
    height: 30px;
    border-radius: 3px;
}
.search-table, td, th{
	border-collapse:collapse; 
	border:1px solid #ddd;
	padding: 8px;
	/*min-width: 200px;*/
}
.search-table, td, th{
	border-collapse:collapse; 
	border:1px solid #ddd;
	padding: 8px;
	min-width: 200px;
}



.custIcon{
	padding:5px;
}
.custIcon.active{
    background: #e5e5e5;
    border: 1px solid #e5e5e5;
    padding:5px;
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
}
#working_capital>tbody>tr:last-child {
font-weight: bold; 
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
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script  type="text/javascript">
    var app = angular.module('workingCapitalApp', [],function($interpolateProvider){
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });
    
    app.controller("workingCapitalController",function($scope,$http,$rootScope,$filter,$window,$location) { 
        $scope.dcList = JSON.parse('<?php echo json_encode($data); ?>');
        angular.forEach($scope.dcList.DC, function(values, keys){
            console.log(keys);
            $scope.DefaultDcList = keys;
            return;
        });
        //Hub List
        $scope.loadHubData = function(value){
            angular.forEach($scope.dcList.Hub, function(hubValues, hubKeys){
                if(value == hubKeys){
                    $scope.hubList = hubValues;
                }
            });
        }

    	$scope.toggleWorkSummary = function(){
			$scope.toggleWorkVariable = !$scope.toggleWorkVariable;
		}
		$scope.toggleDcLeaderSummary = function(){
			$scope.toggleDcLeaderVariable = !$scope.toggleDcLeaderVariable;
		}

		$scope.togglesalesLeaderSummary = function(){
			$scope.togglesalesLeader = !$scope.togglesalesLeader;
		}

		$scope.toggleDeliveryLeaderSummary = function(){
			$scope.toggleDeliveryLeader = !$scope.toggleDeliveryLeader;
		}

		$scope.togglePurchaseLeaderSummary = function(){
			$scope.togglePurchaseLeader = !$scope.togglePurchaseLeader;
		}

		 //working capital 
        $scope.workingCaptialAnalysis=function(dcID){
			var data={
				working_dc:dcID
			};
			$scope.WorkingDataKeyArray=[];
			$scope.WorkingDataValueArray=[];
         	var reqData=$http.post('/logistics/WorkingCapital',data);
        	$("#workingCaptialAnalysis").show();
         	reqData.success(function(successCallback){
        		$("#workingCaptialAnalysis").hide();
      			$scope.WorkingCapitalData=successCallback;
      			if($scope.WorkingCapitalData.status=='true'){
           			$scope.WorkingCapitalDataList =$scope.WorkingCapitalData.data;
           			angular.forEach($scope.WorkingCapitalDataList,function(WorkingCapitalValueList,WorkingCapitalKeyList){
	            		var obj = {};
	        			angular.forEach(WorkingCapitalValueList,function(valueList,keyList){
	             			$scope.WorkingDataKeyArray.push(keyList);
				            var keyss = keyList;
				            obj[keyss] = valueList;
	            		});
	            		$scope.WorkingDataValueArray.push(obj);
           			});
          		}
         	});
         	reqData.error(function(errorCallback){
        		$("#workingCaptialAnalysis").hide();
         	});
        }

        // Dc Leader
        $scope.dcLeaderAnalyse=function(dcID){
			var data = {
				dc_id : dcID
			};
			$scope.dcLeaderKeyArray=[];
			$scope.dcLeaderValueArray=[];
         	var reqData=$http.post('/logistics/getdncleader',data);
        	$("#dcLeaderAnalyse").show();
         	reqData.success(function(successCallback){
        		$("#dcLeaderAnalyse").hide();
      			if(successCallback.status == true){
           			angular.forEach(successCallback.data,function(dcLeaderValue,dcLeaderKey){
	            		var obj = {};
	        			angular.forEach(dcLeaderValue,function(valueList,keyList){
	             			$scope.dcLeaderKeyArray.push(keyList);
				            var keyss = keyList;
				            obj[keyss] = valueList;
	            		});
	            		$scope.dcLeaderValueArray.push(obj);
           			});
          		}
         	});
         	reqData.error(function(errorCallback){
        		$("#dcLeaderAnalyse").hide();
         	});
        }

        // Sales Leader
        $scope.salesLeaderAnalyse=function(dcID){
			var data = {
				dc_id : dcID
			};
			$scope.salesLeaderKeyArray=[];
			$scope.salesLeaderValueArray=[];
         	var reqData=$http.post('/logistics/getsalesleaddata',data);
        	$("#salesLeaderAnalyse").show();
         	reqData.success(function(successCallback){
        		$("#salesLeaderAnalyse").hide();
      			if(successCallback.status == true){
           			angular.forEach(successCallback.data,function(salesLeaderValue,salesLeaderKey){
	            		var obj = {};
	        			angular.forEach(salesLeaderValue,function(valueList,keyList){
	             			$scope.salesLeaderKeyArray.push(keyList);
				            var keyss = keyList;
				            obj[keyss] = valueList;
	            		});
	            		$scope.salesLeaderValueArray.push(obj);
           			});
          		}
         	});
         	reqData.error(function(errorCallback){
        		$("#salesLeaderAnalyse").hide();
         	});
        }

        // Delivery Leader Analysis
        $scope.deliveryLeaderAnalyse=function(dcId,hubID){
        	if(hubID == 'allHub'){
        		hubID = 'null';
        	}else if(hubID == 'current'){
        		hubID = 'null';
        		$scope.deliveryDc = dcId;
        		$scope.loadHubData(dcId);
        		$scope.deliveryHub = 'allHub';
        	}
			var data = {
				hub_id : hubID
			};
			$scope.deliveryLeaderKeyArray=[];
			$scope.deliveryLeaderValueArray=[];

			$scope.deliveryLeadKey = [];
			$scope.deliveryHubList = [];
			$scope.deliveryListData = [];

         	var reqData=$http.post('/logistics/getdeliveryleaderdata',data);
        	$("#deliveryLeaderAnalyse").show();
         	reqData.success(function(successCallback){
        		$("#deliveryLeaderAnalyse").hide();
      			if(successCallback.status == true){
           			angular.forEach(successCallback.data,function(deliveryLeaderValue,deliveryLeaderKey){
	            		var obj = {};
	            		$scope.deliveryHubList.push(deliveryLeaderValue.DC);
	        			angular.forEach(deliveryLeaderValue,function(valueList,keyList){
	             			$scope.deliveryLeaderKeyArray.push(keyList);
	             			if(keyList != 'DC'){
					            var keyss = keyList;
					            obj[keyss] = valueList;
					        }
	            		});
	            		$scope.deliveryLeaderValueArray.push(obj);
           			});
          		}
         	});
         	reqData.error(function(errorCallback){
        		$("#deliveryLeaderAnalyse").hide();
         	});
        }

        // Purchase Leader Summary
        $scope.purchaseLeaderAnalyse=function(dcID){
        	$scope.selectPurchaseDC = dcID;
			var data = {
				dc_id : dcID
			};
			$scope.purchaseLeaderKeyArray=[];
			$scope.purchaseLeaderValueArray=[];
         	var reqData=$http.post('/logistics/getpurchaseleaderdata',data);
        	$("#purchaseLeaderAnalyse").show();
         	reqData.success(function(successCallback){
        		$("#purchaseLeaderAnalyse").hide();
      			if(successCallback.status == true){
           			angular.forEach(successCallback.data,function(purchaseLeaderValue,purchaseLeaderKey){
	            		var obj = {};
	        			angular.forEach(purchaseLeaderValue,function(valueList,keyList){
	             			$scope.purchaseLeaderKeyArray.push(keyList);
				            var keyss = keyList;
				            obj[keyss] = valueList;
	            		});
	            		$scope.purchaseLeaderValueArray.push(obj);
           			});
          		}
         	});
         	reqData.error(function(errorCallback){
        		$("#purchaseLeaderAnalyse").hide();
         	});
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
@stop
@extends('layouts.footer')
