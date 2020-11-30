/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('purchase_returns', {
    pr_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    pr_code: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    sr_invoice_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    inward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pr_status: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      defaultValue: '103001'
    },
    discount_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    discount_amt: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    pr_grand_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    pr_total_qty: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    picker_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    picked_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    picker_assigned_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    pr_remarks: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
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
      allowNull: false
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
    tableName: 'purchase_returns'
  });
};
