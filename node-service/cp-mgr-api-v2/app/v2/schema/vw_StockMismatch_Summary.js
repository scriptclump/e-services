/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_StockMismatch_Summary', {
    DC_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    DC_NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Opening_Stock: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Grn: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Closing: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Difference: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Sales_Return: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Invoiced: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Inventory_Difference: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cycle_Count_Excess: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cycle_Count_Missing: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cycle_Count_Damage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_StockMismatch_Summary'
  });
};
