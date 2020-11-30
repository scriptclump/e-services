/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_groups', {
    group_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    group_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    group_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'rim_groups'
  });
};
