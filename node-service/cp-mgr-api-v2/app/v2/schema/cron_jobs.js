/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('cron_jobs', {
    cron_job_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    regular_job_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cron_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cron_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cron_order: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    cron_seq: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    current_execution: {
      type: DataTypes.DATE,
      allowNull: true
    },
    last_execution: {
      type: DataTypes.DATE,
      allowNull: true
    },
    next_execution: {
      type: DataTypes.DATE,
      allowNull: true
    },
    interval: {
      type: DataTypes.INTEGER(20),
      allowNull: true
    },
    is_processing: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true,
      defaultValue: '0'
    },
    is_active: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true,
      defaultValue: '1'
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
    tableName: 'cron_jobs'
  });
};
