/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket_attachment', {
    attach_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    ticket_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    ref_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    ref_type: {
      type: DataTypes.ENUM('M','R'),
      allowNull: false,
      defaultValue: 'M'
    },
    file_size: {
      type: DataTypes.STRING(32),
      allowNull: false,
      defaultValue: ''
    },
    file_name: {
      type: DataTypes.STRING(128),
      allowNull: false,
      defaultValue: ''
    },
    file_key: {
      type: DataTypes.STRING(128),
      allowNull: false,
      defaultValue: ''
    },
    deleted: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'isost_ticket_attachment'
  });
};
