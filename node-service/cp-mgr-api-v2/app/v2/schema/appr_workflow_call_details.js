/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('appr_workflow_call_details', {
    appr_call_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    appr_call_for: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    appr_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    appr_call_from: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    appr_current_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    appr_call_user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    appr_call_made_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    appr_call_response: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    appr_call_input: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'appr_workflow_call_details'
  });
};
