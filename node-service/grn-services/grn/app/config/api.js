module.exports = function (app) {
    app.use('/egs/grn',require('../v1/api/grnRoutes'));
};
