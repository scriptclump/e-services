/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('scheduled_tasks', {
    task_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    task_summary: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    task_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    scheduled_day: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    scheduled_hr: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    scheduled_min: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    recurchoice: {
      type: DataTypes.ENUM('day','week','date','last day of month'),
      allowNull: false
    },
    recur: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    scheduled_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    task_posted: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'scheduled_tasks'
  });
};
