/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('po_invoice_grid', {
    po_invoice_grid_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    invoice_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    inward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    billing_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    shipping_fee: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_on_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    invoice_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
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
    tableName: 'po_invoice_grid'
  });
};
