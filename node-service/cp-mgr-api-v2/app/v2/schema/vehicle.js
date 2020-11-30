/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vehicle', {
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    vehicle_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '156001'
    },
    vehicle_model: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    replace_with: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    driver_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
    veh_provider: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    body_type: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    reg_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    reg_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    veh_lbh: {
      type: "DOUBLE(10,3)",
      allowNull: true
    },
    veh_lbh_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    license_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    license_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    veh_weight: {
      type: "DOUBLE(10,3)",
      allowNull: true
    },
    veh_weight_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    insurance_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    insurance_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    length: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    breadth: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    height: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    fit_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    poll_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    safty_exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    batch_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    crates: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
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
    tableName: 'vehicle'
  });
};
