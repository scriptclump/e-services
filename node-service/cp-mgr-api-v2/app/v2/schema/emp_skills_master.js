/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_skills_master', {
    emp_skill_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    skill_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    }
  }, {
    tableName: 'emp_skills_master'
  });
};
