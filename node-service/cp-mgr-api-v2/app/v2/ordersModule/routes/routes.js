var express = require('express');
var router = express.Router();
var ordersController = require('../../ordersModule/controller/ordersController');

router.use(function (req, res, next) {
     next();
});



//router related to order controller
router.route('/createOrder').post(ordersController.createOrder)//used to create new orders
router.route('/getMyOrders').post(ordersController.getOrders);//user to get customer orders
router.route('/cancelOrders').post(ordersController.cancelOrder);//used to cancel customer orders
router.route('/orderDetails').post(ordersController.Orderdetails);///user to get order detail
router.route('/getReturnReasons').post(ordersController.returnReasons)//used to get return reason in dropdown while returning
router.route('/getCancelReasons').post(ordersController.cancelReasons);//used to get cancel order reasons in dropdown while cancelling 
router.route('/generateOtpOrder').post(ordersController.generateOtpOrder);//used to generate otp
router.route('/orderOtpConfirmation').post(ordersController.orderOtpConfirmation);//used to confirm order otp
router.route('/getFilterOrderStatus').post(ordersController.getFilterOrderStatus);///used to order status based lookup value
router.route('/generateOrderRef').post(ordersController.genarateOrderRef)//used to get orderRef code
router.route('/salesOrderReturn').post(ordersController.returnOrderApi);//used to return orders
router.route('/cancelReturnRequest').post(ordersController.cancelReturnRequest);//used ot cancel return request
router.route("/getReturnRequest").post(ordersController.getReturnRequest);
router.route("/returnRequestProduct").post(ordersController.returnRequestProductDetails);
router.route("/savePickupStatus").post(ordersController.storePickedQuantityDetails);
module.exports = router;




