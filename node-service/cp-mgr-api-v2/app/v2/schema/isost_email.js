/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_email', {
    email_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    noautoresp: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    priority_id: {
      type: DataTypes.INTEGER(3).UNSIGNED,
      allowNull: false,
      defaultValue: '2'
    },
    dept_id: {
      type: DataTypes.INTEGER(3).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    email: {
      type: DataTypes.STRING(125),
      allowNull: false,
      defaultValue: '',
      unique: true
    },
    name: {
      type: DataTypes.STRING(32),
      allowNull: false,
      defaultValue: ''
    },
    userid: {
      type: DataTypes.STRING(125),
      allowNull: false
    },
    userpass: {
      type: DataTypes.STRING(125),
      allowNull: false
    },
    mail_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    mail_host: {
      type: DataTypes.STRING(125),
      allowNull: false
    },
    mail_protocol: {
      type: DataTypes.ENUM('POP','IMAP'),
      allowNull: false,
      defaultValue: 'POP'
    },
    mail_encryption: {
      type: DataTypes.ENUM('NONE','SSL'),
      allowNull: false
    },
    mail_port: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    mail_fetchfreq: {
      type: DataTypes.INTEGER(3),
      allowNull: false,
      defaultValue: '5'
    },
    mail_fetchmax: {
      type: DataTypes.INTEGER(4),
      allowNull: false,
      defaultValue: '30'
    },
    mail_delete: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    mail_errors: {
      type: DataTypes.INTEGER(3),
      allowNull: false,
      defaultValue: '0'
    },
    mail_lasterror: {
      type: DataTypes.DATE,
      allowNull: true
    },
    mail_lastfetch: {
      type: DataTypes.DATE,
      allowNull: true
    },
    smtp_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    smtp_host: {
      type: DataTypes.STRING(125),
      allowNull: false
    },
    smtp_port: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    smtp_secure: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    smtp_auth: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    }
  }, {
    tableName: 'isost_email'
  });
};
