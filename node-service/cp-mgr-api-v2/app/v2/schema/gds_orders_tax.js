/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_orders_tax', {
    gds_orders_tax_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'products',
        key: 'product_id'
      }
    },
    gds_order_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_order_products',
        key: 'gds_order_prod_id'
      }
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
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
      allowNull: true,
      defaultValue: '0.00'
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
    tableName: 'gds_orders_tax'
  });
};
