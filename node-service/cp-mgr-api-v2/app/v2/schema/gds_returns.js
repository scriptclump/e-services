/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_returns', {
    return_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    reference_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    return_reason_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_quantity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    quarantine_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    excess_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_return_grid',
        key: 'return_grid_id'
      }
    },
    mp_return_id: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amt: {
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
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    SGST: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    CGST: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    IGST: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    UTGST: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_verified: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    is_extra: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    verified_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    verified_at: {
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
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_by_user: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'gds_returns'
  });
};
