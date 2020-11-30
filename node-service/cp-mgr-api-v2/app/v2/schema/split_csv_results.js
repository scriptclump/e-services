/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('split_csv_results', {
    split_csv_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    value_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    type_of_call: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    name_of_call: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'split_csv_results'
  });
};
