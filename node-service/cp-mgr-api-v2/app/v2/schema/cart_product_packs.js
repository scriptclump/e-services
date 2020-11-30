/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('cart_product_packs', {
    cp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    session_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    cart_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_price: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    pack_cashback: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    all_pack_cashback: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    discount_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pack_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu_quantity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'cart_product_packs'
  });
};
