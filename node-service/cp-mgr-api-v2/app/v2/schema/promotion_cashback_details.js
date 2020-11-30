/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('promotion_cashback_details', {
    cbk_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    cbk_ref_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cbk_source_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    cbk_label: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    state_id: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    customer_type: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    wh_id: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    benificiary_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATE,
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
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cbk_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_self: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    manufacturer_id: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    product_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cap_limit: {
      type: "DOUBLE(16,5)",
      allowNull: true
    }
  }, {
    tableName: 'promotion_cashback_details'
  });
};
