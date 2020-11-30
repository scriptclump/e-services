/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_service', {
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    port: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    alarm_threshold: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '2'
    },
    pattern: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    timeout: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    url: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'hosts_service'
  });
};
