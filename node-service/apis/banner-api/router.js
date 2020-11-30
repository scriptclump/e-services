const express=require('express');
const router=express.router();
const bannerroute=require('./app/routes/bannerroute');
const detailroute=require('./app/routes/detailsroute');
const salespjproute=require('./app/routes/salespjproute');
router.any('/banner',bannerroute);
router.any('/details',detailroute);
router.any('/schedulepjp',salespjproute);