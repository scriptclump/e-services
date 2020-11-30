/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dashboard_preference', {
    dashboard_pref_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dashboard_master_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'dashboard_master',
        key: 'dashboard_master_id'
      }
    },
    dashboard_name: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(20),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    period: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    chart_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
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
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'dashboard_preference'
  });
};
