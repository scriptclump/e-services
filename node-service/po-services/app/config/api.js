module.exports = function (app) {
    app.use('/eps/v1', require('../v1/api/payment'));
    app.use('/eps/authenticate', require('../v1/api/payment'));
    app.use('/eps/po',require('../v1/api/purchaseorder'));
};
