var apiCall = system.getModel('orderInfo');
var dateFormat = require('dateformat');

var orderInfo = {

	orderDetails: function(req, res){
		console.log("API working started at "+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("orderIdddd "+req.body.orderId);
		var objj = {};
		objj.Containers = [];
		
		console.log(objj);
		var ContainerInfo = {};
		if(Object.keys(req.body).length === 0){
    		response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid input"
            };
            res.json(response);
            return;
    	}

		if(!req.body.orderId){
            res.json(sleekConfig.badRequestWithWrongInput);
            return;
        }
        else{

        	apiCall.getdata(req.body.orderId, function(apiResponse){
        	
			var jsonData = JSON.parse(apiResponse);

			if(jsonData.length !== 0){
				for(i = 0; i < jsonData.length; i++){

			     	// console.log("iteration : "+i);
				    objj.OrderId  						= jsonData[i].OrderId;
				    objj.OrderCode 						= jsonData[i].OrderCode;
				    objj.OrderDate 						= jsonData[i].OrderDate;

				    var tempContainerInfo 					= {};
				    // tempContainerInfo.items				= [];

				    tempContainerInfo.ContainerBarcode = jsonData[i].ContainerBarcode;
				    tempContainerInfo.ContainerNo = jsonData[i].ContainerNo;
				    tempContainerInfo.PickedBy = jsonData[i].pickedBy;

				    var tempItemInfo 					= {};
				    tempItemInfo.ProductBarcode = jsonData[i].ProductBarcode;
				    tempItemInfo.ProductID = jsonData[i].ProductID;
				    tempItemInfo.sku = jsonData[i].sku;
				    tempItemInfo.PackType = jsonData[i].PackType;
				    tempItemInfo.EachesCount = jsonData[i].EachesCount;
				    tempItemInfo.qty = jsonData[i].qty;
				    
				    tempContainerInfo.items = [tempItemInfo];

				    objj.Containers.push(tempContainerInfo);
	
				}
				response = {
	                "Status":200,
	                "Message":"Success",
	                "ResponseBody":objj
	            };
				// console.log("OBJ data data1"+JSON.stringify(objj));
				res.json(response);
				return;
			}else
			{
				var response = {
	                "Status":200,
	                "Message":"Success",
	                "ResponseBody":"No data for given OrderId"
	            };
				console.log("No data");
				res.json(response);
				return;
			}

		 });


        }

	},
};

module.exports = orderInfo;