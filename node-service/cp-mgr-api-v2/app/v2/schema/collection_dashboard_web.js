/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('collection_dashboard_web', {
    cdw_id: {
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
      type: DataTypes.STRING(500),
      allowNull: false
    },
    dc: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    collection_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    collected_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    del_tgm: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    submitted_by_do: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    remit_hi: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    fin_sub: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    fin_app: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    by_cash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    by_pos: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    due_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    fuel: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    vehicle: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    arrears_deposited: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    coins_on_hand: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    notes_on_hand: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    used_for_expenses: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tot_outstanding: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    notes_tot: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    coins_tot: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'collection_dashboard_web'
  });
};
