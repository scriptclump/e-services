/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rca_approval_history', {
    record_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    approver_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approver_email: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approver_key: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    action_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    action_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    item_order: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'rca_approval_history'
  });
};
