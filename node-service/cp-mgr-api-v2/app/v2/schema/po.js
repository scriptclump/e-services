/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('po', {
    po_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    po_code: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_asset: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    indent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    po_type: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    logistic_associate_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    po_status: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '87001'
    },
    is_closed: {
      type: DataTypes.INTEGER(1).UNSIGNED,
      allowNull: true,
      defaultValue: '0'
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0',
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '4'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0',
      references: {
        model: 'legalentity_warehouses',
        key: 'le_wh_id'
      }
    },
    supply_le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    payment_due_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    exp_delivery_date: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    po_remarks: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    po_validity: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    po_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    reason_to_close: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    platform: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payment_mode: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '1'
    },
    payment_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payment_refno: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    tlm_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    tlm_group: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    logistics_cost: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    is_tax_included: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    apply_discount_on_bill: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    discount_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    payment_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    po_so_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    po_so_order_code: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: '0'
    },
    discount_before_tax: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'po'
  });
};
