/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('temp', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    consumed: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    NewSOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    newOrderQty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    avaiable_inv: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'temp'
  });
};
