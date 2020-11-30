/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_nonMovingProductsList_summary', {
    DC ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    VALUE: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_nonMovingProductsList_summary'
  });
};
