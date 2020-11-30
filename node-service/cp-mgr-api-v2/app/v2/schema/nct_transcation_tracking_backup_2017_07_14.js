/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('nct_transcation_tracking_backup_2017_07_14', {
    nct_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    nct_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    nct_ref_no: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    nct_account_no: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    nct_bank: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '0'
    },
    nct_branch: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '0'
    },
    nct_holdername: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: '0'
    },
    nct_issue_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    transcation_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    nct_collected_by: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    nct_comment: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    nct_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    nct_deposited_to: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    nct_status: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    is_active: {
      type: DataTypes.INTEGER(4),
      allowNull: false,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    proof_image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    extra_charges: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'nct_transcation_tracking_backup_2017_07_14'
  });
};
