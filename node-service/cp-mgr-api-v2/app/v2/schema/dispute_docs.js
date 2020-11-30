/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dispute_docs', {
    dispute_doc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dispute_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'disputes',
        key: 'dispute_id'
      }
    },
    doc_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    doc_path: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    uploaded_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    uploaded_on: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'dispute_docs'
  });
};
