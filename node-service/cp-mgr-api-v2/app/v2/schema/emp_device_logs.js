/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_device_logs', {
    device_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    log_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    device_id: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    emp_code: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    device_direction: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    in_out: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'emp_device_logs'
  });
};
