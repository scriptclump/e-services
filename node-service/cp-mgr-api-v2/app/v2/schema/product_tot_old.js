/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_tot_old', {
    prod_price_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    supplier_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    supplier_sku_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '4'
    },
    atp: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    atp_period: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    msp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    base_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    dlp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    rlp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_markup: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    cbp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    kvi: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    credit_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_terms: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    is_return_accepted: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    inventory_mode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    distributor_margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    retailer_margin_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    retailer_margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    moq: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    moq_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mpq: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_location_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    supplier_dc_relationship: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    grn_freshness_percentage: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    delivery_tat_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    grn_days: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    rtv_allowed: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_preferred_supplier: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    subscribe: {
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'product_tot_old'
  });
};
