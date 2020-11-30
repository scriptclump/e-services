/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('offline_cart_details', {
    cart_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_image: {
      type: DataTypes.STRING(1000),
      allowNull: false
    },
    product_title: {
      type: DataTypes.STRING(1000),
      allowNull: false
    },
    product_star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    color_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(5),
      allowNull: false
    },
    quantity: {
      type: DataTypes.INTEGER(5),
      allowNull: false
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    blocked_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_slab: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    slab_esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_slab_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_type: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    freebie_product_id: {
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
    },
    discount_type: {
      type: DataTypes.ENUM('value','percentage'),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cashback_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cust_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_child: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    packs: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'offline_cart_details'
  });
};
