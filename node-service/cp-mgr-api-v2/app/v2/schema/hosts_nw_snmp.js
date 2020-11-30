/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_nw_snmp', {
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    retry_count: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    timeout: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    community_string: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    alarm_threshold: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '2'
    },
    port: {
      type: DataTypes.INTEGER(3),
      allowNull: true,
      defaultValue: '161'
    },
    version: {
      type: DataTypes.STRING(5),
      allowNull: true,
      defaultValue: 'v2'
    },
    disk_exclude: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    v3_user: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    v3_pwd: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'hosts_nw_snmp'
  });
};
