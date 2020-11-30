const Sequelize = require('sequelize');
const sequelize = require('../../config/sequelize');
var database = require('../../config/mysqldb');
var role = require('../model/Role');
let db = database.DB;
var qtycheck = [];
var http = require('http');
var url = process.env.APP_TAXAPI;

module.exports = {

    getLEWHById: async function (le_wh_id) {
        return new Promise((resolve, reject) => {
            //try {
                let query = `SELECT warehouses.le_wh_id,warehouses.legal_entity_id,warehouses.lp_wh_name,warehouses.address1,warehouses.address2,warehouses.city,
                warehouses.pincode,warehouses.phone_no,warehouses.email,warehouses.credit_limit_check,warehouses.state AS state_id,countries.name AS country,
                zone.name AS state,zone.code AS state_code FROM legalentity_warehouses AS warehouses
                JOIN legal_entities AS legal ON warehouses.legal_entity_id = legal.legal_entity_id 
                LEFT JOIN countries ON countries.country_id = warehouses.country
                LEFT JOIN zone ON zone.zone_id = warehouses.state WHERE warehouses.le_wh_id= ${le_wh_id};`

                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    if(response.length > 0 && typeof response[0] != 'undefined') {
                       resolve(response[0]);
                   }else{
                    resolve(0);
                   }
                    // console.log(response);
                })
            // } catch (err) {
            //     reject(err);
            // }
        })
    },

    getOrderIdByCode: async function(order_code) {
        return new Promise (async(resolve, reject ) => {
            query = `SELECT gds_order_id FROM gds_orders WHERE order_code = '${order_code}' ORDER BY created_at DESC;`
            let response=await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
            if (response.length > 0) {
                resolve(response[0].gds_order_id);
            } else {
                resolve(0);
            }
        })
    },

    /*getPoDetailById : async (poid,warehouse, orderby,) => {
            try {

                let query = `SELECT description FROM master_lookup WHERE VALUE = 78023;`

                let globalSupplier = await sequelize.query(query, {type : Sequelize.QueryTypes.SELECT});

                globalSupplier = globalSupplier[0].description;
                let globalSupplierId = (typeof globalSupplier != 'undefined' && globalSupplier !='') ? globalSupplier : 'NULL';
              
                // console.log('wokkkkkk looo',globalSupplierId);
                let legal_entity_id = await module.exports.getLegalEntityId(warehouse);
                legal_entity_id = legal_entity_id[0].legal_entity_id;

                let is_Stockist = await module.exports.checkStockist(legal_entity_id);
                let dc_le_id_list = await module.exports.getAllDCLeids();
                console.log('sssssssss',dc_le_id_list);
                
              

                return globalSupplierId;
            } catch (err){
                
            }
    },*/

    getLegalEntityId : async(le_wh_id) => {
        //try{

            let query = `SELECT legal_entities.legal_entity_id FROM legal_entities LEFT JOIN legalentity_warehouses ON 
            legal_entities.legal_entity_id = legalentity_warehouses.legal_entity_id
            WHERE legalentity_warehouses.le_wh_id = ${le_wh_id} limit 1;`
            let legal_entity_id = await sequelize.query(query, {type: Sequelize.QueryTypes.SELECT});
            console.log('wokkkkkk')

            return legal_entity_id;
        // } catch{

        // }
    },

    checkStockist : async ( le_wh_id ) => {
        //try {
            let query = `SELECT * FROM legal_entities WHERE legal_entity_type_id IN (1014,1016) AND business_type_id = 47001 AND legal_entity_id = ${le_wh_id};`

            let count = await sequelize.query(query, { type : Sequelize.QueryTypes.SELECT});

            count = count.length;

            return count;

        // } catch{

        // }
    },

    getAllDCLeids : async () => {
        //try {

            // console.log('legal_entity_id,')
            let query = `SELECT GROUP_CONCAT(legal_entity_id) AS dc_le_id_list FROM legal_entities WHERE legal_entity_type_id =1016 LIMIT 1;`

            let legal_entity_id = await sequelize.query(query, {type : Sequelize.QueryTypes.SELECT});
            // dc_le_id_list = dc_le_id_list.dc_le_id_list.length;
            console.log('checkkkk',legal_entity_id[0].hasOwnProperty('dc_le_id_list'));
            let dc_le_id_list = `${legal_entity_id[0].dc_le_id_list}`;
            if(dc_le_id_list){
            // console.log('whats here', dc_le_id_list);
            }

            // console.log('legal_entity_id,', dc_le_id_list.length);
            // return dc_le_id_list.length > 0 ? dc_le_id_list : 'NULL';
            return dc_le_id_list.length > 0 ?  `'NULL'` :dc_le_id_list;
        // } catch{
    },

    getCustomerDataByNo: async(mobile_number)=>{
      try {

            let query = "SELECT password_token,user_id,email_id,mobile_no,firstname,lastname FROM users WHERE mobile_no in ('"+mobile_number+"') and is_active=1 LIMIT 1";

            let userdata = await sequelize.query(query, {type : Sequelize.QueryTypes.SELECT});
            //console.log(userdata);
            return userdata;
        } catch{

        }  
    },

    getAllPurchaseReturns:async function(rowCnt = 0, offset, perPage, filter, sortField = 'pr.pr_id', sortType = 'desc',user_id){
        return new Promise(async(resolve, reject) => {
            var params={'permissionLevelId':'6','user_id':user_id};
            var data2=await role.getFilterData(params);
            var dc_acess_list='';
            if(data2[0].hasOwnProperty('sbu')){
              var filters=data2[0].sbu;
              dc_acess_list=filters[118001];
            }
            var query=` select pr.le_wh_id, pr.legal_entity_id, pr.inward_id,inward.inward_code, pr.pr_id,pr.pr_code,pr.pr_status,pr.sr_invoice_code, pr.approval_status, IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name, GetUserName(pr.picker_id,2) AS picker_name, pr.created_at, pr.pr_grand_total as prValue, GetUserName(pr.created_by,2) AS user_name, legal_entities.business_legal_name,lwh.lp_wh_name,lwh.city, lwh.pincode, lwh.address1 from purchase_returns as pr left join legal_entities on legal_entities.legal_entity_id = pr.legal_entity_id left join inward on inward.inward_id = pr.inward_id left join legalentity_warehouses as lwh on lwh.le_wh_id = pr.le_wh_id where pr.le_wh_id in (${dc_acess_list})`;

            if (Object.keys(filter).length > 0) {
                if (filter['Status'] != null && filter['Status'] != "") {
                    query += ` AND IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) LIKE '%${filter['Status'][0]}%' `
                }
                if (filter['pr_code'] != null && filter['pr_code'] != "") {
                    query += `AND pr.pr_code LIKE '%${filter['pr_code'][0]}%' `
                }
                if (filter['sr_invoice_code'] != null && filter['sr_invoice_code'] != "") {
                    query += `AND pr.sr_invoice_code LIKE '%${filter['sr_invoice_code'][0]}%' `
                }
                if (filter['inwardCode'] != null && filter['inwardCode'] != "") {
                    query += `AND inward.inward_code LIKE '%${filter['inwardCode'][0]}%' `
                }
                if (filter['Supplier'] != null && filter['Supplier'] != "") {
                    query += `AND legal_entities.business_legal_name LIKE '%${filter['Supplier'][0]}%' `
                }
                if (filter['picker_name'] != null && filter['picker_name'] != "") {
                    query += ` AND GetUserName(pr.picker_id,2) LIKE '%${filter['picker_name'][0]}%' `
                }
                if (filter['shipTo'] != null && filter['shipTo'] != "") {
                    query += `  AND lwh.lp_wh_name LIKE '%${filter['shipTo'][0]}%' `
                }
                if (filter['prValue'] != null && filter['prValue'] != "") {
                    query += `  AND ROUND(pr.pr_grand_total,2) ${filter['prValue'][1]} '%${filter['prValue'][0]}%' `
                }
                if (filter['createdBy'] != null && filter['createdBy'] != "") {
                    query += `  AND GetUserName(pr.created_by,2) LIKE '%${filter['createdBy'][0]}%' `
                }
                if (filter['created_at'] != null && filter['created_at'] != "") {
                    query += `  AND pr.created_at ${filter['created_at'][1]} '%${filter['created_at'][0]}%' `
                }
            }
            if (rowCnt == 1) {
                query += ` order by pr.pr_id desc`;
                let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT });
                return resolve(response.length);
            } else {
                query += ` order by ${sortField} ${sortType} limit ${perPage} offset ${offset}`;
                let response = await sequelize.query(query, { type: Sequelize.QueryTypes.SELECT })
                if (response.length > 0) {
                    return resolve(response);
                } else {
                    return resolve('noData');
                };
            }
        });
    },

    getUserByLeId :async function (leId) {
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
    },

    checkInvoiceByInwardId:async function(id){
        return new Promise((resolve,reject)=>{
            var query="select count(po_invoice_grid_id) as count from po_invoice_grid where inward_id ="+id+"";
            db.query(query,{},(err,rows)=>{
                if (err) {
                        reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].count);
                } else {
                    return resolve(0);
                }
            });
        });
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
                            dc_le_id_list = (dc_le_id_list[0].hasOwnProperty('dc_le_id_list'))?dc_le_id_list[0].dc_le_id_list:'';
                            //console.log("dc_le_id_listhhhhhh",dc_le_id_list);
                            if(dc_le_id_list!=''){
                                globalSupperLierId = globalSupperLierId + "," + dc_le_id_list;
                            }else{
                                globalSupperLierId = globalSupperLierId;
                            }
                            console.log("glbbbb",globalSupperLierId);
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
                 WHERE      po.po_id = ${po_id} 
                 ORDER BY   parent_id ASC`;

                            //  console.log(query);
                            // db.query(query, [po_id], function (err, po) {
                            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                                // console.log("po", po);
                                if (err) {
                                    console.log(err);
                                    reject(err);
                                } else {
                                    // console.log('po',response);
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


// module.exports.checkLOCByLeWhid = function (dcid) {
//     try {
//         return new Promise((resolve, reject) => {
//             // change below query to procedure
//             let query = "SELECT  vp.`Order_Limit`  FROM `vw_stockist_payment_details` vp WHERE vp.`le_wh_id` = ? ";
//             db.query(query, [dcid], function (err, rows) {
//                 // console.log("rowsss", rows);
//                 if (err) {
//                     reject(err);
//                 } else if (Object.keys(rows).length > 0) {
//                     let checkqry = rows[0].hasOwnProperty('Order_Limit') ? rows[0].Order_Limit : 0;
//                     resolve(checkqry);
//                 } else {
//                     resolve("");
//                 }
//             })
//         })
//     } catch (err) {
//         console.log(err)
//     }
// }

   module.exports.checkLOCByLeWhid =async function (dcid) {
        return new Promise((resolve, reject) => {
            let query = "SELECT  legal_entity_id  FROM legalentity_warehouses WHERE le_wh_id = "+dcid+" limit 1";
            db.query(query, function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    var leid=rows[0].legal_entity_id;
                    var query1="CALL get_StockistPaymentDetails("+leid+")";
                    db.query(query1, function (err, rows) {
                        if (Object.keys(rows).length > 0) {
                            let checkqry = rows[0][0].hasOwnProperty('Order_Limit') ? rows[0][0].Order_Limit : 0;
                            resolve(checkqry);
                        } else {
                            resolve("");
                        }
                    });
                } else {
                    resolve("");
                }
            })
        });
}

module.exports.getWarehouseById = function (dcleid) {
    try {
        return new Promise((resolve, reject) => {
            let query = "select `warehouse`.*, `countries`.`name` as `country_name`, `zone`.`name` as `state_name`, `zone`.`code` as `state_code`,`legal_entities`.`legal_entity_type_id`, `legal_entities`.`business_legal_name`, `warehouse`.`tin_number` as `gstin` from `legalentity_warehouses` as `warehouse` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `warehouse`.`legal_entity_id` left join `countries` on `countries`.`country_id` = `warehouse`.`country` left join `zone` on `zone`.`zone_id` = `warehouse`.`state` where `warehouse`.`le_wh_id` = ? limit 1";
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

module.exports.ProdSlabFlatRefreshByProductId = function(productid,warehouse){
    try{
        return new Promise((resolve,reject)=> {
            let productslabsquery = "CALL ProdSlabFlatRefreshByProductId('"+productid+"','"+warehouse+"')";
            sequelize.query(productslabsquery).then(result => {
                 if (result != '' && result > 0) {
                      resolve(result);
                 } else {
                      resolve(result);
                 }
            }).catch(err => {
                console.log(err);
                 reject(err);
            })
        })
    }catch (err) {
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

module.exports.getProductSlabsByCust = function(productid,warehouse,userid,customer_type_id){
    try{
        return new Promise((resolve,reject)=> {
            let productslabsquery = "CALL getProductSlabsByCust('"+productid+"','"+warehouse+"','"+userid+"','"+customer_type_id+"')";
            //console.log(productslabsquery);
            sequelize.query(productslabsquery).then(result => {
                 if (result != '' && result.length > 0) {
                      resolve(result);
                 } else {
                      resolve(result);
                 }
            }).catch(err => {
                console.log(err);
                 reject(err);
            })
        })
    }catch (err) {
          console.log(err)
    }
}

module.exports.isFreebie = function(productid){
        return new Promise((resolve,reject)=> {
            let checkfreebee = "select count(free_conf_id) as count from freebee_conf where free_prd_id ="+productid+" limit 1";
            //sequelize.query(checkfreebee).then(result => {
            sequelize.query(checkfreebee, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                console.log(result,'resultresultresultresultresult');
                 if (result != '' && result.length > 0) {
                      resolve(result[0].count);
                 } else {
                      resolve(0);
                 }
            });
        })
}

module.exports.getPoQtyByPoId = function(poId){
     try{
        return new Promise((resolve,reject)=> {
            let checkfreebee = "select SUM(product.qty * product.no_of_eaches) as totQty from po join po_products as product on product.po_id=po.po_id where po.po_id ="+poId+" limit 1";
            //sequelize.query(checkfreebee).then(result => {
            sequelize.query(checkfreebee, { type: Sequelize.QueryTypes.SELECT }).then(result => {
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
        })
    }catch (err) {
          console.log(err)
    }
}


module.exports.getCPEnableData = function(product_id,le_wh_id){
    try{
         return new Promise((resolve,reject)=> {
            let cpenableqry = "select product_id,le_wh_id,cp_enabled,is_sellable from product_cpenabled_dcfcwise where product_id ="+product_id+" and le_wh_id="+le_wh_id+" group by product_id,le_wh_id order by updated_at desc  limit 1";
            //sequelize.query(cpenableqry).then(result => {
              sequelize.query(cpenableqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                 if (result != '' && result.length > 0) {
                      resolve(result[0]);
                 } else {
                      resolve([]);
                 }
            }).catch(err => {
                console.log(err);
                 reject(err);
            })
        })
    }catch (err) {
          console.log(err)
    }
}

module.exports.getMasterLokup = function(masterlookupvalue){
    return new Promise((resolve,reject)=> {
        let masterlookupqry = "select master_lookup_id,description,value from master_lookup where value ="+masterlookupvalue+" limit 1";
        db.query(masterlookupqry,{}, function (err, rows) {
             if (Object.keys(rows).length > 0) {
                  resolve(rows[0]);
             } else {
                  resolve('');
             }
        });
    });
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

module.exports.updatePO = async function (poId, podata) {
    return new Promise((resolve, reject) => {
        var dictionary = '';
        for (var key in podata) {
            
            dictionary += key + "='" + podata[key] + "',"
        }
        var lastChar = dictionary.slice(-1);
        if (lastChar == ',') {
            dictionary = dictionary.slice(0, -1);
        }
        var updatePoPrd = "UPDATE po SET " + dictionary + " WHERE po_id=" + poId;
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

module.exports.getWHByLEId= async function (le_id) {
        return new Promise((resolve, reject) => {
            try {
                let query = "select le_wh_id from legalentity_warehouses WHERE legal_entity_id= "+le_id+" limit 1"

                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                    resolve(response);
                    // console.log(response);
                })
            } catch (err) {
                reject(err);
            }
        })
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
                resolve(response);
            })
        })

    } catch (err) {
        console.log(err)
    }
}

module.exports.getTokenByUserId = function(userId){
    return new Promise((resolve,reject)=>{
        try{
            var tokenqry = "select password_token from users where user_id="+userId+" and is_active =1";
            sequelize.query(tokenqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
            if (result != '' && result.length > 0) {
                      resolve(result[0].password_token);
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
}

module.exports.checkLOCByLeID = function(le_id){
    return new Promise((resolve,reject)=>{
        try{
            var checklocbyleidqry=" CALL get_StockistPaymentDetails("+le_id+")";
            sequelize.query(checklocbyleidqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                          resolve(result[0].Order_Limit);
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
}

module.exports.checkChildPoExist = function(poId,parent_po_code=""){
    return new Promise((resolve,reject)=>{
        try{
            var checkchildpo ="select COUNT(po.po_id) as count from po where po.parent_id="+poId;

            if(parent_po_code!=""){
                checkchildpo += " po_code NOT LIKE %"+parent_po_code+"%";
            }
            checkchildpo += " limit 1";
            sequelize.query(checkchildpo, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                          resolve(result[0].count);
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
}

module.exports.savePOInvoice = function(arr){
    return new Promise((resolve,reject)=>{
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
        var query = "insert into po_invoice_grid "+dictionary+" ";
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
    });
}

module.exports.savePOInvoiceProducts = function(arr){
    return new Promise((resolve,reject)=>{
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
        var query = "insert into po_invoice_products "+dictionary+" ";
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
    });
}