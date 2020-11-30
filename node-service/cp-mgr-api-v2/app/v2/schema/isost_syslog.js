/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_syslog', {
    log_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    log_type: {
      type: DataTypes.ENUM('Debug','Warning','Error'),
      allowNull: false
    },
    title: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    log: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    logger: {
      type: DataTypes.STRING(64),
      allowNull: false
    },
    ip_address: {
      type: DataTypes.STRING(16),
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    }
  }, {
    tableName: 'isost_syslog'
  });
};
