/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_customer', {
    gds_cust_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    suffix: {
      type: DataTypes.STRING(5),
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
    erp_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    mp_user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    email_address: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    dob: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(12),
      allowNull: false
    },
    gender: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    registered_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    language_id: {
      type: DataTypes.INTEGER(11),
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
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_customer'
  });
};
