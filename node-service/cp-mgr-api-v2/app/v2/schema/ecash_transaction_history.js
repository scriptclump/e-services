/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ecash_transaction_history', {
    ecash_transaction_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivered_amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    cash_back_amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    balance_amount: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    comment: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    transaction_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pay_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    mode_type_payment: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    is_deleted: {
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'ecash_transaction_history'
  });
};
