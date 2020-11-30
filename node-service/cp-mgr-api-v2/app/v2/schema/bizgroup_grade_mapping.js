/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bizgroup_grade_mapping', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    business_group_index: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    grade_id: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    }
  }, {
    tableName: 'bizgroup_grade_mapping'
  });
};
