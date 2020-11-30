'user strict';

const Sequelize = require('sequelize');
var database = require('../../config/mysqldb');
let db= database.DB;


module.exports={   
    getLegalEntities: async function(req,res){
        return new Promise((resolve,reject)=>{
        	var concatquery='';
        	var concatwhereqry='';
        	var legalEntityArr = [];
        	var supplierkeyvaluepair;
        	if(req.hasOwnProperty('letype') && req.letype!=''){
        		concatquery="left join users on users.legal_entity_id=legal1.legal_entity_id";
        		concatwhereqry=" and legal2.legal_entity_type_id="+req.letype+'users.is_active=1 group by legal2.legal_entity_id order by legal2.business_legal_name';
        	}
        	var query="select legal2.business_legal_name,legal2.legal_entity_id from legal_entities as legal1 left join legal_entities as legal2 on legal1.legal_entity_id=legal2.parent_id "+concatquery+" where legal2.parent_id="+req.leid+" and legal2.is_approved=1"+concatwhereqry;
        	
        	db.query(query,{},async function(err,rows){
				if(err){
					 reject(err);
				}
				if(Object.keys(rows).length >0){
					for (var i = 0; i < rows.length; i++) {
						supplierkeyvaluepair={'key':rows[i].legal_entity_id,'value': rows[i].business_legal_name};
						legalEntityArr.push(supplierkeyvaluepair);		
					}
					 resolve(legalEntityArr);
				}
				else{
					 reject("No Results found..")
				}
			});
        });
    },

    getLeParentIdByLeId:async function(leId){
       return new Promise((resolve,reject)=>{
            var query="select parent_id from legal_entities where legal_entity_id="+leId+" limit 1";
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].parent_id);
                } else {
                    return resolve([]);
                }
            });
       }); 
    },

    getLegalEntityById:async function(leId){
        return new Promise((resolve,reject)=>{
            var query="select `legal`.`business_legal_name`, `legal`.`logo`,`legal`.`le_code`, `legal`.`address1`, `legal`.`address2`, `legal`.`city`, `legal`.`logo`, `legal`.`pincode`, `legal`.`pan_number`, `legal`.`tin_number`, `legal`.`gstin`, `legal`.`fssai`, `legal`.`legal_entity_id`, `countries`.`name` as `country_name`, `zone`.`name` as `state_name`, `zone`.`name` as `state`, `zone`.`code` as `state_code` from `legal_entities` as `legal` left join `countries` on `countries`.`country_id` = `legal`.`country` left join `zone` on `zone`.`zone_id` = `legal`.`state_id` where `legal`.`legal_entity_id` = "+leId+" limit 1";
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
    },

    getLeId:async function(userId){
        return new Promise((resolve,reject)=>{
            var query="select legal_entity_id from users where user_id="+userId+"";
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].legal_entity_id);
                } else {
                    return resolve([]);
                }
            });
       });
    },

    checkGstState:async function(id){
        return new Promise((resolve,reject)=>{
            var query="select `display_name` as `business_legal_name`, `address1`, `address2`, `city`, `zone`.`name` as `state_name`, `country`, `countries`.`name` as `country_name`, `gstin`, `pincode`, `zone`.`code` as `state_code`, `phone_no`, `email`, `gstin` as `tin_number`, `fssai`, `eb_sup_le_id` from `legal_entity_gst_addresses` as `gst` left join `zone` on `zone`.`zone_id` = `gst`.`state` left join `countries` on `countries`.`country_id` = `gst`.`country` where `gst`.`state` = "+id+" limit 1";
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
}
