const categoryController = require('../controller/categoryController');
var express = require('express');
var router = express.Router();


router.use(function (req, res, next) {
     next();
});


router.route('/getCategories').post(categoryController.getCategories);//used to add cart
router.route('/addReviewRating').post(categoryController.addReviewRating);//used to add user review and rating
router.route('/getMediaDesc').post(categoryController.getMediaDesc);//used to get product Description
router.route('/getProductSlabs').post(categoryController.getProductSlabs);//used to get productSlabs
router.route('/getOfflineProducts').post(categoryController.getOfflineProducts)//used to get productsdetails with parent-child relation
router.route('/getReviewSpec').post(categoryController.getReviewSpecification);//used to get Product review and specification
router.route('/getOfferProducts').post(categoryController.getOfferProducts);//used to get offer product details based on category Id  , sort_id
router.route('/getMustSkuList').post(categoryController.getMustSkuProductList);//used to get  promotion product list once retailer chechIn
router.route('/getcategorySubcategoryList').post(categoryController.getCategoryAndSubcategory);
module.exports = router;



