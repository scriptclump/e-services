/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_duplicatepayments', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order Code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    Collected: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_duplicatepayments'
  });
};
