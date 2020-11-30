/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('expenses_main', {
    exp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    exp_code: {
      type: DataTypes.STRING(20),
      allowNull: false
    },
    exp_req_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    exp_req_type_for_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    exp_subject: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    exp_actual_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tally_ledger_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    exp_approved_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    submit_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    exp_reff_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    submited_by_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    exp_appr_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    is_direct_advance: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
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
    business_unit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'expenses_main'
  });
};
