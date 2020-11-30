/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('OrderQtyVsInventoryOrders', {
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Total Open Order Qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Inv. Order Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product Title: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    SKU: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Warehouse ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'OrderQtyVsInventoryOrders'
  });
};
