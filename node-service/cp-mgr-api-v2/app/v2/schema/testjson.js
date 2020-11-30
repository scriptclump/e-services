/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('testjson', {
    name: {
      type: DataTypes.JSON,
      allowNull: true
    }
  }, {
    tableName: 'testjson'
  });
};
