var express = require('express');
var router = express.Router();
let trackingController = require('../controller/trackingController');


router.use(function (req, res, next) {
     next();
});


router.route('/pincode').post(trackingController.CheckPincode);//used to get check weather entered pincode is in serviable region or not
module.exports = router;