/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('legal_entity_docs_backup_2017_07_03', {
    doc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
    reference_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
    tableName: 'legal_entity_docs_backup_2017_07_03'
  });
};
