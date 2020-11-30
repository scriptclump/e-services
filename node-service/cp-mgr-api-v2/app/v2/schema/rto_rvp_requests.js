/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rto_rvp_requests', {
    request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'inbound_requests',
        key: 'inbound_request_id'
      }
    },
    request_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    request_reason: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    seller_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    channel_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_id: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    user_id: {
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
    tableName: 'rto_rvp_requests'
  });
};
