/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('services', {
    service: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    comments: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'services'
  });
};
