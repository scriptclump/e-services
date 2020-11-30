/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ff_targets_cons', {
    fc_tgt_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ff_user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ff_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    fc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    fc_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    tbv_tgt: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    act_tbv_tgt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tbv_ach: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tbv_ach_per: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    tbv_bal: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    orders_tgt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    orders_ach: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    orders_bal: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    uob_tgt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    uob_ach: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    uob_bal: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    bills_cut_tgt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    act_bc_tgt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bills_cut_ach: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    bills_cut_bal: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    fb_tgt: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    fb_ach: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    fb_bal: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    start_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    end_date: {
      type: DataTypes.DATE,
      allowNull: false
    }
  }, {
    tableName: 'ff_targets_cons'
  });
};
