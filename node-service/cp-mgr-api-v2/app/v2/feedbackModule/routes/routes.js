const feedbackController = require('../controller/feedbackController');
var express = require('express');
var router = express.Router();

router.use(function (req, res, next) {
    next();
});

router.route('/getFeedbackReasons').post(feedbackController.getFeedbackReasons);
router.route('/AppReviewRating').post(feedbackController.appReviewRating)

module.exports = router;