let factailController = require('../controller/factailController');
var express = require('express');
var router = express.Router();


router.use(function (req, res, next) {
     next();
});

router.route('/getHomescreenDetails').post(factailController.homescreenDetails);
module.exports = router;