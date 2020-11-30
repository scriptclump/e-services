/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('indent_products', {
    gds_op_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    indent_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: true,
      references: {
        model: 'indent',
        key: 'indent_id'
      }
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'products',
        key: 'product_id'
      }
    },
    pname: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    no_of_eaches: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    available_inv: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    pack_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    po_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    avail_cfc: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cfc_to_buy: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cfc_elp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cost: {
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
    max_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    target_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    no_of_units: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    indent_status: {
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'indent_products'
  });
};
