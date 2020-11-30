/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_ecash_creditlimit_log', {
    user_ecash_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
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
      allowNull: true
    },
    pre_approve_limit: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    applied_cashback: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    cashback: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    payment_due: {
      type: DataTypes.FLOAT,
      allowNull: true
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
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    ins_created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'user_ecash_creditlimit_log'
  });
};
