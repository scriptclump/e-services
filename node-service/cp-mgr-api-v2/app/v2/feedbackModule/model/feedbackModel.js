var sequelize = require('../../../config/sequelize');//sequlize connection file
const Sequelize = require('sequelize');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const reviewRating = mongoose.model('appReviewRating');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;

module.exports.getFeedbackReasons = (feedback_groupid) => {
    return new Promise((resolve, reject) => {
        try {

            let query = "select master_lookup_name as name, value from master_lookup where parent_lookup_id = $1";

            sequelize.query(query, { bind: [feedback_groupid], type: Sequelize.QueryTypes.SELECT }).then(response => {
                let result = JSON.parse(JSON.stringify(response));
                // console.log("this is from model", result);
                resolve(result);

            }).catch(err => {
                console.log(err);
                reject(err);
            })
        } catch (err) {
            console.log(err);
            reject(err);
        }
    })
}

module.exports.checkSalesToken = function (customer_token) {
    return new Promise((resolve, reject) => {
        let string = JSON.stringify(customer_token);
        let count = 0;
        user.countDocuments({ password_token: customer_token }, function (err, response) {
            if (err) {
                console.log(err);
                reject(err);
            } else if (response > 0) {
                resolve(response)
            } else {
                resolve(count);

            }
        })


        // let data = "select count(user_id) as counts FROM users WHERE password_token =" + string;
        // db.query(data, {}, function (err, rows) {
        //     if (err) {
        //         return reject(err);
        //     }
        //     if (Object.keys(rows).length > 0) {
        //         return resolve(rows[0].counts);
        //     }
        //     else {
        //         return reject("No mapping found..")
        //     }
        //     // db.release()
        // });
    });

    // return new Promise((resolve, reject) => {
    //     try {
    //         let salesToken = JSON.stringify(token);
    //         let query = "select verifyToken(" + salesToken + ") as count";
    //         sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
    //             let result = JSON.parse(JSON.stringify(rows));
    //             resolve(result[0].count);
    //         }).catch(err => {
    //             console.log(err);
    //             reject(err);
    //         })
    //     } catch (err) {
    //         console.log(err);
    //         reject(err);
    //     }
    // })
}

//used to store orders review and rating 
module.exports.storeReviewRating = (userId, Review, Rating = 0) => {
    return new Promise((resolve, reject) => {
        try {
            let status = 1;
            let query = "insert into app_review_rating(user_id, review, rating, STATUS,created_by)values(" + userId + "," + Review + "," + Rating + "," + status + "," + userId + ")";
            sequelize.query(query).then(response => {
                if (response) {
                    resolve(response);
                } else {
                    resolve('');
                }
            }).catch(err => {
                console.log(err);
                reject(err);
            })
        } catch (err) {
            console.log(err);
            reject(err)
        }
    })
}

//Used to check weather user have already rated or not.
module.exports.getReviewRating = (userId) => {
    return new Promise((resolve, reject) => {
        try {
            let count = 0;
            let query = "select count(*) as COUNT from app_review_rating where user_id = $1";
            sequelize.query(query, { bind: [userId], type: Sequelize.QueryTypes.SELECT }).then(response => {
                if (response[0].COUNT > 0) {
                    resolve(response[0].COUNT)
                } else {
                    resolve(count);
                }
            }).catch(err => {
                console.log(err);
                reject(err);
            })
        } catch (err) {
            console.log(err);
            reject(err);
        }
    })

}