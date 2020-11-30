/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('notifications_template', {
    notify_template_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    notif_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    notif_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    notify_template: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    notify_priority: {
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
    }
  }, {
    tableName: 'notifications_template'
  });
};
