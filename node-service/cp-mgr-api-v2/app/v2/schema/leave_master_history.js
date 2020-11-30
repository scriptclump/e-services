/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('leave_master_history', {
    leave_master_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    leave_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    no_of_leaves: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'leave_master_history'
  });
};
