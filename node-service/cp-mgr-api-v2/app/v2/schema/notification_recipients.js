/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('notification_recipients', {
    notification_recipient_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    notification_template_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    notificaiton_recipient_roles: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    notificaiton_recipient_users: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    notificaiton_recipient_legal_entities: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'notification_recipients'
  });
};
