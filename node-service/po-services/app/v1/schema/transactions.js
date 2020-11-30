module.exports = (sequelize, DataTypes) => {
  return sequelize.define('transactions', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    txn_id: {      
      type: DataTypes.STRING(200),
      allowNull: false
    },
    amount: {
      type: DataTypes.FLOAT,
      allowNull: false
    },
    receiver_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    payment_to: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    tax_status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'transactions',
    timestamps: false
  });
}