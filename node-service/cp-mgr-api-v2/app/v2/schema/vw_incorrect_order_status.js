/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_incorrect_order_status', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Invoice Code: {
      type: DataTypes.STRING(20),
      allowNull: true
    }
  }, {
    tableName: 'vw_incorrect_order_status'
  });
};
