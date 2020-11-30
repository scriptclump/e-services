/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('documents_master', {
    doc_master_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    master_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    business_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    country: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    doc_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    is_doc_required: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    mandatory_refno: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    mandatory_doc: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    ref_reqexp: {
      type: DataTypes.STRING(50),
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
    tableName: 'documents_master'
  });
};
