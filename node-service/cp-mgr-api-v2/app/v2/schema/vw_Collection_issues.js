/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_Collection_issues', {
    remittance_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    collected_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    remittance_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    acknowledged_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    by_cash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_cheque: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_online: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_upi: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    submitted_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vw_Collection_issues'
  });
};
