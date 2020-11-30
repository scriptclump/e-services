/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('debit_notes', {
    debit_note_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    party_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_ref_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    currency: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax_per: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    net_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_accepted: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    accepted_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    accepted_on: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    acc_ref_no: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    issued_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    issued_on: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'debit_notes'
  });
};
