var express = require('express');
var router = express.Router();
var cartController = require('../../cartModule/controller/cartController')


router.use(function (req, res, next) {
     next();
});


router.route('/addCart').post(cartController.addCart);//used to add cart
router.route('/checkCartInventory').post(cartController.CheckCartInventory);//used to check cart inventory(review)
router.route('/deleteCart').post(cartController.deleteCart)//used to delete cart details
router.route('/getcartdetails').post(cartController.veiwCart);//used to get cart details
router.route('/cartcount').post(cartController.cartCount)//used to count the products in cart
router.route('/updateBeat').post(cartController.updateBeat);//used to update beat details

module.exports = router;




