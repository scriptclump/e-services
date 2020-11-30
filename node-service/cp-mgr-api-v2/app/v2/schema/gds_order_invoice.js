/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_invoice', {
    gds_order_invoice_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    gds_invoice_grid_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: true,
      references: {
        model: 'gds_invoice_grid',
        key: 'gds_invoice_grid_id'
      }
    },
    base_grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    shipping_tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    shipping_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total_qty: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    discount_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    subtotal: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    billing_address_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_used_for_refund: {
      type: DataTypes.INTEGER(5).UNSIGNED,
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    currency_code: {
      type: DataTypes.STRING(3),
      allowNull: true
    },
    transaction_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    discount_description: {
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
    tableName: 'gds_order_invoice'
  });
};
