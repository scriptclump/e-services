/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bin_inventory_soh_deviation', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    CPInventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    location: {
      type: DataTypes.STRING(225),
      allowNull: true
    },
    bin_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bin_inventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    deviation: {
      type: DataTypes.BIGINT,
      allowNull: true
    }
  }, {
    tableName: 'bin_inventory_soh_deviation'
  });
};
