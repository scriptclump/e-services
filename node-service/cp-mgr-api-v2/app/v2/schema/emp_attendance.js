/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_attendance', {
    id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    emp_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    in_time: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    out_time: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    total_hrs: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    productive_hrs: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    flag: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    }
  }, {
    tableName: 'emp_attendance'
  });
};
