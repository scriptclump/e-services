/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_cpProductWithOutVariants', {
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product Title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Inventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    MRP: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    Variant1: {
      type: DataTypes.STRING(2500),
      allowNull: true
    },
    Variant2: {
      type: DataTypes.STRING(2500),
      allowNull: true
    },
    Variant3: {
      type: DataTypes.STRING(2500),
      allowNull: true
    }
  }, {
    tableName: 'vw_cpProductWithOutVariants'
  });
};
