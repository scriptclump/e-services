const express=require('express');
const router=express.Router();
const bannercontroller= require('../controllers/bannerconfigcontroller');

router.get('/', bannercontroller.index);
router.post('/getcategories',bannercontroller.getCategories);
router.post('/getfeaturedproducts',bannercontroller.featureProducts);
router.post('/getofflineproducts',bannercontroller.getOfflineProducts);
router.post('/addbanner',bannercontroller.addBanner);
router.post('/s3upload',bannercontroller.fileUploadToS3);
router.post('/redis',bannercontroller.getRedis);

module.exports=router;