@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Dc Ops'); ?>
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
    <div class="row">
    	<div class="col-md-6 selectBox" ng-init="loadPickerData(DefaultDcList,'null','allPickers','current')">
            <div class="portlet-body" style="height: 500px;width: 103%;">
        		<div id="loadPickerData" style="display: none" class="loader" ></div>
                <div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="toggle()">
                		<span style="font-weight: 600;">Picking Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="toggleVariable">
	                    <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
	                        <select ng-model="selectedDc" ng-change="loadHubData(selectedDc)">
	                            <option value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                        <select ng-model="selectedHub" ng-change="changePickerData(selectedDc,selectedHub)">
                            <option disabled="disabled" value="">---- Select HUB ----</option>
                            <option value="allHub">All</option>
                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
	                        <select ng-model="selectedPickers">
	                            <option disabled="disabled" value="">---- Select Picker ----</option>
	                            <option value="allPickers">All</option>
                           		<option ng-repeat="(key,value) in PickerData" value="<%value.UserId%>"><%value.UserName%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;" class="col-md-2">
	                        <select ng-model="selectedDate" ng-change="loadSelectedDates(selectedDate)">
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
	                    <div ng-show="selectedDate == 'customDate'">
	                    	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='datetimepickerfromDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="reqFromDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='datetimepickertoDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="reqToDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
	                    </div>
	                    <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectedDate == 'customDate'}">
	                        <button ng-click="loadPickerData(selectedDc,selectedHub,selectedPickers,selectedDate)">Go</button>
	                    </div>
	                </div>
                </div> 
                <div class="tabbable tabs-below">
                    <div class="tab-content">
                        <div class="tab-pane active" id="pickingPie" style="height: 415px !important;"> 
                           <div id="chart-container"></div>   
                        </div>
                        <div class="tab-pane" id="pickingStacked" style="height: 415px !important;"> 
                           <div id="pickingStackChart"></div> 
                        </div>
                        <div class="tab-pane" id="pickingGrid" style="height: 415px !important;padding: 10px;">
                        	<div class="pickSumTable">
								<table id="gridSimpleFiltering"></table>
							</div>
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 97%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#pickingPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#pickingStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#pickingGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 selectBox" ng-init="loadCheckersData(DefaultDcList,'null','allPickers','current')">
			<div class="portlet-body" style="height: 500px;">
        		<div id="loadCheckersData" style="display: none" class="loader" ></div>
				<div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="CheckingSummary()">
                		<span style="font-weight: 600;">Checking Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="CheckingSummaryVariable">
	                    <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
	                        <select ng-model="selectCheckingDc" ng-change="loadHubData(selectCheckingDc)">
	                            <option value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                        <select ng-model="selectCheckingHub" ng-change="changePickerData(selectCheckingDc,selectCheckingHub)">
                            <option disabled="disabled" value="">---- Select HUB ----</option>
                            <option value="allHub">All</option>
                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
	                        <select ng-model="selectCheckingPickers">
	                            <option disabled="disabled" value="">---- Select Picker ----</option>
	                            <option value="allPickers">All</option>
                           		<option ng-repeat="(key,value) in PickerData" value="<%value.UserId%>"><%value.UserName%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;" class="col-md-2">
	                        <select ng-model="selectCheckingDate" ng-change="loadSelectedDates(selectCheckingDate)">
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
	                    <div ng-show="selectCheckingDate == 'customDate'">
	                    	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='checkingfromDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="checkingFromDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='checkingtoDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="checkingToDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
	                    </div>
	                    <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectCheckingDate == 'customDate'}">
	                        <button ng-click="loadCheckersData(selectCheckingDc,selectCheckingHub,selectCheckingPickers,selectCheckingDate)">Go</button>
	                    </div>
	                </div>
                </div> 
                <div class="tabbable tabs-below">
                    <div class="tab-content">
                        <div class="tab-pane active" id="checkingPie" style="height: 415px !important;"> 
                           <div id="checking-container"></div>   
                        </div>
                        <div class="tab-pane" id="checkingStacked" style="height: 415px !important;"> 
                           <div id="checkingStackChart"></div> 
                        </div>
                        <div class="tab-pane" id="checkingGrid" style="height: 415px !important;padding: 10px;">
                        	<div class="pickSumTable">
								<table id="gridCheckingFiltering"></table>
							</div>
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 95%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#checkingPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#checkingStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#checkingGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
			</div>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
		<div class="col-md-6 selectBox">
    		<div class="portlet-body" style="height: 500px;width: 103%;">
        	<!-- <div id="loadReturnSumData" style="display: none" class="loader" ></div>
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
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 97%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#returnSumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#returnSumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#returnSumGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div> -->
    		</div>
    	</div>
    	@include('AngularLogistics::holdSummary')
    </div>
    <div class="row" style="margin-top: 10px;">
    	<div class="col-md-6 selectBox" ng-init="loadCrateSumData(DefaultDcList,'')">
    		<div class="portlet-body" style="height: 500px;width: 103%;">
			<div id="loadCrateSumData" style="display: none;" class="loader"></div>
    			<div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="toggleCrateSum()">
                		<span style="font-weight: 600;">Crate Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="toggleVariableCrateSum">
	                    <div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
	                        <select ng-model="selectCrateSumDc" ng-change="loadHubData(selectCrateSumDc)">
	                            <option selected value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-4">
	                        <select ng-model="selectCrateSumHub">
	                            <option disabled="disabled">---- Select HUB ----</option>
	                            <option value="allHub">All</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                   	<div style="margin-top: 8px;" class="col-md-1">
	                        <button ng-click="loadCrateSumData(selectCrateSumDc,selectCrateSumHub)">Go</button>
	                    </div>
	                </div>
                </div> 
                <div class="tabbable tabs-below">
                	<div class="tab-content">
                        <div class="tab-pane active" id="crateSumPie" style="height: 415px !important;"> 
                        	<div id="crateSum-container"></div>   
                        </div>
                        <div class="tab-pane" id="crateSumStacked" style="height: 415px !important;">
                        	<div id="crateStackChart"></div> 
                        </div>
                        <div class="tab-pane" id="crateSumGrid" style="height: 415px !important;padding: 10px;">
                        	<div class="pickSumTable">
								<!-- <div ui-grid-pagination ui-grid-pinning ui-grid-resize-columns ui-grid-move-columns ng-mouseover="ngGridFIx()" ui-grid="gridCrateCountOptions" ui-grid-exporter class="myGrid"></div> -->
								<table id="gridCrateFiltering"></table>
							</div>
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 97%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#crateSumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#crateSumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#crateSumGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
    		</div>
    	</div>
    	<div class="col-md-6 selectBox" ng-init="loadCycleSumData(DefaultDcList,'current')">
    		<div class="portlet-body" style="height: 500px;">
    			<div id="loadCycleSumData" style="display: none;" class="loader"></div>
                <div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="toggleCycleSummary()">
                		<span style="font-weight: 600;">Cycle Count Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="toggleCycleSummaryVariable">
	                    <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
	                        <select ng-model="selectCycleSumDc" ng-change="loadHubData(selectCycleSumDc)">
	                            <option selected value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div style="margin-top: 8px;" class="col-md-2">
	                        <select ng-model="selectDateCycleSum" ng-change="loadSelectedDates(selectDateCycleSum)">
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
	                    <div ng-show="selectDateCycleSum == 'customDate'">
	                    	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='CycleSumfromDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="CycleSumFromDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
								<div class="form-group">
									<div class='input-group date' id='CycleSumtoDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="CycleSumToDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
	                    </div>
	                    <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectDateCycleSum == 'customDate'}">
	                        <button ng-click="loadCycleSumData(selectCycleSumDc,selectDateCycleSum)">Go</button>
	                    </div>
	                </div>
                </div> 
                <div class="tabbable tabs-below">
                    <div class="tab-content">
                        <div class="tab-pane active" id="CycleSumPie" style="height: 415px !important;"> 
                           <div id="chart-cycleCount"></div>   
                        </div>
                        <div class="tab-pane" id="CycleSumStacked" style="height: 415px !important;">
                           <div id="cycleStackChart"></div> 
                        </div>
                        <div class="tab-pane" id="CycleSumGrid" style="height: 415px !important;padding:10px;">
                        	
                        	<div class="pickSumTable">
								<!-- <div ui-grid-pagination ui-grid-pinning ui-grid-resize-columns ui-grid-move-columns ng-mouseover="ngGridFIx()" ui-grid="gridCycleCountOptions" ui-grid-exporter class="myGrid"></div> -->
								<table id="gridCycleFiltering"></table>
							</div>
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 95%;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#CycleSumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                            <li class="custIcon"><a href="#CycleSumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                            <li class="custIcon"><a href="#CycleSumGrid" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
    	</div>
    </div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-6 selectBox" ng-init="loadInventoryData(DefaultDcList,'current')">
		@include('AngularLogistics::inventoryReport')
	</div>
	<div class="col-md-6 selectBox" ng-init="loadDeliveryData(DefaultDcList,'null','allDos','current')">
		@include('AngularLogistics::deliveryReport')
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	@include('AngularLogistics::damageReport')
</div>
<!-- <div class="row" style="    margin-top: 10px;">
    	<div class="col-md-12 selectBox" >
            <div class="portlet-body">
                <div class="row" style="padding:10px;">
                	<div class="col-md-12" ng-click="togglePerformanceSummary()">
                		<span style="font-weight: 600;">Performance Summary</span>
                		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                	</div>
                	<div ng-show="togglePerformSummaryVar">
	                    <div class="col-md-4">
	                        <select ng-model="selectedPerformanceDc" ng-change="loadHubData(selectedPerformanceDc)">
	                            <option selected value="">---- Select DC ----</option>
	                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div class="col-md-4">
	                        <select ng-model="selectedPerformanceHub">
	                            <option value="">---- Select HUB ----</option>
	                            <option value="allHub" selected>All</option>
	                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
	                        </select>
	                    </div>
	                    <div class="col-md-2">
	                        <select ng-model="selectedPerformanceDate" ng-change="loadSelectedDates(selectedPerformanceDate)">
	                            <option value="" selected="selected">Period</option>
	                            <option value="today">Today</option>
	                            <option value="yesterday">Yesterday</option>
	                            <option value="wtd">WTD</option>
	                            <option value="mtd">MTD</option>
	                            <option value="ytd">YTD</option>
	                            <option value="customDate">Custom Date</option>
	                        </select>
	                    </div>
	                    <div ng-show="selectedPerformanceDate == 'customDate'">
	                    	<div class="col-md-4" style="margin-top: 10px;">
								<div class="form-group">
									<div class='input-group date' id='datetimepickerPerfFromDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" id="reqFromPerformanceDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-top: 10px;">
								<div class="form-group">
									<div class='input-group date' id='PerfToDate' >
										<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" id="reqToPerformanceDate"/ >
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
	                    </div>
	                    <div class="col-md-2" ng-class="{dynamicBut: selectedPerformanceDate == 'customDate'}">
	                        <button ng-click="loadPerformanceLogisticData(selectedPerformanceDc,selectedPerformanceHub,selectedPerformanceDate)">submit</button>
	                    </div>
                   	</div>
                </div> 
                <div class="tabbable tabs-below">
                    <div class="tab-content">
                        <div class="tab-pane active" id="chartperf" style="height: 325px !important;">
                            <div class="perfTable">
                            	 <table class="search-table" ng-if="togglePerformTable == null">
                                	 <thead>
                                		<tr>
                                			<th>Key Performance Indicators</th>
                                			<th ng-repeat="x in performHubList"><span style="float: right;"><%x%></span></th>
                                		</tr>
                                	</thead>
                                	<tbody> 
                                		<tr ng-repeat="(key, data) in perfLogisticsData[0]">
                                			<td><%key%></td>
                                			<td ng-repeat="listData in perfLogisticsData">
    	                        				<span style="float: right;" ><%listData[key]%></span>
		                                	</td>
                                		</tr>

                                	</tbody>
                                </table>
                                <table class="search-table" ng-if="togglePerformTable != null">
                                	 <thead>
                                		<tr>
                                			<th>Hub</th>
                                			<th>Key Performance Indicators</th>
                                			<th ng-repeat="x in dateList"><span style="float: right;"><%x%></span></th>
                                		</tr>
                                	</thead>
                                	<tbody> 
                                		<tr ng-repeat="(key, data) in perfLogisticsData[0]">
                                			<td><%perfHubName%></td>
                                			<td><%key%></td>
                                			<td ng-repeat="listData in perfLogisticsData">
    	                        				<span style="float: right;" ><%listData[key]%></span>
		                                	</td>
                                		</tr>

                                	</tbody>
                                </table>
                            </div>        
                        </div>
                    </div>
                    <div class="barChatCLass" style="border-top: 1px solid #eee;position: relative;">
                        <ul class="nav" style="display: flex;float: right;">
                            <li class="custIcon active"><a href="#chartperf" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            
        </div>
        <div class="col-md-6">
            
        </div>
    </div> -->
</div>

@stop
@section('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<style type="text/css">
#working_capital>tbody>tr:last-child {
	font-weight: bold; 
}
[class*="creditgroup"]{
  display: none;
}
.orderCount { width: 40%; float: left; }
.orderValue { width: 60%; float: right; }
.filterInput{
	height: 20px !important;
    border-radius: 3px !important;
    margin-bottom: 5px;
    padding: 0px !important;
}
.ui-grid-pager-control button {
    height: 10px !important;
}
.ui-grid-pager-control input {
    height: 12px !important;
    width: 20px !important;
}
.ui-grid-pager-control .ui-grid-pager-max-pages-number {
    vertical-align: inherit !important;
}
.ui-grid-pager-row-count-picker select {
    height: 12px !important;
    width: 23% !important;
}
.right_align{
	text-align: right;
}
.pickSumTable{
	/*overflow: scroll;*/
	height: 400px;
	/*padding:10px;*/
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
.ui-grid-cell-contents-auto {
     /* Old Solution */
    /*min-width: max-content !important;*/
    
    /* New Solution */
    display: grid;
    grid-auto-columns: max-content;
    
    /* IE Solution */
    display: -ms-grid;
    -ms-grid-columns: max-content;
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

 .right_align{
 	text-align: right;
    padding-right: 15px;
 }
 .center_align{
    text-align: left;
  	padding-left: 0px;
  	width: 110px !important;
 }
 .left_align{
    text-align: left;
  	padding-left: 0px;
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

#returnsDamMissGrid{
     table-layout: auto !important;
}
#returnsDamMissGrid_headers{
     table-layout: auto !important;
}
.header_center_align{
  text-align: left;
  /*position: absolute;*/
  width: 120px !important;
}
.header_right_align{
	text-align: right !important;
    padding-right: 5px;
}
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<!-- <script src="http://igniteui.com/js/modernizr.min.js"></script>
<script src="http://cdn-na.infragistics.com/igniteui/latest/js/infragistics.core.js"></script>
<script src="http://cdn-na.infragistics.com/igniteui/latest/js/infragistics.lob.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="{{ URL::asset('assets/global/scripts/angular-fusioncharts.min.js') }}"></script>
<script src="https://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>

<script  type="text/javascript">
    var app = angular.module('logisticApp', ["ng-fusioncharts",'ui.grid','ui.grid.pagination', 'ui.grid.exporter','ui.grid.pinning', 'ui.grid.resizeColumns', 'ui.grid.moveColumns'],function($interpolateProvider){
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });
    
    app.controller("logisticController",function($scope,$http,$rootScope,$filter,$window,$location,uiGridConstants) { 
    	var baseUrl = '<?php echo env('EBUTOR_NODE_URL'); ?>';
        $scope.dcList = JSON.parse('<?php echo json_encode($data); ?>');
        angular.forEach($scope.dcList.DC, function(values, keys){
            console.log(keys);
            $scope.DefaultDcList = keys;
            return;
        });
        $scope.hubList = [];
        $scope.PickerData = $scope.dcList.Pickers;

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

        $scope.toggle = function(){
			$scope.toggleVariable = !$scope.toggleVariable;
		}

		$scope.toggleWorkSummary = function(){
			$scope.toggleWorkVariable = !$scope.toggleWorkVariable;
		}

		$scope.toggleReturnSum = function(){
			$scope.toggleVariableReturnSum = !$scope.toggleVariableReturnSum;
		}
		$scope.toggleHoldSum = function(){
			$scope.toggleVariableHoldSum = !$scope.toggleVariableHoldSum;
		}
		$scope.toggleCrateSum = function(){
			$scope.toggleVariableCrateSum = !$scope.toggleVariableCrateSum;
		}

		$scope.returnDME = function(){
			$scope.returnDMEVariable = !$scope.returnDMEVariable;
		}

		$scope.toggleInventorySum = function(){
			$scope.inventoryVariable = !$scope.inventoryVariable;
		}

		$scope.toggleDcSummary = function(){
			$scope.toggleDcSummaryVariable = !$scope.toggleDcSummaryVariable;
		}

		$scope.toggleHubSummary = function(){
			$scope.toggleHubSummaryVariable = !$scope.toggleHubSummaryVariable;
		}

		$scope.CheckingSummary = function(){
			$scope.CheckingSummaryVariable = !$scope.CheckingSummaryVariable;
		}

		$scope.toggleCycleSummary = function(){
			$scope.toggleCycleSummaryVariable = !$scope.toggleCycleSummaryVariable;
		}

		$scope.toggleDeliverySummary = function(){
			$scope.toggleDeliverySummaryVar = !$scope.toggleDeliverySummaryVar;
		}

		$scope.togglePerformanceSummary = function(){
			$scope.togglePerformSummaryVar = !$scope.togglePerformSummaryVar;
		}
		
		// ui-grid section

		// Cycle Count Summary Grid
        $scope.gridCycleCountOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
		    //enableColumnMenus: false,
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Return Summary Grid
        $scope.gridReturnOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
		    //enableColumnMenus: false,
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Crate Summary Grid
        $scope.gridCrateCountOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
		    //enableColumnMenus: false,
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Inventory Summary Grid
        $scope.gridInventoryOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
		    //enableColumnMenus: false,
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Delivery Summary Grid
        $scope.griddeliveryOptions = {
			enableFiltering: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
		    //enableColumnMenus: false,
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Damage and missing Summary Grid
		$scope.damagegridOptions = {
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
			
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		// Hold Summary Grid
		$scope.holdgridOptions = {
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
			
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

		$scope.ngGridFIx = function() {
			window.dispatchEvent(new Event('resize'));
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

        // Picking summary
        $scope.loadPickerData = function(dcValue, hubValue,selectedPickers, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
        	if(hubValue == 'allHub'){
        		hubValue = 'null';
        	}
        	var selectedPicker ="";
        	selectedPicker = selectedPickers;
        	if(selectedPickers == "allPickers" || selectedPickers == undefined){
        		selectedPicker = 'null';
        	}
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#reqFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#reqToDate')).val();
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
        		$scope.selectedHub = 'allHub';
        		$scope.selectedDc = dcValue; 
        		$scope.selectedPickers = selectedPickers;
        		$scope.selectedDate = 'today';
        		$scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectedHub);
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
        		'user_id':selectedPicker,
        		'from_date': serviceFromDate,
        		'to_date': serviceToDate,
        		'report_type': 'getKPIPickingSummaryReport',
        		'period_type': $scope.selectedPeriodType
        	};
        	$scope.tempData = [];
        	$scope.stackChartDataset = [];
        	$scope.stackCategoryList = [];
        	var chckData = [];
        	var tempChckData = [];
        	$scope.chckDataTemp = [];
        	$scope.keysData = [];
        	$scope.gndTotalData = [];
        	$scope.contsPie = [];
        	$scope.tempseriesname = [];
        	$scope.pickGridDefs = [];
        	var pickGridDefstemp = [];
			var pickGridDefsData = [];
			var pickGridDefsDataTest = [];
			var columnKeys = [];
			$scope.pickGridData = [];
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadPickerData").show();
        	req.success(function(successCallback){
        		var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
				pickGridDefsData.push(tempGridHub);
				var tempFile = {
					name: "Period", type: "date", width: '100px', formatter: function (value, record) {
						return value.toLocaleDateString();
					}
				};
				pickGridDefsDataTest.push(tempFile);
				var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
				columnKeys.push(colTemop);
				$("#loadPickerData").hide();

        		if(successCallback.status == 'Success'){
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    							chckData[succValues[i].hub] = new Array();
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										tempChckData[keyss] = new Array();
    								});
    							}
    						}
    					}
        			});
        			
        			var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
					pickGridDefsData.push(tempGridHub);
					var tempFile = { name: "Hub", type: "string", width: '100px' };
					pickGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
					columnKeys.push(colTemop);
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
									    $scope.tempData.push(contsPie);
        						});
        					});
        				}else{
        					
        					var categoryTemp = {
        						"label" : succKeys
        					};
        						
        					$scope.stackCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						if(seriesValues.hub != 'Grand Total'){
        							var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
        							temp = temp.replace(/[\[\]']+/g, '<br/>');
        							temp = temp.replace(/,/g, '<br/>');
        							temp = temp.replace(/tool_tip: null/g, '');
        							temp = temp.replace(/count_value/g, 'Value');
        							temp = temp.replace(/count/g, 'Count');
	            					chckData[seriesValues.hub].push({
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
									$scope.pickGridData.push(comGridData);
        						}else{
        							angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
		            							tempChckData[finalLinekeys].push(
		            								{'value':finalLineValues.count, 'displayValue':finalLineValues.count_value}
		            							);
		            							var gridTemp = {
		            								field: finalLinekeys
		            							};
		            							$scope.pickGridDefs.push(gridTemp);
										});     								
        							});
        						}
        					});
        				}
        			});
        		}
								

        		var newArr = $scope.pickGridDefs.filter(el => {
				    if (pickGridDefstemp.indexOf(el.field) === -1) {
				        // If not present in array, then add it
				        var headerKey = el.field.replace(/\s/g,'');
				        pickGridDefstemp.push(el.field);
				        pickGridDefsData.push({ headerText:el.field, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
				        pickGridDefsDataTest.push({ name:el.field, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
				        var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
						columnKeys.push(colTemop);
				        return true;
				    } else {
				        // Already present in array, don't add it
				        return false;
				    }
				});

				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: pickGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.pickGridData,
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
				            	columnSettings:  columnKeys, 
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
							columnSettings: columnKeys
						});
					}
				if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
						$("#filterByText").igTextEditor("destroy");
						$("#filterByText").remove();
						$("#searchLabel").remove();
				}
				if ($("#gridSimpleFiltering").data("igGrid")) {
					$("#gridSimpleFiltering").igGrid("destroy");
				}
				$("#gridSimpleFiltering").igGrid({
					autoGenerateColumns: false,
					height: "400px",
					width: "100%",
					columns: pickGridDefsData,
					dataSource: $scope.pickGridData,
					features: features
				});
			}

				// End
			


        		for(let k in chckData){
        			var data = {
        				"seriesname": k,
                		"data": chckData[k]
        			}
        			if(data.seriesname != "undefined"){
        				$scope.stackChartDataset.push(data);
        			}
        		}
        		for(let k in tempChckData){
        			var data = {
        				"seriesname": k,
        				"renderAs": "line",
        				"showValues": "0",
                		"data": tempChckData[k]
        			}
    				$scope.stackChartDataset.push(data);
        		}

        		// Picking Summary Pie Chart
        		var propertiesObject = {
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
						    enablemultislicing: "1",
						    slicingdistance: "15",
						    showpercentvalues: "1",
						    showpercentintooltip: "0",
						    borderThickness:3,
						    exportEnabled: "1",
							caption: 'Picking Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.tempData
					}
				}
				var sampleChart = new FusionCharts(propertiesObject);
				sampleChart.render();

				// multi series column
				var stackPropertiesObject = {
					type: 'stackedColumn3DLine',
			        renderAt: 'pickingStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
			                //"numberPrefix": "₹",
			                showvalues: "0",
			                rotateValues: "1",
                			placeValuesInside: "1",
                			caption: 'Picking Summary',
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
			                    "category": $scope.stackCategoryList
			                }
			            ],
			            "dataset": $scope.stackChartDataset
					}
				}
				var pickingStackedChart = new FusionCharts(stackPropertiesObject);
				pickingStackedChart.render();
            });
            req.error(function(errorCallback){
        	$("#loadPickerData").show();
            });
        }

        // Checking summary
        $scope.loadCheckersData = function(dcValue, hubValue,selectedPickers, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
        	if(hubValue == 'allHub'){
        		hubValue = 'null';
        	}
        	var selectedPicker ="";
        	selectedPicker = selectedPickers;
        	if(selectedPickers == "allPickers" || selectedPickers == undefined){
        		selectedPicker = 'null';
        	}
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#checkingFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#checkingToDate')).val();
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
        		$scope.selectCheckingHub = 'allHub';
        		$scope.selectCheckingDc = dcValue; 
        		$scope.selectCheckingPickers = selectedPickers;
        		$scope.selectCheckingDate = 'today';
        		$scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectCheckingHub);
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
        		'user_id':selectedPicker,
        		'from_date': serviceFromDate,
        		'to_date': serviceToDate,
        		'report_type': 'getKPICheckingSummaryReport',
        		'period_type': $scope.selectedPeriodType
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
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadCheckersData").show();
        	req.success(function(successCallback){
				var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
				checkingGridDefsData.push(tempGridHub);
				var tempFile = {
					name: "Period", type: "date", width: '100px', formatter: function (value, record) {
						return value.toLocaleDateString();
					}
				};
				checkingGridDefsDataTest.push(tempFile);
				var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
				columncheckingKeys.push(colTemop);

				$("#loadCheckersData").hide();

        		if(successCallback.status == 'Success'){
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    							chckDataChecking[succValues[i].hub] = new Array();
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										tempChckDataChecking[keyss] = new Array();
    								});
    							}
    						}
    					}
        			});
        			
					var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
					checkingGridDefsData.push(tempGridHub);
					var tempFile = { name: "Hub", type: "string", width: '100px' };
					checkingGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
					columncheckingKeys.push(colTemop);
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
									    $scope.checkingPieData.push(contsPie);
        						});
        					});
        				}else{
        					
        					var categoryTemp = {
        						"label" : succKeys
        					};
        						
        					$scope.stackCheckingCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						if(seriesValues.hub != 'Grand Total'){
        							var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
        							temp = temp.replace(/[\[\]']+/g, '<br/>');
        							temp = temp.replace(/,/g, '<br/>');
        							temp = temp.replace(/tool_tip: null/g, '');
        							temp = temp.replace(/count_value/g, 'Value');
        							temp = temp.replace(/count/g, 'Count');
	            					chckDataChecking[seriesValues.hub].push({
										'value':seriesValues.order_count,
										'displayValue': seriesValues.order_value,
										 tooltext: seriesValues.hub + "{br}{br}Total Count:" + seriesValues.order_count +
										 			"{br}Total Value:" + seriesValues.order_value + "{br}"  + temp
									});

	            					// hub data for grid
									var GridCheckHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
									var gridCheckingData = {};
									var tempKeys;
									angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
											var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
											tempKeys = spaceRemoveFromKey;
											//tempKeys = finalLinekeys;
											gridCheckingData[tempKeys] = finalLineValues.count;
										});
									});
									var comGridData = $.extend( GridCheckHubData, gridCheckingData );
									$scope.checkingGridData.push(comGridData);
        						}else{
        							angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
		            							tempChckDataChecking[finalLinekeys].push(
		            								{'value':finalLineValues.count, 'displayValue':finalLineValues.count_value}
		            							);
		            							var gridTemp = {
		            								field: finalLinekeys
		            							};
		            							$scope.checkingGridDefs.push(gridTemp);
										});     								
        							});
        						}
        					});
        				}
        			});
        		}
								

        		var newArr = $scope.checkingGridDefs.filter(el => {
				    if (checkingGridDefstemp.indexOf(el.field) === -1) {
				        // If not present in array, then add it
				        checkingGridDefstemp.push(el.field);
				        var headerKey = el.field.replace(/\s/g,'');
				        checkingGridDefsData.push({ headerText:el.field, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
						checkingGridDefsDataTest.push({ name:el.field, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
						var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
						columncheckingKeys.push(colTemop);
				        return true;
				    } else {
				        // Already present in array, don't add it
				        return false;
				    }
				});

				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: checkingGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.checkingGridData,
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
				            	columnSettings:  columncheckingKeys, 
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
							columnSettings: columncheckingKeys
						});
					}
					if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
							$("#filterByText").igTextEditor("destroy");
							$("#filterByText").remove();
							$("#searchLabel").remove();
					}
					if ($("#gridCheckingFiltering").data("igGrid")) {
						$("#gridCheckingFiltering").igGrid("destroy");
					}
					$("#gridCheckingFiltering").igGrid({
						autoGenerateColumns: false,
						height: "400px",
						width: "100%",
						columns: checkingGridDefsData,
						dataSource: $scope.checkingGridData,
						features: features
					});
				}
				// End
        		for(let k in chckDataChecking){
        			var data = {
        				"seriesname": k,
                		"data": chckDataChecking[k]
        			}
        			if(data.seriesname != "undefined"){
        				$scope.stackCheckingDataset.push(data);
        			}
        		}
        		for(let k in tempChckDataChecking){
        			var data = {
        				"seriesname": k,
        				"renderAs": "line",
        				"showValues": "0",
                		"data": tempChckDataChecking[k]
        			}
    				$scope.stackCheckingDataset.push(data);
        		}

        		// Checking Summary Pie Chart
        		var CheckingProperties = {
					type : "pie3d",
					id : "checkingPie-chart",
					width : "100%",
					height: "400",
					renderAt: "checking-container",
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
						    caption: 'Checking Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
							//caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.checkingPieData
					}
				}
				var checingSumChart = new FusionCharts(CheckingProperties);
				checingSumChart.render();

				// multi series column
				var stackCheckingProperties = {
					type: 'stackedColumn3DLine',
			        renderAt: 'checkingStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
			                //"numberPrefix": "₹",
			                showvalues: "0",
			                rotateValues: "1",
                			placeValuesInside: "1",
                			caption: 'Checking Summary',
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

        // Cycle Count Summary
        $scope.loadCycleSumData = function(dcValue, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#CycleSumFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#CycleSumToDate')).val();
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
        		$scope.selectCycleSumDc = dcValue;
        		$scope.selectDateCycleSum = 'today';
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
        		'user_id':'null',
        		'hub_id':'null',
        		'from_date': serviceFromDate,
        		'to_date': serviceToDate,
        		'report_type': 'getKPILastCycleCount',
        		'period_type': $scope.selectedPeriodType
        	};
        	$scope.cycleSumPie = [];
        	$scope.cycleCategoryList = [];
        	$scope.cycleDataForm = [];
        	$scope.cycleDatasetData = [];
        	var cycleGridDefsData = [];
        	var tempGridDefs = [];
        	var gridCycleData = [];
        	var cycleGridDefsDataTest = [];
			var columnCycleKeys = [];
        	$scope.cycleCountGridData = [];
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadCycleSumData").show()
        	req.success(function(successCallback){
        		$("#loadCycleSumData").hide();
        		if(successCallback.status == 'Success'){
        			/*var tempGridHub = { field: 'Period' ,type : 'date',
                                            cellFilter : 'date:"dd-MMM-yy"',
                                            filterCellFiltered : 'true',
                                            };
					cycleGridDefsData.push(tempGridHub);*/
					var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
					cycleGridDefsData.push(tempGridHub);
					var tempFile = {
						name: "Period", type: "date", width: '100px', formatter: function (value, record) {
							return value.toLocaleDateString();
						}
					};
					cycleGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
					columnCycleKeys.push(colTemop);
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										$scope.cycleDataForm[keyss] = new Array();
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
							            displayValue: inSecSuccValue.count_value,
								        tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count + '{br} Value: '+ inSecSuccValue.count_value
								    };
								    $scope.cycleSumPie.push(contsPie);
        						});
        					});
        				}else{
        					var categoryTemp = {
        						"label" : succKeys
        					};
        					$scope.cycleCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(cycleValues, cycleKeys){
        						if(cycleValues.hub != 'Grand Total'){
        							// Dynamic Drid Data
									// hub data for grid
									var GridCycleDataData = { 'Period' : succKeys };
									var gridPickData = {};
									var tempKeys;
        							angular.forEach(cycleValues.values, function(innerCycleVal, innerCycleKey){
        								angular.forEach(innerCycleVal, function(finalCycleVal, finalCycleKey){
        									$scope.cycleDataForm[finalCycleKey].push({
        										'value':finalCycleVal.count,
        										'displayValue':finalCycleVal.count_value
        									});

        									// Dynamic Grid Defs
        									if(tempGridDefs.indexOf(finalCycleKey) == -1){
        										tempGridDefs.push(finalCycleKey);
												//cycleGridDefsData
												var headerKey = finalCycleKey.replace(/\s/g,'');
												cycleGridDefsData.push({ headerText:finalCycleKey, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												cycleGridDefsDataTest.push({ name:finalCycleKey, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
												columnCycleKeys.push(colTemop);
												/*cycleGridDefsData.push({'field':finalCycleKey, cellClass: 'right_align', 
											        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
											  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
											  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
											        filter: { 
														term: '',
														//type: uiGridConstants.filter.SELECT, 
														options: [ 
															{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
															{id: uiGridConstants.filter.LESS_THAN, value: '<'},
															{id: uiGridConstants.filter.EXACT, value: '='},
														]     
											        }});*/
        									}

        									// Dynamic Grid Data
        									var spaceRemoveFromKey = finalCycleKey.replace(/\s/g,'');
											tempKeys = spaceRemoveFromKey;
        									//tempKeys = finalCycleKey;
											gridCycleData[tempKeys] = finalCycleVal.count;

        									
        								});
        							});
        							var comGridData = $.extend( GridCycleDataData, gridCycleData );
									$scope.cycleCountGridData.push(comGridData);
        						}
        					});
        				}
        			});
        		}
        		$scope.gridCycleCountOptions.columnDefs = cycleGridDefsData;
				$scope.gridCycleCountOptions.data = $scope.cycleCountGridData;

				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: cycleGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.cycleCountGridData,
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
				            	columnSettings:  columnCycleKeys, 
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
							columnSettings: columnCycleKeys
						});
					}
					if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
							$("#filterByText").igTextEditor("destroy");
							$("#filterByText").remove();
							$("#searchLabel").remove();
					}
					if ($("#gridCycleFiltering").data("igGrid")) {
						$("#gridCycleFiltering").igGrid("destroy");
					}
					$("#gridCycleFiltering").igGrid({
						autoGenerateColumns: false,
						height: "400px",
						width: "100%",
						columns: cycleGridDefsData,
						dataSource: $scope.cycleCountGridData,
						features: features
					});
				}
				// End

        		for(let k in $scope.cycleDataForm){
        			var data = {
        				"seriesname": k,
                		"data": $scope.cycleDataForm[k]
        			}
        			$scope.cycleDatasetData.push(data);
        		}

        		// Cycle Count Summary Pie Chart
        		var propertiesCycleCount = {
					type : "pie3d",
					id : "cycleCount-chart",
					width : "100%",
					height: "400",
					renderAt: "chart-cycleCount",
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
						    caption: 'Cycle Count Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
						    exportEnabled: "1",
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.cycleSumPie
					}
				}
				var sampleCycleChart = new FusionCharts(propertiesCycleCount);
				sampleCycleChart.render();

				// multi series column
				var stackCycleCountObject = {
					type: 'stackedColumn3DLine',
			        renderAt: 'cycleStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
                			showvalues: "0",
                			caption: 'Cycle Count Summary',
                			yaxisname: 'Count',
                			xaxisname: 'Date',
                			toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
			                exportEnabled: "1",
			        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
			                plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
			            },
			            "categories": [
			                {
			                    "category": $scope.cycleCategoryList
			                }
			            ],
			            "dataset": $scope.cycleDatasetData
					}
				}
				var cycleStackedChart = new FusionCharts(stackCycleCountObject);
				cycleStackedChart.render();
        	});
        	req.error(function(errorCallback){
        		$("#loadCycleSumData").hide();
        	});
        }

        // Crate Summary
        $scope.loadCrateSumData = function(dcValue, hubValue){
        	$scope.selectCrateSumDc = dcValue;
        	$scope.selectCrateSumHub = 'allHub';
        	if(hubValue == 'allHub'){
        		hubValue = '';
        	}
        	serviceData = {
        		'dc_id': dcValue,
        		'hub_id':hubValue
        	};
        	$scope.cratePieData = [];
        	$scope.crateCategoryList = [];
        	$scope.crateDataForm = [];
        	$scope.crateDatasetData = [];
        	var crateGridDefsData = [];
        	var crateGridDefsDataTest = [];
			var columnCrateKeys = [];
        	var tempCrateGridDefs = [];
        	$scope.crateCountGridData = [];
        	var req = $http.post('/cratesummaryreport',{'data':serviceData});
        	$("#loadCrateSumData").show();
        	req.success(function(successCallback){
        		$("#loadCrateSumData").hide();
        		if(successCallback.status == "Success"){
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				if(succValues.hub != 'Grant Total'){
        					var temmp = succValues.values;
        					for (var i = 0; i <= temmp.length-1; i++) {
        						var objKey = Object.keys(temmp[i]);
        						$scope.crateDataForm[objKey[0]] = new Array();
        					}
        				}
        			});

        			/*var tempGridHub = { field: 'Hub', width: 150,
				        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
				  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
				  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
				        filter: { 
							options: [ 
								{id: uiGridConstants.filter.STARTS_WITH, value: 'STARTS_WITH'}, 
								{id: uiGridConstants.filter.ENDS_WITH, value: 'ENDS_WITH'},
								{id: uiGridConstants.filter.CONTAINS, value: 'CONTAINS'},
							]     
				        }
				    };
					crateGridDefsData.push(tempGridHub);*/
					var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
					crateGridDefsData.push(tempGridHub);
					var tempFile = { name: "Hub", type: "string", width: '100px' };
					crateGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
					columnCrateKeys.push(colTemop);

        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				if(succValues.hub == 'Grant Total'){
        					angular.forEach(succValues.values, function(innerSuccValue, innerSuccKeys){
        						angular.forEach(innerSuccValue, function(inSecSuccValue, inSecSuccKeys){
        							var contsPie = {
							            label: inSecSuccKeys,
							            value: inSecSuccValue.count,
								        tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count 
								    };
								    $scope.cratePieData.push(contsPie);
        						});
        					});
        				}else{
        					var categoryTemp = {
        						"label" : succValues.hub
        					};
        					$scope.crateCategoryList.push(categoryTemp);
        					// hub data for grid
							//var GridCrateDataData = { 'Period' : succKeys };
							var GridCrateHubData = { 'Hub' : succValues.hub };
							var gridCrateData = {};
							var tempKeys;
        					angular.forEach(succValues.values, function(crateValues, crateKeys){
        						angular.forEach(crateValues, function(finalCrateVal, finalCrateKey){
									$scope.crateDataForm[finalCrateKey].push({
										'value':finalCrateVal.count
									});

									// Dynamic Grid Defs
									if(tempCrateGridDefs.indexOf(finalCrateKey) == -1){
										tempCrateGridDefs.push(finalCrateKey);
										//cycleGridDefsData
										var headerKey = finalCrateKey.replace(/\s/g,'');
										crateGridDefsData.push({ headerText:finalCrateKey, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
										crateGridDefsDataTest.push({ name:finalCrateKey, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
										var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
										columnCrateKeys.push(colTemop);
										/*crateGridDefsData.push({'field':finalCrateKey, cellClass: 'right_align', 
									        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
									  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
									  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
									        filter: { 
												//term: '',
												//type: uiGridConstants.filter.SELECT, 
												options: [ 
													{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
													{id: uiGridConstants.filter.LESS_THAN, value: '<'},
													{id: uiGridConstants.filter.EXACT, value: '='},
												]     
									        }});*/
									}

									// Dynamic Grid Data
									//tempKeys = finalCrateKey;
									var spaceRemoveFromKey = finalCrateKey.replace(/\s/g,'');
									tempKeys = spaceRemoveFromKey;
									gridCrateData[tempKeys] = finalCrateVal.count;
        						});
        					});
        					var comGridData = $.extend( GridCrateHubData, gridCrateData );
							$scope.crateCountGridData.push(comGridData);
        				}
        			});
        		}
        		$scope.gridCrateCountOptions.columnDefs = crateGridDefsData;
				$scope.gridCrateCountOptions.data = $scope.crateCountGridData;

				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: crateGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.crateCountGridData,
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
				            	columnSettings:  columnCrateKeys, 
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
							columnSettings: columnCrateKeys
						});
					}
					if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
							$("#filterByText").igTextEditor("destroy");
							$("#filterByText").remove();
							$("#searchLabel").remove();
					}
					if ($("#gridCrateFiltering").data("igGrid")) {
						$("#gridCrateFiltering").igGrid("destroy");
					}
					$("#gridCrateFiltering").igGrid({
						autoGenerateColumns: false,
						height: "400px",
						width: "100%",
						columns: crateGridDefsData,
						dataSource: $scope.crateCountGridData,
						features: features
					});
				}
				// End

        		for(let k in $scope.crateDataForm){
        			var data = {
        				"seriesname": k,
                		"data": $scope.crateDataForm[k]
        			}
        			$scope.crateDatasetData.push(data);
        		}

        		// Crate Summary Pie Chart
        		var crateSumObject = {
					type : "pie3d",
					id : "crateSum-chart",
					width : "100%",
					height: "400",
					renderAt: "crateSum-container",
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
						    caption: 'Crate Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
							//caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue"
						},
						data:$scope.cratePieData
					}
				}
				var crateSumChart = new FusionCharts(crateSumObject);
				crateSumChart.render();

				// multi series column
				var stackCrateObject = {
					type: 'stackedColumn3DLine',
			        renderAt: 'crateStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
                			showvalues: "0",
                			caption: 'Crate Summary',
                			yaxisname: 'Count',
                			xaxisname: 'Date',
                			toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
			                exportEnabled: "1",
			        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
			                plottooltext: "$seriesname <br/>Count: $datavalue"
			            },
			            "categories": [
			                {
			                    "category": $scope.crateCategoryList
			                }
			            ],
			            "dataset": $scope.crateDatasetData
					}
				}
				var crateStackedChart = new FusionCharts(stackCrateObject);
				crateStackedChart.render();
        	});
        	req.error(function(errorCallback){
        		$("#loadCrateSumData").hide();
        	});

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
        			/*var tempGridHub = { field: 'Period' , 'width':80 ,type : 'date',
                                            cellFilter : 'date:"dd-MMM-yy"',
                                            filterCellFiltered : 'true',
                                            };
					returnedGridDefsData.push(tempGridHub);*/
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

        			/*var tempGridHub = { field: 'Hub', width: 150,
				        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
				  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
				  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
				        filter: { 
							options: [ 
								{id: uiGridConstants.filter.STARTS_WITH, value: 'STARTS_WITH'}, 
								{id: uiGridConstants.filter.ENDS_WITH, value: 'ENDS_WITH'},
								{id: uiGridConstants.filter.CONTAINS, value: 'CONTAINS'},
							]     
				        }
				    };
					returnedGridDefsData.push(tempGridHub);*/

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

												/*returnedGridDefsData.push({'field':finalLinekeys, 'width':130, cellClass: 'right_align', 
											        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
											  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
											  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
											        filter: { 
														//term: '',
														//type: uiGridConstants.filter.SELECT, 
														options: [ 
															{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
															{id: uiGridConstants.filter.LESS_THAN, value: '<'},
															{id: uiGridConstants.filter.EXACT, value: '='},
														]     
											        }});*/
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

        			$scope.gridReturnOptions.columnDefs = returnedGridDefsData;
					$scope.gridReturnOptions.data = $scope.returnedGridData;

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
		
		// Hold Summary
        $scope.loadHoldsumData = function(dcValue, hubValue,selectedReturnDO,fromDate){
        	var serviceFromDate;
        	var serviceToDate;
        	if(hubValue == 'allHub'){
        		hubValue = 'null';
        	}
        	if(selectedReturnDO==undefined || selectedReturnDO == "allDos"){
        		selectedReturnDO = 'null';
        	}
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#holdsumFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#holdsumToDate')).val();
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
        		$scope.selectHoldDc = dcValue;
        		$scope.selectHoldHub = 'allHub';
        		$scope.selectedHoldDO = 'allDos';
        		$scope.selectHoldDate = 'today';
        		$scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectHoldHub);
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
        		'report_type': 'getKPIHoldReport',
        		'period_type': $scope.selectedPeriodType
        	};
        	var holdSumCheckData = [];
        	var holdTempChck = [];
        	$scope.holdPieData = [];
        	$scope.holdCategoryList = [];
        	$scope.holdChartDataset = [];
        	var holdGridDefsData = [];
        	var tempHoldGridDefs = [];
			var holdGridDefsDataTest = [];
			var columnHoldSumKeys = [];
        	$scope.HoldGridData = [];
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadHoldsumData").show();
        	req.success(function(successCallback){
        	$("#loadHoldsumData").hide();

        		if(successCallback.status == 'Success'){
        			/*var tempGridHub = { field: 'Period' , 'width':80 ,type : 'date',
                                            cellFilter : 'date:"dd-MMM-yy"',
                                            filterCellFiltered : 'true',
                                            };
					holdGridDefsData.push(tempGridHub);*/
					var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
					holdGridDefsData.push(tempGridHub);
					var tempFile = {
						name: "Period", type: "date", width: '100px', formatter: function (value, record) {
							return value.toLocaleDateString();
						}
					};
					holdGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
					columnHoldSumKeys.push(colTemop);
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    							holdSumCheckData[succValues[i].hub] = new Array();
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										holdTempChck[keyss] = new Array();
    								});
    							}
    						}
    					}
        			});

        			/*var tempGridHub = { field: 'Hub', width: 150,
				        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
				  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
				  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
				        filter: { 
							options: [ 
								{id: uiGridConstants.filter.STARTS_WITH, value: 'STARTS_WITH'}, 
								{id: uiGridConstants.filter.ENDS_WITH, value: 'ENDS_WITH'},
								{id: uiGridConstants.filter.CONTAINS, value: 'CONTAINS'},
							]     
				        }
				    };
					holdGridDefsData.push(tempGridHub);*/

					var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
					holdGridDefsData.push(tempGridHub);
					var tempFile = { name: "Hub", type: "string", width: '100px' };
					holdGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
					columnHoldSumKeys.push(colTemop);

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
								    $scope.holdPieData.push(contsPie);
        						});
        					});
        				}else{
        					var categoryTemp = {
        						"label" : succKeys
        					};
        					$scope.holdCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						if(seriesValues.hub != 'Grand Total'){
		            				holdSumCheckData[seriesValues.hub].push({
		            													'value':seriesValues.order_count,
		            													'displayValue': seriesValues.order_value,'seperateData': seriesValues.values
		            												});

		            				// hub data for grid
									var GridHoldHubData = { 'Period' : succKeys, 'Hub' : seriesValues.hub };
									var gridHoldData = {};
									var tempKeys;
									angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
											var spaceRemoveFromKey = finalLinekeys.replace(/\s/g,'');
											tempKeys = spaceRemoveFromKey;
											//tempKeys = finalLinekeys;
											gridHoldData[tempKeys] = finalLineValues.count;

											// Dynamic Grid Defs
        									if(tempHoldGridDefs.indexOf(finalLinekeys) == -1){
        										tempHoldGridDefs.push(finalLinekeys);
												//cycleGridDefsData
												var headerKey = finalLinekeys.replace(/\s/g,'');
												holdGridDefsData.push({ headerText:finalLinekeys, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												holdGridDefsDataTest.push({ name:finalLinekeys, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
												columnHoldSumKeys.push(colTemop);


												/*holdGridDefsData.push({'field':finalLinekeys, 'width':130, cellClass: 'right_align', headerClass: 'right_align',
											        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
											  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
											  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
											        filter: { 
														options: [ 
															{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
															{id: uiGridConstants.filter.LESS_THAN, value: '<'},
															{id: uiGridConstants.filter.EXACT, value: '='},
														]     
											        }});*/
        									}
										});
									});
									var comGridData = $.extend( GridHoldHubData, gridHoldData );
									$scope.HoldGridData.push(comGridData);
        						}else{
        							angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
	            							holdTempChck[finalLinekeys].push({
	            								'value':finalLineValues.count, 
	            								'displayValue':finalLineValues.count_value
	            							});
										});     								
        							});
        						}
        					});
        				}
        			});

        			$scope.holdgridOptions.columnDefs = holdGridDefsData;
					$scope.holdgridOptions.data = $scope.HoldGridData;

					// Ignite Grid For Picking
					$(function () {
						var f = true, ds, schema;

						schema = new $.ig.DataSchema("array", {
							fields: holdGridDefsDataTest,
						});
						ds = new $.ig.DataSource({
							dataSource: $scope.HoldGridData,
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
					            	columnSettings:  columnHoldSumKeys, 
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
								columnSettings: columnHoldSumKeys
							});
						}
						if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
								$("#filterByText").igTextEditor("destroy");
								$("#filterByText").remove();
								$("#searchLabel").remove();
						}
						if ($("#gridHoldSumFiltering").data("igGrid")) {
							$("#gridHoldSumFiltering").igGrid("destroy");
						}
						$("#gridHoldSumFiltering").igGrid({
							autoGenerateColumns: false,
							height: "400px",
							width: "100%",
							columns: holdGridDefsData,
							dataSource: $scope.HoldGridData,
							features: features
						});
					}

					// End

        			for(let k in holdSumCheckData){
	        			var data = {
	        				"seriesname": k,
	                		"data": holdSumCheckData[k]
	        			}
	        			if(data.seriesname != "undefined"){
	        				$scope.holdChartDataset.push(data);
	        			}
	        		}
	        		for(let k in holdTempChck){
	        			var data = {
	        				"seriesname": k,
	        				"renderAs": "line",
	        				"showValues": "0",
	                		"data": holdTempChck[k]
	        			}
	    				$scope.holdChartDataset.push(data);
	        		}
	        		// Return Summary Pie Chart
	        		var propertiesHoldChartObject = {
						type : "pie3d",
						id : "HoldChart-chart",
						width : "100%",
						height: "400",
						renderAt: "holdChart-container",
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
							    caption: 'Hold Summary',
								toolTipBorderColor: "#666666",
						        toolTipBgColor: "#666666",
						        toolTipColor: "#ffffff",
								//caption: 'Avg Lines Picked Per Head: ' + $scope.pickingSumAvgHead + '{br}Avg Lines Picked Per Hr: ' + $scope.pickingSumAvgHr,
	    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
	    						exportTargetWindow: "_self",
								plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
							},
							data:$scope.holdPieData
						}
					}
					var holdChartChart = new FusionCharts(propertiesHoldChartObject);
					holdChartChart.render();

					// multi series column
					var stackHoldPropertiesObject = {
						type: 'stackedColumn3DLine',
				        renderAt: 'HoldChartStackChart',
				        width: '100%',
				        height: '400',
				        dataFormat: 'json',
				        dataSource: {
				            "chart": {
				                //"numberPrefix": "₹",
				                showvalues: "0",
				                rotateValues: "1",
				                caption: 'Hold Summary',
	                			yaxisname: 'Count',
	                			xaxisname: 'Date',
	                			toolTipBorderColor: "#666666",
						        toolTipBgColor: "#666666",
						        toolTipColor: "#ffffff",
	                			placeValuesInside: "1",
				                exportEnabled: "1",
				        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
	    						exportTargetWindow: "_self",
				                plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
				            },
				            "categories": [
				                {
				                    "category": $scope.holdCategoryList
				                }
				            ],
				            "dataset": $scope.holdChartDataset
						}
					}
					var holdStackedChart = new FusionCharts(stackHoldPropertiesObject);
					holdStackedChart.render();
        		}

        	});
        	req.error(function(errorCallback){
        		$("#loadHoldsumData").hide();
        	});
        }

        // Inventory Summary
        $scope.loadInventoryData = function(dcValue, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#inventorysumFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#inventorysumToDate')).val();
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
        		var fromDate = new Date();
                fromDate = new Date(fromDate.setDate(fromDate.getDate()-1));
        		serviceFromDate = $filter('date')(fromDate, 'yyyy-MM-dd');
	            serviceToDate = $filter('date')(fromDate, 'yyyy-MM-dd');
        		$scope.selectedPeriodType = 1;
        		$scope.selectInventoryDc = dcValue;
        		$scope.selectInventoryDate = 'yesterday';
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
        		'user_id':'null',
        		'hub_id':'null',
        		'from_date': serviceFromDate,
        		'to_date': serviceToDate,
        		'report_type': 'getKPIInventoryDCReport',
        		'period_type': $scope.selectedPeriodType
        	};
        	$scope.inventorySumPie = [];
        	$scope.inventoryCategoryList = [];
        	$scope.inventoryDataForm = [];
        	$scope.inventoryDatasetData = [];
        	var inventoryGridDefsData = [];
        	var tempInventoryGridDefs = [];
        	var gridInventoryData = [];
        	var inventoryGridDefsDataTest = [];
			var columnInventoryKeys = [];
        	$scope.inventoryGridData = [];
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadInventoryData").show()
        	req.success(function(successCallback){
        		$("#loadInventoryData").hide();
        		if(successCallback.status == 'Success'){
        			/*var tempGridHub = { field: 'Period' ,type : 'date',
                                            cellFilter : 'date:"dd-MMM-yy"',
                                            filterCellFiltered : 'true',
                                            };
					inventoryGridDefsData.push(tempGridHub);*/
					var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
					inventoryGridDefsData.push(tempGridHub);
					var tempFile = {
						name: "Period", type: "date", width: '100px', formatter: function (value, record) {
							return value.toLocaleDateString();
						}
					};
					inventoryGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
					columnInventoryKeys.push(colTemop);
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										$scope.inventoryDataForm[keyss] = new Array();
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
							            displayValue: inSecSuccValue.count_value,
								        tooltext: inSecSuccKeys + '{br} Count: ' + inSecSuccValue.count + '{br} Value: '+ inSecSuccValue.count_value
								    };
								    $scope.inventorySumPie.push(contsPie);
        						});
        					});
        				}else{
        					var categoryTemp = {
        						"label" : succKeys
        					};
        					$scope.inventoryCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(cycleValues, cycleKeys){
        						if(cycleValues.hub != 'Grand Total'){
        							// Dynamic Drid Data
									// hub data for grid
									var GridCycleDataData = { 'Period' : succKeys };
									var gridPickData = {};
									var tempKeys;
        							angular.forEach(cycleValues.values, function(innerCycleVal, innerCycleKey){
        								angular.forEach(innerCycleVal, function(finalCycleVal, finalCycleKey){
        									$scope.inventoryDataForm[finalCycleKey].push({
        										'value':finalCycleVal.count,
        										'displayValue':finalCycleVal.count_value
        									});

        									// Dynamic Grid Defs
        									if(tempInventoryGridDefs.indexOf(finalCycleKey) == -1){
        										tempInventoryGridDefs.push(finalCycleKey);
												//inventoryGridDefsData
												var headerKey = finalCycleKey.replace(/\s/g,'');
												inventoryGridDefsData.push({ headerText:finalCycleKey, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												inventoryGridDefsDataTest.push({ name:finalCycleKey, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
												var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
												columnInventoryKeys.push(colTemop);
												/*inventoryGridDefsData.push({'field':finalCycleKey, cellClass: 'right_align', 
											        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
											  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
											  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
											        filter: { 
														term: '',
														//type: uiGridConstants.filter.SELECT, 
														options: [ 
															{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
															{id: uiGridConstants.filter.LESS_THAN, value: '<'},
															{id: uiGridConstants.filter.EXACT, value: '='},
														]     
											        }});*/
        									}

        									// Dynamic Grid Data
        									var spaceRemoveFromKey = finalCycleKey.replace(/\s/g,'');
											tempKeys = spaceRemoveFromKey;
        									//tempKeys = finalCycleKey;
											gridInventoryData[tempKeys] = finalCycleVal.count;

        									
        								});
        							});
        							var comGridData = $.extend( GridCycleDataData, gridInventoryData );
									$scope.inventoryGridData.push(comGridData);
        						}
        					});
        				}
        			});
        		}
        		$scope.gridInventoryOptions.columnDefs = inventoryGridDefsData;
				$scope.gridInventoryOptions.data = $scope.inventoryGridData;
				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: inventoryGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.inventoryGridData,
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
				            	columnSettings:  columnInventoryKeys, 
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
							columnSettings: columnInventoryKeys
						});
					}
					if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
							$("#filterByText").igTextEditor("destroy");
							$("#filterByText").remove();
							$("#searchLabel").remove();
					}
					if ($("#gridInventoryFiltering").data("igGrid")) {
						$("#gridInventoryFiltering").igGrid("destroy");
					}
					$("#gridInventoryFiltering").igGrid({
						autoGenerateColumns: false,
						height: "400px",
						width: "100%",
						columns: inventoryGridDefsData,
						dataSource: $scope.inventoryGridData,
						features: features
					});
				}

				// End

        		for(let k in $scope.inventoryDataForm){
        			var data = {
        				"seriesname": k,
                		"data": $scope.inventoryDataForm[k]
        			}
        			$scope.inventoryDatasetData.push(data);
        		}

        		// Inventory Summary Pie Chart
        		var propertiesInventory = {
					type : "pie3d",
					id : "inventory-chart",
					width : "100%",
					height: "400",
					renderAt: "chart-inventory",
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
						    caption: 'DC Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
						    exportEnabled: "1",
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.inventorySumPie
					}
				}
				var sampleInventoryChart = new FusionCharts(propertiesInventory);
				sampleInventoryChart.render();

				// multi series column
				var stackInventoryObject = {
					type: 'stackedColumn3DLine',
			        renderAt: 'inventoryStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
                			showvalues: "0",
                			caption: 'DC Summary',
                			yaxisname: 'Count',
                			xaxisname: 'Date',
                			toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
			                exportEnabled: "1",
			        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
			                plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
			            },
			            "categories": [
			                {
			                    "category": $scope.inventoryCategoryList
			                }
			            ],
			            "dataset": $scope.inventoryDatasetData
					}
				}
				var cycleStackedChart = new FusionCharts(stackInventoryObject);
				cycleStackedChart.render();
        	});
        	req.error(function(errorCallback){
        		$("#loadInventoryData").hide();
        	});
        }

        // Delivery summary
        $scope.loadDeliveryData = function(dcValue, hubValue,selectedDeliveryDO, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
        	if(hubValue == 'allHub'){
        		hubValue = 'null';
        	}
        	if(selectedDeliveryDO==undefined || selectedDeliveryDO == "allDos"){
        		selectedDeliveryDO = 'null';
        	}
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#deliverysumFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#deliverysumToDate')).val();
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
        		$scope.selectDeliveryHub = 'allHub';
        		$scope.selectDeliveryDc = dcValue; 
        		$scope.selectedDeliveryDO = 'allDos';
        		$scope.selectDeliveryDate = 'today';
        		$scope.loadHubData(dcValue);
                $scope.changedeliveryData(dcValue,$scope.selectDeliveryHub);
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
        		'user_id':selectedDeliveryDO,
        		'from_date': serviceFromDate,
        		'to_date': serviceToDate,
        		'report_type': 'getKPIDeliveryExecReport',
        		'period_type': $scope.selectedPeriodType
        	};
        	$scope.tempDeliveryData = [];
        	$scope.stackDeliveryChartDataset = [];
        	$scope.stackDeliveryCategoryList = [];
        	var chckDeliveryData = [];
        	var tempDeliveryChckData = [];
        	$scope.contsPie = [];
        	$scope.deliveryGridDefs = [];
        	var deliveryGridDefstemp = [];
			var deliveryGridDefsData = [];
			var deliveryGridDefsDataTest = [];
			var columnDeliveryKeys = [];

			$scope.deliveryGridData = [];
        	var req = $http.post('/logisticsummaryreportsapi',{'data':serviceData});
        	$("#loadDeliveryData").show();
        	req.success(function(successCallback){
        		/*var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'string' };
				deliveryGridDefsData.push(tempGridHub);*/
				/*var tempGridHub = { field: 'Period' ,type : 'date',
                                            cellFilter : 'date:"dd-MMM-yy"',
                                            filterCellFiltered : 'true',
                                            };
				deliveryGridDefsData.push(tempGridHub);*/
				var tempGridHub = { headerText: 'Period', key: 'Period', dataType: 'date', width: '100px' };
				deliveryGridDefsData.push(tempGridHub);
				var tempFile = {
					name: "Period", type: "date", width: '100px', formatter: function (value, record) {
						return value.toLocaleDateString();
					}
				};
				deliveryGridDefsDataTest.push(tempFile);
				var colTemop = { columnKey: "Period", width: '100px', allowSummaries: false };
				columnDeliveryKeys.push(colTemop);
				$("#loadDeliveryData").hide();

        		if(successCallback.status == 'Success'){
        			angular.forEach(successCallback.data, function(succValues, succKeys){
        				for (var i = 0; i <= succValues.length-1; i++) {
    						if(succValues[i].hub != 'Grand Total' || succValues[i].hub === -1){
    							chckDeliveryData[succValues[i].hub] = new Array();
    						}else{
    							var tempp = succValues[i].values;
    							for(var j=0; j <= tempp.length-1; j++){
    								angular.forEach(tempp[j], function(valuess, keyss){
    										tempDeliveryChckData[keyss] = new Array();
    								});
    							}
    						}
    					}
        			});
        			
        			/*var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string' };
					deliveryGridDefsData.push(tempGridHub);*/
					/*var tempGridHub = { field: 'Hub', width: 150,
				        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
				  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
				  			'<input class="form-control filterInput" style="width: 110px;background: #f7f7f7 !important;" type="text" ng-model="colFilter.term" /></div></div>',
				        filter: { 
							options: [ 
								{id: uiGridConstants.filter.STARTS_WITH, value: 'STARTS_WITH'}, 
								{id: uiGridConstants.filter.ENDS_WITH, value: 'ENDS_WITH'},
								{id: uiGridConstants.filter.CONTAINS, value: 'CONTAINS'},
							]     
				        }
				    };
					deliveryGridDefsData.push(tempGridHub);*/
					var tempGridHub = { headerText: 'Hub', key: 'Hub', dataType: 'string', width: '100px' };
					deliveryGridDefsData.push(tempGridHub);
					var tempFile = { name: "Hub", type: "string", width: '100px' };
					deliveryGridDefsDataTest.push(tempFile);
					var colTemop = { columnKey: "Hub", width: '100px', allowSummaries: false };
					columnDeliveryKeys.push(colTemop);
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
									    $scope.tempDeliveryData.push(contsPie);
        						});
        					});
        				}else{
        					
        					var categoryTemp = {
        						"label" : succKeys
        					};
        						
        					$scope.stackDeliveryCategoryList.push(categoryTemp);
        					angular.forEach(succValues, function(seriesValues, seriesKeys){
        						if(seriesValues.hub != 'Grand Total'){
        							var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
        							temp = temp.replace(/[\[\]']+/g, '<br/>');
        							temp = temp.replace(/,/g, '<br/>');
        							temp = temp.replace(/tool_tip: null/g, '');
        							temp = temp.replace(/count_value/g, 'Value');
        							temp = temp.replace(/count/g, 'Count');
	            					chckDeliveryData[seriesValues.hub].push({
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
											//tempKeys = finalLinekeys;
											gridPickData[tempKeys] = finalLineValues.count;
										});
									});
									var comGridData = $.extend( GridHubData, gridPickData );
									$scope.deliveryGridData.push(comGridData);
        						}else{
        							angular.forEach(seriesValues.values, function(lineValues, lineKeys){
										angular.forEach(lineValues, function(finalLineValues, finalLinekeys){
		            							tempDeliveryChckData[finalLinekeys].push(
		            								{'value':finalLineValues.count, 'displayValue':finalLineValues.count_value}
		            							);
		            							var gridTemp = {
		            								field: finalLinekeys
		            							};
		            							$scope.deliveryGridDefs.push(gridTemp);
										});     								
        							});
        						}
        					});
        				}
        			});
        		}
								

        		var newArr = $scope.deliveryGridDefs.filter(el => {
				    if (deliveryGridDefstemp.indexOf(el.field) === -1) {
				        // If not present in array, then add it
				        deliveryGridDefstemp.push(el.field);
				        /*deliveryGridDefsData.push({headerText:el.field,key:el.field, dataType: 'number'});*/
				        var headerKey = el.field.replace(/\s/g,'');
						deliveryGridDefsData.push({ headerText:el.field, key: headerKey, dataType: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
						deliveryGridDefsDataTest.push({ name:el.field, type: 'number', columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader'});
						var colTemop = { columnKey: headerKey, columnCssClass: 'igGridNumber', width: '100px', headerCssClass: 'igGridHEader', allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "Total", "type": "SUM", "active": true }] };
						columnDeliveryKeys.push(colTemop);
				        /*deliveryGridDefsData.push({'field':el.field,'displayName':el.field, cellClass: 'right_align', headerClass: 'right_align',
				        filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div>'+
				  			'<select class="form-control filterInput custom-control" ng-model="colFilter.condition" ng-options="option.id as option.value for option in colFilter.options"></select>'+
				  			'<input class="form-control filterInput" type="text" ng-model="colFilter.term" /></div></div>',
				        filter: { 
							//term: '.',
							//type: uiGridConstants.filter.SELECT, 
							options: [ 
								{id: uiGridConstants.filter.GREATER_THAN, value: '>'}, 
								{id: uiGridConstants.filter.LESS_THAN, value: '<'},
								{id: uiGridConstants.filter.EXACT, value: '='},
							]     
				        }});*/
				        return true;
				    } else {
				        // Already present in array, don't add it
				        return false;
				    }
				});

        		$scope.griddeliveryOptions.columnDefs = deliveryGridDefsData;
				$scope.griddeliveryOptions.data = $scope.deliveryGridData;

				// Ignite Grid For Picking
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: deliveryGridDefsDataTest,
					});
					ds = new $.ig.DataSource({
						dataSource: $scope.deliveryGridData,
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
				            	columnSettings:  columnDeliveryKeys, 
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
							columnSettings: columnDeliveryKeys
						});
					}
					if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
							$("#filterByText").igTextEditor("destroy");
							$("#filterByText").remove();
							$("#searchLabel").remove();
					}
					if ($("#gridDeliveryFiltering").data("igGrid")) {
						$("#gridDeliveryFiltering").igGrid("destroy");
					}
					$("#gridDeliveryFiltering").igGrid({
						autoGenerateColumns: false,
						height: "400px",
						width: "100%",
						columns: deliveryGridDefsData,
						dataSource: $scope.deliveryGridData,
						features: features
					});
				}

				// End
        		for(let k in chckDeliveryData){
        			var data = {
        				"seriesname": k,
                		"data": chckDeliveryData[k]
        			}
        			if(data.seriesname != "undefined"){
        				$scope.stackDeliveryChartDataset.push(data);
        			}
        		}
        		for(let k in tempDeliveryChckData){
        			var data = {
        				"seriesname": k,
        				"renderAs": "line",
        				"showValues": "0",
                		"data": tempDeliveryChckData[k]
        			}
    				$scope.stackDeliveryChartDataset.push(data);
        		}

        		// Delivery Summary Pie Chart
        		var propertiesDeliveryObject = {
					type : "pie3d",
					id : "Delivery-chart",
					width : "100%",
					height: "400",
					renderAt: "chart-Delivery",
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
							caption: 'Delivery Summary',
							toolTipBorderColor: "#666666",
					        toolTipBgColor: "#666666",
					        toolTipColor: "#ffffff",
    						exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
    						exportTargetWindow: "_self",
							plottooltext: "$label<br/>Count: $datavalue <br/>Value: $displayValue"
						},
						data:$scope.tempDeliveryData
					}
				}
				var DeliveryChart = new FusionCharts(propertiesDeliveryObject);
				DeliveryChart.render();

				// multi series column
				var stackDeliveryPropertiesObject = {
					type: 'stackedColumn3DLine',
			        renderAt: 'DeliveryStackChart',
			        width: '100%',
			        height: '400',
			        dataFormat: 'json',
			        dataSource: {
			            "chart": {
			                //"numberPrefix": "₹",
			                showvalues: "0",
			                rotateValues: "0",
                			placeValuesInside: "0",
                			caption: 'Delivery Summary',
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
			                    "category": $scope.stackDeliveryCategoryList
			                }
			            ],
			            "dataset": $scope.stackDeliveryChartDataset
					}
				}
				var DeliveryStackedChart = new FusionCharts(stackDeliveryPropertiesObject);
				DeliveryStackedChart.render();
            });
            req.error(function(errorCallback){
        	$("#loadDeliveryData").show();
            });
        }

        // Logistics Performance
        $scope.loadPerformanceLogisticData = function(dcValue, hubValue, fromDate){
        	var serviceFromDate;
        	var serviceToDate;
        	if(hubValue == 'allHub'){
        		hubValue = null;
        	}
        	$scope.togglePerformTable = hubValue;
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#reqFromPerformanceDate')).val();
				serviceToDate = angular.element(document.querySelector('#reqToPerformanceDate')).val();
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
        	$scope.dateList = [];
        	$scope.performHubList = [];
        	$scope.perfLogisticsData = [];
        	$scope.perfLogisticsDataAllHub = [];
        	$scope.perfKeyList = [];
        	var req = $http.post('/logistics/api?dcId='+dcValue+'&hubId='+hubValue+'&fromDate='+serviceFromDate+'&toDate='+serviceToDate);
        	$("#togglePerformanceSummary").show()
        	req.success(function(successCallback){
        		$("#togglePerformanceSummary").hide();
        		if(successCallback.status == true){
        			$scope.performanceLogistic = successCallback.message;
        			angular.forEach($scope.performanceLogistic, function(values, keys){
        				if($scope.dateList.indexOf(values.Date) == -1){
        					$scope.dateList.push(values.Date);
        				}
        				if($scope.performHubList.indexOf(values.Hub) == -1){
        					$scope.performHubList.push(values.Hub);
        				}
        				$scope.perfLogisticsData.push(values);

        				$scope.perfHubName = values.Hub;
        				delete $scope.perfLogisticsData[keys]["DC"];
        				delete $scope.perfLogisticsData[keys]["Hub"];
        				delete $scope.perfLogisticsData[keys]["Date"];
        				/*angular.forEach(values, function(keyValues, keyList){
        					if(keyList != 'DC' && keyList != 'Hub' && keyList != 'Date'){
        						if ($scope.perfKeyList.indexOf(keyList) == -1) {
	        						$scope.perfKeyList.push(keyList);
	        					}

        					}
        				});*/
        			});
        		}
        	});
        	req.error(function(errorCallback){
        		$("#togglePerformanceSummary").hide();
        	});
        } 

        // Returns With Damage and Missing
        $scope.getDamageReport = function(dcid,hub_id,fromDate){
			$scope.damagegridOptions.data = [];
			var serviceFromDate;
        	var serviceToDate;
        	if(hub_id == 'allHub' || hub_id == undefined){
        		hub_id = null;
        	}
    		if(fromDate == 'customDate'){
        		serviceFromDate = angular.element(document.querySelector('#damFromDate')).val();
				serviceToDate = angular.element(document.querySelector('#damToDate')).val();
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
        		var fromDate = new Date();
                fromDate = new Date(fromDate.setDate(fromDate.getDate()-1));
        		serviceFromDate = $filter('date')(fromDate, 'yyyy-MM-dd');
	            serviceToDate = $filter('date')(fromDate, 'yyyy-MM-dd');
	            $scope.damselectedDate = 'yesterday';
        	}else if(fromDate == 'yesterday'){
        		fromDate = new Date();
                fromDate = new Date(fromDate.setDate(fromDate.getDate()-1));
        		serviceFromDate = $filter('date')(fromDate, 'yyyy-MM-dd');
	            serviceToDate = $filter('date')(fromDate, 'yyyy-MM-dd');
        	}else if(fromDate == 'today'){
	            var toDate = new Date();
	            serviceFromDate = $filter('date')(toDate, 'yyyy-MM-dd');
	            serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
        	}else{
        		serviceFromDate = $scope.fromDatePicker;
	            var toDate = new Date();
	            serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');
        	}

        	if(serviceFromDate == undefined){
        		var toDate = new Date();
	            serviceToDate = $filter('date')(toDate, 'yyyy-MM-dd');	
        	}
			var req = $http.post('/logistics/getdamagereport?dc_id='+dcid+'&hub_id='+hub_id+'&start_date='+serviceFromDate+'&end_date='+serviceToDate);
        	$("#damageReportInit").show();
        	var Damageheader = [];
        	var DamageheaderTemp = [];
        	req.success(function(successCallback){
        		$("#damageReportInit").hide();
        		if(successCallback.status == true){
        			$("#damage_grid").show();
        			var type = "";
					var classTo = "center_align";
					var temmpLate = "";
					var cellClass = "center_align";
					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{2}\/\d{2}\/\d{4}$/;
        			$scope.damageReportData = successCallback.data;
        			angular.forEach($scope.damageReportData[0],function(val,key){
						/*var result = reg.test(val);
                        var pinleft = true;
                        var hidePinLeft=false;
                        var hidePinRight = false;
                        var pinnedLeft=false;
                        var width = 150;

                        if(key == 'Dc' || key == 'DC' || key == 'HUB' || key == 'Hub'){
                            pinnedLeft = true;
                        }

                        if(key == 'HUB' || key == 'Hub'){
                        	width = 130
                        }

                        if(key == 'SKU' || key == 'Sku'){
                        	width = 120
                        }

                        if(key == 'Order Code'){
                        	width = 130
                        }

                        if(key == 'Self Order' || key == 'Dc' || key == 'DC'){
                            width=90;
                        }

        				type = typeof(val);
        				if(typeof(val) === "number" || result){
        					 classTo = "right_align";
        					 cellClass = "right_align";
                             width = 110;
                             type = 'number';
        				}else{
        					classTo = "center_align";
        					cellClass = "center_align";
        				}
        				
        				var dateReg = /^\d{2}-\d{2}-\d{4}$/;
        				var Dateresult = dateReg.test(val);
			            if(Dateresult){
			             type = "date";
                            width = 105;
			            }

        				Damageheader.push({field:key,displayName:key,width:width,type:type,headerCellClass: classTo,footerCellClass: classTo ,footerCellTemplate:temmpLate,cellClass:cellClass,cellTemplate: '<div class="ui-grid-cell-contents"  data-toggle="tooltip" title="<%grid.getCellValue(row, col)%>"><%grid.getCellValue(row, col)%></div>',enablePinning:true, hidePinLeft: hidePinLeft, hidePinRight: hidePinRight,pinnedLeft:pinnedLeft});*/
        				var result = reg.test(val);
                        var width = '110px';
                        var columnCssClass = "";
                        var headerCssClass = "";
                        var headerKey = key;
       					headerKey = headerKey.replace(/_/g, " ");
             			type = typeof(val);
             			if(typeof(val) === "number" || result){
               				columnCssClass = "right_align";
               				headerCssClass = "header_right_align";
                            width = '100px';
                            type = 'number';
                            format = "0.00";                             
             			}else{
			            	format = "";
			            	width = '100px';
			            	columnCssClass = "center_align";             
			            	headerCssClass = "header_center_align";
			            }
			            var Dateresult = dateReg.test(val);

			            if(Dateresult){
			        		type = "date";
			        		format = "dd-MM-yyyy";
			                width = '120px';
			                columnCssClass = "left_align";
			              	headerCssClass = "header_center_align";
			            }
             
            			Damageheader.push({key:key, headerText:headerKey, width:width, dataType:type, format:format,
              			columnCssClass: columnCssClass, headerCssClass: headerCssClass });
              			/*var tempFile = { name: "Hub", type: "string", width: '100px' };
						DamageheaderTest.push(tempFile);*/
        			});
                    //$scope.grid1Api.grid.refresh();
        			//$scope.damagegridOptions.columnDefs = Damageheader;
        			//$scope.damagegridOptions.data = $scope.damageReportData;

        			$('#returnsDamMissGrid').igGrid({
	                    dataSource: $scope.damageReportData,
	                    dataSourceType: "json",
	                    width: "100%",
	                    columns: Damageheader,
	                    initialDataBindDepth: 1,
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

        			/*// Ignite Grid For Picking
					$(function () {
						var f = true, ds, schema;

						schema = new $.ig.DataSchema("array", {
							fields: pickGridDefsDataTest,
						});
						ds = new $.ig.DataSource({
							dataSource: $scope.pickGridData,
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
					            	columnSettings:  columnKeys, 
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
								columnSettings: columnKeys
							});
						}
						if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
								$("#filterByText").igTextEditor("destroy");
								$("#filterByText").remove();
								$("#searchLabel").remove();
						}
						if ($("#gridSimpleFiltering").data("igGrid")) {
							$("#gridSimpleFiltering").igGrid("destroy");
						}
						$("#gridSimpleFiltering").igGrid({
							autoGenerateColumns: false,
							height: "400px",
							width: "100%",
							columns: pickGridDefsData,
							dataSource: $scope.pickGridData,
							features: features
						});
					}

					// End*/
        			
        		}else{
        			$("#damage_grid").hide();
        			$scope.gridErrorMessage = "No data found!";
        		}
        	});
        	req.error(function(errorCallback){
        		$("#damageReportInit").hide();
        	});
		}
});
</script>

<script type="text/javascript">
	$('#datetimepickerfromDate').datetimepicker({
		format: "DD-MM-YYYY"
		// for setting min and max date
		/*minDate: moment().add(-30, 'days'),
		maxDate: moment().add(0, 'days') */
	});

	$('#datetimepickertoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#returnSumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#returnSumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#crateSumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#crateSumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#CycleSumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#CycleSumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#checkingfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#checkingtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#datetimepickerPerfFromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#PerfToDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#datetimeDcSummaryFromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#DcSummaryToDate').datetimepicker({
		format: "DD-MM-YYYY"
	});


	$('#hubSummaryFromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#hubSummaryToDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#HoldsumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#HoldsumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#damFromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#damToDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#inventorysumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#inventorysumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#deliverysumfromDate').datetimepicker({
		format: "DD-MM-YYYY"
	});

	$('#deliverysumtoDate').datetimepicker({
		format: "DD-MM-YYYY"
	});
</script>
@stop
@extends('layouts.footer')
