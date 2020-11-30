/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_orders_batch', {
    gob_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    inward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ord_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    inv_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ret_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_orders_batch'
  });
};
