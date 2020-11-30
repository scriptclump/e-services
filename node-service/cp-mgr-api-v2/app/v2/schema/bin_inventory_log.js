/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bin_inventory_log', {
    bin_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    bin_inv_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    actual_udpated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'bin_inventory_log'
  });
};
