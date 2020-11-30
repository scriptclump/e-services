/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('numbers', {
    n: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    }
  }, {
    tableName: 'numbers'
  });
};
