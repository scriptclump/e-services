/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_cashback_data', {
    ecash_pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cbk_source_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cbk_label: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    benificiary_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    product_star: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_size: {
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
    cbk_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    cbk_value: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    is_applied: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    cbk_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cbk_ref_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cap_limit: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_order_cashback_data'
  });
};
