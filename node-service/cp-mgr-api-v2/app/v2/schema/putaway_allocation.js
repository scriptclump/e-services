/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('putaway_allocation', {
    allocation_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    wh_id: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    putaway_list_id: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    bin_type: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    inbound_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_uom: {
      type: DataTypes.INTEGER(7),
      allowNull: true
    },
    total_qty: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    placed_qty: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    pending_qty: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    picker_id: {
      type: DataTypes.INTEGER(8),
      allowNull: true,
      defaultValue: '0'
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'putaway_allocation'
  });
};
