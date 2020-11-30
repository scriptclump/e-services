	@extends('layouts.default')
	@extends('layouts.header')
	@extends('layouts.sideview')
	@section('content')
	<?php View::share('title', 'Ebutor - Order Map View'); ?>


	<div ng-app="mapRouting" ng-controller="AllOrdersSockRoutingCtlr" ng-init="loadAllOrdersSock()">
		<div class="row dropdownSet">
			<div class="col-md-6"><span style="font-size: 16px;font-weight: 600;">Order Map View</span></div>
			<div class="col-md-6"><i style="font-size: 20px;float: right;padding-right: 15px;" ng-click="toggle()" class="fa fa-filter" aria-hidden="true"></i></div>
		</div>
		<div ng-show="toggleVariable">
			<div class="row dropdownSet">
				<div class="col-md-4">
					<div class="form-group" style="margin-bottom: 0px !important;">
						<div class='input-group date' id='datetimepicker6' >
							<input type='text' class="form-control" style="border: 1px solid #aaa;" placeholder="From Date" ng-model="date" id="reqDate"/ >
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group" style="margin-bottom: 0px !important;">
						<div class='input-group date' id='datetimepicker7' >
							<input type='text' class="form-control" style="border: 1px solid #aaa;" placeholder="To Date" name="to_date" ng-model="date" id="reqToDate"/>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<button type="submit" value="Submit"  style="width: 49%;" class="btn btn-success" ng-click="sendDateRequest()">Submit</button>
					<button type="button" style="width: 49%;float: right;" class="btn btn-primary" ng-click="resetAllFilter()">Reset</button>
				</div>
			</div>
			<div class="row dropdownSet">
				<div class="col-md-2">
					<select ng-model="orderDC"  id="orderDC" name="order_dc"  class=" dropdownSelect form-control" ng-change="getHubData(orderDC)">
						<option selected value="">---Select DC---</option>
						<option ng-repeat="(key,value) in dcFilterData " value="<%value.le_wh_id%>"><%value.lp_wh_name%></option>    
					</select>
				</div>
				<div class="col-md-2">
				<select ng-model="orderHub"  id="orderHub" name="order_hub" class=" dropdownSelect form-control select2me" ng-change="getBeatData(orderHub)">
					<option selected value="">---Select Hub---</option>
					<option ng-repeat="(key,value) in hub_array " value="<%value.hub_id%>" ><%value.lp_wh_name%></option>    
				</select>
					
				</div>
				<div class="col-md-2">
					<select class="dropdownSelect form-control select2me" ng-model="selectedBeat" ng-change="getFFSortData(selectedBeat)">
						<option selected value="">---- Select Beat ----</option>
						<option ng-repeat="(key,value) in beat_array  track by $index" value="<%value.pjp_pincode_area_id%>" ><%value.pjp_name%></option>    
					</select>
				</div>
				<div class="col-md-3">
					<select class="dropdownSelect form-control select2me" ng-model="selectedFF" ng-change="loadFFSelectedData(selectedFF,orderHub)">
						<option selected value="">---- Select Field Force ----</option>
						<option ng-repeat="(key,value) in ff_array" value="<%key.ID%>"><%value.NAME%></option>
					</select>
				</div>
				<div class="col-md-3">
					<select class="dropdownSelect form-control select2me" ng-model="selectedOrder" ng-change="loadOrderStatusSelectedData(selectedOrder)">
						<option selected value="">---- Select Order Status ----</option>
						<option ng-repeat="(key,value) in orderStatusFilterData | orderBy" value="<%key%>"><%value%></option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="map_wrapper">
					<div id="map_canvas" class="mapping"></div>
				</div>
			</div>
		</div>
	</div>
	@stop
	@section('style')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
	<style type="text/css">
	.page-content {
	    /*height: auto !important;
	*/}


	@media (min-width: 992px){
	.page-content-wrapper .page-content {
	    height: auto !important;
	    padding-bottom: 17px !important;
	    min-height: 0px !important;
	}
	}

	#map_wrapper {
	    height: 600px;
	}
	#map_canvas {
	    width: 97.5%;
	    height: 100%;
	    left: 15px;
	}	
	.dropdownSet{
		padding: 15px;
	}
	.dropdownSelect{
		width: 100%;
	    height: 34px;
	    /* border: 1px solid #ddd; */
	    padding-left: 8px;
	    color: #666;
	}

	.input-group .input-group-addon {
	    border-color: #aaaaaa;
	    background: #e5e5e5;
	    min-width: 39px;
	}
	</style>
	@stop

	@section('script')

	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.1/lodash.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>


	<script type="text/javascript">
		$('#datetimepicker6').datetimepicker({
			format: "YYYY-MM-DD"
		});
		$('#datetimepicker7').datetimepicker({
			format: "YYYY-MM-DD",
			useCurrent: false 
		});
	</script>
	<script type="text/javascript">
		var app = angular.module('mapRouting', [],function($interpolateProvider){
			$interpolateProvider.startSymbol('<%');
			$interpolateProvider.endSymbol('%>');
		});
		app.filter('secondDropdown', function () {
		    return function (secondSelect, firstSelect) {
		        var filtered = [];
		        if (firstSelect === null) {
		            return filtered;
		        }
		        angular.forEach(secondSelect, function (s2) {
		            if (s2.idHubParent == firstSelect) {
		                filtered.push(s2);
		            }
		        });
		        return filtered;
		    };
		});
		app.controller('AllOrdersSockRoutingCtlr', function ($scope,$http,$rootScope, $location, $filter) {
			//Main Data
			$scope.orderMapViewData = angular.fromJson(<?php echo $data;?>);
			

			//filter Data
			$scope.orderMapFilterData = angular.fromJson(<?php echo $filter_elements;?>);
			$scope.beatFilterData = $scope.orderMapFilterData.beat;
			$scope.hubFilterData = $scope.orderMapFilterData.HUB;
			$scope.dcFilterData = $scope.orderMapFilterData.DC;
			$scope.fieldForceFilterData = $scope.orderMapFilterData.FieldForce;
			$scope.orderStatusFilterData =  $scope.orderMapFilterData.order_status
			
			$scope.resetAllFilter = function(){
				var dropDown = document.getElementById("selectedHub");
				//dropDown.selectedHub = '';
				window.location.reload();
				$scope.loadAllOrdersSock();
				
			}

			$scope.toggle = function(){
				$scope.toggleVariable = !$scope.toggleVariable;
			}

			/*
			Used to display order on the map using cursors
			*/
			$scope.loadMarkerOnMap = function(markersData) {
				console.log("Inside map")

			var map;
			 var bounds = new google.maps.LatLngBounds();
		    	var mapOptions = {
		    		//center : {lat:17.385044,lng:78.486671},
				center:new google.maps.LatLng(17.385044,78.486671),
				zoom:15,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
		          fullscreenControl: true
		    	};
		    	// Display a map on the page
		    	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
		    	map.setTilt(0);

		    	var markerKey = 1;
		    	angular.forEach(markersData, function(value1, secKey){
		    		if (secKey == 7144 ) {//7144 'orders_with_coords'
		    			var prev = markerKey;
		    			markerKey = 5;
		    			var colorURL = [
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_green'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_grey'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_orange'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_purple'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_white'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_yellow'+markerKey+'.png'
							];
						markerKey =  prev;

		    		}else if(secKey == 7290 ){// 'orders_without_coords'
		    			var prev = markerKey;
		    			markerKey = 4;
		    			var colorURL = [
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_green'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_grey'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_orange'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_purple'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_white'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_yellow'+markerKey+'.png'
							];
						markerKey = prev;
		    		}else{
		    			var colorURL = [
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_green'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_grey'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_orange'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_purple'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_white'+markerKey+'.png',
					    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_yellow'+markerKey+'.png'
							];
		    		}
		    		
		    		angular.forEach(markersData[secKey],function(value,key1){
		    			var order_total_value = $filter('currency')(value.order_total, '' , 2);
		    			var markers = [value.cust_le_id,value.beat,value.first_name,value.last_name,value.latitude,value.longitude,value.order_code,value.order_date,value.order_status,value.hub_name,value.addr1,value.addr2,value.city,value.postcode,value.company,value.cteated_by,value.order_date,value.is_self,value.reason, order_total_value];
		    			
		    			$scope.markers = markers;
		    			if(markers[8] == 'HOLD'){
		    				// Info Window Content
							var infoWindowContent = 
							   '<div class="info_content">' +
								'<p><b>Order Code <span style="padding-left:50px;">: </b>' + markers[6] + '</span></p>' +
							    '<p><b>Order Status <span style="padding-left:43px;">: </b>' + markers[8] + ' </span></p>'+ 
							    '<p><b>Beat <span style="padding-left:90px;">: </b>'+ markers[1] +' </span></p>' +
							    '<p><b>Hub Name <span style="padding-left:55px;">: </b>'+ markers[9] +' </span></p>' +
							    '<p><b>Order Placed By <span style="padding-left:23px;">: </b>'+ markers[15] +' </span></p>' +
							    '<p><b>Self Order <span style="padding-left:58px;">: </b>'+ markers[17] +' </span></p>' +
							    '<p><b>Hold Reason <span style="padding-left:43px;">: </b>'+ markers[18] +' </span></p>' +
							    '<p><b>Order Value <span style="padding-left:48px;">: </b>'+ markers[19] +' </span></p>' +
							    '<p><b>Order Date <span style="padding-left:55px;">: </b>'+ markers[16] +' </span></p>' +
							    '<p><b>Shop Name <span style="padding-left:50px;">: </b>'+ markers[14] +' </span></p>' +
							    '<p><b>Shop Owner Name <span style="padding-left:9px;">: </b>'+ markers[2] +', ' + markers[3] +' </span></p>' +
							    '<p><b>Address <span style="padding-left:71px;">: </b>'+ markers[10] + ', ' + markers[11] +', '+ markers[12] +', ' + markers[13] +' </span></p>' +
							    '</div>';
							}else{
								// Info Window Content
								var infoWindowContent = 
								   '<div class="info_content">' +
									'<p><b>Order Code <span style="padding-left:50px;">: </b>' + markers[6] + '</span></p>' +
								    '<p><b>Order Status <span style="padding-left:43px;">: </b>' + markers[8] + ' </span></p>'+ 
								    '<p><b>Beat <span style="padding-left:90px;">: </b>'+ markers[1] +' </span></p>' +
								    '<p><b>Hub Name <span style="padding-left:55px;">: </b>'+ markers[9] +' </span></p>' +
								    '<p><b>Order Placed By <span style="padding-left:23px;">: </b>'+ markers[15] +' </span></p>' +
								    '<p><b>Self Order <span style="padding-left:58px;">: </b>'+ markers[17] +' </span></p>' +
							    	    '<p><b>Order Value <span style="padding-left:48px;">: </b>'+ markers[19] +' </span></p>' +
								    '<p><b>Order Date <span style="padding-left:55px;">: </b>'+ markers[16] +' </span></p>' +
								    '<p><b>Shop Name <span style="padding-left:50px;">: </b>'+ markers[14] +' </span></p>' +
								    '<p><b>Shop Owner Name <span style="padding-left:9px;">: </b>'+ markers[2] +', ' + markers[3] +' </span></p>' +
								    '<p><b>Address <span style="padding-left:71px;">: </b>'+ markers[10] + ', ' + markers[11] +', '+ markers[12] +', ' + markers[13] +' </span></p>' +
								    '</div>';
							}
		    			

						var infowindow = new google.maps.InfoWindow({maxWidth: 300});

		    			var colorSize = colorURL.length;
						var icon = colorURL[markerKey%colorSize];

		    			var position = new google.maps.LatLng(value.latitude,value.longitude);
					        bounds.extend(position);
					        marker = new google.maps.Marker({
					            position: position,
					            map: map,
					            id: markers[9],
					            title: value.order_code,
					            icon: icon,
					        });
						   map.setCenter(marker.getPosition())

					    var markColor = marker.icon.split("_")[1].replace(/[0-9]/g, '').split(".")[0];

				        if(value.order_status == "HOLD" && (marker.id == value.hub_name && marker.icon.split("_")[1].replace(/[0-9]/g, '').split(".")[0] == markColor)){
				        	
		    				marker.setIcon('https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_'+markColor+'.png');
		    			}
					        //console.log(marker);
					    google.maps.event.addListener(marker,'click', (function(marker,infoWindowContent,infowindow){ 
					        return function() {
					           infowindow.setContent(infoWindowContent);
					           infowindow.open(map,marker);
					        };
					    })(marker,infoWindowContent,infowindow)); 

					    // Automatically center the map fitting all markers on the screen
					     map.fitBounds(bounds);
					     map.panToBounds(bounds);
		    		});
		    		markerKey++;

				});

				angular.forEach($rootScope.loadHubLocatedMark, function(hubmarkvalue, hubmarkkeys){
				 var new_markers = [hubmarkvalue.bu_name,hubmarkvalue.latitude,hubmarkvalue.longitude,hubmarkvalue.address1,hubmarkvalue.address2];
				 var position = new google.maps.LatLng(hubmarkvalue.latitude,hubmarkvalue.longitude);
			        bounds.extend(position);
				   let pos = hubmarkvalue.bu_name.indexOf('DC');
				   if(pos ==-1) {
					new_marker = new google.maps.Marker({
			            position: position,
			            map: map,
			            title: hubmarkvalue.bu_name+ '\n Address: '+hubmarkvalue.address1,
			            icon: '/img/google_markers/pointer-multicolor.png'
			        });
				   } else {
					var markColor = marker.icon.split("_")[1].replace(/[0-9]/g, '').split(".")[0];
					new_marker = new google.maps.Marker({
			            position: position,
			            map: map,
			            title: hubmarkvalue.bu_name+ '\n Address: '+hubmarkvalue.address1,
			           icon: '/img/google_markers/color-icons-green-home.png'
			        });
				   
				   }
					// map.fitBounds(bounds);
					// map.panToBounds(bounds);
				});
				
			// 	var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			//         this.setZoom(12);
			//         google.maps.event.removeListener(boundsListener);
			//     });
			}


			/*
			  Used to load all order stocks
			*/
			$scope.loadAllOrdersSock = function(){
				$scope.dc = [];
		    	$scope.hub = [];
		    	$scope.beat = [];
			    $scope.hubFilterData = $scope.orderMapFilterData.HUB;
		         $scope.beatFilterData = $scope.orderMapFilterData.beat;
		    	if($scope.orderMapViewData.status ==  true){
				    
		    		$rootScope.loadHubLocatedMark = $scope.orderMapViewData.OrderList.hub_details;
					angular.forEach($scope.orderMapViewData.OrderList.orders_with_coords, function(secValue, key){
							if(secValue.hub_id != 0){
								$scope.hubFilterData[secValue.hub_id] = {namehub:secValue.hub_name, idHub:secValue.hub_id};
							}
							$scope.beatFilterData[secValue.beat_id] = {beatname:secValue.beat,idHubParent:secValue.hub_id,beatId:secValue.beat_id};
				    		if($scope.hub[secValue.hub_id] != undefined){
				    			$scope.hub[secValue.hub_id].push(secValue);
				    		}else{
				    			$scope.hub[secValue.hub_id] = [];
				    			$scope.hub[secValue.hub_id].push(secValue);
				    		}

				    		//if key secValue.hub_id exists in $scope.hub
				    		if($scope.beat[secValue.beat_id] != undefined){
				    			$scope.beat[secValue.beat_id].push(secValue);
				    		}else{
				    			$scope.beat[secValue.beat_id] = [];
				    			$scope.beat[secValue.beat_id].push(secValue);
				    		}
			    		});
					    console.log("hello")
					$scope.loadMarkerOnMap($scope.orderMapViewData.OrderList);
				}else{
					alert('No order found');
				}
			}

			/*
			Used to sort hubs based upon Dc selections
			*/
			$scope.getHubData=function(dc_id) {
				    $('#orderHub').select2("val","");
				    $scope.hub_array=[];
					angular.forEach($scope.hubFilterData ,function(value,key){
						if(dc_id==value.dc_id){
							$scope.hub_array.push(value);
						}
					});
					
					//loading beat data
					
					$('#selectedBeat').select2("val","");
					$scope.beat_array=[];
					angular.forEach($scope.beatFilterData,function(value,key){
						if(dc_id==value.le_wh_id){
							$scope.beat_array.push(value);
						}	
					});
					angular.forEach($scope.beatFilterData,function(value,key){
						angular.forEach($scope.hubFilterData,function(hubvalue,hubkey){
							if(hubvalue.hub_id==value.le_wh_id){
								$scope.beat_array.push(value);
							}
						});
					});
					// $scope.loadMarkerOnMap($scope.hub_array);
			}

			/*
			Used to sort beats based upon hubs selection 
			*/
			$scope.getBeatData=function(hub_id){
				if(hub_id!=""){
					$('#selectedBeat').select2("val","");
					$scope.beat_array=[];
					angular.forEach($scope.beatFilterData,function(value,key){
						if(hub_id==value.le_wh_id){
							$scope.beat_array.push(value);
						}
					});
					 $scope.loadMarkerOnMap($scope.beat_array);
					
				}
			else{

				$scope.getHubData($scope.orderDC);
			}
			}

			/*
			Used to sort ff based upon  beat selection
			*/
			$scope.getFFSortData=function(beat_id){
				$scope.ff_array=[];
				if(beat_id!=""){
					$('#selectedFF').select2("val","");
					angular.forEach($scope.fieldForceFilterData,function(value,key){
						if(beat_id==value.beat_id){
							$scope.ff_array.push(value);
						}
					});
					// $scope.loadMarkerOnMap($scope.beat_array);
					
				}
			else{

				$scope.getHubData($scope.orderDC);
			}
			}
			
			
			

			$scope.loadHubSelectedData = function(selectedHub){
				$scope.selectedHubData = [];
				$scope.fieldForceFilterData = {};
				$scope.orderStatusFilterData = {};
				$scope.selectedHubData[selectedHub] = [];
				angular.forEach($scope.orderMapViewData.OrderList.orders_with_coords, function(secValue, secKey){

					console.log("seckey ", seckey)				
						if(selectedHub == secValue.hub_id){
						if($scope.selectedHubData[secValue.beat_id] != undefined){
				    			$scope.selectedHubData[secValue.beat_id].push(secValue);
								$scope.fieldForceFilterData[secValue.ff_id] = secValue.cteated_by;
								$scope.orderStatusFilterData[secValue.order_status_id] = secValue.order_status;
				    	}else{
				    			$scope.selectedHubData[secValue.beat_id] = [];
				    			$scope.selectedHubData[secValue.beat_id].push(secValue);
				    			$scope.fieldForceFilterData[secValue.ff_id] = secValue.cteated_by;
								$scope.orderStatusFilterData[secValue.order_status_id] = secValue.order_status;
				    	}
					}else
					if(selectedHub == 'allHub'){
						if($scope.selectedHubData[secValue.hub_id] != undefined){
							$scope.selectedHubData[secValue.hub_id].push(secValue);
						}else{
				    		$scope.selectedHubData[secValue.hub_id] = [];
							$scope.selectedHubData[secValue.hub_id].push(secValue);
						}			    	
					}
				});
				/*if($scope.selectedHubData[selectedHub].length == 0){
					alert('No Record Found');
				}*/
				$scope.loadMarkerOnMap($scope.selectedHubData);
			}
			$scope.loadOrderStatusSelectedData = function(selectedOrderStatus){
				$scope.selectedOrderStatusData = [];
				$scope.selectedOrderStatusData[selectedOrderStatus] = [];
				angular.forEach($scope.beat, function(value,key){
					angular.forEach($scope.beat[key], function(secValue, secKey){
						if(selectedOrderStatus == secValue.order_status_id){
							$scope.selectedOrderStatusData[secValue.order_status_id].push(secValue);
						}
					});
				});
				if($scope.selectedOrderStatusData[selectedOrderStatus].length == 0){
					alert('No Record Found');
				}
				$scope.loadMarkerOnMap($scope.selectedOrderStatusData);
			}
			$scope.loadBeatSelectedData = function(selectedBeatData,checkingVlaue){
				
				$scope.selectedBeatData = [];
				$scope.selectedBeatData[selectedBeatData] = [];
				angular.forEach($scope.beatFilterData, function(value,key){
					angular.forEach($scope.beatFilterData[key], function(secValue, secKey){
						if(selectedBeatData == secValue.pjp_pincode_area_id){
							$scope.selectedBeatData[secValue.pjp_pincode_area_id].push(secValue);
							$scope.fieldForceFilterData[secValue.ff_id] = secValue.cteated_by;
						}else
						if(selectedBeatData == 'allBeats' && checkingVlaue == secValue.hub_id){
							if($scope.selectedBeatData[secValue.hub_id] != undefined){
									$scope.selectedBeatData[secValue.hub_id].push(secValue);
							}else{
						    		$scope.selectedBeatData[secValue.hub_id] = [];
									$scope.selectedBeatData[secValue.hub_id].push(secValue);
							}	
						}
					});
				});
				if($scope.selectedBeatData[selectedBeatData].length == 0){
					alert('No Record Found');
				}
				$scope.loadMarkerOnMap($scope.selectedBeatData);
			}

			$scope.loadFFSelectedData = function(selectedFFData,hubSlectedValue){
				$scope.selectedFFData = [];
				$scope.selectedFFData[selectedFFData] = [];
				angular.forEach($scope.beat, function(value,key){
					angular.forEach($scope.beat[key], function(secValue, secKey){
						if(hubSlectedValue == secValue.hub_id && selectedFFData == secValue.ff_id) {
								$scope.selectedFFData[selectedFFData].push(secValue);
						}
					});
				});
				if($scope.selectedFFData[selectedFFData].length == 0){
					alert('No Record Found');
				}
				$scope.loadMarkerOnMap($scope.selectedFFData);
			}

			$scope.sendDateRequest = function(){
				$scope.fromDate = angular.element(document.querySelector('#reqDate')).val();
				$scope.toDate = angular.element(document.querySelector('#reqToDate')).val();
				
				$scope.dateJson = {
					'from_date' : $scope.fromDate,
					'to_date' : $scope.toDate
				};

				var res = $http.post('/routingadmin/ordermapviewapi', $scope.dateJson);
				res.success(function(data, status, headers, config) {
					$scope.message = data;
					$rootScope.$broadcast('getFilterData', data);
				});
				res.error(function(data, status, headers, config) {
					alert( "failure message: " + JSON.stringify({data: data}));
				});
			}

			$scope.$on('getFilterData', function(events, args){
			        $scope.orderMapViewData = args;
			        $scope.loadAllOrdersSock();
			})
			
		});
	</script>
	@stop
	@extends('layouts.footer')