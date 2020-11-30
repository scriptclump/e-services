<div class="portlet-body" style="height: 500px;">
	<div id="loadDeliveryPerfData" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
    	<div class="col-md-12" ng-click="toggleDelPerf()">
    		<span style="font-weight: 600;">Delivery Performance Summary</span>
    		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
    	</div>
    	<div ng-show="toggleVarDelPerf">
            <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                <select ng-model="selectDelPerfDc" ng-change="loadHubData(selectDelPerfDc)">
                    <option selected value="">---- Select DC ----</option>
                    <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectDelPerfHub" ng-change="changedeliveryData(selectDelPerfDc,selectDelPerfHub)">
                    <option disabled="disabled">---- Select HUB ----</option>
                    <option value="allHub">All</option>
                    <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectDelPerfDO">
                    <option disabled="disabled">---- Select Delivery Executive ----</option>
                    <option value="allDos">All</option>
                    <option ng-repeat="(key,value) in Deliverydata" value="<%value.UserId%>"><%value.UserName%></option>
                </select>
            </div>
            <div style="margin-top: 8px;" class="col-md-2">
                <select ng-model="selectDelPerfDate" ng-change="loadSelectedDates(selectDelPerfDate)">
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
            <div ng-show="selectDelPerfDate == 'customDate'">
            	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='DelPerffromDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="date" id="DelPerfFromDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='DelPerftoDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="date" id="DelPerfToDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
            </div>
            <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectDelPerfDate == 'customDate'}">
                <button ng-click="loadDeliveryPerfData(selectDelPerfDc,selectDelPerfHub,selectDelPerfDO,selectDelPerfDate)">Go</button>
            </div>
        </div>
    </div> 
    <div class="tabbable tabs-below">
    	<div class="tab-content">
            <div class="tab-pane active" id="DelPerfPie" style="height: 415px !important;"> 
               <div id="delPerfChart-container"></div>   
            </div>
            <div class="tab-pane" id="DelPerfStacked" style="height: 415px !important;">
               <div id="deliveryPerfStackChart"></div>   
            </div>
            <div class="tab-pane" id="DelPerfGrid" style="height: 415px !important;padding: 10px;">
            	<div class="pickSumTable">
            		<table id="gridDelPerformance"></table>
				</div>
            </div>
        </div>
        <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 97%;">
            <ul class="nav" style="display: flex;float: right;">
                <li class="custIcon active"><a href="#DelPerfPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                <li class="custIcon"><a href="#DelPerfStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                <li class="custIcon"><a href="#DelPerfGrid" ng-mouseover="ngGridFIx()" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
            </ul>
        </div>
    </div>
</div>

    	