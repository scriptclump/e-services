/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inv_update_log', {
    inv_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dit_order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_dit_order_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    quarantine_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_quarantine_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    old_dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    ref: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ref_type: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'inv_update_log'
  });
};
