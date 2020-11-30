/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_nw_log', {
    record_id: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      primaryKey: true
    },
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    nw_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    min: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    avg: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    max: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    timestamp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'hosts_nw_log'
  });
};
