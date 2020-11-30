var express = require('express');
var router = express.Router();
var adminController = require('../controller/adminorderController');


router.use(function (req, res, next) {
     next();
});


router.route('/saveGeoData').post(adminController.saveGeoData);//used to order tracking details
module.exports = router;
