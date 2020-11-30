/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_insurance', {
    employee_insurance_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    spouse_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    spouse_dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    no_of_child: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    child_one_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    child_one_dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    child_two_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    child_two_dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    card_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    tpa: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    tpa_contact_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    }
  }, {
    tableName: 'emp_insurance'
  });
};
