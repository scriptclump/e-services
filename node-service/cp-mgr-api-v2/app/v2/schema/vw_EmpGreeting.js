/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_EmpGreeting', {
    NAME: {
      type: DataTypes.STRING(56),
      allowNull: true
    },
    Greeting: {
      type: DataTypes.STRING(225),
      allowNull: true
    },
    YEARS: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    EMAIL: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Profile_Picture: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_EmpGreeting'
  });
};
