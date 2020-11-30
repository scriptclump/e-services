const express = require('express');
const router = express.Router();
const search = require('../controllers/searchcontroller');
const home = require('../controllers/homecontroller');
const picking = require('../controllers/pickingcontroller');
const models = require('../tables/index');
const sequelize = require('sequelize');
const report = require('../controllers/reportcontroller');
const userController = require('../controllers/userProfile.controller');
const cpController = require('../controllers/cpRegistration.controller');


const tokenCheck = (req, res, next) => {
	if (req.body.data) {
		let input = JSON.parse(req.body.data);
		let token = input.hasOwnProperty('customer_token') ? input['customer_token'] : '';
		if (token != '') {
			models.sequelize.query(`select verifyToken(?) AS users_count`, { replacements: [token], type: sequelize.QueryTypes.SELECT }).then(data => {
				console.log(data[0]);
				if (data[0].users_count > 0) {
					next();
				} else {
					res.send({ 'status': 'failed', 'message': 'You are already logged into system!!', 'data': [] });
				}
			});
		} else {
			res.send({ 'status': 'failed', 'message': 'Please send token', 'data': [] });
		}
	} else {
		res.send({ 'status': 'failed', 'message': 'Please send token', 'data': [] });
	}
};

router.post('/autosearch', tokenCheck, search.getSearchAjax);
router.post('/getbeatsbyffid', home.getBeatsByff);// using in routes page to all the beats based on ff_id
router.post('/getassignedverificationlist', picking.getAssignedVerificationList);
router.post('/getpendingverification', tokenCheck, picking.getPendingVerification);
router.post('/getrtdordersdata', picking.getRtdOrdersData);
router.post('/getcategorylist', home.getCategoriesList);
router.post('/getReportDetails', report.getReportDetails);
router.route('/getIconData').get(userController.iconData) // used to get all icon data based on id,s
router.route('/getCountries').post(userController.getStateCountries) //used to get countries and states details 
router.route('/getbeats').post(userController.getFFBeat) //used to get Beat details 
router.route('/profile').post(userController.getUserProfile)//used to get user basic details
router.route('/getPincodeData').post(userController.getPincodeData);//user to get area pincode 
router.route('/updateprofile').post(userController.updateProfile)//used to update user profile 
router.route('/fileUploadToS3').post(userController.fileUpload)//profile picture upload
router.route('/updategeo').post(userController.UpdateGeo)//used to update geo
router.route('/registration').post(cpController.registration)//cp registration api 
router.post('/getReportDetails', report.getReportDetails);
router.route('/getBeatsByPincode').post(userController.getFFBeatByPincode)//used to get Beat details based on ff pincode , sales_token  , ff_id
router.route('/getffonmaps').post(userController.getffmaps)//based on latitude , longitude , and pincode it return nearBy beats at the time of self registration
router.route('/getPincodeList').post(userController.getFFPincodeList)//used to get all pincode list based on ff_id and  enter day
router.route('/getffPincodeList').post(userController.getffPincodeList);//used to get ff warehouse pincode list
router.route('/sendemailtoff').post(cpController.sendEmailtoFF);//used to send an email to the ff when any self registration happens
router.route('/mobileNumberValidation').post(cpController.MobileNoValidation);//used to validate enter telephone number pupose
router.route('/insertFlatTable').post(cpController.insertFlatTabele);///used to insert missing record from flat table

module.exports = router;