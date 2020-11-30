/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stock_product_indent_gen_by_dc', {
    stock_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    p_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    po_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    po_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    sku: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    p_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    kvi: {
      type: DataTypes.STRING(10),
      allowNull: false
    },
    manf_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    manf_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    supplier_code: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    supplier_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    lp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    target_cfc_elp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    cfc_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    invoice_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    return_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    pending_return_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    pending_po_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    pending_ind_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    avail_inv: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    avail_cfc: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    net_sold_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    net_sold_esp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    sale_value_per: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    total_buy_value: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    available_amt: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    actual_buy_value: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    cfc_to_buy: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    cfc_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    final_buy_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    sale_val_per_ol: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ol_cfc_to_buy: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ceil_cfc_to_buy: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ol_final_buy: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'stock_product_indent_gen_by_dc'
  });
};
