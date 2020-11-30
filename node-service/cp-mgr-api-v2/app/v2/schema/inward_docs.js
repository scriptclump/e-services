/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inward_docs', {
    inward_doc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    doc_ref_no: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    doc_ref_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    allow_duplicate: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    doc_url: {
      type: DataTypes.STRING(255),
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
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inward_docs'
  });
};
