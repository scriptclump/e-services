/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_cancel_request', {
    inbound_cancel_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inbound_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'inbound_requests',
        key: 'inbound_request_id'
      }
    },
    status: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    requested_by: {
      type: DataTypes.STRING(20),
      allowNull: false
    },
    requested_at: {
      type: DataTypes.DATE,
      allowNull: false
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
    tableName: 'inbound_cancel_request'
  });
};
