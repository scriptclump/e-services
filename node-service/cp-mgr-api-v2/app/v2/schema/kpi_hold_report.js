/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('kpi_hold_report', {
    kpi_id: {
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
      type: DataTypes.STRING(100),
      allowNull: false
    },
    report_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    cust_dr_lck_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cust_dr_lck_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    wrng_add_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wrng_add_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    no_cash_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    no_cash_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cust_na_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cust_na_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    no_rt_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    no_rt_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    hold_sr_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hold_sr_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    hold_nr_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hold_nr_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    others_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    others_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    delay_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delay_val: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'kpi_hold_report'
  });
};
