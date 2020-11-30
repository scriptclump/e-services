<div class="col-md-12 selectBox" ng-init="getDamageReport(DefaultDcList,selectedHub,'current')">
        <div class="portlet-body" style="height: 500px;width: 100%;">
            <div id="damageReportInit" style="display: none" class="loader" ></div>
            <div class="row" style="padding:10px;">
                <div class="col-md-12" ng-click="returnDME()">
                    <span style="font-weight: 600;">Returns With Damage, Missing and Excess Summary</span>
                    <span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
                </div>
                <div ng-show="returnDMEVariable">
                    <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                        <select ng-model="selectedDc" ng-change="loadHubData(selectedDc)">
                            <option selected value="">---- Select DC ----</option>
                            <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                        </select>
                    </div>
                    <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                        <select ng-model="selectedHub" >
                            <option selected value="">---- Select HUB ----</option>
                            <option value="allHub">All</option>
                            <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                        </select>
                    </div>
                    <div style="margin-top: 8px;" class="col-md-2">
                        <select ng-model="damselectedDate" ng-change="loadSelectedDates(damselectedDate)">
                            <option value="" selected="selected">Period</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="wtd">WTD</option>
                            <option value="mtd">MTD</option>
                            <option value="ytd">YTD</option>
                            <option value="customDate">Custom Date</option>
                        </select>
                    </div>
                    <div ng-show="damselectedDate == 'customDate'">
                        <div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
                            <div class="form-group">
                                <div class='input-group date'>
                                    <input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="From Date" ng-model="damFromDate" id="damFromDate"/ >
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 10px;padding-left: 15px;padding-right: 0px !important;">
                            <div class="form-group">
                                <div class='input-group date' >
                                    <input type='text' class="form-control" style="border: 1px solid #e5e5e5;height: 30px;" placeholder="To Date" ng-model="damToDate" id="damToDate"/ >
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 8px;    margin-left: -8px;padding-left: 0px;padding-right: 0px !important;" class="col-md-1" ng-class="{dynamicBut: damselectedDate == 'customDate'}">
                        <button ng-click="getDamageReport(selectedDc,selectedHub,damselectedDate)">Go</button>
                    </div>
                </div>
            </div> 
            
            <div>
                <!-- <div style="height: 530px;font-size: 12px;" ng-mouseover="ngGridFIx()" ui-grid-pagination ui-grid="damagegridOptions" ui-grid-exporter ui-grid-pinning ui-grid-resize-columns ui-grid-move-columns class="myGrid"></div> -->
                <table id="returnsDamMissGrid"></table>
            </div> 
            <!-- <span style="padding-left: 10px;" ng-if="gridErrorMessage == 'No data found!'">No data found!</span> -->
        </div>
    </div>
