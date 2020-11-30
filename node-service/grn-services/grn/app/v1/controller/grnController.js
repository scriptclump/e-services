'use strict';

const masterLookUp = require('../model/masterLookUpModel');
const grnModel = require('../model/grnModel');
const rolerepo = require('../model/Rolerepo');
const legalentity = require('../model/legalentityModel');
const inwardModel = require('../model/Inward');
const dispute = require('../model/dispute');
const purchaseOrder = require('../model/purchaseOrder.js');
const moment = require('moment');
const converter = require('convert-rupees-into-words');
const aws = require('aws-sdk');
const { exit } = require('process');
const BUCKET_NAME = process.env.S3BucketName;
const IAM_USER_KEY = process.env.S3AccessKeyId;
const IAM_USER_SECRET = process.env.S3AecretAccessKey;
// var upload = require('../../config/s3config');
let current_datetime = new Date();
let created_at = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
var role = require('../model/Role.js');
var cache = require('../../config/redis');//rediscache connection file 
const sequelize = require('../../config/sequelize');
var today = new Date();
var Currentdate = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
var Currenttime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
var CurrentdateTime = Currentdate + ' ' + Currenttime;
/*
Module: GRN
Author: Ebutor
Date: 2019-10-01
*/
var polist = [];
var checkProducts = [];
module.exports = {

    index: function (req, res) {
        res.send('You are in GRN Module');
    },

    grnList: async (req, res) => {
        try {
            let grid_field_db_match = {
                "poID": "po.po_id",
                "grnId": "po.po_code",
                "grnDate": "po.le_code",
                "Supplier": "po.business_legal_name",
                "dcname": "po.lp_wh_name",
                "createdBy": "poValidity",
                "ref_no": "poValue",
                "invoice_no": "payment_mode",
                "povalue": "payment_mode_color",
                "grnvalue": "po.payment_due_date"
            };

            let data = JSON.parse(req.body.data);
            // console.log(data);
            let userId = Number(data.user_id);
            if (isNaN(userId) || userId == 0 || userId == "") {
                res.send("Invalid userID");
                return;
            }

            let fromDate = data.from_date;
            let toDate = data.to_date;


            let sortField = data.sort_field;
            sortField = grid_field_db_match[sortField];
            let sortType = data.sort_type;
            sortType = (sortType == 'true') ? "asc" : "desc";
            let filters = data.filters;
            let filter = {};
            if (filters != [] && filters != null && filters != '') {
                let filterArr = [];
                //forEach to arrange the filters
                if (Array.isArray(filters)) {
                    filters.forEach(x => {
                        let op = x.op;
                        let value = x.value;
                        let result = {};
                        result[x.field] = [value, op];
                        filterArr.push(result);
                    })
                }
                filter = filterArr.reduce((a, b) => Object.assign({}, a, b));
            }
            // console.log("filter", filter);
            // dataArr is the final response being sent by this API.
            let dataArr = [];

            //grnStatusId ===== '87001': 'OPEN','87002': 'PO CLOSED','87003': 'EXPIRED','87004': 'CANCELLED','87005': 'PARTIALLY RECEIVED'
            let grnStatus = data.grn_status;
            //approvalStatusId ==== 'initiated':57106,'created':57029,'verified':57030,'approved':57031,'posit':57033,'checked':57107,'receivedatdc':57034,'grncreated':57035,'shelved':1,'payments':57032
            let approvalStatusId = Number(data.approval_status_id);

            //only either of grnStatusId or approvalStatusId shall be passed in req.body
            grnStatus = grnStatus == "" ? 'all' : grnStatus;
            approvalStatusId = Boolean(approvalStatusId) ? 0 : approvalStatusId; // if grnStatusId is passed approvalStatusId = 0


            let page = Number(data.page);
            let perPage = Number(data.page_size);
            let offset = Number(page * perPage);
            let rowCount;
            let allStatusArr = await masterLookUp.getAllOrderStatus('GRN');
            //reducing different objects in allStatusArr into one object
            allStatusArr = allStatusArr.reduce((a, b) => Object.assign({}, a, b));
            // console.log('allStatusArr', allStatusArr);
            let grnInwards = await grnModel.getAllInwards(userId, grnStatus, approvalStatusId, 0, fromDate, toDate, filter, perPage, offset, sortField, sortType);
    
            // res.send(grnInwards);
            // return;
            //checking for permissions based on userId
            let isViewable = await rolerepo.checkPermissionByFeatureCode('GRN003', userId);
            let isPrintable = await rolerepo.checkPermissionByFeatureCode('GRN005', userId);
            let isDownloadable = await rolerepo.checkPermissionByFeatureCode('GRN006', userId);
            let isEditable = await rolerepo.checkPermissionByFeatureCode('GRN007', userId);
            if (grnInwards.length > 0 && grnInwards != 0) {
                grnInwards.forEach(async grn => {
                    let poValue = (grn.povalue != '') ? grn.povalue : 0;
                    let grnValue = (grn.grnvalue != '') ? grn.grnvalue : 0;
                    let inward_status = allStatusArr[grn.inward_status];
                    let approvalStatus = (grn.approval_status != '') ? grn.approval_status : '';
                    let is_edit = isEditable;

                    // if (approvalStatus == 'GRN Created' || approvalStatus == 'Cancelled' || inward_status == 'PO CLOSED') is_edit = false;
                    // if (approvalStatus == '57035' || approvalStatus == '1'|| grn.invoice_code == '' || approvalStatus == 'Cancelled' || inward_status == 'PO CLOSED') is_edit = false;
                    if (is_edit && grn.invoice_code == '' ) is_edit = true;
                    else is_edit = false;
                    rowCount = grn.Count;
                    let result = {};
                    result = {
                        "createdBy": grn.createdBy,
                        "dcname": grn.dcname,
                        "grnDate": grn.created_at,
                        "grnCode": grn.inward_code,
                        "grnId": grn.inward_id,
                        "grnvalue": grnValue,
                        "invoice_no": grn.invoice_no,
                        "item_discount_value": grn.item_discount_value,
                        "legalsuplier": grn.business_legal_name,
                        "poCode": grn.poCode,
                        "poId": grn.po_no,
                        "povalue": poValue,
                        "ref_no": grn.inward_ref_no,
                        "leid": grn.legal_entity_id,
                        "is_viewable": isViewable,
                        "is_printable": isPrintable,
                        "is_downloadable": isDownloadable,
                        "is_editable": is_edit
                    };

                    dataArr.push(result);
                })
                // let rowCount = await grnModel.getAllInwards(userId, grnStatus, approvalStatusId, 1, fromDate, toDate, filter);
                let numberOfPages = Math.ceil(rowCount / perPage);
                let recordsOnPage = ((offset + perPage) < rowCount) ? (offset + perPage) : rowCount;

                res.send({
                    "Status": "Success", "Message": "Records found",
                    "RecordNumber": `${(offset + 1)} - ${recordsOnPage} of ${rowCount} records`,
                    "NumberOfPages": (numberOfPages), "PresentPage": Number(page + 1), "data": dataArr
                });
            } else {
                res.send({ "Status": "Failed", "Message": "No Records found" });
            }
        } catch (err) {
            res.send({ "Status": "Failed", "Message": "No Records found" });
            console.log(err);
        }
    },

    /*
    This getGrnCounts returns counts of GRN based on status
    Required Params legalentity id,userid
    return counts of all invoiced,all,approved,not approved and not invoiced
    */
    getGrnCounts: async function (req, res) {

        if (Object.keys(req.body.data).length > 0) {
            let params = req.body.data;
            params = JSON.parse(params);

            let userid;
            if (params.hasOwnProperty('user_id')) {
                userid = params.user_id;
                let grnaccess = await rolerepo.checkPermissionByFeatureCode('GRN001', userid);
                if (!grnaccess)
                    res.send({ 'status': 'failed', 'message': 'User Don\'t have access' });
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Send User ID' });
            }

            let allCount = await inwardModel.getInwardCountByStatus({ user_id: userid })

            if (allCount != 0)
                res.send({ 'status': 'success', 'message': 'Data Found', 'data': allCount[0] });
            else res.send({ 'status': 'success', 'message': 'No Data Found' });
        }
    },
    /*
    This function gets all the polist and warehouse list
    Required Params legalentity id,userid
    return all polist and warehouse list
    */
    grnCreate: async function (req, res) {
        let result ='';
        var inwarddocs = [];
        var legalentity;
        var user_id;
        if (Object.keys(req.body.data).length > 0) {
            var params = req.body.data;
            params = JSON.parse(params);

            if (params.hasOwnProperty('user_id')) {
                var userid = params.user_id;
                /*await rolerepo.checkPermissionByFeatureCode('GRN001',userid).then((data)=>{
                    if(!data){
                        res.send({'status':'failed','message':'User Don\'t have access'});
                    }
                });*/
                var grnaccess = rolerepo.checkPermissionByFeatureCode('GRN002', userid);
                if (!grnaccess) {
                    //res.send({ 'status': 'failed', 'message': 'User Don\'t have access' });
                    return res.send({ 'status': '400', 'message': 'User Don\'t have access', 'data': []});
                }
            } else {
                return res.send({ 'status': '400', 'message': 'Please Send User ID', 'data': []});
                //res.send({ 'status': 'failed', 'message': 'Please Send User ID' });
            }

            if (params.hasOwnProperty('legal_entity_id')) {
                var legalentity = params.legal_entity_id;
            } else {
                return res.send({ 'status': '400', 'message': 'Please Send Legal Entity ID', 'data': []});
                //res.send({ 'status': 'failed', 'message': 'Please Send Legal Entity ID' });
            }

            if (params.hasOwnProperty('inwarddocs')) {
                inwarddocs = params.inwarddocs;
            }
            var suppliers = await grnModel.getSuppliers({ 'legalentity': legalentity, 'user_id': userid });
            //res.send({'suppliers':suppliers});
            polist = await grnModel.poList({ 'legalentity': legalentity, 'user_id': userid });
            var doctypes = await dispute.getDocumentTypes();
            //res.send({'polist':polist});
            if (typeof (polist) === "object" && polist.length > 0) {

                Promise.all(polist.map((value, key) => module.exports.loopingthroughPo(value, key))).then(data => {
                    result={ 'suppliers': suppliers,'polist': polist, 'doctypes':doctypes  };
                    //result.push({ 'polist': polist });
                    //return res.send({ 'data': polist });
                    return res.send({ 'status': '200', 'message': 'Data Found', 'data': result});
                });

            }
        }
    },
    loopingthroughPo: function (value, key) {
        return new Promise(async (resolve, reject) => {

            var poQty = await grnModel.getPOQtyById(value.po_id);
            var grnQty = await grnModel.getGrnQtyByPOId(value.po_id);
            if (grnQty.tot_received >= poQty.totpo_qty) {
                delete polist.value;
            }
            resolve();

        })

    },
    /*
    This function gets all the suppliers which are virtual suppliers and dc/fc suppliers
    Required Params legalentity id,userid
    return all suppliers list
    */
    getSuppliers: async function (req, res) {
        var poid;
        var warehouseOptions = [];
        var isDocumentRequired = 1;
        var suppliers = [];
        var warehouseOptions = [];
        if (Object.keys(req.body.data).length > 0) {
            var result = '';
            var params = req.body.data;
            params = JSON.parse(params);
            if (params.hasOwnProperty('poid')) {
                poid = params.poid;
            } else {
                poid = 0;
                //res.send({'status':'failed','message':'Please Send PO ID'});
            }
            if (params.hasOwnProperty('legal_entity_id')) {
                var legalentity = params.legal_entity_id;
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Send Legal Entity ID' });
            }
            if (params.hasOwnProperty('user_id')) {
                var userid = params.user_id;
                /*await rolerepo.checkPermissionByFeatureCode('GRN001',userid).then((data)=>{
                    if(!data){
                        res.send({'status':'failed','message':'User Don\'t have access'});
                    }
                });*/
                var grnaccess = rolerepo.checkPermissionByFeatureCode('GRN002', userid);
                if (!grnaccess) {
                    res.send({ 'status': 'failed', 'message': 'User Don\'t have access' });
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Send User ID' });
            }
            if (poid > 0) {
                suppliers.push({ '0': 'Please Select Supplier' });
                warehouseOptions.push({ '0': 'Select Delivery Location' });
            }
            var params = { 'poid': poid, 'legal_entity_id': legalentity, 'user_id': userid }
            var supplierdata = await grnModel.getPOSupplierProductList(params);
            //result.push({ 'supplierwarehousedata': supplierdata });
            //var podiscountData=await grnModel.getPODiscountDetails(params);
            //console.log(supplierdata.supplierList.legal_entity_id,'supplierdata.supplierList.legal_entity_id');
            var supp_legal_entity_id = supplierdata.supplierList.hasOwnProperty('legal_entity_id') ? supplierdata.supplierList.legal_entity_id : 0;
            var business_legal_name = supplierdata.supplierList.hasOwnProperty('business_legal_name') ? supplierdata.supplierList.business_legal_name : 0;
            var lp_wh_name = supplierdata.warehouselist.hasOwnProperty('lp_wh_name') ? supplierdata.warehouselist.lp_wh_name : 0;
            var le_wh_id = supplierdata.warehouselist.hasOwnProperty('le_wh_id') ? supplierdata.warehouselist.le_wh_id : 0;
            await grnModel.getPODiscountDetails(params).then((podiscountData) => {
                var isdiscount_before_tax = podiscountData.hasOwnProperty('discount_before_tax') ? podiscountData.discount_before_tax : 0
                var pobill_discount = podiscountData.hasOwnProperty('discount') ? podiscountData.discount.toFixed(5) : 0;
                var pobill_discount_type = podiscountData.hasOwnProperty('discount_type') ? podiscountData.discount_type : 0
                result = { 'legal_entity_id': supp_legal_entity_id, 'business_legal_name': business_legal_name, 'lp_wh_name': lp_wh_name, 'le_wh_id': le_wh_id, 'isdiscount_before_tax': isdiscount_before_tax, 'pobill_discount': pobill_discount, 'pobill_discount_type': pobill_discount_type };
                // result.push({ 'pobill_discount': pobill_discount });
                //result.push({ 'pobill_discount_type': pobill_discount_type });
            });
            //res.send({ "data": result });
            return res.send({ 'status': '200', 'message': 'Data Found', 'data': result });
        }

    },
    /*
    This function gets all grn products list based on poid
    Required Params legalentity id,userid,poid
    return all grn products based on poid i.e all the po products are added in grn products list
    */
    getGRNproducts: async function (req, res) {
        var poid = 0;
        var result = '';
        if (Object.keys(req.body.data).length > 0) {
            var result = [];
            var params = req.body.data;
            params = JSON.parse(params);
            if (params.hasOwnProperty('poid')) {
                poid = params.poid;
            } else {
                poid = 0;
            }
            if (params.hasOwnProperty('legal_entity_id')) {
                var legalentity = params.legal_entity_id;
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Send Legal Entity ID' });
            }
            if (params.hasOwnProperty('user_id')) {
                var userid = params.user_id;
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Send User ID' });
            }
            var params = { 'poid': poid, 'legal_entity_id': legalentity, 'user_id': userid }
            var productsdata = await grnModel.getPOGRNProductList(params);
            if (!productsdata)
                return res.send({ 'status': '400', 'message': 'Data Not Found', 'data': 'null' });
            var finalcalculations = (productsdata.length > 0) ? productsdata.reduce((a, b) => ({ total_qty: a.qty + b.qty, total_taxper: a.tax_per + b.tax_per, total_taxvalue: 0.00, base_total: 0.00, grand_total: 0.00 })) : {};
            //finalcalculations.push({'total_taxvalue':0.00,'base_total':0.00,'grand_total':0.00})
            //console.log(finalcalculations,'finalcalculationsfinalcalculationsfinalcalculations');
            result = { 'productsdata': productsdata, 'finalcalculations': finalcalculations };
            //res.send({ "data": result });
            return res.send({ 'status': '200', 'message': 'Data Found', 'data': result });
        } else {
            //res.send({ "data": "Please Send GRN Products" });
            return res.send({ 'status': '400', 'message': 'No Data Found', 'data': [] });
        }
    },


storeGRNData: async function (req, res) {
    sequelize.transaction(async transaction => {
        //try {
            if (typeof req.body.data != 'undefined' && req.body.data != '') {
                let data = JSON.parse(req.body.data);
                let reference_id = (data.reference_id)?(data.reference_id):0;
                let invoice_id = (data.invoice_id)?data.invoice_id:0;
                let invoice_date = data.invoice_date;
                let grn_supplier = (data.grn_supplier)?data.grn_supplier:0;
                let warehouse = (data.warehouse)?(data.warehouse):0;console.log('dhfggv',warehouse)
                let discount_on_bill = data.discount_on_bill;
                let on_bill_discount_type = (data.on_bill_discount_type == 'on') ? 1 : 0;
                let on_bill_discount_value = discount_on_bill;
                let discount_on_bill_options = (data.discount_on_bill_options == 'on') ? 1 : 0;
                if (discount_on_bill_options) {
                    discount_on_bill = 0;
                } else if (discount_on_bill > 0 && on_bill_discount_type == 1) {
                    discount_on_bill = data.discount_on_bill_value ? data.discount_on_bill_value : 0;
                }
                let shippingcost = data.shippingcost;
                let po_id = (data.po_id)?(data.po_id):0;
                let checkProducts = [];
                let checkGRN;
                var grnProductsList = Object.entries(data.po_products);
                //let grn_product_id = data.grn_product_id;
                //let grn_received = data.grn_received;
                let userid = data.userid;
                data.po_products.forEach((value, i) => { //needs to change
                    let result = {};
                    result['product_id'] = value.grn_product_id;
                    result['received_qty'] = value.grn_received;
                    checkProducts.push(result);
                });
                if (po_id > 0) {
                    checkGRN = await grnModel.checkGRNCreated(po_id, checkProducts);
                }
                else {
                    checkGRN = 1;
                }
                let checkOrderStatus = await grnModel.checkPOType(po_id);console.log('checkOrderStatus',checkOrderStatus)
                let po_so_order_code = (checkOrderStatus.po_so_order_code)?(checkOrderStatus.po_so_order_code):0;console.log('po_so_order_code',po_so_order_code)

                let gds_order_id = '';
                let checkInvoice;
                let poInfo = await grnModel.getPOInfo(po_id);
                let supply_le_wh_id = (poInfo.supply_le_wh_id) ? poInfo.supply_le_wh_id : 0;
                let disc_before_tax = (poInfo.discount_before_tax)?poInfo.discount_before_tax:0;
                let whdata = await purchaseOrder.getLEWHById(warehouse);
                let legalentityidofuser = await role.getLegalEntityId(userid);
                let is_create_order=0;
                if(checkOrderStatus!="" && checkOrderStatus.length>0) {
                    if(po_so_order_code!=0 && po_so_order_code!="") {
                        gds_order_id = await purchaseOrder.getOrderIdByCode(po_so_order_code);
                        checkInvoice = await grnModel.checkPOSOInvoiceStatus(gds_order_id);
                        if(checkInvoice.length == 0){
                            return res.send({ 'status': '400', 'message': 'PO Order not Invoiced!'});
                        } 
                    } else {
                        return res.send({ 'status': '400', 'message': 'Order not placed for PO!'});
                    }
                }
                if(checkGRN){
                    //console.log(checkProducts.map(function(obj){ return obj.product_id; }),'checkgrncreated');
                        let grn_type = (po_id > 0) ? 'PO' :'Manual';
                        let baseTotal = ('total_grn_basetotal' in data) ? data.total_grn_basetotal : 0.00;
                        let grandTotal = ('total_grn_grand_total' in data) ? data.total_grn_grand_total : 0.00;
                        let poApprovalStatus = await grnModel.getPoApprovalStatusByPoId(po_id);
                        let grn_products = checkProducts.map(function(obj){ return obj.product_id; });//('grn_product_id' in data) ? data.grn_product_id : [];

                        let poDetailArr = await purchaseOrder.getPoDetailById(po_id,legalentityidofuser);
                        //console.log(poDetailArr);
                        let stock_transfer=poDetailArr[0].hasOwnProperty('stock_transfer')?poDetailArr[0].stock_transfer:0;
                        let stock_transfer_dc=poDetailArr[0].hasOwnProperty('stock_transfer_dc')?poDetailArr[0].stock_transfer_dc:0;
                        let all_product_ids = (poDetailArr.map( el => el.product_id )).map(String);
                        //console.log(all_product_ids);
                        var missed_prd_ids = all_product_ids.filter(x => !grn_products.includes(x));
                        //console.log(missed_prd_ids+'missed_prd_ids');

                        if(poApprovalStatus==57107 && (grn_products.length != poDetailArr.length))
                        {
                            return res.send({ 'status': '400', 'message': 'Items missing!, Please refresh and try again!', 'data': []});
                        }
                        if(supply_le_wh_id != "" && supply_le_wh_id != 0){
                            let legalentityid = await purchaseOrder.getLegalEntityId(supply_le_wh_id);
                            legalentityid = legalentityid[0].legalentityid;
                            let checkLOC=await purchaseOrder.checkLOCByLeWhid(supply_le_wh_id);
                            let warehouse_data = await purchaseOrder.getWarehouseById(supply_le_wh_id);
                            let warehouse_name = warehouse_data[0].hasOwnProperty('display_name')?warehouse_data[0].display_name:"";
                            let availablebalance  = checkLOC - grandTotal;
                            if(availablebalance < 0)
                            {
                                return res.send({ 'status': '400', 'message': 'Insufficient balance for '+warehouse_name+' to place the order!', 'data': []});       
                            }
                            let margin = warehouse_data[0].hasOwnProperty('margin')?warehouse_data[0].margin:"";
                            if(margin === ''){
                             return res.send({ 'status': '400', 'message': 'Invalid Margin Defined  for '+warehouse_name, 'data': []});          
                            }

                            let customer_type_id = await purchaseOrder.getStockistPriceGroup(warehouse_data[0].legal_entity_id,supply_le_wh_id);
                            //console.log(customer_type_id[0].stockist_price_group_id+'customer_type_idcustomer_type_idcustomer_type_id');
                            customer_type_id=customer_type_id[0].stockist_price_group_id;
                            if(customer_type_id == 0){
                             return res.send({ 'status': '400', 'message': 'Pricing not found for DC/FC!', 'data': []});             
                            }
//return res.send("sreeee");return
                            let product_ids=[];
                            let elpproduct_ids =[];
                            let priceNotFoundData=[];
                            let elpvsespData=[];
                            var grn_received=0;
                            var grn_free=0;
                            var grn_damaged=0;
                            var grn_missed=0;
                            var grn_excess=0;
                            var grn_quarantine=0;
                            var totreceived =0;
                            // var grnProductsList = Object.entries(data.po_products);
                            for(const [key,grndata] of grnProductsList){
                                grn_received = parseInt((grndata.grn_received));
                                grn_free = (grndata.hasOwnProperty('grn_free'))?(grndata.grn_free):0;
                                grn_damaged = (grndata.hasOwnProperty('grn_damaged'))?(grndata.grn_damaged):0;
                                grn_missed = parseInt(grndata.grn_missed);
                                grn_excess = parseInt(grndata.grn_excess);
                                grn_quarantine = parseInt(grndata.grn_quarantine);
                                totreceived += grn_received-(grn_free+grn_damaged+grn_missed+grn_excess+grn_quarantine);
                                if(grn_received>0){
                                    let subTotal=0.00;
                                    let rowTotal=0.00;
                                    if(grndata.subTotal)
                                    subTotal = grndata.hasOwnProperty('subTotal')?(grndata.subTotal).replace(',',''):0.00;
                                    if(grndata.rowTotal)
                                    rowTotal = grndata.hasOwnProperty('rowTotal')?(grndata.rowTotal).replace(',',''):0.00;
                                    //console.log(subTotal+'subTotalsubTotalsubTotal');
                                    //console.log(rowTotal+'rowTotalrowTotalrowTotal');
                                    let discountType = 0;
                                    //console.log(data.grn_discount_type+'data.grn_discount_typedata.grn_discount_type');
                                    let discountTypeArray= grndata.hasOwnProperty('grn_discount_type')?grndata.grn_discount_type:0;

                                    if(discountTypeArray>0){
                                        
                                            discountType =1;
                                        
                                    }
                                    let discountIncTax = 0;
                                    let discountIncTaxArray = grndata.hasOwnProperty('grn_discount_inc_tax')?grndata.grn_discount_inc_tax:{};

                                    if(discountIncTaxArray>0){
                                        //if(discountIncTaxArray.includes(productId)){
                                            discountIncTax =1;
                                        //}
                                    }
                                    let goodQty = (grn_received - (grn_damaged + grn_missed + grn_quarantine+grn_free));
                                    if(discount_on_bill > 0 && disc_before_tax==0){
                                        let contribution = rowTotal/grandTotal;
                                        let finalRowDiscount = (contribution * discount_on_bill);
                                        var finalRowTotal = rowTotal-finalRowDiscount; 
                                    }else{
                                        var finalRowTotal = rowTotal;
                                    }
                                    let elp = parseFloat(finalRowTotal)/parseFloat(goodQty);
                                    let product_id = grndata.grn_product_id;
                                    let qty = totreceived;
                                    let appKeyData= process.env.DATABASE_NAME;
                                    let unitPriceData=[];
                                    let productSlabs = await purchaseOrder.ProdSlabFlatRefreshByProductId(product_id,warehouse);
                                    let keyString = appKeyData + '_product_slab_' + product_id + '_customer_type_' + customer_type_id+'_le_wh_id_'+warehouse;
                                    let response = await cache.get(keyString);
                                    //response = JSON.parse(response);
                                    // if (typeof response != 'undefined' && response != null) {
                                    //        //unitpriceData =  (response != '' ? response : []);
                                    // console.log(response,'000000000000000000');  
                                    // } 
                                    let temp  = warehouse.trim("'");
                                    temp = temp.replace(',','_');
                                    let contact_data = await purchaseOrder.getLEWHById(supply_le_wh_id);
                                    let mobile_number = contact_data.phone_no;
                                    var customer_data = await purchaseOrder.getCustomerDataByNo(mobile_number);
                                    let user_id = customer_data[0].user_id;
                                    //console.log(user_id);
                                    if(user_id==0){
                                        temp=0;
                                    }
                                    let availQty = await purchaseOrder.checkInventory(product_id,warehouse);
                                    let CheckUnitPrice;
                                    if(unitPriceData.hasOwnProperty('temp') && unitPriceData.length>0){
                                         CheckUnitPrice = unitPriceData.temp;
                                        let tempDetails = {};
                                        if(availQty){
                                            for(const slabData of CheckUnitPrice){
                                                if(slabData.hasOwnProperty('stock')){
                                                    slabData.stock = availQty;
                                                }
                                                tempDetails= slabData;
                                            }
                                        }
                                        if (!empty($tempDetails)) {
                                            $CheckUnitPrice = $tempDetails;
                                        }
                                        unitPriceData.temp = CheckUnitPrice;
                                        //Cache::put($keyString, json_encode($unitPriceData), 60);
                                    }else{
                                         CheckUnitPrice = await purchaseOrder.getProductSlabsByCust(product_id,warehouse,user_id,customer_type_id);
                                         unitPriceData.temp =JSON.parse(JSON.stringify(CheckUnitPrice));
                                        // unitPriceData = { [temp]: JSON.parse(JSON.stringify(CheckUnitPrice)) };
                                       //  cache.set(keyString, JSON.stringify(unitpriceData), function (error, done) {
                                       //       if (error) {
                                       //            console.log(error);
                                       //       } else {
                                       //            console.log("done=======>607", done);
                                       //       }
                                       //  }).catch(err => {
                                       //      console.log(err);
                                       //      return reject(err);
                                       // });
                                    }
                                    let packSizeArr=[];
                                    let poProductQty=0;
                                    let isFreebie = 0;
                                    isFreebie = await purchaseOrder.isFreebie(product_id);
                                    let productData = await grnModel.getProductInfo(product_id);
                                    if(!((CheckUnitPrice).length>0) && !isFreebie){
                                        product_ids.push(product_id);
                                        for(const price of CheckUnitPrice){
                                                if(Array.isArray(price)){
                                                    //packSizeArr.push({[price['pack_size']]:price['unit_price']});
                                                    packSizeArr[price['pack_size']]=price['unit_price'];
                                                }else if(typeof(price)=='object'){
                                                    //packSizeArr.push({[price.pack_size]:price.unit_price});
                                                    packSizeArr[price.pack_size]=price.unit_price;
                                                }
                                        }
                                            poProductQty=totreceived;
                                            let packSizePrice=await grnModel.getPackPrice(poProductQty,packSizeArr);
                                            if(packSizePrice==""){ // || packSizePrice.length>0
                                                CheckUnitPrice="";
                                            }else{
                                                CheckUnitPrice=packSizePrice;
                                            }
                                            let cp_enable_data = await purchaseOrder.getCPEnableData(product_id,warehouse);
                                            let cp_enable = (cp_enable_data.hasOwnProperty('cp_enabled') && cp_enable_data.cp_enabled==1)?"Yes":"No";
                                            let is_sellable = (cp_enable_data.hasOwnProperty('is_sellable') && cp_enable_data.is_sellable==1)?"Yes":"No";
                                            priceNotFoundData.push({"product_title":productData.product_title,"sku":productData.sku,"CheckUnitPrice":CheckUnitPrice,"cp_enable":cp_enable,"is_sellable":is_sellable});
                                    }else if(!isFreebie){
                                        for(const price of CheckUnitPrice){
                                        //console.log(price.pack_size,'pack_size');
                                            if(Array.isArray(price)){
                                                //packSizeArr.push({[price['pack_size']]:price['unit_price']});
                                                packSizeArr[price['pack_size']]=price['unit_price'];
                                            }else if(typeof(price)=='object'){
                                                packSizeArr[price.pack_size]=price.unit_price;
                                                //packSizeArr.push({[price.pack_size]:price.unit_price});
                                            }
                                        }
                                        poProductQty=totreceived;
                                        //console.log(productId,'productIdproductIdproductId');
                                        let packSizePrice=await grnModel.getPackPrice(poProductQty,packSizeArr);
                                        if(packSizePrice=="" ){ //|| packSizePrice.length>0
                                            CheckUnitPrice="";
                                        }else{
                                            CheckUnitPrice=packSizePrice;
                                        }

                                        if(CheckUnitPrice < Math.round(elp,5)){
                                            let mstdata = await purchaseOrder.getMasterLokup(78024);
                                            let ignore_mnf = mstdata.hasOwnProperty('description')?mstdata.description:"";
                                            let mnflist = ignore_mnf.split(',');
                                            let prd_manf = productData.manufacturer_id;
                                            if(!(mnflist.includes(prd_manf))){
                                                elpproduct_ids.push(product_id);
                                                elpvsespData.push({"product_title":productData.product_title,"sku":productData.sku,"elp":elp,"CheckUnitPrice":CheckUnitPrice});

                                            }
                                        }
                                    }
                                }
                            }
                            if(product_ids.length>0){
                                return res.send({ 'status': '401', "reason" : "Pricing Not Found!","message" : "Pricing mismatch found","adjust_message":"Please Clear Cache or Upload Prices",'data':priceNotFoundData});          
                            }
                            if(elpproduct_ids.length>0){
                                return res.send({ 'status': '401', "reason" : "ELP vs ESP!","message" : "Pricing mismatch found","adjust_message":"Please Check ELP & ESP",'data':elpvsespData});             
                            }   
                        }
                        if(Math.round(grandTotal,2)<=0){
                            return res.send({ 'status' : 400, 'message' : 'Grand Total Cannot Be Zero!'});             
                        }
                        let state_code = (whdata.state_code)? whdata.state_code:"TS";
                        let inward_code= await purchaseOrder.getReferenceCode("GR",state_code);
                        if(inward_code == ""){
                            return res.send({ 'status' : 400, 'message' : 'Serial No should not be empty.'});                
                        }

                        // code related to inventory batches
                        /*let code = poInfo.hasOwnProperty('po_so_order_code')?poInfo.po_so_order_code:0;
                        if(code==0){
                            let get_batch_id=0;
                        }else{
                            let wh_ID = poInfo.hasOwnProperty('le_wh_id')?poInfo.le_wh_id:'';
                            console.log(wh_ID,'wh_IDwh_IDwh_IDwh_ID');
                            if(wh_ID!=''){
                                let leTypeID = await grnModel.getLegalEntityTypeID(wh_ID);
                                let get_batch_id = await grnModel.getMainBatchID(code);

                                if(leTypeID==1016){
                                    get_batch_id= get_batch_id;
                                }
                                if(leTypeID==1014){
                                    get_batch_id = await grnModel.getFCLevelBatchID(get_batch_id);
                                }
                            } 
                        }*/
                        //invoice_date = invoice_date.split("/").reverse().join("-");
                        let grnArr=[];
                        inward_code=inward_code;
                        grnArr['inward_type']=0;//grn_type;
                        grnArr['po_no']=po_id;
                        grnArr['inward_code']=inward_code;
                        grnArr['inward_ref_no']=reference_id;
                        grnArr['invoice_no']=invoice_id;
                        grnArr['invoice_date']=invoice_date;
                        grnArr['legal_entity_id']=grn_supplier;
                        grnArr['currency_id']=4;
                        grnArr['discount_on_bill_options']=discount_on_bill_options;
                        grnArr['on_bill_discount_type']=on_bill_discount_type;
                        grnArr['on_bill_discount_value']=on_bill_discount_value;
                        grnArr['discount_on_total']=discount_on_bill;
                        grnArr['discount_before_tax']=disc_before_tax;
                        grnArr['shipping_fee']=shippingcost;
                        grnArr['base_total']=baseTotal;
                        grnArr['grand_total']=grandTotal;
                        grnArr['le_wh_id']=warehouse;
                        grnArr['inward_status']=76001;
                        grnArr['approval_status']=57023;
                        grnArr['remarks']=data.grn_remarks1;
                        grnArr['created_by']=data.userid;
                        //grnArr['main_batch_id']=get_batch_id;
             //       }
                    
                    // sequelize.transaction(async transaction => {
                    //     try {
                            //let inward_id = 1;
                            let inward_id=await grnModel.grnSave(grnArr);console.log('inward_id',inward_id);
                            // let inward_id=15695;
                            if(inward_id>0){
                                let grnProducts=[];
                                let inputTax =[];
                                let grnProductsDetails=[];
                                let stockInward=[];
                                let productInfo = [];
                                var grn_received=0;
                                var grn_free=0;
                                var grn_damaged=0;
                                var grn_missed=0;
                                var grn_excess=0;
                                var grn_quarantine=0;
                                var totreceived =0;
                                for(const [key,grndata] of grnProductsList){
                                    let productId =grndata.grn_product_id;
                                    let grnProductsData=[];
                                    let inputTaxData=[];
                                    let grnProductsDetailsData={};
                                    let stockInwardData = [];
                                    grn_received = parseInt(grndata.grn_received);
                                    grn_free = (grndata.hasOwnProperty('grn_free'))?(grndata.grn_free):0;
                                    grn_damaged = (grndata.hasOwnProperty('grn_damaged'))?(grndata.grn_damaged):0;
                                    grn_missed = parseInt(grndata.grn_missed);
                                    grn_excess = parseInt(grndata.grn_excess);
                                    grn_quarantine = parseInt(grndata.grn_quarantine);
                                    totreceived += grn_received-(grn_free+grn_damaged+grn_missed+grn_excess+grn_quarantine);
                                    if(grn_received>0){
                                        let subTotal=0.00;
                                        let rowTotal=0.00;
                                        if(grndata.subTotal)
                                        subTotal = grndata.hasOwnProperty('subTotal')?(grndata.subTotal).replace(',',''):0.00;
                                        if(grndata.rowTotal)
                                        rowTotal = grndata.hasOwnProperty('rowTotal')?(grndata.rowTotal).replace(',',''):0.00;
                                        let discountType = 0;
                                        let discountTypeArray= grndata.hasOwnProperty('grn_discount_type')?grndata.grn_discount_type:0;

                                        if(discountTypeArray>0){
                                            if(discountTypeArray.includes(productId)){
                                                discountType =1;
                                            }
                                        }
                                        let discountIncTax = 0;
                                        let discountIncTaxArray = grndata.hasOwnProperty('grn_discount_inc_tax')?grndata.grn_discount_inc_tax:{};

                                        if(discountIncTaxArray>0){
                                            if(discountIncTaxArray.includes(productId)){
                                                discountIncTax =1;
                                            }
                                        }
                                        let goodQty = (grn_received - (grn_damaged + grn_missed + grn_quarantine+grn_free));
                                        if(discount_on_bill > 0 && disc_before_tax==0){
                                            let contribution = rowTotal/grandTotal;
                                            let finalRowDiscount = (contribution * discount_on_bill);
                                            var finalRowTotal = rowTotal-finalRowDiscount; 
                                        }else{
                                            var finalRowTotal = rowTotal;
                                        }
                                        let elp = parseFloat(finalRowTotal)/parseFloat(goodQty);
                                        var po_tax_data = await grnModel.getPOProductTaxData(po_id,productId);
                                        var tax_data = JSON.parse(po_tax_data.tax_data);
                                        var hsn_code = (po_tax_data.hasOwnProperty('hsn_code'))?po_tax_data.hsn_code:'';
                                        var taxGSTAmnt = 0;
                                        var taxGSTPer = 0;
                                        if(tax_data.length>0){
                                            tax_data.forEach((tax,keyt) => {
                                                var tax_per = (tax['Tax Percentage'])?tax['Tax Percentage']:0;
                                                var tax_type = (tax['Tax Type'])?tax['Tax Type']:'';
                                                var taxAmnt = (subTotal * tax_per)/100;
                                                if(tax_type.includes('GST','IGST','UTGST')){
                                                    taxGSTAmnt = taxAmnt;
                                                    taxGSTPer = tax_per;
                                                }
                                                var CGST = (tax['CGST']) ? tax['CGST'] : 0;
                                                var IGST = (tax['IGST']) ? tax['IGST'] : 0;
                                                var SGST = (tax['SGST']) ? tax['SGST'] : 0;
                                                var UTGST = (tax['UTGST']) ? tax['UTGST'] : 0;
                                                CGST = (CGST/100) * taxAmnt;
                                                IGST = (IGST/100) * taxAmnt;
                                                SGST = (SGST/100) * taxAmnt;
                                                UTGST = (UTGST/100) * taxAmnt;
                                                tax_data[keyt]['taxAmt'] = taxAmnt;

                                                tax_data[keyt]['CGST_VALUE'] = CGST;
                                                tax_data[keyt]['IGST_VALUE'] = IGST;
                                                tax_data[keyt]['SGST_VALUE'] = SGST;
                                                tax_data[keyt]['UTGST_VALUE'] = UTGST;
                                                var datetime = new Date();
                                                var transaction_date=datetime.toISOString().slice(0,10);
                                                inputTaxData['inward_id']=inward_id;
                                                inputTaxData['product_id']=grndata.grn_product_id;
                                                inputTaxData['transaction_no']=reference_id;
                                                inputTaxData['transaction_type']=101001;
                                                inputTaxData['transaction_date']= transaction_date;
                                                inputTaxData['tax_type']=grndata.hasOwnProperty('grn_taxtype')?(grndata.grn_taxtype):0;
                                                inputTaxData['tax_percent']=grndata.hasOwnProperty('grn_taxper')?(grndata.grn_taxper):0;
                                                inputTaxData['tax_amount']=grndata.hasOwnProperty('grn_taxvalue')?(grndata.grn_taxvalue):0;
                                                inputTaxData['le_wh_id']=warehouse;
                                                inputTaxData['created_by']=data.userid;

                                                inputTax.push(inputTaxData);
                                            });
                                        }
                                        grnProductsData['inward_id']=inward_id;
                                        grnProductsData['product_id']=grndata.grn_product_id;
                                        grnProductsData['orderd_qty']=grndata.hasOwnProperty('grn_po_qty')?(grndata.grn_po_qty):0;
                                        grnProductsData['received_qty']=grn_received;
                                        grnProductsData['good_qty']=(grn_received - (grn_damaged + grn_missed + grn_quarantine));
                                        grnProductsData['free_qty']=grn_free;
                                        grnProductsData['damage_qty']=grn_damaged;
                                        grnProductsData['missing_qty']=grn_missed;
                                        grnProductsData['excess_qty']=grn_excess;
                                        grnProductsData['quarantine_stock']=grn_quarantine;
                                        grnProductsData['cur_elp']=elp;
                                        grnProductsData['price']=grndata.hasOwnProperty('grn_base_price')?(grndata.grn_base_price):0;
                                        grnProductsData['tax_per']=(taxGSTPer)?taxGSTPer:0;
                                        grnProductsData['tax_amount']=(taxGSTAmnt)?taxGSTAmnt:0;
                                        grnProductsData['hsn_code']=(hsn_code)?hsn_code:0;
                                        grnProductsData['tax_data']=(tax_data)?JSON.stringify(tax_data):0;
                                        grnProductsData['discount_type']=discountType;
                                        grnProductsData['discount_inc_tax']=discountIncTax;
                                        grnProductsData['discount_percentage']=grndata.hasOwnProperty('grn_discount_percent')?(grndata.grn_discount_percent):0;
                                        grnProductsData['discount_total']=grndata.hasOwnProperty('grn_discount_amount')?(grndata.grn_discount_amount):0;
                                        grnProductsData['sub_total']=subTotal;
                                        grnProductsData['row_total']=rowTotal;
                                        grnProductsData['remarks']=grndata.hasOwnProperty('grn_remarks')?(grndata.grn_remarks):0;
                                        grnProductsData['created_by']=data.userid;

                                        grnProducts.push(grnProductsData);                                 
                                        if(grndata.hasOwnProperty('PackDetails')){
                                             //var inwardPrdId;
                                            for(const packdata of grndata.PackDetails){
                                                let inwardPrdId="(SELECT inward_prd_id from inward_products where product_id="+productId+" and inward_id="+inward_id+")";
                                                grnProductsDetailsData['inward_prd_id'] = inwardPrdId;
                                                grnProductsDetailsData['product_id'] = productId;
                                                //packdata.grn_product_id;
                                                grnProductsDetailsData['pack_level'] = (packdata.packsize);//[productId].hasOwnProperty(detkey))?(grndata.grn_packsize):0;
                                                grnProductsDetailsData['pack_qty'] = (packdata.eachesqty);//[productId].hasOwnProperty(detkey))?(data.grn_eachesqty)[productId][detkey]:0;
                                                grnProductsDetailsData['received_qty'] = (packdata.receivedqty);//[productId].hasOwnProperty(detkey))?(data.grn_receivedqty)[productId][detkey]:0;
                                                grnProductsDetailsData['tot_rec_qty'] = (packdata.receivedtotal);//[productId].hasOwnProperty(detkey))?(data.grn_receivedtotal)[productId][detkey]:0;
                                                grnProductsDetailsData['mfg_date'] = (packdata.pkmfg_date);//((data.grn_pkmfg_date)[productId].hasOwnProperty(detkey))?((data.grn_pkmfg_date)[productId][detkey][0]).split("-").reverse().join("-"):0;
                                                grnProductsDetailsData['exp_date'] = (packdata.pkexp_date);//[productId][0]);//.split("-").reverse().join("-");//((data.grn_pkexp_date)[productId].hasOwnProperty(detkey))?((data.grn_pkexp_date)[productId][detkey][0]).split("-").reverse().join("-"):0;
                                                grnProductsDetailsData['freshness_per'] = (packdata.freshness);//[productId].hasOwnProperty(detkey))?(data.grn_freshness_percentage)[productId][detkey]:0;
                                                grnProductsDetailsData['remarks'] = (packdata.pack_remarks)?(packdata.pack_remarks) :"";//[productId].hasOwnProperty(detkey))?(data.grn_pack_remarks)[productId][detkey]:0;
                                                grnProductsDetailsData['status'] = (packdata.pack_status)?(packdata.pack_status):"";//[productId].hasOwnProperty(detkey))?(data.grn_pack_status)[productId][detkey]:0;
                                                grnProductsDetailsData['created_by'] = data.userid;
                                            }
                                            grnProductsDetails.push(grnProductsDetailsData);
                                        }
                                        var checkStockInward = await grnModel.checkStockInward(inward_id,grndata.grn_product_id);console.log('checkStockInwardcheckStockInward',checkStockInward)
                                        if(!checkStockInward){
                                            stockInwardData['le_wh_id']=warehouse;
                                            stockInwardData['product_id']=grndata.grn_product_id;
                                            stockInwardData['good_qty']=(grn_received -(grn_damaged + grn_missed + grn_quarantine));
                                            stockInwardData['free_qty']=grn_free;
                                            stockInwardData['dnd_qty']=grn_missed;
                                            stockInwardData['dit_qty']=grn_damaged;
                                            stockInwardData['quarantine_qty']=grn_quarantine;
                                            stockInwardData['po_no']=po_id;
                                            stockInwardData['reference_no']=inward_id;
                                            stockInwardData['inward_date']=Currentdate;//currentdate
                                            stockInwardData['status']='999';
                                            stockInwardData['created_by']=data.userid;

                                            stockInward.push(stockInwardData);

                                            var productData=[];
                                            productData['product_id'] = grndata.grn_product_id;
                                            productData['soh'] = (grn_received - 
                                                        (grn_damaged + grn_missed + grn_quarantine));
                                            productData['qty'] = (grn_received - 
                                                        (grn_damaged + grn_missed + grn_quarantine));
                                            productData['free_qty'] = grn_free;
                                            productData['quarantine_qty'] = grn_quarantine;
                                            productData['dit_qty'] = grn_damaged;  //damage in transit
                                            productData['dnd_qty'] = grn_missed;
                                            productData['elp'] = elp;
                                            productData['manf_date'] = (grndata.grn_pkmfg_date);
                                            productData['exp_date'] = (grndata.grn_pkexp_date);
                                            var actual_po_quantity = (grndata.po_numof_eaches);
                                            var pending_qty = actual_po_quantity- (productData['soh']);
                                            var pendingproductData=[];//['returns']
                                            if(pending_qty >0 && gds_order_id>0){
                                                var has_parent = await purchaseOrder.isFreebie(grndata.grn_product_id);
                                                var return_id = 59006;
                                                var product = await grnModel.getProductByOrderId(gds_order_id,[grndata.grn_product_id]);
                                                console.log('productproductproductproduct',product)
                                                //product = product[0];
                                                var tax_per_object = await grnModel.getTaxPercentageOnGdsProductId(product.gds_order_prod_id);
                                                var tax_per = tax_per_object.tax_percentage;
                                                var singleUnitPrice = ((product.total / (100+tax_per)*100) / product.qty);
                                                var singleUnitPriceWithtax = ((tax_per/100) * singleUnitPrice) + singleUnitPrice;
                                                var return_total = pending_qty * singleUnitPriceWithtax;       
                                                pendingproductData['product_id'] =  grndata.grn_product_id;console.log('grndata.grn_product_id',grndata.grn_product_id)
                                                pendingproductData['return_qty'] =  pending_qty;
                                                pendingproductData['delivered_qty'] =  productData['soh'];
                                                pendingproductData['return_reason'] =  return_id;
                                                pendingproductData['has_parent'] =  has_parent;
                                                pendingproductData['return_total'] =  return_total;
                                                productInfo.push(['returns',pendingproductData]);
                                            }
                                        }
                                    }else{
                                                var pendingproductData=[];//['returns']
                                                if(gds_order_id > 0){
                                                    var has_parent = await purchaseOrder.isFreebie(grndata.grn_product_id);
                                                    var return_id = 59006;
                                                    var product = await grnModel.getProductByOrderId(gds_order_id,[grndata.grn_product_id]);
                                                    //product = product[0];
                                                    var tax_per_object = await grnModel.getTaxPercentageOnGdsProductId(product.gds_order_prod_id);
                                                    var tax_per = tax_per_object.tax_percentage;
                                                    var singleUnitPrice = ((product.total / (100+tax_per)*100) / product.qty);
                                                    var singleUnitPriceWithtax = ((tax_per/100) * singleUnitPrice) + singleUnitPrice;
                                                    var return_total = pending_qty * singleUnitPriceWithtax;       
                                                    pendingproductData['product_id'] =  grndata.grn_product_id;
                                                    pendingproductData['return_qty'] =  pending_qty;
                                                    pendingproductData['delivered_qty'] =  productData['soh'];
                                                    pendingproductData['return_reason'] =  return_id;
                                                    pendingproductData['has_parent'] =  has_parent;
                                                    pendingproductData['return_total'] =  return_total;
                                                    productInfo.push(['returns',pendingproductData]);
                                                }
                                    }
                                }
                                if(gds_order_id > 0 && missed_prd_ids.length>0){
                                    var pendingproductData=[];
                                    for(const [key,value] of missed_prd_ids){

                                        var productId = value;
                                        var has_parent = await purchaseOrder.isFreebie(product_id);
                                        var return_id = 59006;
                                        var product = await grnModel.getProductByOrderId(gds_order_id,[productId]);
                                        //product = product[0];
                                        var tax_per_object = await grnModel.getTaxPercentageOnGdsProductId(product.gds_order_prod_id);
                                        var tax_per = tax_per_object.tax_percentage;
                                        var singleUnitPrice = ((product.total / (100+tax_per)*100) / product.qty);
                                        var singleUnitPriceWithtax = ((tax_per/100) * singleUnitPrice) + singleUnitPrice;
                                        var return_total = pending_qty * singleUnitPriceWithtax;       
                                        var actual_po_quantity = product.qty;
                                        // var pendingproductData=[];
                                        pendingproductData['product_id'] =  productId;
                                        pendingproductData['return_qty'] =  pending_qty;
                                        pendingproductData['delivered_qty'] =  productData['soh'];
                                        pendingproductData['return_reason'] =  return_id;
                                        pendingproductData['has_parent'] =  has_parent;
                                        pendingproductData['return_total'] =  return_total;
                                        productInfo.push(['returns',pendingproductData]);
                                    }
                                }
                                
                                 await grnModel.saveGrnProducts(grnProducts);
                                 await grnModel.saveInputTax(inputTax);                              
                                 await grnModel.saveGrnProductDetails(grnProductsDetails);

                                var updateResponse =await module.exports.grnUpdateStatus(inward_id,inward_code,po_id, data.userid,stockInward,productInfo,warehouse,stock_transfer,stock_transfer_dc).catch(err=>{ console.log('grnUpdateStatus');transaction.rollback(); });
                                if(updateResponse.hasOwnProperty('status') && updateResponse.status==400){
                                    var message = '';
                                    transaction.rollback();
                                    message = updateResponse.hasOwnProperty('message')?updateResponse.message:'';
                                    return res.send({ 'status' : 400, 'message' : message,'inward_id':0});             
                                }else{
                                    //autocpenable functionality to be written queues functionality
                                    if(supply_le_wh_id !="" && supply_le_wh_id !=0){
                                        //var posodata = await module.exports.createPoByData(inward_id,po_id,warehouse,supply_le_wh_id);
                                        is_create_order=1;
                                    }
                                    var returnsdata=productInfo.map(el => el.returnsdata);
                                    return res.send({ 'status' : 200, 'message' : 'GRN has been created successfully.', 'inward_id' : inward_id,'supplyLeWhId':supply_le_wh_id,'isCreateOrder':is_create_order,'isDeliverOrder':updateResponse.data,'gdsOrderId':gds_order_id,'returnsData':returnsdata});             

                                }
                            }else{
                                    transaction.rollback();
                                    return res.send({ 'status' : 400, 'message' : 'Please input valid data.'});             
                            }

                    //      } catch (error) {
                    //           transaction.rollback();
                    //           throw `TRANSACTION_ERROR`;
                    //     }
                    // })
                }else{
                    console.log('transactionrollback1');
                        transaction.rollback();
                        return res.send({ 'status' : 400, 'message' : 'GRN already created.'});             
                }
            }
       //  }catch (error) {
       //      console.log('transactionrollback');
       // //     console.log(error);
       //      transaction.rollback();
       //      //throw `TRANSACTION_ERROR`;
       //      return res.send({ 'status' : 400, 'message' : 'Please input valid dataend.'});             
       //  }
    });
    /*.catch(err => {
        console.log(err);
    //    transaction.rollback();
        //throw `TRANSACTION_ERROR`;
        return res.send({ 'status' : 400, 'message' : 'Please input valid data.'});             
    });*/
},

    loopingthroughgrnproduts: function (value, index, grnreceivedqty) {
        return new Promise((resolve, reject) => {
            checkProducts[index] = [];
            checkProducts[index].push({ "product_id": value });
            checkProducts[index].push({ "received_qty": grnreceivedqty['grn_received'][index] });
        });
    },

    grnUpdateStatus:async function(inward_id,inward_code,po_id, userid,stockInward,productInfo,warehouse,stock_transfer,stock_transfer_dc){
        return new Promise(async (resolve,reject)=>{
        try{
            let is_deliver_order=0;
            //await grnModel.updateTaxValues(inward_id); // functionality pending //done
            var totalPoQty = await purchaseOrder.getPoQtyByPoId(po_id);
            var poApprovalStatus = await grnModel.getPoApprovalStatusByPoId(po_id);
            var poStatusArray ={'po_status':87002,'approval_status':57035};
            purchaseOrder.updatePO(po_id,poStatusArray);
            await grnModel.updateElpData(inward_id,userid); 
            if(stockInward.length>0 && productInfo){
                await grnModel.saveStockInwardNew(inward_code,warehouse,stockInward,productInfo,stock_transfer,stock_transfer_dc,po_id);
            }else{
                await grnModel.saveStockInward(inward_id,CurrentdateTime); //pending
            }
            await grnModel.assetProductDetails(inward_id); //pending
            var checkOrderStatus = await grnModel.checkPOType(po_id);
            var gds_order_id = "";
            var checkIncvoice = [];

                if (checkOrderStatus != '' && checkOrderStatus.length > 0) {
                    if (checkOrderStatus.po_so_order_code != '0' && checkOrderStatus.po_so_order_code != '') {
                        gds_order_id = await purchaseOrder.getOrderIdByCode(checkOrderStatus.po_so_order_code);
                        checkIncvoice = await grnModel.checkPOSOInvoiceStatus(gds_order_id);
                    
                        if(checkIncvoice.length==0){
                            return resolve({ 'status' : 400, 'message' : 'PO Order not Invoiced!.'});              
                        }
                    }
                }

                //send elpNotification,stroreworkflowHistory pending

                var totalInwardQty = await grnModel.getTotalInwardQtyById(po_id);

                if (totalInwardQty < totalPoQty) {
                    if (poApprovalStatus == 57119) {
                        await grnModel.createSubPo(po_id, userid);
                    } else if (poApprovalStatus == 57107) {
                        return resolve({ 'status': 400, 'message': 'Cannot create GRN with partial quantity' });
                    }
                }

                var deliverPOSOOrder = "";
                if (checkIncvoice.length > 0) {

                // returns=productInfo.map(el => el.returns);
                //deliverPOSOOrder= await module.exports.deliverPOSOOrder(gds_order_id,po_id,userid,returns).catch(err=>{ console.log(err); });
                is_deliver_order=1;
            }

            // addNotifications is pending
            // emailAttachment is pending
            return resolve({ 'status' : 200, 'message' : '','data':is_deliver_order});             
        }catch (error) {
            console.log(error);
//            transaction.rollback();
            //throw `TRANSACTION_ERROR`;
            return resolve({ 'status' : 400, 'message' : 'Please input valid dataend.'});             
        }
        });
    },
    
    fullDeliver: async (req, res) => {
        //try {
            if (Object.keys(req.body.data).length > 0) {
                var data = JSON.parse(req.body.data);
                let gds_order_id = data.gds_order_id;
                let po_id=data.po_id;
                let userid=data.user_id;    
                let deliverPOSOOrder= await module.exports.deliverPOSOOrder(gds_order_id,po_id,userid);
                return res.send(deliverPOSOOrder);
            }else{
                res.send({ 'status': 'failed', 'message': 'Please send valid input' });
            }
        // } catch (err) {
        //     res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime' });
        // }
    },

    deliverPOSOOrder : async function(orderId,po_id,userid,returns=[]){
        //try{
            var invoiceInfo = await grnModel.getInvoiceGridOrderId(orderId,'grid.gds_invoice_grid_id,grid.grand_total,grid.ecash_applied'); 
            var user_legal_entity_id = await role.getLegalEntityId(userid);  
            var poDetailArr = await purchaseOrder.getPoDetailById(po_id,user_legal_entity_id);
            var legal_entity_id = poDetailArr[0].legal_entity_id;
            var deliveryData = await grnModel.getUserByLegalEntityId(legal_entity_id);
            var token;
            if (deliveryData) {
                userid = deliveryData.user_id;
                token = deliveryData.password_token;
            } else {
                token = await purchaseOrder.getTokenByUserId(userid);
            }

            if(!invoiceInfo.hasOwnProperty('gds_invoice_grid_id')){
                return ({ 'status' : 400, 'message' : 'Order not yet Invoiced!'});                    
            }

            var order_data = await grnModel.getOrderInfo(orderId, 'order_status_id');

            var deliver_array = ['17007', '17022', '17023', '17008'];

            if(order_data.hasOwnProperty('order_status_id') && deliver_array.indexOf(order_data.order_status_id)){
                //return ({ 'status' : 200, 'message' : 'Order Already Delivered!'});   
            }

            var return_total = returns.map(el => el.return_total);
            return_total = return_total.reduce((a, b) => a + b, 0);

            var invoiceId = (invoiceInfo.gds_invoice_grid_id)?(invoiceInfo.gds_invoice_grid_id):0;
            var ecash_applied = invoiceInfo.ecash_applied;
            var invoiceAmt = invoiceInfo.grand_total;
            var checkEcash = (invoiceAmt - return_total) - ecash_applied;
            var le_wh_id = poDetailArr[0].le_wh_id;
            var contact_data = await purchaseOrder.getLEWHById(le_wh_id);
            var cust_legal_entity_id = contact_data.legal_entity_id;
            var credit_limit_check = contact_data.credit_limit_check;
            var checkLOC = await purchaseOrder.checkLOCByLeID(cust_legal_entity_id);
            var checkDeliveryLoc = checkLOC - invoiceAmt;

            if(credit_limit_check==1){
                if(Math.floor(checkEcash)>0){
                    return ({ 'status' : 200, 'message' : 'Insufficient wallet balance to deliver the order!'});          
                }
            }

            var url = process.env.DELIVER_URL;
            var data = {};
            data.flag=2;
            data.deliver_token=token;
            data.module_id=2;
            data.user_id=userid;
            data.order_id=orderId;
            data.invoice_id=invoiceId;
            data.net_amount=invoiceAmt - return_total;
            data.amount=invoiceAmt - return_total;
            data.amount_collected=0;
            data.amount_credit=0;
            data.collectable_amt=0;
            data.amount_return=return_total;
            data.payment_mode=22010;
            data.reference_no='--NA--';
            data.round_of_value=invoiceAmt - Math.round(invoiceAmt,2);
            data.discount_applied=0;
            data.discount_deducted=0;
            data.ecash_applied= ecash_applied - return_total;
            data.returns=returns;
            data.payments=[];

            // var data = [];
            // data['flag']=2;
            // data['deliver_token']=token;
            // data['module_id']=2;
            // data['user_id']=userid;
            // data['order_id']=orderId;
            // data['invoice_id']=invoiceId;
            // data['net_amount']=invoiceAmt - return_total;
            // data['amount']=invoiceAmt - return_total;
            // data['amount_collected']=0;
            // data['amount_credit']=0;
            // data['collectable_amt']=0;
            // data['amount_return']=return_total;
            // data['payment_mode']=22010;
            // data['reference_no']='--NA--';
            // data['round_of_value']=invoiceAmt - Math.round(invoiceAmt,2);
            // data['discount_applied']=0;
            // data['discount_deducted']=0;
            // data['ecash_applied']= ecash_applied - return_total;
            // data['returns']=returns;
            // data['payments']=[];

            var response = await grnModel.getInvoiceByReturn(data,url); //  make curl call
            console.log('outrespo',response);
            if(response.status=='success'){
                var orderData = await grnModel.getOrderByOrderId(orderId);
                var collections = await grnModel.collectionDetailsById(orderId);
                var le_wh_id = orderData.le_wh_id;
                var hub_id = orderData.hub_id;
                var colremArray = [];

                colremArray['collected_amt'] = invoiceAmt;
                colremArray['remittance_code'] = (collections.collection_code)?(collections.collection_code):0;
                colremArray['acknowledged_by'] = userid;
                colremArray['hub_id'] = hub_id;
                colremArray['le_wh_id'] = le_wh_id;
                colremArray['by_ecash'] = invoiceAmt;
                colremArray['submitted_at'] = CurrentdateTime; //date
                colremArray['submitted_by'] = userid;
                colremArray['acknowledged_at'] = CurrentdateTime; //date
                colremArray['approval_status'] = 57052;

                var collectionRemittanceMappingId = await grnModel.collectionRemittanceMapping(colremArray);
                var remArray = [];

                remArray["collection_id"] = (collections.collection_id)?collections.collection_id:0;
                remArray["remittance_id"] = collectionRemittanceMappingId;

                var remittanceMappingId = await grnModel.remittanceMapping(remArray);

                return ({ 'status' : 200, 'message' : 'Your Order has been delivered successfully.'});          
            }else{
                return ({ 'status' : 200, 'message' : response.taxinfo});          
            }

//         }catch (error) {
// //            transaction.rollback();
//             //throw `TRANSACTION_ERROR`;
//             return res.send({ 'status' : 400, 'message' : 'Please input valid dataend.'});             
//         }
    },

    enableCp: async function (inward_id, user_id) {
        //try{
        var products = await grnModel.getGrnProductDetails(inward_id);
        var grnDeatil = await inwardModel.getInwardDetail(inward_id);
        var le_wh_id = grnDeatil[0].le_wh_id;
        for (const [key, value] of products) {
            var product_id = value.product_id;
            var check_prop = await grnModel.checkProductProp(product_id, le_wh_id);
            var checkFreebiee = await grnModel.getFreebieParent(product_id);
            if (!(checkFreebiee.main_prd_id) && check_prop) {
                var is_sellable = await grnModel.isSellable(product_id, le_wh_id, user_id, CurrentdateTime);
                if (is_sellable.includes("Successfully")) {
                    var cp_enable = await grnModel.cpEnabled(product_id, le_wh_id, user_id, date);
                    if (cp_enable.includes("Successfully") || cp_enable.includes("Already") || cp_enable.includes("Failed")) {
                        var check_warehouseproductid = await grnModel.checkWhProductId(product_id, le_wh_id);
                        if (check_warehouseproductid == 1) {
                            var esu = await grnModel.getProductSU(product_id);
                            await grnModel.updateProductEsu(product_id, le_wh_id, esu.esu, date);
                        }
                    }
                }
            }
        }
        // }catch (error) {
        //     return res.send({ 'status' : 'failed', 'message' : 'Please input valid data'});       
        // }
    },

    /*
    author : Muzzamil,
    req.body : {"supplier_id" : "17529" , "le_wh_id":"10696" , "term":"Britannia"},

    */

    getSkus: (req, res) => {

        try {
            if (typeof req.body.data != 'undefined' && req.body.data != '') {
                let data = JSON.parse(req.body.data);
                if (data.hasOwnProperty('supplier_id') && data.supplier_id != '') {
                    if (data.hasOwnProperty('le_wh_id') && data.le_wh_id != '') {
                        if (data.hasOwnProperty('term') && data.term != '') {
                            grnModel.getSkus(data.supplier_id, data.le_wh_id, data.term).then(response => {
                                res.status(200).json({ 'status': 'success', 'message': 'getSkus response', 'data': response });
                            }).catch(err => {
                                console.log(err);
                            })
                        } else {
                            console.log('term');
                            return res.status(200).json({ 'status': 'failed', 'message': 'term not sent' });
                        }
                    } else {
                        console.log('le_wh_id');
                        return res.status(200).json({ 'status': 'failed', 'message': 'le_wh_id not sent' });
                    }
                } else {
                    console.log('send supplier_id');
                    return res.status(200).json({ 'status': 'failed', 'message': 'supplier_id not sent' });
                }
            } else {
                return res.status(200).json({ "status": 'failed', 'message': "Request body not sent" })
            }
        } catch (err) {
            return res.staus(200).json({ 'status': 'failed', 'message': "cpMessage.serverError" })
        }
    },



    /*
    author : Muzzamil,
    req.body : {"packproduct_id":"1560","pack_size":"16001","uomqty":"1","rqty":"1"
                ,"qtytotal":"1","mfg_date":"12\/26\/2019","free":"0","damaged":"0"},
    */
    getPackText: async (req, res) => {
        try {
            if (typeof req.body.data != "undefined" && req.body.data != '') {
                let data = JSON.parse(req.body.data);
                let product_id = data.packproduct_id;
                let shelfLife = await grnModel.getProductShelfLife(product_id);
                let shelf_life = shelfLife.map(x => {
                    return x.shelf_life;
                });
                shelf_life = shelf_life[0];
                let shelfuom = shelfLife.map(x => {
                    return x.master_lookup_name;
                });

                shelfuom = shelfuom[0];

                let exp_date;
                let shelfLifePercentage = 0;
                let totdays;
                let currentdate;
                let remaindays;
                let mfg_date;
                if (typeof data.mfg_date != undefined && data.mfg_date != '') {
                    // console.log(data.mfg_date);
                    mfg_date = moment(data.mfg_date).format('DD-MM-YYYY HH:mm:mm');
                    if (shelf_life > 0) {
                        exp_date = moment(data.mfg_date, "MM-DD-YYYY").add(`${shelf_life}`, `${shelfuom}`).format('DD-MM-YYYY HH:mm:mm');
                    } else {
                        exp_date = new Date();
                        exp_date = moment(exp_date).format('DD-MM-YYYY HH:mm:mm');
                    }

                    totdays = moment(exp_date, "DD-MM-YYYY").diff(moment(data.mfg_date, "MM-DD-YYYY"), 'Days');
                    currentdate = moment(new Date(), "DD-MM-YYYY").format("DD-MM-YYYY");
                    remaindays = moment(exp_date, "DD-MM-YYYY").diff(moment(currentdate, "DD-MM-YYYY"), 'Days');
                    if (totdays > 0 && remaindays <= totdays && remaindays > 0) {
                        shelfLifePercentage = ((remaindays * 100) / totdays).toFixed(2);
                    } else {
                        shelfLifePercentage = Number(0).toFixed(2);
                        // console.log(shelfLifePercentage);
                    }
                } else {
                    res.send({ 'status': 'failed', 'message': 'mfg_date not sent' });
                };
                // let packStatus = await grnModel.getProductPackStatus();
                // console.log("packStatus coming here", packStatus);
                // console.log(product_id);
                // console.log(data.pack_size);
                let packUOMInfo = await grnModel.getProductPackUOMInfo(product_id, data.pack_size);
                // console.log("packUOMINfo coming here", packUOMInfo);
                let uomName = packUOMInfo.map(x => {
                    return x.uomName;
                })
                uomName = uomName[0];
                let no_of_eaches = packUOMInfo.map(x => {
                    return x.no_of_eaches;
                })

                no_of_eaches = no_of_eaches[0];
                // console.log("uomName", no_of_eaches);


                let response = [{
                    'product_id': `${product_id}`,
                    'pack_size': `${data.pack_size}`,
                    'no_of_eaches': `${no_of_eaches}`,
                    'rqty': `${data.rqty}`,
                    'qtytotal': `${data.qtytotal}`,
                    'shelfLifePercentage': `${shelfLifePercentage}`,
                    'mfg_date': `${mfg_date}`,
                    'exp_date': `${exp_date}`,
                    'free': `${data.free}`,
                    'damaged': `${data.damaged}`,
                }];
                // console.log("final respone", JSON.stringify(response));

                res.send({ 'status': 'success', 'message': 'getPackText response', 'data': response });


            } else {
                res.send({ 'status': 'failed', 'message': 'Request not sent' });
            }

        } catch (err) {
            // console.log("error", err);
            res.send({ 'status': 'failed', 'message': "cpMessage.serverError" });
        }
    },


    /*
    input : user_id, legal_entity, page, pageSize;
    output : ":[{"grnId":"TSGR20012000270","poId":"TSPO20012001252","grnDate":"2020-01-06 15:20:46",
    "legalsuplier":"Troy Groups","dcname":"Hercules","createdBy":"naresh chiratanagandla",
    "ref_no":"","invoice_no":"","povalue":"1320.00000","grnvalue":"1306.80000",
    "item_discount_value":"13.20000",}    
    */

    getGrnAction: async (req, res) => {
        try {
            if (typeof req.body.data != "undefined" && req.body.data != '') {
                let data = JSON.parse(req.body.data);
                // console.log(data);
                let user_id = data.user_id;
                let legal_entity_id = data.legal_entity_id;
                let page = data.page; // page=0
                let page_size = data.pageSize; // pageSize=50
                let filters = data.filter;
                let orderby_array;
                let filter_by;
                //filter=indexof(tolower(grnId)tsgr17040001130)+ge+0

                // console.log(data);
                // console.log(page);
                let produc_grid_field_db_match =
                {
                    'grnId': 'inward_code',
                    'poId': 'po.po_code',
                    'legalsuplier': 'business_legal_name',
                    'dcname': 'dcname',
                    'grnDate': 'inward.created_at',
                    'createdBy': 'createdBy',
                    'ref_no': 'inward_ref_no',
                    'invoice_no': 'invoice_no',
                    'grnvalue': 'grnvalue',
                    'povalue': 'povalue',
                    'item_discount_value': 'item_discount_value',
                };

                if (data.orderby) {
                    //  orderby=legalsuplier+asc
                    let order = data.orderby.split("+");
                    let order_query_field = order[0];
                    let order_query_type = order[1];
                    let order_by_type = "desc";
                    let order_by = '';

                    if (order_query_type == 'asc') {
                        order_by_type = 'asc';
                    } else {
                        order_by_type = "desc";
                    }

                    if (typeof produc_grid_field_db_match[order_query_field] != 'undefined' &&
                        produc_grid_field_db_match[order_query_field] != '') {
                        order_by = produc_grid_field_db_match[order_query_field];
                    } else {

                    }
                    orderby_array = order_by + ' ' + order_by_type;
                    // console.log(orderby_array);


                    // let fil = "arrr+and+brrr";
                    // let ccc = fil.split('and');

                    // console.log(fil.split('and'));
                    // console.log(ccc[0].split('+'));
                    // console.log("print filter_by")
                    filter_by = filterData(filters);
                    // console.log(filter_by);

                } else {

                }
                // console.log("coming from inward"+filter_by,'\n', filters,'\n',  page,'\n', page_size,'\n', orderby_array);
                let totalInwards = await inwardModel.getAllInward(filter_by, filters, 0, page, page_size, orderby_array, user_id, legal_entity_id);


            }
        }

        catch (err) {

        }

        function filterData(filters) {
            try {
                let filterDataArr = [];
                let dataArr = [];
                let grnDate = 'grnDate';
                let legalSuplier = 'business_legal_name = sai';
                if (typeof filters != 'undefined' && filters != '') {
                    let stringArr = filters.split('and');
                    // console.log(stringArr);
                    if (Array.isArray(stringArr)) {
                        stringArr.forEach(data => {
                            dataArr = data.split('+');
                            // console.log("dataArrrrrr\n", dataArr);
                            // if(dataArr[0].includes('grnDate')){
                            //     console.log(dataArr[0]);
                            //     return dataArr[0];
                            // }
                        })
                        if (dataArr.includes('grnDate')) {
                            return grnDate;
                        } else {
                            return legalSuplier;
                        }

                    }

                    // console.log("dataArrrrrr\n",dataArr);
                    else {

                    }


                } else {

                }
                // return dataArr[1];
            } catch (err) {
                return err;
            }
        }
    },

    detailsAction: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                var data = JSON.parse(req.body.data);
                var user_id = data.user_id;
                if (data.hasOwnProperty('inward_id')) {
                    var grnId = data.inward_id;
                    let isReferenceEdit = await rolerepo.checkPermissionByFeatureCode('GRNREFEDIT001', user_id);
                    let poInvoiceFeature = await rolerepo.checkPermissionByFeatureCode('GRN004', user_id);
                    let poDetailFeature = await rolerepo.checkPermissionByFeatureCode('PO003', user_id);
                    let returnCreateFeature = await rolerepo.checkPermissionByFeatureCode('PR002', user_id);
                    let createOrderFeature = await rolerepo.checkPermissionByFeatureCode('POCRT001', user_id);
                    var grnProductArr = await inwardModel.getInwardDetailById(grnId);
                    var docsArr = await dispute.getDocuments(grnId);
                    if (grnProductArr.length > 0) {
                        let grnDetails = grnProductArr[0];
                        let discount_before_tax = (grnDetails.discount_before_tax) && (grnDetails.discount_before_tax == 1) ? grnDetails.discount_before_tax : 0;
                        let leWhId = (grnDetails.le_wh_id != 'undefined') ? grnDetails.le_wh_id : 0;
                        //let whInfo = await inwardModel.getWarehouseById(leWhId);
                        let deliveryGTIN = await grnModel.getDeliveryGtin(grnId);
                        let leId = (grnDetails.legal_entity_id != 'undefined') ? grnDetails.legal_entity_id : 0;
                        var billingAddress = '';
                        let po_address = (grnDetails.po_address) ? grnDetails.po_address : '';
                        let invoiceExist = await purchaseOrder.checkInvoiceByInwardId(grnId);
                        if (po_address != '') {
                            po_address = JSON.parse(po_address);
                            var whInfo = po_address.shipping;
                            var supplierInfo = po_address.supplier;
                            billingAddress = po_address.billing;
                        } else {
                            var whInfo = await inwardModel.getWarehouseById(leWhId);
                            var supplierInfo = await legalentity.getLegalEntityById(leId);
                            // checking supplier is ebutor supplier
                            let wh_le_id = (whInfo.legal_entity_id != 'undefined') ? whInfo.legal_entity_id : 0;
                            let wh_state_id = (whInfo.state != 'undefined') ? whInfo.state : "";
                            let is_eb_supplier = (supplierInfo.is_eb != 'undefined') ? supplierInfo.is_eb : 0;
                            if (is_eb_supplier) {
                                let apob_data = await purchaseOrder.getApobData(wh_le_id);
                                if (apob_data.length > 0) {
                                    supplierInfo = apob_data[0];
                                }
                            } else {
                                let userInfo = await purchaseOrder.getUserByLeId(leId);
                                if (userInfo) {
                                    supplierInfo.email_id = (userInfo[0].email_id) ? userInfo[0].email_id : '';
                                    supplierInfo.mobile_no = (userInfo[0].mobile_no) ? userInfo[0].mobile_no : '';
                                }
                            }
                            let check_apob = (whInfo.is_apob) ? whInfo.is_apob : 0;
                            if (check_apob || wh_le_id == 2) {
                                billingAddress = await legalentity.checkGstState(wh_state_id);
                                if ((billingAddress.gstin) && billingAddress.gstin != "") {
                                    whInfo.gstin = billingAddress.gstin;
                                }
                            } else {
                                billingAddress = await legalentity.getLegalEntityById(wh_le_id);
                            }
                            if ((whInfo.fssai) && whInfo.fssai != "")
                                billingAddress.fssai = whInfo.fssai;
                        }

                        let approvalStatus = '';
                        let approvalStatusId = '';
                        let bill_disc = 0;
                        let docDetails = [];
                        if (grnDetails.approval_status != 0 && grnDetails.approval_status != '') {
                            let approvalStatusArr = await inwardModel.getAllOrderStatus('Approval Status');
                            approvalStatusArr = approvalStatusArr.reduce((a, b) => Object.assign({}, a, b));
                            approvalStatus = approvalStatusArr[grnDetails.approval_status];
                            approvalStatusId = `${("approval_status" in grnDetails) ? grnDetails.approval_status : ''}`;
                            if (grnDetails.approval_status == 1) {
                                approvalStatus = 'Approved';
                            }
                        }
                        if (!grnDetails.discount_on_bill_options) {
                            if (grnDetails.on_bill_discount_type) {
                                if (grnDetails.hasOwnProperty('discount_on_total')) {
                                    bill_disc = grnDetails.discount_on_total.toFixed(2) + '(' + (grnDetails.on_bill_discount_value.toFixed(2)) + '%)';
                                } else {
                                    bill_disc = 0;
                                }
                            } else {
                                bill_disc = (grnDetails.discount_on_total) ? grnDetails.discount_on_total.toFixed(2) : 0;
                            }
                        } else {
                            bill_disc = 0;
                        }
                        docsArr.forEach(async (docs) => {
                            let docs_data = {};
                            docs_data = {
                                "DocumentType": docs.doc_type,
                                "RefNo": docs.doc_ref_no,
                                "CreatedBy": docs.fullname,
                                "url": docs.doc_url,
                                "DocId": docs.inward_doc_id
                            }
                            docDetails.push(docs_data);
                        });
                        // get packTypes to calculate uom, freeUom
                        let packTypes = await inwardModel.getAllOrderStatus('Levels');
                        packTypes = packTypes.reduce((a, b) => Object.assign({}, a, b));
                        let productDetails = [];
                        let discountBeforeTax = (grnDetails.discount_before_tax != 'undefined' && grnDetails.discount_before_tax == 1) ? grnDetails.discount_before_tax : 0;
                        let discount_per = 0;
                        let base_price = 0;
                        let totQty = 0;
                        let totalTaxValue = 0;
                        let total_cgst = 0;
                        let total_sgst = 0;
                        let total_utgst = 0;
                        let total_igst = 0;
                        let total_cess = 0;
                        let sgst_total = 0;
                        let totalRowDiscount = 0;
                        let totalBaseDiscBeforeTax = 0;
                        let row_total = 0;
                        let totalRecvedQty = 0;
                        var isGst = 0;
                        let is_cgst_display = 0;
                        let is_utgst_display = 0;
                        let is_igst_display = 0;
                        let is_cess_display = 0;
                        for (const product of grnProductArr) {
                            totalRecvedQty += product.received_qty;
                            var packArr = await inwardModel.getProductPackInfo(product.inward_prd_id);
                            var packDetails = [];
                            packArr.forEach(pack => {
                                if (pack.pack_level == 'Eaches') {
                                    var pack_size = pack.pack_level + (pack.pack_qty);
                                } else {
                                    var pack_size = pack.pack_level + '(' + pack.pack_qty + 'Eaches)';
                                }
                                let mfg_date = '';
                                let exp_date = '';
                                if ((pack.mfg_date != '') && pack.mfg_date != '0000-00-00' && pack.mfg_date != '1970-01-01')
                                    mfg_date = pack.mfg_date;
                                if ((pack.exp_date != '') && pack.exp_date != '0000-00-00' && pack.exp_date != '1970-01-01')
                                    exp_date = pack.exp_date;
                                let pack_data = {};
                                pack_data = {
                                    "PackSize ": pack_size,
                                    "Received": pack.received_qty,
                                    "TotRecQty": pack.tot_rec_qty,
                                    "MFGDate ": mfg_date,
                                    "EXPDate ": exp_date,
                                    "Freshness": Math.round(pack.freshness_per)
                                }
                                packDetails.push(pack_data);

                            });

                            let uom = ((product.uom != '' && product.uom != 0 && packTypes[product.uom] != 'undefined') ? packTypes[product.uom] : 'Ea');
                            let freeUom = ((product.free_uom != '' && product.free_uom != 0 && packTypes[product.free_uom] != 'undefined') ? packTypes[product.free_uom] : 'Eaches');
                            let noOfEaches = (product.no_of_eaches == 0 || product.no_of_eaches == '') ? 0 : product.no_of_eaches;
                            let qty = (product.orderd_qty != '') ? product.orderd_qty : 0;
                            let freeQty = (product.free_qty != '') ? product.free_qty : 0;
                            let mrp = +(product.mrp);
                            let subTotal = product.sub_total;
                            base_price = product.sub_total + product.discount_total;
                            totQty = totQty + (product.received_qty);
                            totalTaxValue = totalTaxValue + product.tax_amount;
                            totalRowDiscount = totalRowDiscount + product.discount_total;
                            row_total = row_total + product.row_total;
                            totalBaseDiscBeforeTax += (product.sub_total + product.discount_total);

                            if (product.discount_type)
                                discount_per = product.discount_percentage;

                            let cgst_tax_percentage = 0;
                            let igst_tax_percentage = 0;
                            let sgstPer = 0;
                            let sgstAmt = 0;
                            let tax_name = ''; `   `
                            let cgst_value = 0;
                            let igst_value = 0;
                            let cessPer = 0;
                            let cess_val = 0;
                            var gstTaxVal = [];
                            if (product.tax_data != [] && product.tax_data != null) {
                                let taxDetails;
                                if (Array.isArray(product.tax_data)) {
                                    taxDetails = product.tax_data[0];
                                } else {
                                    taxDetails = product.tax_data;
                                }
                                taxDetails = JSON.parse(taxDetails);
                                taxDetails = taxDetails[0];

                                var product_id = product.product_id;
                                var product_tax = [];
                                var tax_types = {};
                                product_tax[product_id] = [];
                                var taxName;
                                var taxprecent;
                                var taxAmt;
                                if (product.tax_data.length > 0) {
                                    var decodedata = JSON.parse(product.tax_data);
                                    if (Array.isArray(decodedata)) {
                                        //decodedata = Array.isArray(decodedata)?decodedata:Object.entries(decodedata);
                                        for (const tax of decodedata) {
                                            taxprecent = (tax['Tax Percentage']) ? (tax['Tax Percentage']) : 0;
                                            taxName = (tax['Tax Type']) ? (tax['Tax Type']) : [];
                                            if (!(tax_types[taxName]))
                                                tax_types[taxName] = taxName;
                                            if (taxName.includes('GST', 'IGST', 'UTGST')) {
                                                gstTaxVal[product_id] = tax;
                                                taxAmt = (tax['taxAmt']) ? tax['taxAmt'] : product.tax_amount;
                                            } else {
                                                taxAmt = (tax['taxAmt']) ? tax['taxAmt'] : 0;
                                            }
                                            product_tax[product_id][taxName] = { 'tax_per': taxprecent, 'tax_amt': taxAmt };
                                        }
                                        cess_val = (product_tax[product_id]['CESS']) ? product_tax[product_id]['CESS']['tax_amt'] : 0;
                                        cessPer = (product_tax[product_id]['CESS']) ? product_tax[product_id]['CESS']['tax_per'] : 0;
                                        total_cess = (+total_cess) + (+cess_val);

                                        taxDetails = (gstTaxVal[product_id]) ? (gstTaxVal[product_id]) : "taxDetails";
                                        if ('GST' in tax_types || 'IGST' in tax_types || 'UTGST' in tax_types)
                                            isGst = 1;
                                        if (isGst) {
                                            if ('GST' in tax_types || 'UTGST' in tax_types) {
                                                is_cgst_display = 1;
                                                is_utgst_display = 1;
                                            }
                                            if ('IGST' in tax_types)
                                                is_igst_display = 1;
                                            if ('CESS' in tax_types)
                                                is_cess_display = 1;
                                        }
                                    }
                                }
                                let cgst = (taxDetails['CGST'] != '') ? (taxDetails['CGST']) : 0;
                                let igst = (taxDetails['IGST'] != '') ? (taxDetails['IGST']) : 0;
                                let sgst = (taxDetails['SGST'] != '') ? (taxDetails['SGST']) : 0;
                                let utgst = (taxDetails['UTGST'] != '') ? (taxDetails['UTGST']) : 0;

                                cgst_value = (taxDetails['CGST_VALUE']) ? taxDetails['CGST_VALUE'] : 0.00;
                                igst_value = (taxDetails['IGST_VALUE']) ? taxDetails['IGST_VALUE'] : 0.00;
                                let sgst_value = (taxDetails['SGST_VALUE']) ? taxDetails['SGST_VALUE'] : 0.00;
                                let utgst_value = (taxDetails['UTGST_VALUE']) ? taxDetails['UTGST_VALUE'] : 0.00;
                                if (taxDetails['Tax Percentage']) {
                                    var taxPercentage = (taxDetails['Tax Percentage']) ? taxDetails['Tax Percentage'] : 0;
                                    tax_name = (taxDetails['Tax Type'] == '') ? '' : taxDetails['Tax Type'] + ' @';
                                } else {
                                    var taxPercentage = (taxDetails['Tax_Percentage']) ? taxDetails['Tax_Percentage'] : 0;
                                    tax_name = (taxDetails['Tax_Type'] == '') ? '' : taxDetails['Tax_Type'] + ' @';
                                }

                                cgst_tax_percentage = (100 / cgst);
                                cgst_tax_percentage = (taxPercentage / cgst_tax_percentage);
                                igst_tax_percentage = (100 / igst);
                                igst_tax_percentage = (taxPercentage / igst_tax_percentage);
                                let sgst_tax_percentage = (100 / sgst);
                                sgst_tax_percentage = (taxPercentage / sgst_tax_percentage);
                                let utgst_tax_percentage = (100 / utgst);
                                utgst_tax_percentage = (taxPercentage / utgst_tax_percentage);

                                total_cgst = total_cgst + cgst_value;
                                total_sgst = total_sgst + sgst_value;
                                total_utgst = total_utgst + utgst_value;
                                total_igst = total_igst + igst_value;
                                //regarding utgst changes
                                if (sgst_tax_percentage != 0) {
                                    sgstPer = sgst_tax_percentage.toFixed(2);
                                    sgstAmt = sgst_value.toFixed(2);
                                } else if (utgst_tax_percentage != 0) {
                                    sgstPer = utgst_tax_percentage.toFixed(2);
                                    sgstAmt = utgst_value.toFixed(2);
                                } else {
                                    sgstPer = 0;
                                    sgstAmt = 0;
                                }
                                if (total_sgst != 0) {
                                    sgst_total = total_sgst.toFixed(2);
                                } else if (total_utgst != 0) {
                                    sgst_total = total_utgst.toFixed(2);
                                } else {
                                    sgst_total = 0;
                                }
                                if (cgst_value)
                                    is_cgst_display = 1;
                                if (sgstAmt)
                                    is_utgst_display = 1;
                                if (igst_value)
                                    is_igst_display = 1;
                            }
                            let discount_amount;
                            if (product.discount_inc_tax) {
                                discount_amount = '(INC TAX)';
                            } else {
                                discount_amount = '(EXC TAX)';
                            }
                            var returnArr = await grnModel.getProductReturnQty(grnId, product.product_id);
                            product.ret_soh_qty = (returnArr.ret_soh_qty) ? returnArr.ret_soh_qty : 0;
                            product.ret_dit_qty = (returnArr.ret_dit_qty) ? returnArr.ret_dit_qty : 0;
                            product.ret_dnd_qty = (returnArr.ret_dnd_qty) ? returnArr.ret_dnd_qty : 0;
                            product.ret_soh_qty = 19.9999;
                            var ret_soh = parseInt(product.ret_soh_qty);
                            var ret_dit = parseInt(product.ret_dit_qty);
                            var ret_dnd = parseInt(product.ret_dnd_qty);

                            var pending_soh_Qty = parseInt(product.good_qty) - ret_soh;
                            var pending_dit_Qty = parseInt(product.damage_qty) - ret_dit;
                            var pending_dnd_Qty = parseInt(product.missing_qty) - ret_dnd;

                            let result = {};
                            result = {
                                "ProductName": product.product_title,
                                "Sku": product.sku,
                                "HSNCode": product.hsn_code,
                                "POQty": `${qty} ${uom} ${uom != 'Ea' ? '(' + (qty * noOfEaches) + ' Eaches)' : ''}`,
                                "GrnQty": product.received_qty + '(Eaches)',
                                "MRP": mrp.toFixed(2),
                                "Price": product.price.toFixed(5),
                                "TaxableValue": product.sub_total.toFixed(5),
                                //taxable_value.toFixed(2),
                                "TaxName": tax_name + parseFloat(product.tax_per).toFixed(2),
                                "TaxValue": product.tax_amount.toFixed(5),
                                "DiscountPercent": discount_per.toFixed(2),
                                "DiscountAmount": product.discount_total.toFixed(2),
                                "TaxExtension": discount_amount,
                                "BaseRate": base_price.toFixed(2),
                                "Total": product.row_total.toFixed(5),
                                "CGST_PER": +(cgst_tax_percentage.toFixed(2)),
                                "CGST": cgst_value.toFixed(2),
                                "SGST": +sgstAmt,
                                "SGST_PER": sgstPer,
                                "IGST_PER": +(igst_tax_percentage.toFixed(2)),
                                "IGST": igst_value.toFixed(2),
                                "CESS": cess_val.toFixed(2),
                                "CESS_PER": cessPer,
                                "Eachesqty": product.inv_qty,
                                "EachesFreeQty": product.inv_free_qty,
                                "UnitPrice": product.inv_unit_price,
                                "SubTotal": product.inv_price,
                                "SOH": parseInt(pending_soh_Qty),
                                "DIT": parseInt(pending_dit_Qty),
                                "DND": parseInt(pending_dnd_Qty),
                                "DITQty": 0,
                                "DNDQty": 0,
                                "PackDetails": packDetails,
                            }
                            productDetails.push(result);
                        }
                        var totalReturnQty = await grnModel.getReturnQtyByInwardId(grnId);
                        var return_reasons = await inwardModel.getAllOrderStatus('Purchase Return Reasons');
                        let checkOrderStatus = await grnModel.checkPOType(grnDetails.po_no);
                        let orderDelivered = true;
                        let gds_order_id = 0;
                        let legalentityidofuser = await role.getLegalEntityId(user_id);
                        if (checkOrderStatus) {
                            if (checkOrderStatus.po_so_order_code != '0' && checkOrderStatus.po_so_order_code != "") {
                                gds_order_id = await grnModel.checkPODeliverStatus(checkOrderStatus.po_so_order_code);
                                let poDetailArr = await purchaseOrder.getPoDetailById(grnDetails.po_no, legalentityidofuser);
                                if (gds_order_id == "") {
                                    orderDelivered = false;
                                    gds_order_id = await purchaseOrder.getOrderIdByCode(poDetailArr[0].po_so_order_code);
                                }
                            } else {
                                orderDelivered = false;
                            }
                        }
                        res.send({
                            'Status': "Success",
                            "Message": "Records found",
                            "Supplier": {
                                "Name": supplierInfo.business_legal_name,
                                "SupplierCode": supplierInfo.le_code,
                                "Address": `${supplierInfo.address1},${(supplierInfo.address2 != '') ? supplierInfo.address2 : ''},${supplierInfo.city}, ${supplierInfo.state_name},${supplierInfo.country_name},${supplierInfo.pincode}`,
                                "Phone": (supplierInfo.mobile_no) ? supplierInfo.mobile_no : '',
                                "Email": (supplierInfo.email_id) ? supplierInfo.email_id : '',
                                "State": (supplierInfo.state_name) ? supplierInfo.state_name : '',
                                "StateCode": (supplierInfo.state_code) ? supplierInfo.state_code : '',
                                "GstinUin": (supplierInfo.gstin) ? supplierInfo.gstin : '',
                            },
                            "DeliveryAddress": {
                                "Name": whInfo.lp_wh_name,
                                "Code": whInfo.le_wh_code,
                                "ReceivedAt": `${whInfo.address1},${whInfo.address2}`,
                                "ContactPerson": (whInfo.contact_name != null) ? whInfo.contact_name : '',
                                "Phone": (whInfo.phone_no != null) ? whInfo.phone_no : 0,
                                "Email": (whInfo.email != null) ? whInfo.email : 0,
                                "State": (whInfo.state_name != null) ? whInfo.state_name : '',
                                "StateCode": (whInfo.state_code != null) ? whInfo.state_code : 0,
                                "deliveryGTIN": (whInfo.gstin) ? whInfo.gstin : '',
                            },
                            "Billingaddress": {
                                "Name": billingAddress.business_legal_name,
                                "Address": `${billingAddress.address1},${billingAddress.address2}`,
                                "State": (billingAddress.state_name != null) ? billingAddress.state_name : '',
                                "StateCode": (billingAddress.state_code != null) ? billingAddress.state_code : 0,
                                "GstinNo": (billingAddress.gstin != null) ? billingAddress.gstin : 0,
                                "Country": (billingAddress.country_name != null) ? billingAddress.country_name : '',
                            },
                            "GRNDetails": {
                                "GrnCode": grnDetails.inward_code,
                                "PONumber": grnDetails.po_code,
                                "PODate": grnDetails.po_created_date,
                                "GrnDate": grnDetails.inward_created_at,
                                "CreatedBy": grnDetails.firstname + "" + grnDetails.lastname,
                                "InvoiceNo": grnDetails.invoice_no,
                                "InvoiceDate": grnDetails.inward_invoice_date,
                                "RefNo": grnDetails.inward_ref_no,
                                "ApprovalStatus": ((approvalStatus != '') ? approvalStatus : 0),
                                "ApprovalStatusId": (approvalStatusId != '') ? approvalStatusId : 0,
                                "InvoiceExist": invoiceExist,
                            },
                            "productDetails": productDetails,
                            "Total": {
                                "TotalQty": totQty,
                                "TotalTaxableValue": grnDetails.base_total.toFixed(5),
                                "TotalTaxValue": totalTaxValue.toFixed(2),
                                "TotalCGST": total_cgst.toFixed(2),
                                "TotalSGST": sgst_total,
                                "TotalIGST": total_igst,
                                "TotalCESS": total_cess.toFixed(2),
                                "TotalDiscount": totalRowDiscount.toFixed(5),
                                "TotalBaseRate": totalBaseDiscBeforeTax.toFixed(2),
                                "RowTotal": row_total.toFixed(5),
                                "GrandTotal": grnDetails.grand_total.toFixed(2),
                                "ShippingFee": grnDetails.shipping_fee.toFixed(2),
                                "SACCode": '',
                                "ServiceTax": '0.00',
                                "ServiceChargeAmt": '0.00',
                                "BillDiscount": bill_disc,
                            },
                            "GrandTotalInWords": converter(grnDetails.grand_total),
                            "Documents": docDetails,
                            "invoiceDetails": {
                                "InvoiceCode": (grnDetails.invoice_code) ? (grnDetails.invoice_code) : 'null',
                                "invoiceDate": (grnDetails.po_invoice_created_at) ? (grnDetails.po_invoice_created_at) : 'null',
                                "GRNCode": (grnDetails.inward_code) ? (grnDetails.inward_code) : 'null',
                                "GrnDate": (grnDetails.created_at) ? (grnDetails.created_at) : 'null',
                                "poCode": (grnDetails.po_code) ? (grnDetails.po_code) : 'null',
                                "CreatedBy": (grnDetails.invoice_created_name) ? (grnDetails.invoice_created_name) : 'null',
                                "BillDisc": (grnDetails.discount_on_total) ? (parseFloat(grnDetails.discount_on_total).toFixed(2)) : 0
                            },
                            "TotalReceivedQty": totalRecvedQty,
                            "TotalReturnQty": totalReturnQty,
                            "ReturnReasons": return_reasons,
                            "isReferenceEdit": isReferenceEdit,
                            "poInvoiceFeature": poInvoiceFeature,
                            "returnCreateFeature": returnCreateFeature,
                            "createOrderFeature": createOrderFeature,
                            "poDetailFeature": poDetailFeature,
                            "IsFullDeliver": orderDelivered,
                            "GdsOrderId": gds_order_id,
                            "iscgstDisplay": is_cgst_display,
                            "isutgstDisplay": is_utgst_display,
                            "isigstDisplay": is_igst_display,
                            "iscessDisplay": is_cess_display,
                            "DiscountTaxType": discount_before_tax,
                        });

                    } else {
                        res.send({ 'status': 'failed', 'message': 'Please send valid input' });
                    }
                } else {
                    res.send({ 'status': 'failed', 'message': 'Please send inward id' });
                }

            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
            }

        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime' });
        }
    },

    getCommentsList: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inwardId = data.inward_id;
                let disputesArr = await dispute.getCommentsTransactionId(inwardId);
                if (disputesArr) {
                    let dataArr = [];
                    disputesArr.forEach(dispute => {
                        let profile_picture = '';
                        if (dispute.profile_picture) {
                            profile_picture = dispute.profile_picture;
                        }
                        let result = {
                            "CommentDate": dispute.created_at,
                            "FullName": dispute.firstname + "" + dispute.lastname,
                            "RoleName": dispute.roleName,
                            "Comment": dispute.comments,
                            "ProfilePic": profile_picture
                        }
                        dataArr.push(result);
                    });
                    res.send({ 'status': 'success', 'message': 'Data found', 'data': dataArr });
                } else {
                    res.send({ 'status': 'failed', 'message': 'No data found' });
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
            }
        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'No Data' });
        }
    },

    addCommentAction: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inwardId = data.inward_id;
                let comment = data.comment;
                let userId = data.user_id;
                let legalEntityId = await legalentity.getLeId(userId);
                let inwaredArr = await inwardModel.getInwardCodeById(inwardId);
                let inward_code = inwaredArr.hasOwnProperty('inward_code') ? inwaredArr.inward_code : '';
                let disputeId = await dispute.getDisputIdByTransactionId(inwardId);
                if (!disputeId) {
                    let disputeId = await dispute.saveDispute(inwardId, legalEntityId, userId);
                } else {
                    await dispute.saveHistory(disputeId, comment, userId);
                }
                res.send({ 'status': 'success', 'message': 'Inserted successfully' });
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
            }
        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'No Data' });
        }
    },

    uploadDocument: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let userId = data.hasOwnProperty('user_id') ? data.user_id : 0;
                if (userId == 0) {
                    res.send({ 'status': 'failed', 'message': 'Please enter user_id' });
                    return;
                }
                let poId = data.hasOwnProperty('po_id') ? data.po_id : 0;
                let allow_duplicate = data.hasOwnProperty('allow_duplicate') ? data.allow_duplicate : 0;
                let documentType = data.hasOwnProperty('document_type') ? data.document_type : '';
                let ref_no = data.hasOwnProperty('ref_no') ? data.ref_no.trim() : '';

                if (ref_no != "" && documentType != "") {
                    let isDocExist = await grnModel.isDocExist(ref_no, documentType);
                    if (isDocExist > 0 && allow_duplicate == 0) {
                        res.send({ 'status': 'failed', 'message': 'Same Invoice Number already given to another GRN,Please click "Allow Duplicate" option to proceed ' });
                        return;
                    }

                    if (typeof req.files.img != 'undefined') {
                        let path = require('path');
                        let ext = path.extname(req.files.img[0].originalname);
                        let ext_array = ['.pdf', '.doc', '.docx', '.png', '.jpg', '.jpeg', '.jfif', '.JPG', '.PNG', '.JPEG', '.JFIF'];
                        if (!ext_array.includes(ext))
                            res.send({ 'status': 'failed', 'message': 'Please upload only pdf, doc, docx, png, jpg, jpeg,jfif extensions.' });
                        let url = await uploadToS3(req.files.img);
                        // let url = "s";
                        if (url != '') {
                            let docsObj = {
                                'doc_ref_no': ref_no,
                                'po_id': poId,
                                'doc_ref_type': documentType,
                                'allow_duplicate': allow_duplicate,
                                'doc_url': url,
                                'created_by': userId,
                                'created_at': created_at
                            };

                            // let docsArr = [ref_no, poId, documentType, allow_duplicate, url, userId, created_at];

                            let inward_doc_id = await grnModel.saveDocument(docsObj);
                            if (inward_doc_id != 0 && inward_doc_id != null) {
                                let docTypes = await grnModel.getDocumentTypes();
                                // console.log(docTypes);
                                let userInfo = await grnModel.userInfo(userId);
                                if (userInfo && docTypes) {
                                    let createdBy = userInfo.firstname + ' ' + userInfo.lastname;
                                    // let lastname = userInfo.lastname;
                                    let result = {}
                                    docTypes.forEach((docType, i) => {
                                        result[docType['value']] = docType['master_lookup_name'];
                                    });

                                    let document = result.hasOwnProperty(`${documentType}`) ? result[`${documentType}`] : '';
                                    if (document == '') {
                                        res.send({ 'status': 'failed', 'message': 'Please select a valid document' });
                                        return;
                                    }
                                    res.send({ 'status': 'success', 'message': 'Documents uploaded successfully', 'url': url, 'Document_Type': document, 'Created_By': createdBy, 'Ref_no': ref_no, 'Inward_doc_id': inward_doc_id });
                                    return;
                                }
                            } else {
                                res.send({ 'status': 'failed', 'message': 'Error in saving Documents to db' });
                                return;
                            }
                        } else {
                            console.log('uploadtos3');
                            res.send({ 'status': 'failed', 'message': 'Error in uploadtos3' });
                            return;
                        }

                    } else {
                        res.send({ 'status': 'failed', 'message': 'Please upload image' });
                        return;
                    }
                } else {
                    res.send({ 'status': 'failed', 'message': 'Please enter Document Type and Reference No' });
                    return;
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
                return;
            }

            function uploadToS3(file) {
                return new Promise((resolve, reject) => {
                    let s3bucket = new aws.S3({
                        accessKeyId: IAM_USER_KEY,
                        secretAccessKey: IAM_USER_SECRET,
                        Bucket: BUCKET_NAME
                    });
                    var params = {
                        Bucket: BUCKET_NAME,
                        Key: file[0].originalname,
                        Body: file[0].buffer
                    };
                    s3bucket.upload(params, function (err, data) {
                        if (err) {
                            reject(err);
                        } else {
                            return resolve(data.Location);
                        }
                    });
                })
            }
        } catch (err) {
            console.log('errrrr', err);
            res.send({ 'status': 'failed', 'message': 'Error while uploading' });
        }
    },

    deleteDocument: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inwardDocId = data.inward_doc_id;
                // console.log("delete wokring");
                let result = await grnModel.deleteDocument(inwardDocId);
                if (result > 0) {
                    res.send({ 'status': 'success', 'message': 'Document deleted successfully' })
                }
                else res.send({ 'status': 'failed', 'message': 'Document not deleted' });
            }

        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'Error while updating Ref no.' });
        }
    },

    saveReferenceNo: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inwardDocId = data.inward_doc_id;
                let refNo = data.ref_no;

                let result = await grnModel.saveReferenceNo(inwardDocId, refNo);
                if (result > 0) {
                    res.send({ 'status': 'success', 'message': `Reference Number '${refNo}' updated successfully` })
                }
                else res.send({ 'status': 'failed', 'message': 'Reference number not updated' });

            }
            else res.send({ 'status': 'failed', 'message': 'Please provide input' });
        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'Error while updating Ref no.' });
        }
    },

    downloadExcel: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let fromDate = data.from_date;
                let toDate = data.to_date;

                let grnReports = await grnModel.getCSV(fromDate, toDate);
                let dataArr = [];
                for (const grnReport of grnReports[0]) {
                    let result = {};
                    result['PO_No'] = grnReport['PO No'];
                    result['GRN_No'] = grnReport['GRN No'];
                    result['Supplier_Name'] = grnReport['Supplier Name'];
                    result['DC_Name'] = grnReport['DC Name'];
                    result['PO_Created_By'] = grnReport['PO Created By'];
                    result['PO_Date'] = grnReport['PO Date'];
                    result['GRN_Created_By'] = grnReport['GRN Created By'];
                    result['GRN_Date'] = grnReport['GRN Date'];
                    result['Product_Title'] = grnReport['Product Title'];
                    result['Pending_GRN_Qty'] = grnReport['Pending GRN Qty'];
                    result['Pending_GRN_Value'] = grnReport['Pending GRN Value'];
                    result['povalue'] = grnReport['povalue'];
                    result['GRN_Value'] = grnReport['GRN Value'];
                    result['PO_Status'] = grnReport['PO Status'];
                    result['GRN_Status'] = grnReport['GRN Status'];
                    result['Payment_Value'] = grnReport['Payment Value'] != null ? grnReport['Payment Value'] : 0;
                    result['Payment_Balance'] = grnReport['Payment Balance'];

                    dataArr.push(result);
                }
                // console.log(dataArr);
                res.send({ 'status': 'success', 'message': 'Grn List', 'data': dataArr });

            }
            else res.send({ 'status': 'failed', 'message': 'Please provide input' });

        } catch (err) {

        }
    },

    poHistory: async (req, res) => {
        try {

            let data = JSON.parse(req.body.data);
            let poCode = data.po_code;
            //get information for ApprovalHistory Page
            let approvalHistory = await grnModel.getPOHistory(poCode);

            const approvalHistoryArr = [];
            approvalHistory.forEach(history => {

                // let profilePicture = (history.hasOwnProperty('profile_picture')) ? history.profile_picture : '';
                let profilePicture = '';
                let firstName = history.hasOwnProperty('firstname') ? history.firstname : '';
                let lastName = history.hasOwnProperty('lastname') ? history.lastname : '';
                let name = history.hasOwnProperty('name') ? history.name : '';
                let createdAt = history.hasOwnProperty('created_at') ? history.created_at : '';
                let masterLookUpName = history.hasOwnProperty('master_lookup_name') ? history.master_lookup_name : '';
                let awfComment = history.hasOwnProperty('awf_comment') ? history.awf_comment : '';

                let result = {
                    'ProfilePicture': profilePicture,
                    'User': firstName + ' ' + lastName + ' ' + name,
                    'Date': createdAt,
                    'Status': masterLookUpName,
                    'Comments': awfComment,
                }
                approvalHistoryArr.push(result);
            })
            if (approvalHistoryArr.length > 0) {
                res.send({ 'Status': 'Success', 'Message': 'Records Found', 'approvalHistory': approvalHistoryArr });
            } else {
                res.send({ 'Status': 'Success', 'Message': 'No Records', 'approvalHistory': approvalHistoryArr });
            }


        } catch (err) {
            console.log('Approval History API Error', err);
            res.send({ 'Status': 'Failed', 'Message': 'No Data' });
        }
    },


    saveReturn: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inward_id = data.hasOwnProperty('inward_id') ? data.inward_id : 0;
                let comment = data.hasOwnProperty('return_comment') ? data.return_comment : '';
                let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
                var inwDetails = await inwardModel.getInwardDetailById(inward_id);
                var state_code = inwDetails[0].hasOwnProperty('state_code') ? inwDetails[0].state_code : "TS";
                var prCode = await purchaseOrder.getReferenceCode("PR", state_code);
                if (inward_id != 0) {
                    var pr_id = await inwardModel.saveReturns(prCode, inward_id, comment, user_id, inwDetails[0].le_wh_id, inwDetails[0].legal_entity_id);
                    //var pr_id=866;
                    // var arr=[];
                    // arr.pr_code=prCode,
                    // arr.inward_id=inward_id,
                    // arr.pr_remarks=comment,
                    // arr.created_by=user_id,
                    // arr.pr_status=103001,
                    // arr.approval_status =57036,
                    // arr.pr_grand_total= 0,
                    // arr.pr_total_qty= 0,
                    // arr.le_wh_id = inwDetails[0].le_wh_id,
                    // arr.legal_entity_id = inwDetails[0].legal_entity_id
                    // var pr_id = await inwardModel.saveReturns(arr);
                    var grand_total = 0;
                    var totQty = 0;
                    if (pr_id > 0) {
                        if (data.hasOwnProperty('selectedData') && Array.isArray(data.selectedData)) {
                            var return_productArr = [];
                            let grand_total;
                            let totQty;
                            for (const productData of data.selectedData) {
                                var productInfo = await inwardModel.getInwardProductById(inward_id, productData.product_id);
                                var soh_qty = productData.hasOwnProperty('soh_qty') ? productData.soh_qty : 0;
                                var dit_qty = productData.hasOwnProperty('dit_qty') ? productData.dit_qty : 0;
                                var dnd_qty = productData.hasOwnProperty('dnd_qty') ? productData.dnd_qty : 0;
                                var qty = (soh_qty + dit_qty + dnd_qty);
                                if (qty > 0) {
                                    var unit_price = productInfo.hasOwnProperty('price') ? productInfo.price : 0;
                                    var tax_per = productInfo.hasOwnProperty('tax_per') ? productInfo.tax_per : 0;
                                    var sub_total = unit_price * qty;
                                    var tax_amt = (unit_price * tax_per) / 100;
                                    var tax_total = tax_amt * qty;
                                    var total = sub_total + tax_total;
                                    grand_total += +total;
                                    totQty += +qty;
                                    return_productArr.pr_id = pr_id,
                                        return_productArr.product_id = productData.product_id,
                                        return_productArr.qty = soh_qty,
                                        return_productArr.dit_qty = dit_qty,
                                        return_productArr.dnd_qty = dnd_qty,
                                        return_productArr.unit_price = unit_price,
                                        return_productArr.mrp = productInfo.hasOwnProperty('mrp') ? productInfo.mrp : 0,
                                        return_productArr.tax_type = productInfo.hasOwnProperty('tax_type') ? productInfo.tax_type : '',
                                        return_productArr.tax_per = tax_per,
                                        return_productArr.tax_amt = tax_amt,
                                        return_productArr.uom = '16001',
                                        return_productArr.no_of_eaches = '1',
                                        return_productArr.price = unit_price,
                                        return_productArr.sub_total = sub_total,
                                        return_productArr.tax_total = tax_total,
                                        return_productArr.tax_data = productInfo.hasOwnProperty('tax_data') ? productInfo.tax_data : '',
                                        return_productArr.hsn_code = productInfo.hasOwnProperty('hsn_code') ? productInfo.hsn_code : '',
                                        return_productArr.total = total,
                                        return_productArr.reason = productData.hasOwnProperty('return_reason') ? productData.return_reason : '',
                                        return_productArr.created_by = user_id
                                }
                            }
                            if (Array.isArray(return_productArr) && (return_productArr != []) && grand_total > 0 && totQty > 0) {
                                var saveprod = await inwardModel.saveReturnProducts(return_productArr);
                                var updatereturn = await inwardModel.updateReturn(pr_id, grand_total, totQty);
                                res.send({ 'status': 'success', 'message': 'Return Created Successfully', 'inward_id': inward_id, 'pr_id': pr_id });
                            } else {
                                res.send({ 'status': 'failed', 'message': 'Return Qty/Amount should not be zero.' });
                            }
                        } else {
                            res.send({ 'status': 'failed', 'message': 'Please select at least one product' });
                        }
                    } else {
                        res.send({ 'status': 'failed', 'message': 'Return Id should not be zero' });
                    }
                } else {
                    res.send({ 'status': 'failed', 'message': 'Inward Id should not be empty' });
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
            }
        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'No Data' });
        }
    },

    returnsList: async (req, res) => {
        try {
            var grid_field_db_match = {
                'prId': 'pr_code',
                'inwardCode': 'inward_code',
                'Supplier': 'business_legal_name',
                'shipTo': 'lp_wh_name',
                'prValue': 'pr_grand_total',
                'createdBy': 'user_name',
                'picker_name': 'picker_name',
                'createdOn': 'created_at',
                'Status': 'approval_status_name',
                'pr_id': 'pr_id',
            }

            let data = JSON.parse(req.body.data);
            let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            let page = Number(data.page);
            let perPage = Number(data.page_size);
            let offset = Number(page * perPage);

            let sortField = data.sortField;
            sortField = grid_field_db_match[sortField];
            let sortType = data.sortType;
            sortType = (sortType == 'true') ? "asc" : "desc";

            let filters = data.filters;
            let filter = {};
            if (filters != [] && filters != null && filters != '') {
                let filterArr = [];
                if (Array.isArray(filters)) {
                    filters.forEach(x => {
                        let op = x.op;
                        let value = x.value;
                        let result = {};
                        result[x.field] = [value, op];
                        filterArr.push(result);
                    })
                }
                filter = filterArr.reduce((a, b) => Object.assign({}, a, b));
            }
            let returnArr = await purchaseOrder.getAllPurchaseReturns(0, offset, perPage, filter, sortField, sortType, user_id);
            if (returnArr != 'noData') {

                let rowCount = await purchaseOrder.getAllPurchaseReturns(1, offset, perPage, filter, sortField, sortType, user_id);
                let numberOfPages = Math.ceil(rowCount / perPage);
                let recordsOnPage = ((offset + perPage) < rowCount) ? (offset + perPage) : rowCount;

                res.send({
                    "Status": "Success", "Message": "Records found",
                    "RecordNumber": `${(offset + 1)} - ${recordsOnPage} of ${rowCount} records`,
                    "NumberOfPages": (numberOfPages), "PresentPage": Number(page + 1), "data": returnArr
                });
            } else {
                res.send({ "Status": "Failed", "Message": "No Records found" });
            }
        } catch (err) {
            res.send({ "Status": "Failed", "Message": "Please try again after sometime" });
        }
    },

    SaveReferenceNo: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let doc_id = data.hasOwnProperty('inward_doc_id') ? data.inward_doc_id : 0;
                let ref_no = data.hasOwnProperty('doc_ref_no') ? data.doc_ref_no : 0;
                let save = await dispute.SaveReference(doc_id, ref_no);
                if (save > 0) {
                    res.send({ 'status': 'success', 'message': 'Saved successfully' });
                } else {
                    res.send({ 'status': 'failed', 'message': 'unable to save' });
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send valid input' });
            }

        } catch (err) {
            res.send({ "Status": "Failed", "Message": "Please try again after sometime" });
        }
    },

    createInvoiceByinwardId: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                let data = JSON.parse(req.body.data);
                let inwardId = data.hasOwnProperty('inward_id') ? data.inward_id : 0;
                let userId = data.hasOwnProperty('user_id') ? data.user_id : 0;
                var inwardDetails = await inwardModel.getInwardDetailsById(inwardId);
                if (inwardDetails == 0) {
                    res.send({ "Status": "Failed", "Message": "Inward details not found" });
                }
                var checkInvoiceExist = await inwardModel.checkInvoiceByInwardId(inwardId);
                if (checkInvoiceExist.length == 1) {
                    res.send({ "Status": "Failed", "Message": "Invoice already created" });
                }
                var le_wh_id = (inwardDetails[0].le_wh_id) ? inwardDetails[0].le_wh_id : 0;
                var state_id = await inwardModel.getSateIdByDcId(le_wh_id);
                var invoiceCode = await purchaseOrder.getReferenceCode("PI", state_id);
                var grand_total = (inwardDetails[0].grand_total) ? inwardDetails[0].grand_total : 0;
                if (grand_total > 0) {
                    let invoiceGrid = [];
                    invoiceGrid.invoice_code = invoiceCode,
                        invoiceGrid.inward_id = inwardId,
                        invoiceGrid.billing_name = (inwardDetails[0].business_legal_name) ? inwardDetails[0].business_legal_name : '',
                        invoiceGrid.discount_on_total = (inwardDetails[0].discount_on_total) ? inwardDetails[0].discount_on_total : 0,
                        invoiceGrid.shipping_fee = (inwardDetails[0].shipping_fee) ? inwardDetails[0].shipping_fee : 0,
                        invoiceGrid.grand_total = parseFloat(grand_total),
                        invoiceGrid.invoice_status = 11301,
                        invoiceGrid.approval_status = 0,
                        invoiceGrid.created_by = userId

                    var invoice_grid_id = await purchaseOrder.savePOInvoice(invoiceGrid);
                    var invoiceProduct = [];
                    inwardDetails.forEach(product => {
                        invoiceProduct.po_invoice_grid_id = invoice_grid_id,
                            invoiceProduct.product_id = product.product_id,
                            invoiceProduct.qty = product.received_qty,
                            invoiceProduct.free_qty = product.free_qty,
                            invoiceProduct.damage_qty = product.damage_qty,
                            invoiceProduct.unit_price = product.price,
                            invoiceProduct.tax_name = product.tax_name,
                            invoiceProduct.tax_per = parseFloat(product.tax_per),
                            invoiceProduct.tax_amount = product.tax_amount,
                            invoiceProduct.hsn_code = product.hsn_code,
                            invoiceProduct.tax_data = product.tax_data,
                            invoiceProduct.discount_type = product.discount_type,
                            invoiceProduct.discount_per = product.discount_percentage,
                            invoiceProduct.discount_amount = product.discount_total,
                            invoiceProduct.price = product.sub_total,
                            invoiceProduct.sub_total = product.tax_amount + product.sub_total,
                            invoiceProduct.comment = product.remarks,
                            invoiceProduct.created_by = userId
                    });
                    await purchaseOrder.savePOInvoiceProducts(invoiceProduct);
                    res.send({ 'Status': 'success', 'Message': 'Invoice created successfully' });
                } else {
                    res.send({ 'Status': 'Failed', 'Message': 'Invoice grand total cannot be zero.' });
                }
            } else {
                res.send({ 'Status': 'Failed', 'Message': 'Inward Id should not be empty' });
            }
        } catch (err) {
            res.send({ "Status": "Failed", "Message": "Please try again after sometime" });
        }
    },

    getpackstatus: async (req, res) => {
        try {
            if (Object.keys(req.body.data).length > 0) {
                var data = JSON.parse(req.body.data);
                var user_id = data.user_id;
                var packStatus = await grnModel.getProductPackStatus();
                res.send({ 'status': 'success', 'message': 'success', data: packStatus });
            } else {
                res.send({ 'status': 'failed', 'message': 'Please send input' });
            }

        } catch (err) {
            res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime' });
        }
    }
}
