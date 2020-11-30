'use strict';

var Purchasedata = require('../model/purchaseorderModel.js');
let masterLookUp = require('../model/masterLookUpModel.js');
let approval_flow_func = require('../model/CommonApprovalFlowFunctionModel');
var encryption = require('../../config/encryption.js');
const moment = require('moment');
var database = require('../../config/mysqldb');
let roleRepo = require('../model/Rolerepo.js')
let db = database.DB;
let qty = 0;
let no_of_eaches = 0;
let cur_elp = 0;
let poamount = 0;
let current_datetime = new Date();
let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
let converter = require('convert-rupees-into-words');
//used for file upload
var upload = require('../../config/s3config');
const aws = require('aws-sdk');
const { fdatasync } = require('fs');
const BUCKET_NAME = process.env.S3BucketName;
const IAM_USER_KEY = process.env.S3AccessKeyId;
const IAM_USER_SECRET = process.env.S3AecretAccessKey;


/**
 * Below are the ids for different status
 *  
    let poStatusId = {
        'open' : '87001',
        'closed' : '87002',
        'expired' : '87003',
        'cancelled' : '87004',
        'partial' : '87005'
}
 * /
*/

//indexAction : Getting the Grid items according to status
module.exports.POCount = async (req, res) => {
    try {

        let data = JSON.parse(req.body.data);
        let userId = data.user_id;

        // let hasAccess = await roleRepo.checkPermissionByFeatureCode('PO001');
        // console.log('hasAccess',hasAccess);
        // let poGSTReport = await roleRepo.checkPermissionByFeatureCode('POGSTR');
        // console.log('poGSTReport',poGSTReport);

        // if no access throwing error;
        // if (hasAccess == false) { console.log('hasAccess not there ') };

        //get legal_entity_id form user_id ;
        let legal_entity_id = await Purchasedata.getLEID(userId);
        // console.log('legal_entity_id', legal_entity_id);
        //checking with dateFilters;
        let fromDate = data.hasOwnProperty('from_date') ? data.from_date : '';
        let toDate = data.hasOwnProperty('to_date') ? data.to_date : '';
        // console.log(fromDate, toDate)

        let allPOCountArr = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 0, fromDate, toDate);
        // let allPOCountAr = obj => { Object.values(allPOCountArr).reduce((a, b) => (a + b))};
        let allPOCountArrSum = Object.values(allPOCountArr[0]).reduce((a, b) => (a + b));
        let allPOApprovalCountArr = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 1, fromDate, toDate);
        let finalApprovalCountArr = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 2, fromDate, toDate);
        let partialCountArr = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 3, fromDate, toDate);
        let grnCountArr = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 4, fromDate, toDate);
        let immediatePay = await Purchasedata.getPoCountByStatus(userId, legal_entity_id, 5, fromDate, toDate);
        let totalImmediatePay = Object.values(immediatePay[0]).reduce((a, b) => (a + b));
        // console.log("allpoCountarr coming", allPOCountArr);
        // console.log("allpoCountarr coming",allPOCountArr);
        // console.log("allPOApprovalCountArr coming", allPOApprovalCountArr);
        // console.log("finalApprovalCountArr coming", immediatePay);
        // console.log("partialCountArr coming", partialCountArr);
        // console.log("grnCountArr coming", grnCountArr);
        let partial = (partialCountArr.length > 0 && partialCountArr[0]['87005']) ? Number(partialCountArr[0]['87005']) : 0;
        let closed = (grnCountArr.length > 0 && grnCountArr[0]['87002']) ? Number(grnCountArr[0]['87002']) : 0;
        let approvalCancel = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57117']) ? Number(allPOApprovalCountArr[0]['57117']) : '0';
        let cancelled = (allPOCountArr.length > 0 && allPOCountArr[0]['87004']) ? Number(allPOCountArr[0]['87004']) : 0;
        let expired = (allPOCountArr.length > 0 && allPOCountArr[0]['87003']) ? Number(allPOCountArr[0]['87003']) : 0;
        cancelled = cancelled + approvalCancel;
        let opened = (allPOCountArr.length > 0 && allPOCountArr[0]['87001']) ? Number(allPOCountArr[0]['87001']) : 0;
        opened = opened - approvalCancel;
        let shelved = (finalApprovalCountArr.length > 0 && finalApprovalCountArr[0]['1']) ? Number(finalApprovalCountArr[0]['1']) : 0;
        let acceptFull = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57107']) ? Number(allPOApprovalCountArr[0]['57107']) : 0;
        let acceptPart = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57119']) ? Number(allPOApprovalCountArr[0]['57119']) : 0;
        let acceptPartClosed = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57120']) ? Number(allPOApprovalCountArr[0]['57120']) : 0;
        let inspectedFull = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57034']) ? Number(allPOApprovalCountArr[0]['57034']) : 0;
        let inspectedPart = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57122']) ? Number(allPOApprovalCountArr[0]['57122']) : 0;
        let checked = acceptFull + acceptPart + acceptPartClosed;
        let initiated = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57106']) ? Number(allPOApprovalCountArr[0]['57106']) : 0;
        let created = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57029']) ? Number(allPOApprovalCountArr[0]['57029']) : 0;
        let verified = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57030']) ? Number(allPOApprovalCountArr[0]['57030']) : 0;
        let approved = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57031']) ? Number(allPOApprovalCountArr[0]['57031']) : 0;
        let posit = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57033']) ? Number(allPOApprovalCountArr[0]['57033']) : 0;
        let grnCreated = (allPOApprovalCountArr.length > 0 && allPOApprovalCountArr[0]['57035']) ? Number(allPOApprovalCountArr[0]['57035']) : 0;
        let poCounts =
        {
            'Total': allPOCountArrSum,
            'Open': opened,
            'partial': partial,
            'closed': closed,
            'Cancelled': cancelled,
            'expired': expired,
            'Initiation': initiated,
            'Verification': created,
            'Approval': verified,
            'Fulfillment': approved,
            'paid': opened + partial + shelved,
            'immediatepay': totalImmediatePay,
            'Inspection': posit,
            'Acceptance': inspectedPart + inspectedFull,
            'GRN': checked,
            'Putaway': grnCreated,
            'Completed': shelved,
        };


        // console.log('poCounts', poCounts);

        res.send({ 'status': 'success', "message": "POCounts", "data": poCounts })


    } catch (err) {
        console.log("POCount API Error", err);
        res.send({ 'status': 'failed', 'message': "No Data" })

    }

};

//POList is used to get all Purchase Orders list based on filters;
module.exports.POList = async (req, res) => {
    try {
        let grid_field_db_match = {
            'poCode': 'po.po_code',
            'Supplier': 'legal_entities.business_legal_name',
            'le_code': 'legal_entities.le_code',
            'shipTo': 'lwh.lp_wh_name',
            'validity': 'po.po_validity',
            'poValue': 'poValue',
            'createdBy': 'user_name',
            'createdOn': 'po.po_date',
            'payment_mode': 'po.payment_mode',
            'tlm_name': 'po.tlm_name',
            'Status': 'lookup.master_lookup_name',
            'poValue': 'poValue',
            'grn_value': 'grn_value',
            'po_grn_diff': 'po_grn_diff',
            'grn_created': 'grn_created',
            'payment_status': 'po.payment_status',
            'payment_due_date': 'payment_due_date',
            'po_so_order_link': 'po_so_order_code',
            'po_parent_link': 'po_parent_code',
            'duedays': 'duedays',
            'po_id': 'po_id'
        };

        let data = JSON.parse(req.body.data);
        let userId = Number(data.user_id);
        if (isNaN(userId) || userId == 0 || userId == "") {
            res.send("userId invalid");
            return;
        }

        let fromDate = data.from_date;
        let toDate = data.to_date;


        let sortField = data.sort_field;
        sortField = grid_field_db_match[sortField];
        // console.log(grid_field_db_match[`${sortField}`]);
        // return;
        let sortType = data.sort_type;
        sortType = (sortType == 'true') ? "asc" : "desc";
        // console.log(sortType, sortField);
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
            // filter includes {field : [value, operator]}
            filter = filterArr.reduce((a, b) => Object.assign({}, a, b));
        }
        // console.log('filter',filter['shipTo'][0]);
        // console.log('filter',filter);
        // let str1 = 'post paid';
        // let str2 = 'pre paid';
        // let x = (filter['payment_mode'][0]).toLowerCase();
        // console.log('payment_mode  X ',x);
        // let y = str1.includes(x);
        // console.log('result is ',y); 
        // return;

        // dataArr is the final response being sent by this API.
        let dataArr = [];

        //poStatusId ===== '87001': 'OPEN','87002': 'PO CLOSED','87003': 'EXPIRED','87004': 'CANCELLED','87005': 'PARTIALLY RECEIVED'
        let poStatusId = Number(data.po_status_id);

        //approvalStatusId ==== 'initiated':57106,'created':57029,'verified':57030,'approved':57031,'posit':57033,'checked':57107,'receivedatdc':57034,'grncreated':57035,'shelved':1,'payments':57032
        let approvalStatusId = Number(data.approval_status_id);

        //only either of poStatusId or approvalStatusId shall be passed in req.body
        poStatusId = Boolean(approvalStatusId) ? 0 : poStatusId;  // if approvalStatusId is passed poStatusId = 0
        approvalStatusId = Boolean(poStatusId) ? 0 : approvalStatusId; // if poStatusId is passed approvalStatusId = 0


        let page = Number(data.page);
        let perPage = Number(data.page_size);
        let offset = Number(page * perPage);
        let allStatusArr = await masterLookUp.getAllOrderStatus('PURCHASE_ORDER');
        //reducing different objects in allStatusArr into one object
        allStatusArr = allStatusArr.reduce((a, b) => Object.assign({}, a, b));
        // console.log('allStatusArr', allStatusArr);
        // let rowCount = await Purchasedata.getAllPurchasedOrders(userId, poStatusId, approvalStatusId, 1, filter);
        let poOrders = await Purchasedata.getAllPurchasedOrders(userId, poStatusId, approvalStatusId, 0, fromDate, toDate, filter, perPage, offset, sortField, sortType);

        //checking for permissions based on userId
        let isViewable = await roleRepo.checkPermissionByFeatureCode('PO003', userId);
        let isPrintable = await roleRepo.checkPermissionByFeatureCode('PO004', userId);
        let isDownloadable = await roleRepo.checkPermissionByFeatureCode('PO005', userId);
        let isEditable = await roleRepo.checkPermissionByFeatureCode('PO007', userId);

        if (poOrders.length > 0) {
            poOrders.forEach(async po => {
                let poValidity = po.po_validity + " " + (po.po_validity > 1 ? 'Days' : 'Day');
                let poValue = (po.poValue != '') ? po.poValue : 0;
                let payment_mode;
                let payment_mode_color;
                if (po.payment_mode == 2) {
                    payment_mode = (po.parent_id > 0) ? 'Pre Paid' : 'Pre Paid';
                    payment_mode_color = (po.parent_id > 0) ? '#008000' : '#0000FF';
                } else {
                    payment_mode = (po.parent_id > 0) ? 'Post Paid' : 'Post Paid';
                    payment_mode_color = (po.parent_id > 0) ? '#008000' : '#0000FF';
                }

                let poStatus = allStatusArr[po.po_status];

                let approvalStatus = (po.approval_status != '') ? po.approval_status : '';
                let paymentStatus = (po.payment_status != '') ? po.payment_status : '';
                // let payment_due_date = (po.payment_due_date != null) ? moment(po.payment_due_date).format(("YYYY-MM-DD HH:mm:ss")) : null;
                // let createdOn = (po.po_date != null) ? moment(po.po_date).format(("YYYY-MM-DD HH:mm:ss")) : null;
                // let grn_created = (po.grn_created != null) ? moment(po.grn_created).format(("YYYY-MM-DD HH:mm:ss")) : null;

                //not editable if Grn Created, PO Cancelled

                // console.log('approval', approvalStatus, poStatus);
                let is_edit = false;
                if (approvalStatus != '57117' && po.po_status == '87001' && isEditable) is_edit = true;

                let result = {};
                result = {
                    "poID": po.po_id,
                    "poCode": po.po_code,
                    "le_code": po.le_code,
                    "Supplier": po.business_legal_name,
                    "shipTo": po.lp_wh_name,
                    "validity": poValidity,
                    "poValue": poValue,
                    "payment_mode": payment_mode,
                    "payment_mode_color": payment_mode_color,
                    "payment_due_date": po.payment_due_date,
                    "tlm_name": po.tlm_name,
                    "createdBy": po.user_name,
                    "createdOn": po.po_date,
                    "Status": poStatus,
                    "approval_status": approvalStatus,
                    "payment_status": paymentStatus,
                    "is_viewable": isViewable,
                    "is_printable": isPrintable,
                    "is_downloadable": isDownloadable,
                    "is_editable": is_edit,
                    "grn_value": (po.grn_value != null ? po.grn_value : 0),
                    "po_grn_diff": po.po_grn_diff,
                    "grn_created": po.grn_created,
                    "po_so_order_link": po.po_so_order_code,
                    "po_parent_link": po.po_parent_code
                };
                dataArr.push(result);
            })
            let rowCount = await Purchasedata.getAllPurchasedOrders(userId, poStatusId, approvalStatusId, 1, fromDate, toDate, filter);
            let numberOfPages = Math.ceil(rowCount / perPage);
            let recordsOnPage = ((offset + perPage) < rowCount) ? (offset + perPage) : rowCount;

            res.send({
                "Status": "Success", "Message": "Records found",
                "RecordNumber": `${(offset + 1)} - ${recordsOnPage} of ${rowCount} records`,
                "NumberOfPages": (numberOfPages), "PresentPage": Number(page + 1), "data": dataArr
            });
        } else {
            res.send({ "Status": "Failed", "Message": "No Records found" });
            console.log('No po Orders')
        }


    } catch (err) {
        res.send({ "Status": "Failed", "Message": "No Records found" });
        console.log(err);
    }
}

module.exports.paymentsList = async (req, res) => {
    try {
        // let kk = await Purchasedata.lkl(0);
        // res.send(kk);
        // return;
        let grid_field_db_match = {
            'pay_code': 'payment.pay_code',
            'pay_for': 'pay_for_name',
            'pay_type': 'payment_type',
            'ledger_account': 'payment.ledger_account',
            'pay_amount': 'payment.pay_amount',
            'pay_date': 'payment.pay_date',
            'txn_reff_code': 'payment.txn_reff_code',
            'pay_utr_code': 'payment.pay_utr_code',
            'createdBy': 'createdBy',
            'created_at': 'payment.created_at',
            'approval_status': 'approval_status_name',
            'pay_id': 'pay_id'
        }

        let data = JSON.parse(req.body.data);
        // console.log('data',data);
        let leId = data.le_id;

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
            // filter reduced to {field : [value, operator]}
            filter = filterArr.reduce((a, b) => Object.assign({}, a, b));
        }
        // console.log('filter', filter);
        let paymentArr = await Purchasedata.getAllPayments(leId, 0, offset, perPage, filter, sortField, sortType);
        if (paymentArr != 'noData') {

            let rowCount = await Purchasedata.getAllPayments(leId, 1, offset, perPage, filter, sortField, sortType);
            let numberOfPages = Math.ceil(rowCount / perPage);
            let recordsOnPage = ((offset + perPage) < rowCount) ? (offset + perPage) : rowCount;

            res.send({
                "Status": "Success", "Message": "Records found",
                "RecordNumber": `${(offset + 1)} - ${recordsOnPage} of ${rowCount} records`,
                "NumberOfPages": (numberOfPages), "PresentPage": Number(page + 1), "data": paymentArr
            });
        } else {
            res.send({ "Status": "Failed", "Message": "No Records found" });
        }
    } catch (err) {
        console.log("paymentsList Error : ", err);
        res.send({ "Status": "Failed", "Message": "No Records found" });
    }
}


exports.createPOAction = async function (req, res) {
    var params = JSON.parse(req.body.data);
    var user_id = params.user_id;
    var is_apob = params.hasOwnProperty('is_apob') ? 1 : 0;

    /**
     * manufacturersList is needed for the getEbutorSalesDcPurchaseReport api as input.
     * as supplier list is already coming from createPOAction api, manufacturersList is also written in this itself.
     */
    let manufacturersList = await Purchasedata.getManufacturersList();
    Purchasedata.checkUserId(user_id).then(response => {
        Purchasedata.getLegalEntityId(user_id).then((response1) => {
            var le_id = response1.le_id;
            module.exports.getsupplierslist(le_id, user_id).then(supplierlist => {

                module.exports.getindentlist(le_id, user_id).then(indentlist => {

                    module.exports.getwarehouselist(le_id, user_id, is_apob).then(warehouselist => {

                        var data = { "suplplier_list": supplierlist, "indent_list": indentlist, "warehouse_list": warehouselist, "manf_list": manufacturersList };
                        // var data = { "suplplier_list": supplierlist, "indent_list": 'indentlist', "warehouse_list": 'warehouselist' };
                        res.send({ status: 200, message: 'Data Found', data: data });
                    }).catch(err => {
                        console.log(err);
                        res.send({ status: 400, message: 'error on getwarehouselist' });
                    });
                }).catch(err => {
                    console.log(err);
                    res.send({ status: 400, message: 'error on getindentlist' });
                });

            }).catch(err => {
                console.log(err);
                res.send({ status: 400, message: 'error on supplierlist' });
            });
        }).catch(err => {
            console.log(err);
            res.send({ status: 400, message: 'no user data' });
        });
    }).catch(err => {
        console.log("err", err);
        res.send({ status: 400, message: 'no data' });
    });
};

/**
*Checking Token and Getting user id , legal entity id
*/

exports.getdata = function (req, res) {
    try {
        let Data = JSON.parse(req.body.data);
        if (Data.user_id != null) {
            Purchasedata.checkUserId(Data.user_id).then((user_id) => {
                if (user_id > 0) {

                    let user_id = Data.user_id;
                    Purchasedata.getLegalEntityId(user_id).then((response) => {
                        if (response != null) {
                            res.status(200).json({ status: 'success', message: 'Data', data: response })
                        } else {
                            res.status(200).json({ status: 'Failed', message: 'No data Found', })
                        }

                    }).catch((err) => {
                        console.log(err)
                    })
                } else {
                    res.status(200).json({ status: 'Session', message: "You have already logged into the Ebutor System" })
                }
            }).catch((err) => {
                console.log(err)
            })
        } else {
            res.status(200).json({ status: 'Session', message: "You have already logged into the Ebutor System" })
        }
    } catch (err) {
        console.log(err)
        res.status(200).json({ status: 'Failed', message: "Unable to process your request!. Please try later" })
    }
};

/**
* Getting Supplier List
*/

exports.getsupplierslist = async function (le_id, user_id) {
    let data = await Purchasedata.getSuppliersList(le_id, user_id);
    // console.log('data',data);
    return data;

};

/**
* Getting Indent List
*/

exports.getindentlist = async function (le_id, user_id) {
    let data = await Purchasedata.getIndentList(le_id, user_id);
    // console.log("come", data);
    return data;

};

/**
* Getting Warehouse List
*/

exports.getwarehouselist = async function (le_id, user_id, is_apob) {

    let data = await Purchasedata.getWarehouseList(le_id, user_id, is_apob);
    return data;
};

/**
* Get All Seleted Supplier Data
*/

exports.getSuppliersAction = function (req, res) {
    try {
        var data = JSON.parse(req, body, data);
        // console.log("data", data);
        var indent_id = data.indent_id
        if (indent_id > 0) {
            Purchasedata.getPoQtyWithProductByIndentId(indent_id).then(prodArr => {
            })
        } else {

        }
    } catch (err) {
        res.status(500).json({ status: "failed", message: 'Indent Data Not Found' })
    }

};

/**
* Getting SKUS Data
*/

exports.getSkus = function (req, res) {
    try {
        var data = JSON.parse(req.body.data);
        var supplier_id = data.supplier_id;
        var warehouse_id = data.warehouse_id;
        var term = data.term;
        Purchasedata.getSkusData(supplier_id, warehouse_id, term).then(productdata => {
            if (productdata != null) {
                res.status(200).json({ status: "success", message: 'Product Data Found', data: productdata })
            } else {
                res.status(200).json({ status: "failed", message: 'Product Data Not Found', data: null })
            }
        }).catch((err) => {
            console.log(err);
            res.status(200).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })
        });

    } catch (err) {
        res.status(500).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })

    }
}

exports.getProductInfo = function (req, res) {
    try {
        var data = JSON.parse(req.body.data);
        /** Code for decryption 
        //let decryptedData = encryption.decrypt(req.body.data);
        //console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
        //let data  =  JSON.parse(decryptedData);
        */
        //    console.log('data',data);
        var supplier_id = Number(data.supplier_id);
        var warehouse_id = Number(data.warehouse_id);
        var product_id = Number(data.product_id);
        var user_id = Number(data.user_id);
        //let result;
        // if (supplier_id != 0 && warehouse_id != 0 && product_id != 0 && user_id != 0) {
        // console.log('data',supplier_id,warehouse_id,product_id,user_id);
        // Purchasedata.checkProduct(warehouse_id, product_id, supplier_id).then(response => {
        // if (response != 0) {
        Purchasedata.checkUserId(user_id).then(response => {
            if (response != 'undefined') {
                Purchasedata.getLegalEntityId(user_id).then(response1 => {
                    //console.log('response1',response1);
                    var legal_entity_id = response1.le_id;
                    module.exports.subscribeProducts(supplier_id, warehouse_id, product_id).then(subscripprdts => {
                        // console.log("hereee",subscripprdts,subscripprdts.length);
                        let productsAdded = data.hasOwnProperty('products') ? data.products : [];
                        let addfrom = data.hasOwnProperty('addfrom') ? data.addfrom : null;
                        Purchasedata.getFreebieParent(product_id).then(freebieParent => {
                            // console.log("freee",freebieParent);                        
                            let parent_id = (freebieParent.length > 0 && freebieParent[0].hasOwnProperty('main_prd_id')) ? freebieParent[0].main_prd_id : 0;
                            //console.log("parentid",parent_id);
                            module.exports.getPOProductRow(product_id, parent_id, supplier_id, warehouse_id, addfrom, legal_entity_id).then(productTextArr => {
                                // console.log("textarr",productTextArr);
                                if (productTextArr && productTextArr.hasOwnProperty('status')) {
                                    // console.log("productif");
                                    if (productTextArr.status == 200) {
                                        // console.log("status if");
                                        Purchasedata.getFreebieProducts(product_id).then(freebieProducts => {
                                            // console.log(typeof (freebieProducts, "aaaa"));
                                            // console.log("getFreebieProducts", freebieProducts);
                                            let freebe_product_id = freebieProducts && freebieProducts.length > 0 ? freebieProducts[0].free_prd_id : null;
                                            if (freebe_product_id) {
                                                Purchasedata.getFreebieParent(freebe_product_id).then(freebieParent => {
                                                    // console.log("freebieParent", freebieParent);
                                                    let parent_id = freebieParent[0].hasOwnProperty('main_prd_id') ? freebieParent[0].main_prd_id : 0;
                                                    // console.log("freeparentid", parent_id);
                                                    if (freebieProducts.length > 0) {
                                                        var freebies = {};
                                                        freebieProducts.forEach(async function (freeproduct) {
                                                            // console.log("freeproduct", freeproduct)
                                                            if (freeproduct.main_prd_id != freeproduct.free_prd_id && !productsAdded.includes(freeproduct.free_prd_id)) {
                                                                module.exports.subscribeProducts(supplier_id, warehouse_id, freeproduct.free_prd_id).then(subscripprdts => {
                                                                    //console.log("subscripprdts",subscripprdts);
                                                                    Purchasedata.getProductInfoByID(supplier_id, warehouse_id, freeproduct.free_prd_id).then(productarr => {
                                                                        //console.log("productarr",productarr);
                                                                        let freeProductTextArr = [];
                                                                        if (productarr.length > 0) {

                                                                            if (productarr[0].is_sellable == 0 && productarr[0].KVI == 'Q9') {
                                                                                module.exports.getPOProductRow(freeproduct.free_prd_id, parent_id, supplier_id, warehouse_id, addfrom, legal_entity_id).then(freeProductTextArr => {
                                                                                    if (Array.isArray(freeProductTextArr['data'])) {

                                                                                        if (productTextArr['data'][0]['product']['product_id'] == freeProductTextArr['data'][0]['parent_id']) {
                                                                                            productTextArr['data'].push(freeProductTextArr['data'][0]);
                                                                                        } else {
                                                                                        }
                                                                                    }
                                                                                    res.send(productTextArr);
                                                                                    //  result = encryption.encrypt(productTextArr);
                                                                                    //   res.send(result);
                                                                                });
                                                                            } else {
                                                                                res.send(productTextArr);
                                                                                // result = encryption.encrypt(productTextArr);
                                                                                // res.send(result);
                                                                            }
                                                                        } else {
                                                                            res.send({ status: 404, message: "Product Info Not Found", data: null });
                                                                        }
                                                                    });
                                                                });
                                                            }
                                                        });
                                                    } else {
                                                        res.send(productTextArr);
                                                        // result = encryption.encrypt(productTextArr);
                                                        // console.log("======>1792 Encrypted", result);
                                                        // res.send(result);
                                                    }
                                                });
                                            } else {
                                                //productTextArr['data'][0]['freebee'] = null;
                                                res.send(productTextArr);

                                                // result = encryption.encrypt(productTextArr);
                                                // console.log("======>1792 Encrypted", result);
                                                // res.send(result);
                                            }
                                        })
                                    } else {
                                        res.send(productTextArr);
                                        // result = encryption.encrypt(productTextArr);
                                        // console.log("======>1792 Encrypted", result);
                                        // res.send(result);
                                    }
                                }

                            }).catch(err => {
                                console.log(err);
                                res.send({ status: 400, message: 'no user data' });
                            });
                        });
                    });
                }).catch(err => {
                    console.log(err);
                    res.send({ status: 400, message: 'no user data' });
                });
            } else {
                console.log('no data found');
                res.send({ status: 400, message: 'Not a registered User' })
            }
        })
        // } else {
        //     console.log('no data found');
        //     res.send({ status: 400, message: 'no data' })
        // }
        // });
        // } else {
        //     console.log("error")
        //     //reject({ status: "failed", message: 'Unable to process your request!. Please Try Later' });
        //     res.send({ status: 400, message: 'no data' })
        // }
    } catch (err) {
        console.log("error", err)
        //reject({ status: "failed", message: 'Unable to process your request!. Please Try Later' });
        res.send({ status: 400, message: 'no data' });
    }
}


exports.subscribeProducts = function (supplier_id, warehouse_id, product_id) {

    return new Promise(function (resolve, reject) {
        try {
            Purchasedata.checkProductSuscribe(supplier_id, warehouse_id, product_id).then(async (subscribe) => {
                if (subscribe.length > 0 && subscribe[0].hasOwnProperty('subscribe')) {
                    // console.log('jjjjjjjjjj')
                    if (subscribe[0].subscribe == 0) {
                        // console.log('jjjjjjjjjjlll')
                        let product_tot = { subscribe: 1 };
                        let updateprtot = await Purchasedata.updateProductTot(product_tot, supplier_id, warehouse_id, product_id);
                    } else {
                        // console.log('jjjjjmmmmmjjjjj')
                        resolve(1);
                    }
                } else {
                    Purchasedata.getProductdetails(product_id).then(productdet => {
                        // console.log('productdet',productdet);
                        if (productdet.length > 0) {
                            var product_title = productdet[0].hasOwnProperty('product_title') ? productdet[0].product_title : null;
                            let product_tot = { 'product_id': product_id, 'le_wh_id': warehouse_id, 'supplier_id': supplier_id, 'product_name': product_title, 'is_active': 1, 'subscribe': 1 };
                            Purchasedata.saveProductTot(product_tot).then(producttot => {
                                // if (producttot.length > 0) {
                                // console.log('jjjjjjjjjj',product_tot);
                                if (producttot) {
                                    resolve(1);
                                } else {
                                    reject(0);
                                }
                                // } else {
                                //     console.log("error in saveProductTot");
                                //     reject("Bad request from SaveProductTot")
                                // }
                            })
                        } else {
                            console.log("error in getProductdetails");
                            reject("Bad Request this is promise errror");
                        }
                    })
                }


            })
        } catch (err) {
            reject({ status: "failed", message: 'Unable to process your request!. Please Try Later' });

        }
    });


}

exports.getPOProductRow = function (product_id, parent_id, supplier_id, warehouse_id, addfrom, legal_entity_id, indent_id = 0) {
    return new Promise(function (resolve, reject) {
        try {
            Purchasedata.getProductInfoByID(supplier_id, warehouse_id, product_id, legal_entity_id).then(product => {
                // console.log("this is pringting ddddddddddddddddddddddelllllllllll",product);
                if (product.length > 0) {
                    let poArr = [];
                    poArr.push({ 'product_id': product_id, 'le_wh_id': warehouse_id, 'legal_entity_id': supplier_id });
                    //console.log("poooaeee",poArr);
                    module.exports.getTaxInfo(poArr).then(taxArr => {
                        if (taxArr == 0) {
                            return resolve({ status: 500, message: 'Tax Data Not Found!.' });
                        }
                        // console.log("taxNNNNNnnn", taxArr); // two objects
                        /**
                         * taxArr contains two objects.
                         * first object refers to GST Tax
                         * second object refers to CESS Tax if available for the product
                         */
                        let taxArrGST = taxArr[0];

                        // let taxArrCessObj = {};
                        // let taxArrCESS = taxArr[1];
                        // if (taxArr.length > 1) {
                        //     taxArrCessObj = {
                        //         "Tax_Class_ID": taxArrCESS['Tax Class ID'],
                        //         "Tax_Type": taxArrCESS['Tax Type'],
                        //         "Tax_Code": taxArrCESS['Tax Code'],
                        //         "Tax_Percentage": taxArrCESS['Tax Percentage'],
                        //         "HSN_Code": taxArrCESS.HSN_Code,
                        //         "CGST": taxArrCESS.CGST,
                        //         "SGST": taxArrCESS.SGST,
                        //         "IGST": taxArrCESS.IGST,
                        //         "UTGST": taxArrCESS.UTGST
                        //     }
                        // }

                        // console.log("taxarrrrrrrrrrrrrrrrrrrrrrrrrrrr",taxArr);// one object taking
                        let hsn_code = taxArrGST.hasOwnProperty('HSN_Code') ? taxArrGST.HSN_Code : null;
                        if (hsn_code == null) {
                            resolve({ status: 500, message: 'Please check HSN code could not found', 'productList': "" });
                        }
                        //console.log("hsn",hsn_code); 

                        Purchasedata.getProductPackInfo(product_id).then(async (packs) => {
                            let uom = '';
                            if (packs && packs.length > 0) {
                                // console.log("ifpacks");
                                let free_qty = 0;
                                if (parent_id != 0 && product.is_sellable == 0 && product.KVI == 'Q9') {
                                    let free_qty = 1;
                                } else {
                                    let parent_id = 0;
                                }
                                // if(indent_id>0){
                                //     Purchasedata.getIndentProduct(indent_id,product_id).then(indentProd=>{
                                //     })
                                // }        
                                let defltUOMEaches = ((packs.hasOwnProperty('no_of_eaches')) && packs.no_of_eaches != 0) ? packs.no_of_eaches : 1;
                                let cur_symbol = (product.hasOwnProperty('symbol') && product.symbol != '') ? product.symbol : 'Rs.';
                                let mrp = (product.hasOwnProperty('mrp') && product.mrp != '') ? product.mrp : 0;
                                let current_elp = (product.hasOwnProperty('dlp') && product.dlp != '') ? product.dlp : 0;
                                let prev_elp = (product.hasOwnProperty('prev_elp') && product.prev_elp != '') ? product.prev_elp : 0;
                                let result = await module.exports.getPackPrice(indent_id, product_id, defltUOMEaches, current_elp);
                                let { qty, packPrice, dlp } = result;

                                let diffCount = (result.hasOwnProperty('diffCount') && result.diffCount != '') ? result.diffCount : 0;
                                let totPoQty = (result.hasOwnProperty('totPoQty') && result.totPoQty != '') ? result.totPoQty : 0;
                                let totIndentQty = (result.hasOwnProperty('totIndentQty') && result.totIndentQty != '') ? result.totIndentQty : 0;
                                //console.log('result after packs',qty,packPrice,dlp);
                                let total = packPrice * (qty - free_qty);
                                var sumTax = 0;
                                var taxText = '';
                                var taxper = 0;
                                var taxname = '';

                                var tax_code = '';
                                var tax_data = '';

                                tax_code = taxArrGST['Tax Code'];
                                if (tax_code == '') {
                                    let tax_code = taxArrGST.hasOwnProperty('Tax Code') ? taxArrGST['Tax Code'] : '';
                                }
                                //console.log(taxArr);
                                //tax_data = JSON.Parse(taxArr);
                                let base_price = (total / (100 + taxper)) * 100;
                                let taxAmt = total - base_price;
                                Purchasedata.verifyNewProductInWH(warehouse_id, product_id).then(async (newProduct) => {
                                    let soh_data = await module.exports.getfinalsoh(supplier_id, product_id);
                                    let { final_soh, current_soh, noe } = soh_data;
                                    //product.push({"taxArr":taxArr});
                                    var data = {

                                        "product": product[0],
                                        "parent_id": parent_id,
                                        // "taxArr": [{
                                        //     "Tax_Class_ID": taxArrGST['Tax Class ID'],
                                        //     "Tax_Type": taxArrGST['Tax Type'],
                                        //     "Tax_Code": taxArrGST['Tax Code'],
                                        //     "Tax_Percentage": taxArrGST['Tax Percentage'],
                                        //     "HSN_Code": taxArrGST.HSN_Code,
                                        //     "CGST": taxArrGST.CGST,
                                        //     "SGST": taxArrGST.SGST,
                                        //     "IGST": taxArrGST.IGST,
                                        //     "UTGST": taxArrGST.UTGST
                                        // }, taxArrCessObj],
                                        "taxArr": taxArr,
                                        "packs": packs,
                                        "totPoQty": totPoQty,
                                        "totIndentQty": totIndentQty,
                                        "newProduct": newProduct,
                                        "final_soh": final_soh,
                                        "current_soh": current_soh,
                                        "noe": noe
                                    };
                                    // console.log("data", data);
                                    resolve({ status: 200, message: 'Data Found', data: [data] });

                                }).catch(err => {
                                    console.log(err);
                                    resolve({ status: 400, message: 'New product data not available' });
                                });
                            } else {
                                resolve({ status: 400, message: "Please add pack configuration", "productList": "" });
                                // resolve({ status: 400, message: 'Product packsssss data not found' });
                            }


                        }).catch(err => {
                            console.log(err);
                            resolve({ status: 400, message: 'Product pack data not found' });
                        });

                    }).catch(err => {
                        console.log(err);
                        resolve({ status: 400, message: 'please check tax information could not find' });
                    });


                } else {

                    resolve({ status: 200, message: 'Product Data Not Found', data: null });
                }
            }).catch(err => {
                console.log(err);
                resolve({ status: 400, message: 'please check tax information could not find' });
            });

        } catch (err) {
            resolve({ status: 400, message: 'Unable to process your request!. Please Try Later' })
        }
    })
}

exports.getTaxInfo = function (poArr) {
    return new Promise(function (resolve, reject) {
        let taxArr = [];
        // console.log('poooooooooooooooooooooooooArr', poArr);
        if (poArr.length > 0) {
            let product_id = poArr[0].product_id;
            //console.log("productid",product_id);
            let leWhId = poArr[0].hasOwnProperty('le_wh_id') ? poArr[0].le_wh_id : 0;
            //console.log("lewh",leWhId);
            let leId = poArr[0].hasOwnProperty('legal_entity_id') ? poArr[0].legal_entity_id : 0;
            //console.log("leee",leId);
            Purchasedata.getWarehouseById(leWhId).then(whDetail => {
                if (whDetail.length > 0) {
                    // console.log("waredataaaaaaaaaa", whDetail);
                    Purchasedata.getLegalEntityById(leId).then(supplierInfo => {
                        // console.log("leedataaaaaaaa", supplierInfo);
                        let wh_le_id = whDetail.hasOwnProperty('legal_entity_id') ? whDetail.legal_entity_id : 0;
                        //console.log("whleid",wh_le_id);
                        if (wh_le_id > 0 && leId == 24766) {
                            Purchasedata.getLegalEntityTypeId(wh_le_id).then(le_type_id => {
                                if (le_type_id == 1016) {
                                    Purchasedata.getApobData(wh_le_id).then(apob_data => {
                                        if (length.apob_data) {
                                            let supplierInfo = apob_data;
                                        }
                                    })
                                }
                            })
                        }
                        let wh_state_code = whDetail[0].hasOwnProperty('state') ? whDetail[0].state : 4033;
                        // console.log("whstateeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee", wh_state_code);
                        let seller_state_code = supplierInfo[0].hasOwnProperty('state_id') ? supplierInfo[0].state_id : 4033;
                        // console.log("sellerstatettttttttttttttttttttttttttttttttttttttttttttttt", seller_state_code);
                        poArr.forEach(async function (product) {
                            let prodTaxArr = await module.exports.getProductTaxClass(product_id, wh_state_code, seller_state_code)
                            //console.log("prodTaxArr",prodTaxArr);
                            let taxArr = prodTaxArr;
                            // console.log('taxArr,jjjjjj', taxArr);
                            // console.log(taxArr.ResponseBody); 
                            if (taxArr.Status == 200) {
                                resolve(taxArr.ResponseBody);
                            } else {
                                resolve(0);
                            }
                        })
                    })
                } else {
                    console.log("eh for warehouse");
                }
            })

        }
    })
}

exports.getProductTaxClass = function (product_id, wh_state_code, seller_state_code) {
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
                // console.log("parsedBody",parsedBody);
                let taxdata = parsedBody;
                resolve(taxdata);
            })

        })
    } catch (err) {
        console.log('err', err);
    }
};

exports.getPackPrice = function (indent_id, product_id, defltUOMEaches, current_elp) {
    try {
        return new Promise(async (resolve, reject) => {
            if (indent_id > 0) {
                // console.log("hereif");
                Purchasedata.getIndentProduct(indent_id, product_id).then(async (indentProd) => {
                    //    console.log("hereif");
                    //    console.log("indentProd",indentProd);
                    // Promise.all([ Purchasedata.getIndentProduct(indent_id,product_id),Purchasedata.getPoProductQtyByIndentId(indent_id,product_id),Purchasedata.getIndentProductQtyById(indent_id,product_id)]).then(finalresult=>{

                    // })
                    let totPoQty = await Purchasedata.getPoProductQtyByIndentId(indent_id, product_id)
                    // console.log("totPoQty", totPoQty);

                    // Purchasedata.getPoProductQtyByIndentId(indent_id,product_id).then(async(totPoQty) =>{
                    let totIndentQty = await Purchasedata.getIndentProductQtyById(indent_id, product_id)
                    // console.log("indentpoqtys", totPoQty, totIndentQty);
                    let diffCount = totIndentQty[0].totQty - totPoQty[0].totQty;
                    // console.log("diffcount", diffCount);
                    let diffResult = (diffCount / (defltUOMEaches));
                    if (Number(diffResult) === diffResult && diffResult % 1 !== 0) {

                    } else {
                        diffCount = diffResult;
                    }
                    let qty = diffCount;
                    // console.log("qtyy", qty);
                    // console.log("defltUOMEaches", defltUOMEaches);
                    let packPrice = indentProd.length > 0 && (indentProd[0].hasOwnProperty('target_elp') && indentProd[0].target_elp != '') ? indentProd[0].target_elp : 0;
                    // console.log("packprice", packPrice);
                    // console.log
                    let dlp = packPrice / defltUOMEaches; //one cfc price
                    let current_elp = dlp;
                    resolve({ qty, packPrice, dlp, current_elp, diffCount, totPoQty, totIndentQty });
                    //})

                })
            } else {
                // console.log("hereelse");
                let qty = 1;
                let dlp = current_elp;
                let packPrice = dlp * defltUOMEaches;
                resolve({ qty, packPrice, dlp });
            }

        })
    } catch (e) {

    }
}

module.exports.getfinalsoh = function (supplier_id, product_id) {
    return new Promise((resolve, reject) => {
        let le_wh_id = "SELECT lewh.le_wh_id FROM legalentity_warehouses AS lewh WHERE lewh .`legal_entity_id` = " + supplier_id + " AND lewh.dc_type= 118001";
        db.query(le_wh_id, {}, function (err, lewhid) {
            if (lewhid.length > 0) {
                let supplier_le_wh_id = lewhid[0].le_wh_id;
                Purchasedata.checkInventory(product_id, supplier_le_wh_id).then(current_soh => {
                    let Noe = "select SUM(pop.qty*pop.no_of_eaches) as noe from `po_products` as `pop` join `po` AS `po`  ON po.po_id=pop.po_id where `pop`.`product_id` = ? AND po_status=87001 AND po.legal_entity_id=?"
                    db.query(Noe, [product_id, supplier_id], function (err, noofeaches) {
                        let final_soh = current_soh - noofeaches[0].noe;
                        let noe = noofeaches[0].noe;
                        resolve({ final_soh, current_soh, noe });
                    })
                })
            } else {
                let final_soh = 0, current_soh = 0, noe = 0;
                resolve({ final_soh, current_soh });
            }
        })

    })

}


exports.savePurchaseOrderAction = function (req, res) {
    try {
        //        return new Promise(async (resolve,reject)=>{

        var data = JSON.parse(req.body.data);
        let supplier_id = data.hasOwnProperty('supplier_list') ? data.supplier_list : '';
        let warehouse_id = data.hasOwnProperty('warehouse_list') ? data.warehouse_list : '';
        var po_products = data.hasOwnProperty('po_products') ? data.po_products : '';
        //console.log("poproducts",po_products);
        if (data == '') {
            res.status(400).json({ status: "failed", message: 'Please Enter input valid data.' })
        } else if (!supplier_id || !warehouse_id) {
            res.status(400).json({ status: "failed", message: 'Please select warehouse and supplier.' })
        } else if (!po_products) {
            res.status(400).json({ status: "failed", message: 'Please select products' })
        } else {
            Purchasedata.checkSupplier(supplier_id).then(async checkSupplier => {
                //console.log("checkSupplier",checkSupplier);
                if (Array.isArray(checkSupplier) && checkSupplier.length == 0) {
                    res.status(400).json({ status: "failed", message: 'Please check supplier is not Active/Approved' })
                }
                //check order limit to be greater than purchase order i.e purchase order should not exceed order limit
                let productInfo = data.hasOwnProperty('po_products') ? data.po_products : [];
                let le_wh_id = warehouse_id;
                let packsize = data.packsize;
                let po_product_qty = data.hasOwnProperty('qty') ? data.qty : '';
                let stock_transfer = data.hasOwnProperty('stock_transfer') ? data.stock_transfer : ''
                let stock_transfer_dc = data.hasOwnProperty('st_dc_name') ? data.st_dc_name : ''
                var supply_le_wh_id = data.hasOwnProperty('supply_le_wh_id') ? data.supply_le_wh_id : 0


                /**
                 * stock transfer cases
                 */
                if (stock_transfer > 0) {
                    let is_eb_supplier = await Purchasedata.checkIsEbutorSupplier(supplier_id);
                    if (!is_eb_supplier) {
                        res.send({ 'status': 'failed', 'message': 'Please select Ebutor Supplier to transfer stock' });
                    }
                    if (stock_transfer_dc == warehouse_id) {
                        res.send({ 'status': 'failed', 'message': 'Stock Transfer Location and Delivery Location should not same to transfer stock' });
                    }
                    if (supply_le_wh_id > 0) {
                        res.send({ 'status': 'failed', 'message': 'Please uncheck Stock Transfer to Select DC Supply.' });
                    }
                    if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                        res.send({ 'status': 'failed', 'message': 'Please select Stock Transfer Location to transfer stock' });
                    }
                    let whDetail = await Purchasedata.getWarehouseById(warehouse_id);
                    let whDetailTypeId = whDetail[0].hasOwnProperty('legal_entity_type_id') ? whDetail[0].legal_entity_type_id : 0
                    let stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                    let stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0
                    if (whDetailTypeId != 1001 || stWhDetailTypeId != 1001) {
                        res.send({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be APOB to transfer stock.' });
                    }
                    var whDetailStateId = whDetail[0].hasOwnProperty('state') ? whDetail[0].state : 0
                    var stWhDetailStateId = stWhDetail[0].hasOwnProperty('state') ? stWhDetail[0].state : 0
                    if (whDetailStateId != stWhDetailStateId) {
                        res.send({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be in same state to transfer stock.' });
                    }
                } else if (stock_transfer_dc > 0) {
                    res.send({ 'status': 'failed', 'message': 'Please check Stock Transfer to transfer stock.' });
                }



                /**
                 * checking inventory --------
                 */
                // if (stock_transfer > 0) {
                //     let product_title = product.hasOwnProperty('pname') ? product.pname : 0;
                //     let sku = product.hasOwnProperty('sku') ? product.sku : 0;
                //     let availQty = await Purchasedata.checkInventory(products.po_product_id, stock_transfer_dc);
                //     let poProductQty = po_product.no_of_eaches * po_product.qty;

                //     if ($availQty < $poProductQty) {
                //         product_inv_ids.push(products.po_product_id);
                //         inventoryData.push({ 'product_name': product_title, 'avail_qty': availQty, 'po_qty': poProductQty, 'sku': sku });
                //     }
                // }
                // if (stock_transfer > 0) {
                //     if (product_inv_ids.length > 0) {
                //         res.send({ 'status': 'failed', "reason": "No Inventory to transfer stock!", "message": "inv_error_found", "adjust_message": "Add or Remove for No Inventory Products", 'data': $inventoryData });
                //     }
                // }

                /**
                 * check inventory pending
                 */
                /**
                 * Stock transfer case ends
                 */

                let product_id = po_products[0].po_product_id;
                Purchasedata.getlegalidbasedondcid(le_wh_id).then(getleidfordcid => {
                    let legal_entity_id = getleidfordcid[0].legal_entity_id;
                    //console.log("legal_entity_id",legal_entity_id);
                    Purchasedata.checkIsSelfTax(legal_entity_id).then(async is_self_tax => {
                        // console.log("is_self_tax", is_self_tax);
                        let checkorderlimitwith_po = false;
                        if (productInfo) {
                            //console.log("proooo",productInfo);
                            await Promise.all(productInfo.map((value) => module.exports.getPoamountData(value))).then(productdata => {
                            });

                        }
                        // console.log("abcd" + poamount);
                        let dcleid = data.hasOwnProperty('dc_warehouse_id') ? data.dc_warehouse_id : 0;
                        if (dcleid == "") {
                            dcleid = le_wh_id;
                        }
                        //console.log("dcleid",dcleid);

                        let checkLOC = await Purchasedata.checkLOCByLeWhid(dcleid);//.then(async checkLOC => {
                        // console.log("checkloc", checkLOC);
                        //                                      console.log("poamount",poamount);
                        if (poamount > checkLOC) {
                            let contact_data = await Purchasedata.getLEWHById(dcleid);//.then(contact_data => {
                            // console.log("contactdata", contact_data);
                            let credit_limit_check = contact_data[0].credit_limit_check;;
                            // console.log("credit_limit_check", credit_limit_check);
                            if (credit_limit_check == 1) {
                                // console.log('qwerty');
                                let whDetail = await Purchasedata.getWarehouseById(dcleid);//.then(whDetail => {
                                //console.log("whdetail",whDetail);
                                let display_name = whDetail[0].display_name;
                                // console.log("display_namerrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr", display_name);
                                //}) 
                                qty = 0;
                                no_of_eaches = 0;
                                cur_elp = 0;
                                poamount = 0;                                 //resolve({ status: 200, message: 'PO value is greaterthan order limit, PO cannot be placed. Current order limit for '+display_name+' is Rs '+checkLOC+'', po_id:''});
                                res.status(200).json({ status: "failed", message: 'PO value is greaterthan order limit, PO cannot be placed. Current order limit for ' + display_name + ' is Rs ' + checkLOC + '', po_id: 0 })

                            } else {
                                Purchasedata.savePurchaseOrderData(data).then(saveData => {
                                    //console.log("data",saveData);
                                    let data = saveData;
                                    qty = 0;
                                    no_of_eaches = 0;
                                    cur_elp = 0;
                                    poamount = 0;
                                    res.status(200).json({ status: "success", message: 'Po Details Inserted Successfully', po_id: data })
                                })
                            }
                            //})  
                            //                                      resolve({ status: 200, message: 'PO value is greaterthan order limit, PO cannot be placed. Current order limit for '+display_name+' is Rs '+checkLOC+'', po_id:''});
                        }
                        else {
                            //})
                            Purchasedata.savePurchaseOrderData(data).then(saveData => {
                                //console.log("data",saveData);
                                let data = saveData;
                                qty = 0;
                                no_of_eaches = 0;
                                cur_elp = 0;
                                poamount = 0;
                                res.status(200).json({ status: "success", message: 'Po Details Inserted Successfully', po_id: data })
                            })
                        }


                    }).catch((err) => {
                        console.log(err);
                        res.status(400).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })
                    });
                }).catch((err) => {
                    console.log(err);
                    res.status(400).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })
                });
            }).catch((err) => {
                console.log(err);
                res.status(400).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })
            });
        }
        //})          

    } catch (err) {
        res.status(500).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })

    }
}

exports.getPoamountData = async function (product_data) {
    return new Promise(async (resolve, reject) => {
        let po_product = [];
        qty = product_data.hasOwnProperty('qty') ? product_data.qty : 1;
        // console.log("qty", qty);
        let pack_id = product_data.hasOwnProperty('packsize') && product_data.packsize != '' ? product_data.packsize : '';
        //console.log("pack_id",pack_id);
        Purchasedata.getProductPackUOMInfoById(pack_id).then(async uomPackinfo => {
            //console.log("uomPackinfo",uomPackinfo);
            no_of_eaches = uomPackinfo[0].hasOwnProperty('no_of_eaches') ? uomPackinfo[0].no_of_eaches : 0;
            //console.log("no_of_eacheeeeeeees",no_of_eaches);
            cur_elp = product_data.hasOwnProperty('curelpval') ? product_data.curelpval : 0;
            //console.log("cur_elppppp",cur_elp);
            // console.log(qty * no_of_eaches * cur_elp + 'qty' + qty + 'no_of_eaches' + no_of_eaches + 'cur_elp' + cur_elp);
            poamount = poamount + (qty * no_of_eaches * cur_elp);
            // console.log("poamounttttttt" + poamount);
            let tax_name_obj = product_data.hasOwnProperty('po_taxname') && product_data.po_taxname != '' ? product_data.po_taxname : '';
            //console.log("tax_name_objj",tax_name_obj, typeof tax_name_obj);
            // let tax_name = JSON.stringify(tax_name_obj);
            //console.log("taxname",tax_name_obj);
            let tax_name_arr = tax_name_obj.split(",");
            //console.log("tax_name_arr",tax_name_arr);
            if (tax_name_arr.length > 1) {
                //  console.log("hereifff",tax_name_arr);
                res.send({ status: "failed", message: 'Tax Info Error For Product Id:' + product_id, 'po_id': '' });
            }
            // console.log("beforeresolve" + poamount);
            resolve(poamount);
        })
        //resolve(poamount);
    })

}

/*flag representations: flag:1->printpo,
                        flag:2->downloadpopdf,
                        print_flag_type:stock transfer print */

module.exports.poDetails = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let userId = data.user_id;
        let poId = data.po_id;
        var flag = 0;
        var print_flag_type = 0;
        if (data.hasOwnProperty('flag')) {
            flag = data.flag;
        }
        if (data.hasOwnProperty('print_flag_type')) {
            print_flag_type = data.print_flag_type;
        }
        if (poId != '' && userId != '') {
            let legal_entity_id = await Purchasedata.getLEID(userId);
            // console.log('leid', legal_entity_id);

            let poDetailArr = await Purchasedata.getPoDetailById(poId, legal_entity_id);
            let podocs = await Purchasedata.getpoDocs(poId);
            // res.send(poDetailArr);
            // return;
            if (poDetailArr.length > 0) {
                let poDetail = poDetailArr[0];
                let poAddress = (poDetail.po_address != '') ? (JSON.parse(poDetail.po_address)) : '';
                var is_approval_display = 0;
                if (poDetail.po_status != 87003 && poDetail.po_status != 87004) {
                    if ((poDetail.approval_status == 57117) && poDetail.po_status == 87001) {
                    } else {
                        is_approval_display = 1;
                    }
                }

                // /** get TaxBreakUp details from poDetailArr.tax_data
                //  * 
                //  *
                let taxBreakUp = await getTaxBreakUp(poDetailArr);

                var taxPerr = '';
                taxBreakUp.forEach(tax1 => {
                    taxPerr = (taxPerr == '') ? tax1.tax : taxPerr;
                });
                var template = '';
                if (flag == 1) {
                    template = 'printpo';
                    if (taxPerr == '') {
                        template = 'printpo_gst';
                    }
                    if (print_flag_type == 1) {
                        template = "printpo_st";
                    }
                }
                if (flag == 2) {
                    template = 'downloadPO';
                    if (taxPerr == '') {
                        template = 'downloadPO_gst';
                    }
                }

                let leWhId = (poDetail.le_wh_id != 'undefined') ? poDetail.le_wh_id : 0;
                let leId = (poDetail.legal_entity_id != 'undefined') ? poDetail.legal_entity_id : 0;
                let whDetails = await Purchasedata.getWarehouseId(leWhId); // changed the conflicting name form getWarehouseById to getWarehouseId
                // console.log('whDetails',leId);
                let userInfo = await Purchasedata.getUserLeId(leId);
                let isSupplier = await Purchasedata.checkUserIsSupplier(userId);
                isSupplier = isSupplier.length;
                let whDetail = {};
                let billingDetail = {};
                let supplier = {};
                let whLeId = '';
                // console.log('poAddress',poAddress);
                if (poAddress == '' || poAddress == null) {
                    whDetail = billingDetail = whDetails;
                    supplier = await Purchasedata.getLegalEntityDetails(leId);
                    whLeId = (whDetail.legal_entity_id != 'undefined') ? whDetail.legal_entity_id : 0;
                } else {
                    billingDetail = (poAddress['billing'] != 'undefined') ? poAddress['billing'] : {};
                    whDetail = (poAddress['shipping'] != 'undefined') ? poAddress['shipping'] : {};
                    supplier = (poAddress['supplier'] != 'undefined') ? poAddress['supplier'] : {};
                }

                // get packTypes to calculate uom, freeUom
                let packTypes = await masterLookUp.getAllOrderStatus('Levels');
                packTypes = packTypes.reduce((a, b) => Object.assign({}, a, b));

                let productDetails = [];
                let poType = ((poDetail.po_type == 1) ? 'Qty Based' : 'Value Based');
                let indentId = poDetail.indent_id;

                //get the indentCode from indentId and show the data in PODetails[POType]
                let poIndentCode = '';
                let indentCode = '';
                if (indentId != 0 && indentId != '') {
                    poIndentCode = await Purchasedata.getIndentCodeById(indentId);
                    indentCode = poIndentCode[0].indent_code;
                    // console.log('indentCode', indentCode);
                }

                //poStatusId ===== '87001': 'OPEN','87002': 'PO CLOSED','87003': 'EXPIRED','87004': 'CANCELLED','87005': 'PARTIALLY RECEIVED'
                //get poStatus from master_lookup and show the data in PODetails[Status]
                let poStatus = '';
                if (poDetail.po_status != "" && poDetail.po_status != 0) {
                    let poStatusArr = await masterLookUp.getAllOrderStatus('PURCHASE_ORDER');
                    poStatusArr = poStatusArr.reduce((a, b) => Object.assign({}, a, b));
                    poStatus = poStatusArr[poDetail.po_status];
                }

                //approvalStatusId ==== 'initiated':57106,'created':57029,'verified':57030,'approved':57031,'posit':57033,'checked':57107,'receivedatdc':57034,'grncreated':57035,'shelved':1,'payments':57032
                //get approvalStatus from master_lookup and show the data in PODetails[ApprovalStatus]
                let approvalStatus = '';
                let approvalStatusId = '';
                if (poDetail.approval_status != 0 && poDetail.approval_status != '') {
                    let approvalStatusArr = await masterLookUp.getAllOrderStatus('Approval Status');
                    approvalStatusArr = approvalStatusArr.reduce((a, b) => Object.assign({}, a, b));
                    approvalStatus = approvalStatusArr[poDetail.approval_status];
                    approvalStatusId = `${("approval_status" in poDetail) ? poDetail.approval_status : ''}`;
                }

                //**
                // *  edit PO/ update PO feature access based on roles and po status ; 
                // */
                let is_supplier_edit = await roleRepo.checkPermissionByFeatureCode('PO0013', userId);
                let isEditable = await roleRepo.checkPermissionByFeatureCode('PO007', userId);
                let is_edit = false;
                if (poDetail.approval_status != '57117' && poDetail.po_status == '87001' && isEditable) is_edit = true;
                let is_create_grn = false;
                if (poDetail.po_status == '87001' && [57107, 57119, 57120].includes(poDetail.approval_status)) is_create_grn = true;

                let grandTotal = 0;
                poDetailArr.forEach(product1 => {
                    let discAmtItem = 0;
                    if (product1.apply_discount == 1) {
                        if (product1.item_discount_type == 1) {
                            discAmtItem = ((product1.sub_total) * (product1.item_discount)) / 100;
                        } else {
                            discAmtItem = product1.item_discount;
                        }
                    }
                    grandTotal = product1.sub_total - discAmtItem;
                })

                //below variables are used to get Total Sum
                let sumTaxableValue = 0;
                let sumTaxAmount = 0;
                let sumTotal = 0;
                let discountBeforeTax = (poDetail.discount_before_tax != 'undefined' && poDetail.discount_before_tax == 1) ? poDetail.discount_before_tax : 0;
                let tax_amount = 0;
                let taxable_amount = 0;
                let tax_percent = '';
                let totDiscount = (poDetail.discount_on_total != 'undefined') ? poDetail.discount_on_total : 0;
                let shipping_fee = (poDetail.shipping_fee) ? poDetail.shipping_fee : 0;
                let is_cgst_display = 0;
                let is_sgst_display = 0;
                let is_utgst_display = 0;
                let is_igst_display = 0;
                let is_cess_display = 0;
                for (const product of poDetailArr) {

                    //below variables are used to calculate and show the result in Product Details Grid
                    let uom = ((product.uom != '' && product.uom != 0 && packTypes[product.uom] != 'undefined') ? packTypes[product.uom] : 'Ea');
                    let freeUom = ((product.free_uom != '' && product.free_uom != 0 && packTypes[product.free_uom] != 'undefined') ? packTypes[product.free_uom] : 'Eaches');
                    let noOfEaches = (product.no_of_eaches == 0 || product.no_of_eaches == '') ? 0 : product.no_of_eaches;
                    let freeNoOfEaches = (product.free_eaches == 0 || product.free_eaches == '') ? 0 : product.free_eaches;
                    let qty = (product.qty != '') ? product.qty : 0;
                    let freeQty = (product.free_qty != '') ? product.free_qty : 0;
                    let basePrice = product.price;
                    let isTaxIncluded = product.is_tax_included;
                    let mrp = +(product.mrp);
                    let subTotal = product.sub_total;
                    let discAmt = 0;
                    let unitPrice = product.unit_price;
                    let currentElp = product.unit_price;
                    let totQty = ((qty * noOfEaches) - (freeQty * freeNoOfEaches));

                    //CESS changes
                    var product_tax = [];
                    var tax_types = {};
                    let cgst_val = 0;
                    let sgst_val = 0;
                    let igst_val = 0;
                    let cess_val = 0;
                    let utgst_val = 0;
                    let cgstPer = 0;
                    let sgstPer = 0;
                    let utgstPer = 0;
                    let igstPer = 0;
                    let cessPer = 0;
                    if (product.tax_data != [] && product.tax_data != null) {
                        var product_id = product.product_id;
                        product_tax[product_id] = [];
                        var ProducttaxName = product.tax_name;
                        var taxprecent;
                        var tax_name;
                        var taxprecent;
                        var taxAmt;
                        var cgst;
                        var sgst;
                        var igst;
                        var utgst;
                        for (const tax of product.tax_data) {
                            taxprecent = tax['Tax Percentage'];
                            tax_name = tax['Tax Type'];
                            if (!(tax_types[tax_name]))
                                tax_types[tax_name] = tax_name;

                            taxAmt = (tax.hasOwnProperty('taxAmt')) ? tax['taxAmt'] : product.tax_amt;
                            cgst = (tax.hasOwnProperty('CGST')) ? tax['CGST'] : 0;
                            sgst = (tax.hasOwnProperty('SGST')) ? tax['SGST'] : 0;
                            igst = (tax.hasOwnProperty('IGST')) ? tax['IGST'] : 0;
                            utgst = (tax.hasOwnProperty('UTGST')) ? tax['UTGST'] : 0;
                            product_tax[product_id][tax_name] =
                                { 'tax_per': taxprecent, 'tax_amt': taxAmt, 'CGST': cgst, 'SGST': sgst, 'IGST': igst, 'UTGST': utgst };
                        }
                        let cg = (product_tax[product_id][ProducttaxName]['CGST']) ? product_tax[product_id][ProducttaxName]['CGST'] : 0;
                        let sg = (product_tax[product_id][ProducttaxName]['SGST']) ? product_tax[product_id][ProducttaxName]['SGST'] : 0;
                        let ig = (product_tax[product_id][ProducttaxName]['IGST']) ? product_tax[product_id][ProducttaxName]['IGST'] : 0;
                        let ut = (product_tax[product_id][ProducttaxName]['UTGST']) ? product_tax[product_id][ProducttaxName]['UTGST'] : 0;
                        cgstPer = (product.tax_per * cg) / 100;
                        sgstPer = (product.tax_per * sg) / 100;
                        igstPer = (product.tax_per * ig) / 100;
                        utgstPer = (product.tax_per * ut) / 100;

                        cgst_val = (product.tax_amt * cg) / 100;
                        sgst_val = (product.tax_amt * sg) / 100;
                        igst_val = (product.tax_amt * ig) / 100;
                        utgst_val = (product.tax_amt * ut) / 100;
                        cess_val = (product_tax[product_id]['CESS']) ? product_tax[product_id]['CESS']['tax_amt'] : 0;
                        cessPer = (product_tax[product_id]['CESS']) ? product_tax[product_id]['CESS']['tax_per'] : 0;
                        var taxPercentage = 0;
                        var taxTypes = product.tax_data;

                        var totTaxAmt = 0;
                        taxTypes.forEach(tvalue => {
                            taxPercentage = taxPercentage + tvalue['Tax Percentage'];
                            if (tvalue.hasOwnProperty('taxAmt')) {
                                totTaxAmt = (+totTaxAmt) + (+tvalue.taxAmt);
                            } else {
                                totTaxAmt = (+totTaxAmt) + (+product.tax_amt);
                            }
                        });
                    }
                    if (isTaxIncluded == 1) {
                        basePrice = (basePrice / (1 + (taxPercentage / 100)));
                        unitPrice = (unitPrice / (1 + (taxPercentage / 100)));
                    } else {
                        currentElp = unitPrice + ((unitPrice * taxPercentage) / 100);
                    }

                    if (product.apply_discount == 1) {
                        if (product.item_discount_type == 1) {
                            discAmt = (subTotal * (product.item_discount)) / 100;
                        } else {
                            discAmt = product.item_discount;
                        }
                    }

                    let totalAfterItemDisc = subTotal - discAmt;
                    if (product.apply_discount_on_bill == 1) {
                        if (product.discount_type == 1) {
                            discAmt = discAmt + (totalAfterItemDisc * (product.discount)) / 100;
                        } else {
                            let contribution = (grandTotal > 0) ? (totalAfterItemDisc / grandTotal) : 0;
                            discAmt = discAmt + (product.discount * contribution);
                        }
                    }

                    let unitDisc = discAmt / (qty * noOfEaches);
                    currentElp = (currentElp - unitDisc);
                    if (discountBeforeTax == 1) {
                        currentElp = product.cur_elp;
                    }
                    currentElp = +currentElp;

                    let prevElp, thirtyD, std;
                    if (isSupplier == 0) {
                        prevElp = +product.prev_elp;
                        thirtyD = +((product.thirtyd != null && product.thirtyd != 0) ? product.thirtyd : product.dlp);
                        std = +product.std;
                    } else {
                        prevElp = 0;
                        thirtyD = 0;
                        std = 0;
                    }
                    let totPrice = unitPrice * totQty;
                    let applyDiscout = (product.apply_discount == 1) ? 'Yes' : 'No';
                    //let taxName = (product.tax_name == '') ? '' : product.tax_name + ' @';
                    let taxName = '';
                    let taxes = [];
                    let taxNamePer = '';
                    let taxes_name_per = [];
                    let all_names_per = [];
                    let all_taxes = ''
                    for (var i = 0; i < taxTypes.length; i++) {
                        all_taxes = (taxTypes[i]['Tax Type'] == '') ? ('') : taxTypes[i]['Tax Type'] + '@' + (taxTypes[i]['Tax Percentage']);
                        taxName = (taxTypes[i]['Tax Type'] == '') ? ('') : taxTypes[i]['Tax Type'];
                        taxNamePer = (taxTypes[i]['Tax Percentage']);
                        all_names_per.push(all_taxes);
                        taxes.push(taxName);
                        taxes_name_per.push(taxNamePer);
                    }
                    // let subTotal = product.sub_total;

                    // subTotal = 
                    basePrice = +basePrice;
                    tax_percent = (product.tax_per != '') ? product.tax_per : 0;
                    tax_amount += +product.tax_amt;
                    taxable_amount += +totPrice;
                    tax_name = product.tax_name;
                    totDiscount = (+totDiscount) + (+product.discount_amount);
                    if ('GST' in tax_types || 'UTGST' in tax_types) {
                        is_cgst_display = 1;
                    }
                    if ('UTGST' in tax_types)
                        is_utgst_display = 1;
                    else
                        is_sgst_display = 1;
                    if ('IGST' in tax_types)
                        is_igst_display = 1;
                    if ('CESS' in tax_types)
                        is_cess_display = 1;

                    //regarding utgst changes
                    if (sgstPer != 0) {
                        sgstPer = sgstPer;
                        sgst_val = sgst_val.toFixed(2);
                    } else if (utgstPer != 0) {
                        sgstPer = utgstPer;
                        sgst_val = utgst_val.toFixed(2);
                    } else {
                        sgstPer = 0;
                        sgst_val = 0;
                    }

                    let result = {};
                    result = {
                        "ProductName": product.product_title,
                        "Sku": product.sku,
                        "HSNCode": product.hsn_code,
                        "Qty": `${qty} ${uom} ${uom != 'Ea' ? '(' + (qty * noOfEaches) + ' Eaches)' : ''}`,
                        "FreeQty": `${freeQty} ${freeUom} ${freeUom != 'Eaches' ? ((freeQty * freeNoOfEaches) + ' Eaches') : ''}`,
                        "InvDays": 0,// avlble_inv_days is not is sql query;
                        "MRP": mrp.toFixed(3),
                        "LP": currentElp.toFixed(3),
                        "PreviousLP": prevElp.toFixed(3),
                        "thirtyD": thirtyD.toFixed(3),
                        "STD": std.toFixed(3),
                        "BaseRate": basePrice.toFixed(3),
                        "TaxableValue": totPrice.toFixed(2),// looks like need to calculate
                        "TaxAmount": totTaxAmt,
                        "TaxName": all_names_per,
                        "tax_name": taxes,
                        "tax_percent": taxes_name_per,
                        "ApplyDiscount": `Apply: ${applyDiscout} Value: ${(product.item_discount != '') ? parseFloat(+product.item_discount).toFixed(2) : 0} ${(product.item_discount_type == 1) ? '%' : 'Flat'}`,
                        "Discount": (+product.item_discount).toFixed(2),
                        "Total": (+product.sub_total).toFixed(2),
                        "CGST": cgst_val.toFixed(2),
                        "CGST_PER": cgstPer,
                        "Eachesqty": product.inv_qty,
                        "EachesFreeQty": product.inv_free_qty,
                        "UnitPrice": product.inv_unit_price,
                        "SubTotal": product.inv_price,
                        "SGST": sgst_val,
                        "SGST_PER": sgstPer,
                        "IGST": igst_val.toFixed(2),
                        "IGST_PER": igstPer,
                        "CESS": Number(cess_val).toFixed(2),
                        "CESS_PER": cessPer
                    }
                    productDetails.push(result);

                    sumTaxableValue = (sumTaxableValue + +(result.TaxableValue));
                    sumTaxableValue = +sumTaxableValue.toFixed(2);
                    sumTaxAmount = sumTaxAmount + +(totTaxAmt);
                    sumTaxAmount = +sumTaxAmount.toFixed(2);
                    sumTotal = sumTotal + +(result.Total);
                    sumTotal = +sumTotal.toFixed(2);
                }
                res.send({
                    'Status': "Success",
                    "Message": "Records found",
                    "Supplier": {
                        "Name": supplier.business_legal_name,
                        "Address": `${supplier.address1},${(supplier.address2 != '') ? supplier.address2 : ''},${supplier.city}, ${supplier.state_name},${supplier.country_name},${supplier.pincode}`,
                        "Address1": `${supplier.address1}`,
                        "Address2": `${supplier.address2}`,
                        "City": `${supplier.city}`,
                        "Pincode": `${supplier.pincode}`,
                        "Phone": (userInfo != null) ? userInfo.mobile_no : '',
                        "Email": (userInfo != null) ? userInfo.email_id : '',
                        "BankName": (supplier.sup_bank_name != null && supplier.sup_bank_name != 'undefined') ? supplier.sup_bank_name : 'NA',
                        "AccNo": (supplier.sup_account_no != null && supplier.sup_account_no != 'undefined') ? supplier.sup_account_no : 'NA',
                        "AccName": (supplier.sup_account_name != null && supplier.sup_account_name != 'undefined') ? supplier.sup_account_name : 'NA',
                        "IfscCode": (supplier.sup_ifsc_code != null && supplier.sup_ifsc_code != 'undefined') ? supplier.sup_ifsc_code : 'NA',
                        "State": (supplier.state_name != null && supplier.state_name != 'undefined') ? supplier.state_name : '',
                        "StateCode": (supplier.state_code != null && supplier.state_code != 'undefined') ? supplier.state_code : '',
                        "PAN": (supplier.pan_number != null) ? supplier.pan_number : 'NA',
                        "GstinUin": (supplier.gstin != null && supplier.gstin != 'undefined') ? supplier.gstin : '',
                    },
                    "DeliveryAddress": {
                        "Name": whDetail.lp_wh_name,
                        "ConsigneeCode": whDetail.le_wh_code,
                        "Address": `${whDetail.address1},${(whDetail.address2 != '') ? whDetail.address2 : ''},${(whDetail.address2 != '') ? whDetail.city : ''},${(whDetail.state_name != '') ? whDetail.state_name : ''},${(whDetail.country_name != '') ? whDetail.country_name : ''},${(whDetail.pincode != '') ? whDetail.pincode : ''}`,
                        "Address1": `${whDetail.address1}`,
                        "Address2": `${whDetail.address2}`,
                        "City": `${whDetail.city}`,
                        "Pincode": `${whDetail.pincode}`,
                        "ContactName": (whDetail.contact_name != null) ? whDetail.contact_name : '',
                        "Phone": (whDetail.phone_no != null) ? whDetail.phone_no : 0,
                        "Email": (whDetail.email != null) ? whDetail.email : 0,
                        "State": (whDetail.state_name != null) ? whDetail.state_name : '',
                        "StateCode": (whDetail.state_code != null) ? whDetail.state_code : 0,
                        "GstinNo": (whDetail.gstin != null) ? whDetail.gstin : 0,
                        "FSSAI_NO": (whDetail.fssai != null) ? whDetail.fssai : 0,
                    },
                    "Billingaddress": {
                        "Name": billingDetail.business_legal_name,
                        "Address": `${billingDetail.address1},${(billingDetail.address2 != '') ? billingDetail.address2 : ''},${(billingDetail.address2 != '') ? billingDetail.city : ''},${(billingDetail.state_name != '') ? billingDetail.state_name : ''},${(billingDetail.country_name != '') ? billingDetail.country_name : ''},${(billingDetail.pincode != '') ? billingDetail.pincode : ''}`,
                        "Address1": `${billingDetail.address1}`,
                        "Address2": `${billingDetail.address2}`,
                        "City": `${billingDetail.city}`,
                        "Pincode": `${billingDetail.pincode}`,
                        "State": (billingDetail.state_name != null) ? billingDetail.state_name : '',
                        "StateCode": (billingDetail.state_code != null) ? billingDetail.state_code : 0,
                        "GstinNo": (billingDetail.gstin != null) ? billingDetail.gstin : 0,
                        "FSSAI_NO": (billingDetail.fssai != null) ? billingDetail.fssai : 0,
                        "PanNo": (billingDetail.pan_number != null) ? billingDetail.pan_number : 0,
                        "TinNo": (billingDetail.tin_number != null) ? billingDetail.tin_number : 0,
                    },
                    "PODetails": {
                        "PONumber": poDetail.po_code,
                        "PODate": poDetail.po_date,
                        "DelDate": poDetail.delivery_date,
                        "POType": ((indentId != "" && indentId != 0) ? `Indent - ${indentCode} ` : `Direct PO (${poType})`),
                        "po_type": poDetail.po_type,
                        "PaymentMode": ((poDetail.payment_mode == 2) ? 'Pre Paid' : 'Post Paid'),
                        "PaymentDueDate": poDetail.payment_due_date,
                        "CreatedBy": poDetail.user_name,
                        "DCtoSupply": `${(poDetail.dc_name != null && poDetail.dc_name != '') ? poDetail.dc_name : 0}`,
                        "Status": ((poStatus != '') ? poStatus : 0),
                        "ApprovalStatus": ((approvalStatus != '') ? approvalStatus : 0),
                        "ApprovalStatusId": approvalStatusId,
                        // "ProformaInvoice": 0//PoDocs is being sent seperately.
                    },
                    "ProductDetails": productDetails,
                    "Total": {
                        "SumTaxableValue": sumTaxableValue,
                        "SumTaxAmount": sumTaxAmount,
                        "SumTotal": sumTotal,
                    },
                    "TotalInWords": converter(sumTotal),
                    "DiscountOnBill": {
                        "Apply": ((poDetail.apply_discount_on_bill == 1) ? "Yes" : "No"),
                        "Value": ((poDetail.discount != '') ? Number(poDetail.discount) : 0),
                        "DiscountType": ((poDetail.discount_type == 1) ? "%" : "Flat"),
                        "ApplicableBeforeTax": ((poDetail.discount_before_tax == 1) ? "Yes" : "No"),
                        "StockTransfer": ((poDetail.stock_transfer == 1) ? "Yes" : "No")
                    },
                    "TaxDetails": taxBreakUp,
                    "Template": template,
                    "poId": poId,
                    "poValidity": poDetail.po_validity,
                    "leId": leId,
                    "is_editable": is_edit,
                    "invoiceDetails": {
                        "InvoiceCode": (poDetail.invoice_code) ? (poDetail.invoice_code) : 'null',
                        "invoiceDate": (poDetail.invoice_date) ? (poDetail.invoice_date) : 'null',
                        "GRNCode": (poDetail.inward_code) ? (poDetail.inward_code) : 'null',
                        "GrnDate": (poDetail.inward_date) ? (poDetail.inward_date) : 'null',
                        "poCode": (poDetail.po_code) ? (poDetail.po_code) : 'null',
                        "CreatedBy": (poDetail.invoice_created_name) ? (poDetail.invoice_created_name) : 'null',
                        "BillDisc": (poDetail.discount_on_total) ? (parseFloat(poDetail.discount_on_total).toFixed(2)) : 0,
                        "TotalDisc": totDiscount.toFixed(3),
                        "ShippingFee": (+shipping_fee).toFixed(3)
                    },
                    "PoDocs": podocs,
                    "Remarks": poDetail.po_remarks,
                    "isApprovalDisplay": is_approval_display,
                    "iscgstDisplay": is_cgst_display,
                    "isutgstDisplay": is_utgst_display,
                    "issgstDisplay": is_sgst_display,
                    "isigstDisplay": is_igst_display,
                    "iscessDisplay": is_cess_display,
                    "IsSupplierEditable": is_supplier_edit,
                    "IsCreateGrn": is_create_grn,
                    "supplier_id": `${("legal_entity_id" in poDetail) && poDetail.legal_entity_id != null ? poDetail.legal_entity_id : 0}`,
                    "supply_le_wh_id": `${("supply_le_wh_id" in poDetail) && poDetail.supply_le_wh_id != null ? poDetail.supply_le_wh_id : 0}`,
                    "stock_transfer": `${("stock_transfer" in poDetail) && poDetail.stock_transfer != null ? poDetail.stock_transfer : 0}`,
                    "st_dc_name": `${("st_dc_name" in poDetail) && poDetail.st_dc_name != null ? poDetail.st_dc_name : ''}`,
                    "warehouse_id": `${("le_wh_id" in poDetail) && poDetail.le_wh_id != null ? poDetail.le_wh_id : 0}`,
                    "stock_transfer_dc": `${("stock_transfer_dc" in poDetail) && poDetail.stock_transfer_dc != null ? poDetail.stock_transfer_dc : 0}`,
                });
                return;
                // */
            }
            else {
                res.send({ 'Status': 'Failed', "Message": "Please enter valid input" });
            };


        }
    } catch (err) {
        console.log('error poDetails', err);
        res.send({ 'Status': 'Failed', "Message": "No Data" });
    }
}



exports.getPoamountData = async function (product_data) {
    return new Promise(async (resolve, reject) => {
        let po_product = [];
        qty = product_data.hasOwnProperty('qty') ? product_data.qty : 1;
        // console.log("qty", qty);
        let pack_id = product_data.hasOwnProperty('packsize') && product_data.packsize != '' ? product_data.packsize : '';
        //console.log("pack_id",pack_id);
        Purchasedata.getProductPackUOMInfoById(pack_id).then(async uomPackinfo => {
            //console.log("uomPackinfo",uomPackinfo);
            no_of_eaches = uomPackinfo[0].hasOwnProperty('no_of_eaches') ? uomPackinfo[0].no_of_eaches : 0;
            //console.log("no_of_eacheeeeeeees",no_of_eaches);
            cur_elp = product_data.hasOwnProperty('curelpval') ? product_data.curelpval : 0;
            //console.log("cur_elppppp",cur_elp);
            // console.log(qty * no_of_eaches * cur_elp + 'qty' + qty + 'no_of_eaches' + no_of_eaches + 'cur_elp' + cur_elp);
            poamount = poamount + (qty * no_of_eaches * cur_elp);
            // console.log("poamounttttttt" + poamount);
            let tax_name_obj = product_data.hasOwnProperty('po_taxname') && product_data.po_taxname != '' ? product_data.po_taxname : '';
            //console.log("tax_name_objj",tax_name_obj, typeof tax_name_obj);
            // let tax_name = JSON.stringify(tax_name_obj);
            //console.log("taxname",tax_name_obj);
            let tax_name_arr = tax_name_obj.split(",");
            //console.log("tax_name_arr",tax_name_arr);
            if (tax_name_arr.length > 1) {
                //  console.log("hereifff",tax_name_arr);
                // res.send({ status: "failed", message: 'Tax Info Error For Product Id:' + product_id, 'po_id': '' });
                reject(`Tax Info Error For Product Id `)
            }
            // console.log("beforeresolve" + poamount);
            resolve(poamount);
        })
        //resolve(poamount);
    })

}

let getTaxBreakUp = async (poDetailArr) => {
    let cgstAmt = 0;
    let igstAmt = 0;
    let sgstAmt = 0;
    let utgstAmt = 0;
    let cessAmt = 0;
    let finalTaxArr = [];

    poDetailArr.forEach(product => {
        //get Tax Breakup details based on product.tax_data
        if (product.tax_data != [] && product.tax_data != null) {
            let taxDetails;
            // if (Array.isArray(product.tax_data)) taxDetails = product.tax_data[0];
            // else taxDetails = product.tax_data;
            let cgstVal = 0;
            let igstVal = 0;
            let utgstVal = 0;
            let sgstVal = 0;
            for (const taxDetails of product.tax_data) {
                let tax_cess = 0;
                if (taxDetails['Tax Type'] == "CESS" && taxDetails.hasOwnProperty('taxAmt'))
                    tax_cess = taxDetails.taxAmt;
                let tax_cgst = taxDetails.hasOwnProperty('CGST') ? taxDetails.CGST : 0;
                let tax_igst = taxDetails.hasOwnProperty('IGST') ? taxDetails.IGST : 0;
                let tax_sgst = taxDetails.hasOwnProperty('SGST') ? taxDetails.SGST : 0;
                let tax_utgst = taxDetails.hasOwnProperty('UTGST') ? taxDetails.UTGST : 0;

                cgstVal = +(product.tax_amt * tax_cgst) / 100;
                igstVal = +(product.tax_amt * tax_igst) / 100;
                sgstVal = +(product.tax_amt * tax_sgst) / 100;
                utgstVal = +(product.tax_amt * tax_utgst) / 100;

                cgstAmt += cgstVal;
                igstAmt += igstVal;
                sgstAmt += sgstVal;
                utgstAmt += utgstVal;
                cessAmt += tax_cess;
            }
        } else {
            let finalTax = {
                'tax': product.tax_per, 'name': product.tax_name, 'taxAmt': +product.tax_amt
            }
            finalTaxArr.push(finalTax);
        }
    })

    let CGST =
    {
        'tax': '',
        'name': 'CGST',
        'taxAmt': cgstAmt.toFixed(3)
    };
    let IGST =
    {
        'tax': '',
        'name': 'IGST',
        'taxAmt': igstAmt.toFixed(3)
    };
    let SGST =
    {
        'tax': '',
        'name': 'SGST',
        'taxAmt': sgstAmt.toFixed(3)
    };
    let UTGST =
    {
        'tax': '',
        'name': 'UTGST',
        'taxAmt': utgstAmt.toFixed(3)
    };
    let CESS =
    {
        'tax': '',
        'name': 'CESS',
        'taxAmt': cessAmt.toFixed(3)
    };

    if (CGST.taxAmt != 0) { finalTaxArr.push(CGST) };
    if (IGST.taxAmt != 0) { finalTaxArr.push(IGST) };
    if (SGST.taxAmt != 0) { finalTaxArr.push(SGST) };
    if (UTGST.taxAmt != 0) { finalTaxArr.push(UTGST) };
    if (CESS.taxAmt != 0) { finalTaxArr.push(CESS) };

    return finalTaxArr;

};

module.exports.approvalHistory = async (req, res) => {
    try {

        let data = JSON.parse(req.body.data);
        let module_id = data.module_id;
        let module_name = data.module_name;
        //get information for ApprovalHistory Page
        let approvalHistory = await Purchasedata.getApprovalHistory(module_id, module_name);

        const approvalHistoryArr = [];
        approvalHistory.forEach(history => {

            let profilePicture = (history.hasOwnProperty('profile_picture')) ? history.profile_picture : '';
            //let profilePicture = '';
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

        res.send({ 'Status': 'Success', 'Message': 'Records Found', 'approvalHistory': approvalHistoryArr });

    } catch (err) {
        console.log('Approval History API Error', err);
        res.send({ 'Status': 'Failed', 'Message': 'No Data' });
    }
}


module.exports.edit = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let userId = data.user_id;
        let poId = data.po_id;

        if (poId != '' && userId != '') {
            let legal_entity_id = await Purchasedata.getLEID(userId);
            let poDetailArr = await Purchasedata.getPoDetailById(poId, legal_entity_id);
            let poDetail = poDetailArr[0];
            let podocs = await Purchasedata.getpoDocs(poId);
            // let poAddress = (poDetail.po_address != '') ? (JSON.parse(poDetail.po_address)) : '';
            let productDetails = [];
            // console.log('poAddress', poAddress);
            let leId = (poDetail.legal_entity_id != 'undefined') ? poDetail.legal_entity_id : 0;
            let userInfo = await Purchasedata.getUserLeId(leId);
            let supplier = await Purchasedata.getLegalEntityDetails(leId);
            let leWhId = (poDetail.le_wh_id != 'undefined') ? poDetail.le_wh_id : 0;
            let whDetails = await Purchasedata.getWarehouseId(leWhId);
            let productId = poDetail.product_id;
            // console.log(supplier);
            // console.log((whDetails));
            // let packInfo = await Purchasedata.getProductPackInfo(productId);
            // console.log('productId', packInfo);
            //get poStatus from master_lookup and show the data in PODetails[Status]
            let poStatus = '';
            if (poDetail.po_status != "" && poDetail.po_status != 0) {
                let poStatusArr = await masterLookUp.getAllOrderStatus('PURCHASE_ORDER');
                poStatusArr = poStatusArr.reduce((a, b) => Object.assign({}, a, b));
                poStatus = poStatusArr[poDetail.po_status];
            }

            //get approvalStatus from master_lookup and show the data in PODetails[ApprovalStatus]
            let approvalStatus = '';
            if (poDetail.approval_status != 0 && poDetail.approval_status != '') {
                let approvalStatusArr = await masterLookUp.getAllOrderStatus('Approval Status');
                approvalStatusArr = approvalStatusArr.reduce((a, b) => Object.assign({}, a, b));
                approvalStatus = approvalStatusArr[poDetail.approval_status];
            }


            // console.log('whDetails',whDetails);
            let isSupplier = await Purchasedata.checkUserIsSupplier(userId);
            isSupplier = isSupplier.length;

            // / get packTypes to calculate uom, freeUom
            let packTypes = await masterLookUp.getAllOrderStatus('Levels');
            packTypes = packTypes.reduce((a, b) => Object.assign({}, a, b));

            let poType = ((poDetail.po_type == 1) ? 'Qty Based' : 'Value Based');
            let indentId = poDetail.indent_id;

            //get the indentCode from indentId and show the data in PODetails[POType]
            let indentCode = '';
            if (indentId != 0 && indentId != '') {
                indentCode = await Purchasedata.getIndentCodeById(indentId);
            }

            let grandTotal = 0;
            poDetailArr.forEach(product1 => {
                let discAmtItem = 0;
                if (product1.apply_discount == 1) {
                    if (product1.item_discount_type == 1) {
                        discAmtItem = ((product1.sub_total) * (product1.item_discount)) / 100;
                    } else {
                        discAmtItem = product1.item_discount;
                    }
                }
                grandTotal = product1.sub_total - discAmtItem;
            })


            //edit dc to supply 
            let warehouseList = await Purchasedata.getWarehouseList(leId, userId);
            let updateSupplyDC = [];
            warehouseList.forEach(data => {
                let result = {};
                result = {
                    "value": data.le_wh_id,
                    "name": `${data.lp_wh_name} - ${data.le_wh_code}`
                }
                updateSupplyDC.push(result);
            })

            //below variables are used to get Total Sum
            let sumTaxableValue = 0;
            let sumTaxAmount = 0;
            let sumTotal = 0;

            let discountBeforeTax = (poDetail.discount_before_tax != 'undefined' && poDetail.discount_before_tax == 1) ? poDetail.discount_before_tax : 0;

            // res.send(poDetail);
            // /* 
            let paymentMode = `${("payment_mode" in poDetail) ? poDetail.payment_mode : 1}`;
            paymentMode = (paymentMode == 1 ? 'Post Paid' : 'Pre Paid');

            //  get Product Details array
            // Since we use await in the loop, we cannot use higher order functions like forEach.
            for (const product of poDetailArr) {

                //get final_soh
                let finalSoh = await module.exports.getfinalsoh(leId, product.product_id);
                // console.log('finalSoh',finalSoh);
                // let currentSoh = finalSoh.final_soh;
                // console.log('currentSoh',currentSoh);

                //get selectedPack and selectedFreePack
                let selectedPack = 0;
                let selectedFreePack = 0;

                let packInfo = await Purchasedata.getProductPackInfo(product.product_id);
                // console.log('productId', packInfo);
                // console.log('product', product);
                for (const pack of packInfo) {
                    // console.log('pack', pack.level);
                    if (pack.level == product.uom && product.uom != 0) {
                        selectedPack = pack.pack_id;
                        // console.log('selectedPack', selectedPack, product.uom, pack.level, product.product_id);
                    }
                    if (pack.level == product.free_uom && product.free_uom != 0) {
                        selectedFreePack = pack.pack_id;
                        // console.log('selectedFreePack', selectedFreePack, product.free_uom, pack.level, product.product_id);
                    }
                }

                let poArr = [];
                poArr.push({ 'product_id': product.product_id, 'le_wh_id': product.le_wh_id, 'legal_entity_id': product.legal_entity_id });
                let taxArrs = await module.exports.getTaxInfo(poArr);
                let taxArr = (taxArrs != 0) ? taxArrs[0] : 0;
                // console.log('taxArr', taxArr);


                //below variables are used to calculate and show the result in Product Details Grid
                let uom = ((product.uom != '' && product.uom != 0 && packTypes[product.uom] != 'undefined') ? packTypes[product.uom] : 'Ea');
                let freeUom = ((product.free_uom != '' && product.free_uom != 0 && packTypes[product.free_uom] != 'undefined') ? packTypes[product.free_uom] : 'Eaches');
                let noOfEaches = (product.no_of_eaches == 0 || product.no_of_eaches == '') ? 0 : product.no_of_eaches;
                let freeNoOfEaches = (product.free_eaches == 0 || product.free_eaches == '') ? 0 : product.free_eaches;
                let qty = (product.qty != '') ? product.qty : 0;
                let freeQty = (product.free_qty != '') ? product.free_qty : 0;
                let basePrice = product.price;
                let isTaxIncluded = product.is_tax_included;
                let mrp = +(product.mrp);
                let subTotal = product.sub_total;
                let discAmt = 0;
                let unitPrice = product.unit_price;
                let currentElp = product.unit_price;
                let totQty = ((qty * noOfEaches) - (freeQty * freeNoOfEaches));


                if (isTaxIncluded == 1) {
                    basePrice = (basePrice / (1 + (product.tax_per / 100)));
                    unitPrice = (unitPrice / (1 + (product.tax_per / 100)));
                } else {
                    currentElp = unitPrice + ((unitPrice * product.tax_per) / 100);
                }

                if (product.apply_discount == 1) {
                    if (product.item_discount_type == 1) {
                        discAmt = (subTotal * (product.item_discount)) / 100;
                    } else {
                        discAmt = product.item_discount;
                    }
                }

                let totalAfterItemDisc = subTotal - discAmt;
                if (product.apply_discount_on_bill == 1) {
                    if (product.discount_type == 1) {
                        discAmt = discAmt + (totalAfterItemDisc * (product.discount)) / 100;
                    } else {
                        let contribution = (grandTotal > 0) ? (totalAfterItemDisc / grandTotal) : 0;
                        discAmt = discAmt + (product.discount * contribution);
                    }
                }

                let unitDisc = discAmt / (qty * noOfEaches);
                currentElp = (currentElp - unitDisc);
                if (discountBeforeTax == 1) {
                    currentElp = product.cur_elp;
                }
                currentElp = +currentElp;

                let prevElp, thirtyD, std;
                if (isSupplier == 0) {
                    prevElp = +product.prev_elp;
                    thirtyD = +((product.thirtyd != null && product.thirtyd != 0) ? product.thirtyd : product.dlp);
                    std = +product.std;
                } else {
                    prevElp = 0;
                    thirtyD = 0;
                    std = 0;
                }
                let totPrice = unitPrice * totQty;
                let applyDiscout = (product.apply_discount == 1) ? 'checked' : '';
                //let taxName = (product.tax_name == '') ? '' : product.tax_name + ' @';
                let taxName = (Object.keys(taxArr).length > 0 && taxArr['Tax Type'] != '') ? taxArr['Tax Type'] + ' @' + taxArr['Tax Percentage'] : 0;

                //check taxincluded
                let taxIncludeCheck = (isTaxIncluded == 1) ? 'checked' : '';

                let result = {};
                result = {
                    "parent_id": product.parent_id,
                    "product_id": product.product_id,
                    "SKU": product.sku,
                    "ProductName": product.product_title,
                    "Qty": qty,
                    "packUom": product.uom,
                    "no_of_eaches": product.no_of_eaches,
                    "selectedPack": selectedPack,
                    // "Qty Uom": `${uom}(${(qty * noOfEaches)})`, // need more info in dropdown
                    // "pid":packInfo.pack
                    // "HSNCode": product.hsn_code,
                    "FreeQty": freeQty,
                    "freePackUom": product.free_uom,
                    "free_eaches": product.free_eaches,
                    "selectedFreePack": selectedFreePack,
                    // "FreeQtyUom": `${uom}(${(qty * noOfEaches)})`, // need more info in dropdown
                    // "InvDays": 0,// avlble_inv_days is not is sql query;
                    "packs": packInfo,
                    "taxArr": taxArrs,
                    "final_soh": finalSoh.final_soh,
                    "current_soh": finalSoh.current_soh,
                    "noe": finalSoh.noe,
                    "unit_price": product.unit_price,
                    "discount_type": product.discount_type,
                    "apply_discount": product.apply_discount,

                    "MRP": mrp.toFixed(3),
                    "LP": currentElp.toFixed(3),
                    "PreviousLP": prevElp.toFixed(3),
                    // "thirtyD": thirtyD.toFixed(3),
                    // "STD": std.toFixed(3),
                    // "BaseRate": +(basePrice.toFixed(3)),
                    "Price": product.price,
                    "taxIncludeCheck": taxIncludeCheck,
                    "SubTotal": product.sub_total - product.tax_amt,
                    // "TaxPercent": taxName + parseFloat(+product.tax_per).toFixed(0),
                    "TaxPercent": taxName,
                    // "TaxPercent": taxArr['Tax Type'] + parseFloat(+product.tax_per).toFixed(0),

                    "Tax": parseFloat(+product.tax_amt).toFixed(2),
                    // "TaxableValue": totPrice.toFixed(2),// looks like need to calculate
                    // "TaxAmount": parseFloat(product.tax_amt).toFixed(2),
                    // "TaxName": taxName + parseFloat(product.tax_per).toFixed(2),
                    // "ApplyDiscount": `Apply: ${applyDiscout} Value: ${(product.item_discount != '') ? parseFloat(product.item_discount).toFixed(2) : 0} ${(product.item_discount_type == 1) ? '%' : 'Flat'}`,
                    "ApplyDiscount": applyDiscout,
                    // "Discount": parseFloat(product.item_discount).toFixed(2),
                    "Discount": `${(+product.item_discount != '') ? parseFloat(+product.item_discount) : 0}`,
                    "checkDiscount": `${product.item_discount_type == 1 ? 'checked' : ''}`,
                    // "Discount": `${(productItem != '') ? parseFloat(productItem).toFixed(2) : 0}`,
                    // "Dscound" : parseFloat(+product.item_discount).toFixed(2),
                    "checkDiscount": `${product.item_discount_type == 1 ? 'checked' : ''}`,
                    "Total": parseFloat(product.sub_total).toFixed(2)
                }
                productDetails.push(result);

                sumTaxableValue = (sumTaxableValue + +(totPrice));
                sumTaxableValue = +sumTaxableValue.toFixed(2);
                sumTaxAmount = sumTaxAmount + +(product.tax_amt);
                sumTaxAmount = +sumTaxAmount.toFixed(2);
                sumTotal = sumTotal + +(result.Total);
                sumTotal = +sumTotal.toFixed(2);

            }

            res.send({
                "Status": "Success", "Message": "Records Found",
                "poId": poId,
                "userId": userId,
                "PurchaseOrder": poDetail.po_code,
                "Supplier": {
                    "Name": supplier.business_legal_name,
                    "Address": `${supplier.address1} \n ${supplier.address2}, ${supplier.city}, ${supplier.state_name}, ${supplier.country_name},${supplier.pincode}`,
                    "Phone": (userInfo != null) ? userInfo.mobile_no : '',
                    "Email": (userInfo != null) ? userInfo.email_id : '',
                    "GSTIN_UIN": `${supplier.gstin != null ? supplier.gstin : ''}`,
                    "FssaiNo": `${("fssai" in supplier) && supplier.fssai != null ? supplier.fssai : ''}`,
                    "PanNo": `${("pan_number" in supplier) && supplier.pan_number != null ? supplier.pan_number : 'NA'}`,
                    "BankName": (supplier.sup_bank_name != null && supplier.sup_bank_name != 'undefined') ? supplier.sup_bank_name : 'NA',
                    "AccNo": (supplier.sup_account_no != null && supplier.sup_account_no != 'undefined') ? supplier.sup_account_no : 'NA',
                    "AccName": (supplier.sup_account_name != null && supplier.sup_account_name != 'undefined') ? supplier.sup_account_name : 'NA',
                    "IfscCode": (supplier.sup_ifsc_code != null && supplier.sup_ifsc_code != 'undefined') ? supplier.sup_ifsc_code : 'NA',
                    "State": (supplier.state_name != null && supplier.state_name != 'undefined') ? supplier.state_name : '',
                    "StateCode": (supplier.state_code != null && supplier.state_code != 'undefined') ? supplier.state_code : '',
                },
                "DeliveryAddress": {
                    "Name": whDetails.lp_wh_name,
                    "Address": `${whDetails.address1}, ${whDetails.address2}, ${whDetails.city}, ${whDetails.state_name}, ${whDetails.country_name},${whDetails.pincode}`,
                    "ContactPerson": `${(whDetails.contact_name != '' && whDetails.contact_name != null) ? whDetails.contact_name : ''}`,
                    "Phone": whDetails.phone_no,
                    "Email": `${(whDetails.email != '' && whDetails.email != null) ? whDetails.email : ''}`,
                    "State": `${(whDetails.state_name != '' && whDetails.state_name != null) ? whDetails.state_name : ''}`,
                    "StateCode": `${(whDetails.state_code != '' && whDetails.state_code != null) ? whDetails.state_code : ''}`
                },
                "PODetails": {
                    "PONumber": poDetail.po_code,
                    "PODate": poDetail.po_date,
                    "DeliveryDate": poDetail.delivery_date,
                    "ExpectedDeliveryDate": `${(poDetail.exp_delivery_date != null) ? poDetail.exp_delivery_date : ''}`,
                    "POType": `${poDetail.po_type == 1 ? 'Qty Based' : 'Value Based'}`,
                    "po_type": poDetail.po_type,
                    "CreatedBy": poDetail.user_name,
                    "Status": ((poStatus != '') ? poStatus : 0),
                    "ApprovalStatus": ((approvalStatus != '') ? approvalStatus : 0),
                    "DCtoSupply": `${(poDetail.dc_name != null && poDetail.dc_name != '') ? poDetail.dc_name : 0}`, // edit if poDetail.po_status == 87001;
                    "PoSoCode": `${(poDetail.po_so_order_code != '' && poDetail.po_so_order_code != null) ? poDetail.po_so_order_code : 0}`,// edit if poDetail.po_status == 87001 & has editable access;
                    "StockTransferLocation": `${("st_dc_name" in poDetail) && poDetail.st_dc_name != null ? poDetail.st_dc_name : 0}`// edit if poDetail.po_status == 87001;
                },
                "PaymentMode": paymentMode,
                "PaymentDueDate": poDetail.payment_due_date,
                "LogisticCost": poDetail.logistics_cost,
                // "ProformaInvoiceQuote":,
                //"parent_id": poDetail.parent_id,
                "ProductDetails": productDetails,
                "ApplyDiscountOnBill": `${("apply_discount_on_bill" in poDetail) && poDetail.apply_discount_on_bill == 1 ? 'checked' : ''}`,
                "Discount": poDetail.discount,
                "DiscountTypeCheck": `${("discount_type" in poDetail) && poDetail.discount_type == 1 ? 'checked' : ''}`,
                "DiscountBeforeTax": `${("discount_before_tax" in poDetail) && poDetail.discount_before_tax == 1 ? 'checked' : ''}`,
                "StockTransfer": `${("stock_transfer" in poDetail) && poDetail.stock_transfer == 1 ? 'checked' : ''}`,
                "TotalPrice": sumTaxableValue,
                "TotalTax": sumTaxAmount,
                "GrandTotal": sumTotal,
                "Remarks": poDetail.po_remarks,
                "st_dc_name": `${("st_dc_name" in poDetail) && poDetail.st_dc_name != null ? poDetail.st_dc_name : 0}`,
                "supplier_id": `${("legal_entity_id" in poDetail) && poDetail.legal_entity_id != null ? poDetail.legal_entity_id : 0}`,
                "supply_le_wh_id": `${("supply_le_wh_id" in poDetail) && poDetail.supply_le_wh_id != null ? poDetail.supply_le_wh_id : 0}`,
                "stock_transfer": `${("stock_transfer" in poDetail) && poDetail.stock_transfer != null ? poDetail.stock_transfer : 0}`,
                "stock_transfer_dc": `${("stock_transfer_dc" in poDetail) && poDetail.stock_transfer_dc != null ? poDetail.stock_transfer_dc : 0}`,
                "warehouse_id": `${("le_wh_id" in poDetail) && poDetail.le_wh_id != null ? poDetail.le_wh_id : 0}`,
                "le_wh_id": `${("supply_le_wh_id" in poDetail) && poDetail.supply_le_wh_id != null ? poDetail.supply_le_wh_id : 0}`,
                "poValidity": poDetail.po_validity,
                "PoDocs": podocs,
                "create_order": poDetail.create_order
                // "updateSupplyDC" : updateSupplyDC,
            })

            // */

        } else {
            // console.log("invalid input");
            res.send({ 'Status': 'Failed', 'Message': 'No Data' });
        }
    } catch (err) {
        console.log('Approval History API Error', err);
        res.send({ 'Status': 'Failed', 'Message': 'No Data' });
    }

}

module.exports.invoiceList = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let poId = data.po_id;
        let invoicesArr = await Purchasedata.getAllInvoices(poId);
        // let totalInvoices = await Purchasedata.getAllInvoices(poId, 1);
        let dataArr = [];

        if (invoicesArr[0].inward_id != null) {
            invoicesArr.forEach(invoice => {
                let grandTotal = +invoice.grand_total;
                grandTotal = grandTotal.toFixed(2);
                let result = {};
                result = {
                    "invoiceId": invoice.invoice_code,
                    "inward_id": invoice.inward_code,
                    "billingName": invoice.billing_name,
                    "totalAmount": grandTotal,
                    "invoiceDate": invoice.created_at,
                    "TotalQty": +invoice.totQty
                    // "status" : 
                }

                dataArr.push(result);
            });

            res.send({ 'status': 'Success', 'message': 'Invoice List', 'data': dataArr });
        } else {
            res.send({ 'status': 'Failed', 'message': 'No Data Found' });
        }

    } catch (err) {
        console.log("invoiceList error");
        res.send({ 'status': 'Failed', 'message': 'No Data' });
    }
}


// exports.editAction = function(req,res){
//     try {
//         return new Promise(async (resolve,reject)=>{
//             var data = JSON.parse(req.body.data);
//             var token_id = data.lp_token;
//             var po_id= data.po_id;
//             Purchasedata.checkLpToken(token_id).then(response =>{
//                 Purchasedata.getData(token_id).then((response1) => {
//                     var user_id = response1.user_id;
//                     var legal_entity_id= response1.legal_entity_id
//                     let hasAccess = "select count(*) as aggregate from `role_access` inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` where (`user_roles`.`user_id` = ? and `features`.`feature_code` = 78023 and `features`.`is_active` = 1)";
//                             db.query(hasAccess,[user_id], function (err, featurecheck) {
//                             console.log("feature",featurecheck);
//                                 if(featurecheck == 0){
//                                     res.status(400).json({ status: "failed", message: 'Unable to process your request!.Feature code not found' })
//                                 }
//                                 Purchasedata.getPoDetailById(po_id,legal_entity_id).then(poDetailArr =>{
//                                     console.log("poDetailArr",poDetailArr);
//                                     if(poDetailArr.length == 0){

//                                     }
//                                     let leWhId = poDetailArr[0].hasOwnProperty('le_wh_id') ? poDetailArr[0].le_wh_id : 0;
//                                     let indentId = poDetailArr[0].hasOwnProperty('indent_id') ? poDetailArr[0].indent_id : 0;
//                                     let leId = poDetailArr[0].hasOwnProperty('legal_entity_id') ? poDetailArr[0].legal_entity_id : 0;
//                                     let dc_name = poDetailArr[0].hasOwnProperty('dc_name') ? poDetailArr[0].dc_name : 0;
//                                     let supply_le_wh_id = poDetailArr[0].hasOwnProperty('supply_le_wh_id') ? poDetailArr[0].supply_le_wh_id : 0;
//                                     module.exports.getwarehouselist(legal_entity_id,user_id).then(warehouselist=>{
//                                         console.log("warehouselist",warehouselist);
//                                         let indentCode = '';
//                                         if (indentId) {
//                                             let indentCodee = "select `indent`.`indent_code` from `indent` as `indent` where `indent`.`indent_id` = ? limit 1";
//                                             db.query(indentCode,[indentId], function (err, featurecheck) {
//                                             })    
//                                         }
//                                         Purchasedata.getWarehouseById(leWhId).then(whDetail =>{
//                                             console.log("whDetail",whDetail);
//                                             Purchasedata.getUserByLeId(leId).then(userInfo =>{
//                                                 console.log("userInfo",userInfo);
//                                                 Purchasedata.getLegalEntityById(leId).then(supplierInfo =>{
//                                                     console.log("supplierinfo",supplierInfo);
//                                                     module.exports.getTaxInfo(poDetailArr).then(taxArr=>{
//                                                         console.log("taxArr",taxArr);
//                                                         let uom = [];
//                                                         let freeuom = [];
//                                                         Purchasedata.getInwardProductsCountByPOId(po_id).then(inwardProductCount=>{
//                                                             console.log("inwardProductCount",inwardProductCount);
//                                                             let poDetailArr1 = [];
//                                                             Purchasedata.getSuppliersforIndents([{"indent_id":0,leId,user_id}]).then(suppliers_list=>{
//                                                             }) 
//                                                             let le_le_wh_id = "SELECT lewh.le_wh_id FROM legalentity_warehouses AS lewh WHERE lewh .`legal_entity_id` = ? AND lewh.dc_type=118001"; 
//                                                             db.query(le_le_wh_id,[leWhId], function (err, le_wh_id_le) {

//                                                                 let Lelewhid =  le_le_wh_id[0].hasOwnProperty('le_wh_id') ? le_le_wh_id[0].le_wh_id : 0;
//                                                                 console.log("le_wh_id_le",Lelewhid);

//                                                             })   

//                                                         })    
//                                                     })    
//                                                 })    
//                                             })    
//                                         })   

//                                     })    

//                                 })

//                 })           })

//             })                



//         })
//     } catch (err) {
//           res.status(500).json({ status: "failed", message: 'Unable to process your request!. Please Try Later' })

//     }
// }   
// }           

exports.approvalSubmit = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            var approval_unique_id = data.hasOwnProperty('approval_unique_id') ? data.approval_unique_id : ''
            var approval_status = data.hasOwnProperty('approval_status') ? data.approval_status : 0
            var current_status = data.hasOwnProperty('current_status') ? data.current_status : 0
            var approval_comment = data.hasOwnProperty('approval_comment') ? data.approval_comment : ''
            var user_id = data.hasOwnProperty('user_id') ? data.user_id : ''
            let status = approval_status.split(",");
            let nextStatus = status[0];
            let nextstatuses = [57121, 57146];
            if (nextStatus == 57117) {
                var finance_users = await Purchasedata.getUsersByRoleCode(user_id); // Only finance team can cancel PO if payment initiated
                var userIdByCode = arrayColumn(finance_users, 'user_id');
                console.log(userIdByCode);
                function arrayColumn(finance_users, columnName) {
                    return finance_users.map(function (value, index) {
                        return value[columnName];
                    })
                }
                var response = await Purchasedata.checkPayment(approval_unique_id);
                if (response > 0 && !userIdByCode.includes(user_id))
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please close payment requests initiated to cancel this PO' }));
            }
            if (!nextstatuses.includes(nextStatus))
                await Purchasedata.updateStatusAWF(approval_unique_id, approval_status, user_id);
            res.send(encryption.encrypt({ 'status': 'success', 'message': 'Success' }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}
/*
Function Name: updateStDC,
Input Params : required st_dc_name,po_id,supplier_id,supply_le_wh_id,stock_transfer,warehouse_id
Result or Response : success message of updated stock transfer for po
*/
// apis with encryption and decryption

exports.updateStDC = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            //let data=(req.body.data);
            data = JSON.parse(data);
            var le_id = data.hasOwnProperty('le_id') ? data.le_id : 0
            var le_wh_id = data.hasOwnProperty('st_dc_name') ? data.st_dc_name : 0
            var po_id = data.hasOwnProperty('po_id') ? data.po_id : 0
            var supplier_id = data.hasOwnProperty('supplier_id') ? data.supplier_id : 0
            var supply_le_wh_id = data.hasOwnProperty('supply_le_wh_id') ? data.supply_le_wh_id : 0
            var stock_transfer = data.hasOwnProperty('stock_transfer') ? data.stock_transfer : ''
            var stock_transfer_dc = data.hasOwnProperty('st_dc_name') ? data.st_dc_name : ''
            var warehouse_id = data.hasOwnProperty('warehouse_id') ? data.warehouse_id : ''
            if (stock_transfer_dc > 0) {
                stock_transfer = 1;
            } else {
                stock_transfer = 0;
            }
            if (stock_transfer > 0) {
                var is_eb_supplier = await Purchasedata.checkIsEbutorSupplier(supplier_id);
                if (!is_eb_supplier) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Ebutor Supplier to transfer stock' }));
                }
                if (stock_transfer_dc == warehouse_id) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Stock Transfer Location and Delivery Location should not same to transfer stock' }));
                }
                if (supply_le_wh_id > 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please uncheck Stock Transfer to Select DC Supply.' }));
                }
                if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Stock Transfer Location to transfer stock' }));
                }
                var whDetail = await Purchasedata.getWarehouseById(warehouse_id);
                var whDetailTypeId = whDetail[0].hasOwnProperty('legal_entity_type_id') ? whDetail[0].legal_entity_type_id : 0
                var stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                var stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0
                if (whDetailTypeId != 1001 || stWhDetailTypeId != 1001) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be APOB to transfer stock.' }));
                }
                var whDetailStateId = whDetail[0].hasOwnProperty('state') ? whDetail[0].state : 0
                var stWhDetailStateId = stWhDetail[0].hasOwnProperty('state') ? stWhDetail[0].state : 0
                if (whDetailStateId != stWhDetailStateId) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be in same state to transfer stock.' }));
                }
            } else if (stock_transfer_dc > 0) {
                res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please check Stock Transfer to transfer stock.' }));
            }
            var params = { 'po_id': po_id, 'stock_transfer_dc': le_wh_id, 'is_stock_transfer': stock_transfer }
            await Purchasedata.updatePoStDc(params);
            var poDetailArr = await Purchasedata.getPoDetailById(po_id, le_id);
            var display_name = ((poDetailArr[0].st_dc_name) && poDetailArr[0].st_dc_name != "") ? (poDetailArr[0].st_dc_name) : '';
            res.send(encryption.encrypt({
                'status': "success",
                "message": "Stock Transfer Location updated successfully.",
                "Data": {
                    "Display_Name": display_name,
                    "dc_id": le_wh_id,
                }
            }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

/*
Function Name: updateSupplyDC,
Input Params : required supply_dc_name,po_id,supplier_id,stock_transfer,warehouse_id,stock_transfer_dc
Result or Response : success message of updated supply dc for po
*/

exports.updateSupplyDC = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            var le_id = data.hasOwnProperty('le_id') ? data.le_id : 0
            var le_wh_id = data.hasOwnProperty('supply_dc_name') ? data.supply_dc_name : 0
            var po_id = data.hasOwnProperty('po_id') ? data.po_id : 0
            var supplier_id = data.hasOwnProperty('supplier_id') ? data.supplier_id : 0
            var supply_le_wh_id = le_wh_id
            var stock_transfer = data.hasOwnProperty('stock_transfer') ? data.stock_transfer : ''
            var stock_transfer_dc = data.hasOwnProperty('stock_transfer_dc') ? data.stock_transfer_dc : ''
            var warehouse_id = data.hasOwnProperty('warehouse_id') ? data.warehouse_id : '';
            let create_order = data.hasOwnProperty('create_order') ? data.create_order : 0;
            if (stock_transfer > 0) {
                var is_eb_supplier = await Purchasedata.checkIsEbutorSupplier(supplier_id);
                if (!is_eb_supplier) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Ebutor Supplier to transfer stock' }));
                }
                if (stock_transfer_dc == warehouse_id) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Stock Transfer Location and Delivery Location should not same to transfer stock' }));
                }
                if (supply_le_wh_id > 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please uncheck Stock Transfer to Select DC Supply.' }));
                }
                if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Stock Transfer Location to transfer stock' }));
                }
                var whDetail = await Purchasedata.getWarehouseById(warehouse_id);
                var whDetailTypeId = whDetail[0].hasOwnProperty('legal_entity_type_id') ? whDetail[0].legal_entity_type_id : 0
                var stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                var stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0
                if (whDetailTypeId != 1001 || stWhDetailTypeId != 1001) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be APOB to transfer stock.' }));
                }
                var whDetailStateId = whDetail[0].hasOwnProperty('state') ? whDetail[0].state : 0
                var stWhDetailStateId = stWhDetail[0].hasOwnProperty('state') ? stWhDetail[0].state : 0
                if (whDetailStateId != stWhDetailStateId) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be in same state to transfer stock.' }));
                }
            } else if (stock_transfer_dc > 0) {
                res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please check Stock Transfer to transfer stock.' }));
            }
            var params = { 'po_id': po_id, 'supply_le_wh_id': le_wh_id, 'create_order': create_order }
            await Purchasedata.updatePoSupplyDc(params);
            var poDetailArr = await Purchasedata.getPoDetailById(po_id, le_id);
            var display_name = ((poDetailArr[0].dc_name) && poDetailArr[0].dc_name != "") ? (poDetailArr[0].dc_name) : '';
            res.send(encryption.encrypt({
                'status': "success",
                "message": "Supply Dc updated successfully.",
                "Data": {
                    "Display_Name": display_name,
                    "dc_id": le_wh_id,
                }
            }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

/*
Function Name: updatePoSoCode,
Input Params : required po_id,old_po_so_order_code
Result or Response : success message of updated posocode
*/

exports.updatePoSoCode = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            var old_po_so_order_code = data.hasOwnProperty('old_po_so_order_code') ? data.old_po_so_order_code : 0
            if (old_po_so_order_code !== "" && old_po_so_order_code !== 0) {
                var orderId = await Purchasedata.getOrderIdByCode(old_po_so_order_code);
                var order_data = Purchasedata.getOrderInfoById(orderId).then((result) => {
                    if (order_data.hasOwnProperty('order_status_id')) {
                        var check_status = [17009, 17015, 17022];
                        if (!check_status.includes(order_data.order_status_id))
                            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Order status should be Cancelled or Returned status!' }));
                    }
                });
            }
            var po_id = data.hasOwnProperty('po_id') ? data.po_id : 0
            Purchasedata.updatePoSoCode(po_id).then((result) => {
                res.send(encryption.encrypt({ 'status': 'success', 'message': 'PO SO Code Updated Successfully.' }));
            });
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}


/*
Function Name: updateSupplier,
Input Params : required po_id,user_id,supp_name,stock_transfer_dc,supply_le_wh_id,stock_transfer,warehouse_id
Result or Response : success message of updated supplier
*/

exports.updateSupplier = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            //let data=(req.body.data);
            data = JSON.parse(data);
            var le_id = data.hasOwnProperty('le_id') ? data.le_id : 0
            var new_supplier_id = data.hasOwnProperty('supp_name') ? data.supp_name : ''
            var supplier_id = new_supplier_id
            var po_id = data.hasOwnProperty('po_id') ? data.po_id : 0
            var stock_transfer_dc = data.hasOwnProperty('stock_transfer_dc') ? data.stock_transfer_dc : 0
            var supply_le_wh_id = data.hasOwnProperty('supply_le_wh_id') ? data.supply_le_wh_id : 0
            var stock_transfer = data.hasOwnProperty('stock_transfer') ? data.stock_transfer : ''
            var warehouse_id = data.hasOwnProperty('warehouse_id') ? data.warehouse_id : ''
            var po = 'select `id`,`po_id` from `vendor_payment_request` where `po_id` = ' + po_id + ' and `approval_status` IN(57203,57204,57218,57219,57222)';
            db.query(po, {}, function (err, rows) {
                if (Object.keys(rows).length > 0)
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please close payment requests initiated to cancel this PO' }));
            });
            var whDetail = await Purchasedata.getWarehouseById(warehouse_id);
            var supplierInfo = await Purchasedata.getLegalEntityById(supplier_id);
            var is_eb_supplier = (supplierInfo) ? supplierInfo[0].is_eb : 0;
            if (stock_transfer > 0) {
                if (!is_eb_supplier) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Ebutor Supplier to transfer stock' }));
                }
                if (stock_transfer_dc == warehouse_id) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Stock Transfer Location and Delivery Location should not same to transfer stock' }));
                }
                if (supply_le_wh_id > 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please uncheck Stock Transfer to Select DC Supply.' }));
                }
                if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Stock Transfer Location to transfer stock' }));
                }
                var whDetailTypeId = (whDetail) ? whDetail[0].legal_entity_type_id : 0
                var whlegalid = (whDetail) ? whDetail[0].legal_entity_id : 0
                var stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                var stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0
                if ([2, 21837].includes(whlegalid) || stWhDetailTypeId != 1001)
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be APOB to transfer stock.' }));

                var whDetailStateId = (whDetail) ? whDetail[0].state : 0
                var stWhDetailStateId = (stWhDetail) ? stWhDetail[0].state : 0
                if (whDetailStateId != stWhDetailStateId) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be in same state to transfer stock.' }));
                }
            } else if (stock_transfer_dc > 0) {
                res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please check Stock Transfer to transfer stock.' }));
            }
            var user_id = data.hasOwnProperty('user_id') ? data.user_id : 0
            var poDetailArr = await Purchasedata.getPoDetailById(po_id, le_id);
            warehouse_id = (poDetailArr[0].le_wh_id) ? poDetailArr[0].le_wh_id : "";
            var wh_leid = (whDetail[0].legal_entity_id) ? whDetail[0].legal_entity_id : "";
            var po_address = ((poDetailArr[0].po_address) && poDetailArr[0].po_address != "") ? JSON.parse(poDetailArr[0].po_address) : '';
            var address = {};
            address.supplier = supplierInfo[0];
            address.billing = (po_address.billing) ? po_address.billing : [];
            address.shipping = (po_address.shipping) ? po_address.shipping : [];
            var userInfo = '';
            if (is_eb_supplier) {
                var apob_data = await Purchasedata.getApobData(wh_leid);
                if (apob_data) {
                    address.supplier.email_id = (apob_data) ? apob_data[0].email_id : '';
                    address.supplier.mobile_no = (apob_data) ? apob_data[0].mobile_no : '';
                }
            } else {
                userInfo = await Purchasedata.getUserByLeId(supplier_id);
                address.supplier.email_id = (userInfo) ? userInfo[0].email_id : '';
                address.supplier.mobile_no = (userInfo) ? userInfo[0].mobile_no : '';
            }
            if (po_address == '') {
                var ship_whDetail = await Purchasedata.getBillingAddress(warehouse_id);
                var state_code = (ship_whDetail) ? ship_whDetail[0].state_code : "TS";
                var wh_state_id = (ship_whDetail) ? ship_whDetail[0].state : "";
                var wh_state_name = (ship_whDetail) ? ship_whDetail[0].state_name : "";
                var check_apob = (ship_whDetail) ? ship_whDetail[0].is_apob : 0;
                wh_leid = (ship_whDetail) ? ship_whDetail[0].legal_entity_id : 0;

                address.shipping = ship_whDetail[0];
                if (check_apob || wh_leid == 2) {
                    var billingaddr = await Purchasedata.checkGstState(wh_state_id);
                    if (billingaddr == '') {
                        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please add Ebutor billing address for the state ' + wh_state_name }));
                    }
                    address.billing = billingaddr[0];
                    if ((address.billing.gstin) && address.billing.gstin != "") {
                        address.shipping.gstin = address.billing.gstin;
                    }
                } else {
                    address.billing = await Purchasedata.getLegalEntityById(wh_leid);
                }
                if ((address.shipping.fssai) && address.shipping.fssai != "")
                    address.billing.fssai = address.shipping.fssai;
            }
            var poAddress = JSON.stringify(address);
            var params = { 'po_id': po_id, 'legal_entity_id': new_supplier_id, 'po_address': poAddress }
            await Purchasedata.updatePoSupplier(params);
            poDetailArr.forEach(async function (poDetail) {
                module.exports.subscribeProducts(new_supplier_id, poDetail.le_wh_id, poDetail.product_id);
            });
            let supplier = {};
            poDetailArr = await Purchasedata.getPoDetailById(po_id, le_id);
            po_address = ((poDetailArr[0].po_address) && poDetailArr[0].po_address != "") ? JSON.parse(poDetailArr[0].po_address) : [];
            var po_le_id = ((poDetailArr[0].legal_entity_id) && poDetailArr[0].legal_entity_id != "") ? (poDetailArr[0].legal_entity_id) : 0;
            var po_le_wh_id = ((poDetailArr[0].le_wh_id) && poDetailArr[0].le_wh_id != "") ? (poDetailArr[0].le_wh_id) : 0;
            if (po_address != "") {
                supplier = po_address.supplier;
            } else {
                whDetail = await Purchasedata.getWarehouseById(po_le_wh_id);
                supplier = await Purchasedata.getLegalEntityById(po_le_id);
                var wh_le_id = (whDetail.legal_entity_id) ? whDetail.legal_entity_id : 0;
                var is_eb_supplier = (supplier) ? supplier[0].is_eb : 0;
                if (is_eb_supplier) {
                    apob_data = await Purchasedata.getApobData(wh_le_id);
                    if (apob_data.length > 0) {
                        supplier = apob_data[0];
                    }
                } else {
                    userInfo = await Purchasedata.getUserByLeId(po_le_id);
                    supplier[0].email_id = (userInfo) ? userInfo[0].email_id : '';
                    supplier[0].mobile_no = (userInfo) ? userInfo[0].mobile_no : '';
                }
                supplier = supplier[0];
            }
            var country = (supplier.country_name) ? supplier.country_name : 'India';
            res.send(encryption.encrypt({
                'status': 'success',
                "message": "Supplier updated successfully.",
                "poId": po_id,
                "Data": {
                    "supplier_id": new_supplier_id,
                    "Name": supplier.business_legal_name,
                    "Address": `${supplier.address1} \n ${supplier.address2}, ${supplier.city}, ${supplier.state_name}, ${country},${supplier.pincode}`,
                    "Phone": supplier.mobile_no,
                    "Email": supplier.email_id,
                    "GSTIN_UIN": `${supplier.gstin != null ? supplier.gstin : ''}`,
                    "FssaiNo": `${("fssai" in supplier) && supplier.fssai != null ? supplier.fssai : ''}`,
                    "PanNo": `${("pan_number" in supplier) && supplier.pan_number != null ? supplier.pan_number : 'NA'}`,
                    "BankName": (supplier.sup_bank_name != null && supplier.sup_bank_name != 'undefined') ? supplier.sup_bank_name : 'NA',
                    "AccNo": (supplier.sup_account_no != null && supplier.sup_account_no != 'undefined') ? supplier.sup_account_no : 'NA',
                    "AccName": (supplier.sup_account_name != null && supplier.sup_account_name != 'undefined') ? supplier.sup_account_name : 'NA',
                    "IfscCode": (supplier.sup_ifsc_code != null && supplier.sup_ifsc_code != 'undefined') ? supplier.sup_ifsc_code : 'NA',
                    "State": (supplier.state_name != null && supplier.state_name != 'undefined') ? supplier.state_name : '',
                    "StateCode": (supplier.state_code != null && supplier.state_code != 'undefined') ? supplier.state_code : '',
                }

            }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}


exports.updatePOAction = async function (req, res) {
    try {

        if (Object.keys(req.body.data).length > 0) {
            var data = JSON.parse(req.body.data);

            if (data.po_products.length > 0) {
                let poId = data.po_id;
                if (poId) {
                    let productInfo = data.hasOwnProperty('po_products') ? data.po_products : [];
                    let supplier_id = data.hasOwnProperty('supplier_list') ? data.supplier_list : 0;
                    let warehouse_id = data.hasOwnProperty('warehouse_list') ? data.warehouse_list : 0;
                    let stock_transfer = data.hasOwnProperty('is_stock_transfer') ? data.is_stock_transfer : 0;
                    let supply_le_wh_id = data.hasOwnProperty('supply_le_wh_id') ? data.supply_le_wh_id : 0;
                    let stock_transfer_dc = data.hasOwnProperty('stock_transfer_dc') ? data.stock_transfer_dc : 0;
                    let po_so_order_code = data.hasOwnProperty('po_so_order_code') ? data.po_so_order_code : "";

                    let orderId = await Purchasedata.getOrderIdByCode(po_so_order_code);
                    if ((orderId) && orderId != '' && orderId > 0) {
                        let orderStatus = await Purchasedata.getOrderStatusById(orderId);
                        if (orderStatus != 17009 && orderStatus != 17015)
                            return res.send({ 'status': 'failed', 'message': 'Sorry! you cannot update,Order has been generated for this PO', 'po_id': poId });
                    }

                    if (stock_transfer == 1) {
                        if (po_so_order_code != "" && po_so_order_code != 0) {
                            res.send({ 'status': 'failed', 'message': 'PO has SO,It cannot be updated as Stock Transfer PO', 'po_id': poId });
                            return;
                        }
                        if (stock_transfer == "") {
                            res.send({ 'status': 'failed', 'message': 'Please select Stock Transfer Location!', 'po_id': poId });
                            return;
                        }
                    }
                    if (stock_transfer > 0) {
                        if (supplier_id != 24766) {
                            res.send({ 'status': 'failed', 'message': 'Please select "Ebutor Supplier" to transfer stock', 'po_id': '' });
                            return;
                        }
                        if (stock_transfer_dc == warehouse_id) {
                            res.send({ 'status': 'failed', 'message': '"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id': '' });
                            return;
                        }
                        if (supply_le_wh_id > 0) {
                            res.send({ 'status': 'failed', 'message': 'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id': '' });
                            return;
                        }
                        if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                            res.send({ 'status': 'failed', 'message': 'Please select "Stock Transfer Location" to transfer stock".', 'po_id': '' });
                            return;
                        }
                        let whDetail = await Purchasedata.getWarehouseById(warehouse_id);
                        let whDetailTypeId = whDetail[0].hasOwnProperty('legal_entity_type_id') ? whDetail[0].legal_entity_type_id : 0;
                        let stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                        let stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0;

                        if (whDetailTypeId != 1001 || stWhDetailTypeId != 1001) {
                            res.send({ 'status': 'failed', 'message': '"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id': '' });
                            return;
                        }

                        let whDetailStateId = whDetail.hasOwnProperty('state') ? whDetail.state : 0;
                        let stWhDetailStateId = stWhDetail.hasOwnProperty('state') ? stWhDetail.state : 0;

                        if (whDetailStateId != stWhDetailStateId) {
                            res.send({ 'status': 'failed', 'message': '"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id': '' });
                            return;
                        }

                        // let product_inv_ids = [];
                        // let inventoryData = [];

                    } else if (stock_transfer_dc > 0) {
                        res.send({ 'status': 'failed', 'message': 'Please check "Stock Transfer" to transfer stock.', 'po_id': '' });
                        return;
                    }

                    let product_inv_ids = [];
                    let inventoryData = [];
                    
                    let po_type = data.hasOwnProperty('po_type') ? data.po_type : 0;
                    //this
                    let packsize = data.hasOwnProperty('packsize') ? data.packsize : 0;
                    let mailMsg = '';

                    if (data.hasOwnProperty('delete_product') && data.delete_product != '') {
                        await Promise.all(data.delete_product.map(async function (delproduct_id, index) {
                            let product = await Purchasedata.getProductInfoByID(supplier_id, warehouse_id, delproduct_id).catch(err => { console.log('error') });
                            let preDeleteData = await Purchasedata.getPreUpdatePOProducts(poId, delproduct_id).catch(err => { console.log('err') });
                            let tax_per = preDeleteData.hasOwnProperty('tax_per') ? preDeleteData.tax_per : 0;

                            await Purchasedata.deletePoProducts(poId, delproduct_id).catch(err => { console.log('error') });
                            mailMsg += (product.length > 0) ? '<p><strong>' + product[0].pname + ' Deleted</strong></p>' : '';
                        }));
                    }

                    // update the po_docs. Planned approach is to first delete the existing po_doc and saving the new po_doc based on poId by Anvesh.
                    let proforma = data.hasOwnProperty('proforma') ? data.proforma : [];

                    console.log('poid', poId);
                    let dell = await Purchasedata.deletePODoc(poId); //first deleting all the poDocs 
                    if (Array.isArray(proforma) && proforma.length > 0) {
                        for (const doc of proforma) {
                            let bb = await Purchasedata.savePoDocuments(poId, doc);// saving the new docs
                        }
                    }
                    let updated_by = data.hasOwnProperty('user_id') ? data.user_id : '';
                    let legal_entity_id = 2;
                    // let updated_by = data.hasOwnProperty('update_by') ? data.update_by : 0;
                    let taxData = [];
                    if (productInfo != '') {
                        await Promise.all(productInfo.map(async function (products, index) {
                            let product = await Purchasedata.getProductInfoByID(supplier_id, warehouse_id, products.po_product_id, legal_entity_id);
                            // console.log('product',product);
                            //working
                            let po_product = {};
                            po_product.qty = products.hasOwnProperty('qty') ? products.qty : 1;
                            let pack_id = products.hasOwnProperty('packsize') ? products.packsize : '';
                            let uomPackInfo = await Purchasedata.getProductPackUOMInfoById(pack_id);
                            let uomPackinfo = uomPackInfo[0];
                            // console.log('uomPackinfo', uomPackinfo);
                            //working
                            po_product.uom = uomPackinfo.hasOwnProperty('value') ? uomPackinfo.value : 0;
                            po_product.no_of_eaches = uomPackinfo.hasOwnProperty('no_of_eaches') ? uomPackinfo.no_of_eaches : 0;
                            po_product.free_qty = products.hasOwnProperty('freeqty') ? products.freeqty : 0;
                            let free_pack_id = (products.hasOwnProperty('freepacksize') && products.hasOwnProperty('freeqty')) ? products.freepacksize : '';
                            let freeUOMPackInfo = await Purchasedata.getProductPackUOMInfoById(free_pack_id);
                            let freeUOMPackinfo = freeUOMPackInfo[0];
                            // console.log('freeUOMPackinfo', freeUOMPackinfo);
                            //working
                            po_product.free_uom = freeUOMPackinfo.hasOwnProperty('value') ? freeUOMPackinfo.value : 0;
                            po_product.free_eaches = freeUOMPackinfo.hasOwnProperty('no_of_eaches') ? freeUOMPackinfo.no_of_eaches : 0;
                            po_product.is_tax_included = products.hasOwnProperty('pretax') ? products.pretax : 0;
                            po_product.apply_discount = products.hasOwnProperty('apply_discount') ? products.apply_discount : 0;
                            po_product.discount_type = products.hasOwnProperty('discount_type') ? products.discount_type : 0;
                            po_product.discount = products.hasOwnProperty('item_discount') ? products.item_discount : 0;

                            // console.log('po_product', po_product);
                            //working
                            if (po_type == 1) {
                                po_product['unit_price'] = parseInt(0).toFixed(5);
                                po_product['price'] = parseInt(0).toFixed(5);
                                po_product['tax_name'] = '';
                                po_product['tax_per'] = parseInt(0).toFixed(5);
                                po_product['tax_amt'] = parseInt(0).toFixed(5);
                                po_product['sub_total'] = parseInt(0).toFixed(5);
                            } else {
                                // console.log(parseFloat(data.unit_price[productid]).toFixed(5));
                                po_product.unit_price = products.hasOwnProperty('unit_price') ? parseFloat(products.unit_price).toFixed(5) : 0;
                                po_product.price = products.hasOwnProperty('po_baseprice') ? parseFloat(products.po_baseprice).toFixed(5) : 0;
                                po_product.tax_name = products.hasOwnProperty('po_taxname') ? (products.po_taxname) : 0;
                                po_product.tax_per = products.hasOwnProperty('po_taxper') ? parseFloat(products.po_taxper).toFixed(5) : 0;
                                po_product.tax_amt = products.hasOwnProperty('po_taxvalue') ? parseFloat(products.po_taxvalue).toFixed(5) : 0;
                                po_product.sub_total = products.hasOwnProperty('po_totprice') ? parseFloat(products.po_totprice).toFixed(5) : 0;
                            }

                            // console.log('po_product', po_product);
                            //working

                            let productExist = await Purchasedata.checkPOProductExist(poId, products.po_product_id);
                            // console.log('productExist', productExist, poId);
                            //working
                            let preUpdatePOProducts = await Purchasedata.getPreUpdatePOProducts(poId, products.po_product_id);

                            if (productExist == 1) {
                                let flagdata = [];
                                flagdata['update_by'] = updated_by;
                                let valuesOfpoPrd = Object.values(po_product);
                                preUpdatePOProducts = Object.values(preUpdatePOProducts);
                                let changeResult = valuesOfpoPrd.filter(x => !preUpdatePOProducts.includes(x));

                                if (changeResult.length > 0 && product.length > 0) {
                                    mailMsg += "<p><strong>" + product[0].pname + " Updated</strong></p>";
                                }
                                po_product.tax_data = products.hasOwnProperty('po_taxdata') ? products.po_taxdata : 0;
                                po_product.hsn_code = products.hasOwnProperty('hsn_code') ? products.hsn_code : 0;
                                po_product.cur_elp = products.hasOwnProperty('curelpval') ? products.curelpval : 0;
                                //po_product.curelpval=data.hasOwnProperty('curelpval')?(data.curelpval.hasOwnProperty(productid)?data.curelpval[productid]:0):0;
                                // console.log('ttttttttttttt', po_product['tax_data']);
                                // console.log('po_product_updateee', po_product);
                                await Purchasedata.updatePOProducts(po_product, products.po_product_id, poId, flagdata);
                            } else {
                                // console.log('checking', productid , data.po_taxdata , data.po_taxdata[productid])
                                po_product.po_id = poId;
                                po_product.product_id = products.po_product_id;
                                po_product.mrp = product.hasOwnProperty('mrp') ? product.mrp : 0;
                                po_product.parent_id = products.hasOwnProperty('parent_id') ? products.parent_id : 0;
                                let taxData = products.hasOwnProperty('po_taxdata') ? products.po_taxdata : 0;
                                po_product.tax_data = JSON.stringify(taxData);
                                po_product.hsn_code = products.hasOwnProperty('hsn_code') ? products.hsn_code : 0;
                                // po_product.cur_elp = data.hasOwnProperty('cur_elp') ? (data.cur_elp.hasOwnProperty(productid) ? data.cur_elp[productid] : 0) : 0;
                                po_product.cur_elp = products.hasOwnProperty('curelpval') ? products.curelpval : 0;
                                // po_product.curelpval = data.hasOwnProperty('curelpval') ? (data.curelpval.hasOwnProperty(productid) ? data.curelpval[productid] : 0) : 0;

                                // console.log('po_product', po_product, po_product.uom)
                                await Purchasedata.savePoProducts(po_product);
                                mailMsg += (product.length > 0) ? '<p><strong>' + product[0].pname + ' Added</strong></p>' : '';
                            }


                            // collect all the tax percentages of individual products into array, If tax percent is 0 po shall not be updated
                            // console.log('potaxdata', po_product.tax_data);
                            let getTaxPercent = po_product.tax_data;
                            // getTaxPercent = JSON.parse(getTaxPercent);
                            getTaxPercent = (getTaxPercent);
                            if (getTaxPercent.Tax_Percentage == 0) {
                                taxData.push(getTaxPercent.Tax_Percentage);
                                return;
                            }


                            if (po_type == 2) {
                                po_product.created_by = updated_by;
                                po_product.po_id = poId;

                            }

                            if (stock_transfer > 0) {
                                let product_title = product.hasOwnProperty('pname') ? product.pname : 0;
                                let sku = product.hasOwnProperty('sku') ? product.sku : 0;
                                let availQty = await Purchasedata.checkInventory(products.po_product_id, stock_transfer_dc);
                                let poProductQty = po_product.no_of_eaches * po_product.qty;

                                if (availQty < poProductQty) {
                                    product_inv_ids.push(products.po_product_id);
                                    inventoryData.push({ 'product_name': product_title, 'avail_qty': availQty, 'po_qty': poProductQty, 'sku': sku });
                                }
                            }

                        }));

                        // if taxData array contains 0 value (tax percent), PO shall not be updated.
                        if (taxData.includes(0)) {
                            res.send({ 'status': 'failed', 'message': 'Could not find Tax Data' });
                            return
                        }
                        if (stock_transfer > 0) {
                            if (product_inv_ids.length > 0) {
                                res.send({ 'status': 'failed', "reason": "No Inventory to transfer stock!", "message": "inv_error_found", "adjust_message": "Add or Remove for No Inventory Products", 'data': $inventoryData });
                            }
                        }
                        let poArr = [];
                        poArr['updated_by'] = updated_by;
                        poArr['updated_at'] = formatted_date; // this date should be modified

                        if (data.hasOwnProperty('logistics_cost') && data.logistics_cost != '') {
                            poArr['logistics_cost'] = data.logistics_cost;
                        }

                        let payment_mode = data.hasOwnProperty('payment_mode') ? data.payment_mode : 1;
                        let paid_through = data.hasOwnProperty('paid_through') ? data.paid_through : '';
                        let accountinfo = paid_through.split('===');
                        let tlm_name = accountinfo.hasOwnProperty(0) ? accountinfo[0] : '';
                        let tlm_group = accountinfo.hasOwnProperty(1) ? accountinfo[1] : '';
                        let payment_type = data.hasOwnProperty('payment_type') ? data.payment_type : '0';
                        let payment_ref = data.hasOwnProperty('payment_ref') ? data.payment_ref : '';

                        poArr['payment_mode'] = payment_mode;
                        poArr['payment_type'] = (payment_mode == 2) ? payment_type : Number('');
                        poArr['payment_refno'] = (payment_mode == 2) ? payment_ref : '';
                        poArr['tlm_name'] = (payment_mode == 2) ? tlm_name : '';
                        poArr['tlm_group'] = (payment_mode == 2) ? tlm_group : '';
                        poArr['payment_due_date'] = moment(new Date()).format('YYYY-MM-DD 23:59:59');
                        poArr['po_remarks'] = (data.hasOwnProperty('po_remarks')) ? data.po_remarks : '';
                        poArr['apply_discount_on_bill'] = (data.hasOwnProperty('apply_discount_on_bill')) ? data.apply_discount_on_bill : 0;
                        poArr['discount_type'] = (data.hasOwnProperty('bill_discount_type')) ? data.bill_discount_type : 0;
                        poArr['discount'] = (data.hasOwnProperty('bill_discount')) ? data.bill_discount : 0;
                        poArr['discount_before_tax'] = (data.hasOwnProperty('discount_before_tax')) ? data.discount_before_tax : 0;
                        poArr['is_stock_transfer'] = (data.hasOwnProperty('is_stock_transfer')) ? data.is_stock_transfer : 0;
                        poArr['stock_transfer_dc'] = stock_transfer_dc;

                        if (data.hasOwnProperty('payment_due_date') && data.payment_due_date != '' && payment_mode == 1) {
                            poArr['payment_due_date'] = moment(data.payment_due_date).format('YYYY-MM-DD 23:59:59');
                        }

                        await Purchasedata.updatePO(poId, poArr);
                    }
                    let poCode = await Purchasedata.getPoCodeById(poId);
                    let po_code = poCode.hasOwnProperty('po_code') ? poCode.po_code : '';

                    if (mailMsg != '') {
                        let mailData = [];
                        mailData['subject'] = 'PO#' + po_code + ' Updated';
                        mailData['message'] = mailMsg;
                        await module.exports.emailWithAttachment(poId, po_code, mailData);
                        let updateStr = {};
                        updateStr.approval_status = 57106;
                        updateStr.approved_by = updated_by;
                        updateStr.approved_at = formatted_date;
                        await Purchasedata.updatePO(poId, updateStr);
                        let current_status = poCode.hasOwnProperty('approval_status') ? poCode.approval_status : '';
                        // console.log('mailMsg', mailMsg, mailData);
                        // await approval_flow_func.storeWorkFlowHistory('Purchase Order', poId, current_status, 57106, 'PO has been modified hence moving to intiated', updated_by)

                    }

                    //module.exports.poDocUpdate(poId); //this is not required as we have seperate api where we can insert file against partiular po
                    res.send({ 'status': 'success', 'message': 'PO Updated Successfully', 'po_id': poId, 'po_code': po_code });
                }
            } else {
                res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime', 'data': [] });
            }
        }
    } catch (err) {
        console.error(err);
        res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime', 'data': [] });
    }
}

/**
 * send email
 */
exports.emailWithAttachment = async (poId, po_code, mailData) => {
    try {
        let instance = process.MAIL_ENV;
        let subject = `${mailData['subject']}`;
        let body = mailData['message'];
        let email = await Purchasedata.getEmailList('PO001');

        let emailSent = await approval_flow_func.sendMail(subject, email, body);

    } catch (err) {
        console.log('emailWithAttachment error: ', err);
    }
}

/*
Function Name: uploadDocumentAction,
Input Params : required po_id,img
Result or Response : success message of updated docs and doc_id
*/

exports.uploadDocumentAction = async function (req, res) {
    try {
        if (Object.keys(req.body.po_id).length > 0) {
            let poId = req.body.po_id;
            if (typeof req.files.img != 'undefined') {
                var path = require('path');
                var ext = path.extname(req.files.img[0].originalname);
                var ext_array = ['.pdf', '.doc', '.docx', '.png', '.jpg', '.jpeg', '.jfif', '.JPG', '.PNG', '.JPEG', '.JFIF'];
                if (!ext_array.includes(ext))
                    res.send({ 'status': 'failed', 'message': 'Please upload only pdf, doc, docx, png, jpg, jpeg,jfif extensions.' });
                uploadToS3(req.files.img).then(response => {
                    // Purchasedata.savePoDocuments(poId, response).then((response1) => {
                    res.send({ 'status': 'success', 'message': 'Documents uploaded successfully', 'url': response });
                    // }).catch(err => {
                    //     console.log('db upload error');
                    //     res.send({ 'status': 'failed', 'message': 'Error in table uploading' });
                    // });
                }).catch(err => {
                    console.log('uploadtos3');
                    res.send({ 'status': 'failed', 'message': 'Error in uploadtos3' });
                });

            } else {
                res.send({ 'status': 'failed', 'message': 'Please upload image' });
            }
        } else {
            console.log('Invalid input');
            res.send({ 'status': 'failed', 'message': 'Please send valid input' });
        }
    } catch (err) {
        console.log('emailWithAttachment error: ', err);
        return res.send({ 'status': 'failed', 'message': 'Email not Sent' });// time being
    }
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

/*
Function Name: deleteDoc,
Input Params : required po_id,doc_id
Result or Response : success message of deleted docs 
*/

exports.deleteDoc = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = req.body.data;
            data = JSON.parse(data);
            var doc_id = data.hasOwnProperty('doc_id') ? data.doc_id : 0
            var po_id = data.hasOwnProperty('po_id') ? data.po_id : 0
            Purchasedata.deleteDoc(doc_id, po_id).then(response => {
                res.send({ 'status': 'success', 'message': 'Documents deleted successfully', 'data': [] });
            });
        }
    } catch (err) {
        res.send({ 'status': 'failed', 'message': 'Please Try Again after sometime', 'data': [] });
    }
}

/*
Function Name: export PO,
Input Params : required from_date,to_date,dc_id
Result or Response : json data
*/
exports.downloadPoExcel = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let d = new Date();
            let year = d.getFullYear();
            let month = '' + (d.getMonth() + 1);
            let day = d.getDate();
            let fdate = [year, month, '01'].join('-');
            let tdate = [year, month, day].join('-');
            let from_date = data.hasOwnProperty('from_date') ? data.from_date : fdate;
            let to_date = data.hasOwnProperty('to_date') ? data.to_date : tdate;
            let is_grndate = data.hasOwnProperty('grn_date') ? data.grn_date : 1;
            let dc_list = [data.dc_id];
            dc_list = dc_list.join(',');
            let supplier_list = (data.supplier_id != 0) ? [data.supplier_id] : 0;
            if (supplier_list) {
                supplier_list = supplier_list.join(',');
            } else {
                supplier_list = "NULL";
            }
            if (dc_list != 0) {

                let csvData = await Purchasedata.exportToCsv(from_date, to_date, dc_list, is_grndate, supplier_list);
                let dataArr = [];
                for (let i = 0; i < Object.keys(csvData[0]).length; i++) {
                    dataArr.push(csvData[0][i]);
                }

                /**
                 * loop to remove spaces from keys of dataArr
                 */
                let finalArr = [];
                let result = {};
                for (const data of dataArr) {
                    result = {
                        "PoNo": data['PO No'],
                        "GrnNo": data['GRN No'],
                        "SupplierName": data['Supplier Name'],
                        "SupplierCode": data['Supplier Code'],
                        "SupplierType": data['Supplier Type'],
                        "DcName": data['DC Name'],
                        "Validity": data['Validity'],
                        "PaymentMode": data['Payment Mode'],
                        "PaymentAccount": data['Payment Account'],
                        "PoCreatedBy": data['PO Created By'],
                        "PoDate": data['PO Date'],
                        "GrnCreatedBy": data['GRN Created By'],
                        "GrnDate": data['GRN Date'],
                        "ArticleNumber": data['Article Number'],
                        "HsnCode": data['HSN Code'],
                        "ProductTitle": data['Product Title'],
                        "ManufactureName": data['Manufacture Name'],
                        "Brand": data['Brand'],
                        "Category": data['Category'],
                        "Mrp": data['MRP'],
                        "ShelfLife": data['Shelf Life'],
                        "ShelfLifeUom": data['Shelf Life UOM'],
                        "MfgDate": data['MFG DATE'],
                        "ExpDate": data['EXP DATE'],
                        "PoQty_Ea": data['PO Qty(Ea)'],
                        "GrnQty_Ea": data['GRN Qty(Ea)'],
                        "Qty": data['Qty'],
                        "Uom": data['UOM'],
                        "NoOfEaches": data['No.of Eaches'],
                        "FreeQty": data['Free Qty'],
                        "FreeUom": data['Free UOM'],
                        "FreeQtyNoOfEaches": data['Free Qty No.of Eaches'],
                        "BaseRate": data['Base Rate'],
                        "SubTotal": data['Sub Total'],
                        "TaxType": data['Tax Type'],
                        "TaxPercent": data['Tax%'],
                        "TaxAmt": data['Tax Amt'],
                        "PoValue": data['PO Value'],
                        "LogisticsCost": data['Logistics Cost'],
                        "GrnValue": data['GRN Value'],
                        "LineItemDiscount": data['Line Item Discount'],
                        "PoStatus": data['PO Status'],
                        "GrnStatus": data['GRN Status']
                    }
                    finalArr.push(result);
                }
                res.send(encryption.encrypt({ 'status': 'success', 'message': 'Data found', 'data': finalArr }));

            }
            else {
                console.log("dc_list empty");
                res.send(encryption.encrypt({ 'status': 'failed', 'message': 'No Data', 'data': 'null' }));
            }
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime', 'data': 'null' }));
    }
}

exports.downloadPoReport = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let year = current_datetime.getFullYear();
            let month = '' + (current_datetime.getMonth() + 1);
            let day = current_datetime.getDate();
            let fdate = [year, month, '01'].join('-');
            let tdate = [year, month, day].join('-');
            let from_date = data.hasOwnProperty('from_date') ? data.from_date : fdate;
            let to_date = data.hasOwnProperty('to_date') ? data.to_date : tdate;
            let is_grndate = data.hasOwnProperty('grn_date') ? data.grn_date : 0;
            let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            let dc_list = [data.dc_id];
            dc_list = dc_list.join(',');
            var supplierInvoice = '';
            let finalArr = [];
            if (dc_list != 0) {
                var purchaseData = await Purchasedata.getPurchasedata(from_date, to_date, is_grndate, dc_list, user_id);
                var supplierInvoice = '';
                var discountAmount = '';
                if (purchaseData != '') {
                    for (const purchase of purchaseData) {
                        var inwardId = purchase.inward_id;
                        var grnValue = purchase.grnValue;
                        var invoiceValue = (purchase.hasOwnProperty('invoiceValue') && purchase.invoiceValue != '' && purchase.invoiceValue != null) ? purchase.invoiceValue : 0;
                        var invoice_no = (purchase.hasOwnProperty('invoice_no') && purchase.invoice_no != '' && purchase.invoice_no != null) ? purchase.invoice_no : 0;
                        discountAmount = (purchase.hasOwnProperty('discount_total') && purchase.discount_total != '' && purchase.discount_total != null) ? purchase.discount_total : 0;
                        if (purchase.SupplierInvoice) {
                            var reference_arr = purchase.SupplierInvoice.split(',');
                            if (reference_arr != '') {
                                reference_arr = reference_arr.filter(function (elem, pos) {
                                    return reference_arr.indexOf(elem) == pos;
                                });
                                supplierInvoice = (reference_arr.join(',')).trim('');
                            }
                        }
                        if (supplierInvoice == "" || supplierInvoice == 0) {
                            supplierInvoice = invoice_no;
                        }
                        purchase.SupplierInvoice = supplierInvoice;
                        purchase.twoainvoice = ((purchase.twoainvoice) && purchase.twoainvoice != "") ? purchase.twoainvoice : supplierInvoice;
                        var grnProducts = await Purchasedata.getInwardDetailById(inwardId);
                        var taxArr = [];
                        var baseAmtArr = [];
                        let gstBaseamt = 0;
                        var cgst = 0;
                        var sgst = 0;
                        var igst = 0;
                        var utgst = 0;
                        var gstbase = 0;
                        var roundAmount = 0;
                        var drTotals = 0;
                        var crTotals = 0;
                        if (grnProducts.length > 0) {
                            grnProducts.forEach(product => {
                                var taxper = product.tax_per;
                                discountAmount = discountAmount + product.discount_total;
                                if (product.tax_amount > 0) {
                                    if (taxArr.hasOwnProperty('taxper')) {
                                        taxArr[taxper] += +product.tax_amount;
                                    } else {
                                        taxArr[taxper] = product.tax_amount;
                                    }
                                }
                                if (baseAmtArr.hasOwnProperty('taxper')) {
                                    baseAmtArr[taxper] += +product.sub_total;
                                } else {
                                    taxArr[taxper] = product.tax_amount;
                                }
                            });
                        }
                        let gstTaxamt = 0;
                        let utgstPer = 50;
                        let gstPer = 50;
                        if (baseAmtArr.length > 0) {
                            gstBaseamt = Object.values(baseAmtArr).reduce((a, b) => (a + b));
                        }
                        if (taxArr.length > 0) {
                            gstTaxamt = Object.values(taxArr).reduce((a, b) => (a + b));
                        }
                        gstbase = gstBaseamt.toFixed(2);

                        if (grnProducts.hasOwnProperty('tax_name') && grnProducts.tax_name.includes('IGST')) {
                            cgst = 0;
                            sgst = 0;
                            igst = gstTaxamt.toFixed(2);
                            utgst = 0;
                        } else if (grnProducts.hasOwnProperty('tax_name') && grnProducts.tax_name.includes('UTGST')) {
                            cgst = ((gstTaxamt * utgstPer) / 100).toFixed(2);
                            sgst = 0;
                            igst = 0;
                            utgst = ((gstTaxamt * utgstPer) / 100).toFixed(2);

                        } else {
                            cgst = ((gstTaxamt * gstPer) / 100).toFixed(2);
                            sgst = ((gstTaxamt * gstPer) / 100).toFixed(2);
                            igst = 0;
                            utgst = 0;
                        }
                        crTotals = parseFloat(invoiceValue) + parseFloat(discountAmount);
                        drTotals = parseFloat(drTotals) + parseFloat(gstBaseamt) + parseFloat(gstTaxamt);
                        drTotals = ','.replace('', parseFloat(drTotals).toFixed(2));
                        crTotals = ','.replace(',', parseFloat(crTotals).toFixed(2));
                        if (drTotals > crTotals) {
                            roundAmount = parseFloat(drTotals) - parseFloat(crTotals);
                        } else {
                            roundAmount = (crTotals - drTotals);
                        }
                        purchase.discount_total = discountAmount;
                        let result = {};
                        result = {
                            "EPPurchasedate": purchase.created_at,
                            "SupplierName": purchase.business_legal_name,
                            "SupplierCode": purchase.le_code,
                            "DcName": purchase.wh_name,
                            "GSTNo": purchase.gstin,
                            "SupplierInvoice": (purchase.SupplierInvoice) ? (purchase.SupplierInvoice) : '',
                            "IIAInvoiceNo": purchase.twoainvoice,
                            "InvoiceDate": purchase.invoice_date,
                            "POCode": purchase.po_code,
                            "PoDate": purchase.po_date,
                            "POValue": purchase.poValue,
                            "GRNCode": purchase.inward_code,
                            "GrnDate": purchase.inward_date,
                            "GRNValue": purchase.grnValue,
                            "Discount": purchase.discount_total,
                            "TotalInvoiceValue": purchase.invoiceValue,
                            "GSTBaseValue": gstbase,
                            "CGST": cgst,
                            "SGST": sgst,
                            "IGST": igst,
                            "UTGST": utgst,
                            "Roundoff": roundAmount

                        }
                        finalArr.push(result);
                    };
                    res.send(encryption.encrypt({ 'status': 'success', 'message': 'Data found', 'data': finalArr }));
                } else {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'No data found' }));
                }
            }
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

exports.downloadPOHsnReport = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let year = current_datetime.getFullYear();
            let month = '' + (current_datetime.getMonth() + 1);
            let day = current_datetime.getDate();
            let fdate = [year, month, '01'].join('-');
            let tdate = [year, month, day].join('-');
            let from_date = data.hasOwnProperty('from_date') ? data.from_date : fdate;
            let to_date = data.hasOwnProperty('to_date') ? data.to_date : tdate;
            let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            let dc_list = [data.dc_id];
            dc_list = dc_list.join(',');
            var supplierInvoice = '';
            let total_data = [];
            if (dc_list != 0) {
                var purchaseData = await Purchasedata.getPurchaseHSNdata(from_date, to_date, dc_list, user_id);
                if (purchaseData != '') {
                    purchaseData.forEach(purchase => {
                        var invoice_no = purchase.invoice_no;
                        if (purchase.SupplierInvoice) {
                            var reference_arr = purchase.SupplierInvoice.split(',');
                            if (reference_arr != '') {
                                reference_arr = reference_arr.filter(function (elem, pos) {
                                    return reference_arr.indexOf(elem) == pos;
                                });
                                supplierInvoice = (reference_arr.join(',')).trim('');
                            }
                        }
                        if (supplierInvoice == "" || supplierInvoice == 0) {
                            supplierInvoice = invoice_no;
                        }
                        purchase.SupplierInvoice = supplierInvoice;
                        var gstTaxamt = 0;
                        var gstPer = 50;
                        var utgstPer = 50;
                        gstTaxamt = purchase.tax_amount;
                        if (purchase.hasOwnProperty('tax_name') && purchase.tax_name.includes('IGST')) {
                            purchase.cgst = 0;
                            purchase.sgst = 0;
                            purchase.igst = gstTaxamt;
                            purchase.utgst = 0;
                        } else if (purchase.tax_name.includes('UTGST')) {
                            purchase.cgst = ((gstTaxamt * utgstPer) / 100).toFixed(2);
                            purchase.sgst = 0;
                            purchase.igst = 0;
                            purchase.utgst = ((gstTaxamt * utgstPer) / 100).toFixed(2);

                        } else {
                            purchase.cgst = ((gstTaxamt * gstPer) / 100).toFixed(2);
                            purchase.sgst = ((gstTaxamt * gstPer) / 100).toFixed(2);
                            purchase.igst = 0;
                            purchase.utgst = 0;
                        }
                        let result = {};
                        result = {
                            "EPPurchaseDate": purchase.created_at,
                            "SupplierName": purchase.business_legal_name,
                            "DCName": purchase.wh_name,
                            "GSTNo": purchase.gstin,
                            "SupplierInvoice": purchase.SupplierInvoice,
                            "Invoicedate": purchase.invoice_date,
                            "POCode": purchase.po_code,
                            "PODate": purchase.po_date,
                            "POValue": purchase.poValue,
                            "GRNCode": purchase.inward_code,
                            "GrnDate": purchase.inward_date,
                            "GRNValue": purchase.grnValue,
                            "Discount": purchase.discount_total,
                            "HSNCode": purchase.hsn_code,
                            "GSTBaseValue": purchase.gstbase,
                            "TaxType": purchase.tax_name,
                            "Tax": purchase.tax_per,
                            "TaxAmount": purchase.tax_amount,
                            "CGST": +(purchase.cgst),
                            "SGST": +(purchase.sgst),
                            "IGST": +(purchase.igst),
                            "UTGST": +(purchase.utgst)
                        }
                        total_data.push(result);
                    });
                    res.send(encryption.encrypt({ 'status': 'success', 'message': 'Data found', 'data': total_data }));
                } else {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'No data found' }));
                }
            }
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }
    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

exports.downloadPOGSTReport = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let year = current_datetime.getFullYear();
            let month = '' + (current_datetime.getMonth() + 1);
            let day = current_datetime.getDate();
            let fdate = [year, month, '01'].join('-');
            let tdate = [year, month, day].join('-');
            let from_date = data.hasOwnProperty('from_date') ? data.from_date : fdate;
            let to_date = data.hasOwnProperty('to_date') ? data.to_date : tdate;
            let is_grndate = data.hasOwnProperty('grn_date') ? 1 : 0;
            let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            let dc_list = [data.dc_id];
            dc_list = dc_list.join(',');
            let total_data = [];
            if (dc_list != 0) {
                var purchaseData = await Purchasedata.getPurchaseGSTdata(from_date, to_date, is_grndate, dc_list, user_id);
                if (purchaseData != '') {
                    for (const purchase of purchaseData) {
                        var is_eb_supplier = await Purchasedata.checkIsEbutorSupplier(purchase.sup_legal_id);
                        if (!is_eb_supplier) {
                            let apob_data = await Purchasedata.getApobData(purchase.wh_legal_id);
                            if (apob_data.length) {
                                purchase.State = apob_data.state_name;
                            }
                        }
                        let grnProducts = await Purchasedata.getInwardDetailById(purchase.inward_id);
                        let invoiceinfo = await Purchasedata.getInvoiceByCode(purchase.SupplierInvoice);
                        if (invoiceinfo.length > 0) {
                            var invoice_date = invoiceinfo.created_at;
                        } else {
                            var invoice_date = purchase.invoice_date;
                        }
                        let taxArr = [];
                        let baseAmtArr = [];
                        if (grnProducts.length > 0) {
                            grnProducts.forEach(product => {
                                var taxper = product.tax_per;
                                var discountAmount = discountAmount + product.discount_total;
                                if (product.tax_amount > 0) {
                                    if (taxArr.hasOwnProperty('taxper')) {
                                        taxArr[taxper] += product.tax_amount;
                                    } else {
                                        taxArr[taxper] = product.tax_amount;
                                    }
                                }
                                if (baseAmtArr.hasOwnProperty('taxper')) {
                                    baseAmtArr[taxper] += +product.sub_total;
                                } else {
                                    baseAmtArr[taxper] = product.sub_total;
                                }
                            });
                            await Promise.all(baseAmtArr.map(async function (baseAmount, taxper) {
                                var tax_data = (grnProducts[0].tax_data) ? grnProducts[0].tax_data : '{}';
                                var tax_name = grnProducts[0].tax_name;
                                var gstPer = (tax_data.CGST) ? tax_data.CGST : ((tax_name == 'GST') ? 50 : 0);
                                var iGstPer = (tax_data.IGST) ? $tax_data.IGST : ((tax_name == 'IGST') ? 100 : 0);
                                var utgstPer = (tax_data.UTGST) ? tax_data.UTGST : ((tax_name == 'UTGST') ? 50 : 0);
                                var gstTaxamt = taxArr[taxper];
                                var supplierInvoice = purchase.SupplierInvoice;
                                if (purchase.SupplierInvoice) {
                                    var reference_arr = purchase.SupplierInvoice.split(',');
                                    if (reference_arr != '') {
                                        reference_arr = reference_arr.filter(function (elem, pos) {
                                            return reference_arr.indexOf(elem) == pos;
                                        });
                                        supplierInvoice = (reference_arr.join(',')).trim('');
                                    }
                                }
                                var sgst = ((gstTaxamt * gstPer) / 100).toFixed(2);
                                var utgst = ((gstTaxamt * utgstPer) / 100).toFixed(2);
                                let result = {};
                                result = {
                                    'GSTINofsupplier': purchase.gstin,
                                    'SupplierName': purchase.business_legal_name,
                                    'Invoiceumber': supplierInvoice,
                                    'IIAInvoiceNo': ((purchase.twoainvoice) && purchase.twoainvoice != "") ? purchase.twoainvoice : supplierInvoice,
                                    'InvoiceType': "R",
                                    'Invoicedate': (invoice_date) ? invoice_date : '',
                                    'InvoiceValue': purchase.grnValue,
                                    'PlaceofSupply': (purchase.State) ? purchase.State : '',
                                    'SupplyAttractReverseChange': "N",
                                    'Rate': taxper,
                                    'TaxableValue': baseAmount.toFixed(2),
                                    'IntegratedTax': +((gstTaxamt * iGstPer) / 100).toFixed(2),
                                    'CentralTax': +((gstTaxamt * utgstPer) / 100).toFixed(2),
                                    'StateTax': +((sgst) ? sgst : utgst),
                                    'Cess': 0,
                                    'CounterPartyReturnStatus': 'submitted',
                                    'POValue': purchase.poValue,
                                }
                                total_data.push(result);
                            }));
                        }
                    };
                    res.send(encryption.encrypt({ 'status': 'success', 'message': 'Data found', 'data': total_data }));
                } else {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'No data found' }));
                }
            }
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }

}

exports.getSuppliersForReport = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            //let data = (req.body.data);
            data = JSON.parse(data);
            let le_id = data.legal_entity_id;
            let supplier_list = await Purchasedata.getSupplierByLEId(le_id);
            if (supplier_list) {
                return res.send(encryption.encrypt({ 'status': 'success', 'message': 'data found', 'data': supplier_list }));
            } else {
                return res.send(encryption.encrypt({ 'status': 'success', 'message': 'No data found' }));
            }
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}
exports.updateSupplierOption = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let po_id = data.hasOwnProperty('po_id') ? data.po_id : 0;
            let stock_transfer = data.hasOwnProperty('stock_transfer') ? data.stock_transfer : ''
            let new_supplier_id = data.hasOwnProperty('supp_name') ? data.supp_name : ''
            let supplier_id = new_supplier_id
            let stock_transfer_dc = data.hasOwnProperty('stock_transfer_dc') ? data.stock_transfer_dc : 0
            let supply_le_wh_id = data.hasOwnProperty('supply_le_wh_id') ? data.supply_le_wh_id : 0
            let warehouse_id = data.hasOwnProperty('warehouse_id') ? data.warehouse_id : '';
            let supplierdata = data.supplier_data;
            let supplierInfo = {};
            supplierInfo.business_legal_name = supplierdata.business_legal_name;
            supplierInfo.legal_entity_id = supplierdata.legal_entity_id;
            supplierInfo.le_code = supplierdata.le_code;
            supplierInfo.state_id = supplierdata.state_id;
            supplierInfo.tin_number = supplierdata.tin_number;
            supplierInfo.landmark = supplierdata.landmark;
            supplierInfo.locality = supplierdata.locality;
            supplierInfo.is_eb = supplierdata.is_eb;
            supplierInfo.country_name = supplierdata.country_name;
            supplierInfo.state_name = supplierdata.state_name;
            supplierInfo.state_code = supplierdata.state_code;
            supplierInfo.le_type_id = supplierdata.le_type_id
            supplierInfo.address1 = supplierdata.address1;
            supplierInfo.address2 = supplierdata.address2;
            supplierInfo.city = supplierdata.city;
            supplierInfo.pincode = supplierdata.pincode;
            supplierInfo.mobile_no = supplierdata.phone;
            supplierInfo.email_id = supplierdata.email;
            supplierInfo.gstin = supplierdata.gstin;
            supplierInfo.sup_bank_name = supplierdata.sup_bank_name;
            supplierInfo.sup_account_no = supplierdata.sup_account_no;
            supplierInfo.sup_account_name = supplierdata.sup_account_name;
            supplierInfo.sup_ifsc_code = supplierdata.sup_ifsc_code;
            supplierInfo.pan_number = supplierdata.pan_number;
            supplierInfo.fssai = supplierdata.fssai;

            let deliverydata = data.delivery_data;
            let whDetail = {};
            whDetail.display_name = deliverydata.display_name;
            whDetail.authorized_by = deliverydata.authorized_by;
            whDetail.jurisdiction = deliverydata.jurisdiction;
            whDetail.lp_wh_name = deliverydata.lp_wh_name;
            whDetail.contact_name = deliverydata.contact_name;
            whDetail.pincode = deliverydata.pincode;
            whDetail.state = deliverydata.state;
            whDetail.le_wh_code = deliverydata.le_wh_code;
            whDetail.landmark = deliverydata.landmark;
            whDetail.address1 = deliverydata.address1;
            whDetail.address2 = deliverydata.address2;
            whDetail.country_name = deliverydata.country_name;
            whDetail.email = deliverydata.email;
            whDetail.phone_no = deliverydata.phone_no;
            whDetail.city = deliverydata.city;
            whDetail.state_name = deliverydata.state_name;
            whDetail.margin = deliverydata.margin;
            whDetail.is_apob = deliverydata.is_apob;
            whDetail.legal_entity_id = deliverydata.legal_entity_type_id;
            whDetail.state_code = deliverydata.state_code;
            whDetail.business_legal_name = deliverydata.business_legal_name;
            whDetail.gstin = deliverydata.gstin;
            whDetail.legal_entity_type_id = deliverydata.legal_entity_id;
            whDetail.fssai = deliverydata.fssai;
            whDetail.locality = deliverydata.locality;

            let billingdata = data.billing_data;
            let billingInfo = {};
            billingInfo.business_legal_name = billingdata.business_legal_name;
            billingInfo.legal_entity_id = billingdata.legal_entity_id;
            billingInfo.address1 = billingdata.address1;
            billingInfo.address2 = billingdata.address2;
            billingInfo.city = billingdata.city;
            billingInfo.le_code = billingdata.le_code;
            billingInfo.state_id = billingdata.state_id;
            billingInfo.pincode = billingdata.pincode;
            billingInfo.pan_number = billingdata.pan_number;
            billingInfo.tin_number = billingdata.tin_number;
            billingInfo.gstin = billingdata.gstin;
            billingInfo.fssai = billingdata.fssai;
            billingInfo.landmark = billingdata.landmark;
            billingInfo.locality = billingdata.locality;
            billingInfo.is_eb = billingdata.is_eb;
            billingInfo.sup_bank_name = billingdata.sup_bank_name;
            billingInfo.sup_account_no = billingdata.sup_account_no;
            billingInfo.sup_account_name = billingdata.sup_account_name;
            billingInfo.sup_ifsc_code = billingdata.sup_ifsc_code;
            billingInfo.country_name = billingdata.country_name;
            billingInfo.state_name = billingdata.state_name;
            billingInfo.state_code = billingdata.state_code;
            billingInfo.le_type_id = billingdata.le_type_id;
            billingInfo.le_is_eb = billingdata.le_is_eb;

            // var po = 'select `id`,`po_id` from `vendor_payment_request` where `po_id` = ' + po_id + ' and `approval_status` IN(57203,57204,57218,57219,57222)';
            // db.query(po, {}, function (err, rows) {
            //     if (Object.keys(rows).length > 0)
            //         res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please close payment requests initiated to cancel this PO' }));
            // });

            var is_eb_supplier = (supplierInfo.is_eb) ? supplierInfo.is_eb : 0;
            if (stock_transfer > 0) {
                if (!is_eb_supplier) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Ebutor Supplier to transfer stock' }));
                }
                if (stock_transfer_dc == warehouse_id) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Stock Transfer Location and Delivery Location should not same to transfer stock' }));
                }
                if (supply_le_wh_id > 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please uncheck Stock Transfer to Select DC Supply.' }));
                }
                if (stock_transfer_dc == "" || stock_transfer_dc == 0) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select Stock Transfer Location to transfer stock' }));
                }
                var whDetailTypeId = whDetail.hasOwnProperty('legal_entity_type_id') ? whDetail.legal_entity_type_id : 0
                var whlegalid = whDetail.hasOwnProperty('legal_entity_id') ? whDetail.legal_entity_id : 0
                var stWhDetail = await Purchasedata.getWarehouseById(stock_transfer_dc);
                var stWhDetailTypeId = stWhDetail[0].hasOwnProperty('legal_entity_type_id') ? stWhDetail[0].legal_entity_type_id : 0;
                if ([2, 21837].includes(whlegalid) || stWhDetailTypeId != 1001)
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be APOB to transfer stock.' }));

                var whDetailStateId = whDetail.hasOwnProperty('state') ? whDetail.state : 0
                var stWhDetailStateId = stWhDetail[0].hasOwnProperty('state') ? stWhDetail[0].state : 0
                if (whDetailStateId != stWhDetailStateId) {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Dispatch Location and Delivery Location should be in same state to transfer stock.' }));
                }
            } else if (stock_transfer_dc > 0) {
                res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please check Stock Transfer to transfer stock.' }));
            }
            var user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            var le_id = data.hasOwnProperty('legal_entity_id') ? data.legal_entity_id : 0
            var poDetailArr = await Purchasedata.getPoDetailById(po_id, le_id);
            var wh_leid = (whDetail.legal_entity_id) ? whDetail.legal_entity_id : "";
            var po_address = ((poDetailArr[0].po_address) && poDetailArr[0].po_address != "") ? JSON.parse(poDetailArr[0].po_address) : [];
            var address = {};
            address.supplier = supplierInfo;
            address.billing = billingInfo;
            address.shipping = whDetail;
            if (po_address == '') {
                var ship_whDetail = whDetail;
                var state_code = (ship_whDetail.state_code) ? ship_whDetail.state_code : "TS";
                var wh_state_id = (ship_whDetail.state) ? ship_whDetail.state : "";
                var wh_state_name = (ship_whDetail.state_name) ? ship_whDetail.state_name : "";
                var check_apob = (ship_whDetail.is_apob) ? ship_whDetail.is_apob : 0;
                wh_leid = (ship_whDetail.legal_entity_id) ? ship_whDetail.legal_entity_id : 0;

                address.shipping = ship_whDetail;
                if (check_apob || wh_leid == 2) {
                    var billingaddr = billingInfo;
                    if (billingaddr == '') {
                        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please add Ebutor billing address for the state ' + wh_state_name }));
                    }
                    address.billing = billingaddr;
                    if ((address.billing.gstin) && address.billing.gstin != "") {
                        address.shipping.gstin = address.billing.gstin;
                    }
                } else {
                    address.billing = billingInfo;
                }
                if ((address.shipping.fssai) && address.shipping.fssai != "")
                    address.billing.fssai = address.shipping.fssai;
            }
            let updateArr = [];
            updateArr['legal_entity_id'] = new_supplier_id;
            updateArr['po_address'] = JSON.stringify(address);
            if (warehouse_id != '')
                updateArr['le_wh_id'] = warehouse_id;
            await Purchasedata.updatePO(po_id, updateArr);
            poDetailArr.forEach(async function (poDetail) {
                module.exports.subscribeProducts(new_supplier_id, poDetail.le_wh_id, poDetail.product_id);
            });
            res.send(encryption.encrypt({ 'status': 'success', "message": "Supplier updated successfully." }));
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

exports.getSupplierData = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let supplier_id = data.hasOwnProperty('supplier_id') ? data.supplier_id : 0;
            let legal_entity_id = data.hasOwnProperty('legal_entity_id') ? data.legal_entity_id : 0;
            let user_id = data.hasOwnProperty('user_id') ? data.user_id : 0;
            if (supplier_id) {
                let supplier_list = await Purchasedata.getLegalEntityById(supplier_id);
                if (supplier_list) {
                    let supplier = supplier_list[0];
                    let userInfo = await Purchasedata.getUserByLeId(supplier_id);
                    supplier.email_id = (userInfo) ? userInfo[0].email_id : '';
                    supplier.mobile_no = (userInfo) ? userInfo[0].mobile_no : '';
                    supplier.warehouse = await Purchasedata.getWarehouseBySupplierId(user_id, legal_entity_id);
                    return res.send(encryption.encrypt({
                        'Status': "Success",
                        "Message": "Records found",
                        "data": {
                            "Name": supplier.business_legal_name,
                            "Address1": `${supplier.address1}`,
                            "Address2": `${supplier.address2}`,
                            "city": `${supplier.city}`,
                            "Pincode": `${supplier.pincode}`,
                            "Phone": (supplier != null) ? supplier.mobile_no : '',
                            "Email": (supplier != null) ? supplier.email_id : '',
                            "BankName": (supplier.sup_bank_name != null && supplier.sup_bank_name != 'undefined') ? supplier.sup_bank_name : '',
                            "AccNo": (supplier.sup_account_no != null && supplier.sup_account_no != 'undefined') ? supplier.sup_account_no : '',
                            "AccName": (supplier.sup_account_name != null && supplier.sup_account_name != 'undefined') ? supplier.sup_account_name : '',
                            "IfscCode": (supplier.sup_ifsc_code != null && supplier.sup_ifsc_code != 'undefined') ? supplier.sup_ifsc_code : '',
                            "PanNo": (supplier.pan_number != null) ? supplier.pan_number : '',
                            "GstinUin": (supplier.gstin != null && supplier.gstin != 'undefined') ? supplier.gstin : '',
                            "Fssai": (supplier.fssai != null && supplier.fssai != 'undefined') ? supplier.fssai : '',
                            "LegalEntityId": supplier.legal_entity_id,
                            "LeCode": `${supplier.le_code}`,
                            "StateId": `${supplier.state_id}`,
                            "TinNo": `${supplier.tin_number}`,
                            "Landmark": supplier.landmark,
                            "Locality": `${supplier.locality}`,
                            "IsEb": `${supplier.is_eb}`,
                            "LeIsEb": `${supplier.le_is_eb}`,
                            "StateCode": `${supplier.state_code}`,
                            "StateName": `${supplier.state_name}`,
                            "LeTypeId": `${supplier.le_type_id}`,
                            "CountryName": `${supplier.country_name}`,
                            "DeliveryList": supplier.warehouse

                        }
                    }));
                } else {
                    return res.send(encryption.encrypt({ 'status': 'failed', 'message': 'No data found' }));
                }
            } else {
                return res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please select supplier' }));
            }
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

exports.getDeliveryData = async function (req, res) {
    try {
        if (Object.keys(req.body.data).length > 0) {
            let data = encryption.decrypt(req.body.data);
            data = JSON.parse(data);
            let delivery_id = data.hasOwnProperty('delivery_id') ? data.delivery_id : 0;
            var ship_whDetail = await Purchasedata.getBillingAddress(delivery_id);
            var state_code = (ship_whDetail[0].state_code) ? ship_whDetail[0].state_code : "TS";
            var wh_state_id = (ship_whDetail[0].state) ? ship_whDetail[0].state : "";
            var wh_state_name = (ship_whDetail[0].state_name) ? ship_whDetail[0].state_name : "";
            var check_apob = (ship_whDetail[0].is_apob) ? ship_whDetail[0].is_apob : 0;
            let wh_leid = (ship_whDetail[0].legal_entity_id) ? ship_whDetail[0].legal_entity_id : 0;
            let shipping = ship_whDetail[0];
            let billing = '';
            if (check_apob || wh_leid == 2) {
                var billingaddr = await Purchasedata.checkGstState(wh_state_id);
                if (billingaddr == '') {
                    res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please add Ebutor billing address for the state ' + wh_state_name }));
                }
                billing = billingaddr[0];
                if ((billing.gstin) && billing.gstin != "")
                    shipping.gstin = billing.gstin;
            } else {
                billing = await Purchasedata.getLegalEntityById(wh_leid);
                billing = billing[0];
            }
            if ((shipping.gstin) && shipping.gstin != "")
                billing.gstin = shipping.gstin;
            return res.send(encryption.encrypt({
                'Status': "Success",
                "Message": "Records found",
                "DeliveryData": {
                    "Address1": `${shipping.address1}`,
                    "Address2": `${shipping.address2}`,
                    "City": `${shipping.city}`,
                    "Pincode": `${shipping.pincode}`,
                    "Phone": (shipping.phone_no != null) ? shipping.phone_no : '',
                    "Email": (shipping.email != null) ? shipping.email : '',
                    "ContactName": shipping.contact_name,
                    "GstinUin": (shipping.gstin != null && shipping.gstin != 'undefined') ? shipping.gstin : '',
                    "Fssai": (shipping.fssai != null && shipping.fssai != 'undefined') ? shipping.fssai : '',
                    "DisplayName": `${shipping.display_name}`,
                    "AuthorizedBy": `${shipping.authorized_by}`,
                    "Jurisdiction": `${shipping.jurisdiction}`,
                    "LpWhName": `${shipping.lp_wh_name}`,
                    "State": `${shipping.state}`,
                    "StateCode": `${shipping.state_code}`,
                    "StateName": `${shipping.state_name}`,
                    "LeWhCode": `${shipping.le_wh_code}`,
                    "Landmark": `${shipping.landmark}`,
                    "CountryName": `${shipping.country_name}`,
                    "Margin": `${shipping.margin}`,
                    "IsApob": `${shipping.is_apob}`,
                    "LegalEntityId": `${shipping.legal_entity_id}`,
                    "BusinessLegalName": `${shipping.business_legal_name}`,
                    "Locality": `${shipping.locality}`,
                    "LegalEntitytypeId": `${shipping.legal_entity_type_id}`,
                },
                "BillingData": {
                    "Address1": `${billing.address1}`,
                    "Address2": `${billing.address2}`,
                    "City": `${billing.city}`,
                    "Pincode": `${billing.pincode}`,
                    "PanNo": (billing.pan_number) ? billing.pan_number : '',
                    "TinNo": (billing.tin_number) ? billing.tin_number : '',
                    "GstinUin": (billing.gstin != null && billing.gstin != 'undefined') ? billing.gstin : '',
                    "Fssai": (billing.fssai != null && billing.fssai != 'undefined') ? billing.fssai : '',
                    "BankName": (billing.sup_bank_name != null && billing.sup_bank_name != 'undefined') ? billing.sup_bank_name : '',
                    "AccNo": (billing.sup_account_no != null && billing.sup_account_no != 'undefined') ? billing.sup_account_no : '',
                    "AccName": (billing.sup_account_name != null && billing.sup_account_name != 'undefined') ? billing.sup_account_name : '',
                    "IfscCode": (billing.sup_ifsc_code != null && billing.sup_ifsc_code != 'undefined') ? billing.sup_ifsc_code : '',
                    "LegalEntityId": `${billing.legal_entity_id}`,
                    "BusinessLegalName": `${billing.business_legal_name}`,
                    "LeCode": `${billing.le_code}`,
                    "StateId": `${billing.state_id}`,
                    "Landmark": `${billing.landmark}`,
                    "Locality": `${billing.locality}`,
                    "IsEb": `${billing.is_eb}`,
                    "LeIsEb": `${billing.le_is_eb}`,
                    "CountryName": `${billing.country_name}`,
                    "StateCode": `${billing.state_code}`,
                    "StateName": `${billing.state_name}`,
                    "LeTypeId": `${billing.le_type_id}`,
                }
            }));
        } else {
            res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Invalid input' }));
        }

    } catch (err) {
        res.send(encryption.encrypt({ 'status': 'failed', 'message': 'Please Try Again after sometime' }));
    }
}

exports.getEbutorSalesDcPurchaseReport = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);

        let fdate = data.hasOwnProperty('fdate') ? data.fdate : '';
        let tdate = data.hasOwnProperty('tdate') ? data.tdate : '';
        let sup_manf_id = data.hasOwnProperty('loc_sup_id') ? data.loc_sup_id : 0;
        // console.log("sup_manf", sup_manf_id);
        let sup_manf_Names = (sup_manf_id != 0 && sup_manf_id != []) ? sup_manf_id.toString() : null;
        let supplier_manf = data.hasOwnProperty('supplier_manf') ? data.supplier_manf : '';


        let report = await Purchasedata.getPurchaseReport(supplier_manf, sup_manf_Names, fdate, tdate);


        //  let result = '';
        //  if(supplier_manf == 1) {
        //     result = `CALL getpo_dcfc_byManufacturer('${sup_manf_Names}','${fdate}',${tdate}')`;
        //  } 
        //  else if(supplier_manf == 0) {
        //     result = `CALL getpo_apob_byManufacturer('${sup_manf_Names}','${fdate}',${tdate}')`;
        //  }
        res.send({ "status": "success", "message": "Records Found", "data": report[0]});



    } catch (err) {
        console.log("Error catch: ", err);
        res.send({ "status": "failed", "message": "No Records Found" });
    }
}

