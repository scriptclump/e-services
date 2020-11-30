/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_product_grid', {
    product_id: {
      type: DataTypes.INTEGER(11),
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
    product_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    primary_image: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(6),
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
    created_by: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    product_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cp_enabled: {
      type: DataTypes.INTEGER(1),
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
    ptrvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    taxper: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    available_inventory: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    manufacturer_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    product_class_name: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_product_grid'
  });
};
