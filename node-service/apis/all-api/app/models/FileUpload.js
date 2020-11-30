/**
 * FileUpload.js
 * @description :: Uploaded file actions
 */

const db = require('../../dbConnection');

module.exports = {
	
	updateFilePath: function(orderId, filePath, containerName, callback){
		console.log("updateFilePath:- received parameters are: "+orderId+" :: "+containerName+" :: "+filePath);

		try{
			var sql = "SELECT file_id FROM order_verification_files where order_id = ? and container_name = ?";
			db.query(sql, [orderId, containerName], function(err, result){
				if(err){ 
					console.log(err);
					sails.log(err);
					return err;
				}
				if(result.length > 0){
					//console.log(result);
					var update = "UPDATE order_verification_files SET file_path = ? WHERE file_id = ?;";
					db.query(update, [filePath, result[0].file_id], function(err, result2){
						if(err){ 
							console.log(err);
							sails.log(err);
							return err;
						}
						//console.log(result2);
						callback(result2, filePath);
					});
				} else{
					var insert = "INSERT INTO order_verification_files (order_id, container_name, file_path) VALUES (?, ?, ?)";
					db.query(insert, [orderId, containerName, filePath], function(err, result3){
						if(err){ 
							console.log(err);
							sails.log(err);
							return err;
						}
						//console.log(result3);
						callback(result3, filePath);
					});
				}
			});
		} catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err, filePath);
		}
	}
};