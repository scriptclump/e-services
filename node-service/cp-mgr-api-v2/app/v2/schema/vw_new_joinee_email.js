/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_new_joinee_email', {
    NAME: {
      type: DataTypes.STRING(56),
      allowNull: false,
      defaultValue: ''
    },
    PROFILE_PICTURE: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    SUBJECT: {
      type: DataTypes.STRING(305),
      allowNull: false,
      defaultValue: ''
    },
    EMAIL: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    DESIGNATION: {
      type: DataTypes.STRING(269),
      allowNull: true
    }
  }, {
    tableName: 'vw_new_joinee_email'
  });
};
