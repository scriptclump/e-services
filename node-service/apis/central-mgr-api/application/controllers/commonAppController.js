
/*
 * Sample Welcome page Controller
 * 
 * @package Sleek.js
 * @version 1.0
 * @author Robin <robin@cubettech.com>
 * @Date 23-10-2013
 */

//commonApp function
var commonAppController = {

      versioncpmanager: function(req, res){
        var versionName = req.query.versionName;
        var versionNumber = req.query.versionNumber;
        var appType = req.query.appType;

        if(!versionName || !versionNumber  || !appType){
            console.log(clientIPAddress + " : Bad Request received with improper query data.");
            res.json(sleekConfig.badRequest);
        }else{
            
            con.query("select * from app_version_info where app_type='"+appType
                +"' and version_number>'"+versionNumber+"'", function(error,rows){

                    

                    if(error){
                        console.log(error);
                        console.log(clientIPAddress + " : Error happen for the call");
                        console.log('-------------------------------------------------');
                        res.json(sleekConfig.Forbidden);
                    }else{

                        console.log(clientIPAddress + " : Response sent to the client");
                        console.log('-------------------------------------------------');

                        var results = JSON.stringify(rows);
                        results = results.substring(1, results .length-1);

                        if(results!='') {
                            finalResponse = {
                                'status' : 200,
                                'message' : {
                                    'versionUpdateStatus' : 1,
                                    'versionNumber' : rows[0]['version_number']
                                }
                            };

                        }else{

                            finalResponse = {
                                'status' : 200,
                                'message' : {
                                    'versionUpdateStatus' : 0
                                }
                            };
                        }
                        res.json(finalResponse);
                    }
                
            });
        }
    }
};

module.exports = commonAppController;