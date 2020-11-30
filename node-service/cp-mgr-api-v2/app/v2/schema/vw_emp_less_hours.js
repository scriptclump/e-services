/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_emp_less_hours', {
    NAME: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    EMP CODE: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    DATE: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    PRODUCTIVE_HOURS: {
      type: DataTypes.STRING(29),
      allowNull: true
    }
  }, {
    tableName: 'vw_emp_less_hours'
  });
};
