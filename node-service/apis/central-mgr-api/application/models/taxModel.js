/*
Filename : taxModel.js
Author : eButor
CreateData : 20-July-2016
Desc : Tax functions related API calls
 */
var unirest = require('unirest');
var taxModel = {

    getTaxes:function(prod_id, buyer_state_id, seller_state_id, paramDate, callback){
        /*
            This function collects applied VAT/CST tax-classes on a product based on Buyer and Seller States
        */
        var finalResult = new Array();
        try{
            
console.log('Started');
//Check for valid Product ID
            con.query("SELECT product_id FROM tax_class_product_map WHERE product_id = ?", [prod_id], function(error,rows1)
            {
                if(error){
                    console.log(error);
                    var res = {'serverError':"Internal Server Error #Q1"};
                    finalResult.push(res);
                    callback(JSON.stringify(finalResult)) ;
                }
                
                if(rows1.length===0)
                {
                    var res = {'Exception':"Tax Mapping for Product Not Found"};
                    finalResult.push(res);
                    callback(JSON.stringify(finalResult));
                }
                else{
                    //Check for valid Buyer State 
                    con.query("SELECT zone_id FROM zone WHERE zone_id = ? AND country_id = 99", [buyer_state_id], function(error,rows2)
                    {
                        if(error){
                            console.log(error);
                            var res = {'serverError':"Internal Server Error #Q2"};
                            finalResult.push(res);
                            callback(JSON.stringify(finalResult)) ;
                        }
                        
                        if(rows2.length===0)
                        {
                            var res = {'Error':"Invalid Buyer State"};
                            finalResult.push(res);
                            callback(JSON.stringify(finalResult));
                        }
                        else{
                            //Check for valid Seller State 
                            con.query("SELECT zone_id FROM zone WHERE zone_id = ? AND country_id = 99", [seller_state_id], function(error,rows3)
                            {
                                if(error){
                                    console.log(error);
                                    var res = {'serverError':"Internal Server Error #Q3"};
                                    finalResult.push(res);
                                    callback(JSON.stringify(finalResult)) ;
                                }
                                
                                if(rows3.length===0)
                                {
                                    var res = {'Error':"Invalid Seller State"};
                                    finalResult.push(res);
                                    callback(JSON.stringify(finalResult));
                                }
                                else{
                                    //ALL GOOD!! Go ahead and collect info... :D
                                    var newArray = new Array();
                                    if(buyer_state_id === seller_state_id){
console.log("CALL getProductTax("+prod_id+","+seller_state_id+",'10001',"+paramDate+")");

                                        con.query("CALL getProductTax(?,?,?,'10001',?)", [prod_id, seller_state_id, buyer_state_id, paramDate], function(error,rows)
                                        {
                                            if(error){
                                                console.log(error);
                                                var res = {'serverError':"Internal Server Error #Q4"};
                                                newArray.push(res);
                                                //callback(JSON.stringify(finalResult)) ;
                                            }
                                            else{
                                                console.log("finalResult Before Loop");
                                                console.log(finalResult);
                                                var result = JSON.parse(JSON.stringify(rows));
                                                console.log("Procedure Result");
                                                console.log(result);
                                                console.log("--------------");
                                                for(key in result[0]){
                                                    finalResult.push(result[0][key]);
                                                    newArray.push(result[0][key]);
                                                };
                                                console.log("finalResult after loop");
                                                console.log(finalResult);
                                                console.log("+++++++++");
                                                console.log(newArray);
                                                console.log("=======\n");
                                            }
                                            callback(JSON.stringify(finalResult));
                                        });
                                    }
                                    else{
console.log("CALL getProductTax("+prod_id+","+seller_state_id+",'10002',"+paramDate+")");                                        
                                        console.log("IGST Tax");
                                        con.query("CALL getProductTax(?,?,?,'10002',?)", [prod_id, seller_state_id, buyer_state_id, paramDate], function(error,rows)
                                        {
                                            if(error){
                                                console.log(error);
                                                var res = {'serverError':"Internal Server Error #Q5"};
                                                newArray.push(res);
                                                //callback(JSON.stringify(finalResult));
                                            }
                                            else{
                                                var result = JSON.parse(JSON.stringify(rows));
                                                for(key in result[0]){
                                                    finalResult.push(result[0][key]);
                                                    newArray.push(result[0][key]);
                                                };
                                                console.log(finalResult);
                                                console.log("+++++++++");
                                                console.log(newArray);
                                                console.log("=======\n");
                                            }
                                            callback(JSON.stringify(finalResult));
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            });
            
        }catch(err){
            console.error(err);
            return err;
        }
    }
};

module.exports = taxModel;
