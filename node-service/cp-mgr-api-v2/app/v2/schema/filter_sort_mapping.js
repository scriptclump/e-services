/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('filter_sort_mapping', {
    fs_map_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    fs_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'filter_sort_mapping'
  });
};
