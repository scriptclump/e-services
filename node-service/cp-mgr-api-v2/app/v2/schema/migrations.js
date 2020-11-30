/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('migrations', {
    migration: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    batch: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'migrations'
  });
};
