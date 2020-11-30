@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - All Route Map'); ?>

<style type="text/css">
.page-content{
	height: 500px;
} 

#map_canvas {
    height: 500px;
}
.infoAssign{
	float: right;
    color: blue;
    text-decoration: underline;
    cursor: pointer;
}
.infoAssignBut{
	width: 100%;
    background: #c0ccf5;
    border: 5px solid #c0ccf5;
    border-radius: 5px;
}
.delvselect{
		margin-top: 12px;
		height: 40px!important;
		width: 330px!important;
		border-radius: 10px!important;
		margin-left: 100px!important;
		margin-bottom: 10px!important;
}
.routeChangeSelect{
    height: 20px !important;
}
.labelsClass{
    height: 430px;
    overflow-y: scroll;
}
.labelsClass strong{
	font-size: 13px;
}
.markerDetail{
	margin-top: 10px;
    border: 1px solid #ddd;
    width: 96%;
    border-radius: 5px;
}
.markerSaveBut{
	border-radius: 20px !important;
    float: right;
    margin-right: 8%;
    height: 21px;
    padding-top: 1px;
    background: #000099;
    color: white;
}
.routeDisp span{
	font-size: 12px;
	color: white !important;
}
</style>
<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/routingadmin" class="bread-color">Route</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><span class="bread-color">All Route Map</span></li>
		</ul>
	</div>
</div>
<div ng-app="mapRouting" ng-controller="MapMainRoutingCtlr" ng-init="loadAllMapData()" ng-cloak>
    <div>
	    <div class="row">
	    	<div class="col-md-8">
			    <div id="map_canvas" class="mapping"></div>
			</div>
			<div class="col-md-4" style="margin-left: -15px;">
				<div class="row">
					<div class="col-md-3" style="margin-top: 10px;margin-left: 15px;border: 1px solid #ddd;" ng-repeat="color in markersColorData| unique:'id'" ng-style="{background: color.iconValue, color:white}">
						<div class="routeDisp">
							<span><%color.id%></span><br/>
						</div>
					</div>
				</div>
				
				<div>
					<span style="font-size: 15px;">Re-Assign Vehicle Route</span>
				</div>
				<div class="labelsClass">
					<div ng-repeat="(key,value) in selectedMarker" class="markerDetail" ng-cloak>
						<div style="margin-left: 8px;margin-top: 5px;">
							<p><strong>Beat</strong> <span style="padding-left: 44px;">: <%value.beatName%></span></p>
							<p><strong>Order Code</strong><span style="padding-left: 5px;">: <%value.order_code%></span>
							<span style="padding-left: 45px;"><strong>Route Number</strong>: <%value.vehRoute%></span></p>
							<p><strong>Crates</strong><span style="padding-left: 5px;">: <%value.crates_count%></span>
							<span style="padding-left: 13px;"><strong>Cartons</strong><span style="padding-left: 5px">: <%value.cfc_count%></span></span>
							<span style="padding-left: 20px;"><strong>Bags</strong><span style="padding-left: 5px">: <%value.bag_count%></span></span></p>
						</div>
					</div>
				</div>
				<div style="margin-top: 15px;" ng-hide="!assignToKeyPair">
					<strong>Route To Assign</strong> :
					<select class="routeChangeSelect" ng-model="selectedKey" id="delvalue">
						<option disabled selected value>Select Route No</option>
						<option  ng-repeat="keyValue in assignToKeyPair" value="<%keyValue.vehId%>"><%keyValue.vehRoute%></option>
					</select>
					<button class="btn markerSaveBut" ng-click="assignRoute(selectedKey)">Save</button>
				</div>
			</div>
	    </div>
	    <div class="modal" id="loginmodal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class=row>
							<select class="delvselect" ng-model="selectedKey" id="delvalue">
								<option disabled selected value>----Select Route----</option>
								<option  ng-repeat="keyValue in assignToKeyPair" value="<%keyValue.vehId%>"><%keyValue.vehRoute%></option>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" data-dismiss="modal" ng-click="assignRoute(selectedKey)">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.1/lodash.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui/0.4.0/angular-ui.js"></script>


<script type="text/javascript">
	var app = angular.module('mapRouting', ['ui'],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});
	
	app.controller('MapMainRoutingCtlr', function ($scope,$http,$rootScope, $location,$filter) {
		var map;
	    var marker;

	    $scope.markersColorData = [];
	    
	    $rootScope.markersMapData = [];

		$scope.loadAllMapData = function(){
			$scope.markersDataMain = angular.fromJson(<?php echo  $json; ?>);
			map = new google.maps.Map(document.getElementById('map_canvas'), {
				zoom: 12,
				center: new google.maps.LatLng($scope.markersDataMain.hub_coordinates.lat, $scope.markersDataMain.hub_coordinates.long),
				mapTypeId: google.maps.MapTypeId.ROADMAP
		    });
			delete $scope.markersDataMain["unassigned_coordinates"];
			$scope.markerEncodeData = [];
			$scope.completeMAp();
		}
		$scope.completeMAp = function(){
			var hubLat = $scope.markersDataMain.hub_coordinates.lat;
			var hubLong = $scope.markersDataMain.hub_coordinates.long;
			$rootScope.hubLatDup = hubLat;
			$rootScope.hubLongDup = hubLong;
			var positionHub = new google.maps.LatLng(hubLat, hubLong);
			new_marker = new google.maps.Marker({
				position: positionHub,
				map: map,
				title: 'Hub',
			    icon: '/img/google_markers/color-icons-green-home.png'
			});

			var i;
		    angular.forEach($scope.markersDataMain, function(value, key){
				angular.forEach($scope.markersDataMain[key]['coordinates_data'], function(secValue, secKey){
					var invoice_amount_fil = $filter('currency')(secValue.coordinates.invoice_amount,'', 2);
					// Multiple Markers
				    var markers = [
				        [secValue.coordinates.order_code,$scope.markersDataMain[key].vehicleInfo.vehicle_number,secValue.coordinates.beat, secValue.coordinates.lat,secValue.coordinates.long,key,secValue.coordinates.crates_info.crates,secValue.coordinates.crates_info.crates_count,secValue.coordinates.gds_order_id,secValue.coordinates.weight,secValue.distance,secValue.order_id,secValue.coordinates.shop_name,secValue.coordinates.address_info.address,secValue.coordinates.address_info.city,secValue.coordinates.address_info.pin,secValue.coordinates.other_info[0].bag_count,secValue.coordinates.other_info[0].cfc_count,invoice_amount_fil]
				    ];

				    // Display multiple markers on a map
    				var infoWindow = new google.maps.InfoWindow({
						maxWidth: 300
			        });

				    for (i = 0; i < markers.length; i++) { 
				    	var colorURL = [
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_green'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_grey'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_orange'+(secKey+1)+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_purple'+(secKey+1)+'.png',
				    	/*'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_white'+(secKey+1)+'.png',*/
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_yellow'+(secKey+1)+'.png'
						];
						var colorSize = colorURL.length;
						var icon = colorURL[key%colorSize];

						// Info Window Content
					    var infoWindowContent = [
					        ['<div class="info_content">' +
					        '<p><b>Order Code <span style="padding-left:28px;">: </b>' + markers[i][0] + '</span></p>' +
					        '<p><b>Vehicle Number : </b>' + markers[i][1] + ' </p>'+ 
					        '<p><b>Beat <span style="padding-left:67px;">: </b>'+ markers[i][2] +' </span></p>' +
					        '<p><b>Crate Number <span style="padding-left:12px;">: </b>'+ markers[i][6] +' </span></p>' +
					        '<p><b>#Crate <span style="padding-left:54px;">: </b>'+ markers[i][7] +' </span></p>' +
					        '<p><b>#Bags <span style="padding-left:56px;">: </b>'+ markers[i][16] +' </span></p>' +
					        '<p><b>#Cartons <span style="padding-left:39px;">: </b>'+ markers[i][17] +' </span></p>' +
					        '<p><b>Invoice Amount <span style="padding-left:1px;">: </b>'+ markers[i][18] +' </span></p>' +
					        '<p><b>Shop Name <span style="padding-left:25px;">: </b>'+ markers[i][12] +' </span></p>' +
					        '<p><b>address <span style="padding-left:46px;">: </b>'+ markers[i][13] + ', ' + markers[i][14] +', '+ markers[i][15] +' </span></p>'
					        +'</div>']
					    ];
						
					    $scope.selectedMarker = [];
						marker = new google.maps.Marker({
							position: new google.maps.LatLng(markers[i][3], markers[i][4]),
							key:markers[i][5],
				        	id:markers[i][11],
				            map: map,
				            title: markers[i][2],
				            icon: icon
						});
						var tempIcon = icon.split("_")[1].match(/[a-zA-Z]+|[0-9]+/g)[0];
						$scope.markersColorData.push({iconValue:tempIcon, id:'Route No: '+value.route_number});
						

						// Allow each marker to have an info window    
				        google.maps.event.addListener(marker, 'click', (function(marker, i) {
				            return function() {
				                infoWindow.setContent(infoWindowContent[i][0]);
				                infoWindow.open(map, marker);
				            }
				        })(marker, i));
				        $scope.selectedMarker = {};
				        google.maps.event.addListener(marker, 'rightclick', (function (marker, i) {
					       return function() {
				                //marker.setIcon('/img/google_markers/marker-check.png');
				                $rootScope.markerCick = marker;
						        $scope.assignToKeyPair = [];
						      	angular.forEach($scope.markersDataMain, function(valueSplice, keySplice){
					    			if($rootScope.markerCick.key != keySplice){
					    				$scope.assignToKey = {'vehRoute': valueSplice.route_number, 'vehId':keySplice};
										$scope.assignToKeyPair.push($scope.assignToKey);
									}else{
										angular.forEach(valueSplice.coordinates_data, function(filteredValue, filteredKey){
											if(marker.id == filteredValue.order_id){
												$scope.selectedMarker['"'+filteredValue.order_id+'"'] = {
														'vehRoute':valueSplice.route_number,
														'beatName' : marker.title,
														'crates_count' : filteredValue.coordinates.crates_info.crates_count,
														'order_code' : marker.id,
														'vehId': keySplice,
														'bag_count': filteredValue.coordinates.other_info[0].bag_count,
														'cfc_count': filteredValue.coordinates.other_info[0].cfc_count
												} ;
											}
										});
									}
								});
								$scope.$apply();
				            }
					    })(marker, i));
				    }
				});
			});
	    } 
	    $scope.$on('changeRouteMArker', function(events, args){
		        $scope.markersDataMain = args;
		        $scope.markersDataMain['hub_coordinates'] = {lat:$rootScope.hubLatDup, long:$rootScope.hubLongDup};
		        $scope.completeMAp();
		})
		
	    $scope.assignRoute = function(i){
	    	$scope.route_admin_id = window.location.search.substring(1);
        	$scope.route_admin_id = $scope.route_admin_id.split("=")[1];
        	angular.forEach($scope.selectedMarker, function(assignRouteValue,assignRouteKey){
        		$scope.assignedData = {
					'assignTo' : parseInt(i),
					'assignFrom' : parseInt(assignRouteValue.vehId),
					'assignFromOrderId' : assignRouteValue.order_code,
					'route_admin_id' : $scope.route_admin_id
				};
				var res = $http.post('/routingadmin/changeorderfromroute', $scope.assignedData);
				res.success(function(data, status, headers, config) {
					$scope.message = data.data;
					$rootScope.$broadcast('changeRouteMArker', data.data);
				});
				res.error(function(data, status, headers, config) {
					alert( "failure message: " + JSON.stringify({data: data}));
				});
	        });
			
		}
    });
</script>
@stop
@extends('layouts.footer')
