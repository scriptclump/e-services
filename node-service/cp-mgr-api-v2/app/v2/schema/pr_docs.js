/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('pr_docs', {
    doc_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    pr_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    file_path: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'pr_docs'
  });
};
