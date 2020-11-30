/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('poa_rollback_plan', {
    record_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    task_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    task_duration: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    task_owner: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    item_order: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    action_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'poa_rollback_plan'
  });
};
