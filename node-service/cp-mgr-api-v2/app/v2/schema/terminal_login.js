/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('terminal_login', {
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'terminal_login'
  });
};
