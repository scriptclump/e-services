/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('escalation_email', {
    email: {
      type: DataTypes.STRING(500),
      allowNull: true
    }
  }, {
    tableName: 'escalation_email'
  });
};
