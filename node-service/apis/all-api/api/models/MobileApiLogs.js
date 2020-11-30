/**
 * MobileApiLogs.js
 *
 * @description :: TODO: To do CRUD operations for Mobile API logs.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {
    connection: 'someMongodbServer',
    attributes: {
    },
    insertApiLogs: function (moduleName, orderId, details, callback) {
        console.log("\nFunction (Model): insertApiLogs \nReceived parameters are: \nModule Name: " + moduleName + "\nOrder: " + orderId + "\nDetails: " + details);
        var saveRequest = {module_name: moduleName, order_id: orderId, details: details};
        MobileApiLogs.create(saveRequest).exec(function (err, result) {
            if (err) {
                console.log(err);
                return err;
            }
            console.log("\nLast insert Id:" + result.id);
            callback(result);
        });
    }
};

