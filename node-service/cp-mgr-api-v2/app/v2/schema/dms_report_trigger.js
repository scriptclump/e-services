/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dms_report_trigger', {
    dms_trgger_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    period_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    inventory_days: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
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
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'dms_report_trigger'
  });
};
