const models = require('../tables/index');
const Op = require('sequelize').Op;
const rolerepo = require('./rolerepo');
const sequelize = require('sequelize');

module.exports = {
	/**
	 * [getWarehouseData To get user speciifc warehouse access information]
	 * @param  {[int]} userId            [user id]
	 * @param  {[int]} permissionlevelId [permission level Id]
	 * @param  {Number} active            [Flag to get only active dc's list or active+inactive]
	 * @return {[array]}                   [hubs list that user has access to]
	 */
	getWarehouseData: function (userId, permissionlevelId, active = 1) {
		return new Promise((resolve, reject) => {
			let response = [];
			if(userId>0 && permissionlevelId>0){
				console.log('jjjj');
				rolerepo.checkPermissionByFeatureCode('GLB0001',userId).then(res=>{
					let globalFeature = res;
					rolerepo.checkPermissionByFeatureCode('GLBWH0001',userId).then(res2=>{
						let inActiveDCAccess = res2;
						if(active == 0){
							inActiveDCAccess = 1;
						}
						models.user_permssion.findAll({where:{user_id:userId,permission_level_id:permissionlevelId}, group: ['object_id'],attributes:['object_id']}).then(data=>{
							//console.log('**',data);

							let whereStatement={};
							let hubId=0;
							whereStatement.dc_type = {$gt: 0};
							if(inActiveDCAccess == 0){
								whereStatement.status =1;
							}
							if(data.length>0){
								module.exports.getWhIdByPermissionLevel(globalFeature,data,whereStatement).then(res3=>{
									console.log(res3[118002]);
									if(res3[118002]){
										hubId= res3[118002];
									}
									resolve(hubId);
								})
							}else{
									resolve(hubId);
							}
						})
					});

				});
			}
		})
		
	},
	/**
	 * [getWhIdByPermissionLevel To get warehouse and hubs list that user has access to]
	 * @param  {[int]} globalFeature  [Flag whether the user global access or not]
	 * @param  {[Object]} data           [warehouse id's]
	 * @param  {[string]} whereStatement [Adding conditions]
	 * @return {[array]}                [List of warehouses]
	 */
	getWhIdByPermissionLevel: function (globalFeature, data, whereStatement) {
		let object_ids = data.map(ele => ele.object_id);
		return new Promise((resolve,reject)=>{
			if(!globalFeature){
				if(object_ids.indexOf(0)!=-1){
					whereStatement.dc_type = {$in: [118001,118002]};
				}else{
					whereStatement.bu_id = {$in: object_ids};
				}
				models.sequelize.query("SET SESSION group_concat_max_len = 100000").then(sessiondata=>{
					//console.log('--session--',sessiondata);
					models.legalentity_warehouses.findAll({where:whereStatement,attributes:['dc_type',
						[sequelize.fn('GROUP_CONCAT',sequelize.col('le_wh_id')),'le_wh_id']],group:['dc_type']}).then(wh_data=>{
							//console.log(wh_data);
							//resolve(wh_data);
							if(wh_data.length >0){
								let whData = JSON.parse(JSON.stringify(wh_data));
								let response=[];
								whData.forEach(data=>{
									console.log(data);
									response[data.dc_type] = data.le_wh_id;
								});
								resolve(response);
							}
						})

				})

			}else{
				
				models.sequelize.query("SET SESSION group_concat_max_len = 100000").then(sessiondata=>{
					models.legalentity_warehouses.findAll({where:whereStatement,attributes:['dc_type',[
						sequelize.fn('GROUP_CONCAT',sequelize.col('le_wh_id')),'le_wh_id']],group:['dc_type']}).then(wh_data=>{
							//console.log(wh_data);
							//resolve(wh_data);
							if(wh_data.length >0){
								let whData = JSON.parse(JSON.stringify(wh_data));
								let response=[];
								whData.forEach(data=>{
									console.log(data);
									response[data.dc_type] = data.le_wh_id;
								});
								resolve(response);
							}
						})
				});
			}
		});						
			
	}
}