/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('picking_reserve_bins', {
    reserve_id: {
      type: DataTypes.INTEGER(100),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_code: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_config: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'picking_reserve_bins'
  });
};
