/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_orders', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_cust_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    mfc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cust_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    firstname: {
      type: DataTypes.STRING(25),
      allowNull: false
    },
    lastname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(96),
      allowNull: false
    },
    phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    order_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '17002'
    },
    order_transit_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    platform_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    is_epc: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '4'
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ship_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
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
    total_items: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    total_item_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ip: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    user_agent: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    order_type: {
      type: DataTypes.BOOLEAN,
      allowNull: false
    },
    mp_order_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    order_token: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    erp_order_id: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    order_expiry_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    is_indent: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    scheduled_delivery_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    pref_slab1: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pref_slab2: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    actual_delivery_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    beat: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_self: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    self_user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_inv_print: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    ecash_applied: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    auto_assign: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    is_downloaded: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
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
    },
    order_date_new: {
      type: DataTypes.DATE,
      allowNull: true
    },
    order_code_new: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    Column 50: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_cnc: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    mfc_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    discount_before_tax: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    instant_wallet_cashback: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    cashback_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    is_primary_sale: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    is_secondary_sale: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    primary_dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    is_cust_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'gds_orders'
  });
};
