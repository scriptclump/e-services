/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_delivery_temp', {
    gds_delivery_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_data: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_order_delivery_temp'
  });
};
