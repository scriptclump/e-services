module.exports = (sequelize, Sequelize) => {
    const ProductSurvey = sequelize.define("product_survey", {
      ps_id: {
        type: Sequelize.INTEGER,
        primaryKey: true
      },
      retailer_id: {
        type: Sequelize.INTEGER
      },
      category: {
        type: Sequelize.STRING
      },
      qty: {
        type: Sequelize.INTEGER
      },
      unit: {
        type: Sequelize.STRING
      }
    });  
    return ProductSurvey;
};