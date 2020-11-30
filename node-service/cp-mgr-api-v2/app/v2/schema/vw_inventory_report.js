/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_inventory_report', {
    inv_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    primary_image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_class_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_class_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sub_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sub_category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    dcname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    atp: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    available_inventory: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    available_dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    perishable: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    product_form: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    flammable: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    hazardous: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    odour: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    fragile: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    shelflife: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shelf_life_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    status: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    pack_size_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    taxper: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    product_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ptrvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    cp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    map: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    frebee_desc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    freebee_sku: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_sellable: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    pack_type: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    quarantine_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    inv_display_mode: {
      type: DataTypes.ENUM('atp+soh','atp','soh'),
      allowNull: false,
      defaultValue: 'soh'
    },
    min-pickface-replenishment: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    replenishment_UOM: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
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
    isd: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    isd7: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    isd30: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_inventory_report'
  });
};
