/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_ecash_creditlimit', {
    user_ecash_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ecash_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    creditlimit: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    pre_approve_limit: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    applied_cashback: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    cashback: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    payment_due: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
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
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    creditlimit_modified_amt: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.00000'
    }
  }, {
    tableName: 'user_ecash_creditlimit'
  });
};
