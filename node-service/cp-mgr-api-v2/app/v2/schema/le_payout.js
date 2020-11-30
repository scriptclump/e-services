/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('le_payout', {
    le_payout_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    payment_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    pay_period_from: {
      type: DataTypes.DATE,
      allowNull: true
    },
    pay_period_to: {
      type: DataTypes.DATE,
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payee_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    payment_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payee_from: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    payment_transaction_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    cheque_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    payee_address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    manf_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payout_comments: {
      type: DataTypes.STRING(255),
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
    tableName: 'le_payout'
  });
};
