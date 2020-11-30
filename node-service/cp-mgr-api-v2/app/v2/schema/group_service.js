/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('group_service', {
    group_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    service: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'group_service'
  });
};
