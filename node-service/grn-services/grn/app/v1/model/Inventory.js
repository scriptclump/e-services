const Sequelize = require('sequelize');
const sequelize = require('../../config/sequelize');
var database = require('../../config/mysqldb');
var role = require('../model/Role.js');
let db = database.DB;

module.exports = {

    inventoryStockInward: async function (products, le_wh_id, refNo, refType) {
        return new Promise(async (resolve, reject) => {
            try {
                if(Array.isArray(products) && products.length>0){
                    var invLogsFinalArray = [];
                    var batch_history_array =[];
                    var batch_array_final = [];
                    var batch_inventory_update = "";
                    var gds_batch_update = "";

                    var i = 0;
                    for(const product of products){
                        var batchhistoryarray = [];
                        var product_id = product.hasOwnProperty('product_id')?product.product_id:0;

                        if(product_id<=0 || le_wh_id<=0){
                            continue;
                        }
                        var invInfo = await module.exports.getInventory(product_id,le_wh_id);

                        var soh = product.hasOwnProperty('soh')?product.soh:0;
                        var free_qty = product.hasOwnProperty('free_qty')?product.free_qty:0;
                        var quarantine_qty = product.hasOwnProperty('quarantine_qty')?product.quarantine_qty:0;
                        var dit_qty = product.hasOwnProperty('dit_qty')?product.dit_qty:0;
                        var dnd_qty =product.hasOwnProperty('dnd_qty')?product.dnd_qty:0;
                        var prevSOH = invInfo.hasOwnProperty('soh')?invInfo.soh:0;
                        var prevOrderQty = invInfo.hasOwnProperty('order_qty')?invInfo.order_qty:0;
                        var prevQuarantineQty = invInfo.hasOwnProperty('quarantine_qty')?invInfo.quarantine_qty:0;
                        var prevDndQty = invInfo.hasOwnProperty('dnd_qty')?invInfo.dnd_qty:0;
                        var prevDitQty = invInfo.hasOwnProperty('dit_qty')?invInfo.dit_qty:0;

                        var invLogs = [];
                        invLogs['le_wh_id'] = le_wh_id;
                        invLogs['product_id'] = product_id;
                        invLogs['soh'] = soh;
                        invLogs['order_qty'] = 0;
                        invLogs['ref'] = refNo;
                        invLogs['ref_type'] = refType;
                        invLogs['quarantine_qty'] = quarantine_qty;
                        invLogs['dit_qty'] = dit_qty;
                        invLogs['dnd_qty'] = dnd_qty;
                        invLogs['old_soh'] = prevSOH;
                        invLogs['old_order_qty'] = prevOrderQty;
                        invLogs['old_quarantine_qty'] = prevQuarantineQty;
                        invLogs['old_dnd_qty'] = prevDndQty;
                        invLogs['old_dit_qty'] = prevDitQty;
                        invLogs['comments'] = "";

                        invLogsFinalArray.push(invLogs);

                        if(invInfo.hasOwnProperty('inv_id') && invInfo.inv_id>0){
                            await module.exports.inventoryUpdate(le_wh_id,product_id,soh,free_qty,quarantine_qty,dit_qty,dnd_qty,invInfo.inv_id);
                        }else{
                            await module.exports.inventoryInsert(le_wh_id,product_id,soh,free_qty,quarantine_qty,dit_qty,dnd_qty);
                        }

                        if(refType == "Sales Returns" || refType == 3){
                            var gds_order_id = await module.exports.getGdsOrderId(refNo);

                            if(gds_order_id.hasOwnProperty('gds_order_id')){
                                gds_order_id = gds_order_id.gds_order_id;
                                var batchData =  await module.exports.getBactchData(product_id,gds_order_id);
                                
                                if(batchData.length>0){
                                   // var gds_batch_update = [];
                                    var batch_inventory_update = [];
                                    //var batch_history_array = [];
                                    for(const [bkey,bvalue] of batchData){

                                        if(soh>0){
                                            var new_qty = bvalue.inv_qty - bvalue.ret_qty;
                                            var add_qty = (soh >= new_qty)? new_qty:soh;
                                            var gdsbatchqry = "";
                                            gdsbatchqry = "UPDATE gds_orders_batch SET ret_qty=ret_qty+"+add_qty+" where gob_id="+bvalue.gob_id;
                                            gds_batch_update.push(gdsbatchqry);
                                            var batchinventoryupdate = "";
                                            batchinventoryupdate = "UPDATE inventory_batch SET qty=qty+"+add_qty+" where product_id = "+product_id+" and le_wh_id="+le_wh_id+" and inward_id="+bvalue.inward_id;

                                            if(main_batch_id!=""){
                                                batchinventoryupdate += " and main_batch_id = "+main_batch_id;    
                                            }    
                                            batch_inventory_update.push(batchinventoryupdate);

                                            var old_data = await module.exports.getQtyFromInvBatch(bvalue.inward_id,product_id,le_wh_id);
                                            var old_qty = 0;

                                            if(old_data.length>0){
                                                old_qty = old_data.qty;
                                            }
                                            batchhistoryarray["inward_id"]=bvalue.inward_id;
                                            batchhistoryarray["le_wh_id"]=le_wh_id;
                                            batchhistoryarray["product_id"]=product_id;
                                            batchhistoryarray["qty"]=add_qty;
                                            batchhistoryarray["old_qty"]=old_qty;
                                            batchhistoryarray['ref']=refNo;
                                            batchhistoryarray['ref_type']=refType;
                                            batchhistoryarray['dit_qty']=0;
                                            batchhistoryarray['old_dit_qty']=0;
                                            batchhistoryarray['dnd_qty']=0;
                                            batchhistoryarray['old_dnd_qty']=0;
                                            batchhistoryarray['comments']='Qty Added by Sales Returns';

                                            soh = soh-bvalue.inv_qty;
                                            batch_history_array.push(batchhistoryarray);
                                        }
                                    }
                                }
                            }
                        }else{
                            var exp_date = product.hasOwnProperty('exp_date')?product.exp_date:0;
                            var mfg_date = product.hasOwnProperty('manf_date')?product.manf_date:0;
                            var elp = product.hasOwnProperty('elp')?product.elp:0;

                            var esp = await module.exports.getProductEspBywh(product_id,le_wh_id);
                            var batchid = await module.exports.getInwardCode(refNo);
                            var po_so_code = await module.exports.getPOSOCode(batchid);
                            var code = po_so_code.hasOwnProperty('po_so_order_code')?po_so_code.po_so_order_code:'';
                            var mainBatch_id;
                            if(code){
                                mainBatch_id=[];
                                mainBatch_id['main_batch_id']=batchid;
                                mainBatch_id['ord_qty'] = soh;
                                //main_batch_id   done
                            }else{
                                var getbatchId = await module.exports.getOrderIdByOrderCode(code);
                                console.log(getbatchId,'getbatchIdgetbatchIdgetbatchId',code);
                                if(getbatchId){
                                    var main_wh_id = getbatchId[0].hasOwnProperty('le_wh_id')?getbatchId[0].le_wh_id:'NULL';
                                    var gdsOrderId = getbatchId[0].hasOwnProperty('gds_order_id')?getbatchId[0].gds_order_id:'NULL';
                                    getbatchId = module.exports.getQtyByData(product_id,main_wh_id,soh,0,10,[],gdsOrderId);
                                    mainBatch_id = getbatchId;
                                }
                            }
                            var actual_qty = soh;
                            var req_qty;
                            var mainbatchid;
                            if(mainBatch_id){
                                for(const [mainbatch] of mainBatch_id){
                                    req_qty = actual_qty;
                                    mainbatchid = (mainbatch.hasOwnProperty('main_batch_id') && mainbatch.main_batch_id!="")?mainbatch.main_batch_id:batchid;
                                    var bkey = mainbatchid+'_'+product_id;

                                    if(req_qty>0){
                                        if(req_qty>mainbatch.ord_qty){
                                            var used_qty = mainbatch.ord_qty;
                                        }else if(mainbatch.ord_qty >= req_qty){
                                            var used_qty = req_qty;
                                        }

                                        if(batch_array.hasOwnProperty(bkey)){
                                            batch_array[bkey]=[];
                                            batch_array[bkey]['qty'] = batch_array[bkey]['qty']+used_qty;
                                        }else{
                                            var inwdPrdDetails = await module.exports.getinwdPrdDetails(batchid,product_id);
                                            var mfg_date = inwdPrdDetails.hasOwnProperty('mfg_date')?inwdPrdDetails.mfg_date:mfg_date;
                                            var mfg_date = inwdPrdDetails.hasOwnProperty('exp_date')?inwdPrdDetails.exp_date:exp_date;

                                            batch_array[bkey]["inward_id"] = batchid;
                                            batch_array[bkey]["le_wh_id"] = le_wh_id;
                                            batch_array[bkey]["product_id"] = product_id;
                                            batch_array[bkey]["qty"] = used_qty;
                                            batch_array[bkey]["dit_qty"] = dit_qty;
                                            batch_array[bkey]["dnd_qty"] = dnd_qty;
                                            batch_array[bkey]["elp"] = elp;
                                            batch_array[bkey]["esp"] = esp;
                                            batch_array[bkey]["mfg_date"] = mfg_date;
                                            batch_array[bkey]["exp_date"] = exp_date;
                                            batch_array[bkey]["created_by"] = userId;
                                            batch_array[bkey]["updated_by"] = userId;
                                            batch_array[bkey]["main_batch_id"] = mainbatch.main_batch_id;
                                        }
                                        actual_qty = req_qty - used_qty;
                                        batch_array_final.push(batch_array);
                                    }
                                }
                            }
                            batchhistoryarray["inward_id"] = batchid;
                            batchhistoryarray["le_wh_id"] = le_wh_id;
                            batchhistoryarray["product_id"] = product_id;
                            batchhistoryarray["qty"] = soh;
                            batchhistoryarray["ref"] = refNo;
                            batchhistoryarray["ref_type"] = refType;
                            batchhistoryarray["dit_qty"] = dit_qty;
                            batchhistoryarray["dnd_qty"] = dnd_qty;
                            batchhistoryarray["old_dnd_qty"] = prevDndQty;
                            batchhistoryarray["old_dit_qty"] = prevDitQty;
                            batchhistoryarray["comments"] = 'GRN Created';
                            batch_history_array.push(batchhistoryarray)
                        }

                    }

                    if(invLogsFinalArray.length>0){
                        console.log(invLogsFinalArray,'invLogsFinalArrayinvLogsFinalArray');
                     //   module.exports.addInQueueWithBulk(invLogsFinalArray);
                    }

                    if(batch_array_final.length>0){
                        console.log(batch_array_final,'batch_array_finalbatch_array_final');
                        module.exports.insertBatch(batch_array_final);
                    }

                    if(batch_history_array.length>0){
                        module.exports.insertBatchHistory(batch_history_array);
                    }

                    if(gds_batch_update.length>0){
                        console.log(gds_batch_update,'gds_batch_updategds_batch_update');
                         //module.exports.gdsBatchUpdate(gds_batch_update);
                    }

                    if(batch_inventory_update.length>0){
                        console.log(batch_inventory_update,'batch_inventory_updatebatch_inventory_update');
                        //module.exports.inventoryBatchUpdate(batch_inventory_update);
                    }
                }
            } catch (err) {
                reject(err);
            }
        })
    },

    getInventory:async function(productId,leWhId){
        return new Promise((resolve,reject)=>{
            try{
                var inventoryqry = 'select * from inventory where inventory.product_id = '+productId+' AND inventory.le_wh_id='+leWhId+' limit 1';   

                 sequelize.query(inventoryqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                          resolve(result[0]);
                     } else {
                          resolve([]);
                     }
            }).catch(err => {
                console.log(err);
                 reject(err);
            })   
            }catch (err) {
                reject(err);
            }
        })

    },

    getGdsOrderId : async function(refNo){
        return new Promise((resolve,reject)=>{
            try{
                var getOrderIdqry = "select gds_order_id from gds_return_grid where return_order_code="+refNo+" limit 1";
                    sequelize.query(getOrderIdqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                        //console.log(result,'resultresultresultresultresult');
                        if (result != '' && result.length > 0) {
                              resolve(result[0]);
                         } else {
                              resolve([]);
                         }
                    }).catch(err => {
                        console.log(err);
                         reject(err);
                    })   
            }catch (err){
                reject(err);
            }
        })
    },

    getBactchData :async function(product_id,gds_order_id){
        return new Promise((resolve,reject)=>{
            try{
                var getbatchesdataqry = "select * from gds_orders_batch where product_id="+product_id+" and gds_order_id="+gds_order_id+" order by gob_id desc";
                    sequelize.query(getbatchesdataqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                        //console.log(result,'resultresultresultresultresult');
                        if (result != '' && result.length > 0) {
                              resolve(result[0]);
                         } else {
                              resolve([]);
                         }
                    }).catch(err => {
                        console.log(err);
                         reject(err);
                    })   
            }catch (err){
                reject(err);
            }
        })
    },

    getProductEspBywh:async function(product_id,le_wh_id){
        return new Promise((resolve,reject)=>{
            var espQuery = "select getProductEsp_wh("+product_id+","+le_wh_id+") as esp";
            sequelize.query(espQuery, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
                    if (result != '' && result.length > 0) {
                          resolve(result[0].esp);
                     } else {
                          resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })
        })
    },

    getInwardCode : async function(refNo){
        return new Promise((resolve,reject)=>{
            var getinwardcode = "select inward_id from inward where inward_code='"+refNo+"' limit 1";
            sequelize.query(getinwardcode, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    if (result != '' && result.length > 0) {
                          return resolve(result[0].inward_id);
                     } else {
                          return resolve(0);
                     }
                }).catch(err => {
                    console.log(err);
                     reject(err);
                })  
        })
    },

    getPOSOCode : async function(batchid){
        return new Promise((resolve,reject)=>{
          var po_so_codeqry = "select inward_id,po.po_id,po.po_so_order_code from inward join po on po.po_id=inward.po_no where inward_code="+batchid+" limit 1";
            sequelize.query(po_so_codeqry, { type: Sequelize.QueryTypes.SELECT }).then(result => {
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
    },

    getinwdPrdDetails:async function(batch_id,product_id){
        return new Promise((resolve,reject)=>{
            var getinwarddetails = "select inwpd.mfg_date,inwpd.exp_date from inward_product_details as inwpd join inward_products as inwp on inwp.inward_prd_id=inwpd.inward_prd_id where inwp.inward_id="+batch_id+" AND inwp.product_id="+product_id+" LIMIT 1";
            sequelize.query(getinwarddetails, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    //console.log(result,'resultresultresultresultresult');
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
    },

    inventoryStockOutward: async function (products, le_wh_id,outwardType=0, refNo='', refType='') {
        return new Promise(async (resolve, reject) => {
            try {
                if(Array.isArray(products) && products.length>0){
                    var invLogsFinalArray = [];
                    var batch_history_array =[];
                    var stockOutward = [];
                    var batch_inventory_update = "";
                    var comments = "";

                    if(refType == 3){
                        comments = "SOH Subtracted (Stock Transfer PO ID:"+refNo+")";
                    }

                    for(const product of products){
                        var product_id = product.hasOwnProperty('product_id')?product.product_id:0;

                        if(product_id<0 || le_wh_id<0){
                            continue;
                        }
                        var invInfo = await module.exports.getInventory(product_id,le_wh_id);
                        var dit_qty = product.hasOwnProperty('dit_qty')?product.dit_qty:0;
                        var dnd_qty =product.hasOwnProperty('dnd_qty')?product.dnd_qty:0;
                        var prevSOH = invInfo.hasOwnProperty('soh')?invInfo.soh:null;
                        var prevOrderQty = invInfo.hasOwnProperty('order_qty')?invInfo.order_qty:null;
                        var prevQuarantineQty = invInfo.hasOwnProperty('quarantine_qty')?invInfo.quarantine_qty:null;
                        var prevDndQty = invInfo.hasOwnProperty('dnd_qty')?invInfo.dnd_qty:null;
                        var prevDitQty = invInfo.hasOwnProperty('dit_qty')?invInfo.dit_qty:null;

                        if(outwardType == '0') {
                            var fields=[];
                            fields['soh'] = '(soh-'+product.qty+')';
                            fields['order_qty'] = '(order_qty-'+product.qty+')';
                            module.exports.updateQty(fields,le_wh_id,product_id);
                        }else if(outwardType == '1') {
                            var fields=[];
                            fields['soh'] = '(soh-'+product.qty+')';
                            fields['dit_qty'] = '(dit_qty-'+dit_qty+')';
                            fields['dnd_qty'] = '(dnd_qty-'+dnd_qty+')';
                            module.exports.updateQty(fields,le_wh_id,product_id);
                        }

                        var invLogs = [];
                        invLogs['le_wh_id'] = le_wh_id;
                        invLogs['product_id'] = product_id;
                        invLogs['soh'] = '-'+product.qty;
                        invLogs['order_qty'] = '-'+ (outwardType=='0' ? product.qty:0);
                        invLogs['ref'] = refNo;
                        invLogs['ref_type'] = refType;
                        invLogs['dit_qty'] = '-'+dit_qty;
                        invLogs['dnd_qty'] = '-'+dnd_qty;
                        invLogs['old_soh'] = prevSOH;
                        invLogs['old_order_qty'] = prevOrderQty;
                        invLogs['old_quarantine_qty'] = prevQuarantineQty;
                        invLogs['old_dnd_qty'] = prevDndQty;
                        invLogs['old_dit_qty'] = prevDitQty;
                        invLogs['comments'] = comments;

                        invLogsFinalArray.push(invLogs);
                        if (product.qty==0)
                            var total_qty = product.dit_qty;
                        else
                            var total_qty = product.qty; 

                        var invoice_codes = (product.product_invoices)?product.product_invoices:'';
                        if(invoice_codes!=''){
                            invoice_codes =invoice_codes.split(',');
                            var orders_id=await this.getGdsId(invoice_codes);
                            var po_so_codes=await this.getGdsCode(orders_id);
                            var po_nos=await this.getPoId(po_so_codes);
                            var inward_ids=await this.getInwardId(po_nos);

                            var batch_inv_array =await this.getInventoryBatchData(le_wh_id,product_id,inward_ids); 
                            var batch_data=batch_inv_array;
                        }else{
                            var batch_inv_array = await this.getBatchesByData(product_id,le_wh_id,total_qty,0,10,[]);
                        }
                        for(const [ikey,ivalue] of batch_inv_array){
                            var batch_id = ivalue.inward_id;
                            var invb_id = ivalue.invb_id;
                            var elp = ivalue.elp;
                            var req_qty = product.qty;
                            var req_qty_dit = product.dit_qty;
                            if(req_qty > ivalue.qty){
                                var used_qty = ivalue.qty;
                            }else if(ivalue.qty >= req_qty){
                                var used_qty = req_qty;
                            }
                            if(batch_inv_array.length == 1){
                                var batch_ord_qty = product.qty;
                            }else{
                                var batch_ord_qty = used_qty;
                            }

                            if(req_qty_dit > ivalue.dit_qty){
                                var used_qty_dit = ivalue.dit_qty;

                            }else if(ivalue.dit_qty >= req_qty_dit){

                                var used_qty_dit = req_qty_dit;
                            }
                            batch_history_array["inward_id"]=batch_id,
                            batch_history_array["le_wh_id"]=le_wh_id,
                            batch_history_array["product_id"]=product_id,
                            batch_history_array["qty"]='-'+used_qty,
                            batch_history_array["old_qty"]=ivalue.qty,
                            batch_history_array['ref']=refNo,
                            batch_history_array['ref_type']=refType,
                            batch_history_array['dit_qty']='-'+used_qty_dit,
                            batch_history_array['old_dit_qty']=ivalue.dit_qty,
                            batch_history_array['dnd_qty']='-'+dnd_qty,
                            batch_history_array['old_dnd_qty']=ivalue.dnd_qty,
                            batch_history_array['comments']="Qty Substracted for Batch Id:"+batch_id+" ";
                            product.qty = req_qty - used_qty;
                            product.dit_qty = req_qty_dit - used_qty_dit;

                            batch_inventory_update = "UPDATE inventory_batch SET qty=qty-"+used_qty+",dit_qty=dit_qty-"+used_qty_dit+" where invb_id = "+invb_id+" ";
                        }

                    }
                    if(invLogsFinalArray.length>0){
                        await this.addInQueueWithBulk(invLogsFinalArray);
                    }
                    if(batch_history_array.length>0){
                        if(batch_inventory_update.length>0){
                            await this.inventoryBatchUpdate(batch_inventory_update);
                        }
                        await this.insertBatchHistory(batch_history_array);
                    }
                }
            } catch (err) {
                reject(err);
            }
        })
    },

    getOrderIdByOrderCode : async(code) =>{
        return new Promise((resolve,reject)=>{
            var ordercodeqry = "select gds_order_id,le_wh_id from gds_orders where order_code='"+code+"'";
            db.query(ordercodeqry, {}, function (err, rows) {
                    if (err) {
                            reject(err)
                    }
                    if (rows.length > 0) {
                        return resolve(rows);
                    } else {
                        return resolve([]);
                    }
            });
        })
    },

    insertBatch : async(arr)=>{
        return new Promise((resolve,reject)=>{
            var stkInwardColumns = '';
            var stkInwardSaveInfo='';
            for (const key of arr) {
                stkInwardColumns = '';
                stkInwardSaveInfo='';
                for(const [columnkey,batchArr] of Object.entries(key)){
                    stkInwardColumns  += columnkey + ",";
                    stkInwardSaveInfo += "'"+batchArr + "',";
                }
                var stkInwardColumnslastChar = stkInwardColumns.slice(-1);
                if (stkInwardColumnslastChar == ',') {
                    stkInwardColumns = stkInwardColumns.slice(0, -1);
                }
                var stkInwardSaveInfolastChar = stkInwardSaveInfo.slice(-1);
                if (stkInwardSaveInfolastChar == ',') {
                    stkInwardSaveInfo = stkInwardSaveInfo.slice(0, -1);
                }
                var query = "insert into inventory_batch("+stkInwardColumns+") values ("+stkInwardSaveInfo+")";
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
        })
    },

    inventoryInsert : async(le_wh_id,product_id,soh,free_qty,quarantine_qty,dit_qty,dnd_qty)=>{
        return new Promise((resolve,reject)=>{
            var fields_soh = ('soh') + '+' + (soh);
            var fields_free_qty = ('free_qty') + '+' + (free_qty-0);
            var fields_quarantine_qty = ('quarantine_qty') + '+' + (quarantine_qty-0);
            var fields_dit_qty = ('dit_qty') + '+' + (dit_qty-0);
            var fields_dnd_qty = ('dnd_qty') + '+' + (dnd_qty-0);
            var insertqry = "insert into inventory (le_wh_id,product_id,soh,free_qty,quarantine_qty,dit_qty,dnd_qty) VALUES("+le_wh_id+","+product_id+","+fields_soh+","+fields_free_qty+","+fields_quarantine_qty+","+fields_dit_qty+","+fields_dnd_qty+")";
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
        })
    },

    inventoryUpdate : async(le_wh_id,product_id,soh,free_qty,quarantine_qty,dit_qty,dnd_qty,inv_id)=> {
        return new Promise((resolve, reject) => {
            var fields_soh = ('soh') + '+' + (soh);
            var fields_free_qty = ('free_qty') + '+' + (free_qty-0);
            var fields_quarantine_qty = ('quarantine_qty') + '+' + (quarantine_qty-0);
            var fields_dit_qty = ('dit_qty') + '+' + (dit_qty-0);
            var fields_dnd_qty = ('dnd_qty') + '+' + (dnd_qty-0);
            var updatePoPrd = "UPDATE inventory SET le_wh_id="+le_wh_id+" and product_id="+product_id+" and soh="+fields_soh+" and free_qty="+fields_free_qty+" and quarantine_qty="+fields_quarantine_qty+" and dit_qty="+fields_dit_qty+" and dnd_qty="+fields_dnd_qty+" where inv_id="+inv_id+" "; 
            db.query(updatePoPrd, {}, function (err, rows) {
                if (err) {
                    reject('error');
                }
                if (Object.keys(rows).length > 0) {
                    return resolve(rows);
                } else {
                    return resolve(0);
                }
            });
        });
    },

    insertBatchHistory : async(arr)=>{
        return new Promise((resolve,reject)=>{
            var stkInwardColumns = '';
            var stkInwardSaveInfo='';
            for (const key of arr) {
                stkInwardColumns = '';
                stkInwardSaveInfo='';
                for(const [columnkey,batchArr] of Object.entries(key)){
                    stkInwardColumns  += columnkey + ",";
                    stkInwardSaveInfo += "'"+batchArr + "',";
                }
                var stkInwardColumnslastChar = stkInwardColumns.slice(-1);
                if (stkInwardColumnslastChar == ',') {
                    stkInwardColumns = stkInwardColumns.slice(0, -1);
                }
                var stkInwardSaveInfolastChar = stkInwardSaveInfo.slice(-1);
                if (stkInwardSaveInfolastChar == ',') {
                    stkInwardSaveInfo = stkInwardSaveInfo.slice(0, -1);
                }
                var query = "insert into inventory_batch_history("+stkInwardColumns+") values ("+stkInwardSaveInfo+")";
                db.query(query, {}, function (err, rows) {
                    if (err) {
                            reject(err)
                    }
                    if (Object.keys(rows).length > 0) {
                        return resolve(rows.insertId);
                    } else {
                        return resolve(0);
                    }
                });
                resolve(1);
            }
        })
    },

    getQtyFromInvBatch: async (inward_id,product_id,le_wh_id)=> {
        return new Promise((resolve, reject) => {
            let query = "select *  from inventory_batch where inward_id ="+inward_id+" and product_id="+product_id+" and le_wh_id="+le_wh_id+"  limit 1";
            db.query(query, function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows[0]);
                } else {
                    resolve([]);
                }
            });
        });
    },

    getQtyByData: async (product_id,le_wh_id,req_qty,offset,batch_limit,batches=[],gds_orderID)=> {
        return new Promise((resolve, reject) => {
            let query = "select *  from gds_orders_batch where ord_qty>0 and product_id="+product_id+" and gds_order_id="+gds_orderID+" ";
            db.query(query, function (err, rows) {
                if (err) {
                    reject(err);
                } else if (Object.keys(rows).length > 0) {
                    resolve(rows);
                } else {
                    resolve([]);
                }
            });
        });
    },

    updateQty : async(arr,le_wh_id,product_id)=> {
        return new Promise((resolve, reject) => {
            var dictionary = '';
            for (var key in arr) {
                
                dictionary += key + "='" + arr[key] + "',"
            }
            var lastChar = dictionary.slice(-1);
            if (lastChar == ',') {
                dictionary = dictionary.slice(0, -1);
            }
            var query = "UPDATE inventory SET " + dictionary + " WHERE le_wh_id="+le_wh_id+" and product_id="+product_id+"";
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
    },

    getGdsId : async function(invoice_codes){
        return new Promise((resolve,reject)=>{
            var query = "select group_concat(gds_order_id) as gds_order_id from gds_invoice_grid where invoice_code IN ("+invoice_codes+")  ";
                sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(result => {
                    if (result != '' && result.length > 0) {
                        var res=result[0].gds_order_id.split(",");
                        return resolve(res);
                     } else {
                          resolve([]);
                     }
                }).catch(err => {
                     reject(err);
                })   
        })
    },

    getGdsCode : async(ids) =>{
        return new Promise((resolve,reject)=>{
            var query = "select group_concat(order_code) as order_code from gds_orders where gds_order_id IN ("+ids+")";
            db.query(query, {}, function (err, rows) {
                    if (err) {
                        reject(err)
                    }
                    if (rows.length > 0) {
                        var res=rows[0].order_code.split(",");
                        return resolve(res);
                    } else {
                        return resolve([]);
                    }
            });
        })
    },
    
    getPoId : async(codes) =>{
        return new Promise((resolve,reject)=>{
            var ordercodeqry = "select group_concat(po_id) as po_id from po where po_so_order_code IN ("+codes+")";
            db.query(ordercodeqry, {}, function (err, rows) {
                    if (err) {
                        reject(err)
                    }
                    if (rows.length > 0) {
                        var res=rows[0].po_id.split(",");
                        return resolve(res);
                    } else {
                        return resolve([]);
                    }
            });
        })
    },

    getInwardId : async(ids) =>{
        return new Promise((resolve,reject)=>{
            var query = "select group_concat(inward_id) as inward_id from inward where po_no IN ("+ids+")";
            db.query(query, {}, function (err, rows) {
                    if (err) {
                        reject(err)
                    }
                    if (rows.length > 0) {
                        var res=rows[0].inward_id.split(",");
                        return resolve(res);
                    } else {
                        return resolve([]);
                    }
            });
        })
    },

    getInventoryBatchData : async(le_wh_id,product_id,inward_ids) =>{
        return new Promise((resolve,reject)=>{
            var query = "select * from inventory_batch where le_wh_id="+le_wh_id+" and product_id="+product_id+" and qty>0 and inward_id IN ("+inward_ids+") order by created_at asc , invb_id desc";
            db.query(query, {}, function (err, rows) {
                    if (err) {
                        reject(err)
                    }
                    if (rows.length > 0) {
                        return resolve(rows);
                    } else {
                        return resolve([]);
                    }
            });
        })
    },

    getBatchesByData: async(product_id,le_wh_id,req_qty,offset=0,batch_limit,batches=[]) =>{
        return new Promise(async(resolve,reject)=>{
            var query = "select * from inventory_batch where le_wh_id="+le_wh_id+" and product_id="+product_id+" and qty>0 order by invb_id ASC , inward_id ASC limit "+batch_limit+" offset "+offset+" ";
            db.query(query, {}, function (err, rows) {
                    if (err) {
                        reject(err)
                    }
                    if (rows.length > 0) {
                        offset = batch_limit;
                        batch_limit = batch_limit + 10;
                        for(const [key,value] of rows){
                            if(req_qty > 0){
                                batches = value;
                            }else{
                                break;
                            }
                            req_qty -= value.qty;
                            if(req_qty <= 0 ){
                                break;
                            }
                        }
                        if(req_qty > 0){
                            module.exports.getBatchesByData(product_id,le_wh_id,req_qty,batch_limit,batch_limit,batches);
                        }else{
                            batches = batches;
                        }
                        return resolve(batches);
                    } else {
                        return resolve([]);
                    }
            });
        })
    }

}