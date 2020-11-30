/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_docs', {
    inbound_doc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    doc_unique_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    inbound_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'inbound_requests',
        key: 'inbound_request_id'
      }
    },
    doc_type: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    doc_url: {
      type: DataTypes.STRING(255),
      allowNull: false
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
    tableName: 'inbound_docs'
  });
};
