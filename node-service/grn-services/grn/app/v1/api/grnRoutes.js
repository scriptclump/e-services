var express = require('express');
var router = express.Router();
var grnController = require('../controller/grnController');

router.use(function (req, res, next) {
    next();
});

router.post('/index', function (req, res) {
    grnController.index(req, res);
});

router.post('/grn-list', function (req, res) {
    grnController.grnList(req, res);
});

router.post('/grncounts', function (req, res) {
    grnController.getGrnCounts(req, res);
});
router.post('/getgrnrecords', function (req, res) {
    grnController.getGrnRecords(req, res);
});
router.post('/grncreate', function (req, res) {
    grnController.grnCreate(req, res);
});
router.post('/getsuppliers', function (req, res) {
    grnController.getSuppliers(req, res);
});
router.post('/getgrnproducts', function (req, res) {
    grnController.getGRNproducts(req, res);
});
router.post('/storegrndata', function (req, res) {
    grnController.storeGRNData(req, res);
});
router.post('/getSkus', function (req,res){
    grnController.getSkus(req,res);
});
router.post('/getPackText', function (req,res){
    grnController.getPackText(req,res);
});
router.get('/getGrnAction', function (req,res){
    grnController.getGrnAction(req,res);
});
router.post('/grndetails', function (req,res){
    grnController.detailsAction(req,res);
});
router.post('/commentsList', (req,res) => {
    grnController.getCommentsList(req,res);
});
router.post('/addcomments', (req,res) => {
    grnController.addCommentAction(req,res);
});
router.post('/uploadDocument',function (req, res) {
    grnController.uploadDocument(req,res);
});
router.post('/deleteDocument',function (req, res) {
    grnController.deleteDocument(req,res);
});
router.post('/saveReference',function (req, res) {
    grnController.saveReferenceNo(req,res);
});
router.post('/download-excel',function (req, res) {
    grnController.downloadExcel(req,res);
});
router.post('/po-history',function (req, res) {
    grnController.poHistory(req,res);
});

router.post('/saveReturn', (req,res) => {
    grnController.saveReturn(req,res);
});
router.post('/returnsList', (req,res) => {
    grnController.returnsList(req,res);
});
router.post('/savereferenceno', (req,res) => {
    grnController.SaveReferenceNo(req,res);
});
router.post('/createPOInvoice', (req,res) => {
    grnController.createInvoiceByinwardId(req,res);
});
// router.post('/fulldeliver', function (req,res){
//    grnController.fullDeliver(req,res);
// });
router.post('/getpackstatus', function (req,res){
   grnController.getpackstatus(req,res);
});

module.exports = router;
