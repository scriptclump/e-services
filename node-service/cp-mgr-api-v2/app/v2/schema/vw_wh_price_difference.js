/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_wh_price_difference', {
    APOB: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    APOB Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    SKU: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    APOB ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    DC: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    DC Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    FC: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    FC Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    FC ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_wh_price_difference'
  });
};
