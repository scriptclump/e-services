@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.1/lodash.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
.page-content {
    height: 600px;
}
#map_wrapper {
    height: 400px;
}
#map_canvas {
    width: 97.5%;
    height: 450px;
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
<div ng-app="mapRouting" ng-controller="AllOrdersSockRoutingCtlr" ng-init="loadAllOrdersSock()">
	<div class="row dropdownSet">
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>DC</option>
				<option>All</option>
			</select>
		</div>
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>HUB</option>
				<option>All</option>
			</select>
		</div>
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>Beat</option>
				<option>All</option>
			</select>
		</div>
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>Field Force</option>
				<option>All</option>
			</select>
		</div>
	</div>
	<div class="row dropdownSet">
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>Class</option>
				<option>All</option>
			</select>
		</div>
		<div class="col-md-3">
			<select class="dropdownSelect">
				<option>Order Status</option>
				<option>All</option>
			</select>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<div class='input-group date' id='datetimepicker6' >
					<input type='text' class="form-control" style="    border: 1px solid #aaa;" placeholder="Date" name="from_date" ng-model="date" / >
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<!-- <div class="form-group">
				<div class='input-group date' id='datetimepicker7' >
					<input type='text' class="form-control" placeholder="To Date" name="to_date" ng-model="date"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
				</div>
			</div> -->
			<button type="button" style="width: 49%;" class="btn btn-success">Submit</button>
			<button type="button" style="width: 49%;float: right;" class="btn btn-primary">Reset</button>
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
	/*app.controller('AllOrdersSockRoutingCtlr', function ($scope,$http,$rootScope, $location) {

		//$scope.orderMapViewData = angular.fromJson(<?php echo $data; exit; ?>);
		//console.log($scope.orderMapViewData);
		$scope.allOrderData = [{
			'order_id' : 9941,
			'order_Code' : "TSSO17010009904",
			'beat_id': 1,
			'beat_name' : "ramantapur",
			'shop_name' : "Anjan Kirana Store",
			'address' : {
				'customer_id' : 1,
				'address' : "ramantapur, uppal road",
				'pin' : 500001,
				'city' : "hyderabad"
			},
			'lat':17.431145,
			'long':78.538681,
			'status_id' : 1,
			'status_code' : "pending",
			'order_date' : '2017-05-02'
		},{
			'order_id' : 9921,
			'order_Code' : "TSSO17010009884",
			'beat_id': 2,
			'beat_name' : "nacharam",
			'shop_name' : "sai Kirana Store",
			'address' : {
				'customer_id' : 2,
				'address' : "nacharam,near uppal road",
				'pin' : 500001,
				'city' : "hyderabad"
			},
			'lat':17.428289,
			'long':78.539673,
			'status_id' : 1,
			'status_code' : "pending",
			'order_date' : '2017-05-03'	
		},{
			'order_id' : 9918,
			'order_Code' : "TSSO17010009881",
			'beat_id': 3,
			'beat_name' : "alwal",
			'shop_name' : "sai krishna Kirana Store",
			'address' : {
				'customer_id' : 3,
				'address' : "alwal",p
				'pin' : 500021,
				'city' : "hyderabad"
			},
			'lat':17.427349,
			'long':78.539703,
			'status_id' : 2,
			'status_code' : "delivered",
			'order_date' : '2017-05-01'
		},{
			'order_id' : 9940,
			'order_Code' : "TSSO17010009903",
			'beat_id': 3,
			'beat_name' : "alwal",
			'shop_name' : "sai krishna Store",
			'address' : {
				'customer_id' : 3,
				'address' : "alwal",
				'pin' : 500021,
				'city' : "hyderabad"
			},
			'lat':17.429453,
			'long':78.541725,
			'status_id' : 2,
			'status_code' : "delivered",
			'order_date' : '2017-05-01'
		}];


		$scope.loadAllOrdersSock = function(){

			var map;
		    var bounds = new google.maps.LatLngBounds();
	    	var mapOptions = {
	        	mapTypeId: 'roadmap'
	    	};
	                    
	    	// Display a map on the page
	    	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	    	map.setTilt(45);

	    	angular.forEach($scope.allOrderData, function(value, key){
	    		if(value.beat_id){
	    			var markers = [[value.shop_name,value.status_code,value.order_date,value.beat_name,value.order_id,value.order_Code,value.lat,value.long]];
	    		}
	    		// Display multiple markers on a map
    				var infoWindow = new google.maps.InfoWindow(), marker, i;

				    for(i = 0;i<markers.length;i++){
				    	var colorURL = [
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_green'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_grey'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_orange'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_purple'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_white'+value.beat_id+'.png',
				    	'https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_yellow'+value.beat_id+'.png'
						];
					var colorSize = colorURL.length;
					var icon = colorURL[key%colorSize];

					var position = new google.maps.LatLng(markers[i][6], markers[i][7]);
				        bounds.extend(position);
				        marker = new google.maps.Marker({
				            position: position,
				            map: map,
				            title: markers[i][0],
				            icon: icon
				        });

				    // Automatically center the map fitting all markers on the screen
				        map.fitBounds(bounds);
				    }
				    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
				    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
				        this.setZoom(12);
				        google.maps.event.removeListener(boundsListener);
				    });
	    	});
		}
	});*/
</script>
@stop
@extends('layouts.footer')
