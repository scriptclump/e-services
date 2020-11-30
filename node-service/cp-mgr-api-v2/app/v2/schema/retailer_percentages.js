/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('retailer_percentages', {
    ret_pct_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tdv: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tgm: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    gm: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tgm_pct: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tuo: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ff_calls: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    success_rate: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    success_pct: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    delivery_acceptance: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    da_pct: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total_orders: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tot_self_orders: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    self_contribution: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    self_pct: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'retailer_percentages'
  });
};
