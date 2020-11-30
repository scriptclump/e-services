/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_slabs_expiring', {
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Products_Count: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    State: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Customer_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Expiry_date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'vw_slabs_expiring'
  });
};
