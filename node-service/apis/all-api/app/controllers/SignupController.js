const Signup = require('../models/Signup');
const s3 = require('../../config/s3.js');
module.exports = {
    signupall: function(req, res) {
        var data = req.body.data;
        if (!data) {
            var response = {
                "Status": 400,
                "Message": "Bad Request",
                "Response Body": "Invalid JSON Format"
            }
            return res.json(response);
        } else {
            //Signup.checkbusinesslegalName(data, function(dataresponse) {
            //  console.log(dataresponse.length);
            // if (dataresponse.length >= 1) {
            //     var response = {
            //         "Status": 200,
            //         "Message": "data Exist",
            //         "Response Body": "Email Already Exist!"
            //     }
            //     return res.json(response);
            // } else {
            var s3_data = s3.aws_s3;
            var files_list = req.files;
            if (Object.entries(files_list).length === 0) {
                console.log(' i didnt receive any');
                module.exports.saveLegalEntity(data, null, response).then(data => {
                    res.send(data);
                }).catch(function(err) {
                    var response = {
                        "Status": 400,
                        "status": "failed",
                        "message": "failed"
                    };
                    res.send(response);
                })
            } else {
                console.log('hihihi');
                console.log(files_list);
            }
            /*var singleupload = upload.single('logo');
            singleupload(req,res,function(err,fileuploaded){})
            req.file('logo').upload({
            adapter: require('skipper-better-s3'),
            key: s3_data.key,
            secret: s3_data.secret,
            bucket: s3_data.bucket,
            region: s3_data.region,
            dirname: 'feedback',
            saveAs: function(file, handler) {
            var d = new Date();
            var extension = file.filename.split('.').pop();
            // generating unique filename with extension
            var filename = d.getTime() + "." + extension;
            handler(null, filename);
            }
            }, function(err, filesUploaded) {
            if (err) {
            console.log('s3 errorr');
            //return res.json(500, err);
            return res.json(200, {
            Status: 201,
            status: "failed",
            message: "Unable to Upload File",
            ResponseBody: ""
            });
            } else {
            if (filesUploaded.length === 0) {
            picture_upload = null;
            } else {
            picture_upload = filesUploaded[0].extra.Location;
            }

            }
            });*/
            //}
            //});
        }
    },
    saveLegalEntity: function(data, picture_upload, response) {
        return new Promise((resolve, reject) => {
            Signup.saveIntolegalEntity(data, picture_upload, function(res1) {
                Signup.saveIntoWarehouseTable(res1, data, function(response) {
                    Signup.serialNoInvoice(res1, data, function(serialdata) {
                        Signup.saveRetailerFlat(data, res1, response.hub_id, response.beat_id, response.spoke_id, function(retailerID) {
                            Signup.saveStockistPriceMappingTable(res1, data, function(stockist) {
                                Signup.saveStockistIndentConfTable(res1, function(indent) {
                                    Signup.saveIntoUsersTable(res1, data, function(responseId) {
                                        if (responseId.Status != 500) {
                                            Signup.userEcashCreditlimit(res1, responseId, function(ecashRes) {
                                                Signup.saveIntoRolesTable(res1, responseId, data, function(roleid) {
                                                    Signup.saveIntoUserrolesTable(roleid, responseId, function(response) {
                                                        Signup.saveIntoRoleAccess(roleid, function(response) {
                                                            Signup.saveIntoBusinessUnitsTable(res1, data, responseId, function(busresponse) {
                                                                Signup.apobIntoWarehouseTable(res1, data, function(appeal) {
                                                                    Signup.creatingWorkFlowsForUsers(res1, data, roleid, function(resultData) {
                                                                        var response = {
                                                                            "Status": 200,
                                                                            "legal_entity_id":res1,
                                                                            "status": "success",
                                                                            "message": "DC/FC Successfully Created"
                                                                        };
                                                                        resolve(response);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        } else {
                                            var response = {
                                                "Status": 400,
                                                "status": "failed",
                                                "message": "failed"
                                            };
                                            resolve(response);
                                        }
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })
    }
};
