/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('appr_workflow_status_new', {
    awf_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    awf_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    awf_for_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_email: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    awf_mobile_notification: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    awf_notification: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    redirect_url_for_close: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    redirect_url: {
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
    tableName: 'appr_workflow_status_new'
  });
};
