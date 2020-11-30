/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_stockist_orders', {
    Le_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Stockist_Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Stockist_Name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    Order_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order_Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Invoice_Code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    Invoice_Date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order_Total: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    Invoice_Total: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    Return_Total: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Delivered_Total: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_stockist_orders'
  });
};
