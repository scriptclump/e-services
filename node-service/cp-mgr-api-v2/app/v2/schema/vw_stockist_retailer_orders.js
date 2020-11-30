/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_stockist_retailer_orders', {
    Stockist_Code: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Stockist_Name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    State: {
      type: DataTypes.STRING(5000),
      allowNull: true
    },
    City: {
      type: DataTypes.STRING(5000),
      allowNull: true
    },
    Order_Date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    Total_Orders: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    TBV: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Invoiced: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Total_Invoiced: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Returns: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Total_Returned: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Cancel: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Total_Cancelled: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Delivered: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Total_Delivered: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Pending_GRN: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Pending_GRN_Value: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Collected: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Outstanding: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    Opening_Stock: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cashback_Orders: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    STATUS: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    Parent: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_stockist_retailer_orders'
  });
};
