const express = require('express');
const router = express.Router();

const promotiondetails = require('../controllers/promotions');

router.post('/promotion/getsuggestion',promotiondetails.getSuggestion);

const cartdetails = require('../controllers/cartcontroller');

router.post('/cart/savefeedbackreasons',cartdetails.saveFeedbackReasons);
router.post('/cart/checkin',cartdetails.checkin);
router.post('/cart/savecartdata',cartdetails.saveCartData);
router.post('/cart/getcartdata',cartdetails.getCartData);
router.post('/cart/deletecartdata',cartdetails.deleteCartData);
router.post('/cart/recommended-products',cartdetails.getRecommendedProducts);
module.exports = router;