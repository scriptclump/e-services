@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - New Route'); ?>

<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/routingadmin" class="bread-color">Route</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><span class="bread-color">Create New Route</span></li>
		</ul>
	</div>
</div>

<div ng-app="plunker"  ng-controller="AccordionDemoCtrl" ng-init="data = <?php echo htmlspecialchars(json_encode($data)); ?>" ng-cloak>

	<div class="row">
		<div class="col-md-4 col-lg-4 head">
			<h4>Create New Route</h4>
		</div>
	</div>

	<div class="row" style="margin-left: 0px;">
		<div class="col-md-3">
		<!-- <h4 class="col-lg-3 head">Routing Integration</h4> -->
			<select style="border: 1px solid #ddd;" class="form-control btn green-meadow dcselect" id="dcvalue"  ng-model="dckey" ng-change="getHublist()">
				<option disabled selected value>----Select DC----</option>
				<option  ng-repeat="(key,value) in data.dc" value="<%key%>"><%value%></option>
			</select>
		</div>
		<div class="col-md-3" style="margin-left: -18px;">
			<select style="border: 1px solid #ddd;" class="form-control btn green-meadow dcselect" id="hubvalue" ng-model="hcKey" ng-change="getVehicles()" disabled>
				<option disabled selected value>----Select HUB----</option>
				<option  ng-repeat="(keyey,val) in hubdata" value="<%keyey%>"><%val%></option>
			</select>
		</div>
		<!-- <div class="col-md-4">
			<input type="submit" class="btn green-meadow submit" value="Submit" ng-click="getVehicles()">
		</div> -->
	</div>

	<p ng-hide="vehicles.length>0" class="text-center message">
		Please Select DC and Hub to show Orders & Routes
	</p>
	
	<div class="container-fluid" ng-show="vehicles[hub_id]">
		<div class="row" style="margin-left: -15px !important;margin-right: -15px !important;">
			<div class="col-md-3 sidenav">
				<div class="">
					<div class="list-group-item">
						
							<a href="#" ng-click="getOrders();show1=true;show=false;show2=false;show3=false;" class="list-group-item">Orders <span class="badge" ng-model="orders[hub_id].length"><%orders[hub_id].length%></span></a>
							<a href="#" ng-click="show=false;show1=false;show2=true;show3=false;" ng-show="assigned_orders[hub_id]" class="list-group-item">Assigned Orders <span class="badge" ng-show="assigned_orders[hub_id]" ng-model="assnArr[hub_id].length"><%assnArr[hub_id].length%></span></a>
							<a href="#" ng-click="show=false;show1=false;show2=false;show3=true;" ng-show="assigned_orders[hub_id]" class="list-group-item">Unassigned Orders <span class="badge" ng-model="unassigned_orders[hub_id].route_data.coordinates_data.length"><%unassigned_orders[hub_id].route_data.coordinates_data.length%></span></a>
							<a href="#" ng-click="show=true;show1=false;show2=false;show3=false;" ng-show="assigned_orders[hub_id]" class="list-group-item">Routes<span ng-show="assigned_orders[hub_id]" class="badge"><%vehicleinfo[hub_id].length + extraVehicleDetails.length%></span></a>
							<a href="generateViewMapAll?route_admin_id=<%route%>" ng-show="assigned_orders[hub_id]" class="list-group-item" target="_blank">All Routes</a>
							<div class="row buttonrow" style="margin-top: 20px;">
								<div class="col-md-6">
									<span>Time Per ESU</span>
								</div>
								<div class="col-md-6" style="width: 46% !important;">
									<input ng-init="timeperESU = 3" style="width: 100%;height: 35px;position: relative;padding-left: 10px;border: 1px solid #ddd;" type="number" ng-model="timeperESU">
								</div>
							</div>
							<div class="row buttonrow">
								<div class="col-md-6">
									<span>Orders Per Vehicle</span>
								</div>
								<div class="col-md-6" style="width: 46% !important;">
									<input  type="number" ng-init="orderperveh=30" style="width: 100%;height: 35px;position: relative;padding-left: 10px;border: 1px solid #ddd;" ng-model="orderperveh">
								</div>
							</div>
							<div class="row buttonrow">
							<div class="col-md-6">
								<!-- id="distributeorders" is for disabling button-->
								<input type="submit" class="btn green-meadow" value="Distribute Orders" ng-click="generateRouting(orders[hub_id], vehicles[hub_id],timeperESU,orderperveh)">
							</div>
							<div class="col-md-6">
							<!-- <input  type="submit" id="routeclr" class="btn green-meadow" value="Clear Routes" ng-click="clearRoutes()" disabled> -->
							</div>
						</div>
					</div>
					
					
					
				</div>
			</div>


			<div class="col-md-9"  style="overflow:scroll;height:400px; border: 1px solid lightgray;">
				<div class="loader" loading></div>
					<table  class="table table-striped" ng-show="show">
						<thead>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<col></col>
							<tr>
								<th>Route Name</th>
								<th>Vehicle </th>
								<th>Capacity</th>
								<th>#Orders</th>
								<th>#Crates</th>
								<th>#Bags</th>
								<th>#Cartons</th>
								<th>Distance (Km)*</th>
								<th>Time (Min)*</th>
								<th>Status</th>
								<th>Delivery Executive</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
								<p ng-hide="vehicleinfo[hub_id].length>0" ng-if="show" class="text-center message">
									Routes Will Be Displayed After Distributing Orders
								</p>
								<tr ng-repeat="(k,valu) in vehicleinfo[hub_id]" ng-model="vehicleinfo[hub_id]">
									<!-- <td><%k+1%></td> -->
									<td><%valu.route_name%></td>
									<td>
										<select class="form-control" ng-model="selectVehicle"
											ng-options="opt.vehicle_id as opt.vehicle_number for opt in vehicles[hub_id]" id="vehicleDrop<%valu.id%>" ng-show="editorEnabled[valu.id] || deliveryVehcle[valu.id]==''">
											 <option disabled selected value>----Select Vehicle----</option>
										</select>
										<div ng-show="editorEnabled[valu.id]">
											<a href="#" ng-click="save(valu.id,vehicles[hub_id])" title="Save"><i class="glyphicon glyphicon-saved" aria-hidden="true"></i></a>
							      								or
							      			<a href="#" ng-click="disableEditor(valu.id)" title="Cancel"><i class="fa fa-times" aria-hidden="true"></i></a>
							      		</div>
										<span  ng-model="deliveryVehcle[valu.id]" ng-hide="editorEnabled[valu.id]"><%deliveryVehcle[valu.id]%></span>
										<a href="#" ng-hide="editorEnabled[valu.id] || deliveryVehcle[valu.id]==''" title="Reassign"><i class="fa fa-pencil-square-o" aria-hidden="true"  ng-click="enableEditor(valu.id,valu.status)"></i></a>
									</td>
									<td style="text-align: right;"><%valu.vehicle_max_load%></td>
									<td style="text-align: right;"><%valu.orderCount%></td>
									<td style="text-align: right;"><span ng-hide="cratesLen[valu.id]>=0">--</span><span><%cratesLen[valu.id]%></span></td>
									<td style="text-align: right;"><span ng-hide="bagsLen[valu.id]>=0">--</span><span><%bagsLen[valu.id]%></span></td>
									<td style="text-align: right;"><span ng-hide="cfcLen[valu.id]>=0">--</span><span><%cfcLen[valu.id]%></span></td>
									<td style="text-align: right;"><span ng-model="apprxDist[valu.id]"><%apprxDist[valu.id] | number:2%></span></td>
									<td style="text-align: right;"><span  ng-model="apprxTime[valu.id]"><%apprxTime[valu.id] | number:2%></span></td>
									<td>
										<a  href="#" ng-click="updateroutedistanceTime(valu.id)" title="Refresh Approx Distance and Time.."><i class="fa fa-refresh" aria-hidden="true"></i></a>
									</td>
									<td>
										<select class="form-control" ng-model="delvselected"
											ng-options="option  for option in deliveryExec" id="delvDrop<%valu.id%>" ng-show="editorDelv[valu.id] || deliveryExname[valu.id]==''">
											<option disabled selected value>----Select DeliveryExecutive----</option>
										</select>
										<div ng-show="editorDelv[valu.id]">
											<a href="#" ng-click="saveDelv(valu.id,deliveryExec)" title="Save"><i class="glyphicon glyphicon-saved" aria-hidden="true"></i></a>
								      									or
								      		<a href="#" ng-click="disableDelv(valu.id)" title="Cancel"><i class="fa fa-times" aria-hidden="true"></i></a>
								      	</div>
										<span  ng-model="deliveryExname[valu.id]" ng-hide="editorDelv[valu.id]"><%deliveryExname[valu.id]%></span>
										<a href="#"  title="Reassign"  ng-hide="editorDelv[valu.id] || deliveryExname[valu.id]==''">		<i class="fa fa-pencil-square-o" aria-hidden="true" ng-click="enableDelv(valu.id,valu.status)"></i></a>
									</td>

									<td style="width: 75px !important;position: absolute;">
										<a toggle="View-Map" 
										href="generateviewmaponrouteid?route_id=<%valu.id%>"  target="_blank">
										<span class="fa fa-map-marker" title="ViewMap"></span>
										</a>
										<a  toggle="Download-Route-Sheet" 
										href="generateloadsheetonrouteid?route_id=<%valu.id%>"  target="_blank">
										<span class="fa fa-download" title="LoadSheet"></span>
										</a>
										<i class="fa fa-bookmark-o" style="float: right;color: #0088cc;font-weight: bold;font-size: 16px;" aria-hidden="true" title="Assign" ng-click="assignDeliverExec(valu.id,vehicles[hub_id],deliveryExec)"></i>
									</td>
								</tr>
						</tbody>
					</table>
			<!-- Trigger the modal with a button -->

			<!-- Modal -->

			<div class="table table-striped" ng-show="show1">
				<div ui-grid-pagination ui-grid="gridOptions" ng-mouseover="ngGridFIx()" ui-grid-selection ui-grid-exporter class="myGrid"></div>
			</div>
			<div class="table table-striped" ng-show="show2">
				<div ui-grid-pagination ui-grid="gridOptionsAssigned" ng-mouseover="ngGridFIx()" ui-grid-exporter class="myGrid"></div>
			</div>
			<table class="table table-striped" ng-show="show3">
				<thead>
					<tr>
						<th>Order Id </th>
						<th>No Of Crates</th>
						<th>Order Code</th>
						<th>Volume</th>
						<th>Beat</th>
						<th>Re-Assign</th>
						<th></th>
					</tr>

				</thead>
				<tbody>
					<tr ng-repeat="(unaskey,unasval) in unassigned_orders[hub_id].route_data.coordinates_data" ng-model="unassigned_orders[hub_id].route_data.coordinates_data">
						<td>
							<%unasval.coordinates.gds_order_id%>
						</td>
						<td>
							<%unasval.coordinates.crates_info.crates_count%>
						</td>
						<td>
							<%unasval.coordinates.order_code%>
						</td>
						<td>
							<%unasval.coordinates.weight%>
						</td>
						<td>
							<%unasval.coordinates.beat%>
						</td>
						<td>
							<select class="form-control" ng-model="selectInassignTo">
								<option value="">---- select route ----</option>
								<option ng-repeat="(k, value) in vehicleinfo[hub_id]" value="<%value.id%>"><%value.route_name%></option>
							</select>
						</td>
						<td>
							<i class="fa fa-bookmark-o" style="float: right;color: #0088cc;font-weight: bold;font-size: 16px;" aria-hidden="true" title="Assign" ng-click="reassignVehicle(selectInassignTo,unasval.coordinates.gds_order_id,unassigned_orders[hub_id].id,unassigned_orders[hub_id].hub_id,unassigned_orders[hub_id].route_id)"></i>
						</td>

					</tr>
				</tbody>
			</table>
			<p ng-if="status[hub_id] && show1" class="text-center">
				<%status[hub_id]%>
			</p>
		</div>
	</div>
</div>
</div>
@stop

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<style type="text/css" media="screen">

i.fa.fa-refresh {
    font-size: 20px;
    padding-left: 15px;
    margin-top: 5px;
    font-weight: 500;
}
.fa.fa-map-marker{
	font-size: 20px;
}
.fa.fa-download{
	font-size: 20px;margin-left:5px;
}


.ui-grid-menu {
	width: 225px !important;
}
a:active, a:hover {
    outline: 0;
    background: grey;
}
.ui-grid-menu-button {
    z-index: 2;
    position: absolute;
    right: 0;
    top: 0;
    background: #f3f3f3;
    border: 1px solid #d4d4d4;
    cursor: pointer;
    height: 23px !important;
    width: 19px !important;
    font-weight: normal;
}
.testingClass{
	text-align: right !important;
}
.myGrid {
	width: 103%;
    height: 409px;
    margin-left: -15px;
}
.ui-grid-pager-control button {
    height: 10px !important;
}
.ui-grid-pager-control input {
    height: 12px !important;
}
.ui-grid-pager-control .ui-grid-pager-max-pages-number {
    vertical-align: inherit !important;
}
.ui-grid-pager-row-count-picker select {
    height: 12px !important;
}
	th {
    white-space: nowrap;
}
	.page-container {
		background: #f1f1f1;
	}
	.list-group-item {
		position: relative;
		display: block;
		padding: 10px 15px;
		margin-bottom: -1px;
		background-color: #fff;
		border: 1px solid #ddd;
		border-radius: 0px !important;
	}

	
	.modal{
		width : auto!important;
		background-color: transparent!important;
		left: 15%!important;
		top:50px!important;
	}
	.message{
		position: relative;
		top:170px;
	}
	.head {
		margin-left: 18px!important;
		margin-top: 15px;
	}
	.buttonrow{
		position : relative;
		left : 10px!important;
		margin-bottom: 10px;
	}
	
	.tabinfos {
		width: 110% !important;
    	position: relative;
    	left: -14px;
	}

	.innertab {
		border: 1px solid black;
		border-collapse: collapse;
	}
	.vehicle {
		width: 86%;
	}
	.collapse {
		display: block;
	}
	.dcselect {
		margin-top: 12px;
	    margin-bottom: 10px !important;
	    height: 40px !important;
	    width: 100% !important;
	    border-radius: 2px!important;
	    background-color: transparent!important;
	    color: #333333!important;
	}
	.delvselect{

		margin-top: 12px;
		height: 40px!important;
		width: 330px!important;
		border-radius: 10px!important;
		margin-left: 100px!important;
		margin-bottom: 10px!important;


	}
	.submit {
		margin-top: 10px;
		border-radius: 2px!important;
		height: 40px !important;
		width: 200px !important;
	}
	.accordion {
		margin-top: 20px!important;
	}
	
	.sidenav {
		position: relative;
		/*background-color: #f1f1f1;*/
		height: 400px;
	}
	
	[ng\:cloak],
	[ng-cloak],
	[data-ng-cloak],
	[x-ng-cloak],
	.ng-cloak,
	.x-ng-cloak {
		display: none !important;
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
		z-index : 9999999;
	}

	@-webkit-keyframes spin {
		0% { -webkit-transform: rotate(0deg); }
		100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
	@media screen and (max-width: 767px) {
		.sidenav {
			height: 700px;
			padding: 15px;
		}
		.row.content {
			height: 300px;
		}
	}
	@media only screen and (min-width: 1400px) {
	.tabinfos {
		width: 103% !important;
	    position: relative;
	    left: -14px;
	}
	
	

</style>
@stop
@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.2.0.js"></script>
<script src="https://rawgit.com/allenhwkim/angularjs-google-maps/master/build/scripts/ng-map.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sweetalert/1.1.2/SweetAlert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<!-- <link href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet"> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>


<script  type="text/javascript">
	var app = angular.module('plunker', ['ui.bootstrap','ngMap','oitozero.ngSweetAlert','ui.grid','ui.grid.pagination', 'ui.grid.exporter','ui.grid.selection'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	
	app.controller("AccordionDemoCtrl",function($scope,$http,$rootScope,$filter,$window,SweetAlert) { 

		// ui-grid section
	$rootScope.selectedGridData;

	$scope.gridOptionsAssigned = {
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
			columnDefs: [
				{ field: 'Order Code', width: '140', filter: {
		          condition:  function(searchTerm, cellValue) {
		      		var separators = ['-', '/', ':', ';', ','];
		              var strippedValue = searchTerm.split(new RegExp(separators.join('|'), 'g'));
		              var bReturnValue = false;
		              for(iIndex in strippedValue){
		            		var sValueToTest = strippedValue[iIndex];
		            		sValueToTest = sValueToTest.replace(" ", "");
		           		if (cellValue.toLowerCase().indexOf(sValueToTest.toLowerCase()) >= 0)
		              		bReturnValue = true;
		              }
		            
		            return bReturnValue;
		          }
		        }, headerCellClass: $scope.highlightFilteredHeader },
				{ field: '#Crates', width: '80', cellClass: 'testingClass' },
				{ field: '#Cartons', width: '85', cellClass: 'testingClass' },
				{ field: '#Bags', width: '65', cellClass: 'testingClass' },
				{ field: 'Retailer Name', width: '150'},
				{ field: 'Beat', width: '130' },
				{ field: 'Address', width: '500' },
				{ field: 'Volume', width: '70' , cellClass: 'testingClass'},
				{ field: 'Invoice Amt', width: '120' , cellClass: 'testingClass'}
			],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
			}
		};

        $scope.gridOptions = {
			enableFiltering: true,
			enableRowSelection: true,
    		enableSelectAll: true,
			paginationPageSizes: [5, 10, 20, 30, 40],  
        	paginationPageSize: 10,
        	enableGridMenu: true,
     		exporterMenuCsv: true,
    		exporterPdfDefaultStyle: {fontSize: 8},
     		exporterPdfTableStyle: {margin: [0, 0, 0, 0]},
    		exporterPdfTableHeaderStyle: {bold: true, italics: true},
    		exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 590,
			columnDefs: [
				{ field: 'Order Code', width: '140', filter: {
		          condition:  function(searchTerm, cellValue) {
		      		var separators = ['-', '/', ':', ';', ','];
		              var strippedValue = searchTerm.split(new RegExp(separators.join('|'), 'g'));
		              var bReturnValue = false;
		              for(iIndex in strippedValue){
		            		var sValueToTest = strippedValue[iIndex];
		            		sValueToTest = sValueToTest.replace(" ", "");
		           		if (cellValue.toLowerCase().indexOf(sValueToTest.toLowerCase()) >= 0)
		              		bReturnValue = true;
		              }
		            return bReturnValue;
		          }
		        }, headerCellClass: $scope.highlightFilteredHeader },
				{ field: '#Crates', width: '80', cellClass: 'testingClass' },
				{ field: '#Cartons', width: '85', cellClass: 'testingClass' },
				{ field: '#Bags', width: '65', cellClass: 'testingClass' },
				{ field: 'Retailer Name', width: '150'},
				{ field: 'Beat', width: '130' },
				{ field: 'Address', width: '500' },
				{ field: 'Volume', width: '70' , cellClass: 'testingClass'},
				{ field: 'Invoice Amt', width: '120', cellClass: 'testingClass' }
			],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
				gridApi.selection.on.rowSelectionChanged($scope,function(rows){
		            $scope.mySelections = gridApi.selection.getSelectedRows();
		            $rootScope.selectedGridData = $scope.mySelections;
		        });
			}
		};

		$scope.ngGridFIx = function() {
			window.dispatchEvent(new Event('resize'));
		}

		$scope.enableEditor = function(Route,Status) {
			if(Status==3){
				alert("Not Possible to Reassign Now!!!");
			}else{
				$scope.editorEnabled[Route] = true;
			}
		};
		$scope.enableDelv = function(Route,Status) {
			if(Status==3){
				alert("Not Possible to Reassign Now!!!");

			}else{
				$scope.editorDelv[Route] = true;
			}
		};
		$scope.disableEditor = function(route_id) {
		    $scope.editorEnabled[route_id] = false;
		  };
		  $scope.disableDelv = function(route_id) {
		    $scope.editorDelv[route_id] = false;
		  };
		$scope.save = function(route,vehicleData){
			var afterColVeh = angular.element(document.querySelector('#vehicleDrop'+route)).val();
			var vehicle_id = afterColVeh.substr(afterColVeh.indexOf(":") + 1);
			if(vehicle_id==""){
				alert("Please Select Vehicle to Reassign!!");
			}else{
				angular.forEach(vehicleData,function(val,key){
					if(val.vehicle_id == vehicle_id){
						$scope.vehicle_number = val.vehicle_number;
					}
				});
				var data = {'route_id':route,
				"vehicle_id":vehicle_id,
				"vehicle_number":$scope.vehicle_number};
				$http.post("/routingadmin/setdeorvehicleroute",data)
				.success(function(data,status,headers,config){
				if(data.status==true){
					$scope.editorEnabled[route] = false;
					if(!$scope.deliveryVehcle){
						$scope.deliveryVehcle = [];
					}
				if(data.message.vehicle_set.message){
					$scope.deliveryVehcle[route] = data.message.vehicle_set.message;
				}
				}else{
					if((data.message.hasOwnProperty("de_set")) && !data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.de_set.message);
					}else if(!data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.vehicle_set.message);
					}else if(data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.vehicle_set.message+""+""+data.message.de_set.message);
					}else{
						alert(data.message);
					}
				}
			})
			.error(function(data,status,headers,config){

			});
			}
		}
		$scope.saveDelv = function(route,delvData){
			var afterColDel = angular.element(document.querySelector('#delvDrop'+route)).val();

			var delvName = afterColDel.substr(afterColDel.indexOf(":") + 1);
			if(delvName==""){
				alert("Please Select Delivery Executive to Reassign!!");
			}else{
				angular.forEach(delvData,function(value,key){
					if(value == delvName){
						$scope.delId = key;
					}
				});
				var data = {'de_id':$scope.delId,'de_name':delvName,'route_id':route};
				$http.post("/routingadmin/setdeorvehicleroute",data)
			.success(function(data,status,headers,config){
				if(data.status==true){	
					$scope.editorDelv[route] = false;
					if(!$scope.deliveryExname){
						$scope.deliveryExname = [];
					}
					if(data.message.de_set.message){
						$scope.deliveryExname[route] = data.message.de_set.message;
					}	
				}else{
					if((data.message.hasOwnProperty("de_set")) && !data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.de_set.message);
					}else if(!data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.vehicle_set.message);
					}else if(data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
						alert(data.message.vehicle_set.message+""+""+data.message.de_set.message);
					}else{
						alert(data.message);
					}
				}
			})
			.error(function(data,status,headers,config){

			});
			}
		}
		$scope.getHublist = function() {
			var dc = angular.element(document.querySelector('#dcvalue')).val();
			$scope.init = '<?php echo json_encode($data); ?>';
			$scope.init = JSON.parse($scope.init);
			console.log(dc);
			console.log('**',$scope.init);
			console.log('**',$scope.init.hub);
			$scope.initdata = angular.fromJson($scope.init);
			$('#hubvalue').prop('disabled',false);
			$scope.hubdata = [];
			angular.forEach($scope.initdata.hub, function(dcval, dckey) {
				if (dckey === dc) {
					$scope.hubdata = dcval;	
				}
			});
		}
		$scope.getVehicles = function(){
			var dc = angular.element(document.querySelector('#dcvalue')).val();
			var hub = angular.element(document.querySelector('#hubvalue')).val();
			$scope.hub_id = hub;
			$scope.initialdata = '<?php echo json_encode($data); ?>';
			$scope.initialdata = angular.fromJson($scope.initialdata);
			angular.forEach($scope.initialdata.vehicles, function(vehcleval, vehclekey) {
				if (vehclekey == hub ) {
					$scope.vehicles = [];
					if(vehcleval.length>0){
						$scope.vehicles[hub] = vehcleval;
						$scope.show = true;
						$scope.show1 = false;
						$scope.show2 = false;
						$scope.show3 = false;	
					}else{
						SweetAlert.swal({
							title : 'error',
							text : "No Vehicles For the Hub!!!",
							type : 'error'
						});
					}
				}
			});
			$scope.delvData = {'hub_id':hub};
			$http.post('/routingadmin/getDeliveryExecutiveList', $scope.delvData)
			.success(function(data, status, headers, config) {
				if(data.status==true){
					$scope.deliveryExec = data.message;
				}
			})
			.error(function(data, status, header, config) {

			});
		}
		$scope.getOrders = function() {
			var key = angular.element(document.querySelector('#hubvalue')).val();
			$scope.hubinfo = {
				"hub_id": key
			};
			$scope.users = [];
			$http.post('/routingadmin/getordersbyhub', $scope.hubinfo)
			.success(function(data, status, headers, config) {
				if(!$scope.orders){
					$scope.orders = [];
				}
				if(!$scope.status){
					$scope.status = [];
				}
				if (data.status == true) {
					$scope.orders[key] = data.message;
					angular.forEach($scope.orders[key], function(orderValues, orderKeys){
						var invoice_amount_fil_Order = $filter('currency')(orderValues.invoice_amount,'', 2);
						$scope.users.push(
						{'Order_Id' : orderValues.gds_order_id, 'Order Code' : orderValues.order_code, '#Crates': orderValues.crates_info.crates_count, '#Cartons': orderValues.other_info[0].cfc_count, '#Bags': orderValues.other_info[0].bag_count, 'Retailer Name': orderValues.shop_name, 'Beat': orderValues.beat, 'Address': orderValues.address_info.address, 'Volume': orderValues.weight,'Invoice Amt' :invoice_amount_fil_Order, 'orderData': orderValues
						});
						$scope.gridOptions.data = $scope.users;
					});
				}else{
					$scope.status[key] = data.message;
					$scope.orders[key] = [];
				}
			})
			.error(function(data, status, header, config) {

			});
		}
		$scope.generateRouting = function( ordersData, vehicles, timeValue, orderVehValue) {
			
			if(!$rootScope.selectedGridData){
				var orders = ordersData;
			}else{
				var tempData = [];
				angular.forEach($rootScope.selectedGridData, function(selectedValues, selectedKeys){
					tempData.push(selectedValues.orderData);
				});
				var orders = tempData;
			}
			var hub = angular.element(document.querySelector('#hubvalue')).val();
			if (!orders) {
				SweetAlert.swal({
					title : 'error',
					text : "Get Orders first! Click on orders tab!",
					type: "error"
				})
			} else {
				$scope.gendata = {
					'hub_id': hub,
					'order_data': orders,
					'vehicles_details': vehicles,
					'esu_time' : timeValue,
					'order_count' : orderVehValue
				};
				$http.post('/routingadmin/generateroutes', $scope.gendata)
				.success(function(data, status, headers, config) {
                        if(data.status==true){
                        	$("#distributeorders").prop("disabled",true);
                        	$("#routeclr").prop("disabled",false);
                        	if(!$scope.cratesLen){
                        		$scope.cratesLen = [];
                        	}
                        	if(!$scope.bagsLen){
		                		$scope.bagsLen = [];
		                	}
		                	if(!$scope.cfcLen){
		                		$scope.cfcLen = [];
		                	}
                        	if(!$scope.assigned_orders){
                        		$scope.assigned_orders = [];
                        	}
                        	if(!$scope.unassigned_orders){
                        		$scope.unassigned_orders = [];
                        	}
                        	if(!$scope.hub_coordinates){
                        		$scope.hub_coordinates = [];
                        	}
                        	if(!$scope.assnArr){
                        		$scope.assnArr = [];
                        	}
                        	if(!$scope.vehicleinfo){
                        		$scope.vehicleinfo = [];
                        	}
                    		if(!$scope.apprxTime){
					   			$scope.apprxTime = [];
				   			}
				   			if(!$scope.apprxDist){
					   			$scope.apprxDist = [];
				   			}
				   			if(!$scope.editorEnabled){
					   			$scope.editorEnabled = [];
				   			}
                        	if(!$scope.editorDelv){
					   			$scope.editorDelv = [];
				   			}
				   			if(!$scope.deliveryExname){
                        		$scope.deliveryExname = [];
                        	}
                        	if(!$scope.deliveryVehcle){
                        		$scope.deliveryVehcle = [];
                        	}
                        	$scope.extraVehicleDetails = data.message.assigned_coordinates.extra_vehicles;
                        	$scope.assigned_orders[hub] = data.message.assigned_coordinates;
                        	$scope.unassigned_orders[hub] = data.message.unassigned_coordinates;
                        	$scope.hub_coordinates[hub] = data.message.hub_coordinates;
                        	$scope.assnArr[hub] = [];
                        	$scope.vehicleinfo[hub] = [];
                        	$scope.userData = [];
                        	$scope.route = $scope.assigned_orders[hub][0].route_id;
                        	if($scope.assigned_orders[hub]){
                        		angular.forEach($scope.assigned_orders[hub],function(coval,cokey){
                        			//console.log(coval);
                        			if(cokey!=="extra_vehicles"){
                        				$scope.cratesLen[coval.id] = 0;
                        				$scope.bagsLen[coval.id] = 0;
                						$scope.cfcLen[coval.id] = 0;
                        				$scope.vehicleinfo[hub].push(coval.route_data.vehicleInfo);
                        				$scope.vehicleinfo[hub][cokey]["id"] = coval.id;
                        				$scope.vehicleinfo[hub][cokey]["route_id"] = coval.route_id;
                        				$scope.vehicleinfo[hub][cokey]["route_name"] = coval.vehicle_number_generated;
                        				$scope.deliveryVehcle[coval.id] =  coval.vehicle_code;
                        				$scope.deliveryExname[coval.id]  = coval.delivery_executive_name;
	                        			$scope.vehicleinfo[hub][cokey]["status"] = coval.status;
                        				$scope.apprxDist[coval.id] = parseInt(coval.estimated_distance)/1000;
	                        			$scope.apprxTime[coval.id] = parseInt(coval.estimated_time)/60;
	                        			$scope.editorEnabled[coval.id] = false;
	                        			$scope.editorDelv[coval.id] = false;
                        				angular.forEach(coval.route_data.coordinates_data,function(value,keys){
                        					//console.log(value.coordinates);
                        					var invoice_amount_fil = $filter('currency')(value.coordinates.invoice_amount,'', 2);
			                				$scope.userData.push(
											{'Order_Id' : value.coordinates.gds_order_id, 'Order Code' : value.coordinates.order_code, '#Crates': value.coordinates.crates_info.crates_count, '#Cartons': value.coordinates.other_info[0].cfc_count, '#Bags': value.coordinates.other_info[0].bag_count, 'Retailer Name': value.coordinates.shop_name, 'Beat': value.coordinates.beat, 'Address': value.coordinates.address_info.address, 'Volume': value.coordinates.weight, 'Invoice Amt': invoice_amount_fil
											});
											$scope.gridOptionsAssigned.data = $scope.userData;
                        					$scope.assnArr[hub].push(value.coordinates);
                        					$scope.cratesLen[coval.id] +=  parseInt(value.coordinates.crates_info.crates_count);
                        					$scope.bagsLen[coval.id] += parseInt(value.coordinates.other_info[0].bag_count);
                							$scope.cfcLen[coval.id] += parseInt(value.coordinates.other_info[0].cfc_count);
										});
                        			}
								});
                        	}
                        }else{
                        	alert(data.message);
                        }
                })
				.error(function(data, status, header, config) {

				});
			}
		}
		$scope.clearRoutes = function(){
			var hubid = angular.element(document.querySelector('#hubvalue')).val();
			var hubdata = {"hub_id":hubid};
			$http.post("/routingadmin/clearroutes",hubdata)
			.success(function(data,status,headers,config){
				if(data.status==true){
					$("#distributeorders").prop("disabled",false);

				}

			})
			.error(function(data,status,headers,config){

			});

		}

		$scope.reassignVehicle = function(fromId,fromOrderCode,toId,hubIdValue,routeIdValue){
			$scope.unassignToAssign = {
				unassigned_order : fromOrderCode,
				to_route : fromId,
				unassign_route_id : parseInt(toId)
			};
			var res = $http.post('/routingadmin/moveunassignedtoroute', $scope.unassignToAssign);
				res.success(function(data, status, headers, config) {
					$scope.message = data;
					$window.location.href = '/routingadmin/viewroutehistory?route_id='+ routeIdValue + '&hub_id=' +hubIdValue;
					/*$location.path('/routingadmin/viewroutehistory?route_id='+ routeIdValue + '&hub_id=' +hubIdValue); */
					//window.location.reload();
				});
				res.error(function(data, status, headers, config) {
					alert( "failure message: " + JSON.stringify({data: data}));
				});
		}

		$scope.assignDeliverExec = function(route,vehclData,delvData){

		var afterColDel = angular.element(document.querySelector('#delvDrop'+route)).val();
		var afterColVeh = angular.element(document.querySelector('#vehicleDrop'+route)).val();
		var delvName = afterColDel.substr(afterColDel.indexOf(":") + 1);
		var vehicle_id = afterColVeh.substr(afterColVeh.indexOf(":") + 1);

		if(vehicle_id!="?" && delvName!="?"){
			angular.forEach(vehclData,function(val,key){
			if(val.vehicle_id == vehicle_id){
				$scope.vehicle_number = val.vehicle_number;
			}
		});
			angular.forEach(delvData,function(value,key){
			if(value == delvName){
				$scope.delId = key;
			}
		});
			var data = {'de_id':$scope.delId,'de_name':delvName,'route_id':route,
		"vehicle_id":vehicle_id,
		"vehicle_number":$scope.vehicle_number};
		}else if(vehicle_id != "?" && delvName == "?"){
			angular.forEach(vehclData,function(val,key){
			if(val.vehicle_id == vehicle_id){
				$scope.vehicle_number = val.vehicle_number;
			}
		});
			var data = {'route_id':route,
		"vehicle_id":vehicle_id,
		"vehicle_number":$scope.vehicle_number};
		}else if(vehicle_id == "?" && delvName != "?"){
			angular.forEach(delvData,function(value,key){
			if(value == delvName){
				$scope.delId = key;
			}
		});
			var data = {'de_id':$scope.delId,'de_name':delvName,'route_id':route};

		}else if(vehicle_id=="?" && delvName=="?"){
			alert("Select Either Vehicle or Delivery Executive to Assign!!!");return;
		}

		$http.post("/routingadmin/setdeorvehicleroute",data)
		.success(function(data,status,headers,config){
			if(data.status==true){
				if(!$scope.deliveryExname){
					$scope.deliveryExname = [];
				}
				if(!$scope.deliveryVehcle){
					$scope.deliveryVehcle = [];
				}

				if(data.message.de_set.message){
					$scope.deliveryExname[route] = data.message.de_set.message;

				}
				if(data.message.vehicle_set.message){
					$scope.deliveryVehcle[route] = data.message.vehicle_set.message;

				}
				
			}else{
				if((data.message.hasOwnProperty("de_set")) && !data.message.hasOwnProperty("vehicle_set")){
					alert(data.message.de_set.message);
				}else if(!data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
					alert(data.message.vehicle_set.message);
				}else if(data.message.hasOwnProperty("de_set") && data.message.hasOwnProperty("vehicle_set")){
					alert(data.message.vehicle_set.message+""+""+data.message.de_set.message);
				}else{
					alert(data.message);
				}
				
			}

		})
		.error(function(data,status,headers,config){

		});




	}

	$scope.updateroutedistanceTime = function(RouteId){
			$scope.route_data = {"route_id":RouteId};

			$http.post('/routingadmin/updateroutedistanceTime', $scope.route_data)
			.success(function(data, status, headers, config) {
				if(data.status==true){
					

					/*$scope.apprxDist[RouteId] = ;
					$scope.apprxTime[RouteId] = parseInt(data.message.time)/60;*/
					$rootScope.$broadcast("CallCartMethod",data.message,RouteId);
					
				}
				

			})
			.error(function(data, status, header, config) {

			});

		}
		$rootScope.$on("CallCartMethod", function(event,response,routeId){
   			

   			$scope.apprxTime[routeId] = parseInt(response.time)/60;
   			$scope.apprxDist[routeId] = parseInt(response.distance)/1000;
   			
		});

});
app.directive('loading',   ['$http' ,function ($http)
{
	return {
		restrict: 'A',
		link: function (scope, elm, attrs)
		{
			scope.isLoading = function () {
				return $http.pendingRequests.length > 0;
			};

			scope.$watch(scope.isLoading, function (v)
			{
				if(v){
					elm.css('display', 'block');
				}else{
					elm.css('display', 'none');
				}
			});
		}
	};

}]);
</script>
@stop
@extends('layouts.footer')