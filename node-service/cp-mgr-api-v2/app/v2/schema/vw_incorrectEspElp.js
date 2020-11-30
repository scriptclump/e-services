/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_incorrectEspElp', {
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product Title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    MRP: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    SKU: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    ELP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Supplier: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    GRN No: {
      type: DataTypes.BIGINT,
      allowNull: true
    }
  }, {
    tableName: 'vw_incorrectEspElp'
  });
};
