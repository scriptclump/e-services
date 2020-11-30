/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_BirthdayEmp', {
    NAME: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    PROFILE_PICTURE: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    SUBJECT: {
      type: DataTypes.STRING(35),
      allowNull: false,
      defaultValue: ''
    },
    EMAIL: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    BIRTHDAY: {
      type: DataTypes.STRING(14),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_BirthdayEmp'
  });
};
