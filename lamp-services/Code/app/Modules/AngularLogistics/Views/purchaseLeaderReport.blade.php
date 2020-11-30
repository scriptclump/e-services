<div class="portlet-body" style="width: 103%;">
	<div id="purchaseLeaderAnalyse" style="display: none" class="loader" ></div>
	<div class="row" style="padding:10px;">
		<div class="col-md-12" ng-click="togglePurchaseLeaderSummary()">
			<span style="font-weight: 600;">Purchase Leader Summary</span>
			<span style="float: right;"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
		</div>
		<div ng-show="togglePurchaseLeader">
			<div class="col-md-4" style="margin-top: 8px;padding-left: 15px;padding-right: 0px !important;">
				<select ng-model="selectPurchaseDC">
					 <option selected value="">---- Select DC ----</option>
					 <option ng-repeat="(key,value) in dcList.DC" value="<%key%>"><%value%></option>
				</select>
			</div>
			<div style="margin-top: 8px;" class="col-md-1">
				<button ng-click="purchaseLeaderAnalyse(selectPurchaseDC)">Go</button>
			</div>
		</div>
	</div>
	<div class="tabbable tabs-below">
		<div class="tab-content" style="float:center!important;align-content: center;">
			<div class="tab-pane active"  style="height: 350px !important;padding: 10px;overflow: scroll;">
				<table class="search-table" style="width: 100%;text-align: center;">
					<thead>
						<tr><th colspan="2" style="text-align: center;color: black;background-color: #f7f7f7;">Purchase Leader</th></tr>
						<tr>
							<th>Particulars</th>
							<th style="text-align: right;">Value</th>
						</tr>
					</thead>
					<tbody> 
						<tr ng-repeat="(key, data) in purchaseLeaderValueArray[0]">
							<td style="text-align: left"><%key%></td>
							<td style="text-align: right;"><%data%></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>