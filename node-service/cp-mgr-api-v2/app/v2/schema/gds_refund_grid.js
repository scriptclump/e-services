/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_refund_grid', {
    refund_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    total_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    paid_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    refund_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    adjustment_fee: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    paymenttype: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    paidname: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    transactionid: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_return_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_refund_grid'
  });
};
