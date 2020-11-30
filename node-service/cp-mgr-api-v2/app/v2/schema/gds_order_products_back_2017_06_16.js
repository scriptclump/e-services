/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_products_back_2017_06_16', {
    gds_order_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pname: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
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
    cost: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(75),
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
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    no_of_units: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
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
    tableName: 'gds_order_products_back_2017_06_16'
  });
};
