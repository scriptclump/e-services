/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('voucher_details_lines', {
    voucher_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    voucher_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ledger_account: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    trans_type: {
      type: DataTypes.STRING(5),
      allowNull: true
    },
    amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'voucher_details_lines'
  });
};
