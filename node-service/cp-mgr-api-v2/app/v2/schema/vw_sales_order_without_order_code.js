/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_sales_order_without_order_code', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Status ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '17002'
    },
    Order Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Warehouse ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Shop Name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    Created By: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_sales_order_without_order_code'
  });
};
