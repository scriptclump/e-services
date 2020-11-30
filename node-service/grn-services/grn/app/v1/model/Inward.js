'user strict';

const Sequelize = require('sequelize');
var sequelize = require('../../config/sequelize');
var database = require('../../config/mysqldb');
var role = require('../model/Role');
let db = database.DB;


module.exports = {
    getInwardCountByStatus: async function (req, res) {
        return new Promise(async (resolve, reject) => {
            /**
             * count through procedure
             */
            let params = { 'permissionLevelId': '6', 'user_id': req.user_id };
            let filters;
            let dc_access_list;
            let userdata;
            let user_id = params.user_id;
            let flag = 0;


            let filterData = await role.getFilterData(params);
            if (filterData[0].hasOwnProperty('sbu')) {
                filters = filterData[0].sbu;
                dc_access_list = filters[118001];
            }

            userdata = await role.checkUserIsSupplier(user_id);
            if (userdata > 0) {
                let brands = await role.getAllAccessBrands(user_id);
                dc_access_list = brands.join();
                flag=1;

            }

            let grnCount = `CALL getGrnGridCount('${dc_access_list}','${flag}')`

            db.query(grnCount,{},async function(err6,response) {
                if(err6) {
                    reject('error')
                }
                if(response.length > 0) resolve(response[0]);
                else resolve(0);
            })
        });
    },

    getAllInward: async (filterBy, filter, count, page, pageSize, orderbyarray, user_id, legal_entity_id) => {
        return new Promise((resolve, reject) => {
            try {
                // console.log("coming from inwaraaaaaad"+filterBy,'\n', filter,'\n', count,'\n', page,'\n', pageSize,'\n', orderbyarray)
                var inwardlistparams = { 'filter': filter, 'count': count, 'user_id': user_id };
                // console.log('vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv');
                module.exports.getInwardGridList(inwardlistparams).then(data => {

                    data += ` ORDER BY inward_code DESC LIMIT ${pageSize} OFFSET ${page * pageSize} `
                    // console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa      '+data);
                    db.query(data, {}, (err, res) => {
                        if (err) {
                            console.log(err);
                            reject(err);
                        }
                        if (res.length > 0) {
                            // console.log('responseeeeeeee', res);
                            console.log(res.length, 'no. of responses ssssssssssss')
                            resolve(res);
                        }
                    })
                });
                // console.log('eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee');
                // console.log("query not coming",query);
            } catch (err) {

            }
        })
    },

    getInwardGridList: async (req, res) => {
        return new Promise(async (resolve, reject) => {
            // console.log('req', req.filter.status_id);
            var params = { 'permissionLevelId': '6', 'user_id': req.user_id };
            // console.log(params);
            var Json;
            var filters;
            var dc_access_list;
            var arrayfields = '';
            var userdata;
            var user_id = params.user_id;
            var legal_entity_type_id = [1002, 1014, 1016];

            // let cont = await role.checkUserIsSupplier(user_id);
            // console.log("conttt", cont);
            // let kk = await role.getAllAccessBrands(user_id);
            // console.log("kkkkkkkk", kk);
            role.getFilterData(params).then(data2 => {
                // console.log('data2222222222222222',data2);
                if (data2[0].hasOwnProperty('sbu')) {
                    filters = data2[0].sbu;
                    // console.log("aaaaa",filters[118001]);
                    dc_acess_list = filters[118001];
                    if (req.hasOwnProperty('count') && req.count != 0) {
                        arrayfields = 'inward.inward_id';
                        // console.log('in if');
                    } else {
                        // console.log('in else');
                        arrayfields = 'inward.*,legal.business_legal_name,currency.symbol_left as symbol,po.po_code as poCode,GetUserName(inward.created_by,2) as createdBy,getLeWhName(inward.le_wh_id) as dcname,(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as povalue, SUM(products.discount_total) as item_discount_value, inward.grand_total as grnvalue,po.po_code,po_invoice_grid.invoice_code';
                    }
                    //    console.log('before getInwardGridList');
                    var inwardqry = "select " + arrayfields + " from inward left join legal_entities as legal on legal.legal_entity_id=inward.legal_entity_id left join inward_products as products on products.inward_id=inward.inward_id left join currency on currency.currency_id=inward.currency_id left join po on po.po_id=inward.po_no left join po_invoice_grid on inward.inward_id=po_invoice_grid.inward_id and legal.legal_entity_type_id in (" + legal_entity_type_id + ")";
                    role.checkUserIsSupplier(user_id).then(data3 => {
                        //    console.log(data3+'data3');
                        if (data3.length == 0)
                        // if(data3.length>0)
                        {
                            //    console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
                            inwardqry += " where find_in_set(po.le_wh_id,'" + dc_acess_list + "')";

                            // console.log(inwardqry);
                            // resolve(inwardqry);  
                        }
                        if (data3.length > 0)
                        // if(data3.length==0)
                        {
                            role.getAllAccessBrands(user_id).then(data4 => {
                                // console.log("neeeeeeeeeeeeededddddddd", data4)
                                inwardqry += " left join products as pro on pro.product_id=products.product_id where pro.brand_id in (" + data4 + ")";
                                module.exports.globalsupplier(78023).then(data5 => {
                                    // console.log("neeeeeeeeeeeeededddddddd", data5)
                                    inwardqry += " and inward.legal_entity_id in (" + data5 + ")";
                                });
                            });
                        }


                        if (req.filter.status_id == 'invoiced') {
                            // console.log("invoiced is accessed");
                            inwardqry += " and po_invoice_grid.inward_id!=''";
                        }
                        if (req.filter.status_id == 'notinvoiced') {
                            inwardqry += " and po_invoice_grid.inward_id is NULL";
                        }
                        if (req.filter.status_id == 'approved') {
                            inwardqry += " and inward.approval_status=1";
                        }
                        if (req.filter.status_id == 'notapproved') {
                            inwardqry += " and inward.approval_status!=1";
                        }
                        // console.log(req.hasOwnProperty('count'));
                        // if(req.hasOwnProperty('count'))
                        // {
                        //     inwardqry +=" GROUP BY inward.inward_id";
                        // }
                        inwardqry += " GROUP BY inward.inward_id";
                        // inwardqry += "LIMIT  OFFSET"
                        resolve(inwardqry);
                        //     });
                        //    }
                    });
                }
            });

        });
    },
    globalsupplier: async function (master_lookup_value) {
        return new Promise((resolve, reject) => {
            var masterlookupqry = "select description from master_lookup where value=" + master_lookup_value;
            db.query(masterlookupqry, {}, async function (err5, rows5) {
                if (err5) {
                    reject('error');
                }
                if (rows5.length > 0) {
                    resolve(rows5[0].description);
                }
            });
        });
    },
    getInwardSuppliersList: async function () {
        return new Promise((resolve, reject) => {
            var inwardsuppliers = "select legal_entities.business_legal_name as supplier_name,legal_entities.legal_entity_id from legal_entities left join suppliers on legal_entities.legal_entity_id=suppliers.legal_entity_id where legal_entity_type_id=1002 and suppliers.is_active=1";
            db.query(inwardsuppliers, {}, async function (err6, rows6) {
                if (err6) {
                    await reject('error');
                }
                if (rows6.length > 0) {
                    await resolve(rows6);
                }
            });
        });
    },

    getInwardDetailById: async function (grnId) {
        return new Promise((resolve, reject) => {
            var query = "select `inward`.*, `product`.*, `pop`.`uom`, `pop`.`no_of_eaches`, `legal`.`business_legal_name`, `legal`.`gstin`, `legal`.`address1`, `legal`.`address2`, `legal`.`state_id`, `legal`.`city`, `legal`.`pincode`, `legal`.`le_code`, `wh`.`lp_wh_name`, `wh`.`address1` as `dc_address1`, `wh`.`address2` as `dc_address2`, `countries`.`name` as `country_name`, `zone`.`name` as `state_name`, `zone`.`code` as `state_code`, `users`.`firstname`, `users`.`lastname`,po_invoice_products.free_qty AS inv_free_qty,po_invoice_products.unit_price AS inv_unit_price,po_invoice_products.price AS inv_price,po_invoice_products.qty AS inv_qty, (select users.mobile_no from users where users.legal_entity_id=inward.legal_entity_id limit 1) as legalMobile, (select users.email_id from users where users.legal_entity_id=inward.legal_entity_id limit 1) as legalEmail, `currency`.`symbol_left` as `symbol`, `gdsp`.`sku`, `gdsp`.`upc`, `gdsp`.`seller_sku`, `gdsp`.`product_title`, `gdsp`.`mrp`, `tot`.`dlp`, `tot`.`base_price`, `po`.`po_code`,`po`.`po_address`, `po_invoice_grid`.`invoice_code`, `po_invoice_grid`.`created_at` as `po_invoice_created_at`,GetUserName(po_invoice_grid.created_by,2) AS invoice_created_name,DATE_FORMAT(inward.invoice_date,'%Y-%m-%d') as inward_invoice_date,DATE_FORMAT(inward.created_at,'%Y-%m-%d') as inward_created_at, DATE_FORMAT(po.po_date,'%Y-%m-%d') as po_created_date from `inward` inner join `inward_products` as `product` on `inward`.`inward_id` = `product`.`inward_id` inner join `products` as `gdsp` on `gdsp`.`product_id` = `product`.`product_id` left join `product_tot` as `tot` on `gdsp`.`product_id` = `tot`.`product_id` and `tot`.`supplier_id` = `inward`.`legal_entity_id` and `tot`.`le_wh_id` = `inward`.`le_wh_id` left join `po` on `po`.`po_id` = `inward`.`po_no` left join `po_invoice_grid` on `po_invoice_grid`.`inward_id` = `inward`.`inward_id` left join `po_products` as `pop` on `product`.`product_id` = `pop`.`product_id` and `inward`.`po_no` = `pop`.`po_id` inner join `legal_entities` as `legal` on `legal`.`legal_entity_id` = `inward`.`legal_entity_id` inner join `legalentity_warehouses` as `wh` on `wh`.`le_wh_id` = `inward`.`le_wh_id` left join `users` on `users`.`user_id` = `inward`.`created_by` left join `currency` on `currency`.`currency_id` = `inward`.`currency_id` left join `countries` on `countries`.`country_id` = `legal`.`country` left join `zone` on `zone`.`zone_id` = `legal`.`state_id` LEFT JOIN po_invoice_products ON po_invoice_grid.po_invoice_grid_id=po_invoice_products.po_invoice_grid_id AND pop.product_id=po_invoice_products.product_id where `inward`.`inward_id` = " + grnId + "";
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

    getWarehouseById: async function (leWhId) {
        return new Promise((resolve, reject) => {
            let query = `select warehouse.*, countries.name as country_name, zone.name as state_name, zone.code as state_code, legal_entities.business_legal_name, warehouse.tin_number as gstin from legalentity_warehouses as warehouse left join legal_entities on legal_entities.legal_entity_id = warehouse.legal_entity_id left join countries on countries.country_id = warehouse.country left join zone on zone.zone_id = warehouse.state where warehouse.le_wh_id = ${leWhId} limit 1;`
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                resolve(response[0]);
            })
        })
    },

    getAllOrderStatus: async function (catName, isActive = (1)) {
        return new Promise((resolve, reject) => {
            let query = `SELECT master_lookup.master_lookup_name AS NAME,master_lookup.value FROM master_lookup
            JOIN master_lookup_categories ON master_lookup_categories.mas_cat_id = master_lookup.mas_cat_id
            WHERE master_lookup_categories.mas_cat_name = '${catName}' and master_lookup.is_active = ${isActive};`

            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                let orderStatusArr = [];
                let final = [];
                if (response.length > 0) {
                    response.forEach(data => {
                        let result = {};
                        result[data.value] = data.NAME;
                        orderStatusArr.push(result);
                    })
                }
                resolve(orderStatusArr);
            })
        })
    },

    getProductPackInfo: async function (inwardPrdId) {
        return new Promise((resolve, reject) => {
            var query = "select DATE_FORMAT(`detail`.`exp_date`,'%Y-%m-%d') as exp_date, DATE_FORMAT(`detail`.`mfg_date`,'%Y-%m-%d') as mfg_date, `detail`.`freshness_per`, `master_lookup`.`master_lookup_name` as `pack_level`, `detail`.`pack_qty`, `detail`.`received_qty`, `detail`.`tot_rec_qty` from `inward_product_details` as `detail` left join `master_lookup` on `master_lookup`.`value` = `detail`.`pack_level` where `detail`.`inward_prd_id` =" + inwardPrdId + "";
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

    getInwardCodeById: async function (inwardId) {
        return new Promise((resolve, reject) => {
            var query = "select inward_code from inward where inward_id =" + inwardId + " limit 1";
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

    saveReturns: async function (prCode, inward_id, comment, user_id, le_wh_id, legal_entity_id) {
        return new Promise((resolve, reject) => {
            var query = `insert into purchase_returns (pr_code,inward_id,pr_status,approval_status,pr_grand_total,pr_total_qty,pr_remarks,created_by,le_wh_id,legal_entity_id) VALUES ('${prCode}',${inward_id},'103001','57036','0','0','${comment}',${user_id},${le_wh_id},${legal_entity_id})`;
            db.query(query, {}, async function (err, inserted) {
                console.log(query);
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

    getInwardProductById: async function (inward_id, productId) {
        return new Promise((resolve, reject) => {
            var query = "select `inwardprod`.*, `products`.`mrp`, `tax`.`tax_type` from `inward_products` as `inwardprod` left join `products` on `products`.`product_id` = `inwardprod`.`product_id` left join `input_tax` as `tax` on `inwardprod`.`inward_id` = `tax`.`inward_id` and `inwardprod`.`product_id` = `tax`.`product_id` where (`inwardprod`.`inward_id` = " + inward_id + ") and (`inwardprod`.`product_id` = " + productId + ") limit 1";
            db.query(query, {}, async function (err, rows) {
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

    // saveReturns:async function(arr){
    //     return new Promise((resolve,reject)=>{
    //         var columns='';
    //         var values='';
    //         for(var key in arr){
    //             columns +=key +','
    //             values+=" '" + arr[key] + "',"
    //         }
    //         var lastCharColumn = columns.slice(-1);
    //         var lastCharValue = values.slice(-1);
    //         if (lastCharColumn == ',') {
    //             columns = columns.slice(0, -1);
    //         }
    //         if (lastCharValue == ',') {
    //             values = values.slice(0, -1);
    //         }
    //         var dictionary="("+columns+") VALUES ("+values+")"
    //         var query = "insert into purchase_returns "+dictionary+" ";
    //         db.query(query, {}, async function (err, inserted) {console.log(query);
    //             if (err) {
    //                 reject('error');
    //             }
    //             if (Object.keys(inserted).length > 0) {
    //                 return resolve(inserted.insertId);
    //             } else {
    //                 return resolve([]);
    //             }
    //         });
    //     });
    // },

    saveReturnProducts: async function (arr) {
        return new Promise((resolve, reject) => {
            var columns = '';
            var values = '';
            for (var key in arr) {
                columns += key + ','
                values += " '" + arr[key] + "',"
            }
            var lastCharColumn = columns.slice(-1);
            var lastCharValue = values.slice(-1);
            if (lastCharColumn == ',') {
                columns = columns.slice(0, -1);
            }
            if (lastCharValue == ',') {
                values = values.slice(0, -1);
            }
            var dictionary = "(" + columns + ") VALUES (" + values + ")"
            var query = "insert into purchase_return_products " + dictionary + " ";
            db.query(query, {}, async function (err, rows) {
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


    updateReturn: async function (pr_id, grand_total, totQty) {
        return new Promise((resolve, reject) => {
            var query = `update purchase_returns set pr_grand_total=${grand_total},pr_total_qty=${totQty} where pr_id=${pr_id}`;
            db.query(query, {}, async function (err, rows) {
                console.log(query);
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

    getInwardDetailsById: async function (id) {
        return new Promise((resolve, reject) => {
            var query = "select `inward_products`.*, `inward`.`grand_total`, `inward`.`discount_on_total`, `inward`.`shipping_fee`, `po_products`.`price` as `poprice`, `po_products`.`tax_name`, `legal_entities`.`business_legal_name`, `inward`.`le_wh_id` from `inward_products` left join `inward` on `inward`.`inward_id` = `inward_products`.`inward_id` left join `po_products` on `po_products`.`product_id` = `inward_products`.`product_id` and `po_products`.`po_id` = `inward`.`po_no` left join `legal_entities` on `legal_entities`.`legal_entity_id` = `inward`.`legal_entity_id` where `inward`.`inward_id` = " + id + "";
            db.query(query, {}, async function (err, rows) {
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

    checkInvoiceByInwardId: async function (id) {
        return new Promise((resolve, reject) => {
            var query = "select `grid`.`invoice_code`, `grid`.`billing_name`, `grid`.`invoice_status`, `grid`.`created_at` as `invoice_date`, `grid`.`grand_total` from `po_invoice_grid` as `grid` where `grid`.`inward_id` =" + id + "";
            db.query(query, {}, async function (err, rows) {
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

    getSateIdByDcId: async function (id) {
        return new Promise((resolve, reject) => {
            var query = "select state from `legalentity_warehouses` where `le_wh_id` =" + id + " limit 1";
            db.query(query, {}, async function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].state);
                } else {
                    return resolve(4033);
                }
            });
        });
    },

}