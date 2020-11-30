/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_manf_brand_prod_details', {
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manf_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    image: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    thumbnail_image: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_logo: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(2000),
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
    kvi: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
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
    suppliercnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    image_count: {
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
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_sellable: {
      type: DataTypes.INTEGER(1),
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
    approved_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ptr: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    taxper: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    available_inventory: {
      type: DataTypes.BIGINT,
      allowNull: true
    }
  }, {
    tableName: 'vw_manf_brand_prod_details'
  });
};
