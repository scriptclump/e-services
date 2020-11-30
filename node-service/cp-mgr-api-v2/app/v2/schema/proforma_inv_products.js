/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('proforma_inv_products', {
    pf_product_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    proforma_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    is_tax_included: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    tax_name: {
      type: DataTypes.STRING(5),
      allowNull: true
    },
    tax_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    hsn_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    tax_data: {
      type: DataTypes.JSON,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    uom: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    no_of_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    uom_in_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    free_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    free_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    free_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    freeuom_in_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cur_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_on_hand: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    inv_reserved: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    expiry_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    apply_discount: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    discount_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'proforma_inv_products'
  });
};
