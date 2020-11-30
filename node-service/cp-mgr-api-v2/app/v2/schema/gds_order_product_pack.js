/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_product_pack', {
    prod_pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    esu_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    pack_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    discount: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    discount_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'gds_order_product_pack'
  });
};
