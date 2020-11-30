/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_service_log', {
    record_id: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      primaryKey: true
    },
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    port: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    svc_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    timestamp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    loadtime: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'hosts_service_log'
  });
};
