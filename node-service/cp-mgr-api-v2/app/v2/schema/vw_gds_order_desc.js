/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_gds_order_desc', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    seller_sku: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    invoiced: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_gds_order_desc'
  });
};
