/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_audit', {
    inv_audit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_title: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    location_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    location_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    new_location_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    good_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    damage_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    expire_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    appr_good_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    appr_damage_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    appr_expire_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    old_bin_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    unique_audit_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mfg_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    EAN: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    old_soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    auditor: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    audit_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    assigned_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    assigned_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    is_flag: {
      type: DataTypes.INTEGER(12),
      allowNull: true,
      defaultValue: '0'
    },
    type: {
      type: DataTypes.STRING(45),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'inventory_audit'
  });
};
