const express=require('express');
const router=express.Router();
const basic = require('./api/controllers/basicController');
router.post('/basic/getBasicDetails',basic.getBasicDetails);
module.exports=router;