/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('cat_bin_mapping', {
    bin_mapping_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_location: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    bin_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'cat_bin_mapping'
  });
};
