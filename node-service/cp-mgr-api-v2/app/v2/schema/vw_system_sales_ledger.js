/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_system_sales_ledger', {
    voucher_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    voucher_type: {
      type: DataTypes.STRING(5),
      allowNull: false,
      defaultValue: ''
    },
    ledger_account: {
      type: DataTypes.STRING(272),
      allowNull: true
    },
    tran_type: {
      type: DataTypes.STRING(2),
      allowNull: false,
      defaultValue: ''
    },
    amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    naration: {
      type: DataTypes.STRING(351),
      allowNull: true
    },
    cost_centre: {
      type: DataTypes.STRING(6),
      allowNull: false,
      defaultValue: ''
    },
    group: {
      type: DataTypes.STRING(14),
      allowNull: false,
      defaultValue: ''
    },
    Reference_No: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    is_posted: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    tally_resp: {
      type: DataTypes.CHAR(0),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_system_sales_ledger'
  });
};
