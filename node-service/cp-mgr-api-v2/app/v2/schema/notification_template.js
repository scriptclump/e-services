/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('notification_template', {
    notification_template_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    notification_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    notification_message: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    notify_rm: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
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
    tableName: 'notification_template'
  });
};
