/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('assetstatus', {
    statusid: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    status: {
      type: DataTypes.STRING(30),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'assetstatus'
  });
};
