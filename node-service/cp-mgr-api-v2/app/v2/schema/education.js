/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('education', {
    education_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    qualification: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    }
  }, {
    tableName: 'education'
  });
};
