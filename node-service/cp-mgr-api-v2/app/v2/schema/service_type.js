/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('service_type', {
    service_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'service_type'
  });
};
