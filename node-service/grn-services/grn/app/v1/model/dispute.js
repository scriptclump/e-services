'user strict';

const Sequelize = require('sequelize');
var sequelize = require('../../config/sequelize');
var database = require('../../config/mysqldb');
var role = require('../model/Role');
let db= database.DB;
var current_datetime = new Date();
var createddate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();

module.exports={   
    getDocumentTypes: async function(req,res){
        return new Promise((resolve,reject)=>{
            var fields="lookup.value,lookup.master_lookup_name";
            var docqry="select "+fields+" from master_lookup as lookup where lookup.mas_cat_id = 95 and is_active=1";
            db.query(docqry,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    resolve(rows);
                }
            });	
        });
    },

    getDocuments:async function(inwardId){
       return new Promise((resolve,reject)=>{
           var query="select inward_docs.doc_ref_no,inward_docs.inward_doc_id,inward_docs.doc_url, getMastLookupValue(inward_docs.doc_ref_type) as doc_type, GetUserName(inward_docs.created_by,2) as fullname from `inward_docs` where `inward_docs`.`inward_id` ="+inwardId+"";
           db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows);
                } else {
                    return resolve([]);
                }
            });
        });
   },
   
   getCommentsTransactionId:async function(inwardId){
        return new Promise((resolve,reject)=>{
           var query="select `history`.`comments`, DATE_FORMAT(history.created_at,'%Y-%m-%d %H:%i:%s') as created_at, `users`.`firstname`, `users`.`lastname`, `users`.`profile_picture`, `roles`.`name` as `roleName` from `disputes` inner join `dispute_history` as `history` on `disputes`.`dispute_id` = `history`.`dispute_id` inner join `users` on `users`.`user_id` = `history`.`created_by` inner join `user_roles` on `user_roles`.`user_id` = `users`.`user_id` inner join `roles` on `roles`.`role_id` = `user_roles`.`role_id` where `disputes`.`transaction_id` = "+inwardId+" group by `history`.`dispute_history_id` order by `history`.`dispute_history_id` desc";
           
           sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                                resolve(response);
            })
        });
   },

   getDisputIdByTransactionId:async function(transactionId){
        return new Promise((resolve,reject)=>{
           var query="select dispute_id from `disputes`  where `disputes`.`transaction_id` = "+transactionId+" limit 1";
           db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].dispute_id);
                } else {
                    return resolve(0);
                }
            });
        });
    },

    saveDispute:async function(inwardId,legalentityId,userId){
        return new Promise((resolve,reject)=>{
            var query="insert into disputes (legal_entity_id,transaction_type,transaction_id,transaction_date,reported_by,reported_at) values ("+legalentityId+",101001,"+inwardId+",'"+createddate+"',"+userId+",'"+createddate+"')";
            db.query(query,{},(err,inserted)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(inserted).length > 0) {
                    return resolve(inserted.insertId);
                } else {
                    return resolve([]);
                }
            });
        });
    },

    saveHistory:async function(disputeId,comment,userId){
        return new Promise((resolve,reject)=>{
            var query="insert into dispute_history (comments,dispute_id,created_by,created_at) values ('"+comment+"',"+disputeId+","+userId+",'"+createddate+"')";
            db.query(query,{},(err,inserted)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(inserted).length > 0) {
                    return resolve(inserted.insertId);
                } else {
                    return resolve([]);
                }
            });
        });
    },

    SaveReference:async function(doc_id,ref_value){
        return new Promise((resolve,reject)=>{
            var query=`update inward_docs set doc_ref_no='${ref_value}' where inward_doc_id=${doc_id}` 
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows.affectedRows);
                } else {
                    return resolve([]);
                }
            });
        });
    }
}