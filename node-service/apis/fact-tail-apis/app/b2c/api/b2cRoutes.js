const express = require('express');
const router = express.Router();
const introScreens = require('../IntroScreens/controller/introScreensController');
const userAddress = require('../UserAddress/controller/userAddressController');

router.use(function (req, res, next) {
    next();
});

router.post('/saveAddress', function (req, res) {
    console.log("save Address working");
    userAddress.saveUserAddress(req, res);
});
router.post('/updateAddress', function (req, res) {
    userAddress.updateUserAddress(req, res);
});
router.post('/getAddress', function (req, res) {
    userAddress.getAllAddressOfUser(req, res);
});
router.post('/createBanner', function (req, res) {
    introScreens.createBannerScreen(req, res);
});
router.post('/getBanners', function (req, res) {
    introScreens.getbannerScreens(req, res);
});
router.get('/get-banners', function (req, res) {
    introScreens.getBannersList(req, res);
});
router.post('/updateBannerImage', function (req, res) {
    introScreens.updateBannerImage(req, res);
});
router.post('/updateBannerDetails', function (req, res) {
    introScreens.updateBannerDetails(req, res);
});
router.post('/deleteBanner', function (req, res) {
    introScreens.deleteBanner(req, res);
});

module.exports = router;