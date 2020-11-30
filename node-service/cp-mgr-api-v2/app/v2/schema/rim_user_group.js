/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_user_group', {
    group_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'rim_user_group'
  });
};
