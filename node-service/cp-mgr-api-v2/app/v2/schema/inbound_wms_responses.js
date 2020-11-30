/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_wms_responses', {
    inbound_wms_response_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inbound_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    log_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    request_type: {
      type: DataTypes.STRING(35),
      allowNull: true
    },
    status: {
      type: DataTypes.STRING(35),
      allowNull: true
    },
    remarks: {
      type: DataTypes.STRING(255),
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
    tableName: 'inbound_wms_responses'
  });
};
