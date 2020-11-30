/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ff_target', {
    ff_target_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ff_user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    target_name_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    target_daily: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    target_monthly: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    target_weekly: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_by: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'ff_target'
  });
};
