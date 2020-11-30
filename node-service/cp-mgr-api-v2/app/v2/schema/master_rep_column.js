/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('master_rep_column', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    filter_name: {
      type: DataTypes.STRING(20),
      allowNull: false,
      primaryKey: true
    },
    column_list: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'master_rep_column'
  });
};
