/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('doc_repo_tags', {
    doc_repo_tag_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    doc_repo_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    tag_name: {
      type: DataTypes.STRING(150),
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
    tableName: 'doc_repo_tags'
  });
};
