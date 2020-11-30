/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_LastCycleDetails', {
    DC_NAME: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    DATE: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'vw_LastCycleDetails'
  });
};
