/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_organization', {
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    direct_report_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    dot_report_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'user_organization'
  });
};
