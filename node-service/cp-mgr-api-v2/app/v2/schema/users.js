/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('users', {
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    business_unit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    password: {
      type: DataTypes.STRING(40),
      allowNull: false
    },
    firstname: {
      type: DataTypes.STRING(25),
      allowNull: false
    },
    lastname: {
      type: DataTypes.STRING(25),
      allowNull: false
    },
    email_id: {
      type: DataTypes.STRING(96),
      allowNull: false
    },
    department: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    designation: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    landline_no: {
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
    is_parent: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    is_disabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reporting_manager_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    emp_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    otp: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    lp_otp: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    password_token: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    lp_token: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    chat_token: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_email_verified: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    is_password_updated: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    password_updated_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    aadhar_id: {
      type: DataTypes.STRING(12),
      allowNull: true
    }
  }, {
    tableName: 'users'
  });
};
