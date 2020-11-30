/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('order_count', {
    Missing: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    Damaged: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    total_partial_count: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    ALLl: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    OPEN: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    rtd: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    delivered: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    cbc: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    hold: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    cbe: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    picklist_generated: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    stock_in_transit: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    stock_in_hub: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    ofd: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    completed: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    invoiced: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    unpaid: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    total_pending_payments: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    rawd: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    rawm: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    return_initiated: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    return_approved: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    return_hub_approved: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    st_hub_todc: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    stock_indc: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    return_requested: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    PD_return_initiated: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    PD_return_approved: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    PD_return_hub_approved: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    st_hub_to_dc: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    stock_in_dc: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    PD_return_requested: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    collections: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    exec: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'order_count'
  });
};
