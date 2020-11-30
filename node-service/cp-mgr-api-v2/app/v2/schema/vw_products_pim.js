/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_products_pim', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mfg_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    industry: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    department: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sub_department: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    category: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    article_number: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Popularity: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Manufacturer SKU Code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Varient1: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Varient2: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Varient3: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pack_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pack_size_uom: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    product Code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Product Coode Type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_parent: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    Parent: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shelf_life: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    shelf_life_uom: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    perishable: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    product_form: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    flammable: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    hazardous: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    odour: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    fragile: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    licence_req: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    licence_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prefered_channels: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    primary_image: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    warranty_policy: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    return_policy: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_sellable: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    cp_enabled: {
      type: DataTypes.STRING(3),
      allowNull: false,
      defaultValue: ''
    },
    star: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_products_pim'
  });
};
