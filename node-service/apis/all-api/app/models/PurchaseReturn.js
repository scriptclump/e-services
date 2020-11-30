/**
 * PurchaseReturn.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

var _ = require('lodash');
var dateFormat = require('moment-timezone');
const db = require('../../dbConnection');

module.exports = {
    getOpenPRList: function (status,type, picker_id,fdate,tdate,offset,perpage, callback) {
        var sql = "SELECT pr.pr_id,pr.pr_code,pr.le_wh_id,pr.legal_entity_id,"
                + "getMastLookupValue(pr.approval_status) as approvalstatus,"
                + "pr.approval_status,pr.pr_grand_total,pr.picker_id,"
                + "GetUserName(pr.picker_id,2) as picker_name,getLeWhName(pr.le_wh_id) as warehouse_name,leg.business_legal_name,"
                + "(select count(pr_product_id) from purchase_return_products as prd where prd.pr_id = pr.pr_id) as line_item_count"
                + " FROM purchase_returns as pr"
                + " JOIN legal_entities as leg ON leg.legal_entity_id=pr.legal_entity_id"
                + " where ";
        var vals = [];
        if (status != '' && Array.isArray(status)) {
            console.log(status);
            sql += " pr.approval_status IN (?) and ";
                vals.push(status);
        }
        if (type == 'assigned') {
            sql += " pr.picker_id!=0 and";
            if (picker_id != '' && picker_id > 0) {
                sql += " pr.picker_id= ? and";
                vals.push(picker_id);
            }
        } else if(type == 'unassigned') {
            sql += " pr.picker_id=0 and";
        }
        if(fdate != '' && tdate != '') {
            sql += " pr.created_at between  '"+fdate+" 00:00:00' and '"+tdate+" 23:59:59'";
        }        
        sql=sql.replace(/and\s*$/, "");
        sql=sql.replace(/where\s*$/, "");
        sql += " limit "+(offset*perpage)+","+perpage;
        console.log(sql);
        db.query(sql, vals, function (err, result) {
            if (err) {
                console.log(err);
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getPRDetails: function (pr_id, callback) {
        var sql = "SELECT pr.pr_id,pr.pr_code,pr.le_wh_id,pr.legal_entity_id,"
                + "getMastLookupValue(pr.approval_status) as approvalstatus,"
                + "pr.approval_status,pr.pr_grand_total,pr.picker_id,"
                + "GetUserName(pr.picker_id,2) as picker_name,getLeWhName(pr.le_wh_id) as warehouse_name,leg.business_legal_name,"
                + "gdsp.sku,gdsp.product_title"
                +",getMastLookupValue(prd.uom) as uom_name,getMastLookupValue(prd.free_uom) as free_uom_name,"
                + "prd.*"
                + " FROM purchase_returns as pr"
                + " JOIN purchase_return_products as prd ON prd.pr_id=pr.pr_id"
                + " JOIN products as gdsp ON gdsp.product_id=prd.product_id"
                + " JOIN legal_entities as leg ON leg.legal_entity_id=pr.legal_entity_id"
                + " where pr.pr_id = ?";
        db.query(sql, [pr_id], function (err, result) {
            if (err) {
                console.log(err);
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    assignPickerToPR: function (pr_id, picker_id , callback) {
        _.forEach(pr_id, function(prId){
            var sql = "UPDATE `purchase_returns`"
                +"SET `picker_id` = ? "
                +"WHERE `pr_id`= ?";
            db.query(sql, [picker_id,prId], function (err, result) {
                if (err) {
                    console.log(err);
                    sails.log(err);
                    return err;
                }                
            });
        });
        callback(1);        
    },
    checkCustomerToken: function (token, callback) {
        var sql = "SELECT count(u.user_id) as count FROM users as u where u.password_token = ? OR u.lp_token = ? ";
        db.query(sql, [token, token], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    }
};

