/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_nonMovingProductsList', {
    DC ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    PRODUCT ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    DATE: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vw_nonMovingProductsList'
  });
};
