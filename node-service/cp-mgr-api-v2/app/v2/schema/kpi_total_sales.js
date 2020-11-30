/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('kpi_total_sales', {
    kpi_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    hub_name: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    report_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    ord_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ord_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    inv_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    inv_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    ret_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ret_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    cncl_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cncl_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    }
  }, {
    tableName: 'kpi_total_sales'
  });
};
