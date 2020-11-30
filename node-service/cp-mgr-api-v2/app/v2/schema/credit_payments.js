/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('credit_payments', {
    credit_payment_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.BIGINT,
      allowNull: false
    },
    cashback_amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'credit_payments'
  });
};
