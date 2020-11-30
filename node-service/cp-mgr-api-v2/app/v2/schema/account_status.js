/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('account_status', {
    account_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'account_status'
  });
};
