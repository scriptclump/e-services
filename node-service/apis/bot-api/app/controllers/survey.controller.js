const db = require("../models");
const ProductSurvey = db.product_survey;
const Op = db.Sequelize.Op;

// Create and Save a new ProductSurvey
exports.create = (req, res) => {
    var product_survey = req.body.surveys;
    // Save ProductSurvey in the database
    ProductSurvey.bulkCreate(product_survey, {returning: true})
      .then(data => {
        res.send({"status": "success"});
      })
      .catch(err => {
        res.status(500).send({
          message:
            err.message || "Some error occurred while creating the Survey."
        });
    });
};
