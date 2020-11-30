/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_groups', {
    emp_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    group_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    weekend_one: {
      type: DataTypes.ENUM('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
      allowNull: true
    },
    weekend_two: {
      type: DataTypes.ENUM('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
      allowNull: true
    }
  }, {
    tableName: 'emp_groups'
  });
};
