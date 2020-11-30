var express = require('express');
var router = express.Router();
var paymentController = require('../controller/paymentController');

router.use(function (req, res, next) {
    next();
});

router.post('/make-payment', function (req, res) {
    paymentController.createTransaction(req, res);
});

router.get('/check-status/:transactionId', function (req, res) {
    paymentController.getStatus(req, res);
});

module.exports = router;