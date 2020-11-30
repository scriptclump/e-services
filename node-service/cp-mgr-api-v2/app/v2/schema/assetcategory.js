/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('assetcategory', {
    assetcategoryid: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    assetcategory: {
      type: DataTypes.STRING(50),
      allowNull: false
    }
  }, {
    tableName: 'assetcategory'
  });
};
