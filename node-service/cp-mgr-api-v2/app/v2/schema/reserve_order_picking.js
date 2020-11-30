/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('reserve_order_picking', {
    reserve_id: {
      type: DataTypes.INTEGER(100),
      allowNull: false
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reserve_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_code: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'reserve_order_picking'
  });
};
