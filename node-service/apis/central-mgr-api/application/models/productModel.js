/*
Filename : productModel.js
Author : eButor
CreateData : 19-Oct-2016
Desc : DB calls to get Unsellable SKUs
 */
var unirest = require('unirest');
var taxModel = {

    getUnbilledSKUs:function(startDate, endDate, ffList, area_code, callback){
        /*
            This function collects applied VAT/CST tax-classes on a product based on Buyer and Seller States
        */
        global.finalResult = Array();
        try{
            //Check for valid Product ID
            con.query("CALL getUnbilledSKUList(?,?,?,?)", [startDate, endDate, ffList, area_code], function(error,rows)
            {
                if(error){
                    console.log(error);
                    var res = {'serverError':"Internal Server Error"};
                    finalResult.push(res);
                    //callback(JSON.stringify(finalResult)) ;
                }
                else{
                    var result = JSON.parse(JSON.stringify(rows));
                    for(key in result[0]){
                        finalResult.push(result[0][key]);
                    };
                    console.log(finalResult);
                    console.log("=======\n");
                }
                callback(JSON.stringify(finalResult));
            });
            
        }catch(err){
            console.error(err);
            return err;
        }
    }
};

module.exports = taxModel;