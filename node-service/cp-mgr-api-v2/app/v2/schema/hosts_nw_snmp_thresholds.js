/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_nw_snmp_thresholds', {
    id: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      primaryKey: true
    },
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    snmp_parameter: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    param_value: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    }
  }, {
    tableName: 'hosts_nw_snmp_thresholds'
  });
};
