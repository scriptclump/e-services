/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_audit_bulkupload', {
    bulk_audit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    file_path: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    type: {
      type: DataTypes.STRING(12),
      allowNull: false
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    excel_upload_logs_pk: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    audit_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inventory_audit_bulkupload'
  });
};
