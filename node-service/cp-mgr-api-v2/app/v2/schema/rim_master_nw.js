/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_master_nw', {
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hostname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    platform: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    network: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    live: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    count: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    snmp: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    network_snmp_cpu_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    cpu: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    network_snmp_mem_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    mem: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    network_snmp_dsk_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    dsk: {
      type: DataTypes.STRING(500),
      allowNull: true
    }
  }, {
    tableName: 'rim_master_nw'
  });
};
