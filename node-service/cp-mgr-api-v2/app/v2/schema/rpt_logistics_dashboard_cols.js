/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rpt_logistics_dashboard_cols', {
    rpt_id: {
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
      type: DataTypes.STRING(500),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    report_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    report_type: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    key_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    count_no: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    value_tot: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tooltip: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'rpt_logistics_dashboard_cols'
  });
};
