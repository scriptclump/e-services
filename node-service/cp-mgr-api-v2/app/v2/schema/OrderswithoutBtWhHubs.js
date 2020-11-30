/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('OrderswithoutBtWhHubs', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Beat: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Beat Id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Warehouse ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Hub: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Hub ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    FF Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'OrderswithoutBtWhHubs'
  });
};
