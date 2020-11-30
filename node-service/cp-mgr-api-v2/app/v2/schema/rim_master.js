/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_master', {
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    exception: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    emergency: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    high: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    normal: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    low: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    unassigned: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hosts_nw_status: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hosts_svc_status: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hosts_perf_status: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    timestamp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'rim_master'
  });
};
