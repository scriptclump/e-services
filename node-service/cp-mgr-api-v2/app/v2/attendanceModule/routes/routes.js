


var express = require('express');
var router = express.Router();
var AttendanceController = require('../../attendanceModule/controller/attendanceController');

router.use(function (req, res, next) {
     next();
});



//router related to order controller
router.route('/getHubUser').post(AttendanceController.getHubUsers)//used to get hub users
router.route('/saveAttendance').post(AttendanceController.saveAttendance);//user to save attendance
router.route('/getvehicleidsbyuserid').post(AttendanceController.getVehicleIdsByUserId);//user to get vehicle id 
router.route('/savevehicleattendance').post(AttendanceController.saveVehicleAttendance);//user to save vehicle attendance
router.route('/savetemporaryvehicle').post(AttendanceController.saveTemporaryVehicle);//user to save temporary attendance

module.exports = router;


