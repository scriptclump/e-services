/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rca_master', {
    activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    project: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    action_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'rca_master'
  });
};
