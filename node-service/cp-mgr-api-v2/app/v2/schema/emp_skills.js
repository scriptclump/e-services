/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_skills', {
    skill_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    skill_description: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    emp_skill_id: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    experience_years: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    experience_months: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    skill_level: {
      type: DataTypes.ENUM('Expert','Intermediate','Beginner'),
      allowNull: true
    },
    last_used_year: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    }
  }, {
    tableName: 'emp_skills'
  });
};
