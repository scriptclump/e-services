/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_low_stock_report', {
    DC_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    DC_NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    PRODUCT_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    PRODUCT_NAME: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    SKU: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    CFC_TO_BUY: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    FINAL_BUY_VALUE: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_low_stock_report'
  });
};
