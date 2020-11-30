/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('products_inventory_flat', {
    pif_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
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
    hsn_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    primary_image: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    thumbnail_image: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    product_class_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_class_name: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    sub_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sub_category_name: {
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
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    brand_logo: {
      type: DataTypes.STRING(2000),
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
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    seller_sku: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    kvi_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    kvi_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    pack_type: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.STRING(11),
      allowNull: true
    },
    pack_size_uom: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    frebee_desc: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    freebee_sku: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    suppliercnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    image_count: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
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
    tax: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    kvi: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    segment_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    approved_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'products_inventory_flat'
  });
};
