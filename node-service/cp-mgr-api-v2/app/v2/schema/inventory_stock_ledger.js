/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_stock_ledger', {
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inv_date: {
      type: DataTypes.DATEONLY,
      allowNull: false,
      primaryKey: true
    },
    opening_stock: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    sale_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ret_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    collec_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'inventory_stock_ledger'
  });
};
