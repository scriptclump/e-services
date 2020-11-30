/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('space_provider', {
    space_pro_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    erp_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    est_year: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    sup_add1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    sup_add2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    sup_country: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    sup_state: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    sup_city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    sup_pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    sup_account_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sup_bank_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sup_account_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    sup_account_type: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    sup_ifsc_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    sup_branch_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    sup_micr_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    sup_currency_code: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    sup_rm: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '1'
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    sup_rank: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: false,
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
    }
  }, {
    tableName: 'space_provider'
  });
};
