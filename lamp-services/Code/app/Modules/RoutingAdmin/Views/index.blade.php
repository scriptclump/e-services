@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<style type="text/css">
	.norecord{
		margin-left : 450px;
		margin-top: 58px;
	}
	.green{
		color: #00ff00;
	}
	.red{
		color : red;
	}
	.dcdrop{
		
		height: 35px !important;
		width: 100% !important;
		border-radius: 0px!important;
	}
	.btnrow{
		position: relative;
	    /* float: right; */
	    /* right: 100px!important; */
	    margin-bottom: 20px;
	    text-align: center;
	    margin-top: 10px;

	}
	.btnrow .btn {
	    width: 150px;
	}
	.commonbtn{
		padding:7px 40px!important;
	}

	.input-group .input-group-addon {
	    border-color: #aaa;
	    background: #e5e5e5;
	    min-width: 39px;
	}
	
	
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.5/angular.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular-smart-table/2.1.8/smart-table.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ngInfiniteScroll/1.3.0/ng-infinite-scroll.min.js"></script>


<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><span class="bread-color">Report</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/getroutehistory">Route History</a></li>
		</ul>
	</div>
</div>
<div class="col-md-15 col-sm-12" ng-app="plunker" ng-controller="historyController" ng-init="data = <?php echo htmlspecialchars(json_encode($data)); ?>">

	<div  class="portlet-body" style="margin-top: 30px;">
		<div>
		<input type="hidden" name="page_no" value="1"/ >
		<input type="hidden" name="offset_count" value="10"/>
			 			<div class="row">
				<div class='col-md-3'>
					<select class="dcdrop" id="dcvalue" name="dc" ng-model ="dc" ng-change="getHublist()" >
						<option disabled selected value>----Select DC----</option>
						<option  ng-repeat="(key,value) in data.dc" value="<%key%>"><%value%></option>

					</select>
				</div>
				<div class='col-md-3'>
					<select class="dcdrop" id="hubvalue" name="hub_id" disabled >
						<option disabled selected value>----Select HUB----</option>
						<option  ng-repeat="(keyey,val) in hubdata" value="<%keyey%>"><%val%></option>

					</select>
				</div>
			<!-- </div>
			<div class="row" style = "margin-top: 20px!important;"> -->
				<div class='col-md-3'>

					<!-- <Strong>From Date</Strong> -->
					<div class="form-group">

						<div class='input-group date' id='datetimepicker6' >
							<input type='text' id="startDate" style="border: 1px solid #aaa;" class="form-control" max-date="<%maxDate%>" name="from_date" ng-model="date" placeholder="From Date" / >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class='col-md-3'>
					<!-- <Strong>To Date</Strong> -->
					<div class="form-group">
						<div class='input-group date' id='datetimepicker7' >
							<input type='text' id="endDate" style="border: 1px solid #aaa;" class="form-control" max-date="<%maxDate%>" name="to_date" ng-model="date" placeholder="To Date" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row btnrow">
				<input type="submit" id="submitform" class="btn green-meadow btn-xs commonbtn" value="Submit" ng-click="submit()">
				<input type="button" id="newroute" class="btn green-meadow btn-xs commonbtn" value="Create New"  onclick="window.open('/routingadmin/generatenewroutes','_blank')">
			</div>
		</div>
		


		<div class="constrained" style="overflow:auto;height:360px; border: 1px solid lightgray;">
			<table  class="table table-striped" id="div-to-update" infinite-scroll="myPagingFunction(histData)"  infinite-scroll-distance="3" infinite-scroll-parent="true"  infinite-scroll-disabled="busyLoadingData" >
				<thead>
					<tr>
						<th ng-click="sortType = 'route_id'; sortReverse = !sortReverse">Route Id
							<span ng-show="sortType == 'route_id' && !sortReverse" class="fa fa-caret-down"></span>
							<span ng-show="sortType == 'route_id' && sortReverse" class="fa fa-caret-up"></span></th>
							<th >Order Count
								</th>
								<th >Crate Count
									</th>
									<th >Vehicle Count
										</th>
										<th >Unassigned Count
											</th>
											<th ng-click="sortType = 'created_at'; sortReverse = !sortReverse">Created At
											<span ng-show="sortType == 'created_at' && !sortReverse" class="fa fa-caret-down"></span>
											<span ng-show="sortType == 'created_at' && sortReverse" class="fa fa-caret-up"></span></th>
											</th>
											<th>Actions
												</th>


											</tr>
											<tr>
												<th>
													<input  placeholder="search for RouteId" class="input-sm form-control" type="search" ng-model="c1" style="width: 60%;"/>
												</th>
												<th>
													&nbsp;
												</th>
												<th>
													&nbsp;
												</th>
												<th>
													&nbsp;
												</th>
												<th>
													&nbsp;
												</th>
												<th>
													&nbsp;
												</th>
												<th>
													&nbsp;
												</th>
											</tr>

										</thead>
										<div>


											<tbody>

												<input type="hidden" id="page" value="" />
												<tr ng-repeat="(key,val) in histData | filter:{route_id:c1} | orderBy:sortType:sortReverse" ng-model="histData">
													<td><%val.route_id%></td>
													<td><%val.order_count%></td>
													<td><%val.crate_count%></td>
													<td><%val.vehicle_count%></td>
													<td><%val.unassigned_count%></td>
													<td><%val.created_at%></td>
													<td><a href="/routingadmin/viewroutehistory?route_id=<%val.route_id%>&hub_id=<%hub_id%>" target="_blank"><span  class="fa fa-eye"></span></a></td>



												</tr>
											</tbody>

										</div>

									</table>
									<tr><strong><p ng-show="messageScroll" class="norecord"><%messageScroll%></p></strong></tr>

									<p ng-show="histData.length==0" class="norecord">There are No Records to Show for the given Date</p>


								</div>
								<script  type="text/javascript">
									
									var app = angular.module("plunker", ["smart-table","infinite-scroll"],function($interpolateProvider){
										$interpolateProvider.startSymbol('<%');
										$interpolateProvider.endSymbol('%>');
									});
									
									app.controller('historyController', historyController);

									function historyController($http,$scope,$rootScope) {
										$scope.busyLoadingData = true;
										$scope.sortType = "created_at";
										$scope.sortReverse = true;
										$scope.getHublist = function() {
											var dc = angular.element(document.querySelector('#dcvalue')).val();
											$scope.init = '<?php echo json_encode($data); ?>';
											$scope.initdata = angular.fromJson($scope.init);
											$('#hubvalue').prop('disabled',false);
											$scope.hubdata = [];
											angular.forEach($scope.initdata.hub, function(dcval, dckey) {
												if (dckey === dc) {
													$scope.hubdata = dcval;	
												}
											});


										}
										
										$scope.submit = function(){
											

											   //var dc = angular.element(document.querySelector('#dcvalue')).val();
											$scope.hub_id = angular.element(document.querySelector('#hubvalue')).val();
											$scope.fromDate = 
											   angular.element('#startDate').val();
											$scope.toDate = 
											   angular.element('#endDate').val();
											$scope.pageno = angular.element('#page').val();
											if($scope.pageno==""){
												angular.element('#page').val(1);
											}
											$scope.offset = 10;
											var page = angular.element('#page').val();
											$scope.formData = 
											   	angular.toJson({"hub_id": $scope.hub_id,"from_date": $scope.fromDate,"to_date": $scope.toDate,"page_no": page,"offset_count": $scope.offset})
												$http.post('/routingadmin/gethistoricalroutes',$scope.formData)
												.success(function(response, status, headers, config) {

													if(response.status==true){
														$scope.histData = response.message;
														$scope.busyLoadingData = false;
													}else{
														alert(response.message);
													}


												})
												.error(function(data, status, header, config) {

												});
											
											

										}
										$scope.myPagingFunction = function(histData) {
											$scope.hub_id = angular.element(document.querySelector('#hubvalue')).val();
											   $scope.fromDate = 
											   angular.element('#startDate').val();
											    $scope.toDate = 
											   angular.element('#endDate').val();
											   $scope.pageno = angular.element('#page').val();

											if($scope.pageno==""){
												angular.element('#page').val(1);
											}else{
												angular.element('#page').val(parseInt($scope.pageno)+1);				
											}
											if ($scope.busyLoadingData) return;
											$scope.busyLoadingData = true;
											 $scope.offset = 10;
											var pageno = angular.element('#page').val();
											$scope.formData = 
											   	angular.toJson({"hub_id": $scope.hub_id,"from_date": $scope.fromDate,"to_date": $scope.toDate,"page_no": pageno,"offset_count": $scope.offset});
											   	$http.post('/routingadmin/gethistoricalroutes',$scope.formData)
												.success(function(response, status, headers, config) {

													if(response.status==true){
														if(response.message.length!=0){
														$scope.Arr = [];
														angular.forEach(histData, function(arr, key) {
														$scope.Arr[key] = arr;
														
														
													});
													angular.forEach(response.message, function(val, key) {

														$scope.Arr.push(val);
														
														
													});


													$scope.histData = $scope.Arr;
													$scope.busyLoadingData = false;				
													
												}
													}else{
														
														angular.element('#page').val("");
														if(response.message=="pre-existing routes not available"){
														$scope.messageScroll = "No more Records to Show";	
													}else{
														alert(response.message);

													}
														
														
													}


												})
												.error(function(data, status, header, config) {

												});
											
											
											



										}



									}
									
									
									

								</script>
								<script type="text/javascript">
									$('#datetimepicker6').datetimepicker({
										format: "YYYY-MM-DD",
										useCurrent: false //Important! See issue #1075
									});
									$('#datetimepicker7').datetimepicker({
										format: "YYYY-MM-DD",
										
									});






								</script>

	</div>
</div>

@stop
@extends('layouts.footer')