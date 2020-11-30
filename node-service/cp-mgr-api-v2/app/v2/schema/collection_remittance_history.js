/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('collection_remittance_history', {
    remittance_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    collected_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    remittance_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    acknowledged_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    by_cash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_cheque: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_online: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_upi: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_pos: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    coins_onhand: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    notes_onhand: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    used_expenses: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    coins_notes_deposited: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    fuel: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    vehicle: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    due_amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    fuel_image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    vehicle_image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    arrears_deposited: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    amount_deposited: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    remittance_mode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    denominations: {
      type: DataTypes.STRING(3000),
      allowNull: true
    },
    is_parent: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    submitted_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    submitted_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    acknowledged_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'collection_remittance_history'
  });
};
