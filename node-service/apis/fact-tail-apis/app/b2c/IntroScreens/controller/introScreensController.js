'use strict';
const aws = require('aws-sdk');
const BUCKET_NAME = process.env.S3BucketName;
const IAM_USER_KEY = process.env.S3AccessKeyId;
const IAM_USER_SECRET = process.env.S3AecretAccessKey;
const introScreenModel = require('../model/introScreensModel');
const commonModel = require('../../commonModel');

/**
 * Description : Creating Banners 
 * author : Muzzamil 
 */
module.exports.createBannerScreen = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let userId = data.user_id;
        let bannerText = data.banner_text;
        let orderNum = data.order_position; //insert the banner screen at this position

        //banner Text cannot be more than 60 characters
        if (data.hasOwnProperty('banner_text') && bannerText.length > 59) {
            res.send({ "status": "failed", "message": "Banner Text cannot be more than 60 characters" });
        }

        // get user name from userId
        let userName = await commonModel.getUserName(userId);

        // code to check and upload the img file to s3 bucket;
        if (typeof req.files.img != 'undefined') {
            let path = require('path');
            let ext = path.extname(req.files.img[0].originalname);

            //only img files can be uploaded;
            let ext_array = ['.png', '.jpg', '.jpeg', '.jfif', '.JPG', '.PNG', '.JPEG', '.JFIF','.svg'];
            if (!ext_array.includes(ext))
                res.send({ 'status': 'failed', 'message': 'Please upload only png, jpg, jpeg,jfif extensions.' });

            // uploading img to S3 bucket;
            let url = await uploadToS3(req.files.img);
            // let url = "s";
            if (url != '') {

                //save the banner_text and img to db.
                let savedResponse = await introScreenModel.saveBannerScreen(url, bannerText, orderNum, userId, userName);
                if (savedResponse) res.send({ "status": "success", "message": "Banner details uploaded Successfully." });
                else res.send({ "status": "failed", "message": "Banner details upload unsuccessful" });
            } else {
                console.log('uploadtos3');
                res.send({ 'status': 'failed', 'message': 'Error in uploadtos3' });
                return;
            }

        } else {
            res.send({ 'status': 'failed', 'message': 'Please upload image' });
            return;
        }


        function uploadToS3(file) {
            return new Promise((resolve, reject) => {
                let s3bucket = new aws.S3({
                    accessKeyId: IAM_USER_KEY,
                    secretAccessKey: IAM_USER_SECRET,
                    Bucket: BUCKET_NAME
                });
                var params = {
                    Bucket: BUCKET_NAME,
                    Key: file[0].originalname,
                    Body: file[0].buffer
                };
                s3bucket.upload(params, function (err, data) {
                    if (err) {
                        reject(err);
                    } else {
                        return resolve(data.Location);
                    }
                });
            })
        }
    } catch (err) {
        console.log("Error in createBannerScreens :", err);
        res.send({ 'status': 'Failed', 'message': 'Creating Banner unsuccessful' });
    }
};

/**
 * Description : Get Banners. 
 * get active as well as all banners
 * author : Muzzamil
 */

module.exports.getbannerScreens = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);

        // if getBanners is 1, show only active banners else show all banners
        let getBanners = data.get_banners == 1 ? 1 : 0;
        let bannerScreenList = await introScreenModel.getBannerList(getBanners)
        if (bannerScreenList.length > 0) {
            res.send({ "status": "success", "message": "Getting Banner Screens Successfull", "data": bannerScreenList });
        } else {
            res.send({ "status": "failed", "message": "No Banner Screen records found" })
        }
    } catch (err) {
        console.log("Error in getBannerScreens :", err);
        res.send({ 'status': 'Failed', 'message': 'Getting Banner Screens unsuccessful' });
    }
};

/**
 * Description : update Banner Image
 * updating banner image seperately to avoid unnecessary image upload to s3 either in testing or in production
 * author : Muzzamil
 */

module.exports.updateBannerImage = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let isId = data.hasOwnProperty('is_id') ? data.is_id : 0;
        let bannerText = data.banner_text;

        // is_id is mandatory to run the sql queries
        if (isId == 'undefined' || isId == 0) {
            res.send({ "status": "failed", "message": "Please provide is_id in req body" });
            return;
        }



        //code to update image
        let url = '';
        if (typeof req.files.img != 'undefined') {
            let path = require('path');
            let ext = path.extname(req.files.img[0].originalname);

            //only img files can be uploaded;
            let ext_array = ['.png', '.jpg', '.jpeg', '.jfif', '.JPG', '.PNG', '.JPEG', '.JFIF','.svg'];
            if (!ext_array.includes(ext))
                res.send({ 'status': 'failed', 'message': 'Please upload only png, jpg, jpeg,jfif extensions.' });

            // uploading img to S3 bucket;
            url = await uploadToS3(req.files.img);
        }

        if (url != "" && url != 'undefined') {
            let updatedBanner = await introScreenModel.updateBanner(isId, url);
            if (updatedBanner) res.send({ "status": "success", "message": "Banner Image updated Successfully" });
            else res.send({ "status": "failed", "message": "Banner Image update unsuccessful." })
        } else {
            res.send({ "status": "failed", "message": "Please add Image to update." })
        }
        function uploadToS3(file) {
            return new Promise((resolve, reject) => {
                let s3bucket = new aws.S3({
                    accessKeyId: IAM_USER_KEY,
                    secretAccessKey: IAM_USER_SECRET,
                    Bucket: BUCKET_NAME
                });
                var params = {
                    Bucket: BUCKET_NAME,
                    Key: file[0].originalname,
                    Body: file[0].buffer
                };
                s3bucket.upload(params, function (err, data) {
                    if (err) {
                        reject(err);
                    } else {
                        return resolve(data.Location);
                    }
                });
            })
        }


    } catch (err) {
        console.log("Error in updateBannerImage :", err);
        res.send({ 'status': 'Failed', 'message': 'Updating Banner Image unsuccessful' });
    }
};

/**
 * Description : Update Banner Details like Banner order position, Enable / Disable Banner, Updating Banner Text
 * author : Muzzamil
 */
module.exports.updateBannerDetails = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        let isId = data.hasOwnProperty('is_id') ? data.is_id : 0;

        //banner Text cannot be more than 60 characters
        if (data.hasOwnProperty('banner_text') && data.banner_text.length > 59) {
            res.send({ "status": "failed", "message": "Banner Text cannot be more than 60 characters" });
        }

        // is_id is mandatory to run the sql queries
        if (isId == 'undefined' || isId == 0) {
            res.send({ "status": "failed", "message": "Please provide is_id in req body" });
            return;
        }
        let updatedDetails = await introScreenModel.updateDetails(data);
        if (updatedDetails) res.send({ "status": "success", "message": "Banner Details updated successfully." })
        else res.send({ "status": "failed", "message": "Banner Details update unsuccessful" })

    } catch (err) {
        console.log("Error in updateBannerDetails :", err);
        res.send({ 'status': 'failed', 'message': 'Update Banner Details unsuccessful' });
    }
};

module.exports.deleteBanner = async(req,res) => {
    try{
        let data = JSON.parse(req.body.data);
        let isId = data.hasOwnProperty('is_id') ? data.is_id : 0;
        
        if(Object.keys(data).length === 0) throw "Please provide valid input";

        // is_id is mandatory to run the sql queries
        if (isId == 'undefined' || isId == 0) throw "Please provide is_id in req body";

        let deleteResponse = await introScreenModel.deleteBannerFromDB(isId);
        if(deleteResponse) res.send({"status" : "success","message":"Banner Deleted Successfully"});
        else res.send({"status":"failed","message":`No Banner exists with ${isId}`})
    } catch (err) {
        console.log("Error while Deleting Banner :", err);
        res.send({ 'status': 'failed', 'message': err });
    }
};

/**
 * Description : Get Banners. 
 * get active as well as all banners
 * author : Muzzamil
 */

module.exports.getBannersList = async (req, res) => {
    try {
        // let data = JSON.parse(req.body.data);

        // if getBanners is 1, show only active banners else show all banners
        // let getBanners = data.get_banners == 1 ? 1 : 0;
        let bannerScreenList = await introScreenModel.getAllBannerList()
        if (bannerScreenList.length > 0) {
            res.send({ "status": "success", "message": "Getting Banner Screens Successfull", "data": bannerScreenList });
        } else {
            res.send({ "status": "failed", "message": "No Banner Screen records found" })
        }
    } catch (err) {
        console.log("Error in getBannerScreens :", err);
        res.send({ 'status': 'Failed', 'message': 'Getting Banner Screens unsuccessful' });
    }
};