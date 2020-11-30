<div class="portlet-body" style="height: 500px;">
	<div id="loadAllTabsSumData" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
    	<div class="col-md-12" ng-click="toggleAllTabsSumSummary()">
    		<span style="font-weight: 600;">Sales Orders Summary</span>
    		<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
    	</div>
    	<div ng-show="toggleAllTabsSumSummaryVar">
            <div class="col-md-3" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
                <select ng-model="selectAllTabsSumDc" ng-change="loadHubData(selectAllTabsSumDc)">
                    <option selected value="">---- Select DC ----</option>
                    <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;" class="col-md-3">
                <select ng-model="selectAllTabsSumHub">
                    <option disabled="disabled">---- Select HUB ----</option>
                    <option value="allHub">All</option>
                    <option ng-repeat="(key,value) in hubList" value="<%key%>"><%value%></option>
                </select>
            </div>
            <div style="margin-top: 8px;padding-right: 0px !important;" class="col-md-1">
                <button ng-click="loadAllTabsSumData(selectAllTabsSumDc,selectAllTabsSumHub)">Go</button>
            </div>
        </div>
    </div> 
    <div class="tabbable tabs-below">
    	<div class="tab-content">
            <div class="tab-pane active" id="AllTabsSumPie" style="height: 415px !important;"> 
               <div id="chart-AllTabsSum"></div>   
            </div>
            <div class="tab-pane" id="AllTabsSumStacked" style="height: 415px !important;">
               <div id="AllTabsSumStackChart"></div>   
            </div>
            <div class="tab-pane" id="AllTabsSumGrid" style="height: 415px !important;padding: 10px;">
            	<div class="pickSumTable">
                    <table id="gridAllTabsSumFiltering"></table>
				</div>
            </div>
        </div>
        <div class="barChatCLass" style="border-top: 1px solid #eee;position: absolute;bottom: 0px;width: 95%;">
            <ul class="nav" style="display: flex;float: right;">
                <li class="custIcon active"><a href="#AllTabsSumPie" data-toggle="tab" class="fa fa-pie-chart fa-lg" aria-hidden="true" title="Pie Chart"></a></li>
                <li class="custIcon"><a href="#AllTabsSumStacked" data-toggle="tab" class="fa fa-bar-chart fa-lg" aria-hidden="true" title="Stacked Chart"></a></li>
                <li class="custIcon"><a href="#AllTabsSumGrid" ng-mouseover="ngGridFIx()" ng-click="ngGridFIx()" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
            </ul>
        </div>
    </div>
</div>

