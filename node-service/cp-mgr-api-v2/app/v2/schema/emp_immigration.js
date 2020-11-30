/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_immigration', {
    emp_immigration_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    passport_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    passport_expiry_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    visa_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    visa_expiry_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    tableName: 'emp_immigration'
  });
};
