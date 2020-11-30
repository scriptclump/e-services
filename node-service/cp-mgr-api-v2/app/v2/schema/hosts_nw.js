/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hosts_nw', {
    host_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    count: {
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
    alarm_threshold: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '2'
    },
    flap_timeout: {
      type: DataTypes.INTEGER(2),
      allowNull: true,
      defaultValue: '500'
    },
    flap_threshold: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '5'
    }
  }, {
    tableName: 'hosts_nw'
  });
};
