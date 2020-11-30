/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('eseal_customer', {
    customer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    customer_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    parent_company_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    bussiness_title: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    bussiness_legal_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    website: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    brand_description: {
      type: DataTypes.STRING(4000),
      allowNull: true
    },
    cin_number: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    pan_number: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tan_number: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tin_number: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    service_tax_number: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    firstname: {
      type: DataTypes.STRING(32),
      allowNull: false
    },
    lastname: {
      type: DataTypes.STRING(32),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(96),
      allowNull: true
    },
    designation: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    phone: {
      type: DataTypes.STRING(32),
      allowNull: true
    },
    mobile_number: {
      type: DataTypes.STRING(32),
      allowNull: true
    },
    address_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ip: {
      type: DataTypes.STRING(40),
      allowNull: false
    },
    user_agent: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    product_types: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    token: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    otp: {
      type: DataTypes.STRING(55),
      allowNull: false
    },
    is_otp_sent: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_otp_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    admin_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    date_added: {
      type: DataTypes.DATE,
      allowNull: false
    },
    country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    profile_completed: {
      type: DataTypes.INTEGER(4),
      allowNull: false,
      defaultValue: '0'
    },
    is_deleted: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_erp_enabled: {
      type: DataTypes.INTEGER(4),
      allowNull: false
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
    tableName: 'eseal_customer'
  });
};
