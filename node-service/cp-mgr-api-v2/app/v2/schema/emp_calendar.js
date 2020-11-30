/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_calendar', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    db_date: {
      type: DataTypes.DATEONLY,
      allowNull: false,
      unique: true
    },
    year: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    month: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    day: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    quarter: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    week: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    day_name: {
      type: DataTypes.STRING(9),
      allowNull: false
    },
    month_name: {
      type: DataTypes.STRING(9),
      allowNull: false
    },
    holiday_flag: {
      type: DataTypes.CHAR(1),
      allowNull: true,
      defaultValue: 'f'
    },
    weekend_flag: {
      type: DataTypes.CHAR(1),
      allowNull: true,
      defaultValue: 'f'
    },
    event: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'emp_calendar'
  });
};
