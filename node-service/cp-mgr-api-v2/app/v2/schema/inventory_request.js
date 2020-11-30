/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inventory_request', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    segment_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    available_quantity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    total_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    customer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inventory_request'
  });
};
