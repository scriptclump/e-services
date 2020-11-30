/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_purchase_details', {
    po_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    po_code: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    grn_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    supplier: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    lp_wh_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    po_valid_days: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    po_created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    po_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    grn_created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    grn_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    manf_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    poQty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    grnQty: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    qtyUom: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    no_of_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    free_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    free_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    freeUom: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_tax_included: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
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
    po_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    grn_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    po_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    grn_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    price_excl_tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    unit_price_excl_tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    poFreeQty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    shelf_life: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shelf_life_uom: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    erp_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    }
  }, {
    tableName: 'vw_purchase_details'
  });
};
