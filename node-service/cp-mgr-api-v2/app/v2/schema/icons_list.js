/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('icons_list', {
    id: {
      type: DataTypes.INTEGER(6),
      allowNull: false,
      primaryKey: true
    },
    label: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    url: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    icon_type: {
      type: DataTypes.INTEGER(20),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    icon_code: {
      type: DataTypes.STRING(100),
      allowNull: true
    }
  }, {
    tableName: 'icons_list'
  });
};
