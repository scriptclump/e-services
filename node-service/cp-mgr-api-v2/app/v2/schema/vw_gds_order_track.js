/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_gds_order_track', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ordered_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    shipments: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    invoices: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    cancellations: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    returns: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    refunds: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_gds_order_track'
  });
};
