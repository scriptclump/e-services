/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_stockist_payment_history', {
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    transaction_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    pay_code: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    pay_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    pay_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    pay_status: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    payment_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    txn_reff_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ledger_account: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    warehouse_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    FullName: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    },
    Mode_Type: {
      type: DataTypes.STRING(11),
      allowNull: true
    },
    Created_By: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Created_At: {
      type: DataTypes.DATE,
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_stockist_payment_history'
  });
};
