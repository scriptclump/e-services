/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('kpi_checking_summary', {
    check_summ_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    hub_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    rep_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    rtd_count: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    rtd_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    inv_count: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    inv_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tot_verified: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    tot_ver_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tot_nt_verified: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    tot_nt_ver_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tot_issues_found: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    tot_iss_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    }
  }, {
    tableName: 'kpi_checking_summary'
  });
};
