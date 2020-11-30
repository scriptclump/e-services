/**
 * MobileApiLogs.js
 *
 * @description :: TODO: To do CRUD operations for Mobile API logs.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
var MongoClient = require('mongodb').MongoClient

const config = require('../../config/config.json');

module.exports = {
    insertApiLogs: function (moduleName, orderId, details, callback) {
        var host = 'mongodb://' + config['mongo_user'] + ":" + config['mongo_password'] + "@" + config['mongo_host'] + ":" + config['mongo_port'] + "/" + config['mongo_database'] + "?retryWrites=true&w=majority";
        MongoClient.connect(host, function (err, db) {
            //console.log(db);
            var MobileApiLogs = db.collection('ApiLogs');
            console.log("\nFunction (Model): insertApiLogs \nReceived parameters are: \nModule Name: " + moduleName + "\nOrder: " + orderId + "\nDetails: " + details);
            var saveRequest = { module_name: moduleName, order_id: orderId, details: details };
            MobileApiLogs.insertOne(saveRequest, function (err, result) {
                if (err) {
                    console.log(err);
                    return err;
                }
                console.log(result);
                console.log("\nLast insert Id:" + result.insertedId);
                callback(result);
            });
        });
    }
};

