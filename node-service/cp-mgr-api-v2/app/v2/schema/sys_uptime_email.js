/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sys_uptime_email', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    email_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    status: {
      type: DataTypes.ENUM('active','inactive'),
      allowNull: false,
      defaultValue: 'active'
    },
    host_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    continuos_alerts: {
      type: DataTypes.INTEGER(2),
      allowNull: true,
      defaultValue: '0'
    }
  }, {
    tableName: 'sys_uptime_email'
  });
};
