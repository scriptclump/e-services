/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket', {
    ticket_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    ticketID: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    dept_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '1'
    },
    priority_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '2'
    },
    topic_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    staff_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    email: {
      type: DataTypes.STRING(120),
      allowNull: false,
      defaultValue: ''
    },
    name: {
      type: DataTypes.STRING(32),
      allowNull: false,
      defaultValue: ''
    },
    subject: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '[no subject]'
    },
    helptopic: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    phone: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    phone_ext: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    ip_address: {
      type: DataTypes.STRING(16),
      allowNull: false,
      defaultValue: ''
    },
    status: {
      type: DataTypes.ENUM('open','closed'),
      allowNull: false,
      defaultValue: 'open'
    },
    source: {
      type: DataTypes.ENUM('Web','Email','Phone','Other'),
      allowNull: false,
      defaultValue: 'Other'
    },
    isoverdue: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    isanswered: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    duedate: {
      type: DataTypes.DATE,
      allowNull: true
    },
    reopened: {
      type: DataTypes.DATE,
      allowNull: true
    },
    closed: {
      type: DataTypes.DATE,
      allowNull: true
    },
    lastmessage: {
      type: DataTypes.DATE,
      allowNull: true
    },
    lastresponse: {
      type: DataTypes.DATE,
      allowNull: true
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
    },
    track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    asset_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ticket_type_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '1'
    },
    close_tkt_location: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    poa_activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '0'
    },
    pending_approval: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '1'
    }
  }, {
    tableName: 'isost_ticket'
  });
};
