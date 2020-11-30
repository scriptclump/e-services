/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_invoice_grid_backup_2017_07_13', {
    gds_invoice_grid_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    cgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    sgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    igst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    utgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    invoice_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      unique: true
    },
    gds_ship_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    currency_code: {
      type: DataTypes.STRING(3),
      allowNull: true
    },
    billing_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    invoice_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    remarks: {
      type: DataTypes.TEXT,
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
      allowNull: true
    },
    old_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    old_inv_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'gds_invoice_grid_backup_2017_07_13'
  });
};
