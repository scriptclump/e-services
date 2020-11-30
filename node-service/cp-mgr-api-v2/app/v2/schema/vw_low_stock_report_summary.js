/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_low_stock_report_summary', {
    DC_ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    DC_NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    SOH: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    CFC_TO_BUY: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    FINAL_BUY_VALUE: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_low_stock_report_summary'
  });
};
