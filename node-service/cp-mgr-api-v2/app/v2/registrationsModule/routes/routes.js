var express = require('express');
var router = express.Router();
var registrationApiController = require('../../registrationsModule/controller/registrationApiController');


router.use(function (req, res, next) {
     next();
});


//routes related to registration controller
router.route('/registration').post(registrationApiController.registration);//cp registration api sample
router.route('/address').post(registrationApiController.address);//used to update customer address details
router.route('/registrationController').post(registrationApiController.generate_Appid);//used to generate appId
router.route('/retailers').post(registrationApiController.getAllCustomers);//used to get all customer details
router.route('/retailertoken').post(registrationApiController.generateRetailerToken);//used to generate retailer token
router.route('/getotp').post(registrationApiController.getOtp);//used to get generated otp
router.route('/updateRetailerData').post(registrationApiController.updateRetailerData);//used to update retailer data
router.route('/ffcomments').post(registrationApiController.InsertFfComments);//used to insert ff comment
router.route('/API/getdropdowndcs').all(registrationApiController.getAllDcByuserId);//used to get all dc based on userid
router.route('/API/stockistdropdown').all(registrationApiController.getAllStockists);//used to get all stock list
router.route('/getbrandsmanufacturerproductGroup').all(registrationApiController.getBrandsManufacturerProductGroupByUser);//used to get brand manufacture product
router.route('/otpConfirm').post(registrationApiController.confirmOtp);//used to confirm entered otp
router.route('/sendOtp').post(registrationApiController.sendOtp);//used to send generated otp to the end users
router.route('/resendOtp').post(registrationApiController.resendOtp);//used to resend the generated otp
router.route('/loadtestApi').get(registrationApiController.Loadtest);//api for mongodb loadtesting
module.exports = router;




