/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_orders_legal_entity_mismatch', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Orders le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Orders legal entity ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Warehouse le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Warehouse legal entity ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_orders_legal_entity_mismatch'
  });
};
