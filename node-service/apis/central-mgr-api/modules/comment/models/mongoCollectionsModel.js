/*
Filename : mongoCollectionsModel.js
Author : eButor
CreateData : 21-Jun-2016
Desc : All function related to Mongo is written here
*/
var mongoCollectionsModel = {

    getVersion: function(queryData, callback){

        var finalWhereData = {};
        // Prepare query for NotInData
        var notindata = queryData.notindata;

        if(notindata){
            finalWhereData = {
                "product_id":{
                    "$nin":Array()
                }
            };
            arr = notindata.split(',');
            for(i=0; i < arr.length; i++){
                finalWhereData.product_id.$nin.push(parseInt(arr[i]));
            }
        }

        for(key in queryData.wheredata){
            if( queryData.wheredata.hasOwnProperty(key) ){

                // Prepare whare condition
                var wherefld = queryData.wheredata[key].wherefld; 
                var whereArray = {};
                if(wherefld && queryData.wheredata[key].data && queryData.wheredata[key].wheredatatype){
                    if(queryData.wheredata[key].wheredatatype==="int"){
                        finalWhereData[wherefld]=parseInt(queryData.wheredata[key].data);
                    }else if(queryData.wheredatatype==="date"){
                        finalWhereData[wherefld]=new Date(queryData.wheredata[key].data);
                    }else{
                        finalWhereData[wherefld]= new RegExp(queryData.wheredata[key].data, "i");
                    }
                }

            }
        }

        // Prepare to sort Data
        var sortArray = {};
        var sortData = queryData.sortdata;
        if(sortData){
            arr = sortData.split(" ");
            sortArray[arr[0]] = arr[1].toUpperCase()=='DESC' ? -1 : 1;
        }

        // Prepare page limit
        var dataSize = parseInt(queryData.pageSize);
        var dataNumber = parseInt(queryData.pageNumber);

        var collection = mongodb.collection('products_outbound');
        // Locate all the entries using find
        collection.find( finalWhereData ).sort(sortArray).toArray(function(err, results) {
            console.log(finalWhereData);
        	callback(results);
        });
    },
};
module.exports = mongoCollectionsModel;

//.limit(dataSize*1).skip(dataNumber*dataSize)