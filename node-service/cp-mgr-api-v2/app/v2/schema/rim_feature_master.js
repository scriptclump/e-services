/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_feature_master', {
    feature_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    feature_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    feature_comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'rim_feature_master'
  });
};
