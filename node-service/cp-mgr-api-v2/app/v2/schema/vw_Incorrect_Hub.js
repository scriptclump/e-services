/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_Incorrect_Hub', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Customer Le id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Hub: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Actual Hub: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    Order Beat: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
    tableName: 'vw_Incorrect_Hub'
  });
};
