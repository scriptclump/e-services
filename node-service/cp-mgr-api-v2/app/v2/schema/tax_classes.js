/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tax_classes', {
    tax_class_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    tax_class_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    tax_class_type: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tax_percentage: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    zip_regx: {
      type: DataTypes.STRING(20),
      allowNull: true,
      defaultValue: '*'
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '99'
    },
    date_start: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    date_end: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    status: {
      type: DataTypes.STRING(12),
      allowNull: true,
      defaultValue: 'Active'
    },
    SGST: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.00'
    },
    CGST: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.00'
    },
    IGST: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.00'
    },
    UTGST: {
      type: DataTypes.FLOAT,
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
    },
    tlm_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tally_reference: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'tax_classes'
  });
};
