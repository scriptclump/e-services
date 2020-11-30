/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stock_transfer_details', {
    transfer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    old_sku: {
      type: DataTypes.STRING(45),
      allowNull: false
    },
    old_sku_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    new_sku: {
      type: DataTypes.STRING(45),
      allowNull: false
    },
    new_sku_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    activity_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    activity_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    st_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    new_product_name: {
      type: DataTypes.STRING(120),
      allowNull: true
    },
    old_product_name: {
      type: DataTypes.STRING(120),
      allowNull: true
    },
    audited_by: {
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
      allowNull: false
    },
    approval_status: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    approve_comment: {
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
    }
  }, {
    tableName: 'stock_transfer_details'
  });
};
