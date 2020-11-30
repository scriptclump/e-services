/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('super_admin', {
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'super_admin'
  });
};
