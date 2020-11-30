/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_help_topic', {
    topic_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    isactive: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '1'
    },
    noautoresp: {
      type: DataTypes.INTEGER(3).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    priority_id: {
      type: DataTypes.INTEGER(3).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    dept_id: {
      type: DataTypes.INTEGER(3).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    topic: {
      type: DataTypes.STRING(40),
      allowNull: false,
      defaultValue: ''
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
    parent_topic_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    ticket_type_id: {
      type: DataTypes.INTEGER(3),
      allowNull: true,
      defaultValue: '1'
    }
  }, {
    tableName: 'isost_help_topic'
  });
};
