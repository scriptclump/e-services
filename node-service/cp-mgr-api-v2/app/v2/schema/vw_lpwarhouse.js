/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_lpwarhouse', {
    LpNo: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Logo: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    Address: {
      type: DataTypes.STRING(367),
      allowNull: true
    },
    warehouses: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    fullfilment: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    },
    forwarding: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    },
    COD: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    }
  }, {
    tableName: 'vw_lpwarhouse'
  });
};
