/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_docs', {
    doc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    employee_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    doc_name: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    doc_url: {
      type: DataTypes.STRING(1500),
      allowNull: true
    },
    doc_type: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    ref_type: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    media_type: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true
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
      defaultValue: '0000-00-00 00:00:00'
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    }
  }, {
    tableName: 'emp_docs'
  });
};
