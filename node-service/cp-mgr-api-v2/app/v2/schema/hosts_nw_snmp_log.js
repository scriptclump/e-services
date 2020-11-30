/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_nw_snmp_log', {
    record_id: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      primaryKey: true
    },
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    nw_snmp_cpu_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    nw_snmp_mem_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    nw_snmp_dsk_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    cpu_user: {
      type: DataTypes.INTEGER(3),
      allowNull: true
    },
    cpu_system: {
      type: DataTypes.INTEGER(3),
      allowNull: true
    },
    cpu_idle: {
      type: DataTypes.INTEGER(3),
      allowNull: true
    },
    mem_utilization: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    disk_utilization: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    cpu_snmp_result: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    mem_snmp_result: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    timestamp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'hosts_nw_snmp_log'
  });
};
