/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_master_escalation_log', {
    record_index: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      primaryKey: true
    },
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    ticket_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    ticket_date: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    ticket_summary: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ticket_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ticket_priority: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    assigned_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    escalation_level: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    escalation_date: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    }
  }, {
    tableName: 'rim_master_escalation_log'
  });
};
