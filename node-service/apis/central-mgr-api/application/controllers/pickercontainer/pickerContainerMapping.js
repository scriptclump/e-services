var apiCall = system.getModel('pickerContainerMapping');
var dateFormat = require('dateformat');

var pickerContainerMapping = {

	pickerBarCodeInfo: function(req, res){
		console.log("API working started at "+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		var objj = {};
		objj.items = [];
		if(Object.keys(req.body).length === 0){
    		response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid input"
            };
            res.json(response);
            return;
    	}

		if(!req.body.ContainerBarcode){
            res.json(sleekConfig.badRequestWithWrongInput);
            return;
        }
        else{

        	apiCall.getdata(req.body.ContainerBarcode, function(apiResponse){
        	// console.log("checking "+apiResponse);

			var jsonData = JSON.parse(apiResponse);
			// console.log("chkng "+jsonData);
			// console.log("Checking type "+typeof(jsonData));	 
			if(jsonData.length !== 0){
				for(i = 0; i < jsonData.length; i++){
			     
				     objj.ContainerBarcode  	= jsonData[i].ContainerBarcode;
				     objj.ContainerNo 			= jsonData[i].ContainerNo;
				     objj.OrderNo 				= jsonData[i].order_num;
  					 objj.Beat					= jsonData[i].beat_name;
					 objj.Dock					= jsonData[i].st_docket_no;
					 objj.Hub					= jsonData[i].HUB;
				     objj.PickedBy 				= jsonData[i].pickedBy;

					 var temp 					= {};
					 temp.ProductBarcode 		= jsonData[i].ProductBarcode;
					 temp.ProductID 			= jsonData[i].ProductId;
					 temp.sku 					= jsonData[i].sku;
					 temp.PackType 				= jsonData[i].PackType;
					 temp.EachesCount 			= jsonData[i].EachesCount;
					 temp.qty 					= jsonData[i].qty;
					 temp.Producttitle			= jsonData[i].product_title;

					 temp.mrp					= jsonData[i].mrp;
					 temp.ProdOrderQty 			= jsonData[i].qty;
					 objj.items.push(temp);			     
				     // arr.ProductBarcode = jsonData[i].ProductBarcode;
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
	                "ResponseBody":"No data for given ContainerBarcode"
	            };
				console.log("No data");
				res.json(response);
				return;
			}

		 });


        }

	},
};

module.exports = pickerContainerMapping;