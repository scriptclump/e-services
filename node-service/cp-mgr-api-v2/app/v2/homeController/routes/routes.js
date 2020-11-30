var express = require('express');
var router = express.Router();
let homeController = require('../controller/homeController');


router.use(function (req, res, next) {
     next();
});


router.route('/versionCheck').post(homeController.getversion);//used to updateAddres 
router.route('/sorting').post(homeController.getSortingDataFilter);//used to sorrt the data based on selected filter
router.route('/fileUploadToS3').post(homeController.fileUpload);
router.route('/UnBilledSkus').post(homeController.UnBilledSkus);
module.exports = router;