/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('doc_repository', {
    doc_repo_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    doc_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    doc_url: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    doc_type: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    ref_type: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    media_type: {
      type: DataTypes.STRING(15),
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
    tableName: 'doc_repository'
  });
};
