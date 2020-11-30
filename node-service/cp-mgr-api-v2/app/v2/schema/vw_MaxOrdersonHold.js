/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_MaxOrdersonHold', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    COMMENT: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    No_Of_Attempts: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    fromdate: {
      type: DataTypes.DATE,
      allowNull: true
    },
    todate: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'vw_MaxOrdersonHold'
  });
};
