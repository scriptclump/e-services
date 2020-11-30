/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('leave_history', {
    leave_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    emp_ep_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    leave_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    no_of_days: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    reason: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    contact_number: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    emergency_mail: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    module_name: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    project_name: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    hours: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Comment: {
      type: DataTypes.STRING(200),
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'leave_history'
  });
};
