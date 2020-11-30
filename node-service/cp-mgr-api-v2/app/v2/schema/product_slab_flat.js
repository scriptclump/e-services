/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_slab_flat', {
    start_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pack_size: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    is_markup: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    product_price_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ptr: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    level_name: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    pack_eff_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    is_slab: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    star: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
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
      allowNull: true
    },
    blocked_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cashback_details: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'product_slab_flat'
  });
};
