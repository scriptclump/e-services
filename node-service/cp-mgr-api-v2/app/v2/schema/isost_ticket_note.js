/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket_note', {
    note_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    ticket_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    staff_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    source: {
      type: DataTypes.STRING(32),
      allowNull: false,
      defaultValue: ''
    },
    title: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: 'Generic Intermal Notes'
    },
    note: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    time_spent: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '0'
    }
  }, {
    tableName: 'isost_ticket_note'
  });
};
