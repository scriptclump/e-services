/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('products_slab_rates', {
    product_slab_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    start_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    end_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    business_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prmt_lock_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    placed_lock_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    prmt_det_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_markup: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    start_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    product_star_slab: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
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
    }
  }, {
    tableName: 'products_slab_rates'
  });
};
