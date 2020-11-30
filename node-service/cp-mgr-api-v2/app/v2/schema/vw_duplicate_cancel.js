/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_duplicate_cancel', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order Code: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_duplicate_cancel'
  });
};
