@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php //echo $json; exit; ?> 
<?php View::share('title', 'Ebutor - Shortest Route'); ?>

<div ng-app="mapRouting" ng-controller="MapRoutingCtlr">
    
<div class="row">
	<div class="col-md-12">

	    		<div class="ebutorMapLogo">
	    			<img src="{{ URL::asset('img/ebutor.png') }}">
	    		</div>
	    		<div>
			    <ui-gmap-google-map center="map.center" zoom="map.zoom" bounds="map.bounds" control="map.control">
					<ui-gmap-markers ng-if="markerEncodeData && !directionMarkers.length" models="markerEncodeData" coords="'self'" icon="'icon'" labelContent="'label'"></ui-gmap-markers>
					<ui-gmap-markers ng-if="markerEncodeDataSec && !directionSecondMarkers.length" models="markerEncodeDataSec" coords="'self'" icon="'icon'" labelContent="'label'"></ui-gmap-markers>
					<ui-gmap-markers ng-if="directionMarkers.length" models="directionMarkers" coords="'self'" icon="'icon'" click="click()">
						<ui-gmap-windows show="show" ng-cloak>
							<span>Order Code : <% loccc.orderCodeId %><br/>Crate Information : <% loccc.crateInfor %><br/>Crate Count : <% loccc.crateCountInfo %><br/>Beat : <% loccc.beatInfo %></span>
						</ui-gmap-windows>
					</ui-gmap-markers>
					<!-- <ui-gmap-markers ng-if="directionSecondMarkers.length" models="directionSecondMarkers" coords="'self'" icon="'icon'" click="click()">
						<ui-gmap-windows show="show">
						</ui-gmap-windows>
					</ui-gmap-markers> -->
				</ui-gmap-google-map>
				</div>
	
	<button class="slide-toggle clickSlide">Map Datasheet</button>
	<div class="box">
	    		<ul class="nav nav-tabs orderTabs" style="position: absolute;" ng-init="generateMarkers();calculateDirections(roundTrip);generateMarkersSec();calculateSecondDirections(roundTrip)">
	    			<li class="active"><a data-toggle="tab" href="#vehicle" style="padding-left: 20px !important;padding-right: 20px !important;">Route</a></li>
	    			<li><a data-toggle="tab" href="#route" style="padding-left: 20px !important;padding-right: 20px !important;">Vehicle</a></li>
				    <li><a data-toggle="tab" href="#order" style="padding-left: 20px !important;padding-right: 20px !important;">Order</a></li>
				    
				  </ul>
				  <div class="tab-content" style="position: absolute;top: 45px;width: 100%;">
				  	<div id="vehicle" class="tab-pane fade in active">
				  		<div class="iconSetting" ng-cloak>
				  			<i class="fa fa-road" aria-hidden="true"></i><span> <% totalDist %> </span><span ng-hide="totalDist>0"> 0 </span>km
				  			<i class="fa fa-clock-o" aria-hidden="true"></i><span> <% totalTime %> </span><span ng-hide="totalTime>0"> 0 </span>mins(Approx)
				  		</div>
				  		<span ng-show="calculating" class="directions-calculating" style="padding-left: 15px;"><span class="glyphicon glyphicon-repeat spinning"></span> Calculating directions...</span>
						<div ng-hide="calculating" class="directions"></div>
				    		
				    </div>
				  	<div id="route" class="tab-pane fade">
				  		<div class="row">
				  			<div class="col-md-6 routeSpan">
				  				<span>Vehicle Number</span>
				  			</div>
				  			<div class="col-md-1">:</div>
				  			<div class="col-md-5"><span><% markerRouteData.vehicle_number %></span></div>
				  		</div>
				  		<div class="row">
				  			<div class="col-md-6 routeSpan">
				  				<span>Vehicle Max Load</span>
				  			</div>
				  			<div class="col-md-1">:</div>
				  			<div class="col-md-5"><span><% markerRouteData.vehicle_max_load %></span></div>
				  		</div>
				  		<div class="row">
				  			<div class="col-md-6 routeSpan">
				  				<span>Consignment Weight</span>
				  			</div>
				  			<div class="col-md-1">:</div>
				  			<div class="col-md-5"><span><% markerRouteData.consignmentWeight%></span></div>
				  		</div>
				  		<div class="row">
				  			<div class="col-md-6 routeSpan">
				  				<span>Vehicle Crates Capacity</span>
				  			</div>
				  			<div class="col-md-1">:</div>
				  			<div class="col-md-5"><span><% markerRouteData.vehicle_max_load / 0.060 | number:0 %></span></div>
				  		</div>
				    	
				    	
				    </div>
				    <div id="order" class="tab-pane fade" style="overflow: scroll;height: 510px;">
				    	<table style="width: 550px;">
				      		<tr style="" class="trLabel">
				      			<th>SI No.</th>
				      			<th>Order Code</th>
				      			<th>Crate's Barcode</th>
				      			<th>No's Of Crates</th>
				      		</tr>
				      		<tr class="trLabel" ng-repeat="orderCodeValue in markerCount | orderBy">
				      			<td><% $index + 2 %></td>
				      			<td><% orderCodeValue.coordinates.order_code %></td>
				      			<td class="crateVAlue">
				      				<ul ng-repeat="value in orderCodeValue.coordinates.crates_info.crates" style="margin-bottom: 10px;">
				      					<li>
				      						<% value %>
				      					</li>
				      				</ul> 
				      			</td>
				      			<td><% orderCodeValue.coordinates.crates_info.crates_count %></td>
				      		</tr>
				      	</table>
				    </div>
				    
				    
				  </div></div>
</div>
</div>



</div>

@stop
@section('style')
<style type="text/css">
.ebutorMapLogo img{
	height: 45px;
    width: 45px;
    float: right;
    z-index: 999;
    overflow: visible;
    position: relative;
    opacity: 0.4;
    top:10px;
}
img.adp-marker {
    width: 27px !important;
    height: 27px !important;
}
i.fa.fa-road {
    font-size: 18px;
}

i.fa.fa-clock-o {
    font-size: 18px;
    padding-left: 15px;
}
.iconSetting{
	    margin-top: 12px;
    margin-bottom: 12px;
    text-align: center;
}

.crateVAlue>ul>li{
	display: inline-block;
    top: 6px;
    position: relative;
    left: -16px;
}

.trLabel {
    border-bottom: 1px solid #ddd;
    padding-left: 5px;
    line-height: 0.8;
}
.trLabel>th {    
    text-align: center;
    line-height: 35px;
}
.trLabel>td {    
    text-align: center;
}
.orderTabs{
	margin-top: 0px;
}
.orderTabs>li>a {
    margin-right: 2px;
    line-height: 1.42857;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
    width: auto;
}
.angular-google-map-container {
	   height: 600px;
	   /*width: auto;*/
	       position: relative;
    overflow: hidden;
    width: 100%;
    top: -45px;
}
.map,
.directions {
	height: 480px;
}

.map {
	padding-right: 0px;
}

.directions {
	overflow: auto;
}
.directions-calculating {
	font-weight: bold;
	font-size: 1.2em;
}

.adp-placemark:first-of-type {
	margin-top: 0px;
}

.form-group {
	display: inline-block;
	margin-right: 10px;
}
.form-group.marker-count .form-control {
	display: inline-block;
	width: 64px;
}
.form-group.round-trip label {
	margin-bottom: 0px;
}
.form-group.round-trip .form-control {
	display: inline-block;
	width: auto;
	height: 20px;
	margin-top: 0px;
	margin-left: 4px;
	vertical-align: middle;
}
.box{
        float:left;
        overflow: hidden;
        height: 600px !important;
        width:350px;
        position:absolute;
        background:#fff;
        border: 1px solid #e5e5e5;
        left: 24px;
        top: 40px;
        border-radius: 4px;
        z-index: 99999 !important; 
    }

.clickSlide{
	    position: absolute;
    left: 118.1px;
    top: 9.5px;
    height: 29px;
    background: white;
    border: 1px solid #fff;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
    font-family: Roboto, Arial, sans-serif;
    user-select: none;
    font-size: 11px;
    border-left: 1px solid #e5e5e5;
}
.routeSpan span{
	padding-left: 8px;
}


.glyphicon.spinning {
    animation: spin 1.5s infinite linear;
    -webkit-animation: spin2 1.5s infinite linear;
}
@keyframes spin {
    from { transform: scale(1) rotate(0deg); }
    to { transform: scale(1) rotate(360deg); }
}
@-webkit-keyframes spin2 {
    from { -webkit-transform: rotate(0deg); }
    to { -webkit-transform: rotate(360deg); }
}

</style>
@stop
@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.1/lodash.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<!-- <script src="http://cdn.rawgit.com/nmccready/angular-simple-logger/0.0.1/dist/index.js"></script> -->
<!-- Angular-simple-logger index file-->
<script src="{{ URL::asset('assets/global/scripts/index.js') }}" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-simple-logger/0.1.7/angular-simple-logger.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-google-maps/2.4.1/angular-google-maps.js"></script>
<!-- Angular Google Map TSp -->
<!-- <script src="https://cdn.rawgit.com/tzmartin/Google-Maps-TSP-Solver/1c402549/tsp.js"></script>
<script src="https://cdn.rawgit.com/tzmartin/Google-Maps-TSP-Solver/1c402549/BpTspSolver.js"></script>
<script src="https://cdn.rawgit.com/TechNaturally/angular-google-maps-tsp/c4bf8fe3/angular-google-maps-tsp.js"></script> -->
<script src="{{ URL::asset('assets/global/scripts/tsp.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/BpTspSolver.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/angular-google-maps-tsp.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	var app = angular.module('mapRouting', ['gmaps.tsp' , 'uiGmapgoogle-maps'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	app.config(['GoogleMapsTSPProvider', 'uiGmapGoogleMapApiProvider', function(GoogleMapsTSPProvider, GoogleMapApiProviders){
		GoogleMapsTSPProvider.setDefaults({
		});

		GoogleMapApiProviders.configure({
			/*key: 'AIzaSyBXXQKDsKmVWCzUM57aKZonac-gAHaKyfc'*/
		});
	}]);
	app.run(['GoogleMapsTSP', 'uiGmapGoogleMapApi', function(GoogleMapsTSP, uiGmapGoogleMapApi){
		// certain defaults can only be set at run-time (after Google Maps API is loaded)
		uiGmapGoogleMapApi.then(function(){
			GoogleMapsTSP.setDefaults({
				travelMode: google.maps.TravelMode.DRIVING
			});
		});
	}]);
	app.controller('MapRoutingCtlr', ['$scope', 'GoogleMapsTSP', '$interval', function ($scope, GoogleMapsTSP, $interval , $http, uiGmapGoogleMapApi, uiGmapIsReady) {

		$scope.markersData = angular.fromJson(<?php echo  $json; ?>);
		$scope.markerRouteData = $scope.markersData.vehicleInfo;
		$scope.markerEncodeData = $scope.markersData.coordinates_data;
		var startPoint = $scope.markersData.hub_coordinates.lat;
		var endPoint = $scope.markersData.hub_coordinates.long;
		$scope.map = {
			center: { latitude: 17.3850, longitude: 78.4867 },
			zoom: 12, 
			control: {},
			bounds: {}
		};
		var directionsPanel = document.querySelectorAll('.directions');
		if(directionsPanel && directionsPanel.length){
			directionsPanel = directionsPanel[0];
		}
		$scope.roundTrip = true;
		var directionsDisplay;
		$scope.generateMarkers = function(){
			resetDirections();
			$scope.markerCount = $scope.markerEncodeData;
			var iconUrlBase = "https://mts.googleapis.com/maps/vt/icon/name=icons/spotlight/spotlight-waypoint-a.png&psize=16&font=fonts/Roboto-Regular.ttf&ax=44&ay=48&scale=1&color=ff333333"
			$scope.markerEncodeData = [];
			for(var i=0; i < $scope.markerCount.length; i++){
				var newMarker = {
					id: i,
					label: '#'+i,
					latitude: $scope.markerCount[i].coordinates.lat,
					longitude: $scope.markerCount[i].coordinates.long,
					orderCode: $scope.markerCount[i].coordinates.order_code
				};
				$scope.markerEncodeData.push(newMarker);
			}
		};
		function resetDirections(){
			$scope.hasDirections = false;
			$scope.directionMarkers = [];
			$scope.directionSecondMarkers = [];
			if(angular.isDefined(directionsDisplay)){
				directionsDisplay.setMap(null);
				directionsDisplay.setPanel(null);
			}
		}

		// directions calculated callback
			function directionsCalculated(tsp){
				var map = $scope.map.control.getGMap();
				if(map){
					// create a DirectionsRenderer
					if(angular.isUndefined(directionsDisplay)){
						// suppress markers and infoWindows because we will be making our own
						directionsDisplay = new google.maps.DirectionsRenderer(
							{ 
								suppressMarkers: true, 
								suppressInfoWindows: true,
								polylineOptions: {
							    	strokeColor: "red"
							    }
							}
						);
					}

					// check the Google-Maps-TSP-Solver/BpTspSolver.js source for functions that are available on tsp ...
					// ex. tsp.getOrder()
					var directions = tsp.getGDirections();
					$scope.totalDist = 0;
					$scope.totalTime = 0;
					var myroute = directions.routes[0];
					for (i = 0; i < myroute.legs.length; i++) {
						$scope.totalDist += myroute.legs[i].distance.value;
						$scope.totalTime += myroute.legs[i].duration.value;
					}
					/*converting to KM*/
					$scope.totalDist = $scope.totalDist / 1000; 
					/*Converting to Minutes*/
					$scope.totalTime = ($scope.totalTime / 60).toFixed(2); 

					// add the directions to the map
					directionsDisplay.setMap(map);
					directionsDisplay.setDirections(directions);

					// add the directions to the details panel
					if(directionsPanel){
						directionsDisplay.setPanel(directionsPanel);
						// short poll to wait for the directions panel to be rendered
						var checkForRender = $interval(function(){
							// consider it rendered when a child is found
							if(directionsPanel.childNodes.length){
								// stop the poll
								$interval.cancel(checkForRender);
								checkForRender = undefined;
								// just for fun, customize the markers
								customizeDirectionMarkers(tsp);
							}
						}, 50, 20);
					}
					$scope.hasDirections = true;
				}
			}
			function directionsFailed(error){
				alert('Could not calculate directions.\n\n'+(error.code ? '['+error.code+'] ' : '')+error.message);
			}
			function directionsFinished(){
				$scope.calculating = GoogleMapsTSP.isSolving();
			}

				$scope.calculateDirections = function(roundTrip){

					if($scope.markerEncodeData && $scope.markerEncodeData.length){
						resetDirections();
						$scope.calculating = true;
						// configure the waypoints
						var config = {
							waypoints: []
						};

						config.waypoints.push( {
								label: 'Hub',
								position: new google.maps.LatLng(startPoint , endPoint) 
						});
						// copy all markers in as waypoints
						for(var i=0; i < $scope.markerEncodeData.length; i++){
							config.waypoints.push( {
								label: $scope.markerEncodeData[i].label,
								position: new google.maps.LatLng($scope.markerEncodeData[i].latitude, 
									$scope.markerEncodeData[i].longitude),
								orderId: $scope.markerEncodeData[i].orderCode 
							});
						}
						GoogleMapsTSP.solveRoundTrip(config)
								.then(directionsCalculated)
								.catch(directionsFailed)
								.finally(directionsFinished);
					}
					else {
						directionsFailed({message: 'No markers!', code: 'NO_MARKERS'});
					}
				};
				// the following functions are for customizing the direction markers (on the map + in the directions panel)
				function customizeDirectionMarkers(tsp){
					var directionPlaces = jQuery(directionsPanel).find('.adp-placemark');
					for(var i=0; i < directionPlaces.length; i++){
						customizeDirectionMarker(tsp, i, jQuery(directionPlaces[i]));
					}
				}
				function customizeDirectionMarker(tsp, index, element){
					// extract the location and address from the direction route's legs
					var directions = tsp.getGDirections();
					var location, address;
					if(directions.routes && directions.routes.length){
						var route = directions.routes[0];
						if(route.legs){
							if(index < route.legs.length){
								location = route.legs[index].start_location;
								address = route.legs[index].start_address;
								last = false;
							}
							else if(index == route.legs.length){
								location = route.legs[index-1].end_location;
								address = route.legs[index-1].end_address;
								last = true;
							}
						}
					}

					// create the marker
					if(location){
						var marker;
						//var icon = 'https://mts.google.com/vt/icon?psize=14&font=fonts/Roboto-Regular.ttf&color=ff000000&name=icons/spotlight/spotlight-waypoint-a.png&ax=44&ay=48&scale=1&text='+(index+1);
						var icon = 'https://gebweb.net/optimap/newicons/black'+(index+1)+'.png';
						if(!$scope.roundTrip || !last){
							marker = {
								id: index-1,
								latitude: location.lat(),
								longitude: location.lng(),
								icon: icon,
								click: function(){
									for(var i=0; i < $scope.directionMarkers.length; i++){
										if($scope.directionMarkers[i] != this){
											$scope.directionMarkers[i].show = false;
										}
									}
									$scope.markerThisPoint = (address ? address : location.lat()+','+location.lng());
									$scope.markersDataPoint = $scope.markersData.coordinates_data;
									$scope.loccc = {};
									for(var j=0 ; j<$scope.markerEncodeData.length; j++){
										if(this.id == $scope.markerEncodeData[j].id){
											$scope.loccc = {orderCodeId : $scope.markersDataPoint[j].coordinates.order_code,crateInfor: $scope.markersDataPoint[j].coordinates.crates_info.crates,
												crateCountInfo:$scope.markersDataPoint[j].coordinates.crates_info.crates_count, beatInfo : $scope.markersDataPoint[j].coordinates.beat};
										} 
									}

									// open this marker's info window
									this.show = true;
									$scope.$apply(); // google maps doesn't use ng-click, so we need to $scope.$apply() the changes
								},
							};

							// add the directionMarker 
							$scope.directionMarkers.push(marker);
						}

						else if($scope.roundTrip && last){
							// for round trip, re-use first marker
							marker = $scope.directionMarkers[0];
						}

						// handle clicking on the address in the directions panel
						element.parent().on('click', function(event){
							event.stopPropagation();
							marker.click();
						});


						// inject the custom icon into the directions panel
						element.find('.adp-marker').attr('src', icon);
					}
				}

				//New Route
				/*$scope.generateMarkersSec = function(){
					resetDirections();
					$scope.markerCountSecond = [{'lat':17.391636,'long':78.440065},{'lat':17.399031,'long':78.415322},{'lat':17.440080,'long':78.348917},{'lat':17.402380,'long':78.372854},{'lat':17.383309,'long':78.401053}];
					$scope.markerEncodeDataSec = [];
					for(var i=0; i < $scope.markerCountSecond.length; i++){
					var iconUrlBase = 'https://gebweb.net/optimap/newicons/red'+(i+1)+'.png';
						var newMarker = {
							id: i,
							label: '#'+i,
							icon:iconUrlBase,
							latitude: $scope.markerCountSecond[i].lat,
							longitude: $scope.markerCountSecond[i].long,
							orderCode: 0
						};
						$scope.markerEncodeDataSec.push(newMarker);
					}
				};*/


				$(document).ready(function(){
			        $(".slide-toggle").click(function(){
			            $(".box").animate({
			                width: "toggle"
			            });
			        });
			    });

				
    }]);
</script>
@stop
@extends('layouts.footer')
