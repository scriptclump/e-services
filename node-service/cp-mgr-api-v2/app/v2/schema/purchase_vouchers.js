/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('purchase_vouchers', {
    voucher_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    voucher_code: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    voucher_type: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    voucher_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    ledger_group: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    ledger_account: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tran_type: {
      type: DataTypes.STRING(2),
      allowNull: true
    },
    amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    naration: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cost_centre: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    cost_centre_group: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    is_posted: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    sync_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    tally_resp: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    Remarks: {
      type: DataTypes.STRING(100),
      allowNull: true
    }
  }, {
    tableName: 'purchase_vouchers'
  });
};
