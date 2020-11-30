
var express = require('express');
var router = express.Router();
let requireController = require('../controller/requiredApiController');


router.use(function (req, res, next) {
     next();
});


router.route('/updateAddress').post(requireController.updateAddress);//used to updateAddres in payment page at the time of order placing
router.route('/saveFeedbackReasons').post(requireController.saveFeedbackReasons);///used to save feedback reasons
router.route('/saveBrandFeedback').post(requireController.saveBrandFeedback);//used to save brands feedback
router.route('/mobileFeatures').post(requireController.getFeature);//used to get user features.
router.route('/encryptionOrDecryption').post(requireController.encryptOrDecrypt);//used to encrypt or decrypt the input parameter
router.route('/getBeatInfo').post(requireController.getBeatDetails);//used to get Beat ,Warehouse info based on pincode , lat, long.
module.exports = router;