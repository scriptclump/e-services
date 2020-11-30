@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Hub Ops'); ?>
<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/logistics" class="bread-color">Logistics Dashboard</a></li>
			<!-- <li><span class="bread-color">Create New Route</span></li> -->
		</ul>
	</div>
</div>
<div class="" ng-app="logisticApp" ng-controller="logisticController">
    <div class="row" style="margin-top: 10px;">
		<div class="col-md-6 selectBox" ng-init="loadReturnSumData(DefaultDcList,'null','allDos','current')">
    		<div class="portlet-body" style="height: 500px;width: 103%;">
        	<div id="loadReturnSumData" style="display: none" class="loader" ></div>
    			<div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="toggleReturnSum()">
                		<span style="font-weight: 600;">Returns Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="toggleVariableReturnSum">
	                    <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
	                        <select ng-model="selectReturnDc" ng-change="loadHubData(selectReturnDc)">
	                            <option selected value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                        	<select ng-model="selectReturnHub" ng-change="changedeliveryData(selectReturnDc,selectReturnHub)">
	                            <option disabled="disabled">---- Select HUB ----</option>
	                            <option value="allHub">All</option>
	                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
	                        <select ng-model="selectedReturnDO">
	                            <option disabled="disabled">---- Select Delivery Executive ----</option>
                            	<option value="allDos">All</option>
                            	<option ng-repeat="(key,value) in Deliverydata" value="<%value.UserId%>"><%value.UserName%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;" class="col-md-2">
	                        <select ng-model="selectReturnDate" ng-change="loadSelectedDates(selectReturnDate)">
	                            <option disabled="disabled">Period</option>
	                            <option value="today">Today</option>
	                            <option value="yesterday">Yesterday</option>
	                            <option value="wtd">WTD</option>
	                            <option value="mtd">MTD</option>
	                            <option value="quater">Quarter</option>
	                            <option value="ytd">YTD</option>
	                            <option value="customDate">Custom Date</option>
	                        </select>
	                    </div>
	                    <div ng-show="selectReturnDate == 'customDate'">
	                    	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='returnSumfromDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="returnSumFromDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='returnSumtoDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="returnSumToDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
	                    </div>
	                    <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectReturnDate == 'customDate'}">
                        <button ng-click="loadReturnSumData(selectReturnDc,selectReturnHub,selectedReturnDO,selectReturnDate)">Go</button>
	                    </div>
	                </div>
                </div> 
                <div class="tabbable tabs-below">
                	<div class="tab-content">
                        <div class="tab-pane active" id="returnSumPie" style="height: 415px !important;"> 
                           <div id="returnChart-container"></div>   
                        </div>
                        <div class="tab-pane" id="returnSumStacked" style="height: 415px !important;">
                           <div id="returnChartStackChart"></div>   
                        </div>
                        <div class="tab-pane" id="returnSumGrid" style="height: 415px !important;padding: 10px;">
                        	<div class="pickSumTable">
								<table id="gridReturnedFiltering"></table>
							</div>
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 98%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#returnSumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#returnSumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#returnSumGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
    		</div>
    	</div>
        <div class="col-md-6 selectBox" ng-init="loadDeliveryPerfData(DefaultDcList,'null','allDos','current')">
        	@include('AngularLogistics::deliveryPerfReport')
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12 selectBox" ng-init="loadVehicleData(DefaultDcList,'null','allVehicles','current')">
            @include('AngularLogistics::vehicleDetail')
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
    	<div class="col-md-6 selectBox" ng-init="loadCollectionSumData(DefaultDcList,'null','allDos','current')">
            @include('AngularLogistics::collectionSumReport')
    	</div>
    	<div class="col-md-6 selectBox" ng-init="loadAllTabsSumData(DefaultDcList,'null')">
            @include('AngularLogistics::allTabsSumReport')
    	</div>
    </div>

    <div class="row" style="margin-top: 10px;">
    	<div class="col-md-6 selectBox" ng-init="loadVehUtiltySumData(DefaultDcList,'null','allVehicles','current')">
    		@include('AngularLogistics::vehicleUtilizationReport')
    	</div>
    	<div class="col-md-6 selectBox" ng-init="loadhubTotalSumData(DefaultDcList,'null','current')">
    		@include('AngularLogistics::hubTotalSumReport')
    	</div>
    </div>
    
</div>

@stop
@section('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
<style type="text/css">


[class*="creditgroup"]{
  display: none;
}

.filterInput{
	height: 20px !important;
    border-radius: 3px !important;
    margin-bottom: 5px;
    padding: 0px !important;
}

.right_align{
	text-align: right;
}
.pickSumTable{
	height: 400px;
	table-layout: fixed;
}
.page-content {
    background: #e9ecf3;
}
.pickSumTable .testClass {
	border-collapse:collapse; 
	border:1px solid #ddd;
	padding: 8px;
	min-width: 150px;
}
.perfTable {
	overflow: scroll;
	height: 335px;
	padding:10px;
	table-layout: fixed;
}

.dynamicBut{
	margin-top: 10px;
	padding-left: 15px !important;
}
.portlet-body{
    background: #fdfdfd;
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
    width: 50px
;    height: 50px;
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

 .right_align{
 	text-align: right;
    padding-right: 10px;
 }
 .center_align{
    text-align: left;
  	padding-left: 0px;
  	width: 110px !important;
 }
 .left_align{
    text-align: left;
  	padding-left: 10px;
  	width: 120px !important;
 }
.ui-grid-cell-contents {
    width: 100% !important;
}
.custom-control{
 	width: 20px !important;
    position: absolute;
    right: 10px !important;
    color: black;
    background: #f7f7f7 !important;
    height: 20px !important;
}
.ui-iggrid-pagerrecordslabel {
    left: 30% !important;
    display: none !important;
}
.igGridNumber{
	text-align: right;
}
.ui-iggrid tfoot td.ui-state-default .ui-icon.ui-iggrid-icon-summaries {
    /* background: transparent url(images/igGrid/filter-icons-333333.png) no-repeat -416px 0; */
    display: none !important;
}
.ui-iggrid-summaries-footer-text-container {
    font-size: .9em;
    text-align: center !important;
    font-weight: bold;
    margin-left: 0px !important;
}
.ui-widget-footer {
    border: 1px solid #ddd !important;
}
.igGridHEader{
	text-align: right !important;
}
.header_center_align{
  text-align: left;
  width: 120px !important;
}
.header_right_align{
	text-align: right !important;
    padding-right: 5px;
}
#VehiclesDetailGrid{ table-layout: auto!important;}
#VehiclesDetailGrid > thead > tr > th {padding: 0px 5px 0px 5px !important;}
#VehiclesDetailGrid > tbody > tr > td {height: 25px !important; padding: 0px 5px 0px 20px;}

.dateleft_align{
	padding-left: 10px !important;
}
.PAleft_align{
	padding-left: 15px !important;
}
.Hubleft_align{
	padding-left: 10px !important;
}
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="{{ URL::asset('assets/global/scripts/angular-fusioncharts.min.js') }}"></script>
<script src="https://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script  type="text/javascript">
    var app = angular.module('logisticApp', ["ng-fusioncharts"],function($interpolateProvider){
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });
    
    app.controller("logisticController",function($scope,$http,$rootScope,$filter,$window,$location) { 
    	var baseUrl = '<?php echo env('EBUTOR_NODE_URL'); ?>';
        $scope.dcList = JSON.parse('<?php echo json_encode($data); ?>');
        angular.forEach($scope.dcList.DC, function(values, keys){
            console.log(keys);
            $scope.DefaultDcList = keys;
            return;
        });
        $scope.hubList = [];
        $scope.PickerData = $scope.dcList.Pickers;
        $scope.vehicleList = $scope.dcList.Vehicle;
        $scope.fromDatePicker;

        //Hub List
        $scope.loadHubData = function(value){
            angular.forEach($scope.dcList.Hub, function(hubValues, hubKeys){
                if(value == hubKeys){
                    $scope.hubList = hubValues;
                }
            });
        }
        //Date List
        $scope.loadSelectedDates = function(value){
        	var filterDate  = value;
        	var fromDate;
        	var periodTypeVal;

            switch(filterDate){
                case 'wtd':
                    var curr = new Date; // get current date
                    var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
                    var firstday = new Date(curr.setDate(first));
                    fromDate = firstday;
                    periodTypeVal = 2;
                    break;
                case 'mtd':
                    var date = new Date();
                    var fromDate =new Date(date.getFullYear(), date.getMonth(), 1);
                    periodTypeVal = 1;
                    break;
                case 'quater':
                    var fromDate = new Date(new Date().getFullYear(), 0, 1);
                    periodTypeVal = 4;
                    break;
                case 'ytd':
                    var fromDate = new Date(new Date().getFullYear(), 0, 1);
                    periodTypeVal = 3;
                    break;
                case 'yesterday':
                    fromDate = new Date();
                    fromDate = new Date(fromDate.setDate(fromDate.getDate()-1));
                    periodTypeVal = 1;
                    break;
                default:
                    fromDate = new Date();
                    periodTypeVal = 1;
                    break;
            }
            $scope.fromDatePicker = $filter('date')(fromDate, 'yyyy-MM-dd');
            $scope.selectedPeriodType = periodTypeVal;
        }

        $scope.toggleReturnSum = function(){
			$scope.toggleVariableReturnSum = !$scope.toggleVariableReturnSum;
		}
		$scope.toggleDelPerf = function(){
			$scope.toggleVarDelPerf = !$scope.toggleVarDelPerf;
		}
        $scope.toggleVehicle = function(){
            $scope.toggleVarVehicle = !$scope.toggleVarVehicle;
        }
        $scope.toggleCollectionSummary = function(){
			$scope.toggleCollectionSummaryVar = !$scope.toggleCollectionSummaryVar;
		}
        $scope.toggleAllTabsSumSummary = function(){
            $scope.toggleAllTabsSumSummaryVar = !$scope.toggleAllTabsSumSummaryVar;
        }
        $scope.toggleVehUtilitySummary = function(){
            $scope.toggleVehUtilitySummaryVar = !$scope.toggleVehUtilitySummaryVar;
        }
        $scope.togglehubTotalSumSummary = function(){
            $scope.togglehubTotalSumSummaryVar = !$scope.togglehubTotalSumSummaryVar;
        }

        // Pickers Data
        $scope.changePickerData = function(dcid,hubId){
        	var pickersdata = $scope.dcList.Pickers;
        	$scope.PickerData = [];
           	if(hubId != "allHub" ){
        	angular.forEach(pickersdata,function(key,values){
        		if(key.WarehouseId ==dcid || key.WarehouseId==hubId ){
        			$scope.PickerData.push(key);
        		}
        	});

        	}else{
        		$scope.PickerData= pickersdata;
        	}

        }

        // Delivery Executive Data
        $scope.changedeliveryData = function(dcid,hubId){
        	var deliverydata = $scope.dcList.DE;
        	$scope.Deliverydata = [];
           	if(hubId != "allHub" ){
        	angular.forEach(deliverydata,function(key,values){
        		if(key.WarehouseId ==dcid || key.WarehouseId==hubId ){
        			$scope.Deliverydata.push(key);
        		}
        	});

        	}else{
        		$scope.Deliverydata= deliverydata;
        	}

        }

        // Return Summary
        $scope.loadReturnSumData = function(dcValue, hubValue,selectedReturnDO,fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(selectedReturnDO==undefined || selectedReturnDO == "allDos"){
                selectedReturnDO = 'null';
            }
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#returnSumFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#returnSumToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selectReturnDc = dcValue;
                $scope.selectReturnHub = 'allHub';
                $scope.selectedReturnDO = 'allDos';
                $scope.selectReturnDate = 'today';
                $scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectReturnHub);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':selectedReturnDO,
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPIReturnsReport',
                'period_type': $scope.selectedPeriodType
            };
            var returnCheckData = [];
            var returnTempChck = [];
            $scope.returnPieData = [];
            $scope.returnCategoryList = [];
            $scope.returnChartDataset = [];
            var returnedGridDefsData = [];
            var returnedGridDefsDataTest = [];
            var columnReturnedKeys = [];
            $scope.returnedGridData = [];
            var tempReturnGridDefs = [];
            var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
            $("#loadReturnSumData").show()
            req.success(function(successCallback){
                $("#loadReturnSumData").hide()
                if(successCallback.status == 'Success'){
                    var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                    returnedGridDefsData.push(tempGridHub);
                    var tempFile = {
                        name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                            return value.toLocaleDateString();
                        }
                    };
                    returnedGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                    columnReturnedKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                returnCheckData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            returnTempChck[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });

                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    returnedGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    returnedGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columnReturnedKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                    var contsPie = {
                                        label: inSecSuccKeys,
                                        value: inSecSuccValue.count,
                                        displayValue: inSecSuccValue.count_value,
                                        tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count + '{br} Value: '+ inSecSuccValue.count_value
                                    };
                                    $scope.returnPieData.push(contsPie);
                                });
                            });
                        }else{
                            var categoryTemp = {
                                "label" : succKeys
                            };
                            $scope.returnCategoryList.push(categoryTemp);
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
                                    // hub data for grid
                                    var GridReturnedHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridReturnedData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            //tempKeys = finalLinekeys;
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridReturnedData[tempKeys] = finalLineValues.count;

                                            // Dynamic Grid Defs
                                            if(tempReturnGridDefs.indexOf(finalLinekeys) == -1){
                                                tempReturnGridDefs.push(finalLinekeys);
                                                //cycleGridDefsData
                                                var headerKey = finalLinekeys.replace(/\s/g,'');
                                                returnedGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                returnedGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                                                columnReturnedKeys.push(colTemop);
                                            }
                                        });
                                    });
                                    var comGridData = $.extend( GridReturnedHubData, gridReturnedData );
                                    $scope.returnedGridData.push(comGridData);
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

                    // Ignite Grid For Picking
                    $(function () {
                        var f = true, ds, schema;

                        schema = new $.ig.DataSchema("array", {
                            fields: returnedGridDefsDataTest,
                        });
                        ds = new $.ig.DataSource({
                            dataSource: $scope.returnedGridData,
                            schema: schema,
                            filtering: {
                                type: "local"
                            }
                        }).dataBind();
                        createSimpleFilteringGrid(f, ds);
                    });

                    function createSimpleFilteringGrid(f, ds) {
                        var features = [
                                {
                                    name: "Paging",
                                    type: "local",
                                    pageSize: 10
                                },
                                {
                                    name: "Sorting",
                                    type: "local",
                                    persist: true
                                },
                                {
                                    name: 'ColumnFixing',
                                    type: "local"
                                },
                                {
                                    name: "Summaries",
                                    columnSettings:  columnReturnedKeys, 
        							defaultDecimalDisplay: 0
                                },
                                {
                                    name: 'Resizing'
                                }
                        ];

                        if (f) {
                            features.push({
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: columnReturnedKeys
                            });
                        }
                        if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                                $("#filterByText").igTextEditor("destroy");
                                $("#filterByText").remove();
                                $("#searchLabel").remove();
                        }
                        if ($("#gridReturnedFiltering").data("igGrid")) {
                            $("#gridReturnedFiltering").igGrid("destroy");
                        }
                        $("#gridReturnedFiltering").igGrid({
                            autoGenerateColumns: false,
                            height: "400px",
                            width: "100%",
                            columns: returnedGridDefsData,
                            dataSource: $scope.returnedGridData,
                            features: features
                        });
                    }
                // End


                    for(let k in returnCheckData){
                        var data = {
                            "seriesname": k,
                            "data": returnCheckData[k]
                        }
                        if(data.seriesname != "undefined"){
                            $scope.returnChartDataset.push(data);
                        }
                    }
                    for(let k in returnTempChck){
                        var data = {
                            "seriesname": k,
                            "renderAs": "line",
                            "showValues": "0",
                            "data": returnTempChck[k]
                        }
                        $scope.returnChartDataset.push(data);
                    }

                    // Return Summary Pie Chart
                    var propertiesreturnChartObject = {
                        type : "pie3d",
                        id : "returnChart-chart",
                        width : "100%",
                        height: "400",
                        renderAt: "returnChart-container",
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
                                caption: 'Return Summary',
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff",
                                //caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            data:$scope.returnPieData
                        }
                    }
                    var returnChartChart = new FusionCharts(propertiesreturnChartObject);
                    returnChartChart.render();

                    // multi series column
                    var stackReturnPropertiesObject = {
                        type: 'stackedColumn3DLine',
                        renderAt: 'returnChartStackChart',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                //"numberPrefix": "₹",
                                showvalues: "0",
                                rotateValues: "1",
                                caption: 'Return Summary',
                                yaxisname: 'Count',
                                xaxisname: 'Date',
                                placeValuesInside: "1",
                                exportEnabled: "1",
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff"
                                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            "categories": [
                                {
                                    "category": $scope.returnCategoryList
                                }
                            ],
                            "dataset": $scope.returnChartDataset
                        }
                    }
                    var returnStackedChart = new FusionCharts(stackReturnPropertiesObject);
                    returnStackedChart.render();
                }

            });
            req.error(function(errorCallback){
                $("#loadReturnSumData").hide();
            });
        }

        // Delivery Performance summary
        $scope.loadDeliveryPerfData = function(dcValue, hubValue,selectedDelOff, fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(selectedDelOff==undefined || selectedDelOff == "allDos"){
                selectedDelOff = 'null';
            }
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#DelPerfFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#DelPerfToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selectDelPerfHub = 'allHub';
                $scope.selectDelPerfDc = dcValue; 
                $scope.selectDelPerfDO = 'allDos';
                $scope.selectDelPerfDate = 'today';
                $scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectDelPerfHub);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':selectedDelOff,
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPIDeliveryExecPerfReport',
                'period_type': $scope.selectedPeriodType
            };
            $scope.tempDelPerfData = [];
            $scope.stackDelPerfChartDataset = [];
            $scope.stackDelPerfCategoryList = [];
            var chckDelPerfData = [];
            var tempChckDelPerfData = [];
            $scope.chckDelPerfDataTemp = [];
            $scope.delPerfGridDefs = [];
            var delPerfGridDefstemp = [];
            var delPerfGridDefsData = [];
            var delPerfGridDefsDataTest = [];
            var columnDelPerfKeys = [];
            $scope.delPerfGridData = [];
            var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
            $("#loadDeliveryPerfData").show();
            req.success(function(successCallback){
                var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                delPerfGridDefsData.push(tempGridHub);
                var tempFile = {
                    name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                        return value.toLocaleDateString();
                    }
                };
                delPerfGridDefsDataTest.push(tempFile);
                var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                columnDelPerfKeys.push(colTemop);

                $("#loadDeliveryPerfData").hide();

                if(successCallback.status == 'Success'){
                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                chckDelPerfData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            tempChckDelPerfData[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });
                    
                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    delPerfGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    delPerfGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columnDelPerfKeys.push(colTemop);
                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                        var contsPie = {
                                            label: inSecSuccKeys,
                                            value: inSecSuccValue.count,
                                            displayValue: inSecSuccValue.count_value,
                                            tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count + '{br} Value: '+ inSecSuccValue.count_value
                                        };
                                        $scope.tempDelPerfData.push(contsPie);
                                });
                            });
                        }else{
                            
                            var categoryTemp = {
                                "label" : succKeys
                            };
                                
                            $scope.stackDelPerfCategoryList.push(categoryTemp);
                            angular.forEach(succValues, function(seriesValues, seriesKeys){
                                if(seriesValues.hub != 'Grand Total'){
                                    var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
                                    temp = temp.replace(/[\[\]']+/g, '<br/>');
                                    temp = temp.replace(/,/g, '<br/>');
                                    temp = temp.replace(/tool_tip: null/g, '');
                                    temp = temp.replace(/count_value/g, 'Value');
                                    temp = temp.replace(/count/g, 'Count');
                                    chckDelPerfData[seriesValues.hub].push({
                                        'value':seriesValues.order_count,
                                        'displayValue': seriesValues.order_value,
                                        tooltext: seriesValues.hub + "{br}{br}Total Count:" + seriesValues.order_count +
                                                    "{br}Total Value:" + seriesValues.order_value + "{br}"  + temp
                                    });

                                    // hub data for grid
                                    var GridHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridPickData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridPickData[tempKeys] = finalLineValues.count;
                                        });
                                    });
                                    var comGridData = $.extend( GridHubData, gridPickData );
                                    $scope.delPerfGridData.push(comGridData);
                                }else{
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                                tempChckDelPerfData[finalLinekeys].push(
                                                    {'value':finalLineValues.count, 'displayValue':finalLineValues.count_value}
                                                );
                                                var gridTemp = {
                                                    field: finalLinekeys
                                                };
                                                $scope.delPerfGridDefs.push(gridTemp);
                                        });                                     
                                    });
                                }
                            });
                        }
                    });
                }
                                

                var newArr = $scope.delPerfGridDefs.filter(el => {
                    if (delPerfGridDefstemp.indexOf(el.field) === -1) {
                        // If not present in array, then add it
                        var headerKey = el.field.replace(/\s/g,'');
                        delPerfGridDefstemp.push(el.field);
                        delPerfGridDefsData.push({ headerText:el.field, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                        delPerfGridDefsDataTest.push({ name:el.field, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                        var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                        columnDelPerfKeys.push(colTemop);
                        return true;
                    } else {
                        // Already present in array, don't add it
                        return false;
                    }
                });
                //$scope.gridOptions.columnDefs = delPerfGridDefsData;
                //$scope.gridOptions.data = $scope.delPerfGridData;

                // Ignite Grid For deliveryPerf
                $(function () {
                    var f = true, ds, schema;

                    schema = new $.ig.DataSchema("array", {
                        fields: delPerfGridDefsDataTest,
                    });
                    ds = new $.ig.DataSource({
                        dataSource: $scope.delPerfGridData,
                        schema: schema,
                        filtering: {
                            type: "local"
                        }
                    }).dataBind();
                    createSimpleFilteringGrid(f, ds);
                });

                function createSimpleFilteringGrid(f, ds) {
                    var features = [
                            {
                                name: "Paging",
                                type: "local",
                                pageSize: 10
                            },
                            {
                                name: "Sorting",
                                type: "local",
                                persist: true
                            },
                            {
                                name: 'ColumnFixing',
                                type: "local"
                            },
                            {
                                name: "Summaries",
                                columnSettings:  columnDelPerfKeys, 
        						defaultDecimalDisplay: 0
                            },
                            {
                                name: 'Resizing'
                            }
                    ];

                    if (f) {
                        features.push({
                            name: "Filtering",
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings: columnDelPerfKeys
                        });
                    }
                if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                        $("#filterByText").igTextEditor("destroy");
                        $("#filterByText").remove();
                        $("#searchLabel").remove();
                }
                if ($("#gridDelPerformance").data("igGrid")) {
                    $("#gridDelPerformance").igGrid("destroy");
                }
                $("#gridDelPerformance").igGrid({
                    autoGenerateColumns: false,
                    height: "400px",
                    width: "100%",
                    columns: delPerfGridDefsData,
                    dataSource: $scope.delPerfGridData,
                    features: features
                });

                // End
            }


                for(let k in chckDelPerfData){
                    var data = {
                        "seriesname": k,
                        "data": chckDelPerfData[k]
                    }
                    if(data.seriesname != "undefined"){
                        $scope.stackDelPerfChartDataset.push(data);
                    }
                }
                for(let k in tempChckDelPerfData){
                    var data = {
                        "seriesname": k,
                        "renderAs": "line",
                        "showValues": "0",
                        "data": tempChckDelPerfData[k]
                    }
                    $scope.stackDelPerfChartDataset.push(data);
                }

                // deliveryPerf Summary Pie Chart
                var propertiesObject = {
                    type : "pie3d",
                    id : "deliveryPerf-chart",
                    width : "100%",
                    height: "400",
                    renderAt: "delPerfChart-container",
                    startingangle: "120",
                    dataFormat:"json", 
                    dataSource: {
                        chart:{
                            showlabels: "0",
                            showlegend: "1",
                            enablemultislicing: "1",
                            slicingdistance: "15",
                            showpercentvalues: "1",
                            showpercentintooltip: "0",
                            borderThickness:3,
                            exportEnabled: "1",
                            caption: 'Delivery Performance Summary',
                            toolTipBorderColor: "#666666",
                            toolTipBgColor: "#666666",
                            toolTipColor: "#ffffff",
                            exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                            exportTargetWindow: "_self",
                            plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                        },
                        data:$scope.tempDelPerfData
                    }
                }
                var sampleChart = new FusionCharts(propertiesObject);
                sampleChart.render();

                // multi series column
                var stackPropertiesObject = {
                    type: 'stackedColumn3DLine',
                    renderAt: 'deliveryPerfStackChart',
                    width: '100%',
                    height: '400',
                    dataFormat: 'json',
                    dataSource: {
                        "chart": {
                            //"numberPrefix": "₹",
                            showvalues: "0",
                            rotateValues: "1",
                            placeValuesInside: "1",
                            caption: 'Delivery Performance Summary',
                            yaxisname: 'Count',
                            xaxisname: 'Date',
                            exportEnabled: "1",
                            exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                            exportTargetWindow: "_self",
                            toolTipBorderColor: "#666666",
                            toolTipBgColor: "#666666",
                            toolTipColor: "#ffffff"
                            //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                        },
                        "categories": [
                            {
                                "category": $scope.stackDelPerfCategoryList
                            }
                        ],
                        "dataset": $scope.stackDelPerfChartDataset
                    }
                }
                var deliveryPerfStackedChart = new FusionCharts(stackPropertiesObject);
                deliveryPerfStackedChart.render();
            });
            req.error(function(errorCallback){
            $("#loadDeliveryPerfData").show();
            });
        }

        // Vehicle Details
        $scope.loadVehicleData = function(dcValue, hubValue,selectedVehNumber,fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(selectedVehNumber==undefined || selectedVehNumber == "allVehicles"){
                selectedVehNumber = 'null';
            }
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#VehicleFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#VehicleToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selectVehicleDc = dcValue;
                $scope.selectVehicleHub = 'allHub';
                $scope.selectVehicleDO = 'allVehicles';
                $scope.selectVehicleDate = 'today';
                $scope.loadHubData(dcValue);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'vehicle_id':selectedVehNumber,
                'from_date': serviceFromDate,
                'to_date': serviceToDate
            };
            
            var req = $http.post('/logistics/getvehiclereport?dc_id=' + dcValue + '&hub_id=' + hubValue + '&vehicle_id=' +selectedVehNumber + '&start_date=' + serviceFromDate + '&end_date=' + serviceToDate);
            $("#loadVehicleData").show();
            var Vehicleheader = [];
            req.success(function(successCallback){
                $("#loadVehicleData").hide();
                if(successCallback.status == true){
        			$("#damage_grid").show();
        			var type = "";
					//var classTo = "center_align";
					//var temmpLate = "";
					//var cellClass = "center_align";
					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{4}\-\d{2}\-\d{2}$/;
					var headerKey;
        			$scope.vehicleSumData = successCallback.data;
        			angular.forEach($scope.vehicleSumData[0],function(val,key){

        				type = typeof(val);
        				if(typeof val === "number"){
               				columnCssClass = "right_align";
               				//headerCssClass = "header_right_align";
                            width = '70px';
                            type = 'number';
        				}else{
        					columnCssClass = "";             
			            	//headerCssClass = "header_center_align";
			            	type = 'string';
			            	width = '140px';
        				}
						var Dateresult = dateReg.test(val);
			            if(Dateresult){
			             	type = "date";
                            width = '80px';
			                columnCssClass = "dateleft_align";
			              	//headerCssClass = "header_center_align";
			            }

			            if(key == 'Hub_DC'){
        					headerKey = key.replace(/_/g,"/");
        					type = 'string';
        					width = '130px';
			                columnCssClass = "Hubleft_align";
        				}else if(key == 'P_A'){
        					headerKey = key.replace(/_/g,"/");
        					type = 'string';
        					width = '60px';
			                columnCssClass = "PAleft_align";
        				}else{
        					headerKey = key.replace(/_/g," ");
        				}
        				
             			if(key != 'Vehicle ID'){
             				Vehicleheader.push({key:key, headerText:headerKey, width:width, dataType:type, columnCssClass: columnCssClass });
             			}
        			});

        			$('#VehiclesDetailGrid').igGrid({
	                    dataSource: $scope.vehicleSumData,
	                    dataSourceType: "json",
	                    width: "100%",
	                    columns: Vehicleheader,
	                    initialDataBindDepth: 1,
						dataRendered: function() {                
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
						                 pageSize : 10,
						            },
						            {
						                 name: "ColumnFixing"
						            },
						            {
						                 name: "ColumnMoving"
						            },
						            {
						                 name: "Resizing"
						            }
			            ],
			            width: '100%',
			            height: '400px',
			        });
        		}else{
        			$("#damage_grid").hide();
        			$scope.gridErrorMessage = "No data found!";
        		}
            });
            req.error(function(errorCallback){
                $("#loadVehicleData").hide();
            });
        }

        // All Tabs Summary
        $scope.loadAllTabsSumData = function(dcValue, hubValue){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(hubValue == 'null'){
                $scope.selectAllTabsSumDc = dcValue;
                $scope.selectAllTabsSumHub = 'allHub';
            }
            var toDate = new Date();
            serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
            serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':'null',
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPIAllTabsSummaryReport',
                'period_type': $scope.selectedPeriodType
            };
            var allTAbsCheckData = [];
            var allTAbsTempChck = [];
            $scope.allTAbsPieData = [];
            $scope.allTAbsCategoryList = [];
            $scope.allTAbsChartDataset = [];
            var allTAbsGridDefsData = [];
            var allTAbsGridDefsDataTest = [];
            var columnallTAbsKeys = [];
            $scope.allTAbsGridData = [];
            var tempallTAbsGridDefs = [];
            var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
            $("#loadAllTabsSumData").show()
            req.success(function(successCallback){
                $("#loadAllTabsSumData").hide()
                if(successCallback.status == 'Success'){
                    var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                    allTAbsGridDefsData.push(tempGridHub);
                    var tempFile = {
                        name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                            return value.toLocaleDateString();
                        }
                    };
                    allTAbsGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                    columnallTAbsKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                allTAbsCheckData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            allTAbsTempChck[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });

                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    allTAbsGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    allTAbsGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columnallTAbsKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                    var contsPie = {
                                        label: inSecSuccKeys,
                                        value: inSecSuccValue.count,
                                        displayValue: inSecSuccValue.count_value,
                                        tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count + '{br} Value: '+ inSecSuccValue.count_value
                                    };
                                    $scope.allTAbsPieData.push(contsPie);
                                });
                            });
                        }else{
                            var categoryTemp = {
                                "label" : succKeys
                            };
                            $scope.allTAbsCategoryList.push(categoryTemp);
                            angular.forEach(succValues, function(seriesValues, seriesKeys){
                                if(seriesValues.hub != 'Grand Total'){
                                    var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
                                    temp = temp.replace(/[\[\]']+/g, '<br/>');
                                    temp = temp.replace(/,/g, '<br/>');
                                    temp = temp.replace(/tool_tip: null/g, '');
                                    temp = temp.replace(/count_value/g, 'Value');
                                    temp = temp.replace(/count/g, 'Count');
                                    allTAbsCheckData[seriesValues.hub].push({
                                                                        'value':seriesValues.order_count,
                                                                        'displayValue': seriesValues.order_value,
                                                                        tooltext: seriesValues.hub + "{br}{br}Total Count:"
                                                                                + seriesValues.order_count +
                                                                                "{br}Total Value:" + seriesValues.order_value 
                                                                                + "{br}"  + temp
                                                                    });
                                    // hub data for grid
                                    var GridReturnedHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridReturnedData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            //tempKeys = finalLinekeys;
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridReturnedData[tempKeys] = finalLineValues.count;

                                            // Dynamic Grid Defs
                                            if(tempallTAbsGridDefs.indexOf(finalLinekeys) == -1){
                                                tempallTAbsGridDefs.push(finalLinekeys);
                                                //cycleGridDefsData
                                                var headerKey = finalLinekeys.replace(/\s/g,'');
                                                allTAbsGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                allTAbsGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                                                columnallTAbsKeys.push(colTemop);
                                            }
                                        });
                                    });
                                    var comGridData = $.extend( GridReturnedHubData, gridReturnedData );
                                    $scope.allTAbsGridData.push(comGridData);
                                }else{
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            allTAbsTempChck[finalLinekeys].push({
                                                'value':finalLineValues.count, 
                                                'displayValue':finalLineValues.count_value
                                            });
                                        });                                     
                                    });
                                }
                            });
                        }
                    });

                    // Ignite Grid For All TAbs Summary
                    $(function () {
                        var f = true, ds, schema;

                        schema = new $.ig.DataSchema("array", {
                            fields: allTAbsGridDefsDataTest,
                        });
                        ds = new $.ig.DataSource({
                            dataSource: $scope.allTAbsGridData,
                            schema: schema,
                            filtering: {
                                type: "local"
                            }
                        }).dataBind();
                        createSimpleFilteringGrid(f, ds);
                    });

                    function createSimpleFilteringGrid(f, ds) {
                        var features = [
                                {
                                    name: "Paging",
                                    type: "local",
                                    pageSize: 10
                                },
                                {
                                    name: "Sorting",
                                    type: "local",
                                    persist: true
                                },
                                {
                                    name: 'ColumnFixing',
                                    type: "local"
                                },
                                {
                                    name: "Summaries",
                                    columnSettings:  columnallTAbsKeys, 
        							defaultDecimalDisplay: 0
                                },
                                {
                                    name: 'Resizing'
                                }
                        ];

                        if (f) {
                            features.push({
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: columnallTAbsKeys
                            });
                        }
                        if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                                $("#filterByText").igTextEditor("destroy");
                                $("#filterByText").remove();
                                $("#searchLabel").remove();
                        }
                        if ($("#gridAllTabsSumFiltering").data("igGrid")) {
                            $("#gridAllTabsSumFiltering").igGrid("destroy");
                        }
                        $("#gridAllTabsSumFiltering").igGrid({
                            autoGenerateColumns: false,
                            height: "400px",
                            width: "100%",
                            columns: allTAbsGridDefsData,
                            dataSource: $scope.allTAbsGridData,
                            features: features
                        });
                    }
                // End


                    for(let k in allTAbsCheckData){
                        var data = {
                            "seriesname": k,
                            "data": allTAbsCheckData[k]
                        }
                        if(data.seriesname != "undefined"){
                            $scope.allTAbsChartDataset.push(data);
                        }
                    }
                    for(let k in allTAbsTempChck){
                        var data = {
                            "seriesname": k,
                            "renderAs": "line",
                            "showValues": "0",
                            "data": allTAbsTempChck[k]
                        }
                        $scope.allTAbsChartDataset.push(data);
                    }

                    // All Tabs Summary Pie Chart
                    var propertiesAllTabsChartObject = {
                        type : "pie3d",
                        id : "allTabsChart-chart",
                        width : "100%",
                        height: "400",
                        renderAt: "chart-AllTabsSum",
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
                                caption: 'Sales Orders Summary',
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff",
                                //caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            data:$scope.allTAbsPieData
                        }
                    }
                    var allTAbsChartChart = new FusionCharts(propertiesAllTabsChartObject);
                    allTAbsChartChart.render();

                    // multi series column
                    var stackAllTabsPropertiesObject = {
                        type: 'stackedColumn3DLine',
                        renderAt: 'AllTabsSumStackChart',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                //"numberPrefix": "₹",
                                showvalues: "0",
                                rotateValues: "1",
                                caption: 'Sales Orders Summary',
                                yaxisname: 'Count',
                                xaxisname: 'Date',
                                placeValuesInside: "1",
                                exportEnabled: "1",
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff"
                                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            "categories": [
                                {
                                    "category": $scope.allTAbsCategoryList
                                }
                            ],
                            "dataset": $scope.allTAbsChartDataset
                        }
                    }
                    var allTAbsStackedChart = new FusionCharts(stackAllTabsPropertiesObject);
                    allTAbsStackedChart.render();
                }

            });
            req.error(function(errorCallback){
                $("#loadAllTabsSumData").hide();
            });
        }

        // Collection Summary
        $scope.loadCollectionSumData = function(dcValue, hubValue,selectedReturnDO,fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(selectedReturnDO==undefined || selectedReturnDO == "allDos"){
        		selectedReturnDO = 'null';
        	}
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#CollectionsumFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#CollectionsumToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selectCollectionDc = dcValue;
                $scope.selectCollectionHub = 'allHub';
                $scope.selectedCollectionDO = 'allDos';
                $scope.selectCollectionDate = 'today';
                $scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectCollectionHub);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':selectedReturnDO,
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPICollectionStatusReport',
                'period_type': $scope.selectedPeriodType
            };
            var collectionCheckData = [];
            var collectionTempChck = [];
            $scope.collectionPieData = [];
            $scope.collectionCategoryList = [];
            $scope.collectionChartDataset = [];
            var collectionGridDefsData = [];
            var collectionGridDefsDataTest = [];
            var columncollectionKeys = [];
            $scope.collectionGridData = [];
            var tempcollectionGridDefs = [];
            var req = $http.post('/kpi/collection',{'data':serviceData});
            $("#loadCollectionSumData").show()
            req.success(function(successCallback){
                $("#loadCollectionSumData").hide()
                if(successCallback.status == 'Success'){
                    var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                    collectionGridDefsData.push(tempGridHub);
                    var tempFile = {
                        name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                            return value.toLocaleDateString();
                        }
                    };
                    collectionGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                    columncollectionKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                collectionCheckData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            collectionTempChck[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });

                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    collectionGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    collectionGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columncollectionKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                    var contsPie = {
                                        label: inSecSuccKeys,
                                        value: inSecSuccValue.count,
                                        displayValue: inSecSuccValue.count_pct,
                                        tooltext: inSecSuccKeys + '{br} Value: ' + inSecSuccValue.count + '{br} Percentage: '+ inSecSuccValue.count_pct
                                    };
                                    $scope.collectionPieData.push(contsPie);
                                });
                            });
                        }else{
                            var categoryTemp = {
                                "label" : succKeys
                            };
                            $scope.collectionCategoryList.push(categoryTemp);
                            angular.forEach(succValues, function(seriesValues, seriesKeys){
                                if(seriesValues.hub != 'Grand Total'){
                                    var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
                                    temp = temp.replace(/[\[\]']+/g, '<br/>');
                                    temp = temp.replace(/,/g, '<br/>');
                                    temp = temp.replace(/tool_tip: null/g, '');
                                    temp = temp.replace(/count_pct/g, 'Percentage');
                                    temp = temp.replace(/count/g, 'Value');
                                    collectionCheckData[seriesValues.hub].push({
                                                                        'value':seriesValues.value_count,
                                                                        'displayValue': seriesValues.valuepct_count,
                                                                        tooltext: seriesValues.hub + "{br}{br}Total Value:"
                                                                                + seriesValues.value_count +
                                                                                "{br}Total Percentage:" + seriesValues.valuepct_count 
                                                                                + "{br}"  + temp
                                                                    });
                                    // hub data for grid
                                    var GridReturnedHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridReturnedData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            //tempKeys = finalLinekeys;
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridReturnedData[tempKeys] = finalLineValues.count;

                                            // Dynamic Grid Defs
                                            if(tempcollectionGridDefs.indexOf(finalLinekeys) == -1){
                                                tempcollectionGridDefs.push(finalLinekeys);
                                                //cycleGridDefsData
                                                var headerKey = finalLinekeys.replace(/\s/g,'');
                                                collectionGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                collectionGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                                                columncollectionKeys.push(colTemop);
                                            }
                                        });
                                    });
                                    var comGridData = $.extend( GridReturnedHubData, gridReturnedData );
                                    $scope.collectionGridData.push(comGridData);
                                }else{
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            collectionTempChck[finalLinekeys].push({
                                                'value':finalLineValues.count, 
                                                'displayValue':finalLineValues.count_value
                                            });
                                        });                                     
                                    });
                                }
                            });
                        }
                    });

                    // Ignite Grid For All TAbs Summary
                    $(function () {
                        var f = true, ds, schema;

                        schema = new $.ig.DataSchema("array", {
                            fields: collectionGridDefsDataTest,
                        });
                        ds = new $.ig.DataSource({
                            dataSource: $scope.collectionGridData,
                            schema: schema,
                            filtering: {
                                type: "local"
                            }
                        }).dataBind();
                        createSimpleFilteringGrid(f, ds);
                    });

                    function createSimpleFilteringGrid(f, ds) {
                        var features = [
                                {
                                    name: "Paging",
                                    type: "local",
                                    pageSize: 10
                                },
                                {
                                    name: "Sorting",
                                    type: "local",
                                    persist: true
                                },
                                {
                                    name: 'ColumnFixing',
                                    type: "local"
                                },
                                {
                                    name: "Summaries",
                                    columnSettings:  columncollectionKeys,
									defaultDecimalDisplay: 0
                                },
                                {
                                    name: 'Resizing'
                                }
                        ];

                        if (f) {
                            features.push({
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: columncollectionKeys
                            });
                        }
                        if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                                $("#filterByText").igTextEditor("destroy");
                                $("#filterByText").remove();
                                $("#searchLabel").remove();
                        }
                        if ($("#gridCollectionFiltering").data("igGrid")) {
                            $("#gridCollectionFiltering").igGrid("destroy");
                        }
                        $("#gridCollectionFiltering").igGrid({
                            autoGenerateColumns: false,
                            height: "400px",
                            width: "100%",
                            columns: collectionGridDefsData,
                            dataSource: $scope.collectionGridData,
                            features: features
                        });
                    }
                // End


                    for(let k in collectionCheckData){
                        var data = {
                            "seriesname": k,
                            "data": collectionCheckData[k]
                        }
                        if(data.seriesname != "undefined"){
                            $scope.collectionChartDataset.push(data);
                        }
                    }
                    for(let k in collectionTempChck){
                        var data = {
                            "seriesname": k,
                            "renderAs": "line",
                            "showValues": "0",
                            "data": collectionTempChck[k]
                        }
                        $scope.collectionChartDataset.push(data);
                    }

                    // Collection Summary Pie Chart
                    var propertiesCollectionChartObject = {
                        type : "pie3d",
                        id : "collectionChart-chart",
                        width : "100%",
                        height: "400",
                        renderAt: "chart-collectionSum",
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
                                caption: 'Collection Summary',
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff",
                                //caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            data:$scope.collectionPieData
                        }
                    }
                    var collectionChartChart = new FusionCharts(propertiesCollectionChartObject);
                    collectionChartChart.render();

                    // multi series column
                    var stackCollectionPropertiesObject = {
                        type: 'stackedColumn3DLine',
                        renderAt: 'CollectionSumStackChart',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                //"numberPrefix": "₹",
                                showvalues: "0",
                                rotateValues: "1",
                                caption: 'Collection Summary',
                                yaxisname: 'Count',
                                xaxisname: 'Date',
                                placeValuesInside: "1",
                                exportEnabled: "1",
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff"
                                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            "categories": [
                                {
                                    "category": $scope.collectionCategoryList
                                }
                            ],
                            "dataset": $scope.collectionChartDataset
                        }
                    }
                    var collectionStackedChart = new FusionCharts(stackCollectionPropertiesObject);
                    collectionStackedChart.render();
                }

            });
            req.error(function(errorCallback){
                $("#loadCollectionSumData").hide();
            });
        }

        // Vehicle Utilization Summary
        $scope.loadVehUtiltySumData = function(dcValue, hubValue,selectedVehNumber, fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(selectedVehNumber==undefined || selectedVehNumber == "allVehicles"){
                selectedVehNumber = 'null';
            }
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#VehUtiltysumFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#VehUtiltysumToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selectVehUtiltyDc = dcValue;
                $scope.selectVehUtiltyHub = 'allHub';
                $scope.selectVehUtiltyNumber = 'allVehicles';
                $scope.selectVehUtiltyDate = 'today';
                $scope.loadHubData(dcValue);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':selectedVehNumber,
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPIVehicleUtiliseReport',
                'period_type': $scope.selectedPeriodType
            };
            var vehicleUtilizeCheckData = [];
            var vehicleUtilizeTempChck = [];
            $scope.vehicleUtilizePieData = [];
            $scope.vehicleUtilizeCategoryList = [];
            $scope.vehicleUtilizeChartDataset = [];
            var vehicleUtilizeGridDefsData = [];
            var vehicleUtilizeGridDefsDataTest = [];
            var columnvehicleUtilizeKeys = [];
            $scope.vehicleUtilizeGridData = [];
            var tempvehicleUtilizeGridDefs = [];
            var req = $http.post('/kpi/collection',{'data':serviceData});
            $("#loadVehUtiltySumData").show()
            req.success(function(successCallback){
                $("#loadVehUtiltySumData").hide()
                if(successCallback.status == 'Success'){
                    var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                    vehicleUtilizeGridDefsData.push(tempGridHub);
                    var tempFile = {
                        name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                            return value.toLocaleDateString();
                        }
                    };
                    vehicleUtilizeGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                    columnvehicleUtilizeKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                vehicleUtilizeCheckData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            vehicleUtilizeTempChck[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });

                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    vehicleUtilizeGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    vehicleUtilizeGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columnvehicleUtilizeKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                    var contsPie = {
                                        label: inSecSuccKeys,
                                        value: inSecSuccValue.count,
                                        displayValue: inSecSuccValue.count_pct,
                                        tooltext: inSecSuccKeys + '{br} Value: ' + inSecSuccValue.count + '{br} Percentage: '+ inSecSuccValue.count_pct
                                    };
                                    $scope.vehicleUtilizePieData.push(contsPie);
                                });
                            });
                        }else{
                            var categoryTemp = {
                                "label" : succKeys
                            };
                            $scope.vehicleUtilizeCategoryList.push(categoryTemp);
                            angular.forEach(succValues, function(seriesValues, seriesKeys){
                                if(seriesValues.hub != 'Grand Total'){
                                    var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
                                    temp = temp.replace(/[\[\]']+/g, '<br/>');
                                    temp = temp.replace(/,/g, '<br/>');
                                    temp = temp.replace(/tool_tip: null/g, '');
                                    temp = temp.replace(/count_pct/g, 'Percentage');
                                    temp = temp.replace(/count/g, 'Value');
                                    vehicleUtilizeCheckData[seriesValues.hub].push({
                                                                        'value':seriesValues.value_count,
                                                                        'displayValue': seriesValues.valuepct_count,
                                                                        tooltext: seriesValues.hub + "{br}{br}Total Value:"
                                                                                + seriesValues.value_count +
                                                                                "{br}Total Percentage:" + seriesValues.valuepct_count 
                                                                                + "{br}"  + temp
                                                                    });
                                    // hub data for grid
                                    var GridReturnedHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridReturnedData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            //tempKeys = finalLinekeys;
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridReturnedData[tempKeys] = finalLineValues.count;

                                            // Dynamic Grid Defs
                                            if(tempvehicleUtilizeGridDefs.indexOf(finalLinekeys) == -1){
                                                tempvehicleUtilizeGridDefs.push(finalLinekeys);
                                                //cycleGridDefsData
                                                var headerKey = finalLinekeys.replace(/\s/g,'');
                                                vehicleUtilizeGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                vehicleUtilizeGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                                                columnvehicleUtilizeKeys.push(colTemop);
                                            }
                                        });
                                    });
                                    var comGridData = $.extend( GridReturnedHubData, gridReturnedData );
                                    $scope.vehicleUtilizeGridData.push(comGridData);
                                }else{
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            vehicleUtilizeTempChck[finalLinekeys].push({
                                                'value':finalLineValues.count, 
                                                'displayValue':finalLineValues.count_value
                                            });
                                        });                                     
                                    });
                                }
                            });
                        }
                    });

                    // Ignite Grid For All TAbs Summary
                    $(function () {
                        var f = true, ds, schema;

                        schema = new $.ig.DataSchema("array", {
                            fields: vehicleUtilizeGridDefsDataTest,
                        });
                        ds = new $.ig.DataSource({
                            dataSource: $scope.vehicleUtilizeGridData,
                            schema: schema,
                            filtering: {
                                type: "local"
                            }
                        }).dataBind();
                        createSimpleFilteringGrid(f, ds);
                    });

                    function createSimpleFilteringGrid(f, ds) {
                        var features = [
                                {
                                    name: "Paging",
                                    type: "local",
                                    pageSize: 10
                                },
                                {
                                    name: "Sorting",
                                    type: "local",
                                    persist: true
                                },
                                {
                                    name: 'ColumnFixing',
                                    type: "local"
                                },
                                {
                                    name: "Summaries",
                                    columnSettings:  columnvehicleUtilizeKeys, 
        							defaultDecimalDisplay: 0
                                },
                                {
                                    name: 'Resizing'
                                }
                        ];

                        if (f) {
                            features.push({
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: columnvehicleUtilizeKeys
                            });
                        }
                        if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                                $("#filterByText").igTextEditor("destroy");
                                $("#filterByText").remove();
                                $("#searchLabel").remove();
                        }
                        if ($("#gridvehicleUtilizeFiltering").data("igGrid")) {
                            $("#gridvehicleUtilizeFiltering").igGrid("destroy");
                        }
                        $("#gridvehicleUtilizeFiltering").igGrid({
                            autoGenerateColumns: false,
                            height: "400px",
                            width: "100%",
                            columns: vehicleUtilizeGridDefsData,
                            dataSource: $scope.vehicleUtilizeGridData,
                            features: features
                        });
                    }
                // End


                    for(let k in vehicleUtilizeCheckData){
                        var data = {
                            "seriesname": k,
                            "data": vehicleUtilizeCheckData[k]
                        }
                        if(data.seriesname != "undefined"){
                            $scope.vehicleUtilizeChartDataset.push(data);
                        }
                    }
                    for(let k in vehicleUtilizeTempChck){
                        var data = {
                            "seriesname": k,
                            "renderAs": "line",
                            "showValues": "0",
                            "data": vehicleUtilizeTempChck[k]
                        }
                        $scope.vehicleUtilizeChartDataset.push(data);
                    }

                    // vehicleUtilize Summary Pie Chart
                    var propertiesvehicleUtilizeChartObject = {
                        type : "pie3d",
                        id : "vehicleUtilizeChart-chart",
                        width : "100%",
                        height: "400",
                        renderAt: "chart-vehicleUtilizeSum",
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
                                caption: 'Vehicle Utilization Summary',
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff",
                                //caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            data:$scope.vehicleUtilizePieData
                        }
                    }
                    var vehicleUtilizeChartChart = new FusionCharts(propertiesvehicleUtilizeChartObject);
                    vehicleUtilizeChartChart.render();

                    // multi series column
                    var stackvehicleUtilizePropertiesObject = {
                        type: 'stackedColumn3DLine',
                        renderAt: 'vehicleUtilizeSumStackChart',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                //"numberPrefix": "₹",
                                showvalues: "0",
                                rotateValues: "1",
                                caption: 'Vehicle Utilization Summary',
                                yaxisname: 'Count',
                                xaxisname: 'Date',
                                placeValuesInside: "1",
                                exportEnabled: "1",
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff"
                                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            "categories": [
                                {
                                    "category": $scope.vehicleUtilizeCategoryList
                                }
                            ],
                            "dataset": $scope.vehicleUtilizeChartDataset
                        }
                    }
                    var vehicleUtilizeStackedChart = new FusionCharts(stackvehicleUtilizePropertiesObject);
                    vehicleUtilizeStackedChart.render();
                }

            });
            req.error(function(errorCallback){
                $("#loadVehUtiltySumData").hide();
            });
        }

        // Hub Total Summary
        $scope.loadhubTotalSumData = function(dcValue, hubValue,fromDate){
            var serviceFromDate;
            var serviceToDate;
            if(hubValue == 'allHub'){
                hubValue = 'null';
            }
            if(fromDate == 'customDate'){
                serviceFromDate = angular.element(document.querySelector('#hubTotalSumsumFromDate')).val();
                serviceToDate = angular.element(document.querySelector('#hubTotalSumsumToDate')).val();
                serviceFromDate = serviceFromDate.split("-").reverse().join("-");
                serviceToDate = serviceToDate.split("-").reverse().join("-");
                var diffDays = moment(serviceToDate).diff(moment(serviceFromDate), 'days');
                if(diffDays < 7){
                    $scope.selectedPeriodType = 2;
                }else if(diffDays <= 31){
                    $scope.selectedPeriodType = 1;
                }else if(diffDays > 31){
                    $scope.selectedPeriodType = 3;
                }
            }else if(fromDate == 'current'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
                $scope.selectedPeriodType = 1;
                $scope.selecthubTotalSumDc = dcValue;
                $scope.selecthubTotalSumHub = 'allHub';
                $scope.selecthubTotalSumDate = 'today';
                $scope.loadHubData(dcValue);
            }else if(fromDate == 'yesterday'){
                serviceFromDate = $scope.fromDatePicker;
                serviceToDate = $scope.fromDatePicker;
            }else if(fromDate == 'today'){
                var toDate = new Date();
                serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }else{
                serviceFromDate = $scope.fromDatePicker;
                var toDate = new Date();
                serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
            }
            serviceData = {
                'dc_id': dcValue,
                'hub_id': hubValue,
                'user_id':'null',
                'from_date': serviceFromDate,
                'to_date': serviceToDate,
                'report_type': 'getKPITotalSummaryReport',
                'period_type': $scope.selectedPeriodType
            };
            var hubTotalCheckData = [];
            var hubTotalTempChck = [];
            $scope.hubTotalPieData = [];
            $scope.hubTotalCategoryList = [];
            $scope.hubTotalChartDataset = [];
            var hubTotalGridDefsData = [];
            var hubTotalGridDefsDataTest = [];
            var columnhubTotalKeys = [];
            $scope.hubTotalGridData = [];
            var temphubTotalGridDefs = [];
            var req = $http.post('/kpi/collection',{'data':serviceData});
            $("#loadhubTotalSumData").show()
            req.success(function(successCallback){
                $("#loadhubTotalSumData").hide()
                if(successCallback.status == 'Success'){
                    var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
                    hubTotalGridDefsData.push(tempGridHub);
                    var tempFile = {
                        name: "Period", type: "date", width: '100px', formatter: function (value, record) {
                            return value.toLocaleDateString();
                        }
                    };
                    hubTotalGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
                    columnhubTotalKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        for (var i = 0; i <= succValues.length-1; i++) {
                            if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
                                hubTotalCheckData[succValues[i].hub] = new Array();
                            }else{
                                var tempp = succValues[i].values;
                                for(var j=0; j <= tempp.length-1; j++){
                                    angular.forEach(tempp[j], function(valuess, keyss){
                                            hubTotalTempChck[keyss] = new Array();
                                    });
                                }
                            }
                        }
                    });

                    var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
                    hubTotalGridDefsData.push(tempGridHub);
                    var tempFile = { name: "Hub", type: "string", width: '100px' };
                    hubTotalGridDefsDataTest.push(tempFile);
                    var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
                    columnhubTotalKeys.push(colTemop);

                    angular.forEach(successCallback.data, function(succValues, succKeys){
                        if(succKeys == 'Total'){
                            angular.forEach(succValues, function(innerSuccValue, innerSuccKeys){
                                angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
                                    var contsPie = {
                                        label: inSecSuccKeys,
                                        value: inSecSuccValue.count,
                                        displayValue: inSecSuccValue.count_pct,
                                        tooltext: inSecSuccKeys + '{br} Value: ' + inSecSuccValue.count + '{br} Percentage: '+ inSecSuccValue.count_pct
                                    };
                                    $scope.hubTotalPieData.push(contsPie);
                                });
                            });
                        }else{
                            var categoryTemp = {
                                "label" : succKeys
                            };
                            $scope.hubTotalCategoryList.push(categoryTemp);
                            angular.forEach(succValues, function(seriesValues, seriesKeys){
                                if(seriesValues.hub != 'Grand Total'){
                                    var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
                                    temp = temp.replace(/[\[\]']+/g, '<br/>');
                                    temp = temp.replace(/,/g, '<br/>');
                                    temp = temp.replace(/tool_tip: null/g, '');
                                    temp = temp.replace(/count_pct/g, 'Percentage');
                                    temp = temp.replace(/count/g, 'Value');
                                    hubTotalCheckData[seriesValues.hub].push({
                                                                        'value':seriesValues.value_count,
                                                                        'displayValue': seriesValues.valuepct_count,
                                                                        tooltext: seriesValues.hub + "{br}{br}Total Value:"
                                                                                + seriesValues.value_count +
                                                                                "{br}Total Percentage:" + seriesValues.valuepct_count 
                                                                                + "{br}"  + temp
                                                                    });
                                    // hub data for grid
                                    var GridReturnedHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
                                    var gridReturnedData = {};
                                    var tempKeys;
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            //tempKeys = finalLinekeys;
                                            var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
                                            tempKeys = spaceRemoveFromKey;
                                            gridReturnedData[tempKeys] = finalLineValues.count;

                                            // Dynamic Grid Defs
                                            if(temphubTotalGridDefs.indexOf(finalLinekeys) == -1){
                                                temphubTotalGridDefs.push(finalLinekeys);
                                                //cycleGridDefsData
                                                var headerKey = finalLinekeys.replace(/\s/g,'');
                                                hubTotalGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                hubTotalGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
                                                var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
                                                columnhubTotalKeys.push(colTemop);
                                            }
                                        });
                                    });
                                    var comGridData = $.extend( GridReturnedHubData, gridReturnedData );
                                    $scope.hubTotalGridData.push(comGridData);
                                }else{
                                    angular.forEach(seriesValues.values, function(lineValues, lineKeys){
                                        angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
                                            hubTotalTempChck[finalLinekeys].push({
                                                'value':finalLineValues.count, 
                                                'displayValue':finalLineValues.count_value
                                            });
                                        });                                     
                                    });
                                }
                            });
                        }
                    });

                    // Ignite Grid For All TAbs Summary
                    $(function () {
                        var f = true, ds, schema;

                        schema = new $.ig.DataSchema("array", {
                            fields: hubTotalGridDefsDataTest,
                        });
                        ds = new $.ig.DataSource({
                            dataSource: $scope.hubTotalGridData,
                            schema: schema,
                            filtering: {
                                type: "local"
                            }
                        }).dataBind();
                        createSimpleFilteringGrid(f, ds);
                    });

                    function createSimpleFilteringGrid(f, ds) {
                        var features = [
                                {
                                    name: "Paging",
                                    type: "local",
                                    pageSize: 10
                                },
                                {
                                    name: "Sorting",
                                    type: "local",
                                    persist: true
                                },
                                {
                                    name: 'ColumnFixing',
                                    type: "local"
                                },
                                {
                                    name: "Summaries",
                                    columnSettings:  columnhubTotalKeys, 
        							defaultDecimalDisplay: 0
                                },
                                {
                                    name: 'Resizing'
                                }
                        ];

                        if (f) {
                            features.push({
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: columnhubTotalKeys
                            });
                        }
                        if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
                                $("#filterByText").igTextEditor("destroy");
                                $("#filterByText").remove();
                                $("#searchLabel").remove();
                        }
                        if ($("#gridhubTotalSumFiltering").data("igGrid")) {
                            $("#gridhubTotalSumFiltering").igGrid("destroy");
                        }
                        $("#gridhubTotalSumFiltering").igGrid({
                            autoGenerateColumns: false,
                            height: "400px",
                            width: "100%",
                            columns: hubTotalGridDefsData,
                            dataSource: $scope.hubTotalGridData,
                            features: features
                        });
                    }
                // End


                    for(let k in hubTotalCheckData){
                        var data = {
                            "seriesname": k,
                            "data": hubTotalCheckData[k]
                        }
                        if(data.seriesname != "undefined"){
                            $scope.hubTotalChartDataset.push(data);
                        }
                    }
                    for(let k in hubTotalTempChck){
                        var data = {
                            "seriesname": k,
                            "renderAs": "line",
                            "showValues": "0",
                            "data": hubTotalTempChck[k]
                        }
                        $scope.hubTotalChartDataset.push(data);
                    }

                    // Hub Total Summary Pie Chart
                    var propertieshubTotalChartObject = {
                        type : "pie3d",
                        id : "hubTotalChart-chart",
                        width : "100%",
                        height: "400",
                        renderAt: "chart-hubTotalSum",
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
                                caption: 'All Hub Summary',
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff",
                                //caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            data:$scope.hubTotalPieData
                        }
                    }
                    var hubTotalChartChart = new FusionCharts(propertieshubTotalChartObject);
                    hubTotalChartChart.render();

                    // multi series column
                    var stackhubTotalPropertiesObject = {
                        type: 'stackedColumn3DLine',
                        renderAt: 'hubTotalSumStackChart',
                        width: '100%',
                        height: '400',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                //"numberPrefix": "₹",
                                showvalues: "0",
                                rotateValues: "1",
                                caption: 'All Hub Summary',
                                yaxisname: 'Count',
                                xaxisname: 'Date',
                                placeValuesInside: "1",
                                exportEnabled: "1",
                                exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
                                exportTargetWindow: "_self",
                                toolTipBorderColor: "#666666",
                                toolTipBgColor: "#666666",
                                toolTipColor: "#ffffff"
                                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
                            },
                            "categories": [
                                {
                                    "category": $scope.hubTotalCategoryList
                                }
                            ],
                            "dataset": $scope.hubTotalChartDataset
                        }
                    }
                    var hubTotalStackedChart = new FusionCharts(stackhubTotalPropertiesObject);
                    hubTotalStackedChart.render();
                }

            });
            req.error(function(errorCallback){
                $("#loadhubTotalSumData").hide();
            });
        }

});
</script>

<script type="text/javascript">

	$('#returnSumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#returnSumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

    $('#VehiclefromDate').datetimepicker({
        format: "DD-MM-YYYY"
    });

    $('#VehicletoDate').datetimepicker({
        format: "DD-MM-YYYY"
    });

	$('#DelPerfFromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#DelPerfToDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#CollectionsumfromDate').datetimepicker({
        format: "DD-MM-YYYY"
    });

    $('#CollectionsumtoDate').datetimepicker({
        format: "DD-MM-YYYY"
    });

	$('#AllTabsSumsumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#AllTabsSumsumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#VehUtiltysumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#VehUtiltysumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#hubTotalSumsumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#hubTotalSumsumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

</script>
@stop
@extends('layouts.footer')
