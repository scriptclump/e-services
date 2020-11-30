var express = require('express');
var router = express.Router();
var assignOrderController = require('../controller/assignOrderController');


router.use(function (req, res, next) {
     next();
});

router.route('/getPendingCollectionDate').post(assignOrderController.getPendingCollectionDate);//used to get pending collection details
module.exports = router;
