var express = require('express');
var router = express.Router();
var accountController = require('../../../v2/accountModule/controller/accountController');


router.use(function (req, res, next) {
     next();
});


//route related to account controller
router.route('/profile').post(accountController.getUserProfile);//used to get customer basic info
router.route('/disableContactUser').post(accountController.DisableContactuser);//user to disable user contact
router.route('/updateProfile').post(accountController.updateProfile);//used to update customer profile info
router.route('/getShippingAddress').post(accountController.getShippingAddress);//used to get shippingaddress
router.route('/saveAddress').post(accountController.saveAddress);//used to save customer address info
router.route('/editAddress').post(accountController.editAddress);//used to edit customer address info
router.route('/getCountries').post(accountController.getStateCountries);//used to get country and state details
router.route('/confirmOtp').post(accountController.confirmMobileNumber);//used to confirm number
router.route('/otpForVerification').post(accountController.generateOtpForVerification);///used to generate otp for mobile number validation purpose only
router.route('/emailValidator').post(accountController.emailValidation);
router.route('/stateValidator').post(accountController.stateValidator);
router.route("/chatBotUser").post(accountController.chatBotApi);//used to get userdetail for chat bot
router.route("/getCustomerData").post(accountController.getCustomerDetails);

module.exports = router;




