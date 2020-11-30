/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_duplicate_order_invoice', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order Code: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Invoice Code: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_duplicate_order_invoice'
  });
};
