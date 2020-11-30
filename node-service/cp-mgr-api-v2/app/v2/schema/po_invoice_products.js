/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('po_invoice_products', {
    invoice_product_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    po_invoice_grid_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      references: {
        model: 'po_invoice_grid',
        key: 'po_invoice_grid_id'
      }
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    free_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    damage_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_name: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    tax_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    hsn_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    tax_data: {
      type: DataTypes.JSON,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    discount_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    comment: {
      type: DataTypes.STRING(500),
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
    tableName: 'po_invoice_products'
  });
};
