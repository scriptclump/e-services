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
            Signup.checkbusinesslegalName(data, function(dataresponse) {
                console.log(dataresponse.length);
                if (dataresponse.length >= 1) {
                    var response = {
                        "Status": 200,
                        "Message": "data Exist",
                        "Response Body": "Email Already Exist!"
                    }
                    return res.json(response);
                } else {
                    var s3_data = sails.config.s3.aws_s3;
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
                            Signup.saveIntolegalEntity(data, picture_upload, function(res1) {
                                // console.log('Im saving into legalentity table result is from the table', res1);
                                Signup.saveRetailerFlat(data, res1, function(retailerID) {
                                    // console.log('Im saving into RetailerFlat table result is from the table', response);
                                    Signup.saveIntoWarehouseTable(res1, data, function(response) {
                                        // console.log('Im saving into saveIntoWarehouseTable  result is from the table is DC,ID', response);
                                        Signup.saveStockistPriceMappingTable(res1, data, function(stockist) {
                                            // console.log('Im saving into saveStockistPriceMappingTable  result is from the table is DC,ID', stockist);
                                            Signup.saveStockistIndentConfTable(res1, function(indent) {
                                                // console.log('Im saving into saveStockistIndentConfTable  result is from the table is DC,ID', indent);
                                                Signup.saveIntoUsersTable(res1, data, function(responseId) {
                                                    // console.log('Im saving into saveIntoUsersTable  result is from the table', responseId);
                                                    Signup.userEcashCreditlimit(res1, responseId, function(ecashRes) {
                                                        // console.log('Im saving into userEcashCreditlimit  result is from the table', ecashRes);
                                                        Signup.saveIntoRolesTable(res1, responseId, data, function(roleid) {
                                                            // console.log('Im saving into saveIntoRolesTable  result is from the table', roleid);
                                                            Signup.saveIntoUserrolesTable(roleid, responseId, function(response) {
                                                                // console.log('Im saving into saveIntoUserrolesTable  result is from the table', response);
                                                                Signup.saveIntoRoleAccess(roleid, function(response) {
                                                                    // console.log('Im saving into saveIntoRoleAccess  result is from the table', response);
                                                                    Signup.saveIntoBusinessUnitsTable(res1, data, responseId, function(busresponse) {
                                                                        console.log('Im saving into saveIntoBusinessUnitsTable  result is from the table', busresponse);
                                                                        Signup.apobIntoWarehouseTable(res1, data, function(appeal) {
                                                                            console.log('Im saving into apobIntoWarehouseTable  result', appeal);
                                                                            Signup.creatingWorkFlowsForUsers(res1, data,roleid, function(resultData) {
                                                                                 console.log('Im saving into creatingWorkFlowsForUsersTable  result',resultData);
                                                                                var response = {
                                                                                    "Status": 200,
                                                                                    "status": "success",
                                                                                    "message": "Signup Succesfully"
                                                                                };
                                                                                res.send(response);
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    });
                                });
                            });
                        }
                    });
                }
            });
        }
    },
};