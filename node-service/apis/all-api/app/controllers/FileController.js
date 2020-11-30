/**
 * FileController
 *
 * @description :: Server-side logic for managing Crate related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const FileUpload = require('../models/FileUpload');
const s3data = require('../../config/s3.js')

let multer=require('multer');
const multers3 = require('multer-s3');
const aws = require('aws-sdk');
aws.config.update({
	accessKeyId: s3data.key,
    secretAccessKey: s3data.secret,
    region: s3data.region
});
const s3 = new aws.S3;
const upload = multer({
	storage: multers3({
		s3: s3,
		bucket:'ebutormedia-test',
		acl: 'public-read',
		metadata: function (req, file, cb) {
	      cb(null, {fieldName: file.fieldname});
	    },
	    key: function (req, file, cb) {
	      cb(null, Date.now().toString()+'.'+file.originalname.split('.')[1])
	    }
	})
});
module.exports = upload;
module.exports = {
	uploadFile: function(req,res){
		try{
			//console.log('Under upload file api',req);			

			// console.log(req.file('upload_file')._files[0].stream.filename);

			var orderId = req.body.order_id;
			var container = req.body.container;

			console.log("Param: "+orderId+" :: "+container);
			if(orderId.length == 0 || orderId == null){
				var response = {status: 400, message: 'Order ID Missing', data: ''}
				return res.json(response);
			}
			if(container.length == 0 || container == null){
				var response = {status: 400, message: 'Container name Missing', data: ''}
				return res.json(response);
			}

			const singleUpload = upload.single('upload_file');

			singleUpload(req,res,function(err,data){

				if(err){
					return res.status(422).send({errors: [{title: 'Image Upload Error', detail: err.message}] });
					console.log(req.file);
				}else{
					FileUpload.updateFilePath(orderId,req.files.upload_file[0].location,container,(result,filepath)=>{
						return res.json({ status: 200, message: 'Upload Success', data:filepath});
					});
				}
			});
			
		} catch(err){
			console.error('Final Error: '+err+"\n");
			res.json({status: 400, message: 'Some error occurred', data:err});
		}
	}
};