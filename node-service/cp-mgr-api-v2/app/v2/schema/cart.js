/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('cart', {
    cart_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    session_id: {
      type: DataTypes.STRING(32),
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    esu_quantity: {
      type: DataTypes.INTEGER(5),
      allowNull: false,
      defaultValue: '0'
    },
    quantity: {
      type: DataTypes.INTEGER(5),
      allowNull: false,
      defaultValue: '0'
    },
    dit_quantity: {
      type: DataTypes.INTEGER(5),
      allowNull: true,
      defaultValue: '0'
    },
    rate: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    le_wh_id_list: {
      type: DataTypes.STRING(200),
      allowNull: false,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    dit_reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    discount_type: {
      type: DataTypes.ENUM('value','percentage'),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_on: {
      type: DataTypes.ENUM('Star','Product','Bill'),
      allowNull: true
    },
    discount_on_values: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_slab: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    product_slab_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    freebee_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    freebee_mpq: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'cart'
  });
};
