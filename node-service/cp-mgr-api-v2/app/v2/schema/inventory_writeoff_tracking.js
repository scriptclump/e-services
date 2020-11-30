/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_writeoff_tracking', {
    track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    old_dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dnd_diff: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    old_dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dit_diff: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    upload_dnd_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    upload_dit_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    writeoff_upload_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    approval_comment: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    from_date: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    to_date: {
      type: DataTypes.TEXT,
      allowNull: false
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
    approved_at: {
      type: DataTypes.DATE,
      allowNull: false
    }
  }, {
    tableName: 'inventory_writeoff_tracking'
  });
};
