/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_inv_stock_diff', {
    DC/FC: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Product_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product_Name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Stock_Diff: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    Opening_Stock: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    GRN: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Sales_Return: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Invoiced: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Closing: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Cycle_Count_Damage: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Cycle_Count_Excess: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Cycle_Count_Missing: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Stock_Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    tableName: 'vw_inv_stock_diff'
  });
};
