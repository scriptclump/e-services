/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_products_list', {
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
    is_parent: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
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
    }
  }, {
    tableName: 'vw_products_list'
  });
};
