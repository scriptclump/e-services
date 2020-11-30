module.exports = function (app) {
    app.use('/b2c/registration',require('../b2c/api/b2cRoutes'));
};
