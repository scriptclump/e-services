/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('payment_details', {
    pay_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pay_code: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    pay_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    pay_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    reff_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pay_utr_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    pay_to_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pay_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    payment_from: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pay_for: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    deposite_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    pay_status: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    txn_reff_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ledger_group: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ledger_account: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cost_center: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    cost_center_group: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    txn_tolegal_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pay_for_module: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    auto_initiate: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
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
    }
  }, {
    tableName: 'payment_details'
  });
};
