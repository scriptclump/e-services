const express=require('express');
const router=express.Router();
const bannerdetails= require('../controllers/bannerdetails');

router.post('/impression',bannerdetails.impression)
module.exports=router;