var express = require('express');
var router = express.Router();
var purchaseorderController = require('../controller/purchaseorderController');
var auth = require('../middleware/auth');


router.post('/po-count', (req,res) => {
	purchaseorderController.POCount(req,res);
});
router.post('/po-list', (req,res) => {
	purchaseorderController.POList(req,res);
});
router.post('/po-details', (req,res) => {
	purchaseorderController.poDetails(req,res);
});
router.post('/approval-history', (req,res) => {
	purchaseorderController.approvalHistory(req,res);
})
router.post('/edit-po', (req,res) => {
	purchaseorderController.edit(req,res);
})
router.post('/po-invoice', (req,res) => {
	purchaseorderController.invoiceList(req,res);
})
router.post('/payments-list',function (req, res) {
	purchaseorderController.paymentsList(req,res);
});
router.post('/create', function (req, res) {
	purchaseorderController.createPOAction(req,res);
});
router.post('/getdata', function (req,res) {
	purchaseorderController.getdata(req, res);
});

router.post('/supplierlist',function (req, res) {
	purchaseorderController.getsupplierslist(req,res);
});

router.post('/indentlist',function (req, res) {
	purchaseorderController.getindentlist(req,res);
});

router.post('/warehouselist',function (req, res) {
	purchaseorderController.getwarehouselist(req,res);
});

router.post('/getsuppliersdata',function (req, res) {
	purchaseorderController.getSuppliersAction(req,res);
});

router.post('/getSkus',function (req, res) {
	purchaseorderController.getSkus(req,res);
});

router.post('/getProductInfo',function (req, res) {
	purchaseorderController.getProductInfo(req,res);
});

router.post('/savepo',function (req, res) {
	purchaseorderController.savePurchaseOrderAction(req,res);
});

// router.post('/editpo/:poId',function (req, res) {
// 	console.log("request",req.body);
// 	purchaseorderController.editAction(req,res);
// });
router.post('/printPo', function(req,res){
    purchaseorderController.printPoAction(req,res);
});

router.post('/updatestdc',function(req,res){
	purchaseorderController.updateStDC(req,res);
});

router.post('/updatesuppydc',function(req,res){
	purchaseorderController.updateSupplyDC(req,res);
});

router.post('/po_so_code_update',function(req,res){
	purchaseorderController.updatePoSoCode(req,res);
});

router.post('/updatesupplier',function(req,res){
	purchaseorderController.updateSupplier(req,res);
});
router.post('/update-po',function(req,res){
	purchaseorderController.updatePOAction(req,res);
});
router.post('/approvalsubmit',function(req,res){
	purchaseorderController.approvalSubmit(req,res);
});

router.post('/approvalsubmit',function(req,res){
	purchaseorderController.approvalSubmit(req,res);
});

router.post('/updatepodocs',function(req,res){
	purchaseorderController.uploadDocumentAction(req,res);
});

router.post('/deletepodocs',function(req,res){
	purchaseorderController.deleteDoc(req,res);
});
router.post('/downloadPoExcel',function(req,res){
	purchaseorderController.downloadPoExcel(req,res);
});
router.post('/downloadPo', function(req,res){
    purchaseorderController.downloadPoAction(req,res);
});

router.post('/downloadPoReport',function(req,res){
	purchaseorderController.downloadPoReport(req,res);
});

router.post('/downloadPOHsnReport',function(req,res){
	purchaseorderController.downloadPOHsnReport(req,res);
});

router.post('/downloadPOGstReport',function(req,res){
	purchaseorderController.downloadPOGSTReport(req,res);
});

router.post('/getsuppliersforreport',function(req,res){
	purchaseorderController.getSuppliersForReport(req,res);
});

router.post('/updatesupplierindetails',function(req,res){
	purchaseorderController.updateSupplierOption(req,res);
});

router.post('/getsupplieronchangedata',function(req,res){
	purchaseorderController.getSupplierData(req,res);
});

router.post('/getdeliveryonchangedata',function(req,res){
	purchaseorderController.getDeliveryData(req,res);
});

router.post('/salesreport',function(req,res){
	purchaseorderController.getEbutorSalesDcPurchaseReport(req,res); 
})

module.exports =router;