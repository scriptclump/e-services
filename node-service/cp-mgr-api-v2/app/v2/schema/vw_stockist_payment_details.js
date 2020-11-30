/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_stockist_payment_details', {
    cust_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Total_Invoice_Value: {
      type: DataTypes.STRING(86),
      allowNull: true
    },
    Return_Total_Value: {
      type: DataTypes.STRING(62),
      allowNull: false,
      defaultValue: ''
    },
    Delivered_Total_Value: {
      type: DataTypes.STRING(62),
      allowNull: true
    },
    Unsettled: {
      type: DataTypes.STRING(83),
      allowNull: false,
      defaultValue: ''
    },
    Actual_Outstanding: {
      type: DataTypes.STRING(62),
      allowNull: true
    },
    Outstanding: {
      type: DataTypes.STRING(62),
      allowNull: false,
      defaultValue: ''
    },
    Total_Payment_value: {
      type: DataTypes.STRING(79),
      allowNull: true
    },
    Order_Limit: {
      type: "DOUBLE(22,2)",
      allowNull: true
    },
    Stock Value: {
      type: DataTypes.STRING(58),
      allowNull: false,
      defaultValue: ''
    },
    credit_limit_check: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true,
      defaultValue: '1'
    }
  }, {
    tableName: 'vw_stockist_payment_details'
  });
};
