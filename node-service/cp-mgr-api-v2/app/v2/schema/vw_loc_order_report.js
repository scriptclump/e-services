/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_loc_order_report', {
    Customer_Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Shop_Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Customer_Code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Credit_Limit: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    Created_By: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order_Value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Invoice_Amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    Invoice_Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Return_Amount: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Collectable: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Collected: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Pending_Amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Order_Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order_Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Dc_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_loc_order_report'
  });
};
