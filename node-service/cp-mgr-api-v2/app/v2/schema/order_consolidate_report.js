/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('order_consolidate_report', {
    report_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    hub_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    crate_no: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    checker_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    checking_time: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    checked_status: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    checker_reasons: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    order_time: {
      type: DataTypes.TIME,
      allowNull: true
    },
    retailer_code: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    invoice_date: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    invoice_time: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    article_no: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    manufacturer: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    brand: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    category: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    product_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_group_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ordered_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoiced_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cancelled_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    base_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_pct: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    tax_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ordered_esu_qty: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ordered_skus: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_skus: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cancelled_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    returns_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    line_status: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    order_status: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    picked_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    delivered_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    beat: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    star: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    order_datetime: {
      type: DataTypes.DATE,
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_transist_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hsn_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_self: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    state: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'order_consolidate_report'
  });
};
