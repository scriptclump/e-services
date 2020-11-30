/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_contact', {
    employee_contact_id: {
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
    emergency_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    emergency_relation: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    emergency_contact_one: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    emergency_contact_two: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    facebook_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    linkedin_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    cu_address: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    cu_address2: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    cu_city: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    cu_state: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    cu_country: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    cu_zip_code: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    pe_address: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pe_address2: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pe_city: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    pe_state: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    pe_country: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    pe_zip_code: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    spouse_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    no_of_childerns: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    child1_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    child1_dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    child1_age: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    child2_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    child2_dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    child2_age: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ref_one_relation: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ref_one_contact_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ref_one_address: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_one_city: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_one_state: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_one_country: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_one_pin_code: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_two_contact_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ref_two_relation: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ref_two_address: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_two_city: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_two_state: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_two_country: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ref_two_pin_code: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'emp_contact'
  });
};
