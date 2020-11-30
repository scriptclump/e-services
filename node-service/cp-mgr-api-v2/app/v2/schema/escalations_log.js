/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('escalations_log', {
    ticket_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    level: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    timestamp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'escalations_log'
  });
};
