/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket_priority', {
    priority_id: {
      type: DataTypes.INTEGER(4),
      allowNull: false,
      primaryKey: true
    },
    priority: {
      type: DataTypes.STRING(60),
      allowNull: false,
      defaultValue: '',
      unique: true
    },
    priority_desc: {
      type: DataTypes.STRING(30),
      allowNull: false,
      defaultValue: ''
    },
    priority_color: {
      type: DataTypes.STRING(7),
      allowNull: false,
      defaultValue: ''
    },
    priority_urgency: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    ispublic: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    }
  }, {
    tableName: 'isost_ticket_priority'
  });
};
