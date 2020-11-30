/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_IntCurMonthSales_ByWH', {
    WH: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Total Sale: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    Cancelled: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Invoiced: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    Returned: {
      type: "DOUBLE(22,2)",
      allowNull: false,
      defaultValue: '0.00'
    },
    Delivered: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    }
  }, {
    tableName: 'vw_IntCurMonthSales_ByWH'
  });
};
