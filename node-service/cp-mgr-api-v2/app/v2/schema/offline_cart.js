/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('offline_cart', {
    oc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    cart_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    cust_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'offline_cart'
  });
};
