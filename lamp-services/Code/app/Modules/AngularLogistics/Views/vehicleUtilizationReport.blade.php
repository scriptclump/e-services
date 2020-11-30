<div class="portlet-body" style="height: 500px;width: 103%;">
	<div id="loadVehUtiltySumData" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
    	<div class="col-md-12" ng-click="toggleVehUtilitySummary()">
    		<span style="font-weight: 600;">Vehicle Utilization Summary</span>
    		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
    	</div>
    	<div ng-show="toggleVehUtilitySummaryVar">
            <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                <select ng-model="selectVehUtiltyDc" ng-change="loadHubData(selectVehUtiltyDc)">
                    <option selected value="">---- Select DC ----</option>
                    <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectVehUtiltyHub">
                    <option disabled="disabled">---- Select HUB ----</option>
                    <option value="allHub">All</option>
                    <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectVehUtiltyNumber">
                    <option disabled="disabled">---- Select Vehicle Number ----</option>
                    <option value="allVehicles">All</option>
                    <option ng-repeat="(key,value) in vehicleList" value="<%value.vehicle_id%>"><%value.reg_no%></option>
                </select>
            </div>
            <div style="margin-top: 8px;" class="col-md-2">
                <select ng-model="selectVehUtiltyDate" ng-change="loadSelectedDates(selectVehUtiltyDate)">
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
            <div ng-show="selectVehUtiltyDate == 'customDate'">
            	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='VehUtiltysumfromDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeinventoryer="From Date" ng-model="date" id="VehUtiltysumFromDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='VehUtiltysumtoDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeinventoryer="To Date" ng-model="date" id="VehUtiltysumToDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
            </div>
            <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectVehUtiltyDate == 'customDate'}">
                <button ng-click="loadVehUtiltySumData(selectVehUtiltyDc,selectVehUtiltyHub,selectVehUtiltyNumber,selectVehUtiltyDate)">Go</button>
            </div>
        </div>
    </div> 
    <div class="tabbable tabs-below">
    	<div class="tab-content">
            <div class="tab-pane active" id="VehUtiltySumPie" style="height: 415px !important;"> 
               <div id="chart-vehicleUtilizeSum"></div>   
            </div>
            <div class="tab-pane" id="VehUtiltySumStacked" style="height: 415px !important;">
               <div id="vehicleUtilizeSumStackChart"></div>   
            </div>
            <div class="tab-pane" id="VehUtiltySumGrid" style="height: 415px !important;padding: 10px;">
            	<div class="pickSumTable">
                    <table id="gridvehicleUtilizeFiltering"></table>
				</div>
            </div>
        </div>
        <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 98%;">
            <ul class="nav" style="display: flex;float: right;">
                <li class="custIcon active"><a href="#VehUtiltySumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                <li class="custIcon"><a href="#VehUtiltySumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                <li class="custIcon"><a href="#VehUtiltySumGrid" ng-mouseover="ngGridFIx()" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
            </ul>
        </div>
    </div>
</div>

