/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('appr_workflow_history', {
    awf_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    awf_for_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    awf_for_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_for_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    awf_comment: {
      type: DataTypes.STRING(555),
      allowNull: true
    },
    status_from_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status_to_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    condition_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    next_lbl_role: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_final: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    ticket_created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    created_by_manager: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    title: {
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
    tableName: 'appr_workflow_history'
  });
};
