/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_group_leave_master', {
    grp_leave_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'emp_groups',
        key: 'emp_group_id'
      }
    },
    leave_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    no_of_leaves: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    frequency_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    old_quart: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    old_year: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
    tableName: 'emp_group_leave_master'
  });
};
