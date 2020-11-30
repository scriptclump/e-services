/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_dynamic_order_details', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    beat: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cust_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    legal_entity_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    retailer: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    retailer_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    order_esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    order_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    current_esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    current_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    order_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    invoice_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    return_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    cancel_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    delivered_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    booked_tbv: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    invoice_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    return_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    delivered_tbv: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    booked_tgm: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    delivered_tgm: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_dynamic_order_details'
  });
};
