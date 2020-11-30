/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('book', {
    id: {
      type: DataTypes.INTEGER(8).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    title: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    tags: {
      type: DataTypes.JSON,
      allowNull: true
    }
  }, {
    tableName: 'book'
  });
};
