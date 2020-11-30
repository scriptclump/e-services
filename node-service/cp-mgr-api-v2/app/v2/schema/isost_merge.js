/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_merge', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ticket_id: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'isost_merge'
  });
};
