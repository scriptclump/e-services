/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('nct_transcation_history_backup_2017_07_14', {
    hist_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    nct_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hist_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    nct_ref_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prev_status: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    current_status: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    nct_bank: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    nct_branch: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    changed_by: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    comment: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    extra_charges: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(50),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(50),
      allowNull: true
    }
  }, {
    tableName: 'nct_transcation_history_backup_2017_07_14'
  });
};
