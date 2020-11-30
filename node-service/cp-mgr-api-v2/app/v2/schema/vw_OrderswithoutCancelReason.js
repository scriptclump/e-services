/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_OrderswithoutCancelReason', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Picker Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Sch Picking Date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    Picked Date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    cancel_reason_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Hold Reason: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'vw_OrderswithoutCancelReason'
  });
};
