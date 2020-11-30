/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_di_mi_ci_flat_dt', {
    index_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    sku: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    prod_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    manf: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    cp_enabled: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    po_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    po_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    supplier_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    supplier_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    p_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    sp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    lp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    ptr: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    di: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    mi: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    ci: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    stock_days7: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    stock_days30: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    noofstockdays: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    tlc: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    avgesp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    avgelp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    return_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    invoice_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    net_sold_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    rm: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    rm_email: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    avgdaysales_ea: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    avgdaysales_cfc: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    total_earnings: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    avg_dimici: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    kvi_ranking: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    available_cfc: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    di_stock_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    mi_stock_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    ci_stock_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pending_return_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pending_po_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    available_inv: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    avg_dimici_stock_days: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    stock_norm: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    suggested_bq: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    cfc_to_buy: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    final_buy_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cfc_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    target_cfc_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    total_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    max_bq: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    max_cfc_allowed: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    max_cfc_elp_allowed: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    freebie_title: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    star: {
      type: DataTypes.STRING(20),
      allowNull: true
    }
  }, {
    tableName: 'product_di_mi_ci_flat_dt'
  });
};
