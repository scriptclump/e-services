/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_group_feature', {
    group_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    feature_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'rim_group_feature'
  });
};
