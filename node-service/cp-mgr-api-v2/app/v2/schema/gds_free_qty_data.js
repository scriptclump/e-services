/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_free_qty_data', {
    gds_free_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ref_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    range_from: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    range_to: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_sample: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_applied: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_free_qty_data'
  });
};
