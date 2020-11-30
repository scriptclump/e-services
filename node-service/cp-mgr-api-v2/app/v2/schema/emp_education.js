/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_education', {
    emp_education_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    institute: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    degree: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    specilization: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    grade: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    from_year: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_year: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    education_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    edu_year: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    university: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    percentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'emp_education'
  });
};
