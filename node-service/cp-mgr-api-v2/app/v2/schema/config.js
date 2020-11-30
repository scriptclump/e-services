/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('config', {
    account_as_email: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    md5_enable: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    asset_prefix: {
      type: DataTypes.STRING(10),
      allowNull: true,
      defaultValue: 'AX'
    },
    audit_expiry: {
      type: DataTypes.INTEGER(3),
      allowNull: true,
      defaultValue: '15'
    },
    ezrim: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    master_url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    master_api_key: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    agency_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '1'
    },
    service_dash: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    service_ezasset: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    service_ezticket: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    service_network: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    https: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    snmp: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    terminal: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    terminalport: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    menu_bgcolor: {
      type: DataTypes.STRING(20),
      allowNull: true,
      defaultValue: '#336699'
    }
  }, {
    tableName: 'config'
  });
};
