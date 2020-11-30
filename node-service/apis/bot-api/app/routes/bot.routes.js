module.exports = app => {
    const ProductSurvey = require("../controllers/survey.controller.js");
  
    var router = require("express").Router();
  
    // Create a new Survey
    router.post("/survey_products", ProductSurvey.create);

    app.use('/api/bot', router);
};