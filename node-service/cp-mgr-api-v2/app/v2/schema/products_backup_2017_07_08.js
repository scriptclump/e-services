/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('products_backup_2017_07_08', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    kvi: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
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
    thumbnail_image: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true,
      unique: true
    },
    seller_sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    star: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '140003'
    },
    upc: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    upc_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_size_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_parent: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    product_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    business_unit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_gds_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    tax_class_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_deleted: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    no_of_units: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    meta_keywords: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    shelf_life: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shelf_life_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    popularity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prefered_channels: {
      type: DataTypes.STRING(255),
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
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_sellable: {
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
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true,
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
    }
  }, {
    tableName: 'products_backup_2017_07_08'
  });
};
