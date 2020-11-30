/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_product_slab_rates', {
    start_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pack_size: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_markup: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    product_price_id: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    ptr: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    level_name: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    effective_date: {
      type: "BINARY(0)",
      allowNull: true
    },
    pack_eff_date: {
      type: "BINARY(0)",
      allowNull: true
    },
    created_at: {
      type: "BINARY(0)",
      allowNull: true
    },
    is_slab: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    star: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    esu: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_slab_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    blocked_qty: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    cashback_details: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_product_slab_rates'
  });
};
