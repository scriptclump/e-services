/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_category_products', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
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
    segemnt_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    segemnts: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    meta_keywords: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_category_products'
  });
};
