/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('agency_type', {
    agency_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'agency_type'
  });
};
