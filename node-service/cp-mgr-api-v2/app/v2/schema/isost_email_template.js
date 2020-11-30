/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_email_template', {
    tpl_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    cfg_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    name: {
      type: DataTypes.STRING(32),
      allowNull: false,
      defaultValue: ''
    },
    notes: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ticket_autoresp_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    ticket_autoresp_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    ticket_notice_subj: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    ticket_notice_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    ticket_alert_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    ticket_alert_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    message_autoresp_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    message_autoresp_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    message_alert_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    message_alert_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    note_alert_subj: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    note_alert_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    assigned_alert_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    assigned_alert_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    ticket_overdue_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    ticket_overdue_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    ticket_overlimit_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    ticket_overlimit_body: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    ticket_reply_subj: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    ticket_reply_body: {
      type: DataTypes.TEXT,
      allowNull: false
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
    tableName: 'isost_email_template'
  });
};
