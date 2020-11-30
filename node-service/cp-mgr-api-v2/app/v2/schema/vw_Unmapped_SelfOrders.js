/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_Unmapped_SelfOrders', {
    ff: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vw_Unmapped_SelfOrders'
  });
};
