'use strict';

const Sequelize = require('sequelize');
const sequelize = require('../../config/sequelize');
const moment = require('moment');
var database = require('../../config/mysqldb');
var inventoryModel = require('../model/Inventory.js');
var role = require('../model/Role');
var roleModel = require('../model/Rolerepo');
let db = database.DB;
var qtycheck = [];
var http = require('http');
const { fromCallback } = require('bluebird');
var url = process.env.APP_TAXAPI;
let purchaseOrder = require('../model/purchaseOrder.js');


module.exports = {

    getAllInwards: async (userId, grnStatus, approvalStatusId, rowCnt = 0, fromDate, toDate, filter, perPage, offset, sortField = "inward_code", sortType = "desc") => {
        try {
            return new Promise(async (resolve, reject) => {
                const currentDateEnd = moment().format(("YYYY-MM-DD 23:59:59"));
                let userData = await module.exports.checkUserIsSupplier(userId);
                let globalFeature = await roleModel.checkPermissionByFeatureCode('GLB0001', userId);//response true/false
                let inActiveDCAccess = await roleModel.checkPermissionByFeatureCode('GLBWH0001', userId);//response true/false
                let accessList = await module.exports.getAccesDetails(userId);
                // console.log('DC accesss list:  ', accessList); // DC Access List

                // let query = `SELECT inward.*,
                // legal.business_legal_name, currency.symbol_left AS symbol,
                // po.po_code AS poCode, GetUserName(inward.created_by,2) AS createdBy,
                // getLeWhName(inward.le_wh_id) AS dcname, 
                // (SELECT SUM(po_products.sub_total) FROM po_products WHERE po_products.po_id=po.po_id) AS povalue,
                // SUM(products.discount_total) AS item_discount_value,
                // inward.grand_total AS grnvalue, po.po_code, po_invoice_grid.invoice_code FROM inward 
                // LEFT JOIN legal_entities AS legal ON legal.legal_entity_id = inward.legal_entity_id
                // LEFT JOIN inward_products AS products ON products.inward_id = inward.inward_id
                // LEFT JOIN currency ON currency.currency_id = inward.currency_id 
                // LEFT JOIN po ON po.po_id = inward.po_no 
                // LEFT JOIN po_invoice_grid ON inward.inward_id = po_invoice_grid.inward_id
                // INNER JOIN legalentity_warehouses AS lwh ON lwh.le_wh_id = inward.le_wh_id`
                // if (!globalFeature) {
                //     query += ` INNER JOIN user_permssion AS up ON CASE WHEN up.object_id=0 THEN 1 else up.object_id=lwh.bu_id end 
                //                   AND up.user_id = ${userId} AND up.permission_level_id = 6`
                // }

                // query += ` WHERE legal.legal_entity_type_id in ( 1002, 1014, 1016 )  ` // Supplier, DC, FC
                // //query += ` AND lwh.status = 1`;
                // if (userData.length == 0) {
                //     query += ` AND po.le_wh_id IN (` + accessList.dc_acess_list + `)`;
                // }

                /**
                 * 
                 * checking the new procedure
                 */
                let grnFilter = '';
                let grnQuery = '';
                let flag = 0;
                let dc_access_list = accessList.dc_acess_list;

                if (userData.length > 0) {
                    let brands = await role.getAllAccessBrands(user_id);
                    dc_access_list = brands.join();
                    flag = 1;
                }

                if (grnStatus != "" && grnStatus != 'all') {
                    if (grnStatus == 'invoiced') {
                        // query += ` AND po_invoice_grid.inward_id IS NOT NULL`;
                        grnFilter += ` AND po_invoice_grid.inward_id IS NOT NULL`
                    } else if (grnStatus == 'notinvoiced') {
                        // query += ` AND po_invoice_grid.inward_id IS NULL`;
                        grnFilter += ` AND po_invoice_grid.inward_id IS NULL`;
                    } else if (grnStatus == 'approved') {
                        // query += ` AND inward.approval_status = 1`;
                        grnFilter += ` AND inward.approval_status = 1`;
                    } else if (grnStatus == 'notapproved') {
                        // query += ` AND inward.approval_status != 1`;
                        grnFilter += ` AND inward.approval_status != 1`;
                    }
                }

                if (Object.keys(filter).length > 0) {
                    if (filter['createdBy'] != null && filter['createdBy'] != "") {
                        if (filter['createdBy'][1] == 'contains') {
                            // query += `  AND GetUserName(inward.created_by,2) LIKE '%${filter['createdBy'][0]}%' `;
                            grnQuery += `  AND GetUserName(inward.created_by,2) LIKE '%${filter['createdBy'][0]}%' `;
                        } else {
                            // query += `  AND GetUserName(inward.created_by,2) = '${filter['createdBy'][0]}' `;
                            grnQuery += `  AND GetUserName(inward.created_by,2) = '${filter['createdBy'][0]}' `;
                        }
                    }
                    if (filter['dcname'] != null && filter['dcname'] != "") {
                        if (filter['dcname'][1] == 'contains') {
                            // query += `  AND getLeWhName(inward.le_wh_id) LIKE '%${filter['dcname'][0]}%' `;
                            grnQuery += `  AND getLeWhName(inward.le_wh_id) LIKE '%${filter['dcname'][0]}%' `;
                        } else {
                            // query += `  AND getLeWhName(inward.le_wh_id) = '${filter['dcname'][0]}' `;
                            grnQuery += `  AND getLeWhName(inward.le_wh_id) = '${filter['dcname'][0]}' `;
                        }
                    }
                    if (filter['grnDate'] != null && filter['grnDate'] != "") {
                        const dateStart = moment(filter['grnDate'][0]).format(("YYYY-MM-DD 00:00:00"));
                        const dateEnd = moment(filter['grnDate'][0]).format(("YYYY-MM-DD 23:59:59"));
                        if (filter['grnDate'][1] == 'on') {
                            // query += ` AND inward.created_at between '${dateStart}' AND '${dateEnd}'`;
                            grnQuery += ` AND inward.created_at between '${dateStart}' AND '${dateEnd}'`;
                        } else if (filter['grnDate'][1] == 'after') {
                            // query += ` AND inward.created_at > '${dateEnd}' `;
                            grnQuery += ` AND inward.created_at > '${dateEnd}' `;
                        } else if (filter['grnDate'][1] == 'before') {
                            // query += ` AND inward.created_at < '${dateStart}' `;
                            grnQuery += ` AND inward.created_at < '${dateStart}' `;
                        } else if (filter['grnDate'][1] == 'today') {
                            // query += ` AND inward.created_at  between '${dateStart}' AND '${dateEnd}' `;
                            grnQuery += ` AND inward.created_at  between '${dateStart}' AND '${dateEnd}' `;
                        } else if (filter['grnDate'][1] == 'yesterday') {
                            // query += ` AND inward.created_at  between '${dateStart}' AND '${dateEnd}' `;
                            grnQuery += ` AND inward.created_at  between '${dateStart}' AND '${dateEnd}' `;
                        }
                    }
                    if (filter['grnCode'] != null && filter['grnCode'] != "") {
                        if (filter['grnCode'][1] == 'contains') {
                            // query += `  AND inward_code LIKE '%${filter['grnCode'][0]}%' `;
                            grnQuery += `  AND inward_code LIKE '%${filter['grnCode'][0]}%' `;
                        } else {
                            // query += `  AND inward_code = '${filter['grnCode'][0]}' `;
                            grnQuery += `  AND inward_code = '${filter['grnCode'][0]}' `;
                        }
                    }
                    // if (filter['grnvalue'] != null && filter['grnvalue'] != "") {
                    //     if (filter['grnvalue'][1] == 'contains') {
                    //         query += `  AND grn.grnvalue LIKE '%${filter['grnvalue'][0]}%' `
                    //     } else {
                    //         query += `  AND grn.grnvalue = '${filter['grnvalue'][0]}' `
                    //     }
                    // }
                    if (filter['invoice_no'] != null && filter['invoice_no'] != "") {
                        if (filter['invoice_no'][1] == 'contains') {
                            // query += `  AND invoice_no LIKE '%${filter['invoice_no'][0]}%' `;
                            grnQuery += `  AND invoice_no LIKE '%${filter['invoice_no'][0]}%' `;
                        } else {
                            // query += `  AND invoice_no = '${filter['invoice_no'][0]}' `;
                            grnQuery += `  AND invoice_no = '${filter['invoice_no'][0]}' `;
                        }
                    }
                    // if (filter['item_discount_value'] != null && filter['item_discount_value'] != "") {
                    //     if (filter['item_discount_value'][1] == 'contains') {
                    //         query += `  AND grn.item_discount_value LIKE '%${filter['item_discount_value'][0]}%' `
                    //     } else {
                    //         query += `  AND grn.item_discount_value = '${filter['item_discount_value'][0]}' `
                    //     }
                    // }
                    if (filter['legalsuplier'] != null && filter['legalsuplier'] != "") {
                        if (filter['legalsuplier'][1] == 'contains') {
                            // query += `  AND legal.business_legal_name LIKE '%${filter['legalsuplier'][0]}%' `;
                            grnQuery += `  AND legal.business_legal_name LIKE '%${filter['legalsuplier'][0]}%' `;
                        } else {
                            // query += `  AND legal.business_legal_name = '${filter['legalsuplier'][0]}' `;
                            grnQuery += `  AND legal.business_legal_name = '${filter['legalsuplier'][0]}' `;
                        }
                    }
                    if (filter['poCode'] != null && filter['poCode'] != "") {
                        if (filter['poCode'][1] == 'contains') {
                            // query += `  AND po.po_code LIKE '%${filter['poCode'][0]}%' `;
                            grnQuery += `  AND po.po_code LIKE '%${filter['poCode'][0]}%' `;
                        } else {
                            // query += `  AND po.po_code = '${filter['poCode'][0]}' `;
                            grnQuery += `  AND po.po_code = '${filter['poCode'][0]}' `;
                        }
                    }
                    // if (filter['povalue'] != null && filter['povalue'] != "") {
                    //     if (filter['povalue'][1] == 'contains') {
                    //         query += `  AND grn.povalue LIKE '%${filter['povalue'][0]}%' `
                    //     } else {
                    //         query += `  AND grn.povalue = '${filter['povalue'][0]}' `
                    //     }
                    // }
                    if (filter['ref_no'] != null && filter['ref_no'] != "") {
                        if (filter['ref_no'][1] == 'contains') {
                            // query += `  AND inward_ref_no LIKE '%${filter['ref_no'][0]}%' `;
                            grnQuery += `  AND inward_ref_no LIKE '%${filter['ref_no'][0]}%' `;
                        } else {
                            // query += `  AND inward_ref_no = '${filter['ref_no'][0]}' `;
                            grnQuery += `  AND inward_ref_no = '${filter['ref_no'][0]}' `;
                        }
                    }
                }
                if (fromDate != null && toDate != null && fromDate != "" && toDate != "") {
                    // query += ` AND po.po_date between '${fromDate + ' 00:00:00'}' AND '${toDate + ' 23:59:59'}'`;
                    grnQuery += ` AND po.po_date between '${fromDate + ' 00:00:00'}' AND '${toDate + ' 23:59:59'}'`;
                }


                // query += ` GROUP BY inward.inward_id `;
                grnQuery += ` GROUP BY inward.inward_id `;

                // //below queries shall be performed after group by in sql 
                if (filter['grnvalue'] != null && filter['grnvalue'] != "") {
                    if (filter['grnvalue'][1] == '=') {
                        // query += `  having ROUND(grnvalue,2) = '${filter['grnvalue'][0]}' `;
                        grnQuery += `  having ROUND(grnvalue,2) = '${filter['grnvalue'][0]}' `;
                    } else if (filter['grnvalue'][1] == '>') {
                        // query += ` having ROUND(grnvalue,2) > '${filter['grnvalue'][0]}' `;
                        grnQuery += ` having ROUND(grnvalue,2) > '${filter['grnvalue'][0]}' `;
                    } else {
                        // query += ` having ROUND(grnvalue,2) < '${filter['grnvalue'][0]}' `;
                        grnQuery += ` having ROUND(grnvalue,2) < '${filter['grnvalue'][0]}' `;
                    }
                }

                if (filter['item_discount_value'] != null && filter['item_discount_value'] != "") {
                    if (filter['item_discount_value'][1] == '=') {
                        // query += `   having ROUND(grn.item_discount_value,2) = '${filter['item_discount_value'][0]}' `;
                        grnQuery += `   having ROUND(grn.item_discount_value,2) = '${filter['item_discount_value'][0]}' `;
                    } else if (filter['grn_value'][1] == '>') {
                        // query += `  having ROUND(grn.item_discount_value,2) > '${filter['item_discount_value'][0]}' `;
                        grnQuery += `  having ROUND(grn.item_discount_value,2) > '${filter['item_discount_value'][0]}' `;
                    } else {
                        // query += `  having ROUND(grn.item_discount_value,2) < '${filter['item_discount_value'][0]}' `;
                        grnQuery += `  having ROUND(grn.item_discount_value,2) < '${filter['item_discount_value'][0]}' `;
                    }
                }

                if (filter['povalue'] != null && filter['povalue'] != "") {
                    if (filter['povalue'][1] == '=') {
                        // query += `  having ROUND(grn.povalue,2) = '${filter['povalue'][0]}' `;
                        grnQuery += `  having ROUND(grn.povalue,2) = '${filter['povalue'][0]}' `;
                    } else if (filter['povalue'][1] == '>') {
                        // query += `  having ROUND(grn.povalue,2) > '${filter['povalue'][0]}' `;
                        grnQuery += `  having ROUND(grn.povalue,2) > '${filter['povalue'][0]}' `;
                    } else {
                        // query += `  having ROUND(grn.povalue,2) < '${filter['povalue'][0]}' `;
                        grnQuery += `  having ROUND(grn.povalue,2) < '${filter['povalue'][0]}' `;
                    }
                }
                // if (rowCnt == 1) {

                //     query += ` ORDER BY inward_code DESC`
                //     sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                //         resolve(response.length);
                //     })
                // } else {
                // query += ` ORDER BY ${sortField} ${sortType} LIMIT ${perPage} OFFSET ${offset};`;
                grnQuery += ` ORDER BY ${sortField} ${sortType} LIMIT ${perPage} OFFSET ${offset}`;
                // console.log('query', query);
                // sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                //     resolve(response);
                // })
                // }
                let grnListResponse = `CALL getGrnGridData('${dc_access_list}','${flag}','${grnFilter}',"${grnQuery}")`;
                db.query(grnListResponse, {}, async function (err6, response) {
                    if (err6) {
                        reject('error')
                    }
                    // console.log("response",response[0]);
                    if (response.length > 0) resolve(response[0]);
                    else resolve(0);
                })

            })
        } catch (err) {
            reject(err);
        }

    },
    getAccesDetails: async function (user_id) {
        return new Promise((resolve, reject) => {
            let requestBody = { 'permissionLevelId': 6, 'user_id': user_id };
            role.getFilterData(requestBody).then((roles) => {
                //roles = JSON.parse(roles[0]);
                // console.log('roles', roles);
                var data = [];
                var filters = roles[0].sbu;
                var dc_acess_list = filters.hasOwnProperty('118001') ? filters['118001'] : 'NULL';
                var hub_acess_list = filters.hasOwnProperty('118002') ? filters['118002'] : 'NULL';
                data.dc_acess_list = dc_acess_list;
                data.hub_acess_list = hub_acess_list;
                return resolve(data);
            });
        });
    },

    checkUserIsSupplier: async (userId) => {
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
    },

    getSuppliers: async function (req, res) {
        var legalentitytype;
        var params = { 'user_id': req.user_id, 'permissionLevelId': '6' };
        var suppliersMergeArray = [];
        var dcfcList = [];
        return new Promise((resolve, reject) => {

            module.exports.getLegalEntityTypeId(req.legalentity).then(async (data1) => {
                legalentitytype = data1;
                let sbu = await role.getFilterData(params);//.then(async(sbu)=>{
                //if(sbu[0].hasOwnProperty('sbu')){
                var filters = sbu[0].sbu;
                var dc_acess_list = filters.hasOwnProperty('118001') ? filters[118001] : 'NULL';
                let suppliers = await role.suppliersbasedOnLegalEnitityID(req.legalentity);

                var params1 = { 'legalentity': req.legalentity, 'fields': 'legal_entity_id,business_legal_name' };
                let dcfcdata = await role.getDCFCData(params1);
                //console.log(suppliers);
                if (dcfcdata.length > 0) {
                    suppliersMergeArray = [...dcfcdata, ...suppliers];
                } else {
                    suppliersMergeArray = [...suppliers];
                }
                if (legalentitytype == 1001) {
                    var legal_entity_type_id = [1014, 1016];
                    let fc_dc_legal_entities = await module.exports.getDCFCMappingsForDCList(dc_acess_list);
                    fc_dc_legal_entities = fc_dc_legal_entities.hasOwnProperty('dc_le_id') ? fc_dc_legal_entities.dc_le_id : "";
                    if (fc_dc_legal_entities != '') {
                        dcfcList = await module.exports.getDCFCListForLegalEnitytType(fc_dc_legal_entities, legal_entity_type_id);
                        if (dcfcList.length) {
                            suppliersMergeArray = [...suppliersMergeArray, ...dcfcList];
                        }
                    }
                    resolve(suppliersMergeArray);
                } else {
                    resolve(suppliers);
                }
                //}
                //});   
            });
        })
    },
    getLegalEntityTypeId: async function (legalentity) {
        return new Promise((resolve, reject) => {
            var qry = "select legal_entity_type_id from legal_entities where legal_entity_id=" + legalentity + " limit 1";
            db.query(qry, {}, async function (err, rows) {
                if (err) {
                    await reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    var le_type_id = rows[0].hasOwnProperty('legal_entity_type_id') ? rows[0].legal_entity_type_id : '';
                    await resolve(le_type_id);
                }
            });
        });
    },
    getDCFCMappingsForDCList: async function (dclist) {
        return new Promise((resolve, reject) => {
            var dcfcqry = "select GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id from dc_fc_mapping where dc_fc_mapping.dc_le_wh_id in (" + dclist + ") or dc_fc_mapping.fc_le_wh_id in (" + dclist + ") limit 1";
            db.query(dcfcqry, {}, async function (err, result) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(result).length > 0) {
                    //console.log(typeof(result)+'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
                    await resolve(result);
                }
            });
        });
    },

    getDCFCListForLegalEnitytType: async function (dcfcList, legal_entity_type_id) {
        return new Promise((resolve, reject) => {
            var dcfclist = "select legal_entity_id,business_legal_name from legal_entities where legal_entities.legal_entity_id in (" + fc_dc_legal_entities + ") and legal_entities.legal_entity_type_id in (" + legal_entity_type_id + ")";
            db.query(dcfclist, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    //console.log(typeof(rows)+'aaaaaaaaaaaaaaaaaaaaaaaaaaaa');
                    resolve(rows);
                }
            })
        })
    },
    poList: async function (params) {
        return new Promise((resolve, reject) => {
            var legal_entity_id = params.legalentity;
            var user_id = params.user_id;
            var legalentitytype;
            var suppliersMergeArray;
            var globalsupplier;
            module.exports.getLegalEntityTypeId(params.legalentity).then(async (data1) => {
                legalentitytype = data1;
                var filterdata = { "permissionLevelId": '6', "user_id": user_id };
                let sbu = await role.getFilterData(filterdata);
                var filters = sbu[0].sbu;
                var dc_acess_list = filters.hasOwnProperty('118001') ? filters[118001] : 'NULL';
                let suppliers = await role.suppliersbasedOnLegalEnitityID(params.legalentity);
                var params1 = { 'legalentity': params.legalentity, 'fields': 'legal_entity_id,business_legal_name' };
                let dcfcdata = await role.getDCFCData(params1);
                if (dcfcdata.length > 0) {
                    //console.log('suppliers '+suppliers);
                    dcfcdata = dcfcdata.map(function (value, index) { return value['legal_entity_id'] });
                    //console.log('dcfcdata '+dcfcdata);
                    suppliers = suppliers.map(function (value, index) { return value['legal_entity_id'] });
                    suppliersMergeArray = [...dcfcdata, ...suppliers];
                    if (suppliersMergeArray.length == 0) {
                        suppliersMergeArray = dcfcdata;
                    }
                    //resolve(suppliersMergeArray);
                } else {
                    suppliersMergeArray = suppliers;
                }
                var fields1 = "po.po_id,po.po_code";
                var query = "select " + fields1 + " from po";
                if (legalentitytype == 1001) {
                    query += " where ";
                } else {
                    dc_acess_list = dc_acess_list.split(',');
                    query += " where po.le_wh_id in (" + dc_acess_list + ") and";
                }
                globalsupplier = await role.masterLookUpDescriptionByvalue(78023);
                var globalSupperLierId = globalsupplier.hasOwnProperty('description') ? globalsupplier.description : 'NULL';
                suppliersMergeArray.push(globalSupperLierId);
                suppliersMergeArray.push(2);
                query += " po.is_closed=0 and po.approval_status in (57107,57119,57120,1) and po.po_status in (87001, 87005) order by po.po_id desc";
                db.query(query, {}, async function (err, rows) {
                    if (err) {
                        reject('error');
                    }
                    if (rows.length > 0) {
                        resolve(rows);
                    }
                });
            });
        })
    },
    getPOQtyById: async function (poid) {
        return new Promise((resolve, reject) => {
            var fields2 = "SUM(poprd.qty*no_of_eaches) AS totpo_qty";
            var poqtyqry = "select " + fields2 + " from po join po_products as poprd on poprd.po_id=po.po_id where po.po_id=" + poid;
            db.query(poqtyqry, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (rows.length > 0) {
                    resolve(rows);
                } else {
                    resolve({});
                }
            })
        })
    },
    getGrnQtyByPOId: async function (poid) {
        return new Promise((resolve, reject) => {
            var fields3 = "SUM(received_qty) AS tot_received";
            var grnqtyqry = "select " + fields3 + " from inward join inward_products as inwrdprd on inwrdprd.inward_id=inward.inward_id where inward.po_no=" + poid;
            db.query(grnqtyqry, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (rows.length > 0) {
                    resolve(rows);
                } else {
                    resolve({});
                }
            });
        });
    },
    getPOSupplierProductList: async function (params) {
        return new Promise(async (resolve, reject) => {
            var legal_entity_id = params.legal_entity_id;
            var user_id = params.userid;
            var poid = params.poid;
            var legal_entity_type_id = [1002, 1014, 1016];
            var response = '';

            var supplierlist = await module.exports.suppliersListByPOID(poid, legal_entity_type_id, legal_entity_id);
            var warehouselist = await module.exports.warehouseListByPO(poid, legal_entity_id);
            response = { "supplierList": supplierlist, "warehouselist": warehouselist };
            resolve(response);
        });
    },
    suppliersListByPOID: async function (poid, legal_entity_type_id, legal_entity_id) {
        return new Promise((resolve, reject) => {
            var supplierslist = "select legal_entities.legal_entity_id,legal_entities.business_legal_name from legal_entities";
            if (poid > 0) {
                supplierslist += " join po on po.legal_entity_id=legal_entities.legal_entity_id where po.po_id=" + poid + " and legal_entities.legal_entity_type_id in (" + legal_entity_type_id + ")";
            } else {
                supplierslist += " legal_entities.legal_entity_type_id=1002 and parent_id=" + legal_entity_id;
            }
            supplierslist += "and legal_entities.is_approved=1";
            db.query(supplierslist, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (rows.length > 0) {
                    resolve(rows[0]);
                } else {
                    resolve({});
                }
            });

        });
    },
    warehouseListByPO: async function (poid, legal_entity_id) {
        return new Promise((resolve, reject) => {
            var warehouselistbypoid = "select legalentity_warehouses.lp_wh_name,legalentity_warehouses.le_wh_id from legalentity_warehouses";
            if (poid > 0) {
                warehouselistbypoid += " join po on po.le_wh_id=legalentity_warehouses.le_wh_id where po.po_id=" + poid;
            } else {
                warehouselistbypoid += " legalentity_warehouses.legal_entity_id=" + legal_entity_id;
            }
            db.query(warehouselistbypoid, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (rows.length > 0) {
                    resolve(rows[0]);
                } else {
                    resolve({});
                }
            });
        });
    },
    getPODiscountDetails: async function (params) {
        return new Promise((resolve, reject) => {
            var podiscountqry = "select apply_discount_on_bill,discount_type,discount,discount_before_tax from po where po_id=" + params.poid + " and apply_discount_on_bill=1 limit 1";
            db.query(podiscountqry, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (rows.length > 0) {
                    resolve(rows[0]);
                } else {
                    resolve({});
                }
            });
        });
    },
    getPOGRNProductList: async function (params) {
        return new Promise((resolve, reject) => {
            var legal_entity_id = params.legal_entity_id;
            var user_id = params.userid;
            var poid = params.poid;
            var legal_entity_type_id = [1002, 1014, 1016];
            var response = [];
            if (poid > 0) {
                var productsqry = "select poprod.product_id,po.approval_status,products.product_title as product_name,poprod.qty,poprod.is_tax_included,poprod.no_of_eaches,IFNULL(sum(inward_products.received_qty),0) as received_qty,poprod.tax_per,poprod.tax_name,poprod.uom,products.sku,products.seller_sku,products.mrp,products.kvi,products.upc,poprod.unit_price,brands.brand_id,brands.brand_name,inventory.mbq,inventory.soh,(poprod.no_of_eaches * poprod.qty) AS actual_po_quantity,inventory.atp,inventory.order_qty,products.pack_size,tot.dlp,IFNULL(tot.base_price,0) AS base_price,currency.symbol_right as symbol,(CASE WHEN poprod.parent_id=0 THEN poprod.product_id ELSE poprod.parent_id END) AS product_parent_id,IFNULL(poprod.apply_discount,0) as apply_discount,IFNULL(poprod.discount_type,0) as discount_type,IFNULL(poprod.discount,0) as discount,(CASE WHEN inward_products.received_qty IS NOT NULL THEN (poprod.no_of_eaches * poprod.qty) - inward_products.received_qty ELSE (poprod.no_of_eaches * poprod.qty) END) AS po_quantity from po_products as poprod left join products on products.product_id=poprod.product_id left join po on po.po_id=poprod.po_id left join inward on inward.po_no=po.po_id left join inward_products on inward_products.inward_id=inward.inward_id and poprod.product_id=inward_products.product_id left join brands on products.brand_id=brands.brand_id left join product_tot as tot on products.product_id=tot.product_id and tot.supplier_id=po.legal_entity_id and tot.le_wh_id=po.le_wh_id left join inventory on products.product_id=inventory.product_id and po.le_wh_id=inventory.le_wh_id left join currency on tot.currency_id=currency.currency_id where poprod.po_id=" + poid + " group by poprod.product_id having po_quantity>0 order by product_parent_id asc";
                db.query(productsqry, {}, async function (err, rows) {
                    if (err) {
                        reject('error');
                    }
                    if (Object.keys(rows).length > 0) {
                        qtycheck = rows;
                        var productslist = await Promise.all(qtycheck.map((value, key) => module.exports.loopingthroughProducts(value, key, poid)));

                        Promise.all(qtycheck.map((value, key) => module.exports.getProductPackUOMInfoForCreateGrn(value.product_id, value.uom,key))).then(data => {
                            Promise.all(qtycheck.map((value,key)=>module.exports.getProductPackInfo(value.product_id,key))).then(data=>{
                                Promise.all(qtycheck.map((value, key) => module.exports.getProductTaxClass(value.product_id, key, 4033, 4033))).then(data => {
                                    Promise.all(qtycheck.map((value, key) => module.exports.getProductShelfLife(value.product_id,key))).then(data => {
                                        Promise.all(qtycheck.map((value, key) => module.exports.getMfgDate(value.product_id,key,poid))).then(data => {
                                            resolve(qtycheck);
                                        });
                                    });
                                });
                            });
                        });


                    } else {
                        resolve('');
                    }
                });
            }
        });
    },
    loopingthroughProducts: function (value, key, poid) {
        return new Promise(async (resolve, reject) => {
            await module.exports.getGrnQtyByPOProductId(value.product_id, key, poid).then(async (productslist) => {
                qtycheck[key].po_remaining_qty = value.actual_po_quantity-value.received_qty;
                if(value.is_tax_included==1){
                    qtycheck[key].unit_price = (value.unit_price / (1 + (value.tax_per / 100)));  
                    qtycheck[key].unitDiscountPrice = Math.round(value.discount/(value.actual_po_quantity-value.received_qty)).toFixed(5); 
                }
                resolve(productslist);
            });

        });
    },
    getGrnQtyByPOProductId: function (product_id, key, poid) {
        return new Promise(async (resolve, reject) => {
            var qry = "select orderd_qty,(poprd.qty*poprd.no_of_eaches) AS po_qty,SUM(inwrdprd.received_qty) AS tot_received,SUM(inwrdprd.free_qty) AS tot_free_received from inward join inward_products as inwrdprd on inwrdprd.inward_id=inward.inward_id LEFT JOIN po_products as poprd ON poprd.po_id=inward.po_no and poprd.product_id=inwrdprd.product_id where inward.po_no=" + poid + " and inwrdprd.product_id=" + product_id + " limit 1";
            db.query(qry, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    //resolve(rows);
                    if ((rows.tot_received >= rows.po_qty) && rows.tot_received != '') {
                        delete qtycheck.key;
                    } else {
                        if (rows.hasOwnProperty('tot_received') && rows.tot_received != '' && rows.orderd_qty != '' && rows.orderd_qty > rows.tot_received) {
                            qtycheck[key].qty = rows.orderd_qty - rows.tot_received;
                        }
                    }
                    resolve(qtycheck);
                } else {
                    resolve({});
                }
            })
        });
    },

    getProductTaxClass :function (product_id,index, wh_state_code, seller_state_code) {
        try {
            return new Promise((resolve, reject) => {
                let rp = require('request-promise');
                let url = process.env.APP_TAXAPI;
                let options = {
                    method: 'POST',
                    uri: url,
                    body: {
                        'product_id': product_id,
                        'buyer_state_id': wh_state_code,
                        'seller_state_id': seller_state_code
                    },
                    json: true
                };
                rp(options).then(function (parsedBody) {
                    let taxdata = parsedBody;
                    if (taxdata.Status == 200) {
                        qtycheck[index].taxinfo = taxdata.ResponseBody;
                    } else {
                        qtycheck[index].taxinfo = "No data from Api";
                    }
                    resolve(qtycheck);
                })
            })
        } catch (err) {
            console.log('err', err);
        }
    },

    /* 
    author : Muzzamil,
    previous-author : Nishant,
     */
    getProductPackUOMInfo: function (product_id, pack_size) {
        return new Promise(async (resolve, reject) => {
            try {
                // console.log(product_id, 'product_idproduct_idproduct_id');
                let uomqry = "select lookup.value,lookup.master_lookup_name as uomName,pack.no_of_eaches from product_pack_config as pack left join master_lookup as lookup on pack.level=lookup.value where pack.product_id=" + product_id + " and pack.level=" + pack_size + " limit 1";
                sequelize.query(uomqry, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    if (response.length > 0) {
                        resolve(response);
                    }
                })
            } catch (err) {
                console.log(err);
                reject(err);
            }
        });
    },

    checkGRNCreated: async function (poid, checkgrnproducts) {
        return new Promise((resolve, reject) => {
            let productId;
            let grn_received;
            let check = true;
            if (Array.isArray(checkgrnproducts)) {

                let po_qty;
                let tot_received;
                checkgrnproducts.forEach(async (product) => {
                    productId = product.product_id;
                    grn_received = product.received_qty;
                    let podata = await this.getPOQtyByProductId(poid, productId);
                    let grndata = await this.getGRNQtyByProductId(poid, productId);
                    let po_qty = podata[0].po_qty ? podata[0].po_qty : 0;
                    let tot_received = grndata[0].tot_received ? grndata[0].tot_received : 0;
                    let remaining_qty = ( po_qty - tot_received);
                    if ( grn_received > remaining_qty) {
                        check = false;
                    }
                })
            } else {
                check = false;
            }
            resolve(check);
        })
    },

    getPOQtyByProductId: async function (poid, productId) {
        return new Promise((resolve, reject) => { 
                let query = `SELECT po_id,(poprd.qty*poprd.no_of_eaches) AS po_qty FROM po_products AS poprd
                WHERE po_id = ${poid} AND product_id = ${productId} LIMIT 1;`
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => { 
                    resolve(response);
                })
        })

    },
     getGRNQtyByProductId: async function (poid, productId) {
        return new Promise((resolve, reject) => { 
                let query = `SELECT orderd_qty,SUM(inwrdprd.received_qty) AS tot_received FROM inward JOIN inward_products AS inwrdprd ON
                inwrdprd.inward_id = inward.inward_id WHERE inward.po_no = ${poid} AND inwrdprd.product_id = ${productId} LIMIT 1 `;
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => { 
                    resolve(response);
                })
        })

    },

    checkPOType : async function(poid) {
        return new Promise((resolve, reject) => {
                // console.log("started successfully",poid)
                let query = `SELECT po_so_order_code FROM po WHERE po_id = ${poid} AND po_so_status = 1;`
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => { 
                    if(response.length > 0 && typeof response[0] != 'undefined') {
                       resolve(response[0]);
                   }else{
                    resolve(0);
                   }
                })
        })
    },
    
    getPOInfo : async function(poid) {
        return new Promise((resolve, reject) => {
                let query = `SELECT * FROM po WHERE po_id = ${poid} limit 1;`
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then( response => {
                    resolve(response[0]);
                })
        })
    },

    checkPOSOInvoiceStatus : async function(gds_order_id) {
        return new Promise((resolve, reject) => {
                let query = `SELECT gds_order_id FROM gds_invoice_grid WHERE gds_order_id = '${gds_order_id}';`
                sequelize.query(query, {type: Sequelize.QueryTypes.SELECT}).then(response => {
                    let result =[];
                    if(response.length > 0){
                      result = response[0].gds_order_id;
                    console.log(result, 'result')
                    } else {
                        result = [];
                    }
                   resolve(result);
                })
                // resolve(checkPOInvoice);
        })
    },

    getPoApprovalStatusByPoId: (poid) => {
        return new Promise((resolve, reject) => {
            try {
                let query = `SELECT approval_status FROM po WHERE po_id = ${poid};`

                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    let result = response.map(xx => xx.approval_status);
                    // resolve(result);
                    if (result.length > 0 && typeof result[0] != 'undefined') {
                        let approvalStatus = result[0];
                        resolve(approvalStatus);
                    } else {
                        reject();
                    }
                })

            } catch (err) {

            }
        })
    },

    /*
    author : Muzzamil
     */

    getSkus: (supplier_id, le_wh_id, term) => {
        return new Promise((resolve, reject) => {
            try {
                let query = `SELECT p.product_id,p.product_title,p.upc,p.sku,p.pack_size,p.seller_sku,p.mrp,brands.brand_id,
                brands.brand_name  FROM products AS p  LEFT JOIN product_tot AS tot ON p.product_id = tot.product_id 
                LEFT JOIN brands ON p.brand_id = brands.brand_id 
                LEFT JOIN product_content AS content ON p.product_id = content.product_id
                 WHERE tot.supplier_id = '${supplier_id}' AND tot.le_wh_id= '${le_wh_id}' AND
                ( p.sku LIKE '%${term}%'  OR p.product_title LIKE '%${term}%' OR p.upc LIKE '%${term}%')`;
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    let prodAry = [];
                    if (response.length > 0) {
                        response.forEach((product) => {
                            let arr = {
                                'label': product.product_title,
                                'product_id': product.product_id,
                                'product_title': product.product_title,
                                'brand': product.brand_name,
                                'upc': product.upc,
                                'mrp': `Rs. ${(product.mrp != '') ? product.mrp : 0}`,
                            }
                            prodAry.push(arr);
                        })
                        //    console.log(prodAry);
                    } else {
                        resolve("No data found");
                    }
                    // resolve(response[0]);
                    resolve(prodAry);
                })
            } catch (err) {
                console.log(err);
                reject(err);
            }
        })
    },

    /*
   author : Muzzamil
    */
    getProductPackStatus: () => {
        return new Promise((resolve, reject) => {
            //try {
            let query = `SELECT lookup.value, lookup.master_lookup_name 
                        FROM master_lookup AS lookup WHERE lookup.mas_cat_id = 91 `;
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                if (response.length > 0) {
                    resolve(response);
                } else {
                    reject("no response from getProductPackStatus ")
                }
            })
            // } catch {
            //     reject("Query failed for getProductPackStatus Promise");
            // }
        }
        )
    },

    /*
   author : Muzzamil
    */

    // getProductShelfLife: async (product_id) => {
    //     return new Promise((resolve, reject) => {console.log(product_id)
    //         //try {
    //             let query = `SELECT products.shelf_life,products.shelf_life_uom,
    //             lookup.master_lookup_name FROM products LEFT JOIN master_lookup AS lookup ON lookup.value = products.shelf_life_uom 
    //             WHERE products.product_id = ` + product_id + ' LIMIT 1';
    //             sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(async (response) => {console.log(query);
    //                 if (response.length > 0) {console.log(response);
    //                     resolve(response);
    //                 } else {
    //                     console.log("no data");
    //                     resolve("no data");
    //                 }
    //             }).catch(err => {
    //                 console.log(err);
    //             })
    //         // } catch {
    //         //     console.log(" getProductShelfLife query failed");
    //         //     reject(" getProductShelfLife query failed ");
    //         // }
    //     })
    // },

    getDeliveryGtin: async function (inwardId) {
        return new Promise((resolve, reject) => {
            let query = "select `le`.`gstin` from `legal_entities` left join `inward` on `inward`.`legal_entity_id` = `legal_entities`.`legal_entity_id` left join `legal_entities` as `le` on `le`.`legal_entity_id` = `legal_entities`.`parent_id` where `inward`.`inward_id` =" + inwardId + " limit 1";
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
    },

    getBillingAddress: async function (whId) {
        return new Promise((resolve, reject) => {
            let query = "select lwh.lp_wh_name as business_legal_name, `lwh`.`address1`, `lwh`.`address2`, countries.name as country_name, getStateNameById(lwh.state) AS state, getStateCodeById(lwh.state) AS state_code, lwh.tin_number as gstin, `lwh`.`legal_entity_id` from `legalentity_warehouses` as `lwh` left join `countries` on `countries`.`country_id` = `lwh`.`country` where `lwh`.`le_wh_id` =" + whId + " limit 1";
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
    },

    isDocExist: async (ref_no, documentType) => {
        try {
            let query = `SELECT * 
            FROM   inward_docs i 
                   JOIN inward id 
                     ON i.inward_id = id.inward_id 
            WHERE  i.doc_ref_no = ${ref_no} 
                   AND i.doc_ref_type = ${documentType}; `

            let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
            if (response.length > 0) return response.length;
            else return 0;
        } catch (err) {
            return `isDocExist error : ${err}`;
        }
    },

    saveDocument: async (docsObj) => {
        try {
            let query = `INSERT INTO inward_docs (doc_ref_no,po_id,doc_ref_type,allow_duplicate,doc_url,created_by,created_at) 
            VALUES ('${docsObj.doc_ref_no}','${docsObj.po_id}','${docsObj.doc_ref_type}','${docsObj.allow_duplicate}','${docsObj.doc_url}','${docsObj.created_by}','${docsObj.created_at}');`
            let response = await new Promise((resolve, reject) => db.query(query, {}, (err, rows) => {
                // console.log('rows', rows);
                if (err) {
                    reject(err);
                }
                if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve(0);
                }
            }));
            return response.insertId
        } catch (err) {
            return `saveDocument error : ${err}`;
        }
    },

    deleteDocument: async (id) => {
        try {
            let query = `DELETE FROM inward_docs WHERE inward_doc_id = ${id};`

            let response = await new Promise((resolve, reject) => db.query(query, {}, (err, rows) => {
                if (err) {
                    reject(err);
                }
                if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve(0);
                }
            }))

            return response.affectedRows;

        } catch (err) {

        }
    },

    saveReferenceNo: async (id, refNo) => {
        try {
            let query = `UPDATE inward_docs SET doc_ref_no = ${refNo} WHERE inward_doc_id = ${id};`
            let response = await new Promise((resolve, reject) => db.query(query, {}, (err, rows) => {
                if (err) {
                    reject(err);
                }


                if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve(0);
                }
            }))

            return response.affectedRows;
        } catch (err) {

        }
    },

    getDocumentTypes: async () => {
        try {
            let query = `SELECT value, 
            master_lookup_name 
            FROM   master_lookup 
            WHERE  mas_cat_id = 95; `
            let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
            if (response.length > 0) return response;
            else return 0;

        } catch (err) {
            return `getDocumentTypes error : ${err}`;
        }
    },

    userInfo: async (userId) => {
        try {
            let query = `SELECT firstname, lastname FROM users WHERE user_id = ${userId};`
            let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
            if (response.length > 0) return response[0];
            else return 0;
        } catch (err) {
            return `userInfo error : ${err}`;
        }
    },
    getProductInfo: async (productId) => {
        return new Promise((resolve, reject) => {
            let productqry = "select * from products where product_id=" + productId + " limit 1";
            sequelize.query(productqry, { type: Sequelize.QueryTypes.SELECT }).then(async (response) => {
                if (response.length > 0) {
                    resolve(response[0]);
                } else {
                    console.log("no data");
                    resolve([]);
                }
            }).catch(err => {
                console.log(err);
                resolve([]);
            })
        })
    },

    getPackPrice : async(poProductQty,packSizeArr)=>{
        return new Promise((resolve,reject)=>{
           var response=0;
            if(packSizeArr.hasOwnProperty(poProductQty)){
              //  console.log('ifffffffffffffffffffffffffffffff');
                response=packSizeArr[poProductQty];
            }else{
                //console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
                packSizeArr = (Object.keys(packSizeArr)).sort();
                for (const [packSize, packPrice] of packSizeArr) {
                    if (poProductQty > packSize) {
                        console.log(packSize);
                        response=packSizeArr[packSize];
                        break; 
                    }
                }
            }
            resolve(response);
        })
    },

    grnSave: async (grnArr) => {
        return new Promise((resolve, reject) => {
            var grnColumns = '';
            var grnSaveInfo = ''
            for (var key in grnArr) {
                grnColumns += key + ",";
                grnSaveInfo += "'" + grnArr[key] + "',";
            }
            var grnColumnslastChar = grnColumns.slice(-1);
            if (grnColumnslastChar == ',') {
                grnColumns = grnColumns.slice(0, -1);
            }
            var grnSaveInfolastChar = grnSaveInfo.slice(-1);
            if (grnSaveInfolastChar == ',') {
                grnSaveInfo = grnSaveInfo.slice(0, -1);
            }
            var grnInsertQry= "insert into inward ("+grnColumns+") values ("+grnSaveInfo+")";
            db.query(grnInsertQry, {}, function (err, rows) {
                if (err) {
                        reject(err)
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows.insertId);
                } else {
                    return resolve([]);
                }
            });
        })
    },

    checkStockInward: async (inward_id, productId) => {
        try {
            return new Promise((resolve, reject) => {
                let checkstockbyinwardid = "select stock_inward_id from stock_inward where reference_no =" + inward_id + " and product_id=" + productId + " limit 1";
                sequelize.query(checkstockbyinwardid, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0].stock_inward_id);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            })
        } catch (err) {
            console.log(err)
        }
    },

    getProductByOrderId :async(gds_order_id,productId)=>{
       // try{
            return new Promise((resolve,reject)=> {
                let checkstockbyinwardid = "select product.*,GROUP_CONCAT(DISTINCT(`master_lookup`.`master_lookup_name`) ) as starname,GROUP_CONCAT(DISTINCT(master_lookup.description) ) as starcolor,currency.code,(product.price / product.qty) as unitPrice,orders.le_wh_id,orders.order_code,orders.order_status_id,orders.shop_name,currency.symbol_left as symbol,(CASE WHEN ISNULL(`product`.`parent_id`) THEN `product`.`product_id` ELSE `product`.`parent_id` ) AS `parent_id`,getInvoicePrdQty (product.gds_order_id,product.product_id)  AS invoiced_qty from gds_order_products as product join gds_orders as orders on orders.gds_order_id=product.gds_order_id left join gds_order_product_pack as gop on product.product_id=gop.product_id and orders.gds_order_id=gop.gds_order_id left join master_lookup on master_lookup.value=gop.star join currency on orders.currency_id=currency.currency_id";

                if(productId.length>0){
                    checkstockbyinwardid += " where product.product_id in ("+productId+")"                    
                }

                if(Array.isArray(gds_order_id) && gds_order_id.length>0){
                    checkstockbyinwardid += " and product.gds_order_id in ("+gds_order_id+") group by orders.gds_order_id";
                }else{
                    checkstockbyinwardid += " and product.gds_order_id in ("+gds_order_id+")";
                }

                checkstockbyinwardid += " group by product.product_id order by product.pname asc,parent_id asc";

                sequelize.query(checkstockbyinwardid, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    console.log('11111111',checkstockbyinwardid)
                     if (result != '' && result.length > 0) {
                          resolve(result[0]);
                     } else {
                          resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })
            })
        // }catch (err) {
        //       console.log(err)
        // }
    },

    getTaxPercentageOnGdsProductId: async(gds_order_prod_id)=>{
        try{
            return new Promise((resolve,reject)=> {
                let taxbygdsproductqry = "select sum(tax) as tax_percentage , tax_class,tax.CGST,tax.IGST,tax.SGST,tax.UTGST from gds_orders_tax as tax where tax.gds_order_prod_id ="+gds_order_prod_id+" limit 1";
                //sequelize.query(checkfreebee).then(result => {
                sequelize.query(taxbygdsproductqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    console.log(result,'gettaxresultresultresultresult');
                     if (result != '' && result.length > 0) {
                          resolve(result[0]);
                     } else {
                          resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })
            })
        }catch (err) {
              console.log(err)
        }
    },

    saveGrnProducts: async (grnProducts) => {
        return new Promise((resolve, reject) => {
            var grnPrdColumns = '';
            var grnPrdSaveInfo = '';
            for (const key of grnProducts) {
                grnPrdColumns = '';
                grnPrdSaveInfo = '';
                for (const [columnkey, grnPrdArr] of Object.entries(key)) {
                    grnPrdColumns += columnkey + ",";
                    grnPrdSaveInfo += "'" + grnPrdArr + "',";
                }

                var grnPrdColumnslastChar = grnPrdColumns.slice(-1);
                if (grnPrdColumnslastChar == ',') {
                    grnPrdColumns = grnPrdColumns.slice(0, -1);
                }
                
                    var grnPrdSaveInfolastChar = grnPrdSaveInfo.slice(-1);
                    if (grnPrdSaveInfolastChar == ',') {
                        grnPrdSaveInfo = grnPrdSaveInfo.slice(0, -1);
                    }                    
                    var insertqry = "insert into inward_products("+grnPrdColumns+") values ("+grnPrdSaveInfo+")";
                    db.query(insertqry, {}, function (err, rows) {
                        if (err) {
                                reject(err)
                        }
                        if (Object.keys(rows).length > 0) {
                            return resolve(rows.insertId);
                        } else {
                            return resolve(0);
                        }
                    });
                    //resolve(1);
            }
        })
    },

    saveInputTax: async (inputTax) => {
        return new Promise((resolve, reject) => {
            var grnTaxColumns = '';
            var grnTaxSaveInfo = '';
            for (const key of inputTax) {
                grnTaxColumns = '';
                grnTaxSaveInfo = '';
                for (const [columnkey, grnTaxArr] of Object.entries(key)) {
                    grnTaxColumns += columnkey + ",";
                    grnTaxSaveInfo += "'" + grnTaxArr + "',";
                }

                var grnTaxPrdColumnslastChar = grnTaxColumns.slice(-1);
                if (grnTaxPrdColumnslastChar == ',') {
                    grnTaxColumns = grnTaxColumns.slice(0, -1);
                }
                var grnTaxPrdSaveInfolastChar = grnTaxSaveInfo.slice(-1);
                if (grnTaxPrdSaveInfolastChar == ',') {
                    grnTaxSaveInfo = grnTaxSaveInfo.slice(0, -1);
                }
                var insertqry = "insert into input_tax("+grnTaxColumns+") values ("+grnTaxSaveInfo+")";
               db.query(insertqry, {}, function (err, rows) {
                    if (err) {
                            reject(err)
                    }
                    if (Object.keys(rows).length > 0) {
                        return resolve(rows.insertId);
                    } else {
                        return resolve(0);
                    }
                });
               //resolve(1);
            }
        })
    },

    saveGrnProductDetails: async (grnProductsDetails) => {
        return new Promise((resolve, reject) => {
            var grnPrdDtlsColumns = '';
            var grnPrdDtlsSaveInfo = '';
            for (const key of grnProductsDetails) {
                grnPrdDtlsColumns = '';
                grnPrdDtlsSaveInfo='';
                for(const [columnkey,grnPrdDtlsArr] of Object.entries(key)){
                    if(columnkey=='inward_prd_id'){
                        grnPrdDtlsColumns  += columnkey + ",";
                        grnPrdDtlsSaveInfo+=grnPrdDtlsArr +',';
                    }
                    if(columnkey!='inward_prd_id'){
                        grnPrdDtlsColumns  += columnkey + ",";
                        grnPrdDtlsSaveInfo += "'"+grnPrdDtlsArr + "',";
                    }
                }
                //}
                var grnPrdDtlsColumnslastChar = grnPrdDtlsColumns.slice(-1);
                if (grnPrdDtlsColumnslastChar == ',') {
                    grnPrdDtlsColumns = grnPrdDtlsColumns.slice(0, -1);
                }
                var grnPrdDtlsSaveInfolastChar = grnPrdDtlsSaveInfo.slice(-1);
                if (grnPrdDtlsSaveInfolastChar == ',') {
                    grnPrdDtlsSaveInfo = grnPrdDtlsSaveInfo.slice(0, -1);
                }
                var insertqry = "insert into inward_product_details("+grnPrdDtlsColumns+") values ("+grnPrdDtlsSaveInfo+")";
                db.query(insertqry, {}, function (err, rows) {
                    if (err) {
                            reject(err)
                    }
                    if (rows > 0) {
                        return resolve(rows.insertId);
                    } else {
                        return resolve(0);
                    }
                });
                //resolve(1);
            }
        })
    },

    updateTaxValues : async(inward_id)=>{
        try{
            //inward_id=15118;
            return new Promise((resolve,reject)=> {
                if(inward_id>0){
                    var taxInfofinal=[];
                    let getInwardPrdList = "select inward_products.inward_prd_id,po_products.hsn_code,po_products.tax_data,inward_products.tax_amount from inward_products left join inward on inward.inward_id=inward_products.inward_id left join po_products on po_products.product_id=inward_products.product_id and inward.po_no=po_products.po_id where inward_products.inward_id="+inward_id+" group by inward_products.product_id";
                    sequelize.query(getInwardPrdList, { type: Sequelize.QueryTypes.SELECT }).then(async result => {
                         if (result != '' && result.length > 0) {
                              for( const inwardPrdlst of result){
                                var inputData=[];
                                var taxInfo = [];
                                var taxDetails = [];
                                var inward_prd_id = inwardPrdlst.hasOwnProperty('inward_prd_id') ? inwardPrdlst.inward_prd_id : 0;
                                var hsnCode = inwardPrdlst.hasOwnProperty('hsn_code') ? inwardPrdlst.hsn_code : '';
                                var taxData = inwardPrdlst.hasOwnProperty('tax_data') ? inwardPrdlst.tax_data : '';
                                var taxAmount = inwardPrdlst.hasOwnProperty('tax_amount') ? inwardPrdlst.tax_amount : 0.00;
                                inputData['hsn_code'] = hsnCode;
                                taxDetails = taxData;
                                if(taxDetails && taxDetails!={}){
                                    taxInfo = taxDetails[0];
                                    taxInfo = taxDetails;
                                    var CGST    = (taxInfo.CGST)?taxInfo.CGST: 0
                                    var IGST    = (taxInfo.IGST)?taxInfo.IGST: 0
                                    var SGST    = (taxInfo.SGST)?taxInfo.SGST: 0
                                    var UTGST    = (taxInfo.UTGST)?taxInfo.UTGST: 0
                                
                                    CGST = (CGST/100) * taxAmount;
                                    IGST = (IGST/100) * taxAmount;
                                    SGST = (SGST/100) * taxAmount;
                                    UTGST = (UTGST/100) * taxAmount;
                                    
                                    taxInfo['CGST_VALUE'] = CGST;
                                    taxInfo['IGST_VALUE'] = IGST;
                                    taxInfo['SGST_VALUE'] = SGST;
                                    taxInfo['UTGST_VALUE'] = UTGST;
                                }
                                taxInfofinal.push(taxInfo);
                                //inputData['tax_data'] =
                                await module.exports.updateInwardPrds(taxInfofinal,inward_prd_id); 
                              }
                              resolve(1);
                         } else {
                              resolve(0);
                         }
                    }).catch(err => {
                        console.log(err);
                        reject(err);
                    })
                }
            })
        } catch (err) {
            console.log(err)
        }
    },
    updateElpData :async(inward_id,userid)=>{
        return new Promise((resolve,reject)=>{
            try{
                var elpDataArr=[];
                if(inward_id){
                    var elpDetailsqry = "select po.po_id,inward_products.product_id,po.le_wh_id,po.legal_entity_id as supplier_id,inward_products.cur_elp AS elp,CURDATE() AS effective_date from inward_products left join inward on inward.inward_id=inward_products.inward_id left join po on po.po_id=inward.po_no where inward.inward_id="+inward_id+" having elp > 0";
                    sequelize.query(elpDetailsqry, { type: Sequelize.QueryTypes.SELECT }).then(async (elpDetails) => {
                         if (elpDetails != '' && elpDetails.length > 0) {
                              var childPOexist = await purchaseOrder.checkChildPoExist(elpDetails[0].po_id);
                              var wh_data = await purchaseOrder.getLEWHById(elpDetails[0].le_wh_id);
                              var wh_legal_entity_id = wh_data.legal_entity_id;
                              var wh_state_id = wh_data.state_id;
                              var checkDCFC = await module.exports.getLegalEntityTypeId(wh_legal_entity_id);
                              var dc_le_wh_id = 0;

                              if(checkDCFC == 1014){
                                var params = { 'legalentity': wh_legal_entity_id, 'fields': 'dc_le_wh_id' };
                                dc_le_wh_id = await role.getDCFCData(params);
                                dc_le_wh_id = dc_le_wh_id[0].hasOwnProperty('dc_le_wh_id') ? dc_le_wh_id[0].dc_le_wh_id : 0;
                            } else if (checkDCFC == 1016) {
                                var supplier_id = elpDetails[0].supplier_id;
                                var check_supplier = await purchaseOrder.getWHByLEId(supplier_id);

                                if (check_supplier.length > 0) {
                                    dc_le_wh_id = check_supplier[0].le_wh_id
                                } else {
                                    dc_le_wh_id = await purchaseOrder.getApobData(wh_legal_entity_id);
                                    dc_le_wh_id = dc_le_wh_id[0].hasOwnProperty('dc_le_wh_id') ? dc_le_wh_id[0].dc_le_wh_id : 0;
                                }
                              }
                              for(const elpData of elpDetails){
//console.log(elpData,'beforebeforebefore');
                                elpData['created_by']=userid;
                                var productId = elpData.hasOwnProperty('product_id')?elpData.product_id:0;

                                if (productId > 0) {
                                    var elp = await module.exports.getCurrentElpByPrdPO(elpDetails[0].po_id, productId);

                                    elpData['elp'] = elp;

                                    if(childPOexist > 0){
                                        elp = await module.exports.getCurrentElpByParentPrdPO(elpDetails[0].po_id,productId);
                                        elpData['elp'] = elp;
                                    }

                                    elpData['actual_elp'] = elpData['elp'];

                                    if (dc_le_wh_id != 0) {
                                        var actual_elp = await module.exports.getActualELPByLEWHIDprdId(dc_le_wh_id, productId);

                                        if (actual_elp != "") {
                                            elpData['actual_elp'] = actual_elp;
                                        }
                                    }
  //                                  console.log(elpData,'elpDataelpDataelpDataelpDataelpData');
                                        elpDataArr.push(elpData);
                                }
                              }
                              //purchase price inser query
                              //console.log(elpDataArr,'elpDataArrelpDataArrelpDataArr');
                                await module.exports.insertELPtopurchasePriceHistory(elpDataArr);
                                resolve(1);
                         } else {
                            console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaa');
                            resolve(0);
                        }
                    }).catch(err => {
                        console.log(err);
                        reject(err);
                    })

                }
            } catch (err) {
                console.log(err)
            }
        })
    },


    saveStockInwardNew: async(inward_code,warehouse,stockInward,productInfo,stock_transfer=0,stock_transfer_dc=0,po_id=0)=>{
        return new Promise(async (resolve,reject)=>{
            try{
                var stockInwardId =await module.exports.saveInwardStock(stockInward);
                if(productInfo.length>0){
                    //code commented in grn       
                }
                var response = await inventoryModel.inventoryStockInward(productInfo, warehouse, inward_code, 1);

                if (stock_transfer == 1) {
                    inventoryModel.inventoryStockOutward(productInfo, stock_transfer_dc, 1, po_id, 3);
                }

                resolve(stockInwardId);
            } catch (err) {
                console.log(err)
            }
        })
    },

    /*checkPOType: async(po_id)=>{
        return new Promise((resolve,reject)=>{
            try{
                var posoorderqry = "select po_so_order_code from po where po_id="+po_id+" and po_so_status=1 limit 1";
                sequelize.query(posoorderqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                        //console.log(result,'resultresultresultresultresult');
                         if (result != '' && result.length > 0) {
                              resolve(result[0].approval_status);
                         } else {
                              resolve(0);
                         }
                    }).catch(err => {
                        console.log(err);
                         reject(err);
                    })                   
            }catch (err) {
                  console.log(err)
            }
        })
    },*/

    saveStockInward: async(inwardId,currentDateTime)=>{
        return new Promise((resolve,reject)=>{
            try{
                var stockInwardId=0;

                if(inwardId>0){
                    var productList =[];
                    var inwardDeailsqry = "select inward.le_wh_id, inward.inward_code, inward.po_no, inward_products.product_id, inward_products.received_qty,inward_products.free_qty, inward_products.damage_qty, inward_products.missing_qty,inward_products.excess_qty, inward_products.quarantine_stock,inward_products.orderd_qty FROM inward LEFT JOIN inward_products ON inward_products.inward_id=inward.inward_id WHERE inward_products.inward_id="+inwardId;
                    sequelize.query(inwardDeailsqry, { type: Sequelize.QueryTypes.SELECT }).then(async (result) => {
                        if (result != '' && result.length > 0) {
                              var data = [];
                              var ref_type = "Grn";
                              var i=0;
                              let productInfo=[];
                              var inwardCode = 0;
                              var leWhId = 0;
                              var productId =0;
                              var productList=[];
                              var checkStockInward ='';
                              var stockInwardInsertQry='';
                              for(const inwardProducts of result){
                                productId = inwardProducts.product_id;
                                productList.push(productId);

                                var checkStockInwardQry = "select count(stock_inward_id) as count from stock_inward where reference_no="+inwardId+" and product_id="+productId+" limit 1";

                                sequelize.query(checkStockInwardQry, { type: Sequelize.QueryTypes.SELECT }).then(async (checkStockInward) => {
                                        if (checkStockInward != '' && checkStockInward.length > 0) {
                                            checkStockInward=checkStockInward[0].count;
                                        }else{
                                            checkStockInward=0;
                                        }
                                        inwardCode= inwardProducts.inward_code;
                                        leWhId = inwardProducts.le_wh_id;

                                        if(checkStockInward==0){
                                            var stockInward = [];
                                            stockInward['le_wh_id']=inwardProducts.le_wh_id;
                                            stockInward['product_id']=inwardProducts.product_id;
                                            stockInward['good_qty']=(inwardProducts.received_qty - 
                                            (inwardProducts.damage_qty + inwardProducts.missing_qty + inwardProducts.quarantine_stock));
                                            stockInward['free_qty']=inwardProducts.free_qty;
                                            stockInward['dnd_qty']=inwardProducts.missing_qty;
                                            stockInward['dit_qty']=inwardProducts.damage_qty;
                                            stockInward['quarantine_qty']=inwardProducts.quarantine_stock;
                                            stockInward['po_no']=inwardProducts.po_no;
                                            stockInward['reference_no']=inwardId;
                                            stockInward['inward_date']=currentDateTime;
                                            stockInward['status']='';
                                            stockInward['created_by']=userid;

                                            stockInwardInsertQry= await saveInwardStock(stockInward);
                                            let productData = [];
                                            productData['product_id'] = inwardProducts.product_id;
                                            $productData['soh'] = (inwardProducts.received_qty - 
                                                        (inwardProducts.damage_qty + inwardProducts.missing_qty + inwardProducts.quarantine_stock));
                                            productData['free_qty'] = inwardProducts.free_qty;
                                            productData['quarantine_qty'] = inwardProducts.quarantine_stock;
                                            productData['dit_qty'] = inwardProducts.damage_qty;  //damage in transit
                                            productData['dnd_qty'] = inwardProducts.missing_qty;
                                            
                                            productInfo.push(productData);
                                        }
                                })


                              }
                            if(productInfo.length>0){
                                //code commented in grn       
                               
                                var response = await inventoryModel.inventoryStockInward(productInfo,leWhId,inwardCode,1);

                                if(response){

                                }else{
                                    console.log('Error from inventory model inventoryStockInward for inward '.$inwardCode);
                                }
                            }
                            resolve(stockInwardId);
                        } else {
                            resolve(0);
                        }
                    }).catch(err => {
                        console.log(err);
                         reject(err);
                    })
                }
            }catch (err) {
                console.log(err)
            }  
        });
    },

    assetProductDetails: async (inwardId) => {
        return new Promise((resolve, reject) => {
            try {
                var qty = 0;
                var product_id = 0;
                var createdByID = 0;
                var purchase_date = 0;
                var invoice_no = 0;
                var businessunitname = 0;
                var warentyEndDate = 0;
                var WarrantyYear = 0;
                var WarrantyMonts = 0;
                var depresiationDate = 0;
                var depresiation_month = 0;
                var depresiation_per_month = 0;
                var asset_category_id = 0;
                var isManualImport = 0;
                var qty_data = [];
                var productList = "select products.product_id,products.business_unit_id,products.asset_category,inward_products.received_qty as qty,inward_products.created_by,inward.invoice_no,inward.invoice_date from inward_products left join inward on inward.inward_id=inward_products.inward_id left join products on products.product_id=inward_products.product_id where products.product_type_id=130001 and inward_products.inward_id=" + inwardId;
                sequelize.query(productList, { type: Sequelize.QueryTypes.SELECT }).then(async (response) => {
                    if (response.length > 0) {
                        for (const productData of response[0]) {
                            qty = productData.hasOwnProperty('qty') ? productData.qty : 0;
                            product_id = productData.hasOwnProperty('product_id') ? productData.product_id : 0;
                            createdByID = productData.hasOwnProperty('created_by') ? productData.created_by : 0;
                            purchase_date = productData.hasOwnProperty('invoice_date') ? productData.invoice_date : "";
                            invoice_no = productData.hasOwnProperty('invoice_no') ? productData.invoice_no : 0;

                            //sending values from import excel
                            businessunitname = productData.hasOwnProperty('business_unit_id') ? $productData.business_unit_id : 0;

                            warentyEndDate = productData.hasOwnProperty('warranty_end_date') ? productData.warranty_end_date : 0;
                            WarrantyYear = productData.hasOwnProperty('WarrantyYear') ? productData.WarrantyYear : 0;
                            WarrantyMonts = productData.hasOwnProperty('WarrantyMonts') ? productData.WarrantyMonts : 0;

                            depresiationDate = productData.hasOwnProperty('depresiation_date') ? productData.depresiation_date : 0;
                            depresiation_month = productData.hasOwnProperty('depresiation_month') ? productData.depresiation_month : 0;
                            depresiation_per_month = productData.hasOwnProperty('depresiation_per_month') ? productData.depresiation_per_month : 0;
                            asset_category_id = productData.hasOwnProperty('asset_category') ? productData.asset_category : 0;

                            isManualImport = productData.hasOwnProperty('is_manual_import') ? productData.is_manual_import : 0;
                        
                            for ( i = 1; i <= qty; i++){

                                qty_data = []; 
                                qty_data['product_id']          =      product_id;
                                qty_data['purchase_date']       =      purchase_date;
                                qty_data['invoice_number']      =      invoice_no;
                                qty_data['is_working']          =      "Yes";
                                qty_data['business_unit']       =      businessunitname;

                                qty_data['warranty_end_date']   =      warentyEndDate;
                                qty_data['warranty_year']       =      WarrantyYear;
                                qty_data['warranty_month']      =      WarrantyMonts;

                                qty_data['created_by']          =      createdByID;
                                qty_data['is_manual_import']    =      isManualImport;
                                qty_data['depresiation_date']   =      depresiationDate;
                                qty_data['depresiation_per_month']=    depresiation_per_month;
                                qty_data['depresiation_month']  =      depresiation_month;
                                qty_data['asset_category']      =      asset_category_id;
                                qty_data['asset_status']        =      1;  
                                  
                                await module.exports.saveQtyWiseProducts(qty_data);
                            }

                        }
                   }
                        resolve(1);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    saveQtyWiseProducts: async(qty_data)=>{console.log('saveQtyWiseProductssave')
        return new Promise(async(resolve,reject)=>{
            try{
                var refNoArr=await purchaseOrder.getReferenceCode('AST','TS');
                var codeFound="select count(*) as count from assets where company_asset_code="+refNoArr+"";
                db.query(codeFound, function (err, rows) {
                    if (err) {
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        qty_data.company_asset_code  =  refNoArr;
                        var stkInwardColumns = '';
                        var stkInwardSaveInfo='';
                        for (const key of qty_data) {
                            stkInwardColumns = '';
                            stkInwardSaveInfo='';
                            for(const [columnkey,qty_dataArr] of Object.entries(key)){
                                stkInwardColumns  += columnkey + ",";
                                stkInwardSaveInfo += "'"+qty_dataArr + "',";
                            }
                            var stkInwardColumnslastChar = stkInwardColumns.slice(-1);
                            if (stkInwardColumnslastChar == ',') {
                                stkInwardColumns = stkInwardColumns.slice(0, -1);
                            }
                            var stkInwardSaveInfolastChar = stkInwardSaveInfo.slice(-1);
                            if (stkInwardSaveInfolastChar == ',') {
                                stkInwardSaveInfo = stkInwardSaveInfo.slice(0, -1);
                            }
                            var query = "insert into assets("+stkInwardColumns+") values ("+stkInwardSaveInfo+")";
                            db.query(insertqry, {}, function (err, rows) {
                                if (err) {
                                        reject(err)
                                }
                                if (Object.keys(rows).length > 0) {
                                    return resolve(rows.insertId);
                                } else {
                                    return resolve(0);
                                }
                            }).catch(err => {
                                console.log(err);
                                 reject(err);
                            });
                            //resolve(1);
                        }
                    } else {
                        return resolve([]);
                    }   
                }); 
            }catch (err) {
                  console.log(err)
            }
        })
    },

    getTotalInwardQtyById: async (poid) => {
        return new Promise((resolve, reject) => {
            try {
                var query = "select SUM(inward_products.received_qty) as totQty from inward_products left join inward on inward.inward_id=inward_products.inward_id where inward.po_no=" + poid + " group by inward.po_no limit 1";

                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0].totQty);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    getInvoiceGridOrderId: async (gds_order_id, fieldsList) => {
        return new Promise((resolve, reject) => {
            try {
                var invoiceqry = "select " + fieldsList + " FROM gds_invoice_grid as grid where grid.gds_order_id=" + gds_order_id;
                sequelize.query(invoiceqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0]);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    remittanceMapping : async(arr)=>{
        return new Promise((resolve,reject)=>{
            try{
                var columns='';
                var values='';
                for(var key in arr){
                    columns +=key +','
                    values+=" '" + arr[key] + "',"
                }
                var lastCharColumn = columns.slice(-1);
                var lastCharValue = values.slice(-1);
                if (lastCharColumn == ',') {
                    columns = columns.slice(0, -1);
                }
                if (lastCharValue == ',') {
                    values = values.slice(0, -1);
                }
                var dictionary="("+columns+") VALUES ("+values+")"
                var query = "insert into remittance_mapping "+dictionary+" ";
                db.query(query, {}, async function (err, inserted) {
                    if (err) {
                        reject('error');
                    }
                    if (Object.keys(inserted).length > 0) {
                        return resolve(inserted.insertId);
                    } else {
                        return resolve([]);
                    }
                });

            } catch (err) {
                console.log(err)
            }
        })
    },

    collectionRemittanceMapping : async(arr)=>{console.log('collectionRemittanceMappingvv');
        return new Promise((resolve,reject)=>{
            try{
                var columns='';
                var values='';
                for(var key in arr){
                    columns +=key +','
                    values+=" '" + arr[key] + "',"
                }
                var lastCharColumn = columns.slice(-1);
                var lastCharValue = values.slice(-1);
                if (lastCharColumn == ',') {
                    columns = columns.slice(0, -1);
                }
                if (lastCharValue == ',') {
                    values = values.slice(0, -1);
                }
                var dictionary="("+columns+") VALUES ("+values+")"
                var query = "insert into collection_remittance_history "+dictionary+" ";
                db.query(query, {}, async function (err, inserted) {
                    if (err) {
                        reject('error');
                    }
                    if (Object.keys(inserted).length > 0) {
                        return resolve(inserted.insertId);
                    } else {
                        return resolve([]);
                    }
                });
            }catch (err) {
                  console.log(err)
            }
        })
    },

    getUserByLegalEntityId: async (legal_entity_id) => {
        return new Promise((resolve, reject) => {
            try {
                var tokenqry = "select password_token,user_id from users where legal_entity_id=" + legal_entity_id + " and is_active =1 and is_parent=0";
                sequelize.query(tokenqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0]);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    getOrderInfo: async (orderIds, fields) => {
        return new Promise((resolve, reject) => {
            try {
                var orderstsqry = "select " + fields + " from gds_orders as orders where orders.gds_order_id=" + orderIds;
                sequelize.query(orderstsqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0]);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    getInwardProductID : async(prdId,inwardId)=>{
      return new Promise((resolve,reject)=>{
            var inwrdprdqry = "SELECT inward_prd_id from inward_products where product_id="+prdId+" and inward_id="+inwardId+" ";
            db.query(inwrdprdqry, {}, function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    resolve(rows[0].inward_prd_id);
                } else {
                    resolve(0);
                }
            });
        }); 
    },

    getCurrentElpByPrdPO: async (poId, productId) => {
        return new Promise((resolve, reject) => {
            try {
                var curelpqry = "SELECT pp.cur_elp from po left join po_products as pp ON po.po_id=pp.po_id where po.po_id=" + poId + " and pp.product_id=" + productId + "  limit 1";
                sequelize.query(curelpqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0].cur_elp);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    getCurrentElpByParentPrdPO: async (poId, productId) => {
        return new Promise((resolve, reject) => {
            try {
                var curelpqry = "SELECT pp.cur_elp from po left join po_products as pp ON po.po_id=pp.po_id where po.parent_id=" + poId + " and pp.product_id=" + productId + "  limit 1";
                sequelize.query(curelpqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0].cur_elp);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    getActualELPByLEWHIDprdId: async (dcId, productId) => {
        return new Promise((resolve, reject) => {
            try {
                var curelpqry = "SELECT actual_elp from purchase_price_history where le_wh_id=" + dcId + " and product_id=" + productId + " order by created_at desc limit 1";
                sequelize.query(curelpqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                        resolve(result[0].actual_elp);
                    } else {
                        resolve(0);
                    }
                }).catch(err => {
                    console.log(err);
                    reject(err);
                })
            } catch (err) {
                console.log(err)
            }
        })
    },

    saveInwardStock: async (saveStock) => {
        return new Promise((resolve, reject) => {
            var stkInwardColumns = '';
            var stkInwardSaveInfo = '';
            for (const key of saveStock) {
                stkInwardColumns = '';
                stkInwardSaveInfo = '';
                for (const [columnkey, saveStockArr] of Object.entries(key)) {
                    stkInwardColumns += columnkey + ",";
                    stkInwardSaveInfo += "'" + saveStockArr + "',";
                }
                //}
                var stkInwardColumnslastChar = stkInwardColumns.slice(-1);
                if (stkInwardColumnslastChar == ',') {
                    stkInwardColumns = stkInwardColumns.slice(0, -1);
                }
                var stkInwardSaveInfolastChar = stkInwardSaveInfo.slice(-1);
                if (stkInwardSaveInfolastChar == ',') {
                    stkInwardSaveInfo = stkInwardSaveInfo.slice(0, -1);
                }
                var insertqry = "insert into stock_inward("+stkInwardColumns+") values ("+stkInwardSaveInfo+")";
                db.query(insertqry, {}, function (err, rows) {
                    if (err) {
                            reject(err);
                    }
                    if (Object.keys(rows).length > 0) {
                        return resolve(rows.insertId);
                    } else {
                        return resolve(0);
                    }
                });
                //resolve(1);
            }
        });
    },

    createSubPo:async function(poId,createdBy){
        return new Promise((resolve, reject) => {
            var current_datetime = new Date();
            var createddate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
            var query="select po.*, IF((po.`apply_discount_on_bill` = 1 AND po.`discount_type` = 0), (po.`discount` - inward.`discount_on_total`), po.`discount`) AS final_discount from `po` left join `inward` on `inward`.`po_no` = `po`.`po_id` where `po`.`po_id` = "+poId+" limit 1"
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    var poDetails=rows[0];
                    poDetails.discount = poDetails.final_discount;
                    poDetails.hasOwnProperty('po_id')? (delete poDetails.po_id):0;
                    poDetails.hasOwnProperty('updated_at')?(delete poDetails.updated_at):0;
                    poDetails.hasOwnProperty('created_at')?(delete poDetails.created_at):0;
                    poDetails.hasOwnProperty('final_discount')?(delete poDetails.final_discount):0;
                    poDetails.hasOwnProperty('po_so_order_code')?(delete poDetails.po_so_order_code):0;
                    poDetails.hasOwnProperty('po_so_status')?(delete poDetails.po_so_status):0;
                    var currentPoCode = poDetails.po_code;
                    var poCodeDetails = currentPoCode.split("_");
                    var poCode = (poCodeDetails[0]) ? poCodeDetails[0] : currentPoCode;
                    var query1="select count(inward_id) as count from `inward` left join `po` on `po`.`po_id` = `inward`.`po_no` where `po_code` like '%' '"+poCode+"' '%'";
                    db.query(query1,{},(err,codeCount)=>{
                        if (err) {
                                reject('error');
                        }
                        poDetails.po_code = poCode + '_'+ codeCount[0].count;
                        poDetails.po_status = 87001;
                        poDetails.approval_status = 57031;
                        poDetails.parent_id = poId;
                        poDetails.logistic_associate_id = '';
                        poDetails.is_closed = 0;
                        poDetails.platform = 5001;
                        poDetails.po_date = createddate;
                        poDetails.created_by = createdBy;
                        poDetails.updated_by = createdBy;
                        var columns='';
                        var values='';
                        for(var key in poDetails){
                            columns +=key +','
                            values+=" '" + poDetails[key] + "',"
                        }
                        var lastCharColumn = columns.slice(-1);
                        var lastCharValue = values.slice(-1);
                        if (lastCharColumn == ',') {
                            columns = columns.slice(0, -1);
                        }
                        if (lastCharValue == ',') {
                            values = values.slice(0, -1);
                        }
                        var dictionary="("+columns+") VALUES ("+values+")"
                        var query2 = "INSERT INTO po " + dictionary + " ";
                        db.query(query2,{},(err,inserted)=>{
                            console.log(query2);
                            if (err) {
                                    reject('error');
                            }
                            if (Object.keys(inserted).length > 0) {
                                var newPoId=inserted.insertId; 
                                var query3 = "CALL getSubPoProductDetails("+poId+")";
                                db.query(query3,{},(err,productCollection)=>{
                                    if (err) {
                                            reject('error');
                                    }
                                    if (Object.keys(productCollection).length > 0) {
                                        var newPoProductData = [];
                                        for(const tempProductInfo of productCollection){
                                        //productCollection.forEach(tempProductInfo=>{
                                            var diffCount = (tempProductInfo.diff) ? tempProductInfo.diff : 0;
                                            if(diffCount > 0){ 
                                                tempProductInfo.hasOwnProperty('po_product_id')?(delete tempProductInfo.po_product_id):0;
                                                tempProductInfo.hasOwnProperty('diff')?(delete tempProductInfo.diff):0;
                                                tempProductInfo.hasOwnProperty('created_at')?(delete tempProductInfo.created_at):0; 
                                                tempProductInfo.po_id = newPoId;
                                                var no_of_eaches = tempProductInfo.no_of_eaches;
                                                var qty = tempProductInfo.qty;
                                                if((no_of_eaches * qty) <= diffCount){
                                                    var diffResult = (diffCount/no_of_eaches);
                                                    if(parseFloat(diffResult)){
                                                        no_of_eaches = 1;
                                                        tempProductInfo.no_of_eaches = 1;
                                                        tempProductInfo.uom = 16001;
                                                    }else{
                                                        diffCount = diffResult;
                                                    }
                                                }else{
                                                    no_of_eaches = 1;
                                                    tempProductInfo.no_of_eaches = 1;
                                                    tempProductInfo.uom = 16001;
                                                }
                                                var unit_price = tempProductInfo.unit_price;
                                                var tax_per = tempProductInfo.tax_per;
                                                var is_tax_included = tempProductInfo.is_tax_included;
                                                tempProductInfo.qty = diffCount;
                                                var subTotal = ((diffCount * no_of_eaches) * unit_price);
                                                tempProductInfo.price = (no_of_eaches * unit_price);
                                                var taxAmount = 0;
                                                if(is_tax_included){
                                                    var basePrice = (subTotal/(100+tax_per)*100);
                                                    taxAmount = (subTotal - basePrice);
                                                }else{
                                                    taxAmount = ((subTotal * tax_per)/100);
                                                }
                                                tempProductInfo.tax_amt = taxAmount;
                                                newPoProductData= tempProductInfo;
                                                console.log(newPoProductData);
                                            }
                                        }
                                        console.log('outtttttt',newPoProductData);
                                
                                        if(newPoProductData){
                                            var columns='';
                                            var values='';
                                            for(var key in newPoProductData){
                                                columns +=key +','
                                                values+=" '" + newPoProductData[key] + "',"
                                            }
                                            var lastCharColumn = columns.slice(-1);
                                            var lastCharValue = values.slice(-1);
                                            if (lastCharColumn == ',') {
                                                columns = columns.slice(0, -1);
                                            }
                                            if (lastCharValue == ',') {
                                                values = values.slice(0, -1);
                                            }
                                            var dictionary="("+columns+") VALUES ("+values+")"
                                            var query4= "INSERT INTO po_products "+dictionary+" ";
                                            db.query(query4,{},(err,rows)=>{
                                                if (err) {
                                                        reject('error');
                                                }
                                                if (Object.keys(rows).length > 0) {
                                                    return resolve(rows);
                                                } else {
                                                    return resolve([]);
                                                }
                                            }); 
                                        }
                                    } else {
                                        return resolve([]);
                                    }
                                });
                            } else {
                                return resolve([]);
                            }
                       });
                    });
                } else {
                    return resolve([]);
                }
            });
        });
    },

    getGrnProductDetails:async function(grnId){
        return new Promise((resolve,reject)=>{
            var query="select `products`.`mrp`, `products`.`kvi`, `products`.`sku`, `products`.`product_title`, `po_products`.`tax_per`, `po_products`.`tax_name`, `po_products`.`qty`, `po_products`.`uom`, `master_lookup`.`master_lookup_name` as `uomName`, `po_products`.`no_of_eaches`, (po_products.no_of_eaches * po_products.qty) AS actual_po_quantity, `po_products`.`free_qty`, `po_products`.`free_uom`, `po_products`.`free_eaches`, `po_products`.`hsn_code` as `hsn`, `po_products`.`tax_data` as `po_tax_data`,po_products.`apply_discount`,`po_products`.`discount_type`,`po_products`.`discount`, `inward_products`.* from `inward_products` left join `inward` on `inward`.`inward_id` = `inward_products`.`inward_id` left join `po` on `po`.`po_id` = `inward`.`po_no` left join `po_products` on `po_products`.`po_id` = `inward`.`po_no` and `po_products`.`product_id` = `inward_products`.`product_id` left join `products` on `products`.`product_id` = `inward_products`.`product_id` left join `master_lookup` on `master_lookup`.`value` = `po_products`.`uom` where `inward`.`inward_id` = "+grnId+" group by `inward_products`.`product_id`";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    resolve(rows);
                }
            }); 
        });
    },

    getHubIdByDcId:async function(dc_id){
        return new Promise((resolve,reject)=>{
            var query="select hub_id from dc_hub_mapping  where dc_id = "+dc_id+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    resolve(rows[0].hub_id);
                }
            }); 
        });
    },

    initiatePutAway:async function(inwardId){
        return new Promise((resolve,reject)=>{
            var failed=1;
            var status='HOLD';
            var statusId=12803;
            var query="select value from master_lookup where mas_cat_id=128 and LOWER(master_lookup_name) = '"+status+"' limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    statusId = rows[0].value;
                }
                query1 ="insert into putaway_list (putaway_source,source_id,putaway_status) VALUES ('GRN',"+inwardId+","+statusId+")";
                db.query(query1,{},async function(err,inserted){
                    if(err){
                        reject('error');
                    }
                    if(inserted.length>0){
                        putAwayIncId = inserted.insertId;
                        var response =await this.putawayBinAllocation(putAwayIncId);
                        status = (allocationData.hasOwnProperty('status')) ? allocationData.status : 0;
                        if(status == 'failed'){
                            //$this->sendPutAwayFailedMail($putAwayIncId, $allocationData);
                        }
                    }else{
                        return resolve([]);
                    }
                });
            });
        });
    },
    
    
    binReservation:async function(wh_id,bin_id,putawayListId,pack_type,pro_id,tot_qty,tot_grn_qty,bin_type){
        return new Promise ((resolve,reject)=>{
            if('109005' <= bin_type)
                bin_type='109005';
            var checkReseredRs=`select qty from putaway_allocation where wh_id=${wh_id} and bin_id=${bin_id} and  putaway_list_id=${putawayListId} and is_active=1 and pack_level=${pack_type} and prod_id=${pro_id} and bin_type=${bin_type} and NULL(picker_id) limit 1`;
            db.query(checkReseredRs,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    var rs1=`update 'putaway_allocation' set qty=${tot_qty} where wh_id=${wh_id} and bin_id=${bin_id} and putaway_list_id=${putawayListId} and is_active=1 and bin_type=${bin_type} and pack_level=${pack_type} `
                    db.query(rs1,{},async function(err,rows){
                        if(err){
                            reject('error');
                        }
                        if(rows.length>0){
                            return resolve(rows);
                        }
                    });
                }else{
                    var rs1=`insert into putaway_allocation (wh_id,bin_id,prod_id,pack_level,putaway_list_id,qty,pending_qty,total_qty,bin_type,is_active) VALUES (${wh_id},${bin_id}.${pro_id},${pack_type},${putawayListId},${tot_qty},${tot_qty},${tot_grn_qty},${bin_type},1)`;
                    db.query(rs1,{},async function(err,rows){
                        if(err){
                            reject('error');
                        }
                        if(rows.length>0){
                            return resolve(rows);
                        }
                    });
                }
            });
                
        });
    },

    getProductWiseBinLocation:async function(id,type,product_id,grn_eaches_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,bin_dim_type,emptyStorageBin){
        return new Promise ((resolve,reject)=>{
            var tot_grn_qty=grn_eaches_qty;
            var bin_type_cnt =0;
            var remaining_grn_qty=grn_eaches_qty;
            //var binData array_unique($this->binArray);
            var query="select `wh_conf`.`le_wh_id` as `wh_id`, `wh_conf`.`wh_location` as `bin_code`, `wh_conf`.`wh_loc_id` as `bin_id`, `wh_conf`.`res_prod_grp_id`, `wh_conf`.`pref_prod_id`, `wh_conf`.`bin_type_dim_id`, `bin_dim`.`length` as `length`, `bin_dim`.`breadth` as `breadth`, `bin_dim`.`heigth` as `height`, `bin_category` from `warehouse_config` as `wh_conf` inner join `bin_type_dimensions` as `bin_dim` on `bin_dim`.`bin_type_dim_id` = `wh_conf`.`bin_type_dim_id` where `le_wh_id` = "+wh_id+" and `wh_location_types` = 120006 and `bin_dim`.`bin_type` = "+bin_dim_type+" and `bin_category` !='' and `wh_conf`.`pref_prod_id` in ("+product_id+") and `wh_conf`.`wh_loc_id` not in ("+binData+")";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(bin_dim_type!=109003 && (rows=='')){
                    bin_type_cnt =1;
                    var query1="select `wh_conf`.`le_wh_id` as `wh_id`, `wh_conf`.`wh_location` as `bin_code`, `wh_conf`.`wh_loc_id` as `bin_id`, `wh_conf`.`res_prod_grp_id`, `wh_conf`.`pref_prod_id`, `wh_conf`.`bin_type_dim_id`, `bin_dim`.`length` as `length`, `bin_dim`.`breadth` as `breadth`, `bin_dim`.`heigth` as `height`, `bin_category` from `warehouse_config` as `wh_conf` inner join `bin_type_dimensions` as `bin_dim` on `bin_dim`.`bin_type_dim_id` = `wh_conf`.`bin_type_dim_id` where `le_wh_id` = "+wh_id+" and `wh_location_types` = 120006 and `bin_dim`.`bin_type` = "+bin_dim_type+" and `wh_conf`.`pref_prod_id` = 0 and `bin_category` !='' and `wh_conf`.`wh_loc_id` not in ("+binData+")";
                    db.query(query1,{},async function(err,rows){
                        if(err){
                            reject('error');
                        }
                    });
                }
                var loop_cnt=0;
                //var chcekBinArray = array_filter($getBins);
                if((chcekBinArray!=0) && grn_eaches_qty >0 && remaining_grn_qty >0){  
                    //var cnt = 0;
                    await Promise.all(getBins.map(async function(key,bintypeValue){
                        //var unique_bin=array_unique($this->binArray)
                        //cnt++;
                        var productBinCatType = await this.productBinCategoryType(product_id);
                        if((!bintypeValue.bin_id.includes(unique_bin)) && productBinCatType == bintypeValue.bin_category && remaining_grn_qty >0) {
                            var checkMinMax = await this.checkReservedProductMinMax(product_id,wh_id,bintypeValue.bin_type_dim_id);
                            if(checkMinMax==0 && bin_dim_type <= 109006 && bin_dim_type!=109003){  
                                var sss= await this.dynamicBins(bintypeValue.bin_id,bintypeValue.bin_type_dim_id,wh_id,product_id);
                            }else if(bintypeValue.pref_prod_id==0 && remaining_grn_qty >0){

                                var productGrpid = await this.getProductGroupID(product_id);
                                var add=await this.addProductToBin(bintypeValue.bin_id,productGrpid,product_id);
                                remaining_grn_qty= await this.getProductWiseBinLocation(id,type,product_id,remaining_grn_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,bin_dim_type,1);
                            }
                            var productMinMaxInfo = await this.productMinMaxValues(wh_id,bintypeValue.res_prod_grp_id,bintypeValue.bin_type_dim_id);
                            var binCurrentQty =await this.binReservedQty(bintypeValue.bin_id,product_id);
                            var replenishmentStatus = await this.binReplenishmentStatus(bintypeValue.bin_id);
                            if((productMinMaxInfo) && (replenishmentStatus=='')){
                                var eachesCount = await this.getProductPackEaches(product_id,productMinMaxInfo.pack_conf_id);
                                var eachesQty = eachesCount.no_of_eaches*productMinMaxInfo.min_qty;
                                if(binCurrentQty != 0){
                                    var productCapacityRange = productMinMaxInfo.max_qty-productMinMaxInfo.min_qty; 
                                }else{
                                    var productCapacityRange = $productMinMaxInfo['max_qty'];
                                }
                                productCapacityRange = productCapacityRange*eachesCount.no_of_eaches;

                                if(bin_dim_type == 109005 || bin_dim_type ==109006){
                                    eachesQty = eachesCount.no_of_eaches*productMinMaxInfo.max_qty;
                                    var storageMaxQty = productMinMaxInfo.max_qty*eachesCount.no_of_eaches;
                                    productCapacityRange = storageMaxQty-binCurrentQty;
                                   
                                }

                                if(binCurrentQty <= eachesQty ){
                                   if(remaining_grn_qty <= productCapacityRange && remaining_grn_qty>0){

                                       // if(!bintypeValue.bin_id.includes(this->binArray){
                                            grn_eaches_qty = grn_eaches_qty;
                                            
                                            //var this->locationArray[] ={'wh_id':bintypeValue.wh_id,'product_id':product_id,'bin_code':bintypeValue.bin_code,'bin_id':bintypeValue.bin_id,'pack_type':pack_level,'qty'.remaining_grn_qty,'tot_grn_qty':grn_eaches_qty,'bin_type':bin_dim_type};

                                            //$this->binArray[]= bintypeValue.bin_id;
                                            loop_cnt = 1;
                                            remaining_grn_qty = 0;
                                            grn_eaches_qty =0;
                                            return resolve(remaining_grn_qty);
                                        //}            
                                   }else if(remaining_grn_qty>0 && remaining_grn_qty >=productCapacityRange ){
                                        remaining_grn_qty =  remaining_grn_qty-productCapacityRange;
                                        if(remaining_grn_qty > 0){
                                            locationArray.push({'wh_id':bintypeValue['wh_id'],'product_id':product_id,'bin_code':bintypeValue['bin_code'],'bin_id':bintypeValue['bin_id'],'pack_type':pack_level,'qty':productCapacityRange,'tot_grn_qty':remaining_grn_qty,'bin_type':bin_dim_type});
                                            //this->binArray[]= bintypeValue.bin_id;
                                            loop_cnt = 1;
                                        } 
                                   }                                  
                                }
                            }
                        }
                        if(remaining_grn_qty === 0){
                            return resolve(remaining_grn_qty);
                        }
                    }));
                    if(remaining_grn_qty > 0){   
                        if(loop_cnt >0 && bin_dim_type==109004) {
                            bin_dim_type =109004;
                        }else if(loop_cnt >0 && bin_dim_type==109005){
                            bin_dim_type =109005;
                        }else{
                            bin_dim_type= bin_dim_type+1;
                        }
                        if(bin_dim_type <= 109006){
                            remaining_grn_qty= await this.getProductWiseBinLocation(id,type,product_id,remaining_grn_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,bin_dim_type,1);

                        }else{
                            //$this->productBinConfigArray[]=array('product_id'=>$product_id);
                        }
                    }
                }else {
                    bin_dim_type= bin_dim_type+1;
                    if(bin_dim_type <= 109005 ){     
                        remaining_grn_qty= await this.getProductWiseBinLocation(id,type,product_id,remaining_grn_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,bin_dim_type,0);
                      
                    }else if(bin_dim_type == 109006){
                        remaining_grn_qty= await this.getProductWiseBinLocation(id,type,product_id,remaining_grn_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,bin_dim_type,1);
                    }else{
                        //$this->productBinConfigArray[]=array('product_id'=>$product_id);
                    }
                }
            });
        });
    },

    productBinCategoryType:async function(product_id){
        return new Promise ((resolve,reject)=>{
            var query="select `bin_category_type` from `products_characteristics` where `product_id` ="+product_id+"";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows[0]);
                }
            });
        });
    },

    checkReservedProductMinMax:async function(pid,wh_id,dim_type){
        return new Promise (async (resolve,reject)=>{
            var productGrpid = await this.getProductGroupID(pid);
            var query="select `*` from `product_bin_config` where `prod_group_id` ="+productGrpid+" and wh_id="+wh_id+" and bin_type_dim_id="+dim_type+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows);
                }
            });
        });
    },

    getProductGroupID:async function(pid){
        return new Promise ((resolve,reject)=>{
            var query="select product_group_id from `products` where `product_id` ="+pid+" ";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows[0]);
                }
            });
        });
    },

    addProductToBin:async function(bin_id,groupId,pid){
        return new Promise ((resolve,reject)=>{
            var query="update warehouse_config set res_prod_grp_id="+groupId+" and pref_prod_id="+pid+" where wh_loc_id="+bin_id+"";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    var query1="update bin_inventory set product_id="+pid+" where bin_id="+bin_id+"";
                    db.query(query1,{},async function(err,rows){
                        if(err){
                            reject('error');
                        }
                        if(rows.length>0){
                            return resolve(rows);
                        }
                    });
                }
            });
        });
    },

    productMinMaxValues:async function(wh_id,productGrpId,bintype){
        return new Promise ((resolve,reject)=>{
            var query="select pack_conf_id,min_qty,max_qty from product_bin_config where wh_id="+wh_id+" and prod_group_id="+productGrpId+" and bin_type_dim_id="+bin_type+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows);
                }
            });
        });
    },

    binReservedQty:async function(bin_id,pid){
        return new Promise ((resolve,reject)=>{
            var query="select getBinProdQty("+bin_id+","+pid+") as qty";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows[0].qty);
                }
            });
        });
    },

    binReplenishmentStatus:async function(bin_id){
        return new Promise ((resolve,reject)=>{
            var query="'select * from replenishment_products where bin_id="+bin_id+" and status IN('Open','Assigned') limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows);
                }
            });
        });
    },

    getProductPackEaches:async function(pid,pack_type){
        return new Promise ((resolve,reject)=>{
            var query="select no_of_eaches from product_pack_config where product_id="+pid+" and level="+pack_type+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows);
                }
            });
        });
    },

    dynamicBins:async function(bin_id,bin_dim_type,wh_id,pid){
        return new Promise ((resolve,reject)=>{
            var query="select * from `warehouse_config` as `wh` inner join `bin_type_dimensions` as `bin_dim` on `bin_dim`.`bin_type_dim_id` = `wh`.`bin_type_dim_id` inner join `product_bin_config` as `bin_con` on `bin_con`.`bin_type_dim_id` = `bin_dim`.`bin_type_dim_id` and `wh`.`res_prod_grp_id` = `bin_con`.`prod_group_id` and `wh`.`le_wh_id` = `bin_con`.`wh_id` where `wh`.`bin_type_dim_id` = "+bin_dim_type+" and `bin_dim`.`bin_type` in (109004, 109005) and `wh_loc_id` = "+bin_id+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    if(checkEmptyBin.res_prod_grp_id!=0){
                        var productMinMaxInfo = await this.productMinMaxValues(wh_id,checkEmptyBin.res_prod_grp_id,bin_dim_type);
                        if($productMinMaxInfo==''){
                            var productVolumn = await this.productVolumn(pid);
                            var productVolumnByCFC = await this.getProductWiseCFC(pid);
                            if(productVolumnByCFC.length>0){
                                var bin_volumn = await this.getBinDimByBinId(bin_dim_type);
                                bin_volumn = (bin_volumn>0)?bin_volumn:1;
                                productVolumn = (productVolumn>0)?productVolumn:1;

                                //var binTotalMinMaxCFCWise= floor(bin_volumn/productVolumn);
                                //var totProductVolumnByCFC = floor(binTotalMinMaxCFCWise/productVolumnByCFC);

                                var productGrpid = await this.getProductGroupID(pid);
                                var binVolum =`insert into product_bin_config (prod_group_id,wh_id,bin_type_dim_id,pack_conf_id,min_qty,max_qty) VALUES(${checkEmptyBin.res_prod_grp_id},${wh_id},${bin_dim_type},16004,1,${totProductVolumnByCFC})`;
                                db.query(binVolum,{},async function(err,rows){
                                    if(err){
                                        reject('error');
                                    }
                                });
                            }
                        }
                    }else{
                        var productVolumn = await this.productVolumn(pid);
                        var productVolumnByCFC = await this.getProductWiseCFC(pid);
                        if(productVolumnByCFC.length>0){
                            var bin_volumn = await this.getBinDimByBinId(bin_dim_type);
                            bin_volumn = (bin_volumn>0)?bin_volumn:1;
                            productVolumn = (productVolumn>0)?productVolumn:1;
                            
                            //var binTotalMinMaxCFCWise= floor(bin_volumn/productVolumn);
                            //var totProductVolumnByCFC = floor(binTotalMinMaxCFCWise/productVolumnByCFC);

                            var productGrpid = await this.getProductGroupID(pid);
                            var binPro = await this.addProductToBin(bin_id,productGrpid,pid);
                
                            var binVolum =`insert into product_bin_config (prod_group_id,wh_id,bin_type_dim_id,pack_conf_id,min_qty,max_qty) VALUES(${productGrpid},${wh_id},${bin_dim_type},16004,1,${totProductVolumnByCFC})`;
                            db.query(binVolum,{},async function(err,rows){
                                if(err){
                                    reject('error');
                                }
                            });
                        }
                    } 
                }else{
                    var productVolumn = await this.productVolumn(pid);
                    var productVolumnByCFC = await this.getProductWiseCFC(pid);
                    if($productVolumnByCFC.length>0) {
                        var bin_volumn = await this.getBinDimByBinId(bin_dim_type);
                        bin_volumn = (bin_volumn>0)?bin_volumn:1;
                        productVolumn = (productVolumn>0)?productVolumn:1;
                        
                        //var binTotalMinMaxCFCWise= floor(bin_volumn/productVolumn);
                        //var totProductVolumnByCFC = floor(binTotalMinMaxCFCWise/productVolumnByCFC);

                        var productGrpid = await this.getProductGroupID(pid);
                        var binPro =await this.addProductToBin(bin_id,productGrpid,pid);
                        var binVolum =`insert into product_bin_config (prod_group_id,wh_id,bin_type_dim_id,pack_conf_id,min_qty,max_qty) VALUES(${productGrpid},${wh_id},${bin_dim_type},16004,1,${totProductVolumnByCFC})`;
                        db.query(binVolum,{},async function(err,rows){
                            if(err){
                                reject('error');
                            }
                        });
                    }
                }
            });
        });
    },

    productVolumn:async function(pid){
        return new Promise ((resolve,reject)=>{
            var query="select `product_title`, `product_group_id`, `length`, `breadth`, `height` from `product_pack_config` inner join `products` on `products`.`product_id` = `product_pack_config`.`product_id` where `level` = 16001 and `product_pack_config`.`product_id` = "+pid+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    //$arrayData['config']=["product_title"=>$productEachLevel['product_title']];
                    var product_volumn = rows.length*rows.breadth*rows.height;
                    return resolve(product_volumn);
                }
            })
        });
    },

    getProductWiseCFC:async function(pid){
        return new Promise ((resolve,reject)=>{
            var query="select no_of_eaches from `product_pack_config`  where product_id= "+pid+" and `level` =16004";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    return resolve(rows[0]);
                }else{
                    return resolve(1);
                }
            })
        });
    },

    getBinDimByBinId:async function(binDimId){
        return new Promise ((resolve,reject)=>{
            var query="select length,breadth,heigth from `bin_type_dimensions`  where bin_type_dim_id= "+binDimId+" limit 1";
            db.query(query,{},async function(err,rows){
                if(err){
                    reject('error');
                }
                if(rows.length>0){
                    var binVolumn= rows.length*rows.breadth*rows.heigth;
                    return resolve(binVolumn);
                } 
            })
        });
    },

    // putawayBinAllocation:async function(putListId){
    //     return new Promise ((resolve,reject)=>{
    //         var putawayListArray ="";
    //         var productIdList = '';
    //         var binPutawayList=[];
    //         var productTitleArray = [];
    //         var status_cnt=0;
    //         var query="select putaway_source as type,source_id as id from putaway_list where putaway_id="+putListId+" and putaway_status!=12804 and putaway_status!=12801 limit 1";
    //         db.query(query,{},async function(err,rows){
    //             if(err){
    //                 reject('error');
    //             }
    //             if(rows.length>0){
    //                 var id=rows[0].id;
    //                 var type=rows[0].type;
    //                     var query1=" select `inw`.`le_wh_id` as `wh_id`, `inw`.`inward_id`, `inw_det`.`product_id`, `inw_det`.`pack_level`, `inw_det`.`tot_rec_qty`, `inw_det`.`pack_qty` as `pack_qty`, `inw_det`.`received_qty` as `tot_pack_qty` from `inward` as `inw` inner join `inward_products` as `inw_pro` on `inw_pro`.`inward_id` = `inw`.`inward_id` inner join `inward_product_details` as `inw_det` on `inw_det`.`inward_prd_id` = `inw_pro`.`inward_prd_id` where `inw`.`inward_id` = "+id+"";
    //                     db.query(query1,{},async function(err,productIdList){
    //                         if(err){
    //                             reject('error');
    //                         }
    //                         if(productIdList.length>0){
    //                             productIdList.forEach(putwayListValue=>{
    //                                 var product_id= putwayListValue.product_id;
    //                                 var grn_eaches_qty= putwayListValue.tot_rec_qty;
    //                                 var wh_id= putwayListValue.wh_id;
    //                                 var pack_level= (putwayListValue.hasOwnProperty('pack_level'))?putwayListValue.pack_level:'16001';
    //                                 var pack_wise_qty =(putwayListValue.hasOwnProperty('pack_qty'))?putwayListValue.pack_qty:'0';
    //                                 var pack_wise_tot_qty =(putwayListValue.hasOwnProperty('tot_pack_qty'))?putwayListValue.tot_pack_qty:'0';
    //                                 var remaining_grn_qty=await this.getProductWiseBinLocation(id,type,product_id,grn_eaches_qty,wh_id,pack_level,pack_wise_qty,pack_wise_tot_qty,109003,0);
    //                             });
    //                             //if(empty($this->productBinConfigArray)){
    //                                 //if(empty($this->nonLocationArray)){
    //                                         //DB::beginTransaction();
    //                                        // foreach($this->locationArray as $locationValue){
    //                                             var binRs= await this.binReservation(locationValue.wh_id,locationValue.bin_id,putListId,locationValue.pack_type,locationValue.product_id,locationValue.qty,locationValue.tot_grn_qty,locationValue.bin_type);
    //                                                 if(binRs == 1){
    //                                                     status_cnt = 1;
    //                                                 }
    //                                         //}
    //                                         if(status_cnt == 1){
    //                                             var rs = "update putaway_list set putaway_status=12801 where putaway_id="+putListId+"";
    //                                            // DB::commit();
    //                                             db.query(rs,{},async function(err,rows){
    //                                                 if(err){
    //                                                     reject('error');
    //                                                 }
    //                                                 if(rows.length>0){
    //                                                     return resolve(1);
    //                                                 }
    //                                             }); 
    //                                         }else{
    //                                            // DB::rollback();
    //                                         }
    //                                 }else {
    //                                     //var getArray="select product_title from products where product_id IN ($this->nonLocationArray)";
    //                                     db.query(getArray,{},async function(err,rows){
    //                                         if(err){
    //                                             reject('error');
    //                                         }
    //                                         if(rows.length>0){
    //                                             console.log('This product dont have bins, grn status hold.');
    //                                             return resolve(rows);
    //                                         }
    //                                     }); 
    //                                 } 
    //                             }else{
    //                                 //var getArray="select product_title from products where product_id IN ($this->productBinConfigArray) ";
    //                                 db.query(getArray,{},async function(err,rows){
    //                                     if(err){
    //                                         reject('error');
    //                                     }
    //                                     if(rows.length>0){
    //                                         console.log('This product dont have min and max capacity (or) Storage bins are not Configure (or) Replenishment happens.');
    //                                         return resolve(rows);
    //                                     }
    //                                 });
    //                             }

    //                         }else {
    //                             reject('Products not available');
    //                         }
    //                     });
    //             } else {
    //                 reject('This putaway list is already created');
    //             }
    //         });
    //     });
    // },

     getOrderByOrderId :async(orderId)=>{
        return new Promise((resolve,reject)=>{
            try{
             var orderstsqry = "select le_wh_id,hub_id from gds_orders where gds_order_id="+orderId;   
                sequelize.query(orderstsqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                        //console.log(result,'resultresultresultresultresult');
                if (result != '' && result.length > 0) {
                          resolve(result[0]);
                     } else {
                          resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })
            }catch (err) {
                  console.log(err)
            }
        })
    },

    collectionDetailsById :async(orderId)=>{
        return new Promise((resolve,reject)=>{
            try{
             var orderstsqry = "SELECT * FROM collections c WHERE c.gds_order_id = "+orderId+" LIMIT 1";   
                sequelize.query(orderstsqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                        //console.log(result,'resultresultresultresultresult');
                if (result != '' && result.length > 0) {
                          resolve(result[0]);
                     } else {
                          resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })
            }catch (err) {
                  console.log(err)
            }
        })
    },

    updateInwardPrds : async(taxInfo,inward_prd_id)=>{
        return new Promise((resolve,reject)=>{
             var updateInwardPrdsqry = "update inward_products set tax_data='"+JSON.stringify(taxInfo[0])+"' where inward_prd_id="+inward_prd_id;
             db.query(updateInwardPrdsqry, {}, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject('error');
                }
                if (rows> 0) {
                    console.log(rows,'rowsrowsrowsrowsrowsrows');
                    return resolve(rows.insertId);
                } else {
                    return resolve([]);
                }
            }); 
            //resolve(1);  
        })
    },

    insertELPtopurchasePriceHistory : async(elpDataArr)=>{
        return new Promise((resolve,reject)=>{
            try{
                var grnElpColumns = '';
                var grnElpSaveInfo='';
                for (const key of elpDataArr) {
                    grnElpColumns = '';
                    grnElpSaveInfo='';
                    for(const [columnkey,grnelpDtlsArr] of Object.entries(key)){
                        grnElpColumns  += columnkey + ",";
                        grnElpSaveInfo += "'"+grnelpDtlsArr + "',";
                    }
                
                    var grnElpColumnslastChar = grnElpColumns.slice(-1);
                    if (grnElpColumnslastChar == ',') {
                        grnElpColumns = grnElpColumns.slice(0, -1);
                    }
                    var grnElpSaveInfolastChar = grnElpSaveInfo.slice(-1);
                    if (grnElpSaveInfolastChar == ',') {
                        grnElpSaveInfo = grnElpSaveInfo.slice(0, -1);
                    }
                    var insertqry = "insert into purchase_price_history("+grnElpColumns+") values ("+grnElpSaveInfo+")";
                    db.query(insertqry, {}, function (err, rows) {
                        if (err) {
                            console.log(err);
                                reject(err)
                        }
                        if (rows) {
                            return resolve(rows.insertId);
                        } else {
                            return resolve(0);
                        }
                    })
                    //return resolve(1);
                }                
            }catch (err) {
                  console.log(err)
            }
        })
    },

    getProductPackUOMInfoForCreateGrn: function (product_id, pack_size,index) {
        return new Promise(async (resolve, reject) => {
                try {
                    let uomqry = "select lookup.value,lookup.master_lookup_name as uomName,pack.no_of_eaches from product_pack_config as pack left join master_lookup as lookup on pack.level=lookup.value where pack.product_id=" + product_id + " and pack.level=" + pack_size + " limit 1";
                 
                db.query(uomqry, {}, async function (err, rows) {
                    if (err) {
                        reject('error');
                    }
                    if (Object.keys(rows).length > 0) {
                        qtycheck[index].selectedpack = rows;
                        resolve(qtycheck);
                    } else {
                        qtycheck[index].selectedpack = [];
                        resolve(qtycheck);
                    }
                });
            }catch (err) {
                      console.log(err)
            }
        })
    },

    getCSV: async (fromDate, toDate) => {
        try {
            let query = `CALL getGRNReport('${fromDate}','${toDate}')`;
            let response = await new Promise((resolve, reject) => db.query(query, {}, (err, rows) => {
                if (err) {
                    reject(err);
                }
                if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve(0);
                }
            }

            ))

            // console.log('response', response);
            return response;


        } catch (err) {
            console.log("getCSV error", err);
            return "";
        }
    },

    getPOHistory: async (poCode) => {
        try {
            return new Promise(async (resolve, reject) => {
                let totalHistory;
                let history;
                let value;
                let poId;

                //get the poId from poCode
                let poQuery = `SELECT po_id FROM po WHERE po_code = '${poCode}';`
                let result = await sequelize.query(poQuery, { type: Sequelize.QueryTypes.SELECT })
                poId = result[0].po_id;

                //getting approvalHistory from appr_comments table
                //get value dynamically (value = 56015)
                let query = `SELECT value FROM master_lookup WHERE master_lookup_name = 'Purchase Order'  AND mas_cat_id = 56;`
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT })
                    .then(response => {
                        value = response[0].value;
                    })
                    //get comments from appr_comments table
                    .then(() => {
                        let query1 = `SELECT comments FROM appr_comments WHERE comments_id = ${poId} AND awf_for_type_id = ${value};`
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
                                  WHERE hs.awf_for_id = ${poId} 
                                  AND hs.awf_for_type = 'Purchase Order' 
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
            console.lo("getApprovalHistory Error", err);
            reject(err);
        }
    },

    getReturnQtyByInwardId:async function(inwardId){
        return new Promise((resolve, reject) => {
            let query="select SUM(returns.pr_total_qty) as totReturnQty from `purchase_returns` as `returns` where `returns`.`inward_id` = "+inwardId+" limit 1";
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].totReturnQty);
                } else {
                    return resolve([]);
                }
            });
        });
    },

    getProductReturnQty:async function(inwardId,productId){
        return new Promise((resolve, reject) => {
            let query="select SUM(reurnpr.qty) as ret_soh_qty, SUM(reurnpr.dit_qty) as ret_dit_qty, SUM(reurnpr.dnd_qty) as ret_dnd_qty from `purchase_returns` as `returns` inner join `purchase_return_products` as `reurnpr` on `returns`.`pr_id` = `reurnpr`.`pr_id` where `returns`.`inward_id` = "+inwardId+" and `reurnpr`.`product_id` = "+productId+" limit 1";
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
    getProductPackInfo :async function (product_id,index) {
        try {
            return new Promise((resolve, reject) => {
                let packs = "select `products`.`seller_sku`, `products`.`upc`, `pack`.`pack_id`, `pack`.`level`, `pack`.`no_of_eaches`, `pack`.`pack_sku_code`, `lookup`.`master_lookup_name` as `packname`, `products`.`mrp` from `products` inner join `product_pack_config` as `pack` on `pack`.`product_id` = `products`.`product_id` inner join `master_lookup` as `lookup` on `pack`.`level` = `lookup`.`value` where `pack`.`no_of_eaches` > ? and `products`.`product_id` = ? order by `lookup`.`sort_order` desc, `pack`.`effective_date` desc";
                db.query(packs, [0, product_id], function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        qtycheck[index].packConfig = rows;
                        resolve(qtycheck)
                    } else {
                        qtycheck[index].packConfig = [];
                    }
                })
            })

        } catch (err) {
            console.log(err)
        }

    },

    getInvoiceByReturn :async function(data,url){
        return new Promise((resolve, reject) => {
            var rp = require('request-promise');
            var options = {
                method: 'POST',
                uri: url,
                body: {'data':data},
                json: true
            };
            console.log(options);
            rp(options).then(function (parsedBody) {
                console.log('psssssss',parsedBody);
                 resolve(parsedBody);
            })
        });
    },

    checkPODeliverStatus:async function(order_code){
        return new Promise((resolve, reject) => {
            let query="select gds_order_id from gds_orders where order_status_id = 17021 and order_code = '"+order_code+"' limit 1";
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].gds_order_id);
                } else {
                    return resolve("");
                }
            });
        });
    },    

    getGrnProductDetails :async function (grnId) {
        return new Promise((resolve, reject) => {
            let query = "select `products`.`mrp`, `products`.`kvi`, `products`.`sku`, `products`.`product_title`, `po_products`.`tax_per`, `po_products`.`tax_name`, `po_products`.`qty`, `po_products`.`uom`, `master_lookup`.`master_lookup_name` as `uomName`, `po_products`.`no_of_eaches`, (po_products.no_of_eaches * po_products.qty) AS actual_po_quantity, `po_products`.`free_qty`, `po_products`.`free_uom`, `po_products`.`free_eaches`, `po_products`.`hsn_code` as `hsn`, `po_products`.`tax_data` as `po_tax_data`,po_products.`apply_discount`,po_products.`discount_type`,po_products.`discount`, `inward_products`.* from `inward_products` left join `inward` on `inward`.`inward_id` = `inward_products`.`inward_id` left join `po` on `po`.`po_id` = `inward`.`po_no` left join `po_products` on `po_products`.`po_id` = `inward`.`po_no` and `po_products`.`product_id` = `inward_products`.`product_id` left join `products` on `products`.`product_id` = `inward_products`.`product_id` left join `master_lookup` on `master_lookup`.`value` = `po_products`.`uom` where `inward`.`inward_id` = "+grnId+" group by `inward_products`.`product_id`";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows)
                } else {
                    return resolve([]);
                }
            });
        });
    },

    checkProductProp :async function (product_id,le_wh_id) {
        return new Promise((resolve, reject) => {
            let query = "select count(product_id) as count from products where product_id="+product_id+" and is_sellable=1 and cp_enabled=1";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].count)
                } else {
                    return resolve([]);
                }
            });
        });
    },

    getFreebieParent :async function (product_id) {
        return new Promise((resolve, reject) => {
            let query = "select free_conf_id,main_prd_id,free_prd_id from freebee_conf  where free_prd_id= "+product_id+" order by created_at desc limit 1";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0])
                } else {
                    return resolve([]);
                }
            });
        });
    },

    isSellable :async function (product_id,le_wh_id,user_id,date) {
        return new Promise(async(resolve, reject) => {
            if(le_wh_id!=0){    
                var check_warehouseproductid=await this.getProductIsSellableStatusByWarehouse(productid,le_wh_id,user_id,date);
                var query="select display_name from legalentity_warehouses where le_wh_id="+le_wh_id+" and status=1";
                db.query(query, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else {
                        var display_name="";
                        if (Object.keys(rows).length > 0) 
                        display_name=rows[0].display_name;
                        if(check_warehouseproductid){
                            var insert_warehouseproductissellable='Is Sellable Status Updated Successfully for '+ display_name;
                        }else{
                            var insert_warehouseproductissellable='Failed to Update Is Sellable Status for '+ display_name;
                        }
                    }
                });
            }else{
                var params={'permissionLevelId':'6','user_id':user_id};
                var filterData=await role.getFilterData(params);
                if(filterData[0].hasOwnProperty('sbu')){
                    var filters=data2[0].sbu;
                    var dc_acess_list=filters[118001];
                    var getallactivedcs="select le_wh_id from legalentity_warehouses where status=1 and le_wh_id IN '"+dc_acess_list+"' ";
                    db.query(query, function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject(err);
                        } else if (Object.keys(rows).length > 0) {
                            var success=0;
                            var failed=0;
                            // for(const dcs of getallactivedcs){
                            //     var check_warehouseproductid=await this.getProductIsSellableStatusByWarehouse(productid,dcs.le_wh_id,user_id,date);
                            //     if(check_warehouseproductid){
                            //             success=success+1;
                            //     }else{
                            //         failed=failed+1;
                            //     } 

                            // }
                            var insert_warehouseproductissellable="For "+ success +" dcs Sellable status  Updated Successfully and for "+failed+" dcs failed to update Sellable status for not having tax/pricing/sellable";  
                        }
                    });   
                }
            }
            return resolve(insert_warehouseproductissellable);
        });
    },

    getProductSU :async function (product_id) {
        return new Promise((resolve, reject) => {
            let query = "select esu from products  where product_id= "+product_id+" limit 1";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0])
                } else {
                    return resolve([]);
                }
            });
        });
    },

    getProductIsSellableStatusByWarehouse :async function (product_id,le_wh_id,user_id,date) {
        return new Promise((resolve, reject) => {
            let query = "select product_cpenabled_dcfc_id from product_cpenabled_dcfcwise where product_id="+productid+" and le_wh_id="+le_wh_id+" ";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    var query1 = "update product_cpenabled_dcfcwise set is_sellable=1 , updated_by="+user_id+" , updated_at='"+date+"' where product_id="+product_id+" and le_wh_id="+le_wh_id+"";
                     console.log(query1);
                     db.query(query1, {}, function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject('error');
                        }
                        if (rows> 0) {
                            return resolve(true);
                        } else {
                            return resolve(false);
                        }
                    }); 
                } else {
                    var query2 = "insert into product_cpenabled_dcfcwise (product_id,le_wh_id,is_sellable,created_by,created_at) VALUES ("+product_id+","+le_wh_id+",1,"+user_id+",'"+date+"')";
                     console.log(query2);
                     db.query(query2, {}, function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject('error');
                        }
                        if (rows> 0) {
                            return resolve(true);
                        } else {
                            return resolve(false);
                        }
                    }); 
                }
            });
        });
    },

    checkWhProductId :async function (product_id,le_wh_id) {
        return new Promise((resolve, reject) => {
            let query = "select count(*) as aggregate from `product_cpenabled_dcfcwise` where (`product_id` = "+product_id+" and `le_wh_id` = "+le_wh_id+") and (`esu` = 0 or `esu` is null)";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].aggregate)
                } else {
                    return resolve(0);
                }
            });
        });
    },

    updateProductEsu :async function (product_id,le_wh_id,esu,date) {
        return new Promise((resolve, reject) => {
            var query = "update product_cpenabled_dcfcwise set esu="+esu+" , updated_at='"+date+"' where product_id="+product_id+" and le_wh_id="+le_wh_id+"";
                console.log(query);
                db.query(query, {}, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject('error');
                    }
                    if (rows> 0) {
                        return resolve(1);
                    } else {
                        return resolve(0);
                    }
                }); 
        });
    },

    cpEnabled :async function (product_id,le_wh_id,user_id,date) {
        return new Promise(async(resolve, reject) => {
            if(le_wh_id!=0){
                var pricing = await this.productPricing(productid,user_id,le_wh_id);
                var tax = await this.productTaxModelByWarehouse(productid,le_wh_id);
                var sellableprdt= await this.isSellableProductByDcid(product_id,le_wh_id);
                if(pricing=='' || tax=='' || sellableprdt[0].is_sellable=='0' || sellableprdt=='' || sellableprdt[0].is_sellable==''){
                    return resolve('Tax/Pricing/is Sellable  not available for this product');
                    return;
                }
                var insert_warehouseproduct=await this.getProductCPStatusByWarehouse(productid,le_wh_id,date);
                var query="select display_name from legalentity_warehouses where le_wh_id="+le_wh_id+" and status=1";
                db.query(query, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else {
                        var display_name="";
                        if (Object.keys(rows).length > 0) 
                        display_name=rows[0].display_name;
                        if(insert_warehouseproduct){
                            insert_warehouseproduct='CP Enable Status Updated Successfully for '+ display_name;
                        }else{
                            insert_warehouseproduct='Failed to Update CP Enable Status for '+ display_name;
                        }
                    }
                });

            }else{
                var params={'permissionLevelId':'6','user_id':user_id};
                var filterData=await role.getFilterData(params);
                if(filterData[0].hasOwnProperty('sbu')){
                    var filters=data2[0].sbu;
                    var dc_acess_list=filters[118001];
                    var getallactivedcs="select le_wh_id from legalentity_warehouses where status=1 and le_wh_id IN '"+dc_acess_list+"' ";
                    db.query(query, async function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject(err);
                        } else if (Object.keys(rows).length > 0) {
                            var success=0;
                            var failed=0;
                            for(const dcs of getallactivedcs){
                                var insert_warehouseproduct='';
                                var pricing = await this.productPricing(productid,user_id,dcs.le_wh_id);
                                var tax = await this.productTaxModelByWarehouse(productid,dcs.le_wh_id);
                                le_wh_id= dcs.le_wh_id;
                                var sellableprdt= await this.isSellableProductByDcid(product_id,le_wh_id);
                                if((pricing!='' && tax!='' && sellableprdt[0].is_sellable!='0' && !(sellableprdt=='') && sellableprdt[0].is_sellable!='')){
                                    insert_warehouseproduct=await this.getProductCPStatusByWarehouse(productid,le_wh_id,date);
                                    if(insert_warehouseproduct){
                                        success=success+1;
                                    }else{
                                        failed=failed+1;
                                    }
                                }else{
                                    failed=failed+1;
                               }
                            }
                            insert_warehouseproduct="For "+success+" dcs CP status  Updated Successfully and for "+failed+" dcs failed to update CP status for not having tax/pricing/sellable";
                        }
                    });
                }                       
           }
           return resolve(insert_warehouseproduct);   
        });
    },

    productPricing :async function (product_id,user_id,le_wh_id) {
        return new Promise((resolve, reject) => {
            let query = "select price from product_prices where product_id= "+product_id+" and dc_id="+le_wh_id+" ";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].price)
                } else {
                    return resolve('');
                }
            });
        });
    },
     
    productTaxModelByWarehouse :async function (product_id,le_wh_id) {
        return new Promise((resolve, reject) => {
            let query = "select getProductTaxByWarehouse('"+product_id+"','"+le_wh_id+"') as taxcount ";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].taxcount)
                } else {
                    return resolve(0);
                }
            });
        });
    },

    isSellableProductByDcid :async function (product_id,le_wh_id) {
        return new Promise((resolve, reject) => {
            let query = "select is_sellable from product_cpenabled_dcfcwise where product_id= "+product_id+" and le_wh_id="+le_wh_id+" ";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    return resolve(rows)
                } else {
                    rows[0]['is_sellable']=='';
                    return resolve(rows);
                }
            });
        });
    },

    getProductCPStatusByWarehouse :async function (product_id,le_wh_id,date) {
        return new Promise((resolve, reject) => {
            let query = "select product_cpenabled_dcfc_id from product_cpenabled_dcfcwise where product_id= "+product_id+" and le_wh_id="+le_wh_id+" ";
            db.query(query, function (err, rows) {
                if (err) {
                    console.log(err);
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    var query1 = "update product_cpenabled_dcfcwise set cp_enabled=1 , updated_by="+user_id+" , updated_at='"+date+"' where product_id="+product_id+" and le_wh_id="+le_wh_id+"";
                     console.log(query1);
                     db.query(query1, {}, function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject('error');
                        }
                        if (rows> 0) {
                            return resolve(true);
                        } else {
                            return resolve(false);
                        }
                    }); 
                } else {
                    var query2 = "insert into product_cpenabled_dcfcwise (product_id,le_wh_id,cp_enabled,created_by,created_at) VALUES ("+product_id+","+le_wh_id+",1,"+user_id+",'"+date+"')";
                     console.log(query2);
                     db.query(query2, {}, function (err, rows) {
                        if (err) {
                            console.log(err);
                            reject('error');
                        }
                        if (rows> 0) {
                            return resolve(true);
                        } else {
                            return resolve(false);
                        }
                    }); 
                }
            });
        });
    },

    getProductShelfLife: function (product_id, index) {
        return new Promise(async (resolve, reject) => {
                let uomqry = "select products.shelf_life,products.shelf_life_uom,lookup.master_lookup_name from products left join master_lookup as lookup on lookup.value=products.shelf_life_uom where products.product_id=" + product_id + " limit 1";
                db.query(uomqry, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        qtycheck[index].grnprdInfo = rows;
                    } else {
                        qtycheck[index].grnprdInfo = [];
                    }
                    resolve(qtycheck);
                });
        });
    },

    getMfgDate: function (product_id, index,poid) {
        return new Promise(async (resolve, reject) => {
            try {
                var today = new Date();
                var Currentdate = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                let po_so_code = "select po_so_order_code from po where po_id in ("+poid+") limit 1";
                db.query(po_so_code, async function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        po_so_code = rows[0].po_so_order_code;//rows[0].hasOwnProperty('po_so_order_code')?rows[0].po_so_order_code:'';
                        if(po_so_code==''){
                            
                            //resolve(Currentdate);
                            qtycheck[index].mfg_date = Currentdate;
                            resolve(qtycheck)
                        }else{
                            console.log(po_so_code);
                            var gds_main_batch_id = await module.exports.getMainBatchID(po_so_code,product_id);
                        console.log(gds_main_batch_id,'po_so_codepo_so_code');
                            
                            if(gds_main_batch_id!=''){
                                var inwrdprdDtlsmfgdate = await module.exports.getMnfDateByBatchId(gds_main_batch_id,product_id);
                                console.log('aaaaaaaaaaaaaaaaaaaaaaabbbbbbbbb');
                                qtycheck[index].mfg_date = inwrdprdDtlsmfgdate;
                              //  resolve(inwrdprdDtlsmfgdate);
                            }
                            resolve(qtycheck)
                        }
                    } else {
                        qtycheck[index].mfg_date = Currentdate;                   
                        resolve(qtycheck)
                    }
                })
            } catch (err) {
                console.log(err);
                reject(err);
            }
        });
    },

    getMainBatchID : async function(po_so_order_code,product_id){
        return new Promise((resolve,reject)=>{
            var qry = "select gob.main_batch_id from gds_orders as go join gds_orders_batch as gob on gob.gds_order_id=go.gds_order_id where order_code='"+po_so_order_code+"' and gob.product_id="+product_id+" limit 1" ;
            db.query(qry, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        console.log('rows',rows);
                        resolve(rows[0].main_batch_id);
                    } else {
                        resolve('');
                    }
            })
        })
    },

    getMnfDateByBatchId : async function(gds_main_batch_id,product_id){
        return new Promise((resolve,reject)=>{
            var qry = "select inwpd.mfg_date from inward_product_details as inwpd join inward_products as inwp on inwp.inward_prd_id=inwpd.inward_prd_id where inwp.inward_id='"+gds_main_batch_id+"' and inwp.product_id="+product_id+" limit 1";
            db.query(qry, function (err, rows) {
                    if (err) {
                        console.log(err);
                        reject(err);
                    } else if (Object.keys(rows).length > 0) {
                        console.log(rows[0].mfg_date,'12222222222222222222');
                        var datemfg=new Date(rows[0].mfg_date);
                        var getutcdate=datemfg.getUTCDate();
                        var getutcmonth=datemfg.getUTCMonth()+1;
                        var getutcyear=datemfg.getFullYear();
                        var fulldate=getutcyear+'-'+getutcmonth+'-'+getutcdate;
                        resolve(fulldate);
                    } else {
                        var today = new Date();
                        var Currentdate = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                        resolve(Currentdate);
                    }
            })
        })
    },

    getProductPackStatus:async function(){
        return new Promise((resolve,reject)=>{
            var qry = "select value,master_lookup_name from master_lookup where mas_cat_id=91";
            db.query(qry, function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                }else{
                    resolve(0);
                }
            });
        });
    },
    
    getPOProductTaxData:async function(po_id,product_id){
        return new Promise((resolve,reject)=>{
            var query = "select tax_data,hsn_code from po_products where po_id="+po_id+" and product_id="+product_id+" limit 1";
            db.query(query, function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows[0]);
                }else{
                    resolve(0);
                }
            });
        });
    }
}
        