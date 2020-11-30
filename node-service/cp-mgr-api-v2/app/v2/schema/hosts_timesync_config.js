/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_timesync_config', {
    timeservers: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: 'pool.ntp.org'
    },
    diffthreshold: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '1500'
    }
  }, {
    tableName: 'hosts_timesync_config'
  });
};
