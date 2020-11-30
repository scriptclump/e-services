/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_refund_products', {
    refund_products_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    refund_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_refund_grid',
        key: 'refund_grid_id'
      }
    },
    base_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    row_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    quantity: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'gds_refund_products'
  });
};
