/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('serial_numbers', {
    serial_no_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    state_code: {
      type: DataTypes.STRING(4),
      allowNull: true
    },
    prefix: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    yearlength: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    monthlength: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    length: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '7'
    },
    reference_id: {
      type: DataTypes.INTEGER(7).UNSIGNED,
      allowNull: false
    }
  }, {
    tableName: 'serial_numbers'
  });
};
