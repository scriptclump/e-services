/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_products_freshness_by_fc', {
    FC ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    FC Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Product Id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Product Name: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    GRN_QTY: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Mfg Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Exp Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Total Days: {
      type: DataTypes.INTEGER(7),
      allowNull: true
    },
    Freshness: {
      type: DataTypes.INTEGER(7),
      allowNull: true
    },
    FreshnessPercentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    slot: {
      type: DataTypes.STRING(8),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_products_freshness_by_fc'
  });
};
