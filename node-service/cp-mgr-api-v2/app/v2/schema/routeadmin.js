/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routeadmin', {
    id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updatedAt: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'routeadmin'
  });
};
