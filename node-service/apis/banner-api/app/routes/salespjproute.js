const express=require('express');
const router=express.Router();
const ffmschedulepjp= require('../controllers/ffmschedulepjp');

router.post('/dcfclist',ffmschedulepjp.dcfclistbyuser);
router.post('/beatslist',ffmschedulepjp.getBeatsByUser);
router.post('/retailers',ffmschedulepjp.getRetailersByBeatId);
router.post('/ffmcheckin',ffmschedulepjp.checkIn);
router.post('/ffmcheckout',ffmschedulepjp.checkOut);
router.post('/cities',ffmschedulepjp.citiesList);
router.post('/citycoordinates',ffmschedulepjp.getLatitudeLongitudeByCityName);
module.exports=router;