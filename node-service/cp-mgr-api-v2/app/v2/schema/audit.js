/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('audit', {
    audit_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false
    },
    audit_timestamp: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    audit_username: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    audit_luser: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    audit_ssnname: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    audit_hostname: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    audit_command: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    audit_comments: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    audit_record_type: {
      type: DataTypes.STRING(25),
      allowNull: true
    }
  }, {
    tableName: 'audit'
  });
};
