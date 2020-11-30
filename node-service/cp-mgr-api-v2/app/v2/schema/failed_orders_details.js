/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('failed_orders_details', {
    fod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    order_code: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    is_processed: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'failed_orders_details'
  });
};
