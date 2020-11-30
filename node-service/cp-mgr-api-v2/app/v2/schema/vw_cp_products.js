/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_cp_products', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
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
    product_inventory: {
      type: DataTypes.INTEGER(11),
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
    variant_value1: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    variant_value2: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    variant_value3: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    is_default: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    key_value_index: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    parent_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    meta_keywords: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_cp_products'
  });
};
