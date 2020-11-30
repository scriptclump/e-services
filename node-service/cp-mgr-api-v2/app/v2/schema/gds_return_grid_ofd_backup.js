/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_return_grid_ofd_backup', {
    return_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_return_id: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    return_order_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    is_verified: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    total_return_value: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    total_return_items: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    total_return_item_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    igst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    utgst_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    return_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
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
    }
  }, {
    tableName: 'gds_return_grid_ofd_backup'
  });
};
