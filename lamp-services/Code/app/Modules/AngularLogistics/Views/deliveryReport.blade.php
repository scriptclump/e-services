<div class="portlet-body" style="height: 500px;">
	<div id="loadDeliveryData" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
    	<div class="col-md-12" ng-click="toggleDeliverySummary()">
    		<span style="font-weight: 600;">Delivery Summary</span>
    		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
    	</div>
    	<div ng-show="toggleDeliverySummaryVar">
            <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                <select ng-model="selectDeliveryDc" ng-change="loadHubData(selectDeliveryDc)">
                    <option selected value="">---- Select DC ----</option>
                    <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectDeliveryHub" ng-change="changedeliveryData(selectDeliveryDc,selectDeliveryHub)">
                    <option disabled="disabled">---- Select HUB ----</option>
                    <option value="allHub">All</option>
                    <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectedDeliveryDO">
                    <option disabled="disabled">---- Select Delivery Executive ----</option>
                    <option value="allDos">All</option>
                    <option ng-repeat="(key,value) in Deliverydata" value="<%value.UserId%>"><%value.UserName%></option>
                </select>
            </div>
            <div style="margin-top: 8px;" class="col-md-2">
                <select ng-model="selectDeliveryDate" ng-change="loadSelectedDates(selectDeliveryDate)">
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
            <div ng-show="selectDeliveryDate == 'customDate'">
            	<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='deliverysumfromDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeinventoryer="From Date" ng-model="date" id="deliverysumFromDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
					<div class="form-group">
						<div class='input-group date' id='deliverysumtoDate' >
							<input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeinventoryer="To Date" ng-model="date" id="deliverysumToDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
            </div>
            <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: selectDeliveryDate == 'customDate'}">
                <button ng-click="loadDeliveryData(selectDeliveryDc,selectDeliveryHub,selectedDeliveryDO,selectDeliveryDate)">Go</button>
            </div>
        </div>
    </div> 
    <div class="tabbable tabs-below">
    	<div class="tab-content">
            <div class="tab-pane active" id="deliverySumPie" style="height: 415px !important;"> 
               <div id="chart-Delivery"></div>   
            </div>
            <div class="tab-pane" id="deliverySumStacked" style="height: 415px !important;">
               <div id="DeliveryStackChart"></div>   
            </div>
            <div class="tab-pane" id="deliverySumGrid" style="height: 415px !important;padding: 10px;">
            	<div class="pickSumTable">
					<!-- <div ui-grid-pagination ng-mouseover="ngGridFIx()" ui-grid-pinning ui-grid-resize-columns ui-grid-move-columns ui-grid="griddeliveryOptions" ui-grid-exporter class="myGrid"></div> -->
                    <table id="gridDeliveryFiltering"></table>
				</div>
            </div>
        </div>
        <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 95%;">
            <ul class="nav" style="display: flex;float: right;">
                <li class="custIcon active"><a href="#deliverySumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                <li class="custIcon"><a href="#deliverySumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                <li class="custIcon"><a href="#deliverySumGrid" ng-mouseover="ngGridFIx()" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
            </ul>
        </div>
    </div>
</div>

