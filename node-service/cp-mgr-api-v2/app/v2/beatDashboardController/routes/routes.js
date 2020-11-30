
var express = require('express');
var router = express.Router();
const beatDashboardController = require('../controller/beatDashboardController')


router.use(function (req, res, next) {
     next();
});


router.route('/storeSpoke').post(beatDashboardController.storeSpoke);//used to store spoke
router.route('/storeBeat').post(beatDashboardController.storeBeat);//used to store beat
router.route('/getAllData').post(beatDashboardController.getAllData);//used to getAllData
module.exports = router;
