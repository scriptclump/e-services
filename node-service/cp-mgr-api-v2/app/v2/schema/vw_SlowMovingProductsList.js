/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_SlowMovingProductsList', {
    Order_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product_Title: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    FC_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    FC_Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ELP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_SlowMovingProductsList'
  });
};
