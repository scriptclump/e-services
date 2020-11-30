/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('employee', {
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    business_unit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    prefix: {
      type: DataTypes.ENUM('Mr.','Ms.','Mrs.'),
      allowNull: true
    },
    firstname: {
      type: DataTypes.STRING(25),
      allowNull: false
    },
    middlename: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    lastname: {
      type: DataTypes.STRING(25),
      allowNull: false
    },
    email_id: {
      type: DataTypes.STRING(96),
      allowNull: false
    },
    office_email: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    role_id: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    role_code: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    reporting_manager_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    designation: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    grade: {
      type: DataTypes.STRING(11),
      allowNull: true
    },
    department: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    password: {
      type: DataTypes.STRING(40),
      allowNull: false,
      defaultValue: 'ebutor@123'
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    alternative_mno: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    landline_ext: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    profile_picture: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    employment_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    dob: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    father_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    mother_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    gender: {
      type: DataTypes.ENUM('Male','Female'),
      allowNull: true
    },
    marital_status: {
      type: DataTypes.ENUM('Single','Married'),
      allowNull: true
    },
    blood_group: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    nationality: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    aadhar_number: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    aadhar_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pan_card_number: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    pan_card_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    driving_licence_number: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    dl_expiry_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    uan_number: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    passport_number: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    passport_valid_to: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    emp_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    doj: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    exit_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '57148'
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    comment: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'employee'
  });
};
