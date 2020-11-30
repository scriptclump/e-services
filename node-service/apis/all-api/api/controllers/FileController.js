/**
 * FileController
 *
 * @description :: Server-side logic for managing Crate related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */

module.exports = {
	uploadFile: function(req,res){
		try{
			console.log('Under upload file api');			
			var s3_data = sails.config.s3.aws_s3;		

			console.log(req._fileparser.upstreams.length);
			// console.log(req.file('upload_file')._files[0].stream.filename);

			var orderId = req.body.order_id;
			var container = req.body.container;

			console.log("Param: "+orderId+" :: "+container);
			if(orderId.length == 0 || orderId == null){
				var response = {status: 400, message: 'Order ID Missing', data: ''}
				res.json(response);
			}
			if(container.length == 0 || container == null){
				var response = {status: 400, message: 'Container name Missing', data: ''}
				res.json(response);
			}

			if(req._fileparser.upstreams.length == 0){
				var response = {status: 400, message: 'File Missing', data:''}
				res.json(response);
			} else{
				req.file('upload_file').upload({
					adapter: require('skipper-better-s3'),
					key: s3_data.key,
					secret: s3_data.secret,
					bucket: s3_data.bucket,
					region: s3_data.region,
					dirname: 'verification'
				},function(err, files) {
					if (err) {
						console.log("Error in upload: ", err);
						return res.serverError(err);
					}
					console.log(files[0].extra.Location);
					var filePath = files[0].extra.Location;
					FileUpload.updateFilePath(orderId, filePath, container, function(result, filePath){
						//console.log(result);
						res.json({ status: 200, message: 'Upload Success', data:filePath});
					});
				});					
			}
		} catch(err){
			console.error('Final Error: '+err+"\n");
			res.json({status: 400, message: 'Some error occurred', data:err});
		}
	}
};