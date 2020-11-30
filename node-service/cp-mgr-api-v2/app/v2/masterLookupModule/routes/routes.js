const masterLookupController = require('../controller/masterLookupController');
var express = require('express');
var router = express.Router();

router.use(function (req, res, next) {
    next();
});

router.route('/getCashbackHistory').post(masterLookupController.getCashbackHistory);
router.route('/getFFByHub').post(masterLookupController.getFFByHub);
router.route('/getCustomerFeedback').get(masterLookupController.getCustomerfeedback);

module.exports = router;