/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_order_details', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    shop_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    name: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    mp_order_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_total: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    article_no: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    pname: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    base_price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    tax_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    esu_qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    order_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    area: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    le_wh_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    order_product_status: {
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
    manf_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ordered_qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    invoiced_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    canceled_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shipped_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    to_be_shipped_qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    parent_id: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    inv_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    can_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ret_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(30),
      allowNull: false,
      defaultValue: ''
    },
    scheduled_delivery_date: {
      type: DataTypes.STRING(10),
      allowNull: false,
      defaultValue: ''
    },
    actual_delivery_date: {
      type: DataTypes.STRING(10),
      allowNull: false,
      defaultValue: ''
    },
    invoice_date: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    scheduled_delivery_slot: {
      type: DataTypes.STRING(10),
      allowNull: false,
      defaultValue: ''
    },
    beat: {
      type: DataTypes.STRING(11),
      allowNull: false,
      defaultValue: ''
    },
    pincode: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sales_excutive: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    picker_name: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    picker_date: {
      type: DataTypes.STRING(30),
      allowNull: false,
      defaultValue: ''
    },
    gds_order_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    pref_slot1: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    pref_slot2: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_order_details'
  });
};
