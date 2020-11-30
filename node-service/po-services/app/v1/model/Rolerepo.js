'user strict';

const Sequelize = require('sequelize');
const sequelize = require('../../config/sequelize');
var database = require('../../config/mysqldb');
let db= database.DB;


module.exports.checkPermissionByFeatureCode = async (featureCode, userId) => {
    return new Promise((resolve, reject) => {
 try{
    let query = `SELECT COUNT(features.name) as num FROM role_access JOIN features ON role_access.feature_id = features.feature_id 
    JOIN user_roles ON role_access.role_id= user_roles.role_id WHERE feature_code = '${featureCode}' AND is_active = 1 AND user_id =${userId};`
    sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(async (response) => {
        let result = await response[0].num;
        // console.log('response', result);
        resolve (result > 0 ) ? true : false;
    });

}
 catch(err) {
    reject(err);
}
    })
}

module.exports.getFilterData=async function(permissionLevelId,userId=0){
   try{
    var response={};
    if(permissionLevelId>0){
        if(userId){
           var currentUserId=userId;
        }
        var getPermissionLevelName=await this.getPermissionLevelData(permissionLevelId);
        if(getPermissionLevelName){
            response[getPermissionLevelName.name]=await this.getWarehouseData(currentUserId,permissionLevelId)
        }
        return JSON.stringify(response);
    }

   }catch(err){
    res.send({'status':'failed','message':'Please Try Again after sometime','data':'null'});
  }
}

module.exports.getPermissionLevelData=async function(Id){
    return new Promise((resolve,reject) =>{
       var query="select name from permission_level where permission_level_id="+Id+" limit 1";
       db.query(query,{},(err,rows)=>{
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows[0]);
            } else {
                return resolve([]);
            }
       });
    });
}

module.exports.getWarehouseData=async function(currentUserId,permissionLevelId){
    return new Promise((resolve,reject)=>{
        var response={};
        var objectIds;
        if(currentUserId>0 && permissionLevelId>0){
            this.checkPermissionByFeatureCode('GLB0001',currentUserId).then((globalFeature) => {
               this.checkPermissionByFeatureCode('GLBWH0001',currentUserId).then((inActiveDCAccess) => {
                    var get_object="select group_concat(object_id) as object_id from user_permssion where user_id="+currentUserId+" and permission_level_id="+permissionLevelId;
                    db.query(get_object,{},function(err,rows){
                        if(err){
                            return reject(err);
                        }

                        if(rows.length>0){
                            objectIds=rows[0].object_id.split(",");
                            var length="SET SESSION group_concat_max_len = 100000";
                            var query="select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type>0 AND is_disabled=0 ";
                            if(!inActiveDCAccess){
                                query+="AND status=1 ";
                            }
                            if(!globalFeature){
                                if(objectIds.length==1 || objectIds.includes('0')){
                                    if((objectIds[0] && objectIds[0]==0) || objectIds.includes('0')){
                                        query+="AND dc_type IN (118001,118002) group By `dc_type`";
                                    }else{
                                        query+="AND bu_id IN ("+objectIds+") group By `dc_type`";
                                    }
                                }else{
                                    query+="AND bu_id IN ("+objectIds+") group By `dc_type`";
                                }
                                db.query(query,{},(err,rows)=>{
                                   if (err) {
                                        reject('error');
                                    }
                                    if (Object.keys(rows).length > 0) {
                                        rows.forEach(details=>{
                                            response[details.dc_type]=details.le_wh_id;
                                        });
                                        return resolve(JSON.stringify(response));
                                    }
                                });
                            }else{
                                query+="group By `dc_type`";
                                db.query(query,{},(err,rows)=>{
                                    if (err) {
                                        reject('error');
                                    }
                                    if (Object.keys(rows).length > 0) {
                                        rows.forEach(details=>{
                                            response[details.dc_type]=details.le_wh_id;
                                        });
                                        return resolve(JSON.stringify(response)); 
                                    }
                                })
                            }
                        } else {
                            return resolve([]);
                        }
                    });
                });
            });
        }
    });
}