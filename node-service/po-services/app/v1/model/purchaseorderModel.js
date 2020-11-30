const Sequelize = require('sequelize');
var sequelize = require('../../config/sequelize');
const moment = require('moment');
var express = require('express');
// var router = express.Router();

var purchaseOrder = require('../controller/purchaseorderController');
let roleModel = require('./Rolerepo.js')
var auth = require('../middleware/auth');
var database = require('../../config/mysqldb');
let db = database.DB;
let con = db;


module.exports.getAllPurchasedOrders = async (userId, poStatusId, approvalStatusId, rowCnt = 0, fromDate, toDate, filter, perPage, offset, sortField = "po_id", sortType = "desc") => {
    try {
        return new Promise(async (resolve, reject) => {

            // const currentDateStart = moment().format(("YYYY-MM-DD 00:00:00"));
            const currentDateEnd = moment().format(("YYYY-MM-DD 23:59:59"));
            // const inputDateStart = moment().format("YYYY-MM-DD 00:00:00");
            // const inputDateEnd = moment().format("YYYY-MM-DD 23:59:59");
            // const previousDateStart = moment().subtract(1, 'days').format(("YYYY-MM-DD 00:00:00"));
            // const previousDateEnd = moment().subtract(1, 'days').format(("YYYY-MM-DD  23:59:59"));
            let userData = await this.checkUserIsSupplier(userId);
            let globalFeature = await roleModel.checkPermissionByFeatureCode('GLB0001', userId);//response true/false
            let inActiveDCAccess = await roleModel.checkPermissionByFeatureCode('GLBWH0001', userId);//response true/false


            let query = `SELECT po.le_wh_id, po.legal_entity_id, 
            po.po_id, po.po_code, 
            po.parent_id, po.po_validity, 
            po.payment_mode, po.payment_due_date, 
            po.tlm_name, po.po_status, 
            po.approval_status AS approval_status_val, 
            po.po_so_order_code, IF(po.approval_status=1,"Shelved", 
            getMastLookupValue(po.approval_status)) AS approval_status, 
            getMastLookupValue(po.payment_status) AS payment_status, 
            po.created_at, po.po_date, (SELECT SUM(po_products.sub_total) FROM po_products
             WHERE po_products.po_id=po.po_id) AS poValue, GetUserName(po.created_by,2) AS user_name, 
             (SELECT SUM(inward.grand_total) FROM inward WHERE inward.po_no=po.po_id) AS grn_value, 
             ((SELECT SUM(po_products.sub_total) FROM po_products 
             WHERE po_products.po_id=po.po_id)-(SELECT SUM(inward.grand_total) FROM inward 
             WHERE inward.po_no=po.po_id)) AS po_grn_diff, (SELECT inward.created_at FROM inward 
              WHERE inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1) AS grn_created, 
              p1.po_code AS po_parent_code, p1.po_id AS po_parent_id,
               currency.code AS currency_code, currency.symbol_left AS symbol,
                legal_entities.business_legal_name, legal_entities.le_code, lwh.lp_wh_name, 
                lwh.city, lwh.pincode, lwh.address1 FROM po INNER JOIN legal_entities ON 
              legal_entities.legal_entity_id = po.legal_entity_id LEFT JOIN po AS p1 
              ON p1.po_id = po.parent_id INNER JOIN legalentity_warehouses AS lwh 
              ON lwh.le_wh_id = po.le_wh_id `
            if (!globalFeature) {
                query += ` inner join user_permssion as up on case when up.object_id=0 then 1 else up.object_id=lwh.bu_id end 
                            and up.user_id = ${userId} and up.permission_level_id = 6`
            }

            query += ` LEFT JOIN currency ON currency.currency_id = po.currency_id `

            if (!inActiveDCAccess) {
                query += ` WHERE (lwh.status = 1)`
            } else {
                query += ` WHERE lwh.status in (0,1)`
            }

            //  query += ` WHERE lwh.status = 1`;
            if (poStatusId != "" && poStatusId != 0) {
                if (poStatusId == 87001) {
                    query += ` AND po.po_status = 87001 and po.approval_status != 57117`
                }
                else if (poStatusId == 87004) {
                    query += ` AND (po.po_status = 87004 or po.approval_status = 57117)`
                }

            }

            //approvalStatusId ==== 'initiated':57106,'created':57029,'verified':57030,'approved':57031,'posit':57033,'checked':57107,'receivedatdc':57034,'grncreated':57035,'shelved':1,'payments':57032
            else if (approvalStatusId != "" && approvalStatusId != 0) {
                if (approvalStatusId == 57106 || approvalStatusId == 57029 || approvalStatusId == 57030 || approvalStatusId == 57031 || approvalStatusId == 57033 || approvalStatusId == 57035) {
                    query += ` AND  po.approval_status = ${approvalStatusId}`
                }
                else if (approvalStatusId == 57034) { // receivedatdc
                    query += ` AND  po.approval_status in (57122, 57034) and po.approval_status not in (57117)`
                }
                else if (approvalStatusId == 57107) { // checked
                    query += ` AND  po.approval_status in (57119, 57120, 57107) and po.approval_status not in (57117)`

                }
                else if (approvalStatusId == 57032) { // payments 
                    query += ` AND  (po.payment_mode = 2 or po.payment_due_date <= '${currentDateEnd}') 
                    and (po.payment_status = 57118 or po.payment_status is null) 
                    and po.approval_status not in (57117, 57106, 57029, 57030)`
                }
                else if (approvalStatusId == 1) { // shelved
                    query += ` AND  po.approval_status = 1 and po.po_status not in (87003, 87004) `
                }
                query += ` AND po.po_status not in (87003, 87004)`
            }

            if (Object.keys(filter).length > 0) {
                if (filter['poCode'] != null && filter['poCode'] != "") {
                    if (filter['poCode'][1] == 'contains') {
                        query += `  AND po.po_code LIKE '%${filter['poCode'][0]}%' `
                    } else {
                        query += `  AND po.po_code = '${filter['poCode'][0]}' `
                    }
                } if (filter['le_code'] != null && filter['le_code'] != "") {
                    if (filter['le_code'][1] == 'contains') {
                        query += `  AND legal_entities.le_code LIKE '%${filter['le_code'][0]}%' `
                    } else {
                        query += `  AND legal_entities.le_code = '${filter['le_code'][0]}' `
                    }
                } if (filter['Supplier'] != null && filter['Supplier'] != "") {
                    if (filter['Supplier'][1] == 'contains') {
                        query += `  AND legal_entities.business_legal_name LIKE '%${filter['Supplier'][0]}%' `
                    } else {
                        query += `  AND legal_entities.business_legal_name = '${filter['Supplier'][0]}' `
                    }
                } if (filter['shipTo'] != null && filter['shipTo'] != "") {
                    if (filter['shipTo'][1] == 'contains') {
                        query += `  AND lwh.lp_wh_name LIKE '%${filter['shipTo'][0]}%' `
                    } else {
                        query += `  AND lwh.lp_wh_name = '${filter['shipTo'][0]}' `
                    }
                } if (filter['validity'] != null && filter['validity'] != "") {
                    if (filter['validity'][1] == 'contains') {
                        query += `  AND po.po_validity LIKE '%${filter['validity'][0]}%' `
                    } else {
                        query += `  AND po.po_validity = '${filter['validity'][0]}' `
                    }
                } if (filter['payment_mode'] != null) {
                    let str = 'p'; str1 = 'post paid'; str2 = 'pre paid';
                    let value = (filter['payment_mode'][0]).toLowerCase();
                    if (str.includes(value)) {
                        query += `  and po.payment_mode in (1,2)`
                    } else if (str1.includes(value)) {
                        query += `  and po.payment_mode in (1)`
                    } else if (str2.includes(value)) {
                        query += `  and po.payment_mode in (2)`
                    } else {
                        query += `  and po.payment_mode not in (1,2)`
                    }
                }
                if (filter['payment_due_date'] != null) {
                    const dateStart = moment(filter['payment_due_date'][0]).format(("YYYY-MM-DD 00:00:00"));
                    const dateEnd = moment(filter['payment_due_date'][0]).format(("YYYY-MM-DD 23:59:59"));
                    query += ` AND po.payment_due_date`
                    if (filter['payment_due_date'][1] == 'on') {
                        query += ` between '${dateStart}' AND '${dateEnd}'`
                    } else if (filter['payment_due_date'][1] == 'after') {
                        query += ` > '${dateEnd}' `
                    } else if (filter['payment_due_date'][1] == 'before') {
                        query += `  < '${dateStart}' `
                    } else if (filter['payment_due_date'][1] == 'today') {
                        query += ` between '${dateStart}' AND '${dateEnd}' `
                    } else if (filter['payment_due_date'][1] == 'yesterday') {
                        query += ` between '${dateStart}' AND '${dateEnd}' `
                    }
                }
                if (filter['tlm_name'] != null && filter['tlm_name'] != "") {
                    if (filter['tlm_name'][1] == 'contains') {
                        query += `  AND po.tlm_name LIKE '%${filter['tlm_name'][0]}%' `
                    } else {
                        query += `  AND po.tlm_name = '${filter['tlm_name'][0]}' `
                    }
                } if (filter['createdBy'] != null && filter['createdBy'] != "") {
                    if (filter['createdBy'][1] == 'contains') {
                        query += `  AND GetUserName(po.created_by,2) LIKE '%${filter['createdBy'][0]}%' `
                    } else {
                        query += `  AND GetUserName(po.created_by,2) = '${filter['createdBy'][0]}' `
                    }
                } if (filter['createdOn'] != null) {
                    const dateStart = moment(filter['createdOn'][0]).format(("YYYY-MM-DD 00:00:00"));
                    const dateEnd = moment(filter['createdOn'][0]).format(("YYYY-MM-DD 23:59:59"));
                    if (filter['createdOn'][1] == 'on') {
                        query += ` AND po.po_date between '${dateStart}' AND '${dateEnd}'`
                    } else if (filter['createdOn'][1] == 'after') {
                        query += ` AND po.po_date > '${dateEnd}' `
                    } else if (filter['createdOn'][1] == 'before') {
                        query += ` AND po.po_date < '${dateStart}' `
                    } else if (filter['createdOn'][1] == 'today') {
                        query += ` AND po.po_date  between '${dateStart}' AND '${dateEnd}' `
                    } else if (filter['createdOn'][1] == 'yesterday') {
                        query += ` AND po.po_date  between '${dateStart}' AND '${dateEnd}' `
                    }
                }
                if (filter['approval_status'] != null && filter['approval_status'] != "") {
                    if (filter['approval_status'][1] == 'contains') {
                        query += `  AND IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) LIKE '%${filter['approval_status'][0]}%' `
                    } else {
                        query += `  AND IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) = '${filter['approval_status'][0]}' `
                    }
                } if (filter['payment_status'] != null && filter['payment_status'] != "") {
                    if (filter['payment_status'][1] == 'contains') {
                        query += `  AND getMastLookupValue(po.payment_status) LIKE '%${filter['payment_status'][0]}%' `
                    } else {
                        query += `  AND getMastLookupValue(po.payment_status) = '${filter['payment_status'][0]}' `
                    }
                }
                if (filter['grn_created'] != null && filter['grn_created'] != "") {
                    const dateStart = moment(filter['grn_created'][0]).format(("YYYY-MM-DD 00:00:00"));
                    const dateEnd = moment(filter['grn_created'][0]).format(("YYYY-MM-DD 23:59:59"));
                    query += ` AND (select inward.created_at from inward 
                    where inward.po_no = po.po_id ORDER BY 
                    created_at DESC LIMIT 1) `
                    if (filter['grn_created'][1] == 'on') {
                        query += ` between '${dateStart}' AND '${dateEnd}' `
                    } else if (filter['grn_created'][1] == 'after') {
                        query += `  > '${dateEnd}' `
                    } else if (filter['grn_created'][1] == 'before') {
                        query += ` < '${dateStart}' `
                    } else if (filter['grn_created'][1] == 'today') {
                        query += `   between '${dateStart}' AND '${dateEnd}' `
                    } else if (filter['grn_created'][1] == 'yesterday') {
                        query += ` between '${dateStart}' AND '${dateEnd}' `
                    }
                }
                if (filter['po_so_order_link'] != null && filter['po_so_order_link'] != "") {
                    if (filter['po_so_order_link'][1] == 'contains') {
                        query += `  AND po.po_so_order_code LIKE '%${filter['po_so_order_link'][0]}%' `
                    } else {
                        query += `  AND po.po_so_order_code = '${filter['po_so_order_link'][0]}' `
                    }
                } if (filter['po_parent_link'] != null && filter['po_parent_link'] != "") {
                    if (filter['po_parent_link'][1] == 'contains') {
                        query += `  AND p1.po_code LIKE '%${filter['po_parent_link'][0]}%' `
                    } else {
                        query += `  AND p1.po_code = '${filter['po_parent_link'][0]}' `
                    }
                }
            }
            if (fromDate != null && toDate != null && fromDate != "" && toDate != "") {
                query += ` AND po.po_date between '${fromDate + ' 00:00:00'}' AND '${toDate + ' 23:59:59'}'`
            }


            query += ` group by po.po_id`

            //below queries shall be performed after group by in sql 
            if (filter['grn_value'] != null && filter['grn_value'] != "") {
                if (filter['grn_value'][1] == '=') {
                    query += `  having ROUND(grn_value,2) = '${filter['grn_value'][0]}' `
                } else if (filter['grn_value'][1] == '>') {
                    query += ` having ROUND(grn_value,2) > '${filter['grn_value'][0]}' `
                } else {
                    query += ` having ROUND(grn_value,2) < '${filter['grn_value'][0]}' `
                }
            }

            if (filter['po_grn_diff'] != null && filter['po_grn_diff'] != "") {
                if (filter['po_grn_diff'][1] == '=') {
                    query += `   having ROUND(po_grn_diff,2) = '${filter['po_grn_diff'][0]}' `
                } else if (filter['grn_value'][1] == '>') {
                    query += `  having ROUND(po_grn_diff,2) > '${filter['po_grn_diff'][0]}' `
                } else {
                    query += `  having ROUND(po_grn_diff,2) < '${filter['po_grn_diff'][0]}' `
                }
            }

            if (filter['poValue'] != null && filter['poValue'] != "") {
                if (filter['poValue'][1] == '=') {
                    query += `  having ROUND(poValue,2) = '${filter['poValue'][0]}' `
                } else if (filter['poValue'][1] == '>') {
                    query += `  having ROUND(poValue,2) > '${filter['poValue'][0]}' `
                } else {
                    query += `  having ROUND(poValue,2) < '${filter['poValue'][0]}' `
                }
            }


            if (rowCnt == 1) {
                query += ` order by po_id desc;`
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    resolve(response.length);
                })
            } else {
                query += ` order by ${sortField} ${sortType} limit ${perPage} offset ${offset};`
                // console.log('query', query);
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    resolve(response);
                })
            }
        })
    } catch (err) {
        reject(err);
        console.log('allPurchaseOrder Error', err);
    }

}




// SHOULD BE DELETED IF NO LINK TO BE PASSED FOR SO NO.
// module.exports.getOrderIdByCode = async (orderCode) => {
//  try {
//      return new Promise((resolve, reject) => {
//          let query = `SELECT gds_order_id FROM gds_orders WHERE order_code = ${orderCode} ORDER BY created_at DESC;`

//          sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
//              console.log(response[0].gds_order_id);
//              resolve(response[0].gds_order_id);
//          })
//      })
//      } catch (err) {
//          console.log(err);
//          reject(err);
//      }
// }



module.exports.getSuppliersList = async function (le_id, user_id, $active = 1) {

    return new Promise((resolve, reject) => {
        let legalentityquery = 'select `legal_entity_type_id` from `legal_entities` where `legal_entity_id` = ' + le_id + ' limit 1';
        db.query(legalentityquery, {}, (err, le_type) => {
            // console.log('inside db');
            if (err) {
                console.log('err',err);
                reject('Bad request');
            } else {
                let le_type_id = le_type[0].legal_entity_type_id;
                let permissionlevel = 'select `name` from `permission_level` where `permission_level_id` = 6 limit 1';
                db.query(permissionlevel, {}, (err, per_level) => {
                    if (err) {
                        reject('Bad request');
                    } else {
                        if (per_level.length > 0) {
                            let per_level_name = per_level[0].name;
                            let featurecheck1 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` = 1)';
                            db.query(featurecheck1, [user_id, 'GLB0001'], (err, fea_check1) => {
                                if (err) {
                                    reject('Bad request');
                                } else {
                                    if (fea_check1.length > 0) {
                                        let global_aceess = fea_check1[0].aggregate;
                                        let featurecheck2 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` =1)';
                                        db.query(featurecheck2, [user_id, 'GLBWH0001'], (err, fea_check2) => {
                                            if (err) {
                                                reject('Bad request');
                                            } else {
                                                let global_wh_aceess = fea_check2[0].aggregate;
                                                let userpermission = 'select group_concat(distinct(`object_id`)) as bu_id from `user_permssion` where (`user_id` = ? and `permission_level_id` = 6)';
                                                db.query(userpermission, [user_id], (err, user_per) => {
                                                    if (err) {
                                                        reject('Bad request');
                                                    } else {
                                                        let dc_list = {};
                                                        // console.log('userrrrr', user_per);
                                                        let bu_id = user_per[0].bu_id;
                                                        let bu_list = (bu_id != null && bu_id != '') ? bu_id.split(',') : [];
                                                        let getDcList = 'select GROUP_CONCAT(le_wh_id) as le_wh_id, `dc_type` from `legalentity_warehouses` where `dc_type` > 0 ';
                                                        if (!global_aceess) {
                                                            if (!bu_list.includes('0'))
                                                                getDcList = getDcList + ' and bu_id in (' + bu_id + ')';
                                                            else
                                                                getDcList = getDcList + ' and dc_type in (118001,118002)';
                                                        }
                                                        getDcList = getDcList + ' group by `dc_type`';
                                                        let dbsessionset = 'SET SESSION group_concat_max_len = 100000';
                                                        getDcList = dbsessionset + ";" + getDcList;
                                                        // console.log('check dclistttt',global_aceess, getDcList )
                                                        db.query(getDcList, {}, (err, dc_data) => {
                                                            if (err) {
                                                                reject('Bad request');
                                                            } else {
                                                                // console.log("sesoooo111111111111111", dc_data[1]);
                                                                let dc_list = {};
                                                                dc_data[1].forEach(dc_specific => {
                                                                    let { dc_type } = dc_specific;
                                                                    // console.log('dc_type_eded');
                                                                    // console.log(dc_type);
                                                                    dc_list[dc_type] = dc_specific.le_wh_id;
                                                                })

                                                                let supplierlist = 'select `legal_entities`.`legal_entity_id`,legal_entities.le_code, `legal_entities`.`legal_entity_type_id`, CONCAT_WS(" - ",`legal_entities`.`business_legal_name`,getMastLookupValue(legal_entities.legal_entity_type_id)) AS business_legal_name from `legal_entities` inner join `suppliers` on `suppliers`.`legal_entity_id` = `legal_entities`.`legal_entity_id` where `legal_entities`.`legal_entity_type_id` = ? and `suppliers`.`is_active` = 1 and `legal_entities`.`is_approved` = 1 and `parent_id` = ?';
                                                                db.query(supplierlist, [1002, le_id], (err, supplier_list) => {
                                                                    //console.log("suplllllllllllllllllllllll",supplier_list);
                                                                    if (err) {
                                                                        reject('Bad request');
                                                                    } else {
                                                                        let supplierList = supplier_list;
                                                                        let universalSupplier = 'select `description` from `master_lookup` where `value` = ? limit 1';
                                                                        db.query(universalSupplier, [78023], (err, universal_Supplier) => {
                                                                            if (err) {
                                                                                reject('Bad request');
                                                                            } else {
                                                                                let unisupplier = universal_Supplier[0].description;
                                                                                let legal_id = "select `legal_entity_id`, `legal_entity_type_id`, `business_legal_name`, (SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype from `legal_entities` where `legal_entity_id` = ? limit 1";

                                                                                db.query(legal_id, [unisupplier], (err, legal_Id) => {
                                                                                    if (err) {
                                                                                        reject('Bad request');
                                                                                    } else {
                                                                                        let dc_fc_mapping_data = 'select *, (SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype from `legal_entities` left join `dc_fc_mapping` on `dc_le_id` = `legal_entities`.`legal_entity_id` where `dc_fc_mapping`.`fc_le_id` = ?';
                                                                                        db.query(dc_fc_mapping_data, [le_id], (err, Dc_Fc_Mapping_Data) => {
                                                                                            if (err) {
                                                                                                reject('Bad request');
                                                                                            } else {
                                                                                                Dc_Fc_Mapping_Data.forEach(dc_fc => {
                                                                                                    let dc_fc_legal_entity_type_id = dc_fc.hasOwnProperty('legal_entity_type_id') ? dc_fc.legal_entity_type_id : 0;
                                                                                                })
                                                                                                // console.log('cekkk', Dc_Fc_Mapping_Data);
                                                                                                if (le_type_id == 1001) {
                                                                                                    le_type_id = [1014, 1016];
                                                                                                    let DC_data = (dc_data[1][0].le_wh_id).split(',');
                                                                                                    let dbsessionset1 = 'SET SESSION group_concat_max_len = 100000';
                                                                                                    let fc_dc_legal_entities = 'select GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,",",fc_le_id) ) AS dc_le_id from `dc_fc_mapping` where `dc_fc_mapping`.`dc_le_wh_id` in (?) or `dc_fc_mapping`.`fc_le_wh_id` in (?) limit 1';
                                                                                                    fc_dc_legal_entities = dbsessionset1 + ";" + fc_dc_legal_entities;
                                                                                                    // console.log('checkkkkkk',fc_dc_legal_entities, DC_data  );
                                                                                                    db.query(fc_dc_legal_entities, [DC_data, DC_data], (err, Fc_Dc_Legal_Entities) => {
                                                                                                        // console.log("Fc_Dc_Legal_Entitiesssssssssss", Fc_Dc_Legal_Entities[1]);
                                                                                                        if (err) {
                                                                                                            reject('Bad request');
                                                                                                        } else {
                                                                                                            let Fc_Dc_LegalEntities = Fc_Dc_Legal_Entities[1][0].hasOwnProperty('dc_le_id') ? Fc_Dc_Legal_Entities[1][0].dc_le_id : "";
                                                                                                            let FcDcLegalEntities = (Fc_Dc_LegalEntities).split(',');
                                                                                                            // console.log('check thissss', FcDcLegalEntities);
                                                                                                            if (Fc_Dc_LegalEntities != "") {
                                                                                                                let dcfcList = 'select `legal_entities`.`legal_entity_id`,legal_entities.le_code, `legal_entities`.`legal_entity_type_id`,CONCAT_WS(" - ",`legal_entities`.`business_legal_name`,getMastLookupValue(legal_entities.legal_entity_type_id)) AS business_legal_name from `legal_entities` where `legal_entities`.`legal_entity_id` in (?) and `legal_entities`.`legal_entity_type_id` in (1014,1016)';
                                                                                                                db.query(dcfcList, [FcDcLegalEntities], (err, DcFcList) => {
                                                                                                                    if (err) {
                                                                                                                        reject('Bad request ');
                                                                                                                    } else {
                                                                                                                        DcFcList.forEach(Dc_Fc_List => {
                                                                                                                            let dc_fc_list_legal_entity_type_id = Dc_Fc_List.hasOwnProperty('legal_entity_type_id') ? Dc_Fc_List.legal_entity_type_id : 0;
                                                                                                                        })
                                                                                                                        let supplier_list = [...DcFcList, ...legal_Id, ...supplierList];
                                                                                                                        // console.log('sup',DcFcList);
                                                                                                                        // console.log('sup',legal_Id);
                                                                                                                        // console.log('sup', supplierList);
                                                                                                                        resolve(supplier_list);

                                                                                                                    }

                                                                                                                })
                                                                                                            }
                                                                                                        }
                                                                                                    })
                                                                                                } else {
                                                                                                    resolve(Dc_Fc_Mapping_Data);
                                                                                                }
                                                                                            }
                                                                                        })
                                                                                    }
                                                                                })
                                                                            }
                                                                        });

                                                                    }
                                                                });

                                                            }


                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    } else {
                                        console.log("no data from fea_check1");
                                        reject('error in fea_check1');
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });
    })

}


// module.exports.checkLpToken = function (lp_token) {
//  return new Promise((resolve, reject) => {
//      let response = [];
//      let string = JSON.stringify(lp_token)
//      let data = "select user_id,legal_entity_id FROM users WHERE lp_token =" + string;
//      db.query(data, {}, function (err, rows) {
//          if (err) {
//              reject(err);
//          }
//          if (Object.keys(rows).length > 0) {
//              response.push(rows[0]);
//              resolve(response[0].counts);
//          }
//          else {
//              reject("No mapping found..")
//          }
//      });
//  });
// }

module.exports.checkUserId = function (user_id) {
    return new Promise((resolve, reject) => {
        var data = "select user_id from users where user_id =" + user_id;
        db.query(data, {}, async function (err, res) {
            if (err) {
                reject(err);
            }
            if (Object.keys(res).length > 0) {
                resolve(1);
            } else {
                reject("No mapping found..");
            }
        });
    });
}

module.exports.getIndentList = async function (le_id, user_id) {
    return new Promise((resolve, reject) => {

        let pofeature = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` = 1)';
        db.query(pofeature, [user_id, 'PO002'], (err, po_fea) => {
            if (err) {
                reject('Bad request');
            } else {
                let po_feature = po_fea[0].aggregate;
                let permissionlevel = 'select `name` from `permission_level` where `permission_level_id` = 6 limit 1';
                db.query(permissionlevel, {}, (err, per_level) => {
                    if (err) {
                        reject('Bad request');
                    } else {
                        let per_level_name = per_level[0].name;
                        let featurecheck1 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` = 1)';
                        db.query(featurecheck1, [user_id, 'GLB0001'], (err, fea_check1) => {
                            if (err) {
                                reject('Bad request');
                            } else {
                                let global_aceess = fea_check1[0].aggregate;
                                let featurecheck2 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` =1)';
                                db.query(featurecheck2, [user_id, 'GLBWH0001'], (err, fea_check2) => {
                                    if (err) {
                                        reject('Bad request');
                                    } else {
                                        let global_wh_aceess = fea_check2[0].aggregate;
                                        let userpermission = 'select group_concat(distinct(`object_id`)) as bu_id from `user_permssion` where (`user_id` = ? and `permission_level_id` = 6)';
                                        db.query(userpermission, [user_id], (err, user_per) => {
                                            if (err) {
                                                reject('Bad request');
                                            } else {
                                                let dc_list = {};
                                                let bu_id = user_per[0].bu_id;
                                                let bu_list = (bu_id != null && bu_id != '') ? bu_id.split(',') : [];
                                                let getDcList = 'select GROUP_CONCAT(le_wh_id) as le_wh_id, `dc_type` from `legalentity_warehouses` where `dc_type` > 0 ';
                                                if (!global_aceess) {
                                                    if (!bu_list.includes(0))
                                                        getDcList = getDcList + ' and bu_id in (' + bu_id + ')';
                                                    else
                                                        getDcList = getDcList + ' and dc_type in (118001,118002)';

                                                }
                                                getDcList = getDcList + ' group by `dc_type`';
                                                // console.log('llllllllllllllllllllllllll', getDcList);
                                                db.query(getDcList, {}, (err, dc_data) => {
                                                    if (err) {
                                                        reject('Bad request');
                                                    } else {

                                                        let le_wh_id = dc_data[0].le_wh_id;
                                                        // console.log("XXXXXXXXXXXXXXXXXXXX", dc_data);
                                                        let indentsList = 'select `indent_id`, `indent_code` from `indent` where (`indent_status` = ?) and `le_wh_id` in(?)and `legal_entity_id` is not null order by `indent_id` desc';
                                                        db.query(indentsList, [[70001], [le_wh_id], le_id], (err, indent_list) => {
                                                            if (err) {
                                                                reject('Bad request');
                                                            } else {
                                                                // console.log('indent_list', indent_list);

                                                                resolve(indent_list);
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });
}

module.exports.getWarehouseList = async function (le_id, user_id,is_apob) {
	return new Promise((resolve, reject) => {
		let permissionlevel = 'select `name` from `permission_level` where `permission_level_id` = 6 limit 1';
		db.query(permissionlevel, {}, (err, per_level) => {
			if (err) {
				reject('Bad request');
			} else {
				let per_level_name = per_level[0].name;
				let featurecheck1 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` = 1)';
				db.query(featurecheck1, [user_id, 'GLB0001'], (err, fea_check1) => {
					if (err) {
						reject('Bad request');
					} else {
						let global_aceess = fea_check1[0].aggregate;
						let featurecheck2 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` =1)';
						db.query(featurecheck2, [user_id, 'GLBWH0001'], (err, fea_check2) => {
							if (err) {
								reject('Bad request');
							} else {
								let global_wh_aceess = fea_check2[0].aggregate;
								let userpermission = 'select group_concat(distinct(`object_id`)) as bu_id from `user_permssion` where (`user_id` = ? and `permission_level_id` = 6)';
								db.query(userpermission, [user_id], (err, user_per) => {
									if (err) {
										reject('Bad request');
									} else {
										let dc_list = {};
										let bu_id = user_per[0].bu_id;
										let bu_list = bu_id.split(',');
										let getDcList = 'select GROUP_CONCAT(le_wh_id) as le_wh_id, `dc_type` from `legalentity_warehouses` where `dc_type` > 0 ';
										if (!global_aceess) {
											if (!bu_list.includes('0'))
												getDcList = getDcList + ' and bu_id in (' + bu_id + ')';
											else
												getDcList = getDcList + ' and dc_type in (118001,118002)';

										}
										getDcList = getDcList + ' group by `dc_type`';
										let dbsessionset = 'SET SESSION group_concat_max_len = 100000';
										getDcList = dbsessionset + ";" + getDcList;
										db.query(getDcList, {}, (err, dc_data) => {
											if (err) {
												reject('Bad request');
											} else {
												// console.log('zzzzzzzzzzz', dc_data[1][0]);
												let le_wh_id = dc_data[1][0].le_wh_id;
												// console.log("yyyyy", le_wh_id);
												let warehouselist = 'select `lewh`.`lp_wh_name`, `lewh`.`city`, `lewh`.`address1`, `lewh`.`pincode`, `lewh`.`le_wh_id`, `lewh`.`margin`, `lewh`.`le_wh_code`, `le`.`legal_entity_type_id` from `legalentity_warehouses` as `lewh` left join `product_tot` as `lewhmap` on `lewhmap`.`le_wh_id` = `lewh`.`le_wh_id` left join `legal_entities` as `le` on `le`.`legal_entity_id` = `lewh`.`legal_entity_id` where `lewh`.`dc_type` = ? AND le.legal_entity_type_id IS NOT NULL and `lewh`.`le_wh_id` in(' + le_wh_id + ') ';
												if(is_apob==1)
                                                warehouselist+="and (lewh.legal_entity_id in (2,21837) OR lewh.is_apob=1)";
                                                warehouselist+=" group by lewh.le_wh_id";
												db.query(warehouselist, [[118001]], (err, warehouse_list) => {
													if (err) {
														reject('Bad request');
													} else {
														// console.log('warehouse_list', warehouse_list);

														resolve(warehouse_list);
													}
												});
											}
										});
									}
								});
							}
						});
					}
				});
			}
		});
	});
}

// module.exports.getData = function (lp_token) {
//  return new Promise((resolve, reject) => {
//      let token = "select user_id,legal_entity_id from users where lp_token='" + lp_token + "'";
//      db.query(token, {}, (err, getdata) => {
//          if (err) {
//              reject('Bad request');
//          } else {
//              let user_id = getdata[0].user_id;
//              let le_id = getdata[0].legal_entity_id
//              resolve({ user_id: user_id, le_id: le_id });
//          }
//      });
//  });
// }

module.exports.getEmailList = async (message) => {
    try {

        let emailLIst = [];
        let query = `SELECT * FROM notification_recipients LEFT JOIN  notification_template ON notification_recipients.notification_template_id = 
        notification_template.notification_template_id WHERE notification_code = '${message}';`

        let result = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
        if (result != null && result.length > 0) {
            // console.log('result', result);
            let roles = result[0].notificaiton_recipient_roles;
            let users = result[0].notificaiton_recipient_users;
            let legalEntities = result.notificaiton_recipient_legal_entities;


            if(roles != null) {
            let rolesQuery = `SELECT users.email_id FROM user_roles JOIN users ON users.user_id = user_roles.user_id WHERE user_roles.role_id IN (${roles}) and users.is_active = 1;`
            let rolesResult = await sequelize.query(rolesQuery, { type: Sequelize.QueryTypes.SELECT });
            if (rolesResult != null && rolesResult.length > 0){
                for(const roleEmail of rolesResult){
                    emailLIst.push(roleEmail['email_id']);
                }
            } 
            else {throw "error in getting roles list"};
            }

            if(users != null){
            let usersQuery = `SELECT email_id FROM users WHERE user_id IN (${users}) and users.is_active = 1;`;
            let usersResult = await sequelize.query(usersQuery, { type: Sequelize.QueryTypes.SELECT });
            if (usersResult != null && usersResult.length > 0) {
                for(const userEmail of usersResult){
                    emailLIst.push(userEmail['email_id']);
                }
            } 
            else {throw "error in getting users list"};
            }

            if(legalEntities != null) {
            let legalEntitiesQuery = `SELECT email_id FROM users WHERE legal_entity_id IN (${legalEntities}) and users.is_active = 1;`;
            let legalEntitiesResult = await sequelize.query(legalEntitiesQuery, { type: Sequelize.QueryTypes.SELECT });
            if (legalEntitiesResult != null && legalEntitiesResult.length > 0){
                for(const legalEntitiesEmail of legalEntitiesResult){
                    emailLIst.push(legalEntitiesEmail['email_id']);
                }
            } 
            //  emailLIst.push(legalEntitiesResult[0]);
            else {throw "error in getting legalEntities list"};
            }

            return emailLIst;

        } else {
            return "error in getting mail list from 'PO001' "
        }

    } catch (err) {
        return `Error in getting mail list: ${err}`
    }
}

module.exports.getLegalEntityId = function (user_id) {
    return new Promise((resolve, reject) => {
        let LegalEntityId = 'select `legal_entity_id` from `users` where user_id=' + user_id + '';
        db.query(LegalEntityId, {}, (err, rows) => {
            if (err) {
                reject('Bad request');
            }
            if (Object.keys(rows).length > 0) {
                let le_id = rows[0].legal_entity_id
                return resolve({ le_id: le_id });
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getSuppliersData = async function (indent_id) {
    return new Promise((resolve, reject) => {
        let legaltype = 'select `legal_entity_type_id` from `legal_entities` where `legal_entity_id` = ' + le_id + ' limit 1';
        db.query(legaltype, {}, (err, le_type) => {
            if (err) {
                console.log(err);
                reject('Bad request');
            } else {
                let le_type_id = le_type[0].legal_entity_type_id;
                let prodArr = "select `product`.`product_id`, SUM(product.qty) as totQty from `po` inner join `po_products` as `product` on `product`.`po_id` = `po`.`po_id` where `po`.`indent_id` = ? group by `product`.`product_id`";
                db.query(prodArr, indent_id, le_id, (err, product_data) => {
                    if (err) {
                        reject('no product data');
                    } else {
                        let supplierList = "select `legal_entities`.`legal_entity_id`, `legal_entities`.`legal_entity_type_id`, `legal_entities`.`business_legal_name`, (SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype from `legal_entities` inner join `suppliers` on `suppliers`.`legal_entity_id` = `legal_entities`.`legal_entity_id` inner join `indent` on `indent`.`legal_entity_id` = `legal_entities`.`legal_entity_id` where (`indent`.`indent_id` = ? and `legal_entities`.`legal_entity_type_id` = ? and `suppliers`.`is_active` = 1 and `legal_entities`.`is_approved` = 1 and `parent_id` = 2)";
                        db.query(supplierList, indent_id, le_type_id, (err, supplier_List) => {
                            if (err) {
                                reject('no supplier data');
                            } else {
                                let warehouseList = "select `legalentity_warehouses`.`lp_wh_name`, `legalentity_warehouses`.`le_wh_id`, `legal_entities`.`legal_entity_type_id` from `legalentity_warehouses` inner join `indent` on `indent`.`le_wh_id` = `legalentity_warehouses`.`le_wh_id` inner join `legal_entities` on `legal_entities`.`legal_entity_id` = `legalentity_warehouses`.`legal_entity_id` where (`indent`.`indent_id` = ?) and `legalentity_warehouses`.`le_wh_id` in (?)";
                                db.query(warehouseList, [indent_id], [le_wh_id], (err, warehouse_List) => {
                                    if (err) {
                                        reject('no warehouse data');
                                    } else {
                                        let indentList = "select `indent`.`indent_id`, `indent`.`le_wh_id`, `indent`.`legal_entity_id`, `indentprod`.`product_id`, `tot`.`subscribe` from `indent_products` as `indentprod` left join `indent` on `indent`.`indent_id` = `indentprod`.`indent_id` left join `products` on `products`.`product_id` = `indentprod`.`product_id` left join `product_tot` as `tot` on `products`.`product_id` = `tot`.`product_id` and `indent`.`le_wh_id` = `tot`.`le_wh_id` and `indent`.`legal_entity_id` = `tot`.`supplier_id` left join `brands` on `products`.`brand_id` = `brands`.`brand_id` where (`indent`.`indent_id` = ?)";
                                        db.query(indentList, [indent_id], (err, indent_List) => {
                                            if (err) {
                                                reject('no indent data');
                                            } else {
                                                resolve(indent_List);
                                            }
                                        });
                                    }
                                });
                            }
                        });

                    }
                });

            }
        });

    });
}

module.exports.getSkusData = function (supplier_id, warehouse_id, term) {
    try {
        return new Promise((resolve, reject) => {
            let products = 'select `products`.`product_id`, `products`.`product_title`, `products`.`upc`, `products`.`sku`, `products`.`seller_sku`, `products`.`mrp`, `brands`.`brand_id`, `brands`.`brand_name` from `products` left join `brands` on `products`.`brand_id` = `brands`.`brand_id` left join `product_content` as `content` on `products`.`product_id` = `content`.`product_id` left join `product_tot` as `tot` on `products`.`product_id` = `tot`.`product_id` where `products`.`is_sellable` = 1 and (`products`.`sku` like "%' + term + '%" or `products`.`product_title` like "%' + term + '%" or `products`.`upc` like "%' + term + '%") group by `tot`.`product_id`';
            db.query(products, {}, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    // console.log(rows);
                    resolve(rows)
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getFreebieParent = function (product_id) {
    try {
        return new Promise((resolve, reject) => {
            // console.log("pppppp", product_id);
            let product = "select `free_conf_id`, `main_prd_id`, `free_prd_id` from `freebee_conf` where `free_prd_id` = ? order by `created_at` desc limit 1";
            db.query(product, [product_id], function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(rows)
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getFreebieProducts = function (product_id) {
    try {
        return new Promise((resolve, reject) => {
            let product = "select free_conf_id,main_prd_id,free_prd_id,mpq,qty from freebee_conf where main_prd_id =" + product_id;
            db.query(product, {}, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    // console.log(rows);
                    resolve(rows)
                } else {
                    // console.log(typeof ([]), "aaaaa");
                    resolve([]);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.checkProductSuscribe = function (supplier_id, warehouse_id, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let sup = "select `tot`.`product_id`, `tot`.`supplier_id`, `tot`.`subscribe` from `product_tot` as `tot` where `tot`.`product_id` = ? and `tot`.`supplier_id` = ? and `tot`.`le_wh_id` = ? limit 1 ";
            db.query(sup, [product_id, supplier_id, warehouse_id], function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.checkProduct = function (warehouse_id, product_id, supplier_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = `SELECT le_wh_id, product_id, supplier_id FROM product_tot WHERE le_wh_id = ${warehouse_id} AND product_id = ${product_id} AND supplier_id = ${supplier_id};`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                if (response.length > 0) {
                    resolve(response);
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log("rejected error", err)
    }
}

module.exports.getAllPayments = async (leId, rowCnt = 0, offset, perPage, filter, sortField = 'pay_id', sortType = 'desc') => {
    try {

        let query = `SELECT payment.*, 
        Getmastlookupvalue(payment.pay_type)         AS payment_type, 
        Getmastlookupvalue(payment.pay_for)          AS pay_for_name, 
        IF(payment.approval_status = 1, "payment completed", 
        Getmastlookupvalue(payment.approval_status)) AS approval_status_name, 
        Getusername(payment.created_by, 2)           AS createdBy, 
        (SELECT po_code 
         FROM   po 
         WHERE  po.po_id = payment.reff_id 
                AND payment.pay_for_module = 'PO')   AS 'po_code', 
        (SELECT Sum(po_products.sub_total) 
         FROM   po_products 
         WHERE  po_products.po_id = payment.reff_id 
                AND payment.pay_for_module = 'PO')   AS po_value, 
        (SELECT Sum(inward.grand_total) 
         FROM   inward 
         WHERE  inward.po_no = payment.reff_id 
                AND payment.pay_for_module = 'PO')   AS grn_value 
        FROM   payment_details AS payment 
         WHERE  payment.txn_tolegal_id = ${leId} `

        if (Object.keys(filter).length > 0) {
            if (filter['pay_code'] != null && filter['pay_code'] != "") {
                query += `  AND payment.pay_code LIKE '%${filter['pay_code'][0]}%' `
            }
            if (filter['po_code'] != null && filter['po_code'] != "") {
                query += `  AND (SELECT po_code FROM po WHERE po.po_id = payment.reff_id AND payment.pay_for_module='PO') LIKE '%${filter['po_code'][0]}%' `
            }
            if (filter['pay_type'] != null && filter['pay_type'] != "") {
                query += `  AND getMastLookupValue(payment.pay_type) LIKE '%${filter['pay_type'][0]}%' `
            }
            if (filter['pay_for'] != null && filter['pay_for'] != "") {
                query += `  AND getMastLookupValue(payment.pay_for) LIKE '%${filter['pay_for'][0]}%' `
            }
            if (filter['approval_status'] != null && filter['approval_status'] != "") {
                query += `  AND IF(payment.approval_status=1,"Payment Completed", getMastLookupValue(payment.approval_status)) LIKE '%${filter['approval_status'][0]}%' `
            }
            if (filter['ledger_account'] != null && filter['ledger_account'] != "") {
                query += `  AND payment.ledger_account LIKE '%${filter['ledger_account'][0]}%' `
            }
            if (filter['txn_reff_code'] != null && filter['txn_reff_code'] != "") {
                query += `  AND payment.txn_reff_code LIKE '%${filter['txn_reff_code'][0]}%' `
            }
            if (filter['pay_utr_code'] != null && filter['pay_utr_code'] != "") {
                query += `  AND payment.pay_utr_code LIKE '%${filter['pay_utr_code'][0]}%' `
            }
            if (filter['createdBy'] != null && filter['createdBy'] != "") {
                query += `  AND GetUserName(payment.created_by,2) LIKE '%${filter['createdBy'][0]}%' `
            }
            if (filter['pay_amount'] != null && filter['pay_amount'] != "") {
                query += `  AND payment.pay_amount ${filter['pay_amount'][1]} '%${filter['pay_amount'][0]}%' `
            }
            if (filter['po_value'] != null && filter['po_value'] != "") {
                query += `  AND (SELECT SUM(po_products.sub_total) FROM po_products WHERE po_products.po_id=payment.reff_id AND payment.pay_for_module='PO') ${filter['po_value'][1]} '%${filter['po_value'][0]}%'`
            }
            if (filter['grn_value'] != null && filter['grn_value'] != "") {
                query += `  AND (SELECT SUM(inward.grand_total) FROM inward WHERE inward.po_no=payment.reff_id AND payment.pay_for_module='PO') ${filter['grn_value'][1]} '%${filter['grn_value'][0]}%' `
            }
            if (filter['pay_date'] != null && filter['pay_amopay_dateunt'] != "") {
                query += `  AND pay_date ${filter['pay_date'][1]} '%${filter['pay_date'][0]}%' `
            }
            if (filter['created_at'] != null && filter['created_at'] != "") {
                query += `  AND created_at ${filter['created_at'][1]} '%${filter['created_at'][0]}%' `
            }
        }
        if (rowCnt == 1) {
            query += ` order by pay_id desc;`
            let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
            return response.length;
        } else {
            query += ` order by ${sortField} ${sortType} limit ${perPage} offset ${offset};`
            let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT })
            // console.log('response', response.length);
            if (response.length > 0) {
                return response;
            } else {
                return 'noData';
            };
        }
    } catch (err) {

    }
}

// module.exports.lkl = async (leid) => {
//  try{let query = ` select * from users limit 10;`
//  let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT })
//  // console.log(response);
//  return response;
// } catch (err) {

// }
// }

module.exports.updateProductTot = function (product_tot, supplier_id, warehouse_id, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "update product_tot set product_tot ='" + product_tot + "'where product_id =" + product_id + "le_wh_id =" + warehouse_id + "product_id = " + product_id;
            db.query(query, {}, function (err, updated) {
                if (err) {
                    console.log(err);
                } else {
                    resolve(updated);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getProductdetails = function (product_id) {
    try {
        return new Promise((resolve, reject) => {
            let sup = "select prds.product_title,prds.product_id from products as prds where prds.product_id =" + product_id;
            db.query(sup, {}, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(rows)
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.saveProductTot = function (product_tot) {
    try {
        return new Promise((resolve, reject) => {
            var product_id = product_tot.product_id;
            var supplier_id = product_tot.supplier_id;
            var le_wh_id = product_tot.le_wh_id;
            var product_name = product_tot.product_name;
            var is_active = product_tot.is_active;
            var subscribe = product_tot.subscribe;
            let producttot = "Insert into product_tot (product_id,supplier_id,le_wh_id,product_name,is_active,subscribe) values(?,?,?,?,?,?)";
            db.query(producttot, [product_id, supplier_id, le_wh_id, product_name, is_active, subscribe], function (err, rows) {
                // console.log('rowssssssssssssss', rows);
                if (err) {
                    console.log(err);
                    reject(0);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(1)
                } else {
                    resolve(1);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getProductInfoByID = function (supplier_id, warehouse_id, product_id, legal_entity_id) {
    try {
        //console.log("greeeee");
        return new Promise((resolve, reject) => {
            let globalSupperLier = "select `description` from `master_lookup` where `value` = 78023";
            db.query(globalSupperLier, {}, function (err, glbsupplier) {
                //console.log("gllll",glbsupplier);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(glbsupplier).length > 0) {
                    let globalSupperLierId = glbsupplier[0].hasOwnProperty('description') ? glbsupplier[0].description : null;
                    //console.log("ghhhhh",globalSupperLierId);
                    module.exports.checkStockist(legal_entity_id).then(is_Stockist => {
                        //console.log("stosss",is_Stockist);
                        module.exports.getAllDCLeids().then(dc_le_id_list => {
                            //console.log(dc_le_id_list);
                            //console.log(JSON.stringify(dc_le_id_list));
                            //dc_le_id_list=JSON.stringify(dc_le_id_list);
                            dc_le_id_list = dc_le_id_list[0].dc_le_id_list;
                            globalSupperLierId = globalSupperLierId + "," + dc_le_id_list;
                            //console.log("glbbbb",globalSupperLierId);
                            //console.log("dclistttt",dc_le_id_list);
                            if (is_Stockist > 0) {
                                let stockistQuery = "and pph.supplier_id IN (" + globalSupperLierId + ")";
                                db.query(stockistQuery, {}, function (err, stockistquery) {
                                    if (err) {
                                        reject('no  data');
                                    } else {
                                        module.exports.getStockistPriceGroup(legal_entity_id, $warehouse_id).then(customer_type_id => {
                                            //console.log("custtidd",customer_type_id);
                                            let globalDcId = "select description from master_lookup where value= 78021";
                                            db.query(globalDcId, {}, function (err, glbdcid) {
                                                let globalDcId = glbdcid[0].hasOwnProperty('description') ? glbdcid[0].description : null;
                                                //console.log('globalDcId',globalDcId);
                                                let esp = "(SELECT p_p.`ptr` FROM product_prices p_p  WHERE p_p.`product_id` = ?  AND p_p.`dc_id` = ? AND customer_type = ? AND p_p.`effective_date` <= CURRENT_DATE ORDER BY effective_date limit 1) as dlp";
                                                db.query(esp, [product_id, globalDcId, customer_type_id], function (err, resp) {
                                                    if (err) {
                                                        reject(err);
                                                    } else {
                                                        // console.log('espppppppppppp');
                                                        resolve(resp);
                                                    }
                                                })
                                            })
                                        })
                                    }
                                })
                            } else {

                                let stockistQuery = "and pph.supplier_id NOT IN (" + globalSupperLierId + ")";
                                let esp = 'tot.dlp';

                            }
                            //console.log('globalSupperLierId',globalSupperLierId,'product_id',product_id,'supplier_id',supplier_id,'warehouse_id',warehouse_id);
                            let products = "select `products`.`product_id`, `products`.`upc`, `products`.`product_title` as `pname`, `products`.`sku`, `products`.`pack_size`, `products`.`seller_sku`, `products`.`mrp`, `tot`.`base_price` as `price`, tot.dlp, `tot`.`supplier_id`, `tot`.`le_wh_id`, `products`.`product_type_id`, `brands`.`brand_id`, `brands`.`brand_name`, `inventory`.`mbq`, `inventory`.`soh`, `inventory`.`atp`, `inventory`.`order_qty`, `currency`.`symbol_left` as `symbol`, `products`.`is_sellable`, getPackType(products.product_id) AS packType, (select elp from purchase_price_history as pph where pph.product_id=products.product_id and pph.supplier_id NOT IN (?) and pph.le_wh_id = ? order by pur_price_id desc limit 0,1)  as prev_elp, getMastLookupValue(products.kvi) AS KVI from `products` left join `brands` on `products`.`brand_id` = `brands`.`brand_id` left join `product_content` as `content` on `products`.`product_id` = `content`.`product_id` left join `product_tot` as `tot` on `products`.`product_id` = `tot`.`product_id` left join `inventory` on `products`.`product_id` = `inventory`.`product_id` and `tot`.`le_wh_id` = `inventory`.`le_wh_id` left join `currency` on `tot`.`currency_id` = `currency`.`currency_id` where `products`.`product_id` = ? and `tot`.`supplier_id` = ? and `tot`.`le_wh_id` = ? limit 1";
                            db.query(products, [globalSupperLierId, warehouse_id, product_id, supplier_id, warehouse_id], function (err, product) {
                                //console.log("product",product);
                                if (err) {
                                    reject(err);
                                } else {
                                    // console.log('esppppprrrrrrrrrrrrppppppp');
                                    resolve(product);
                                }
                            })
                        })
                    })
                }
            })
        })

    } catch (err) {
        console.log(err)
    }

}

module.exports.checkStockist = function (legal_entity_id) {
    try {
        return new Promise((resolve, reject) => {
            let count = "select count(*) as aggregate from `legal_entities` where `legal_entity_type_id` in (?, ?) and `business_type_id` = ? and `legal_entity_id` = ?";
            db.query(count, [1014, 1016, 47001, legal_entity_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    console.log(rows);
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getAllDCLeids = function () {
    try {
        return new Promise((resolve, reject) => {
            let legal_entity_id = "select GROUP_CONCAT(legal_entity_id) as dc_le_id_list from `legal_entities` where `legal_entity_type_id` = 1016 limit 1";
            db.query(legal_entity_id, {}, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(rows)
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getStockistPriceGroup = function (legal_entity_id, warehouse_id) {
    try {
        return new Promise((resolve, reject) => {
            let price_id_data = "select stockist_price_group_id from `stockist_price_mapping` where `legal_entity_id` = ? and `le_wh_id` = ? ";
            db.query(price_id_data, [legal_entity_id, warehouse_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    // console.log(rows);
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getWarehouseId = function (leWhId) {
    try {
        return new Promise((resolve, reject) => {
            let query = `select warehouse.*, countries.name as country_name, zone.name as state_name, zone.code as state_code, legal_entities.business_legal_name, warehouse.tin_number as gstin from legalentity_warehouses as warehouse left join legal_entities on legal_entities.legal_entity_id = warehouse.legal_entity_id left join countries on countries.country_id = warehouse.country left join zone on zone.zone_id = warehouse.state where warehouse.le_wh_id = ${leWhId} limit 1;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                resolve(response[0]);
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getApobData = function (wh_leid) {
	try {
		return new Promise((resolve, reject) => {
			let query = `SELECT lw.display_name AS business_legal_name, 
			lw.le_wh_id, 
			lw.legal_entity_id, 
			legal.logo, 
			lw.address1, 
			lw.state        AS state_id, 
			lw.address2, 
			lw.city, 
			legal.logo, 
			lw.pincode, 
			lw.email        AS email_id, 
			lw.phone_no     AS mobile_no, 
			lw.tin_number   AS gstin, 
			countries.NAME  AS country_name, 
			zone.NAME       AS state_name, 
			zone.NAME       AS state, 
			zone.code       AS state_code 
	 FROM   legalentity_warehouses AS lw 
			LEFT JOIN dc_fc_mapping 
				   ON dc_le_wh_id = lw.le_wh_id 
			LEFT JOIN legal_entities AS legal 
				   ON legal.legal_entity_id = dc_fc_mapping.dc_le_id 
			LEFT JOIN countries 
				   ON countries.country_id = lw.country 
			LEFT JOIN zone 
				   ON zone.zone_id = lw.state 
	 WHERE  dc_fc_mapping.fc_le_id = ${wh_leid} 
			AND lw.dc_type = 118001; `
			sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
				if(response.length>0){
				   resolve(response);
				}else{
					resolve(0);
				}
			})
		})

    } catch (err) {
        console.log(err)
    }
}



module.exports.getLegalEntityDetails = function (leId) {
    try {
        return new Promise((resolve, reject) => {
            let query = `select legal.business_legal_name, legal.legal_entity_id, legal.address1, legal.address2, legal.city, legal.state_id, legal.pincode, legal.pan_number, legal.tin_number, legal.gstin, suppliers.sup_bank_name, suppliers.sup_account_no, suppliers.sup_account_name, suppliers.sup_ifsc_code, countries.name as country_name, zone.name as state_name, zone.code as state_code from legal_entities as legal left join countries on countries.country_id = legal.country left join zone on zone.zone_id = legal.state_id left join suppliers on suppliers.legal_entity_id = legal.legal_entity_id where legal.legal_entity_id = ${leId} limit 1;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                resolve(response[0]);
            })
        })

    } catch (err) {
        console.log(err)
    }
}

// get le_wh_id from legalentity_warehouses table with legal_entity_id
module.exports.getLeWhId = async (leId) => {
    try {
        return new Promise((resolve, reject) => {
            let query = `SELECT lewh.le_wh_id FROM legalentity_warehouses AS lewh WHERE lewh .legal_entity_id = ${leId}
            AND lewh.dc_type=118001;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                resolve(response[0]);
            })

        })

    } catch (err) {
        console.log(err)
    }
}
module.exports.getLegalEntityById = function (leId) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `legal`.`business_legal_name`, `legal`.`legal_entity_id`,`legal`.`le_code`,`legal`.`is_eb`, `legal`.`address1`, `legal`.`address2`, `legal`.`city`, `legal`.`state_id`, `legal`.`pincode`, `legal`.`pan_number`, `legal`.`tin_number`, `legal`.`gstin`, `legal`.`fssai`, `legal`.`landmark`,`legal`.`locality`,`suppliers`.`sup_bank_name`, `suppliers`.`sup_account_no`, `suppliers`.`sup_account_name`, `suppliers`.`sup_ifsc_code`, `countries`.`name` as `country_name`, `zone`.`name` as `state_name`, `zone`.`code` as `state_code`,`legal`.`legal_entity_type_id` as `le_type_id`,`legal`.`is_eb` as `le_is_eb` from `legal_entities` as `legal` left join `countries` on `countries`.`country_id` = `legal`.`country` left join `zone` on `zone`.`zone_id` = `legal`.`state_id` left join `suppliers` on `suppliers`.`legal_entity_id` = `legal`.`legal_entity_id` where `legal`.`legal_entity_id` = ? limit 1";
            db.query(query, [leId], function (err, rows) {
                //console.log("rowsss",rows);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //console.log(rows);
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}



module.exports.getProductPackInfo = function (product_id) {
    try {
        return new Promise((resolve, reject) => {
            let packs = "select `products`.`product_id`, `products`.`product_title`, `products`.`seller_sku`, `products`.`upc`, `pack`.`pack_id`, `pack`.`level`, `pack`.`no_of_eaches`, `pack`.`pack_sku_code`, `lookup`.`master_lookup_name` as `packname`, `products`.`mrp` from `products` inner join `product_pack_config` as `pack` on `pack`.`product_id` = `products`.`product_id` inner join `master_lookup` as `lookup` on `pack`.`level` = `lookup`.`value` where `pack`.`no_of_eaches` > ? and `products`.`product_id` = ? order by `lookup`.`sort_order` desc, `pack`.`effective_date` desc";
            db.query(packs, [0, product_id], function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows)
                } else {
                    // console.log("rowssssssssss errrrrr",rows);
                    // resolving [];
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.verifyNewProductInWH = function (warehouse_id, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `stock_inward_id`, `product_id`, `le_wh_id` from `stock_inward` where `stock_inward`.`le_wh_id` = ? and `stock_inward`.`product_id` = ? group by `product_id`";
            db.query(query, [warehouse_id, product_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    //      if(rows && rows.length>0){
                    //          resolve (1);
                    // }else{
                    resolve(1);
                    //}

                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getIndentProduct = function (indentId, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "SELECT  indentprod.indent_id,indentprod.product_id,indentprod.qty,indentprod.target_elp,indentprod.no_of_units,indentprod.pack_type FROM `indent_products` AS `indentprod` WHERE indentprod.indent_id =  ? AND indentprod.product_id = ? ";
            db.query(query, [indentId, product_id], function (err, result) {
                // console.log("resulrt", result);


                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(result).length > 0) {
                    resolve(result)
                } else {
                    resolve(result);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getPoProductQtyByIndentId = function (indent_id, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select SUM(product.qty*product.no_of_eaches) as totQty from `po` inner join `po_products` as `product` on `product`.`po_id` = `po`.`po_id` where `product`.`product_id` = ? and `po`.`indent_id` = ? limit 1";
            db.query(query, [product_id, indent_id], function (err, rows) {

                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}
module.exports.getIndentProductQtyById = function (indent_id, product_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select SUM(product.qty*product.no_of_eaches) as totQty from `indent_products` as `product` where `product`.`product_id` = ? and `product`.`indent_id` = ? limit 1";
            db.query(query, [product_id, indent_id], function (err, rows) {

                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows)
                } else {
                    resolve(rows);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.checkInventory = function (product_id, le_wh_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select inv_display_mode from `inventory` where `product_id` = ? and `le_wh_id` = ?";
            db.query(query, [product_id, le_wh_id], function (err, checkInventory) {
                // console.log("checkInventory", checkInventory);
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(checkInventory).length > 0) {
                    let displaymode = checkInventory[0].hasOwnProperty('inv_display_mode') ? checkInventory[0].inv_display_mode : 0;
                    let query = "select (soh-(order_qty+reserved_qty)) as availQty from `inventory` where `product_id` = ? and `le_wh_id` = ?";
                    db.query(query, [product_id, le_wh_id], function (err, availQty) {
                        if (err) {
                            console.log(err);
                            reject(err);
                        } else if (Object.keys(availQty).length > 0) {
                            availQty = availQty[0].availQty;
                            // console.log("availQty", availQty);
                            resolve(availQty)
                        }
                    })
                } else {
                    resolve(0);
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.checkSupplier = function (supplier_list) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `legal_entities`.`legal_entity_id`, `legal_entities`.`business_legal_name` from `legal_entities` left join `suppliers` on `suppliers`.`legal_entity_id` = `legal_entities`.`legal_entity_id` where (`legal_entities`.`is_approved` = 1 and `legal_entities`.`legal_entity_id` = ?)";
            db.query(query, [supplier_list], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {

                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    let legal_entity_type_id = [1014, 1016];
                    let query1 = "select `legal_entities`.`legal_entity_id`, `legal_entities`.`business_legal_name` from `legal_entities` where (`legal_entities`.`is_approved` = 1 and `legal_entities`.`legal_entity_id` = ?) and `legal_entities`.`legal_entity_type_id` in (?, ?)";
                    db.query(query1, [supplier_list, 1014, 1016], function (err, rows1) {
                        if (err) {
                            reject(err);
                        } else if (Object.keys(rows1).length > 0) {
                            resolve(rows.concat(rows1));
                        } else {
                            resolve(rows);
                        }
                    })

                } else {
                    console.log('herechecksupplier');
                }
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getlegalidbasedondcid = function (le_wh_id) {
    try {
        return new Promise((resolve, reject) => {

            let query = "select `legal_entity_id`, `bu_id` from `legalentity_warehouses` where `le_wh_id` = ? limit 1";
            db.query(query, [le_wh_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve([]);
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.checkIsSelfTax = function (legal_entity_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `is_self_tax` from `legal_entities` where `legal_entity_id` = ?";
            db.query(query, [legal_entity_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve([]);
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.getProductPackUOMInfoById = function (pack_id) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `lookup`.`value`, `lookup`.`master_lookup_name` as `uomName`, `pack`.`no_of_eaches` from `product_pack_config` as `pack` left join `master_lookup` as `lookup` on `pack`.`level` = `lookup`.`value` where `pack`.`pack_id` = ? limit 1";
            db.query(query, [pack_id], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.checkLOCByLeWhid = function (dcleid) {
    try {
        return new Promise((resolve, reject) => {
            let query = "SELECT  vp.`Order_Limit`  FROM `vw_stockist_payment_details` vp WHERE vp.`le_wh_id` = ? ";
            db.query(query, [dcleid], function (err, rows) {
                // console.log("rowsss", rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    let checkqry = rows[0].hasOwnProperty('Order_Limit') ? rows[0].Order_Limit : 0;
                    resolve(checkqry);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

/*

*/
module.exports.getLEWHById = function (dcleid) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `warehouses`.`le_wh_id`, `warehouses`.`legal_entity_id`, `warehouses`.`lp_wh_name`, `warehouses`.`address1`, `warehouses`.`address2`, `warehouses`.`city`, `warehouses`.`pincode`, `warehouses`.`phone_no`, `warehouses`.`email`, `warehouses`.`credit_limit_check`, `warehouses`.`state` as `state_id`, `countries`.`name` as `country`, `zone`.`name` as `state`, `zone`.`code` as `state_code` from `legalentity_warehouses` as `warehouses` inner join `legal_entities` as `legal` on `warehouses`.`legal_entity_id` = `legal`.`legal_entity_id` left join `countries` on `countries`.`country_id` = `warehouses`.`country` left join `zone` on `zone`.`zone_id` = `warehouses`.`state` where `warehouses`.`le_wh_id` = ? limit 1";
            db.query(query, [dcleid], function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.getBillingAddress = async (wh_le_id) => {
    try {
        let query = `SELECT warehouse.display_name, 
        warehouse.authorized_by, 
        warehouse.jurisdiction, 
        warehouse.lp_wh_name, 
        warehouse.contact_name, 
        warehouse.pincode, 
        warehouse.state, 
        warehouse.le_wh_code, 
        warehouse.landmark, 
        warehouse.address1, 
        warehouse.address2, 
        countries.NAME AS country_name, 
        warehouse.email, 
        warehouse.phone_no, 
        warehouse.city, 
        zone.NAME      AS state_name, 
        warehouse.margin, 
        warehouse.is_apob, 
        warehouse.legal_entity_id, 
        zone.code      AS state_code, 
        legal_entities.business_legal_name, 
        legal_entities.gstin, 
        legal_entities.legal_entity_type_id, 
        warehouse.fssai, 
        legal_entities.locality, 
        legal_entities.landmark 
 FROM   legalentity_warehouses AS warehouse 
        LEFT JOIN legal_entities 
               ON legal_entities.legal_entity_id = warehouse.legal_entity_id 
        LEFT JOIN countries 
               ON countries.country_id = warehouse.country 
        LEFT JOIN zone 
               ON zone.zone_id = warehouse.state 
 WHERE  warehouse.le_wh_id = ${wh_le_id}; `

        let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
        if (response.length > 0) {
            return response;
        } else {
            return '';
        }

    } catch (err) {
        console.log(err);
        return '';
    }

};

module.exports.checkGstState = async (wh_state_id) => {
    try {
        let query = `SELECT display_name   AS business_legal_name, 
        address1, 
        address2, 
        city, 
        zone.NAME      AS state_name, 
        country, 
        countries.NAME AS country_name, 
        gstin, 
        pincode, 
        zone.code      AS state_code, 
        phone_no, 
        email, 
        gstin, 
        fssai, 
        eb_sup_le_id,
        gstin as tin_number 
 FROM   legal_entity_gst_addresses AS gst 
        LEFT JOIN zone 
               ON zone.zone_id = gst.state 
        LEFT JOIN countries 
               ON countries.country_id = gst.country 
 WHERE  gst.state = ${wh_state_id}; `

        let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });

        if (response.length > 0) {
            console.log(response);
            return response;
        } else {
            return '';
        }
    } catch (err) {
        console.log('checkGSTState error', err);
        return '';
    }
};

module.exports.getWarehouseById = function (dcleid) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `warehouse`.*, `countries`.`name` as `country_name`, `zone`.`name` as `state_name`, `zone`.`code` as `state_code`,`legal_entities`.`legal_entity_type_id`, `legal_entities`.`business_legal_name`, `warehouse`.`tin_number` as `gstin` from `legalentity_warehouses` as `warehouse` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `warehouse`.`legal_entity_id` left join `countries` on `countries`.`country_id` = `warehouse`.`country` left join `zone` on `zone`.`zone_id` = `warehouse`.`state` where `warehouse`.`le_wh_id` = ? limit 1";
            db.query(query, [dcleid], function (err, rows) {
                // console.log("query",rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}



module.exports.savePurchaseOrderData = async function (data) {
    try {
        return new Promise((resolve, reject) => {
            // console.log("data", data);
            let poId = 0;
            let totPoQty = 0;
            let expDeliveryDate = "";
            var user_id = data.user_id;
            module.exports.checkUserId(user_id).then(response => {
                module.exports.getLegalEntityId(user_id).then((response1) => {
                    module.exports.getDeliveryDate([]).then(expDeliveryDateArr => {
                        expDeliveryDate = expDeliveryDateArr.join(",");
                        // console.log("expDeliveryDate", expDeliveryDate);
                    })
                    let warehouse_id = data.hasOwnProperty('warehouse_list') ? data.warehouse_list : 0;
                    module.exports.getLEWHById(warehouse_id).then(whdetails => {
                        // console.log("whdetails", whdetails);
                        let state_code = whdetails[0].hasOwnProperty('state_code') ? whdetails[0].state_code : "TS";
                        // console.log("state_code", state_code);
                        module.exports.getReferenceCode("PO", state_code).then(serialNumber => {
                            // console.log("serialNumber", serialNumber);
                            if (serialNumber == "") {
                                resolve({ status: 400, message: 'PO number generation error.', po_id: 0, serialNumber: serialNumber });
                            }
                            let indentId = data.hasOwnProperty('indent_id') ? data.indent_id : 0;
                            // console.log("indentId", indentId);
                            let supplier_id = data.hasOwnProperty('supplier_list') ? data.supplier_list : 0;
                            // console.log("supplier_id", supplier_id);
                            let po_type = data.hasOwnProperty('po_type') ? data.po_type : 1;
                            // console.log("po_type", po_type);
                            let payment_mode = data.hasOwnProperty('payment_mode') ? data.payment_mode : 1;
                            // console.log("payment_mode", payment_mode);
                            let paid_through = data.hasOwnProperty('paid_through') ? data.paid_through : '';
                            // console.log("paid_through", paid_through);
                            let accountinfo = paid_through.split("===");
                            // console.log("accountinfo", accountinfo);
                            //let tlm_name = (isset($accountinfo[0]))?$accountinfo[0]:'';
                            //let tlm_group = (isset($accountinfo[1]))?$accountinfo[1]:'';
                            let payment_type = data.hasOwnProperty('payment_type') ? data.payment_type : '';
                            // console.log("payment_type", payment_type);
                            let payment_ref = data.hasOwnProperty('payment_ref') ? data.payment_ref : '';
                            // console.log("payment_ref", payment_ref);
                            let legal_entity_id = supplier_id;
                            // console.log("legal_entity_id", legal_entity_id);
                            let le_wh_id = warehouse_id;
                            // console.log("le_wh_id", le_wh_id);
                            let supply_le_wh_id = data.hasOwnProperty('dc_warehouse_id') ? data.dc_warehouse_id : 0;
                            let create_order = data.hasOwnProperty('create_order') ? data.create_order : 0;
                            // console.log("supply_le_wh_id", supply_le_wh_id);
                            // console.log(data.delivery_before);
                            let del_bef = data.delivery_before.split('-');
                            let delivery_date = del_bef[2] + '-' + del_bef[1] + '-' + del_bef[0] + " 00:00:00";
                            // let delivery_date = new Date(data.delivery_before).toISOString().slice(0,10);
                            // console.log("delivery_date",delivery_date);
                            po_type = data.po_type;
                            // console.log("po_type", po_type);
                            let platform = data.hasOwnProperty('platform_id') && data.platform_id != '' ? data.platform_id : 5001;
                            payment_mode = data.payment_mode;
                            payment_type = data.payment_type;
                            let payment_refno = data.payment_ref;
                            //let tlm_name = $tlm_name;
                            //let tlm_group = $tlm_group;
                            let approval_status = 57106;
                            let apply_discount_on_bill = data.hasOwnProperty('apply_discount_on_bill') ? data.apply_discount_on_bill : 0;
                            let discount_type = data.hasOwnProperty('discount_type') ? data.discount_type : 0;
                            let discount = data.hasOwnProperty('bill_discount') ? data.bill_discount : 0;
                            let is_stock_transfer = data.hasOwnProperty('is_stock_transfer') ? data.is_stock_transfer : 0;
                            let stock_transfer_dc = data.hasOwnProperty('st_dc_name')? data.st_dc_name : 0;
                            let po_date = "";
                            if (data.po_date != "") {
                                po_date = data.po_date;
                                //po_date= po_date.toISOString().replace(/([^T]+)T([^\.]+).*/g, '$1 $2');
                                // console.log("po_date", po_date);
                            }
                            let payment_due_date = "";
                            if (data.payment_due_date != "" && payment_mode == 1) {
                                payment_due_date = new Date(data.payment_due_date);
                                payment_due_date = payment_due_date.toISOString().replace(/([^T]+)T([^\.]+).*/g, '$1 $2');
                                // console.log("payment_due_date", payment_due_date);
                            }
                            let logistics_cost = data.hasOwnProperty('logistics_cost') ? data.logistics_cost : 0;
                            // console.log('logistics_cost', logistics_cost);
                            let po_validity = data.hasOwnProperty('validity') ? data.validity : 7;
                            let po_remarks = data.hasOwnProperty('po_remarks') ? data.po_remarks : "";
                            // console.log("po_remarks", po_remarks);
                            let po_code = serialNumber;
                            let discount_before_tax = data.hasOwnProperty('discount_before_tax') ? data.discount_before_tax : 0;
                            let created_by = user_id;
                            if (indentId) {
                                let indent_id = indentId;
                            }
                            let exp_delivery_date = "";
                            if (expDeliveryDate != "") {
                                let exp_delivery_date = expDeliveryDate;
                            }
                            // console.log("exp_delivery_date", exp_delivery_date);
                            let insert_query = "insert into po (legal_entity_id, po_type,po_code,le_wh_id,delivery_date,po_validity,po_remarks,indent_id,po_date,payment_due_date,platform,logistics_cost,approval_status,apply_discount_on_bill,discount_type,discount,discount_before_tax,is_stock_transfer,stock_transfer_dc,supply_le_wh_id,exp_delivery_date,created_by) values (" + legal_entity_id + "," + po_type + ",'" + po_code + "'," + le_wh_id + ",'" + delivery_date + "'," + po_validity + ",'" + po_remarks + "'," + indentId + ",'" + po_date + "','" + payment_due_date + "'," + platform + "," + logistics_cost + "," + approval_status + "," + apply_discount_on_bill + "," + discount_type + "," + discount + "," + discount_before_tax + "," + is_stock_transfer + ","+ stock_transfer_dc +"," + supply_le_wh_id + ",'" + exp_delivery_date + "'," + created_by + ")";
                            db.query(insert_query, {}, async function (err, inserted) {
                                // console.log("insert_query", insert_query)
                                if (err) {
                                    // console.log(err)
                                    con.rollback(function (err) {
                                        reject(err)
                                    })
                                } else {
                                    // console.log("Inserted Successfully");
                                    let poId = inserted.insertId;
                                    //resolve(poId);
                                    if (poId) {
                                        // saving of the po documents in po_docs table after getting the poId
                                        let proforma = data.hasOwnProperty('proforma') ? data.proforma : [];


                                        if (Array.isArray(proforma) && proforma.length > 0) {
                                            for (const doc of proforma) {
                                                await exports.savePoDocuments(poId, doc);
                                            }
                                        }

                                        let productInfo = data.hasOwnProperty('po_products') ? data.po_products : [];
                                        // console.log("productInfo000000000000000000000000", productInfo);
                                        productInfo.forEach(function (product_info, key) {
                                            let product_id = product_info.po_product_id;
                                            module.exports.getProductInfoByID(supplier_id, warehouse_id, product_id, legal_entity_id).then(product => {
                                                // console.log("product", product);
                                                let parent_id = product_info.parent_id;
                                                // console.log("parent_id", parent_id);
                                                let mrp = 0;
                                                if (product && product.length > 0) {
                                                    mrp = product[0].mrp;
                                                }

                                                // console.log("mrp", mrp);
                                                let qty = product_info.qty;
                                                // console.log("qty", qty);
                                                let pack_size = product_info.packsize;
                                                // console.log("pack_size", pack_size);
                                                module.exports.getProductPackUOMInfoById(pack_size).then(uomPackinfo => {
                                                    // console.log("uomPackinfo", uomPackinfo);
                                                    let uom = uomPackinfo[0].value;
                                                    // console.log("uom", uom);
                                                    let no_of_eaches = uomPackinfo[0].no_of_eaches;
                                                    let free_qty = product_info.freeqty;
                                                    // console.log("free_qty", free_qty);
                                                    let free_packsize = product_info.freepacksize;
                                                    // console.log("free_packsize", free_packsize);
                                                    module.exports.getProductPackUOMInfoById(free_packsize).then(freeUOMPackinfo => {
                                                        // console.log("freeUOMPackinfo", freeUOMPackinfo);
                                                        let free_uom = freeUOMPackinfo[0].value;
                                                        // console.log("free_uom", free_uom);
                                                        let free_eaches = freeUOMPackinfo[0].no_of_eaches;
                                                        // console.log("free_eaches", free_eaches);
                                                        let is_tax_included = product_info.hasOwnProperty('pretax') ? product_info.pretax : 0;
                                                        let apply_discount = product_info.hasOwnProperty('apply_discount') ? product_info.apply_discount : 0;
                                                        let discount_type = product_info.hasOwnProperty('discount_type') ? product_info.discount_type : 0;
                                                        let discount = product_info.hasOwnProperty('item_discount') ? product_info.item_discount : 0;
                                                        let cur_elp = product_info.hasOwnProperty('curelpval') ? product_info.curelpval : 0;

                                                        if (po_type == 1) {
                                                            var unit_price = 0;
                                                            var price = 0;
                                                            var sub_total = 0;
                                                            var tax_name = null;
                                                            var tax_per = 0;
                                                            var tax_amt = 0;
                                                        } else {
                                                            var unit_price = product_info.hasOwnProperty('unit_price') ? product_info.unit_price : 0;
                                                            var price = product_info.hasOwnProperty('po_baseprice') ? product_info.po_baseprice : 0;
                                                            var sub_total = product_info.hasOwnProperty('po_totprice') ? product_info.po_totprice : 0;
                                                            var tax_name = product_info.hasOwnProperty('po_taxname') ? product_info.po_taxname : "";
                                                            var tax_per = product_info.hasOwnProperty('po_taxper') ? product_info.po_taxper : 0;
                                                            var tax_amt = product_info.hasOwnProperty('po_taxvalue') ? product_info.po_taxvalue : 0;
                                                            if (product_info.po_taxdata != null) {
                                                                var tax_data = JSON.stringify(product_info.po_taxdata);
                                                                // var tax_data = (product_info.hasOwnProperty('po_taxdata') && product_info.po_taxdata != '')?Buffer.from(product_info.po_taxdata, 'base64').toString('ascii'):null; 
                                                                //console.log("taxdataaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",JSON.parse(tax_data));
                                                                // console.log("taxdataaaaaaaaaaaaaaaaaaaaaaaaaaaaaa", JSON.stringify(tax_data));
                                                                // console.log("taxdata", tax_data);
                                                            } else {
                                                                var tax_data = null;
                                                            }
                                                            var hsn_code = product_info.hasOwnProperty('hsn_code') ? product_info.hsn_code : 0;
                                                        }
                                                        let po_id = poId;
                                                        let po_products_insert = "insert into `po_products` (`product_id`, `parent_id`, `mrp`, `qty`, `uom`, `no_of_eaches`, `free_qty`, `free_uom`, `free_eaches`, `is_tax_included`, `apply_discount`, `discount_type`, `discount`, `cur_elp`, `unit_price`, `price`, `sub_total`, `tax_name`, `tax_per`, `tax_amt`, `tax_data`, `hsn_code`, `po_id`) values (" + product_id + "," + parent_id + "," + mrp + "," + qty + ",'" + uom + "'," + no_of_eaches + "," + free_qty + "," + free_uom + "," + free_eaches + "," + is_tax_included + "," + apply_discount + "," + discount_type + "," + discount + "," + cur_elp + "," + unit_price + "," + price + "," + sub_total + ",'" + tax_name + "'," + tax_per + "," + tax_amt + ",'" + tax_data + "','" + hsn_code + "'," + po_id + ")";
                                                        db.query(po_products_insert, {}, function (err, productdatainserted) {
                                                            // console.log("productdatainserted", productdatainserted)
                                                            if (err) {
                                                                console.log(err)
                                                                con.rollback(function (err) {
                                                                    reject(err)
                                                                })
                                                            } else {
                                                                console.log("po products Inserted Successfully");
                                                                resolve(poId);
                                                            }
                                                        })

                                                    })
                                                })
                                            })
                                        })
                                    }
                                }
                            })
                        })
                    })
                })
            })
        })
    } catch (err) {
        console.log(err)
    }
}



module.exports.getDeliveryDate = function (days) {
    try {
        return new Promise((resolve, reject) => {

            let curDate = new Date('Y-m-d');
            let deliveryDate = [];
            if (Array.isArray(days) && days.length > 0) {
                days.forEach(day => {
                    let date = date('Y-m-d', day.toTimeString('this week'));
                    if (date >= curDate) {
                        let date = date('Y-m-d', day.toTimeString(' next week'));
                    }
                    deliveryDate = date;

                })
            }
            resolve(deliveryDate);
        })

    } catch (err) {
        console.log(err)
    }

}

module.exports.getReferenceCode = function (prefix, stateCode = 'TS', commit = 1) {
    try {
        return new Promise((resolve, reject) => {
            let refNoArr = "SELECT CONCAT(state_code,prefix,DATE_FORMAT(CURDATE(), '%y'),LPAD(MONTH(CURDATE()), 2, '0'),LPAD(serial_numbers.`reference_id`,serial_numbers.`length`,0)) AS ref_no FROM serial_numbers WHERE serial_numbers.`state_code` = ? AND serial_numbers.`prefix` = ? LIMIT 1";
            db.query(refNoArr, [stateCode, prefix], function (err, rows) {
                // console.log("rows", rows);
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    let update = "update `serial_numbers` set `reference_id` = (reference_id+1) where `prefix` = ? and `state_code` = ?";
                    db.query(update, [prefix, stateCode], function (err, updaterows) {
                        let refno = rows[0].hasOwnProperty('ref_no') ? rows[0].ref_no : '';
                        // console.log("refno", refno);
                        resolve(refno);

                    })

                } else {
                    resolve("");
                }

            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.getPoDetailById = function (po_id, legal_entity_id, orderby = 'parent_id') {
	try {
		return new Promise((resolve, reject) => {
			let globalSupperLier = "select `description` from `master_lookup` where `value` = 78023";
			db.query(globalSupperLier, {}, function (err, glbsupplier) {
				// console.log("gllll", glbsupplier);
				if (err) {
					console.log(err);
					reject(err);
				} else if (Object.keys(glbsupplier).length > 0) {
					let globalSupperLierId = glbsupplier[0].hasOwnProperty('description') ? glbsupplier[0].description : null;
					// console.log("ghhhhh", globalSupperLierId);
					module.exports.checkStockist(legal_entity_id).then(is_Stockist => {
						// console.log("stosss", is_Stockist);
						module.exports.getAllDCLeids().then(dc_le_id_list => {
							//console.log("dc_le_id_listhere",dc_le_id_list);
							dc_le_id_list = dc_le_id_list[0];
							//console.log("dc_le_id_listhhhhhh",dc_le_id_list);
							globalSupperLierId = globalSupperLierId + "," + dc_le_id_list.dc_le_id_list;
							//console.log("glbbbb",globalSupperLierId);
							//console.log("dclistttt",dc_le_id_list);
							let stockistQuery = "";
							if (is_Stockist > 0) {
								stockistQuery = "and pph.supplier_id IN (" + globalSupperLierId + ")";
								// console.log("if", stockistQuery);

							} else {
								stockistQuery = "and pph.supplier_id NOT IN (" + globalSupperLierId + ")";
								// console.log("ifelse", stockistQuery);

							}
							// console.log("ifelse", stockistQuery);
							let curdate = new Date()
							let currentdate = curdate.toISOString().split('T')[0];
							currentdate = currentdate + ' 23:59:59';
							// console.log("currentdate", currentdate);
							let lastdate = curdate.setDate(curdate.getDate() - 30);
							lastdate = new Date(lastdate);
							lastdate = lastdate.toISOString().split('T')[0];
							lastdate = lastdate + ' 00:00:00';
							// console.log("lastdate", lastdate);
							//let lastdate = new Date('Y-m-d 00:00:00',strtotime('-30 days'));

							let query = `SELECT     po.le_wh_id, 
							po.legal_entity_id, 
							po.po_address, 
							po.po_id, 
							po.po_code, 
							po.parent_id AS poparentid, 
							po.indent_id, 
							po.po_type, 
							po.po_status, 
							po.is_closed, 
							po.po_date, 
							po.payment_due_date, 
							po.created_at, 
							po.delivery_date, 
							po.exp_delivery_date,
							po.po_validity, 
							po.po_remarks, 
							po.reason_to_close, 
							po.logistics_cost, 
							po.payment_mode, 
							po.payment_type, 
							po.payment_refno, 
							po.tlm_name, 
							po.tlm_group, 
							po.approval_status, 
							po.is_stock_transfer as stock_transfer,
							po.stock_transfer_dc,
							po.create_order,
							po.payment_status, 
							po.apply_discount_on_bill, 
							po.discount_type, 
							po.discount, 
							po.po_so_status, 
							po.po_so_order_code, 
							po.discount_before_tax, 
							Getmastlookupvalue(po.payment_status) AS paymentstatus, 
							Getusername(po.created_by,2)          AS user_name, 
							Getlewhname(po.supply_le_wh_id)       AS dc_name, 
							getLeWhName(po.stock_transfer_dc)     AS st_dc_name,
							currency.code                         AS currency_code, 
							supply_le_wh_id, 
							currency.symbol_left AS symbol, 
							po_products.product_id, 
							po_products.qty, 
							po_products.free_qty, 
							po_products.free_uom, 
							po_products.free_eaches, 
							po_products.price, 
							po_products.sub_total, 
							po_products.uom, 
							po_products.unit_price, 
							po_products.cur_elp,
							po_invoice_products.free_qty AS inv_free_qty,
							po_invoice_products.unit_price AS inv_unit_price,
							po_invoice_products.price AS inv_price,
							po_invoice_products.qty AS inv_qty,
							po_invoice_products.discount_amount,
							inward.shipping_fee,
							inward.discount_on_total,
							inward.inward_code,
							inward.created_at AS inward_date,
							po_invoice_grid.created_at AS invoice_date,
							po_invoice_grid.invoice_code,
							GetUserName(po_invoice_grid.created_by,2) AS invoice_created_name, 
							gdsp.product_title, 
							gdsp.mrp, 
							gdsp.sku, 
							gdsp.seller_sku, 
							gdsp.manufacturer_id, 
							po_products.is_tax_included, 
							po_products.tax_name, 
							po_products.tax_per, 
							po_products.tax_amt, 
							po_products.hsn_code, 
							po_products.tax_data, 
							po_products.no_of_eaches, 
							po_products.apply_discount, 
							po_products.discount_type AS item_discount_type, 
							po_products.discount      AS item_discount, 
							tot.dlp, 
							( 
								   SELECT Min(elp) 
								   FROM   purchase_price_history AS pph 
								   WHERE  pph.product_id=po_products.product_id  ${stockistQuery} ) AS std , 
							( 
								   SELECT min(elp) 
								   FROM   purchase_price_history AS pph 
								   WHERE  pph.product_id=po_products.product_id 
								   AND    effective_date BETWEEN ' ${lastdate}' AND  '${currentdate}' ${stockistQuery}) AS thirtyd,
							( 
									 SELECT   elp 
									 FROM     purchase_price_history AS pph 
									 WHERE    pph.product_id=po_products.product_id  ${stockistQuery}
									 AND      pph.created_at < po.created_at 
									 AND      pph.po_id!=po.po_id 
									 AND      pph.le_wh_id = po.le_wh_id 
									 ORDER BY effective_date DESC 
									 LIMIT    0,1) AS prev_elp, 
							( 
								   SELECT available_inventory 
								   FROM   vw_inventory_report 
								   WHERE  product_id = po_products.product_id 
								   AND    le_wh_id = po.le_wh_id) AS 'available_inventory', ( 
							CASE 
									   WHEN po_products.parent_id=0 THEN po_products.product_id 
									   ELSE po_products.parent_id 
							end ) AS parent_id 
				 FROM       po 
				 INNER JOIN po_products 
				 ON         po.po_id = po_products.po_id 
				 INNER JOIN products AS gdsp 
				 ON         gdsp.product_id = po_products.product_id 
				 LEFT JOIN  product_tot AS tot 
				 ON         po_products.product_id = tot.product_id 
				 AND        gdsp.product_id = tot.product_id 
				 AND        po.le_wh_id = tot.le_wh_id 
				 AND        po.legal_entity_id = tot.supplier_id 
				 LEFT JOIN  currency 
				 ON         currency.currency_id = po.currency_id 
				 LEFT JOIN inward ON po.po_id=inward.po_no
				LEFT JOIN po_invoice_grid ON inward.inward_id=po_invoice_grid.inward_id
				LEFT JOIN po_invoice_products ON po_invoice_grid.po_invoice_grid_id=po_invoice_products.po_invoice_grid_id
				AND po_products.product_id=po_invoice_products.product_id
				 WHERE      po.po_id = ${po_id} 
				 ORDER BY   parent_id ASC`

							sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
								if (err) {
									console.log(err);
									reject(err);
								} else {
									resolve(response);
								}
							})
						})
					})
				}

            })



        })
    } catch (err) {
        console.log(err)
    }
}

/**
 * Duplicate of getUserByLeId
 */
module.exports.getUserLeId = function (leId) {
    try {
        return new Promise((resolve, reject) => {
            let query = `select users.* from users where users.legal_entity_id = ${leId} limit 1;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                resolve(response[0]);
                // console.log(response);
            })
        })
    } catch (err) {
        console.log(err)
    }
}


module.exports.getUserByLeId = function (leId) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `users`.* from `users` where `users`.`legal_entity_id` = ? limit 1";
            db.query(query, [leId], function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
    }
}


module.exports.getInwardProductsCountByPOId = function (poId) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `inward_products`.`product_id`, SUM(inward_products.received_qty) as received from `inward_products` left join `inward` on `inward`.`inward_id` = `inward_products`.`inward_id` where `inward`.`po_no` = ? group by `inward_products`.`product_id`";
            db.query(query, [poId], function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve("");
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

/**
 * get legal_entity_id from users table
 */
module.exports.getLEID = (userId) => {
    try {
        return new Promise((resolve, reject) => {
            let query = `Select legal_entity_id from users where user_id = ${userId};`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                if (response.length > 0) {
                    resolve(response[0].legal_entity_id);
                    // console.log(response);
                } else {
                    console.log("getLEID invalid userID");
                    reject();
                }
            })
        })
    } catch (err) {
        console.log(err)
    }
}

module.exports.getPoCountByStatus = async (userId, leId, approval = 0, fromDate, toDate) => {
    try {
        return new Promise(async (resolve, reject) => {
            let dataArr = [];
            let bb = [];
            let userData = await this.checkUserIsSupplier(userId);
            let globalFeature = await roleModel.checkPermissionByFeatureCode('GLB0001', userId);//response true/false
            let inActiveDCAccess = await roleModel.checkPermissionByFeatureCode('GLBWH0001', userId);//response true/false
            let query = `select po.po_status ,po.approval_status ,count(DISTINCT po.po_id ) as tot from po 
            join legal_entities on legal_entities.legal_entity_id = po.legal_entity_id 
            join legalentity_warehouses as lewh on lewh.le_wh_id  = po.le_wh_id`

            if (!globalFeature) {
                query += ` join user_permssion as up on up.object_id = lewh.bu_id AND
                up.user_id = ${userId} and up.permission_level_id = 6`
            } else {
                // console.log("not a globalFeature");
            }
            if (!inActiveDCAccess) {
                query += ` where lewh.status = 1`
            } else {
                // console.log('has access to inActiveDCs');
                query += ` where lewh.status in (0,1)`

            }

            if (fromDate != "" && toDate != "") {
                //  console.log(`taking dates ${fromDate} and ${toDate}`);
                //  query += ` and po.po_date between '${fromDate}' and '${toDate}'`;
                query += ` AND po.po_date between '${fromDate} 00:00:00' and '${toDate} 23:59:59'`
            }

            if (approval == 1) {
                // console.log("approval 1");
                query += ` AND po.po_status not in (87003,87004) group by po.approval_status`;
            } else if (approval == 2) {
                // console.log("approval 2");
                query += ` AND po.po_status in (87002,87005) and po.approval_status = 1 group by po.approval_status`;
            } else if (approval == 3) {
                query += `  AND po.po_status =87005 and po.is_closed = 0 group by po.po_status`;
            } else if (approval == 4) {
                query += ` AND (po.po_status =87002 OR po.is_closed = 1) AND approval_status NOT IN (1,0,null) group by  po.po_status`;
            }
            else if (approval == 5) {// incomplete code
                query += ` And (po.payment_mode = 2 or po.payment_due_date <= NOW() ) and (po.payment_status = 57118 or po.payment_status is null) and po.approval_status not in (57117, 57106, 57029, 57030) group by po.po_status`;
            }

            else {
                query += ` group by po.po_status`;
            }
            if (userData.length > 0) {
                let brands = await this.getAllAccessBrands(userId);
                // console.log('brands',brands);
                // **********PENDING**************PENDING***********************PENDING************
                // return;
            }

            // console.log('query WHICH I WANT',query);



            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                response.forEach(data => {
                    let result = {};
                    if (approval == 1 || approval == 2) {
                        result[data.approval_status] = data.tot;
                    } else {
                        result[data.po_status] = data.tot;
                    }
                    result[data.po_status] = data.tot;
                    dataArr.push(result);
                })
                if (dataArr.length > 0) {
                    dataArr = dataArr.reduce((a, b) => Object.assign({}, a, b));
                    bb.push(dataArr);
                    // console.log('dataArr', bb);
                }
                resolve(bb);
            })
        })
    } catch (err) {
        console.log('Catch(err) -> getPoCountByStatus', err);

    }
}

module.exports.checkUserIsSupplier = async (userId) => {
    try {
        return new Promise((resolve, reject) => {
            let query = ` SELECT * FROM users u JOIN legal_entities l ON u.legal_entity_id= l.legal_entity_id 
            WHERE l.legal_entity_type_id IN (1006,1002,89002) 
            AND  u.is_active=1 AND u.user_id= ${userId}`

            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                (response.length > 0) ? resolve(response) : resolve([]);
            })
        })
    } catch (err) {
        console.log('checkUserIsSupplier Error : ', err);
        reject(err);
    }
}

module.exports.getAllAccessBrands = async (userId) => {
    try {
        return new Promise((resolve, reject) => {
            // let brands;
            let query = ` SELECT object_id FROM user_permssion WHERE permission_level_id = 7 AND user_id =${userId};`
            // let query  = ` SELECT object_id FROM user_permssion WHERE permission_level_id = 7 limit 10;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                // console.log('respone', response);
                let brands = response.map(o => o['object_id']);
                resolve(brands);
            })
        })
    } catch (err) {
        console.log('getAllAccessBrands Error', err);
        reject(err);
    }
}

module.exports.getIndentCodeById = async (indentId) => {
	try {
		return new Promise((resolve, reject) => {
			let query = `SELECT indent_code FROM indent WHERE indent_id = ${indentId};`

			sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
				resolve(response);
			})
		})
	} catch (err) {
		console.log("getIndentCodeById Error", err);
		reject(err);
	}
}

//both for po and grn approval history
module.exports.getApprovalHistory = async (id,module_name) => {
	try {
		return new Promise((resolve, reject) => {
			let totalHistory;
			let history;
			let value;

			let query = "SELECT value FROM master_lookup WHERE master_lookup_name = '"+module_name+"'  AND mas_cat_id = 56";
			sequelize.query(query, { type: Sequelize.QueryTypes.SELECT })
				.then(response => {
					value = response[0].value;
				})
				//get comments from appr_comments table
				.then(() => {
					let query1 = `SELECT comments FROM appr_comments WHERE comments_id = ${id} AND awf_for_type_id = ${value};`
					sequelize.query(query1, { type: Sequelize.QueryTypes.SELECT }).then(response => {
						if (response.length > 0) {
							history = response[0].comments;
							history = JSON.parse(history);
							if (Array.isArray(history) || history.length > 0) {
								history = history.reverse();
								resolve(history);
							} else {
								history = [];
								resolve(history);
							}
						}

						// get details from appr_workflow_history table
						else {
							let query2 = `SELECT us.profile_picture, us.firstname, us.lastname, group_concat(rl.name) 
							AS 
							  name, 
							  hs.created_at, hs.status_to_id, hs.status_from_id, hs.awf_comment, ml.master_lookup_name 
							  FROM appr_workflow_history 
							AS 
							  hs 
							  JOIN users 
							AS 
							  us ON us.user_id=hs.user_id 
							  JOIN user_roles 
							AS 
							  ur ON ur.user_id=hs.user_id 
							  JOIN roles 
							AS 
							  rl ON rl.role_id=ur.role_id 
							  JOIN master_lookup 
							AS 
							  ml ON ml.value=hs.status_to_id 
							  WHERE hs.awf_for_id = ${id} 
							  AND hs.awf_for_type = '${module_name}'
							  GROUP BY hs.created_at 
							  ORDER BY hs.created_at DESC ;`

							sequelize.query(query2, { type: Sequelize.QueryTypes.SELECT }).then(response => {
								history = response;
								resolve(history);
							})

						}
					})
				})
		})
	} catch (err) {
		reject(err);
	}

}

// module.exports.getSuppliersforIndents = function(data){
//  try{
//      return new Promise((resolve,reject)=>{
//          var leId = data[0].leId;
//          var userId = data[0].user_id;
//          let legalentityquery = 'select `legal_entity_type_id` from `legal_entities` where `legal_entity_id` = '+leId+' limit 1';
//              db.query(legalentityquery,{},(err,le_type)=>{
//                  console.log('inside db');
//                  if(err){
//                      console.log(err);
//                      reject('Bad request');
//                  }else{
//                      let le_type_id= le_type[0].legal_entity_type_id;
//                      let permissionlevel = 'select `name` from `permission_level` where `permission_level_id` = 6 limit 1';
//                      db.query(permissionlevel,{},(err,per_level)=>{
//                          if(err){
//                              reject('Bad request');
//                          }else{
//                              let per_level_name = per_level[0].name;
//                              let featurecheck1 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` = 1)';
//                              db.query(featurecheck1,[userId,'GLB0001'],(err,fea_check1)=>{
//                                  if(err){
//                                      reject('Bad request');
//                                  }else{
//                                  let global_aceess = fea_check1[0].aggregate;
//                                  let featurecheck2 = 'select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = ? and `features`.`is_active` =1)';
//                                      db.query(featurecheck2,[userId,'GLBWH0001'],(err,fea_check2)=>{
//                                          if(err){
//                                              reject('Bad request');
//                                          }else{
//                                              let global_wh_aceess = fea_check2[0].aggregate;
//                                              let userpermission ='select group_concat(distinct(`object_id`)) as bu_id from `user_permssion` where (`user_id` = ? and `permission_level_id` = 6)';
//                                                  db.query(userpermission,[userId],(err,user_per)=>{
//                                                      if(err){
//                                                          reject('Bad request');
//                                                      }else{
//                                                          console.log(user_per);
//                                                          let bu_id= user_per[0].bu_id;
//                                                          let bu_list = bu_id.split(',');
//                                                          let getDcList = 'select GROUP_CONCAT(le_wh_id) as le_wh_id, `dc_type` from `legalentity_warehouses` where `dc_type` > 0 ';
//                                                          if(!global_aceess){
//                                                              if(!bu_list.includes(0))
//                                                                  getDcList = getDcList+ ' and bu_id in ('+bu_id+')';
//                                                              else
//                                                                  getDcList = getDcList+' and dc_type in (118001,118002)';
//                                                          }
//                                                          getDcList = getDcList+' group by `dc_type`';
//                                                              db.query(getDcList,{},(err,dc_data)=>{
//                                                                  if(err){
//                                                                      reject('Bad request');
//                                                                  }else{
//                                                                      let dc_list={};
//                                                                          dc_data.forEach(dc_specific=>{
//                                                                              let {dc_type}=dc_specific;
//                                                                              console.log('dc_type_eded');
//                                                                              console.log(dc_type);
//                                                                              dc_list[dc_type]=dc_specific.le_wh_id;
//                                                                          })
//                                                                          console.log(dc_list);
//                                                                          let supplierlist = 'select `legal_entities`.`legal_entity_id`, `legal_entities`.`business_legal_name`, `city`, `legal_entities`.`le_code` from `legal_entities` inner join `suppliers` on `suppliers`.`legal_entity_id` = `legal_entities`.`legal_entity_id` where (`legal_entities`.`legal_entity_type_id` = ? and `suppliers`.`is_active` = 1 and `legal_entities`.`is_approved` = 1 and `parent_id` = ?)';
//                                                                              db.query(supplierlist,[1002,leId],(err,supplier_list)=>{
//                                                                                  if(err){
//                                                                                      reject('Bad request');
//                                                                                  }else{
//                                                                                      //console.log(supplier_list);
//                                                                                      let supplierlist = supplier_list;
//                                                                                      if(!supplierlist){
//                                                                                          supplierlist=[];
//                                                                                      }
//                                                                                      let masterlookupname = 'select `description` from `master_lookup` where `value` = ? limit 1';
//                                                                                      db.query(masterlookupname,[78023],(err,mst_name)=>{
//                                                                                          if(err){
//                                                                                              reject('Bad request');
//                                                                                          }else{
//                                                                                              universalSupplier = mst_name[0].description;
//                                                                                              let getbussinessname = 'select `legal_entity_id`, `business_legal_name` from `legal_entities` where `legal_entity_id` = ? limit 1';
//                                                                                              db.query(getbussinessname,[universalSupplier],(err,bus_name)=>{
//                                                                                                  if(err){
//                                                                                                      reject('Bad request');
//                                                                                                  }else{
//                                                                                                      let le_list = bus_name;         
//                                                                                                      supplierlist=supplierlist.concat(le_list);
//                                                                                                      //console.log('supplierlistpradeepa');
//                                                                                                      let btype = 'select  business_legal_name,legal_entity_id,address1,address2, city,pincode,pan_number,tin_number, (SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype from `legal_entities` left join `dc_fc_mapping` on `dc_le_id` = `legal_entities`.`legal_entity_id` where `dc_fc_mapping`.`fc_le_id` = ?';
//                                                                                                          db.query(btype,[le_id],(err,btype)=>{
//                                                                                                              if(err){
//                                                                                                                  reject('Bad request');
//                                                                                                              }else{
//                                                                                                                  if(btype.length>0){
//                                                                                                                      supplierlist=supplierlist.concat(btype);
//                                                                                                                  }
//                                                                                                                  console.log('le_type',le_type_id);
//                                                                                                                  if(le_type_id==1001){
//                                                                                                                      let fc_dc_legalentities = "select GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id from dc_fc_mapping where (dc_le_wh_id in (?) or fc_le_wh_id in (?)) limit 1";
//                                                                                                                      db.query(fc_dc_legalentities,[dc_list[118001],dc_list[118001]],(err,dc_fc_list)=>{
//                                                                                                                          if(err){
//                                                                                                                              reject('Bad request');
//                                                                                                                          }else{
//                                                                                                                          console.log(dc_fc_list);
//                                                                                                                              if(dc_fc_list[0].dc_le_id){
//                                                                                                                                  let dc_le_id_list = dc_fc_list[0].dc_le_id;
//                                                                                                                                  console.log('dc_le_id_list',dc_le_id_list);
//                                                                                                                                  let dc_le_list = dc_le_id_list.split(',');
//                                                                                                                                  console.log('dc_le_list',dc_le_list);

//                                                                                                                                  let dcfcList="Select legal_entities.legal_entity_id,legal_entities.legal_entity_type_id, legal_entities.business_legal_name,(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype from legal_entities where legal_entities.legal_entity_id in (?) and legal_entities.legal_entity_type_id in (1014,1016)";
//                                                                                                                                      db.query(dcfcList,[dc_le_list],(err,dc_fc_list2)=>{
//                                                                                                                                          if(err){
//                                                                                                                                              reject('Bad request');
//                                                                                                                                          }else{
//                                                                                                                                              console.log('dc_fc_list2',dc_fc_list2);
//                                                                                                                                              supplierlist=supplierlist.concat(dc_fc_list2);
//                                                                                                                                              resolve(supplierlist);
//                                                                                                                                          }
//                                                                                                                                      })
//                                                                                                                              }
//                                                                                                                          }
//                                                                                                                      })  
//                                                                                                                  }                           


//                                                                                                  }
//                                                                                              })                      
//                                                                                          }
//                                                                                      })
//                                                                                  }
//                                                                              })              
//                                                                      }
//                                                              })
//                                                      }
//                                                  })
//                                              }                           

//                                      })
//                                  }       
//                              })
//                          }
//                      })
//                  }
//              })                  
//      });     
//  } catch(err){
//      console.log(err)
//  }
// }
module.exports.deletePoProducts = async function (poId, delproduct_id) {
    return new Promise(async (resolve, reject) => {
        var deletePoProduct = 'delete from po_products where po_id=' + poId + ' and product_id=' + delproduct_id + ' ';
        db.query(deletePoProduct, {}, function (err, res) {
            if (err) {
                reject('error');
            } else {
                resolve(res);
            }
        });
    });
}

module.exports.checkPOProductExist = async function (poId, product_id) {
    return new Promise((resolve, reject) => {
        var productInfo = 'select `po_id`,`product_id` from `po_products` where `po_id` =' + poId + '  and `product_id`=' + product_id + ' limit 1';
        db.query(productInfo, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows.length);
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getPreUpdatePOProducts = async function (poId, product_id) {
    return new Promise((resolve, reject) => {
        var productInfo = 'select `qty`, `uom`,`no_of_eaches`, `free_qty`,`free_uom`, `free_eaches`,`is_tax_included`, `apply_discount`, `discount_type`,`discount`,`unit_price`,`price`,`tax_name`,`tax_per`,`tax_amt`,`sub_total` from `po_products` where `po_id` =' + poId + '  and `product_id`=' + product_id + ' limit 1';
        db.query(productInfo, {}, function (err, rows) {
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
}

module.exports.savePoProducts = async function (params) {
    return new Promise((resolve, reject) => {
        // console.log('params', params.qty);
        let query = `INSERT INTO po_products (qty,uom,no_of_eaches,free_qty,free_uom,free_eaches,is_tax_included,apply_discount,
            discount_type,discount,unit_price,price,tax_name,
            tax_per,tax_amt,sub_total,po_id,product_id,mrp,parent_id,tax_data,hsn_code,cur_elp) VALUES ('${params.qty}', '${params.uom}', '${params.no_of_eaches}', '${params.free_qty}', '${params.free_uom}', '${params.free_eaches}', '${params.is_tax_included}', '${params.apply_discount}', '${params.
                discount_type}', '${params.discount}', '${params.unit_price}', '${params.price}', '${params.tax_name}', '${params.
                    tax_per}', '${params.tax_amt}', '${params.sub_total}', '${params.po_id}', '${params.product_id}', '${params.mrp}', '${params.parent_id}', '${params.tax_data}', '${params.hsn_code}', '${params.cur_elp}');`

        db.query(query, {}, function (err, rows) {
            if (err) {
                con.rollback(function (err) {
                    reject(err)
                })
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            } else {
                return resolve([]);
            }
        });

    });
}

module.exports.updatePOProducts = async function (po_product, productid, poId, flagdata) {
    return new Promise((resolve, reject) => {
        let dictionary = '';
        for (var key in po_product) {
            // console.log('keyyyyyyy', key);
            if (key != 'tax_data') {
                dictionary += key + "='" + po_product[key] + "',"
            }
            // if(key == payment_type) {
            //  dictionary += key + "=" + +po_product[key] + ","
            // } 
            else {
                // console.log('keyyyyy', key);
                dictionary += key + "='" + JSON.stringify(po_product[key]) + "',"
                // dictionary += `${key} = ${po_product[key]}`
                // console.log('yyyyyyy', dictionary, po_product[key]);
            }
        }
        let lastChar = dictionary.slice(-1);
        if (lastChar == ',') {
            dictionary = dictionary.slice(0, -1);
        }
        // console.log('keyyyyyyy', dictionary);
        // console.log("this should be resolved::::", dictionary['tax_data'], `${po_product['tax_data']}`); 

        let updatePoPrd = "UPDATE po_products SET " + dictionary + " WHERE product_id=" + productid + " and po_id=" + poId;
        // console.log('queryyyy ',updatePoPrd);
        db.query(updatePoPrd, {}, function (err, rows) {
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
}

module.exports.updatePO = async function (poId, podata) {
    return new Promise((resolve, reject) => {
        var dictionary = '';
        for (var key in podata) {
            // console.log('keyyy', key);
            // if(key == payment_type){
            //  dictionary += key + "=" + +podata[key] + ","
            // } else {
            // }
            dictionary += key + "='" + podata[key] + "',"
        }
        var lastChar = dictionary.slice(-1);
        if (lastChar == ',') {
            dictionary = dictionary.slice(0, -1);
        }
        // console.log('dictionary', dictionary);
        var updatePoPrd = "UPDATE po SET " + dictionary + " WHERE po_id=" + poId;
        // console.log('updatePoPrd', updatePoPrd);
        db.query(updatePoPrd, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                // console.log('=====================');
                return resolve(rows);
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getPoCodeById = async function (poId) {
    return new Promise((resolve, reject) => {
        var poCode = 'select `po_code`,`le_wh_id`,`parent_id`,`po_date`,`legal_entity_id`,`approval_status`,`payment_status` from `po` where `po_id` =' + poId + ' limit 1';
        db.query(poCode, {}, function (err, rows) {
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

module.exports.updatePoStDc = function (req, res) {
    return new Promise((resolve, reject) => {
        var updatePoId = "update po set stock_transfer_dc = '" + req.stock_transfer_dc + "', is_stock_transfer = '" + req.is_stock_transfer + "' where po_id = '" + req.po_id + "'";
        db.query(updatePoId, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            }
            else {
                return resolve([]);
            }
        });
    });
}

module.exports.updatePoSupplyDc = function (req, res) {
    return new Promise((resolve, reject) => {
        var updatePoId = "update po set supply_le_wh_id = " + req.supply_le_wh_id + ",create_order="+req.create_order+" where po_id = " + req.po_id + " ";
        db.query(updatePoId, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            }
            else {
                return resolve([]);
            }
        });
    });
}

module.exports.updatePoSoCode = function (id) {
    return new Promise((resolve, reject) => {
        var query = "update po set po_so_order_code = '" + '' + "', po_so_status = 0 where po_id = " + id + "";
        db.query(query, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            }
            else {
                return resolve([]);
            }
        });
    });
}


module.exports.updatePoSupplier = function (req, res) {
    return new Promise((resolve, reject) => {
        var updatePoId = "update po set legal_entity_id = "+req.legal_entity_id+" , po_address='"+req.po_address+"' where po_id = " + req.po_id + " ";
        db.query(updatePoId, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            }
            else {
                return resolve([]);
            }
        });
    });
}

module.exports.getOrderIdByCode = async function (order_code) {
    return new Promise((resolve, reject) => {
        var query = "select `gds_order_id` from `gds_orders` where `order_code` ='" + order_code + "' order by created_at desc ";
        db.query(query, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows[0]['gds_order_id']);
            } else {
                return resolve('');
            }
        });
    });
}

module.exports.getOrderInfoById = function (orderId) {
    return new Promise((resolve, reject) => {
        var query = 'select `order_status_id` from `gds_orders` where `gds_order_id` = ' + orderId + '  limit 1';
        db.query(query, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows[0]['order_status_id']);
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getUsersByRoleCode = async function (user_id) {
    return new Promise(async (resolve, reject) => {
        var result = "select `roles`.`name`,`roles`.`role_id`,concat(`users`.`firstname`,' ',`users`.`lastname`) as username,users.user_id,users.email_id, users.mobile_no from `roles`  join `user_roles` on `roles`.`role_id` = `user_roles`.`role_id` join `users` on `user_roles`.`user_id`= `users`.`user_id` where (`users`.`is_active` = 1 and `roles`.`is_deleted` = 0) and `roles`.`short_code` IN ('FS','FH','FFNO','FFNM')";
        let globalAccess = await roleModel.checkPermissionByFeatureCode('GLB0001', user_id);
        if (!globalAccess) {
            module.exports.getLegalEntityId(user_id).then(le_id => {
                result += "and `users`.`legal_entity_id`=" + le_id.le_id + "";
            });
        }
        result += "group by users.user_id order by CONCAT(users.firstname,' ',users.lastname) asc";
        db.query(result, {}, function (err, rows) {
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
}

module.exports.updateStatusAWF = async function (approval_unique_id, next_status_id, user_id) {
    return new Promise((resolve, reject) => {
        var status = next_status_id.split(",");
        if (status[1] == 0) {
            var new_status = status[0];
        } else {
            var new_status = status[1];
        }
        var invoice = {};
        invoice.approved_by = user_id;
        var current_datetime = new Date();
        var status_createddate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
        invoice.approved_at = status_createddate;
        var check_status = [57118, 57032, 57222, 57223, 57224];
        if (check_status.includes(new_status)) {
            invoice.payment_status = new_status
        } else {
            invoice.approval_status = new_status;
        }
        var dictionary = '';
        for (var key in invoice) {
            dictionary += key + "='" + invoice[key] + "',"
        }
        var lastChar = dictionary.slice(-1);
        if (lastChar == ',') {
            dictionary = dictionary.slice(0, -1);
        }
        var query = "update po set " + dictionary + " where po_id=" + approval_unique_id + "";
        db.query(query, {}, function (err, rows) {
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
}

module.exports.savePoDocuments = async function (poId, url) {
    return new Promise((resolve, reject) => {
        var current_datetime = new Date();
        var createddate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
        var query = "INSERT INTO po_docs (po_id,file_path,created_at) VALUES (" + poId + ",'" + url + "','" + createddate + "') ";
        db.query(query, {}, function (err, inserted) {
            if (err) {
                console.log('model error');
                reject('error');
            }
            if (Object.keys(inserted).length > 0) {
                return resolve(inserted.insertId);
            } else {
                console.log('model upload error');
                return resolve([]);
            }
        });
    });
}

module.exports.deleteDoc = async function (doc_id, po_id) {
    return new Promise((resolve, reject) => {
        var query = "DELETE from po_docs where po_id=" + po_id + " and doc_id=" + doc_id + ""
        db.query(query, {}, function (err, rows) {
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
}

module.exports.deletePODoc = async function (po_id) {
    return new Promise((resolve, reject) => {
        var query = "DELETE from po_docs where po_id=" + po_id
        db.query(query, {}, function (err, rows) {
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
}


module.exports.checkPayment = async function (po_id) {
    return new Promise((resolve, reject) => {
        var query = "select `po_id` from `vendor_payment_request` where `po_id` = '" + po_id + "' and `approval_status` IN('57203','57204','57218','57219','57222')";
        db.query(query, {}, function (err, rows) {
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
}

module.exports.getAllInvoices = async (poId, rowCnt = 0) => {
    return new Promise((resolve, reject) => {

        let query = `SELECT invoice.*,SUM(items.qty) AS totQty, inward.inward_code FROM po_invoice_grid AS invoice
        JOIN po_invoice_products AS items ON invoice.po_invoice_grid_id = items.po_invoice_grid_id
        JOIN inward  ON invoice.inward_id = inward.inward_id WHERE inward.po_no = ${poId};`

        sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
            if (response.length > 0) {
                resolve(response);
            } else {
                console.log('no invoices found');
                reject("No Invoice Found");
            }
        })
    })
}


module.exports.exportToCsv = async (fromDate, toDate, dcList,is_grndate,supplier_list) => {
    return new Promise((resolve,reject)=>{
    	if(supplier_list!='NULL'){
		    var query = `CALL getPurchaseDetails('${fromDate}','${toDate}','${dcList}','${is_grndate}','${supplier_list}');`
		}else{
			var query = `CALL getPurchaseDetails('${fromDate}','${toDate}','${dcList}','${is_grndate}',${supplier_list});`
		}
		sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
			if (response.length > 0) {
				resolve(response);
			} else {
				console.log('no data found');
				reject("No data found");
			}
		});
	});
}




module.exports.getSupplierData = async function (new_supplier_id) {
    return new Promise((resolve, reject) => {
        var query = "select * from suppliers where legal_entity_id=" + new_supplier_id + ""
        db.query(query, {}, (err, rows) => {
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
}


module.exports.getDcData = async function (le_wh_id) {
    return new Promise((resolve, reject) => {
        var query = "select display_name,le_wh_id from legalentity_warehouses where le_wh_id=" + le_wh_id + "";
        db.query(query, {}, (err, rows) => {
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

module.exports.getPoInvoiceDetailById = async function (invoiceId) {
    return new Promise((resolve, reject) => {
        var query = "select `po`.`po_id`, `po`.`po_status`, `po`.`po_code`, `inward`.`inward_id`, `inward`.`inward_code`, `inward`.`created_at` as `inward_date`, `inward`.`le_wh_id`, `inward`.`legal_entity_id`, `inward`.`shipping_fee`, `inward`.`discount_on_total`, `grid`.`invoice_code`, `grid`.`billing_name`, `grid`.`invoice_status`, `grid`.`created_at` as `invoice_date`, `grid`.`grand_total`, `grid`.`approval_status`, GetUserName(grid.created_by,2) AS user_name, `invprod`.`product_id`, `invprod`.`qty`, `invprod`.`free_qty`, `invprod`.`unit_price`, `invprod`.`price`, `invprod`.`sub_total`, `invprod`.`tax_name`, `invprod`.`tax_per`, `invprod`.`tax_amount` as `tax_amt`, `invprod`.`hsn_code`, `invprod`.`tax_data`, `invprod`.`discount_per`, `invprod`.`discount_amount`, `invprod`.`comment`, `brands`.`brand_name`, `gdsp`.`product_title`, `gdsp`.`sku`, `gdsp`.`mrp` from `po_invoice_grid` as `grid` inner join `po_invoice_products` as `invprod` on `grid`.`po_invoice_grid_id` = `invprod`.`po_invoice_grid_id` inner join `inward` on `inward`.`inward_id` = `grid`.`inward_id` inner join `po` on `inward`.`po_no` = `po`.`po_id` inner join `products` as `gdsp` on `gdsp`.`product_id` = `invprod`.`product_id` left join `brands` on `brands`.`brand_id` = `gdsp`.`brand_id` where `grid`.`po_invoice_grid_id` = " + invoiceId + " ";
        db.query(query, {}, (err, rows) => {
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
}

module.exports.getPurchasedata=async function(from_date,to_date,is_grndate,dc_list,user_id){
	return new Promise((resolve, reject) => {
		this.getAccesDetails(user_id).then((filters) => {
			var dc_acess_list = filters['dc_acess_list'];
			var query="select date(po.created_at) as created_at, `legal_entities`.`business_legal_name`, `legal_entities`.`le_code`, getLeWhName(po.le_wh_id) as wh_name, `legal_entities`.`gstin`, (select GROUP_CONCAT(doc_ref_no) FROM inward_docs where inward_id = inward.inward_id) as SupplierInvoice, `inward`.`invoice_no`, `inward`.`2a_invoice_no` as `twoainvoice`, `inward`.`invoice_date`, `po`.`po_code`, date(po.po_date) as po_date, (select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue, `inward`.`inward_code`, date(`inward`.`created_at`) as `inward_date`, `inward`.`grand_total` as `grnValue`, `inward`.`discount_on_total` as `discount_total`, `po_invoice_grid`.`grand_total` as `invoiceValue`, `po_invoice_grid`.`po_invoice_grid_id`, `inward`.`inward_id`, `po`.`po_id` from `po` left join `inward` on `inward`.`po_no` = `po`.`po_id` left join `po_invoice_grid` on `inward`.`inward_id` = `po_invoice_grid`.`inward_id` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `po`.`legal_entity_id`";
			this.checkUserIsSupplier(user_id).then((userData) => {
				if(userData.length==0){
					query+="where";
				    query+=" FIND_IN_SET(po.le_wh_id,'"+dc_acess_list+"') IS NOT NULL";
			    }else{
			    	this.getAllAccessBrands(user_id).then((brands) => {
				    	this.getMasterLookUpDesc().then((globalSupperLierId)=>{
					    	query+="left join po_products as pop on pop.po_id=po.po_id left join products as pro on pop.product_id=pro.product_id";
					    	brands=brands.join(',');
					    	brands=brands.split(',');
					    	query+="where ";
					    	if(brands!=''){
		                        query+=" pop.brand_id IN ("+brands+")";
					    	}
					    	query+=" po.legal_entity_id NOT IN ("+globalSupperLierId+")";
				        });
			        });
	            }
	            query+=" AND po.le_wh_id IN ("+dc_list+")";
	            if(is_grndate==1){
	            	query+=" AND inward.created_at between '"+from_date+" 00:00:00' AND '"+to_date+" 23:59:59' ";
	            }else{
                   query+=" AND po.po_date between '"+from_date+" 00:00:00' AND '"+to_date+" 23:59:59' ";
                }
	            query+=" order by `po`.`po_date` desc";
	   
	            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
								resolve(response);
				})

	        });
		});
    });
}


module.exports.getAccesDetails = async function (user_id) {
    return new Promise((resolve, reject) => {
        roleModel.getFilterData(6, user_id).then((roles) => {
            roles = JSON.parse(roles);
            var data = [];
            var filters = JSON.parse(roles.sbu);
            var dc_acess_list = filters.hasOwnProperty('118001') ? filters['118001'] : 'NULL';
            var hub_acess_list = filters.hasOwnProperty('118002') ? filters['118002'] : 'NULL';
            data.dc_acess_list = dc_acess_list;
            data.hub_acess_list = hub_acess_list;
            return resolve(data);
        });
    });
}

module.exports.getInwardDetailById = async function (id) {
    return new Promise((resolve, reject) => {
        var query = "select `inward`.`grand_total`, `inward`.`discount_on_total`, `ip`.`tax_per`, `ip`.`tax_amount`, `ip`.`tax_data`, (select tax_name from po_products where po_products.po_id=inward.po_no and po_products.product_id=ip.product_id limit 1) as tax_name, `ip`.`discount_total`, `ip`.`sub_total`, `inward`.`inward_id`, `inward`.`inward_code`, `inward`.`created_at` from `inward` inner join `inward_products` as `ip` on `inward`.`inward_id` = `ip`.`inward_id` where `inward`.`inward_id`=" + id + " "
        db.query(query, {}, (err, rows) => {
            if (err) {
                console.log('err');
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows);
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getMasterLookUpDesc = async function () {
    return new Promise((resolve, reject) => {
        var globalSupplier = "select description from master_lookup where value=78023";
        db.query(globalSupplier, {}, (err, rows) => {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                var globalSupperLierId = rows[0].hasOwnProperty('description') ? rows[0].description : 'NULL';
                return resolve(globalSupperLierId);
            } else {
                return resolve([]);
            }
        });
    });
}

module.exports.getPurchaseHSNdata=async function(from_date,to_date,dc_list,user_id){
	return new Promise((resolve,reject)=>{
		var query="select Date(po.created_at) as created_at, `legal_entities`.`business_legal_name`, getLeWhName(po.le_wh_id) as wh_name, `legal_entities`.`gstin`, (select GROUP_CONCAT(doc_ref_no) FROM inward_docs where inward_id = inward.inward_id) as SupplierInvoice, `inward`.`invoice_no`, `inward`.`invoice_date`, `po`.`po_code`, Date(po.po_date) as po_date, (select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue, `inward`.`inward_code`, date(`inward`.`created_at`) as `inward_date`, `inward`.`grand_total` as `grnValue`, `inward`.`discount_on_total` as `discount_total`, `ip`.`hsn_code`, SUM(ip.sub_total) AS gstbase, (SELECT tax_name FROM po_products WHERE po_products.po_id=inward.po_no AND po_products.product_id=ip.product_id LIMIT 1) AS tax_name, `ip`.`tax_per`, SUM(ip.tax_amount) AS tax_amount, `inward`.`inward_id`, `po`.`po_id` from `inward_products` as `ip` left join `inward` on `inward`.`inward_id` = `ip`.`inward_id` left join `po` on `po`.`po_id` = `inward`.`po_no` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `inward`.`legal_entity_id` where `inward`.`le_wh_id` in ("+dc_list+") and `inward`.`created_at` between '"+from_date+"' and '"+to_date+"' group by `inward`.`inward_id`, `ip`.`hsn_code`, `ip`.`tax_per` order by `inward`.`created_at` desc"

          sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
								resolve(response);
			})
	});
}

module.exports.getPurchaseGSTdata=async function(from_date,to_date,is_grndate,dc_list,user_id){
	return new Promise((resolve,reject)=>{
		var query=" select `legal_entities`.`gstin`, `legal_entities`.`business_legal_name`, (select GROUP_CONCAT(doc_ref_no) FROM inward_docs where inward_id = inward.inward_id ) as SupplierInvoice, `inward`.`2a_invoice_no` as `twoainvoice`, (CASE WHEN `po`.`payment_mode` = 2 THEN 'R' ELSE 'R' END) AS 'paymentType', `inward`.`invoice_date`, `inward`.`grand_total` as `grnValue`, `po_invoice_grid`.`po_invoice_grid_id`, `inward`.`inward_id`, `po`.`po_id`, (select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue, getStateNameById(legalentity_warehouses.state) as State, `legalentity_warehouses`.`legal_entity_id` as `wh_legal_id`, `po`.`legal_entity_id` as `sup_legal_id` from `po` left join `inward` on `inward`.`po_no` = `po`.`po_id` left join `po_invoice_grid` on `inward`.`inward_id` = `po_invoice_grid`.`inward_id` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `po`.`legal_entity_id` left join `legalentity_warehouses` on `legalentity_warehouses`.`le_wh_id` = `po`.`le_wh_id`";
		this.checkUserIsSupplier(user_id).then((userData) => {
			if(userData.length==0){
				query+="where";
			    query+=" po.le_wh_id IN ("+dc_list+")";
		    }else{
		    	this.getAllAccessBrands(user_id).then((brands) => {
			    	this.getMasterLookUpDesc().then((globalSupperLierId)=>{
				    	query+="left join po_products as pop on pop.po_id=po.po_id left join products as pro on pop.product_id=pro.product_id";
				    	brands=brands.join(',');
				    	brands=brands.split(',');
				    	query+="where ";
				    	if(brands!=''){
	                        query+=" pop.brand_id IN ("+brands+")";
				    	}
				    	query+=" po.legal_entity_id NOT IN ("+globalSupperLierId+")";
			        });
		        });
            }
            if(is_grndate==1){
            	query+=" AND inward.created_at between '"+from_date+" 00:00:00' AND '"+to_date+"23:59:59' ";
            }else{
               query+=" AND po.po_date between '"+from_date+" 00:00:00' AND '"+to_date+" 23:59:59' ";
            }
            query+=" order by `po`.`po_date` desc";

            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
								resolve(response);
			})
		});
	});
}

module.exports.getInvoiceByCode = async function (invoiceCode) {
    return new Promise((resolve, reject) => {
        var query = "select'gds_invoice_grid.invoice_code','gds_invoice_grid_id','gds_invoice_grid.created_at' from gds_invoice_grid where invoice_code='" + invoiceCode + "' limit 1";
        db.query(query, {}, (err, rows) => {
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
}
module.exports.getpoDocs = async function (id) {
	return new Promise((resolve, reject) => {
		var query ="select file_path from po_docs where po_id = "+id+" ";
		db.query(query, {}, (err, rows) => {
			if (err) {
				reject('error');
				console.log('err', err);
			}
			if (Object.keys(rows).length > 0) {
				return resolve(rows);
			} else {
				return resolve([]);
			}
		});
	});
}

module.exports.checkIsEbutorSupplier = async function (id) {
	return new Promise((resolve, reject) => {
		var query ="select count(legal_entity_id) as count from legal_entities where legal_entity_id = "+id+" and is_eb=1 limit 1";
		db.query(query, {}, (err, rows) => {console.log(rows);
			if (err) {
				reject('error');
			}
			if (Object.keys(rows).length > 0) {
				return resolve(rows[0].count);
			} else {
				return resolve([]);
			}
		});
	});
}

module.exports.getSupplierByLEId = async function (id) {
	return new Promise((resolve, reject) => {
		var query ="select legal_entity_id,business_legal_name,le_code from legal_entities where legal_entity_type_id = 1002 and is_approved = 1 and parent_id ="+id+"";
		db.query(query, {}, (err, rows) => {
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
}
module.exports.getOrderStatusById = async function (order_id) {
    return new Promise((resolve, reject) => {
        var query = "select `order_status_id` from `gds_orders` where `gds_order_id` ='" + order_id + "' order by created_at desc ";
        db.query(query, {}, function (err, rows) {
            if (err) {
                reject('error');
            }
            if (Object.keys(rows).length > 0) {
                return resolve(rows[0]['order_status_id']);
            } else {
                return resolve('');
            }
        });
    });
}

module.exports.getWarehouseBySupplierId=async function(user_id,le_id){
	return new Promise((resolve, reject) => {
		this.getAccesDetails(user_id).then((filters) => {
			var dc_acess_list = filters['dc_acess_list'];
			var query="select `lewh`.`lp_wh_name`, `lewh`.`le_wh_id` from `legalentity_warehouses` as `lewh` left join `product_tot` as `lewhmap` on `lewhmap`.`le_wh_id` = `lewh`.`le_wh_id` left join `legal_entities` as `le` on `le`.`legal_entity_id` = `lewh`.`legal_entity_id` where `lewh`.`dc_type` = "+le_id+" and `lewh`.`le_wh_id` in ('"+dc_acess_list+"') group by `lewh`.`le_wh_id`";
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
				resolve(response);
			})
		});
    });
}

module.exports.getPurchaseReport = async (supplier_manf,sup_manf_Names,fdate,tdate) => {
    return new Promise((resolve,reject) =>{
        let query = '';

        /**
         * manufacture wise report if supplier_manf == 1
         */
        if(supplier_manf == 1) {
            query = `CALL getpo_dcfc_byManufacturer('${sup_manf_Names}','${fdate}','${tdate}')`;
        } 

         /**
         * manufacture wise report if supplier_manf == 0
         */
        else if(supplier_manf == 0) {
            query = `CALL getpo_apob_byManufacturer('${sup_manf_Names}','${fdate}','${tdate}')`;
        }

        db.query(query,{}, (err,response) => {
            if(err) {
                reject('error');
            } else{
                resolve(response);
            }
        })
    }
    )
}

module.exports.getManufacturersList = async () => {
    return new Promise((resolve,reject) => {
        let query = `SELECT business_legal_name, legal_entity_id FROM legal_entities WHERE legal_entity_type_id = 1006 GROUP BY legal_entity_id ORDER BY business_legal_name ASC ;`
    
        db.query(query,{}, (err,response) => {
            if(err) {
                reject('error');
            } else{
                resolve(response);
            }
        })
    })
}
