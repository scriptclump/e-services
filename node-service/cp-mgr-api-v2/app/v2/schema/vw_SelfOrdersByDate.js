/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_SelfOrdersByDate', {
    shop_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    Beat: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Order Hub: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    FF: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_SelfOrdersByDate'
  });
};
