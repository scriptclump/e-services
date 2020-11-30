<div class="portlet-body" style="height: 500px;">
	<div id="loadVehicleData" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
    	<div class="col-md-12" ng-click="toggleVehicle()">
    		<span style="font-weight: 600;">Vehicle Details Summary</span>
    		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
    	</div>
    	<div ng-show="toggleVarVehicle">
            <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                <select ng-model="selectVehicleDc" ng-change="loadHubData(selectVehicleDc)">
                    <option selected value="">---- Select DC ----</option>
                    <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectVehicleHub">
                    <option disabled="disabled">---- Select HUB ----</option>
                    <option value="allHub">All</option>
                    <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectVehicleDO">
                    <option disabled="disabled">---- Select Vehicle Number ----</option>
                    <option value="allVehicles">All</option>
                    <option ng-repeat="(key,value) in vehicleList" value="<%value.vehicle_id%>"><%value.reg_no%></option>
                </select>
            </div>
            <div style="margin-top: 8px;" class="col-md-2">
                <select ng-model="selectVehicleDate" ng-change="loadSelectedDates(selectVehicleDate)">
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
            <div ng-show="selectVehicleDate == 'customDate'">
            	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='VehiclefromDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="VehicleFromDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='VehicletoDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="VehicleToDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
            </div>
            <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectVehicleDate == 'customDate'}">
                <button ng-click="loadVehicleData(selectVehicleDc,selectVehicleHub,selectVehicleDO,selectVehicleDate)">Go</button>
            </div>
        </div>
    </div> 
    <div class="tabbable tabs-below">
    	<div class="tab-content">
            <div class="tab-pane active" id="VehicleGrid" style="height: 415px !important;padding: 10px;">
            	<div class="pickSumTable">
            		<table id="VehiclesDetailGrid" style="white-space: normal;"></table>
				</div>
            </div>
        </div>
        <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 97%;">
            <ul class="nav" style="display: flex;float: right;">
                <li class="custIcon"><a href="#VehicleGrid" ng-mouseover="ngGridFIx()" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
            </ul>
        </div>
    </div>
</div>

    	