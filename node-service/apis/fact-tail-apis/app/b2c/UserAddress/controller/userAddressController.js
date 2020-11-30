'use strict';
const userAddressModel = require('../Model/userAddressModel');


/**
 * Description : view the addresses of the user.
 * req body : {}
 * author : Muzzmail
 * 
 */
module.exports.getAllAddressOfUser = async (req,res) => {
    try{
        let data = JSON.parse(req.body.data);
        let userId = data.user_id;
        
        //get all the addresses of the User
        let getAddressOfUser = await userAddressModel.getUserAddress(userId);
        if(getAddressOfUser.length > 0){
            res.send({"status":"success", "message":"Address Details fetched", "data":getAddressOfUser});
        } else {
            res.send({"status":"failed", "message":"Address fetching failed"});
        }
    } catch(err){
        console.log("Error in getAllAddressOfUser :", err);
        res.send({ 'status': 'Failed', 'message': 'Address fetching unsuccessful' });
    }
}

/**
 * Description : save address of the user.
 * req body : {}
 * author : Muzzamil
 * 
 */
module.exports.saveUserAddress = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);

        //saving details of the user;
        let savedData = await userAddressModel.saveUserData(data);
        if (savedData) res.send({ "status": "success", "message": "Address added successfully", "data": `Address added at ab_id ${savedData}` });
        else res.send({ "status": "failed", "message": "Not a Registered Address." });

    } catch (err) {
        console.log("Error in saveUserAddress :", err);
        res.send({ 'status': 'failed', 'message': 'Saving Address unsuccessful' });
    }
};

/**
 * description : update User address details also SOFT DELETE if delete_address parameter is passed.
 * (here delete is to update the status, deleted_at and deleted_by columns in the table.
 *  Initially there is no HARD DELETE functionality planned)
 * req.body:{}
 * author : Muzzamil
 */

module.exports.updateUserAddress = async (req, res) => {
    try {
        let data = JSON.parse(req.body.data);
        // console.log(data);

        let updatedData = await userAddressModel.updateUserData(data);
        if (updatedData > 0) res.send({ "status": "success", "message": "Address successfully updated" });
        else res.send({ "status": "failed", "message": err });

    } catch (err) {
        console.log("Error in updateUserAddress :", err);
        res.send({ 'status': 'failed', 'message': "Updating Address unsuccessful. Please try again!" });
    }
}