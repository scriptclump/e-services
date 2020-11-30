@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Route History'); ?>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.12.0/semantic.min.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/css/ui-grid.css') }}">
<style type="text/css" media="screen">

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

.fa.fa-download.loadicon {
    font-size: 20px;
}

i.fa.fa-bookmark-o {
    color: #0088cc;
    
    font-size: 20px;
    padding-left: 6px;
    font-weight: 500;
}
.fa.fa-map-marker {
    font-size: 20px;
    margin-top: 5px;
    font-weight: 500;
}
i.fa.fa-refresh {
    font-size: 20px;
    padding-left: 15px;
    margin-top: 5px;
    font-weight: 500;
}
.fa.fa-file-excel-o {
        font-size: 24px;
    /* padding-left: 5px; */
    color: #12631c;
    font-weight: 100;
    float: right;
    margin-top: -18px;
    margin-right: 15px;
}
	th {
		white-space: nowrap;
	}
	td{
		font-size: 12px;
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
		margin-left: 40px!important;
		margin-top: 20px;
	}
	.buttonrow{
		position : relative;
		left : 10px!important;
		margin-bottom: 10px;
	}

	.tabinfos {
		width: 100% !important;
		position : relative;
		/*left : -21px;*/

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
		margin-bottom: 10px!important;
		height: 40px!important;
		width: 330px!important;
		border-radius: 10px!important;
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
		border-radius: 10px!important;
		height: 39px!important;
		width: 100px!important;
		margin-left: 10px!important;
		margin-bottom: 10px!important;
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

</style>

<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/routingadmin" class="bread-color">Route</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><span class="bread-color">Route History View</span></li>
		</ul>
	</div>
</div>

<div ng-app="plunker"  ng-controller="AccordionDemoCtrl"  class="portlet-body ng-cloak">

	<div class="row" style="margin-left: 15px;height: 50px;">
		<div class="col-md-3 col-sm-3" style="margin-top: 5px;">
			<h4>Route History View</h4>
		</div>
		<div class="col-md-9 col-sm-9" style="margin-top: 15px;">
			<div style="padding-left: 25%;">
				<span style="font-size: 17px;font-weight: bold;">Route Admin Id : <%sendData.route_id%>, </span><span style="font-size: 17px;font-weight: bold;">Date : </span><span  style="font-size: 17px;font-weight: bold;" ng-bind="formatDate(historyCreatedDate) |  date:'MM/dd/yyyy'"></span><span style="float: right;padding-right: 55px;
    			font-size: 14px;font-weight: 600;padding-top: 4px;">* Approx.</span> 
    			<button ng-show="show2" style="border: 1px solid #ddd;background: white;    height: 30px;margin-left: 25%;" ng-click="reAssignToUnAssigned()">Unassign</button>
			</div>
			
			<a toggle="download-Excel" href="downloadtripsheet?route_admin_id=<%sendData.route_id%>" target="_blank" title="Download-Excel">
				<span class="fa fa-file-excel-o"></span>
			</a>
		</div>
	</div>

	<div class="container-fluid" >
		<div class="row">
			<div class="col-md-3 sidenav">
				<div class="">
					<div class="list-group-item">
						<a href="#" ng-click="show1=true;show=false;show2=false;show3=false;" class="list-group-item">Orders <span class="badge" ><%assnArr.length+unassigned_orders.length%></span></a>
						<a href="#" ng-click="show=false;show1=false;show2=true;show3=false;" ng-show="assigned_orders" class="list-group-item">Assigned Orders <span class="badge" ng-show="assigned_orders" ng-model="assnArr.length"><%assnArr.length%></span></a>
						<a href="#" ng-click="show=false;show1=false;show2=false;show3=true;" ng-show="assigned_orders" class="list-group-item">Unassigned Orders <span class="badge" ng-model="unassigned_orders.length"><%unassigned_orders.length%></span></a>
						<a href="#" ng-click="show=true;show1=false;show2=false;show3=false;" class="list-group-item">Routes<span class="badge"><%vehicleinfo.length%></span></a>
						<a href="generateViewMapAll?route_admin_id=<%route%>" class="list-group-item" target="_blank">All Routes</a>
					</div>
				</div>
			</div>
			<div class="col-md-9"  style="overflow:scroll;height:430px; border: 1px solid lightgray;">
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
								<th>Route Id</th>
								<th >Route Name </th>
								<th>Vehicle</th>
								<th >Capacity</th>
								<th>#Orders</th>
								<th>#Crates</th>
								<th>#Bags</th>
								<th>#Cartons</th>
								<th>Distance<br>(Km)*</th>
								<th>Time<br>(Min)*</th>
								<th>Status</th>
								<th>Delivery Executive</th>
								<th style="position: absolute;width: 80px;height: 57px;background: #fbfcfd;
    border-bottom: 2px solid #e7ecf1">Action</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="(k,valu) in vehicleinfo" ng-model="vehicleinfo">
								<td><%k+1%></td>
								<td><%valu.route_name%></td>
								<td>

									<select class="form-control" ng-model="selectVehicle"
									ng-options="opt.vehicle_id as opt.vehicle_number for opt in vehicles" id="vehicleDrop<%valu.id%>"  ng-show="editorEnabled[valu.id] || deliveryVehcle[valu.id]==''">
									<option disabled selected value>----Select Vehicle----</option>
								</select>
								<div ng-show="editorEnabled[valu.id]">
									<a href="#" ng-click="save(valu.id,vehicles)" title="Save"><i class="glyphicon glyphicon-saved" aria-hidden="true"></i></a>
									or
									<a href="#" ng-click="disableEditor(valu.id)" title="Cancel"><i class="fa fa-times" aria-hidden="true"></i></a>
								</div>
								<span  ng-model="deliveryVehcle[valu.id]" ng-hide="editorEnabled[valu.id]"><%deliveryVehcle[valu.id]%></span>
								<a href="#" ng-hide="editorEnabled[valu.id] || deliveryVehcle[valu.id]==''" title="Reassign"><i class="fa fa-pencil-square-o" aria-hidden="true"  ng-click="enableEditor(valu.id,valu.status)"></i></a></td>
								<td style="text-align: right;"><%valu.vehicle_max_load%></td>
								<td style="text-align: right;"><%valu.orderCount%></td>
								<td style="text-align: right;"><span ng-hide="cratesLen[valu.id]>=0">--</span><span><%cratesLen[valu.id]%></span></td>
								<td style="text-align: right;"><span ng-hide="bagsLen[valu.id]>=0">--</span><span><%bagsLen[valu.id]%></span></td>
								<td style="text-align: right;"><span ng-hide="cfcLen[valu.id]>=0">--</span><span><%cfcLen[valu.id]%></span></td>
								<td style="text-align: right;"><span ng-model="apprxDist[valu.id]"><%apprxDist[valu.id] | number:2%></span></td>
								<td style="text-align: right;"><span  ng-model="apprxTime[valu.id]"><%apprxTime[valu.id] | number:2%></span></td>
								<td><a  href="#" ng-click="updateroutedistanceTime(valu.id)" title="Refresh Approx Distance and Time.."><i class="fa fa-refresh" aria-hidden="true"></i></a></td>
								<td>

									<select  class="form-control" ng-model="delvselected"
									ng-options="option  for option in deliveryExec" id="delvDrop<%valu.id%>" ng-show="editorDelv[valu.id] || deliveryExname[valu.id]==''">
									<option disabled selected value>----Select DeliveryExecutive----</option>
								</select>
								<div ng-show="editorDelv[valu.id]">
									<a href="#" ng-click="saveDelv(valu.id,deliveryExec)"><i class="glyphicon glyphicon-saved" aria-hidden="true" title="Save"></i></a>
									or
									<a href="#" ng-click="disableDelv(valu.id)" title="Cancel"><i class="fa fa-times" aria-hidden="true"></i></a>
								</div>
								<span  ng-model="deliveryExname[valu.id]" ng-hide="editorDelv[valu.id]"><%deliveryExname[valu.id]%></span>
								<a href="#"  title="Reassign"  ng-hide="editorDelv[valu.id] || deliveryExname[valu.id]==''"><i class="fa fa-pencil-square-o" aria-hidden="true" ng-click="enableDelv(valu.id,valu.status)"></i></a>
							</td>
							<td style="position: absolute;width: 80px;height: 50px;background: #fbfcfd;">
								<a toggle="View-Map" 
								href="generateviewmaponrouteid?route_id=<%valu.id%>"  target="_blank" title="View-Map">
								<span class="fa fa-map-marker" ></span>
							</a>
							<a  toggle="Download-Route-Sheet" 
							href="generateloadsheetonrouteid?route_id=<%valu.id%>"  target="_blank" title="LoadSheet">
								<span class="fa fa-download loadicon" style="margin-left: 2px!important;"></span>
							</a>
							<i class="fa fa-bookmark-o" aria-hidden="true" title="Assign" ng-click="assignDeliverExec(valu.id,vehicles,deliveryExec)"></i>
							
							
						<div class="modal fade" id="bsModal3" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title text-center" id="mySmallModalLabel">Orders Not In Hub</h4>
									</div>
									<div class="modal-body">
									<table  class="table table-striped" ng-show="show">
									<thead>
									<tr>
									<th><input type="checkbox" value="" ng-click="checkAllBoxes()"/></th>
									<th>S No</th>
									<th >Order Id </th>
									<th>Order Code</th>
									</tr>
									</thead>
									<tbody>
									<tr ng-repeat="(k,value) in hubOrders" ng-model="hubOrders">
										<td><input type="checkbox" ng-model="hubOrdersCheck" class="orderidcheck" value="value.order_id" ng-click="getSleectedHub(value.order_id)"/> </td>
										<td><%k+1%></td>
										<td><%value.order_id%></td>
										<td><%value.order_code%></td>
									</tr>

									</tbody>
								</table>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										<button type="button" class="btn btn-primary" ng-click="moveOrders(hubOrders,RouteId)">Move</button>
									</div>
								</div>
							</div>
						</div>

					</td>


				</tr>

			</tbody>
		</table>
		<!-- Trigger the modal with a button -->

		<!-- Modal -->

		<table class="table table-striped" ng-show="show1">
			<thead>
				<tr>
					<!-- <th>Order Id </th> -->
					<th>Order Code</th>
					<th>#Crates</th>
					<th>#Cartons</th>
					<th>#Bags</th>
					<th>Retailer Name</th>
					<th>Beat</th>
					<th>Address</th>
					<th>Volume</th>

				</tr>

			</thead>
			<div>
				<tbody ng-repeat="(histk,histv) in orders" ng-model="orders">
					<tr ng-repeat="(keys,values) in histv"  >
						<!-- <td><%values.coordinates.gds_order_id%></td> -->
						<td><%values.coordinates.order_code%></td>
						<td style="text-align: right;"><%values.coordinates.crates_info.crates_count%></td>
						<td style="text-align: right;"><%values.coordinates.other_info[0].cfc_count%></td>
						<td style="text-align: right;"><%values.coordinates.other_info[0].bag_count%></td>
						<td><%values.coordinates.shop_name%></td>
						<td><%values.coordinates.beat%></td>
						<td><%values.coordinates.address_info.address%></td>
						<td style="text-align: right;"><%values.coordinates.weight%></td>
					</tr>
				</tbody>
			</div>
		</table>
		<div ng-show="show2">
			<div ui-grid-pagination ui-grid="gridOptions" ng-mouseover="ngGridFIx()" ui-grid-selection ui-grid-exporter class="myGrid"></div>
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
				<tr ng-repeat="(unaskey,unasval) in unassigned_orders" ng-model="unassigned_orders">
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
						<select  class="form-control" ng-model="selectedReassign">
							<option value="">---- select route ----</option>
							<option ng-repeat="(k,value) in vehicleinfo" value="<%value.id%>"><%value.route_name%></option>
						</select>
					</td>
					<td>
						<i class="fa fa-bookmark-o" style="float: right;color: #0088cc;font-weight: bold;font-size: 16px;" aria-hidden="true" title="Assign" ng-click="reassignVehicle(selectedReassign,unasval.coordinates.gds_order_id,unassigned_orders.id)"></i>
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


<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.2.0.js"></script>
<script src="https://rawgit.com/allenhwkim/angularjs-google-maps/master/build/scripts/ng-map.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-grid/4.0.6/ui-grid.js"></script>
<script src="{{ URL::asset('assets/global/scripts/csv.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/pdfmake.js') }}"></script>
<script src="{{ URL::asset('assets/global/scripts/vfs_fonts.js') }}"></script>


<script  type="text/javascript">
	var app = angular.module('plunker', ['ui.bootstrap','ngMap','ui.grid','ui.grid.pagination', 'ui.grid.exporter','ui.grid.selection'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});


	app.controller("AccordionDemoCtrl",function($scope,$http,$rootScope,$filter,$window) { 
	// ui-grid section
	$rootScope.selectedGridData;

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
				{ field: 'DE Name', width: '150' },
				{ field: 'Vehicle Number', width: '150' },
				{ field: 'Invoice Amt', width: '100' , cellClass: 'testingClass'}
			],
			onRegisterApi: function (gridApi) {
				$scope.grid1Api = gridApi;
				gridApi.selection.on.rowSelectionChanged($scope,function(rows){
					//console.log(gridApi.selection.selectAllRows());
		            $scope.mySelections = gridApi.selection.getSelectedRows();
		            $rootScope.selectedGridData = $scope.mySelections;
		        });
			}
		};

		$scope.ngGridFIx = function() {
			window.dispatchEvent(new Event('resize'));
		}

		$scope.reAssignToUnAssigned = function(){
            var orders_ids = [];
            var orderIds;
            var routeIdFrom;
            angular.forEach($rootScope.selectedGridData, function(value, key){
            	orders_ids.push(value.Order_Id);
            	routeIdFrom = value.route_id;
            	orderIds = orders_ids.join();
            });

            var data ={
            	route_id : routeIdFrom,
            	order_id_list : orderIds
            }
            var req = $http.post('/routingadmin/movetounassigned',data);
			req.success(function(successCallback){
				console.log(successCallback);
				$window.location.reload();
			});
			req.error(function(errorCallback){
				alert(JSON.stringify(errorCallback));
			});

		}
		// ui-grid end
	getParams();
	$scope.formatDate = function(date){
          var dateOut = new Date(date);
          return dateOut;
    };
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
			alert("Please Select DeliveryExecutive to Reassign!!");
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
	
	$scope.assignDeliverExec = function(route,vehclData,delvData){
		if($scope.deliveryVehcle[route] == "" && $scope.deliveryExname[route] == ""){
			var afterColVeh = angular.element(document.querySelector('#vehicleDrop'+route)).val();
			var vehicle_id = afterColVeh.substr(afterColVeh.indexOf(":") + 1);
			var afterColDel = angular.element(document.querySelector('#delvDrop'+route)).val();
			var delvName = afterColDel.substr(afterColDel.indexOf(":") + 1);
			if(vehicle_id != "" && delvName != ""){
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

			}else if(vehicle_id !="" && delvName == ""){
				angular.forEach(vehclData,function(val,key){
				if(val.vehicle_id == vehicle_id){
					$scope.vehicle_number = val.vehicle_number;
				}
			});

			var data = {'route_id':route,
			"vehicle_id":vehicle_id,
			"vehicle_number":$scope.vehicle_number};

			}else if(vehicle_id =="" && delvName !== ""){
				angular.forEach(delvData,function(value,key){
				if(value == delvName){
					$scope.delId = key;
				}
			});
			var data = {'de_id':$scope.delId,'de_name':delvName,'route_id':route};

			}else{
				alert("Assign Either Vehicle Or Executive or Both!!");
			}
		}else if($scope.deliveryVehcle[route] == "" && $scope.deliveryExname[route] !== ""){
			var afterColVeh = angular.element(document.querySelector('#vehicleDrop'+route)).val();
			var vehicle_id = afterColVeh.substr(afterColVeh.indexOf(":") + 1);
			angular.forEach(vehclData,function(val,key){
				if(val.vehicle_id == vehicle_id){
					$scope.vehicle_number = val.vehicle_number;
				}
			});
			var data = {'route_id':route,
			"vehicle_id":vehicle_id,
			"vehicle_number":$scope.vehicle_number};

		}else if($scope.deliveryVehcle[route] !== "" && $scope.deliveryExname[route] == ""){
			var afterColDel = angular.element(document.querySelector('#delvDrop'+route)).val();
			var delvName = afterColDel.substr(afterColDel.indexOf(":") + 1);
			angular.forEach(delvData,function(value,key){
				if(value == delvName){
					$scope.delId = key;
				}
			});
			var data = {'de_id':$scope.delId,'de_name':delvName,'route_id':route};
		}else if($scope.deliveryVehcle[route] !== "" && $scope.deliveryExname[route] !== ""){
			alert("Use Reassign Buttons to Change either Executive Or Vehicle");return;
		}

		$http.post("/routingadmin/setdeorvehicleroute",data)
		.success(function(data,status,headers,config){
			if(!$scope.deliveryExname){
				$scope.deliveryExname = [];
			}
			if(!$scope.deliveryVehcle){
				$scope.deliveryVehcle = [];
			}
			if(data.message.hasOwnProperty("de_set")){
				var de_status = data.message.de_set.status;
				var de_message = data.message.de_set.message;
			}else if(data.message.hasOwnProperty("ve_set")){
				var ve_status = data.message.vehicle_set.status;
				var ve_message = data.message.vehicle_set.message;
			}
			if(de_status!=null  && ve_status!=null){
				if(de_status==true && ve_status==true){
					$scope.deliveryVehcle[route] = ve_message;
				}else if(de_status==true && ve_status==false){
					$scope.deliveryExname[route] = de_message;
					alert(ve_message+"  and Executive Updated");
				}else if(de_status==false && ve_status==true){
					$scope.deliveryVehcle[route] = ve_message;
					alert(de_message+"  and Vehicle Updated");
					if(de_message=="Some orders are still not in hub"){
						$scope.RouteId = route;
						$scope.hubOrders = data.message.de_set.NotInHub;
						$("#bsModal3").modal('show');
					}
				}else if(de_status==false && ve_status==false){
					alert(de_message+" and "+ve_message);
				}
			}else if(de_status!= null && !ve_status){
				if(de_status==true){
					$scope.deliveryExname[route] = de_message;
				}else{
					alert(de_message);
					if(de_message=="Some orders are still not in hub"){
						$scope.RouteId = route;
						$scope.hubOrders = data.message.de_set.NotInHub;
						$("#bsModal3").modal('show');
					}
				}
			}else if(!de_status  && ve_status!=null){
				if(ve_status==true){
					$scope.deliveryVehcle[route] = ve_message;
				}else{
					alert(ve_message);return;
				}
			}
		})
		.error(function(data,status,headers,config){

		});
	}
	function getParams(){
		var params = {};
		if (location.search) {
			var parts = location.search.substring(1).split('&');
			for (var i = 0; i < parts.length; i++) {
				var nv = parts[i].split('=');
				if (!nv[0]) continue;
				params[nv[0]] = nv[1] || true;
			}
		}
		if(params.route_id && params.hub_id){
			$scope.route = params.route_id;
			$scope.sendData = {"hub_id":params.hub_id,"route_id":params.route_id};
			$scope.users = [];
			$http.post('/routingadmin/getroutesinfo',$scope.sendData)
			.success(function(response, status, headers, config) {
				if(response.status==true){
					$scope.preData = response.message;
					if(!$scope.assigned_orders){
						$scope.assigned_orders = [];
                	}
                	if(!$scope.assignOrdersData){
                		$scope.assignOrdersData = [];
                	}
                	if(!$scope.unassigned_orders){
                		$scope.unassigned_orders = [];
                	}
                	if(!$scope.orders){
                		$scope.orders = [];
                	}
                	if(!$scope.assnArr){
                		$scope.assnArr = [];
                	}
                	if(!$scope.vehicleinfo){
                		$scope.vehicleinfo = [];
                	}
                	if(!$scope.cratesLen){
                		$scope.cratesLen = [];
                	}
                	if(!$scope.bagsLen){
                		$scope.bagsLen = [];
                	}
                	if(!$scope.cfcLen){
                		$scope.cfcLen = [];
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
                	angular.forEach($scope.preData,function(preval,prekey){
                	$scope.historyCreatedDate = preval.created_at;
                		if(preval.vehicle_number_generated == "unassigned"){
                			$scope.unassigned_orders.push(preval.route_data.coordinates_data);
                			if($scope.unassigned_orders[0]){
                				$scope.unassigned_orders = $scope.unassigned_orders[0];
                			}
                			if($scope.unassigned_orders.hasOwnProperty("id") == false){
                				$scope.unassigned_orders["id"] = preval.id;
                			}
                		}else if(preval.vehicle_number_generated !== "unassigned"){
                			$scope.assigned_orders.push(preval.route_data);
                			$scope.assignOrdersData = {coords:preval.route_data,d_executine_name:preval.delivery_executive_name};
                			$scope.vehicleinfo.push(preval.route_data.vehicleInfo);
                			$scope.vehicleinfo[prekey]["id"] = preval.id;
                			$scope.vehicleinfo[prekey]["route_id"] = preval.route_id;
                			$scope.vehicleinfo[prekey]["route_name"] = preval.vehicle_number_generated;
                			$scope.deliveryVehcle[preval.id] =  preval.vehicle_code;
                			$scope.deliveryExname[preval.id]  = preval.delivery_executive_name;

                			$scope.vehicleinfo[prekey]["status"] = preval.status;

                			$scope.apprxDist[preval.id] = parseInt(preval.estimated_distance)/1000;
                			$scope.apprxTime[preval.id] = parseInt(preval.estimated_time)/60;
                			$scope.editorEnabled[preval.id] = false;
                			$scope.editorDelv[preval.id] = false;
                			$scope.cratesLen[preval.id] = 0;
                			$scope.bagsLen[preval.id] = 0;
                			$scope.cfcLen[preval.id] = 0;
                			angular.forEach(preval.route_data.coordinates_data,function(value,keys){
                				$scope.assnArr.push(value.coordinates);
                				$scope.cratesLen[preval.id] +=  parseInt(value.coordinates.crates_info.crates_count);
                				$scope.bagsLen[preval.id] += parseInt(value.coordinates.other_info[0].bag_count);
                				$scope.cfcLen[preval.id] += parseInt(value.coordinates.other_info[0].cfc_count);
                			});
                			angular.forEach($scope.assignOrdersData.coords.coordinates_data, function(assingedValue, assignedKey){
                			 var invoice_amount_fil = $filter('currency')(assingedValue.coordinates.invoice_amount,'', 2);
                				$scope.users.push(
								{'Order_Id' : assingedValue.coordinates.gds_order_id, 'Order Code' : assingedValue.coordinates.order_code, '#Crates': assingedValue.coordinates.crates_info.crates_count, '#Cartons': assingedValue.coordinates.other_info[0].cfc_count, '#Bags': assingedValue.coordinates.other_info[0].bag_count, 'Retailer Name': assingedValue.coordinates.shop_name, 'Beat': assingedValue.coordinates.beat, 'Address': assingedValue.coordinates.address_info.address, 'Volume': assingedValue.coordinates.weight, 'DE Name': $scope.assignOrdersData.d_executine_name, 'Vehicle Number': $scope.assignOrdersData.coords.vehicleInfo.vehicle_number,'Invoice Amt': invoice_amount_fil, 'route_id': preval.id
								});
								$scope.gridOptions.data = $scope.users;
                			});
                		}
                		$scope.orders.push(preval.route_data.coordinates_data);
                	});
                    }else{
                    	alert(response.message);
                    }
                })
			.error(function(data, status, header, config) {

			});
		}
		$scope.initialdata = '<?php echo json_encode($data); ?>';
		$scope.initialdata = angular.fromJson($scope.initialdata);
		angular.forEach($scope.initialdata.vehicles, function(vehcleval, vehclekey) {
			if (vehclekey == params.hub_id ) {
				if(vehcleval.length>0){
					$scope.vehicles = vehcleval;
					$scope.show = true;
					$scope.show1 = false;
					$scope.show2 = false;
					$scope.show3 = false;	
				}else{
					alert("No Vehicles For the Hub!!!");
				}
			}
		});
		$scope.delvData = {'hub_id':params.hub_id};
		$http.post('/routingadmin/getDeliveryExecutiveList', $scope.delvData)
		.success(function(data, status, headers, config) {
			if(data.status==true){
				$scope.deliveryExec = data.message;
			}
		})
		.error(function(data, status, header, config) {
		});
	}
	$scope.reassignVehicle = function(fromId,fromOrderCode,toId){
		$scope.unassignToAssign = {
			unassigned_order : fromOrderCode,
			to_route : fromId,
			unassign_route_id : parseInt(toId)
		};
		var res = $http.post('/routingadmin/moveunassignedtoroute', $scope.unassignToAssign);
			res.success(function(data, status, headers, config) {
				$scope.message = data;
				window.location.reload();
			});
			res.error(function(data, status, headers, config) {
				alert( "failure message: " + JSON.stringify({data: data}));
			});
	}
	$scope.updateroutedistanceTime = function(RouteId){
		$scope.route_data = {"route_id":RouteId};

		$http.post('/routingadmin/updateroutedistanceTime', $scope.route_data)
		.success(function(data, status, headers, config) {
			if(data.status==true){
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
	$scope.checkAllBoxes = function(){
		angular.element(document.querySelector(".orderidcheck")).checked = true;
	}
	$rootScope.orderNotInHub = [];
	$scope.getSleectedHub = function(selectedHubId){
		$rootScope.orderNotInHub.push(selectedHubId);
	}

	$scope.moveOrders = function(hubOrders,routeid){
		var selectedHubOrder = $rootScope.orderNotInHub;
		selectedHubOrder = selectedHubOrder.toString();
		var data = {"route_id":routeid,"order_id_list":selectedHubOrder};
		$http.post('/routingadmin/movetounassigned', data)
		.success(function(data, status, headers, config){
			if(data.status==true){
				alert(data.message);
				$('#bsModal3').modal('hide');

			}else{
				alert(data.message);
			}
		})
		.error(function(data, status, header, config){

		});
	}
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