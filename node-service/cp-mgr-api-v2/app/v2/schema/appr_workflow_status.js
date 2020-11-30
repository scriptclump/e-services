/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('appr_workflow_status', {
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
    awf_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_condition_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_status_to_go_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_fast_last_flag: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    applied_role_ids: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_final: {
      type: DataTypes.INTEGER(1),
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
    tableName: 'appr_workflow_status'
  });
};
