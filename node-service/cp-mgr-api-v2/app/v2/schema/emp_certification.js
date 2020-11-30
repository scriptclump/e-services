/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_certification', {
    employee_certification_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    certification_name: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    institution_name: {
      type: DataTypes.STRING(40),
      allowNull: true
    },
    grade: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    certified_on: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    valid_upto: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    tableName: 'emp_certification'
  });
};
