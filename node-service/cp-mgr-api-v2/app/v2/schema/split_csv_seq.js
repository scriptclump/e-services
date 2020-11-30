/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('split_csv_seq', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    }
  }, {
    tableName: 'split_csv_seq'
  });
};
