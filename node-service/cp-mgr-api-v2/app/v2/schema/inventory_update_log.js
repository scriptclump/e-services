/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_update_log', {
    log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    current_order_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    new_order_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    old_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    new_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inventory_update_log'
  });
};
