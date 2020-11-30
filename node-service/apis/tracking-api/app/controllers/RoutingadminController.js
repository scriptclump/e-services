/**
 * RoutingadminController
 *
 * @description :: Server-side logic for managing routingadmins
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */

module.exports = {

	index:function(req,res){

		return res.view('map/mapview');
	},

	receiveOrdersList:function(req,res){
		if (req.method == 'GET') {
			return res.json(405,'Not allowed request type');
		}

		var data = req.body;
		if(data.order_ids){
			var datasend = [];
			datasend['order_ids'] = data.order_ids;
			
			Routeadmin.getOrderCoordinates(datasend,function(result){

				ret = result;
				return res.json(200,ret);
		 	});

		}
	},

	caclulateHubtoDesAll:function(req,res, ret,cb){
		var async = require('async');
		if(req.method == 'GET'){

			return res.json(405,'Not allowed request type');
		}

		var data = req.body;
		var hubcoordinates = data.hubcoordinates;
		var orderscoordinates = data.orderscoordinates;
		//console.log(orderscoordinates);
		var calls = [];

		for(var i= 0; i < orderscoordinates.length; i++){
		        
		        var rp = require("request-promise");
		    	var destination = orderscoordinates[i].lat+','+orderscoordinates[i].long;
		    	var origin = hubcoordinates.lat+','+hubcoordinates.long;
				var options = { method: 'GET',
					url: 'https://maps.googleapis.com/maps/api/distancematrix/json',
					qs: 
						{ 	units: 'metric',
							origins: origin,
							destinations: destination,
							mode : 'driving', 
							key: 'AIzaSyBWlWya2qMdyq3vy1NfvDMs7bdnv5Ihex4' 
							//key : 'AIzaSyCpMRKb4BAuuazrFEWFgwVg2XwbmK1DY7A'
							//key : 'AIzaSyAVAEYc7taRfr4zbOPePB_DWmMOYetYLfs'//'AIzaSyBTSlofs2oeBxpC4XlBAnRysJMEzhqdBGU' //shekhar unlimited
						},
						json: true
					};

				calls.push(rp(options));
		}

		var distanceData = [];
		var temp = {};
		Promise.all(calls)
		    .then((results) => {
		    	console.log({result: results});
		    	var lowestDistance = 0;
				for(var i= 0; i < results.length ; i++){

					if(lowestDistance == 0){

						if(results[i]['rows'][0]['elements'][0]['status'] == 'ZERO_RESULTS'){
							var value = 0;
						}else{
							var value = results[i]['rows'][0]['elements'][0]['distance']['value'];
						}
						distanceData[i] = {	
								coordinates: orderscoordinates[i], 
								distance : value,//results[i]['rows'][0]['elements'][0]['distance']['value'],
								order_id : orderscoordinates[i]['gds_order_id']
								//,
								//time : results[i]['rows'][0]['elements'][0]['time']['value']	
						};
					}
				}
				if(ret == 1){
					cb(distanceData);
				}else{
					return res.json(200,distanceData);
				}
				
		    }).catch(err => console.log(err));  // First rejected promise

	},

	calculateDistance:function(lat1, lon1, lat2, lon2, unit) {
		console.log('SSSSSSS');
		if(lat1 == lat2 && lon1 == lon2){
					console.log('oopsoopsoopsoopsoops');

			return 0;
		}
		console.log(lat1,lon1,lat2,lon2);

        var radlat1 = Math.PI * lat1/180
        var radlat2 = Math.PI * lat2/180
        var radlon1 = Math.PI * lon1/180
        var radlon2 = Math.PI * lon2/180
        var theta = lon1-lon2
        var radtheta = Math.PI * theta/180
        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
        dist = Math.acos(dist)
        dist = dist * 180/Math.PI
        dist = dist * 60 * 1.1515
        if (unit=="K") { dist = dist * 1.609344 }
        if (unit=="N") { dist = dist * 0.8684 }
        return dist
    },

    calculateDegree:function(lat1, lon1, lat2, lon2){

    	deltaX = lat2 - lat1;
		deltaY = lon2 - lon1;
		deg = Math.atan2(deltaY, deltaX)*180.0/Math.PI;

		if (deg < 0) deg = 360 + deg; // range [0, 360)
		return deg;
    },

    googledistanceMatrix:function(lat1, lon1, lat2, lon2,mode, cb){

    	var request = require("request");
    	var destination = lat2+','+lon2;
    	var origin = lat1+','+lon1;
		var options = { method: 'GET',
			url: 'https://maps.googleapis.com/maps/api/distancematrix/json',
			qs: 
			{ 	units: 'metric',
				origins: origin,
				destinations: destination,
				mode : mode, 
				//key: 'AIzaSyBWlWya2qMdyq3vy1NfvDMs7bdnv5Ihex4'
				key : 'AIzaSyBTSlofs2oeBxpC4XlBAnRysJMEzhqdBGU'
				},
			};

			request(options, function (error, response, body) {
				console.log(body);
			  if (error) throw new Error(error);
			  	cb(body);
			});			
    },
    distanceApiGoogleMaps:function(lat1,lon1,lat2,lon2,mode,cb){

    	var key ='AIzaSyBWlWya2qMdyq3vy1NfvDMs7bdnv5Ihex4';
    	var googleMapsClient = require('@google/maps').createClient({
  												key: key
							    });
    	var origin = "'"+lat1+','+lon1+"'";
    	var destination = "'"+lat2+','+lon2+"'";
    	// Geocode an address.
		googleMapsClient.distanceMatrix({
		  	origins : ['17.403105,78.539825'],
		  	destinations : ['17.402206,78.545746'],
		  	mode : mode,
		  	units : 'metric'
		}, function(err, response) {
		  if (!err) {
		  	console.log(response);
		    cb(response);
		  }else{
		  	console.log(err);
		  }
		});
    },

    getDistance:function(req,res){

    	var mode = 'driving';
    	if(req.method == 'GET'){

			return res.json(405,'Not allowed request type');
		}
    	var req_data = req.body;
    	if(!req_data.hub.latitude || !req_data.hub.longitude || !req_data.outlet.latitude || !req_data.outlet.longitude){
    		return res.json(400,'Bad request!!');
    	}else{
    		module.exports.distanceApiGoogleMaps(req_data.hub.latitude,req_data.hub.longitude,req_data.outlet.latitude,req_data.outlet.longitude,mode,function(distance){
    			return res.json(200,distance);
    		});
    	}
    	
    },
    ////////////////////////////////////////////////Centroid Calculation///////////////////////////////////////////////
    /**
     * [rad2degr description]
     * @param  {[type]} rad [description]
     * @return {[type]}     [description]
     */
    rad2degr:function(rad) { 
    	return rad * 180/Math.PI; 
    },
	
	degr2rad:function(degr) { 
		return degr * Math.PI/180; 
	},

    getLatLngCenter:function(latLngInDegr) {
	    var LATIDX = 0;
	    var LNGIDX = 1;
	    var sumX = 0;
	    var sumY = 0;
	    var sumZ = 0;

	    for (var i=0; i<latLngInDegr.length; i++) {
	        var lat = module.exports.degr2rad(latLngInDegr[i][LATIDX]);
	        var lng = module.exports.degr2rad(latLngInDegr[i][LNGIDX]);
	        // sum of cartesian coordinates
	        sumX += Math.cos(lat) * Math.cos(lng);
	        sumY += Math.cos(lat) * Math.sin(lng);
	        sumZ += Math.sin(lat);
	    }

	    var avgX = sumX / latLngInDegr.length;
	    var avgY = sumY / latLngInDegr.length;
	    var avgZ = sumZ / latLngInDegr.length;

	    // convert average x, y, z coordinate to latitude and longtitude
	    var lng = Math.atan2(avgY, avgX);
	    var hyp = Math.sqrt(avgX * avgX + avgY * avgY);
	    var lat = Math.atan2(avgZ, hyp);
    	return ([module.exports.rad2degr(lat), module.exports.rad2degr(lng)]);
	},
    ////////////////////////////////////////////////Centroid Calculation///////////////////////////////////////////////

    /**
     * [splitloadOnLocation description]
     * @param  {[type]} req [description]
     * @param  {[type]} res [description]
     * @return {[type]}     [description]
     * {	
	"vehicles_details" : [
		
		{
			"vehicle_number" : "TS1",
			"vehicle_max_load" : 500
		}
	],
	
	"hubcoordinates" : {
		"lat": "17.403105",
		"long":"78.539825"
	},
	"orderscoordinates": [
		
		{
			"lat":"17.402206",
			"long":"78.545746",
			"weight" : 20

		}
	]
	}
     */
    
    splitloadOnLocation:function(req,res){
    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

				//console.log(req.body.orderscoordinates.length);
				var orderMapHolder = [];
				var coordinates_cluster = [];
				var kmeanCluster = [];
				for(var i=0;i<req.body.orderscoordinates.length;i++){

					orderMapHolder[i] = String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']);
					var temp = [];
					temp.push(req.body.orderscoordinates[i]['lat']);
					temp.push(req.body.orderscoordinates[i]['long']);
					coordinates_cluster.push(temp);
					var tempData = { 
										x: parseFloat(req.body.orderscoordinates[i]['lat']),
										y: parseFloat(req.body.orderscoordinates[i]['long']), 
										data: String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long'])
									};
					kmeanCluster.push(tempData);
				}

				var kmeans = require('kmeans-node');
				var kmeanClusterDivided = kmeans.object(kmeanCluster,req_data.vehicles_details.length);

				var hubCoordinates =  req.body.hubcoordinates;
				for(var i=0;i < kmeanClusterDivided.length;i++){
					
					var angle = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'], kmeanClusterDivided[i]['x'],kmeanClusterDivided[i]['y'], 'K')
					kmeanClusterDivided[i]['angle'] = angle;
				}

				kmeanClusterDivided.sort(function(a, b) {
						return a.angle - b.angle;
					});

				console.log(kmeanClusterDivided);
				var vehicles_details = req.body.vehicles_details;
				var vehicles_count = vehicles_details.length;
				var vehicle_assets = {};
				for(var i=0;i<vehicles_count;i++){

					vehicles_details[i]['consignmentWeight'] = 0 ;
    				vehicle_assets[i] = {
											"coordinates_data" : [],
											"vehicleInfo" : vehicles_details[i]
    								    }	
				}

				cluster = [];
				console.log(cluster);
				
				// pulling all orders into a linear list of arrays
				var clusterStraight = [];

				for(var i=0;i<kmeanClusterDivided.length;i++){

					var loopcount = 0;
					while(loopcount <= (kmeanClusterDivided[i]['points'].length)-1){

						coordinatesCollection = kmeanClusterDivided[i]['points'][loopcount]['data'];
						loopcount++;
						var key = orderMapHolder.indexOf(coordinatesCollection);
						//console.log(key);
						delete orderMapHolder[key];
						//var temp = [];
						//
						var temp = {
							coordinates : req.body.orderscoordinates[key],
							distance : 0,
							order_id : req.body.orderscoordinates[key]['gds_order_id']

						}

						clusterStraight.push(temp);

					}

				}
				var looper = 0;

				for(var j=0;j<clusterStraight.length;j++){
					
					//console.log(looper);
					var consignmentWeight = parseFloat(vehicle_assets[looper]['vehicleInfo']['consignmentWeight']);
					var order_weight = parseFloat(clusterStraight[j]['coordinates']['weight']);
					var cur_weight = consignmentWeight+order_weight;
					var vehicle_max_load = parseFloat(vehicle_assets[looper]['vehicleInfo']['vehicle_max_load']);

					//console.log(vehicle_assets[looper]);
					if(cur_weight <= vehicle_max_load){

						vehicle_assets[looper]['coordinates_data'].push(clusterStraight[j]);
						console.log(clusterStraight.length);
						delete clusterStraight[j];				
						vehicle_assets[looper]['vehicleInfo']['consignmentWeight'] = cur_weight;
					}else{

						looper = looper+1;
						if(vehicle_assets[looper]){							
							j--;	
						}else{
							break;
						}
						
					}
				}

				vehicle_assets['unassigned_coordinates'] = [];
				
				if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							vehicle_assets['unassigned_coordinates'].push(unassigned[i]);
						}

				}

				var newArray = [];
				for (var i = 0; i < clusterStraight.length; i++) {
				    if (clusterStraight[i]) {
				      newArray.push(clusterStraight[i]);
				    }else{
				    }
				}

				clusterStraight = newArray;
				for (var i = 0; i < clusterStraight.length; i++) {

					vehicle_assets['unassigned_coordinates'].push(clusterStraight[i]);

				}


				
				
				console.log(vehicle_assets);
				// now see this key does not exist in the vehicle asset 
				// after we push it to the response 
				return res.ok(vehicle_assets);
				
	   		}
    	}
    },
    //ClusterPackageNode
    splitloadOnLocationClusterPackageNode:function(req,res){

    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

				var orderMapHolder = [];
				var coordinates_cluster = [];
				for(var i=0;i<req.body.orderscoordinates.length;i++){

					orderMapHolder[i] = String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']);
					var temp = [];
					temp.push(parseFloat(req.body.orderscoordinates[i]['lat']));
					temp.push(parseFloat(req.body.orderscoordinates[i]['long']));
					coordinates_cluster.push(temp);
				}

				// console.log(orderMapHolder);
				// console.log(coordinates_cluster);


				var clusterMaker = require('clusters');
				//number of clusters, defaults to undefined 
				clusterMaker.k(req_data.vehicles_details.length); // arrange according to the clusters

				//number of iterations (higher number gives more time to converge), defaults to 1000 
				clusterMaker.iterations(700);
				 
				//data from which to identify clusters, defaults to [] 
				//clusterMaker.data([[1, 0], [0, 1], [0, 0], [-10, 10], [-9, 11], [10, 10], [11, 12]]);
				clusterMaker.data(coordinates_cluster);
				 
				var cluster = clusterMaker.clusters();
				var clusterHolders = [];
				var hubCoordinates =  req.body.hubcoordinates;
				var clusterSortChecker = [];

				//finding the angle of the cluster from the hub
				//ths will help us move clock wise or anticlock wise
				//trial basis degree calculation in 360 degree
				for(var i=0;i < cluster.length;i++){					
					cluster[i]['angle'] = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'], cluster[i]['centroid'][0],cluster[i]['centroid'][1], 'K');
				}

				//console.log(cluster);

				//sort cluster according to the angle 
				//we recived it will save the orders list in a constant level
				cluster.sort(function(a, b) {
						return a.angle - b.angle;
					});

				var vehicles_details = req.body.vehicles_details;
				var vehicles_count = vehicles_details.length;
				var vehicle_assets = {};
				for(var i=0;i<vehicles_count;i++){

					vehicles_details[i]['consignmentWeight'] = 0 ;
    				vehicle_assets[i] = {
											"coordinates_data" : [],
											"vehicleInfo" : vehicles_details[i]
    								    }	
				}
				console.log(orderMapHolder.length);
				var clusterStraight = [];
				for(var i=0;i < cluster.length;i++){
					
					console.log(cluster[i]['points'].length);
					// console.log(cluster[i]);
					for(var j=0;j<cluster[i]['points'].length;j++){


						var key = String(cluster[i]['points'][j][0]) + String(cluster[i]['points'][j][1]);
						var key = orderMapHolder.indexOf(key);
						
						if(key > -1){
							var temp = {
									coordinates : req.body.orderscoordinates[key],
									distance : 0,
									order_id : req.body.orderscoordinates[key]['gds_order_id']

							}
							clusterStraight.push(temp);
						}
						
					}
				}
				

				var looper = 0;

				for(var j=0;j<clusterStraight.length;j++){
					
					//console.log(looper);
					var consignmentWeight = parseFloat(vehicle_assets[looper]['vehicleInfo']['consignmentWeight']);
					var order_weight = parseFloat(clusterStraight[j]['coordinates']['weight']);
					var cur_weight = consignmentWeight+order_weight;
					var vehicle_max_load = parseFloat(vehicle_assets[looper]['vehicleInfo']['vehicle_max_load']);

					//console.log(vehicle_assets[looper]);
					if(cur_weight <= vehicle_max_load){

						vehicle_assets[looper]['coordinates_data'].push(clusterStraight[j]);
						console.log(clusterStraight.length);
						delete clusterStraight[j];				
						vehicle_assets[looper]['vehicleInfo']['consignmentWeight'] = cur_weight;
					}else{

						looper = looper+1;
						if(vehicle_assets[looper]){
							j--;
						}
						
					}
				}

				vehicle_assets['unassigned_coordinates'] = [];
				
				if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							vehicle_assets['unassigned_coordinates'].push(unassigned[i]);
						}

				}

				var newArray = [];
				for (var i = 0; i < clusterStraight.length; i++) {
				    if (clusterStraight[i]) {
				      newArray.push(clusterStraight[i]);
				    }else{
				    }
				}

				clusterStraight = newArray;
				for (var i = 0; i < clusterStraight.length; i++) {

					vehicle_assets['unassigned_coordinates'].push(clusterStraight[i]);

				}


				
				
				console.log(vehicle_assets);
				// // now see this key does not exist in the vehicle asset 
				// // after we push it to the response 
				return res.ok(vehicle_assets);
    		}
    	}

    },

     //Kmeans Original Package
    splitloadOnLocationOnOrderCountBaseBeat:function(req,res){
    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

				var hubCoordinates =  req.body.hubcoordinates;
				var ordersCount = req.body.orderscoordinates.length;
				var loadCount = req.body.order_count; //30
				
				var clusterCount = Math.ceil(parseFloat(ordersCount/loadCount));
				console.log(clusterCount);
				if(clusterCount == 1){
					var clusterCount = 2;
				}
				//console.log(req.body.orderscoordinates.length);
				var orderMapHolder = [];
				var coordinates_cluster = [];
				var kmeanCluster = [];

				for(var i=0;i<req.body.orderscoordinates.length;i++){

					orderMapHolder[i] = String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']);
					var temp = [];
					temp.push(req.body.orderscoordinates[i]['lat']);
					temp.push(req.body.orderscoordinates[i]['long']);
					var angleToHub = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'],req.body.orderscoordinates[i]['lat'],req.body.orderscoordinates[i]['long'],'K');
					coordinates_cluster.push(temp);
					var tempData = { 
										x: parseFloat(req.body.orderscoordinates[i]['lat']),
										y: parseFloat(req.body.orderscoordinates[i]['long']), 
										data: String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']),
										angle : angleToHub,
										beat : req.body.orderscoordinates[i]['beat']
									};

					kmeanCluster.push(tempData);
				}

				kmeanCluster.sort(function(a, b) {
						return a.beat - b.beat;
				});

				/** @ [clustering on beat] */
				var beatCluster = [];
				for(var i=0;i<kmeanCluster.length;i++){

					var beat = kmeanCluster[i]['beat'];
					if(!(beat in beatCluster)){

						beatCluster[beat] = []; 
						beatCluster[beat].coordinates = [];
						beatCluster[beat].centroid = '';
						beatCluster[beat].angle = 0;
					}
					beatCluster[beat].coordinates.push(kmeanCluster[i]);


				}

				for (var key in beatCluster) {

					var coordinateslist = [];

						for(var j=0;j<beatCluster[key].coordinates.length;j++){

							var tempcoords = [beatCluster[key].coordinates[j].x,beatCluster[key].coordinates[j].y];
							coordinateslist.push(tempcoords);
						}
						var centroid = module.exports.getLatLngCenter(coordinateslist);
						beatCluster[key].centroid = centroid;				
						beatCluster[key].angle = module.exports.calculateDegree(hubCoordinates['lat'],hubCoordinates['long'],centroid[0],centroid[1],'K');
				}

				beatClusterHolder = [];// without the ket=ys on beat
				for(var key in beatCluster){

					beatClusterHolder.push(beatCluster[key]);

				}

				//arrange the cluster in angle
				beatClusterHolder.sort(function(a, b) {
						return a.angle - b.angle;
				});
				
				// sorting each beat orders in angle
				for(var i=0;i<beatClusterHolder.length;i++){

					beatClusterHolder[i].sort(function(a,b){
						return a.angle-b.angle;
					});
				}

				var clusterStraightTemp = [];

				for(var i=0;i<beatClusterHolder.length;i++){

					var loopcount = 0;
					while(loopcount <= (beatClusterHolder[i]['coordinates'].length)-1){

						var coordinatesCollection = beatClusterHolder[i]['coordinates'][loopcount];
						loopcount++;
						clusterStraightTemp.push(coordinatesCollection);

					}

				}

				console.log(clusterStraightTemp);

			//pulling all orders into a linear list of arrays
				
				var clusterStraight = [];

				for(var i=0;i<clusterStraightTemp.length;i++){

					coordinatesCollection = clusterStraightTemp[i]['data'];
					var key = orderMapHolder.indexOf(coordinatesCollection);

						if(key > -1){
							delete orderMapHolder[key];	
							var temp = {
									coordinates : req.body.orderscoordinates[key],
									distance : 0,
									order_id : req.body.orderscoordinates[key]['gds_order_id']

							}
							clusterStraight.push(temp);
						}
				}
					console.log('==========');
					console.log(clusterStraight);		
					console.log('==========');	


			// 	console.log(clusterStraight.length);

				var vehicles_details = req.body.vehicles_details;
				var vehicles_count = vehicles_details.length;
				var vehicles_actual_count = vehicles_count;
				var vehicle_assets = {};
				console.log('count count ' + vehicles_count);
				if(vehicles_count < clusterCount){

					vehicles_count = clusterCount;

				}

				console.log('cluster count ' + clusterCount);
				console.log('new vehicle count '+vehicles_count);
				for(var i=0;i < vehicles_count;i++){
				
					//console.log(vehicles_details[i]);
					if(typeof vehicles_details[i] == 'undefined'){
						var temp = {
							'vehicle_number' : 'vehicle_route_'+(i+1),
        					'vehicle_max_load' : vehicles_details[0]['vehicle_max_load'],
        					'esu_time' : vehicles_details[0]['esu_time']
						};
						vehicles_details.push(temp);
					}else{

						vehicles_details[i]['vehicle_number'] = 'vehicle_route_'+(i+1);
					}
					//making the vehicle data as zero
					vehicles_details[i]['vehicle_id'] = 0;
					vehicles_details[i]['consignmentWeight'] = 0 ;
					vehicles_details[i]['orderCount'] = 0 ;
    				vehicle_assets[i] = {
											"coordinates_data" : [],
											"vehicleInfo" : vehicles_details[i],
    								    }

				}
				

				console.log('cluster lenght' + clusterStraight.length);
				var looper = 0;

				for(var j=0;j<clusterStraight.length;j++){
					
					// console.log(looper);
					//console.log(vehicle_assets[looper]);
					var consignmentWeight = parseFloat(vehicle_assets[looper]['vehicleInfo']['consignmentWeight']);
					var order_weight = parseFloat(clusterStraight[j]['coordinates']['weight']);
					var cur_weight = consignmentWeight+order_weight;
					var vehicle_max_load = parseFloat(vehicle_assets[looper]['vehicleInfo']['vehicle_max_load']);
					var cur_count = vehicle_assets[looper]['vehicleInfo']['orderCount'];
					// console.log('cur_count');
					// console.log(cur_count);
					// 30
					if(cur_count < loadCount && cur_weight < vehicle_max_load){

						vehicle_assets[looper]['coordinates_data'].push(clusterStraight[j]);
						delete clusterStraight[j];				
						vehicle_assets[looper]['vehicleInfo']['consignmentWeight'] = cur_weight;
						vehicle_assets[looper]['vehicleInfo']['orderCount'] += 1;
					}else{

						looper = looper+1;
						if(vehicle_assets[looper]){
							j--;
						}else{

							console.log('breaking it at' + looper);
							break;
						}
						
					}
				}
				
				for (var j in vehicle_assets) {
					
 	 				console.log(vehicle_assets[j]['coordinates_data'].length);
					if(vehicle_assets[j]['coordinates_data'].length == 0){
						console.log('deleting vehicle_assets ' + j);
						delete vehicle_assets[j];
					}
				}
				//console.log(vehicle_assets);
				var vehicles_count = Object.keys(vehicle_assets).length;
				//console.log(vehicles_count - vehicles_actual_count);

				if(vehicles_count > vehicles_actual_count){
					vehicle_assets['extra_vehicles']= [];
					for (var i = (vehicles_actual_count); i < vehicles_count; i++) {
						vehicle_assets['extra_vehicles'].push(vehicle_assets[i]['vehicleInfo']);
					}
				}
				

				vehicle_assets['unassigned_coordinates'] = {};
				vehicle_assets['unassigned_coordinates']['coordinates_data'] = {};
				//var temp_cordinates_data = [];
				var keyCount = 0;
				if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							vehicle_assets['unassigned_coordinates']['coordinates_data'][keyCount] = unassigned[i];
							keyCount++;
						}


				}


				var newArray = [];
				for (var i = 0; i < clusterStraight.length; i++) {
				    if (clusterStraight[i]) {
				      newArray.push(clusterStraight[i]);
				    }else{
				    }
				}

				clusterStraight = newArray;
				for (var i = 0; i < clusterStraight.length; i++) {

					vehicle_assets['unassigned_coordinates']['coordinates_data'][keyCount] = clusterStraight[i];
					keyCount++;

				}
				
				

				//vehicle_assets['unassigned_coordinates']['coordinates_data'].push(temp_cordinates_data);
				
				//console.log(vehicle_assets);
				// // now see this key does not exist in the vehicle asset 
				// // after we push it to the response
				let result= {status:true,data:vehicle_assets};
				res.send(result);
				//return res.send(vehicle_assets);
	
			}
     	}

    },


    //Kmeans Original Package
    splitloadOnLocationOnOrderCount:function(req,res){

    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

				var hubCoordinates =  req.body.hubcoordinates;
				var ordersCount = req.body.orderscoordinates.length;
				var loadCount = req.body.order_count; //30
				
				var clusterCount = Math.ceil(parseFloat(ordersCount/loadCount));
				console.log(clusterCount);
				if(clusterCount == 1){
					var clusterCount = 2;
				}
				//console.log(req.body.orderscoordinates.length);
				var orderMapHolder = [];
				var coordinates_cluster = [];
				var kmeanCluster = [];
				
				for(var i=0;i<req.body.orderscoordinates.length;i++){

					orderMapHolder[i] = String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']);
					var temp = [];
					temp.push(req.body.orderscoordinates[i]['lat']);
					temp.push(req.body.orderscoordinates[i]['long']);
					var angleToHub = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'],req.body.orderscoordinates[i]['lat'],req.body.orderscoordinates[i]['long'],'K');
					coordinates_cluster.push(temp);
					var tempData = { 
										x: parseFloat(req.body.orderscoordinates[i]['lat']),
										y: parseFloat(req.body.orderscoordinates[i]['long']), 
										data: String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']),
										angle : angleToHub
									};

					kmeanCluster.push(tempData);
				}

				var kmeans = require('kmeans-node');
				var kmeanClusterDivided = kmeans.object(kmeanCluster,clusterCount);
			
				for(var i=0;i < kmeanClusterDivided.length;i++){
					
					var angle = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'], kmeanClusterDivided[i]['x'],kmeanClusterDivided[i]['y'], 'K')
					kmeanClusterDivided[i]['angle'];
				}

				kmeanClusterDivided.sort(function(a, b) {
						return a.angle - b.angle;
				});


				console.log(kmeanClusterDivided[0]['points']);

				for(var i=0;i<kmeanClusterDivided.length;i++){
					kmeanClusterDivided[i]['points'].sort(function(a, b){
						return a.angle - b.angle;
					});
				}

				var clusterStraightTemp = [];

				for(var i=0;i<kmeanClusterDivided.length;i++){

					var loopcount = 0;
					while(loopcount <= (kmeanClusterDivided[i]['points'].length)-1){

						var coordinatesCollection = kmeanClusterDivided[i]['points'][loopcount];
						loopcount++;
						clusterStraightTemp.push(coordinatesCollection);

					}

				}

				clusterStraightTemp.sort(function(a, b){
						return a.angle - b.angle;
				});

				console.log(clusterStraightTemp.length);

				//pulling all orders into a linear list of arrays
				
				var clusterStraight = [];

				for(var i=0;i<clusterStraightTemp.length;i++){

					coordinatesCollection = clusterStraightTemp[i]['data'];
					var key = orderMapHolder.indexOf(coordinatesCollection);				
						if(key > -1){
							delete orderMapHolder[key];	
							var temp = {
									coordinates : req.body.orderscoordinates[key],
									distance : 0,
									order_id : req.body.orderscoordinates[key]['gds_order_id']

							}
							clusterStraight.push(temp);
						}
				}



				console.log(clusterStraight.length);

				var vehicles_details = req.body.vehicles_details;
				var vehicles_count = vehicles_details.length;
				var vehicles_actual_count = vehicles_count;
				var vehicle_assets = {};
				console.log('count count ' + vehicles_count);
				if(vehicles_count < clusterCount){

					vehicles_count = clusterCount;

				}

				console.log('cluster count ' + clusterCount);
				console.log('new vehicle count '+vehicles_count);
				for(var i=0;i < vehicles_count;i++){
				
					//console.log(vehicles_details[i]);
					if(typeof vehicles_details[i] == 'undefined'){
						var temp = {
							'vehicle_number' : 'vehicle_route_'+(i+1),
        					'vehicle_max_load' : vehicles_details[0]['vehicle_max_load'],
        					'esu_time' : vehicles_details[0]['esu_time']
						};
						vehicles_details.push(temp);
					}else{

						vehicles_details[i]['vehicle_number'] = 'vehicle_route_'+(i+1);
					}
					//making the vehicle data as zero
					vehicles_details[i]['vehicle_id'] = 0;
					vehicles_details[i]['consignmentWeight'] = 0 ;
					vehicles_details[i]['orderCount'] = 0 ;
    				vehicle_assets[i] = {
											"coordinates_data" : [],
											"vehicleInfo" : vehicles_details[i],
    								    }

				}

				console.log(vehicle_assets);

				console.log('cluster lenght' + clusterStraight.length);
				var looper = 0;

				for(var j=0;j<clusterStraight.length;j++){
					
					// console.log(looper);
					//console.log(vehicle_assets[looper]);
					var consignmentWeight = parseFloat(vehicle_assets[looper]['vehicleInfo']['consignmentWeight']);
					var order_weight = parseFloat(clusterStraight[j]['coordinates']['weight']);
					var cur_weight = consignmentWeight+order_weight;
					var vehicle_max_load = parseFloat(vehicle_assets[looper]['vehicleInfo']['vehicle_max_load']);
					var cur_count = vehicle_assets[looper]['vehicleInfo']['orderCount'];
					// console.log('cur_count');
					// console.log(cur_count);
					// 30
					if(cur_count < loadCount && cur_weight < vehicle_max_load){
						vehicle_assets[looper]['coordinates_data'].push(clusterStraight[j]);
						delete clusterStraight[j];				
						vehicle_assets[looper]['vehicleInfo']['consignmentWeight'] = cur_weight;
						vehicle_assets[looper]['vehicleInfo']['orderCount'] += 1;
					}else{

						looper = looper+1;
						if(vehicle_assets[looper]){
							j--;
						}else{

							console.log('breaking it at' + looper);
							break;
						}
						
					}
				}
				
				//console.log(vehicle_assets);
				
				for (var j in vehicle_assets) {

 	 				console.log(vehicle_assets[j]['coordinates_data'].length);
					if(vehicle_assets[j]['coordinates_data'].length == 0){
						console.log('deleting vehicle_assets ' + j);
						delete vehicle_assets[j];
					}
				}
				//console.log(vehicle_assets);
				var vehicles_count = Object.keys(vehicle_assets).length;
				//console.log(vehicles_count - vehicles_actual_count);

				if(vehicles_count > vehicles_actual_count){
					vehicle_assets['extra_vehicles']= [];
					for (var i = (vehicles_actual_count); i < vehicles_count; i++) {
						vehicle_assets['extra_vehicles'].push(vehicle_assets[i]['vehicleInfo']);
					}
				}
				

				vehicle_assets['unassigned_coordinates'] = {};
				vehicle_assets['unassigned_coordinates']['coordinates_data'] = {};
				//var temp_cordinates_data = [];
				
				var keyCount = 0;
				if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							vehicle_assets['unassigned_coordinates']['coordinates_data'][keyCount] = unassigned[i];
							keyCount++;
						}


				}


				var newArray = [];
				for (var i = 0; i < clusterStraight.length; i++) {
				    if (clusterStraight[i]) {
				      newArray.push(clusterStraight[i]);
				    }else{
				    }
				}

				clusterStraight = newArray;
				for (var i = 0; i < clusterStraight.length; i++) {

					vehicle_assets['unassigned_coordinates']['coordinates_data'][keyCount] = clusterStraight[i];
					keyCount++;

				}

				//vehicle_assets['unassigned_coordinates']['coordinates_data'].push(temp_cordinates_data);
				
				console.log(vehicle_assets);
				// // now see this key does not exist in the vehicle asset 
				// // after we push it to the response
				
				return res.send(vehicle_assets);

			}
    	}

    },

    //ClusterPackageNode
    splitloadOnLocationOnOrderCount_ClusterPackageNode:function(req,res){

    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

				var orderMapHolder = [];
				var coordinates_cluster = [];
				for(var i=0;i<req.body.orderscoordinates.length;i++){

					orderMapHolder[i] = String(req.body.orderscoordinates[i]['lat'])+String(req.body.orderscoordinates[i]['long']);
					var temp = [];
					temp.push(parseFloat(req.body.orderscoordinates[i]['lat']));
					temp.push(parseFloat(req.body.orderscoordinates[i]['long']));
					coordinates_cluster.push(temp);
				}

				// console.log(orderMapHolder);
				// console.log(coordinates_cluster);

				var ordersCount = req.body.orderscoordinates.length;
				var loadCount = 35;
				var clusterCount = Math.ceil(parseFloat(ordersCount/loadCount));
				var clusterMaker = require('clusters');
				//number of clusters, defaults to undefined 
				clusterMaker.k(clusterCount); // arrange according to the clusters

				//number of iterations (higher number gives more time to converge), defaults to 1000 
				clusterMaker.iterations(700);
				 
				//data from which to identify clusters, defaults to [] 
				//clusterMaker.data([[1, 0], [0, 1], [0, 0], [-10, 10], [-9, 11], [10, 10], [11, 12]]);
				clusterMaker.data(coordinates_cluster);
				 
				var cluster = clusterMaker.clusters();
				var clusterHolders = [];
				var hubCoordinates =  req.body.hubcoordinates;
				var clusterSortChecker = [];

				//finding the angle of the cluster from the hub
				//ths will help us move clock wise or anticlock wise
				//trial basis degree calculation in 360 degree
				for(var i=0;i < cluster.length;i++){					
					cluster[i]['angle'] = module.exports.calculateDegree(hubCoordinates['lat'], hubCoordinates['long'], cluster[i]['centroid'][0],cluster[i]['centroid'][1], 'K');
				}

				//console.log(cluster);

				//sort cluster according to the angle 
				//we recived it will save the orders list in a constant level
				cluster.sort(function(a, b) {
						return a.angle - b.angle;
					});

				var vehicles_details = req.body.vehicles_details;
				var vehicles_count = vehicles_details.length;
				var vehicles_actual_count = vehicles_count;
				var vehicle_assets = {};
				console.log('count count ' + vehicles_count);
				if(vehicles_count < clusterCount){

					vehicles_count = clusterCount;

				}

				console.log('cluster count ' + clusterCount);
				console.log('new vehicle count '+vehicles_count);
				for(var i=0;i < vehicles_count;i++){
				
					//console.log(vehicles_details[i]);
					if(typeof vehicles_details[i] == 'undefined'){
						var temp = {
							'vehicle_number' : 'newVehice'+i,
        					'vehicle_max_load' : vehicles_details[0]['vehicle_max_load']
						};
						vehicles_details.push(temp);
					}

					vehicles_details[i]['consignmentWeight'] = 0 ;
					vehicles_details[i]['orderCount'] = 0 ;
    				vehicle_assets[i] = {
											"coordinates_data" : [],
											"vehicleInfo" : vehicles_details[i],
    								    }

				}

				console.log(vehicle_assets);


				var clusterStraight = [];
				for(var i=0;i < cluster.length;i++){
					

					//console.log(cluster[i]['points'].length);
					// console.log(cluster[i]);
					for(var j=0;j<cluster[i]['points'].length;j++){


						var key = String(cluster[i]['points'][j][0]) + String(cluster[i]['points'][j][1]);
						var key = orderMapHolder.indexOf(key);
						
						if(key > -1){
							var temp = {
									coordinates : req.body.orderscoordinates[key],
									distance : 0,
									order_id : req.body.orderscoordinates[key]['gds_order_id']

							}
							clusterStraight.push(temp);
						}
						
					}
				}
				console.log('cluster lenght' + clusterStraight.length);
				var looper = 0;

				for(var j=0;j<clusterStraight.length;j++){
					
					// console.log(looper);
					//console.log(vehicle_assets[looper]);
					var consignmentWeight = parseFloat(vehicle_assets[looper]['vehicleInfo']['consignmentWeight']);
					var order_weight = parseFloat(clusterStraight[j]['coordinates']['weight']);
					var cur_weight = consignmentWeight+order_weight;
					var vehicle_max_load = parseFloat(vehicle_assets[looper]['vehicleInfo']['vehicle_max_load']);
					var cur_count = vehicle_assets[looper]['vehicleInfo']['orderCount'];
					// console.log('cur_count');
					// console.log(cur_count);
					if(cur_count < 35 && cur_weight < vehicle_max_load){
						vehicle_assets[looper]['coordinates_data'].push(clusterStraight[j]);
						delete clusterStraight[j];				
						vehicle_assets[looper]['vehicleInfo']['consignmentWeight'] = cur_weight;
						vehicle_assets[looper]['vehicleInfo']['orderCount'] += 1;
					}else{

						looper = looper+1;
						if(vehicle_assets[looper]){
							j--;
						}else{

							console.log('breaking it at' + looper);
							break;
						}
						
					}
				}
				
				//console.log(vehicle_assets.length);
				
				for (var j in vehicle_assets) {

 	 				console.log(vehicle_assets[j]['coordinates_data'].length);
					if(vehicle_assets[j]['coordinates_data'].length == 0){
						console.log('deleting vehicle_assets ' + j);
						delete vehicle_assets[j];
					}
				}

				var vehicles_count = Object.keys(vehicle_assets).length;
				//console.log(vehicles_count - vehicles_actual_count);

				if(vehicles_count > vehicles_actual_count){
					vehicle_assets['extra_vehicles']= [];
					for (var i = (vehicles_actual_count); i < vehicles_count; i++) {
						vehicle_assets['extra_vehicles'].push(vehicle_assets[i]['vehicleInfo']);
					}
				}
				

				vehicle_assets['unassigned_coordinates'] = [];
				
				if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							vehicle_assets['unassigned_coordinates'].push(unassigned[i]);
						}

				}


				var newArray = [];
				for (var i = 0; i < clusterStraight.length; i++) {
				    if (clusterStraight[i]) {
				      newArray.push(clusterStraight[i]);
				    }else{
				    }
				}

				clusterStraight = newArray;
				for (var i = 0; i < clusterStraight.length; i++) {

					vehicle_assets['unassigned_coordinates'].push(clusterStraight[i]);

				}

				
				
				console.log(vehicle_assets);
				// // // now see this key does not exist in the vehicle asset 
				// // // after we push it to the response
				
				return res.ok(vehicle_assets);
    		}
    	}

    },

    splitloadOnLocationbak:function(req,res){
    	var async = require('async');
    	var alldata = [];
    	var test = [];
    	if(req.method == 'GET'){
    		return res.json(405,'Not Allowed request type');
    	}else{

    		var req_data = req.body;
    		if(!req_data.vehicles_details){
    			return res.json(400,'Vehicle data Missing !!!');
    		}else{

    			var orderscoordinates = req.body.orderscoordinates;
    			var unassigned = [];
    			for(var i= 0; i < orderscoordinates.length; i++){

    				if(parseInt(orderscoordinates[i].lat) == 0 || parseInt(orderscoordinates[i].long) == 0){
    					console.log('got zero');
      					var temp = {
      						coordinates : orderscoordinates[i],
      						distance : 0,
      						order_id : orderscoordinates[i]['gds_order_id']
      					}
      					console.log(temp);
    					unassigned.push(temp);
    					delete orderscoordinates[i];
    				}
    			}

    			var newArray = [];
				for (var i = 0; i < orderscoordinates.length; i++) {
				    if (orderscoordinates[i]) {
				      newArray.push(orderscoordinates[i]);
				    }else{
				    }
				}

				orderscoordinates = newArray;
				req.body.orderscoordinates = orderscoordinates;

    			var that = module.exports;
    			var googleResponseDecided = module.exports.caclulateHubtoDesAll(req , res, 1,function(googleResponse){
    				googleResponse.sort(function(a, b) {
						return parseInt(a.distance) - parseInt(b.distance);
					});
					
					var vehicles_details = req_data.vehicles_details;
					var vehicles_count = vehicles_details.length;
					console.log(googleResponse.length);

					var vehicle_assets = [];

					//puting in the lowest coordinates to the fist starting point
					for(var i=0; i < vehicles_count ; i++){
						vehicle_assets[i] = [];
						if(googleResponse[i]){
							vehicle_assets[i][0] = googleResponse[i];
						}
					}

					//putting the second smallest to the pool
					googleResponse.splice(0,vehicles_count) ;
					console.log('after !st smallest given');
					for(var i=0; i < vehicles_count ; i++){

						if(googleResponse[i]){
							vehicle_assets[i][1] = googleResponse[i];
						}
						

					}
					//console.log(vehicle_assets);
					//placing the highest distances to the vehicles reverse
					googleResponse.splice(0,vehicles_count);
					for(var i=0; i < vehicles_count;i++){

						googleResponseCount = googleResponse.length;
						if(googleResponse[googleResponseCount-1]){
						
							vehicle_assets[i][2] = googleResponse[googleResponseCount-1];
							googleResponse.splice(googleResponseCount-1,1);	
						
						}
					}

					//console.log(vehicle_assets);
					var data = that.distributeOrders(googleResponse,vehicle_assets,vehicles_details);
					console.log("un assigned");
					console.log(unassigned);
					console.log(data);
					if(unassigned.length > 0){

						for(var i = 0; i < unassigned.length ;i++){

							data['unassigned_coordinates'].push(unassigned[i]);
						}

					}



					return res.ok(data);

    			});
	   		}
    	}
    },

    distributeOrders:function(googleResponse,vehicle_assets,vehicles_details){

    	console.log('distribute orders');
    	console.log(vehicle_assets);
    	var newVehicleAssetsSetup = {};
   	
		for(var i = 0; i < vehicle_assets.length; i++){

    		var cumulativeload = 0;
    		for(var j = 0 ; j < vehicle_assets[i].length; j++){
    			cumulativeload += parseFloat(vehicle_assets[i][j].coordinates.weight);
    			
    		}
    		vehicles_details[i]['consignmentWeight'] = cumulativeload ;
    		newVehicleAssetsSetup[i] = {	"coordinates_data" : vehicle_assets[i],
    										"vehicleInfo" : vehicles_details[i]
    								    }
    	}

    	var countUnassignedCoordinates = googleResponse.length;
    	var countVehicleCount = vehicle_assets.length;

    	//filter points inside the the triangle
    	//arrange the points which lies in the triange first come first serve style 
    	//if the weights dnt match move to next vehicle 
    	//most of the time will not happen
    	//
    	//move through the list and assign the side
    	var removecoordinates = [];
    	for(var i = 0 ;i < countUnassignedCoordinates; i++){

    		for(var j=0;j < countVehicleCount;j++){

    			var vehicleAsset = newVehicleAssetsSetup[j];
    			var cornersCoordinates = module.exports.array_column(vehicleAsset['coordinates_data'],'coordinates');
    			var cornersX = module.exports.array_column(cornersCoordinates,'lat');
    			var cornersY = module.exports.array_column(cornersCoordinates,'long');
    			var ret  = module.exports.checkPointInsideThePolygon(googleResponse[i].lat,googleResponse[i].long, cornersX, cornersY);
    			if(ret){
    				var vehicle_max_load = parseFloat(vehicleAsset['vehicleInfo']['vehicle_max_load']);
    				var consignmentWeight = parseFloat(vehicleAsset['vehicleInfo']['consignmentWeight']);
    				var newConsignmentWeight = consignmentWeight +  parseFloat(googleResponse[i]['coordinates']['weight']);
    				if( vehicle_max_load > newConsignmentWeight){
    					newVehicleAssetsSetup[j]['coordinates_data'].push(googleResponse[i]);
    					delete googleResponse[i];
	    				removecoordinates.push(i);
	    				break;
    				} 
    				
    			}

    		}
    	}

    	var remaingingUnassignedCount = googleResponse.length;    	
    	var newArray = [];
		for (var i = 0; i < remaingingUnassignedCount; i++) {
		    if (googleResponse[i]) {
		      newArray.push(googleResponse[i]);
		    }else{
		    }
		}

		/*
			giving back all unassigned parts back
			naming the scattered array formation 
			of null and length back to actual response
		*/
		googleResponse = newArray;
		var groups2 = []; // The grouped values
		var split = countVehicleCount;
		for(var i = 0, j = 0, length = googleResponse.length; i < length; i+=split, j++) {
		    groups2[j] = googleResponse.slice(i, i + split);
		}

		googleResponse = groups2;
		
		for(var i=0;i<googleResponse.length;i++){

			groupdata = googleResponse[i].length;
			for(var j=0;j<groupdata;j++){
				var vehicleAsset = newVehicleAssetsSetup[j];
				var vehicle_max_load = parseFloat(vehicleAsset['vehicleInfo']['vehicle_max_load']);
				var consignmentWeight = parseFloat(vehicleAsset['vehicleInfo']['consignmentWeight']);
				var newConsignmentWeight = consignmentWeight +  parseFloat(googleResponse[i][j]['coordinates']['weight']);
				if( vehicle_max_load > newConsignmentWeight){
    					
    					newVehicleAssetsSetup[j]['coordinates_data'].push(googleResponse[i][j]);
    					delete googleResponse[i][j];
    					newVehicleAssetsSetup[j]['vehicleInfo']['consignmentWeight'] = newConsignmentWeight;

    			}
			}
		}

		/*
			Ungroping the remaings back to a 
			single lINEAR array
		*/
		console.log(googleResponse.length);
		var unAssignedCoordinates = [];
		for(var i = 0;i < googleResponse.length;i++){
			console.log(googleResponse[i].length);
			var responseBlock = googleResponse[i];
			for (var j = 0; j < responseBlock.length; j++) {
				
				if(responseBlock[j]) {
					console.log(responseBlock[j]);
					unAssignedCoordinates.push(responseBlock[j]);
				}
				
			}
		}
		
		newVehicleAssetsSetup['unassigned_coordinates'] = unAssignedCoordinates;

    	return newVehicleAssetsSetup;

    },

    array_column: function(list, column, indice){
		    var result;

		    if(typeof indice != "undefined"){
		        result = {};

		        for(key in list)
		            result[list[key][indice]] = list[key][column];
		    }else{
		        result = [];

		        for(key in list)
		            result.push( list[key][column] );
		    }

		    return result;
		},
    /***

    ***/
    checkPointInsideThePolygon:function (x, y, cornersX, cornersY) {

        var i, j=cornersX.length-1 ;
        var  oddNodes=false;

        var polyX = cornersX;
        var polyY = cornersY;

        for (i=0; i<cornersX.length; i++) {
            if ((polyY[i]< y && polyY[j]>=y ||  polyY[j]< y && polyY[i]>=y) &&  (polyX[i]<=x || polyX[j]<=x)) {
              oddNodes^=(polyX[i]+(y-polyY[i])/(polyY[j]-polyY[i])*(polyX[j]-polyX[i])<x); 
            }
            j=i; 
        }

          return oddNodes;
    },

    /**
     * 
     */
    arrangeShortestPath:function(req,res){
    	if(req.method == 'GET'){
    		return res.json(403,'Method not allowed');
    	}
    	var data = req.body;
    	var coordinates_data_length = data['coordinates_data'].length;
    	var coordinates = data['coordinates_data'];
    	coordinates.sort(function(a, b) {
						return parseInt(a.distance) - parseInt(b.distance);
					});
    	console.log('111111111111111');
    	var map = {};
    	//var restrictedA = coordinates[0];
    	var orderSequenceHolder = [];
    	for(var i=0;i<coordinates_data_length;i++){
    		console.log('2222222222222');
    		var nodeCoordinates = coordinates[i];
    		var startLat = nodeCoordinates['coordinates']['lat'];
    		var startLong = nodeCoordinates['coordinates']['long'];
    		var order_id = nodeCoordinates['order_id'];
    		orderSequenceHolder.push(order_id);
    		if(map[order_id]){
    			map[order_id] = [];
    		}

    		var edges = {};
    		for(var j=0;j<coordinates_data_length;j++){
    			console.log('3333333333333');
    			var nodeCoordinates = coordinates[j];
    			var endOrderId = nodeCoordinates['order_id'];
    			var endNodeLat = nodeCoordinates['coordinates']['lat'];
    			var endNodeLong = nodeCoordinates['coordinates']['long'];

				var distance = module.exports.calculateDistance(startLat, startLong, endNodeLat, endNodeLong, 'K');
    			var distanceMeter = Math.round(distance*1000);
    				edges[endOrderId] = distanceMeter; 			
    			
    		}

    		map[order_id] = edges;  		
    	}
    	console.log('444444444444444444',map);

    	var orderHolder = {};
    	var originalData = data['coordinates_data'];

    	for(key in orderSequenceHolder){
    		console.log('5555555555555');
			order_id = orderSequenceHolder[key];
			for (var j = 0; j< originalData.length; j++) {
				
				if(order_id == originalData[j]['order_id']){
					orderHolder[order_id] = originalData[j];
				}
			}

    	}
    	console.log('55555555555555');
    	var Promise = require('bluebird');
    	var result = new Promise(function(resolve,reject){
    			module.exports.ShortestLookUp(map,coordinates,function(result){
    			resolve(result); 
    	})});
    	console.log('6666666666666666');
    	result.then(function(result){

    			var sequence = result;
    			console.log(sequence);	
				var return_sequence = [];
				var startingOrderId = orderSequenceHolder[0];
				console.log('******');
				console.log(startingOrderId);
				var sequenceRearrange = [];
				var pivot;
				for(key in sequence){

					if(sequence[key] == startingOrderId){
						pivot = key;
						break;
					}
				}
				console.log(pivot);
				if(pivot == 0){
					sequence.splice(-1,1);
				}else{

					/**
					 * [p1 Arranging the data according to 
					 * the starting nearest point]
					 * @type {[type]}
					 */
					
					var p1 = result.slice(pivot);
					var p2 = result.slice(0,pivot);
					console.log(p1);
					console.log(p2);
					var newsequence = [];
					// var spinRightCount = p1.length-1;
					// var spinLeftCount = p2.length;
					p2.splice(0,1);
					for(key in p1){
						//if(spinRightCount != key){
							newsequence.push(p1[key]);
						//}
						
					}
					for(key in p2){
						newsequence.push(p2[key]);
					}
					
					//Replacing the sequence with the way 
					//we want from the 
					sequence = newsequence;
				}

				// console.log(sequence);
				// var hubcoordinates = {};
				// hubcoordinates['coordinates'] = data['hubcoordinates'];
				// hubcoordinates['distance'] = 0;
				// hubcoordinates['order_id'] = 0;
				// //return_sequence.push(hubcoordinates);
				for (var j=0; j<sequence.length; j++) {
					
					var order_id = sequence[j];
					var temp =  orderHolder[order_id];
					return_sequence.push(temp);
				}
				console.log('********',sequence);
				return_data = {};
				return_data['vehicleInfo'] = data['vehicleInfo'];
				return_data['hubcoordinates'] = data['hubcoordinates'];
				return_data['coordinates_data'] = return_sequence;
				//return_data['startingOrderId'] = startingOrderId;
				res.send(return_data);

		    	//return res.ok(return_data);
    	});

    },

    Dijiskstra:function(graph,start,end){

    	var sequence = [];
    	var visited = [];
    	var maxnodes = 0;

    	for(key in graph){
    		
    		maxnodes += 1;
    	}   	

  		initialnode = graph[start]; 
  		visited.push(start);
  		var temp_root = start;
   		while( visited.length > 0 && visited.length < maxnodes){

   			var linear_shortest = 0;
   			initialnode = graph[temp_root];
   			for( key in initialnode){

   				if(linear_shortest == 0){
   					
   					linear_shortest = initialnode[key];
   					temp_root = parseInt(key);
   				}else{

   					if(visited.indexOf(parseInt(key)) > -1){
   						//do nothing
   					}else{

   						if(linear_shortest > initialnode[key]){
   							linear_shortest = initialnode[key];
   							temp_root = parseInt(key);
   						}
   					}

   				}

   			}
   			visited.push(temp_root);
   			initialnode = graph[temp_root];

   			/*
   				Emptying out the arrays to make search space much efficient
   			 */
   			for(var i =0;i<visited.length;i++){

   				for(key in initialnode){
   					if(key == visited[i]){
   						delete initialnode[key];
   					}
   				}
   			}
   		}
   		sequence =  visited;
   		console.log(visited);
    	return sequence;

    },

    /**
     * [ShortestLookUp Maintaining a look up test]
     * @param {[type]}   map         [description]
     * @param {[type]}   coordinates [description]
     * @param {Function} cb          [description]
     */
    ShortestLookUp:function(map,coordinates,cb){
    	console.log('777777777',map);
    	var countCoordinates = coordinates.length;

    	costMatrix = [];
    	orderIdMap = [];
    	for(key in map){
    		orderIdMap.push(key);
    		var temparray = [];
    		var submap = map[key];
    		for(key in submap){
    			temparray.push(submap[key]);
    		}
    		console.log('XXXXXX',temparray);
    		costMatrix.push(temparray);
    	}
    	console.log('88888888888');
    	var solver = require('node-tspsolver');
    	console.log('9999999999999');
		var costMatrix = [
		    [0, 1, 3, 4],
		    [1, 0, 2, 3],
		    [3, 2, 0, 5],
		    [4, 3, 5, 0]
		];
		var Promise = require('bluebird');
		console.log('costmatrix',costMatrix);
		var result = new Promise(function(resolve,reject){

			//use false to stop round trip
			solver.solveTsp(costMatrix, true, {}).then(result=> {
				console.log('DDDDDDDDDDDD',result)
		    	resolve(result); // result is an array of indices specifying the route.
			});

		});
		console.log('101010100101');
		result.then(function(result){
			var sequence = [];
					console.log('12122121212');

			for(key in result){
				sequence.push(orderIdMap[result[key]]);
			}
			console.log('sqsqsqsqqsq',sequence);
			cb(sequence);
		});
		//console.log(sequenceList);

    },

    testPromise:function(req,res){
		var numbers = [1,2,3,4,5,6,7,8]; // Initial values
		var groups2 = []; // The grouped values
		var split = 3;
		for(var i = 0, j = 0, length = numbers.length; i < length; i+=split, j++) {
		    groups2[j] = numbers.slice(i, i + split);
		}
		console.log(groups2);
		var distribute = [];

		for(var i=0;i < groups2.length;i++){
			
			for(var j=0;j<split;j++){
				if(distribute[j] === undefined){
					console.log("creating vehicle");
					distribute[j] = [];
					distribute[j].push(groups2[i][j]);
				}else{
					console.log(groups2[i][j]);
					distribute[j].push(groups2[i][j]);
				}
				
			}			
		}
    	
    	console.log(distribute);
    }
};

