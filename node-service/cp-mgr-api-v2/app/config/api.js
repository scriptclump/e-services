const cors = require('cors');// cross origin resource security
module.exports = function (app) {
    var my_list = ['http://localhost:1203', 'http://159.89.174.81:1002'];
    const Options = (req, callback) => {
        let corsOptions;
        if (my_list.indexOf(req.header('Origin')) !== -1) {
            corsOptions = { origin: true }
        } else {
            corsOptions = { origin: false }
        }
        callback(null, corsOptions)
    }


    app.use(cors(Options));
    app.use('/mobileapi/v2', require('../v2/accountModule/routes/routes'));//routes related to account controller
    app.use('/mobileapi/v2', require('../v2/ordersModule/routes/routes'));//will provide routes related to ordercontroller
    app.use('/mobileapi/v2', require('../v2/registrationsModule/routes/routes'));//will provide routes related to registrationcontroller
    app.use('/mobileapi/v2', require('../v2/cartModule/routes/routes'));//will provide routes related to cartcontroller
    app.use('/mobileapi/v2', require('../v2/adminOrderModule/routes/routes'));//will provide routes related to adminOrderController
    app.use('/mobileapi/v2', require('../v2/assignOrderModule/routes/routes'));//will provide routes related to assignOrderController
    app.use('/mobileapi/v2', require('../v2/attendanceModule/routes/routes'));//will provide routes related to attendanceController
    app.use('/mobileapi/v2', require('../v2/beatDashboardController/routes/routes'));//will provide routes related to beatDashboardController
    app.use('/mobileapi/v2', require('../v2/categoryModule/routes/route'));//will provide routes related to categoryController
    app.use('/mobileapi/v2', require('../v2/RequiredApi/routes/routes'));//will provide routes related to requiredApi controller
    app.use('/mobileapi/v2', require('../v2/TrackingController/routes/routes'));//will provide routes related to trackingController
    app.use('/mobileapi/v2', require('../v2/homeController/routes/routes'));//will provide routes related to HomeController(like -> versionCheck)
    app.use('/mobileapi/v2', require('../v2/feedbackModule/routes/routes'));//will provide routes related to feedbackController
    app.use('/mobileapi/v2', require('../v2/masterLookupModule/routes/routes'));//will provide routes related to masterLookupController;
    app.use('/mobileapi/v2', require('../v2/factail/routes/routes'));//will provide routes related to factail
};
