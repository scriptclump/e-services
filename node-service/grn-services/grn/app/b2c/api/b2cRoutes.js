const express = require('express');
const router = express.Router();
const userAddress = require('../UserAddress/controller/userAddressController');

router.use(function (req, res, next) {
    next();
});

router.post('/saveAddress', function (req, res) {
    userAddress.saveUserAddress(req, res);
});
router.post('/updateAddress', function (req, res) {
    userAddress.updateUserAddress(req, res);
});
router.post('/getAddress', function (req, res) {
    userAddress.getAllAddressOfUser(req, res);
});


module.exports = router;