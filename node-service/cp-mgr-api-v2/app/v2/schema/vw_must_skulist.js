/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_must_skulist', {
    Product_ID: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    Display_Name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Product_Title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    SKU: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Brand: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ManfName: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ProductLogo: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    STATUS: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    }
  }, {
    tableName: 'vw_must_skulist'
  });
};
