/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_work_experience', {
    work_experience_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    organization_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    designation: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    location: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    reference_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    reference_contact_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    }
  }, {
    tableName: 'emp_work_experience'
  });
};
