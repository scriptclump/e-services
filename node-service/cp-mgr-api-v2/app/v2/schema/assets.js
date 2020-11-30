/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('assets', {
    asset_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    company_asset_code: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    business_unit: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    allocated_to_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    allocated_to_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    asset_status: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    asset_allocated_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    responsible_technician: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    purchase_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    invoice_number: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    serial_number: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    warranty_status: {
      type: DataTypes.ENUM('Yes','No'),
      allowNull: false
    },
    warranty_end_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    warranty_year: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    warranty_month: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    depresiation_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    depresiation_per_month: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    depresiation_month: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_working: {
      type: DataTypes.ENUM('No','Yes'),
      allowNull: false
    },
    is_manual_import: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    notes: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    asset_category: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    isactive: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'assets'
  });
};
