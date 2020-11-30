/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('holiday_list', {
    holiday_list_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    holiday_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    holiday_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    holiday_type: {
      type: DataTypes.INTEGER(1),
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
      allowNull: true
    }
  }, {
    tableName: 'holiday_list'
  });
};
