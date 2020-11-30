/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('shipping_services', {
    service_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    carrier_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    service_name: {
      type: DataTypes.STRING(100),
      allowNull: false
    }
  }, {
    tableName: 'shipping_services'
  });
};
