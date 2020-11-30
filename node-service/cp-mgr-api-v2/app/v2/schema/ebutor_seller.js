/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ebutor_seller', {
    seller_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    seller_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    seller_company_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    seller_address_1: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    seller_address_2: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    seller_city: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    seller_country: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    seller_state: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    seller_zipcode: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mobile_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    TIN: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    CIN: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    TAN: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    VAT: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    CST: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    PAN: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ac_holder_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ac_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_ac_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_branch: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_city: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bank_ifsc_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    cancel_cheque_id: {
      type: DataTypes.STRING(50),
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
    tableName: 'ebutor_seller'
  });
};
