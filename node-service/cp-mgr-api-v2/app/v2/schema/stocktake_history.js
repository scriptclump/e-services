/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stocktake_history', {
    stock_take_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    picked_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    picked_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    assigned_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    assigned_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    physical_count: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    good_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    damaged_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
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
    remarks: {
      type: DataTypes.STRING(250),
      allowNull: true
    }
  }, {
    tableName: 'stocktake_history'
  });
};
