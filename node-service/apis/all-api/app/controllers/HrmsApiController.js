/**
 * comomoapicontroller
 *
 * @description :: logic for self signup all cutomers
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const HrmsApi = require('../models/HrmsApi');

module.exports = {
	savepolicydetails:function(req,res){
		var data = req.body.data;
		if(!data){
			var response = {"Status":400,"Message":"Bad Request","Response Body":"Invalid JSON Format"}
			return res.json(response);
		}else{
				var s3_data = sails.config.s3.aws_s3;
				req.file('file').upload({
                    adapter: require('skipper-better-s3'),
                    key: s3_data.key,
                    secret: s3_data.secret,
                    bucket: s3_data.bucket,
                    region: s3_data.region,
                    dirname: 'feedback',
                    maxBytes: 1000000,
                        maxTimeToBuffer: 100000,

                    saveAs: function (file, handler) {
                        var d = new Date();
                        var extension = file.filename.split('.').pop();
                        // generating unique filename with extension
                        var filename = d.getTime() + "." + extension;
                        handler(null, filename);
                    }
                },function (err, filesUploaded) {
                	console.log(filesUploaded);
                    if (err) {
                        console.log('s3 errorr');
                        //return res.json(500, err);
                        return res.json(200, {Status: 201,status: "failed", message: "Unable to Upload File", ResponseBody: ""});
                    }else {
            
                        if (filesUploaded.length === 0) {
                            picture_upload = null;
                        }else{
                        	picture_upload = filesUploaded[0].extra.Location;
                        }

                        //console.log(picture_upload);

                       	HrmsApi.savepolicydetails(data,picture_upload,function(response){
            			var response = {"Status": 200,"status": "success", "message": "Policy added succesfully"};
						res.send(response);
					});						
                    }
                });

		}
	},

	viewpolicies:function(req,res){
			HrmsApi.viewallpolicies(function(response){
				var response1 = {"status":"success","Message":"data found","data":response}
				res.send(response1);
		});
	},

}; 