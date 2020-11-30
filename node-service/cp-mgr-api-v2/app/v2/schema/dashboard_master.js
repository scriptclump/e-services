/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dashboard_master', {
    dashboard_master_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dashboard_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    dashboard_desc: {
      type: DataTypes.STRING(400),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    x-axis_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    y-axis_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    chart_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    period: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    proc_name: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    update_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'dashboard_master'
  });
};
