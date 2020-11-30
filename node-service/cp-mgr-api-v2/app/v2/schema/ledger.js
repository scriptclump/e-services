/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ledger', {
    ledger_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ledger_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    party_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    invoice_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    reference_no: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    particulars: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    dr_account: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    dr_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cr_account: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    cr_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    collected_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payment_mode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    balance: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    remarks: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sync_status: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    sync_ref_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    sync_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    sync_remarks: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    proof: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cost_centre: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    is_authorized: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    authorized_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    authorized_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'ledger'
  });
};
