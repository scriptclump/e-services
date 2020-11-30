/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_promotions_expiring', {
    Promotion_Name: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    State: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Customer_Type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Expiry_Date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'vw_promotions_expiring'
  });
};
