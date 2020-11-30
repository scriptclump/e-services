/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_prod_pack_details', {
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
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
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    pack eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    packlevel: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Weight: {
      type: DataTypes.STRING(2500),
      allowNull: false
    },
    length: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    breadth: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    height: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000000'
    },
    Weight In: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pack Size: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    pack Size gm: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    returnss: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    orders: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_prod_pack_details'
  });
};
