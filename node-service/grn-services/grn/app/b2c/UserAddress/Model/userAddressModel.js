'use strict';

const userAddressController = require('../controller/userAddressController');
const Sequelize = require('sequelize');
const database = require('../../../config/mysqldb');
const { response } = require('express');
const db = database.DB;


module.exports.saveUserData = async (data) => {
    try {

        let userId = data.user_id;
        let address1 = data.address1;
        let address2 = data.address2;
        let cityCode = data.city_code;
        let stateId = data.state_id;
        let pincode = data.pincode;
        let countryId = data.country_id;
        let isPrimaryAddress = data.is_primary_address;//only one address can be primary.

        // as the data is being saved, we consider the user to be active (i.e status = 1)
        return new Promise((resolve, reject) => {
            let query = `Insert into address_book (user_id,address1,address2,city_code,state_id,pincode,country,is_primary,status,created_by)
            VALUES(${userId},"${address1}","${address2}","${cityCode}",${stateId},${pincode},${countryId},"${isPrimaryAddress}","1",${userId});`;

            // console.log("query", query);
            db.query(query, {}, function (err, response) {
                if (err) {
                    console.log("saveCustomerData Error :", err);
                    reject(err);
                    return err;
                }
                // console.log("response", response);
                resolve(response.insertId);
            })
        })
    } catch (err) {
        console.log("saveCustomerData catchError :", err);
        return [];
    }
};

module.exports.updateUserData = async (data) => {
    try {
        return new Promise((resolve, reject) => {
            let abId = data.ab_id; //addressBookId
            let userId = data.user_id;
            let address1 = data.address1;
            let address2 = data.address2;
            let cityCode = data.city_code;
            let stateId = data.state_id;
            let pincode = data.pincode;
            let countryId = 99 //data.country_id;
            let isPrimaryAddress = data.is_primary_address == 1 ? data.is_primary_address : "0";//only one address can be primary.

            //limiting the user not to enter address greater than 250 characters length.
            if(address1.length > 250){
                reject("address1 exceeding max limit of 250 characters. ");
                return;
            };
            if(address2.length > 250){
                reject("address2 exceeding max limit of 250 characters. ");
                return;
            };

            //if the updating address is marking as Primary, the previous primary address flag shall be changed to non-primary.
            // console.log('isPrimaryAddress', isPrimaryAddress);
            let query1 = "";
            if(isPrimaryAddress) {
                // let query = `UPDATE address_book SET is_primary = "0" where user_id = ${userId};`
                // db.query(query, {}, (err, response) => {
                //     if(err) {
                //         console.log("error while changing is_primary to 0 ", err);
                //         reject("Unable to set address as Primary");
                //         return;
                //     }
                // })
                query1 = `UPDATE address_book SET is_primary = "0" where user_id = ${userId};`
            }
            let query = `UPDATE address_book SET address1 = "${address1}" , address2 = "${address2}", city_code = "${cityCode}", state_id ="${stateId}" ,
            pincode ="${pincode}", country ="${countryId}", is_primary="${isPrimaryAddress}",updated_by = "${userId}"`;

            //if user wants to delete address; 
            let deleteAddress = data.hasOwnProperty('delete_address') ? data.delete_address : 0 ;
            let current_datetime = new Date();
            let deleted_at = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
            // console.log("deleted_at", deleted_at);
            // let ms = deleted_at.replace('/\T\H/g'," ");
            // console.log("ms", ms);
            if(deleteAddress) {
                query += `,status = "0", deleted_by = ${userId}, deleted_at = "${deleted_at}"`
            }

            query += ` where ab_id = ${abId} and user_id = ${userId}`

            

            // console.log("query", query);
            db.query(query1 + query, {}, function (err, response) {
                if (err) {
                    console.log("updateCustomerData Error :", err);
                    reject("error in db query");
                    return err;
                }
                // resolving 1 just to set the query is successfully executed.
                console.log("response", response);
                console.log("responsedddd", response[1].affectedRows);
                resolve(response[1].affectedRows);
            })
        })
    } catch (err) {
        console.log("updateCustomerData catchError :", err);
        return err;
    }
};

module.exports.getUserAddress = async (userId) => {
    try{
        return new Promise ((resolve, reject) => {
            let query = `Select * from address_book where user_id = ${userId} and status = "1";`
            db.query(query, {}, async (err, response) => {
                if(err) {
                    console.log("getUserAddress Error :", err);
                    reject(err);
                    return err;
                }
                // console.log("response", response);
                resolve(response);
            })
        })

    } catch(err){
        console.log("getUserAddress catchError :", err);
        return [];
    }
}