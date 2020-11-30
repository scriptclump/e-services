/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('asset_template', {
    email: {
      type: DataTypes.STRING(1000),
      allowNull: true
    }
  }, {
    tableName: 'asset_template'
  });
};
