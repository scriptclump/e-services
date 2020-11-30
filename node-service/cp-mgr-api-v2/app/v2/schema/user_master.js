/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_master', {
    username: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '',
      primaryKey: true
    },
    password: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    staff_number: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    first_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    last_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_phone_office: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_phone_residence: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_phone_mobile: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    permanent_address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    office_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    official_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    personal_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    account_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    account_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    account_expiry: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    account_created_on: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    account_created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '1'
    },
    business_group_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '1'
    },
    grade_id: {
      type: DataTypes.STRING(10),
      allowNull: true
    }
  }, {
    tableName: 'user_master'
  });
};
