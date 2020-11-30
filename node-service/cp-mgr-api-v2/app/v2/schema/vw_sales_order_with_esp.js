/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_sales_order_with_esp', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    Mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Unit Price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Created At: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_sales_order_with_esp'
  });
};
